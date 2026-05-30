<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ \App\setting::where('name', 'site_title')->value('input1') ?? 'سامانه تعاونیهای مسکن بیمه ایران' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
            <link href="/assets/css/login-3.css" rel="stylesheet" type="text/css" />


        <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />

    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css"/>

    <script src="/assets/js/modernizr.min.js"></script>

</head>

<body>
<div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

</div>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">


    </div>
    <div class="m-t-30  login-content">

        <div class="text-center">
            <h4 class="text-uppercase font-bold mb-0">ورود مدیریت</h4>
        </div>
        <div class="p-20 " id="div-form">
            <form  id="form-register" class="form-horizontal m-t-20" method="post" action="/admin">
                @csrf
                <div class="form-group">
                    <div class="col-xs-12">
                        <label class="font-size-h6 font-weight-bolder text-dark" for="exampleInputEmail1">شماره موبایل : </label>
                        <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.3em;padding: 10px 0;height: auto;" class="form-control h-auto py-7 px-6 rounded-lg border-0" name="mobile" type="text" required=""  data-parsley-pattern="09[0-9]{9}" placeholder=".........09">
                    </div>
                </div>


                               <div class="form-group">
                                     <div class="col-xs-12">
                                            <label class="font-size-h6 font-weight-bolder text-dark" for="exampleInputEmail1">رمز عبور : </label>
                                         <input  class="form-control h-auto py-7 px-6 rounded-lg border-0" name="pass" data-parsley-minlength="6" type="password" required=""  id="pass1" placeholder="رمز عبور">
                                           </div>
                                   </div>


                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <button class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" type="submit">
                            ورود
                        </button>
                    </div>
                </div>
                {{--<div class="form-group m-t-30 mb-0">--}}
                    {{--<div class="col-sm-12">--}}
                        {{--<a href="/register" class="text-muted"><i class="fa fa-lock m-r-5"></i> رمز عبور خود را فراموش کرده اید ؟ </a>--}}
                    {{--</div>--}}
                {{--</div>--}}

            </form>

        </div>
    </div>
    <!-- end card-box-->
    <div class="row">
        <div class="col-sm-12 text-center">
            @if ($hide_register_link == null || $hide_register_link->input1 == 0)
            <p class="text-muted">حساب کاربری ندارید ؟ <a href="/register" class="text-primary m-l-5"><b>ثبت نام</b></a></p>
            @endif
        </div>
    </div><!-- end wrapper page -->

</div>
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
    $('form').parsley();



</script>

</body>
</html>
