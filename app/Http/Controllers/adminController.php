<?php

namespace App\Http\Controllers;

use App\pm;
use App\pay;
use App\crop;
use App\form;
use App\grop;
use App\menu;
use App\user;
use App\admin;
use App\issue;
use App\under;
use Kavenegar;
use App\ticket;
use App\invoice;
use App\manager;
use App\message;
use App\pm_show;
use App\product;
use App\setting;
use App\contactu;
use App\crop_pay;
use App\usergrop;
use App\user_form;
use App\user_grop;
use App\invoice_pay;
use App\invoice_fine;
use App\advertising_pay;
use App\invoice_penalty;
use App\product_advertising;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AddNewUserRequest;
use Illuminate\Support\Facades\DB;
use App\Services\InvoicePenaltyCalculator;
use App\Services\WalletService;
use App\Wallet;
use App\WalletTransaction;


class adminController extends Controller
{
  public function __construct() {}

  private function jsonArray($value)
  {
    if ($value === null || $value === '' || $value === 'null') {
      return [];
    }

    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [];
  }

  private function accessSettingItems($setting)
  {
    return $this->jsonArray($setting ? $setting["input3"] : null);
  }

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
  function RandomString()
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
      $randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
  }





  public function home(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    return view('admin.dashboard');
  }


  public function grops(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grops = grop::all();
    return view('admin.grop', ["grops" => $grops]);
  }
  public function gropAdd(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    return view('admin.gropAdd');
  }

  public function addGrop(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $grop = new grop();
    $grop->name = $request["name"];
    $grop->am = $request["am"];
    $grop->top = $request["top"];
    $grop->bottom = $request["bottom"];
    if ($request["type"] == 2) {
      $grop->typeNumber = $request["hsab"];
    }
    if ($request["type"] == 3) {
      $grop->typeNumber = $request["card"];
    }
    $grop->type = $request["type"];
    $grop->code = rand(1000000, 9999999);

    if (isset($request["checkboxName"])) {
      $grop->checkboxName = 0;
    }
    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("grop_img", $rond . $name);
      $grop->img1 = $rond . $name;
    }
    if ($file = $request->file("img2")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("grop_img", $rond . $name);
      $grop->img2 = $rond . $name;
    }
    $grop->user_menu_access = json_encode($request["user_menu_access"]);

    $grop->save();

    $usergrop = new usergrop();
    $usergrop->name = "کاربری عادی";
    $usergrop->text = "کاربری عادی";
    $usergrop->id_grop = $grop["id"];
    $usergrop->normal = 1;
    $usergrop->save();
    return redirect("/admin/grop");
  }

  public function gropEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $default_menu_access = '[{"menu":"bills","hide":"0"},{"menu":"ticket","hide":"0"},{"menu":"advertisment","hide":"0"},{"menu":"products","hide":"0"}]';
    if (
      $grop->user_menu_access === null ||
      $grop->user_menu_access === 'null' ||
      trim($grop->user_menu_access) === '' ||
      $grop->user_menu_access == "[]"
    ) {
      $grop->user_menu_access = $default_menu_access;
    }
    $decoded = json_decode($grop->user_menu_access);
    if (!is_array($decoded) || count($decoded) < 4) {
      $grop->user_menu_access = $default_menu_access;
      $decoded = json_decode($default_menu_access);
    }
    $grop->user_menu_access = $decoded;
    return view('admin.gropEdit', ['grop' => $grop]);
  }

  public function gropEdit($id, Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $grop = grop::findOrfail($id);
    $grop->name = $request["name"];
    $grop->am = $request["am"];
    $grop->top = $request["top"];
    $grop->bottom = $request["bottom"];
    if ($request["type"] == 2) {
      $grop->typeNumber = $request["hsab"];
    }
    if ($request["type"] == 3) {
      $grop->typeNumber = $request["card"];
    }
    $grop->type = $request["type"];

    if (isset($request["checkboxName"])) {
      $grop->checkboxName = 0;
    } else {
      $grop->checkboxName = 1;
    }

    if (isset($request["prevent_user_sms"])) {
      $grop->prevent_user_sms = 1;
    } else {
      $grop->prevent_user_sms = 0;
    }
    if (isset($request["lock_user_group_usergroup"])) {
      $grop->lock_user_group_usergroup = 1;
    } else {
      $grop->lock_user_group_usergroup = 0;
    }

    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("grop_img", $rond . $name);
      $grop->img1 = $rond . $name;
    }
    if ($file = $request->file("img2")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("grop_img", $rond . $name);
      $grop->img2 = $rond . $name;
    }
    $grop->user_menu_access = json_encode($request["user_menu_access"]);
    // dd($grop->user_menu_access);

    $grop->save();
    return redirect("/admin/grop");
  }

  public function gropCodeEdit($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $grop->code = rand(1000000, 9999999);
    $grop->save();
    return redirect("/admin/grop");
  }
  public function gropActivePay($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product = grop::findOrfail($id);
    $product2 = grop::findOrfail($id);
    if ($product2["active_pay"] == 1) {
      $product->active_pay = 0;
      $product->save();
    }
    if ($product2["active_pay"] == 0) {
      $product->active_pay = 1;
      $product->save();
    }

    return redirect("/admin/grop");
  }
  public function gropDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $user_grops = user_grop::where("grop_id", $grop["id"])->get();
    if (count($user_grops) == 0) {
      $grop->delete();
    }
    return redirect("/admin/grop");
  }


  public function manager($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $managers = manager::where("id_grop", $id)->get();
    $setting = setting::where("name", "access")->first();
    $settingAccessItems = $this->accessSettingItems($setting);
    foreach ($managers as $manager) {
      $accessName = [];

      //نام کاربر
      $_get_name_user = user::where("id", $manager["id_user"])->first(['name']);
      $manager['Name User'] = optional($_get_name_user)->name ?? '';

      if ($manager["access"] != null and $manager["access"] != "null") {
        foreach ($this->jsonArray($manager["access"]) as $item => $access) {
          foreach ($settingAccessItems as $settingItem) {
            if (isset($settingItem["id"]) && $access == $settingItem["id"]) {
              $accessName[] = $settingItem["name"];
            }
          }
        }
      }
      $accessName2 = [];
      if ($manager["accessGropUser"] != null and $manager["accessGropUser"] != "null") {
        foreach ($this->jsonArray($manager["accessGropUser"]) as $item => $access) {
          $usergrop = usergrop::find($access);
          if ($usergrop) $accessName2[] = $usergrop["name"];
        }
      }
      $manager["access"] = $accessName;
      $manager["accessGropUser"] = $accessName2;
    }

    return view('admin.gropManager', ["grop" => $grop, "managers" => $managers]);
  }
  public function gropManagerAddPage($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $grop = grop::findOrfail($id);

    $users = user::all();
    $user_a = [];
    foreach ($users as $user) {
      $existing_managers = manager::where("id_grop", $id)->where("id_user", $user["id"])->get();
      if (count($existing_managers) == 0) {
        $user_a[] = $user;
      }
    }
    $setting = setting::where("name", "access")->first();
    $usergrops = usergrop::where("id_grop", $id)->get();
    $managers_list = manager::where("id_grop", $id)->get();

    return view('admin.gropManagerAdd', ["grop" => $grop, "users" => $user_a, "setting" => $setting, "managers" => $managers_list, "usergrops" => $usergrops]);
  }

  public function gropManagerAdd(Request $request, $id)
  {
    if (!session()->has('admin')) return redirect('/');

    $grop = grop::findOrfail($id);

    $manager = new manager();
    $manager->id_user = $request->input("user");
    $manager->id_grop = $grop["id"];
    $manager->access = json_encode($request->input("access", []));
    //        $manager->accessGropUser=json_encode($request["accessGropUser"]);
    $manager->save();

    return redirect("/admin/grop/manager/" . $id);
  }
  public function deleteManager($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $gropId = $manager["id_grop"];
    $manager->delete();
    return redirect("/admin/grop/manager/" . $gropId);
  }

  public function gropManagerEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $manager["user"] = user::findOrfail($manager["id_user"]);
    $grop = grop::findOrfail($manager["id_grop"]);
    $setting = setting::where("name", "access")->first();

    return view('admin.gropManagerEdit', ["manager" => $manager, "setting" => $setting, "grop" => $grop]);
  }

  public function gropManagerEdit($id, Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $manager->access = json_encode($request->input("access", []));
    $manager->save();
    return redirect("/admin/grop/manager/" . $manager["id_grop"]);
  }





  public function gropUser($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $usergrops = usergrop::where("id_grop", $id)->get();
    return view('admin.gropUser', ["grop" => $grop, "usergrops" => $usergrops]);
  }
  public function gropUserAddPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);

    return view('admin.gropUserAdd', ["grop" => $grop]);
  }
  public function gropUserAdd(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);

    $usergrop = new usergrop();
    $usergrop->name = $request["name"];
    $usergrop->text = $request["text"];
    $usergrop->id_grop = $grop["id"];
    $usergrop->save();

    return redirect("/admin/grop/user/" . $id);
  }
  public function gropUserEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $usergrop = usergrop::findOrfail($id);
    $grop = grop::findOrfail($usergrop["id_grop"]);
    return view('admin.gropUserEdit', ["grop" => $grop, "usergrop" => $usergrop]);
  }
  public function gropUserEdit(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $usergrop = usergrop::findOrfail($id);
    $usergrop->name = $request["name"];
    $usergrop->text = $request["text"];
    $usergrop->save();

    return redirect("/admin/grop/user/" . $usergrop["id_grop"]);
  }
  public function deleteGropUser($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $usergrop = usergrop::findOrfail($id);
    $gropId = $usergrop["id_grop"];

    if ($usergrop->normal == 1) {
      return redirect("/admin/grop/user/" . $gropId)->with('error', 'گروه کاربری پیش‌فرض قابل حذف نیست.');
    }

    $defaultUsergrop = usergrop::where('id_grop', $gropId)->where('normal', 1)->first();

    \DB::transaction(function () use ($usergrop, $defaultUsergrop) {
      if ($defaultUsergrop) {
        user_grop::where('id_usergrop', $usergrop->id)->update(['id_usergrop' => $defaultUsergrop->id]);
      }
      $usergrop->delete();
    });

    return redirect("/admin/grop/user/" . $gropId);
  }


  public function access($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $setting = setting::where("name", "access")->first();
    $usergrops = usergrop::where("id_grop", $id)->get();
    $grop = grop::findOrfail($id);

    return view('admin.access', ["setting" => $setting, "accessItems" => $this->accessSettingItems($setting), "usergrops" => $usergrops, "grop" => $grop]);
  }

  public function accessGropUser($idGrop, $idGropUser)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $managers = manager::where("id_grop", $idGrop)->get();
    $usergrop = usergrop::findOrfail($idGropUser);
    $grop = grop::findOrfail($idGrop);
    $managerAccess = [];
    foreach ($managers as $manager) {
      $manager["user"] = user::where("id", $manager["id_user"])->first();
      if ($manager["accessGropUser"] != "null" && $manager["accessGropUser"] != null) {
        foreach ($this->jsonArray($manager["accessGropUser"]) as $item => $access) {
          if ($access == $idGropUser) {
            $managerAccess[] = $manager;
          }
        }
      }
    }
    return view('admin.accessShow', ["managerAccess" => $managerAccess, "usergrop" => $usergrop, "grop" => $grop]);
  }

  public function accessGropUserDelete($idManagers, $idUsergrop)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($idManagers);

    if ($manager["accessGropUser"] != "null" && $manager["accessGropUser"] !== null) {
      $accessId = [];
      foreach ($this->jsonArray($manager["accessGropUser"]) as $item => $access) {
        if ($access != $idUsergrop) {
          $accessId[] = $access;
        }
      }
      if (count($accessId) == 0) {
        $manager->accessGropUser = "null";
        if (count($this->jsonArray($manager["access"])) == 0) {
          $manager->delete();
        } else {
          $manager->save();
        }
      } else {
        $manager->accessGropUser = json_encode($accessId);
        $manager->save();
      }
    }
    return redirect("/admin/grop/access/accessGropUser/" . $manager['id_grop'] . "/" . $idUsergrop);
  }


  public function accessSetting($idGrop, $idSetting)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $managers = manager::where("id_grop", $idGrop)->get();
    $setting = setting::where("name", "access")->first();
    $settingItem = ["id" => $idSetting, "name" => ""];
    foreach ($this->accessSettingItems($setting) as $item) {
      if ($item["id"] == $idSetting) {
        $settingItem = $item;
      }
    }
    $grop = grop::findOrfail($idGrop);
    $managerAccess = [];
    foreach ($managers as $manager) {
      $manager["user"] = user::where("id", $manager["id_user"])->first();
      if ($manager["access"] != "null" && $manager["access"] !== null) {
        foreach ($this->jsonArray($manager["access"]) as $item => $access) {
          if ($access == $idSetting) {
            $managerAccess[] = $manager;
          }
        }
      }
    }
    return view('admin.accessShowSetting', ["managerAccess" => $managerAccess, "setting" => $settingItem, "grop" => $grop]);
  }

  public function accessSettingDelete($idManagers, $idSetting)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($idManagers);

    if ($manager["access"] != "null" && $manager["access"] !== null) {
      $accessId = [];
      foreach ($this->jsonArray($manager["access"]) as $item => $access) {
        if ($access != $idSetting) {
          $accessId[] = $access;
        }
      }
      if (count($accessId) == 0) {
        if (count($this->jsonArray($manager["accessGropUser"])) == 0) {
          $manager->delete();
        } else {
          $manager->access = "null";
          $manager->save();
        }
      } else {
        $manager->access = json_encode($accessId);
        $manager->save();
      }
    }
    return redirect("/admin/grop/access/page/" . $manager['id_grop'] . "/" . $idSetting);
  }












  public function menuAddPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $grops = grop::all();
    $forms = form::all();

    return view('admin.menu.add', ["grops" => $grops, "forms" => $forms]);
  }

  public function menuGropUser(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($request["grop"]);
    $usergrops = usergrop::where("id_grop", $grop["id"])->get();
    echo json_encode($usergrops);
  }

  /**
   * برگشت لیست گروه های کاربری بر اساس مجموعه های ارسال شده
   */
  public function menuGropUsers(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $gropIds = array_map('intval', $request['grop']);
    //  return $gropIds;
    $usergrops = usergrop::whereIn("id_grop", $gropIds)->get();
    //  dd($usergrops->count());
    echo json_encode($usergrops);
  }
  private function menuItemsValidation($_request)
  {
    if (
      //مجموعه
      !isset($_request["grop"])
      ||
      !is_array($_request["grop"])

      ||

      //گروه صهای کاربری مجموعه


      //نام
      !isset($_request['name'])
      ||
      $_request['name'] != htmlspecialchars($_request['name'])

      ||

      //فرم
      (
        !empty($_request['form'])
        &&
        !is_numeric($_request['form'])
      )
    ) return false;

    //گروه های کاربری مجموعه انتخاب شده
    $_user_grops_selected = array_filter((array) $_request['gropUser']);
    foreach ($_user_grops_selected as $_key => $_user_grop_selected) :
      if (!is_numeric($_key) || !is_numeric($_user_grop_selected)) unset($_user_grops_selected[$_key]);
    endforeach;
    if (empty($_user_grops_selected)) return usergrop::whereIn("id_grop", $_request['grop'])->get();;

    return $_user_grops_selected;
  }

  public function menuAdd(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');

    $_user_grops_selected = $this->menuItemsValidation($_request); //گروه های کاربری مجموعه انتخاب شده
    if (!$_user_grops_selected) return redirect('/admin/menu/add');
    if (is_object($_user_grops_selected) && method_exists($_user_grops_selected, 'pluck')) {
        $_implode_user_grops_selected = $_user_grops_selected->pluck('id')->implode(',');
    } else {
        $_implode_user_grops_selected = implode(',', (array)$_user_grops_selected);
    }

    $_menu = new menu();
    $_menu->id_grop = implode(',', array_filter((array) $_request['grop'])); //آیدی های مجموعه ها
    $_menu->id_usergrop = $_implode_user_grops_selected; //گروه های کاربری مجموعه
    $_menu->name = (string) $_request['name'];
    $_menu->text = (string) htmlspecialchars($_request['text']);
    $_menu->id_form = (int) $_request['form']; //فرم
    $_menu->save();
    return redirect('/admin/menu');
  }

  public function menuPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $_menus = menu::all();

    // Collect all IDs upfront to avoid N+1
    $allGropIds     = [];
    $allUserGropIds = [];
    foreach ($_menus as $_menu) {
      foreach (array_filter(explode(',', $_menu['id_grop'])) as $id) $allGropIds[]     = (int) $id;
      $ugRaw = is_array($_menu['id_usergrop']) ? $_menu['id_usergrop'] : explode(',', $_menu['id_usergrop']);
      foreach (array_filter($ugRaw) as $id) $allUserGropIds[] = (int) $id;
    }

    $allGrops     = $allGropIds     ? grop::whereIn('id', array_unique($allGropIds))->get(['id', 'name'])->keyBy('id')         : collect();
    $allUserGrops = $allUserGropIds ? usergrop::whereIn('id', array_unique($allUserGropIds))->get(['id', 'name'])->keyBy('id') : collect();

    foreach ($_menus as $_menu) {
      $gropIds   = array_filter(explode(',', $_menu['id_grop']));
      $ugRaw     = is_array($_menu['id_usergrop']) ? $_menu['id_usergrop'] : explode(',', $_menu['id_usergrop']);

      $_menu['grop']             = collect($gropIds)->map(function ($id) use ($allGrops) {
        return $allGrops->get((int) $id);
      })->filter()->values();
      $_menu['names_user_grops'] = collect($ugRaw)->map(function ($id) use ($allUserGrops) {
        return $allUserGrops->get((int) $id);
      })->filter()->values();
    }

    return view('admin.menu.page', ['menus' => $_menus]);
  }

  public function menuEditPage(int $_id)
  {
    $_menu = menu::findOrfail($_id, ['id', 'id_usergrop', 'id_grop', 'text', 'name', 'id_form']);
    $_menu['text'] = $_menu['text'] ? htmlspecialchars_decode($_menu['text']) : ''; //توضیحات
    $idGropArray = explode(',', $_menu['id_grop']);

    return view('admin.menu.edit', [
      'menu' => $_menu,
      'grops' => grop::all(),
      'grops_selected' => strstr($_menu['id_grop'], ',') ? explode(',', $_menu['id_grop']) : array($_menu['id_grop']),

      'user_grops' => usergrop::whereIn('id_grop', $idGropArray)->get(),
      'user_grops_selected' => strstr($_menu['id_usergrop'], ',') ? explode(',', $_menu['id_usergrop']) : array($_menu['id_usergrop']), //گروه های کاربری مجموعه انتخاب شده
      'forms' => form::all()
    ]);
  }

  public function menuEdit(Request $_request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    $_user_grops_selected = $this->menuItemsValidation($_request); //گروه های کاربری مجموعه انتخاب شده
    if (!$_user_grops_selected) return redirect("/admin/menu/edit/$_id");
    if (is_object($_user_grops_selected) && method_exists($_user_grops_selected, 'pluck')) {
        $_implode_user_grops_selected = $_user_grops_selected->pluck('id')->implode(',');
    } else {
        $_implode_user_grops_selected = implode(',', (array)$_user_grops_selected);
    }
    $_grops = $_request['grop'];
    $_menu = menu::findOrfail($_id);


    $_menu->id_grop = implode(',', array_filter((array) $_request['grop'])); //آیدی های مجموعه ها
    $_menu->id_usergrop = $_implode_user_grops_selected; //گروه های کاربری مجموعه
    $_menu->name = (string) $_request['name'];
    $_menu->text = (string) htmlspecialchars($_request['text']);
    $_menu->id_form = (int) $_request['form']; //فرم
    $_menu->save();

    return redirect("/admin/menu/edit/$_id");
  }

  public function menuDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    $menu->delete();
    return redirect("/admin/menu/");
  }




  public function menuUnderPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    $unders = under::where("id_menu", $id)->get();
    return view('admin.menu.under.page', ["menu" => $menu, "unders" => $unders]);
  }
  public function menuUnderAddPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    $forms = form::all();
    return view('admin.menu.under.add', ["menu" => $menu, "forms" => $forms]);
  }
  public function menuUnderAdd(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    if ($menu["under"] == 0) {
      $menu->under = 1;
      $menu->save();
    }
    $under = new under();
    $under->name = $request["name"];
    $under->text = $request["text"];
    $under->id_form = $request["form"];
    $under->id_menu = $menu["id"];
    $under->save();
    return redirect("/admin/menu/under/" . $menu["id"]);
  }
  public function menuUnderEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $under = under::findOrfail($id);
    $forms = form::all();
    return view('admin.menu.under.edit', ["under" => $under, "forms" => $forms]);
  }
  public function menuUnderEdit(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $under = under::findOrfail($id);
    $under->name = $request["name"];
    $under->text = $request["text"];
    $under->id_form = $request["form"];
    $under->save();
    return redirect("/admin/menu/under/" . $under["id_menu"]);
  }
  public function underDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $under = under::findOrfail($id);
    $menu_id = $under['id_menu'];
    $under->delete();
    $unders = under::where("id_menu", $menu_id)->get();
    if (count($unders) == 0) {
      $menu = menu::findOrfail($menu_id);
      $menu->under = 0;
      $menu->save();
    }
    return redirect("/admin/menu/under/" . $menu_id);
  }










   public function userPage()
   {
     if (!session()->has('admin')) {
       return redirect('/');
       exit();
     }

     $showHidden = request()->has('show_hidden');
     $hasHiddenCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_hidden');
     $userColumns = ['users.id', 'users.hash', 'users.name', 'users.name2', 'users.kod', 'users.lawyer_name', 'users.mobile', 'users.phone', 'users.state', 'users.city', 'users.active_am', 'users.inactive', 'users.password'];
     if ($hasHiddenCol) {
         $userColumns[] = 'users.is_hidden';
     }
     $gropId = request('grop_id');
     $users = user::with(['user_grops.usergrop'])
        ->leftJoin('invoice_fines', 'invoice_fines.id_user', '=', 'users.id')
        ->leftJoin('invoices', 'invoices.id', '=', 'invoice_fines.id_invoice')
        ->select($userColumns)
        ->selectRaw('SUM(COALESCE(invoices.price, 0)) as total_invoices')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_penalty, 0)) as total_penalties')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_paid, 0)) as total_paid')
        ->when($hasHiddenCol && !$showHidden, function ($q) {
          return $q->where('users.is_hidden', false);
        })
        ->when($gropId, function ($q) use ($gropId) {
          return $q->whereHas('user_grops', function ($sq) use ($gropId) {
            return $sq->where('grop_id', $gropId);
          });
        })
        ->groupBy($userColumns)
        ->orderBy('users.id', 'asc')
        ->get();

     $grop = user_grop::orderBy('user_id', 'asc')->get(['user_id', 'grop_id']);
     $grops = grop::orderBy('name')->get(['id', 'name']);

     return view('admin.user.page', ["users" => $users, "grop" => $grop, "grops" => $grops, "selected_grop_id" => $gropId]);
   }
  public function userAdd()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grops = grop::all();
    $usergrops = usergrop::all();
    $prevent_user_nationalid_required_unique = setting::where("name", "prevent_user_nationalid_required_unique")->first();

    return view('admin.user.add', ["grops" => $grops, "usergrops" => $usergrops, "prevent_user_nationalid_required_unique" => $prevent_user_nationalid_required_unique]);
  }

  public function user(AddNewUserRequest $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = new user();
    $user->mobile = $request["mobile"];
    $user->name = $request["name"];
    $user->name2 = $request["name2"];
    $user->phone = $request["phone"] ?: null;
    $user->kod = $request["kod"] ?: null;
    $user->hash = $request["hash"] ?: null;
    $user->lawyer_name = $request["lawyer_name"] ?: null;
    $user->state = $request["state"];
    $user->city = $request["city"];
    $user->save();

    $grop = grop::findOrfail($request["grop"]);
    // بصورت اتوماتیک گروه کاربری را 
    // "کاربر عادی"
    // در نظر می گرفت
    // $usergrop=usergrop::where("id_grop",$grop["id"])->where("normal",1)->first();

    $user_grop = new user_grop();
    $user_grop->user_id = $user["id"];
    $user_grop->grop_id = $grop["id"];
    // گروه کاربر بر اساس مورد انتخاب شده از کمبوباکس واکشی می شود
    $user_grop->id_usergrop = $request["user_grop_id"];
    $user_grop->save();

    return redirect("/admin/user/");
  }

  public function userEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    return view('admin.user.edit', ["user" => $user]);
  }
  public function userEdit(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $preventKodRequired = setting::where('name', 'prevent_user_nationalid_required_unique')->first();
    $kodRule = ($preventKodRequired && $preventKodRequired->input1)
      ? 'nullable|digits:10|unique:users,kod,' . $id
      : 'required|digits:10|unique:users,kod,' . $id;

    $request->validate([
      'mobile' => ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile,' . $id],
      'phone'  => 'nullable|unique:users,phone,' . $id,
      'hash'   => 'required|unique:users,hash,' . $id,
      'kod'    => $kodRule,
    ], [
      'mobile.unique'  => 'این شماره موبایل قبلاً ثبت شده است',
      'mobile.regex'   => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد',
      'phone.unique'   => 'این شماره تلفن قبلاً ثبت شده است',
      'hash.required'  => 'کد اختصاصی الزامی است',
      'hash.unique'    => 'این کد اختصاصی قبلاً ثبت شده است',
      'kod.required'   => 'کد ملی الزامی است',
      'kod.digits'     => 'کد ملی باید دقیقاً ۱۰ رقم باشد',
      'kod.unique'     => 'این کد ملی قبلاً ثبت شده است',
    ]);
    $user = user::findOrfail($id);
    $user->mobile = $request["mobile"];
    $user->name = $request["name"];
    $user->name2 = $request["name2"];
    $user->phone = $request["phone"] ?: null;
    $user->kod = $request["kod"] ?: null;
    $user->hash = $request["hash"] ?: null;
    $user->lawyer_name = $request["lawyer_name"] ?: null;
    $user->state = $request["state"];
    $user->city = $request["city"];
    $user->active_am = $request["active_am"];
    $user->save();

    return redirect("/admin/user/");
  }
  public function checkRegisterEdit(Request $request)
  {
    $found = user::withoutGlobalScopes()->where("mobile", $request->mobile)->where("id", "!=", $request->id)->exists();
    echo $found ? "ok" : "no";
  }
  /**
   * حذف تکی کاربر
   */
  public function userDelete($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id);
    if (isset($user)) {
      $user->delete();
    } else {
      return redirect('/');
      exit();
    }

    return redirect('/admin/user');
  }
  /**
   * فعال/غیرفعل کردن تکی کاربر
   */
  public function userInactive($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id);
    if (isset($user)) {
      $user->inactive = !$user->inactive;
      $user->save();
    } else {
      return redirect('/');
      exit();
    }

    return redirect('/admin/user');
  }
  public function userToggleHidden($id)
  {
    if (!session()->has('admin')) return redirect('/');
    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_hidden')) return back();
    $user = user::withoutGlobalScopes()->findOrFail($id);
    $user->is_hidden = !$user->is_hidden;
    $user->save();
    return back();
  }

  public function userAddXlsxPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grops = grop::all();
    $usergrops = usergrop::all();

    return view('admin.user.addXlsx', compact(['grops', "usergrops"]));
  }
  public function userAddXlsx(Request $request)
  {
    if (!session()->has('admin')) return redirect('/');
    $file = json_decode($request["file"], true);
    $grop = grop::findOrfail($request["grop"]);
    // بصورت اتوماتیک گروه کاربری را 
    // "کاربر عادی"
    // در نظر می گرفت
    // $usergrop = usergrop::where("id_grop", $grop["id"])->where("normal", 1)->first();
    foreach ($file["user"] as $user2) :
      if (isset($user2["mobile"])) {
        $user = User::where("mobile", $user2["mobile"])->get();

        if (count($user) == 0) {
          $user = new user();
          $user->mobile = strval($user2["mobile"]);
          if (isset($user2["name"])) {
            $user->name = $user2["name"];
          }
          if (isset($user2["family"]) || isset($user2["last"])) {
            $user->name2 = $user2["family"] ?? $user2["last"];
          }
          if (isset($user2["phone"])) {
            $user->phone = $user2["phone"] ?: null;
          }
          if (isset($user2["national_code"]) || isset($user2["code"])) {
            $user->kod = ($user2["national_code"] ?? $user2["code"]) ?: null;
          }
          if (isset($user2["special_code"]) || isset($user2["hash"])) {
            $user->hash = ($user2["special_code"] ?? $user2["hash"]) ?: null;
          }
          if (isset($user2["lawyer_name"])) {
            $user->lawyer_name = $user2["lawyer_name"] ?: null;
          }
          if (isset($user2["state"]) || isset($user2["stash"])) {
            $user->state = $user2["state"] ?? $user2["stash"];
          }
          if (isset($user2["city"])) {
            $user->city = $user2["city"];
          }
          $user->save();


          $user_grop = new user_grop();
          $user_grop->user_id = $user["id"];
          $user_grop->grop_id = $grop["id"];
          // گروه کاربر بر اساس مورد انتخاب شده از کمبوباکس واکشی می شود
          $user_grop->id_usergrop = $request["user_grop_id"];
          $user_grop->save();
        }
      }
    endforeach;

    echo 100;
  }


  public function userPayPage($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id);
    $pays = pay::where('id_user', $user['id'])->where('active', 1)->get();
    foreach ($pays as $pay) :
      $pay['product'] = product::where('id', $pay['id_product'])->get();
      $pay['am'] = verta($pay['updated_at']);
    endforeach;

    return view('admin.user.userPay', [
      'user' => $user,
      'pays' => $pays
    ]);
  }

  public function userForgotPassword($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id);
    $user->password = null;
    $user->save();

    return redirect('/admin/user');
  }


  public function userDataPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_user = user::findOrfail($_id);
    $_user_grop = user_grop::where('user_id', $_user['id'])->first(['grop_id', 'id_usergrop']);

    $_sum_price_invoices_user = 0; //جمع تمام قیمت ها
    foreach (
      invoice::whereRaw(
        'id_users LIKE ? OR id_users= ? OR id_users LIKE ? OR id_users LIKE ? OR id_users LIKE ?',
        array(
          '%"' . (isset($_user_grop['id_usergrop']) ? $_user_grop['id_usergrop'] : 'Without User Grop') . '"%',
          '[' . $_user['id'] . ']',
          '%,' . $_user['id'] . ',%',
          '%[' . $_user['id'] . ',%',
          '%,' . $_user['id'] . ']%'
        )
      )->get(['price']) as $_info_invoice_user
    ) $_sum_price_invoices_user += $_info_invoice_user['price'];

    $price = 0;
    foreach (invoice_pay::where('id_user', $_user['id'])->get(['price']) as $invoice_pay) $price += $invoice_pay['price'];

    $price_fine = 0;
    foreach (invoice_fine::where('id_user', $_user['id'])->get(['price']) as $invoice_fine) $price_fine += $invoice_fine['price'];

    return view('admin.user.data', [
      'user' => $_user,
      'grop' => !empty($_user_grop) ? grop::findOrfail($_user_grop['grop_id'], ['name']) : array(),
      'price' => ($_sum_price_invoices_user + $price_fine) - $price
    ]);
  }

  public function userDataEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    return view('admin.user.dataEdit', ["user" => $user]);
  }

  public function userDataEdit($id, Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    $user->gender = $request["gender"];
    $user->marriage = $request["marriage"];
    $user->birth = $request["birth"];
    $user->father = $request["father"];
    $user->mail = $request["mail"];
    $user->number_vahed = $request["number_vahed"];
    $user->location = $request["location"];
    $user->number_block = $request["number_block"];
    $user->number_block2 = $request["number_block2"];
    $user->warehouse = $request["warehouse"];
    $user->parking = $request["parking"];
    $user->form_j = $request["form_j"];
    $user->contract = $request["contract"];
    $user->am_start_contract = $request["am_start_contract"];
    $user->am_end_contract = $request["am_end_contract"];
    $user->unit = $request["unit"];
    $user->namename2 = $request["namename2"];
    $user->kodkod2 = $request["kodkod2"];
    $user->numbernumber2 = $request["numbernumber2"];
    $user->significant = $request["significant"];
    $user->theory = $request["theory"];
    $user->tashati = $request["tashati"];
    $user->other = $request["other"];
    $user->save();
    return redirect('/admin/user/data/' . $user["id"]);
  }

  public function userGropPage($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id);
    $user_grops = user_grop::where("user_id", $user["id"])->get();
    foreach ($user_grops as $user_grop) :
      $user_grop["grop"] = grop::findOrfail($user_grop["grop_id"]);
      $user_grop["usergrop"] = usergrop::where('id', $user_grop["id_usergrop"])->get();
      $user_grop["am"] = verta($user_grop["created_at"]);
    endforeach;

    return view('admin.user.grop', ["user" => $user, "user_grops" => $user_grops]);
  }

  public function userGropDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user_grop = user_grop::findOrfail($id);
    $user_id = $user_grop["user_id"];
    if ($user_grop->grop->lock_user_group_usergroup) {
      return redirect('/admin/user/grop/' . $user_id)->withInput(['message' => 'تغییر تنظیمات مجموعه و گروه کاربری برای مجموعه این کاربر قفل شده است!']);
    } else {
      $user_grop->delete();
    }

    return redirect('/admin/user/grop/' . $user_id);
  }

  public function userGropAddPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user_grop = $this->check_user_group_is_lock($id);
    if ($user_grop) {
      $lock_user_group_usergroup = $user_grop->grop->lock_user_group_usergroup;
      return view('admin.user.gropAdd', ["lock_user_group_usergroup" => $lock_user_group_usergroup]);
    }

    $user = user::findOrfail($id);
    $grops = grop::all();

    return view('admin.user.gropAdd', ["user" => $user, "grops" => $grops]);
  }
  private function check_user_group_is_lock($id)
  {
    $user_grop = user_grop::where('user_id', $id)
      ->whereHas('grop', function ($query) {
        $query = $query->where('lock_user_group_usergroup', true);
      })
      ->orderBy('id', 'desc')->first();

    return $user_grop;
  }

  public function userGropAdd($id, Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    $grop = grop::findOrfail($request["grop"]);
    $usergrop = usergrop::findOrfail($request["gropUser"]);
    if ($usergrop["id_grop"] == $grop["id"]) {
      $user_grop = new user_grop();
      $user_grop->user_id = $user["id"];
      $user_grop->grop_id = $grop["id"];
      $user_grop->id_usergrop = $usergrop["id"];
      $user_grop->save();
      return redirect('/admin/user/grop/' . $user["id"]);
    } else {
      return redirect('/');
      exit();
    }
  }

  public function userChangeGrop(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    foreach ($request["ids"] as $id) {
      user_grop::where("user_id", $id)->update([
        'grop_id' => $request['selectedGroupId'],
        'id_usergrop' => $request['selectedUserGroupId']
      ]);
    }
    exit();
  }















  public function userInvoicePage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    $invoice_pays = invoice_pay::where("id_user", $user["id"])->get();

    $price = 0;
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay["invoice"] = invoice::findOrfail($invoice_pay["id_invoice"]);
      if ($invoice_pay["am"] == null) {
        $invoice_pay["am"] = \verta($invoice_pay["created_at"]);
      }
      $price += $invoice_pay["price"];
    }

    return view('admin.user.invoice', ["user" => $user, "invoice_pays" => $invoice_pays, "price" => $price]);
  }






  public function settingPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $register_pass = setting::where("name", "register_pass")->first();
    $code_grop = setting::where("name", "code_grop")->first();
    $code_grop_none = setting::where("name", "code_grop_none")->first();
    $name_register = setting::where("name", "name_register")->first();
    $name_login = setting::where("name", "name_login")->first();
    $contactu = setting::where("name", "contactu")->first();
    $description = setting::where("name", "description")->first();
    $img_login = setting::where("name", "img_login")->first();
    $img_register = setting::where("name", "img_register")->first();
    $mobile_contactu = setting::where("name", "mobile_contactu")->first();
    $text_contactu = setting::where("name", "text_contactu")->first();
    $prevent_users_login = setting::where('name', "prevent_users_login")->firstOrNew([]);
    $hide_register_link = setting::where('name', "hide_register_link")->firstOrNew([]);
    $prevent_user_nationalid_required_unique = setting::where('name', "prevent_user_nationalid_required_unique")->firstOrNew([]);
    $hide_mainpage_contactus_link = setting::where('name', "hide_mainpage_contactus_link")->firstOrNew([]);
    $unique_user = setting::where('name', "unique_login")->firstOrNew([]);

    return view(
      'admin.setting',
      [
        "mobile_contactu" => $mobile_contactu,
        "text_contactu" => $text_contactu,
        "img_login" => $img_login,
        "img_register" => $img_register,
        "description" => $description,
        "contactu" => $contactu,
        "name_login" => $name_login,
        "name_register" => $name_register,
        "site_title" => \App\setting::where("name", "site_title")->firstOrNew(["name" => "site_title", "input1" => "سامانه تعاونیهای مسکن بیمه ایران"]),
        "register_pass" => $register_pass,
        "code_grop" => $code_grop,
        "code_grop_none" => $code_grop_none,
        "prevent_user_login" => $prevent_users_login,
        "hide_register_link" => $hide_register_link,
        "prevent_user_nationalid_required_unique" => $prevent_user_nationalid_required_unique,
        "hide_mainpage_contactus_link" => $hide_mainpage_contactus_link,
        "unique_login" => $unique_user
      ],
    );
  }

  public function setting(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $register_pass = setting::where("name", "register_pass")->first();
    if (isset($request['register_pass'])) {
      $register_pass->input1 = 0;
      $register_pass->save();
    } else {
      $register_pass->input1 = 1;
      $register_pass->save();
    }
    $code_grop = setting::where("name", "code_grop")->first();
    $code_grop->input1 = $request['code_grop'];
    $code_grop->save();
    $code_grop_none = setting::where("name", "code_grop_none")->first();
    if (isset($request['code_grop_none'])) {
      $code_grop_none->input1 = 1;
      $code_grop_none->input2 = $request['code_grop_none_code'];
      $code_grop_none->save();
    } else {
      $code_grop_none->input1 = 0;
      $code_grop_none->input2 = null;
      $code_grop_none->save();
    }


    $name_login = setting::where("name", "name_login")->first();
    $name_login->input1 = $request['name_login'];
    $name_login->save();

    $name_register = setting::where("name", "name_register")->first();
    $name_register->input1 = $request['name_register'];
    $name_register->save();

    $site_title = setting::firstOrNew(["name" => "site_title"]);
    $site_title->input1 = $request['site_title'];
    $site_title->save();


    $description = setting::where("name", "description")->first();
    $description->input3 = $request["description"];
    $description->save();


    if ($file = $request->file("img_login")) {
      $img_login = setting::where("name", "img_login")->first();
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("img_login_register", $rond . $name);
      $img_login->input1 = $rond . $name;
      $img_login->save();
    }
    if ($file2 = $request->file("img_register")) {
      $img_register = setting::where("name", "img_register")->first();
      $name = $file2->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file2->move("img_login_register", $rond . $name);
      $img_register->input1 = $rond . $name;
      $img_register->save();
    }

    if (isset($request['unique_login'])) {
      $unique_login = setting::where("name", "unique_login")->first();
      $unique_login->input1 = $request['unique_login'];
      $unique_login->save();
    }

    $mobile_contactu = setting::where("name", "mobile_contactu")->first();
    $mobile_contactu->input1 = $request["mobile_contactu"];
    $mobile_contactu->save();

    $text_contactu = setting::where("name", "text_contactu")->first();
    $text_contactu->input3 = $request["text_contactu"];
    $text_contactu->save();

    /// تیک جلوگیری از لاگین کاربران عادی
    $prevent_users_login = setting::where("name", "prevent_users_login")->first();
    if (!$prevent_users_login) {
      $prevent_users_login = new setting();
      $prevent_users_login->name = "prevent_users_login";
    }
    if (isset($request['prevent_users_login'])) {
      $prevent_users_login->input1 = 1;
      $prevent_users_login->save();
    } else {
      $prevent_users_login->input1 = 0;
      $prevent_users_login->save();
    }

    /// تیک مخفی کردن لینک ثبت نام در صفحه ورود کاربر و ادمین
    $hide_register_link = setting::where("name", "hide_register_link")->first();
    if (!$hide_register_link) {
      $hide_register_link = new setting();
      $hide_register_link->name = "hide_register_link";
    }
    if (isset($request['hide_register_link'])) {
      $hide_register_link->input1 = 1;
      $hide_register_link->save();
    } else {
      $hide_register_link->input1 = 0;
      $hide_register_link->save();
    }

    /// تیک اجباری/یکتا نبودن فیلد کد ملی برای تعریف کاربر
    $prevent_user_nationalid_required_unique = setting::where("name", "prevent_user_nationalid_required_unique")->first();
    if (!$prevent_user_nationalid_required_unique) {
      $prevent_user_nationalid_required_unique = new setting();
      $prevent_user_nationalid_required_unique->name = "prevent_user_nationalid_required_unique";
    }
    if (isset($request['prevent_user_nationalid_required_unique'])) {
      $prevent_user_nationalid_required_unique->input1 = 1;
      $prevent_user_nationalid_required_unique->save();
    } else {
      $prevent_user_nationalid_required_unique->input1 = 0;
      $prevent_user_nationalid_required_unique->save();
    }

    /// تیک مخفی شدن لینک "تماس با ما" در صفحه اول سایت
    $hide_mainpage_contactus_link = setting::where("name", "hide_mainpage_contactus_link")->first();
    if (!$hide_mainpage_contactus_link) {
      $hide_mainpage_contactus_link = new setting();
      $hide_mainpage_contactus_link->name = "hide_mainpage_contactus_link";
    }
    if (isset($request['hide_mainpage_contactus_link'])) {
      $hide_mainpage_contactus_link->input1 = 1;
      $hide_mainpage_contactus_link->save();
    } else {
      $hide_mainpage_contactus_link->input1 = 0;
      $hide_mainpage_contactus_link->save();
    }

    return redirect("/admin/setting");
  }


  /* START فرم ها */
  public function formPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $_forms = form::all(['id', 'name', 'text', 'hash', 'id_grop']);
    foreach ($_forms as $_form) :
      //دریافت نام مجموعه ها
      $name_grops = array();
      $_form['user_forms_count'] = $_form->user_forms()->count(); // تعداد فرمهای که توسط کاربران پر شده است
      foreach (grop::whereRaw('id' . (!strstr($_form['id_grop'], '[') ? '=' . $_form['id_grop'] : ' IN (' . implode(',', json_decode($_form['id_grop'], true)) . ')'))->get(['name']) as $name_grop) $name_grops[] = $name_grop['name'];
      $_form['name_grops'] = $name_grops;
    endforeach;

    return view('admin.form.page', ['forms' => $_forms]);
  }
  /* START ایجاد */
  public function formAddPage()
  {
    if (!session()->has('admin')) return redirect('/');

    return view('admin.form.add', [
      'types_fields_form' => parent::$_types_fields_form, //فرمت های فیلد
      'items_validation' => parent::$_validations_form,
      'items_auto_complete' => parent::$_auto_completes_form,
      'settings_auto_completes_form' => parent::$_settings_auto_completes_form,
      'grops' => grop::all(['id', 'name'])
    ]);
  }
  /* START ذخیره */
  public function formAdd(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');

    $_new_info_form = $this->_validation_form($_request); //اطلاعات جدید فرم
    if (!$_new_info_form) return redirect('/admin/form/add');

    $_insert_form = new form();
    $_insert_form->name = (string) $_request['name_form']; //نام
    $_insert_form->text = (string) $_request['text_form']; //توضیحات
    $_insert_form->fild = (string) json_encode($_new_info_form);

    $_insert_form->hash = (string) $this->RandomString();
    $_insert_form->id_grop = (string) count($_request['grops']) > 1 ? json_encode($_request['grops']) : $_request['grops'][0]; //مجموعه ها
    if (isset($_request['required'])) $_insert_form->required = 1; //اجباری
    if (isset($_request['saving_disabled'])) $_insert_form->saving_disabled = 1;
    if (isset($_request['show_onuserpanel'])) $_insert_form->show_onuserpanel = 1; //اجباری
    $_insert_form->notify_on_submit = ((bool) $_request->input('notify_on_submit', false)) ? 1 : NULL;
    $_insert_form->notify_phone = $_request->input('notify_phone') ?: NULL;

    $inputGroupIds = $_insert_form->id_grop; // Example: [1, 2, 3]
    $groupIds = [];


    // Handle different input formats
    if (is_string($inputGroupIds)) {
      // Check if the input is a JSON-encoded string
      $decodedGroupIds = json_decode($inputGroupIds, true);

      // If decoding is successful and results in an array, use it
      if (json_last_error() === JSON_ERROR_NONE && is_array($decodedGroupIds)) {
        $groupIds = $decodedGroupIds;
      } else {
        // If decoding fails, treat the input as a comma-separated string
        $groupIds = explode(',', $inputGroupIds);
      }
    } elseif (is_array($inputGroupIds)) {
      // If input is already an array, use it directly
      $groupIds = $inputGroupIds;
    }

    // Sanitize: Convert all values to integers and filter out non-numeric values
    $groupIds = array_filter(array_map('intval', $groupIds), function ($id) {
      return $id > 0;
    });
    if (isset($_request['confirm'])) {
      $_insert_form->confirm = 1;
      $_insert_form->save();
      $users = DB::table('users')
        ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
        ->whereIn('user_grops.grop_id', $groupIds)
        ->select('users.*')
        ->distinct()
        ->get();

      foreach ($users as $user) {
        $u_form = new user_form();
        $u_form->id_user = $user->id;
        $u_form->id_form = $_insert_form->id;
        $u_form->form = null;
        $u_form->save();
      } //غیرفعال بودن ثبت
    } //غیرفعال بودن ثبت
    else $_insert_form->save();

    return redirect('/admin/form');
  }
  /* END ذخیره */
  /* END ایجاد */


  /* START ویرایش */
  public function formEditPage(Request $_request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    return $this->pageEditForm($_request, $_id);
  }
  /* START ذخیره */
  public function formEdit(Request $_request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_new_info_form = $this->_validation_form($_request); //اطلاعات جدید فرم
    if (!$_new_info_form) return redirect("/admin/form/user/$_id");

    $inputGroupIds = $_request['grops']; // Example: [1, 2, 3]
    $groupIds = [];


    // Handle different input formats
    if (is_string($inputGroupIds)) {
      // Check if the input is a JSON-encoded string
      $decodedGroupIds = json_decode($inputGroupIds, true);

      // If decoding is successful and results in an array, use it
      if (json_last_error() === JSON_ERROR_NONE && is_array($decodedGroupIds)) {
        $groupIds = $decodedGroupIds;
      } else {
        // If decoding fails, treat the input as a comma-separated string
        $groupIds = explode(',', $inputGroupIds);
      }
    } elseif (is_array($inputGroupIds)) {
      // If input is already an array, use it directly
      $groupIds = $inputGroupIds;
    }

    // Sanitize: Convert all values to integers and filter out non-numeric values
    $groupIds = array_filter(array_map('intval', $groupIds), function ($id) {
      return $id > 0;
    });

    $form = form::where('id', $_id)->first();
    // create confirm forms
    if ($_request['confirm'] && $form->confirm != 1) {

      $users = DB::table('users')
        ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
        ->whereIn('user_grops.grop_id', $groupIds)
        ->select('users.*')
        ->distinct()
        ->get();


      foreach ($users as $user) {
        $u_form = new user_form();
        $u_form->id_user = $user->id;
        $u_form->id_form = $_id;
        $u_form->form = null;
        $u_form->save();
      }
    }
    form::where('id', $_id)->update(array(
      'name' => (string) $_request['name_form'], //نام
      'text' => (string) $_request['text_form'], //توضیحات
      'fild' => (string) json_encode($_new_info_form),
      'id_grop' => (string) count($_request['grops']) > 1 ? json_encode($_request['grops']) : $_request['grops'][0], //مجموعه ها
      'required' => isset($_request['required']) ? 1 : NULL, //اجباری
      'show_onuserpanel' => isset($_request['show_onuserpanel']) ? 1 : NULL,
      'saving_disabled' => isset($_request['saving_disabled']) ? 1 : NULL, //غیرفعال بودن ثبت
      'fill_form_limitation' => $_request['fill_form_limitation'], // محدویت حداکثر تعداد ثبت فرم
      'notify_on_submit' => ((bool) $_request->input('notify_on_submit', false)) ? 1 : NULL,
      'notify_phone' => $_request->input('notify_phone') ?: NULL,
    ));

    return redirect("/admin/form/user/$_id");
  }
  /* END ذخیره */
  /* END ویرایش */


  public function formUserPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    return $this->pageUserForm($_id);
  }

  //   not filled form
  public function formNoneUserPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    return $this->pageUserNoneFillForm($_id);
  }


  /* START ویرایش */
  function formUserEdit(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');
    return $this->editUserForm($_request);
  }
  /* END ویرایش */


  public function formUserAddPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $form = form::findOrfail($_id);
    $form_fild = json_decode($form['fild'], true);

    return view('admin.form.addXlsx', [
      'form' => $form,
      'form_fild' => $form_fild
    ]);
  }


  public function formUserAdd(Request $request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $form = form::findOrfail($_id);
    $form_fild = json_decode($form["fild"], 1);
    $file = json_decode($request["file"], 1);
    foreach ($file["form"] as $fileForm) {
      if (!isset($fileForm['user_id'])) {
        if (!isset($fileForm['user_mobile'])) {
          continue;
        } else {
          $user = user::where("mobile", $fileForm['user_mobile'])->first();
          if (!isset($user)) {
            continue;
          }
          $fileForm['user_id'] = $user->id;
        }
      }

      $user_form = [];
      foreach ($form_fild as $fild) {
        if (isset($fileForm[$fild["name"]])) {
          if ($fild["type"] == 1 or $fild["type"] == 2 or $fild["type"] == 3) {
            $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => $fileForm[$fild["name"]]];
          }
          if ($fild["type"] == 5) {
            if ($fileForm[$fild["name"]] == 1) {
              $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => 1];
            } else {
              $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => 0];
            }
          }
          if ($fild["type"] == 7) {
            if ($fileForm[$fild["name"]] == 1) {
              $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => 1];
            } else {
              $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => 0];
            }
          }
          if ($fild["type"] == 4 or $fild["type"] == 6) {
            $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => $fileForm[$fild["name"]]];
          }
        } else {
          if ($fild["type"] == 5) {
            $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => 0];
          } else {

            $user_form[] = ["type" => $fild["type"], "name_itme" => $fild["name"], $fild["name"] => null];
          }
        }
      }
      $user_forms = new user_form();
      $user_forms->id_user = $fileForm['user_id'];
      $user_forms->id_form = $form["id"];
      $user_forms->form = json_encode($user_form);
      $user_forms->save();
    }

    echo "ok";
  }


  /* START خروجی گرفتن از ستون های فرم */
  function formUserAddDownloadExcelExample(Request $_request, int $_id_form)
  {
    if (!session()->has('admin')) return redirect('/');
    $this->DownloadExcelExampleColumnsForm($_id_form, form::findOrfail($_id_form)['fild']);
  }
  /* END خروجی گرفتن از ستون های فرم */


  public function formUserDelete(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $user_form = user_form::findOrfail($_id);
    $id_form = $user_form['id_form'];
    $user_form->delete();

    return redirect('/admin/form/user/' . $id_form);
  }
  public function formUserNoneFillDelete(int $formId, int $userId)
  {
    if (!session()->has('admin')) return redirect('/');

    if (!user_form::where('id_user', $userId)->where('id_form', $formId)->exists()) {
      user_form::create(['id_user' => $userId, 'id_form' => $formId, 'form' => json_encode([])]);
    }

    return redirect()->back();
  }

  /* START حذف گروهی */
  public function formUserDeleteGroup(Request $_request)
  {
    $_link_page_before = $_SERVER['HTTP_REFERER']; //لینک صفحه قبل
    if (!session()->has('admin')) return redirect($_link_page_before);
    return $this->DeleteUserForm($_request, $_link_page_before);
  }
  /* END حذف گروهی */


  public function formDelete($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $form = form::findOrfail($id);
    $user_forms = user_form::where("id_form", $form["id"])->get();
    if (count($user_forms) == 0) {
      $menus = menu::where("id_form", $form["id"])->get();
      $unders = under::where("id_form", $form["id"])->get();
      foreach ($menus as $menu) $menu->delete();
      foreach ($unders as $under) $under->delete();
      $form->delete();
    } else {
      return redirect('/');
      exit();
    }

    return redirect('/admin/form');
  }
  /* START دریافت فیلدهای اضافی فرم بر اساس آیدی فرم */
  function formFieldsGet(int $_id_form)
  {
    if (!session()->has('admin')) return;

    $_extra_fields_form = form::where('id', $_id_form)->first(['fild']); //فیلدهای اضافی فرم
    if (!isset($_extra_fields_form['fild'])) return;

    echo $_extra_fields_form['fild'];
  }
  /* END دریافت فیلدهای اضافی فرم بر اساس آیدی فرم */

  /* START لینک خارجی فرم */
  public function createExternalToken(Request $request, $formId)
  {
    if (!session()->has('admin')) return redirect('/');
    $request->validate(['token_password' => 'required|min:4']);
    $tokenRecord = \App\ExternalFormToken::create([
      'form_id'             => $formId,
      'token'               => \Illuminate\Support\Str::random(40),
      'password_hash'       => password_hash($request->token_password, PASSWORD_DEFAULT),
      'created_by_admin_id' => null,
      'expires_at'          => $request->expires_at ?: null,
      'is_active'           => true,
    ]);
    $link = url('/external/form/' . $tokenRecord->token);
    return back()->with('external_link', $link)->with('external_link_form_id', $formId);
  }

  public function deactivateExternalToken($tokenId)
  {
    if (!session()->has('admin')) return redirect('/');
    \App\ExternalFormToken::findOrFail($tokenId)->update(['is_active' => false]);
    return back()->with('success', 'لینک غیرفعال شد.');
  }
  /* END لینک خارجی فرم */

  /* END فرم ها */


  public function ticketPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $grops = grop::select(['*'])->selectSub('SELECT COUNT(id_grop) FROM tickets WHERE id_grop=grops.id AND active=1', 'count_tickets_enable')->selectSub('SELECT COUNT(id_grop) FROM tickets WHERE id_grop=grops.id AND name="پشتیبانی فنی سامانه" AND active=1', 'count_tickets_system_technical_support_enable')->get();

    return view('admin.ticket.gropPage', ['grops' => $grops]);
  }


  public function ticketGropPage($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $grop = grop::findOrfail($id);
    $tickets = ticket::select(['tickets.id', 'tickets.name', 'tickets.active', 'tickets.id_user', 'tickets.created_at'])->selectSub(
      'CONCAT(users.name, " ", users.name2)',
      'name_user'
    )->join(
      'users',
      'users.id',
      'tickets.id_user'
    )->where("tickets.id_grop", $id)->get();
    $issues = issue::all();
    foreach ($tickets as $ticket) {
      $ticket["am"] = verta($ticket["created_at"]);
    }

    return view('admin.ticket.ticket', [
      "tickets" => $tickets,
      "statuses_ticket" => parent::$_statuses_ticket,
      "grop" => $grop,
      "issues" => $issues,
    ]);
  }


  public function ticketMessagePage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $ticket = ticket::findOrfail($id);

    if ($ticket["active"] == 1) : //وضعیت فعال
      $ticket->active = 2; //تغییر وضعیت به در حال بررسی
      $ticket->save();
    endif;

    $grop = grop::findOrfail($ticket["id_grop"]);
    $messages = message::where("id_ticket", $ticket["id"])->orderBy('id', 'ASC')->get();
    foreach ($messages as $message) {
      $message["am"] = verta($message["created_at"]);
    }
    return view('admin.ticket.ticketMessage', [
      "ticket" => $ticket,
      "messages" => $messages,
      "grop" => $grop
    ]);
  }

  public function ticketMessage($id, Request $_request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $ticket = ticket::findOrfail($id);

    if (
      $ticket['active'] != 0 //وضعیت بسته
      &&
      $ticket['active'] != 5 //وضعیت بایگانی
    ) :
      if ($_file = $_request->file('file')) :
        $_name = $_file->getClientOriginalName();
        $number_rand = rand(10000, 99999);
        $_file->move('file_ticket', $number_rand . $_name);
        $message = new message();
        $message->file = $number_rand . $_name;
        $message->text = $_name;
        $message->id_ticket = $ticket['id'];
        $message->id_user = session('id'); //آیدی کاربر پیام دهنده
        $message->type = 2;
        $message->save();
      endif;

      if ($_request['text'] != null) :
        $message = new message();
        $message->text = $_request['text'];
        $message->id_ticket = $ticket['id'];
        $message->id_user = session('id'); //آیدی کاربر پیام دهنده
        $message->type = 2;
        $message->save();
      endif;

      if ($ticket["new"] == 0) {
        $ticket->new = 1;
        $_save_ticket = true;
      }
    endif;

    if (
      //وضعیت دستی
      isset($_request["status"])
      &&
      array_key_exists($_request["status"], parent::$_statuses_ticket)
    ) :
      $ticket->active = $_request["status"];
      $_save_ticket = true;
    elseif (
      isset($_request["text"])
      &&
      $_request["text"] != ""
      &&
      $ticket["active"] != 3
    ) : //وضعیت پاسخ داده شد
      $ticket->active = 3; //تغییر وضعیت به پاسخ داده شد
      $_save_ticket = true;
    endif;

    if (isset($_save_ticket)) :
      $ticket->save();
    endif;

    return redirect("/admin/ticket/message/" . $id);
  }

  public function ticketActive($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $ticket = ticket::findOrfail($id);
    $ticket2 = ticket::findOrfail($id);
    if ($ticket2["active"] == 1) {
      $ticket->active = 0;
      $ticket->save();
    }
    if ($ticket2["active"] == 0) {
      $ticket->active = 1;
      $ticket->save();
    }

    return redirect("/admin/ticket/" . $ticket["id_grop"]);
  }


  public function ticketIssuePage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $issues = issue::all();
    return view('admin.ticket.issue', ["issues" => $issues]);
  }


  public function ticketIssueAdd()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    return view('admin.ticket.issueAdd');
  }
  public function ticketIssue(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $issue = new issue();
    $issue->name = $request["name"];
    $issue->save();
    return redirect("/admin/issue");
  }
  public function ticketIssueDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $issue = issue::findOrfail($id);
    $tickets = ticket::where("name", $issue["name"])->get();
    foreach ($tickets as $ticket) {
      $ticket->name = "سایر موارد";
      $ticket->save();
    }

    $issue->delete();
    return redirect("/admin/issue");
  }

  public function ticketMessageDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $message = message::findOrfail($id);
    $id_ticket = $message["id_ticket"];
    if ($message["file"] !== null) {
      @unlink("file_ticket/" . $message["file"]);
    }
    $message->delete();
    return redirect("/admin/ticket/message/" . $id_ticket);
  }
  public function ticketDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $ticket = ticket::findOrfail($id);

    $messages = message::where("id_ticket", $ticket["id"])->get();
    foreach ($messages as $message) {
      if ($message["file"] !== null) {
        @unlink("file_ticket/" . $message["file"]);
      }
      $message->delete();
    }
    $grop = $ticket["id_grop"];
    $ticket->delete();
    return redirect("/admin/ticket/" . $grop);
  }




  public function contactuPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $contactus = contactu::all();
    return view('admin.ticket.contactu', ["contactus" => $contactus]);
  }

  public function contactuDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $contactu = contactu::findOrfail($id);
    $contactu->delete();
    return redirect("/admin/contactu");
  }



  public function ticketDeleteDay()
  {

    $messages = message::all();
    foreach ($messages as $message) {
      if ($message["file"] !== null) {
        $am = Verta::parse(verta()->formatDate());
        $am3 = Verta::parse(verta($message["created_at"])->formatDate());
        $number = $am->diffDays($am3);
        if ($number <= -30) {
          @unlink("file_ticket/" . $message["file"]);
          $message->delete();
        }
      }
    }
  }


  /* START اشتراک */
  public $product_statuses = array(
    'غیرفعال',
    'فعال',
    'بایگانی'
  ); //وضعیت ها

  private $_id_and_name_grops_before_used_products = array(); //آیدی و نام مجموعه های از قبل فراخوانی شده
  public function productPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $_products = product::get(['id', 'name', 'number_days', 'amount_1_day', 'id_grops', 'status']);
    foreach ($_products as $_product) :
      if ($_product['id_grops']) :
        $_name_grops = array(); //نانم مجموعه ها

        //حذف آیدی مجموعه هایی که قبلاً فراخوانی شده اند
        $_id_grops = json_decode($_product['id_grops']); //آیدی مجموعه ها
        foreach ($_id_grops as $_key => $_id_grop) :
          if (array_key_exists($_id_grop, $this->_id_and_name_grops_before_used_products)) :
            unset($_id_grops[$_key]);
            $_name_grops[] = array('name' => $this->_id_and_name_grops_before_used_products[$_id_grop]);
          endif;
        endforeach;

        if (!empty($_id_grops)) :
          $_get_name_grops = grop::whereRaw('id IN (' . implode(',', $_id_grops) . ')')->get(['id', 'name']); //نام مجموعه ها
          foreach ($_get_name_grops as $_key => $_get_id_and_name_grop) :
            $_name_grops[] = array('name' => $_get_id_and_name_grop['name']);
            $this->_id_and_name_grops_before_used_products[$_get_id_and_name_grop['id']] = $_get_id_and_name_grop['name']; //ثبت در لیست قبلاً فراخوانی شده ها
          endforeach;
        endif;

        $_product['name_grops'] = $_name_grops;
      else :
        $_product['name_grops'] = '';
      endif;

      $_product['status'] = $this->product_statuses[$_product['status'] ? $_product['status'] : 0]; //نام وضعیت
    endforeach;

    return view('admin.product.page', ['products' => $_products]);
  }

  public function productAddPage()
  {
    if (!session()->has('admin')) return redirect('/');

    $_grops = grop::all(['id', 'name']); //مجموعه ها
    return view('admin.product.add', [
      'grops' => $_grops,
      'statuses' => $this->product_statuses
    ]);
  }

  private function product_check_fields($_request)
  {
    if (
      //نام بسته
      !isset($_request['name'])
      ||
      !is_string($_request['name'])

      ||

      //تعداد روز
      !isset($_request['number-days'])
      ||
      !is_numeric($_request['number-days'])

      ||

      //نوع بسته
      !isset($_request['package-type'])
      ||
      !in_array($_request['package-type'], array(1, 2, 3))

      ||

      //مبلغ 1 روز
      (
        $_request['package-type'] != 3 //نوع بسته تشویقی نبود
        &&
        (
          !isset($_request['amount-1-day'])
          ||
          !is_numeric($_request['amount-1-day'])
        )
      )

      ||

      //مجموعه ها
      !isset($_request['grops'])
      ||
      !is_array($_request['grops'])

      ||

      //وضعیت
      !isset($_request['status'])
      ||
      !array_key_exists($_request['status'], $this->product_statuses)
    ) return false;


    /* START بررسی نوع بسته تشویقی */
    if (
      $_request['package-type'] == 3 //نوع بسته تشویقی نبود

      &&

      isset($_request['amount-1-day'])
      &&
      !empty($_request['amount-1-day'])
    ) return false;
    /* END بررسی نوع بسته تشویقی */


    /* START بررسی مجموعه ها */
    //پاکسازی مجموعه یا مجموعه ها
    foreach ($_request['grops'] as $_key => $_id_grop) :
      if (!is_numeric($_id_grop)) unset($_request['grops'][$_key]);
    endforeach;

    if (count(grop::whereRaw('id IN (' . implode(',', $_request['grops']) . ')')->get()) <= 0) return false;
    /* END بررسی مجموعه ها */

    return true;
  }

  public function productAdd(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');

    if (!$this->product_check_fields($_request)) return redirect("/admin/product/add");

    $_product = new product();
    $_product->name = $_request['name']; //نام بسته
    $_product->number_days = $_request['number-days']; //تعداد روز
    $_product->amount_1_day = $_request['amount-1-day']; //تعداد روز
    $_product->id_grops = json_encode($_request['grops']); //مجموعه ها
    $_product->description = $_request['description']; //توضیحات
    $_product->status = ($_request['status'] > 0) ? $_request['status'] : ''; //وضعیت
    $_product->package_type = $_request['package-type']; //نوع بسته
    $_product->save();

    return redirect("/admin/product");
  }


  /* START ویرایش */
  public function productEditPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_product = product::findOrfail($_id, ['name', 'number_days', 'amount_1_day', 'id_grops', 'description', 'status', 'package_type']);
    $_product['id_grops'] = json_decode($_product['id_grops']); //آیدی مجموعه های انتخاب شده
    $_grops = grop::all(['id', 'name']); //همه مجموعه ها
    return view('admin.product.edit', [
      'product' => $_product,
      'grops' => $_grops,
      'statuses' => $this->product_statuses
    ]);
  }

  /* بروزرسانی */
  public function productEdit(Request $_request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    if (!$this->product_check_fields($_request)) return redirect("/admin/product/edit/$_id");

    $_product = product::findOrfail($_id);
    $_product->name = $_request['name']; //نام بسته
    $_product->number_days = $_request['number-days']; //تعداد روز
    $_product->amount_1_day = $_request['amount-1-day']; //تعداد روز
    $_product->id_grops = json_encode($_request['grops']); //مجموعه ها
    $_product->description = $_request['description']; //توضیحات
    $_product->status = ($_request['status'] > 0) ? $_request['status'] : NULL; //وضعیت
    $_product->package_type = $_request['package-type']; //نوع بسته
    $_product->save();

    return redirect("/admin/product/edit/$_id");
  }
  /* END ویرایش */

  /* START تغییر وضعیت */
  public function productChangeStatus(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_status_product = product::findOrfail($_id, ['id', 'status']);
    $_status_product->status = ($_status_product['status'] == NULL) ? 1 : NULL;
    $_status_product->save();

    return redirect('/admin/product');
  }
  /* END تغییر وضعیت */


  /* START حذف */
  public function productDelete(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_product = product::findOrfail($_id, ['id']);
    $_product->delete();

    return redirect('/admin/product');
  }
  /* END حذف */


  /* START محصولات خریداری شده */
  public function productsPurchased()
  {
    if (!session()->has('admin')) return redirect('/');

    return view('admin.product.purchased.page', [
      'pays' => pay::select(['id', 'price', 'id_user', 'referenceId', 'created_at'])->
        //نام بسته
        selectSub(
          'SELECT name FROM products WHERE id=pays.id_product',
          'name_product'
        )->
        //نام و نام خانوادگی کاربر
        selectSub(
          'SELECT CONCAT(name, " ", name2) FROM users WHERE id=pays.id_user',
          'name_and_family_user'
        )->
        //نام مجموعه
        selectSub(
          'SELECT name FROM grops WHERE id=(SELECT grop_id FROM user_grops WHERE user_id=pays.id_user)',
          'name_grop'
        )->
        //تاریخ خاتمه اعتبار
        selectSub(
          'SELECT active_am FROM users WHERE id=pays.id_user',
          'expiry_date'
        )->where('active', 1)->get()
    ]);
  }

  /* START حذف */
  function productDeletePurchased(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_pay = pay::findOrfail($_id, ['id']);
    $_pay->delete();

    return redirect('/admin/product/purchased');
  }
  /* END حذف */
  /* END محصولات خریداری شده */
  /* END اشتراک */


  public function cropPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $products = crop::all();
    foreach ($products as $product) {
      $product["grop"] = grop::findOrfail($product["id_grop"]);
    }
    return view('admin.crop.page', ["products" => $products]);
  }
  public function cropAddPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $grops = grop::all();
    return view('admin.crop.add', ['grops' => $grops]);
  }

  public function cropAdd(Request $request)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $product = new crop();
    $product->name = $request["name"];
    $product->text = $request["text"];
    $product->price = intval(str_replace(",", "", $request["price"]));
    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("crop_img", $rond . $name);
      $product->img = $rond . $name;
    }
    $product->id_grop = $request["grop"];
    $product->save();
    return redirect("/admin/crop");
  }

  public function cropEditPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product = crop::findOrfail($id);
    $grops = grop::all();
    return view('admin.crop.edit', ["product" => $product, "grops" => $grops]);
  }
  public function cropEdit(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $product = crop::findOrfail($id);
    $product->name = $request["name"];
    $product->text = $request["text"];
    $product->price = intval(str_replace(",", "", $request["price"]));
    $product->id_grop = $request["grop"];
    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("crop_img", $rond . $name);
      $product->img = $rond . $name;
    }
    $product->save();
    return redirect("/admin/crop");
  }
  public function cropActive($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product = crop::findOrfail($id);
    $product2 = crop::findOrfail($id);
    if ($product2["active"] == 1) {
      $product->active = 0;
      $product->save();
    }
    if ($product2["active"] == 0) {
      $product->active = 1;
      $product->save();
    }

    return redirect("/admin/crop");
  }


  public function cropPay($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product = crop::findOrfail($id);
    $product_pay = crop_pay::where("id_crop", $product["id"])->where("active", 1)->get();
    $price = 0;
    foreach ($product_pay as $pay) {
      $pay["am"] = \verta($pay["created_at"]);
      $pay["user"] = user::findOrfail($pay["id_user"]);
      $price += $pay["price"];
    }

    return view('admin.crop.pay', ["product" => $product, "product_pay" => $product_pay, "price" => $price]);
  }


  public function cropPayDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $crop_pay = crop_pay::findOrfail($id);
    $id_crop = $crop_pay["id_crop"];
    $crop_pay->delete();
    return redirect("/admin/crop/pay/" . $id_crop);
  }















  public function advertisingPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }

    $advertising_pays = advertising_pay::where("active_pay", 1)->get();
    foreach ($advertising_pays as $advertising_pay) {
      $user = user::find($advertising_pay["id_user"]);

      if ($user !== null) {
        $advertising_pay["user"] = $user;
      }

      $product_advertising = product_advertising::find($advertising_pay["id_product_advertising"]);
      if ($product_advertising !== null) {
        $advertising_pay["product"] = $product_advertising;
      }
    }

    return view('admin.advertising.page', ["advertising_pays" => $advertising_pays]);
  }


  public function advertisingImg($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $advertising_pay = advertising_pay::findOrfail($id);
    unlink("advertising_img/" . $advertising_pay["img"]);
    $advertising_pay->active = 2;
    $advertising_pay->img = null;
    $advertising_pay->save();
    return redirect('/admin/advertising');
  }

  public function advertisingActive($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $advertising_pay = advertising_pay::findOrfail($id);
    if ($advertising_pay["id_product_advertising"] == 25) {
      $advertising_pay->active = 1;
      $advertising_pay->active_show = 1;
      $advertising_pay->am_end = verta($advertising_pay["day"] . ' day')->formatDate();
      $advertising_pay->save();
    } else {
      $advertising_pay->active = 1;
      $advertising_pay->save();
    }

    return redirect('/admin/advertising');
  }
  public function AdvertisingDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $advertising_pay = advertising_pay::findOrfail($id);
    $advertising_pay->delete();
    return redirect("/admin/advertising");
  }

  private function advertisingCronJob()
  {
    $product_advertisings = product_advertising::where("id", "!=", "25")->where("id", "!=", "26")->get();
    foreach ($product_advertisings as $product_advertising) {
      $advertising_pays = advertising_pay::where("active", 1)->where("active_pay", 1)->where("active_show", 1)->where("id_product_advertising", $product_advertising["id"])->get();

      if (count($advertising_pays) == 0) {
        $advertising_pay_show = advertising_pay::where("active", 1)->where("active_pay", 1)->where("active_show", 0)->where("id_product_advertising", $product_advertising["id"])->orderBy('id', 'ASC')->get();
        if (count($advertising_pay_show) > 0) {
          $advertising_pay_show[0]->active_show = 1;
          $advertising_pay_show[0]->am_end = verta($advertising_pay_show[0]["day"] . ' day')->formatDate();
          $advertising_pay_show[0]->save();
        }
      }
    }
  }

  public function advertisingCronJobEnd()
  {
    $advertising_pays = advertising_pay::where("active", 1)->where("active_pay", 1)->where("active_show", 1)->where("id_product_advertising", "!=", "26")->get();
    foreach ($advertising_pays as $advertising_pay) {
      $am = Verta::parse(verta()->formatDate());
      $am3 = Verta::parse($advertising_pay["am_end"]);
      $number = $am->diffDays($am3);
      if ($number <= 0) {
        $advertising_pay->active_show = 2;
        $advertising_pay->save();
      }
    }
    $this->advertisingCronJob();
  }

  public function advertisingProductPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product_advertisings = product_advertising::all();
    return view('admin.advertising.product', ["product_advertisings" => $product_advertisings]);
  }

  public function advertisingProductEdit($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product_advertising = product_advertising::findOrfail($id);
    return view('admin.advertising.edit', ["product_advertising" => $product_advertising]);
  }

  public function advertisingProduct(Request $request, $id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $product_advertising = product_advertising::findOrfail($id);
    $product_advertising->name = $request["name"];
    $product_advertising->price = $request["price"];
    if (isset($request["active"])) {
      $product_advertising->active = 1;
    } else {
      $product_advertising->active = 0;
    }

    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("advertising_img", $rond . $name);
      $product_advertising->img = $rond . $name;
    }

    $product_advertising->save();
    return redirect('/admin/advertising/product');
  }












  public function pmPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $pms = pm::with('groups')->get();
    foreach ($pms as $pm) {
      $pm["am"] = \verta($pm["created_at"]);
    }
    return view('admin.pm.page', ["pms" => $pms]);
  }

  public function pmAddPage()
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $grops = grop::all();
    return view('admin.pm.add', ["grops" => $grops]);
  }
  public function pmAddUser(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');

    $_grop_users = array_filter((array) $_request->input('gropUser', []), function ($id) {
      return is_numeric($id);
    });

    if (empty($_grop_users)) {
      echo json_encode([]);
      return;
    }

    echo json_encode(user_grop::select(['users.id'])->selectSub(
      'CONCAT(name, " ", name2, " [", users.id, "]" , IF(hash, CONCAT(" [", hash, "]"), ""))',
      'info_user'
    )->join(
      'users',
      'users.id',
      'user_grops.user_id'
    )->whereIn('id_usergrop', $_grop_users)->get());
  }


  public function pmAdd(Request $_request)
  {
    if (!session()->has('admin')) redirect('/');

    if (!isset($_request['title']) || $_request['title'] == '') redirect('/admin/pm');

    // تاریخ و ساعت خاتمه نمایش
    $_date_end_show = null;
    if (isset($_request['date-end-show']) && $_request['date-end-show']) {
      $_date_end_show = $_request['date-end-show'];
      if (!empty($_request['time-end-show'])) $_date_end_show .= ' ' . $_request['time-end-show'];
    }

    // آپلود فایل (یکبار انجام می‌شود)
    $_file_name = null;
    if ($_file = $_request->file('file')) {
      $_file_name = rand(10000, 99999) . $_file->getClientOriginalName();
      $_file->move('pm_img', $_file_name);
    }

    // مجموعه‌های انتخاب شده
    $_grop_ids = array_values(array_filter(array_map('intval', (array) $_request["grop"])));
    if (empty($_grop_ids)) return redirect('/admin/pm');

    // جمع‌آوری کاربران از تمام مجموعه‌های انتخاب شده (یکجا)
    $_all_users        = [];
    $_all_note_user_ids = [];

    if (!empty($_request['gropUser']) && is_array($_request['gropUser']) && count($_request['gropUser']) > 1) {
      // چند گروه کاربری انتخاب شده → ارسال به همه اعضای آن گروه‌ها
      foreach ($_request['gropUser'] as $_id_grop_user) {
        if (is_numeric($_id_grop_user)) $_all_users['Grop Users'][] = (int) $_id_grop_user;
      }
      foreach (($_all_users['Grop Users'] ?? []) as $_gug_id) {
        foreach (\App\user_grop::where('id_usergrop', $_gug_id)->get() as $_ug)
          $_all_note_user_ids[] = $_ug['user_id'];
      }
    } elseif (isset($_request['user']) && is_array($_request['user'])) {
      // کاربران مشخص انتخاب شده
      foreach ($_request['user'] as $_uid) {
        if (user::where('id', $_uid)->exists()) {
          $_all_users[]         = (int) $_uid;
          $_all_note_user_ids[] = (int) $_uid;
        }
      }
    } else {
      // یک گروه کاربری یا همه کاربران همه مجموعه‌های انتخاب شده
      $_seen_ids = [];
      foreach ($_grop_ids as $_grop_id) {
        $_user_grops = (!empty($_request['gropUser']) && is_array($_request['gropUser']))
          ? user_grop::where('id_usergrop', $_request['gropUser'][0])->where('grop_id', $_grop_id)->get()
          : user_grop::where('grop_id', $_grop_id)->get();

        foreach ($_user_grops as $_ug) {
          $_uid = $_ug['user_id'];
          if (!in_array($_uid, $_seen_ids) && user::where('id', $_uid)->exists()) {
            $_all_users[]         = $_uid;
            $_all_note_user_ids[] = $_uid;
            $_seen_ids[]          = $_uid;
          }
        }
      }
    }

    // ایجاد یک رکورد پیام واحد برای همه مجموعه‌های انتخاب شده
    $_pm           = new pm();
    $_pm->id_grop  = $_grop_ids[0]; // اولین مجموعه برای سازگاری با کدهای موجود
    $_pm->id_users = json_encode($_all_users);
    $_pm->title    = $_request['title'];
    if ($_file_name) {
      $_pm->img = $_file_name;
    } elseif (isset($_request['sms'])) {
      $_pm->text = $_request['sms'];
    }
    $_pm->date_end_show = $_date_end_show;
    $_pm->created_by    = 1;
    $_pm->save();

    // ثبت رابطه با تمام مجموعه‌ها از طریق جدول pivot
    $_pm->groups()->attach($_grop_ids);

    // Task 4: only flag the PM; notes are registered separately after admin approval
    if ($_request->has('save_as_note') && isset($_request['sms']) && $_request['sms'] != '') {
      $_pm->save_as_note = true;
      $_pm->save();
    }

    return redirect('/admin/pm');
  }


  public function pmShowPage($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $pm = pm::findOrfail($id);
    $pm_shows = pm_show::where('id_pm', $pm['id'])->get();
    foreach ($pm_shows as $pm_show) {
      $pm_show["user"] = user::findOrfail($pm_show["id_user"]);
    }
    return view('admin.pm.show', ["pm_shows" => $pm_shows, "pm" => $pm]);
  }

  /* START حذف مشاهده کننده پیام */
  function pmShowDelete(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_pm_show = pm_show::findOrfail($_id, ['id', 'id_pm']);
    $_pm_show->delete();

    return redirect('/admin/pm/show/' . $_pm_show['id_pm']);
  }
  /* END حذف مشاهده کننده پیام */

  // Task 4: register PM content as notes for all targeted users after admin approval
  public function pmRegisterAsNote($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $pm = pm::findOrFail($id);

    if (!$pm->save_as_note) {
      return redirect('/admin/pm')->with('error', 'این پیام تنظیم ثبت یادداشت ندارد.');
    }

    if ($pm->note_registered_at) {
      return redirect('/admin/pm')->with('error', 'یادداشت‌های این پیام قبلاً ثبت شده‌اند.');
    }

    $userIds = json_decode($pm->id_users, true) ?? [];
    foreach (array_unique(array_filter(array_map('intval', (array)$userIds))) as $uid) {
      \App\note::create([
        'user_id'    => $uid,
        'admin_id'   => session('id'),
        'content'    => $pm->text,
        'status'     => 'pending',
        'visibility' => 'admin_and_user',
      ]);
    }

    $pm->update(['note_registered_at' => now()]);

    return redirect('/admin/pm/show/' . $id)->with('success', 'یادداشت‌ها با موفقیت ثبت شدند و در انتظار تایید هستند.');
  }

  /* START ویرایش پیام */
  public function pmEditPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_pm = pm::select(['id_grop', 'id_users', 'title', 'img', 'text', 'date_end_show'])->selectSub(
      'IF(pms.id_users LIKE \'%"Grop Users"%\', pms.id_users, (SELECT id_usergrop FROM user_grops WHERE user_id=IF(pms.id_users LIKE "%,%", SUBSTRING(pms.id_users, 2, POSITION("," IN pms.id_users) - 2), SUBSTRING(pms.id_users, 2, POSITION("]" IN pms.id_users) - 2)) AND grop_id=pms.id_grop LIMIT 1))',
      'id_user_grop' //آیدی گروه کاربری کاربر
    )->findOrfail($_id);

    $_id_users = json_decode($_pm['id_users'], true); //آیدی کاربران
    if (array_key_exists('Grop Users', $_id_users)) :
      $_implode_id_users = '';
      $_user_grops = usergrop::where('id_grop', $_pm['id_grop'])->whereRaw('id IN (' . implode(',', $_id_users['Grop Users']) . ')')-> //آیدی گروه کاربری کاربر یا آیدی گروه های انتخاب شده بصورت چند تایی
        get(['name']);
    else :
      $_implode_id_users = implode(',', $_id_users);
      $_user_grops = array();
    endif;

    return view('admin.pm.edit', [
      'pm' => $_pm,
      'id_users' => $_id_users, //آیدی کاربران
      'grop' => grop::findOrFail($_pm['id_grop'], ['name'])['name'], //مجموعه ها
      'user_grops' => $_user_grops, //گروه های کاربری مجموعه
      'users_grop' => $_implode_id_users ? user::selectSub(
        'CONCAT(name, " ", name2, " [", id, "]" , IF(hash, CONCAT(" [", hash, "]"), ""))',
        'info_user'
      )->whereRaw(
        '(SELECT COUNT(id) FROM user_grops WHERE user_id=users.id AND id_usergrop=?)=1',
        $_pm['id_user_grop']
      )->whereRaw("id IN ($_implode_id_users)")->get() : '' //کاربران گروه کاربری مجموعه
    ]);
  }

  /* START ثبت */
  public function pmEdit(int $_id, Request $_request)
  {
    if (!session()->has('admin')) redirect('/');

    //تاریخ و ساعت خاتمه نمایش
    if (
      isset($_request['date-end-show'])
      &&
      $_request['date-end-show']
    ) :
      $_date_end_show = $_request['date-end-show'];

      if (isset($_request['time-end-show']) && isset($_request['time-end-show'])) $_date_end_show .= ' ' . $_request['time-end-show'];

      pm::where('id', $_id)->update(array('date_end_show' => isset($_date_end_show) ? $_date_end_show : NULL));
    endif;

    return redirect('/admin/pm');
  }
  /* END ثبت */
  /* END ویرایش پیام */


  public function pmDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $pm = pm::findOrfail($id);
    $pm_shows = pm_show::where('id_pm', $pm['id'])->get();
    foreach ($pm_shows as $pm_show) {
      $pm_show->delete();
    }
    if ($pm["img"] != null) {
      unlink("pm_img/" . $pm["img"]);
    }
    $pm->groups()->detach();
    $pm->delete();
    return redirect('/admin/pm');
  }


  /* START صورتحساب */
  /* START لیست مجموعه ها */
  public function invoicePage()
  {
    if (!session()->has('admin')) return redirect('/');
    return view('admin.invoice.gropPage', [
      'grops' => grop::select(['*'])->selectSub('SELECT COUNT(id_grop) FROM tickets WHERE id_grop=grops.id AND active=1', 'count_tickets_enable')->selectSub('SELECT COUNT(id_grop) FROM tickets WHERE id_grop=grops.id AND name="پشتیبانی فنی سامانه" AND active=1', 'count_tickets_system_technical_support_enable')->get()
    ]);
  }
  /* END لیست مجموعه ها */


  /* START لیست صورتحساب ها */
  public function invoiceGropPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_name_grop = grop::findOrfail($_id, ['name']);

    $invoices = invoice::where('id_grop', $_id)->get(['id', 'id_users', 'id_grop', 'title', 'text', 'price', 'am_start', 'am_end', 'daily_fine_amount', 'fixed_penalty_amount', 'number', 'created_at']);
    foreach ($invoices as $invoice) :
      if (!empty($invoice['id_users'])) :
        $_users_invoice = !empty($invoice['id_users']) ? json_decode($invoice['id_users'], true) : array();
        if (array_key_exists('Grop Users', $_users_invoice)) :
          $_save_name_user_grops = array();
          $_name_user_grops = usergrop::whereRaw('id IN (' . implode(',', $_users_invoice['Grop Users']) . ')')->get(['name']);
          foreach ($_name_user_grops as $_name_user_grop) $_save_name_user_grops[] = $_name_user_grop['name'];
          $invoice['id_users'] = (count($_save_name_user_grops) > 4) ? implode(' - ', array_slice($_save_name_user_grops, 0, 4)) . ' ...' : implode(' - ', $_save_name_user_grops);
        else :
          $invoice['id_users'] = (count($_users_invoice) > 4) ? implode(' - ', array_slice($_users_invoice, 0, 4)) . ' ...' : implode(' - ', $_users_invoice);
        endif;
      endif;

      $invoice['am'] = verta($invoice['created_at']);
    endforeach;

    return view('admin.invoice.page', [
      'name_grop' => $_name_grop['name'],
      'invoices' => $invoices
    ]);
  }
  /* END لیست صورتحساب ها */


  /* START ایجاد */
  public function invoiceAddPage()
  {
    if (!session()->has('admin')) return redirect('/');
    $grops = grop::all();
    return view('admin.invoice.add', ["grops" => $grops]);
  }


  public function invoiceAdd(Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');
    $this->invoiceSave($_request, 'admin', new invoice());
    return redirect('/admin/invoice');
  }
  /* END ایجاد */


  /* START ویرایش */
  public function invoiceEditPage(int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    $_info_invoice = invoice::findOrFail($_id, ['id_users', 'id_grop', 'title', 'text', 'price', 'am_start', 'am_end', 'daily_fine_amount', 'fixed_penalty_amount', 'number', 'fine_calc_date', 'fine_calc_amount']);

    $_user_grops_invoice = usergrop::where('id_grop', $_info_invoice['id_grop'])->get(['id', 'name']); //گروه های کاربری مجموعه
    /* START گروه های کاربری مجموعه انتخاب شده */
    $_user_grops_selected = array();

    $_id_users_invoice = !empty($_info_invoice['id_users']) ? json_decode($_info_invoice['id_users'], true) : array();
    if (array_key_exists('Grop Users', $_id_users_invoice)) : //گروه های کاربری مجموعه
      $_user_grops_selected = $_id_users_invoice['Grop Users'];
    else : //کاربر انتخاب شده بود
      $_users_invoice = user::join('user_grops', 'user_id', 'users.id')->where('grop_id', $_info_invoice['id_grop'])->distinct()->get(['id_usergrop', 'users.id', 'name', 'name2', 'hash']);
      foreach ($_users_invoice as $_user_invoice) :
        if (in_array($_user_invoice['id'], $_id_users_invoice)) $_user_grops_selected[] = $_user_invoice['id_usergrop'];
      endforeach;
    endif;
    /* END گروه های کاربری مجموعه انتخاب شده */

    return view('admin.invoice.edit', [
      'info_invoice' => $_info_invoice,
      'grops' => grop::all(['id', 'name']),
      'user_grops' => $_user_grops_invoice,
      'user_grops_selected' => isset($_user_grops_selected) ? $_user_grops_selected : array(),
      'users' => isset($_users_invoice) ? $_users_invoice : array(),
      'users_selected' => isset($_users_invoice) ? $_id_users_invoice : array(),
    ]);
  }


  public function invoiceEdit(Request $_request, int $_id)
  {
    if (!session()->has('admin')) return redirect('/');
    $this->invoiceSave($_request, 'admin', invoice::findOrFail($_id));
    return redirect("/admin/invoice/edit/$_id");
  }
  /* END ویرایش */


  public function invoiceUserPage($id)
  {
    if (!session()->has('admin')) return redirect('/');

    $_invoice = invoice::findOrfail($id, ['id', 'id_users', 'id_grop', 'title', 'price', 'am_start', 'am_end', 'number']);
    $_id_users_invoice = !empty($_invoice['id_users']) ? json_decode($_invoice['id_users'], true) : array();

    // Join invoice_fines once per user instead of N+1 queries; read total_penalty (not stale price column).
    if (array_key_exists('Grop Users', $_id_users_invoice)) :
      // group by user_id to prevent duplicates when a user belongs to multiple usergrops
      $_users_invoice = user_grop::select(['mobile', 'users.name', 'name2', 'hash', 'user_id AS id'])
        ->selectRaw('MIN(usergrops.name) as name_user_grop')
        ->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop')
        ->join('users', 'users.id', 'user_id')
        ->leftJoin('invoice_fines', function ($join) use ($_invoice) {
            $join->on('invoice_fines.id_user', '=', 'users.id')
                 ->where('invoice_fines.id_invoice', '=', $_invoice['id']);
        })
        ->selectRaw('COALESCE(MAX(invoice_fines.total_penalty), 0) as price_fine')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_paid - invoice_fines.total_discount), 0) as price_active')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_discount), 0) as Price_Discount')
        ->whereRaw('id_usergrop IN (' . implode(',', $_id_users_invoice['Grop Users']) . ')')
        ->groupBy('user_id', 'mobile', 'users.name', 'name2', 'hash')
        ->get();
    else :
      // group by users.id to prevent duplicates from multiple user_grops entries
      $_users_invoice = user::select(['users.id', 'mobile', 'users.name', 'name2', 'hash'])
        ->selectRaw('MIN(usergrops.name) as name_user_grop')
        ->join('user_grops', 'user_id', 'users.id')
        ->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop')
        ->leftJoin('invoice_fines', function ($join) use ($_invoice) {
            $join->on('invoice_fines.id_user', '=', 'users.id')
                 ->where('invoice_fines.id_invoice', '=', $_invoice['id']);
        })
        ->selectRaw('COALESCE(MAX(invoice_fines.total_penalty), 0) as price_fine')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_paid - invoice_fines.total_discount), 0) as price_active')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_discount), 0) as Price_Discount')
        ->whereRaw('users.id IN (' . implode(',', $_id_users_invoice) . ')')
        ->groupBy('users.id', 'mobile', 'users.name', 'name2', 'hash')
        ->get();
    endif;

    $grop = grop::where('id', $_invoice['id_grop'])->get();

    return view('admin.invoice.user', [
      'grop' => $grop,
      'invoice' => $_invoice,
      'invoice_users' => $_users_invoice
    ]);
  }


  public function invoiceUser(int $id, int $id_user)
  {
    if (!session()->has('admin')) return redirect('/');

    $user = user::findOrfail($id_user);
    $invoice = invoice::findOrfail($id);

    $invoice_pays = invoice_pay::select(['id', 'price', 'text', 'referenceId', 'active_pay', 'am', 'created_at', 'picture'])
      ->selectSub('id', 'ID')
      ->where("id_user", $user["id"])
      ->where("id_invoice", $invoice["id"])
      ->orderBy('created_at', 'DESC')
      ->get();

    // درصورتی که واریز تایید شده نداشتیم دکمه "بروزرسانی" جریمه نمایش داده نشود
    $show_calc_fine_button = count($invoice_pays->where('active_pay', 1)) > 0;
    $price_active = 0;
    $price_discount = 0;
    foreach ($invoice_pays as $invoice_pay) {
      if ($invoice_pay['active_pay'] == 1) {
        if (mb_substr($invoice_pay['text'], 0, 8) == 'تخفیف > ') {
          $price_discount += $invoice_pay['price'];
        } else {
          $price_active += $invoice_pay['price'];
        }
      }
    }

    if ($invoice["am_end"] != null) {
      $am = Verta::parse(verta()->formatDate());
      $am3 = Verta::parse($invoice["am_end"]);
      $invoice["number_end"] = $am->diffDays($am3);

      $penalty_data = (new InvoicePenaltyCalculator())->recalculate((int) $invoice["id"], (int) $user["id"]);
      $invoice["price_fine"] = $penalty_data['total_penalty'];
    }

    return view('admin.invoice.userPay', [
      'user' => $user,
      'price_active' => $price_active,
      'price_discount' => $price_discount,
      'invoice_user' => $invoice,
      'invoice_pays' => $invoice_pays,
      'show_calc_fine_button' => $show_calc_fine_button,
    ]);
  }

  public function invoiceUserPay(int $_id, int $_id_user, Request $_request)
  {
    if (!session()->has('admin')) return redirect('/');

    $_link_page_before = $_SERVER['HTTP_REFERER']; //لینک صفحه قبل

    //شماره
    $_number = (string) $_request['number'];
    if ($_number == '' || $_number != htmlspecialchars($_number)) return redirect($_link_page_before)->with('error', 'شماره فیش معتبر نیست');

    //قیمت
    $_price = (int) intval(str_replace(',', '', $_request['price']));
    if ($_price <= 0) return redirect($_link_page_before)->with('error', 'مبلغ معتبر نیست');

    //شرح
    $_text = (string) $_request['text'];
    if ($_text == '' || $_text != htmlspecialchars($_text)) return redirect($_link_page_before)->with('error', 'شرح وارد نشده است');

    //تاریخ
    $_date = (string) $_request['am'];
    if ($_date == '' || $_date != htmlspecialchars($_date)) redirect($_link_page_before);
    $_explode_date = explode('/', $_date);
    if (
      count($_explode_date) != 3

      ||

      //سال
      $_explode_date[0] <= 1395
      ||
      $_date > verta()->format('Y/m/d')


      ||

      //ماه
      $_explode_date[1] <= 0
      ||
      $_explode_date[1] >= 13

      ||

      //روز
      $_explode_date[2] <= 00
      ||
      $_explode_date[2] >= 32
    ) return redirect($_link_page_before)->with('error', 'تاریخ معتبر نیست');

    // Task 4: یکتایی شماره فیش در محدوده همین صورتحساب (collection) — نه global
    $_id_grop_admin = invoice::findOrFail($_id, ['id_grop'])->id_grop;
    $_receipt_exists = invoice_pay::where('invoice_pays.id', $_number)
        ->join('invoices', 'invoices.id', '=', 'invoice_pays.id_invoice')
        ->where('invoices.id_grop', $_id_grop_admin)
        ->exists();
    if ($_receipt_exists) return redirect($_link_page_before)->with('error', 'این شماره فیش قبلا در این مجموعه ثبت شده است');

    $_info_user = (object) user::select(['users.id', 'id_usergrop'])->join('user_grops', 'user_id', 'users.id')->where('users.id', $_id_user)->firstOrFail();
    $_id_user = (int) $_info_user['id']; //آیدی کاربر
    $_id_grop_user = (int) $_info_user['id_usergrop']; //گروه کاربری
    $_invoice_check = invoice::findOrFail($_id, ['id', 'am_end']);
    if (empty($_invoice_check['am_end'])) return redirect($_link_page_before)->with('error', 'این صورتحساب تاریخ پایان ندارد');
    $_id_invoice = (int) $_invoice_check['id'];

    //مبلغ وارد شده بیشتر از مانده تعهد نباشد - جریمه live محاسبه می‌شود
    $_invoice_price = invoice::where('id', $_id_invoice)->value('price');
    $_total_paid = invoice_pay::where('id_invoice', $_id_invoice)->where('id_user', $_id_user)->where('active_pay', 1)->sum('price');
    $_live_penalty = (new InvoicePenaltyCalculator())->compute($_id_invoice, $_id_user)['total_penalty'];
    if (($_invoice_price + $_live_penalty - $_total_paid) < $_price) return redirect($_link_page_before);

    if (isset($_request['type'])) $_text = 'تخفیف > ' . $_text;

    $_invoice_pay = new invoice_pay();
    $_invoice_pay->id = $_number; //شماره
    $_invoice_pay->id_user = $_id_user;
    $_invoice_pay->id_invoice = $_id_invoice;
    $_invoice_pay->price = $_price;
    $_invoice_pay->text = $_text;
    $_invoice_pay->am = $_date;
    $_invoice_pay->active_pay = 1;
    $_invoice_pay->save();

    // به دلیل اینکه وضعیت پرداخت تایید شده می باشد
    // عملیات محاسبه جریمه برای پرداخت های کاربر روی صورتحساب اعمال می شود
    if ($_invoice_pay->active_pay == 1) {
      $this->createOrUpdate_user_fine($_id_user, $_id_invoice);
    }

    return redirect(!empty($_link_page_before) ? $_link_page_before : '/admin/invoice/user/' . $_id_invoice . '/' . $_id_user)->with('message', 'پرداخت با موفقیت ثبت شد.');
  }

  /**
   * محاسبه جریمه باقیمانده مانده تعهد و
   * ثبت اون در جدول جریمه ها
   */
  public function calc_remaining_commitment_fine($id_invoice, $id_user)
  {
    $this->createOrUpdate_user_fine($id_user, $id_invoice);

    $_link_page_before = $_SERVER['HTTP_REFERER'];
    return redirect(!empty($_link_page_before) ? $_link_page_before : '/admin/invoice/user/' . $id_invoice . '/' . $id_user);
  }

  /* START حسابرسی جریمه ها - با کرون جاب کار میکند */
  public function day_penalty()
  {
    $calculator = new InvoicePenaltyCalculator();

    // همه صورت‌حساب‌هایی که تاریخ تعهدشان گذشته و ضریب جریمه دارند
    invoice::where('am_end', '<=', verta()->format('Y/m/d'))
        ->where(function ($q) {
            $q->where('daily_fine_amount', '>', 0)
              ->orWhere('fixed_penalty_amount', '>', 0);
        })
        ->chunk(50, function ($invoices) use ($calculator) {
            foreach ($invoices as $inv) {
                $calculator->recalculateAllUsersForInvoice((int) $inv['id']);
            }
        });
  }
  /* END حسابرسی جریمه ها - با کرون جاب کار میکند */


  public function invoicePayDelete($id)
  {
    $invoice_pay = invoice_pay::findOrfail($id);
    $id_invoice = $invoice_pay["id_invoice"];
    $id_user = $invoice_pay["id_user"];
    $invoice_pay->delete();

    $total_user_pays = invoice_pay::where('id_invoice', $id_invoice)
      ->where('id_user', $id_user)
      ->count();
    if ($total_user_pays > 0) {
      // جریمه کاربر روی صورتحساب را محاسبه کن و اونو تو جدول جریمه ها ثبت کن
      $this->createOrUpdate_user_fine($id_user, $id_invoice);
    } else {
      $this->calcualte_user_fine_without_pay($id_invoice, $id_user);
    }

    return redirect('/admin/invoice/user/' . $id_invoice . '/' . $id_user);
  }

  public function invoiceDelete($id)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $invoice = invoice::findOrfail($id);
    $invoice_pays = invoice_pay::where("id_invoice", $invoice["id"])->get();
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay->delete();
    }
    $invoice_fines = invoice_fine::where("id_invoice", $invoice["id"])->get();
    foreach ($invoice_fines as $invoice_fine) {
      $invoice_fine->delete();
    }
    $invoice->delete();
    return redirect('/admin/invoice');
  }

  /**
   * حذف کاربر از یک فاکتور به هماری تمامی پرداخت ها و جرائم مربوط به آن
   */
  public function invoiceUserPaysFinesDelete($id_invoice, $id_user)
  {
    if (!session()->has('admin')) {
      return redirect('/');
      exit();
    }
    $invoice = invoice::findOrFail($id_invoice);
    $invoice_pays = invoice_pay::where("id_invoice", $invoice->id)->where("id_user", $id_user)->get();
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay->delete();
    }
    $invoice_fines = invoice_fine::where("id_invoice", $invoice->id)->where("id_user", $id_user)->get();
    foreach ($invoice_fines as $invoice_fine) {
      $invoice_fine->delete();
    }
    $id_users = json_decode($invoice->id_users, true) ?: [];
    $id_users = array_values(array_diff($id_users, [$id_user]));
    $invoice->id_users = json_encode($id_users);
    $invoice->update();
    if (count($id_users) > 0) {
      return redirect('/admin/invoice/' . $id_invoice);
    } else {
      return redirect('/admin/invoice');
    }
  }
  public function showform(int $_id, $_idf, $role = 'admin')
  {
    if (!session()->has('admin')) return redirect('/');
    $_info_form = form::findOrfail($_idf, ['id', 'name', 'fild']);
    $form_fild = json_decode($_info_form['fild'], 1);
    $user_forms = user_form::join('users', 'users.id', 'id_user')->where('user_forms.id', $_id)->get(['user_forms.id', 'id_user', 'id_form', 'form', 'users.id AS ID_User', 'mobile', 'name', 'name2', 'kod', 'hash']);

    foreach ($user_forms as $user_form) {
      $user_form->form = json_decode($user_form->form, true); // Decode JSON to array
    }
    $user_form = $user_forms->first(); // Get the first record

    return view("admin.form.userPagedetail", [
      'form_fild' => $form_fild,
      'user_forms' => $user_form,
      'user_form' => $user_form,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user),
    ]);
  }
  public function editform(int $_id, $_idf, $role = 'admin')
  {
    if (!session()->has('admin')) return redirect('/');
    $_info_form = form::findOrfail($_idf, ['id', 'name', 'fild']);
    $form_fild = json_decode($_info_form['fild'], 1);
    $user_forms = user_form::join('users', 'users.id', 'id_user')->where('user_forms.id', $_id)->get(['user_forms.id', 'id_user', 'id_form', 'form', 'users.id AS ID_User', 'mobile', 'name', 'name2', 'kod', 'hash']);

    foreach ($user_forms as $user_form) {
      $user_form->form = json_decode($user_form->form, true); // Decode JSON to array
    }
    $user_form = $user_forms->first(); // Get the first record

    return view("admin.form.userPageEdit", [
      'form_fild'            => $form_fild,
      'user_forms'           => $user_form,
      'user_form'            => $user_form,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      // Bug 5: needed by the view to build links to existing uploaded files
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user),
    ]);
  }
  public function copy_form($id)
  {
    if (!session()->has('admin')) return redirect('/');
    $_info_form = form::findOrfail($id);
    $_insert_form = new form();
    $_insert_form->name = $_info_form->name; //نام
    $_insert_form->text = $_info_form->text; //توضیحات
    $_insert_form->fild = $_info_form->fild;
    $_insert_form->hash = (string) $this->RandomString();

    $_insert_form->id_grop = $_info_form->id_grop;
    if ($_info_form->required == 1) $_insert_form->required = 1;
    if ($_info_form->saving_disabled == 1);
    $_insert_form->saving_disabled = 1;
    $_insert_form->save();
    $inputGroupIds = $_insert_form->id_grop; // Example: [1, 2, 3]
    $groupIds = [];
    // Handle different input formats
    if (is_string($inputGroupIds)) {
      // Check if the input is a JSON-encoded string
      $decodedGroupIds = json_decode($inputGroupIds, true);

      // If decoding is successful and results in an array, use it
      if (json_last_error() === JSON_ERROR_NONE && is_array($decodedGroupIds)) {
        $groupIds = $decodedGroupIds;
      } else {
        // If decoding fails, treat the input as a comma-separated string
        $groupIds = explode(',', $inputGroupIds);
      }
    } elseif (is_array($inputGroupIds)) {
      // If input is already an array, use it directly
      $groupIds = $inputGroupIds;
    }

    // Sanitize: Convert all values to integers and filter out non-numeric values
    $groupIds = array_filter(array_map('intval', $groupIds), function ($id) {
      return $id > 0;
    });
    if ($_info_form->confirm) {
      $users = DB::table('users')
        ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
        ->whereIn('user_grops.grop_id', $groupIds)
        ->select('users.*')
        ->distinct()
        ->get();

      foreach ($users as $user) {
        $u_form = new user_form();
        $u_form->id_user = $user->id;
        $u_form->id_form = $_insert_form->id;
        $u_form->form = $_insert_form;
        $u_form->save();
      } //غیرفعال بودن ثبت
    }
    return redirect("/admin/form");
  }
  public function  getUserGrop(Request $request)
  {
    if (!session()->has('admin')) return redirect('/');

    $request->validate([
      'groupId' => 'required|integer',
    ]);
    if (!session()->has('admin')) return redirect('/');
    // Retrieve user groups based on the provided group ID
    $userGroups = usergrop::where('id_grop', $request->input('groupId'))->get(['id', 'name']);

    // Return the result as JSON
    return response()->json($userGroups);
  }



  public function pmNotShow($id)
  {
    if (!session()->has('admin')) return redirect('/');
    $pm = pm::findOrfail($id)->toArray();
    $pm_idgrop = $pm["id_grop"];
    $id_check = $pm["id_users"];
    $data = json_decode($id_check, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($data['Grop Users'])) {
      $id_users = user_grop::whereIn("id_usergrop", $data['Grop Users'])->pluck("user_id")->toArray();
    } else {
      $id_users = is_array($data) ? $data : [];
    }
    $seen_pms = pm_show::where('id_pm', $id)->pluck('id_user')->toArray();
    $unseen_users = array_diff($id_users, $seen_pms);
    $unseen_users_info = user::whereIn('id', $unseen_users)->get();



    return view('admin.pm.nonshow', ["un_Showpm" => $unseen_users_info, "pm" => $pm]);
  }
  public function invoicexlspage()
  {
    if (!session()->has('admin')) return redirect('/');
    $grops = grop::all();
    return view("admin.invoice.xlspage", ['grops' => $grops]);
  }
  public function invoicexls($request)
  {
    if (!session()->has('admin')) return redirect('/');
    $file = json_decode($request["file"], true);
    $grop = grop::findOrfail($request["grop"]);
    foreach ($file["invoice"] as $invoice2) :
      if (isset($invoice2["title"])) {
        $invoice = invoice::where("title", $invoice2["title"])->get();
        if (count($invoice) == 0) {
          $invoice = new invoice();
          $invoice->title = $invoice2["title"];
          if (isset($invoice2["text"])) {
            $invoice->text = $invoice2["text"];
          };
          if (isset($invoice2["price"])) {
            $invoice->price = $invoice2["price"];
          };
          if (isset($invoice2["am_start"])) {
            $invoice->am_start = $invoice2["am_start"];
          };
          if (isset($invoice2["am_end"])) {
            $invoice->am_end = $invoice2["am_end"];
          };
          if (isset($invoice2["am_end"])) {
            $invoice->am_end = $invoice2["am_end"];
          };
          if (isset($invoice2["daily_fine_amount"])) {
            $invoice->daily_fine_amount = $invoice2["daily_fine_amount"];
          };
          if (isset($invoice2["fixed_penalty_amount"])) {
            $invoice->daily_fine_amount = $invoice2["fixed_penalty_amount"];
          };
          if (isset($invoice2["number"])) {
            $invoice->number = $invoice2["number"];
          };
          if (isset($request["grop"])) {
            $invoice->grop = $invoice2["grop"];
          };
          if (isset($request["user"])) {
            $invoice->user = $invoice2["user"];
          };
          $invoice->save();
        };
      }
    endforeach;
  }

  public function getUserGroup(Request $request)
  {
    $userId = $request->input('user_id');
    $grop = user_grop::all();

    return response()->json($grop);
  }

  //    public function shopAdd(Request $request)
  //    {
  //        if(!session()->has('admin')){return redirect('/');exit();}
  //        $shop=new shop();
  //
  //        $shop->name=$request["name"];
  //        $shop->text=$request["text"];
  //        $shop->price=$request["price"];
  //        $shop->number=$request["number"];
  //
  //
  //
  //        $shop->save();
  //
  //        return redirect("/admin/user");
  //    }
  //    public function deleteShop($id)
  //    {
  //        if(!session()->has('admin')){return redirect('/');exit();}
  //        $shop=shop::findOrfail($id);
  //
  //        $shop->delete();
  //        return redirect("/admin/shop/");
  //
  //    }
  /* END صورتحساب */

  /* START کیف پول */
  public function walletShow($managerId)
  {
    if (!session()->has('admin')) {
      return redirect('/');
    }
    $walletService = app(WalletService::class);
    $wallet = $walletService->getOrCreate($managerId);
    $transactions = WalletTransaction::where('wallet_id', $wallet->id)
        ->orderBy('created_at', 'desc')
        ->get();
    return view('admin.wallet.show', compact('wallet', 'transactions', 'managerId'));
  }

  public function walletCredit(Request $request, $managerId)
  {
    if (!session()->has('admin')) {
      return redirect('/');
    }
    $request->validate([
        'amount'      => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
    ]);
    app(WalletService::class)->credit($managerId, $request->amount, $request->description, session('admin'));
    return back()->with('success', 'کیف پول شارژ شد.');
  }

  public function walletSetThreshold(Request $request, $managerId)
  {
    if (!session()->has('admin')) {
      return redirect('/');
    }
    $request->validate(['min_threshold' => 'required|numeric|min:0']);
    app(WalletService::class)->setThreshold($managerId, $request->min_threshold);
    return back()->with('success', 'حداقل موجودی تعیین شد.');
  }
  /* END کیف پول */
}
