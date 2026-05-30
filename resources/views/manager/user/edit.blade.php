@extends("theme.manager")
@section("container")
    <div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

    </div>
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ویرایش کاربر</h4>
                        <a href="/manager/user"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>



                    <form @if(\App\Http\Controllers\managerController::managerAccessLevel(41)) onsubmit="return register();" @endif  id="form-register" class="form-horizontal m-t-20" method="post" action="/manager/user/edit/{{$user["id"]}}">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(\App\Http\Controllers\managerController::managerAccessLevel(41))
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">شماره موبایل : </label>
                                    <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.5em;padding: 10px 0;height: auto;" class="form-control" name="mobile" type="text" value="{{$user["mobile"]}}"  >
                                </div>
                            </div>
                             @else
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">شماره موبایل : </label>
                                    <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.5em;padding: 10px 0;height: auto;" disabled class="form-control"  type="text" value="{{$user["mobile"]}}"  >
                                </div>
                            </div>
                            @endif
                        @if(\App\Http\Controllers\managerController::managerAccessLevel(42))
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">نام : </label>
                                <input  class="form-control" name="name" type="text" required="" value="{{$user["name"]}}"  placeholder="نام ">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1"> نام خانوادگی : </label>
                                <input  class="form-control" name="name2" type="text" required="" value="{{$user["name2"]}}"   placeholder="نام خانوادگی">
                            </div>
                        </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">کداختصاصی : </label>
                                    <input  class="form-control" name="hash" type="text" value="{{$user["hash"]}}"  placeholder="کداختصاصی">
                                </div>
                            </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">تلفن : </label>
                                <input  class="form-control" name="phone" type="text"  value="{{$user["phone"]}}" data-parsley-pattern="0[0-9]{10}" placeholder="تلفن">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">کدملی : </label>
                                <input  class="form-control" name="kod" type="text" required="" value="{{$user["kod"]}}" data-parsley-pattern="[0-9]{10}"  placeholder="کد ملی">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="lawyer_name">نام وکیل : </label>
                                <input class="form-control" name="lawyer_name" type="text" value="{{$user['lawyer_name']}}" placeholder="نام و نام خانوادگی وکیل">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">استان : </label>
                                <input  class="form-control" name="state" type="text"   value="{{$user["state"]}}" placeholder="استان محل اقامت">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">شهر : </label>
                                <input  class="form-control" name="city" type="text"   value="{{$user["city"]}}" placeholder="شهر محل اقامت">
                            </div>
                        </div>

                            @else
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">نام : </label>
                                    <input disabled class="form-control" type="text" required="" value="{{$user["name"]}}"  placeholder="نام ">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1"> نام خانوادگی : </label>
                                    <input disabled  class="form-control" type="text" required="" value="{{$user["name2"]}}"   placeholder="نام خانوادگی">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">کداختصاصی : </label>
                                    <input disabled class="form-control" name="hash" type="text" value="{{$user["hash"]}}"  placeholder="کداختصاصی">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">تلفن : </label>
                                    <input disabled class="form-control"  type="text"  value="{{$user["phone"]}}" data-parsley-pattern="0[0-9]{10}" placeholder="تلفن">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">کدملی : </label>
                                    <input disabled class="form-control" type="text" required="" value="{{$user["kod"]}}" data-parsley-pattern="[0-9]{10}"  placeholder="کد ملی">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">استان : </label>
                                    <input disabled class="form-control"  type="text"   value="{{$user["state"]}}" placeholder="استان محل اقامت">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">شهر : </label>
                                    <input disabled class="form-control"  type="text"   value="{{$user["city"]}}" placeholder="شهر محل اقامت">
                                </div>
                            </div>

                        @endif
                        @if(\App\Http\Controllers\managerController::managerAccessLevel(43))
                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">تاریخ اشتراک : </label>
                                <input  class="form-control" name="active_am" type="text" data-parsley-pattern="140[0-9]{1}-[0,1]{1}[0-9]{1}-[0,1,2,3]{1}[0-9]{1}" required=""  value="{{$user["active_am"]}}" placeholder="تاریخ اشتراک">
                            </div>
                        </div>
                        @else
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <label for="exampleInputEmail1">تاریخ اشتراک : </label>
                                    <input  class="form-control" disabled type="text" data-parsley-pattern="140[0-9]{1}-[0,1]{1}[0-9]{1}-[0,1,2,3]{1}[0-9]{1}" required=""  value="{{$user["active_am"]}}" placeholder="تاریخ اشتراک">
                                </div>
                            </div>
                            @endif



                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                                    ویرایش کاربر
                                </button>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>

        @if(\App\Http\Controllers\managerController::managerAccessLevel(45))
        <div class="row justify-content-center mt-3">
            <div class="col-12 col-lg-8 mb-3">
                <div class="card-box">
                    <h5 class="header-title mb-3">تغییر رمز عبور</h5>
                    <div id="password-change-result"></div>
                    <div class="form-group">
                        <label>رمز عبور جدید</label>
                        <input type="password" id="new_password" class="form-control" placeholder="حداقل ۸ کاراکتر">
                    </div>
                    <div class="form-group">
                        <label>تکرار رمز عبور</label>
                        <input type="password" id="new_password_confirmation" class="form-control" placeholder="تکرار رمز عبور">
                    </div>
                    <button type="button" id="btn-change-password" class="btn btn-warning waves-effect waves-light">
                        تغییر رمز عبور
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>

        $('form').parsley();


        function register() {
            var error = 0;
            var mobile = $('input[name="mobile"]').val();
            var id={{$user["id"]}};

            var token = $('input[name="_token"]').val();
            $.ajax('/manager/user/check/registerEdit',
                {
                    type: 'post',
                    async: false
                    , data: {_token: token, mobile: mobile,id:id}
                    , success: function (msg) {

                        if (msg == 100) {
                            error = error + 1;
                            $("#alert_login").prepend('<div id="alert-1" class="alert alert-danger alert-dismissable">\n' +
                                '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                                '                <span>این شماره موبایل قبلا ثبت شده .</span>\n' +
                                '            </div>')
                            setTimeout(function() {
                                $('#alert-1').remove();
                            }, 5000);

                        }

                    }
                })




            if (error!=0) {

                return false;
            }

        }






    $('#btn-change-password').on('click', function() {
        var pass = $('#new_password').val();
        var passConf = $('#new_password_confirmation').val();
        var token = $('input[name="_token"]').val();
        var userId = {{$user["id"]}};

        if (pass.length < 8) {
            $('#password-change-result').html('<div class="alert alert-danger">رمز عبور باید حداقل ۸ کاراکتر باشد.</div>');
            return;
        }
        if (pass !== passConf) {
            $('#password-change-result').html('<div class="alert alert-danger">رمز عبور و تکرار آن یکسان نیستند.</div>');
            return;
        }

        $.ajax('/manager/user/changePassword/' + userId, {
            type: 'post',
            data: { _token: token, password: pass, password_confirmation: passConf },
            success: function(res) {
                $('#password-change-result').html('<div class="alert alert-success">' + (res.success || 'رمز عبور تغییر یافت.') + '</div>');
                $('#new_password').val('');
                $('#new_password_confirmation').val('');
            },
            error: function(xhr) {
                var msg = 'خطا در تغییر رمز عبور.';
                if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.password) msg = xhr.responseJSON.errors.password[0];
                $('#password-change-result').html('<div class="alert alert-danger">' + msg + '</div>');
            }
        });
    });
    </script>


@endsection
