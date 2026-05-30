
@php
  $role = session()->has('admin') ? 'admin' : 'manager';
  $check_access_27_manager = ($role == 'admin' || $role == 'manager' && \App\Http\Controllers\managerController::managerAccessLevel(27)) ? true : false
@endphp
@extends('theme.default')
@section('container')
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <script src="/assets/js/modernizr.min.js"></script>

<div class="row">
    <div class="col-12 mb-3">
        <div class="card-box">
            <div class="row">
                @foreach($form_fild as $fild)

                    @php
                        $value = '';
                        foreach (($user_forms['form'] ?? []) as $form_from_user) {
                            if (isset($form_from_user['name_itme']) && $form_from_user['name_itme'] == $fild['name']) {
                                $value = $form_from_user[$fild['name']] ?? '';
                                break;
                            }
                        }
                    @endphp

                    @if($fild['type'] == 1 || $fild['type'] == 2 || $fild['type'] == 3)
                        <div class="col-lg-6 mt-3">
                            <label>{{ $fild['title'] }}</label>
                            <input type="text" disabled class="form-control" value="{{ $value }}" />
                        </div>

                    @elseif($fild['type'] == 7)
                        <div class="col-lg-6 mt-3">
                            <label>{{ $fild['title'] }}</label>
                            @if($value)
                                <div>
                                    <a href="/{{ $directory_files_user ?? 'Files/User/' }}{{ $user_form['id_user'] }}/Form/{{ $user_form['id_form'] }}/{{ $value }}"
                                       target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-file"></i> مشاهده فایل
                                    </a>
                                </div>
                            @else
                                <div class="text-muted">فایلی بارگذاری نشده</div>
                            @endif
                        </div>

                    @elseif($fild['type'] == 8)
                        <div class="col-lg-6 mt-3">
                            <label>{{ $fild['title'] }}</label>
                            <textarea disabled class="form-control" rows="4">{{ $value }}</textarea>
                        </div>

                    @elseif($fild['type'] == 5)
                        <div class="col-lg-6 mt-3" style="min-height:77px;">
                            <div class="checkbox w-100 mt-4">
                                <input type="checkbox" disabled
                                    @foreach(($user_form['form'] ?? []) as $form)
                                        @if(is_array($form) && array_key_exists($fild['name'], $form)) checked="checked" @endif
                                    @endforeach
                                    id="chk_detail_{{ $fild['name'] }}" />
                                <label for="chk_detail_{{ $fild['name'] }}">{{ $fild['title'] }}</label>
                            </div>
                        </div>

                    @elseif($fild['type'] == 4)
                        <div class="col-lg-6 mt-3">
                            <label>{{ $fild['title'] }}</label>
                            <select disabled class="form-control">
                                <option disabled selected>انتخاب کنید</option>
                                @foreach($fild['itme'] as $itme)
                                    <option
                                        @foreach(($user_form['form'] ?? []) as $form)
                                            @if(is_array($form) && isset($form['name_itme']) && $form['name_itme'] == $fild['name'] && isset($form[$fild['name']]) && $form[$fild['name']] == $itme['title']) selected @endif
                                        @endforeach
                                        value="{{ $itme['title'] }}">{{ $itme['title'] }}</option>
                                @endforeach
                            </select>
                        </div>

                    @elseif($fild['type'] == 6)
                        <div class="col-lg-6 mt-3">
                            <label class="d-block">{{ $fild['title'] }}</label>
                            @foreach($fild['itme'] as $number => $itme)
                                <div class="checkbox d-inline">
                                    <div class="radio radio-info form-check-inline">
                                        <input type="radio" disabled
                                            value="{{ $itme['title'] }}"
                                            id="rd_detail_{{ $fild['name'] }}_{{ $number }}"
                                            @foreach(($user_form['form'] ?? []) as $form)
                                                @if(is_array($form) && isset($form[$fild['name']]) && $form[$fild['name']] == $itme['title']) checked @endif
                                            @endforeach />
                                        <label for="rd_detail_{{ $fild['name'] }}_{{ $number }}">{{ $itme['title'] }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
