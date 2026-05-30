<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ $setting2->input1 }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    		<link href="/assets/css/login-3.css" rel="stylesheet" type="text/css" />

    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css"/>

    <script src="/assets/js/modernizr.min.js"></script>
</head>

<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 11 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head><base href="../../../../">
		<meta charset="utf-8" />
		<title>Sign Up | Keenthemes</title>
		<meta name="description" content="Singin page example" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="canonical" href="https://keenthemes.com/metronic" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="assets/css/pages/login/login-3.css" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<link href="assets/css/themes/layout/header/base/light.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/header/menu/light.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/brand/dark.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/themes/layout/aside/dark.css" rel="stylesheet" type="text/css" />
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
				<div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid wizard" id="kt_login">
	<div class="login-aside d-flex flex-column flex-row-auto">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
						<!--begin::Aside header-->
						<a href="#" class="login-logo text-center pt-lg-25 pb-10">
							<img src="assets/media/logos/logo-1.png" class="max-h-70px" alt="" />
						</a>
						<!--end::Aside header-->
						<!--begin::Aside Title-->
						<h1 class="font-weight-bolder text-center font-size-h4 text-dark-30 line-height-xl">{{ \App\setting::where('name', 'name_register')->value('input1') ?? 'سامانه تعاونیهای مسکن بیمه ایران' }}
					</h1>
						<!--end::Aside Title-->
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-x-center" style="background-position-y: calc(100% + 5rem);">
					    
					                  <img id="img_advertising1"
                    style="

    max-height: 140px;
    width: 100%;
    height: 140px;"
                    width="300px" height="100px" img_active="1"
                    img1="{{ \App\Http\Controllers\userController::img_advertising(1) }}"
                    img2="{{ \App\Http\Controllers\userController::img_advertising(2) }}"
                    img3="{{ \App\Http\Controllers\userController::img_advertising(3) }}"
                    img4="{{ \App\Http\Controllers\userController::img_advertising(4) }}"
                    src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(1) }}">
      
            <img id="img_advertising2"
                style="
                            max-height: 140px;
            width: 100%;
            height: 140px;"
                width="300px" height="100px" img_active="1"
                img1="{{ \App\Http\Controllers\userController::img_advertising(5) }}"
                img2="{{ \App\Http\Controllers\userController::img_advertising(6) }}"
                img3="{{ \App\Http\Controllers\userController::img_advertising(7) }}"
                img4="{{ \App\Http\Controllers\userController::img_advertising(8) }}"
                src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(5) }}">

            <img id="img_advertising3"
                style="
            max-height: 140px;
            
            width: 100%;
            height: 140px;
            "width="300px"
                height="100px" img_active="1" img1="{{ \App\Http\Controllers\userController::img_advertising(9) }}"
                img2="{{ \App\Http\Controllers\userController::img_advertising(10) }}"
                img3="{{ \App\Http\Controllers\userController::img_advertising(11) }}"
                img4="{{ \App\Http\Controllers\userController::img_advertising(12) }}"
                src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(9) }}">

				</div>
					</div>				
				<!--begin::Content-->
				<div class="login-content flex-column-fluid d-flex flex-column p-10">

					<!--begin::Top-->
					<!--end::Top-->
					<!--begin::Wrapper-->
					<div class="d-flex flex-row-fluid flex-center">
						<!--begin::Signin-->
						<div class="login-form login-form-signup">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column ">
						<!--begin::Aside header-->
		
						<!--end::Aside header-->
						<!--begin::Aside Title-->
						<!--end::Aside Title-->
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
        <div class="p-20" id="div-form">

							<!--begin::Form-->
							<form class="form" onsubmit="return register();" id="form-register" method="post" action="/"  novalidate="novalidate" >
							                    @csrf

								<!--begin: Wizard Step 1-->
								<div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
									<!--begin::Title-->
									<div class="pb-10 pb-lg-15">
										<h3 class="font-weight-bolder text-dark display5 d-flex ">فرم ثبت نام</h3>
										<div class="text-muted font-weight-bold font-size-h4 d-flex "> حساب کاربری دارید؟      
										<a href="/" class="text-primary font-weight-bolder">برگشت به صفحه ورود </a></div>
									</div>
																		<div class="form-group">
										<label dir="rtl" class="font-size-h6 font-weight-bolder text-dark d-flex pr-2"   type="text">شماره تماس</label>
										<input type="tel" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="mobile"   required=""  data-parsley-pattern="09[0-9]{9}" placeholder=".........09" />
									</div>
									<!--begin::Title-->
									<!--begin::Form Group-->
									<div class="form-group">
										<label class="font-size-h6 font-weight-bolder text-dark d-flex pr-2">نام</label>
										<input type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="name" type="text" required=""  />
									</div>
									<!--end::Form Group-->
									<!--begin::Form Group-->
									<div class="form-group">
										<label class="font-size-h6 font-weight-bolder text-dark d-flex pr-2">نام خانوادگی</label>
										<input type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="name2" type="text" required="" value="" />
									</div>
													<div class="form-group">
										<label class="font-size-h6 font-weight-bolder text-dark d-flex pr-2">کد ملی</label>
										<input type="number" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="kod" type="text" required="" data-parsley-pattern="[0-9]{10}"  placeholder="کد ملی" />
									</div>
									               @if($setting["input1"]==0)
                <div class="form-group">
                 
                        <label for="code_grop" class="font-size-h6 font-weight-bolder text-dark d-flex pr-2">کد ثبت نام </label>
                        <input  class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="code_grop" type="text" name="code_grop" type="text"    placeholder="کد ثبت نام">
                </div>
                   @endif
					<div class="form-group">
			         <div class=" text-center">
                    <canvas id="canvas"></canvas>
                </div>
					</div>
													<div class="form-group">
										<label class="font-size-h6 font-weight-bolder text-dark d-flex pr-2">کد امنیتی</label>
										<input type="number" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" required="" data-parsley-minlength="5" data-parsley-maxlength="5" id="captcha" name="captcha" value="" />
									</div>
					<div class="form-group">

                  <button class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3 d-flex" type="submit">
                            ثبت نام
                        </button>
                        									</div>

									<!--end::Form Group-->
									<!--begin::Form Group-->

									<!--end::Form Group-->
								</div>
								<!--end: Wizard Step 1-->
		
							</form>
							</div>
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
			</div>
