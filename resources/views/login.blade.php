<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ \App\setting::where('name', 'site_title')->value('input1') ?? 'سامانه تعاونیهای مسکن بیمه ایران' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <link href="/assets/css/login-3.css" rel="stylesheet" type="text/css" />

        <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />

    <script src="/assets/js/modernizr.min.js"></script>

</head>

<body>

    <!-- sample modal content -->
    <div id="myModal" class="modal fade  bs-example-modal-lg " tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0" id="myModalLabel">تماس با ما</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <form id="form-register-contactu" class="form-horizontal m-t-20" method="post"
                        action="/contactu/add">
                        @csrf

                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label for="exampleInputEmail1">شماره موبایل : </label>
                                <input class="form-control" name="contactu_mobile" type="text"
                                    placeholder="شماره موبایل " required="" data-parsley-pattern="09[0-9]{9}">
                            </div>


                            <div class="col-lg-4 mb-3">
                                <label for="exampleInputEmail1">نام : </label>
                                <input class="form-control" name="contactu_name" type="text" required=""
                                    placeholder="نام ">
                            </div>


                            <div class="col-lg-4 mb-3">
                                <label for="exampleInputEmail1"> نام خانوادگی : </label>
                                <input class="form-control" name="contactu_name2" type="text" required=""
                                    placeholder="نام خانوادگی">
                            </div>

                            <div class="col-12 mb-3">
                                <label for="exampleInputEmail1"> متن پیام : </label>
                                <textarea class="form-control" name="contactu_text" type="text" required="" placeholder="متن پیام"></textarea>
                            </div>



                        </div>

                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light"
                                    type="submit">
                                   ارسال پیام
                                </button>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <p><strong>شماره تلفن : </strong> {{ $mobile_contactu['input1'] }}</p>
                            </div>
                            <div class="col-12 ">
                                <p><strong>نشانی : </strong> {{ $text_contactu['input3'] }}</p>
                            </div>
                        </div>

                    </form>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <!-- sample modal content -->
    <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0" id="myModalLabel">راهنما</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    {{ $description['input3'] }}

                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




