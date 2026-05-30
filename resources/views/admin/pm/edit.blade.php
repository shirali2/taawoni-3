@extends("theme.default")
@section("container")
  <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>

  <style type="text/css">
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

  <div class="container">
    <div class="row">
      <div class="col-12 mb-3">
        <div class="card-box ">
          <div class="m-t-0 m-b-30">
            <h4 class="header-title d-inline-block ">ارسال پیام جدید</h4>
            <a href="/admin/pm" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
          </div>

          <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
            @csrf    @csrf @csrf

            @foreach($errors->all() as $ereor)
              <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
            @endforeach

            <div class="row">
              <div class="col-12">
                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">مجموعه</label>
                  <div class="col-sm-8">
                    <p class="col-form-label border p-2 text-justify">{{$grop}}</p>
                  </div>
                </div>

                @if(!empty($user_grops))
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">گروه های کاربری مجموعه</label>
                    <div class="col-sm-8">
                      <p class="col-form-label float-right w-100 border p-2 text-justify">
                        @foreach($user_grops as $user_grop)
                          <span class="float-left bg-dark text-white m-1 p-2">{{$user_grop['name']}}</span>
                        @endforeach
                      </p>
                    </div>
                  </div>
                @endif

                @if($users_grop != '')
                  <div class="select-users form-group row">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">کاربران</label>
                    <div class="col-sm-8">
                      <p class="col-form-label float-right w-100 border p-2 text-justify">
                        @foreach($users_grop as $user_grop)
                          <span class="float-left bg-dark text-white m-1 p-2">{{$user_grop['info_user']}}</span>
                        @endforeach
                      </p>
                    </div>
                  </div>
                @endif

                <div class="form-group row">
                  <label for="inputEmail3" class="col-sm-3 col-form-label">عنوان </label>
                  <div class="col-sm-8">
                    <p class="col-form-label border p-2 text-justify">{{$pm['title']}}</p>
                  </div>
                </div>

                @if($pm['img'])
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">انتخاب عکس </label>
                    <div class="col-sm-8">
                      <img src="/pm_img/{{$pm['img']}}" width="100%" />
                    </div>
                  </div>
                @endif

                @if($pm['text'])
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-3 col-form-label">متن پیام </label>
                    <div class="col-sm-8">
                      <p class="col-form-label border p-2 text-justify">{{$pm['text']}}</p>
                    </div>
                  </div>
                @endif

                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">خاتمه نمایش</label>
                  <div class="col-sm-8">
                    @php $date_and_time_end_show = !empty($pm['date_end_show']) ? explode(' ', $pm['date_end_show']) : array('', '') @endphp

                    <label for="date-end-show" class="w-100 col-form-label">
                      <span class="text-danger">تاریخ:</span>
                      <input type="text" name="date-end-show" id="date-end-show" class="form-control" placeholder="مثال: 1402/07/10" style="margin-top: 5px" value="{{$date_and_time_end_show[0]}}" />
                    </label>

                    <label for="time-end-show" class="w-100 col-form-label">
                      <span class="text-danger">ساعت:</span>
                      <input type="time" name="time-end-show" id="time-end-show" class="form-control" style="margin-top: 5px" value="{{$date_and_time_end_show[1]}}" />
                    </label>
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
  </script>
@endsection