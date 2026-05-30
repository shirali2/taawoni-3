@extends("theme.manager")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <link href="/assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-9 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">تنظیمات سیستم</h4>
                    </div>


                    <form class="form-horizontal form" method="post"  action="/manager/setting" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
                       @csrf

                        <div class="form-group row">
                            <label for="inputEmail34" class="col-sm-4 col-form-label">ورود کاربران با رمز یکبار مصرف</label>
                            <div class="col-sm-6">
                                <input type="checkbox" @if($register_pass["input1"]==0) checked @endif name="register_pass" data-plugin="switchery" data-color="#7266ba"/>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 col-form-label">کد مجموعه پیش فرض </label>
                            <div class="col-sm-8">
                                <input value="{{$code_grop['input1']}}" name="code_grop" type="text" class="form-control" placeholder="کد مجموعه پیش فرض را وارد کنید">
                            </div>
                        </div>
                        <br>
                                      <hr>

                        <div class="form-group row">
                            <label for="inputEmail34" class="col-sm-5 col-form-label">نمایش ندادن کد مجموعه در هنگام تبت نام</label>
                            <div class="col-sm-6">
                                <input type="checkbox" @if($code_grop_none["input1"]==1) checked @endif name="code_grop_none"  data-plugin="switchery" data-color="#7266ba"/>
                            </div>
                        </div>


                        <div id="code_grop_none_div"></div>
                        <br>
                        <hr>
                          <div class="row">
                        <div class="col-lg-6 mt-3">
                            <label for="inputEmail31">متن ثبت نام</label>

                            <input value="{{$name_register["input1"]}}" required name="name_register" type="text" class="form-control"
                                   id="inputEmail31" placeholder="متن ثبت نام را وارد کنید">

                        </div>
                        <div class="col-lg-6 mt-3">
                            <label for="inputEmail31">متن ورود</label>

                            <input value="{{$name_login["input1"]}}" required name="name_login" type="text" class="form-control"
                                   id="inputEmail31" placeholder="متن ورود را وارد کنید">

                        </div>
                          </div>


                        <br>
                        <hr>

                        <div class="row">
                            <div class="col-12 mt-3">
                                <label for="inputEmail31">.توضیحات</label>

                                <textarea required name="description" type="text" class="form-control"
                                          id="inputEmail31" placeholder="متن توضیحات را وارد کنید">{{$description["input3"]}}</textarea>

                            </div>

                        </div>
                        <br>
                        <hr>


                        <div class="row">
                            <div class="col-6 mb-4">

                                <label for="inputimg" class=" m-t-0 mb-3">عکس صفحه ورود</label>
                                <input name="img_login" id="inputimg"  type="file" class="dropify" data-default-file="/img_login_register/{{$img_login["input1"]}}"  />

                            </div>
                            <div class="col-6 mb-4">

                                <label for="inputimg" class=" m-t-0 mb-3">عکس  صفحه  ثبت ثام</label>
                                <input name="img_register" id="inputimg" type="file" class="dropify" data-default-file="/img_login_register/{{$img_register["input1"]}}"  />

                            </div>
                        </div>

                        <br>
                        <hr>

                        <div class="row">
                            <div class="col-lg-6 mt-3">
                                <label for="title_tag_input">عنوان تب مرورگر (Title Tag)</label>
                                <input value="{{ $title_tag['input1'] ?? '' }}" name="title_tag" id="title_tag_input" type="text" class="form-control" placeholder="عنوان سایت را وارد کنید">
                            </div>
                            <div class="col-6 mb-4 mt-3">
                                <label class="m-t-0 mb-3">لوگوی سایت</label>
                                <input name="site_logo" type="file" class="dropify"
                                    @if(!empty($site_logo['input1'])) data-default-file="/img_login_register/{{ $site_logo['input1'] }}" @endif />
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="col-12 mt-4">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                       ویرایش
                                    </button>
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

        if($("input[name='code_grop_none']").is(':checked')) {
            $("#code_grop_none_div").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">کد مجموعه نمایش ندادن </label><div class="col-sm-8"><input class=" form-control" type="text" name="code_grop_none_code" required="" value="{{$code_grop_none["input2"]}}" placeholder="کد مجموعه را وارد کنید" /></div></div>');
        }
        $("input[name='code_grop_none']").change(function() {
            $("#code_grop_none_div").find("div").remove();
            if($(this).is(':checked')) {

                $("#code_grop_none_div").append('  <div class="form-group row"><label class="col-sm-3 col-form-label" for="emailaddress">کد مجموعه نمایش ندادن </label><div class="col-sm-8"><input class=" form-control" type="text" name="code_grop_none_code" required="" value="{{$code_grop_none["input2"]}}" placeholder="کد مجموعه را وارد کنید" /></div></div>');
            }

        })



    </script>
    <script src="/assets/plugins/switchery/switchery.min.js"></script>

@endsection
