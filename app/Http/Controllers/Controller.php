<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\form;
use App\under;
use App\menu;
use App\user_form;
use App\grop;
use App\invoice;
use App\invoice_fine;
use App\invoice_pay;
use App\manager;
use Maatwebsite\Excel\Facades\Excel;
use App\user;
use App\user_grop;
use App\Services\InvoicePenaltyCalculator;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
/* START خروجی اکسل از ستون ها فقط */
use Maatwebsite\Excel\Concerns\WithHeadings;
use Morilog\Jalali\Jalalian;

class ExportOnlyFormHeadings implements WithHeadings
{
  protected $headings;
  function __construct($headings)
  {
    $this->headings = $headings;
  }

  public function headings(): array
  {
    return $this->headings;
  }
}
/* END خروجی اکسل از ستون ها فقط */


function generateRandomString($length = 6)
{
  return bin2hex(random_bytes($length / 2));
}
class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  static $_validations_form = [
      'national-code' => 'کد ملی',
      'date' => 'تاریخ'
    ], //گزینه های اعتبارسنجی فرم
    $_types_fields_form = [
      1 => 'متن',
      2 => 'ایمیل',
      3 => 'شماره',
      4 => 'لیست کشویی',
      5 => 'انتخابی',
      6 => 'رادیویی',
      7 => 'فایل',
      8 => 'متن طولانی'
    ], //فرمت های فیلد
    $_auto_completes_form = [
      'form' => 'فرم',
      'user' => 'کاربر',
      'invoice' => 'صورتحساب'
    ], //گزینه های تکمیل خودکار
    $_settings_auto_completes_form = [
      'user' => [
        'mobile' => 'شماره موبایل',
        'name' => 'نام',
        'name_2' => 'نام خانوادگی',
        'national_code' => 'کد ملی',
        'hash' => 'کد اختصاصی',
        'date_subscription' => 'تاریخ اشتراک',
        'name_grop' => 'نام گروه'
      ],
      'invoice' => [
        'price-invoices' => 'جمع تعهدها',
        'price-fines' => 'جمع جرائم',
        'price-paid' => 'جمع واریزی ها',
        'final-debt-balance' => 'مانده بدهی نهایی'
      ]
    ], //تنظیمات مربوط به گزینه ها
    $_directory_files_user = '/public_html/Files/User/', //آدرس پوشه فایل های کاربران
    $_menu__formats_allowed_field_files = array(
      'image/png',
      'image/jpeg',
      'image/gif',
      'application/pdf'
    ), //فرمت های مجاز فایل
    $_statuses_ticket = [
      ["Text" => "بسته"],
      [
        "Text" => "فعال",
        "Color" => "success"
      ],
      [
        "Text" => "در حال بررسی",
        "Color" => "info"
      ],
      [
        "Text" => "پاسخ داده شد",
        "Color" => "primary"
      ],
      [
        "Text" => "در انتظار اقدام کاربر",
        "Color" => "danger"
      ],
      [
        "Text" => "بایگانی",
        "Color" => "warning"
      ]
    ]; //وضعیت های تیکت


  /* START ثبت صورتحساب جدید */
  protected function invoiceSave($_request, string $name_part, $_invoice, int $_id_grop = 0)
  {
    if (
      //گروه های کاربری مجموعه
      (
        isset($_request['gropUser'])
        &&
        (
          !is_array($_request['gropUser'])
          ||
          empty($_request['gropUser'])
        )
      )

      ||

      //مبلغ جریمه روزانه
      (
        isset($_request['daily-fine-amount'])
        &&
        !in_array($_request['daily-fine-amount'], range(0, 9))
      )

      ||

      //مبلغ جریمه ثابت
      (
        isset($_request['fixed-penalty-amount'])
        &&
        $_request['fixed-penalty-amount']
        &&
        !is_numeric($_request['fixed-penalty-amount'])
      )
    ) return redirect("/$name_part/invoice/add");

    $_id_grop = ($_id_grop == 0) ? $_request['grop'] : $_id_grop;
    if (empty($_id_grop)) return redirect("/$name_part/invoice/add");

    $_users = [];
    if (isset($_request['user'])):
      foreach ($_request['user'] as $_id_user):
        if (is_numeric($_id_user)) $_users[] = $_id_user;
      endforeach;

      $_users = !empty($_users) ? '[' . implode(',', $_users) . ']' : '';
    else:
      foreach ($_request['gropUser'] as $_id_grop_user):
        if (is_numeric($_id_grop_user)) $_users['Grop Users'][] = $_id_grop_user;
      endforeach;

      $_users = !empty($_users) ? json_encode($_users) : '';
    endif;

    $is_edit_mode = $_invoice->id;
    $_invoice->id_grop = (int) $_id_grop;
    $_invoice->id_users = (string) $_users;
    $_invoice->title = (string) $_request['title'];
    $_invoice->text = (string) $_request['text'];
    $_invoice->number = (int) $_request['number'];
    $_invoice->price = (int) str_replace(',', '', $_request['price']);
    $_invoice->am_start = (string) $_request['am_start'];
    $_invoice->am_end = (string) $_request['am_end'];
    $_invoice->fine_calc_date = (string) $_request['fine_calc_date'];

    // Validate date formats if they are not empty
    try {
        if (!empty($_invoice->am_start)) Verta::parse($_invoice->am_start);
        if (!empty($_invoice->am_end)) Verta::parse($_invoice->am_end);
        if (!empty($_invoice->fine_calc_date)) Verta::parse($_invoice->fine_calc_date);
    } catch (\Exception $e) {
        return redirect("/$name_part/invoice/add")->withErrors(['date' => 'فرمت تاریخ وارد شده صحیح نیست. لطفا از تقویم استفاده کنید.']);
    }
    $_invoice->daily_fine_amount = (int) $_request['daily-fine-amount']; //مبلغ جریمه روزانه
    $_invoice->fixed_penalty_amount = (int) $_request['fixed-penalty-amount']; //مبلغ جریمه ثابت
    $_invoice->fine_calc_amount = (int) str_replace(',', '', $_request['fine_calc_amount']); // مبلغ جریمه محاسبه شده
    $_invoice->save();

    /**
     * در صورتی که قصد ثبت صورتحساب جدید داشتیم
     * مقدار فیلد "مبلغ جریمه محاسبه شده" به عنوان مبلغ جریمه اولیه در 
     * جدول جریمه های کاربران حاضر در این صورتحساب
     * ثبت می شود
     */
    if (!$is_edit_mode && is_iterable($_request['user'])) {
      foreach ($_request['user'] as $_user_id) {
        $invoice_fine = new invoice_fine();
        $invoice_fine->id_user = $_user_id;
        $invoice_fine->id_invoice = $_invoice['id'];
        $invoice_fine->price = empty($_invoice->fine_calc_amount) ? 0 : $_invoice->fine_calc_amount;
        $invoice_fine->am = Verta::parse(verta()->formatDate());
        $invoice_fine->save();
      }
    }
  }
  /* END ثبت صورتحساب جدید */


  /* START فرم ها */
  /* START خروجی اکسل از ستون های فرم */
  protected function DownloadExcelExampleColumnsForm($_id_form, string $_fields_form)
  {
    $columns_excel = []; //ستون های اکسل
    foreach (json_decode($_fields_form, true) as $form_field) $columns_excel[] = $form_field['name'];
    $columns_excel[] = 'user_id';

    Excel::download(
      new ExportOnlyFormHeadings($columns_excel),
      "Form Example User - $_id_form.xlsx"
    )->send();
    die();
  }
  /* END خروجی اکسل از ستون های فرم */


  /* START حذف گروهی کاربران */
  protected function DeleteUserForm($_request, $_link_page_before, $_id_grop = 0)
  {
    if (
      !isset($_request['ids'])
      ||
      !is_array($_request['ids'])
      ||
      empty($_request['ids'])
    ) return redirect($_link_page_before);

    $_ids_users_form_selected = []; //آیدی کاربران انتخاب شده فرم
    foreach ($_request['ids'] as $_id_user_form => $_value):
      if (is_numeric($_id_user_form)) $_ids_users_form_selected[] = $_id_user_form;
    endforeach;
    if (empty($_ids_users_form_selected)) return redirect($_link_page_before);

    if (empty($_id_grop)): user_form::whereRaw('id IN (' . implode(',', $_ids_users_form_selected) . ')')->delete(); //حذف
    else: user_form::join('user_grops', 'user_id', 'id_user')->whereRaw('user_forms.id IN (' . implode(',', $_ids_users_form_selected) . ')')->where('grop_id', $_id_grop)->delete();
    endif;
    return redirect($_link_page_before);
  }
  /* END حذف گروهی کاربران */


  /* START صفحه ویرایش */
  protected function pageEditForm($_request, $_id, $role = 'admin')
  {
    $form = form::findOrFail($_id, ['id', 'name', 'text', 'fild', 'id_grop', 'required', 'saving_disabled', 'confirm', 'start_date', 'end_date', 'show_onuserpanel', 'fill_form_limitation', 'notify_on_submit', 'notify_phone']); //اطلاعات

    $_list_ids_form_auto_complete = []; //لیست آیدی های فرم تکمیل خودکار
    foreach (json_decode($form['fild'], true) as $field_available):
      if ($field_available['type'] == 1 || $field_available['type'] == 2 || $field_available['type'] == 3):
        $_value_auto_complete_field_available = array_key_exists('auto-complete', $field_available) ? (array) $field_available['auto-complete'] : []; //تکمیل خودکار بر اساس
        if (
          empty($_value_auto_complete_field_available)
          ||
          (array_key_exists('Type', $_value_auto_complete_field_available) ? strtolower($_value_auto_complete_field_available['Type']) : '') != 'form' //نوع
          ||
          !array_key_exists('ID', $_value_auto_complete_field_available)
          ||
          !array_key_exists('Field', $_value_auto_complete_field_available)
        ) continue;

        $_id_auto_complete_field_available = $_value_auto_complete_field_available['ID']; //آیدی فیلد
        if (array_key_exists($_id_auto_complete_field_available, $_list_ids_form_auto_complete)) continue;

        $_list_ids_form_auto_complete[$_id_auto_complete_field_available] = $_id_auto_complete_field_available;
      endif;
    endforeach;

    //دریافت اطلاعات فرم های استفاده تکمیل خودکار
    if (!empty($_list_ids_form_auto_complete)):
      $list_id_and_field_auto_complete_form = [];
      foreach (form::whereRaw('id IN (' . implode(',', array_keys($_list_ids_form_auto_complete)) . ')')->get(['id', 'fild']) as $id_form_with_field) $list_id_and_field_auto_complete_form[$id_form_with_field['id']] = json_decode($id_form_with_field['fild'], true);
    endif;

    $form['fild'] = json_decode($form['fild'], true);
    $form['id_grop'] = (array) json_decode($form['id_grop'], true); //آیدی مجموعه ها

    return view("$role.form.edit", [
      'info_form' => $form,
      'types_fields_form' => self::$_types_fields_form, //فرمت های فیلد
      'items_validation' => self::$_validations_form,
      'items_auto_complete' => self::$_auto_completes_form,
      'settings_auto_completes_form' => self::$_settings_auto_completes_form,
      'list_id_and_field_auto_complete_form' => isset($list_id_and_field_auto_complete_form) ? $list_id_and_field_auto_complete_form : [],
      'grops' => grop::all(['id', 'name']),
      'role' => $role,
    ]);
  }
  /* END صفحه ویرایش */


  /* START اعتبارسنجی */
  protected function _validation_form($_request, $role = 'admin')
  {
    if ($role == 'admin'):
      if (
        //مجموعه ها
        !isset($_request['grops'])
        ||
        !is_array($_request['grops'])
      ) return false;

      //اعتبارسنجی مجموعه های انتخاب شده
      foreach ($_request['grops'] as $_key => $_id_grop):
        if (!is_numeric($_key) || !is_numeric($_id_grop)) unset($_request['grops'][$_key]);
      endforeach;
      if (empty($_request['grops'])) return false;
    endif;


    /* START دریافت لیست آیدی های فرم های انتخاب شده موجود */
    $_sql_forms_auto_complete = []; //کوئری فرم های تکمیل خودکار
    $_bind_values_sql_forms_auto_complete = []; //رمزنگاری ها
    for ($x = 0; $x <= $_request['number']; $x++):
      if (
        !isset($_request['type' . $x])
        ||
        !in_array($_request['type' . $x], range(1, 3))
        ||
        !array_key_exists($_request['auto-complete' . $x], self::$_auto_completes_form)
        ||
        $_request['auto-complete' . $x] != 'form'
        ||
        !is_numeric($_request['id-form-' . $x])
        ||

        //فیلد
        !isset($_request['field-form-' . $x])
        ||
        $_request['field-form-' . $x] == ''
      ) continue;

      $_sql_forms_auto_complete[] = '(id=' . $_request['id-form-' . $x] . ' AND fild LIKE ?)';
      $_bind_values_sql_forms_auto_complete[] = '%"name":"' . $_request['field-form-' . $x] . '"%';
    endfor;

    //لیست آیدی های فرم های انتخاب شده موجود
    $_list_of_ids_of_available_selected_forms = [];
    if (!empty($_sql_forms_auto_complete)) foreach (form::whereRaw(implode(' OR ', $_sql_forms_auto_complete), $_bind_values_sql_forms_auto_complete)->get(['id']) as $_form_available) $_list_of_ids_of_available_selected_forms[] = $_form_available['id'];
    /* END دریافت لیست آیدی های فرم های انتخاب شده موجود */


    $_form = [];
    for ($x = 0; $x <= $_request['number']; $x++):
      if (isset($_request["type" . $x])):
        $_info_form = [];
        if (isset($_request['visible' . $x])) $_info_form['visible'] = 1; //قابل مشاهده
        if (isset($_request['editable' . $x])) $_info_form['editable'] = 1; //قابل ویرایش
        $_info_form['value'] = isset($_request["value" . $x]) ? $_request["value" . $x] : null; //اجباری
        $_info_form['description'] = isset($_request["description" . $x]) ? $_request["description" . $x] : null; //اجباری
        /* START تکمیل خودکار بر اساس */
        if ($_request["type" . $x] == 1 or $_request["type" . $x] == 2 or $_request["type" . $x] == 3 or $_request["type" . $x] == 5 || $_request['type' . $x] == 7 || $_request['type' . $x] == 8):
          $_info_form['type'] = $_request['type' . $x];
          $_info_form['title'] = $_request['title' . $x];
          $_info_form['name'] = $_request['name' . $x];
          $_info_form['value'] = isset($_request["value" . $x]) ? $_request["value" . $x] : null; //اجباری
          $_info_form['description'] = isset($_request["description" . $x]) ? $_request["description" . $x] : null; //اجباری
          /* START تکمیل خودکار بر اساس */
          if (array_key_exists($_request['validation-based' . $x], self::$_validations_form)) $_info_form['validation-based'] = $_request['validation-based' . $x]; //اعتبارسنجی بر اساس
          $_info_form['value'] = isset($_request["value" . $x]) ? $_request["value" . $x] : null; //اجباری
          $_info_form['description'] = isset($_request["description" . $x]) ? $_request["description" . $x] : null; //اجباری
          /* START تکمیل خودکار بر اساس */
          if (
            (
              $_request['type' . $x] == 1
              ||
              $_request['type' . $x] == 2
              ||
              $_request['type' . $x] == 3
            )
            &&
            array_key_exists($_request['auto-complete' . $x], self::$_auto_completes_form)
          ):
            /* START فرم */
            if (
              $_request['auto-complete' . $x] == 'form'
              &&
              in_array($_request['id-form-' . $x], $_list_of_ids_of_available_selected_forms) //آیدی فرم
            ):
              $_info_form['auto-complete'] = [
                'Type' => 'Form',
                'ID' => $_request['id-form-' . $x],
                'Field' => $_request['field-form-' . $x]
              ];
            /* END فرم */


            /* START کاربر */
            elseif (
              $_request['auto-complete' . $x] == 'user'
              &&
              array_key_exists($_request['info-user-' . $x], self::$_settings_auto_completes_form['user'])
            ):
              $_info_form['auto-complete'] = [
                'Type' => 'User',
                'Field' => $_request['info-user-' . $x]
              ];
            /* END کاربر */


            /* START صورتحساب */
            elseif (
              $_request['auto-complete' . $x] == 'invoice'
              &&
              array_key_exists($_request['info-invoice-' . $x], self::$_settings_auto_completes_form['invoice'])
            ):
              $_info_form['auto-complete'] = [
                'Type' => 'Invoice',
                'Field' => $_request['info-invoice-' . $x]
              ];
            endif;
          /* END صورتحساب */
          endif;
          /* END تکمیل خودکار بر اساس */


          $_info_form['checkbox'] = isset($_request['checkbox' . $x]) ? 1 : 0;

          if ($_request['type' . $x] == 3): //نوع شماره
            if (isset($_request['unique' . $x])) $_info_form['unique'] = 1; //یکتا
            if (isset($_request['separator' . $x])) $_info_form['separator'] = 1; //جدا کننده
          endif;

          $_form[] = $_info_form;
        endif;

        if ($_request["type" . $x] == 4 or $_request["type" . $x] == 6):
          $_form_itme = [];
          for ($x2 = 1; $x2 <= $_request['number_add_' . $x]; $x2++):
            if (isset($_request["title_" . $x . "_" . $x2])) $_form_itme[] = ["title" => $_request["title_" . $x . "_" . $x2]];
          endfor;

          $_form[] = array_merge(
            ['type' => $_request["type" . $x], 'title' => $_request["title" . $x], 'name' => $_request["name" . $x], 'checkbox' => isset($_request["checkbox" . $x]) ? 1 : 0, 'itme' => $_form_itme, 'value' => isset($_request["value"]) ? $_request["value"] : null],
            $_info_form
          );
        endif;
      endif;
    endfor;

    return $_form;
  }
  /* END اعتبارسنجی */
  /* START پر شده توسط کاربران */
  protected function pageUserForm($_id, $role = 'admin')
  {
    $_info_form = form::findOrfail($_id, ['id', 'name', 'fild', 'id_grop']);
    $form_fild = json_decode($_info_form['fild'], 1);

    $user_forms = user_form::join('users', 'users.id', 'id_user')->where('id_form', $_id);

    if ($role == 'manager') {
      /**
       * محدود کردن لیست فرم های پر شده برای کاربران زیر مجموعه هر مدیر در پنل مدیر مجموعه
       */
      $manager = manager::where("id_user", session("id_manager"))->first();
      $form_grop_ids = json_decode($_info_form['id_grop']);
      if (!is_array($form_grop_ids)) {
        $form_grop_ids = [$form_grop_ids];
      }

      if (in_array($manager->id_grop, $form_grop_ids)) {
        $user_forms = user_grop::join('user_forms', 'user_forms.id_user', 'user_id')
          ->join('users', 'users.id', 'user_grops.user_id')
          ->where('user_forms.id_form', $_id)
          ->where('user_grops.grop_id', $manager->id_grop);
      }
    }

    $user_forms = $user_forms->get(['user_forms.id', 'id_user', 'id_form', 'form', 'users.id AS ID_User', 'mobile', 'name', 'name2', 'kod', 'hash'])
      ->map(function ($row) {
        $row->form = json_decode($row->form, true) ?? [];
        return $row;
      });

    return view("$role.form.userPage", [
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user), //آدرس پوشه فایل های کاربران
      'id_form' => $_info_form['id'],
      'name_form' => $_info_form['name'],
      'user_forms' => $user_forms,
      'form_fild' => $form_fild,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      'filledOrNoneFilled' => 'Filled',
    ]);
  }

  /* START پر نشده توسط کاربران */
  protected function pageUserNoneFillForm($formId, $role = 'admin')
  {
    $formInfo = form::findOrFail($formId, ['id', 'name', 'fild', 'id_grop']);
    $formFields = json_decode($formInfo->fild, true);

    // id_grop is stored as JSON array or a plain integer — normalise to int[]
    $gropIds = json_decode($formInfo->id_grop, true);
    if (!is_array($gropIds)) {
      $gropIds = array_map('intval', explode(',', $formInfo->id_grop));
    }
    $gropIds = array_values(array_filter(array_map('intval', (array) $gropIds)));

    // Users who are in the form's groups but have not yet submitted the form
    $query = User::leftJoin('user_forms', function ($join) use ($formId) {
        $join->on('users.id', '=', 'user_forms.id_user')
             ->where('user_forms.id_form', '=', $formId);
      })
      ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
      ->whereIn('user_grops.grop_id', $gropIds)
      ->whereNull('user_forms.id');

    // Scope to the manager's own group
    if ($role === 'manager') {
      $manager = manager::where('id_user', session('id_manager'))->first();
      if ($manager) {
        $query->where('user_grops.grop_id', $manager->id_grop);
      }
    }

    $usersWithoutForm = $query
      ->select(['users.id AS ID_User', 'users.mobile', 'users.name', 'users.name2', 'users.kod', 'users.hash'])
      ->distinct()
      ->get();

    $userForms = $usersWithoutForm->map(function ($user) use ($formId) {
      return [
        'id'      => null,
        'id_user' => $user->ID_User,
        'id_form' => $formId,
        'form'    => null,
        'ID_User' => $user->ID_User,
        'mobile'  => $user->mobile,
        'name'    => $user->name,
        'name2'   => $user->name2,
        'kod'     => $user->kod,
        'hash'    => $user->hash,
      ];
    })->toArray();

    return view("$role.form.userPage", [
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user),
      'id_form'              => $formInfo->id,
      'name_form'            => $formInfo->name,
      'user_forms'           => $userForms,
      'form_fild'            => $formFields,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      'filledOrNoneFilled'   => 'NoneFilled',
    ]);
  }
  /* START ویرایش */
  protected function editUserForm($_request)
  {
    if (
      //آیدی فرم پر شده
      !isset($_request['id-user-form'])
      ||
      !is_numeric($_request['id-user-form'])
    ) return "no user id";

    $_id_user_form = (int) $_request['id-user-form'];
    $_info_user_form = (object) user_form::findOrFail($_id_user_form, ['id_user', 'id_form', 'form']); //اطلاعات
    $_id_form = $_info_user_form['id_form']; //آیدی فرم
    $_info_form = form::findOrFail($_id_form, ['fild']); //فیلدهای فرم

    $_filled_fields_to_change = array(); //فیلدهای پر شده برای تغییر
    foreach (json_decode($_info_form['fild'], true) as $_fields_form):
      if ($_fields_form['type'] == 7): //نوع فایل
        if (
          !isset($_FILES[$_fields_form['name']])
          ||
          is_array($_FILES[$_fields_form['name']]['error']) //چند فایل ارسال شده بود
          ||
          $_FILES[$_fields_form['name']]['error'] > 0 //خطا
          ||
          !in_array($_FILES[$_fields_form['name']]['type'], self::$_menu__formats_allowed_field_files) //فرمت

          ||

          //حجم
          $_FILES[$_fields_form['name']]['size'] <= 0
          ||
          $_FILES[$_fields_form['name']]['size'] > 3000000
        ) continue;

        //درج اطلاعات فایل در لیست آپلودها
        global $counter_number;
        $rand_name = generateRandomString();
        $counter_number++;
        $_name_file = $rand_name . $counter_number . substr($_FILES[$_fields_form['name']]['name'], strripos($_FILES[$_fields_form['name']]['name'], '.')); //نام
        $_info_files_for_upload[] = array(
          'Name' => $_name_file,
          'TMP Name' => $_FILES[$_fields_form['name']]['tmp_name']
        );

        $_filled_fields_to_change[] = [
          'type' => $_fields_form['type'],
          'name_itme' => $_fields_form['name'],
          $_fields_form['name'] => $_name_file
        ];

        continue;
      endif;


      if (
        isset($_request[$_fields_form['name']])
        &&
        is_string($_request[$_fields_form['name']])
        &&
        $_request[$_fields_form['name']]
      ) $_filled_fields_to_change[] = array(
        'type' => $_fields_form['type'],
        'name_itme' => $_fields_form['name'],
        $_fields_form['name'] => $_request[$_fields_form['name']]
      );

    endforeach;
    /* START آپلود فایل ها */
    if (isset($_info_files_for_upload)):
      $_id_user = $_info_user_form['id_user']; //آیدی کاربر مربوط به فرم پر شده

      foreach ($_info_files_for_upload as $_info_file_for_upload):
        //مشخصات پوشه
        $_directory = '..' . self::$_directory_files_user;
        //ساخت پوشه مربوط به کاربر
        if (!is_dir("$_directory$_id_user")) mkdir("$_directory/$_id_user");
        $_directory .= "/$_id_user";
        if (!is_dir("$_directory/Form")) mkdir("$_directory/Form");
        $_directory .= "/Form";
        if (!is_dir("$_directory/$_id_form")) mkdir("$_directory/$_id_form");
        $_directory .= "/$_id_form";

        $_full_address_file = "$_directory/" . $_info_file_for_upload['Name']; //آدرس کامل
        if (!move_uploaded_file($_info_file_for_upload['TMP Name'], $_full_address_file)):
          unlink($_directory);
          return redirect($_SERVER['HTTP_REFERER']);
        endif;
      endforeach;
    endif;
    /* END آپلود فایل ها */


    user_form::where('id', $_id_user_form)->update(array('form' => json_encode($_filled_fields_to_change)));
    $role = session()->has('admin') ? 'admin' : 'manager';
    return redirect("/$role/form/user/{$_info_user_form['id_form']}");
  }
  /* END ویرایش */
  /* END پر شده توسط کاربران */
  /* END فرم ها */


  /* START کاربر - بررسی وجود فرم اجباری در منوها */
  static function _user__get_id_last_form_required_in_menus($_id_grop)
  {
    $idGropList = $_id_grop;
    $_extra_where = "id_grop='{$idGropList}' OR JSON_CONTAINS(id_grop, '\"{$idGropList}\"') OR JSON_CONTAINS(id_grop, '[\"{$idGropList}\"]') OR JSON_CONTAINS(id_grop, '{$idGropList}')";

    // دریافت تمامی فرم‌های اجباری مربوط به گروه کاربر
    $forms = form::where('required', 1)
      ->whereRaw("($_extra_where)")
      ->orderBy('id', 'DESC')
      ->get();

    foreach ($forms as $form) {
      // بررسی اینکه آیا کاربر این فرم را قبلاً پر کرده است؟
      $isFilled = user_form::where('id_form', $form->id)
        ->where("id_user", session('id_user'))
        ->exists();

      if (!$isFilled) {
        // اول چک می‌کنیم آیا این فرم در زیرمنوهاست؟
        $under = under::where('id_form', $form->id)
          ->select('id AS ID_Under', 'id_menu AS ID_Menu_Under')
          ->first();

        if ($under) {
          return [
            'form' => $form,
            'url' => "/user/menu/{$under->ID_Menu_Under}/{$under->ID_Under}",
            'hash' => $form->hash
          ];
        }

        // اگر در زیرمنو نبود، چک می‌کنیم در منوهای اصلی هست؟
        $menu = menu::where('id_form', $form->id)
          ->select('id AS ID_Menu')
          ->first();

        if ($menu) {
          return [
            'form' => $form,
            'url' => "/user/menu/{$menu->ID_Menu}",
            'hash' => $form->hash
          ];
        }
      }
    }

    return null;
  }
  /* END کاربر - بررسی وجود فرم اجباری در منوها */


  /**
   * محاسبه جریمه و ذخیره — wrapper برای backward compat
   */
  protected function calcualte_user_fine_without_pay($id_invoice, $id_user)
  {
    (new InvoicePenaltyCalculator())->recalculate((int) $id_invoice, (int) $id_user);
  }

  /**
   * محاسبه جریمه و ذخیره — wrapper برای backward compat
   */
  protected function createOrUpdate_user_fine($id_user, $id_invoice)
  {
    (new InvoicePenaltyCalculator())->recalculate((int) $id_invoice, (int) $id_user);
  }

  /**
   * محاسبه مبلغ جریمه یک کاربر روی یک صورتحساب — wrapper برای backward compat
   * مقدار total_penalty را برمی‌گرداند
   */
  protected function user_fine_calculate($id_user, $id_invoice, $is_calculate_remaining_commitment_fine = false)
  {
    $totals = (new InvoicePenaltyCalculator())->compute((int) $id_invoice, (int) $id_user);
    return $totals['total_penalty'];
  }
}
