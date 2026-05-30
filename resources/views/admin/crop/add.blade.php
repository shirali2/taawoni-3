@extends("theme.default")
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

                        <h4 class="header-title d-inline-block ">ایجاد محصول</h4>
                        <a href="/admin/crop"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
@csrf                     @foreach($errors->all() as $ereor)
                            <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                        @endforeach
                    <div class="row">


                        <div class="col-12">

                            <div class=" mb-4">
                                <div class="col-8" style="margin: 0 auto;">
                                    <label for="inputimg" class=" m-t-0 mb-3">عکس محصول</label>
                                    <input name="img" id="inputimg"  type="file" class="dropify" data-default-file=""  />
                                </div>
                            </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">نام محصول</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="name" type="text" class="form-control"
                                       id="inputEmail31" placeholder="نام محصول را وارد کنید">
                            </div>
                        </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">توضیح محصول</label>
                            <div class="col-sm-8">
                                <textarea parsley-trigger="change" required name="text" type="text" class="form-control"
                                          id="inputEmail31" placeholder="توضیح محصول را وارد کنید"></textarea>
                            </div>
                        </div>

                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">قیمت (تومان)</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="price"  type="text" class="form-control"
                                       id="inputEmail31" placeholder="قیمت را وارد کنید">
                            </div>
                        </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 col-form-label">انتخاب مجموعه </label>
                                <div class="col-sm-8">
                                    <select id="inputEmail34" required name="grop" class="form-control select2">
                                        <option selected="" disabled="">انتخاب مجموعه</option>
                                        @foreach($grops as $grop)
                                            <option value="{{$grop["id"]}}">{{$grop["name"]}}</option>
                                        @endforeach
                                    </select>
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


        function number_format (number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            const n = !isFinite(+number) ? 0 : +number
            const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
            const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
            let s = ''

            const toFixedFix = function (n, prec) {
                if (('' + n).indexOf('e') === -1) {
                    return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
                } else {
                    const arr = ('' + n).split('e')
                    let sig = ''
                    if (+arr[1] + prec > 0) {
                        sig = '+'
                    }
                    return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
                }
            }


            s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }
        $('input[name="price"]').keyup(function(){
            number_keyup()
        })
        number_keyup()
        function number_keyup(){
            var val=  $('input[name="price"]').val();

            $('input[name="price"]').val(number_format(val))

        }
       // kamaDatepicker('test-date-id', customOptions);


        // function register() {
        //     var error = 0;
        //
        //     var username = $("input[name='grop']").val();
        //     var email = $("input[name='email']").val();
        //     var token = $("input[name='_token']").val();
        //
        //         $.ajax('/admin/addUser/check',
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
        //     $.ajax('/admin/addUser/checkEmail',
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
        $.ajax('/admin/menu/add/gropUser',
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
            $.ajax('/admin/menu/add/gropUser',
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
