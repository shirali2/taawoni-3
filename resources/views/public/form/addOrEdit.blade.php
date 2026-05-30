@php $role = session()->has('admin') ? 'admin' : 'manager' @endphp
@section("container")
<style>
    .cord_add_input {
        border: 1px solid #9999;
        padding-top: 15px
    }

    .cord_add_input2 {
        border-top: 1px solid #9999;
        padding-top: 15px
    }

    .btn_close_input {
        height: 28px;
        padding: 0px 8px;
        position: absolute;
        top: 0px;
        left: 0
    }

    .badge_input {
        position: absolute;
        top: -14px;
        padding: 7px;
        font-size: 11px;
        right: -1px
    }

    @if($role=='admin') .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #71b6f9;
        -webkit-border: unset;
        -moz-border: unset;
        -ms-border: unset;
        -ms-ie-border: unset;
        -o-border: unset;
        -khtml-border: unset;
        border: unset;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        -ms-border-radius: 3px;
        -ms-ie-border-radius: 3px;
        -o-border-radius: 3px;
        -khtml-border-radius: 3px;
        border-radius: 3px;
        padding-right: 7px;
        padding-left: 7px
    }

    @endif

</style>

<div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;"></div>

<link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="{{ asset('libs/jalaali/jalaali.js') }}"></script>

