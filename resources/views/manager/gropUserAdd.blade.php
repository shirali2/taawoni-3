@extends("theme.manager")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-9 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ایجاد گروه کاربری جدید مجموعه {{$grop["name"]}}</h4>
                        <a href="/manager/grop/user/{{$grop["id"]}}"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post"  action="/manager/grop/user/add/{{$grop["id"]}}" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
                       @csrf


                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">نام گروه کاربری </label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="name" type="text" class="form-control"
                                       id="inputEmail3" placeholder="نام گروه کاربری را وارد کنید">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">توضیحات گروه کاربری </label>
                            <div class="col-sm-8">
                                <textarea required name="text" type="text" class="form-control"
                                          id="inputEmail3" placeholder="توضیحات گروه کاربری را وارد کنید"></textarea>
                            </div>
                        </div>



                        <div class="col-12 mt-4">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                       افزودن
                                    </button>
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


        $("select[name='type']").change(function() {
            var type=$(this).find("option:selected").val();
            $("#type").find("div").remove();
            if(type==3){
                $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره کارت</label><div class="col-sm-8"><input class=" form-control" type="text" name="card" required="" placeholder="شماره کارت را وارد کنید" /></div></div>');
            } if(type==2){
                $("#type").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره حساب</label><div class="col-sm-8"> <input class=" form-control" type="text"  name="hsab" required="" placeholder="شماره حساب را وارد کنید" /></div></div>');

            }

        })



    </script>


@endsection
