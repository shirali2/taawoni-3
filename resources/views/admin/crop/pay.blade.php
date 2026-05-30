@extends("theme.default")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
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

                    <h4 class="header-title d-inline-block ">فهرست پرداخت محصول {{$product["name"]}}  </h4>

                    <a href="/admin/crop/"
                       class="btn btn-pink btn-trans ml-2 waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                </div>

                <div class="row w-100">
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">{{count($product_pay)}}</h2>
                                <h5>تعداد کل فروش</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-success" data-plugin="counterup">{{number_format($price)}} تومان</h2>
                                <h5>مبلغ فروش کل</h5>
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
                            <th class="sorting"  style="text-align: right !important;" >نام نام خانوادگی</th>
                            <th class="sorting"  style="text-align: right !important;" >شماره موبایل</th>
                            <th class="sorting"  style="text-align: right !important;" >شناسه کاربر</th>
                            <th class="sorting"  style="text-align: right !important;" >تاریخ پرداخت</th>
                            <th class="sorting"  style="text-align: right !important;" >مبلغ (تومان) </th>
                            <th class="sorting"  style="text-align: right !important;" >کد پیگیری پرداخت </th>
                            <th class="sorting"  style="text-align: right !important;" >اقدامات </th>
                            </tr>
                        </thead>


                        <tbody>
                        @foreach($product_pay as $pay)

                            <tr role="row" class="odd">
                                <td style="text-align: right !important;" class="sorting_1">{{$pay["id"]}}</td>
                                <td style="text-align: right !important;">{{$pay["user"]["name"]}} {{$pay["user"]["name2"]}}</td>
                                <td style="text-align: right !important;">{{$pay["user"]["mobile"]}}</td>
                                <td style="text-align: right !important;">{{$pay["user"]["id"]}}</td>
                                <td style="text-align: right !important;">{{$pay['am']}}</td>
                                <td style="text-align: right !important;">{{number_format($pay["price"])}}</td>
                                <td style="text-align: right !important;">{{$pay["referenceId"]}}</td>
                                <td style="text-align: right !important;">
                                    <button class="btn btn-danger btn-delete" id="{{$pay["id"]}}" name="{{$pay["user"]["name"]}} {{$pay["user"]["name2"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>

                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table></div></div></div>
            </div>



            </div>

    </div>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>
        function number_format (number, decimals, decPoint, thousandsSep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
            const n = !isFinite(+number) ? 0 : +number
            const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
            const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
            const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
            let s = ''

            const toFixedFix = function (n, prec) {
                if (('' + n).indexOf('e') === -1) {
                    return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
                } else {
                    const arr = ('' + n).split('e')
                    let sig = ''
                    if (+arr[1] + prec > 0) {
                        sig = '+'
                    }
                    return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
                }
            }


            s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || ''
                s[1] += new Array(prec - s[1].length + 1).join('0')
            }

            return s.join(dec)
        }
        $('input[name="price"]').keyup(function(){
            number_keyup()
        })
        number_keyup()
        function number_keyup(){
            var val=  $('input[name="price"]').val();

            $('input[name="price"]').val(number_format(val))

        }












        $(".btn-delete").click(function(){
            var id=$(this).attr("id");
            var email=$(this).attr("name");


            Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف پرداخت کاربر   '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/crop/pay/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

            $("#close-delete").click(function(e){
                $(".swal2-close").trigger('click');

            });
        });
    </script>


@endsection
