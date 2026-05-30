<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>سامانه </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css"/>

    <script src="/assets/js/modernizr.min.js"></script>

</head>

<body>
<style>
    #parsley-id-multiple-grop{
        position: absolute;
        bottom: -25px;
        right: 0px;
    }
</style>
<div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

</div>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">
        <a href="index.html" class="logo"><span>خوش آمدید</span></a>

    </div>
    <div class="m-t-30 card-box">

        <div class="text-center">
            <h4 class="text-uppercase font-bold mb-0">به کدام پنل وارد می شوید</h4>
        </div>
        <div class="p-20" id="div-form">
            <form method="post" action="/user/grop">
                @csrf
            <ul class="list-unstyled task-list" id="drag-inprogress" style="position: relative;">
                @foreach($user_grops as $user_grop)
                <li style="height: 64px;" >
                    <div class="card-box kanban-box h-100">
                        <div class="checkbox-wrapper">
                            <div class="radio radio-success">
                                <input type="radio" name="grop" required="" id="radio{{$user_grop["id"]}}" value="{{$user_grop["id"]}}">
                                <label for="radio{{$user_grop["id"]}}">{{$user_grop["grop"]["name"]}}</label>
                            </div>
                        </div>


                    </div>
                </li>
@endforeach




            </ul>
                    <div class="text-center mt-4">
                        <button class="btn btn-success waves-effect w-md waves-light m-b-5" type="submit">ورود</button>

                    </div>
            </form>
        </div>
    </div>


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
