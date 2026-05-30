<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>تکمیل فرم - {{ $form->name }}</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f4f6fb; font-family: Tahoma, Arial, sans-serif; }
        .card-box {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            margin-bottom: 20px;
        }
        .form-control { text-align: right; }
        label { font-weight: 500; }
        .user-info-box {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .badge-group {
            background: #188ae2;
            color: #fff;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="container" style="margin-top: 30px; max-width: 900px;">

    <div class="card-box">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">فرم: {{ $form->name }}</h4>
            @if($form->text)
                <small class="text-muted">{{ $form->text }}</small>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search Box --}}
    <div class="card-box">
        <h5 class="mb-3">جستجوی کاربر</h5>
        <form method="GET" action="/external/form/{{ $token }}/search">
            <div class="input-group">
                <input type="text"
                       name="q"
                       class="form-control"
                       value="{{ request('q') }}"
                       placeholder="شناسه، کد یا نام کاربر را وارد کنید"/>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">جستجو</button>
                </div>
            </div>
        </form>
    </div>

    @if(request()->filled('q') && !$user)
        <div class="alert alert-warning">کاربری یافت نشد.</div>
    @endif

    @if($user)
        {{-- User Info --}}
        <div class="card-box">
            <div class="user-info-box">
                <div class="row">
                    <div class="col-sm-4">
                        <strong>نام:</strong>
                        {{ $user->name }} {{ $user->name2 }}
                    </div>
                    <div class="col-sm-4">
                        <strong>کد:</strong> {{ $user->kod }}
                    </div>
                    @if($user->group)
                    <div class="col-sm-4">
                        <strong>مجموعه:</strong>
                        <span class="badge-group">{{ $user->group->name }}</span>
                    </div>
                    @endif
                </div>
                @if($user->mobile)
                <div class="mt-2">
                    <strong>موبایل:</strong> {{ $user->mobile }}
                </div>
                @endif
            </div>

            {{-- Form Fields --}}
            <form method="POST" action="/external/form/{{ $token }}/submit" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}"/>

                <div class="row">
                    @foreach($form_fild as $fild)
                        @php
                            $fieldName  = $fild['name'];
                            $fieldTitle = $fild['title'] ?? $fieldName;
                            $fieldType  = $fild['type'];
                            $currentVal = $existingData[$fieldName] ?? '';
                        @endphp

                        @if($fieldType == 1 || $fieldType == 2 || $fieldType == 3)
                            <div class="col-md-6 mt-3">
                                <label>{{ $fieldTitle }}</label>
                                <input
                                    type="{{ $fieldType == 1 ? 'text' : ($fieldType == 2 ? 'email' : 'number') }}"
                                    name="{{ $fieldName }}"
                                    class="form-control"
                                    value="{{ $currentVal }}"
                                    />
                            </div>

                        @elseif($fieldType == 4)
                            <div class="col-md-6 mt-3">
                                <label>{{ $fieldTitle }}</label>
                                <select name="{{ $fieldName }}"
                                        class="form-control">
                                    <option value="">انتخاب کنید</option>
                                    @foreach($fild['itme'] ?? [] as $item)
                                        <option value="{{ $item['title'] }}"
                                            {{ $currentVal == $item['title'] ? 'selected' : '' }}>
                                            {{ $item['title'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        @elseif($fieldType == 5)
                            <div class="col-md-6 mt-3">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="{{ $fieldName }}"
                                           id="chk_{{ $fieldName }}"
                                           {{ $currentVal ? 'checked' : '' }}
                                           />
                                    <label class="form-check-label" for="chk_{{ $fieldName }}">{{ $fieldTitle }}</label>
                                </div>
                            </div>

                        @elseif($fieldType == 6)
                            <div class="col-md-6 mt-3">
                                <label class="d-block">{{ $fieldTitle }}</label>
                                @foreach($fild['itme'] ?? [] as $idx => $item)
                                    <div class="form-check form-check-inline">
                                        <input type="radio"
                                               class="form-check-input"
                                               name="{{ $fieldName }}"
                                               id="radio_{{ $fieldName }}_{{ $idx }}"
                                               value="{{ $item['title'] }}"
                                               {{ $currentVal == $item['title'] ? 'checked' : '' }}
                                               />
                                        <label class="form-check-label" for="radio_{{ $fieldName }}_{{ $idx }}">
                                            {{ $item['title'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                        @elseif($fieldType == 7)
                            <div class="col-md-6 mt-3">
                                <label>{{ $fieldTitle }}</label>
                                @if($currentVal)
                                    <p class="text-muted mb-1">فایل فعلی: <strong>{{ $currentVal }}</strong></p>
                                @endif
                                <input type="file" name="{{ $fieldName }}" class="form-control-file"/>
                            </div>

                        @elseif($fieldType == 8)
                            <div class="col-md-12 mt-3">
                                <label>{{ $fieldTitle }}</label>
                                <textarea name="{{ $fieldName }}"
                                          class="form-control"
                                          rows="4">{{ $currentVal }}</textarea>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success btn-block py-3">
                        ثبت اطلاعات
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>

<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/js/bootstrap.min.js"></script>
</body>
</html>
