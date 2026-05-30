@extends('theme.default')
@section('container')
    <div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

    </div>
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">ایجاد کاربران با اکسل</h4>
                        <a href="/admin/user"
                            class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>
                    <div class="alert alert-info">
                        <strong>ستون‌های مورد نیاز در فایل اکسل (نام شیت: user):</strong><br>
                        <code>mobile</code> (موبایل - الزامی) &nbsp;|&nbsp;
                        <code>name</code> (نام) &nbsp;|&nbsp;
                        <code>family</code> (نام خانوادگی) &nbsp;|&nbsp;
                        <code>phone</code> (تلفن) &nbsp;|&nbsp;
                        <code>national_code</code> (کد ملی) &nbsp;|&nbsp;
                        <code>special_code</code> (کد اختصاصی) &nbsp;|&nbsp;
                        <code>lawyer_name</code> (نام وکیل) &nbsp;|&nbsp;
                        <code>state</code> (استان) &nbsp;|&nbsp;
                        <code>city</code> (شهر)
                    </div>


                    <form onsubmit="return register();" id="form-register" class="form-horizontal m-t-20" method="post"
                        action="/admin/user/add">
                        @csrf


                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">فایل اکسل : </label>
                                <input type="file" id="file_upload" accept=".xlsx" name="xlsx" required=""
                                    class="form-control">

                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="gropId">انتخاب مجموعه :</label>
                                <select id="gropId" required name="grop" class="form-control select2">
                                    <option selected="" disabled="">انتخاب مجموعه</option>
                                    @foreach ($grops as $grop)
                                        <option value="{{ $grop['id'] }}">{{ $grop['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="userGroupId">انتخاب گروه کاربری :</label>
                                <select id="userGroupId" required name="user_grop_id" class="form-control select2">
                                    <option selected="" disabled="">انتخاب گروه کاربری</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light"
                                    type="submit">
                                    ثبت کاربر
                                </button>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>


    </div>

    <script src="{{ asset('libs/xlsx/xlsx.min.js') }}"></script>
    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>
        var userGrops = [
            @foreach ($usergrops as $item)
                {
                    "id": {{ $item->id }},
                    "grop_id": {{ $item->id_grop }},
                    "name": "{{ $item->text }}"
                },
            @endforeach
        ];

        $("#gropId").change(function() {
            $("#userGroupId").empty();
            userGrops.filter(x => x.grop_id == parseInt(this.value)).forEach(function(item) {
                $("#userGroupId").append(
                    $('<option></option>').val(item.id).html(item.name)
                );
            });
        });
        // $('form').parsley();



        function register() {


            var files = document.getElementById('file_upload').files;
            if (files.length == 0) {
                alert("Please choose any file...");
                return;
            }
            var filename = files[0].name;
            var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
            if (extension == '.XLS' || extension == '.XLSX') {
                excelFileToJSON(files[0]);
            } else {
                alert("Please select a valid excel file.");
            }
            var error = 0;


            return false;


        }



        //Method to read excel file and convert it into JSON
        function excelFileToJSON(file) {

            var reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = function(e) {

                var data = e.target.result;
                var workbook = XLSX.read(data, {
                    type: 'binary'
                });
                var result = {};
                workbook.SheetNames.forEach(function(sheetName) {
                    var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                    if (roa.length > 0) {
                        result[sheetName] = roa;
                    }
                });

                ajak(JSON.stringify(result, null, 3));
            }

        }

        function ajak(file) {


            var token = "{{ csrf_token() }}";
            var grop = $('select[name="grop"]').find("option:selected").val();
            var user_grop_id = $('select[name="user_grop_id"]').find("option:selected").val();

            if ($("#form-register").parsley().isValid()) {

                $.ajax('/admin/user/add/xlsx', {
                    type: 'post',
                    async: false,
                    data: {
                        _token: token,
                        grop: grop,
                        user_grop_id:user_grop_id,
                        file: file,
                    },
                    success: function(msg) {

                        if (msg == 100) {

                            window.location.replace("/admin/user");
                        }

                    }
                })
            }
        }
    </script>
@endsection
