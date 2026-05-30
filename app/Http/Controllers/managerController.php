<?php

namespace App\Http\Controllers;

use App\advertising_pay;
use App\Convert_Number_To_Persian_Words;
use App\contactu;
use App\crop;
use App\crop_pay;
use App\form;
use App\grop;
use App\invoice;
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
use App\setting;
use App\user_form;
use App\user_grop;
use App\usergrop;
use App\BulkPaymentCredit;
use App\Services\BulkPaymentService;
use App\Services\InvoicePenaltyCalculator;
use App\Services\PenaltyRecalculationService;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kavenegar;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class managerController extends Controller
{
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













  public static function managerAccessLevel($id)
  {
    if (!session()->has('manager') || !session()->has('id_manager')) {
      return false;
    }

    static $access_list = null;

    if ($access_list === null) {
      // Query manager directly — querying user::where() applies CollectionManagerScope
      // which filters out the manager's own account (not in user_grops), returning null.
      $manager = manager::where("id_user", session("id_manager"))->first();
      $access_list = [];
      if ($manager && $manager["access"] && $manager["access"] !== "null") {
        $decoded = json_decode($manager["access"], 1);
        $access_list = is_array($decoded) ? $decoded : [];
      }
    }

    foreach ($access_list as $access) {
      if ($access == $id) {
        return true;
      }
    }
    return false;
  }












  public function home(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    return view('manager.dashboard');
  }


  public function grops(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(54) and !$this->managerAccessLevel(55)) {
      return redirect('/');
      exit();
    }
    if ($this->managerAccessLevel(54)) {
      $grops = grop::all();
    } elseif ($this->managerAccessLevel(55)) {
      $manager = manager::where("id_user", session("id_manager"))->first();
      $grops = grop::where("id", $manager["id_grop"])->get();
    }

    return view('manager.grop', ["grops" => $grops]);
  }

  public function gropAddPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(53)) {
      return redirect('/');
      exit();
    }
    return view('manager.gropAdd');
  }

  public function addGrop(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(53)) {
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
    $grop->user_menu_access = '[{"menu":"bills","hide":"0"},{"menu":"ticket","hide":"0"},{"menu":"advertisment","hide":"0"},{"menu":"products","hide":"0"}]';

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
    $grop->save();

    $usergrop = new usergrop();
    $usergrop->name = "کاربری عادی";
    $usergrop->text = "کاربری عادی";
    $usergrop->id_grop = $grop["id"];
    $usergrop->normal = 1;
    $usergrop->save();

    return redirect("/manager/grop")->with('success', 'مجموعه با موفقیت ایجاد شد.');
  }

  public function gropEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(56)) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    return view('manager.gropEdit', ['grop' => $grop]);
  }

  public function gropEdit($id, Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(56)) {
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


    $grop->save();
    return redirect("/manager/grop");
  }

  public function gropCodeEdit($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(57)) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $grop->code = rand(1000000, 9999999);
    $grop->save();
    return redirect("/manager/grop");
  }

  public function gropDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(58)) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);
    $user_grops = user_grop::where("grop_id", $grop["id"])->get();
    if (count($user_grops) == 0) {
      $grop->delete();
      return redirect("/manager/grop")->with('success', 'مجموعه با موفقیت حذف شد.');
    }
    return redirect("/manager/grop")->with('error', 'امکان حذف مجموعه‌ای که دارای کاربر است وجود ندارد.');
  }


  public function manager($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(62)) {
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


    return view('manager.gropManager', ["grop" => $grop, "managers" => $managers]);
  }
  public function gropManagerAddPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(63)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($id);

    $users = user::all();
    $user_a = [];
    foreach ($users as $user) {
      $existing_managers = manager::where("id_grop", $id)->where("id_user", $user["id"])->get();
      $user_grop = user_grop::where("grop_id", $id)->where("user_id", $user["id"])->get();
      if (count($existing_managers) == 0) {
        if (count($user_grop) == 1) {
          $user_a[] = $user;
        }
      }
    }
    $setting = setting::where("name", "access")->first();
    $usergrops = usergrop::where("id_grop", $id)->get();
    $managers_list = manager::where("id_grop", $id)->get();

    return view('manager.gropManagerAdd', ["grop" => $grop, "manager" => $manager, "users" => $user_a, "setting" => $setting, "managers" => $managers_list, "usergrops" => $usergrops]);
  }

  public function gropManagerAdd(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(63)) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);

    $manager = new manager();
    $manager->id_user = $request->input("user");
    $manager->id_grop = $grop["id"];
    $manager->access = json_encode($request->input("access", []));
    //        $manager->accessGropUser=json_encode($request["accessGropUser"]);
    $manager->save();

    return redirect("/manager/grop/manager/" . $id);
  }
  public function deleteManager($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(65)) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $gropId = $manager["id_grop"];
    $manager->delete();
    return redirect("/manager/grop/manager/" . $gropId);
  }
  public function gropManagerEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(64)) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $manager["user"] = user::findOrfail($manager["id_user"]);
    $grop = grop::findOrfail($manager["id_grop"]);
    $setting = setting::where("name", "access")->first();

    return view('manager.gropManagerEdit', ["manager" => $manager, "setting" => $setting, "grop" => $grop]);
  }

  public function gropManagerEdit($id, Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(64)) {
      return redirect('/');
      exit();
    }
    $manager = manager::findOrfail($id);
    $manager->access = json_encode($request->input("access", []));
    $manager->save();
    return redirect("/manager/grop/manager/" . $manager["id_grop"]);
  }


















  public function gropUser($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(59) and !$this->managerAccessLevel(60) and !$this->managerAccessLevel(61)) {
      return redirect('/');
      exit();
    }

    $grop = grop::findOrfail($id);
    $usergrops = usergrop::where("id_grop", $id)->get();

    return view('manager.gropUser', ["grop" => $grop, "usergrops" => $usergrops]);
  }
  public function gropUserAddPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(59)) {
      return redirect('/');
      exit();
    }

    $grop = grop::findOrfail($id);
    return view('manager.gropUserAdd', ["grop" => $grop]);
  }
  public function gropUserAdd(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(59)) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($id);

    $usergrop = new usergrop();
    $usergrop->name = $request["name"];
    $usergrop->text = $request["text"];
    $usergrop->id_grop = $grop["id"];
    $usergrop->save();

    return redirect("/manager/grop/user/" . $id);
  }
  public function gropUserEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(60)) {
      return redirect('/');
      exit();
    }
    $usergrop = usergrop::findOrfail($id);
    $grop = grop::findOrfail($usergrop["id_grop"]);
    return view('manager.gropUserEdit', ["grop" => $grop, "usergrop" => $usergrop]);
  }
  public function gropUserEdit(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(60)) {
      return redirect('/');
      exit();
    }

    $usergrop = usergrop::findOrfail($id);
    $usergrop->name = $request["name"];
    $usergrop->text = $request["text"];
    $usergrop->save();

    return redirect("/manager/grop/user/" . $usergrop["id_grop"]);
  }
  public function deleteGropUser($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(61)) {
      return redirect('/');
      exit();
    }

    $usergrop = usergrop::findOrfail($id);
    $gropId = $usergrop["id_grop"];

    if ($usergrop->normal == 1) {
      return redirect("/manager/grop/user/" . $gropId)->with('error', 'گروه کاربری پیش‌فرض قابل حذف نیست.');
    }

    $defaultUsergrop = usergrop::where('id_grop', $gropId)->where('normal', 1)->first();

    \DB::transaction(function () use ($usergrop, $defaultUsergrop) {
      if ($defaultUsergrop) {
        user_grop::where('id_usergrop', $usergrop->id)->update(['id_usergrop' => $defaultUsergrop->id]);
      }
      $usergrop->delete();
    });

    return redirect("/manager/grop/user/" . $gropId)->with('success', 'گروه کاربری با موفقیت حذف شد.');
  }


  public function access($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $setting = setting::where("name", "access")->first();
    $usergrops = usergrop::where("id_grop", $id)->get();
    $grop = grop::findOrfail($id);

    return view('manager.access', ["setting" => $setting, "accessItems" => $this->accessSettingItems($setting), "usergrops" => $usergrops, "grop" => $grop]);
  }

  public function accessGropUser($idGrop, $idGropUser)
  {
    if (!session()->has('manager')) {
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
    return view('manager.accessShow', ["managerAccess" => $managerAccess, "usergrop" => $usergrop, "grop" => $grop]);
  }

  public function accessGropUserDelete($idManagers, $idUsergrop)
  {
    if (!session()->has('manager')) {
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
    return redirect("/manager/grop/access/accessGropUser/" . $manager['id_grop'] . "/" . $idUsergrop);
  }


  public function accessSetting($idGrop, $idSetting)
  {
    if (!session()->has('manager')) {
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
    return view('manager.accessShowSetting', ["managerAccess" => $managerAccess, "setting" => $settingItem, "grop" => $grop]);
  }

  public function accessSettingDelete($idManagers, $idSetting)
  {
    if (!session()->has('manager')) {
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
    return redirect("/manager/grop/access/page/" . $manager['id_grop'] . "/" . $idSetting);
  }












  public function menuAddPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(29)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();
    $usergrops = usergrop::where("id_grop", $manager["id_grop"])->get();
    $forms = form::where("id_grop", $manager["id_grop"])->get();
    return view('manager.menu.add', ["usergrops" => $usergrops, "forms" => $forms]);
  }

  public function menuGropUser(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($request["grop"]);
    $usergrops = usergrop::where("id_grop", $grop["id"])->get();
    echo json_encode($usergrops);
  }

  private function menuItemsValidation($_request)
  {
    if (
      //گروه های کاربری مجموعه
      !isset($_request['gropUser'])
      ||
      !is_array($_request['gropUser'])

      ||

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
    if (empty($_user_grops_selected)) return false;

    return $_user_grops_selected;
  }

  public function menuAdd(Request $_request)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(29)
    ) return redirect('/');

    $_user_grops_selected = $this->menuItemsValidation($_request); //گروه های کاربری مجموعه انتخاب شده
    if (!$_user_grops_selected) return redirect('/manager/menu/add');
    $_implode_user_grops_selected = implode(',', $_user_grops_selected);

    $_grops = (object) grop::whereRaw("(SELECT COUNT(id) FROM usergrops WHERE id IN ($_implode_user_grops_selected) AND id_grop=grops.id)=" . COUNT($_user_grops_selected))->findOrfail(session('id_grop_manager'), ['id']);
    $_menu = new menu();
    $_menu->id_grop = $_grops['id']; //آیدی مجموعه
    $_menu->id_usergrop = $_implode_user_grops_selected; //گروه های کاربری مجموعه
    $_menu->name = (string) $_request['name'];
    $_menu->text = (string) htmlspecialchars($_request['text']);
    $_menu->id_form = (int) $_request['form']; //فرم
    $_menu->save();

    return redirect('/manager/menu');
  }

  public function menuPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(31)
    ) return redirect('/');

    $gropId  = session('id_grop_manager');
    $_menus  = menu::where('id_grop', $gropId)->get();

    $grop         = grop::find($gropId, ['id', 'name']);
    $allUserGropIds = [];
    foreach ($_menus as $_menu) {
      foreach (array_filter(explode(',', $_menu['id_usergrop'])) as $id) $allUserGropIds[] = (int) $id;
    }
    $allUserGrops = $allUserGropIds
      ? usergrop::whereIn('id', array_unique($allUserGropIds))->get(['id', 'name'])->keyBy('id')
      : collect();

    foreach ($_menus as $_menu) {
      $_menu['grop']             = $grop;
      $ugIds                     = array_filter(explode(',', $_menu['id_usergrop']));
      $_menu['names_user_grops'] = collect($ugIds)->map(function ($id) use ($allUserGrops) {
        return $allUserGrops->get((int) $id);
      })->filter()->values();
    }

    return view('manager.menu.page', ['menus' => $_menus]);
  }

  public function menuEditPage(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(32)
    ) return redirect('/');

    $_id_grop_manager = session('id_grop_manager'); //آیدی مجموعه

    $_menu = menu::where('id_grop', $_id_grop_manager)->findOrfail($_id, ['id', 'id_grop', 'id_usergrop', 'text', 'name', 'id_form']);
    $_menu['text'] = $_menu['text'] ? htmlspecialchars_decode($_menu['text']) : ''; //توضیحات

    return view('manager.menu.edit', [
      'menu' => $_menu,
      'user_grops' => usergrop::where('id_grop', $_menu['id_grop'])->get(),
      'user_grops_selected' => strstr($_menu['id_usergrop'], ',') ? explode(',', $_menu['id_usergrop']) : array($_menu['id_usergrop']), //گروه های کاربری مجموعه انتخاب شده
      'forms' => form::where('id_grop', $_id_grop_manager)->get()
    ]);
  }

  public function menuEdit(Request $_request, int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(32)
    ) return redirect('/');

    $_user_grops_selected = $this->menuItemsValidation($_request); //گروه های کاربری مجموعه انتخاب شده
    if (!$_user_grops_selected) return redirect("/manager/menu/edit/$_id");
    $_implode_user_grops_selected = implode(',', $_user_grops_selected);

    $_grops = (object) grop::whereRaw("(SELECT COUNT(id) FROM usergrops WHERE id IN ($_implode_user_grops_selected) AND id_grop=grops.id)=" . COUNT($_user_grops_selected))->findOrfail((int) session('id_grop_manager'), ['id']);
    $_menu = menu::findOrfail($_id);
    $_menu->id_grop = $_grops['id']; //آیدی مجموعه
    $_menu->id_usergrop = $_implode_user_grops_selected; //گروه های کاربری مجموعه
    $_menu->name = (string) $_request['name'];
    $_menu->text = (string) htmlspecialchars($_request['text']);
    $_menu->id_form = (int) $_request['form']; //فرم
    $_menu->save();

    return redirect("/manager/menu/edit/$_id");
  }

  public function menuDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(32)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();
    if (!$manager) return redirect('/');
    $menu = menu::where("id", $id)->where("id_grop", $manager["id_grop"])->first();
    if (!$menu) return redirect("/manager/menu/");
    $menu->delete();
    return redirect("/manager/menu/");
  }




  public function menuUnderPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(31)) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    $unders = under::where("id_menu", $id)->get();
    return view('manager.menu.under.page', ["menu" => $menu, "unders" => $unders]);
  }
  public function menuUnderAddPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(30)) {
      return redirect('/');
      exit();
    }
    $menu = menu::findOrfail($id);
    $manager = manager::where("id_user", session("id_manager"))->first();
    $forms = form::where("id_grop", $manager["id_grop"])->where('id_grop', 'LIKE', '%"' . $manager['id_grop'] . '"%', 'OR')->get();
    return view('manager.menu.under.add', ["menu" => $menu, "forms" => $forms]);
  }
  public function menuUnderAdd(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(30)) {
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
    return redirect("/manager/menu/under/" . $menu["id"]);
  }
  public function menuUnderEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(32)) {
      return redirect('/');
      exit();
    }
    $under = under::findOrfail($id);
    $manager = manager::where("id_user", session("id_manager"))->first();
    $forms = form::where("id_grop", $manager["id_grop"])->get();
    return view('manager.menu.under.edit', ["under" => $under, "forms" => $forms]);
  }
  public function menuUnderEdit(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(32)) {
      return redirect('/');
      exit();
    }
    $under = under::findOrfail($id);
    $under->name = $request["name"];
    $under->text = $request["text"];
    $under->id_form = $request["form"];
    $under->save();
    return redirect("/manager/menu/under/" . $under["id_menu"]);
  }
  public function underDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(32)) {
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
    return redirect("/manager/menu/under/" . $menu_id);
  }



  public function userChangeGrop(Request $request)
  {
    if (!session()->has('manager')) {
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




  public function  getUserGrop(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $request->validate([
      'groupId' => 'required|integer',
    ]);
    // Retrieve user groups based on the provided group ID
    $userGroups = usergrop::where('id_grop', $request->input('groupId'))->get(['id', 'name']);

    // Return the result as JSON
    return response()->json($userGroups);
  }



  public function userPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(39)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();

    $canManageHidden = $this->managerAccessLevel(78);
    $showHidden = $canManageHidden && request()->has('show_hidden');

    $hasHiddenCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_hidden');
    $mgUserColumns = [
        'users.id', 'users.hash', 'users.name', 'users.name2', 'users.kod',
        'users.lawyer_name', 'users.mobile', 'users.phone', 'users.state',
        'users.city', 'users.active_am', 'users.inactive', 'users.password'
    ];
    if ($hasHiddenCol) {
        $mgUserColumns[] = 'users.is_hidden';
    }
    $filterUsergropId = request('usergrop_id');
    $users = user::with(['user_grops.usergrop'])
        ->join('user_grops', 'user_grops.user_id', '=', 'users.id')
        ->leftJoin('invoice_fines', 'invoice_fines.id_user', '=', 'users.id')
        ->leftJoin('invoices', 'invoices.id', '=', 'invoice_fines.id_invoice')
        ->select($mgUserColumns)
        ->selectRaw('SUM(COALESCE(invoices.price, 0)) as total_invoices')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_penalty, 0)) as total_penalties')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_paid, 0)) as total_paid')
        ->where('user_grops.grop_id', $manager["id_grop"])
        ->when($filterUsergropId, function ($q) use ($filterUsergropId) {
          return $q->where('user_grops.id_usergrop', $filterUsergropId);
        })
        ->when($hasHiddenCol && !$showHidden, function ($q) {
          return $q->where('users.is_hidden', false);
        })
        ->groupBy($mgUserColumns)
        ->orderBy('users.id', 'asc')
        ->get();

    $grop = user_grop::select('user_id', 'grop_id')->where('grop_id', $manager["id_grop"])->get();
    $usergrops = usergrop::where('id_grop', $manager["id_grop"])->get(['id', 'name']);

    return view('manager.user.page', [
        "users" => $users,
        "grop" => $grop,
        "canManageHidden" => $canManageHidden,
        "usergrops" => $usergrops,
        "selected_usergrop_id" => $filterUsergropId,
    ]);
  }
  public function userAdd()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(36)) {
      return redirect('/');
      exit();
    }

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($manager["id_grop"]);
    $usergrops = usergrop::where("id_grop", $grop["id"])->get();

    return view('manager.user.add', ["usergrops" => $usergrops]);
  }
  public function user(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(36)) {
      return redirect('/');
      exit();
    }
    $request->validate([
      'mobile' => 'required|unique:users,mobile',
      'phone'  => 'nullable|unique:users,phone',
      'hash'   => 'nullable|unique:users,hash',
      'kod'    => 'nullable|min:10|unique:users,kod',
    ], [
      'mobile.unique' => 'این شماره موبایل قبلاً ثبت شده است',
      'phone.unique'  => 'این شماره تلفن قبلاً ثبت شده است',
      'hash.unique'   => 'این کد اختصاصی قبلاً ثبت شده است',
      'kod.min'       => 'کد ملی باید ۱۰ رقم باشد',
      'kod.unique'    => 'این کد ملی قبلاً ثبت شده است',
    ]);
    $user = new user();
    $user->mobile = $request["mobile"];
    $user->name = $request["name"];
    $user->name2 = $request["name2"];
    $user->phone = $request["phone"] ?: null;
    $user->kod = $request["kod"] ?: null;
    $user->lawyer_name = $request["lawyer_name"] ?: null;
    $user->hash = $request["hash"] ?: null;
    $user->state = $request["state"];
    $user->city = $request["city"];
    $user->save();

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($manager["id_grop"]);

    $user_grop = new user_grop();
    $user_grop->user_id = $user["id"];
    $user_grop->grop_id = $grop["id"];
    $user_grop->id_usergrop = $request["user_grop_id"];
    $user_grop->save();

    return redirect("/manager/user/");
  }
  public function userEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(41) and !$this->managerAccessLevel(42) and !$this->managerAccessLevel(43)) {
      return redirect('/');
      exit();
    }
    $user = user::findOrfail($id);
    return view('manager.user.edit', ["user" => $user]);
  }
  public function userEdit(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $validationRules = [];
    $validationMessages = [];
    if ($this->managerAccessLevel(41)) {
      $validationRules['mobile'] = ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile,' . $id];
      $validationMessages['mobile.unique'] = 'این شماره موبایل قبلاً ثبت شده است';
      $validationMessages['mobile.regex']  = 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد';
    }
    if ($this->managerAccessLevel(42)) {
      $preventKodRequired = setting::where('name', 'prevent_user_nationalid_required_unique')->first();
      $kodRule = ($preventKodRequired && $preventKodRequired->input1)
        ? 'nullable|digits:10|unique:users,kod,' . $id
        : 'required|digits:10|unique:users,kod,' . $id;

      $validationRules['phone'] = 'nullable|unique:users,phone,' . $id;
      $validationRules['hash']  = 'required|unique:users,hash,' . $id;
      $validationRules['kod']   = $kodRule;
      $validationMessages['phone.unique']  = 'این شماره تلفن قبلاً ثبت شده است';
      $validationMessages['hash.required'] = 'کد اختصاصی الزامی است';
      $validationMessages['hash.unique']   = 'این کد اختصاصی قبلاً ثبت شده است';
      $validationMessages['kod.required']  = 'کد ملی الزامی است';
      $validationMessages['kod.digits']    = 'کد ملی باید دقیقاً ۱۰ رقم باشد';
      $validationMessages['kod.unique']    = 'این کد ملی قبلاً ثبت شده است';
    }
    if (!empty($validationRules)) {
      $request->validate($validationRules, $validationMessages);
    }
    $user = user::findOrfail($id);
    if ($this->managerAccessLevel(41)) {
      $user->mobile = $request["mobile"];
    }
    if ($this->managerAccessLevel(42)) {
      $user->name = $request["name"];
      $user->name2 = $request["name2"];
      $user->phone = $request["phone"] ?: null;
      $user->kod = $request["kod"] ?: null;
      $user->hash = $request["hash"] ?: null;
      $user->lawyer_name = $request["lawyer_name"] ?: null;
      $user->state = $request["state"];
      $user->city = $request["city"];
    }
    if ($this->managerAccessLevel(43)) {
      $user->active_am = $request["active_am"];
    }
    $user->save();

    return redirect("/manager/user/");
  }
  public function checkRegisterEdit(Request $request)
  {
    $found = user::withoutGlobalScopes()->where("mobile", $request->mobile)->where("id", "!=", $request->id)->exists();
    echo $found ? 100 : 0;
  }
  /**
   * فعال/غیرفعل کردن تکی کاربر
   */
  public function userInactive($id)
  {
    if (!session()->has('manager')) return redirect('/');

    $user = user::findOrfail($id);
    if (isset($user)) {
      $user->inactive = !$user->inactive;
      $user->save();
    } else {
      return redirect('/');
      exit();
    }

    return redirect('/manager/user');
  }

  public function userToggleHidden($id)
  {
    if (!session()->has('manager')) return redirect('/');
    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_hidden')) return back();
    $user = user::withoutGlobalScopes()->findOrFail($id);
    $user->is_hidden = !$user->is_hidden;
    $user->save();
    return back();
  }

  public function userAddXlsxPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(38)) {
      return redirect('/');
      exit();
    }

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($manager["id_grop"]);
    $usergrops = usergrop::where("id_grop", $grop["id"])->get();

    return view('manager.user.addXlsx', ["usergrops" => $usergrops]);
  }
  public function userAddXlsx(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(38)) {
      return redirect('/');
      exit();
    }

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($manager["id_grop"]);

    $file = json_decode($request["file"], 1);
    foreach ($file["user"] as $user2) {
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
          $user_grop->id_usergrop = $request["user_grop_id"];
          $user_grop->save();
        }
      }
    }
    echo 100;
  }

  public function userPayPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $id)->select('users.*')->firstOrFail();
    $pays = pay::where("id_user", $user["id"])->where("active", 1)->get();
    foreach ($pays as $pay) {
      $pay["product"] = product::findOrfail($pay["id_product"]);
      $pay["am"] = verta($pay["updated_at"]);
    }
    return view('manager.user.userPay', ["user" => $user, "pays" => $pays]);
  }

  public function userForgotPassword($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(45)) {
      return redirect('/');
      exit();
    }

    $user = user::findOrfail($id);
    $user->password = null;
    $user->save();
    return redirect('/manager/user');
  }

  public function userChangePassword($id, Request $request)
  {
    if (!session()->has('manager')) return response()->json(['error' => 'Unauthorized'], 401);
    if (!$this->managerAccessLevel(45)) return response()->json(['error' => 'Forbidden'], 403);

    $request->validate([
      'password' => 'required|min:8|confirmed',
    ]);

    $user = user::where('id', $id)
      ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->select('users.*')
      ->first();

    if (!$user) return response()->json(['error' => 'کاربر یافت نشد یا دسترسی ندارید'], 403);

    $user->password = \Hash::make($request->password);
    $user->save();

    return response()->json(['success' => 'رمز عبور با موفقیت تغییر یافت']);
  }

  public function userDelete($id)
  {
    if (!session()->has('manager')) return redirect('/');

    $user = user::where('users.id', $id)
      ->join('user_grops', 'users.id', '=', 'user_grops.user_id')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->select('users.*')
      ->first();

    if (!$user) return redirect('/manager/user');

    $user->delete();
    return redirect('/manager/user');
  }


  public function userDataAllPage()
  {
    if (
      !session()->has('manager')
      &&
      !$this->managerAccessLevel(73)
    ) return redirect('/');

    $_users_invoices = array();

    $_invoices = invoice::where('id_grop', session('id_grop_manager'))->whereRaw('am_end!=""')->orderBy('id', 'DESC')->get(['id_users', 'price']);
    foreach ($_invoices as $_invoice) :
      $_id_users_invoice = !empty($_invoice['id_users']) ? json_decode($_invoice['id_users'], true) : array();
      $_userColumns = ['users.id', 'users.mobile', 'users.name', 'users.name2', 'users.kod', 'users.hash', 'users.gender', 'users.marriage', 'users.birth', 'users.father', 'users.mail', 'users.number_vahed', 'users.number_block', 'users.number_block2', 'users.warehouse', 'users.parking', 'users.location', 'users.form_j', 'users.contract', 'users.am_start_contract', 'users.am_end_contract', 'users.unit', 'users.namename2', 'users.kodkod2', 'users.numbernumber2', 'users.significant', 'users.theory', 'users.tashati', 'users.other'];
      if (array_key_exists('Grop Users', $_id_users_invoice) && !empty($_id_users_invoice['Grop Users'])) : //گروه های کاربری مجموعه
        $_user_ids = user_grop::whereIn('id_usergrop', $_id_users_invoice['Grop Users'])->pluck('user_id');
        $_info_users_invoice = $_user_ids->isNotEmpty() ? user::select($_userColumns)->whereIn('users.id', $_user_ids)->get() : collect();
      elseif (!empty($_id_users_invoice)) : //کاربر انتخاب شده بود
        $_info_users_invoice = user::select($_userColumns)->whereIn('users.id', array_values($_id_users_invoice))->get();
      else :
        $_info_users_invoice = collect();
      endif;

      foreach ($_info_users_invoice as $_info_user_invoice) :
        $_users_invoices[$_info_user_invoice['id']] = $_info_user_invoice;
        $_users_invoices[$_info_user_invoice['id']]['invoice_price'] = (isset($_users_invoices[$_info_user_invoice['id']]['invoice_price']) ? $_users_invoices[$_info_user_invoice['id']]['invoice_price'] : 0) + $_invoice['price'];
      endforeach;
    endforeach;

    foreach ($_users_invoices as $user) :
      $user["price"] = $user["price_fine"] = 0;
      foreach (invoice_pay::where("id_user", $user["id"])->get(['price']) as $invoice_pay) $user["price"] += $invoice_pay["price"];
      foreach (invoice_fine::where("id_user", $user["id"])->get(['price']) as $invoice_fine) $user["price_fine"] += $invoice_fine["price"];
    endforeach;

    return view('manager.user.dataAll', ["users" => $_users_invoices]);
  }


  public function userDataPage(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(73)
    ) return redirect('/');

    $_user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $_id)->select('users.*')->firstOrFail();
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

    return view('manager.user.data', [
      'user' => $_user,
      'grop' => !empty($_user_grop) ? grop::findOrfail($_user_grop['grop_id'], ['name']) : array(),
      'price' => ($_sum_price_invoices_user + $price_fine) - $price
    ]);
  }


  public function userDataEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(74)) {
      return redirect('/');
      exit();
    }

    $user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $id)->select('users.*')->firstOrFail();
    return view('manager.user.dataEdit', ["user" => $user]);
  }

  public function userDataEdit($id, Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(74)) {
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
    return redirect('/manager/user/data/' . $user["id"]);
  }



  public function userGropPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(46)) {
      return redirect('/');
      exit();
    }
    $user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $id)->select('users.*')->firstOrFail();
    $user_grops = user_grop::where("user_id", $user["id"])->get();
    foreach ($user_grops as $user_grop) {
      $user_grop["grop"] = grop::findOrfail($user_grop["grop_id"]);
      $user_grop["usergrop"] = usergrop::findOrfail($user_grop["id_usergrop"]);
      $user_grop["am"] = verta($user_grop["created_at"]);
    }
    return view('manager.user.grop', ["user" => $user, "user_grops" => $user_grops]);
  }

  public function userGropDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(49)) {
      return redirect('/');
      exit();
    }
    $user_grop = user_grop::findOrfail($id);
    $user_id = $user_grop["user_id"];

    if ($user_grop->grop && $user_grop->grop->lock_user_group_usergroup) {
      return redirect('/manager/user/grop/' . $user_id)->withInput(['message' => 'تغییر تنظیمات مجموعه و گروه کاربری برای مجموعه این کاربر قفل شده است!']);
    }

    $user_grop->delete();

    return redirect('/manager/user/grop/' . $user_id);
  }

  public function userGropAddPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(47)) {
      return redirect('/');
      exit();
    }
    $grop = grop::where('id', session('id_grop_manager'))->first();

    if ($grop && $grop->lock_user_group_usergroup == true) {
      return view('manager.user.gropAdd', ["lock_user_group_usergroup" => $grop->lock_user_group_usergroup]);
    }

    $user = user::findOrfail($id);
    $manager = manager::where("id_user", session("id_manager"))->first();
    $usergrops = usergrop::where("id_grop", $manager["id_grop"])->get();

    return view('manager.user.gropAdd', ["user" => $user, "usergrops" => $usergrops]);
  }

  public function userGropAdd($id, Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(47)) {
      return redirect('/');
      exit();
    }

    $user = user::findOrfail($id);
    $manager = manager::where("id_user", session("id_manager"))->first();
    // جلوگیری از ثبت بیش از یک مجموعه برای یک گروه در پنل مدیر مجموعه
    $user_grops_count = user_grop::where('user_id', $id)->count();
    if ($user_grops_count >= 1) {
      $usergrops = usergrop::where("id_grop", $manager["id_grop"])->get();
      return view('manager.user.gropAdd', ["user" => $user, "usergrops" => $usergrops, 'prevent_add_grop_to_user' => true])
        ->withErrors('امکان ثبت بیش از یک مجموعه وجود ندارد.');
    }
    $usergrop = usergrop::findOrfail($request["gropUser"]);
    if ($usergrop["id_grop"] == $manager["id_grop"]) {
      $user_grop = new user_grop();
      $user_grop->user_id = $user["id"];
      $user_grop->grop_id = $manager["id_grop"];
      $user_grop->id_usergrop = $usergrop["id"];
      $user_grop->save();
      return redirect('/manager/user/grop/' . $user["id"]);
    } else {
      return redirect('/');
      exit();
    }
  }


















  public function settingPage()
  {
    if (!session()->has('manager')) {
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
    $title_tag = setting::where("name", "title_tag")->first();
    $site_logo = setting::where("name", "site_logo")->first();
    return view('manager.setting', ["img_login" => $img_login, "img_register" => $img_register, "description" => $description, "contactu" => $contactu, "name_login" => $name_login, "name_register" => $name_register, "register_pass" => $register_pass, "code_grop" => $code_grop, "code_grop_none" => $code_grop_none, "title_tag" => $title_tag, "site_logo" => $site_logo]);
  }

  public function setting(Request $request)
  {
    if (!session()->has('manager')) {
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

    $title_tag = setting::where("name", "title_tag")->first();
    if (!$title_tag) {
      $title_tag = new setting();
      $title_tag->name = 'title_tag';
    }
    $title_tag->input1 = $request['title_tag'] ?? '';
    $title_tag->save();

    if ($file3 = $request->file("site_logo")) {
      $site_logo = setting::where("name", "site_logo")->first();
      if (!$site_logo) {
        $site_logo = new setting();
        $site_logo->name = 'site_logo';
      }
      $name3 = $file3->getClientOriginalName();
      $rond3 = rand(10000, 99999);
      $file3->move("img_login_register", $rond3 . $name3);
      $site_logo->input1 = $rond3 . $name3;
      $site_logo->save();
    }

    return redirect("/manager/setting");
  }


  /* START فرم ها */
  public function formPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('23')
    ) return redirect('/');

    $_id_grop_manager = session('id_grop_manager'); //آیدی مجموعه
    // $forms = form::where('id_grop', $_id_grop_manager)
    //   ->where('id_grop', 'LIKE', '%"' . $_id_grop_manager . '"%', 'OR');

    //       $sql = vsprintf(str_replace('?', "'%s'", $forms->toSql()), $forms->getBindings());
    // dd($sql);
    //      $forms=$forms->get();

    //   $formIds = Form::join('user_forms as uf', 'forms.id', '=', 'uf.id_form')
    // ->join('user_grops as ug', 'ug.user_id', '=', 'uf.id_user')
    // ->where('forms.id_grop', '2')
    // ->orWhereJsonContains('forms.id_grop', '2')
    // ->distinct()
    // ->pluck('forms.id'); // فقط IDهای یکتا را می‌گیریم
    //$forms = Form::whereIn('id', $formIds)->get();

    $query = DB::table('forms as f')
      ->select(
        'f.*',
        DB::raw('(SELECT COUNT(*) 
                   FROM user_grops ug 
                   JOIN user_forms uf ON ug.user_id = uf.id_user 
                   WHERE ug.grop_id = ' . session('id_grop_manager') . ' AND uf.id_form = f.id) AS user_count')
      )
      ->where('f.id_grop', '=', session('id_grop_manager'))
      ->orWhereRaw('JSON_CONTAINS(f.id_grop, "' . session('id_grop_manager') . '")')
      ->distinct();
    // ->join('user_forms as uf', 'f.id', '=', 'uf.id_form')
    // ->join('user_grops as ug', 'ug.user_id', '=', 'uf.id_user');
    // ->take(1)

    // $sql = $query->toSql();

    // $bindings = $query->getBindings();

    // // جایگزینی مقادیر bindings در query
    // foreach ($bindings as $binding) {
    //   $sql = preg_replace('/\?/', "'" . $binding . "'", $sql, 1);
    // }
    // die($sql);

    $forms = $query->get();

    return view('manager.form.page', ['forms' => $forms]);
  }
  /* START ایجاد */
  public function formAddPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('22')
    ) return redirect('/');

    return view('manager.form.add', [
      'types_fields_form' => parent::$_types_fields_form, //فرمت های فیلد
      'items_validation' => parent::$_validations_form,
      'items_auto_complete' => parent::$_auto_completes_form,
      'settings_auto_completes_form' => parent::$_settings_auto_completes_form
    ]);
  }
  /* START ذخیره */
  public function formAdd(Request $_request)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('22')
    ) return redirect('/');

    $_new_info_form = $this->_validation_form($_request, 'manager'); //اطلاعات جدید فرم
    if (!$_new_info_form) return redirect('/manager/form/add');

    $_insert_form = new form();
    $_insert_form->name = (string) $_request['name_form']; //نام
    $_insert_form->text = (string) $_request['text_form']; //توضیحات
    $_insert_form->fild = (string) json_encode($_new_info_form);
    $_insert_form->hash = (string) $this->RandomString();
    $_insert_form->id_grop = (int) session('id_grop_manager'); //مجموعه
    if (isset($_request['required'])) $_insert_form->required = 1; //اجباری
    if (isset($_request['saving_disabled'])) $_insert_form->saving_disabled = 1; //غیرفعال بودن ثبت
    if (isset($_request['show_onuserpanel'])) $_insert_form->show_onuserpanel = 1; //نمایش در پنل
    $_insert_form->save();

    return redirect('/manager/form');
  }
  /* END ذخیره */
  /* END ایجاد */


  /* START ویرایش */
  public function formEditPage(Request $_request, int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('22')
    ) return redirect('/');

    return $this->pageEditForm($_request, $_id, 'manager');
  }
  /* START ذخیره */
  public function formEdit(Request $_request, int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('22')
    ) return redirect('/');

    $_new_info_form = $this->_validation_form($_request, 'manager'); //اطلاعات جدید فرم
    if (!$_new_info_form) return redirect("/manager/form/edit/$_id");

    form::where('id', $_id)->update(array(
      'name' => (string) $_request['name_form'], //نام
      'text' => (string) $_request['text_form'], //توضیحات
      'fild' => (string) json_encode($_new_info_form),
      'required' => isset($_request['required']) ? 1 : NULL, //اجباری
      'show_onuserpanel' => isset($_request['show_onuserpanel']) ? 1 : NULL, //نمایش در پنل
      'saving_disabled' => isset($_request['saving_disabled']) ? 1 : NULL, //غیرفعال بودن ثبت
      'fill_form_limitation' => $_request['fill_form_limitation'], // محدویت حداکثر تعداد ثبت فرم
    ));

    return redirect("/manager/form/edit/$_id");
  }
  /* END ذخیره */
  /* END ویرایش */


  public function formUserPage(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel("25")
    ) return redirect('/');

    return $this->pageUserForm($_id, 'manager');
  }
  /* START ویرایش */
  function formUserEdit(Request $_request)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel("25")
    ) return redirect('/');

    return $this->editUserForm($_request);
  }
  /* END ویرایش */


  public function formUserAddPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel("27")) {
      return redirect('/');
      exit();
    }

    $form = form::findOrfail($id);
    $form_fild = json_decode($form["fild"], 1);

    return view('manager.form.addXlsx', ["form" => $form, "form_fild" => $form_fild]);
  }

  public function formUserAdd(Request $request, $id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel("27")
    ) return redirect('/');

    $form = form::findOrfail($id);
    $form_fild = json_decode($form["fild"], 1);

    $file = json_decode($request["file"], 1);
    foreach ($file["form"] as $fileForm) {
      if (isset($fileForm['user_id'])) {
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
    }
    echo 100;
  }

  /* START خروجی گرفتن از ستون های فرم */
  function formUserAddDownloadExcelExample(Request $_request, int $_id_form)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('27')
    ) return redirect('/');

    $this->DownloadExcelExampleColumnsForm($_id_form, form::findOrfail($_id_form)['fild']);
  }
  /* END خروجی گرفتن از ستون های فرم */


  public function formUserNoneFillDelete(int $formId, int $userId)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel("28")) return redirect('/');

    if (!user_form::where('id_user', $userId)->where('id_form', $formId)->exists()) {
      user_form::create(['id_user' => $userId, 'id_form' => $formId, 'form' => json_encode([])]);
    }

    return redirect()->back();
  }

  public function formUserDelete($id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel("28")
    ) return redirect('/');

    $user_form = user_form::findOrfail($id);
    $id_form = $user_form["id_form"];
    $user_form->delete();

    return redirect("/manager/form/user/" . $id_form);
  }
  /* START حذف گروهی */
  public function formUserDeleteGroup(Request $_request)
  {
    $_link_page_before = $_SERVER['HTTP_REFERER']; //لینک صفحه قبل
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('28')
    ) return redirect($_link_page_before);
    return $this->DeleteUserForm($_request, $_link_page_before, (int) session('id_grop_manager'));
  }
  /* END حذف گروهی */


  public function formDelete($id)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel("24")) {
      return redirect('/');
    }
    $form = form::findOrFail($id);
    $user_forms = user_form::where("id_form", $form["id"])->get();
    if (count($user_forms) == 0) {
      $menus = menu::where("id_form", $form["id"])->get();
      $unders = under::where("id_form", $form["id"])->get();
      foreach ($menus as $menu) {
        $menu->delete();
      }
      foreach ($unders as $under) {
        $under->delete();
      }
      $form->delete();
    } else {
      return redirect('/manager/form')->with('error', 'فرم دارای فرم‌های پر شده است و قابل حذف نیست');
    }

    return redirect('/manager/form');
  }


  /* START دریافت فیلدهای اضافی فرم بر اساس آیدی فرم */
  function formFieldsGet(int $_id_form)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel('22')
    ) return;

    $_extra_fields_form = form::where('id', $_id_form)->first(['fild']); //فیلدهای اضافی فرم
    if (!isset($_extra_fields_form['fild'])) return;

    echo $_extra_fields_form['fild'];
  }
  /* END دریافت فیلدهای اضافی فرم بر اساس آیدی فرم */
  /* END فرم ها */


  public function ticketPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(19)
    ) return redirect('/');

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grop = grop::findOrfail($manager["id_grop"]);
    $tickets = ticket::select(['tickets.id', 'tickets.name', 'tickets.active', 'tickets.id_user', 'tickets.created_at', 'users.hash AS hash_user'])->selectSub(
      'CONCAT(users.name, " ", users.name2)',
      'name_user'
    )->join(
      'users',
      'users.id',
      'tickets.id_user'
    )->whereRaw("(tickets.name!='پشتیبانی فنی سامانه' OR tickets.id_user=?)", array($manager['id_user']))->where("tickets.id_grop", $manager["id_grop"])->get();
    foreach ($tickets as $ticket) {
      $ticket["am"] = verta($ticket["created_at"]);
    }

    $issues = issue::get();

    return view('manager.ticket.ticket', [
      "tickets" => $tickets,
      "statuses_ticket" => parent::$_statuses_ticket,
      "grop" => $grop,
      "issues" => $issues
    ]);
  }

  public function ticketMessagePage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(19)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();
    $ticket = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", array($manager['id_user']))->findOrfail($id);

    if ($ticket["active"] == 1) : //وضعیت فعال
      $ticket->active = 2; //تغییر وضعیت به در حال بررسی
      $ticket->save();
    endif;

    if ($ticket["id_grop"] != $manager["id_grop"]) {
      return redirect('/');
      exit();
    }
    $grop = grop::findOrfail($ticket["id_grop"]);
    $messages = message::where("id_ticket", $ticket["id"])->orderBy('id', 'ASC')->get();
    foreach ($messages as $message) {
      $message["am"] = verta($message["created_at"]);
      if ($message["type"] == 1) {
        $user = user::where("id", $ticket["id_user"])->first();
        $message["user"] = $user;
      }
    }
    return view('manager.ticket.ticketMessage', [
      "ticket" => $ticket,
      "messages" => $messages,
      "grop" => $grop
    ]);
  }

  public function ticketMessage($id, Request $_request)
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(11.5)
    ) return redirect('/');

    $ticket = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", array(session("id_manager")))->findOrfail($id);

    if ($_file = $_request->file('file')) :
      $_name = $_file->getClientOriginalName();
      $number_rand = rand(10000, 99999);
      $_file->move('file_ticket', $number_rand . $_name);
      $message = new message();
      $message->file = $number_rand . $_name;
      $message->text = $_name;
      $message->id_ticket = $ticket["id"];
      $message->id_user = session('id_manager'); //آیدی کاربر پیام دهنده
      $message->type = 2;
      $message->save();
    endif;

    if ($_request['text'] != null) :
      $message = new message();
      $message->text = $_request['text'];
      $message->id_ticket = $ticket["id"];
      $message->id_user = session('id_manager'); //آیدی کاربر پیام دهنده
      $message->type = 2;
      $message->save();

      if ($ticket['new'] == 0) :
        $ticket->new = 1;
        $_save_ticket = true;
      endif;
    endif;

    if (
      !isset($_save_ticket)
      &&
      $ticket["new"] == 1
    ) :
      $ticket->new = 0;
      $_save_ticket = true;
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

    return redirect("/manager/ticket/message/" . $id);
  }

  public function ticketActive($id)
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(11.5)
      ||
      !$this->managerAccessLevel(15)
    ) return redirect('/');

    $ticket = ticket::whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", array(session("id_manager")))->findOrfail($id);
    $ticket->active = ($ticket["active"] >= 1) ? 0 : 1;
    $ticket->save();

    return redirect("/manager/ticket/");
  }


  public function ticketIssuePage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(12) and !$this->managerAccessLevel(13)) {
      return redirect('/');
      exit();
    }
    $issues = issue::get();
    return view('manager.ticket.issue', ["issues" => $issues]);
  }


  public function ticketIssueAdd()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(12)) {
      return redirect('/');
      exit();
    }

    return view('manager.ticket.issueAdd');
  }
  public function ticketIssue(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(12)) {
      return redirect('/');
      exit();
    }
    $issue = new issue();
    $issue->name = $request["name"];
    $issue->save();
    return redirect("/manager/issue");
  }
  public function ticketIssueDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(13)) {
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
    return redirect("/manager/issue");
  }

  public function ticketMessageDelete($id)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel(14)) {
      return redirect('/');
    }

    $message = message::findOrFail($id);

    $manager = manager::where('id_user', session('id_manager'))->firstOrFail();
    ticket::where('id', $message['id_ticket'])
      ->where(function ($q) use ($manager) {
        $q->where('id_grop', $manager['id_grop'])
          ->orWhere('id_user', $manager['id_user']);
      })
      ->firstOrFail();

    $id_ticket = $message['id_ticket'];
    if ($message['file'] !== null) {
      @unlink('file_ticket/' . $message['file']);
    }
    $message->delete();

    return redirect('/manager/ticket/message/' . $id_ticket);
  }
  public function ticketDelete($id)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel(18)) return redirect('/');

    $manager = manager::where("id_user", session("id_manager"))->first();
    if (!$manager) return redirect('/');

    $ticket = ticket::where("id_grop", $manager["id_grop"])
      ->whereRaw("(name!='پشتیبانی فنی سامانه' OR id_user=?)", [$manager['id_user']])
      ->findOrFail($id);

    $messages = message::where("id_ticket", $ticket["id"])->get();
    foreach ($messages as $message) {
      if ($message["file"] !== null) {
        $filePath = "file_ticket/" . $message["file"];
        if (file_exists($filePath)) {
          unlink($filePath);
        }
      }
      $message->delete();
    }
    $ticket->delete();

    return redirect("/manager/ticket/");
  }




  public function contactuPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(21)) {
      return redirect('/');
      exit();
    }
    $contactus = contactu::join('users', 'users.mobile', '=', 'contactus.mobile')
      ->join('user_grops', 'user_grops.user_id', '=', 'users.id')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->select('contactus.*')
      ->get();
    return view('manager.ticket.contactu', ["contactus" => $contactus]);
  }

  public function contactuDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(21)) {
      return redirect('/');
      exit();
    }
    $contactu = contactu::findOrfail($id);
    $contactu->delete();
    return redirect("/manager/contactu");
  }


  public function productPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $grop = grop::find(session('id_grop_manager'));
    $products = product::get(); // CollectionManagerScope filters by id_grops JSON
    foreach ($products as $product) {
      $product["grop"] = $grop;
    }
    return view('manager.product.page', ["products" => $products]);
  }
  public function productAddPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $grops = grop::all();
    return view('manager.product.add', ['grops' => $grops]);
  }

  public function productAdd(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $product = new product();
    $product->name = $request["name"];
    $product->am = $request["am"];
    $product->price = $request["price"];
    $product->id_grop = $request["grop"];
    $product->save();
    return redirect("/manager/product");
  }

  public function productEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $product = product::findOrFail($id); // CollectionManagerScope restricts to manager's groups
    $grops = grop::all();
    return view('manager.product.edit', ["product" => $product, "grops" => $grops]);
  }
  public function productEdit(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $product = product::findOrfail($id);
    $product->name = $request["name"];
    $product->am = $request["am"];
    $product->price = $request["price"];
    $product->id_grop = $request["grop"];
    $product->save();
    return redirect("/manager/product");
  }
  public function productActive($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $product = product::findOrfail($id);
    $product2 = product::findOrfail($id);
    if ($product2["active"] == 1) {
      $product->active = 0;
      $product->save();
    }
    if ($product2["active"] == 0) {
      $product->active = 1;
      $product->save();
    }

    return redirect("/manager/product");
  }


















  public function advertisingPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(6)) {
      return redirect('/');
      exit();
    }
    $advertising_pays = advertising_pay::join('user_grops', 'user_grops.user_id', '=', 'advertising_pays.id_user')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->where("active_pay", 1)
      ->select('advertising_pays.*')
      ->get();
    foreach ($advertising_pays as $advertising_pay) {
      $advertising_pay["user"] = user::find($advertising_pay["id_user"]);
      $advertising_pay["product"] = product_advertising::findOrfail($advertising_pay["id_product_advertising"]);
    }

    return view('manager.advertising.page', ["advertising_pays" => $advertising_pays]);
  }


  public function advertisingImg($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(5)) {
      return redirect('/');
      exit();
    }
    $advertising_pay = advertising_pay::findOrfail($id);
    unlink("advertising_img/" . $advertising_pay["img"]);
    $advertising_pay->active = 2;
    $advertising_pay->img = null;
    $advertising_pay->save();
    return redirect('/manager/advertising');
  }

  public function advertisingActive($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(4)) {
      return redirect('/');
      exit();
    }
    $advertising_pay = advertising_pay::findOrfail($id);
    if ($advertising_pay["id_product_advertising"] == 25) {
      $advertising_pay->active = 1;
      $advertising_pay->active_show = 1;
      $advertising_pay->am_end = verta($advertising_pay["day"] . ' day')->formatDate();;
      $advertising_pay->save();
    } else {
      $advertising_pay->active = 1;
      $advertising_pay->save();
    }

    return redirect('/manager/advertising');
  }


  public function advertisingProductPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(1)) {
      return redirect('/');
      exit();
    }
    $product_advertisings = product_advertising::all();
    return view('manager.advertising.product', ["product_advertisings" => $product_advertisings]);
  }

  public function advertisingProductEdit($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(2)) {
      return redirect('/');
      exit();
    }
    $product_advertising = product_advertising::findOrfail($id);
    return view('manager.advertising.edit', ["product_advertising" => $product_advertising]);
  }

  public function advertisingProduct(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(2)) {
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
    return redirect('/manager/advertising/product');
  }



  public function pmPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(7)
    ) return redirect('/');

    $manager = manager::where("id_user", session("id_manager"))->first();

    $pms = pm::with('groups')->where("id_grop", $manager["id_grop"])->where('created_by', NULL)-> //توسط مدیر مجموعه ایجاد شده باشد
      get();
    foreach ($pms as $pm) :
      $pm["am"] = \verta($pm["created_at"]);
    endforeach;

    return view('manager.pm.page', ["pms" => $pms]);
  }

  public function pmAddPage()
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
      ||
      !$this->managerAccessLevel(8)
    ) return redirect('/');

    $manager = manager::where("id_user", session("id_manager"))->first();
    $grops = usergrop::where("id_grop", $manager["id_grop"])->get();

    return view('manager.pm.add', ["grops" => $grops]);
  }
  public function pmAddUser(Request $_request)
  {
    if (
      !session()->has('manager')
      // ||
      // $this->managerAccessLevel(6.5)
      // ||
      // !$this->managerAccessLevel(8)
    ) return redirect('/');

    $manager = manager::where("id_user", session("id_manager"))->first();
    $_grop_users = array_filter((array) $_request->input('gropUser', []), function ($id) {
      return is_numeric($id);
    });

    if (!$manager || empty($_grop_users)) {
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
    )->where('user_grops.grop_id', $manager["id_grop"])->whereIn('id_usergrop', $_grop_users)->get());
  }


  public function pmAdd(Request $_request)
  {
    if (
      !isset($_request['gropUser'])
      ||
      !is_array($_request['gropUser'])
      ||
      !isset($_request['title'])
      ||
      $_request['title'] == ''
    ) return redirect('/manager/pm');

    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
      ||
      !$this->managerAccessLevel(8)
    ) return redirect('/');

    $_manager = manager::where('id_user', session('id_manager'))->first();
    $_users = [];
    $_selected_user_grops = array_values(array_filter($_request['gropUser'], function ($id) {
      return is_numeric($id);
    }));

    if (!$_manager || empty($_selected_user_grops)) return redirect('/manager/pm');

    if (count($_selected_user_grops) > 1) : //گروه کاربری مجموعه بیشتر از یکی انتخاب شده بود
      foreach ($_selected_user_grops as $_id_grop_user) :
        if (is_numeric($_id_grop_user)) $_users['Grop Users'][] = $_id_grop_user;
      endforeach;
    elseif (isset($_request['user']) && is_array($_request['user']) && count($_request['user']) > 0) :
      foreach ($_request['user'] as $_user) $_users[] = user::where('id', $_user)->first()['id'];
    else :
      $_user_grops = user_grop::where('grop_id', $_manager['id_grop'])->whereIn('id_usergrop', $_selected_user_grops)->get();
      foreach ($_user_grops as $_user_grop) :
        $_user = user::where('id', $_user_grop['user_id'])->get();
        if (count($_user) > 0) :
          $id_user_active = 1;

          foreach ($_users as $_user2) :
            if ($_user2 == $_user_grop["user_id"]) :
              $id_user_active = 0;
              break;
            endif;
          endforeach;

          if ($id_user_active) $_users[] = $_user_grop["user_id"];
        endif;
      endforeach;
    endif;

    //تاریخ و ساعت خاتمه نمایش
    if (
      isset($_request['date-end-show'])
      &&
      $_request['date-end-show']
    ) :
      $_date_end_show = $_request['date-end-show'];

      if (isset($_request['time-end-show']) && isset($_request['time-end-show'])) $_date_end_show .= ' ' . $_request['time-end-show'];
    endif;

    $_pm = new pm();
    $_pm->id_grop = $_manager['id_grop']; //آیدی گروه
    $_pm->id_users = is_array($_users) ? json_encode($_users) : $_users; //آیدی کاربران یا آیدی گروه های کاربری مجموعه
    $_pm->title = $_request['title']; //عنوان
    if ($_file = $_request->file('file')) : //عکس
      $_name_file = $_pm->img = rand(10000, 99999) . $_file->getClientOriginalName();
      $_file->move('pm_img', $_name_file);
    elseif (isset($_request['sms'])) : //متن پیام
      $_pm->text = $_request['sms'];
    endif;
    if (isset($_date_end_show)) $_pm->date_end_show = $_date_end_show; //تاریخ و ساعت خاتمه نمایش
    $_pm->save();
    $_pm->groups()->attach((int) $_manager['id_grop']);

    if ($_request->has('save_as_note') && isset($_request['sms']) && $_request['sms'] != '') {
      $_note_user_ids = [];
      if (isset($_users['Grop Users'])) {
        foreach ($_users['Grop Users'] as $_grop_user_id) {
          foreach (\App\user_grop::where('id_usergrop', $_grop_user_id)->get() as $_ug)
            $_note_user_ids[] = $_ug['user_id'];
        }
      } else {
        $_note_user_ids = $_users;
      }
      foreach (array_unique($_note_user_ids) as $_uid) {
        \App\note::create([
          'user_id'  => $_uid,
          'admin_id' => session('id_manager'),
          'content'  => $_request['sms'],
          'status'   => 'pending',
        ]);
      }
    }

    return redirect('/manager/pm');
  }


  public function pmShowPage($id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(9)
    ) :
      return redirect('/');
    endif;

    $_pm = pm::where('created_by', NULL)-> //توسط مدیر مجموعه ایجاد شده باشد
      findOrfail($id, ['id', 'id_grop', 'text']);

    $_pm_shows = pm_show::where('id_pm', $_pm['id'])->get();
    foreach ($_pm_shows as $_pm_show) :
      $_pm_show['user'] = user::findOrfail($_pm_show['id_user']);
    endforeach;

    $grop = grop::findOrfail($_pm["id_grop"]);

    return view('manager.pm.show', ['grop' => $grop, "pm_shows" => $_pm_shows, "pm" => $_pm]);
  }

  /* START حذف مشاهده کننده پیام */
  function pmShowDelete(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
      ||
      !$this->managerAccessLevel(9)
    ) :
      return redirect('/');
    endif;

    $_pm_show = pm_show::whereRaw(
      '(SELECT COUNT(id_grop) FROM pms WHERE id=pm_shows.id_pm AND id_grop=?)=1',
      session('id_grop_manager')
    )->findOrfail($_id, ['id', 'id_pm']);
    $_pm_show->delete();

    return redirect('/manager/pm/show/' . $_pm_show['id_pm']);
  }
  /* END حذف مشاهده کننده پیام */


  /* START ویرایش پیام */
  public function pmEditPage(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
    ) return redirect('/');

    $_manager = manager::where('id_user', session('id_manager'))->first(['id_grop']);

    $_pm = pm::select(['id_grop', 'id_users', 'title', 'img', 'text', 'date_end_show'])->selectSub(
      'IF(pms.id_users LIKE \'%"Grop Users"%\', pms.id_users, (SELECT id_usergrop FROM user_grops WHERE user_id=IF(pms.id_users LIKE "%,%", SUBSTRING(pms.id_users, 2, POSITION("," IN pms.id_users) - 2), SUBSTRING(pms.id_users, 2, POSITION("]" IN pms.id_users) - 2)) AND grop_id=pms.id_grop))',
      'id_user_grop' //آیدی گروه کاربری کاربر
    )->where('id_grop', $_manager['id_grop'])-> //مدیر مجموعه پیام بود
      where('created_by', NULL)-> //توسط مدیر مجموعه ایجاد شده باشد
      findOrfail($_id);

    $_id_users = json_decode($_pm['id_users'], true); //آیدی کاربران
    if (array_key_exists('Grop Users', $_id_users)) :
      $_implode_id_users = '';
      $_user_grops = usergrop::where('id_grop', $_pm['id_grop'])->whereRaw('id IN (' . implode(',', $_id_users['Grop Users']) . ')')-> //آیدی گروه کاربری کاربر یا آیدی گروه های انتخاب شده بصورت چند تایی
        get(['name']);
    else :
      $_implode_id_users = implode(',', $_id_users);
      $_user_grops = array();
    endif;

    return view('manager.pm.edit', [
      'pm' => $_pm,
      'id_users' => $_id_users, //آیدی کاربران
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
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
    ) return redirect('/');

    //تاریخ و ساعت خاتمه نمایش
    if (
      isset($_request['date-end-show'])
      &&
      $_request['date-end-show']
    ) :
      $_date_end_show = $_request['date-end-show'];

      if (isset($_request['time-end-show']) && isset($_request['time-end-show'])) $_date_end_show .= ' ' . $_request['time-end-show'];

      $_manager = manager::where('id_user', session('id_manager'))->first();

      pm::where('id', $_id)->where('id_grop', $_manager['id_grop'])-> //مدیر مجموعه پیام بود
        where('created_by', NULL)-> //توسط مدیر مجموعه ایجاد شده باشد
        update(array('date_end_show' => isset($_date_end_show) ? $_date_end_show : NULL));
    endif;

    return redirect("/manager/pm/edit/$_id");
  }
  /* END ثبت */
  /* END ویرایش پیام */


  //حذف پیام
  public function pmDelete(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      $this->managerAccessLevel(6.5)
      ||
      !$this->managerAccessLevel(10)
    ) return redirect('/');

    $_pm = pm::where('created_by', NULL)-> //توسط مدیر مجموعه ایجاد شده باشد
      findOrfail($_id);

    foreach (pm_show::where('id_pm', $_pm['id'])->get() as $pm_show) :
      $pm_show->delete();
    endforeach;

    if ($_pm["img"] != null) unlink("pm_img/" . $_pm["img"]);
    $_pm->delete();

    return redirect('/manager/pm');
  }














  public function invoicePage()
  {
    if (!session()->has('manager')) return redirect('/');

    $invoices = invoice::where('id_grop', session('id_grop_manager'))->get(['id', 'id_users', 'id_grop', 'title', 'text', 'price', 'am_start', 'am_end', 'daily_fine_amount', 'fixed_penalty_amount', 'number', 'created_at']);
    foreach ($invoices as $invoice) {
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

      $invoice['grop'] = grop::where("id", $invoice["id_grop"])->first();
      $invoice["am"] = \verta($invoice["created_at"]);
    }

    return view('manager.invoice.page', ["invoices" => $invoices]);
  }


  /* START صورتحساب کلی کاربران */
  /* START گروه های کاربری */
  function invoiceUserAllUserGrops()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(72)
    ) return redirect('/');

    return view('manager.invoice.userAll.userGrops', ['user_grops' => usergrop::where('id_grop', manager::where('id_user', session('id_manager'))->first(['id_grop'])['id_grop'])->get(['id', 'name'])]);
  }
  /* END گروه های کاربری */


  public function invoiceUserAllList(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(72)
    ) return redirect('/');

    $_info_users_invoices = $_id_users_invoices = $_id_user_grops_invoices = array();

    $_info_invoices_grop = invoice::where('id_grop', session('id_grop_manager'))->whereRaw('am_end!=""')->orderBy('id', 'DESC')->get(['id', 'id_users', 'price']);
    foreach ($_info_invoices_grop as $_info_invoice_grop) :
      if (empty($_info_invoice_grop['id_users'])) continue;
      $_id_users_invoice = json_decode($_info_invoice_grop['id_users'], true);
      if (array_key_exists('Grop Users', $_id_users_invoice)) : //گروه های کاربری مجموعه
        foreach ($_id_users_invoice['Grop Users'] as $_id_grop_invoice) $_id_user_grops_invoices[$_id_grop_invoice] = (array_key_exists($_id_grop_invoice, $_id_user_grops_invoices) ? $_id_user_grops_invoices[$_id_grop_invoice] : 0) + $_info_invoice_grop['price'];
      else : //کاربر انتخاب شده بود
        foreach ($_id_users_invoice as $_id_user_invoice) $_id_users_invoices[$_id_user_invoice] = (array_key_exists($_id_user_invoice, $_id_users_invoices) ? $_id_users_invoices[$_id_user_invoice] : 0) + $_info_invoice_grop['price'];
      endif;
    endforeach;

    $_group_invoice_ids = $_info_invoices_grop->pluck('id')->toArray();

    if (
      !empty($_id_users_invoices)
      &&
      (
        $_id <= 0
        ||
        empty($_id_user_grops_invoices)
        ||
        array_key_exists($_id, $_id_user_grops_invoices)
        ||
        array_key_exists($_id, $_id_users_invoices)
      )
    ) :
      // group only by users.id to prevent duplicates from multiple usergrop memberships
      $_info_users_invoices = user::select([
        'users.id',
        'mobile',
        'hash',
        'users.name',
        'name2',
      ])
        ->selectRaw('MIN(user_grops.id_usergrop) AS ID_User_Grop')
        ->selectRaw('MIN(usergrops.name) AS Name_User_Grops')
        ->leftJoin('invoice_fines', function ($join) use ($_group_invoice_ids) {
          $join->on('invoice_fines.id_user', '=', 'users.id');
          if (!empty($_group_invoice_ids)) {
            $join->whereIn('invoice_fines.id_invoice', $_group_invoice_ids);
          }
        })
        ->leftJoin('invoices', 'invoices.id', '=', 'invoice_fines.id_invoice')
        ->selectRaw('SUM(COALESCE(invoices.price, 0)) as Price_Invoice')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_penalty, 0)) as Price_Penalty')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_paid, 0)) as Price_Paid')
        ->selectRaw('SUM(COALESCE(invoice_fines.total_discount, 0)) as Price_Discount')
        ->join('user_grops', 'user_id', 'users.id')
        ->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop')
        ->where(function ($q) use ($_id, $_id_user_grops_invoices, $_id_users_invoices) {
          $hasFilter = false;
          if ($_id > 0) {
            $q->where('user_grops.id_usergrop', $_id);
            $hasFilter = true;
          } elseif (!empty($_id_user_grops_invoices)) {
            $q->whereIn('user_grops.id_usergrop', array_keys($_id_user_grops_invoices));
            $hasFilter = true;
          }
          if (!empty($_id_users_invoices)) {
            $hasFilter ? $q->orWhereIn('users.id', array_keys($_id_users_invoices)) : $q->whereIn('users.id', array_keys($_id_users_invoices));
          }
        })
        ->groupBy('users.id', 'mobile', 'hash', 'users.name', 'name2')
        ->get();
    endif;

    if (!empty($_id_users_invoices)) foreach ($_info_users_invoices as $_info_user_invoices) if (array_key_exists($_info_user_invoices['id'], $_id_users_invoices)) $_info_user_invoices['Price_Invoice'] = $_id_users_invoices[$_info_user_invoices['id']]; //اضافه کردن قیمت صورتحساب کاربران
    if (!empty($_id_user_grops_invoices)) foreach ($_info_users_invoices as $_info_user_invoices) if (array_key_exists($_info_user_invoices['ID_User_Grop'], $_id_user_grops_invoices)) $_info_user_invoices['Price_Invoice'] = $_info_user_invoices['Price_Invoice'] + $_id_user_grops_invoices[$_info_user_invoices['ID_User_Grop']]; //اضافه کردن قیمت صورتحساب گروه های کاربری مجموعه

    return view('manager.invoice.userAll.list', [
      'convert_number_to_persian_words' => new Convert_Number_To_Persian_Words(),
      'users' => $_info_users_invoices
    ]);
  }
  /* END صورتحساب کلی کاربران */


  /* START فیش های تایید نشده */
  function invoiceUnconfirmedReceipts()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(72)
    ) return redirect('/');

    return view('manager.invoice.unconfirmedReceipts', [
      'directory_files_user' => str_replace('/public_html/', '', parent::$_directory_files_user), //آدرس پوشه فایل های کاربران
      'invoice_pays' => invoice_pay::select(
        [
          'invoice_pays.id',
          'id_user',
          'id_invoice',
          'invoice_pays.price',
          'invoice_pays.text',
          'am',
          'picture',
          'name',
          'name2',
          'hash',
          'mobile'
        ]
      )->join('invoices', 'invoices.id', 'id_invoice')->join('users', 'users.id', 'id_user')->where('active_pay', 2)->where('id_grop', session('id_grop_manager'))->get()
    ]);
  }
  /* END فیش های تایید نشده */


  /* START صورتحساب تکی کاربر */
  /* START گروه های کاربری */
  function invoiceUserSingle()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(72)
    ) return redirect('/');

    return view('manager.invoice.userSingle.search');
  }
  /* END گروه های کاربری */
  /**
   * محاسبه مانده تعهد برای صورت حساب کاربر
   */
  private function calulate_remainder_commitment($invoice)
  {
    $invoice_pays = invoice_pay::select(['id AS ID', 'price', 'text', 'referenceId', 'active_pay', 'am', 'picture', 'created_at'])
      ->where('id_user', $invoice["ID_User"])
      ->where('id_invoice', $invoice["id"])->get();

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

    $invoice_fine = invoice_fine::where("id_user", $invoice["ID_User"])
      ->where('id_invoice', $invoice["id"])
      ->get();

    $price_fine = 0;
    foreach ($invoice_fine as $record) $price_fine += $record["price"];

    return $invoice['price'] + $price_fine - $price_active - $price_discount;
  }

  public function invoiceUserSingleInfo(string $_category, string $_id_or_hash_user_or_receipt_number_invoice)
  {
    if (
      !in_array($_category, array('invoices', 'final', 'payments'))
      ||
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(72)
    ) return redirect('/');

    $validator = \Illuminate\Support\Facades\Validator::make(
      ['user_id' => $_id_or_hash_user_or_receipt_number_invoice],
      ['user_id' => 'required|integer|min:1']
    );
    if ($validator->fails()) {
      return back()->withErrors(['user_id' => 'شناسه کاربر باید عدد صحیح مثبت باشد'])->withInput();
    }
    $_id_or_hash_user_or_receipt_number_invoice = (int) $_id_or_hash_user_or_receipt_number_invoice;
    if ($_category == 'invoices') : //صورتحساب
      $query = user::select(['users.id AS ID_User', 'name', 'name2', 'hash', 'invoices.id', 'id_users', 'title', 'invoices.text', 'invoices.price', 'am_start', 'am_end', 'number'])
        ->join('user_grops', function($join) { $join->on('user_grops.user_id', '=', 'users.id')->whereRaw('user_grops.id = (SELECT MIN(id) FROM user_grops AS ug WHERE ug.user_id = users.id AND ug.grop_id = user_grops.grop_id)'); })
        ->join('invoices', 'id_grop', 'grop_id')
        ->leftJoin('invoice_pays', array(
          'id_invoice' => 'invoices.id',
          'id_user' => 'users.id'
        ))->whereRaw('(users.id=? OR hash=?)', array($_id_or_hash_user_or_receipt_number_invoice, $_id_or_hash_user_or_receipt_number_invoice))
        ->where('grop_id', session('id_grop_manager'))->whereRaw('(id_users LIKE CONCAT("[", users.id, "]") OR id_users LIKE CONCAT("[", users.id, ",%") OR id_users LIKE CONCAT("%,", users.id, ",%") OR id_users LIKE CONCAT("%,", users.id, "]") OR id_users LIKE CONCAT("%\"", user_grops.id_usergrop, "\"%"))')->groupBy(['users.id', 'name', 'name2', 'hash', 'invoices.id', 'id_users', 'title', 'invoices.text', 'invoices.price', 'am_start', 'am_end', 'number']);

      $sql = $query->toSql();
      $bindings = $query->getBindings();

      // جایگزینی مقادیر bindings در query
      foreach ($bindings as $binding) {
        $sql = preg_replace('/\?/', "'" . $binding . "'", $sql, 1);
      }

      $invoices = $query->get();
      foreach ($invoices as $key => $value) {

        $value['remainder_commitment'] = $this->calulate_remainder_commitment($value);
      }
      return view('manager.invoice.userSingle.invoices', [
        'invoices' => $invoices,
        'id_user' => $_id_or_hash_user_or_receipt_number_invoice,
      ]);
    elseif ($_category == 'final') : //نهایی
      $grop = grop::findOrfail(session('id_grop_manager'));

      return view('manager.invoice.userSingle.final', [
        'grop' => $grop,
        'invoices' => user::select(['users.id AS ID_User', 'name', 'name2', 'hash', 'invoices.id', 'am_start', 'title', 'invoices.price'])->selectSub('SELECT COALESCE(total_penalty, price, 0) FROM invoice_fines WHERE id_user=users.id AND id_invoice=invoices.id ORDER BY id DESC LIMIT 1', 'Price_Penalty')-> //قیمت جریمه
          selectSub('SELECT SUM(price) FROM invoice_pays WHERE id_user=users.id AND id_invoice=invoices.id AND active_pay=1', 'Price_Paid')-> //قیمت پرداخت شده
          selectSub("SELECT COALESCE(SUM(price), 0) FROM invoice_pays WHERE id_user=users.id AND id_invoice=invoices.id AND active_pay=1 AND text LIKE 'تخفیف > %'", 'Price_Discount')-> //قیمت تخفیف
          join('user_grops', function($join) { $join->on('user_grops.user_id', '=', 'users.id')->whereRaw('user_grops.id = (SELECT MIN(id) FROM user_grops AS ug WHERE ug.user_id = users.id AND ug.grop_id = user_grops.grop_id)'); })->join('invoices', 'id_grop', 'grop_id')->leftJoin('invoice_pays', array(
            'id_invoice' => 'invoices.id',
            'id_user' => 'users.id'
          ))->whereRaw('(users.id=? OR hash=?)', array($_id_or_hash_user_or_receipt_number_invoice, $_id_or_hash_user_or_receipt_number_invoice))->where('grop_id', session('id_grop_manager'))->whereRaw('(id_users LIKE CONCAT("[", users.id, "]") OR id_users LIKE CONCAT("[", users.id, ",%") OR id_users LIKE CONCAT("%,", users.id, ",%") OR id_users LIKE CONCAT("%,", users.id, "]") OR id_users LIKE CONCAT("%\"", user_grops.id_usergrop, "\"%"))')->orderBy('invoices.id', 'DESC')->groupBy(['users.id', 'name', 'name2', 'hash', 'invoices.id', 'id_users', 'title', 'invoices.text', 'invoices.price', 'am_start', 'am_end', 'number'])->get()
      ]);
    elseif ($_category == 'payments') : //پرداختی ها
      return view('manager.invoice.userSingle.payments', [
        'invoice_pays' => user::select(['users.id AS ID_User', 'name', 'name2', 'hash', 'am_start', 'number', 'invoice_pays.id', 'invoice_pays.text', 'invoice_pays.price', 'referenceId', 'am', 'invoice_pays.created_at'])->selectSub('SELECT SUM(price) FROM invoice_pays WHERE id_user=users.id AND active_pay=1', 'SUM_Prices')->join('user_grops', 'user_id', 'users.id')->join('invoice_pays', 'id_user', 'users.id')->join('invoices', 'invoices.id', 'id_invoice')->whereRaw('(users.id=? OR hash=?)', array($_id_or_hash_user_or_receipt_number_invoice, $_id_or_hash_user_or_receipt_number_invoice))->where('active_pay', 1)-> //پرداخت های تایید شده
          where('grop_id', session('id_grop_manager'))->get()
      ]);
    endif;
  }
  /* END صورتحساب تکی کاربر */


  /* START ایجاد */
  public function invoiceAddPage()
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(66)
    ) return redirect('/');

    return view('manager.invoice.add', ['usergrops' => usergrop::where('id_grop', manager::where('id_user', session('id_manager'))->first(['id_grop'])['id_grop'])->get()]);
  }


  public function invoiceAdd(Request $_request)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(66)
    ) return redirect('/');

    $this->invoiceSave($_request, 'manager', new invoice(), manager::where('id_user', session('id_manager'))->first(['id_grop'])['id_grop']);
    return redirect('/manager/invoice');
  }
  /* END ایجاد */


  /* START ویرایش */
  public function invoiceEditPage(int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(66)
    ) return redirect('/');

    $_id_grop_manager = session('id_grop_manager'); //آیدی مجموعه

    $_info_invoice = invoice::where('id_grop', $_id_grop_manager)->findOrFail($_id, ['id_users', 'title', 'text', 'price', 'am_start', 'am_end', 'daily_fine_amount', 'fixed_penalty_amount', 'number', 'fine_calc_date', 'fine_calc_amount']);

    $_user_grops_invoice = usergrop::where('id_grop', $_id_grop_manager)->get(['id', 'name']); //گروه های کاربری مجموعه
    /* START گروه های کاربری مجموعه انتخاب شده */
    $_user_grops_selected = array();

    $_id_users_invoice = !empty($_info_invoice['id_users']) ? json_decode($_info_invoice['id_users'], true) : array();
    if (array_key_exists('Grop Users', $_id_users_invoice)) : //گروه های کاربری مجموعه
      $_user_grops_selected = $_id_users_invoice['Grop Users'];
    else : //کاربر انتخاب شده بود
      $_users_invoice = user::join('user_grops', 'user_id', 'users.id')->where('grop_id', $_id_grop_manager)->distinct()->get(['id_usergrop', 'users.id', 'name', 'name2', 'hash']);
      foreach ($_users_invoice as $_user_invoice) :
        if (in_array($_user_invoice['id'], $_id_users_invoice)) $_user_grops_selected[] = $_user_invoice['id_usergrop'];
      endforeach;
    endif;
    /* END گروه های کاربری مجموعه انتخاب شده */

    return view('manager.invoice.edit', [
      'info_invoice' => $_info_invoice,
      'user_grops' => $_user_grops_invoice,
      'user_grops_selected' => isset($_user_grops_selected) ? $_user_grops_selected : array(),
      'users' => isset($_users_invoice) ? $_users_invoice : array(),
      'users_selected' => isset($_users_invoice) ? $_id_users_invoice : array(),
    ]);
  }


  public function invoiceEdit(Request $_request, int $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(66)
    ) return redirect('/');

    $_id_grop_manager = session('id_grop_manager'); //آیدی مجموعه

    $this->invoiceSave($_request, 'manager', invoice::where('id_grop', $_id_grop_manager)->findOrFail($_id), $_id_grop_manager);
    return redirect("/manager/invoice/edit/$_id");
  }
  /* END ویرایش */
  /**
   * حذف یک صورتحساب خاص از صورتحساب ها کاربر
   */
  public function invoiceUserDelete($id_invoice, $id_user, Request $request)
  {
    $check_pays_fines = filter_var($request->input('check_pays_fines', true), FILTER_VALIDATE_BOOLEAN);
    if (
      !session()->has('manager')
    ) {
      return redirect('/');
    }

    // حذف جریمه ها صورتحساب
    $invoice_pays = invoice_pay::where('id_invoice', $id_invoice)
      ->where('id_user', $id_user)
      ->get();
    if (count($invoice_pays) > 0) {
      if ($check_pays_fines) {
        return response()->json([
          'success' => false,
          'data' => [
            'has_pays_fines' => true,
          ]
        ], 200);
      } else {
        foreach ($invoice_pays as $pay) {
          $pay->delete();
        }
      }
    }

    // حذف پرداخت ها صورت حساب
    $invoice_fines = invoice_fine::where('id_invoice', $id_invoice)
      ->where('id_user', $id_user)
      ->get();
    if (count($invoice_fines) > 0) {
      if ($check_pays_fines) {
        return response()->json([
          'success' => false,
          'data' => [
            'has_pays_fines' => true,
          ]
        ], 200);
      } else {
        foreach ($invoice_fines as $fine) {
          $fine->delete();
        }
      }
    }

    // حذف شناسه کاربر از صورتحساب
    $invoice = invoice::findOrfail($id_invoice);
    // رشته را تبدیل به آرایه عددی می کند
    $id_users = explode(',', trim($invoice->id_users, '[]'));
    $id_users = array_diff($id_users, [$id_user]);
    if ($id_users != null && $id_users != "") {
      $invoice->id_users = '[' . implode(',', $id_users) . ']';
      $invoice->save();
    } else {
      $invoice->delete();
    }

    return response()->json([
      'success' => true,
      'data' => [],
    ], 200);
  }

  public function invoiceUserPage($id)
  {
    if (
      !session()->has('manager')
      &&
      !$this->managerAccessLevel(68)
    ) return redirect('/');

    $_invoice = invoice::where('id_grop', session('id_grop_manager'))->findOrFail($id, ['id', 'id_users', 'id_grop', 'title', 'price', 'am_start', 'am_end', 'number']);
    $_id_users_invoice = !empty($_invoice['id_users']) ? json_decode($_invoice['id_users'], true) : array();
    if (array_key_exists('Grop Users', $_id_users_invoice)) : //گروه های کاربری مجموعه
      // group by user_id to prevent duplicates when a user belongs to multiple usergrops
      $_users_invoice = user_grop::select(['mobile', 'users.name', 'name2', 'hash', 'user_id AS id'])
        ->selectRaw('MIN(usergrops.name) AS name_user_grop')
        ->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop')
        ->join('users', 'users.id', 'user_id')
        ->leftJoin('invoice_fines', function($join) use ($_invoice) {
            $join->on('invoice_fines.id_user', '=', 'users.id')
                 ->where('invoice_fines.id_invoice', '=', $_invoice['id']);
        })
        ->selectRaw('COALESCE(MAX(invoice_fines.total_penalty), 0) as price_fine')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_paid - invoice_fines.total_discount), 0) as price_active')
        ->selectRaw('COALESCE(MAX(invoice_fines.total_discount), 0) as Price_Discount')
        ->whereRaw('id_usergrop IN (' . implode(',', $_id_users_invoice['Grop Users']) . ')')
        ->groupBy('user_id', 'mobile', 'users.name', 'name2', 'hash')
        ->get();
    else : //کاربر انتخاب شده بود
      // group by users.id to prevent duplicates from multiple user_grops entries
      $_users_invoice = user::select(['users.id', 'mobile', 'users.name', 'name2', 'hash'])
        ->selectRaw('MIN(usergrops.name) AS name_user_grop')
        ->join('user_grops', 'user_id', 'users.id')
        ->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop')
        ->leftJoin('invoice_fines', function($join) use ($_invoice) {
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


    return view('manager.invoice.user', [
      'grop' => $grop,
      'invoice' => $_invoice,
      'invoice_users' => $_users_invoice
    ]);
  }

  public function invoiceUser($id, $id_user)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(69)
    ) return redirect('/');

    $user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $id_user)->select('users.*')->firstOrFail();
    $invoice = invoice::where('id_grop', session('id_grop_manager'))->findOrFail($id);

    $invoice_pays = invoice_pay::select(['id AS ID', 'id', 'price', 'text', 'referenceId', 'active_pay', 'am', 'picture', 'created_at'])
      ->where('id_user', $user['id'])
      ->where('id_invoice', $invoice['id'])
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
    return view('manager.invoice.userPay', [
      'directory_files_user' => str_replace('/public_html/', '', parent::$_directory_files_user), //آدرس پوشه فایل های کاربران
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
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(70)
    ) return redirect('/');

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

    // Task 4: یکتایی شماره فیش در محدوده همین مجموعه (collection) — نه global
    $_receipt_exists = invoice_pay::where('invoice_pays.id', $_number)
        ->join('invoices', 'invoices.id', '=', 'invoice_pays.id_invoice')
        ->where('invoices.id_grop', session('id_grop_manager'))
        ->exists();
    if ($_receipt_exists) return redirect($_link_page_before)->with('error', 'این شماره فیش قبلا در این مجموعه ثبت شده است');

    $_info_user = (object) user::select(['users.id', 'id_usergrop'])->join('user_grops', 'user_id', 'users.id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $_id_user)->firstOrFail();
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
    $_invoice_pay->id = $_number;
    $_invoice_pay->id_user = $_id_user;
    $_invoice_pay->id_invoice = $_id_invoice;
    $_invoice_pay->price =  $_price;
    $_invoice_pay->text = $_text;
    $_invoice_pay->am = $_date;
    $_invoice_pay->active_pay = 1;
    $_invoice_pay->save();

    // به دلیل اینکه وضعیت پرداخت تایید شده می باشد
    // عملیات محاسبه جریمه برای پرداخت های کاربر روی صورتحساب اعمال می شود
    if ($_invoice_pay->active_pay == 1) {
      $this->createOrUpdate_user_fine($_id_user, $_id_invoice);
    }

    return redirect(!empty($_link_page_before) ? $_link_page_before : '/manager/invoice/user/' . $_id_invoice . '/' . $_id_user)->with('message', 'پرداخت با موفقیت ثبت شد.');
  }
  /**
   * محاسبه جریمه باقیمانده مانده تعهد و
   * ثبت اون در جدول جریمه ها
   */
  public function calc_remaining_commitment_fine($id_invoice, $id_user)
  {
    $this->createOrUpdate_user_fine($id_user, $id_invoice);

    $_link_page_before = $_SERVER['HTTP_REFERER'];
    return redirect(!empty($_link_page_before) ? $_link_page_before : '/manager/invoice/user/' . $id_invoice . '/' . $id_user);
  }

  /* START تغییر وضعیت فعال/غیرفعال به بالعکس */
  public function invoicePayActive(string $_id)
  {
    if (
      !session()->has('manager')
      ||
      !$this->managerAccessLevel(70)
    ) return redirect('/');

    $_select_invoice_pay = invoice_pay::join('user_grops', 'invoice_pays.id_user', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->findOrFail($_id, ['invoice_pays.id', 'invoice_pays.id_user', 'invoice_pays.id_invoice', 'invoice_pays.active_pay']);
    $_newly_approved = false;
    if ($_select_invoice_pay['active_pay'] != 3) :
      $_new_status = ($_select_invoice_pay['active_pay'] != 2) ? 2 : 1;
      $_select_invoice_pay->active_pay = $_new_status;
      $_select_invoice_pay->save();
      $_newly_approved = ($_new_status === 1);
    endif;

    $id_user = $_select_invoice_pay['id_user'];
    $id_invoice = $_select_invoice_pay['id_invoice'];
    // جریمه کاربر روی صورتحساب را محاسبه کن و اونو تو جدول جریمه ها ثبت کن
    $this->createOrUpdate_user_fine($id_user, $id_invoice);

    if ($_newly_approved) {
      try {
        $_pay_user = user::find($id_user);
        $_full_pay = invoice_pay::find($_select_invoice_pay['id']);
        $parser = app(\App\Services\SmsVariableParser::class);
        $_invoice = \App\invoice::find($id_invoice);
        $template = '{نام_کامل} عزیز، فیش واریزی شما به مبلغ ' . number_format($_full_pay->amount ?? 0) . ' ریال توسط حسابداری تایید گردید.';
        $smsText = $parser->parse($template, $_pay_user, $_invoice);
        if (!empty($_pay_user->mobile)) {
          dispatch(new \App\Jobs\SendSingleSms($_pay_user->mobile, $smsText, $_pay_user->id));
        }
      } catch (\Exception $e) {
        \Log::warning('Payment approval SMS failed', ['error' => $e->getMessage()]);
      }
    }

    $_link_page_before = $_SERVER['HTTP_REFERER'];
    return redirect(!empty($_link_page_before) ? $_link_page_before : '/manager/invoice/user/' . $_select_invoice_pay['id_invoice'] . '/' . $_select_invoice_pay['id_user']);
  }
  /* END تغییر وضعیت فعال/غیرفعال به بالعکس */

  public function invoicePayDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(70)) {
      return redirect('/');
      exit();
    }

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

    return redirect('/manager/invoice/user/' . $id_invoice . '/' . $id_user);
  }

  /* START واریزی انبوه */

  public function invoiceBulkPayPage(int $idUser)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel(80)) {
      return redirect('/');
    }

    $user = user::select(['users.id', 'name', 'name2', 'hash'])
      ->join('user_grops', 'user_id', 'users.id')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->where('users.id', $idUser)
      ->firstOrFail();

    $calc    = new InvoicePenaltyCalculator();
    $service = new BulkPaymentService($calc, new PenaltyRecalculationService($calc));
    $debts   = $service->getUserDebts((int) $user['id']);

    $credits = BulkPaymentCredit::where('id_user', $user['id'])
      ->orderByDesc('created_at')
      ->get();

    return view('manager.invoice.bulkPay', [
      'user'    => $user,
      'debts'   => $debts,
      'credits' => $credits,
    ]);
  }

  public function invoiceBulkPay(int $idUser, Request $request)
  {
    if (!session()->has('manager') || !$this->managerAccessLevel(80)) {
      return redirect('/');
    }

    // شماره فیش
    $receiptId = (string) $request['number'];
    if ($receiptId === '' || $receiptId !== htmlspecialchars($receiptId)) {
      return back()->with('error', 'شماره فیش معتبر نیست');
    }

    // بررسی تکراری نبودن شماره واریزی انبوه
    $alreadyUsed = invoice_pay::where('id', 'like', $receiptId . '-I%')->exists();
    if ($alreadyUsed) {
      return back()->with('error', 'این شماره واریزی قبلاً ثبت شده است');
    }

    // مبلغ
    $amount = (int) intval(str_replace(',', '', $request['price']));
    if ($amount <= 0) {
      return back()->with('error', 'مبلغ معتبر نیست');
    }

    // تاریخ
    $date         = (string) $request['am'];
    $explodedDate = explode('/', $date);
    if (
      $date === ''
      || $date !== htmlspecialchars($date)
      || count($explodedDate) !== 3
      || $explodedDate[0] <= 1395
      || $date > verta()->format('Y/m/d')
      || $explodedDate[1] <= 0 || $explodedDate[1] >= 13
      || $explodedDate[2] <= 0 || $explodedDate[2] >= 32
    ) {
      return back()->with('error', 'تاریخ معتبر نیست');
    }

    // بررسی دسترسی مدیر به این کاربر
    $userCheck = user::select(['users.id'])
      ->join('user_grops', 'user_id', 'users.id')
      ->where('user_grops.grop_id', session('id_grop_manager'))
      ->where('users.id', $idUser)
      ->first();
    if (!$userCheck) {
      return redirect('/');
    }

    $calculator = new InvoicePenaltyCalculator();
    $service    = new BulkPaymentService($calculator, new PenaltyRecalculationService($calculator));
    $result   = $service->distribute($idUser, $amount, $receiptId, $date);

    if (empty($result['affected'])) {
      return redirect('/manager/invoice/bulk-pay/' . $idUser)
        ->with('error', 'هیچ بدهی فعالی برای این کاربر یافت نشد. ابتدا جریمه‌ها را محاسبه کنید.');
    }

    $msg = 'واریزی انبوه با موفقیت ثبت شد — مجموع پرداخت‌شده: ' . number_format($result['total_paid']) . ' ریال';
    if ($result['credit_remaining'] > 0) {
      $msg .= ' | اعتبار مازاد: ' . number_format($result['credit_remaining']) . ' ریال ذخیره شد';
    }

    return redirect('/manager/invoice/user/single/invoices/' . $idUser)->with('message', $msg);
  }

  /* END واریزی انبوه */

  public function invoiceDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $invoice = invoice::where('id_grop', session('id_grop_manager'))->findOrFail($id);
    $invoice_pays = invoice_pay::where("id_invoice", $invoice["id"])->get();
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay->delete();
    }
    $invoice_fines = invoice_fine::where("id_invoice", $invoice["id"])->get();
    foreach ($invoice_fines as $invoice_fine) {
      $invoice_fine->delete();
    }
    $invoice->delete();
    return redirect('/manager/invoice');
  }
  public function userInvoicePage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(71)) {
      return redirect('/');
      exit();
    }
    $user = user::join('user_grops', 'users.id', '=', 'user_grops.user_id')->where('user_grops.grop_id', session('id_grop_manager'))->where('users.id', $id)->select('users.*')->firstOrFail();
    $invoice_pays = invoice_pay::where("id_user", $user["id"])->where("active_pay", 1)->get();

    $price = 0;
    $price_discount = 0;
    foreach ($invoice_pays as $invoice_pay) {
      $invoice_pay["invoice"] = invoice::findOrfail($invoice_pay["id_invoice"]);
      if ($invoice_pay["am"] == null) {
        $invoice_pay["am"] = \verta($invoice_pay["created_at"]);
      }
      if (mb_substr($invoice_pay['text'], 0, 8) == 'تخفیف > ') {
        $price_discount += $invoice_pay["price"];
      } else {
        $price += $invoice_pay["price"];
      }
    }

    return view('manager.user.invoice', [
      "user" => $user,
      "invoice_pays" => $invoice_pays,
      "price" => $price,
      "price_discount" => $price_discount
    ]);
  }

  /**
   * حذف کاربر از یک فاکتور به هماری تمامی پرداخت ها و جرائم مربوط به آن
   */
  public function invoiceUserPaysFinesDelete($id_invoice, $id_user)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(67)) {
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
      return redirect('/manager/invoice/' . $id_invoice);
    } else {
      return redirect('/manager/invoice');
    }
  }

  public function cropPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(33)) {
      return redirect('/');
      exit();
    }
    $manager = manager::where("id_user", session("id_manager"))->first();

    $products = crop::where("id_grop", $manager["id_grop"])->get();
    foreach ($products as $product) {
      $product["grop"] = grop::findOrfail($product["id_grop"]);
    }
    return view('manager.crop.page', ["products" => $products]);
  }
  public function cropAddPage()
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(34)) {
      return redirect('/');
      exit();
    }

    return view('manager.crop.add');
  }

  public function cropAdd(Request $request)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(34)) {
      return redirect('/');
      exit();
    }

    $manager = manager::where("id_user", session("id_manager"))->first();

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
    $product->id_grop = $manager["id_grop"];
    $product->save();
    return redirect("/manager/crop");
  }

  public function cropEditPage($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(34)) {
      return redirect('/');
      exit();
    }

    $product = crop::where('id_grop', session('id_grop_manager'))->findOrFail($id);
    $grops = grop::all();
    return view('manager.crop.edit', ["product" => $product, "grops" => $grops]);
  }
  public function cropEdit(Request $request, $id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(34)) {
      return redirect('/');
      exit();
    }

    $product = crop::findOrfail($id);
    $product->name = $request["name"];
    $product->text = $request["text"];
    $product->price = intval(str_replace(",", "", $request["price"]));
    if ($file = $request->file("img")) {
      $name = $file->getClientOriginalName();
      $rond = rand(10000, 99999);
      $file->move("crop_img", $rond . $name);
      $product->img = $rond . $name;
    }
    $product->save();
    return redirect("/manager/crop");
  }
  public function cropActive($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    if (!$this->managerAccessLevel(35)) {
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

    return redirect("/manager/crop");
  }


  public function cropPay($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $product = crop::where('id_grop', session('id_grop_manager'))->findOrFail($id);
    $product_pay = crop_pay::where("id_crop", $product["id"])->where("active", 1)->get();
    $price = 0;
    foreach ($product_pay as $pay) {
      $pay["am"] = \verta($pay["created_at"]);
      $pay["user"] = user::findOrfail($pay["id_user"]);
      $price += $pay["price"];
    }

    return view('manager.crop.pay', ["product" => $product, "product_pay" => $product_pay, "price" => $price]);
  }


  public function cropPayDelete($id)
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $crop_pay = crop_pay::findOrfail($id);
    $id_crop = $crop_pay["id_crop"];
    $crop_pay->delete();
    return redirect("/manager/crop/pay/" . $id_crop);
  }

  public function showform(int $_id, $_idf, $role = 'manager')
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }
    $_info_form = form::findOrfail($_idf, ['id', 'name', 'fild']);
    $form_fild = json_decode($_info_form['fild'], 1);
    $user_forms = user_form::join('users', 'users.id', 'id_user')->where('user_forms.id', $_id)->get(['user_forms.id', 'id_user', 'id_form', 'form', 'users.id AS ID_User', 'mobile', 'name', 'name2', 'kod', 'hash']);

    foreach ($user_forms as $user_form) {
      $user_form->form = json_decode($user_form->form, true); // Decode JSON to array
    }
    $user_form = $user_forms->first(); // Get the first record

    return view("manager.form.userPagedetail", [
      'form_fild' => $form_fild,
      'user_forms' => $user_form,
      'user_form' => $user_form,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user),
    ]);
  }

  public function editform(int $_id, $_idf, $role = 'manager')
  {
    if (!session()->has('manager')) {
      return redirect('/');
      exit();
    }

    $_info_form = form::findOrfail($_idf, ['id', 'name', 'fild']);
    $form_fild = json_decode($_info_form['fild'], 1);
    $user_forms = user_form::join('users', 'users.id', 'id_user')->where('user_forms.id', $_id)->get(['user_forms.id', 'id_user', 'id_form', 'form', 'users.id AS ID_User', 'mobile', 'name', 'name2', 'kod', 'hash']);

    foreach ($user_forms as $user_form) {
      $user_form->form = json_decode($user_form->form, true); // Decode JSON to array
    }
    $user_form = $user_forms->first(); // Get the first record

    return view("manager.form.userPageEdit", [
      'form_fild'            => $form_fild,
      'user_forms'           => $user_form,
      'user_form'            => $user_form,
      'formats_allowed_file' => self::$_menu__formats_allowed_field_files,
      // Bug 5: needed by the view to build links to existing uploaded files
      'directory_files_user' => str_replace('/public_html/', '', self::$_directory_files_user),
    ]);
  }

  public function formNoneUserPage(int $_id)
  {
    if (!session()->has('manager')) return redirect('/');
    return $this->pageUserNoneFillForm($_id, 'manager');
  }

  /**
   * عملیات کپی از یک فرم در صفحه ویرایش
   */
  public function copy_form($id)
  {
    if (!session()->has('manager')) return redirect('/');

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
    return redirect("/manager/form");
  }


  //    public function shopAdd(Request $request)
  //    {
  //        if(!session()->has('manager')){return redirect('/');exit();}
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
  //        return redirect("/manager/user");
  //    }
  //    public function deleteShop($id)
  //    {
  //        if(!session()->has('manager')){return redirect('/');exit();}
  //        $shop=shop::findOrfail($id);
  //
  //        $shop->delete();
  //        return redirect("/manager/shop/");
  //
  //    }
}
