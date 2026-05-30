@extends('theme.default')
@section('container')
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-9 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ایجاد مدیر جدید مجموعه {{ $grop['name'] }}</h4>
                        <a href="/admin/grop/manager/{{ $grop['id'] }}"
                            class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post" action="/admin/grop/manager/add/{{ $grop['id'] }}"
                        role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf

                        <div class="form-group row">
                            <label for="cmbUserList" class="col-sm-3 col-form-label">کاربران</label>
                            <div class="col-sm-8">
                                <select id="cmbUserList" required name="user" class="form-control select2_user_list">
                                    <option selected="" disabled="">انتخاب کاربر</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }} [{{ $user['id'] }}]
                                            [{{ $user['hash'] }}]</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-sm-3"></div>
                            <div class="col-sm-8">
                                <label id="selectedCategoryLabel" style="font-size:large; color:red;">مقدار انتخابی در اینجا نمایش داده می‌شود</label>
                            </div>
                        </div>
                        @php
                            $accessItems = $setting ? (json_decode($setting['input3'], true) ?: []) : [];
                        @endphp
                        <div class="row">
                            @foreach ($accessItems as $val)
                                @if ($val['id'] == 1)
                                    <h5 class="col-12">دسترسی آگهی ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 6.5)
                                    <h5 class="col-12">صندوق پیام ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 11.5)
                                    <h5 class="col-12">تیکت ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 21)
                                    <h5 class="col-12">تماس با ما :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 22)
                                    <h5 class="col-12">فرم ساز :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 29)
                                    <h5 class="col-12">منو و زیر منو ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 33)
                                    <h5 class="col-12">محصولات :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 36)
                                    <h5 class="col-12">کاربران :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 54)
                                    <h5 class="col-12">مجموعه ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 66)
                                    <h5 class="col-12">صورت حساب ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 79)
                                    <h5 class="col-12">یادداشت‌ها :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                @if ($val['id'] == 80)
                                    <h5 class="col-12">واریزی انبوه :</h5>
                                    <hr class="col-12 mt-0">
                                @endif
                                <div class=" mb-3  col-lg-6 col-xl-4">
                                    <label for="checkbox6c{{ $val['id'] }}">
                                        <input id="checkbox6c{{ $val['id'] }}" value="{{ $val['id'] }}"
                                            name="access[]" type="checkbox">

                                        {{ $val['name'] }}
                                    </label>
                                </div>
                            @endforeach
                            {{-- @foreach ($usergrops as $usergrop) --}}
                            {{-- <div class="checkbox mb-2 checkbox-inverse col-lg-6 col-xl-4"> --}}
                            {{-- <input id="checkbox6c2{{$usergrop["id"]}}" value="{{$usergrop["id"]}}" name="accessGropUser[]" type="checkbox"> --}}
                            {{-- <label for="checkbox6c2{{$usergrop["id"]}}"> --}}
                            {{-- {{$usergrop["name"]}} --}}
                            {{-- </label> --}}
                            {{-- </div> --}}
                            {{-- @endforeach --}}
                        </div>



                        <div class="col-12 mt-4">
                            <div class="text-center">
                                <button type="submit"
                                    class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
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
        var no_select3_in_main_layout=true;
        $(document).ready(function() {
            $(".select2_user_list").select2();
            // وقتی که یک آیتم انتخاب شد
            $('.select2_user_list').on('change', function() {
                var selectedValue = $(this).find('option:selected').text(); // مقدار انتخابی

                // تگ label رو پیدا کن و مقدار انتخابی رو بهش اضافه کن
                if (selectedValue) {
                    $('#selectedCategoryLabel').text('کاربر انتخابی: ' + selectedValue);
                    $('#selectedCategoryLabel').css("color", "green"); // تغییر رنگ دلخواه
                } else {
                    $('#selectedCategoryLabel').text('کاربر انتخابی در اینجا نمایش داده می‌شود');
                    $('#selectedCategoryLabel').css("color", "red"); // رنگ پیش‌فرض
                }
            });
        });

        $('#cmbUserList')
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
            placeholder: "روز / ماه / سال",
            twodigit: true,
            closeAfterSelect: true,
            nextButtonIcon: "fa fa-arrow-circle-right",
            previousButtonIcon: "fa fa-arrow-circle-left",
            buttonsColor: "blue",
            forceFarsiDigits: true,
            markToday: true,
            markHolidays: true,
            highlightSelectedDay: true,
            sync: true,
            gotoToday: true
        }
        kamaDatepicker('test-date-id', customOptions);


        // function register() {
        //     var error = 0;
        //
        //     var username = $("input[name='username']").val();
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


        $("select[name='type']").change(function() {
            var type = $(this).find("option:selected").val();
            $("#type").find("div").remove();
            if (type == 3) {
                $("#type").append(
                    '  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره کارت</label><div class="col-sm-8"><input class=" form-control" type="text" name="card" required="" placeholder="شماره کارت را وارد کنید" /></div></div>'
                    );
            }
            if (type == 2) {
                $("#type").append(
                    '  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">شماره حساب</label><div class="col-sm-8"> <input class=" form-control" type="text"  name="hsab" required="" placeholder="شماره حساب را وارد کنید" /></div></div>'
                    );

            }

        })
    </script>
@endsection
