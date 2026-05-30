@extends("theme.manager")
@section("container")
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<script src="/assets/plugins/jalali/jalaali.min.js"></script>

<style>
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #71b6f9;
        border: 1px solid transparent;
        color: #ffffff;
        border-radius: 3px;
        padding: 0 7px
    }

</style>

<link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<script src="/ckeditor/ckeditor.js" type="text/javascript"></script>

<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card-box ">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block ">افزودن صورتحساب جدید</h4>
                    <a href="/manager/invoice" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                </div>


                <form id='frmInvoice' class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
                    @csrf @csrf @csrf @foreach($errors->all() as $ereor)

                    <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                    @endforeach
                    <div class="row">


                        <div class="col-12">

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">انتخاب گروه کاربری مجموعه </label>
                                <div class="col-sm-8">
                                    <select name="gropUser[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="انتخاب گروه کاربری مجموعه" tabindex="-1" aria-hidden="true">
                                        @foreach($usergrops as $usergrop)
                                        <option value="{{$usergrop["id"]}}">{{$usergrop["name"]}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">انتخاب کاربر </label>
                                <div class="col-sm-8">
                                    <select name="user[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="انتخاب کاربر" tabindex="-1" aria-hidden="true">

                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">عنوان </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="عنوان را وارد کنید" name="title" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">شرح </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="شرح را وارد کنید" name="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ تعهد (تومان) </label>
                                <div class="col-sm-8">
                                    <input type="text" id='price' required placeholder="مبلغ تعهد را وارد کنید" name="price" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">شماره سند </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="شماره سند را وارد کنید" name="number" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">تاریخ ایجاد </label>
                                <div class="col-sm-8">
                                    <input type="text" required id="test-date-id2" placeholder="تاریخ ایجاد را وارد کنید" name="am_start" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">تاریخ تعهد </label>
                                <div class="col-sm-8">
                                    <input type="text" id="test-date-id" placeholder="تاریخ تعهد را وارد کنید" name="am_end" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="fine_calc_date_display">تاریخ محاسبه جریمه </label>
                                <div class="col-sm-8">
                                    <input type="text" id="fine_calc_date_display" placeholder="تاریخ محاسبه جریمه را وارد کنید" name="fine_calc_date" class="form-control">
                                    <input type="hidden" id="fine_calc_date" placeholder="تاریخ محاسبه جریمه را وارد کنید" name="fine_calc_date" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ جریمه روزانه (بر اساس 1000 تومان در روز)</label>
                                <div class="col-sm-8">
                                    <select name="daily-fine-amount" tabindex="-1" aria-hidden="true" id="daily-fine-amount" class="form-control select2">
                                        <option disabled="disabled" selected="selected">انتخاب عدد</option>
                                        @foreach(range(0, 9) as $number)
                                        <option>{{$number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ جریمه ثابت</label>
                                <div class="col-sm-8">
                                    <input type="text" name="fixed-penalty-amount" placeholder="مبلغ جریمه ثابت را وارد کنید" id="fixed-penalty-amount" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="fine_calc_amount">مبلغ جریمه محاسبه شده</label>
                                <div class="col-sm-8">
                                    <input type="text" id="fine_calc_amount" name="fine_calc_amount" placeholder="مبلغ جریمه محاسبه شده را وارد کنید" class="form-control" />
                                </div>
                            </div>

                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="offset-sm-4 text-right col-sm-12">
                                    <button type="button" id="btnFineCalc" class="btn btn-default">محاسبه جریمه</button>
                                    <button type="submit" id="btnSubmitInvoice" class="d-none btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                        افزودن
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>


</div>

<script src="/assets/js/kamadatepicker.min.js"></script>
<script>
    var customOptions = {
        placeholder: "روز / ماه / سال"
        , twodigit: true
        , closeAfterSelect: true
        , nextButtonIcon: "fa fa-arrow-circle-right"
        , previousButtonIcon: "fa fa-arrow-circle-left"
        , buttonsColor: "blue"
        , forceFarsiDigits: true
        , markToday: true
        , markHolidays: true
        , highlightSelectedDay: true
        , sync: true
        , gotoToday: true
    }


    kamaDatepicker('test-date-id', customOptions);
    kamaDatepicker('test-date-id2', customOptions);
    kamaDatepicker('fine_calc_date_display', customOptions);

    $('input[name="price"],input[name="fine_calc_amount"]').keyup(function() {
        number_keyup(this);
    })
    const fine_calc_date_display = document.getElementById('fine_calc_date_display');
    const fine_calc_date = document.getElementById('fine_calc_date');
    fine_calc_date_display.value = get_today_jalali_date();

    document.getElementById('frmInvoice').addEventListener('submit', function(e) {
        fine_calc_date.value = convert_jalali_to_miladi(fine_calc_date_display.value);
        console.log(`(${this.value}) To Miladi = `, fine_calc_date.value);
    });

    document.getElementById('btnFineCalc').addEventListener('click', function(e) {
        let fine_calc_amount = document.getElementById('fine_calc_amount')
            , price = document.getElementById('price').value
            , daily_fine_amount = document.getElementById('daily-fine-amount').value;
        const fine_calc_dateStr = $('#fine_calc_date_display').val();
        const am_endStr = $('#test-date-id').val();

        if (document.getElementById('daily-fine-amount').selectedIndex <= 0 || !fine_calc_dateStr || !am_endStr) {
            alert('لطفا فیلد های"مبلغ تعهد"، "مبلغ جریمه روزانه" ، "تاریخ تعهد" و "تاریخ حاسبه جریمه" پر شوند');
            return;
        }

        fine_calc_amount.value = 0;
        if (first_date_GreatherThan_second_date(fine_calc_dateStr, am_endStr)) {
            fine_calc_amount.value = ((parseInt(price.replaceAll(",", '')) / 1000) * parseInt(daily_fine_amount.replaceAll(",", ''))) * calculateDifference();
        }

        document.getElementById('btnSubmitInvoice').classList.remove('d-none');
        number_keyup($('input[name="fine_calc_amount"]'));
    });

    function convert_jalali_to_miladi(jalali_date) {
        if (!jalali_date) return;

        const parts = jalali_date.split('/');
        if (parts.length !== 3) return;

        const g = jalaali.toGregorian(
            parseInt(parts[0])
            , parseInt(parts[1])
            , parseInt(parts[2])
        );
        let miladi = `${g.gy}-${String(g.gm).padStart(2, '0')}-${String(g.gd).padStart(2, '0')}`;
        return miladi;
    }

    function get_today_jalali_date() {
        // get today (Gregorian)
        const today = new Date();

        // convert to Jalali
        const j = jalaali.toJalaali(
            today.getFullYear()
            , today.getMonth() + 1
            , today.getDate()
        );

        // format Jalali date
        const jalaliToday =
            j.jy + '/' +
            String(j.jm).padStart(2, '0') + '/' +
            String(j.jd).padStart(2, '0');
        return jalaliToday;
    }

    function calculateDifference() {
        // Get date strings from inputs
        const fine_calc_dateStr = $('#fine_calc_date_display').val();
        const am_endStr = $('#test-date-id').val();

        if (!fine_calc_dateStr || !am_endStr) {
            alert('لطفا فیلد های "تاریخ تعهد" و "تاریخ حاسبه جریمه" پر شوند');
            return;
        }

        // Parse the dates (format: YYYY/MM/DD)
        const startParts = fine_calc_dateStr.split('/');
        const endParts = am_endStr.split('/');

        const fine_calc_date = {
            jy: parseInt(startParts[0])
            , jm: parseInt(startParts[1])
            , jd: parseInt(startParts[2])
        };

        const am_end = {
            jy: parseInt(endParts[0])
            , jm: parseInt(endParts[1])
            , jd: parseInt(endParts[2])
        };

        // Calculate difference
        const dayDiff = getJalaliDayDifference(fine_calc_date, am_end);

        // Display result
        return dayDiff;
    }

    function getJalaliDayDifference(date1, date2) {
        // Convert both dates to Gregorian
        const g1 = jalaali.toGregorian(date1.jy, date1.jm, date1.jd);
        const g2 = jalaali.toGregorian(date2.jy, date2.jm, date2.jd);

        // Create Date objects (months are 0-based in JavaScript)
        const d1 = new Date(g1.gy, g1.gm - 1, g1.gd);
        const d2 = new Date(g2.gy, g2.gm - 1, g2.gd);

        // Calculate difference in days
        const diffTime = Math.abs(d2.getTime() - d1.getTime());
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return diffDays;
    }

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

    number_keyup($('input[name="price"]'));
    number_keyup($('input[name="fine_calc_amount"]'));

    function number_keyup(element) {
        var val = $(element).val();

        $(element).val(number_format(val))

    }

    $('select[name="gropUser[]"]').change(function() {
        var gropUser = $(this).val();
        if (gropUser == '') {
            $('select[name="user[]"]').empty();
            return;
        }

        var token = $('input[name="_token"]').val();
        $.ajax('/manager/pm/add/user', {
            type: 'post'
            , async: false
            , data: {
                _token: token
                , gropUser: gropUser
            }
            , success: function(data1) {
                $("select[name='user[]']").find("option").remove();
                var data = jQuery.parseJSON(data1);
                $("select[name='user[]']").append('<option disabled="">انتخاب کاربر </option>')

                $.each(data, function(index, value) {
                    if (value["hash"] == null) {
                        value["hash"] = "";
                    }
                    $("select[name='user[]']").append('<option value="' + value['id'] + '">' + value['info_user'] + '</option>')
                });
            }
        })
    })


    /* START بررسی فیلدهای جریمه */
    function check_penalty_fields() {
        var daily_fine_amount = jQuery('#daily-fine-amount'), //مبلغ جریمه روزانه
            value_daily_fine_amount = daily_fine_amount.val()
            , fixed_penalty_amount = jQuery('#fixed-penalty-amount'), //مبلغ جریمه ثابت
            value_fixed_penalty_amount = fixed_penalty_amount.val();

        fixed_penalty_amount.val(number_format(value_fixed_penalty_amount));

        placeholder_fixed_penalty_amount = (typeof placeholder_fixed_penalty_amount === 'undefined') ? fixed_penalty_amount.attr('placeholder') : placeholder_fixed_penalty_amount;

        //مبلغ جریمه روزانه
        if (value_daily_fine_amount > 0) {
            fixed_penalty_amount.attr('disabled', 'disabled').attr('placeholder', 'غیرفعال')
        } else if (value_daily_fine_amount == 0) {
            fixed_penalty_amount.removeAttr('disabled', 'disabled').attr('placeholder', placeholder_fixed_penalty_amount)
        }

        //مبلغ جریمه ثابت
        if (value_fixed_penalty_amount.replace(/,/g, '') > 0) {
            if (typeof daily_fine_amount.attr('disabled') === 'undefined') daily_fine_amount.attr('disabled', 'disabled').prepend('<option selected="selected">غیرفعال</option>')
        } else if (typeof daily_fine_amount.attr('disabled') !== 'undefined') {
            daily_fine_amount.removeAttr('disabled', 'disabled');
            daily_fine_amount.find(':first-child').remove(); //حذف غیرفعال
            daily_fine_amount.find(':first-child').prop('selected', true)
        }
    }
    jQuery('#daily-fine-amount, #fixed-penalty-amount').on('input change keyup keydown keypress', check_penalty_fields)
    /* END بررسی فیلدهای جریمه */

</script>
<script>
    function first_date_GreatherThan_second_date(first, second) {
        // Split Jalali dates into parts (year, month, day)
        const [sy, sm, sd] = first.split('/').map(Number);
        const [ey, em, ed] = second.split('/').map(Number);

        // Convert Jalali to Gregorian
        const gStart = jalaali.toGregorian(sy, sm, sd);
        const gEnd = jalaali.toGregorian(ey, em, ed);

        // Create JS Date objects
        const dStart = new Date(gStart.gy, gStart.gm - 1, gStart.gd);
        const dEnd = new Date(gEnd.gy, gEnd.gm - 1, gEnd.gd);

        // Compare
        let message = '';


        return dStart.getTime() > dEnd.getTime();
    }

</script>

@endsection
