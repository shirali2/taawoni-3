@extends("theme.manager")
@section("container")
@php $remainder_commitment = $invoice_user['price'] + $invoice_user['price_fine'] - $price_active - $price_discount //مانده تعهد @endphp

<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<style>
    .widget-user h2 {
        font-size: 25px
    }
</style>

<script src="/assets/js/modernizr.min.js"></script>

@if($remainder_commitment > 0)
@foreach(array(
'receipt' => 'فیش',
'discount' => 'تخفیف'
) as $name_english => $name_persian)
<div class="modal fade create-{{$name_english}}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0" id="myLargeModalLabel">ثبت {{$name_persian}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>

            <div class="modal-body">
                <form method="post" action="/manager/invoice/pay/add/{{$invoice_user["id"]}}/{{$user["id"]}}">
                    @csrf

                    @if($name_english == 'discount')
                    <input type="hidden" name="type" value="{{$name_english}}" class="d-none" />
                    @endif

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">شماره</label>
                        <div class="col-sm-8">
                            <input type="text" required placeholder="شماره فیش را وارد کنید" name="number" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">مبلغ (تومان)</label>
                        <div class="col-sm-8">
                            <input type="text" required max_valu="{{$remainder_commitment}}" placeholder="مبلغ واریزی را وارد کنید" name="price" class="form-control">
                            <ul class="parsley-errors-list"></ul>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">شرح</label>
                        <div class="col-sm-8">
                            <input type="text" required placeholder="شرح مبلغ را وارد کنید" name="text" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">تاریخ</label>
                        <div class="col-sm-8">
                            <input type="text" required id="am-start-{{$name_english}}" name="am" class="set-datepicker form-control">
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success waves-effect waves-light">ثبت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif

<div class="row">
    <div class="col-12 mb-3">
        <div class="card-box table-responsive">
            <div class="m-t-0 m-b-30">
                <h4 class="header-title d-inline-block ">فهرست پرداخت صورت حساب {{$invoice_user["title"]}} - {{$user["name"]}} {{$user["name2"]}} - شناسه کاریری {{$user['id']}} - کد {{$user["hash"]}} </h4>

                @if(
                \App\Http\Controllers\managerController::managerAccessLevel(70)
                &&
                $remainder_commitment > 0
                )
                <button class="btn btn-primary waves-effect waves-light float-right" data-toggle="modal" data-target=".create-discount">ثبت تخفیف</button>
                <button class="btn btn-primary waves-effect waves-light float-right ml-2" data-toggle="modal" data-target=".create-receipt">ثبت فیش</button>
                @endif

                <a href="/manager/invoice/user/{{$invoice_user["id"]}}" class="btn btn-pink btn-trans ml-2 waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
            </div>

            <div class="row w-100">
                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="text-center">
                            <h2 class="text-info" data-plugin="counterup">{{number_format($remainder_commitment)}} تومان</h2>
                            <h5>مانده تعهد</h5>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="text-center">
                            <h2 class="text-success" data-plugin="counterup">{{number_format($price_active)}} تومان</h2>
                            <h5>مجموع واریزی</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="text-center">
                            <h2 class="text-warning" data-plugin="counterup">@if($remainder_commitment == 0){{'پرداخت شد'}}@else{{$invoice_user["number_end"]}}{{' روز'}}@endif</h2>
                            <h5>زمان باقیمانده پرداخت</h5>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="text-center">
                            <h2 class="text-danger" data-plugin="counterup">{{number_format($invoice_user["price_fine"])}} تومان</h2>
                            <h5>مبلغ جریمه</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                {{--<button type="button" id="basic"--}}
                {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                <table id="datatable-buttons" class="table datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                    <thead>
                        <tr role="row">
                            <th class="sorting_asc" style="width: 15px;text-align: right !important;">شماره</th>
                            <th class="sorting" style="text-align: right !important;">مبلغ (تومان)</th>
                            <th class="sorting" style="text-align: right !important;">شرح</th>
                            <th class="sorting" style="text-align: right !important;">تاریخ</th>
                            <th>تصویر</th>
                            <th class="sorting" style="text-align: right !important;">کد پیگیری پرداخت</th>
                            <th>تایید شده</th>
                            @if(\App\Http\Controllers\managerController::managerAccessLevel(70))
                            <th class="sorting" style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($invoice_pays as $invoice_pay)
                        <tr role="row" class="odd">
                            <td style="text-align: right !important;" class="sorting_1">
                                {{$invoice_pay['ID']}}
                            </td>
                            <td style="text-align: right !important;">{{number_format($invoice_pay["price"])}}</td>
                            <td style="text-align: right !important;">@if(strstr($invoice_pay['text'], 'کاربر > '))<span class="d-inline-block bg-info text-white px-3 py-1 rounded-pill mb-1 ml-2">کاربر</span>{{str_replace('کاربر > ', '', $invoice_pay['text'])}}@else{{$invoice_pay['text']}}@endif</td>
                            <td style="text-align: right !important;">@if($invoice_pay['am']){{$invoice_pay['am']}}@else{{verta($invoice_pay['created_at'])}}@endif</td>
                            <td>@if($invoice_pay['picture'] != '')
                                {{-- Task 6a: picture already contains full path (/Files/User/...) — no prefix needed --}}
                                <a href="{{$invoice_pay['picture']}}" target="_blank">باز کردن</a>
                                @endif</td>
                            <td>@if(!$invoice_pay['referenceId'] && !$invoice_pay['am']) <span class="text-danger">@if($invoice_pay['active_pay'] == 3){{'در حال پرداخت'}}@else{{'پرداخت ناموفق'}}@endif</span> @else @if($invoice_pay['active_pay'] == 1) <span class="text-success">موفقیت آمیز</span> @else <span class="text-danger">در انتظار تایید</span> @endif @endif</td>
                            <td>@if($invoice_pay['active_pay'] == 1){{'بله'}}@else{{'خیر'}}@endif</td>
                            @if(\App\Http\Controllers\managerController::managerAccessLevel(70))
                            <td>
                                @if($invoice_pay['active_pay'] != 3)
                                {{-- Task 7: POST form instead of GET link to prevent accidental trigger on hover/prefetch --}}
                                <form method="post" action="/manager/invoice/pay/active/{{$invoice_pay["ID"]}}" class="d-inline-block toggle-active-form">
                                    @csrf
                                    <button type="button"
                                        class="btn btn-@if($invoice_pay['active_pay'] == 1){{'warning'}}@else{{'success'}}@endif btn-toggle-active"
                                        data-toggle="tooltip"
                                        data-original-title="@if($invoice_pay['active_pay'] == 1){{'رد'}}@else{{'تایید'}}@endif"
                                        data-confirm="@if($invoice_pay['active_pay'] == 1)آیا از رد این فیش مطمئن هستید؟@else آیا از تایید این فیش مطمئن هستید؟@endif">
                                        <i class="fa @if($invoice_pay['active_pay'] == 1){{'fa-ban'}}@else{{'fa-check'}}@endif"></i>
                                    </button>
                                </form>
                                @endif

                                <button class="btn btn-danger btn-delete" id="{{$invoice_pay["ID"]}}" name="{{$invoice_pay["text"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i class="fa fa-times"></i></button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/kamadatepicker.min.js"></script>
<script>
    function number_format(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        const n = !isFinite(+number) ? 0 : +number
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        let s = ''

        const toFixedFix = function(n, prec) {
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
    $('input[name="price"]').keyup(function() {
        $(this).val(number_format($(this).val()))
    })

    var customOptions = {
        nextButtonIcon: 'fa fa-arrow-circle-right'
        , previousButtonIcon: 'fa fa-arrow-circle-left'
        , buttonsColor: 'blue'
        , markToday: true
        , markHolidays: true
        , highlightSelectedDay: true
        , gotoToday: true
    }
    $('.set-datepicker').each(function() {
        kamaDatepicker($(this).attr('id'), customOptions)
    })

    function price22(form) {
        var form = $(form)
            , field_price = form.find('input[name="price"]')
            , val = parseInt(field_price.val().replace(/,/g, ''))
            , max = parseInt(field_price.attr('max_valu'))
            , element_error = form.next();
        element_error.empty();

        if (val > max) {
            element_error.append('<li class="parsley-max">این مقدار باید کمتر از یا برابر با ' + parseInt(max).toLocaleString() + ' باشد.</li>')
            return false;
        } else if (
            max > 10000 &&
            val < 10000
        ) {
            element_error.append('<li class="parsley-max">این مقدار باید بیشتر از یا مساوی 10,000 باشد.</li>')
            return false;
        }
    }


    // Task 7: تایید قبل از تغییر وضعیت فعال/غیرفعال — فقط با click، نه hover
    $(document).on('click', '.btn-toggle-active', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var msg = $(this).data('confirm');
        Swal.fire({
            title: 'تایید عملیات',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'بله',
            cancelButtonText: 'لغو'
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
    });

    $(".btn-delete").click(function() {
        var id = $(this).attr("id")
            , email = $(this).attr("name");

        Swal.fire({
            html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف فیش  ' + email + ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/manager/invoice/pay/delete/' + id + '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>'
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

<script>
    // نمایش پیام های سیستم
    @if(session('message'))
        Swal.fire({
            title: 'موفقیت',
            text: "{{ session('message') }}",
            icon: 'success',
            confirmButtonText: 'باشه'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'خطا',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'باشه'
        });
    @endif

    // غیرفعال کردن دکمه ثبت موقع کلیک برای جلوگیری از ثبت تکراری و حس فوری بودن
    $('form').on('submit', function(e) {
        // ابتدا چک کردن ولیدیشن قیمت
        if (typeof price22 === 'function' && price22(this) === false) {
            e.preventDefault();
            return false;
        }

        var btn = $(this).find('button[type="submit"]');
        if (btn.length > 0 && !btn.hasClass('btn-danger')) {
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> در حال ثبت...');
        }
    });
</script>
@endsection
