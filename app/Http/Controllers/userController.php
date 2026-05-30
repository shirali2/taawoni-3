<?php

namespace App\Http\Controllers;

use App\advertising_pay;
use App\crop;
use App\crop_pay;
use App\form;
use App\grop;
use App\invoice_fine;
use App\invoice_pay;
use App\issue;
use App\manager;
use App\menu;
use App\message;
use App\pay;
use App\pm;
use App\pm_show;
use App\product;
use App\product_advertising;
use App\ticket;
use App\under;
use App\user;
use App\admin;
use App\invoice as AppInvoice;
use App\setting;
use App\user_form;
use App\user_grop;
use App\usergrop;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kavenegar;

use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use App\Services\InvoicePenaltyCalculator;


class userController extends Controller
{

  private function sms($mobil, $name)
  {

    try {

      $receptor = "$mobil";
      $token = "$name";
      $token2 = "";
      $token3 = "";
      $type = "sms"; //sms | call
      $result = Kavenegar::VerifyLookup($receptor, $token, $token2, $token3, "maskanbimehiran", $type);
      if ($result) {
        foreach ($result as $r) {
          $messageid = $r->messageid;
          $message = $r->message;
          $status = $r->status;
          $statustext = $r->statustext;
          $sender = $r->sender;
          $receptor = $r->receptor;
          $date = $r->date;
          $cost = $r->cost;
        }
      }
    } catch (\Kavenegar\Exceptions\ApiException $e) {
      // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
      echo $e->errorMessage();
    } catch (\Kavenegar\Exceptions\HttpException $e) {
      // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
      echo $e->errorMessage();
    }
  }

  private static function normalizePmIds($ids)
  {
    if (!is_array($ids)) {
      return [];
    }

    $normalized = [];
    foreach ($ids as $id) {
      if ($id !== null && $id !== '') {
        $normalized[] = (string) $id;
      }
    }

    return $normalized;
  }

  private static function pmTargetsUser($idUsers, $userId, $userGropId)
  {
    $decoded = json_decode($idUsers, true);
    if (!is_array($decoded)) {
      return false;
    }

    if (array_key_exists('Grop Users', $decoded)) {
      return in_array((string) $userGropId, self::normalizePmIds((array) $decoded['Grop Users']), true);
    }

    return in_array((string) $userId, self::normalizePmIds($decoded), true);
  }

  //نمایش تیکت های خوانده نشده بصورت پاپ آپ
  public static function pm()
  {
    if (!session()->has('mobile_user')) return collect([]);
    if (!session()->has('grop_user')) return collect([]);

    $_user = user::where('mobile', session('mobile_user'))->first();
    if (!$_user) return collect([]);
    $_id_user = $_user['id']; //آیدی کاربر

    $_user_grop = user_grop::find(session('grop_user')); //اطلاعات گروه
    if (!$_user_grop) return collect([]);
    $_id_grop_user = $_user_grop['id_usergrop']; //آیدی گروه کاربری مجموعه

    //پیامی که تاریخ و ساعت خاتمه اش نگذشته باشد ، تاریخ و ساعتش پاک میشود تا به بایگانی نرود و برای کاربر نمایش داده شود
    pm::where('date_end_show', '!=', NULL)->where('date_end_show', '>=', Verta()->format('Y-m-d H:i:s'))->update(array(
      'updated_at' => Verta()->formatGregorian('Y-m-d H:i:s'),
      'date_end_show' => NULL
    ));

    return pm::where(function ($q) use ($_user_grop) {
      $q->where('id_grop', $_user_grop['grop_id'])
        ->orWhereHas('groups', function ($sq) use ($_user_grop) {
          $sq->where('grop_id', $_user_grop['grop_id']);
        });
    })->select(['id', 'title', 'img', 'text', 'created_at', 'id_users'])->whereRaw(
      '(SELECT COUNT(id) FROM pm_shows WHERE id_user=? AND id_pm=pms.id)=0',
      array($_id_user)
    )->where('date_end_show', '=', NULL)->orderBy('id', 'DESC')->get()->filter(function ($_pm) use ($_id_user, $_id_grop_user) {
      return self::pmTargetsUser($_pm['id_users'], $_id_user, $_id_grop_user);
    })->values(); //پیام های جدید کاربر
  }

  /**
   * نمایش صفحه
   * در انتظار محاسبه جریمه صورت حساب ها
   */
  public function WaitingUserFineCalc()
  {
    if (!session()->has('mobile_user')) return redirect('/');

    return View('user.waitingUserFineCalc');
  }

  /**
   * محاسبه جریمه پرداخت های ثبت شده 
   * روی تمامی صورتحساب های کاربر
   */
  public function full_fine_calc()
  {
    $result = ['success' => true, 'msg' => 'عملیات با موفقیت انجام شد'];
    try {
      $id_user = (int) session('id_user');
      $id_usergrop = (int) session('grop_user') ? user_grop::find(session('grop_user'), ['id_usergrop'])['id_usergrop'] ?? 0 : 0;

      $invoices = AppInvoice::whereRaw(
        'id_users LIKE ? OR id_users LIKE ? OR id_users LIKE ? OR id_users LIKE ? OR id_users LIKE ?',
        array(
          '[' . $id_user . ']',
          '[' . $id_user . ',%',
          '%,' . $id_user . ',%',
          '%,' . $id_user . ']',
          '%"' . $id_usergrop . '"%'
        )
      )->get();

      foreach ($invoices as $invoice) {
        $this->createOrUpdate_user_fine($id_user, (int) $invoice['id']);
      }
    } catch (\Throwable $th) {
      $result['msg'] = $th->getMessage();
      $result['success'] = false;
    }
    return response()->json($result);
  }

  public function check_user_prodact()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    if ($user["active_am"] != null) {
      $am = Verta::parse(verta()->formatDate());
      $am3 = Verta::parse($user["active_am"]);
      $number = $am->diffDays($am3);
      if ($number > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function home()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    } elseif (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    } elseif (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user_grop = user_grop::find(session('grop_user'));
    if (!$user_grop) {
      session()->forget('grop_user');
      return redirect('/user/grop');
    }
    $grop = grop::find($user_grop["grop_id"]);
    if (!$grop) {
      session()->forget('grop_user');
      return redirect('/user/grop');
    }

    return view('user.dashboard', ["grop" => $grop]);
  }

  public function grop()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    $user_grops = user_grop::where("user_id", session('id_user'))->get();
    if (count($user_grops) > 1) {
      foreach ($user_grops as $user_grop) {
        $user_grop["grop"] = grop::findOrfail($user_grop["grop_id"]);
      }
      return view('user.grop', ["user_grops" => $user_grops]);
    } else {
      $user_grop = user_grop::where("user_id", session('id_user'))->where("id", $user_grops[0]["id"])->get();
      if (count($user_grop) > 0) {
        $grop = grop::findOrfail($user_grop[0]["grop_id"]);
        session(['grop_user' => $user_grop[0]["id"]]);
        session(['logo' => $grop["img1"]]);
        if ($grop["checkboxName"]) {
          session(['name_grop' => $grop["name"]]);
        } else {
          session(['name_grop' => null]);
        }
        $_usergrop = usergrop::where("id_grop", $user_grop[0]["grop_id"])->where("id", $user_grop[0]["id_usergrop"])->first();

        session(['id_grop_user' => $user_grop[0]["grop_id"]]);
        session(['id_user_grop' => $user_grop[0]["id_usergrop"]]);
        session(['name_grop_user' => $_usergrop ? $_usergrop->name : '']);
        session(['grop_name' => $grop->name]);

        $_usergrop_id = $_usergrop ? $_usergrop["id"] : $user_grop[0]["id_usergrop"];
        $_wheres_id_user_grop = array('id_usergrop=' . $_usergrop_id);
        $_wheres_id_user_grop[] = 'id_usergrop LIKE "' . $_usergrop_id . ',%"';
        $_wheres_id_user_grop[] = 'id_usergrop LIKE "%,' . $_usergrop_id . ',%"';
        $_wheres_id_user_grop[] = 'id_usergrop LIKE "%,' . $_usergrop_id . '"';
        $_menus = menu::where("id_grop", $user_grop[0]["grop_id"])->whereRaw('(' . implode(' OR ', $_wheres_id_user_grop) . ')')->get();
        $_menu_id = [];
        foreach ($_menus as $_menu) {
          if ($_menu["under"] == 1) {
            $_menu_under_id = [];
            $unders = under::where("id_menu", $_menu["id"])->get();
            foreach ($unders as $under) {
              $_menu_under_id[] = ['id' => $under["id"], "name" => $under["name"]];
            }
            $_menu_id[] = ['id' => $_menu["id"], "name" => $_menu["name"], 'under' => 1, "menu_under_id" => $_menu_under_id];
          }
          if ($_menu["under"] == 0) {
            $_menu_id[] = ['id' => $_menu["id"], "name" => $_menu["name"], 'under' => 0];
          }
        }
        session(['menus' => $_menu_id]);
        return redirect("/user");
      } else {
        return redirect("/user");
        exit();
      }
    }
  }
  public function gropAdd(Request $request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    $user_grop = user_grop::where("user_id", session('id_user'))->where("id", $request["grop"])->get();
    if (count($user_grop) > 0) {
      $grop = grop::findOrfail($user_grop[0]["grop_id"]);
      session(['grop_user' => $user_grop[0]["id"]]);
      session(['logo' => $grop["img1"]]);

      if ($grop["checkboxName"]) {
        session(['name_grop' => $grop["name"]]);
      } else {
        session(['name_grop' => null]);
      }

      $_usergrop = usergrop::where("id_grop", $user_grop[0]["grop_id"])->where("id", $user_grop[0]["id_usergrop"])->first();

      session(['id_grop_user' => $user_grop[0]["grop_id"]]);
      session(['id_user_grop' => $user_grop[0]["id_usergrop"]]);
      session(['name_grop_user' => $_usergrop ? $_usergrop->name : '']);
      session(['grop_name' => $grop->name]);

      $_usergrop_id = $_usergrop ? $_usergrop["id"] : $user_grop[0]["id_usergrop"];
      $_wheres_id_user_grop = array('id_usergrop=' . $_usergrop_id);
      $_wheres_id_user_grop[] = 'id_usergrop LIKE "' . $_usergrop_id . ',%"';
      $_wheres_id_user_grop[] = 'id_usergrop LIKE "%,' . $_usergrop_id . ',%"';
      $_wheres_id_user_grop[] = 'id_usergrop LIKE "%,' . $_usergrop_id . '"';
      $_menus = menu::where("id_grop", $user_grop[0]['grop_id'])->whereRaw('(' . implode(' OR ', $_wheres_id_user_grop) . ')')->get();
      $_menu_id = [];
      foreach ($_menus as $_menu) {
        if ($_menu["under"] == 1) {
          $_menu_under_id = [];
          $unders = under::where("id_menu", $_menu["id"])->get();
          foreach ($unders as $under) {
            $_menu_under_id[] = ['id' => $under["id"], "name" => $under["name"]];
          }
          $_menu_id[] = ['id' => $_menu["id"], "name" => $_menu["name"], 'under' => 1, "menu_under_id" => $_menu_under_id];
        }
        if ($_menu["under"] == 0) {
          $_menu_id[] = ['id' => $_menu["id"], "name" => $_menu["name"], 'under' => 0];
        }
      }
      session(['menus' => $_menu_id]);

      return redirect("/user");
      exit();
    } else {
      return redirect("/user");
      exit();
    }
  }



