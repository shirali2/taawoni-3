@extends("theme.manager")
@section("container")
@php $accessOnlyShow = \App\Http\Controllers\managerController::managerAccessLevel(11.5) //فقط مشاهده @endphp
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <script src="/assets/js/modernizr.min.js"></script>

    <div class="row">

        <div class="col-12 mb-3">
            <div  class="card-box table-responsive">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block ">فهرست تیکت ها مجموعه {{$grop["name"]}}</h4>
                </div>

                <div class="col-12">

                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#home1" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                همه
                            </a>
                        </li>
                        @foreach($issues as $issue)
                            <li class="nav-item">
                                <a href="#issue{{$issue["id"]}}" data-toggle="tab" aria-expanded="true" class="nav-link show">
                                    {{$issue["name"]}}
                                </a>
                            </li>
                            @endforeach


                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active show" id="home1">
                            <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                                {{--<button type="button" id="basic"--}}
                                {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                                <table id="datatable-buttons" class="table ticket datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                                        <th class="sorting"  style="text-align: right !important;" >موضوع</th>
                                        <th class="sorting"  style="text-align: right !important;" >وضعیت</th>
                                        <th class="sorting"  style="text-align: right !important;" >تاریخ ایجاد</th>
                                        <th class="sorting"  style="text-align: right !important;" >نام کاربر</th>
                                        <th class="sorting"  style="text-align: right !important;" >شناسه کاربر</th>
                                        <th class="sorting"  style="text-align: right !important;" >کد کاربر</th>
                                        <th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
                                    </thead>


                                    <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr role="row" class="odd">
                                            <td style="text-align: right !important;" class="sorting_1">{{$ticket["id"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["name"]}}</td>
                                            <td style="text-align: right !important;">
                                              @if(array_key_exists($ticket["active"], $statuses_ticket))
                                                <p class="@if(array_key_exists('Color', $statuses_ticket[$ticket['active']])) text-{{$statuses_ticket[$ticket['active']]['Color']}} @endif mb-1">{{$statuses_ticket[$ticket["active"]]["Text"]}}</p>
                                              @endif
                                            </td>
                                            <td style="text-align: right !important;">{{$ticket["am"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["name_user"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["id_user"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["hash_user"]}}</td>
                                            <td style="display: none;" class="d-block-s">
                                              <a href="/manager/ticket/message/{{$ticket["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="نمایش" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                              @if(!$accessOnlyShow)
                                                @if(\App\Http\Controllers\managerController::managerAccessLevel(15))
                                                  <a href="/manager/ticket/active/{{$ticket["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="تغییر وضعیت" @if($ticket["active"]==1) class="btn btn-danger" @else class="btn btn-success" @endif><i @if($ticket["active"]==1) class="fa fa-lock" @else class="fa fa-unlock" @endif ></i></a>
                                                @endif

                                                @if(\App\Http\Controllers\managerController::managerAccessLevel(18))
                                                  <button class="btn btn-danger btn-delete" id="{{$ticket["id"]}}" name="{{$ticket["name"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>
                                                @endif
                                              @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @foreach($issues as $issue)
                        <div role="tabpanel" class="tab-pane fade" id="issue{{$issue["id"]}}">


                            <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                                {{--<button type="button" id="basic"--}}
                                {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                                <table id="datatable-buttons" class="table ticket datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                                        <th class="sorting"  style="text-align: right !important;" >موضوع</th>
                                        <th class="sorting"  style="text-align: right !important;" >وضعیت</th>
                                        <th class="sorting"  style="text-align: right !important;" >تاریخ ایجاد</th>
                                        <th class="sorting"  style="text-align: right !important;" >نام کاربر</th>
                                        <th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
                                    </thead>


                                    <tbody>
                                    @foreach($tickets as $ticket)
                                       @if($issue["name"]==$ticket["name"])
                                        <tr role="row" class="odd">
                                            <td style="text-align: right !important;" class="sorting_1">{{$ticket["id"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["name"]}}</td>
                                            <td style="text-align: right !important;">
                                              @if(array_key_exists($ticket["active"], $statuses_ticket))
                                                <p class="@if(array_key_exists('Color', $statuses_ticket[$ticket['active']])) text-{{$statuses_ticket[$ticket['active']]['Color']}} @endif mb-1">{{$statuses_ticket[$ticket["active"]]["Text"]}}</p>
                                              @endif
                                            </td>
                                            <td style="text-align: right !important;">{{$ticket["am"]}}</td>
                                            <td style="text-align: right !important;">{{$ticket["name_user"]}}</td>
                                            <td style="display: none;" class="d-block-s">
                                              <a href="/manager/ticket/message/{{$ticket["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="نمایش" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                              @if(!$accessOnlyShow)
                                                @if(\App\Http\Controllers\managerController::managerAccessLevel(15))
                                                  <a href="/manager/ticket/active/{{$ticket["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="تغییر وضعیت" @if($ticket["active"]==1) class="btn btn-danger" @else class="btn btn-success" @endif><i @if($ticket["active"]==1) class="fa fa-lock" @else class="fa fa-unlock" @endif ></i></a>
                                                @endif

                                                @if(\App\Http\Controllers\managerController::managerAccessLevel(18))
                                                  <button class="btn btn-danger btn-delete" id="{{$ticket["id"]}}" name="{{$ticket["name"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>
                                                @endif
                                              @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>



                        </div>
                       @endforeach
                    </div>
                </div>


            </div>
      </div>
    </div>

  @if(!$accessOnlyShow)
    <script src="/assets/js/printThis.js"></script>
    <script>
      $(".btn-delete").click(function(){
        var id=$(this).attr("id"),
            email=$(this).attr("name");

        Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف تیکت  '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/manager/ticket/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

        $("#close-delete").click(function(e){
          $(".swal2-close").trigger('click')
        })
      });
    </script>
  @endif
@endsection