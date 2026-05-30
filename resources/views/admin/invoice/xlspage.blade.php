@extends("theme.default")
@section("container")
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #71b6f9;
        border: 1px solid transparent;
        color: #ffffff;
        border-radius: 3px;
        padding: 0 7px
    }
</style>

<link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<script src="/ckeditor/ckeditor.js" type="text/javascript"></script>

<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card-box ">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block ">افزودن صورتحساب جدید</h4>
                    <form onsubmit="register()">
                        @csrf
                        <div class="mb-3 mt-5">
                            <label for="inputfile" class="form-label">فایل اکسل</label>
                            <input id="inputfile" type="file" accept=".xls,.xlsx" class="form-control">
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">انتخاب مجموعه </label>
                            <div class="col-sm-8">
                                <select required name="grop" class="form-control select2">
                                    <option selected="" disabled="">انتخاب مجموعه</option>
                                    @foreach($grops as $grop)
                                    <option value="{{$grop["id"]}}">{{$grop["name"]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">انتخاب گروه کاربری مجموعه </label>
                            <div class="col-sm-8">
                                <select name="gropUser[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="انتخاب گروه کاربری مجموعه" tabindex="-1" aria-hidden="true"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">انتخاب کاربر </label>
                            <div class="col-sm-8">
                                <select name="user[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="انتخاب کاربر" tabindex="-1" aria-hidden="true"></select>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">ارسال</button>
                        </div>
                    </form>
                    <script src="{{ asset('libs/xlsx/xlsx.min.js') }}"></script>
    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
                    <script>
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
var grop = $('select[name="gropUser[]"]').find("option:selected").val();
var user = $('select[name="user[]"]').find("option:selected").val();

if ($("#form-register").parsley().isValid()) {

    $.ajax('/admin/user/add/xlsx', {
        type: 'post',
        async: false,
        data: {
            _token: token,
            grop: grop,
            user:user,
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

    $("select[name='gropUser[]']").change(function () {
      var gropUser = $(this).val();
      if(gropUser == ''){
        $('select[name="user[]"]').empty();
        return;
      }

      var token = $("input[name='_token']").val();
      $.ajax('/admin/pm/add/user', {
        type: 'post',
        async: false,
        data: {
          _token: token,
          gropUser: gropUser
        },
        success: function (data1) {
          $("select[name='user[]']").find("option").remove();
          var data=jQuery.parseJSON( data1 );
          $("select[name='user[]']").append('<option disabled="">انتخاب کاربر </option>')

          $.each(data, function(index, value){
            if (value["hash"]==null){value["hash"]="";}
            $("select[name='user[]']").append('<option value="' + value['id'] + '">' + value['info_user'] + '</option>')
          });
        }
      })
    })

    $("select[name='grop']").change(function() {
      var grop=$(this).find("option:selected").val();
      $("#type").find("div").remove();
      var token = $("input[name='_token']").val();
      $.ajax('/admin/menu/add/gropUser', {
        type: 'post',
        async: false,
        data: {_token: token,grop: grop},
        success: function (data1) {
            var data=jQuery.parseJSON( data1 );
            $("select[name='gropUser[]']").empty();

            $.each(data, function(index, value){
              $("select[name='gropUser[]']").append('<option value="'+value["id"]+'">'+value["name"]+'</option>')
            })
        }
      })
    })


    /* START بررسی فیلدهای جریمه */
    function check_penalty_fields(){
      var daily_fine_amount = jQuery('#daily-fine-amount'), //مبلغ جریمه روزانه
            value_daily_fine_amount = daily_fine_amount.val(),
          fixed_penalty_amount = jQuery('#fixed-penalty-amount'), //مبلغ جریمه ثابت
            value_fixed_penalty_amount = fixed_penalty_amount.val();

      fixed_penalty_amount.val(number_format(value_fixed_penalty_amount));

      placeholder_fixed_penalty_amount = (typeof placeholder_fixed_penalty_amount === 'undefined') ? fixed_penalty_amount.attr('placeholder') : placeholder_fixed_penalty_amount;

      //مبلغ جریمه روزانه
      if(value_daily_fine_amount > 0){
        fixed_penalty_amount.attr('disabled', 'disabled').attr('placeholder', 'غیرفعال')
      }else if(value_daily_fine_amount == 0){
        fixed_penalty_amount.removeAttr('disabled', 'disabled').attr('placeholder', placeholder_fixed_penalty_amount)
      }

      //مبلغ جریمه ثابت
      if(value_fixed_penalty_amount.replace(/,/g, '') > 0){
        if(typeof daily_fine_amount.attr('disabled') === 'undefined') daily_fine_amount.attr('disabled', 'disabled').prepend('<option selected="selected">غیرفعال</option>')
      }else if(typeof daily_fine_amount.attr('disabled') !== 'undefined'){
        daily_fine_amount.removeAttr('disabled', 'disabled');
        daily_fine_amount.find(':first-child').remove(); //حذف غیرفعال
        daily_fine_amount.find(':first-child').prop('selected', true)
      }
    }
    jQuery('#daily-fine-amount, #fixed-penalty-amount').on('input change keyup keydown keypress', check_penalty_fields)
    /* END بررسی فیلدهای جریمه */
  </script>
                    @endsection

