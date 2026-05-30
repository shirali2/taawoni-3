@extends("theme.default")
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

                    <h4 class="header-title d-inline-block ">فهرست کاربران</h4>
                    <a href="/addUser" class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5">افزودن کاربر</a>
                </div>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    <button type="button" id="basic"
                            class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 15px;text-align: right !important;" aria-sort="ascending" aria-label="Name: activate to sort column descending">شناسه</th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 210px;text-align: right !important;" aria-label="Position: activate to sort column ascending">نام شخض</th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 100px;text-align: right !important;" aria-label="Office: activate to sort column ascending">گروه</th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 180px;text-align: right !important;" aria-label="Age: activate to sort column ascending">شماره موبایل</th>
                                    <th class="sorting" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 112px;text-align: right !important;" aria-label="Start date: activate to sort column ascending">تراز مالی</th>
                                    <th class="sorting d-block-s" tabindex="0" aria-controls="datatable-buttons" rowspan="1" colspan="1" style="width: 260px;text-align: right !important;display: none;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
                                </thead>


                                <tbody>
                                @foreach($users as $user)
                                    @php
                                        $grop=\App\grop::find($user["grop"]);




        $sales_invoice=\App\sales_invoice::where("customer",$user["id"])->get();
        $total_sales_invoice = 0;$discount_total_sales_invoice = 0;$taxes_total_sales_invoice = 0;
        $total_purchase_service_positive_sales_invoice=0;
        $total_purchase_service_negative_sales_invoice=0;
        $total_payment_sales_invoice = 0;
        $total_discount_sell=0;
        foreach ($sales_invoice as $purchase){
            $purchase_invoice_row =\App\sales_invoice_row::where("id_sales_invoice", $purchase["id"])->get();
            $purchase_service = \App\purchase_service::where("id_sales_invoice", $purchase["id"])->get();
            $payment = \App\payment::where("id_sales_invoice", $purchase["id"])->get();
            $check = \App\check::where("id_sales_invoice", $purchase["id"])->get();
            foreach ($purchase_invoice_row as $row) {
                if ($row["returned"] > 0) {$row["number"] = $row["number"] - $row["returned"];}
                if ($row["returned_n"] > 0) {$row["number_n"] = $row["number_n"] - $row["returned_n"];}
                if ($row["number"] > 0 or $row["number_n"] > 0) {
                    $total_sales_invoice +=   ($row["number"]+$row["number_n"]) * $row["price"];
                    $discount_total_sales_invoice +=  $row["discount"];
                    $taxes_total_sales_invoice +=  $row["taxes"];
                }
            }
            foreach ($purchase_service as $service) {if ($service["type"]==0){$total_purchase_service_negative_sales_invoice+=$service["price"];}elseif ($service["type"]==1){$total_purchase_service_positive_sales_invoice+=$service["price"];}}
            foreach ($payment as $payments) {$total_payment_sales_invoice +=  $payments["price"];}

            $discount_sell = \App\discount_sell::where("id_sales_invoice", $purchase["id"])->get();
            foreach ($discount_sell as $discounts) {$total_discount_sell +=  $discounts["price"];}

            foreach ($check as $checks) {$total_payment_sales_invoice +=  $checks["price"];}
        }


                                    $col_sales_invoice=$total_sales_invoice-($discount_total_sales_invoice+$total_discount_sell)+$taxes_total_sales_invoice+$total_purchase_service_positive_sales_invoice-$total_purchase_service_negative_sales_invoice;
                                   $total_col_pay=$total_payment_sales_invoice;
                                    @endphp
                                    <tr role="row" class="odd">
                                        <td style="text-align: right !important;" class="sorting_1">{{$user["id"]}}</td>
                                        <td style="text-align: right !important;">{{$user["name"]}}</td>
                                        <td style="text-align: right !important;">{{$grop["name"]}}</td>
                                        <td style="text-align: right !important;">{{$user["mobile"]}}</td>
                                        <td style="text-align: right !important;">
                                            @if($col_sales_invoice-$total_col_pay==0)
                                                <span class="badge badge-success">0</span>
                                                @else
                                                <span class="badge badge-danger">{{number_format($col_sales_invoice-$total_col_pay)}}</span>

                                                @endif


                                        </td>
                                        <td style="display: none;" class="d-block-s">
                                            <a href="/viewUser/{{$user["id"]}}" class="btn btn-success"><i class="fa fa-eye"></i></a>
                                            <a href="/user/edit/{{$user["id"]}}" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                                            <button class="btn btn-danger"><i class="fa fa-times"></i></button>
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
        {{--$('#basic').on("click", function () {--}}
            {{--$('.demo').printThis({--}}
                {{--base: "https://jasonday.github.io/printThis/",--}}
                {{--header:"<h3 style='text-align: right !important;margin-bottom: 50px;'>فهرست کاربران</h3>",--}}
                {{--loadCSS: ["{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/bootstrap.min.css","{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/font.css"],--}}
                {{--importCSS:true,--}}
                {{--importStyle: true--}}
            {{--});--}}
        {{--})--}}
    </script>


@endsection