  /* START منوها */
  static function get_menus()
  { //دریافت منوها با زیرمنوها
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return null;

    try {
      $_user_grop = user_grop::findOrFail(session('grop_user'), ['grop_id', 'id_usergrop']);

      $gropId  = $_user_grop['grop_id'];
      $uGropId = $_user_grop['id_usergrop'];

      // version counter allows targeted cache-busting from MenuObserver
      $dbName   = \Illuminate\Support\Facades\Config::get('database.connections.mysql.database', 'default');
      $version  = Cache::get("menu_version_{$gropId}", 1);
      $cacheKey = "menu_{$dbName}_grop_{$gropId}_ug_{$uGropId}_v{$version}";

      return Cache::remember($cacheKey, 3600, function () use ($_user_grop, $gropId, $uGropId) {

        $_wheres_id_user_grop = [
          "id_usergrop={$uGropId}",
          "id_usergrop LIKE \"{$uGropId},%\"",
          "id_usergrop LIKE \"%,{$uGropId},%\"",
          "id_usergrop LIKE \"%,{$uGropId}\"",
        ];

        $_menus_and_submenus = menu::where(function ($q) use ($gropId) {
            $q->where('id_grop', $gropId)
              ->orWhere('id_grop', 'like', "%,{$gropId},%")
              ->orWhere('id_grop', 'like', "{$gropId},%")
              ->orWhere('id_grop', 'like', "%,{$gropId}");
          })
          ->whereRaw('(' . implode(' OR ', $_wheres_id_user_grop) . ')')
          ->get(['id', 'name', 'under']);

        if (count($_menus_and_submenus) === 0) return collect([]);

        $_id_menus_has_submenu = [];
        foreach ($_menus_and_submenus as $_info_menu) {
          if ($_info_menu['under'] == 1) $_id_menus_has_submenu[] = $_info_menu['id'];
        }

        if (count($_id_menus_has_submenu) === 0) return $_menus_and_submenus;

        $_get_submenus = under::whereIn('id_menu', $_id_menus_has_submenu)->get(['id', 'name', 'id_menu']);

        if (count($_get_submenus) === 0) return $_menus_and_submenus;

        $_submenus = [];
        foreach ($_get_submenus as $_info_submenu) {
          $_submenus[$_info_submenu['id_menu']][] = [
            'ID'   => $_info_submenu['id'],
            'Name' => $_info_submenu['name'],
          ];
        }

        foreach ($_menus_and_submenus as $_info_menu) {
          $_info_menu['info_unders'] = $_submenus[$_info_menu['id']] ?? null;
        }

        return $_menus_and_submenus;
      });

    } catch (\Throwable $e) {
      \Illuminate\Support\Facades\Log::error('get_menus error: ' . $e->getMessage(), [
        'grop_user' => session('grop_user'),
        'user_id'   => session('id_user'),
        'trace'     => $e->getTraceAsString(),
      ]);
      return null;
    }
  }
  /* END منوها */


  public function menu($id)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_menu = menu::findOrfail($id);
    if ($_menu['under'] == 1) {
      return redirect("/user");
      exit();
    }
    // Parse grop IDs — stored as JSON array ("[1,2]") or comma-separated ("1,2")
    $_grop_ids_raw = $_menu["id_grop"];
    $_grop_ids = json_decode($_grop_ids_raw, true);
    if (!is_array($_grop_ids)) {
      $_grop_ids = explode(',', $_grop_ids_raw);
    }
    $_grop_ids = array_values(array_filter(array_map('intval', $_grop_ids)));

    $user_grop = user_grop::where("user_id", session('id_user'))
      ->whereIn("grop_id", $_grop_ids)
      ->where(function ($query) use ($_menu) {
        if (strstr($_menu['id_usergrop'], ',')) {
          $query->whereRaw('FIND_IN_SET(id_usergrop, ?)', [$_menu['id_usergrop']]);
        } else {
          $query->where("id_usergrop", $_menu['id_usergrop']);
        }
      })
      ->get();

