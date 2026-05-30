@extends("theme.manager")
@section("container")

<style type="text/css">
  @media screen and (max-width: 767px){
    .clear-both{
      clear: both
    }
    .button-back{
      margin-top: 5px
    }
    .button-form-example{
      margin-bottom: 5px
    }
  }
</style>

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
                    <!--  Modal content for the above example -->
                    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title mt-0" id="myLargeModalLabel">{{$form["name"]}}</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        @foreach($form_fild as $fild)
                                            @if($fild["type"]==1 or $fild["type"]==2 or $fild["type"]==3)
                                                <div class="col-lg-6 mt-3 position-relative">
                                                    <label for="inputEmail31">{{$fild["title"]}} ({{$fild["name"]}})</label>

                                                    <input @if($fild["checkbox"]) required  @endif name="{{$fild["name"]}}"
                                                           @if($fild["type"]==1) type="text" @endif @if($fild["type"]==2) type="email" @endif @if($fild["type"]==3) type="number" @endif
                                                           class="form-control" id="inputEmail31" placeholder="{{$fild["title"]}}  را وارد کنید">

                                                </div>
                                            @endif
                                            @if($fild["type"]==5)
                                                <div class="col-lg-6 mt-3 position-relative height-77px">
                                                    <div class="checkbox checkbox_input ">
                                                        <input @if($fild["checkbox"]) required  @endif name="{{$fild["name"]}}" id="checkbox{{$fild["name"]}}" type="checkbox">
                                                        <label for="checkbox{{$fild["name"]}}">
                                                            {{$fild["title"]}} ({{$fild["name"]}})
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($fild["type"]==4)
                                                <div class="col-lg-6 mt-3 position-relative " >
                                                    <label for="inputEmail3">{{$fild["title"]}} ({{$fild["name"]}})</label>
                                                    <select id="inputEmail34" @if($fild["checkbox"]) required  @endif name="{{$fild["name"]}}" class="form-control select2">
                                                        <option selected="" disabled="" value="0">انتخاب کنید</option>
                                                        @foreach($fild["itme"] as $itme)
                                                            <option value="{{$itme["title"]}}">{{$itme["title"]}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            @if($fild["type"]==6)
                                                <div class="col-lg-6 mt-3 position-relative ">
                                                    <label class="d-block" for="inputEmail3">{{$fild["title"]}} ({{$fild["name"]}})</label>
                                                    @foreach($fild["itme"] as $number=>$itme)
                                                        <div class="checkbox d-inline">
                                                            <div class="radio radio-info form-check-inline">
                                                                <input type="radio" id="inlineRadio_{{$fild["name"]}}_{{$number}}" value="{{$itme["title"]}}" name="{{$fild["name"]}}" @if($fild["checkbox"]) required  @endif >
                                                                <label for="inlineRadio_{{$fild["name"]}}_{{$number}}"> {{$itme["title"]}} </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                    <div class="float-right w-100 m-t-0 m-b-30">
                      <h4 class="header-title d-md-inline-block">ایجاد اطلاعات فرم با اکسل</h4>
                      <a href="/manager/form/user/{{$form["id"]}}" class="button-back btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                      <button class="button-form-example btn btn-primary waves-effect waves-light float-right clear-both ml-md-3" data-toggle="modal" data-target=".bs-example-modal-lg" data-toggle="tooltip" data-placement="top" data-original-title="نمایش">نمونه فرم</button>
                      <a href="/manager/form/user/add/xlsx/download-example/{{$form['id']}}" class="btn btn-info float-right clear-both ml-md-3">دانلود اکسل نمونه</a>
                    </div>

                    <form onsubmit="return register();" id="form-register" class="form-horizontal m-t-20" method="post" action="/manager/user/add">
                        @csrf


                        <div class="form-group">
                            <div class="col-xs-12">
                                <label for="exampleInputEmail1">فایل اکسل : </label>
                                <input type="file" id="file_upload" accept=".xlsx" name="xlsx" required="" class="form-control">

                            </div>
                        </div>

                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
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

        $('form').parsley();



        function register() {


            var files = document.getElementById('file_upload').files;
            if(files.length==0){
                alert("Please choose any file...");
                return;
            }
            var filename = files[0].name;
            var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
            if (extension == '.XLS' || extension == '.XLSX') {
               excelFileToJSON(files[0]);
            }else{
                alert("Please select a valid excel file.");
            }
            var error = 0;


               return false;


        }



        //Method to read excel file and convert it into JSON
        function excelFileToJSON(file){

                var reader = new FileReader();
                reader.readAsBinaryString(file);
                reader.onload = function(e) {

                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type : 'binary'
                    });
                    var result = {};
                    workbook.SheetNames.forEach(function(sheetName) {
                        var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                        if (roa.length > 0) {
                            result[sheetName] = roa;
                        }
                    });

                    ajak( JSON.stringify(result, null, 3));
                }

        }
             function ajak(file) {


                 var token = "{{ csrf_token() }}";
                 var id = {{$form['id']}};


            if ($("#form-register").parsley().isValid()) {

                 $.ajax('/manager/form/user/add/xlsx/'+id,
                     {
                         type: 'post',
                         async: false
                         , data: {_token: token,file: file}
                         , success: function (msg) {

                             if (msg == 100) {

                                 window.location.replace("/manager/form/user/"+id);
                             }

                         }
                     })
            }
             }


    </script>
@endsection