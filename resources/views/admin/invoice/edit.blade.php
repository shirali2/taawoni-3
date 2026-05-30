@extends("theme.default")
@section("container")
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
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
                    <h4 class="header-title d-inline-block ">ویرایش صورتحساب</h4>
                    <a href="/admin/invoice" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                </div>

                <form class="form-horizontal form" method="post" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
                    @csrf @csrf @csrf
                    <input type="hidden" name="fine_calc_date" value="{{$info_invoice['fine_calc_date']? \Carbon\Carbon::parse($info_invoice['fine_calc_date'])->toDateString():\Carbon\Carbon::today()->toDateString()}}" />
                    <input type="hidden" name="fine_calc_amount" value="{{$info_invoice['fine_calc_amount']?$info_invoice['fine_calc_amount']:0}}" />

                    @foreach($errors->all() as $ereor)
                    <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                    @endforeach

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">انتخاب مجموعه </label>
                                <div class="col-sm-8">
                                    <select required name="grop" class="form-control select2">
                                        <option selected="" disabled="">انتخاب مجموعه</option>
                                        @foreach($grops as $grop)
                                        <option value="{{$grop['id']}}" @if($grop['id']==$info_invoice['id_grop']) selected="selected" @endif>{{$grop["name"]}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">انتخاب گروه کاربری مجموعه </label>
                                <div class="col-sm-8">
                                    <select name="gropUser[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="انتخاب گروه کاربری مجموعه" tabindex="-1" aria-hidden="true">
                                        @foreach($user_grops as $user_grop)
                                        <option value="{{$user_grop['id']}}" @if(in_array($user_grop['id'], $user_grops_selected)) selected="selected" @endif>{{$user_grop['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">انتخاب کاربر </label>
                                <div class="col-sm-8">
                                    <select name="user[]" class="form-control select2 select2-multiple select2-hidden-accessible" multiple="" data-placeholder="انتخاب کاربر" tabindex="-1" aria-hidden="true">
                                        @foreach($users as $user)
                                        <option value="{{$user['id']}}" @if(in_array($user['id'], $users_selected)) selected="selected" @endif>{{$user['name']}} {{$user['name2']}} [{{$user['id']}}] [{{$user['hash']}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">عنوان </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="عنوان را وارد کنید" name="title" value="{{$info_invoice['title']}}" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">شرح </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="شرح را وارد کنید" name="text" value="{{$info_invoice['text']}}" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ تعهد (تومان) </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="مبلغ تعهد را وارد کنید" name="price" value="{{number_format($info_invoice['price'])}}" class="form-control number-format">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">شماره سند </label>
                                <div class="col-sm-8">
                                    <input type="text" required placeholder="شماره سند را وارد کنید" name="number" value="{{$info_invoice['number']}}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">تاریخ ایجاد </label>
                                <div class="col-sm-8">
                                    <input type="text" required id="am-start" placeholder="تاریخ ایجاد را وارد کنید" value="{{$info_invoice['am_start']}}" name="am_start" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">تاریخ تعهد </label>
                                <div class="col-sm-8">
                                    <input type="text" id="am-end" placeholder="تاریخ تعهد را وارد کنید" name="am_end" value="{{$info_invoice['am_end']}}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ جریمه روزانه (بر اساس 1000 تومان در روز)</label>
                                <div class="col-sm-8">
                                    <select name="daily-fine-amount" tabindex="-1" aria-hidden="true" id="daily-fine-amount" class="form-control select2">
                                        <option disabled="disabled" selected="selected">انتخاب عدد</option>
                                        @foreach(range(0, 9) as $number)
                                        <option @if($number> 0 && $number == $info_invoice['daily_fine_amount']) selected="selected" @endif>{{$number}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">مبلغ جریمه ثابت</label>
                                <div class="col-sm-8">
                                    <input type="text" name="fixed-penalty-amount" placeholder="مبلغ جریمه ثابت را وارد کنید" value="{{number_format($info_invoice['fixed_penalty_amount'])}}" id="fixed-penalty-amount" class="form-control number-format" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <div class="offset-sm-4 text-right col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">ویرایش</button>
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
    kamaDatepicker('am-start', customOptions);
    kamaDatepicker('am-end', customOptions);


    /* START جدا کردن اعداد از هم به روش پولی */
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
        if (s[0].length > x) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        }

        return s.join(dec)
    }
    /* END جدا کردن اعداد از هم به روش پولی */
    $('.number-format').on('input change keyup keydown keypress', function() {
        $(this).val(number_format($(this).val()))
    })


    $("select[name='gropUser[]']").change(function() {
        var gropUser = $(this).val();
        if (gropUser == '') {
            $('select[name="user[]"]').empty();
            return;
        }

        var token = $("input[name='_token']").val();
        $.ajax('/admin/pm/add/user', {
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

    $("select[name='grop']").change(function() {
        var grop = $(this).find("option:selected").val();
        $("#type").find("div").remove();
        var token = $("input[name='_token']").val();
        $.ajax('/admin/menu/add/gropUser', {
            type: 'post'
            , async: false
            , data: {
                _token: token
                , grop: grop
            }
            , success: function(data1) {
                var data = jQuery.parseJSON(data1);
                $("select[name='gropUser[]']").empty();

                $.each(data, function(index, value) {
                    $("select[name='gropUser[]']").append('<option value="' + value["id"] + '">' + value["name"] + '</option>')
                })
            }
        })
    })


    /* START بررسی فیلدهای جریمه */
    function check_penalty_fields() {
        var daily_fine_amount = jQuery('#daily-fine-amount'), //مبلغ جریمه روزانه
            value_daily_fine_amount = daily_fine_amount.val()
            , fixed_penalty_amount = jQuery('#fixed-penalty-amount'), //مبلغ جریمه ثابت
            value_fixed_penalty_amount = fixed_penalty_amount.val();

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
@endsection
