@extends('theme.user')
@section('container')
<style>
    .blog-single-content {
        color: #44474c;
        line-height: 2.2
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

    .blog-single-content li+li {
        margin: 5px 0 0
    }

    .blog-single-content h2,
    .blog-single-content h2 a {
        color: #4b515a;
        font-weight: 600 !important;
        font-size: 1.75rem !important;
        margin-bottom: 0;
        line-height: 2em
    }

    .checkbox_input {
        margin: 0;
        position: absolute;
        top: 61%;
        right: 14px;
    }

    .checkbox_input label {
        margin: 0 !important
    }

    .checkbox label::before {
        top: 7px
    }

    .parsley-errors-list {
        position: absolute;
        bottom: -24px;
        right: 18px
    }

    .radio label::after {
        top: 10px
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

    .file-selected-for-field {
        width: 140px
    }

    /* فایل انتخاب شده برای فیلد */

    .popup-container { display: inline-block; position: absolute; top: 6px; left: 6px; z-index: 10; }
    .popup-place { cursor: pointer; display: inline-block; }
    .popup-place i { color: #888; font-size: 16px; }
    .popup-content {
        display: none;
        position: absolute;
        background: #fff;
        border: 1px solid #ccc;
        padding: 8px 12px;
        z-index: 100;
        min-width: 200px;
        max-width: 280px;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,.18);
        right: 0;
        top: 22px;
        font-size: 13px;
        color: #444;
        line-height: 1.6;
    }
    .popup-place:hover .popup-content { display: block; }
</style>
<script>
    function convertToFarsiDigits(str) {
        const farsiDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        return str.replace(/\d/g, (d) => farsiDigits[d]);
    }

    // Function to handle conversion for all number inputs
    function handleNumberInputs() {
        // Select all input elements of type number
        const numberInputs = document.querySelectorAll('input[type="number"]');

        numberInputs.forEach(input => {
            // Convert type to text
            input.type = 'text';

            // Convert value to Persian digits
            input.value = convertToFarsiDigits(input.value);
            console.log(input.value);
            // Listen for changes and convert input value to Persian digits
            input.addEventListener('input', (event) => {
                event.target.value = convertToFarsiDigits(event.target.value);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', handleNumberInputs);
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-11 col-xl-10  mb-3">
            <div class="card-box blog-single-content ">
                @if (isset($form))
                <div class="col-12 pb-4">
                    <div class="bg-success p-3 px-sm-4">
                        <h1 class="text-white my-0 font-weight-bold h6">{{ $form['name'] }}</h1>
                    </div>

                    <div class="row">
                        @foreach ($form['fild'] as $fild)
                        @php
                        if (!is_array($fild) || !array_key_exists('visible', $fild)) {
                        continue;
                        }
                        //قابل مشاهده نبود
                        @endphp
       

                        @if ($fild['type'] == 1 or $fild['type'] == 2 or $fild['type'] == 3 || $fild['type'] == 7)
                        <div class="col-lg-6 mt-3 position-relative">
                                                            @if(isset($fild['description']) && $fild['description'])
                            <div class="popup-container">
                                <div class="popup-place">
                                    <i class="fa fa-question-circle"></i>
                                    <div class="popup-content">{{ $fild['description'] }}</div>
                                </div>
                            </div>
                        @endif
                            <label for="inputEmail31">{{ $fild['title'] }}</label>

                            @php
                            //بدست آوردن محتوای فیلد
                            $value = '';
                            if(isset($user_form['form']) && is_array($user_form['form'])):
                            foreach ($user_form['form'] as $form_from_user):

                            if (is_array($form_from_user) && isset($form_from_user['name_itme']) && $form_from_user['name_itme'] == $fild['name']):
                            $value = $form_from_user[$fild['name']] ?? '';
                            break;
                            endif;
                            endforeach;
                            endif;

                            @endphp

                            @if ($fild['type'] != 7) <input
                                disabled @if ($fild['type']==1) type="text" @endif
                                @if ($fild['type']==2) type="email" @endif
                                @if ($fild['type']==3) type="@if (!array_key_exists('separator', $fild)){{ 'number' }}@else{{ 'text' }}@endif"
                                @endif class="form-control text-primary"
                                value="@if(array_key_exists('separator', $fild)){{ is_numeric(str_replace(',', '', $value)) ? number_format((float)str_replace(',', '', $value)) : $value }}@elseif($value !== '' && $value !== null){{ $value }}@elseif(array_key_exists('value', $fild) && $fild['value']){{ $fild['value'] }}@endif">

                            @elseif($value)
                            <a href="/{{ $directory_files_user }}{{ $id_user }}/Form/{{ $form['id'] }}/{{ $value }}"
                                target="_blank" class="d-block">
                                @if (!file_exists(url('/') . "/$directory_files_user$id_user/Form/" . $form['id'] . "/$value")) <img
                                    src="/{{ $directory_files_user }}{{ $id_user }}/Form/{{ $form['id'] }}/{{ $value }}"
                                    class="file-selected-for-field" />
                                @else{{ 'برای باز شدن کلیک کنید' }}@endif
                            </a>
                            @endif
                        </div>
                        @elseif($fild['type'] == 8)
                        <div class="col-lg-6 mt-3 position-relative">
                            <label>{{ $fild['title'] }}</label>
                            <textarea name="{{ $fild['name'] }}"
                                class="form-control @if ($fild['checkbox']) required text-primary @endif"
                                @if (!array_key_exists('editable', $fild)) {{ 'readonly' }} @endif>
                                                 @if(isset($user_form['form']) && is_array($user_form['form']))
                                                @foreach ($user_form['form'] as $form_from_user)
                                                    @if (is_array($form_from_user) && isset($form_from_user['name_itme']) && $form_from_user['name_itme'] == $fild['name'])
                                                        {{ $form_from_user[$fild['name']] ?? '' }}
                                                        @break
                                                    @endif
                                                @endforeach
                                                @endif

</textarea>
                        </div>
                        @elseif($fild['type'] == 5)
                        <div class="col-lg-6 mt-3 position-relative height-77px">
                            <div class="checkbox checkbox_input ">
                                 <input disabled=""
                                    @if(isset($user_form['form']) && is_array($user_form['form']))
                                    @foreach ($user_form['form'] as $_form) @if (is_array($_form) && isset($_form['name_itme']) && $_form['name_itme']==$fild['name']) @if (isset($_form[$fild['name']]) && $_form[$fild['name']]==1) checked="" @endif @endif @endforeach
                                    @endif
                                    id="checkbox{{ $fild['name'] }}" type="checkbox" class="text-primary">
                                <label for="checkbox{{ $fild['name'] }}">
                                    {{ $fild['title'] }}
                                </label>
                            </div>
                        </div>
                        @elseif($fild['type'] == 4)
                        <div class="col-lg-6 mt-3 position-relative ">
                            <label for="inputEmail3">{{ $fild['title'] }}</label>
                            <select disabled="" id="inputEmail34" class="form-control select2">
                                <option selected="" disabled="" value="0">انتخاب کنید
                                </option>
                                 @foreach ($fild['itme'] as $itme)
                                <option
                                    @if(isset($user_form['form']) && is_array($user_form['form']))
                                    @foreach ($user_form['form'] as $_form) @if (is_array($_form) && isset($_form['name_itme']) && $_form['name_itme']==$fild['name']) @if (isset($_form[$fild['name']]) && $_form[$fild['name']]==$itme['title']) selected @endif @endif @endforeach
                                    @endif
                                    value="{{ $itme['title'] }}">{{ $itme['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @elseif($fild['type'] == 6)
                        <div class="col-lg-6 mt-3 position-relative ">
                            <label class="d-block" for="inputEmail3">{{ $fild['title'] }}</label>
                            @foreach ($fild['itme'] as $number => $itme)
                            <div class="checkbox d-inline">
                                <div class="radio radio-info form-check-inline">
                                     <input disabled="" type="radio"
                                        id="inlineRadio_{{ $fild['name'] }}_{{ $number }}"
                                        @if(isset($user_form['form']) && is_array($user_form['form']))
                                        @foreach ($user_form['form'] as $_form) @if (is_array($_form) && isset($_form['name_itme']) && $_form['name_itme']==$fild['name']) @if (isset($_form[$fild['name']]) && $_form[$fild['name']]==$itme['title']) checked @endif @endif @endforeach
                                        @endif
                                        class="text-primary" />
                                    <label for="inlineRadio_{{ $fild['name'] }}_{{ $number }}">
                                        {{ $itme['title'] }} </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                    </div>
                    </form>
                </div>
                @endif
                {!! htmlspecialchars_decode($menu['text']) !!}
            </div>
        </div>
    </div>
</div>
@endsection