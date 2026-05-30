@extends("theme.user")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <script src="/assets/js/modernizr.min.js"></script>

    <div class="row">
        <div class="col-12 mb-3">
            <div  class="card-box table-responsive">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block "> پیام های مشاهده شده  </h4>
                </div>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                                    <th class="sorting"  style="text-align: right !important;" >عنوان</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ انتشار</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ مشاهده</th>
                                    <th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
                                </thead>


                                <tbody>
                                @foreach($pm_user as $pm)
                                    <div id="myModal_{{$pm["id"]}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-modal="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title mt-0" id="myModalLabel">{{$pm["title"]}}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                </div>
                                                <div class="modal-body pt-5">
                                                    @if($pm["img"])
                                                        <div class="text-center">
                                                            <img style="max-width: 440px;" src="/pm_img/{{$pm["img"]}}">
                                                        </div>
                                                    @endif
                                                    {{$pm["text"]}}

                                                    <p class="mt-5">تاریخ انتشار : {{$pm["am_start"]}}</p>
                                                    <p>تاریخ مشاهده : {{$pm['View_Date']}}</p>
                                                    <p> {{$pm["user"]["name"]}} {{$pm["user"]["name2"]}}</p>
                                                </div>

                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>

                                    <tr role="row" class="odd">
                                        <td style="text-align: right !important;" class="sorting_1">{{$pm["id"]}} </td>
                                        <td style="text-align: right !important;">{{$pm["title"]}}</td>
                                        <td style="text-align: right !important;">{{$pm["am_start"]}}</td>
                                        <td style="text-align: right !important;">{{$pm['View_Date']}}</td>

                                        <td style="display: none;" class="d-block-s">
                                            <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#myModal_{{$pm["id"]}}"><i class="fa fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table></div></div></div>
            </div>



            </div>

    </div>
    <script src="/assets/js/printThis.js"></script>
    <script>
        $(".btn-delete").click(function(){
            var id=$(this).attr("id");
            var email=$(this).attr("name");


            Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف زیر منو  '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/menu/under/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

            $("#close-delete").click(function(e){
                $(".swal2-close").trigger('click');

            });
        });
    </script>


@endsection
