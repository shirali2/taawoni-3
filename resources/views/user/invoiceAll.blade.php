@extends("theme.user")
@section("container")

<style>
@media print {
    .no-print, #kt_header_mobile, #kt_aside, .header-mobile, #kt_aside_menu_wrapper, #kt_quick_user, .aside-secondary { display: none !important; }
    .wrapper { padding: 0 !important; margin: 0 !important; }
    .card-box { border: none !important; box-shadow: none !important; width: 100% !important; }
    .container-fluid { padding: 0 !important; }
    body { direction: rtl; }
}
</style>

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
        <div class="card-box table-responsive">
            <div class="m-t-0 m-b-30 d-flex align-items-center justify-content-between flex-wrap">
                <h4 class="header-title d-inline-block" style="line-height: 30px;">
                    صورت حساب نهایی {{$user["name"]}} {{$user["name2"]}} - کاربری {{$user["id"]}} - کد {{$user["hash"]}} - گزارش {{verta()->format('j-n-Y')}}
                    {{empty($user["lawyer_name"]) ? "" : '(وکیل: "'.$user["lawyer_name"].'")'}}
                </h4>
                <button type="button" class="btn btn-secondary waves-effect no-print" onclick="window.print()">
                    <i class="fa fa-print"></i> دانلود / چاپ PDF
                </button>
            </div>
            <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 p-0 m-0 no-footer">
                <table id="datatable-buttons" class="table table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                    <thead>
                        <tr role="row">
                            <th class="sorting_asc" style="width: 15px;text-align: right !important;">شناسه</th>
                            <th class="sorting" style="text-align: right !important;">تاریخ ایجاد</th>
                            <th class="sorting" style="text-align: right !important;">شرح</th>
                            <th class="sorting" style="text-align: right !important;">بدهکار (تومان)</th>
                            <th class="sorting" style="text-align: right !important;">بستانکار (تومان)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $invoice_price = 0;
                            $price_penalty = 0;
                            $price_paid = 0;
                            $paied_without_discount = 0;
                            $discount_paied = 0;
                        @endphp
                        @foreach($invoices as $invoice)
                        @php
                            $invoice_price += (int)$invoice['price'];
                            $price_penalty += (int)$invoice['Price_Penalty'];
                            $price_paid += (int)$invoice['Price_Paid'];
                            $paied_without_discount += (int)$invoice['paied_without_discount'];
                            $discount_paied += (int)$invoice['discount_paied'];
                        @endphp
                        <tr role="row" class="odd">
                            <td style="text-align: right !important;" class="sorting_1">{{$invoice["id"]}}</td>
                            <td style="text-align: right !important;">{{$invoice["am_start"]}}</td>
                            <td style="text-align: right !important;">{{$invoice["title"]}}</td>
                            <td style="text-align: right !important;">{{number_format($invoice['price'])}}</td>
                            <td style="text-align: right !important;"></td>
                        </tr>
                        @endforeach

                        <tr class="text-white">
                            <td class="bg-danger" colspan="2"></td>
                            <td class="bg-danger">جرائم تاخیر</td>
                            <td class="bg-danger">{{number_format($price_penalty)}}</td>
                            <td class="bg-danger"></td>
                        </tr>
                        <tr class="text-white">
                            <td class="bg-success" colspan="2"></td>
                            <td class="bg-success">جمع واریزی ها</td>
                            <td class="bg-success"></td>
                            <td class="bg-success">{{number_format($paied_without_discount)}}</td>
                        </tr>
                        <tr class="text-white">
                            <td class="bg-info" colspan="2"></td>
                            <td class="bg-info">تخفیفات</td>
                            <td class="bg-info"></td>
                            <td class="bg-info">{{number_format($discount_paied)}}</td>
                        </tr>
                        <tr>
                            <td class="bg-warning" colspan="2"></td>
                            <td class="bg-warning">جمع کل</td>
                            <td class="bg-warning">{{number_format($invoice_price + $price_penalty)}}</td>
                            <td class="bg-warning">{{number_format($paied_without_discount + $discount_paied)}}</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td class="bg-dark text-white text-nowrap" colspan="3">
                                مانده بدهی نهایی:
                                <span class="bg-white p-1 h6" style="color: red">
                                    {{number_format($invoice_price + $price_penalty - $paied_without_discount - $discount_paied)}} تومان
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/printThis.js"></script>
<script>
    $(".btn-delete").click(function() {
        var id = $(this).attr("id");
        var email = $(this).attr("name");


        Swal.fire({
            html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف زیر منو  ' + email + ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/menu/under/delete/' + id + '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>'
            , showCancelButton: !0
            , confirmButtonClass: "d-none"
            , confirmButtonText: "Yes, Delete It!"
            , cancelButtonClass: "d-none"
            , buttonsStyling: !1
            , showCloseButton: !0
        })

        $("#close-delete").click(function(e) {
            $(".swal2-close").trigger('click');

        });
    });

</script>
@endsection
