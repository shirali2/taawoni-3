@extends("theme.manager")
@section("container")

<script src="/assets/plugins/jalali/jalaali.min.js"></script>

  <div class="row">
    <div class="col-12 mb-3">
      <div class="card-box">
        <div class="m-t-0 m-b-30">
          <h4 class="header-title d-inline-block">ثبت یادداشت جدید</h4>
          <a href="{{ route('manager.notes.index') }}" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
        </div>

        @if(isset($grop))
          <div class="alert alert-info py-2" style="font-size:0.9em">
            <i class="fa fa-info-circle"></i>
            یادداشت برای کاربران مجموعه: <strong>{{ $grop->name }}</strong>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('success') }}
          </div>
        @endif

        @foreach($errors->all() as $error)
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ $error }}
          </div>
        @endforeach

        <form method="POST" action="{{ route('manager.notes.store') }}">
          @csrf

          {{-- 1. کد کاربر (اول) --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">کد کاربر</label>
            <div class="col-sm-10">
              <input type="text" id="user-search" class="form-control"
                     placeholder="آی‌دی یا کد اختصاصی کاربر را وارد کنید">
              <div id="user-info-preview" class="mt-2" style="display:none;padding:7px 12px;background:#e8f4fd;border:1px solid #bee5eb;border-radius:4px;font-size:0.9em;line-height:1.8"></div>
              <input type="hidden" id="user-id-input" name="user_ids[]">
              <small class="text-muted">در صورت ثبت برای یک نفر، کد را وارد کنید. در غیر این صورت از گروه زیر استفاده کنید.</small>
            </div>
          </div>

          <hr class="my-2">

          {{-- 2. گروه کاربری (بدون انتخاب مجموعه - مجموعه از session مدیر) --}}
          <div class="form-group row" id="usergrop-row">
            <label class="col-sm-2 col-form-label text-left">
              گروه کاربری
              <span id="all-users-label" class="text-info d-block" style="display:none!important;font-size:11px">برای تمام کاربران مجموعه</span>
            </label>
            <div class="col-sm-10">
              <select id="usergrop-select" name="group_ids[]" multiple class="form-control" size="4">
                @foreach($usergrops as $ug)
                  <option value="{{ $ug->id }}">{{ $ug->name }}</option>
                @endforeach
              </select>
              <small class="text-muted">
                در صورت عدم انتخاب، یادداشت برای تمام کاربران مجموعه ثبت می‌شود.
                برای انتخاب چند مورد Ctrl نگه دارید.
              </small>
            </div>
          </div>

          <hr class="my-2">

          {{-- 3. متن یادداشت --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">متن یادداشت</label>
            <div class="col-sm-10">
              <textarea name="content" rows="5" class="form-control"
                        placeholder="متن یادداشت را وارد کنید..." required maxlength="5000">{{ old('content') }}</textarea>
            </div>
          </div>

          {{-- 4. نوع نمایش --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">نوع نمایش</label>
            <div class="col-sm-10 d-flex align-items-center" style="gap:16px">
              <label class="mb-0">
                <input type="radio" name="visibility" value="admin_only" checked class="mr-1">
                فقط مدیر
              </label>
              <label class="mb-0">
                <input type="radio" name="visibility" value="admin_and_user" class="mr-1">
                مدیر و کاربر
              </label>
            </div>
          </div>

          {{-- 5. یادداشت اجباری --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">یادداشت اجباری</label>
            <div class="col-sm-10 d-flex align-items-center">
              <input type="checkbox" id="is-mandatory" name="is_mandatory" value="1" class="mr-2">
              <label for="is-mandatory" class="mb-0">بله، کاربر باید این یادداشت را ببیند</label>
            </div>
          </div>

          {{-- 6. یادآوری --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">یادآوری</label>
            <div class="col-sm-10">
              <div class="d-flex align-items-center">
                <input type="checkbox" id="reminder-toggle" class="mr-2">
                <label for="reminder-toggle" class="mb-0">تنظیم تاریخ یادآوری</label>
              </div>

              <div id="reminder-section" style="display:none;margin-top:10px">
                <div class="d-flex align-items-center flex-wrap" style="gap:10px;margin-bottom:8px">
                  <label class="mb-0" style="white-space:nowrap">تعداد روز از امروز:</label>
                  <input type="number" id="reminder-days" class="form-control"
                         style="width:90px" min="1" max="3650" placeholder="مثلاً ۳۰">
                  <span id="reminder-jalali-display" class="text-primary" style="font-size:0.95em;font-weight:600"></span>
                </div>
                <div class="d-flex align-items-center" style="gap:10px">
                  <label class="mb-0" style="white-space:nowrap;font-size:0.9em;color:#666">یا تاریخ مستقیم:</label>
                  <input type="date" id="reminder-date-direct" class="form-control" style="max-width:190px">
                </div>
                <input type="hidden" name="reminder_date" id="reminder-date-hidden">
                <small class="text-muted d-block mt-1">یکی از دو روش بالا را انتخاب کنید</small>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
              <button type="submit" class="btn btn-primary waves-effect w-md waves-light">ثبت یادداشت</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var userSearch      = document.getElementById('user-search');
      var preview         = document.getElementById('user-info-preview');
      var userIdInput     = document.getElementById('user-id-input');
      var ugropSelect     = document.getElementById('usergrop-select');
      var allLabel        = document.getElementById('all-users-label');
      var reminderToggle  = document.getElementById('reminder-toggle');
      var reminderSection = document.getElementById('reminder-section');
      var reminderDays    = document.getElementById('reminder-days');
      var reminderJalali  = document.getElementById('reminder-jalali-display');
      var reminderDirect  = document.getElementById('reminder-date-direct');
      var reminderHidden  = document.getElementById('reminder-date-hidden');
      var debounceTimer   = null;

      function pad2(n) { return String(n).padStart(2, '0'); }

      function dateToGregorianStr(d) {
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
      }

      function dateToJalaliStr(d) {
        if (typeof jalaali === 'undefined') return '';
        var j = jalaali.toJalaali(d.getFullYear(), d.getMonth() + 1, d.getDate());
        return j.jy + '/' + pad2(j.jm) + '/' + pad2(j.jd);
      }

      function updateAllLabel() {
        var hasUser  = userIdInput.value !== '';
        var hasGroup = Array.from(ugropSelect.selectedOptions).length > 0;
        allLabel.style.display = (!hasUser && !hasGroup) ? 'block' : 'none';
      }

      /* ── user search ─────────────────────────────────────── */
      userSearch.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        var val = this.value.trim();

        if (val.length < 2) {
          preview.style.display = 'none';
          userIdInput.value = '';
          ugropSelect.disabled = false;
          updateAllLabel();
          return;
        }

        debounceTimer = setTimeout(function () {
          fetch('/manager/notes/user-info?id=' + encodeURIComponent(val))
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (data.found) {
                var parts = ['نام: <strong>' + data.name + '</strong>'];
                if (data.lawyer_name) parts.push('وکیل: ' + data.lawyer_name);
                parts.push('آی‌دی: <strong>' + data.id + '</strong>');
                if (data.hash) parts.push('کد اختصاصی: <strong>' + data.hash + '</strong>');
                parts.push('تلفن: ' + (data.mobile || '—'));
                if (data.usergrop) parts.push('گروه: ' + data.usergrop);
                preview.innerHTML = parts.join(' &nbsp;|&nbsp; ');
                preview.style.display = 'block';
                userIdInput.value = data.id;
                ugropSelect.disabled = true;
              } else {
                preview.innerHTML = '<span class="text-danger">کاربری با این مشخصات در مجموعه شما یافت نشد</span>';
                preview.style.display = 'block';
                userIdInput.value = '';
                ugropSelect.disabled = false;
              }
              updateAllLabel();
            })
            .catch(function () {
              preview.innerHTML = '<span class="text-danger">خطا در اتصال به سرور</span>';
              preview.style.display = 'block';
            });
        }, 400);
      });

      ugropSelect.addEventListener('change', updateAllLabel);

      /* ── reminder ────────────────────────────────────────── */
      reminderToggle.addEventListener('change', function () {
        reminderSection.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
          reminderDays.value = '';
          reminderDirect.value = '';
          reminderHidden.value = '';
          reminderJalali.textContent = '';
        }
      });

      reminderDays.addEventListener('input', function () {
        var days = parseInt(this.value);
        if (!days || days < 1) {
          reminderJalali.textContent = '';
          reminderHidden.value = '';
          return;
        }
        var future = new Date();
        future.setDate(future.getDate() + days);
        reminderHidden.value       = dateToGregorianStr(future);
        reminderJalali.textContent = '→ ' + dateToJalaliStr(future);
        reminderDirect.value = '';
      });

      reminderDirect.addEventListener('change', function () {
        reminderHidden.value       = this.value;
        reminderDays.value         = '';
        reminderJalali.textContent = '';
      });

    })();
  </script>

@endsection
