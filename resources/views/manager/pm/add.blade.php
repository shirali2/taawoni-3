@extends("theme.manager")
@section("container")
  <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>

  <style type="text/css">
    .select2-container .select2-selection--multiple .select2-selection__choice{
      background-color: #71b6f9;
      -webkit-border: 1px solid transparent;
      -moz-border: 1px solid transparent;
      -ms-border: 1px solid transparent;
      -ms-ie-border: 1px solid transparent;
      -o-border: 1px solid transparent;
      -khtml-border: 1px solid transparent;
      border: 1px solid transparent;
      color: #ffffff;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      -ms-border-radius: 3px;
      -ms-ie-border-radius: 3px;
      -o-border-radius: 3px;
      -khtml-border-radius: 3px;
      border-radius: 3px;
      padding: 0 7px
    }

    /* START تقویم */
    .bd-main,
      .bd-table,
        .day{ /* روزها */
      width: 100% !important
    }

    .bd-main{
      padding-bottom: 0
    }
      .bd-calendar{
        width: 100%
      }

        .bd-title{ /* هدر */
          margin-right: auto;
          margin-left: auto
        }
          .bd-next{ /* دکمه بعدی */
            margin-bottom: 5px
          }

        thead th{
          text-align: -webkit-center !important;
          text-align: -moz-center !important;
          text-align: -ms-center !important;
          text-align: -ms-ie-center !important;
          text-align: -o-center !important;
          text-align: -khtml-center !important;
          text-align: center !important
        }

        .bd-goto-today{ /* دکمه برو به امروز */
          margin-top: 6px;
          margin-right: auto;
          margin-left: auto;
          background-color: #000
        }
        .bd-goto-today:hover{
          background-color: unset;
          color: #000;
          border-top: 1px solid #000;
          border-right: 1px solid #000;
          border-left: 1px solid #000
        }
    /* END تقویم */
  </style>

  <!-- DataTables -->
  <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
  <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>

  <div class="container">
    <div class="row">
      <div class="col-12 mb-3">
        <div class="card-box ">
          <div class="m-t-0 m-b-30">
            <h4 class="header-title d-inline-block ">ارسال پیام جدید</h4>
            <a href="/manager/pm" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
          </div>

          <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
            @csrf    @csrf @csrf

            @foreach($errors->all() as $ereor)
              <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
            @endforeach

            <div class="row">
              <div class="col-12">
                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">انتخاب گروه کاربری مجموعه </label>
                  <div class="col-sm-8">
                    <select name="gropUser[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="انتخاب گروه کاربری مجموعه" tabindex="-1" aria-hidden="true">
                      @foreach($grops as $grop)
                        <option value="{{$grop["id"]}}">{{$grop["name"]}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="select-users form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">انتخاب کاربر </label>
                  <div class="col-sm-8">
                    <select name="user[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="انتخاب کاربر" tabindex="-1" aria-hidden="true"></select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">عنوان </label>
                  <div class="col-sm-8">
                    <input type="text" required placeholder="عنوان را وارد کنید" name="title" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">انتخاب عکس </label>
                  <div class="col-sm-8">
                    <input type="file" name="file" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">متن پیام </label>
                  <div class="col-sm-8">
                    <textarea name="sms" placeholder="متن پیام را وارد کنید" class="form-control"></textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">ذخیره به عنوان یادداشت</label>
                  <div class="col-sm-8 d-flex align-items-center">
                    <input type="checkbox" name="save_as_note" value="1" id="save_as_note">
                    <label for="save_as_note" class="mb-0 mr-2">متن پیام را در یادداشت‌های کاربران ذخیره کن</label>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">خاتمه نمایش</label>
                  <div class="col-sm-8">
                    <label for="date-end-show" class="w-100 col-form-label">
                      <span class="text-danger">تاریخ:</span>
                      <input type="text" name="date-end-show" id="date-end-show" class="form-control" placeholder="مثال: 1402/07/10" style="margin-top: 5px" />
                    </label>

                    <label for="time-end-show" class="w-100 col-form-label">
                      <span class="text-danger">ساعت:</span>
                      <input type="time" name="time-end-show" id="time-end-show" class="form-control" style="margin-top: 5px" />
                    </label>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="form-group">
                  <div class="offset-sm-4 text-right col-sm-12">
                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">افزودن</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/assets/js/kamadatepicker.min.js"></script>
  <script type="text/javascript" language="JavaScript">
    kamaDatepicker('date-end-show', {
      twodigit: true,
      closeAfterSelect: true,
      nextButtonIcon: 'fa fa-arrow-circle-right',
      previousButtonIcon: 'fa fa-arrow-circle-left',
      buttonsColor: 'green',
      markToday: true,
      markHolidays: true,
      highlightSelectedDay: true,
      gotoToday: true
    }); //تنظیم تاریخ بر روی فیلد تاریخ

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


    var ajax_get_users_grop; //ایجکس دریافت کاربران
    $('select[name="gropUser[]"]').change(function(){
      if(typeof ajax_get_users_grop !== 'undefined') ajax_get_users_grop.abort();

      var grop_user_selected = $(this).val() || []; //گروه کاربری مجموعه انتخاب شده
      if(grop_user_selected.length == 0){return}
      else if(grop_user_selected.length > 1){
        var div_select_users = $('.select-users'); //دایو انتخاب کاربران
        div_select_users.hide();
        div_select_users.find('select').val('');
        return;
      }
      $('.select-users').show();

      var token = $("input[name='_token']").val();

      ajax_get_users_grop = $.ajax('/manager/pm/add/user', {
        type: 'post',
        async: false,
        data: {
          _token: token,
          gropUser: grop_user_selected
        },
        success: function(data){
          var selectbox_users = $("select[name='user[]']"); //سلکت باکس انتخاب کاربران
          selectbox_users.html('<option disabled="">انتخاب کاربر </option>')

          $.each(jQuery.parseJSON(data), function(index, value){
            $("select[name='user[]']").append('<option value="' + value['id'] + '">' + value['info_user'] + '</option>')
          });
        }
      })
    })
  </script>
@endsection
