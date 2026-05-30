@extends("theme.default")
@section("container")
  <!-- DataTables -->
  <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
  <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>

  <style type="text/css">
    .select2-container .select2-selection--multiple .select2-selection__choice{
      background-color: #71b6f9;
      -webkit-border: unset;
      -moz-border: unset;
      -ms-border: unset;
      -ms-ie-border: unset;
      -o-border: unset;
      -khtml-border: unset;
      border: unset;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      -ms-border-radius: 3px;
      -ms-ie-border-radius: 3px;
      -o-border-radius: 3px;
      -khtml-border-radius: 3px;
      border-radius: 3px;
      padding: 0 7px
    }
  </style>
  
  <div class="container">
    <div class="row">
      <div class="col-12 mb-3">
        <div class="card-box ">
          <div class="m-t-0 m-b-30">
            <h4 class="header-title d-inline-block ">ویرایش اشتراک</h4>
            <a href="/admin/product" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
          </div>

          <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
            @csrf

            @foreach($errors->all() as $ereor)
              <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
            @endforeach

            <div class="row">
              <div class="col-12">
                <div class="form-group row">
                  <label for="name" class="col-sm-3 col-form-label">نام بسته</label>
                  <div class="col-sm-8">
                    <input parsley-trigger="change" required name="name" value="{{$product['name']}}" type="text" class="form-control" id="name" placeholder="ضروری" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="number-days" class="col-sm-3 col-form-label">تعداد روز</label>
                  <div class="col-sm-8">
                    <input parsley-trigger="change" required name="number-days" value="{{$product['number_days']}}" type="text" class="form-control" id="number-days" placeholder="ضروری" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="amount-1-day" class="col-sm-3 col-form-label">مبلغ 1 روز</label>
                  <div class="col-sm-8">
                    <input parsley-trigger="change"  name="amount-1-day" value="{{$product['amount_1_day']}}" data-parsley-type="number" type="text" class="form-control" id="amount-1-day" placeholder="قیمت را وارد کنید" @if($product["package_type"] == 3) disabled="disabled" @endif />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">مبلغ کل</label>
                  <div class="col-sm-8">
                    @php
                      $extra_class_total_amount = $extra_attr_total_amount = '';
                      if($product["package_type"] == 3):
                        $extra_class_total_amount = ' form-control';
                        $extra_attr_total_amount = ' readonly';
                      endif
                    @endphp
                    <p id="total-amount" class="border mb-0 p-2 text-justify text-break{{$extra_class_total_amount}}"{{$extra_attr_total_amount}}>@if($product["package_type"] != 3 && $product['number_days'] && $product['amount_1_day'] >= 0){{number_format($product['number_days'] * $product['amount_1_day'])}} تومان @else در نوع بسته تشویقی مبلغ غیرفعال است @endif</p>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="description" class="col-sm-3 col-form-label">توضیحات</label>
                  <div class="col-sm-8">
                    <textarea parsley-trigger="change" data-parsley-type="text" name="description" rows="6" placeholder="اینجا بنویسید ..." id="description" class="form-control p-3">{{$product['description']}}</textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">انتخاب مجموعه</label>
                  <div class="col-sm-8">
                    <select name="grops[]" class="form-control select2 select2-multiple" multiple="multiple" data-placeholder="ضروری" required>
                      @foreach($grops as $grop)
                        <option value="{{$grop['id']}}"@if(in_array($grop['id'], $product['id_grops'])) selected="selected" @endif>{{$grop['name']}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">وضعیت</label>
                  <div class="col-sm-8">
                    <select name="status" class="form-control select2" required>
                      @foreach($statuses as $key_status => $name_status)
                        <option value="{{$key_status}}"@if($product["status"] == $key_status) selected="selected" @endif>{{$name_status}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">نوع بسته</label>
                  <div class="col-sm-8">
                    <select name="package-type" id="package-type" class="form-control select2" required>
                      <option value="1"@if($product["package_type"] == 1) selected="selected" @endif>اولیه</option>
                      <option value="2"@if($product["package_type"] == 2) selected="selected" @endif>تمدیدی</option>
                      <option value="3"@if($product["package_type"] == 3) selected="selected" @endif>تشویقی</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="form-group">
                  <div class="offset-sm-4 text-right col-sm-12">
                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">ویرایش</button>
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
  <script type="text/javascript" language="JavaScript">
    /* START جدا کردن اعداد از هم به روش پولی */
    function number_format(get_number){
      get_number += '';
      var split_number = get_number.split('.'),
          number_before_float = split_number[0], //عدد قبل اعشار
          number_format_regex = /(\d+)(\d{3})/;

      while(number_format_regex.test(number_before_float)){
        number_before_float = number_before_float.replace(number_format_regex, '$1' + ',' + '$2');
      }

      return number_before_float + ((split_number.length > 1) ? '.' + split_number[1] : '');
    }
    /* END جدا کردن اعداد از هم به روش پولی */


    /* START حساب کردن مبلغ کل */
    var amount_1_day = jQuery('#amount-1-day'), //مبلغ 1 روز
          value_amount_1_day = amount_1_day.val(),
        number_days = jQuery('#number-days'), //تعداد روز
        total_amount = jQuery('#total-amount'); //مبلغ کل
          text_default_total_amount = (total_amount.text() != ' در نوع بسته تشویقی مبلغ غیرفعال است ') ? total_amount.text() : 'سیستمی'
    function calculate_total_amount(){
      value_amount_1_day = amount_1_day.val();

      var value_number_days = number_days.val(); //تعداد روز

      if(value_amount_1_day != ''){
        total_amount.text(number_format(value_number_days ? value_amount_1_day * value_number_days : 1) + ' تومان')
      }else{
        total_amount.text(text_default_total_amount)
      }
    }
    amount_1_day.on('input change keyup keydown keypress', calculate_total_amount)
    number_days.on('input change keyup keydown keypress', calculate_total_amount)
    /* END حساب کردن مبلغ کل */


    /* START تغییر نوع بسته */
    jQuery('#package-type').change(function(){
      if(jQuery(this).val() == 3){ //تشویقی
        amount_1_day.val('').attr('disabled', 'disabled').removeAttr('required');
        total_amount.addClass('form-control').attr('readonly', '').text('در نوع بسته تشویقی مبلغ غیرفعال است')
      }else{
        amount_1_day.val(value_amount_1_day).attr('required', 'required').removeAttr('disabled').change();
        total_amount.removeClass('form-control').removeAttr('readonly')
      }
    })
    /* END تغییر نوع بسته */


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
    $.ajax('/admin/menu/add/gropUser', {
      type: 'post',
      async: false,
      data: {
        _token: token,
        grop: grop
      },
      success:function(data1){
        var data=jQuery.parseJSON(data1);
        $("select[name='gropUser']").find("option").remove();
        $("select[name='gropUser']").append('<option selected="" disabled="">انتخاب گروه کاربری مجموعه</option>')

        $.each(data, function(index, value){
          $("select[name='gropUser']").append('<option value="'+value["id"]+'">'+value["name"]+'</option>')
        });
      }
    })

    $("select[name='grop']").change(function(){
      var grop=$(this).find("option:selected").val();
      $("#type").find("div").remove();
      var token = $("input[name='_token']").val();
      $.ajax('/admin/menu/add/gropUser', {
        type: 'post',
        async: false,
        data: {
          _token: token,
          grop: grop
        },
        success: function(data1){
          var data=jQuery.parseJSON(data1);
          $("select[name='gropUser']").find("option").remove();
          $("select[name='gropUser']").append('<option selected="" disabled="">انتخاب گروه کاربری مجموعه</option>')

          $.each(data, function(index, value){
            $("select[name='gropUser']").append('<option value="'+value["id"]+'">'+value["name"]+'</option>')
          });
        }
      })
    })
  </script>
@endsection