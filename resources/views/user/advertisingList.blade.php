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

                    <h4 class="header-title d-inline-block ">فهرست تبلیغات خریداری شده</h4>
                    <a href="/user/advertising" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">خرید تبلیغ</a>
                </div>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                                    <th class="sorting"  style="text-align: right !important;" >نام تبلیغات</th>
                                    <th class="sorting"  style="text-align: right !important;" >روز ها</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ اتمام تبلیغ</th>
                                    <th class="sorting"  style="text-align: right !important;" >قیمت</th>
                                    <th class="sorting"  style="text-align: right !important;" >کد پیگیری پرداخت</th>
                                    <th class="sorting"  style="text-align: right !important;" >وضعیت</th>
                                    <th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
                                </thead>


                                <tbody>
                                @foreach($advertising_pays as $advertising_pay)
                                    @if($advertising_pay["active"]==2)
                                    <div id="myModal_{{$advertising_pay["id"]}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title mt-0" id="myModalLabel">آپلود بنر تبلیغ</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                </div>
                                                <form   id="form-register" class="form-horizontal m-t-20" method="post" action="/user/advertising/img/{{$advertising_pay["id"]}}" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                    @csrf
                                                <div class="modal-body ">

                                                    <div class=" mb-3">
                                                        <label for="exampleInputEmail1"> بنر تبلیغات : </label>
                                                        <input type="file" accept="image/png, image/jpeg" name="file" class="form-control" required="">
                                                    </div>
                                                </div>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                                                            آپلود
                                                        </button>
                                                    </div>

                                                </form>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                    @endif
                                    <tr role="row" class="odd">
                                        <td style="text-align: right !important;" class="sorting_1">{{$advertising_pay["id"]}} </td>
                                        <td style="text-align: right !important;">{{$advertising_pay["product"]["name"]}}</td>
                                        <td style="text-align: right !important;">{{$advertising_pay["day"]}}</td>
                                        <td style="text-align: right !important;">{{$advertising_pay["am_end"]}}</td>
                                        <td style="text-align: right !important;">{{number_format($advertising_pay["price"])}} تومان </td>
                                        <td style="text-align: right !important;">{{$advertising_pay["referenceId"]}}</td>
                                        <td style="text-align: right !important;">
                                            @if($advertising_pay["active"]==1)
                                                @if($advertising_pay["active_show"]==0)
                                                    <p class="text-warning mb-1">
                                                        در انتظار انتشار
                                                    </p>
                                                    @endif
                                                @if($advertising_pay["active_show"]==1)
                                                        <p class="text-success mb-1">
                                                            فعال
                                                        </p>
                                                    @endif
                                                @if($advertising_pay["active_show"]==2)
                                                        <p class="text-muted mb-1">
                                                            پایان یافته
                                                        </p>
                                                    @endif


                                            @elseif($advertising_pay["active"]==2)
                                                <p class="text-danger mb-1">
                                                    بنر تبلیغاتی تایید نشده لطفا دوباره اپلود کنید
                                                </p>
                                            @else
                                                <p class="text-danger mb-1">
                                                    در انتظار تایید تبلیغ
                                                </p>
                                            @endif
                                        </td>
                                        <td style="display: none;" class="d-block-s">
                                            @if($advertising_pay["active"]==2) <button data-toggle="modal" data-target="#myModal_{{$advertising_pay["id"]}}" class="btn btn-primary"><i class="fa fa-eye"></i></button>@endif
                                            {{--<button class="btn btn-danger btn-delete" id="{{$ticket["id"]}}" name="{{$ticket["name"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>--}}
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
