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

                    <h4 class="header-title d-inline-block ">اطلاعات اختصاصی کاربر</h4>
                    @if(\App\Http\Controllers\managerController::managerAccessLevel(74))

                    <a href="/manager/user/data/edit/{{$user["id"]}}" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">ویرایش اطلاعات اختصاصی </a>
@endif
                </div>

                <div class="text-center">
                  @if(isset($grop['name']))
                    <h3>{{$grop['name']}}</h3>
                  @endif

                  <h4>گزارش وضعیت کاربر {{$user["name"]}} {{$user["name2"]}} با کد شماره {{$user["hash"]}}</h4>
                </div>

                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                                <thead>
                                <tr role="row">
                                    <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >ردیف</th>
                                    <th class="sorting"  style="text-align: right !important;" >عنوان</th>
                                    <th class="sorting"  style="text-align: right !important;" >شرح </th>
                               </thead>


                                <tbody>

                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">1</td>
                                    <td style="text-align: right !important;">کد</td>
                                    <td style="text-align: right !important;">
                                       {{$user["hash"]}}
                                    </td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">2</td>
                                    <td style="text-align: right !important;">جنسیت</td>
                                    <td style="text-align: right !important;">
                                        @if($user["gender"]==1)
                                            زن
                                            @else
                                            مرد
                                        @endif
                                    </td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">3</td>
                                    <td style="text-align: right !important;">متاهل / مجرد</td>
                                    <td style="text-align: right !important;">
                                        @if($user["marriage"]==1)
                                            متاهل
                                        @else
                                            مجرد
                                        @endif
                                    </td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">4</td>
                                    <td style="text-align: right !important;">نام عضو/کاربر</td>
                                    <td style="text-align: right !important;">
                                        {{$user["name"]}}
                                    </td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">5</td>
                                    <td style="text-align: right !important;">نام خانوادگی عضو/کاربر</td>
                                    <td style="text-align: right !important;">
                                        {{$user["name2"]}}
                                    </td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">6</td>
                                    <td style="text-align: right !important;">کدملی</td>
                                    <td style="text-align: right !important;">
                                        {{$user["kod"]}}
                                    </td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">7</td>
                                    <td style="text-align: right !important;">تاریخ تولد</td>
                                    <td style="text-align: right !important;">{{$user["birth"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">8</td>
                                    <td style="text-align: right !important;">نام پدر</td>
                                    <td style="text-align: right !important;">{{$user["father"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">9</td>
                                    <td style="text-align: right !important;">کد پستی</td>
                                    <td style="text-align: right !important;">{{$user["mail"]}}</td>

                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">10</td>
                                    <td style="text-align: right !important;">شماره واحد</td>
                                    <td style="text-align: right !important;">{{$user["number_vahed"]}}</td>

                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">11</td>
                                    <td style="text-align: right !important;">شماره داخلی بلوک</td>
                                    <td style="text-align: right !important;">{{$user["number_block"]}}</td>

                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">12</td>
                                    <td style="text-align: right !important;">شماره ثبتی بلوک</td>
                                    <td style="text-align: right !important;">{{$user["number_block2"]}}</td>

                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">13</td>
                                    <td style="text-align: right !important;">انباری</td>
                                    <td style="text-align: right !important;">
                                        @if($user["warehouse"]==1)
                                            دارد
                                        @else
                                            ندارد
                                        @endif
                                    </td>

                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">14</td>
                                    <td style="text-align: right !important;">پارکینگ</td>
                                    <td style="text-align: right !important;">
                                        @if($user["parking"]==1)
                                            دارد
                                        @else
                                            ندارد
                                        @endif
                                    </td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">15</td>
                                    <td style="text-align: right !important;">محل پروژه</td>
                                    <td style="text-align: right !important;">{{$user["location"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">16</td>
                                    <td style="text-align: right !important;">فرم جیم</td>
                                    <td style="text-align: right !important;">
                                        @if($user["form_j"]==1)
                                            دارد
                                        @else
                                            ندارد
                                        @endif
                                    </td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">17</td>
                                    <td style="text-align: right !important;">قرارداد</td>
                                    <td style="text-align: right !important;">
                                        @if($user["contract"]==1)
                                            دارد
                                        @else
                                            ندارد
                                        @endif
                                    </td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">18</td>
                                    <td style="text-align: right !important;">تاریخ شروع قرارداد</td>
                                    <td style="text-align: right !important;">{{$user["am_start_contract"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">19</td>
                                    <td style="text-align: right !important;">تاریخ پایان قرارداد</td>
                                    <td style="text-align: right !important;">{{$user["am_end_contract"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">20</td>
                                    <td style="text-align: right !important;">سند واحد</td>
                                    <td style="text-align: right !important;">{{$user["unit"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">21</td>
                                    <td style="text-align: right !important;">مبلغ بدهی (تومان)</td>
                                    <td style="text-align: right !important;">{{number_format($price)}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">22</td>
                                    <td style="text-align: right !important;">نام نام خانوادگی وکیل</td>
                                    <td style="text-align: right !important;">{{$user["namename2"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">23</td>
                                    <td style="text-align: right !important;">کدملی وکیل</td>
                                    <td style="text-align: right !important;">{{$user["kodkod2"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">24</td>
                                    <td style="text-align: right !important;">شماره وکالتنامه وکیل</td>
                                    <td style="text-align: right !important;">{{$user["numbernumber2"]}}</td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">25</td>
                                    <td style="text-align: right !important;">تلفن همراه</td>
                                    <td style="text-align: right !important;">{{$user["mobile"]}}</td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">26</td>
                                    <td style="text-align: right !important;">قابل توجه کاربر/عضو</td>
                                    <td style="text-align: right !important;">{{$user["significant"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">27</td>
                                    <td style="text-align: right !important;">نظریه کارشناس </td>
                                    <td style="text-align: right !important;">{{$user["theory"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">28</td>
                                    <td style="text-align: right !important;">نشانی</td>
                                    <td style="text-align: right !important;">{{$user["tashati"]}}</td>
                                </tr><tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">29</td>
                                    <td style="text-align: right !important;">سایر</td>
                                    <td style="text-align: right !important;">{{$user["other"]}}</td>
                                </tr>

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