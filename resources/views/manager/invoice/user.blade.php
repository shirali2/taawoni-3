@extends("theme.manager")
@section("container")
<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<script src="/assets/js/modernizr.min.js"></script>

<div class="row">
    <div class="col-12 mb-3">
        <div class="card-box table-responsive">
            <div class="m-t-0 m-b-30">
                <a href="/manager/invoice" class="btn btn-pink btn-trans ml-2 waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                <h4 class="header-title d-inline-block ">فهرست کاربران صورت حساب {{$invoice["title"]}}</h4>
            </div>

            <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                {{--<button type="button" id="basic"--}}
                {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                    <thead>
                        <tr role="row">
                            <th style="width: 15px">شناسه</th>
                            <th style="width: 15px">شناسه کاربر</th>
                            <th style="width: 15px">شماره سند</th>
                            <th>نام نام خانوادگی</th>
                            <th>شماره موبایل</th>
                            <th>کد کاربر</th>
                            <th>گروه کاربری</th>
                            <th>تاریخ ایجاد</th>
                            <th>تاریخ تعهد</th>
                            <th>مبلغ تعهد (تومان)</th>
                            <th>مبلغ جریمه (تومان)</th>
                            <th>مجموع واریزی (تومان)</th>
                            <th>تخفیف (تومان)</th>
                            <th>مانده تعهد (تومان)</th>
                            <th>اقدامات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($invoice_users as $invoice_user)
                        <tr role="row" class="odd">
                            <td>{{$invoice['id']}}</td>
                            <td>{{$invoice_user["id"]}}</td>
                            <td>{{$invoice["number"]}}</td>
                            <td>{{$invoice_user["name"]}} {{$invoice_user["name2"]}}</td>
                            <td>{{$invoice_user["mobile"]}}</td>
                            <td>{{$invoice_user["hash"]}}</td>
                            <td>{{$invoice_user["name_user_grop"]}}</td>
                            <td>{{$invoice['am_start']}}</td>
                            <td>{{$invoice['am_end']}}</td>
                            <td>{{number_format($invoice['price'])}}</td>
                            <td>@if($invoice["am_end"]!=null){{number_format($invoice_user['price_fine'])}}@endif</td>
                            <td>{{number_format($invoice_user['price_active'])}}</td>
                            <td>{{number_format($invoice_user['Price_Discount'])}}</td>
                            <td>{{number_format($invoice['price'] + $invoice_user['price_fine'] - $invoice_user['price_active'])}}</td>
                            <td>
                                @if(\App\Http\Controllers\managerController::managerAccessLevel(68))
                                <a href="/manager/invoice/user/{{$invoice["id"]}}/{{$invoice_user["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="فرم های پرشده" class="btn btn-success"><i class="fa fa-eye"></i></a>
                                @endif
                                @if(\App\Http\Controllers\managerController::managerAccessLevel(77))
                                <button class="btn btn-danger btn-delete" data-route='{{route("invoiceUserPaysFinesDelete",[$invoice["id"],$invoice_user["id"]])}}' data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/printThis.js"></script>
<script type="text/javascript" language="JavaScript">
    $('.btn-delete').click(function() {
        var delete_route = $(this).data("route");

        Swal.fire({
            html: `<div class="mt-3 font-sans">
   <img src="/img/clipart2619611.png" style="width: 150px" />
   <div class="mt-4 pt-2 fs-15 mx-5">
      <h4 class="font-sans">مطمئن هستید ؟</h4>
      <p style="direction: rtl" class="text-muted mx-4 mb-0">
         در صورت حذف لیست تمامی واریزی ها و تخفیفات این صورت حساب پاک خواهند شد.
      </p>
      <form
         class="d-inline-block"
         action="${delete_route}"
         method="delete">
         @csrf<input name="_method" type="hidden" value="delete" /><button
            type="submit"
            class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3"
            aria-label="">
            بله , حذف
         </button>
         <button
            type="button"
            class="btn btn-secondary waves-effect w-md m-b-5 mt-3"
            id="close-delete">
            لغو
         </button>
      </form>
   </div>
</div>
`
            , showCancelButton: !0
            , confirmButtonClass: "d-none"
            , confirmButtonText: "Yes, Delete It!"
            , cancelButtonClass: "d-none"
            , buttonsStyling: !1
            , showCloseButton: !0
        })

        $("#close-delete").click(function(e) {
            $(".swal2-close").trigger('click')
        })
    });

</script>
@endsection
