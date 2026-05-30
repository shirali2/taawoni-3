@extends("theme.manager")
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

                    <h4 class="header-title d-inline-block ">فهرست اطلاعات اختصاصی کاربران</h4>

               </div>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc"  style="width: 15px;text-align: right !important;" > شناسه کاربر</th>
                                    <th class="sorting"  style="text-align: right !important;" >کد</th>
                                    <th class="sorting"  style="text-align: right !important;" >جنسیت</th>
                                    <th class="sorting"  style="text-align: right !important;" >متاهل / مجرد</th>
                                    <th class="sorting"  style="text-align: right !important;" >نام عضو/کاربر</th>
                                    <th class="sorting"  style="text-align: right !important;" >نام خانوادگی عضو/کاربر</th>
                                    <th class="sorting"  style="text-align: right !important;" >کدملی</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ تولد</th>
                                    <th class="sorting"  style="text-align: right !important;" >نام پدر</th>
                                    <th class="sorting"  style="text-align: right !important;" >کد پستی</th>
                                    <th class="sorting"  style="text-align: right !important;" >شماره واحد</th>
                                    <th class="sorting"  style="text-align: right !important;" >شماره داخلی بلوک</th>
                                    <th class="sorting"  style="text-align: right !important;" >شماره ثبتی بلوک</th>
                                    <th class="sorting"  style="text-align: right !important;" >انباری</th>
                                    <th class="sorting"  style="text-align: right !important;" >پارکینگ</th>
                                    <th class="sorting"  style="text-align: right !important;" >محل پروژه</th>
                                    <th class="sorting"  style="text-align: right !important;" >فرم جیم</th>
                                    <th class="sorting"  style="text-align: right !important;" >قرارداد</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ شروع قرارداد</th>
                                    <th class="sorting"  style="text-align: right !important;" >تاریخ پایان قرارداد</th>
                                    <th class="sorting"  style="text-align: right !important;" >سند واحد</th>
                                    <th class="sorting"  style="text-align: right !important;" >مبلغ بدهی (تومان)</th>
                                    <th class="sorting"  style="text-align: right !important;" >نام نام خانوادگی وکیل</th>
                                    <th class="sorting"  style="text-align: right !important;" >کدملی وکیل</th>
                                    <th class="sorting"  style="text-align: right !important;" >شماره وکالتنامه وکیل</th>
                                    <th class="sorting"  style="text-align: right !important;" >تلفن همراه</th>
                                    <th class="sorting"  style="text-align: right !important;" >قابل توجه کاربر/عضو</th>
                                    <th class="sorting"  style="text-align: right !important;" >نظریه کارشناس </th>
                                    <th class="sorting"  style="text-align: right !important;" >تشاتی</th>
                                    <th class="sorting"  style="text-align: right !important;" >سایر</th>
                                </thead>


                                <tbody>
                                @foreach($users as $user)

                                    <tr role="row" class="odd">
                                        <td style="text-align: right !important;" class="sorting_1">{{$user["id"]}}</td>
                                        <td style="text-align: right !important;">{{$user["hash"]}}</td>
                                        <td style="text-align: right !important;">   @if($user["gender"]==1)
                                                زن
                                            @else
                                                مرد
                                            @endif</td>
                                        <td style="text-align: right !important;">   @if($user["marriage"]==1)
                                                متاهل
                                            @else
                                                مجرد
                                            @endif</td>
                                        <td style="text-align: right !important;">   {{$user["name"]}}</td>
                                        <td style="text-align: right !important;"> {{$user["name2"]}}</td>
                                        <td style="text-align: right !important;">       {{$user["kod"]}}</td>
                                        <td style="text-align: right !important;">{{$user["birth"]}}</td>
                                        <td style="text-align: right !important;">{{$user["father"]}}</td>
                                        <td style="text-align: right !important;">{{$user["mail"]}}</td>
                                        <td style="text-align: right !important;">{{$user["number_vahed"]}}</td>
                                        <td style="text-align: right !important;">{{$user["number_block"]}}</td>
                                        <td style="text-align: right !important;">{{$user["number_block2"]}}</td>
                                        <td style="text-align: right !important;">   @if($user["warehouse"]==1)
                                                دارد
                                            @else
                                                ندارد
                                            @endif</td>
                                        <td style="text-align: right !important;">     @if($user["parking"]==1)
                                                دارد
                                            @else
                                                ندارد
                                            @endif</td>
                                        <td style="text-align: right !important;">{{$user["location"]}}</td>
                                        <td style="text-align: right !important;"> @if($user["form_j"]==1)
                                                دارد
                                            @else
                                                ندارد
                                            @endif</td>
                                        <td style="text-align: right !important;">      @if($user["contract"]==1)
                                                دارد
                                            @else
                                                ندارد
                                            @endif</td>
                                        <td style="text-align: right !important;">{{$user["am_start_contract"]}}</td>
                                        <td style="text-align: right !important;">{{$user["am_end_contract"]}}</td>
                                        <td style="text-align: right !important;">{{$user["unit"]}}</td>
                                        <td style="text-align: right !important;">{{number_format(($user["invoice_price"]+$user["price_fine"])-$user["price"])}}</td>
                                        <td style="text-align: right !important;">{{$user["namename2"]}}</td>
                                        <td style="text-align: right !important;">{{$user["kodkod2"]}}</td>
                                        <td style="text-align: right !important;">{{$user["numbernumber2"]}}</td>
                                        <td style="text-align: right !important;">{{$user["mobile"]}}</td>
                                        <td style="text-align: right !important;">{{$user["significant"]}}</td>
                                        <td style="text-align: right !important;">{{$user["theory"]}}</td>
                                        <td style="text-align: right !important;">{{$user["tashati"]}}</td>
                                        <td style="text-align: right !important;">{{$user["other"]}}</td>

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