<!-- PersianDate Library -->
<script src="{{ asset('libs/jquery/jquery-3.6.0.min.js') }}"></script>
<!-- Bootstrap JS and Popper.js -->
<script src="{{ asset('libs/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- MD.BootstrapPersianDateTimePicker JS -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 mb-3">
            <div class="card-box ">
                <div class="m-t-0 m-b-30">
                    <h4 class="header-title d-inline-block ">ایجاد فرم</h4>
                    <a href="/{{$role}}/form" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    @if(isset($info_form))
                    <form method="post" class="d-inline" action="/{{$role}}/form/copy/{{$info_form['id']}}">
                        @csrf

                        <button id="copy" type="submit" class="float-right btn btn-primary">کپی</button>
                    </form>
                    @endif
                </div>

                <form class="form-horizontal m-t-20" id="date-form" method="post" action="/{{$role}}/form/@if(!isset($info_form)){{'add'}}@else{{'edit/' . $info_form['id']}}@endif">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-9">
                                    <select name="type" class="form-control">
                                        @foreach($types_fields_form as $key_type_fields_form => $type_fields_form)
                                        <option value="{{$key_type_fields_form}}">{{$type_fields_form}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3">
                                    <button id="add_input" type="button" class="float-right btn btn-success">افزودن</button>
                                </div>

                                <div class="col-lg-6 mt-3">
                                    <label>نام فرم</label>
                                    <input parsley-trigger="change" required name="name_form" type="text" class="form-control" placeholder="نام فرم را وارد کنید" value="@if(isset($info_form)){{$info_form['name']}}@endif" />
                                </div>

                                <div class="col-lg-6 mt-3">
                                    <label>توضیحات</label>
                                    <input parsley-trigger="change" required name="text_form" type="text" class="form-control" placeholder="توضیحات فرم را وارد کنید" value="@if(isset($info_form)){{$info_form['text']}}@endif" />
                                </div>

                                <div class="col-lg-6 mt-6">
                                    <label>حداکثر تعداد مجاز ثبت فرم</label>
                                    <input type="number" parsley-trigger="change" name="fill_form_limitation" type="text" class="form-control" placeholder="حداکثر تعداد را وارد کنید" value="{{isset($info_form)?$info_form['fill_form_limitation']:""}}" />
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="checkbox col-sm-4 float-left">
                                        <input type="checkbox" name="required" id="required" @if(isset($info_form) && $info_form['required']){{'checked="checked"'}}@endif />
                                        <label for="required">اجباری</label>
                                    </div>
                                    <div class="checkbox col-sm-4 float-left">
                                        <input type="checkbox" name="show_onuserpanel" id="show_onuserpanel" @if(isset($info_form) && $info_form['show_onuserpanel']){{'checked="checked"'}}@endif />
                                        <label for="show_onuserpanel">نمایش در پنل کاربری</label>
                                    </div>
                                    <div class="checkbox col-sm-4 float-left">
                                        <input type="checkbox" name="saving_disabled" id="saving_disabled" @if(isset($info_form) && $info_form['saving_disabled']){{'checked="checked"'}}@endif />
                                        <label for="saving_disabled">غیرفعال بودن ثبت</label>
                                    </div>
                                    <div class="checkbox col-sm-4 float-left">
                                        <input type="checkbox" name="confirm" id="confirm" @if(isset($info_form) && $info_form['confirm']){{'checked="checked"'}}@endif />
                                        <label for="confirm">تایید کلی</label>
                                    </div>
                                    <div class="checkbox col-sm-4 float-left">
                                        <input type="checkbox" name="notify_on_submit" id="notify_on_submit" value="1" @if(isset($info_form) && $info_form['notify_on_submit']){{'checked="checked"'}}@endif />
                                        <label for="notify_on_submit">ارسال پیامک هنگام ثبت فرم توسط کاربر</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-2" id="notify_phone_group" style="{{ isset($info_form) && $info_form['notify_on_submit'] ? '' : 'display:none' }}">
                                    <label>شماره اطلاع‌رسانی فرم (اگر خالی باشد از تنظیمات مجموعه استفاده می‌شود):</label>
                                    <input type="text" name="notify_phone" class="form-control" value="{{ isset($info_form) ? $info_form['notify_phone'] : '' }}">
                                </div>
                            </div>
                            <script>
                            document.getElementById('notify_on_submit').addEventListener('change', function() {
                                document.getElementById('notify_phone_group').style.display = this.checked ? '' : 'none';
                            });
                            </script>

                            @if($role == 'admin')
                            <div class="col-lg-12 mt-3">
                                <label>انتخاب مجموعه</label>
                                <select name="grops[]" class="form-control select2 select2-multiple" multiple="multiple" data-placeholder="انتخاب مجموعه یا مجموعه ها" required>
                                    @foreach($grops as $grop)
                                    <option value="{{$grop['id']}}" @if(isset($info_form) && in_array($grop['id'], $info_form['id_grop'])){{' selected="selected"'}}@endif>{{$grop['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-12 mt-4" id="div_input">
                                <input type="hidden" name="number" value="{{ isset($info_form) ? count($info_form['fild']) : 0 }}" />
                                @if(isset($info_form))

                                @foreach($info_form['fild'] as $key_field_available => $field_available)
                                <div id="row_input_{{$key_field_available}}" class="row cord_add_input position-relative mb-3">
                                    <span class="badge badge-purple badge_input">{{$types_fields_form[$field_available['type']]}}</span>
                                    <input type="hidden" name="type{{$key_field_available}}" value="{{$field_available['type']}}" class="d-none" />
                                    @if($field_available['type']==8)
                                    <div class="col-lg-12">
                                        <label>عنوان : </label>
                                        <textarea name="title{{$key_field_available}}" class="form-control" required>{{$field_available['title']}}</textarea>
                                    </div>
                                    @else
                                    <div class="col-lg-6">
                                        <label>عنوان : </label>
                                        <input type="text" name="title{{$key_field_available}}" value="{{$field_available['title']}}" class="form-control" required />
                                    </div>
                                    @endif

                                    <div class="col-lg-6 mb-3">
                                        <label>نام به انگلیسی : </label>
                                        <input type="text" name="name{{$key_field_available}}" value="{{$field_available['name']}}" class="form-control" required />
                                    </div>

                                    @if($field_available['type'] == 1 || $field_available['type'] == 2 ||
                                    $field_available['type'] == 3 || $field_available['type'] == 5 ||
                                    $field_available['type'] == 7 || $field_available['type'] == 8)
                                    @if($field_available['type'] == 1 || $field_available['type'] == 3)
                                    <div class="col-12 mb-3">
                                        <label>اعتبارسنجی بر اساس</label>
                                        <select name="validation-based{{$key_field_available}}" class="form-control select2">
                                            @if(!array_key_exists('validation-based', $field_available))
                                            <option disabled="disabled" selected="selected">انتخاب اعتبارسنجی</option>
                                            @else
                                            <option>غیرفعال</option>
                                            @endif

                                            @foreach($items_validation as $slug_item_validation => $name_item_validation)
                                            <option value="{{$slug_item_validation}}" @if(array_key_exists('validation-based', $field_available) && $field_available['validation-based']==$slug_item_validation){{' selected="selected"'}}@endif>{{$name_item_validation}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    @if($field_available['type'] == 1 || $field_available['type'] == 2 || $field_available['type'] == 3)
                                    @php
                                    $value_auto_complete_field_available = array_key_exists('auto-complete', $field_available) ? $field_available['auto-complete'] : array(); //تکمیل خودکار بر اساس
                                    $type_auto_complete_field_available = array_key_exists('Type', $value_auto_complete_field_available) ? strtolower($value_auto_complete_field_available['Type']) : ''; //نوع
                                    $field_auto_complete_field_available = array_key_exists('Field', $value_auto_complete_field_available) ? strtolower($value_auto_complete_field_available['Field']) : '' //فیلد
                                    @endphp
                                    <div class="col-lg-6 mb-3">
                                        <label>مقدار پیشفرض</label>
                                        <input type="text" name="value{{$key_field_available}}" class="form-control" value="{{ isset($field_available['value']) ? $field_available['value'] : '' }}" />
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label>توضیحات</label>
                                        <input type="text" name="description{{$key_field_available}}" value="{{ isset($field_available['description']) ? $field_available['description'] : '' }}" class="form-control" />
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label>تکمیل خودکار بر اساس</label>
                                        <select name="auto-complete{{$key_field_available}}" id="auto-complete" class="form-control select2" data-number-special="{{$key_field_available}}">
                                            @if(!array_key_exists('auto-complete', $field_available))
                                            <option disabled="disabled" selected="selected">انتخاب</option>
                                            @else
                                            <option>غیرفعال</option>
                                            @endif

                                            @foreach($items_auto_complete as $slug_item_auto_complete => $name_item_auto_complete)
                                            <option value="{{$slug_item_auto_complete}}" @if($type_auto_complete_field_available==$slug_item_auto_complete){{' selected="selected"'}}@endif>{{$name_item_auto_complete}}</option>
                                            @endforeach
                                        </select>

                                        <div class="settings-value-auto-complete col-12 float-left mt-2 mb-4">
                                            @if($type_auto_complete_field_available == 'form')
                                            @php if(!array_key_exists('ID', $value_auto_complete_field_available)) continue @endphp

                                            <div class="col-12 col-sm-6 float-left">
                                                <input type="number" name="id-form-{{$key_field_available}}" placeholder="آیدی" value="{{$value_auto_complete_field_available['ID']}}" id="id-form" class="form-control" data-number-special="{{$key_field_available}}" />
                                            </div>

                                            @if(array_key_exists($value_auto_complete_field_available['ID'], $list_id_and_field_auto_complete_form))
                                            <div class="col-12 col-sm-6 mt-2 mt-sm-0 float-left">
                                                <select name="field-form-{{$key_field_available}}" class="form-control">
                                                    @if(!array_key_exists('auto-complete', $field_available))
                                                    <option disabled="disabled" selected="selected">انتخاب فیلد</option>
                                                    @else
                                                    <option>غیرفعال</option>
                                                    @endif

                                                    @foreach($list_id_and_field_auto_complete_form[$value_auto_complete_field_available['ID']] as $fields_auto_complete)
                                                    <option value="{{$fields_auto_complete['name']}}" @if($value_auto_complete_field_available['Field']==$fields_auto_complete['name']){{' selected="selected"'}}@endif>{{$fields_auto_complete['title']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            @elseif($type_auto_complete_field_available == 'user')
                                            <select name="info-user-{{$key_field_available}}" class="form-control">
                                                <option disabled="disabled" @if(!$field_auto_complete_field_available){{'selected="selected"'}}@endif>انتخاب فیلد</option>
                                                @foreach($settings_auto_completes_form['user'] as $slug_setting_value_auto_complete => $name_setting_value_auto_complete)
                                                <option value="{{$slug_setting_value_auto_complete}}" @if($value_auto_complete_field_available['Field']==$slug_setting_value_auto_complete){{' selected="selected"'}}@endif>{{$name_setting_value_auto_complete}}</option>
                                                @endforeach
                                            </select>
                                            @elseif($type_auto_complete_field_available == 'invoice')
                                            <select name="info-invoice-{{$key_field_available}}" class="form-control">
                                                <option disabled="disabled" @if(!$field_auto_complete_field_available){{'selected="selected"'}}@endif>انتخاب مورد</option>
                                                @foreach($settings_auto_completes_form['invoice'] as $slug_setting_value_auto_complete => $name_setting_value_auto_complete)
                                                <option value="{{$slug_setting_value_auto_complete}}" @if($value_auto_complete_field_available['Field']==$slug_setting_value_auto_complete){{' selected="selected"'}}@endif>{{$name_setting_value_auto_complete}}</option>
                                                @endforeach
                                            </select>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    <div class="col-12 mb-3">
                                        <div class="checkbox col-12 col-sm-6 float-left">
                                            <input id="checkbox{{$key_field_available}}" name="checkbox{{$key_field_available}}" type="checkbox" @if(isset($field_available['checkbox']) && $field_available['checkbox']==1){{'checked="checked"'}}@endif />
                                            <label for="checkbox{{$key_field_available}}">اجباری</label>
                                        </div>

                                        @if($field_available['type'] == 3)
                                        <div class="checkbox col-12 col-sm-6 float-left">
                                            <input type="checkbox" name="unique{{$key_field_available}}" @if(array_key_exists('unique', $field_available)){{'checked="checked"'}}@endif id="unique{{$key_field_available}}" />
                                            <label for="unique{{$key_field_available}}">یکتا</label>
                                        </div>

                                        <div class="checkbox col-12 col-sm-6 float-left">
                                            <input type="checkbox" name="separator{{$key_field_available}}" @if(array_key_exists('separator', $field_available)){{'checked="checked"'}}@endif id="separator{{$key_field_available}}" />
                                            <label for="separator{{$key_field_available}}">جدا کننده</label>
                                        </div>
                                        @endif

                                        @if($field_available['type'] == 4 || $field_available['type'] == 6)
                                    </div>

                                    <div class="col-12 cord_add_input2">
                                        <span class="badge badge-pink badge_input">گزینه‌ها</span>
                                        <button onclick="btn_add_input({{$key_field_available}})" type="button" class="btn_close_input btn btn-success" data-toggle="tooltip" data-original-title="افزودن گزینه"><i class="fa fa-plus"></i></button>

                                        <div class="row" id="row_input_add_{{$key_field_available}}">
                                            @if(array_key_exists('itme', $field_available))
                                            @foreach($field_available['itme'] as $counter_number => $items_field_available)
                                            <div class="col-lg-6 mb-3">
                                                <label>عنوان:</label>
                                                <input type="text" name="title_{{$key_field_available}}_{{$counter_number + 1}}" value="{{$items_field_available['title']}}" class="form-control" required />
                                            </div>
                                            @endforeach

                                            <input type="hidden" name="number_add_{{$key_field_available}}" value="{{count($field_available['itme'])}}" class="d-none" />
                                            @else
                                            <input type="hidden" name="number_add_{{$key_field_available}}" value="1" class="d-none" />
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <div class="checkbox col-12 col-sm-6 float-left">
                                        <input type="checkbox" name="visible{{$key_field_available}}" @if(array_key_exists('visible', $field_available)){{'checked="checked"'}}@endif id="visible{{$key_field_available}}" />
                                        <label for="visible{{$key_field_available}}">قابل مشاهده</label>
                                    </div>

                                    <div class="checkbox col-12 col-sm-6 float-left">
                                        <input type="checkbox" name="editable{{$key_field_available}}" @if(array_key_exists('editable', $field_available)){{'checked="checked"'}}@endif id="editable{{$key_field_available}}" />
                                        <label for="editable{{$key_field_available}}">قابل ویرایش</label>
                                    </div>

                                    @if($field_available['type'] == 1 || $field_available['type'] == 2 || $field_available['type'] == 3 || $field_available['type'] == 5 || $field_available['type'] == 7 || $field_available['type'] == 8)
                                </div>
                                @endif
                                <button onclick="moveRowUp({{$key_field_available}})" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Up"><i class="fa fa-arrow-up"></i></button>
                                <button onclick="moveRowDown({{$key_field_available}})" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Down"><i class="fa fa-arrow-down"></i></button>
                                <button type="button" onclick="btn_close_input({{$key_field_available}})" class="btn btn-danger btn_close_input" data-toggle="tooltip" data-original-title="حذف"><i class="fa fa-close"></i></button>
                            </div>
                            @endforeach
                            @else
                            <input type="hidden" name="number" value="0" />
                            @endif
                        </div>
                    </div>
            </div>
        </div>

        <div class="form-group text-center m-t-30">
            <div class="col-xs-12">
                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">ثبت فرم</button>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
</div>

<script>

</script>
<script>
    function btn_close_input(id) {
        $("#row_input_" + id).remove()
    }

    // Function to move a row up
    function moveRowUp(isd) {
        var $row = $('#row_input_' + isd);
        var $prevRow = $row.prev('.row');
        if ($prevRow.length) {
            $row.insertBefore($prevRow);
            updateInputNumbers();
        }
    }

    // Function to move a row down
    function moveRowDown(isd) {
        var $row = $('#row_input_' + isd);
        var $nextRow = $row.next('.row');
        if ($nextRow.length) {
            $row.insertAfter($nextRow);
            updateInputNumbers();
        }
    }

    // Function to update row IDs and associated input names
    function updateInputNumbers() {
        $('#div_input .row').each(function(index) {
            var currentNumber = index + 1;
            var oldId = $(this).attr('id').split('_')[2];
            $(this).attr('id', 'row_input_' + currentNumber);
            $(this).find('[name]').each(function() {
                var name = $(this).attr('name');
                $(this).attr('name', name.replace(oldId, currentNumber));
            });
            $(this).find('button').each(function() {
                var onclick = $(this).attr('onclick');
                if (onclick) {
                    $(this).attr('onclick', onclick.replace(oldId, currentNumber));
                }
            });
        });
    }

    // Function to handle adding new rows
    function btn_add_input(id) {
        var number = parseInt($('input[name="number_add_' + id + '"]').val()) + 1;
        $('input[name="number_add_' + id + '"]').val(number);
        $("#row_input_add_" + id).append('<div class="col-lg-6 mb-3 ">\n' +
            '                         <label for="exampleInputEmail1">عنوان : </label>\n' +
            '                         <input class="form-control" required="" name="title_' + id + '_' + number + '" type="text">\n' +
            '                       </div>');
    }


    $("#add_input").click(function() {
        var type = $("select[name='type']").find("option:selected").val()
            , type_text = $("select[name='type']").find("option:selected").text()
            , number = parseInt($("input[name='number']").val());

        if (type == 1 || type == 2 || type == 3 || type == 5 || type == 7 || type == 8) {
            $("input[name='number']").val(number + 1);
            var html_form_type = ' <div id="row_input_' + number + '" class="row cord_add_input position-relative mb-3">\n' +
                '                                                       <span class="badge badge-purple badge_input">' + type_text + '</span> \n' +
                '                                                       <input type="hidden" name="type' + number + '" value="' + type + '" class="d-none" />\n';
            if (type == 8) {
                html_form_type +=
                    '                                                   <div class="col-lg-12">\n' +
                    '                                                       <label for="exampleInputEmail1">عنوان : </label>\n' +
                    '                                                       <textarea required class="form-control" name="title' + number + '" cols="10"></textarea>\n' +
                    '                                                   </div>\n';
            } else {
                html_form_type +=
                    '                                                   <div class="col-lg-6">\n' +
                    '                                                       <label for="exampleInputEmail1">عنوان : </label>\n' +
                    '                                                       <input class="form-control" required name="title' + number + '" type="text" />\n' +
                    '                                                   </div>\n';
            }
            html_form_type +=
                '                                                   <div class="col-lg-6 mb-3">\n' +
                '                                                       <label for="exampleInputEmail1">نام به انگلیسی : </label>\n' +
                '                                                       <input class="form-control" required name="name' + number + '" type="text" />\n' +
                '                                                   </div>\n' + ((type == 1 || type == 3) ? '<div class="col-12 mb-3">\n' +
                    '<label>اعتبارسنجی بر اساس</label>\n' +
                    '<select name="validation-based' + number + '" class="form-control select2">\n' +
                    '<option disabled="disabled" selected="selected">انتخاب اعتبارسنجی</option>\n' +
                    @foreach($items_validation as $slug_item_validation => $name_item_validation)
                    '<option value="{{$slug_item_validation}}">{{$name_item_validation}}</option>\n' +
                    @endforeach


                    '</select>\n' +
                    '</div>\n' : '') +
                ' <div class="col-lg-6 mb-3">\n' +
                '<label>مقدار پیشفرض</label>\n' +
                '<input type="text" name="value' + number + '"  class="form-control"  />' +
                '</div>\n' +
                ' <div class="col-lg-6 mb-3">\n' +
                '<label>توضیحات</label>\n' +
                '<input type="text" name="description' + number + '"  class="form-control"  />' +
                '</div>\n' +


                ((type == 1 || type == 2 || type == 3) ? '<div class="col-12 mb-3">\n' +
                    '<label>تکمیل خودکار بر اساس</label>\n' +
                    '<select name="auto-complete' + number + '" id="auto-complete" class="form-control select2" data-number-special="' + number + '">\n' +
                    '<option disabled="disabled" selected="selected">انتخاب</option>\n' +
                    @foreach($items_auto_complete as $slug_item_auto_complete => $name_item_auto_complete)
                    '<option value="{{$slug_item_auto_complete}}">{{$name_item_auto_complete}}</option>\n' +
                    @endforeach '</select>\n' +
                    '</div>\n' : '') +
                '                                                   <div class="col-12 mb-3">\n' +
                '                                                       <div class="checkbox col-12 col-sm-6 float-left">\n' +
                '                                                           <input id="checkbox' + number + '" name="checkbox' + number + '" type="checkbox" />\n' +
                '                                                           <label for="checkbox' + number + '">\n' +
                '                                                               اجباری\n' +
                '                                                           </label>\n' +
                '                                                       </div>\n' + (type == 3 ? '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                    '<input type="checkbox" name="unique' + number + '" id="unique' + number + '" />\n' +
                    '<label for="unique' + number + '">یکتا</label>\n' +
                    '</div>\n' +
                    '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                    '<input type="checkbox" name="separator' + number + '" id="separator' + number + '" />\n' +
                    '<label for="separator' + number + '">جدا کننده</label>\n' +
                    '</div>\n' : '') +
                '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                '<input type="checkbox" name="visible' + number + '" checked="checked" id="visible' + number + '" />\n' +
                '<label for="visible' + number + '">قابل مشاهده</label>\n' +
                '</div>\n' +
                '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                '<input type="checkbox" name="editable' + number + '" checked="checked" id="editable' + number + '" />\n' +
                '<label for="editable' + number + '">قابل ویرایش</label>\n' +
                '</div>\n' +
                '                                                   </div>\n' +

                '    <button onclick="moveRowUp(' + number + ')" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Up"><i class="fa fa-arrow-up"></i></button>\n' +
                '    <button onclick="moveRowDown(' + number + ')" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Down"><i class="fa fa-arrow-down"></i></button>\n' +
                '                                               <button onclick="btn_close_input(' + number + ')" type="button" class="btn btn-danger btn_close_input"><i class="fa fa-close"></i></button>\n' +
                '                                           </div>';
            $("#div_input").append(html_form_type);
        } else if (type == 4 || type == 6) {
            $("input[name='number']").val(number + 1);
            $("#div_input").append(' <div id="row_input_' + number + '" class="row cord_add_input position-relative mb-3">\n' +
                '                                        <span class="badge badge-purple badge_input">' + type_text + '</span>\n' +
                '                                        <input class="form-control" value="' + type + '" name="type' + number + '" type="hidden" />\n' +
                '                                        <div class="col-lg-6 mb-3">\n' +
                '                                            <label for="exampleInputEmail1">عنوان : </label>\n' +
                '                                            <input class="form-control" required="" name="title' + number + '" type="text">\n' +
                '                                        </div>\n' +
                '                                        <div class="col-lg-6 mb-3">\n' +
                '                                            <label for="exampleInputEmail1">نام به انگلیسی : </label>\n' +
                '                                            <input class="form-control" required="" name="name' + number + '" type="text" />\n' +
                '                                        </div>\n' +
                '                                        <div class="col-lg-6 mb-3">\n' +
                '                                            <div class="checkbox">\n' +
                '                                                <input id="checkbox' + number + '" name="checkbox' + number + '" type="checkbox" />\n' +
                '                                                <label for="checkbox' + number + '">\n' +
                '                                                    اجباری\n' +
                '                                                </label>\n' +
                '                                            </div>\n' +
                '                                        </div>\n' +
                '                                        <div class="col-12 cord_add_input2">\n' +
                '                                            <input type="hidden" name="number_add_' + number + '" value="1" />\n' +
                '                                            <span class="badge badge-pink badge_input">گزینه‌ها</span>\n' +
                '                                            <button onclick="btn_add_input(' + number + ')" type="button" class="btn btn-success btn_close_input" data-toggle="tooltip" data-original-title="افزودن گزینه"><i class="fa fa-plus"></i></button>\n' +
                '\n' +
                '                                            <div class="row" id="row_input_add_' + number + '">\n' +
                '                                               <div class="col-lg-6 mb-3 ">\n' +
                '                                                   <label for="exampleInputEmail1">عنوان : </label>\n' +
                '                                                   <input class="form-control" required="" name="title_' + number + '_1" type="text" />\n' +
                '                                               </div>\n' +
                '                                           </div>\n' +
                '                                        </div>\n' +
                '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                '<input type="checkbox" name="visible' + number + '" checked="checked" id="visible' + number + '" />\n' +
                '<label for="visible' + number + '">قابل مشاهده</label>\n' +
                '</div>\n' +
                '<div class="checkbox col-12 col-sm-6 float-left">\n' +
                '<input type="checkbox" name="editable' + number + '" checked="checked" id="editable' + number + '" />\n' +
                '<label for="editable' + number + '">قابل ویرایش</label>\n' +

                '    <button onclick="moveRowUp(' + number + ')" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Up"><i class="fa fa-arrow-up"></i></button>\n' +
                '    <button onclick="moveRowDown(' + number + ')" type="button" class="btn btn-secondary btn_move_row" data-toggle="tooltip" data-original-title="Move Down"><i class="fa fa-arrow-down"></i></button>\n' +
                '</div>\n' +
                '                                        <button onclick="btn_close_input(' + number + ')" type="button" class="btn btn-danger btn_close_input" data-toggle="tooltip" data-original-title="حذف"><i class="fa fa-close"></i></button>\n' +
                '                                    </div>')
        }
    })



    /* START گزینه های تکمیل خودکار بر اساس */
    $(document).on('change', '#auto-complete', function() {
        var selector = $(this)
            , value = selector.val()
            , number_special = selector.data('number-special'); //عدد اختصاصی ردیف

        selector.parent().find('.settings-value-auto-complete').remove();

        if (value == 'form') {
            selector.parent().append('<div class="settings-value-auto-complete col-12 float-left mt-2 mb-4"><div class="col-12 col-sm-6 float-left"><input type="number" name="id-form-' + number_special + '" placeholder="آیدی" id="id-form" class="form-control" data-number-special="' + number_special + '" /></div></div>')
        } //فرم
        else if (value == 'user') {
            selector.parent().append('<div class="settings-value-auto-complete mt-2 mb-4 px-3"><select name="info-user-' + number_special + '" class="form-control"><option disabled="disabled" selected="selected">انتخاب فیلد</option>' +
                @foreach($settings_auto_completes_form['user'] as $slug_setting_value_auto_complete => $name_setting_value_auto_complete)
                '<option value="{{$slug_setting_value_auto_complete}}">{{$name_setting_value_auto_complete}}</option>\n' +
                @endforeach +
                '</select></div>')
        } //کاربر
        else if (value == 'invoice') {
            selector.parent().append('<div class="settings-value-auto-complete mt-2 mb-4 px-3"><select name="info-invoice-' + number_special + '" class="form-control"><option disabled="disabled" selected="selected">انتخاب مورد</option>' +
                @foreach($settings_auto_completes_form['invoice'] as $slug_setting_value_auto_complete => $name_setting_value_auto_complete)
                '<option value="{{$slug_setting_value_auto_complete}}">{{$name_setting_value_auto_complete}}</option>\n' +
                @endforeach +
                '</select></div>')
        } //صورتحساب
    })


    /* START بر اساس آیدی فرم */
    var before_values = {}, //محتواهای قبلی فیلدها
        timeout_get_fields_form_by_id
        , ajax_get_fields_form_by_id;
    jQuery(document).on('input change keyup keydown keypress', '#id-form', function() {
        if (typeof ajax_get_fields_form_by_id !== 'undefined') ajax_get_fields_form_by_id.abort();

        selector = $(this);

        clearTimeout(timeout_get_fields_form_by_id);
        timeout_get_fields_form_by_id = setTimeout(function() {
            var value = selector.val();
            if (value == '') return;

            var number_special = selector.data('number-special'); //عدد اختصاصی ردیف
            if (value == before_values[number_special]) return;
            before_values[number_special] = value;

            var parent = selector.parent();
            parent.next().remove();
            parent.after('<div class="col-12 col-sm-6 mt-2 mt-sm-0 float-left"><i class="fa fa-spinner fa-pulse" style="font-size: 30px; margin-top: 4px"></i></div>'); //لودینگ

            var counter_number_resend_ajax_get_fields_form_by_id = 0;

            function send_ajax_get_fields_form_by_id() {
                $.get('/{{$role}}/form/extra-fields/get/' + value, function(fields_form, status_ajax) {
                    if (status_ajax == 'success') {
                        var element_result_fields_form = parent.next();
                        if (!fields_form) element_result_fields_form.remove();

                        var html_selectbox_extra_fields = '<select name="field-form-' + number_special + '" class="form-control">';
                        html_selectbox_extra_fields += '<option disabled="disabled" selected="selected">انتخاب فیلد</option>';
                        $.each($.parseJSON(fields_form), function(key, value) {
                            html_selectbox_extra_fields += '<option value="' + value.name + '">' + value.title + '</option>';
                        })
                        element_result_fields_form.html(html_selectbox_extra_fields + '</select>');
                    } else if (counter_number_resend_ajax_get_fields_form_by_id < 5) {
                        ajax_get_fields_form_by_id = send_ajax_get_fields_form_by_id();
                    }

                    counter_number_resend_ajax_get_fields_form_by_id++;
                });
            }
            ajax_get_fields_form_by_id = send_ajax_get_fields_form_by_id();
        }, 800);
    })
    /* END بر اساس آیدی فرم */
    /* END گزینه های تکمیل خودکار بر اساس */

</script>
@endsection
