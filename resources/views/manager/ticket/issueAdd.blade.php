@extends("theme.manager")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ایجاد موضوع</h4>
                        <a href="/manager/issue"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
@csrf                     @foreach($errors->all() as $ereor)
                            <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                        @endforeach
                    <div class="row">


                        <div class="col-12">


                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">نام موضوع</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="name" type="text" class="form-control"
                                       id="inputEmail31" placeholder="نام موضوع را وارد کنید">
                            </div>
                        </div>












                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="offset-sm-4 text-right col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                       افزودن
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


       // kamaDatepicker('test-date-id', customOptions);


        // function register() {
        //     var error = 0;
        //
        //     var username = $("input[name='grop']").val();
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

        var grop=$("select[name='grop']").find("option:selected").val();
        $("#type").find("div").remove();
        var token = $("input[name='_token']").val();
        $.ajax('/manager/menu/add/gropUser',
            {
                type: 'post',
                async: false
                , data: {_token: token,grop: grop}
                , success: function (data1) {
                    var data=jQuery.parseJSON( data1 );
                    $("select[name='gropUser']").find("option").remove();
                    $("select[name='gropUser']").append('<option selected="" disabled="">انتخاب گروه کاربری مجموعه</option>')

                    $.each(data, function( index, value ) {
                        $("select[name='gropUser']").append('<option value="'+value["id"]+'">'+value["name"]+'</option>')

                    });
                }
            })
        $("select[name='grop']").change(function() {
            var grop=$(this).find("option:selected").val();
            $("#type").find("div").remove();
            var token = $("input[name='_token']").val();
            $.ajax('/manager/menu/add/gropUser',
                        {
                            type: 'post',
                            async: false
                            , data: {_token: token,grop: grop}
                            , success: function (data1) {
                                var data=jQuery.parseJSON( data1 );
                                $("select[name='gropUser']").find("option").remove();
                                $("select[name='gropUser']").append('<option selected="" disabled="">انتخاب گروه کاربری مجموعه</option>')

                                $.each(data, function( index, value ) {
                                    $("select[name='gropUser']").append('<option value="'+value["id"]+'">'+value["name"]+'</option>')

                                });
                            }
                        })

        })



    </script>


@endsection