<!--here-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Aside-->
				<div class="login-aside d-flex flex-column flex-row-auto">
					<!--begin::Aside Top-->
					<div class="d-flex flex-column-auto flex-column pt-lg-40 ">
						<!--begin::Aside header-->
						<a href="#" class="login-logo text-center pt-lg-25 pb-10">
							<img src="assets/media/logos/logo-1.png" class="max-h-70px" alt="" />
						</a>
						<!--end::Aside header-->
						<!--begin::Aside Title-->
						<h3 class="font-weight-bolder text-center font-size-h4 text-dark-50 line-height-xl">{{ $setting->input1 ?? 'سامانه تعاونیهای مسکن بیمه ایران' }}
					</h3>
						<!--end::Aside Title-->
					</div>
					<!--end::Aside Top-->
					<!--begin::Aside Bottom-->
					<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-x-center" style="background-position-y: calc(100% + 5rem);">
					                    <img id="img_advertising1"
                    style="
    max-height: 140px;
    width: -webkit-fill-available;
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
            width: -webkit-fill-available;
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
            width: -webkit-fill-available;
    height: 140px;"
            width="300px"
                height="100px" img_active="1" img1="{{ \App\Http\Controllers\userController::img_advertising(9) }}"
                img2="{{ \App\Http\Controllers\userController::img_advertising(10) }}"
                img3="{{ \App\Http\Controllers\userController::img_advertising(11) }}"
                img4="{{ \App\Http\Controllers\userController::img_advertising(12) }}"
                src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(9) }}">
					               
				</div>
					</div>
  
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="login-content flex-row-fluid d-flex flex-column p-10">
		
					<!--begin::Wrapper-->
						<!--begin::Signin-->
						<div class="login-form">
						   	<div id="div-form" >

							<!--begin::Form-->
							<form class="form" onsubmit="return login();"  id="kt_login_singin_form" action="">
								<!--begin::Title-->
								<div class="pb-5 pb-lg-15">
									<h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">ورود به سامانه </h3>
									            @if ($hide_register_link == null || $hide_register_link->input1 == 0)
									<div class="text-muted font-weight-bold font-size-h4">حساب کاربری ندارید؟
									<a href="/register" class="text-primary font-weight-bolder">ثبت نام</a></div>                                </b></a></p>
                @endif

								</div>
								<!--begin::Title-->
								<!--begin::Form group-->
								<div  class="form-group">
									<label class="font-size-h6 font-weight-bolder text-dark">شماره تماس</label>
									<input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="text"  autocomplete="off"   name="mobile" type="text" required=""
                                data-parsley-pattern="09[0-9]{9}" placeholder=".........09"/>
								</div>
								<!--end::Form group-->
								<!--begin::Form group-->

								<!--end::Form group-->
								<!--begin::Action-->
								<div class="pb-lg-0 pb-5">
									<button type="submit" id="kt_login_singin_form_submit_button" class="btn btn-success font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" style="width:85px;">ورود </button>
	 					           @if ($hide_mainpage_contactus_link == null || $hide_mainpage_contactus_link->input1 == 0)
                    <button type="button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3 " style="width:110px;" data-toggle="modal"
                        data-target="#myModal">تماس</button>
                @endif
                
                <button type="button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3 " style="width:100px;" data-toggle="modal"
                    data-target="#myModal2">راهنما</button>
								</div>
								<!--end::Action-->
							</form>
		
							<!--end::Form-->
						</div>
						<!--end::Signin-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
			</body>

    <!--here-->
    <div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

    </div>

    <!--<div class="account-pages"-->
    <!--    style="background-image: url('/img_login_register/{{ $img_login['input1'] }}') !important;background-size: auto; background-repeat: no-repeat, repeat; background-size: cover;">-->
    <!--</div>-->
    <div class="clearfix"></div>

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

    <!-- Validation js (Parsleyjs) -->
    <script type="text/javascript" src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>
    <script>
        setTimeout(img_advertising1, 2000);


        function img_advertising1() {

            var img_active = $("#img_advertising1").attr("img_active");

            if (img_active == 1) {
                var img = $("#img_advertising1").attr("img2");
                $("#img_advertising1").attr("src", "/advertising_img/" + img);
                $("#img_advertising1").attr("img_active", "2");
            }
            if (img_active == 2) {
                var img = $("#img_advertising1").attr("img3");
                $("#img_advertising1").attr("src", "/advertising_img/" + img);
                $("#img_advertising1").attr("img_active", "3");
            }
            if (img_active == 3) {
                var img = $("#img_advertising1").attr("img4");
                $("#img_advertising1").attr("src", "/advertising_img/" + img);
                $("#img_advertising1").attr("img_active", "4");
            }
            if (img_active == 4) {
                var img = $("#img_advertising1").attr("img1");
                $("#img_advertising1").attr("src", "/advertising_img/" + img);
                $("#img_advertising1").attr("img_active", "1");
            }
            setTimeout(img_advertising1, 2000);
        }

        setTimeout(img_advertising2, 2000);


        function img_advertising2() {

            var img_active = $("#img_advertising2").attr("img_active");

            if (img_active == 1) {
                var img = $("#img_advertising2").attr("img2");
                $("#img_advertising2").attr("src", "/advertising_img/" + img);
                $("#img_advertising2").attr("img_active", "2");
            }
            if (img_active == 2) {
                var img = $("#img_advertising2").attr("img3");
                $("#img_advertising2").attr("src", "/advertising_img/" + img);
                $("#img_advertising2").attr("img_active", "3");
            }
            if (img_active == 3) {
                var img = $("#img_advertising2").attr("img4");
                $("#img_advertising2").attr("src", "/advertising_img/" + img);
                $("#img_advertising2").attr("img_active", "4");
            }
            if (img_active == 4) {
                var img = $("#img_advertising2").attr("img1");
                $("#img_advertising2").attr("src", "/advertising_img/" + img);
                $("#img_advertising2").attr("img_active", "1");
            }
            setTimeout(img_advertising2, 2000);
        }

        setTimeout(img_advertising3, 2000);


        function img_advertising3() {

            var img_active = $("#img_advertising3").attr("img_active");

            if (img_active == 1) {
                var img = $("#img_advertising3").attr("img2");
                $("#img_advertising3").attr("src", "/advertising_img/" + img);
                $("#img_advertising3").attr("img_active", "2");
            }
            if (img_active == 2) {
                var img = $("#img_advertising3").attr("img3");
                $("#img_advertising3").attr("src", "/advertising_img/" + img);
                $("#img_advertising3").attr("img_active", "3");
            }
            if (img_active == 3) {
                var img = $("#img_advertising3").attr("img4");
                $("#img_advertising3").attr("src", "/advertising_img/" + img);
                $("#img_advertising3").attr("img_active", "4");
            }
            if (img_active == 4) {
                var img = $("#img_advertising3").attr("img1");
                $("#img_advertising3").attr("src", "/advertising_img/" + img);
                $("#img_advertising3").attr("img_active", "1");
            }
            setTimeout(img_advertising3, 2000);
        }



        $('form').parsley();

        function code1() {
            var error = 0;

            var code = $('input[name="code"]').val();
            var token = $('input[name="_token"]').val();

            $.ajax('/login/code/pass', {
                type: 'post',
                async: false,
                data: {
                    _token: token,
                    code: code
                },
                success: function(msg) {
                    console.log("1")
                    if (msg == 100) {
                        error = error + 1;
                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>کد تایید اشتباه می باشد .</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    }


                }
            })
            if (error > 0) {
                return false;
            }

        }

        function code2() {
            var error = 0;

            var pass = $('input[name="pass"]').val();
            var token = $('input[name="_token"]').val();

            $.ajax('/login/pass', {
                type: 'post',
                async: false,
                data: {
                    _token: token,
                    pass: pass
                },
                success: function(msg) {
                    console.log("1")
                    if (msg == 100) {
                        error = error + 1;
                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>پسورد وارد شده اشتباه می باشد .</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    } else if (msg == 101) {
                        error = error + 1;
                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>شما اجازه ورود ندارید .</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    } else if (msg == 102) {
                        error = error + 1;
                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>وضعیت کاربری شما غیرفعال می باشد.</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    }


                }
            })
            if (error > 0) {
                return false;
            }

        }

        function code3() {
            var error = 0;

            var code = $('input[name="code"]').val();
            var token = $('input[name="_token"]').val();

            $.ajax('/login/code', {
                type: 'post',
                async: false,
                data: {
                    _token: token,
                    code: code
                },
                success: function(msg) {
                    console.log(msg)
                    if (msg == 100) {
                        error = error + 1;
                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>کد تایید اشتباه می باشد .</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    }


                }
            })

            if (error > 0) {
                return false;
            }


        }

        function login() {
            var error = 0;

            var mobile = $('input[name="mobile"]').val();
            var token = $('input[name="_token"]').val();
            $.ajax('/check/login', {
                type: 'post',
                async: false,
                data: {
                    _token: token,
                    mobile: mobile
                },
                success: function(msg) {

                    console.log(msg)
// change for new theme
								
                    if (msg == 100 && $("#kt_login_singin_form").parsley().isValid()) {
                        $("#kt_login_singin_form").remove();
                        $("#div-form").append(
                            '<form onsubmit="return code2();" id="form-register2" class="form-horizontal m-t-20" method="post" action="/login/pass/form">\n' +
                            '                @csrf\n' +
                            '\n' +
                            '<div class="pb-5 pb-lg-15">\n'+
                            '<h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">ورود</h3> \n'+
                            '<div class="text-muted font-weight-bold font-size-h4">حساب کاربری ندارید؟\n'+
                            '									<a href="custom/pages/login/login-3/signup.html" class="text-primary font-weight-bolder">ثبت نام</a></div>\n'+
                            '</div>\n'+
                            '                <div class="form-group">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <label for="pass" class="font-size-h6 font-weight-bolder text-dark">رمز عبور : </label>\n' +
                            '                        <input  class="form-control  h-auto py-7 px-6 rounded-lg border-0" name="pass" data-parsley-minlength="6" type="password" required=""  id="pass1" placeholder="رمز عبور">\n' +
                            '                    </div>\n' +
                            '\n' +
                            '\n' +
                            '                <div class="form-group text-center m-t-30">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit">\n' +
                            '                            ورود\n' +
                            '                        </button>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '<a href="/login/forgotPassword" class="text-primary m-l-5"><b>فراموشی رمز عبور</b></a>\n' +
                            '\n' +
                            '            </form>'
                        )

                    }
                    if (msg == 70 && $("#kt_login_singin_form").parsley().isValid()) {
                        $("#kt_login_singin_form").remove();
                        $("#div-form").append(
                            '<form onsubmit="return code3();" id="form-register2" class="form-horizontal m-t-20" method="post" action="/login/code/form">\n' +
                            '                @csrf\n' +
                            '\n' +
                            '                <div class="form-group">\n' +
                            '                    <div class="font-size-h6 font-weight-bolder text-dark">\n' +
                            '                        <label for="exampleInputEmail1">کد تایید : </label>\n' +
                            '                        <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.5em;padding: 10px 0;height: auto;" class="form-control" name="code" type="text" required=""  data-parsley-pattern="[0-9]{5}" placeholder=".....">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '\n' +
                            '\n' +
                            '                <div class="form-group text-center m-t-30">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <button class="btn btn-success font-size-h6 font-weight-bolder text-dark">\n' +
                            '                            ورود\n' +
                            '                        </button>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '\n' +
                            '\n' +
                            '            </form>'
                        )

                    }
                    if (msg == 50 && $("#kt_login_singin_form").parsley().isValid()) {
                        $("#kt_login_singin_form").remove();
                        $("#div-form").append(
                            '<form onsubmit="return code1();" id="form-register2" class="form-horizontal m-t-20" method="post" action="/login/code/pass/form">\n' +
                            '                @csrf\n' +
                            '\n' +
                            '                <div class="form-group">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <label for="exampleInputEmail1">کد تایید : </label>\n' +
                            '                        <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.5em;padding: 10px 0;height: auto;" class="form-control h-auto py-7 px-6 rounded-lg border-0" name="code" type="text" required=""  data-parsley-pattern="[0-9]{5}" placeholder=".....">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="form-group">\n' +
                            '                    <div class="font-size-h6 font-weight-bolder text-dark">\n' +
                            '                        <label for="exampleInputEmail1">رمز عبور : </label>\n' +
                            '                        <input  class="form-control h-auto py-7 px-6 rounded-lg border-0" name="pass" data-parsley-minlength="6" type="password" required=""  id="pass1" placeholder="رمز عبور">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '                <div class="form-group">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <label for="exampleInputEmail1">تکرار رمز عبور : </label>\n' +
                            '                        <input  class="form-control h-auto py-7 px-6 rounded-lg border-0" name="pass2" type="password" required="" data-parsley-equalto="#pass1"   placeholder="تکرار رمز عبور">\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '\n' +
                            '\n' +
                            '                <div class="form-group text-center m-t-30">\n' +
                            '                    <div class="col-xs-12">\n' +
                            '                        <button class="btn btn-success font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" type="submit">\n' +
                            '                            ورود\n' +
                            '                        </button>\n' +
                            '                    </div>\n' +
                            '                </div>\n' +
                            '\n' +
                            '\n' +
                            '            </form>'
                        )
                    }
                    if (msg == 0) {
                        error = error + 1;
                        // $("#alert_login").prepend('<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                        //     '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                        //     '                <span>شما هنوز ثبت نام نکرده اید لطفا <a href="/register" class="text-primary">ثبت نام</a> کنید</span>\n' +
                        //     '            </div>')
                        // setTimeout(function() {
                        //     $('#alert-1').remove();
                        // }, 5000);

                        $("#alert_login").prepend(
                            '<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                            '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                            '                <span>کاربری شما وجود ندارد</span>\n' +
                            '            </div>')
                        setTimeout(function() {
                            $('#alert-1').remove();
                        }, 5000);
                    }
                    $('form').parsley();

                }
            })


            return false;

        }
    </script>

</body>

</html>