    if (count($user_grop) > 0) {
      $usergrop = usergrop::where("id_grop", $user_grop[0]["grop_id"])->where("id", $user_grop[0]["id_usergrop"])->get();
      if (count($usergrop) > 0) {
        if (in_array($usergrop[0]['id'], explode(',', $_menu['id_usergrop']))) {
          if ($_menu['id_form'] != null) {
            $form = form::findOrfail($_menu['id_form']);
            $form["fild"] = json_decode($form["fild"], 1);

            $_id_user = session('id_user');
            $user_form = user_form::where("id_user", $_id_user)->where("id_form", $form["id"])->get();
            if (count($user_form) > 0) {
              $user_form = user_form::findOrfail($user_form[0]["id"]);
              $user_form["form"] = json_decode($user_form["form"], 1);
              return view('user.menuUserForm', [
                'directory_files_user' => str_replace('/public_html/', '', parent::$_directory_files_user),
                'id_user' => $_id_user,
                'menu' => $_menu,
                'form' => $form,
                'user_form' => $user_form,
              ]);
            } else {
              $_user_grop_record = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']);
              $_info_ac = $this->_get_info_auto_complete_fields($form['fild'], $_id_user, $_user_grop_record);
              return view('user.menu', [
                'menu' => $_menu,
                'form' => $form,
                'directory_files_user' => str_replace('/public_html/', '', parent::$_directory_files_user),
                'id_user' => $_id_user,
                'user_data' => user::find($_id_user),
                'info_user_forms_used' => $_info_ac['Info User Forms Used'],
                'info_invoice' => $_info_ac['Info Invoice'],
                'settings_auto_completes_form' => parent::$_settings_auto_completes_form,
                'formats_allowed_file' => parent::$_menu__formats_allowed_field_files,
              ]);
            }
          } else {
            return view('user.menu', ['menu' => $_menu]);
          }
        } else {
          return redirect("/user");
          exit();
        }
      } else {
        return redirect("/user");
        exit();
      }
    } else {
      return redirect("/user");
      exit();
    }
  }
  public function menuUnder($id, $id_under)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');
    $_menu = menu::findOrfail($id, ['id', 'id_grop', 'id_usergrop', 'id_form', 'under']);
    if ($_menu['under'] == 0) return redirect("/user");
    // dd($_menu);
    $_id_user = session('id_user'); //آیدی کاربر
    // dd(explode(',',$_menu["id_grop"]));
    $user_grop = user_grop::where("user_id", $_id_user)
      ->whereIn("grop_id", array_map('intval', explode(',', $_menu["id_grop"])))
      ->whereRaw(strstr($_menu['id_usergrop'], ',') ? 'id_usergrop IN (' . $_menu['id_usergrop'] . ')' : 'id_usergrop=' . $_menu['id_usergrop'])
      ->get();
    if (count($user_grop) > 0) {
      $usergrop = usergrop::where("id_grop", $user_grop[0]["grop_id"])->where("id", $user_grop[0]["id_usergrop"])->get();
      if (count($usergrop) > 0) {
        if (in_array($usergrop[0]["id"], explode(',', $_menu["id_usergrop"]))) {
          $_menu_under = under::findOrfail($id_under);
          if ($_menu_under["id_menu"] != $_menu["id"]) {
            dd("$_menu_under id_menu ba id yeki nist");
            exit();
          }

          if ($_menu_under['id_form'] != null) {
            $form = form::findOrfail($_menu_under['id_form']);
            $form["fild"] = json_decode($form["fild"], 1);
            // dd($form);

            $user_form = user_form::where('id_user', $_id_user)->where("id_form", $form["id"])->get();

            // die();
            if (count($user_form) > 0) {
              $user_form = user_form::findOrfail($user_form[0]["id"]);
              $user_form["form"] = json_decode($user_form["form"], 1);
              //   dd($user_form);
              return view('user.menuUserForm', [
                'directory_files_user' => str_replace('/public_html/', '', parent::$_directory_files_user), //آدرس پوشه فایل های کاربران
                'id_user' => $_id_user,
                'menu' => $_menu_under,
                'form' => $form,
                'user_form' => $user_form
              ]);
            } else {
              //  dd($form);
              $_info_auto_complete_fields = $this->_get_info_auto_complete_fields(
                $form['fild'],
                $_id_user,
                $user_grop[0] //اطلاعات گروه
              ); //اطلاعات فیلدهای تکمیل خودکار

              return view('user.menu', [
                'menu' => $_menu_under,
                'info_user_forms_used' => $_info_auto_complete_fields['Info User Forms Used'],
                'info_invoice' => $_info_auto_complete_fields['Info Invoice'],
                'settings_auto_completes_form' => parent::$_settings_auto_completes_form,
                'form' => $form,
                'formats_allowed_file' => parent::$_menu__formats_allowed_field_files
              ]);
            }
          } else {
            return view('user.menu', [
              'menu' => $_menu_under,
              'formats_allowed_file' => parent::$_menu__formats_allowed_field_files
            ]);
          }
        } else {
          return redirect("/user");
          exit();
        }
      } else {
        return redirect("/user");
        exit();
      }
    } else {
      return redirect("/user");
      exit();
    }
  }
  /* END منوها */


  /* START فرم */
  /* START دریافت اطلاعات فیلدهای تکمیل خودکار */
  function _get_info_auto_complete_fields($_fields_form, $_id_user, $_user_grop)
  {
    $_sql_forms_auto_complete = array(); //کوئری فرم های تکمیل خودکار
    $_bind_values_sql_forms_auto_complete = array(); //رمزنگاری ها

    $_has_type_invoice = false;
    foreach ($_fields_form as $fild) :
      if (
        !array_key_exists('visible', $fild)
        ||
        !array_key_exists('auto-complete', $fild)
      ) continue;

      if ($fild['auto-complete']['Type'] == 'Form') : //فرم
        $_sql_forms_auto_complete[] = '(forms.id=' . $fild['auto-complete']['ID'] . ' AND fild LIKE ? AND form LIKE ?)';
        $_bind_values_sql_forms_auto_complete[] = '%"name":"' . $fild['auto-complete']['Field'] . '"%';
        $_bind_values_sql_forms_auto_complete[] = '%"' . $fild['auto-complete']['Field'] . '":%';
      elseif ($fild['auto-complete']['Type'] == 'Invoice') : $_has_type_invoice = true;
      endif;
    endforeach;

    //اطلاعات فرم های پر شده توسط کاربر برای تکمیل خودکار فیلدها
    $_info_user_forms_used = array();

    if (!empty($_sql_forms_auto_complete)) foreach (
      form::select('forms.id', 'user_forms.form')
        ->join('user_forms', function ($join) use ($_id_user) {
          $join->on('user_forms.id_form', '=', 'forms.id')
               ->where('user_forms.id_user', '=', $_id_user);
        })
        ->whereRaw(implode(' OR ', $_sql_forms_auto_complete), $_bind_values_sql_forms_auto_complete)
        ->get() as $_form_available
    ) :
      $_decode_user_form = json_decode($_form_available['form'], true);
      if (!is_array($_decode_user_form)) continue;
      $_values_user_form_with_sort = array();

      foreach ($_decode_user_form as $_info_user_form)
        if (is_array($_info_user_form) && isset($_info_user_form['name_itme'])) {
          $_values_user_form_with_sort[$_info_user_form['name_itme']] = $_info_user_form[$_info_user_form['name_itme']] ?? '';
          $_info_user_forms_used[$_form_available['id']] = $_values_user_form_with_sort;
        }
    endforeach;

    // Bug 6: handle source_form_id cross-form prefill — fetch the source form submissions
    // even when the field is not marked visible (so the value can be pre-populated).
    $_source_form_ids = [];
    foreach ($_fields_form as $_fld) {
      if (isset($_fld['source_form_id']) && is_numeric($_fld['source_form_id'])) {
        $_source_form_ids[] = (int) $_fld['source_form_id'];
      }
    }
    if (!empty($_source_form_ids)) {
      $source_subs = user_form::where('id_user', $_id_user)
        ->whereIn('id_form', array_unique($_source_form_ids))
        ->get(['id_form', 'form']);
      foreach ($source_subs as $_source_sub) {
        $_fid = (int) $_source_sub['id_form'];
        if (isset($_info_user_forms_used[$_fid])) continue; // don't overwrite auto-complete data
        $_decoded = json_decode($_source_sub['form'], true);
        if (!is_array($_decoded)) continue;
        $_mapped = [];
        foreach ($_decoded as $_item) {
          if (is_array($_item) && isset($_item['name_itme'])) {
            $_mapped[$_item['name_itme']] = $_item[$_item['name_itme']] ?? '';
          }
        }
        $_info_user_forms_used[$_fid] = $_mapped;
      }
    }

    //اطلاعات صورتحساب
    if ($_has_type_invoice) $_info_invoice = \App\invoice::selectSub('SUM(invoices.price)', 'Price_Invoices')->selectSub("SELECT SUM(price) FROM invoice_fines WHERE id_user=$_id_user", 'Price_Penalty')-> //قیمت جریمه
      selectSub("SELECT SUM(price) FROM invoice_pays WHERE id_user=$_id_user AND active_pay=1", 'Price_Paid')-> //قیمت پرداخت شده
      where('id_grop', $_user_grop['grop_id'])->whereRaw("(id_users LIKE CONCAT('[', $_id_user, ']') OR id_users LIKE CONCAT('[', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ']') OR id_users LIKE CONCAT('%\"', " . $_user_grop['id_usergrop'] . ", '\"%'))")->first();

    return array(
      'Info User Forms Used' => $_info_user_forms_used,
      'Info Invoice' => isset($_info_invoice) ? $_info_invoice : array()
    );
  }
  /* END دریافت اطلاعات فیلدهای تکمیل خودکار */


  /* START بررسی */
  function formCheck(int $_id_under_menu, Request $_request)
  {
    $_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']); //اطلاعات گروه

    //دریافت فیلدهای فرم
    $_filled_forms_form = under::join('menus', 'menus.id', 'id_menu')->join('user_forms', 'user_forms.id_form', 'unders.id_form')->where('unders.id', $_id_under_menu)->where('id_grop', $_user_grop['grop_id'])-> //آیدی مجموعه
      where('form', '!=', '')-> //آیدی مجموعه
      whereRaw(
        '(id_usergrop=? OR id_usergrop LIKE ? OR id_usergrop LIKE ? OR id_usergrop LIKE ?)',
        array(
          $_user_grop['id_usergrop'],
          $_user_grop['id_usergrop'] . ',%',
          '%,' . $_user_grop['id_usergrop'] . ',%',
          '%,' . $_user_grop['id_usergrop']
        )
      )-> //آیدی گروه کاربری مجموعه
      get(['form']);
    if (count($_filled_forms_form) == 0) return;
    foreach ($_filled_forms_form as $_filled_form_form) : //فرم های پر شده برای این فرم
      foreach (json_decode($_filled_form_form['form'], true) as $filled_form) : //اطلاعات فرم پر شده
        if (
          $filled_form['type'] == 3 //نوع شماره
          &&
          $filled_form[$filled_form['name_itme']] == $_request['value'] //مقدار وارد شده در بین مقدارها وجود داشت
        ) :
          echo 'Available';
          exit();
        endif;
      endforeach;
    endforeach;
  }
  /* END بررسی */


  /* START ثبت */
  /* START اعتبارسنجی بر اساس کد ملی */
  function check_national_code($_national_code)
  {
    $_national_code = strval(intval($_national_code));
    if (str_split($_national_code)[0] == 0 && strlen($_national_code) != 10) return false;

    $positionNumber = 10;
    $result = 0;

    foreach (str_split(substr($_national_code, 0, strlen($_national_code) - 1)) as $number_national_code) : //تک تک اعداد بجز عدد آخر
      $result += intval($number_national_code) * $positionNumber;
      $positionNumber--;
    endforeach;

    $remain = $result % 11;
    $controllerNumber = $remain;
    if ($remain >= 2) $controllerNumber = 11 - $remain;

    return substr($_national_code, strlen($_national_code) - 1) == $controllerNumber;
  }
  /* END اعتبارسنجی بر اساس کد ملی */


  function formSave(string $_hash, Request $_request)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    //اطلاعات فرم
    $_form = form::where('hash', $_hash)->get(['id', 'fild', 'saving_disabled', 'fill_form_limitation']);
    /**
     * در صورتی که تعداد فرم های پرشده از محدودیت حداکثر تعداد مجاز بیشتر بود اجازه 
     * ثبت نمی دهد و به کاربر پیغام مناسب نمایش می دهد
     */
    if (
      $_form->first()->fill_form_limitation != null &&
      $_form->first()->user_forms->count() >= $_form->first()->fill_form_limitation
    ) {
      return redirect($_SERVER['HTTP_REFERER'])->with('message', 'امکان ثبت فرم وجود ندارد. شما با خطای محدودیت حداکثر فرم های پر شده مواجه شدید.');
    }

    if (count($_form) <= 0) {
      return redirect('/user');
      exit();
    }
    $_form = $_form[0];
    if ($_form['saving_disabled']) {
      return redirect('/user');
      exit();
    }
    $_id_form = $_form['id']; //آیدی

    $_id_user = session('id_user'); //آیدی کاربر

    if (count(user_form::where('id_user', $_id_user)->where('id_form', $_id_form)->get()) == 0) : //از قبل ارسال نشده باشد
      $_user_form = [];

      $_info_auto_complete_fields = $this->_get_info_auto_complete_fields(
        json_decode($_form['fild'], true),
        $_id_user,
        user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop'])
      );

      // Map auto-complete user field keys to actual user table columns
      $_user_obj = user::find($_id_user);
      $_user_field_col_map = [
        'mobile'            => 'mobile',
        'name'              => 'name',
        'name_2'            => 'name2',
        'national_code'     => 'kod',
        'hash'              => 'hash',
        'date_subscription' => 'date_subscription',
        'name_grop'         => null, // comes from session
      ];

      foreach (json_decode($_form['fild'], true) as $_form_field) :
        if (
          !array_key_exists('visible', $_form_field) //قابل مشاهده نبود
          ||
          !array_key_exists('editable', $_form_field) //قابل ویرایش نبود
        ) :
          if (
            !array_key_exists('editable', $_form_field) //قابل ویرایش نبود
            &&
            ($_form_field['type'] == 1 || $_form_field['type'] == 2 || $_form_field['type'] == 3)
            &&
            array_key_exists('auto-complete', $_form_field)
          ) :
            if (
              $_form_field['auto-complete']['Type'] == 'Form'
              &&
              isset($_info_auto_complete_fields['Info User Forms Used'][$_form_field['auto-complete']['ID']][$_form_field['auto-complete']['Field']])
            ) : //فرم
              $_user_form[] = [
                'type' => $_form_field['type'],
                'name_itme' => $_form_field['name'],
                $_form_field['name'] => $_info_auto_complete_fields['Info User Forms Used'][$_form_field['auto-complete']['ID']][$_form_field['auto-complete']['Field']]
              ];
            elseif ($_form_field['auto-complete']['Type'] == 'User') :
              $_ac_field = $_form_field['auto-complete']['Field'];
              if ($_ac_field === 'name_grop') {
                $_ac_value = session('name_grop_user', '');
              } elseif (isset($_user_field_col_map[$_ac_field]) && $_user_obj) {
                $_col = $_user_field_col_map[$_ac_field];
                $_ac_value = $_user_obj->{$_col} ?? '';
              } else {
                $_ac_value = session($_ac_field . '_user', '');
              }
              $_user_form[] = [
                'type'      => $_form_field['type'],
                'name_itme' => $_form_field['name'],
                $_form_field['name'] => $_ac_value,
              ];
            elseif ($_form_field['auto-complete']['Type'] == 'Invoice') :
              $price_invoices = isset($_info_auto_complete_fields['Info Invoice']['Price_Invoices']) ? (int) $_info_auto_complete_fields['Info Invoice']['Price_Invoices'] : 0;
              $price_penalty  = isset($_info_auto_complete_fields['Info Invoice']['Price_Penalty'])  ? (int) $_info_auto_complete_fields['Info Invoice']['Price_Penalty']  : 0;
              $price_paid     = isset($_info_auto_complete_fields['Info Invoice']['Price_Paid'])     ? (int) $_info_auto_complete_fields['Info Invoice']['Price_Paid']     : 0;

              if ($_form_field['auto-complete']['Field'] == 'price-invoices') : $_price_invoice_selected = $price_invoices;
              elseif ($_form_field['auto-complete']['Field'] == 'price-fines') : $_price_invoice_selected = $price_penalty;
              elseif ($_form_field['auto-complete']['Field'] == 'price-paid') : $_price_invoice_selected = $price_paid;
              elseif ($_form_field['auto-complete']['Field'] == 'final-debt-balance') : $_price_invoice_selected = $price_invoices + $price_penalty - $price_paid;
              endif;

              if (isset($_price_invoice_selected)) $_user_form[] = [
                'type' => $_form_field['type'],
                'name_itme' => $_form_field['name'],
                $_form_field['name'] => $_price_invoice_selected
              ];
            endif;
          endif;

          continue;
        endif;

        /* START فیلد اجباری بود */
        $_request_field_name = str_replace([' ', '.'], '_', $_form_field['name']);
        if ($_form_field['checkbox'] == 1) :
          if (
            !isset($_request[$_request_field_name])
            ||
            $_request[$_request_field_name] === ''
          ) :
            return redirect()->back()->with('message', 'لطفا تمامی فیلدهای اجباری را پر کنید.');
          endif;
        endif;
        /* END فیلد اجباری بود */


        if ($_form_field['type'] == 5) : //نوع انتخابی
          $_user_form[] = [
            'type' => $_form_field['type'],
            'name_itme' => $_form_field['name'],
            $_form_field['name'] => isset($_request[$_request_field_name]) ? 1 : 0
          ];
        elseif ($_form_field['type'] == 4) : //نوع لیست کشویی
          if (
            !isset($_request[$_request_field_name])
            ||
            !array_key_exists('itme', $_form_field)
          ) continue;

          foreach ($_form_field['itme'] as $_items_field) :
            if ($_items_field['title'] != $_request[$_request_field_name]) continue;

            $_user_form[] = [
              'type' => $_form_field['type'],
              'name_itme' => $_form_field['name'],
              $_form_field['name'] => $_items_field['title']
            ];
            break;
          endforeach;
        elseif ($_form_field['type'] == 7) : //نوع فایل
          if (
            !isset($_FILES[$_form_field['name']])
            ||
            $_FILES[$_form_field['name']]['type'] == ''
          ) continue;

          if (
            is_array($_FILES[$_form_field['name']]['error']) //چند فایل ارسال شده بود
            ||
            $_FILES[$_form_field['name']]['error'] > 0 //خطا
            ||
            !in_array($_FILES[$_form_field['name']]['type'], parent::$_menu__formats_allowed_field_files) //فرمت

            ||

            //حجم
            $_FILES[$_form_field['name']]['size'] <= 0
            ||
            $_FILES[$_form_field['name']]['size'] > 3000000
          ) :
            return redirect()->back()->with('message', 'خطا در بارگذاری فایل رخ داد یا حجم فایل بیش از حد مجاز است.');
          endif;

          //درج اطلاعات فایل در لیست آپلودها
          global $counter_number;
          $counter_number++;
          $_name_file = $counter_number . substr($_FILES[$_form_field['name']]['name'], strripos($_FILES[$_form_field['name']]['name'], '.')); //نام
          $_info_files_for_upload[] = array(
            'Name' => $_name_file,
            'TMP Name' => $_FILES[$_form_field['name']]['tmp_name']
          );

          $_user_form[] = [
            'type' => $_form_field['type'],
            'name_itme' => $_form_field['name'],
            $_form_field['name'] => $_name_file
          ];
        elseif ($_form_field['type'] == 8) : //نوع متن طولانی
          $_user_form[] = [
            'type' => $_form_field['type'],
            'name_itme' => $_form_field['name'],
            $_form_field['name'] => $_request[$_request_field_name],
          ];
        else :
          if ($_form_field['type'] == 1 || $_form_field['type'] == 2 || $_form_field['type'] == 3 || $_form_field['type'] == 6) : //نوع متن یا شماره یا ایمیل یا رادیویی
            /* START اعتبارسنجی بر اساس */
            if (!array_key_exists('validation-based', $_form_field)) :
              $_user_form[] = [
                'type' => $_form_field['type'],
                'name_itme' => $_form_field['name'],
                $_form_field['name'] => $_request[$_request_field_name]
              ];

              continue;
            endif;

            if (
              $_form_field['validation-based'] == 'national-code'
              &&
              !$this->check_national_code($_request[$_request_field_name])
            ) :
              return redirect()->back()->with('message', 'کد ملی وارد شده معتبر نمی‌باشد.');
            endif;

            //تاریخ
            if ($_form_field['validation-based'] == 'date') :
              try {
                  Verta::parse($_request[$_request_field_name]);
              } catch (\Exception $e) {
                  return redirect()->back()->with('message', 'تاریخ وارد شده معتبر نمی‌باشد.');
              }
            endif;
            /* END اعتبارسنجی بر اساس */

            $_user_form[] = [
              'type' => $_form_field['type'],
              'name_itme' => $_form_field['name'],
              $_form_field['name'] => $_request[$_request_field_name]
            ];
          endif;
        endif;
      endforeach;


      /* START آپلود فایل ها */
      if (!empty($_info_files_for_upload)) :
        foreach ($_info_files_for_upload as $_info_file_for_upload) :
          //مشخصات پوشه
          $_directory = str_replace('/public_html/', '', parent::$_directory_files_user);
          //ساخت پوشه مربوط به کاربر
          if (!is_dir("$_directory$_id_user")) mkdir("$_directory$_id_user");
          $_directory .= "$_id_user";
          if (!is_dir("$_directory/Form")) mkdir("$_directory/Form");
          $_directory .= "/Form/";
          if (!is_dir("$_directory$_id_form")) mkdir("$_directory$_id_form");
          $_directory .= "$_id_form/";

          $_full_address_file = $_directory . $_info_file_for_upload['Name']; //آدرس کامل
          if (!move_uploaded_file($_info_file_for_upload['TMP Name'], $_full_address_file)) :
            unlink($_directory);
            $_user_form = [];
            break;
          endif;
        endforeach;
      endif;
      /* END آپلود فایل ها */


      if (!empty($_user_form)) :
        $_save_user_form = new user_form();
        $_save_user_form->id_user = (int) $_id_user;
        $_save_user_form->id_form = (int) $_id_form;
        $_save_user_form->form = (string) json_encode($_user_form);
        $_save_user_form->save();

        try {
          $_notify_form = form::find($_id_form);
          if ($_notify_form && $_notify_form->notify_on_submit) {
            $notifyPhone = !empty($_notify_form->notify_phone)
              ? $_notify_form->notify_phone
              : optional(setting::where('name', 'sms_notify_form')->first())->value;
            if (!empty($notifyPhone)) {
              $_notify_user = user::find($_id_user);
              $smsText = 'فرم جدید ثبت شد'
                . "\nفرم: " . $_notify_form->name
                . "\nکاربر: " . (($_notify_user->full_name ?? '') ?: '')
                . "\nشناسه: " . ($_notify_user->code ?? $_notify_user->id ?? '')
                . "\nزمان: " . now()->format('H:i — Y/m/d');
              dispatch(new \App\Jobs\SendSingleSms($notifyPhone, $smsText));
            }
          }
        } catch (\Exception $e) {
          \Log::warning('Form SMS notification failed', ['error' => $e->getMessage()]);
        }
      endif;
    endif;

    return redirect($_SERVER['HTTP_REFERER']);
  }
  /* END ثبت */
  /* END فرم */


  public function ticketPage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $user_grop = user_grop::findOrfail(session('grop_user'));
    $tickets = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", array($user["id"]))->where("active", "!=", 5)->where("id_user", $user["id"])->where("id_grop", $user_grop["grop_id"])->where("id_user_grop", $user_grop["id_usergrop"])->get();
    foreach ($tickets as $ticket) {
      $ticket["am"] = verta($ticket["created_at"]);
    }

    return view('user.ticket', [
      "tickets" => $tickets,
      "statuses_ticket" => parent::$_statuses_ticket
    ]);
  }
  public function ticketAddPage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $issues = issue::all();

    return view('user.ticketAdd', ["issues" => $issues]);
  }

  public function ticketAdd(Request $_request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $user_grop = user_grop::findOrfail(session('grop_user'));

    if ($user_grop["user_id"] != $user["id"]) return redirect("/user");

    $ticket = new ticket();
    $ticket->name = $_request["name"];
    $ticket->id_grop = $user_grop['grop_id'];
    $ticket->id_user_grop = $user_grop['id_usergrop'];
    $ticket->id_user = $user["id"];
    $ticket->save();

    try {
      $notifyPhone = optional(setting::where('name', 'sms_notify_ticket')->first())->value;
      if (!empty($notifyPhone)) {
        $smsText = 'تیکت جدید ثبت شد'
          . "\nنوع: " . ($ticket->type ?? 'عمومی')
          . "\nکاربر: " . ($user->full_name ?? $user->name ?? '')
          . "\nشناسه: " . ($user->code ?? $user->id)
          . "\nزمان: " . now()->format('H:i — Y/m/d');
        dispatch(new \App\Jobs\SendSingleSms($notifyPhone, $smsText));
      }
    } catch (\Exception $e) {
      \Log::warning('Ticket SMS notification failed', ['error' => $e->getMessage()]);
    }

    $message = new message();
    $message->text = $_request['text'];
    $message->id_ticket = $ticket['id'];
    $message->id_user = $user['id']; //آیدی کاربر
    $message->type = 1;
    $message->save();

    if ($_file = $_request->file('file')) :
      $_name = $_file->getClientOriginalName();
      $number_rand = rand(10000, 99999);
      $_file->move("file_ticket", $number_rand . $_name);
      $message = new message();
      $message->file = $number_rand . $_name;
      $message->text = $_name;
      $message->id_ticket = $ticket['id'];
      $message->id_user = $user['id']; //آیدی کاربر
      $message->type = 1;
      $message->save();
    endif;

    return redirect("/user/ticket");
  }


  public function ticketMessagePage($id)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $ticket = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", array($user["id"]))->where("active", "!=", 5)->findOrfail($id);
    if ($ticket["new"] == 1) {
      $ticket->new = 0;
      $ticket->save();
    }
    $user_grop = user_grop::findOrfail(session('grop_user'));

    if ($ticket["id_user"] != $user["id"] or $ticket["id_grop"] != $user_grop["grop_id"] or $ticket["id_user_grop"] != $user_grop["id_usergrop"]) {
      return redirect('/user/grop');
      exit();
    }
    $messages = message::where("id_ticket", $ticket["id"])->orderBy('id', 'ASC')->get();
    foreach ($messages as $message) {
      $message["am"] = verta($message["created_at"]);
      if ($message["type"] == 1) {
        $message["user"] = $user;
      }
    }

    return view('user.ticketMessage', ["ticket" => $ticket, "messages" => $messages]);
  }

  public function ticketMessage($id, Request $_request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $ticket = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?) AND (active IN (1, 3, 4))", array($user["id"]))->findOrfail($id);
    $user_grop = user_grop::findOrfail(session('grop_user'));

    if ($ticket["id_user"] != $user["id"] or $ticket["id_grop"] != $user_grop["grop_id"] or $ticket["id_user_grop"] != $user_grop["id_usergrop"]) {
      return redirect('/user/ticket');
      exit();
    }
    if ($ticket["active"] == 0) return redirect("/user/ticket/message/" . $id);

    if ($_file = $_request->file('file')) :
      $_name = $_file->getClientOriginalName();
      $number_rand = rand(10000, 99999);
      $_file->move('file_ticket', $number_rand . $_name);
      $message = new message();
      $message->file = $number_rand . $_name;
      $message->text = $_name;
      $message->id_ticket = $ticket['id'];
      $message->id_user = $user['id']; //آیدی کاربر
      $message->type = 1;
      $message->save();
    endif;

    if ($_request['text'] != null) :
      $message = new message();
      $message->id_ticket = $ticket['id'];
      $message->id_user = $user['id']; //آیدی کاربر
      $message->text = $_request['text'];
      $message->type = 1;
      $message->save();
    endif;

    $ticket->active = 1; //تغییر وضعیت به فعال
    $ticket->save();

    return redirect("/user/ticket/message/" . $id);
  }


  /* START اشتراک */
  /* START نمایش لیست پکیج ها */
  public function productPage()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');

    $_user = user::where('mobile', session('mobile_user'))->first(['id', 'active_am', 'packages_encouragement_received']);
    $_user_grops = user_grop::where('id', session('grop_user'))->first(['grop_id']);

    if (!$_user || !$_user_grops) {
      session()->forget('grop_user');
      return redirect('/user/grop');
    }

    $_packages_received = $_user['packages_encouragement_received']
      ? (json_decode($_user['packages_encouragement_received'], true) ?: [])
      : [];

    try {
      $_products = product::where('status', 1)->where('id_grops', 'LIKE', '%\"' . $_user_grops['grop_id'] . '\"%')->whereRaw('package_type IN (' . ($_user['active_am'] ? '2,3' : '1,3') . ')')-> //کاربر قبلاً اشتراک داشته => نوع بسته های تمدیدی و تشویقی ... کاربر قبلاً اشتراک نداشته => نوع بسته های اولیه و تشویقی
        when(!empty($_packages_received), function ($q) use ($_packages_received) {
          $q->whereRaw('(package_type!=3 OR id NOT IN (' . implode(',', array_map('intval', $_packages_received)) . '))');
        }, function ($q) {
          $q->whereRaw('(package_type!=3 OR 1=1)');
        })-> //از قبل بسته تشویقی دریافت نشده بود
        get(['products.id', 'products.name', 'products.number_days', 'products.amount_1_day', 'products.package_type']);
    } catch (\Throwable $e) {
      \Illuminate\Support\Facades\Log::error('productPage query error: ' . $e->getMessage(), [
        'db' => config('database.connections.mysql.database'),
      ]);
      $_products = collect([]);
    }

    if ($_user['active_am'] != null) $_user['day_am'] = Verta::parse(verta()->formatDate())->diffDays(Verta::parse($_user['active_am']));

    return view('user.product', [
      'products' => $_products,
      'user' => $_user
    ]);
  }
  /* END نمایش لیست پکیج ها */


  /* START خرید یا دریافت بسته تشویقی */
  public function productPay($_id)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');

    $_user = user::where('mobile', session('mobile_user'))->first(['id', 'active_am', 'updated_at', 'packages_encouragement_received']);
    $_user_grops = user_grop::where('id', session('grop_user'))->first(['grop_id']);

    if (!$_user || !$_user_grops) {
      session()->forget('grop_user');
      return redirect('/user/grop');
    }

    $_packages_received = $_user['packages_encouragement_received']
      ? (json_decode($_user['packages_encouragement_received'], true) ?: [])
      : [];

    $_product = product::where('status', 1)->where('id_grops', 'LIKE', '%\"' . $_user_grops['grop_id'] . '\"%')->whereRaw('package_type IN (' . ($_user['active_am'] ? '2,3' : '1,3') . ')')-> //کاربر قبلاً اشتراک داشته => نوع بسته های تمدیدی و تشویقی ... کاربر قبلاً اشتراک نداشته => نوع بسته های اولیه و تشویقی
      when(!empty($_packages_received), function ($q) use ($_packages_received) {
        $q->whereRaw('(package_type!=3 OR id NOT IN (' . implode(',', array_map('intval', $_packages_received)) . '))');
      }, function ($q) {
        $q->whereRaw('(package_type!=3 OR 1=1)');
      })-> //از قبل بسته تشویقی دریافت نشده بود
      findOrfail($_id, ['id', 'number_days', 'amount_1_day', 'package_type']);

    if ($_product['package_type'] == 3 || $_product['amount_1_day'] == 0) : //نوع بسته تشویقی بود
      $_user->active_am = verta('+' . $_product['number_days'] . ' days')->formatDate();
      $_user->packages_encouragement_received = json_encode(array_merge(array_filter((array) $_user->packages_encouragement_received), array($_product['id']))); //بسته های تشویقی دریافت شده
      $_user->save();

      return redirect('/user/product');
    else :
      $_invoice = (new Invoice)->amount($_product['number_days'] * $_product['amount_1_day']);

      $_pay = new pay();
      $_pay->price = $_product['number_days'] * $_product['amount_1_day'];
      $_pay->id_user = $_user['id'];
      $_pay->id_product = $_product['id'];
      $_pay->uuid = $_invoice->getUuid();
      $_pay->save();

      $_actual_link = request()->getSchemeAndHttpHost();

      $_pay2 = Payment::callbackUrl($_actual_link . '/user/product/pay/verify/' . $_pay['id'])->purchase($_invoice, function ($_driver, $_transactionId) {
        $_vv = (array) $_driver;
        $_vv2 = (array) $_vv["\x00*\x00invoice"];
        $_pay = pay::where('uuid', $_vv2["\x00*\x00uuid"])->first();
        $_pay->transactionId = $_transactionId;
        $_pay->save();
      })->pay();

      return redirect($_pay2->getAction());
    endif;
  }
  /* END خرید یا دریافت بسته تشویقی */


  /* START ذخیره خرید */
  public function productPayVerify($_id)
  {
    $_payment = pay::findOrfail($_id, ['id', 'transactionId', 'price', 'id_user', 'id_product', 'updated_at']);
    try {
      $_receipt = Payment::amount($_payment['price'])->transactionId($_payment['transactionId'])->verify();

      // You can show payment referenceId to the user.
      $_payment->referenceId = $_receipt->getReferenceId();
      $_payment->active = 1;
      $_payment->save();

      $_user = user::findOrfail($_payment['id_user'], ['id', 'active_am', 'id_pay', 'updated_at']);
      $_user_grops = user_grop::where('id', session('grop_user'))->first(['grop_id']);

      $_product = product::where('status', 1)->where('id_grops', 'LIKE', '%\"' . $_user_grops['grop_id'] . '\"%')->whereRaw('package_type IN (' . ($_user['active_am'] ? '2,3' : '1,3') . ')')-> //کاربر قبلاً اشتراک داشته => نوع بسته های تمدیدی و تشویقی ... کاربر قبلاً اشتراک نداشته => نوع بسته های اولیه و تشویقی
        whereRaw(
          '(package_type!=3 OR id NOT IN (?))',
          $_user['packages_encouragement_received'] ? implode(',', json_decode($_user['packages_encouragement_received'], true)) : '-1'
        )-> //از قبل بسته تشویقی دریافت نشده بود
        findOrfail($_payment['id_product'], ['number_days']);
      $_user->active_am = verta('+' . $_product['number_days'] . ' days')->formatDate();
      $_user->id_pay = $_id;
      $_user->save();

      return redirect('/user/product');
    } catch (InvalidPaymentException $exception) {
      /**
              when payment is not verified, it will throw an exception.
              We can catch the exception to handle invalid payments.
              getMessage method, returns a suitable message that can be used in user interface.
       **/

      return view('user.alert', ['message' => $exception->getMessage()]);
    }
  }
  /* END ذخیره خرید */
  /* END اشتراک */


  public function cropPage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }

    $user = user::where("mobile", session('mobile_user'))->first();
    $user_grops = user_grop::where("id", session('grop_user'))->first();
    $products = crop::where("active", 1)->where("id_grop", $user_grops["grop_id"])->get();

    foreach ($products as $product) {

      $crop_pay = crop_pay::where("id_user", $user["id"])->where("id_crop", $product["id"])->where("active", 1)->get();

      if (count($crop_pay) > 0) {
        $product["pay"] = 1;
      } else {
        $product['pay'] = 0;
      }
    }


    return view('user.crop', ["products" => $products]);
  }

  public function cropPay($id)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $product = crop::findOrfail($id);

    $invoice = (new Invoice)->amount($product["price"]);

    $pay = new crop_pay();
    $pay->price = $product["price"];
    $pay->id_user = $user["id"];
    $pay->id_crop = $product["id"];
    $pay->uuid = $invoice->getUuid();
    $pay->save();


    $actual_link = request()->getSchemeAndHttpHost();


    $pay2 = Payment::callbackUrl($actual_link . "/user/crop/pay/verify/" . $pay["id"])->purchase(
      $invoice,
      function ($driver, $transactionId) {
        $vv = (array) $driver;
        $vv2 = (array) $vv["\x00*\x00invoice"];
        $pay = crop_pay::where("uuid", $vv2["\x00*\x00uuid"])->first();
        $pay->transactionId = $transactionId;
        $pay->save();
      }
    )->pay();

    return redirect($pay2->getAction());
  }

  public function cropPayVerify($id)
  {
    $payment = crop_pay::findOrfail($id);
    try {
      $receipt = Payment::amount($payment["price"])->transactionId($payment["transactionId"])->verify();

      // You can show payment referenceId to the user.
      $payment->referenceId = $receipt->getReferenceId();
      $payment->active = 1;
      $payment->save();



      return redirect("/user/crop");
    } catch (InvalidPaymentException $exception) {
      /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
       **/

      return view('user.alert', ["message" => $exception->getMessage()]);
    }
  }




















  public function settingpage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }

    $user = user::where("mobile", session('mobile_user'))->first();


    return view('user.setting', ["user" => $user]);
  }

  public function setting(Request $request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    $user = user::where("mobile", session('mobile_user'))->first();
    $user->name = $request["name"];
    $user->name2 = $request["name2"];
    $user->phone = $request["phone"];
    $user->kod = $request["kod"];
    $user->state = $request["state"];
    $user->city = $request["city"];
    $user->save();

    return redirect("/user/setting");
  }

  public function settingPassword(Request $request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }

    $user = user::where("mobile", session('mobile_user'))->first();
    $user->password = Hash::make($request["pass"]);
    $user->save();
    return redirect("/logout");
  }





















  public function advertisingListPage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }

    $user = user::where("mobile", session('mobile_user'))->first();
    $advertising_pays = advertising_pay::where("id_user", $user["id"])->get();
    foreach ($advertising_pays as $advertising_pay) {
      $advertising_pay["product"] = product_advertising::findOrfail($advertising_pay["id_product_advertising"]);
    }

    return view('user.advertisingList', ["advertising_pays" => $advertising_pays]);
  }

  public function advertisingPage()
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }


    $product_advertisings = product_advertising::where("active", 1)->get();
    foreach ($product_advertisings as $product_advertising) {
      if ($product_advertising["id"] == 25) {
        $advertising_pay = advertising_pay::where('id_product_advertising', $product_advertising["id"])->where('active_pay', 1)->where('active_show', "!=", 2)->get();
        if (count($advertising_pay) < 8) {
          $product_advertising["show"] = 1;
        } else {
          $product_advertising["show"] = 0;
        }
      } else {
        $advertising_pay = advertising_pay::where('id_product_advertising', $product_advertising["id"])->where('active_pay', 1)->where('active_show', "!=", 2)->get();
        if (count($advertising_pay) != 0) {
          if (count($advertising_pay) == 1) {
            $product_advertising["show"] = 2;
            if ($advertising_pay[0]["am_end"] != null) {
              $am = Verta::parse(verta()->formatDate());
              $am3 = Verta::parse($advertising_pay[0]["am_end"]);
              $product_advertising["number"] = $am->diffDays($am3);
            } else {
              $product_advertising["number"] = $advertising_pay[0]["day"];
            }
          }
          if (count($advertising_pay) == 2) {
            $product_advertising["show"] = 0;
          }
        } else {
          $product_advertising["show"] = 1;
        }
      }
    }

    return view('user.advertising', ["product_advertisings" => $product_advertisings]);
  }

  public function advertising(Request $request)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    if (!$request->file("file")) {
      return redirect('/');
      exit();
    }
    if (!$request["day"] > 0) {
      return redirect('/');
      exit();
    }
    $product = product_advertising::findOrfail($request["product"]);
    $user = user::where("mobile", session('mobile_user'))->first();

    $advertising_pay = new advertising_pay();
    $advertising_pay->id_user = $user["id"];
    $advertising_pay->id_product_advertising = $product["id"];
    $advertising_pay->day = $request["day"];
    $advertising_pay->price = $request["day"] * $product["price"];

    $file = $request->file("file");
    $name = $file->getClientOriginalName();
    $rond = rand(10000, 99999);
    $file->move("advertising_img", $rond . $name);
    $advertising_pay->img = $rond . $name;


    $invoice = (new Invoice)->amount($request["day"] * $product["price"]);

    $advertising_pay->uuid = $invoice->getUuid();
    $advertising_pay->save();


    $actual_link = request()->getSchemeAndHttpHost();


    $pay2 = Payment::callbackUrl($actual_link . "/user/advertising/pay/verify/" . $advertising_pay["id"])->purchase(
      $invoice,
      function ($driver, $transactionId) {
        $vv = (array) $driver;
        $vv2 = (array) $vv["\x00*\x00invoice"];
        $advertising_pay = advertising_pay::where("uuid", $vv2["\x00*\x00uuid"])->first();
        $advertising_pay->transactionId = $transactionId;
        $advertising_pay->save();
      }
    )->pay();

    return redirect($pay2->getAction());
  }

  public function advertisingPayVerify($id)
  {
    $payment = advertising_pay::findOrfail($id);
    try {
      $receipt = Payment::amount($payment["price"])->transactionId($payment["transactionId"])->verify();

      // You can show payment referenceId to the user.
      $payment->referenceId = $receipt->getReferenceId();
      $payment->active_pay = 1;
      $payment->save();


      return redirect("/user/advertising");
    } catch (InvalidPaymentException $exception) {
      /**
            when payment is not verified, it will throw an exception.
            We can catch the exception to handle invalid payments.
            getMessage method, returns a suitable message that can be used in user interface.
       **/

      return view('user.alert', ["message" => $exception->getMessage()]);
    }
  }

  public function advertisingImg(Request $request, $id)
  {
    if (!session()->has('mobile_user')) {
      return redirect('/');
      exit();
    }
    if (!session()->has('grop_user')) {
      return redirect('/user/grop');
      exit();
    }
    if (!$this->check_user_prodact()) {
      return redirect('/user/product');
      exit();
    }
    if (!$request->file("file")) {
      return redirect('/');
      exit();
    }



    $advertising_pay = advertising_pay::findOrfail($id);
    $file = $request->file("file");
    $name = $file->getClientOriginalName();
    $rond = rand(10000, 99999);
    $file->move("advertising_img", $rond . $name);
    $advertising_pay->img = $rond . $name;
    $advertising_pay->active = 0;
    $advertising_pay->save();

    return redirect('/user/advertising/list');
  }

  public static function img_advertising($id)
  {
    $product_advertising = product_advertising::findOrfail($id);
    $advertising_pays = advertising_pay::where("active", 1)->where("active_pay", 1)->where("active_show", 1)->where("id_product_advertising", $product_advertising["id"])->get();
    if ($id == 25) {
      return $advertising_pays;
    } else {
      if (count($advertising_pays) == 1) {
        return $advertising_pays[0]["img"];
      }
      if (count($advertising_pays) == 0) {
        return $product_advertising["img"];
      }
    }
  }


  /* START صندوق پیام */
  /* START لیست */
  public function pmPage()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_user = user::where('mobile', session('mobile_user'))->first();
    $_id_user = $_user['id']; //آیدی کاربر

    $_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']); //اطلاعات گروه
    $_id_grop_user = $_user_grop['id_usergrop']; //آیدی گروه کاربری مجموعه

    $_pms = pm::where(function ($q) use ($_user_grop) {
      $q->where('id_grop', $_user_grop['grop_id'])
        ->orWhereHas('groups', function ($sq) use ($_user_grop) {
          $sq->where('grop_id', $_user_grop['grop_id']);
        });
    })->where('date_end_show', '=', NULL)->select(['id', 'title', 'img', 'text', 'created_at', 'id_users'])->selectRaw(
      '(SELECT am FROM pm_shows WHERE id_user=? AND id_pm=pms.id LIMIT 1) AS View_Date',
      array($_id_user)
    )->whereRaw(
      '(SELECT COUNT(id) FROM pm_shows WHERE id_user=? AND id_pm=pms.id)>0',
      array($_id_user)
    )->orderBy('id', 'DESC')->get()->filter(function ($_pm) use ($_id_user, $_id_grop_user) {
      return self::pmTargetsUser($_pm['id_users'], $_id_user, $_id_grop_user);
    })->values();

    foreach ($_pms as $_pm) :
      $_pm['am_start'] = $_pm['created_at'] ? verta($_pm['created_at'])->format('Y/n/j H:i') : '';
      $_pm['user'] = $_user;
    endforeach;

    return view('user.pm', ['pm_user' => $_pms]);
  }
  /* END لیست */


  /* START ثبت پیام در لیست پیام های مشاهده شده کاربر */
  public function pmActive(int $_id)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');

    $_user = user::join('user_grops', 'user_id', 'users.id')->where('mobile', session('mobile_user'))->where('user_grops.id', session('grop_user'))->first(['users.id', 'name', 'name2', 'mobile', 'grop_id', 'id_usergrop']);
    if (!$_user) return redirect('/user/grop');

    $_id_user = (int) $_user['id']; //آیدی کاربر
    $_id_grop_user = (int) $_user['id_usergrop']; //آیدی گروه کاربری مجموعه

    $_pm = pm::where("id", $_id)->where(function ($q) use ($_user) {
      $q->where('id_grop', $_user['grop_id'])
        ->orWhereHas('groups', function ($sq) use ($_user) {
          $sq->where('grop_id', $_user['grop_id']);
        });
    })->where('date_end_show', '=', NULL)->first(['pms.id', 'id_users']);

    if (
      $_pm &&
      self::pmTargetsUser($_pm['id_users'], $_id_user, $_id_grop_user) &&
      !pm_show::where('id_user', $_id_user)->where('id_pm', $_id)->exists()
    ) :
      $_pm_show = new pm_show();
      $_pm_show->id_user = $_id_user; //آیدی کاربر
      $_pm_show->name_user = (string) $_user['name'] . " " . $_user['name2']; //نام و نام خانوادگی کاربر
      $_pm_show->mobile_user = (int) $_user['mobile']; //موبایل کاربر
      $_pm_show->id_pm = $_id; //آیدی پیام
      $_pm_show->am = (string) verta();
      $_pm_show->save();
    endif;

    return redirect('/');
  }
  /* END ثبت پیام در لیست پیام های مشاهده شده کاربر */
  /* END صندوق پیام */


  /* START صورتحساب */
  public function invoicePage()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_id_user = (int) session('id_user'); //آیدی کاربر
    $_info_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']);

    return view('user.invoice', [
      'invoice_user' => \App\invoice::select(['id', 'title', 'text', 'price', 'am_start', 'am_end', 'number'])->selectSub("SELECT COALESCE(total_penalty, price, 0) FROM invoice_fines WHERE id_user=$_id_user AND id_invoice=invoices.id ORDER BY id DESC LIMIT 1", 'Price_Penalty')-> //قیمت جریمه
        selectSub("SELECT SUM(price) FROM invoice_pays WHERE id_user=$_id_user AND id_invoice=invoices.id AND active_pay=1", 'Price_Paid')-> //قیمت پرداخت شده
        whereRaw("(id_users LIKE CONCAT('[', $_id_user, ']') OR id_users LIKE CONCAT('[', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ']') OR id_users LIKE CONCAT('%\"', " . $_info_user_grop['id_usergrop'] . ", '\"%'))")->where('id_grop', $_info_user_grop['grop_id'])->orderBy('id', 'DESC')->get()
    ]);
  }


  public function invoicePayPage($id)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $user = user::where("mobile", session('mobile_user'))->first();
    $_info_user_grop = user_grop::findOrfail(session('grop_user'));
    $grop = grop::findOrfail($_info_user_grop["grop_id"]);
    $corp = crop::where("name", "اطلاعات صورتحساب مالی")->where("id_grop", $_info_user_grop["grop_id"])->where("active", 1)->get();
    if (count($corp) > 0) {
      $crop_pay = crop_pay::where("id_user", $user["id"])->where("id_crop", $corp[0]["id"])->get();
      if (count($crop_pay) == 0) {
        return redirect('/user/crop');
        exit();
      }
    }

    $_id_user = session('id_user'); //آیدی کاربر

    $invoice_id = \App\invoice::findOrfail($id);
    $invoices = \App\invoice::whereRaw("(id_users LIKE CONCAT('[', $_id_user, ']') OR id_users LIKE CONCAT('[', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ']') OR id_users LIKE CONCAT('%\"', " . $_info_user_grop['id_usergrop'] . ", '\"%'))")->where('id_grop', $_info_user_grop['grop_id'])->where('id', $invoice_id['id'])->get();
    if (count($invoices) == 0) return redirect('/');

    $invoice_pays = invoice_pay::where("id_user", $user["id"])->where("id_invoice", $invoice_id["id"])->orderBy('created_at', 'DESC')->get(['id AS ID', 'price', 'text', 'referenceId', 'active_pay', 'am', 'created_at']);
    $price_active = 0;
    $price_discount = 0;
    foreach ($invoice_pays as $invoice_pay) :
      if ($invoice_pay['active_pay'] == 1) {
        if (mb_substr($invoice_pay['text'], 0, 8) == 'تخفیف > ') {
          $price_discount += $invoice_pay['price'];
        } else {
          $price_active += $invoice_pay['price'];
        }
      }
    endforeach;

    if ($invoice_id["am_end"] != null) {
      $am = Verta::parse(verta()->formatDate());
      $am3 = Verta::parse($invoice_id["am_end"]);
      $invoice_id["number_end"] = $am->diffDays($am3);

      $penalty_data = (new InvoicePenaltyCalculator())->recalculate((int) $invoice_id["id"], (int) $user["id"]);
      $invoice_id["price_fine"] = $penalty_data['total_penalty'];
    }

    return view('user.invoiceppay', [
      'grop' => $grop,
      'user' => $user,
      'price_active' => $price_active,
      'price_discount' => $price_discount,
      'invoice_user' => $invoice_id,
      'invoice_pays' => $invoice_pays
    ]);
  }


  /* START ثبت پرداخت آنلاین یا فیش */
  public function invoicePayAdd(int $_id, Request $_request)
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_link_return = '/user/invoice/pay/' . $_id; //آدرس بازگشت

    if (!isset($_request['price'])) return redirect($_link_return)->with('error', 'مبلغ وارد نشده است');
    $_price = intval(str_replace(',', '', $_request['price'])); //مبلغ درخواست شده
    if (!is_numeric($_price) || $_price <= 0) return redirect($_link_return)->with('error', 'مبلغ معتبر نیست');

    $_id_user = session('id_user'); //آیدی کاربر

    if (!isset($_request['number'])) :
      $_info_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']); //مجموعه و گروه کاربری کاربر

      $_info_invoice = \App\invoice::select('price')->selectSub("SELECT SUM(price) FROM invoice_pays WHERE id_user=$_id_user AND id_invoice=invoices.id AND active_pay=1", 'Price_Paid')-> //قیمت پرداخت شده
        whereRaw("(id_users LIKE CONCAT('[', $_id_user, ']') OR id_users LIKE CONCAT('[', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ']') OR id_users LIKE CONCAT('%\"', " . $_info_user_grop['id_usergrop'] . ", '\"%'))")->where('id_grop', $_info_user_grop['grop_id'])->findOrfail($_id);
    else :
      $_info_invoice = \App\invoice::select('price')->selectSub("SELECT SUM(price) FROM invoice_pays WHERE id_user=$_id_user AND id_invoice=invoices.id AND active_pay=1", 'Price_Paid')-> //قیمت پرداخت شده
        findOrfail($_id);
    endif;

    //اعتبارسنجی مبلغ - جریمه را live محاسبه می‌کنیم
    $_live_penalty = (new InvoicePenaltyCalculator())->compute((int) $_id, (int) $_id_user)['total_penalty'];
    $_remainder_commitment = ($_info_invoice['price'] - $_info_invoice['Price_Paid']) + $_live_penalty; //مانده تعهد
    if ($_price > $_remainder_commitment) return redirect($_link_return);

    /* START پرداخت آنلاین */
    if (!isset($_request['number'])) :
      $corp = crop::where('name', 'اطلاعات صورتحساب مالی')->where('id_grop', $_info_user_grop['grop_id'])->where('active', 1)->get();
      if (count($corp) > 0) :
        $crop_pay = crop_pay::where('id_user', $_id_user)->where('id_crop', $corp[0]['id'])->get();
        if (count($crop_pay) == 0) return redirect('/user/crop');
      endif;

      $_invoice = (new Invoice)->amount($_price);
      $_id_invoice_pay = rand(100000, 999999) . rand(100000, 999999) . rand(100000, 999999) . rand(100000, 999999); //آیدی پرداخت
      $actual_link = request()->getSchemeAndHttpHost();

      $pay2 = Payment::callbackUrl($actual_link . '/user/invoice/pay/add/verify/' . $_id_invoice_pay)->purchase($_invoice, function ($driver, $_id_transaction) use ($_id, $_price, $_id_user, $_invoice, $_id_invoice_pay) {
        $_invoice_pay = new invoice_pay();
        $_invoice_pay->id = $_id_invoice_pay;
        $_invoice_pay->id_user = (int) $_id_user;
        $_invoice_pay->id_invoice = (int) $_id;
        $_invoice_pay->price = (int) $_price;
        $_invoice_pay->text = 'واریزی';
        $_invoice_pay->transactionId = $_id_transaction;
        $_invoice_pay->uuid = (string) $_invoice->getUuid();
        $_invoice_pay->active_pay = 3;
        $_invoice_pay->save();
      })->pay();

      return redirect($pay2->getAction());
    endif;
    /* END پرداخت آنلاین */


    /* START فیش */
    if (
      //شماره فیش
      !isset($_request['number'])
      ||
      !is_numeric($_request['number'])

      ||

      //توضیحات
      !isset($_request['description'])
      ||
      empty($_request['description'])

      ||

      //تاریخ ایجاد
      !isset($_request['creation-date'])
      ||
      empty($_request['creation-date'])
    ) return redirect($_link_return);

    //اعتبارسنجی تاریخ ایجاد
    $_explode_creation_date = explode('/', $_request['creation-date']);
    if (
      count($_explode_creation_date) != 3

      ||

      //سال
      $_explode_creation_date[0] <= 1380
      ||
      $_explode_creation_date[0] >= 1430


      ||

      //ماه
      $_explode_creation_date[1] <= 0
      ||
      $_explode_creation_date[1] >= 13

      ||

      //روز
      $_explode_creation_date[2] <= 00
      ||
      $_explode_creation_date[2] >= 32
    ) return redirect($_link_return);

    /* START آپلود تصویر */
    if (
      isset($_FILES['picture'])
      &&
      $_FILES['picture']['name'] != ''
    ) :
      //اعتبارسنجی تصویر
      if (
        is_array($_FILES['picture']['error']) //چند فایل ارسال شده بود
        ||
        $_FILES['picture']['error'] > 0
        ||
        $_FILES['picture']['size'] <= 0
        ||
        $_FILES['picture']['size'] > 3000000 //3 مگابایت
        ||
        !in_array($_FILES['picture']['type'], array('image/png', 'image/jpeg', 'image/gif'))
      ) return redirect($_link_return);
      //مشخصات پوشه تصویر
      $_directory_pay = str_replace('/public_html/', '', parent::$_directory_files_user);

      //ساخت پوشه تصویر مربوط به کاربر و صورتحسابش
      if (!is_dir("$_directory_pay/$_id_user")) mkdir("$_directory_pay$_id_user", 0755, true);

      $_directory_pay .= "/$_id_user";
      if (!is_dir("$_directory_pay/Invoice")) mkdir("$_directory_pay/Invoice", 0755, true);
      $_directory_pay .= "/Invoice";
      if (!is_dir("$_directory_pay/" . $_id)) mkdir("$_directory_pay/" . $_id, 0755, true);
      $_directory_pay .= '/' . $_id;
      if (!is_dir("$_directory_pay/Pay")) mkdir("$_directory_pay/Pay", 0755, true);
      $_directory_pay .= "/Pay";


      $_full_address_picture = "$_directory_pay/" . $_request['number'] . substr($_FILES['picture']['name'], strrpos($_FILES['picture']['name'], '.')); //آدرس کامل تصویر
      if (!move_uploaded_file($_FILES['picture']['tmp_name'], $_full_address_picture)) return redirect($_link_return);
    endif;
    /* END آپلود تصویر */

    $_invoice_pay = new invoice_pay();
    $_invoice_pay->id = (int) $_request['number'];
    $_invoice_pay->id_user = (int) $_id_user;
    $_invoice_pay->id_invoice = (int) $_id;
    $_invoice_pay->price = (int) $_price;
    $_invoice_pay->text = (string) 'کاربر > ' . $_request['description'];
    $_invoice_pay->active_pay = 2;
    $_invoice_pay->am = (string) $_request['creation-date'];
    $_invoice_pay->picture = (string) isset($_full_address_picture) ? str_replace(parent::$_directory_files_user, '', $_full_address_picture) : '';
    $_invoice_pay->save();

    return redirect($_link_return)->with('message', 'فیش شما با موفقیت ثبت شد و در انتظار تایید مدیریت است.');
    /* END فیش */
  }
  /* END ثبت پرداخت آنلاین یا فیش */


  public function invoicePayAddVerify(string $_id)
  {
    $_invoice_pay = invoice_pay::findOrfail($_id);
    try {
      $_receipt = Payment::amount($_invoice_pay['price'])->transactionId($_invoice_pay['transactionId'])->verify();

      // You can show payment referenceId to the user.
      $_invoice_pay->referenceId = $_receipt->getReferenceId();
      $_invoice_pay->active_pay = 1;
      $_invoice_pay->save();

      // بعد از تایید پرداخت آنلاین، جریمه را بلافاصله بازمحاسبه کن
      (new InvoicePenaltyCalculator())->recalculate(
          (int) $_invoice_pay['id_invoice'],
          (int) $_invoice_pay['id_user']
      );

      return redirect('/user/invoice/pay/' . $_invoice_pay['id_invoice']);
    } catch (InvalidPaymentException $exception) {
      /** when payment is not verified, it will throw an exception.
          We can catch the exception to handle invalid payments.
          getMessage method, returns a suitable message that can be used in user interface.
       **/

      $_invoice_pay->active_pay = 4;
      $_invoice_pay->save();

      return view('user.alert', ['message' => $exception->getMessage()]);
    }
  }


  public function invoiceAllPage()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_user = user::where('mobile', session('mobile_user'))->first(['id', 'name', 'name2', 'hash', 'lawyer_name']);
    $_id_user = $_user['id']; //آیدی کاربر
    $_info_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']);
    $corp = crop::where("name", "اطلاعات صورتحساب مالی")->where("id_grop", $_info_user_grop['grop_id'])->where("active", 1)->get();
    if (count($corp) > 0) {
      $crop_pay = crop_pay::where("id_user", $_id_user)->where("id_crop", $corp[0]["id"])->get();
      if (count($crop_pay) == 0) return redirect('/user/crop');
    }

    $invoices = \App\invoice::select(['invoices.id', 'invoices.title', 'invoices.price', 'invoices.am_start'])
      ->leftJoin('invoice_fines', function($join) use ($_id_user) {
          $join->on('invoice_fines.id_invoice', '=', 'invoices.id')
               ->where('invoice_fines.id_user', '=', $_id_user);
      })
      ->selectRaw('COALESCE(invoice_fines.total_penalty, 0) as Price_Penalty')
      ->selectRaw('COALESCE(invoice_fines.total_paid, 0) as Price_Paid')
      ->selectRaw('COALESCE(invoice_fines.total_paid - invoice_fines.total_discount, 0) as paied_without_discount')
      ->selectRaw('COALESCE(invoice_fines.total_discount, 0) as discount_paied')
      ->where('invoices.id_grop', $_info_user_grop['grop_id'])
      ->whereRaw("(invoices.id_users LIKE CONCAT('[', $_id_user, ']') OR invoices.id_users LIKE CONCAT('[', $_id_user, ',%') OR invoices.id_users LIKE CONCAT('%,', $_id_user, ',%') OR invoices.id_users LIKE CONCAT('%,', $_id_user, ']') OR invoices.id_users LIKE CONCAT('%\"', " . $_info_user_grop['id_usergrop'] . ", '\"%'))")
      ->orderBy('invoices.id', 'DESC')
      ->get();
    return view('user.invoiceAll', [
      'user' => $_user,
      'invoices' => $invoices
    ]);
  }


  public function invoiceUserPayPage()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $user = user::where("mobile", session('mobile_user'))->first();
    $user_grop = user_grop::findOrfail(session('grop_user'));
    $corp = crop::where("name", "اطلاعات صورتحساب مالی")->where("id_grop", $user_grop["grop_id"])->where("active", 1)->get();
    if (count($corp) > 0) {
      $crop_pay = crop_pay::where("id_user", $user["id"])->where("id_crop", $corp[0]["id"])->get();
      if (count($crop_pay) == 0) {
        return redirect('/user/crop');
        exit();
      }
    }
    $invoice_pays = invoice_pay::select(['id', 'price', 'text', 'referenceId', 'active_pay', 'am', 'created_at', 'id_invoice'])
      ->where('id_user', $user['id'])
      //->where('id_invoice', $corp['id'])
      ->where('active_pay', 1)
      ->get();
    $price = 0;
    $price_discount = 0;
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay["invoice"] = \App\invoice::findOrfail($invoice_pay["id_invoice"]);
      if ($invoice_pay["am"] == null) {
        $invoice_pay["am"] = \verta($invoice_pay["created_at"]);
      }
      if (mb_substr($invoice_pay['text'], 0, 8) == 'تخفیف > ') {
        $price_discount += $invoice_pay["price"];
      } else {
        $price += $invoice_pay["price"];
      }
    }
    return view('user.invoiceUserPay', ["user" => $user, "invoice_pays" => $invoice_pays, "price" => $price, "price_discount" => $price_discount]);
  }
  /* END صورتحساب */


  public function data()
  {
    if (!session()->has('mobile_user')) return redirect('/');
    if (!session()->has('grop_user')) return redirect('/user/grop');
    if (!$this->check_user_prodact()) return redirect('/user/product');

    $_info_user = user::where("mobile", session('mobile_user'))->first();
    $_id_user = $_info_user['id']; //آیدی کاربر
    $_info_user_grop = user_grop::findOrfail(session('grop_user'), ['grop_id', 'id_usergrop']); //مجموعه و گروه کاربری کاربر

    $info_invoices = \App\invoice::selectSub('SUM(price)', 'All_Prices')-> //تمام قیمت ها
      selectSub('SELECT name FROM grops WHERE id=' . $_info_user_grop['grop_id'], 'Name_Grop')-> //نام مجموعه
      whereRaw("(id_users LIKE CONCAT('[', $_id_user, ']') OR id_users LIKE CONCAT('[', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ',%') OR id_users LIKE CONCAT('%,', $_id_user, ']') OR id_users LIKE CONCAT('%\"', " . $_info_user_grop['id_usergrop'] . ", '\"%'))")->groupBy('invoices.id')->first(); //اطلاعات صورتحساب ها

    $invoice_pays = invoice_pay::where("id_user", $_id_user)->get();

    $price = 0;
    $price_discount = 0;
    foreach ($invoice_pays as $invoice_pay) {
      if (mb_substr($invoice_pay['text'], 0, 8) == 'تخفیف > ') {
        $price_discount += $invoice_pay["price"];
      } else {
        $price += $invoice_pay["price"];
      }
    }

    $invoice_fines = invoice_fine::where("id_user", $_id_user)->get();
    $price_fine = 0;
    foreach ($invoice_fines as $invoice_fine) $price_fine += $invoice_fine["price"];

    return view('user.data', [
      'user' => $_info_user,
      'name_grop' => $info_invoices['Name_Grop'],
      'price' => ($info_invoices['All_Prices'] + $price_fine) - ($price + $price_discount),
      'price_discount' => $price_discount
    ]);
  }
}
