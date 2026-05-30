@extends("theme.default")
@section("container")

<div class="row justify-content-center">
  <div class="col-lg-8 col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center">
        <h4 class="header-title">تنظیمات سیستم پیامک</h4>
        <a href="{{ route('admin.sms.index') }}"
           class="btn btn-sm btn-secondary waves-effect">بازگشت</a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.sms.settings.save') }}" class="form-horizontal">
        @csrf

        {{-- Driver --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">سامانه پیامکی</label>
          <div class="col-sm-6">
            <input type="text" name="sms_driver" class="form-control"
              value="{{ $settings['sms_driver'] ?? '' }}"
              placeholder="مثال: kavenegar">
          </div>
        </div>

        {{-- API Key --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">کلید API</label>
          <div class="col-sm-6">
            <input type="text" name="sms_api_key" class="form-control"
              value="{{ $settings['sms_api_key'] ?? '' }}"
              placeholder="کلید API سامانه پیامک">
          </div>
        </div>

        {{-- Line Number --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">خط ارسال</label>
          <div class="col-sm-6">
            <input type="text" name="sms_line_number" class="form-control"
              value="{{ $settings['sms_line_number'] ?? '' }}"
              placeholder="شماره خط ارسال پیامک">
          </div>
        </div>

        {{-- Cost per SMS --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">مبلغ هر پیامک (ریال)</label>
          <div class="col-sm-4">
            <input type="number" name="sms_cost_per_unit" class="form-control"
              value="{{ $settings['sms_cost_per_unit'] ?? 0 }}" min="0" step="1">
          </div>
        </div>

        <hr>

        {{-- Time Window --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">ساعت شروع ارسال</label>
          <div class="col-sm-3">
            <input type="number" name="sms_start_hour" class="form-control"
              value="{{ $settings['sms_start_hour'] ?? 8 }}" min="0" max="23">
            <small class="text-muted">0 تا 23</small>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">ساعت پایان ارسال</label>
          <div class="col-sm-3">
            <input type="number" name="sms_end_hour" class="form-control"
              value="{{ $settings['sms_end_hour'] ?? 22 }}" min="0" max="23">
            <small class="text-muted">0 تا 23</small>
          </div>
        </div>

        {{-- Min / Max send count --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">حداقل تعداد پیامک در هر ارسال</label>
          <div class="col-sm-3">
            <input type="number" name="sms_min_send_count" class="form-control"
              value="{{ $settings['sms_min_send_count'] ?? 1 }}" min="1">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">حداکثر تعداد پیامک در هر ارسال</label>
          <div class="col-sm-3">
            <input type="number" name="sms_max_send_count" class="form-control"
              value="{{ $settings['sms_max_send_count'] ?? 10000 }}" min="1">
          </div>
        </div>

        {{-- Interval --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">فاصله بین هر پیامک (ثانیه)</label>
          <div class="col-sm-3">
            <input type="number" name="sms_interval_seconds" class="form-control"
              value="{{ $settings['sms_interval_seconds'] ?? 3 }}" min="0">
          </div>
        </div>

        <hr>

        {{-- Notify numbers (per tenant) --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">شماره اطلاع‌رسانی تیکت</label>
          <div class="col-sm-5">
            <input type="text" name="sms_notify_ticket" class="form-control"
              value="{{ $settings['sms_notify_ticket'] ?? '' }}"
              placeholder="شماره موبایل دریافت‌کننده اطلاع‌رسانی تیکت">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">شماره اطلاع‌رسانی فرم</label>
          <div class="col-sm-5">
            <input type="text" name="sms_notify_form" class="form-control"
              value="{{ $settings['sms_notify_form'] ?? '' }}"
              placeholder="شماره موبایل دریافت‌کننده اطلاع‌رسانی فرم">
          </div>
        </div>

        {{-- System status --}}
        <div class="form-group row">
          <label class="col-sm-4 col-form-label">وضعیت سیستم پیامک</label>
          <div class="col-sm-4">
            <select name="sms_system_status" class="form-control">
              <option value="active" {{ ($settings['sms_system_status'] ?? '') === 'active' ? 'selected' : '' }}>فعال</option>
              <option value="inactive" {{ ($settings['sms_system_status'] ?? '') === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
              <option value="test" {{ ($settings['sms_system_status'] ?? '') === 'test' ? 'selected' : '' }}>آزمایشی</option>
            </select>
          </div>
        </div>

        <div class="form-group row mt-4">
          <div class="col-sm-8 offset-sm-4">
            <button type="submit" class="btn btn-primary waves-effect">ذخیره تنظیمات</button>
          </div>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection
