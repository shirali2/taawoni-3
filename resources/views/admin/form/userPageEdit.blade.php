@php
  $role = session()->has('admin') ? 'admin' : 'manager';
  $check_access_27_manager = ($role == 'admin' || $role == 'manager' && \App\Http\Controllers\managerController::managerAccessLevel(27)) ? true : false;
@endphp
@extends('theme.default')
@section('container')

<style>
    .file-selected-for-field { width: 140px; }
    .current-file-label { font-size: 12px; color: #666; }
</style>

<form action="/admin/form/user/edit" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input name="id-user-form" value="{{ $user_form['id'] }}" hidden>

        @foreach($form_fild as $fild)

            @php
                {{-- Resolve current saved value for this field --}}
                $value = '';
                foreach (($user_forms['form'] ?? []) as $form_from_user) {
                    if (isset($form_from_user['name_itme']) && $form_from_user['name_itme'] == $fild['name']) {
                        $value = $form_from_user[$fild['name']] ?? '';
                        break;
                    }
                }
            @endphp

            @if($fild['type'] == 1 || $fild['type'] == 2 || $fild['type'] == 3)
                {{-- Text / email / number inputs --}}
                <div class="col-lg-6 mt-3 position-relative">
                    <label>{{ $fild['title'] }}</label>
                    <input
                        @if($fild['type'] == 1) type="text"
                        @elseif($fild['type'] == 2) type="email"
                        @elseif($fild['type'] == 3) type="number"
                        @endif
                        name="{{ $fild['name'] }}"
                        class="form-control"
                        value="{{ $value }}">
                </div>

            @elseif($fild['type'] == 7)
                {{-- Bug 5: file fields — never set value on <input type="file">.
                     Show existing filename as a read-only label and offer an
                     optional "Change file" section below it. --}}
                <div class="col-lg-6 mt-3 position-relative">
                    <label>{{ $fild['title'] }}</label>
                    @if($value)
                        <div class="mb-2 p-2 bg-light border rounded">
                            <span class="current-file-label">فایل فعلی: </span>
                            <a href="/{{ $directory_files_user ?? 'Files/User/' }}{{ $user_form['id_user'] }}/Form/{{ $user_form['id_form'] }}/{{ $value }}"
                               target="_blank" class="text-primary">{{ $value }}</a>
                        </div>
                    @endif
                    {{-- Optional replacement: only uploaded if user picks a new file --}}
                    <label class="d-block text-muted" style="font-size:12px;">تغییر فایل (اختیاری):</label>
                    <input type="file"
                           name="{{ $fild['name'] }}"
                           class="form-control-file"
                           accept="{{ implode(',', $formats_allowed_file) }}">
                    <small class="text-muted">مجاز: {{ implode('، ', $formats_allowed_file) }} — حداکثر ۵ مگابایت</small>
                </div>

            @elseif($fild['type'] == 8)
                {{-- Bug 5: textarea must be a real <textarea>, not an <input> --}}
                <div class="col-lg-6 mt-3 position-relative">
                    <label>{{ $fild['title'] }}</label>
                    <textarea name="{{ $fild['name'] }}"
                              class="form-control"
                              rows="6"
                              style="resize: vertical;">{{ $value }}</textarea>
                </div>

            @elseif($fild['type'] == 5)
                <div class="col-lg-6 mt-3 position-relative" style="min-height:77px;">
                    <div class="checkbox w-100 mt-4">
                        <input type="checkbox" name="{{ $fild['name'] }}"
                            @foreach(($user_form['form'] ?? []) as $form)
                                @if(array_key_exists($fild['name'], $form)) checked="checked" @endif
                            @endforeach
                            id="checkbox{{ $fild['name'] }}">
                        <label for="checkbox{{ $fild['name'] }}">{{ $fild['title'] }}</label>
                    </div>
                </div>

            @elseif($fild['type'] == 4)
                <div class="col-lg-6 mt-3 position-relative">
                    <label>{{ $fild['title'] }}</label>
                    <select name="{{ $fild['name'] }}" class="form-control select2">
                        <option disabled="disabled" selected="selected" value="">انتخاب کنید</option>
                        @foreach($fild['itme'] as $itme)
                            <option
                                @foreach(($user_form['form'] ?? []) as $form)
                                    @if(isset($form['name_itme']) && $form['name_itme'] == $fild['name'] && isset($form[$fild['name']]) && $form[$fild['name']] == $itme['title']) selected="selected" @endif
                                @endforeach
                                value="{{ $itme['title'] }}">{{ $itme['title'] }}</option>
                        @endforeach
                    </select>
                </div>

            @elseif($fild['type'] == 6)
                <div class="col-lg-6 mt-3 position-relative">
                    <label class="d-block">{{ $fild['title'] }}</label>
                    @foreach($fild['itme'] as $number => $itme)
                        <div class="checkbox d-inline">
                            <div class="radio radio-info form-check-inline">
                                <input type="radio" name="{{ $fild['name'] }}"
                                    value="{{ $itme['title'] }}"
                                    id="inlineRadio_{{ $fild['name'] }}_{{ $number }}"
                                    @foreach(($user_form['form'] ?? []) as $form)
                                        @if(array_key_exists($fild['name'], $form) && $form[$fild['name']] == $itme['title']) checked @endif
                                    @endforeach>
                                <label for="inlineRadio_{{ $fild['name'] }}_{{ $number }}">{{ $itme['title'] }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif
        @endforeach

        <div class="col-12">
            <button type="submit" class="btn btn-success w-100 mt-5 py-3">ویرایش</button>
        </div>
    </div>
</form>

@endsection
