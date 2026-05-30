<?php

namespace App\Http\Controllers;

use App\contactu;
use App\grop;
use App\manager;
use App\user;
use App\admin;
use App\setting;
use App\user_grop;
use App\usergrop;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Kavenegar;


class homeController extends Controller
{

    private function sms($mobil, $code)
    {

        try {

            $receptor = "$mobil";
            $token = "$code";
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
    public function page()
    {
        //if (SESSION)
        if (session()->has('mobile') or session()->has('mobile_user') or session()->has('mobile_manager')) {
            if (session()->has('admin')) {
                return redirect('/admin/panel');
            } elseif (session()->has('manager')) {
                return redirect('/manager/panel');
            } else {
                return redirect('/user');
            }
        } else {
            session()->forget('mobile_login_code');
            $setting = setting::where("name", "name_login")->first();
            $contactu = setting::where("name", "contactu")->first();
            $description = setting::where("name", "description")->first();
            $img_login = setting::where("name", "img_login")->first();
            $mobile_contactu = setting::where("name", "mobile_contactu")->first();
            $text_contactu = setting::where("name", "text_contactu")->first();
            $hide_register_link = setting::where("name", "hide_register_link")->first();
            $hide_mainpage_contactus_link = setting::where("name", "hide_mainpage_contactus_link")->first();
            // dd($hide_register_link)
            return view('login', [
                "mobile_contactu" => $mobile_contactu,
                "text_contactu" => $text_contactu,
                "img_login" => $img_login,
                "setting" => $setting,
                "description" => $description,
                "contactu" => $contactu,
                "hide_register_link" => $hide_register_link,
                "hide_mainpage_contactus_link" => $hide_mainpage_contactus_link,
            ]);
        }
    }
    public function admin()
    {
        //if (SESSION)
        if (session()->has('mobile')) {
            if (session()->has('admin')) {
                return redirect('/admin/panel');
            }
        } else {
            $hide_register_link = setting::where("name", "hide_register_link")->first();

            return view('loginAdmin', [
                "hide_register_link" => $hide_register_link,
            ]);
        }
    }



    public function registerPage()
    {
        //if (SESSION)
        if (session()->has('email') or session()->has('mobile_user')) {
            if (session()->has('admin')) {
                return redirect('/admin/listUser');
            } else {
                return redirect('/user/scoreboard');
            }
        } else {
            session()->forget('mobile_register_code');
            $setting = setting::where("name", "code_grop_none")->first();
            $setting2 = setting::where("name", "name_register")->first();
            $img_register = setting::where("name", "img_register")->first();
            return view('register', ["img_register" => $img_register, "setting" => $setting, "setting2" => $setting2]);
        }
    }

    public function checkLoginAdmin(Request $request)
    {
        $user = admin::where("mobile", $request->mobile)->get();

        if (count($user) > 0) {
            if (Hash::check($request->pass, $user[0]["password"])) {

                session(['mobile' => $user[0]["mobile"]]);
                session(['name' => $user[0]["name"]]);
                session(['id' => $user[0]["id"]]);
                session(['admin' => 1]);

                return redirect('/admin');
            } else {

                return redirect('/');
            }
        } else {

            return redirect('/');
        }
    }


    public function checkLoginUser(string $_mobile, string $_password = null)
    {
        $_info_user = user::select(['users.id', 'mobile', 'users.name', 'name2', 'kod', 'hash', 'password', 'active_am'])->selectSub('user_grops.grop_id', 'ID_Grop')-> //آیدی مجموعه
            selectSub('user_grops.id_usergrop', 'ID_User_Grop')-> //آیدی گروه کاربری
            selectSub('usergrops.name', 'Name_Grop')-> //نام گروه
            where('mobile', $_mobile)->join('user_grops', 'user_id', 'users.id')->join('usergrops', 'usergrops.id', 'user_grops.id_usergrop');
        // dd(str_replace(['?'], ['\'%s\''], $_info_user->toSql()), $_info_user->getBindings());
        //   dd($_mobile);
        $_info_user = $_info_user->first();
        if (!isset($_info_user['mobile'])) return;
        $_id_grop_manager = manager::where('id_user', $_info_user['id'])->first(['id_grop']);
        if (!empty($_id_grop_manager)) :
            session(['mobile_manager' => $_info_user['mobile']]);
            session(['name_manager' => $_info_user['name']]);
            session(['id_manager' => $_info_user['id']]);
            session(['id_grop_manager' => $_id_grop_manager['id_grop']]);
            $_manager_grop = grop::find($_id_grop_manager['id_grop']);
            session(['name_grop_manager' => $_manager_grop ? $_manager_grop->name : '']);
            session(['manager' => 1]);
        else :
            $setting = setting::where('name', 'register_pass')->first();
            if ($setting['input1']) :
                if (Hash::check($_password, $_info_user['password'])) :
                    session(['mobile_user' => $_info_user['mobile']]);
                    session(['name_user' => $_info_user['name']]);
                    session(['id_user' => $_info_user['id']]);

                    $_set_sessions = true;
                endif;
            else :
                session(['mobile_user' => $_info_user['mobile']]);
                session(['name_user' => $_info_user['name']]);
                session(['id_user' => $_info_user['id']]);

                $_set_sessions = true;
            endif;

            if (isset($_set_sessions)) :
                $grop = grop::where('id', $_info_user['ID_Grop'])->first();
                session(['name_2_user' => $_info_user['name2']]);
                session(['national_code_user' => $_info_user['kod']]);
                session(['hash_user' => $_info_user['hash']]);
                session(['date_subscription_user' => $_info_user['active_am']]);
                session(['id_grop_user' => $_info_user['ID_Grop']]);
                session(['grop_name' => $grop->name]);
                session(['id_user_grop' => $_info_user['ID_User_Grop']]);
                session(['name_grop_user' => $_info_user['Name_Grop']]);
            endif;
        endif;
    }


    public function logout()
    {
        if (session()->has('admin')) {
            session()->forget('mobile');
            session()->forget('id');
            session()->forget('name');
            session()->forget('admin');
        } elseif (session()->has('manager')) {
            session()->forget('mobile_manager');
            session()->forget('name_manager');
            session()->forget('id_manager');
            session()->forget('manager');
        } else {
            if (session()->has('mobile_user')) {
                session()->forget('mobile_user');
                session()->forget('id_user');
                session()->forget('name_user');
                session()->forget('grop_user');
                session()->forget('menus');
            }
        }
        return redirect('/');
    }


    public function checkRegister(Request $request)
    {
        $user = User::where("mobile", $request->mobile)->get();

        if (count($user) > 0) {
            echo 100;
        } else {

            echo 0;
        }
    }
    public function checkLogin(Request $request)
    {
        $user = user::where("mobile", $request->mobile)->get();
        $setting = setting::where("name", "register_pass")->first();
        if (count($user) > 0) {
            $user_grop = user_grop::where('user_id', $user->first()->id)->first();
            $prevent_user_sms = 0;
            if ($user_grop != null && $user_grop->grop) {
                $prevent_user_sms = $user_grop->grop->prevent_user_sms;
            }
            $code = rand(10000, 99999);
            session(['mobile_login_code' => $request["mobile"]]);
            if ($setting["input1"]) {
                if ($user[0]["password"] == null) {
                    echo 50;
                    /**
                     * جلوگیری از ارسال پیامک در صورتی که تیک جلوگیری از ارسال پیامک در گروه خورده باشد
                     */
                    if (!$prevent_user_sms) {
                        $user[0]->code = $code;
                        $user[0]->save();
                        $this->sms($user[0]["mobile"], $code);
                    }
                } else {
                    echo 100;
                }
            } else {
                /**
                 * جلوگیری از ارسال پیامک در صورتی که تیک جلوگیری از ارسال پیامک در گروه خورده باشد
                 */
                if (!$prevent_user_sms) {
                    $user[0]->code = $code;
                    $user[0]->save();
                    $this->sms($user[0]["mobile"], $code);
                }
                echo 70;
            }
        } else {
            echo 0;
        }
    }

    public function register(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }

        $user = new user();
        $setting = setting::where("name", "register_pass")->first();
        $setting_grop_none = setting::where("name", "code_grop_none")->first();

        $code = rand(10000, 99999);

        $user->mobile = $request["mobile"];
        $user->name = $request["name"];
        $user->name2 = $request["name2"];
        //        $user->phone=$request["phone"];
        $user->kod = $request["kod"];
        //        $user->state=$request["state"];
        //        $user->city=$request["city"];
        $user->code = $code;
        session(['mobile_register_code' => $request["mobile"]]);
        $user->save();
        $this->sms($user["mobile"], $code);
        if ($setting_grop_none["input1"]) {
            $grop = grop::where("code", $setting_grop_none["input2"])->first();
            $usergrop = usergrop::where("id_grop", $grop["id"])->where("normal", 1)->first();
            $user_grop = new user_grop();
            $user_grop->user_id = $user["id"];
            $user_grop->grop_id = $grop["id"];
            $user_grop->id_usergrop = $usergrop["id"];
            $user_grop->save();
        } else {
            $setting_code_grop = setting::where("name", "code_grop")->first();
            $grop = grop::where("code", $request["code_grop"])->get();
            if (count($grop) == 0) {
                $grop2 = grop::where("code", $setting_code_grop["input1"])->first();
                $usergrop2 = usergrop::where("id_grop", $grop2["id"])->where("normal", 1)->first();
                $user_grop = new user_grop();
                $user_grop->user_id = $user["id"];
                $user_grop->grop_id = $grop2["id"];
                $user_grop->id_usergrop = $usergrop2["id"];
                $user_grop->save();
            } else {
                $usergrop = usergrop::where("id_grop", $grop[0]["id"])->where("normal", 1)->first();
                $user_grop = new user_grop();
                $user_grop->user_id = $user["id"];
                $user_grop->grop_id = $grop[0]["id"];
                $user_grop->id_usergrop = $usergrop["id"];
                $user_grop->save();
            }
        }

        if ($setting["input1"]) {
            echo 100;
        } else {
            echo 50;
        }
    }
    public function checkRegisterCode(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_register_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_register_code'))->get();

        if (count($user) > 0) {
            if ($user[0]["code"] != $request["code"]) {
                echo 100;
            }
        } else {
            echo 0;
        }
    }

    public function registerLogin(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_register_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_register_code'))->first();
        $setting = setting::where("name", "register_pass")->first();

        if ($user["code"] == $request["code"]) {
            if ($setting["input1"]) {
                $user->password = Hash::make($request["pass"]);
                $user->save();
                $this->checkLoginUser($user["mobile"], $request["pass"]);
            } else {
                $this->checkLoginUser($user["mobile"]);
            }

            return redirect('/');
            exit();
        } else {
            return redirect('/');
            exit();
        }
    }
    public function forgotPassword()
    {
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }
        $user_get = user::where("mobile", session('mobile_login_code'))->get();
        if (count($user_get) > 0) {
            $user = user::findOrfail($user_get[0]["id"]);
            $user->password = null;
            $user->save();
            return redirect('/');
        } else {
            return redirect('/');
            exit();
        }
    }


