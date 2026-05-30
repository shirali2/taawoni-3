@extends("theme.manager")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ویرایش مجموعه</h4>
                        <a href="/manager/grop"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post" onsubmit="return register();" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
@csrf                     @foreach($errors->all() as $ereor)
                            <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                        @endforeach
                    <div class="row">
                       <div class="col-6 mb-4">
                            <div class="col-md-8" style="margin: 0 auto;">
                                <label for="inputimg" class=" m-t-0 mb-3">آرم</label>
                                    <input name="img" id="inputimg" type="file" class="dropify" data-default-file="/grop_img/{{$grop["img1"]}}"  />
                             </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="col-md-8" style="margin: 0 auto;">
                                <label for="inputimg" class=" m-t-0 mb-3">طرح صفحه داشبورد کاربر</label>
                                    <input name="img2" id="inputimg" type="file" class="dropify" data-default-file="/grop_img/{{$grop["img2"]}}"  />
                             </div>
                        </div>

                        <div class="col-lg-6">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">نام مجموعه </label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="name" value="{{$grop["name"]}}" type="text" class="form-control"
                                       id="inputEmail3" placeholder="نام  مجموعه را وارد کنید">
                            </div>
                        </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">تاریخ اعتبار مجموعه</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="am" value="{{$grop["am"]}}" id="test-date-id" type="text" class="form-control"
                                       id="inputEmail31" placeholder="تاریخ اعتبار مجموعه را وارد کنید">
                            </div>
                        </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">عناوین بالا</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="top" type="text" value="{{$grop["top"]}}"  class="form-control"
                                       id="inputEmail31" placeholder="عناوین بالا را وارد کنید">
                            </div>
                        </div> <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">عناوین پایین</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="bottom" value="{{$grop["bottom"]}}" type="text" class="form-control"
                                       id="inputEmail31" placeholder="عناوین پایین را وارد کنید">
                            </div>
                        </div>










                        </div>  <div class="col-lg-6">


                            <div class="form-group row">
                                <label for="inputEmail34" class="col-sm-3 col-form-label">نحوه پرداخت</label>
                                <div class="col-sm-8">
                                    <select id="inputEmail34" required name="type" class="form-control select2">
                                        <option selected="" disabled="">نحوه پرداخت</option>
                                        <option value="1" @if($grop["type"]==1) selected @endif >الکترنیکی</option>
                                        <option value="2" @if($grop["type"]==2) selected @endif >شماره حساب</option>
                                        <option value="3" @if($grop["type"]==3) selected @endif>کارت</option>

                                    </select>
                                </div>
                            </div>

                            <div id="type">


                            </div>
                            <div class="checkbox">
                                <input id="checkbox0" @if($grop["checkboxName"]==0) checked @endif name="checkboxName" type="checkbox">
                                <label for="checkbox0">
                                    نمایش ندادن نام مجموعه
                                </label>
                            </div>

                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="offset-sm-4 text-right col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                       ویرایش
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    </form>

                </div>
            </div>
        </div>


    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>

        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });
        var customOptions = {
            placeholder: "روز / ماه / سال"
            , twodigit: true
            , closeAfterSelect: true
            , nextButtonIcon: "fa fa-arrow-circle-right"
            , previousButtonIcon: "fa fa-arrow-circle-left"
            , buttonsColor: "blue"
            , forceFarsiDigits: true
            , markToday: true
            , markHolidays: true
            , highlightSelectedDay: true
            , sync: true
            , gotoToday: true
        }
        kamaDatepicker('test-date-id', customOptions);


        // function register() {
        //     var error = 0;
        //
        //     var username = $("input[name='username']").val();
        //     var email = $("input[name='email']").val();
        //     var token = $("input[name='_token']").val();
        //
        //         $.ajax('/manager/addUser/check',
        //             {
        //                 type: 'post',
        //                 async: false
        //                 , data: {_token: token,username: username}
        //                 , success: function (data1) {
        //                     if (data1 == 0) {
        //                         error = error + 1;
        //                         $("input[name='username']").addClass('red rtl').attr("placeholder", "این نام کاربری قبلا ثبت شده است").val('');
        //                     } else {
        //                     }
        //                 }
        //             })
        //
        //     $.ajax('/manager/addUser/checkEmail',
        //             {
        //                 type: 'post',
        //                 async: false
        //                 , data: {_token: token,email: email}
        //                 , success: function (data1) {
        //                     if (data1 == 0) {
        //                         error = error + 1;
        //                         $("input[name='email']").addClass('red rtl').attr("placeholder", "این ایمیل قبلا ثبت شده است").val('');
        //                     } else {
        //                     }
        //                 }
        //             })
        //
        //
        //     if (error > 0) {
        //         return false;
        //     }
        // }

        var type=$("select[name='type']").find("option:selected").val();
        $("#type").find("div").remove();
        var typeNumber='{{$grop["typeNumber"]}}';
        if(type==3){
            $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره کارت</label><div class="col-sm-8"><input class=" form-control" type="text" name="card" value="'+typeNumber+'" required="" placeholder="شماره کارت را وارد کنید" /></div></div>');
        } if(type==2){
            $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره حساب</label><div class="col-sm-8"> <input class=" form-control" type="text"  name="hsab" required="" value="'+typeNumber+'" placeholder="شماره حساب را وارد کنید" /></div></div>');

        }
        $("select[name='type']").change(function() {
            var type=$(this).find("option:selected").val();
            var typeNumber='{{$grop["typeNumber"]}}';
            $("#type").find("div").remove();
            if(type==3){
                $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره کارت</label><div class="col-sm-8"><input class=" form-control" type="text" name="card"  required="" placeholder="شماره کارت را وارد کنید" /></div></div>');
            } if(type==2){
                $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره حساب</label><div class="col-sm-8"> <input class=" form-control" type="text"  name="hsab"  required="" placeholder="شماره حساب را وارد کنید" /></div></div>');

            }

        })



    </script>


@endsection
