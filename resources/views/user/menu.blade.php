@extends('theme.user')
@section('container')
    @php
        $explode_url = explode(DIRECTORY_SEPARATOR, $_SERVER['REQUEST_URI']);
        $_id_under_menu = count($explode_url) == 5 && is_numeric($explode_url[4]) ? $explode_url[4] : ''; //آیدی زیرمنو
        $prefillData = $prefillData ?? [];
    @endphp

    <style>
        .blog-single-content {
            color: #44474c;
            line-height: 2.2
        }

        .blog-single-content img {
            display: block;
            vertical-align: top;
            margin: 5px auto;
            cursor: pointer;
            position: relative;
            max-width: 100%
        }

        .blog-single-content h2,
        .blog-single-content h3,
        .blog-single-content h4,
        .blog-single-content h5,
        .blog-single-content h6,
        .blog-single-content p {
            text-align: right
        }

        .blog-single-content p {
            margin: 0 0 1.5em;
            padding: 0;
            font-weight: 300 !important;
            font-size: 16px
        }

        .blog-single-content h3,
        .blog-single-content h3 a {
            border-bottom: 1px solid #f0f1f2;
            color: #4b515a;
            display: inline-block;
            line-height: 2em;
            font-weight: 501 !important;
            margin: 0 0 .5rem;
            padding: 0 0 .2rem;
            font-size: 1.5rem !important
        }

        .blog-single-content h3 a,
        .blog-single-content h2 a {
            color: #107abe !important
        }

        .blog-single-content h2,
        .blog-single-content h2 a {
            color: #4b515a;
            font-weight: 600 !important;
            font-size: 1.75rem !important;
            margin-bottom: 0;
            line-height: 2em
        }

        .height-77px {
            height: 77px
        }

        .title {
            background-color: rgb(249, 249, 249);
            padding: 3px 7px
        }

        .title h1 {
            font-size: 15px;
            color: rgb(0, 0, 0);
            font-weight: normal
        }

        /* Bug 4: standardise form-field wrapper heights so Rial/text fields align */
        .form-field-col .form-control {
            height: calc(1.5em + .75rem + 2px);
        }
        .form-field-col textarea.form-control {
            height: auto;
        }
    </style>
    {{-- Alpine.js removed in favor of standard bootstrap tooltips --}}

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10  mb-3">
                <div class="card-box blog-single-content ">
                    @if (isset($form))
                        <div class="col-12">
                            @if (session()->has('message'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message') }}
                                </div>
                            @endif
                            <div class="bg-success p-3 px-sm-4">
                                <h1 class="text-white my-0 font-weight-bold h6">{{ $form['name'] }}</h1>
                            </div>
                            <form method="post" action="/user/form/{{ $form['hash'] }}" role="form"
                                enctype="multipart/form-data" class="menu-with-form">
                                @csrf
                                <div class="row">
                                    @foreach ($form['fild'] as $fild)
                                        @php
                                            if (!is_array($fild) || !array_key_exists('visible', $fild)) {
                                                continue;
                                            }
                                        @endphp

                                        @if ($fild['type'] == 1 or $fild['type'] == 2 or $fild['type'] == 3 || $fild['type'] == 7)
                                            {{-- Bug 4: form-field-col standardises control height --}}
                                            <div class="col-lg-6 mt-3 position-relative form-field-col">
                                                {{-- Bug 1: tooltip icon right of label, above input --}}
                                                <div class="d-inline-flex align-items-center mb-1">
                                                    <label class="mb-0">{{ $fild['title'] }}</label>
                                                    @if (isset($fild['description']))
                                                        <i class="fa fa-question-circle text-muted ml-1" style="cursor:pointer; font-size:14px; vertical-align:middle;" data-toggle="tooltip" data-placement="top" title="{{ $fild['description'] }}"></i>
                                                    @endif
                                                </div>
                                                <input name="{{ $fild['name'] }}"
                                                    @if ($fild['type'] == 1) type="text" @endif
                                                    @if ($fild['type'] == 2) type="email" @endif
                                                    @if ($fild['type'] == 3) type="@if (!array_key_exists('separator', $fild)){{ 'number' }}@else{{ 'text' }} @endif"
                                                    @endif
                                                @if ($fild['type'] == 7) type="file" @endif
                                                @php
$classes = array('text-primary');

                                if($fild['checkbox']) $classes []= 'required';

                                if($fild['type'] == 3):
                                  if(array_key_exists('unique', $fild)) $classes []= 'unique';
                                  if(array_key_exists('separator', $fild)) $classes []= 'separator';
                                endif;

                                if(array_key_exists('validation-based', $fild)):
                                  if($fild['validation-based'] == 'national-code'):
                                   $classes []= 'validation-based-national-code';
                                  else:
                                  $classes []= 'validation-based-date';
                                  endif;
                                endif @endphp
                                                {{-- Bug 6: source_form_id cross-form prefill (takes priority over auto-complete) --}}
                                                @if (isset($fild['source_form_id']) && isset($info_user_forms_used[$fild['source_form_id']]))
                                                    @php $_src_field = $fild['source_field'] ?? $fild['name']; @endphp
                                                    @if (isset($info_user_forms_used[$fild['source_form_id']][$_src_field]))
                                                        value="{{ $info_user_forms_used[$fild['source_form_id']][$_src_field] }}"
                                                    @endif
                                                @elseif (array_key_exists('auto-complete', $fild)) @if ($fild['auto-complete']['Type'] == 'Form' && array_key_exists($fild['auto-complete']['ID'], $info_user_forms_used))
                                  @if (array_key_exists($fild['auto-complete']['Field'], $info_user_forms_used[$fild['auto-complete']['ID']]))
                                    value="{{ $info_user_forms_used[$fild['auto-complete']['ID']][$fild['auto-complete']['Field']] }}" @endif
                                            @elseif(
                                                $fild['auto-complete']['Type'] == 'User' &&
                                                    isset($settings_auto_completes_form['user']) &&
                                                    array_key_exists($fild['auto-complete']['Field'], $settings_auto_completes_form['user']))
                                                @php
$_ac_field = $fild['auto-complete']['Field'];
$_user_col_map = ['mobile'=>'mobile','name'=>'name','name_2'=>'name2','national_code'=>'kod','hash'=>'hash','date_subscription'=>'date_subscription','name_grop'=>null];
if ($_ac_field === 'name_grop') {
    $_ac_val = session('name_grop_user', '');
} elseif (isset($user_data) && isset($_user_col_map[$_ac_field]) && $_user_col_map[$_ac_field] !== null) {
    $_ac_val = $user_data->{$_user_col_map[$_ac_field]} ?? '';
} else {
    $_ac_val = session($_ac_field . '_user', '');
}
@endphp
                                                value="{{ $_ac_val }}"
                                            @elseif(
                                                $fild['auto-complete']['Type'] == 'Invoice' &&
                                                    array_key_exists($fild['auto-complete']['Field'], $settings_auto_completes_form['invoice']))
                                                @php
$raw_price_invoices = isset($info_invoice['Price_Invoices']) ? (int)str_replace(',', '', $info_invoice['Price_Invoices']) : 0;
$raw_price_penalty  = isset($info_invoice['Price_Penalty'])  ? (int)str_replace(',', '', $info_invoice['Price_Penalty'])  : 0;
$raw_price_paid     = isset($info_invoice['Price_Paid'])     ? (int)str_replace(',', '', $info_invoice['Price_Paid'])     : 0;
$raw_final_balance  = $raw_price_invoices + $raw_price_penalty - $raw_price_paid;

$price_invoices = array_key_exists('separator', $fild) ? number_format($raw_price_invoices) : $raw_price_invoices;
$price_penalty  = array_key_exists('separator', $fild) ? number_format($raw_price_penalty)  : $raw_price_penalty;
$price_paid     = array_key_exists('separator', $fild) ? number_format($raw_price_paid)     : $raw_price_paid;
$final_balance  = array_key_exists('separator', $fild) ? number_format($raw_final_balance)  : $raw_final_balance;
@endphp
                                                @if ($fild['auto-complete']['Field'] == 'price-invoices') value="{{ $price_invoices }}"
                                  @elseif($fild['auto-complete']['Field'] == 'price-fines')
                                    value="{{ $price_penalty }}"
                                  @elseif($fild['auto-complete']['Field'] == 'price-paid')
                                    value="{{ $price_paid }}"
                                  @elseif($fild['auto-complete']['Field'] == 'final-debt-balance')
                                    value="{{ $final_balance }}"
                                        @endif
                                    @endif

                    @endif
                    @if (isset($fild['value'])) value="{{ $fild['value'] }}" @elseif(isset($prefillData[$fild['name']])) value="{{ $prefillData[$fild['name']] }}" @endif
                    class="form-control {{ implode(' ', $classes) }}" placeholder="{{ $fild['title'] }} را وارد کنید"
                    @if (!array_key_exists('editable', $fild)) {{ 'readonly' }} @endif
                    @if ($fild['checkbox']) {{ 'required' }} @endif
                    @if ($fild['type'] == 7) accept="{{ implode(',', $formats_allowed_file) }}" @endif>

                </div>
            @elseif($fild['type'] == 8)
                {{-- Bug 4: form-field-col --}}
                <div class="col-lg-6 mt-3 position-relative form-field-col">
                    {{-- Bug 1: tooltip above field --}}
                    <div class="d-inline-flex align-items-center mb-1">
                        <label class="mb-0">{{ $fild['title'] }}</label>
                        @if (isset($fild['description']))
                            <span x-data="{ open: false }" class="position-relative ml-1" style="display:inline-block; font-size:11px; vertical-align:middle; line-height:1;">
                                <i class="fa fa-question-circle text-muted" style="cursor:pointer;" @mouseenter="open = true" @mouseleave="open = false"></i>
                                <div x-show="open" x-cloak class="bg-dark text-white rounded p-2" style="position:absolute; bottom:120%; right:0; font-size:12px; z-index:500; min-width:150px; line-height:1.5; pointer-events:none; white-space:normal;">
                                    {{ $fild['description'] }}
                                </div>
                            </span>
                        @endif
                    </div>
                    {{-- Bug 2: rows min 6, resize vertical, no maxlength --}}
                    <textarea name="{{ $fild['name'] }}"
                        class="form-control @if ($fild['checkbox']) required text-primary @endif"
                        rows="6"
                        style="resize: vertical;"
                        @if (!array_key_exists('editable', $fild)) {{ 'readonly' }} @endif
                        @if ($fild['checkbox']) {{ 'required' }} @endif>{{ isset($fild['value']) ? $fild['value'] : '' }}</textarea>
                </div>
            @elseif($fild['type'] == 5)
                <div class="col-lg-6 mt-3 position-relative height-77px">
                    <div class="checkbox checkbox_input">
                        <input name="{{ $fild['name'] }}" id="checkbox{{ $fild['name'] }}" type="checkbox"
                            style="opacity: 1 !important; position: static !important; width: 20px; height: 20px; margin-left: 10px;"
                            @if (!array_key_exists('editable', $fild)) {{ 'readonly' }} @endif
                            @if ($fild['checkbox']) class="required text-primary" @endif />
                        {{-- Bug 1: label + tooltip icon inline --}}
                        <label for="checkbox{{ $fild['name'] }}" class="mb-0 d-inline">{{ $fild['title'] }}</label>
                        @if (isset($fild['description']))
                            <span x-data="{ open: false }" class="position-relative ml-1" style="display:inline-block; font-size:11px; vertical-align:middle; line-height:1;">
                                <i class="fa fa-question-circle text-muted" style="cursor:pointer;" @mouseenter="open = true" @mouseleave="open = false"></i>
                                <div x-show="open" x-cloak class="bg-dark text-white rounded p-2" style="position:absolute; bottom:120%; right:0; font-size:12px; z-index:500; min-width:150px; line-height:1.5; pointer-events:none; white-space:normal;">
                                    {{ $fild['description'] }}
                                </div>
                            </span>
                        @endif
                    </div>
                </div>
            @elseif($fild['type'] == 4)
                {{-- Bug 4: form-field-col --}}
                <div class="col-lg-6 mt-3 position-relative form-field-col">
                    {{-- Bug 1: tooltip above field --}}
                    <div class="d-inline-flex align-items-center mb-1">
                        <label for="inputEmail3" class="mb-0">{{ $fild['title'] }}</label>
                        @if (isset($fild['description']))
                            <span x-data="{ open: false }" class="position-relative ml-1" style="display:inline-block; font-size:11px; vertical-align:middle; line-height:1;">
                                <i class="fa fa-question-circle text-muted" style="cursor:pointer;" @mouseenter="open = true" @mouseleave="open = false"></i>
                                <div x-show="open" x-cloak class="bg-dark text-white rounded p-2" style="position:absolute; bottom:120%; right:0; font-size:12px; z-index:500; min-width:150px; line-height:1.5; pointer-events:none; white-space:normal;">
                                    {{ $fild['description'] }}
                                </div>
                            </span>
                        @endif
                    </div>
                    {{-- Bug 3: self-closing option tag was breaking the select; fixed below --}}
                    <select id="inputEmail34" name="{{ $fild['name'] }}"
                        class="form-control @if ($fild['checkbox']) {{ 'required' }} @endif">
                        <option selected="selected" disabled="disabled" value="0">@if (array_key_exists('editable', $fild))انتخاب کنید@elseغیر قابل انتخاب@endif</option>
                        @foreach ($fild['itme'] as $itme)
                            <option value="{{ $itme['title'] }}"
                                @if (!array_key_exists('editable', $fild)) {{ 'disabled="disabled"' }} @endif>{{ $itme['title'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @elseif($fild['type'] == 6)
                <div class="col-lg-6 mt-3 position-relative">
                    {{-- Bug 1: tooltip to the right of the group label --}}
                    <div class="d-inline-flex align-items-center mb-1">
                        <label class="d-block mb-0">{{ $fild['title'] }}</label>
                        @if (isset($fild['description']))
                            <span x-data="{ open: false }" class="position-relative ml-1" style="display:inline-block; font-size:11px; vertical-align:middle; line-height:1;">
                                <i class="fa fa-question-circle text-muted" style="cursor:pointer;" @mouseenter="open = true" @mouseleave="open = false"></i>
                                <div x-show="open" x-cloak class="bg-dark text-white rounded p-2" style="position:absolute; bottom:120%; right:0; font-size:12px; z-index:500; min-width:150px; line-height:1.5; pointer-events:none; white-space:normal;">
                                    {{ $fild['description'] }}
                                </div>
                            </span>
                        @endif
                    </div>
                    @foreach ($fild['itme'] as $number => $itme)
                        @php $checked = !isset($checked) ? 'checked' : '' @endphp
                        <div class="checkbox d-inline">
                            <div class="radio radio-info form-check-inline">
                                <input type="radio" id="inlineRadio_{{ $fild['name'] }}_{{ $number }}"
                                    value="{{ $itme['title'] }}" name="{{ $fild['name'] }}"
                                    @if (!array_key_exists('editable', $fild)) {{ 'readonly' }} @endif
                                    @if ($fild['checkbox']) {{ $checked }} class="required text-primary" required @endif>
                                <label for="inlineRadio_{{ $fild['name'] }}_{{ $number }}">{{ $itme['title'] }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                @endforeach

                @if (!$form['saving_disabled'])
                    <div class="col-12 mt-4 text-left">
                        <input type="submit" class="btn btn-success" value="ثبت">
                    </div>
                @endif
            </div>
            </form>
        </div>
        @endif
        @php echo htmlspecialchars_decode($menu['text']) @endphp
    </div>
    </div>
    </div>
    </div>

    <script>
        /* START جدا کننده اعداد از هم */
        function number_format(number) {
            var output = new Array(),
                number_string = number.toString();

            for (var counter_number = 0, length_number_string = number_string.length; counter_number <
                length_number_string; counter_number += 1) output.push(+number_string.charAt(counter_number));

            for (var counter_number = 3; counter_number <= Math.ceil(output.length / 3 * 2); counter_number++) {
                output[output.length - counter_number] = ',' + output[output.length - counter_number];
                counter_number = counter_number * 2 - 1;
            }

            return output.join('');
        }
        /* END جدا کننده اعداد از هم */


        /* START اعتبارسنجی */
        function add_element_error(element, element_error) {
            if (element_error.length > 0) return
            element.after('<ul class="parsley-errors-list"></ul>');
            return true
        }


        /* START بررسی خالی نبودن فیلدهای اجباری */
        function check_not_empty_fields_required() {
            var element = $(this),
                type = element.attr('type'),
                parent = element.parent();

            if (
                type == 'checkbox' //چک باکس
                ||
                type == 'radio' //رادیو
            ) {
                //ساخت و خالی کردن پیام خطا
                var parent_parent = type == 'checkbox' ? parent : parent.parent().parent(),
                    element_error = parent_parent.find('.parsley-errors-list')
                if (!add_element_error(parent_parent.find('> :last-child'), element_error)) element_error.find(
                    '.error-empty').remove()
                element_error = parent_parent.find('.parsley-errors-list')

                if (type == 'radio') element = $('[name="' + $(this).attr('name') + '"]')
                if (!element.is(':checked')) element_error.html((type == 'checkbox') ?
                    '<li class="error-empty">لطفاً انتخاب کنید</li>' :
                    '<li class="error-empty">لطفاً یکی را انتخاب کنید</li>').show()

                return
            }

            var value = element.val(),
                element_error = parent.find('.parsley-errors-list')

            if (
                //سلکت باکس
                element.hasClass('select2') &&
                add_element_error(parent.find('.select2-container'), element_error)
            ) {
                element_error.empty()
            } else if (!add_element_error(element, element_error)) {
                element_error.find('.error-empty').remove()
            }

            if (
                typeof value == 'string' &&
                value != ''
            ) return

            parent.find('.parsley-errors-list').html('<li class="error-empty">این مقدار لازم است.</li>').show()
        }
        $('.required').on('input click change keyup keydown keypress', check_not_empty_fields_required)
        /* END بررسی خالی نبودن فیلدهای اجباری */


        /* START فایل */
        $('[type="file"]').change(function() {
                    if (!window.File || !window.FileReader || !window.FileList || !window.Blob) return

                    var element = $(this),
                        element_error = element.parent().find('.parsley-errors-list')
                    if (!add_element_error(element, element_error)) element_error.find('.error-format-file').remove()

                    //اطلاعات فایل انتخاب شده
                    info_file = $(this).prop('files')
                    if (info_file.length != 1) return
                    info_file = info_file[0]

                    //بررسی حجم
                    if (info_file.size <= 0) element_error.append(
                        '<li class="error-format-file">فایل انتخاب شده خالی است.</li>').show();
                    if (info_file.size > 3000000) element_error.append(
                        '<li class="error-format-file">حجم فایل نهایت میتواند 3 مگابایت باشد.</li>').show();
                    try {
                        //بررسی فرمت
                        var formats_allowed_file = jQuery.parseJSON(
                            '<?= isset($formats_allowed_file) ? json_encode($formats_allowed_file) : json_encode([]) ?>'
                            )
                        if (formats_allowed_file.indexOf(info_file.type) == -1) element_error.append(
                            '<li class="error-format-file">فرمت فایل معتبر نیست.</li>').show()
                    } catch(e) {}
                })

                /* END فایل */


                /* START کد ملی */
                $('.validation-based-national-code').on('input change keyup keydown keypress', function() {
                    var element = $(this),
                        value = element.val(),
                        element_error = element.parent().find('.parsley-errors-list')
                    if (!add_element_error(element, element_error)) element_error.find(
                        '.error-validation-based-national-code').remove()

                    if (value == '') {
                        return
                    } else if (!jQuery.isNumeric(value.replace(/,/g, ''))) {
                        element_error.append(
                                '<li class="error-validation-based-national-code">لطفاً عدد وارد نمایید.</li>')
                            .show()
                    } else if (value.length != 10) {
                        element_error.append('<li class="error-validation-based-national-code">باید 10 رقم باشد.</li>')
                            .show()
                    } else {
                        value = value.toString()
                        if (value.split('')[0] == 0) return false

                        var positionNumber = 10,
                            result = 0

                        jQuery.each(value.substring(0, value.length - 1).split(''), function(key,
                            number_national_code) { //تک تک اعداد بجز عدد آخر
                            result += number_national_code * positionNumber;
                            positionNumber--;
                        })

                        var remain = result % 11,
                            controllerNumber = remain
                        if (remain >= 2) controllerNumber = 11 - remain

                        if (value.substring(value.length - 1) != controllerNumber) element_error.append(
                            '<li class="error-validation-based-national-code">صحیح نیست و باید بر اساس کد ملی باشد.</li>'
                        ).show()
                    }
                })
                /* END کد ملی */


                /* START تاریخ */
                $('.validation-based-date').on('input change keyup keydown keypress', function() {
                    var element = $(this),
                        element_error = element.parent().find('.parsley-errors-list')
                    if (!add_element_error(element, element_error)) element_error.find('.error-validation-based-date')
                        .remove()

                    var value = element.val()
                    if (value && !/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/.test(value)) element_error
                        .append(
                            '<li class="error-validation-based-date">فرمت نادرست است. فرمت نمونه صحیح: 02-11-1400</li>')
                        .show()
                })
                /* END تاریخ */
                /* END اعتبارسنجی */


                /* START بررسی یکتا */
                var timeout_unique,
                    ajax_unique; $('.unique').on('input keyup keydown keypress', function() {
                    clearTimeout(timeout_unique);

                    if (typeof ajax_unique !== 'undefined') ajax_unique.abort()

                    var element = $(this)

                    timeout_unique = setTimeout(function() {
                        var parent = element.parent(),
                            value = element.val(),
                            element_error = parent.find('.parsley-errors-list')
                        if (!add_element_error(element, element_error)) element_error.find('.error-unique')
                            .remove()
                        if (!value) return

                        element.addClass('pl-5');

                        //لودینگ
                        parent.find('.fa').remove();
                        element.after(
                            '<i class="fa fa-spinner fa-pulse position-absolute" style="font-size: 25px; top: 46px; left: 21px"></i>'
                        )

                        function send_ajax_unique() {
                            $.ajax({
                                url: '/user/form/check/{{ $_id_under_menu }}',
                                data: 'value=' + value,
                                success: function(check_available) {
                                    element.removeClass('pl-5');
                                    parent.find('.fa').remove() //حذف لودینگ

                                    if (check_available == 'Available') element_error.append(
                                            '<li class="error-unique">مقدار وارد شده تکراری است.</li>'
                                            )
                                        .show()
                                },
                                error: function(ajax_error_jqXHR, ajax_error_text_status) {
                                    if (
                                        ajax_error_text_status != "abort" &&
                                        ajax_error_jqXHR.status != 500
                                    ) {
                                        ajax_unique = send_ajax_unique()
                                    }
                                },
                                timeout: 5000
                            });
                        }
                        ajax_unique = send_ajax_unique()
                    }, 500)
                })
                /* END بررسی یکتا */


                /* START جدا کننده اعداد از هم */
                $('.separator').on('input keyup keydown keypress', function() {
                    var value = $(this).val().replace(/\D/g, '')
                    if (value != '') $(this).val(number_format(value))
                })
                /* END جدا کننده اعداد از هم */


                /* START ارسال فرم بعد اعتبارسنجی */
                function validation_fields_and_send(event) {
                    jQuery('.required').each(check_not_empty_fields_required)
                    if ($('.parsley-errors-list li').length == 0) return

                    event.preventDefault();
                    event.stopPropagation();
                    return false
                }
                $('button').click(validation_fields_and_send) $('form').submit(validation_fields_and_send)
                /* END ارسال فرم بعد اعتبارسنجی */
    </script>
@endsection