    public function loginPassCode(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->get();

        if (count($user) > 0) {
            if ($user[0]["code"] != $request["code"]) {
                echo 100;
            }
        } else {
            echo 0;
        }
    }
    public function loginPass(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->get();

        if (count($user) > 0) {
            if (!Hash::check($request["pass"], $user[0]["password"])) {
                echo 100;
            } else {
                // در صورتی که کاربر غیرفعال باشد اجازه ورود نمی دهد
                if ($user[0]["inactive"] == 1) {
                    echo 102;
                } else {
                    $prevent_users_login = setting::where("name", "prevent_users_login")->first();
                    $_user_manager = manager::where('id_user', $user[0]['id'])->first();
                    //  echo $user->id;
                    // جلوگیری از ورود کاربر غیر مدیر در صورتی که در پنل ادمین تیک جلوگیری از ورود کاربر خورده باشد
                    if (
                        empty($_user_manager) &&
                        $prevent_users_login != null && $prevent_users_login->input1 == 1
                    ) {
                        echo 101;
                    }
                }
            }
        } else {

            echo 0;
        }
    }
    public function loginCode(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->get();

        if (count($user) > 0) {
            if ($user[0]["code"] != $request["code"]) {
                echo 100;
            }
        } else {
            echo 0;
        }
    }

