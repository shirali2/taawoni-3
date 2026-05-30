@extends("theme.user")
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

                        <h4 class="header-title d-inline-block "> تنظیمات </h4>
                         </div>



                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a href="#home1" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                    اطلاعات کاربر
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#profile1" data-toggle="tab" aria-expanded="true" class="nav-link ">
                                    تغییر رمز
                                </a>
                            </li>

                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="home1">
                                {{--<form   id="form-register" class="form-horizontal m-t-20" method="post" action="">--}}
                                    {{--@csrf--}}

                                            <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="exampleInputEmail1">شماره موبایل : </label>
                                            <input style="text-align: center;font-weight: bold;letter-spacing: 10px;text-shadow: 1px 1px 1px #999;font-size: 1.5em;padding: 10px 0;height: auto;" class="form-control" type="text" value="{{$user["mobile"]}}" disabled >
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">نام : </label>
                                            <input  class="form-control" disabled  type="text" required="" value="{{$user["name"]}}"  placeholder="نام ">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1"> نام خانوادگی : </label>
                                            <input  class="form-control" disabled type="text" required="" value="{{$user["name2"]}}"   placeholder="نام خانوادگی">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">تلفن : </label>
                                            <input  class="form-control" disabled type="text"  value="{{$user["phone"]}}" data-parsley-pattern="0[0-9]{10}" placeholder="تلفن">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">کدملی : </label>
                                            <input  class="form-control" disabled type="text" required="" value="{{$user["kod"]}}" data-parsley-pattern="[0-9]{10}"  placeholder="کد ملی">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">استان : </label>
                                            <input  class="form-control" disabled type="text"   value="{{$user["state"]}}" placeholder="استان  ">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">شهر : </label>
                                            <input  class="form-control" disabled type="text"   value="{{$user["city"]}}" placeholder="شهر ">
                                        </div>


                                            </div>

                                    {{--<div class="form-group text-center m-t-30">--}}
                                        {{--<div class="col-xs-12">--}}
                                            {{--<button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">--}}
                                                {{--ویرایش کاربر--}}
                                            {{--</button>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}


                                {{--</form>--}}
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="profile1">
                                <form   id="form-register" class="form-horizontal m-t-20" method="post" action="/user/setting/password">
                                    @csrf

                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <label for="exampleInputEmail1">رمز جدید : </label>
                                            <input id="hori-pass1" name="pass" class="form-control" placeholder="رمز جدید" type="password"  required="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <label for="exampleInputEmail1">تکرار رمز جدید : </label>
                                            <input  class="form-control" placeholder="تکرار رمز جدید" data-parsley-equalto="#hori-pass1" name="pass2" type="password" required=""  >
                                        </div>
                                    </div>




                                    <div class="form-group text-center m-t-30">
                                        <div class="col-xs-12">
                                            <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                                                ویرایش رمز عبور
                                            </button>
                                        </div>
                                    </div>


                                </form>
                            </div>


                        </div>




                </div>
            </div>
        </div>


    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>

        $('form').parsley();







    </script>


@endsection
