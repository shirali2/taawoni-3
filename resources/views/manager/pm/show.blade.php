@extends("theme.manager")
@section("container")
@php $accessOnlyShow = \App\Http\Controllers\managerController::managerAccessLevel(6.5) //فقط مشاهده @endphp
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <script src="/assets/js/modernizr.min.js"></script>

  <div class="row">
    <div class="col-12 mb-3">
      <div  class="card-box table-responsive">
        <div class="m-t-0 m-b-30">
          <a href="/manager/pm" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
          <h4 class="header-title d-inline-block ">فهرست لیست کاربرانی که پیام را مشاهده کرده اند</h4>
          <br> <h4 class="header-title d-inline-block ">{{$grop["name"]}}</h4>
        </div>

        <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                  {{--<button type="button" id="basic"--}}
                          {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
          <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
            <thead>
              <tr role="row">
                <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                <th class="sorting" style="text-align: right !important;" >نام نام خانوادگی کاربر</th>
                <th class="sorting" style="text-align: right !important;" >شماره موبایل کاربر</th>
                <th class="sorting" style="text-align: right !important;" >شناسه کاربر</th>
                <th class="sorting" style="text-align: right !important;" >کد کاربر</th>
                <th class="sorting" style="text-align: right !important;" >متن پیام</th>
                <th class="sorting" style="text-align: right !important;" >تاریخ مشاهده</th>
                @if(!$accessOnlyShow)
                  <th class="sorting" style="text-align: right !important;" >اقدامات</th>
                @endif
              </tr>
            </thead>

            <tbody>
              @foreach($pm_shows as $pm_show)
                <tr role="row" class="odd">
                  <td style="text-align: right !important;" class="sorting_1">{{$pm_show["id"]}}</td>
                  <td style="text-align: right !important;">{{$pm_show["name_user"]}}</td>
                  <td style="text-align: right !important;">{{$pm_show["mobile_user"]}}</td>
                  <td style="text-align: right !important;">{{$pm_show["user"]["id"]}}</td>
                  <td style="text-align: right !important;">{{$pm_show["user"]["hash"]}}</td>
                  <td style="text-align: right !important;">{{$pm["text"]}}</td>
                  <td style="text-align: right !important;">{{$pm_show["am"]}}</td>
                  @if(!$accessOnlyShow)
                    <td style="display: none;" class="d-block-s">
                      @if(\App\Http\Controllers\managerController::managerAccessLevel(10))
                        <button class="btn btn-danger btn-delete" id="{{$pm_show['id']}}" name="{{$pm_show['name_user']}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>
                      @endif
                    </td>
                  @endif
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @if(!$accessOnlyShow)
    <script type="text/javascript" language="JavaScript">
      $(".btn-delete").click(function(){
        var id = $(this).attr("id"),
            email = $(this).attr("name");

        Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف فرم  '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/manager/pm/show/delete/' + id + '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

        $("#close-delete").click(function(e){
          $(".swal2-close").trigger('click')
        });
      });
    </script>
  @endif
@endsection