    public function loginPassCodeForm(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->first();
        $setting = setting::where("name", "register_pass")->first();

        if ($user["code"] == $request["code"]) {
            if ($setting["input1"]) {
                $user->password = Hash::make($request["pass"]);
                $user->save();
                $this->checkLoginUser($user['mobile'], $request["pass"]);
            } else {
                $this->checkLoginUser($user['mobile']);
            }

            return redirect('/');
        } else {
            return redirect('/');
            exit();
        }
    }
    public function loginPassForm(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->first();
        if (Hash::check($request["pass"], $user["password"])) {
            $this->checkLoginUser(session('mobile_login_code'), $request["pass"]);
            //return redirect('/');
            return redirect('/user/WaitingUserFineCalc');
        } else {
            return redirect('/');
        }
    }

    public function loginCodeForm(Request $request)
    {
        if (session()->has('admin')) {
            return redirect('/');
            exit();
        }
        if (session()->has('mobile_user')) {
            return redirect('/');
            exit();
        }
        if (!session()->has('mobile_login_code')) {
            return redirect('/');
            exit();
        }

        $user = user::where("mobile", session('mobile_login_code'))->first();


        if ($user["code"] == $request["code"]) {
            $this->checkLoginUser(session('mobile_login_code'));
            return redirect('/');
        } else {
            return redirect('/');
            exit();
        }
    }





    public function contactu(Request $request)
    {
        $contactu = new contactu();
        $contactu->mobile = $request["contactu_mobile"];
        $contactu->name = $request["contactu_name"];
        $contactu->name2 = $request["contactu_name2"];
        $contactu->text = $request["contactu_text"];
        $contactu->save();

        return redirect('/');
    }
}
