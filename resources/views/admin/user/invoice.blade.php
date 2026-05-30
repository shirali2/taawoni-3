@extends("theme.default")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<style>
    .widget-user h2{
        font-size: 25px;
    }
</style>
    <script src="/assets/js/modernizr.min.js"></script>

    <div class="row">
        <div class="col-12 mb-3">
            <div  class="card-box table-responsive">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block ">فهرست پرداخت صورت حساب های کاربر {{$user["name"]}} {{$user["name2"]}} </h4>

                    <a href="/admin/user"
                       class="btn btn-pink btn-trans ml-2 waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">{{number_format($price)}} تومان</h2>
                                <h5>جمع واریزی</h5>
                            </div>
                        </div>
                    </div>


                </div>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                        <thead>
                        <tr role="row">
                            <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                            <th class="sorting"  style="text-align: right !important;" >شماره سند</th>
                            <th class="sorting"  style="text-align: right !important;" >تاریخ ایجاد</th>
                            <th class="sorting"  style="text-align: right !important;" >تاریخ پرداخت</th>
                            <th class="sorting"  style="text-align: right !important;" >عنوان</th>
                            <th class="sorting"  style="text-align: right !important;" >مبلغ واریزی (تومان) </th>
                            <th class="sorting"  style="text-align: right !important;" >کد پیگیری پرداخت </th>
                            {{--<th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>--}}
                        </thead>


                        <tbody>
                        @foreach($invoice_pays as $invoice_pay)

                            <tr role="row" class="odd">
                                <td style="text-align: right !important;" class="sorting_1">{{$invoice_pay["id"]}}</td>
                                <td style="text-align: right !important;">{{$invoice_pay["invoice"]["number"]}}</td>
                                <td style="text-align: right !important;">{{$invoice_pay["invoice"]["am_start"]}}</td>
                                <td style="text-align: right !important;">{{$invoice_pay['am']}}</td>
                                <td style="text-align: right !important;">{{$invoice_pay["text"]}}</td>
                                <td style="text-align: right !important;">{{number_format($invoice_pay["price"])}}</td>
                                <td style="text-align: right !important;">{{$invoice_pay["referenceId"]}}</td>


                                {{--<td style="display: none;" class="d-block-s">--}}
                                    {{--<a href="/user/invoice/pay/{{$invoice["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="فرم های پرشده" class="btn btn-success"><i class="fa fa-eye"></i></a>--}}
                                    {{--<button class="btn btn-danger btn-delete" id="{{$invoice["id"]}}" name="{{$invoice["title"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>--}}
                                {{--</td>--}}
                            </tr>
                        @endforeach

                        </tbody>
                    </table></div></div></div>
            </div>



            </div>

    </div>

    <script>


        $('input[name="price"]').keyup(function(){
            number_keyup()
        })
        number_keyup()
        function number_keyup(){
            var val=  $('input[name="price"]').val();
            if(parseInt(val)){
                val=val.replace(/,/g, "");
                $('input[name="price"]').val(parseInt(val).toLocaleString())
            }else{
                $('input[name="price"]').val(0)
            }
        }



        function price22() {
            var er=0;
            var val=parseInt($('input[name="price"]').val().replace(/,/g, ""));
            var max=parseInt($('input[name="price"]').attr("max_valu"));
            $("#parsley-id-5-5").find("li").remove();
            if (val>max){
                er=1;
                $("#parsley-id-5-5").append('<li class="parsley-max">این مقدار باید کمتر از یا برابر با '+parseInt(max).toLocaleString()+' باشد . </li>')
            }
            if (val<10000){
                er=1;
                $("#parsley-id-5-5").append('<li class="parsley-max">این مقدار باید بیشتر از یا مساوی 10,000 باشد.</li>')
            }
            if (er==1){
                return false;
            }
        }
    </script>


@endsection