<div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

</div>
<div class="clearfix"></div>
<div class="wrapper-page">


<!-- end wrapper page -->


<!-- jQuery  -->
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/popper.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/waves.js"></script>
<script src="/assets/js/jquery.slimscroll.js"></script>

<!-- App js -->
<script src="/assets/js/jquery.core.js"></script>
<script src="/assets/js/jquery.app.js"></script>
<script src="/js/jquery-captcha.min.js"></script>
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
<script>


    setTimeout(img_advertising1, 2000);


    function img_advertising1(){

        var img_active=$("#img_advertising1").attr("img_active");

        if (img_active==1){
            var img=$("#img_advertising1").attr("img2");
            $("#img_advertising1").attr("src","/advertising_img/"+img);
            $("#img_advertising1").attr("img_active","2");
        }
        if (img_active==2){
            var img=$("#img_advertising1").attr("img3");
            $("#img_advertising1").attr("src","/advertising_img/"+img);
            $("#img_advertising1").attr("img_active","3");
        }
        if (img_active==3){
            var img=$("#img_advertising1").attr("img4");
            $("#img_advertising1").attr("src","/advertising_img/"+img);
            $("#img_advertising1").attr("img_active","4");
        }
        if (img_active==4){
            var img=$("#img_advertising1").attr("img1");
            $("#img_advertising1").attr("src","/advertising_img/"+img);
            $("#img_advertising1").attr("img_active","1");
        }
        setTimeout(img_advertising1, 2000);
    }

    setTimeout(img_advertising2, 2000);
    function img_advertising2(){

        var img_active=$("#img_advertising2").attr("img_active");

        if (img_active==1){
            var img=$("#img_advertising2").attr("img2");
            $("#img_advertising2").attr("src","/advertising_img/"+img);
            $("#img_advertising2").attr("img_active","2");
        }
        if (img_active==2){
            var img=$("#img_advertising2").attr("img3");
            $("#img_advertising2").attr("src","/advertising_img/"+img);
            $("#img_advertising2").attr("img_active","3");
        }
        if (img_active==3){
            var img=$("#img_advertising2").attr("img4");
            $("#img_advertising2").attr("src","/advertising_img/"+img);
            $("#img_advertising2").attr("img_active","4");
        }
        if (img_active==4){
            var img=$("#img_advertising2").attr("img1");
            $("#img_advertising2").attr("src","/advertising_img/"+img);
            $("#img_advertising2").attr("img_active","1");
        }
        setTimeout(img_advertising2, 2000);
    }

    setTimeout(img_advertising3, 2000);


    function img_advertising3(){

        var img_active=$("#img_advertising3").attr("img_active");

        if (img_active==1){
            var img=$("#img_advertising3").attr("img2");
            $("#img_advertising3").attr("src","/advertising_img/"+img);
            $("#img_advertising3").attr("img_active","2");
        }
        if (img_active==2){
            var img=$("#img_advertising3").attr("img3");
            $("#img_advertising3").attr("src","/advertising_img/"+img);
            $("#img_advertising3").attr("img_active","3");
        }
        if (img_active==3){
            var img=$("#img_advertising3").attr("img4");
            $("#img_advertising3").attr("src","/advertising_img/"+img);
            $("#img_advertising3").attr("img_active","4");
        }
        if (img_active==4){
            var img=$("#img_advertising3").attr("img1");
            $("#img_advertising3").attr("src","/advertising_img/"+img);
            $("#img_advertising3").attr("img_active","1");
        }
        setTimeout(img_advertising3, 2000);
    }




    $('form').parsley();
    function code11() {
        var error = 0;
        var code = $('input[name="code"]').val();
        var pass = $('input[name="pass"]').val();
        var token = $('input[name="_token"]').val();

        $.ajax('/check/register/code', {
            type: 'post',
            async: false
            , data: {_token: token,code:code,pass:pass}
            , success: function (msg) {

                if (msg == 100) {
                    error = error + 1;
                    $("#alert_login").prepend('<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                        '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                        '                <span>کد تایید اشتباه می باشد .</span>\n' +
                        '            </div>')
                    setTimeout(function() {
                        $('#alert-1').remove();
                    }, 5000);
                }


            }
        })
        if (error>0 ){
            return false;
        }

    }
    const captcha = new Captcha($('#canvas'),{
        width: 200,
        length: 5,
        caseSensitive: true,
        height: 70,
        font: 'bold 40px IRANSans',
        resourceType: '0',

    });

    function register() {
        var error = 0;
        var mobile = $('input[name="mobile"]').val();
        var name = $('input[name="name"]').val();
        var name2 = $('input[name="name2"]').val();
        // var phone = $('input[name="phone"]').val();
        var kod = $('input[name="kod"]').val();
        // var state = $('input[name="state"]').val();
        // var city = $('input[name="city"]').val();
        var code_grop = $('input[name="code_grop"]').val();

        var token = $('input[name="_token"]').val();
        var factor= $('input[name="captcha"]').val();
        const ans = captcha.valid(factor);
        if(!ans){
            error = error + 1;
            $("#alert_login").prepend('<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                <span>کد امنیتی  اشتباه می باشد</span>\n' +
                '            </div>')
            setTimeout(function() {
                $('#alert-1').remove();
            }, 5000);
        }
        $.ajax('/check/register',
            {
                type: 'post',
                async: false
                , data: {_token: token, mobile: mobile}
                , success: function (msg) {

                    if (msg == 100) {
                        error = error + 1;
                        $("#alert_login").prepend('<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>شما قبلا ثبتنام کرده اید لطفا <a href="/" class="text-primary">وارد</a> شوید</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);

                    }

                }
            })


        if (error==0 && $("#form-register").parsley().isValid()){
            $.ajax('/register',
                {
                    type: 'post',
                    async: false
                    , data: {_token: token, mobile: mobile,name:name,name2:name2,kod:kod,code_grop:code_grop}
                    , success: function (msg) {

                        if (msg == 100) {
                                 $("#form-register").remove();
                                 $("#div-form").append(
                                     '<form onsubmit="return code11();" id="form-register2" class="form-horizontal m-t-20" method="post" action="/register/code">\n' +
                                     '                @csrf\n' +
                                     '\n' +
                                     '                <div class="form-group">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <label class=" d-flex pr-2" for="exampleInputEmail1">کد تایید : </label>\n' +
                                     '                        <input  class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="code" type="text" required=""  data-parsley-pattern="[0-9]{5}" placeholder=".....">\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '                <div class="form-group">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <label class=" d-flex pr-2"  for="exampleInputEmail1">رمز عبور : </label>\n' +
                                     '                        <input  class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="pass" data-parsley-minlength="6" type="password" required=""  id="pass1" placeholder="رمز عبور">\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '                <div class="form-group">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <label class=" d-flex pr-2"  for="exampleInputEmail1">تکرار رمز عبور : </label>\n' +
                                     '                        <input  class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="pass2" type="password" required="" data-parsley-equalto="#pass1"   placeholder="تکرار رمز عبور">\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '\n' +
                                     '\n' +
                                     '                <div class="form-group text-center m-t-30">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <button class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3 d-flex" type="submit">\n' +
                                     '                            ورود\n' +
                                     '                        </button>\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '\n' +
                                     '\n' +
                                     '            </form>'
                                 )

                        }if (msg == 50) {
                                 $("#form-register").remove();
                                 $("#div-form").append(
                                     '<form onsubmit="return code11();" id="form-register2" class="form-horizontal m-t-20" method="post" action="/register/code">\n' +
                                     '                @csrf\n' +
                                     '\n' +
                                     '                <div class="form-group">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <label class=" d-flex pr-2"  for="exampleInputEmail1">کد تایید : </label>\n' +
                                     '                        <input  class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="code" type="text" required=""  data-parsley-pattern="[0-9]{5}" placeholder=".....">\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '\n' +
                                     '\n' +
                                     '                <div class="form-group text-center m-t-30">\n' +
                                     '                    <div class="col-xs-12">\n' +
                                     '                        <button class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3 d-flex"" type="submit">\n' +
                                     '                            ورود\n' +
                                     '                        </button>\n' +
                                     '                    </div>\n' +
                                     '                </div>\n' +
                                     '\n' +
                                     '\n' +
                                     '            </form>'
                                 )

                        }
                        $('form').parsley();
                    }
                })
        }


            return false;

    }





</script>

</body>
</html>
