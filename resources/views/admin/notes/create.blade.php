@extends("theme.default")
@section("container")

<script src="/assets/plugins/jalali/jalaali.min.js"></script>

  <div class="row">
    <div class="col-12 mb-3">
      <div class="card-box">
        <div class="m-t-0 m-b-30">
          <h4 class="header-title d-inline-block">ثبت یادداشت جدید</h4>
          <a href="{{ route('admin.notes.index') }}" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
        </div>

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

        <form method="POST" action="{{ route('admin.notes.store') }}">
          @csrf

          {{-- 1. کد کاربر (اول) --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">کد کاربر</label>
            <div class="col-sm-10">
              <input type="text" id="user-search" class="form-control"
                     placeholder="آی‌دی، کد اختصاصی یا موبایل کاربر را وارد کنید">
              <div id="user-info-preview" class="mt-2" style="display:none;padding:7px 12px;background:#e8f4fd;border:1px solid #bee5eb;border-radius:4px;font-size:0.9em;line-height:1.8"></div>
              <input type="hidden" id="user-id-input" name="user_ids[]">
              <small class="text-muted">در صورت ثبت برای یک نفر، کد کاربر را وارد کنید. در غیر این صورت از گروه‌بندی زیر استفاده کنید.</small>
            </div>
          </div>

          <hr class="my-2">

          {{-- 2. مجموعه --}}
          <div class="form-group row" id="grop-row">
            <label class="col-sm-2 col-form-label text-left">مجموعه</label>
            <div class="col-sm-10">
              <select id="grop-select" name="grop_ids[]" multiple class="form-control" size="4">
                @foreach($grops as $grop)
                  <option value="{{ $grop->id }}">{{ $grop->name }}</option>
                @endforeach
              </select>
              <small class="text-muted">برای انتخاب چند مورد، Ctrl نگه دارید</small>
            </div>
          </div>

          {{-- 3. گروه کاربری --}}
          <div class="form-group row" id="usergrop-row">
            <label class="col-sm-2 col-form-label text-left">
              گروه کاربری
              <span id="all-users-label" class="text-info" style="display:none;font-size:11px;display:block">برای تمام کاربران</span>
            </label>
            <div class="col-sm-10">
              <select id="usergrop-select" name="group_ids[]" multiple class="form-control" size="4" disabled>
                @foreach($usergrops as $ug)
                  <option value="{{ $ug->id }}" data-grop-id="{{ $ug->id_grop }}">{{ $ug->name }}</option>
                @endforeach
              </select>
              <small class="text-muted">ابتدا یک مجموعه انتخاب کنید</small>
            </div>
          </div>

          <hr class="my-2">

          {{-- 4. متن یادداشت --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">متن یادداشت</label>
            <div class="col-sm-10">
              <textarea name="content" rows="5" class="form-control"
                        placeholder="متن یادداشت را وارد کنید..." required maxlength="5000">{{ old('content') }}</textarea>
            </div>
          </div>

          {{-- 5. نوع نمایش --}}
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

          {{-- 6. یادداشت اجباری --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">یادداشت اجباری</label>
            <div class="col-sm-10 d-flex align-items-center">
              <input type="checkbox" id="is-mandatory" name="is_mandatory" value="1" class="mr-2">
              <label for="is-mandatory" class="mb-0">بله، کاربر باید این یادداشت را ببیند</label>
            </div>
          </div>

          {{-- 7. یادآوری --}}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-left">یادآوری</label>
            <div class="col-sm-10">
              <div class="d-flex align-items-center">
                <input type="checkbox" id="reminder-toggle" class="mr-2">
                <label for="reminder-toggle" class="mb-0">تنظیم تاریخ یادآوری</label>
              </div>

              <div id="reminder-section" style="display:none;margin-top:10px">
                {{-- روش اول: تعداد روز --}}
                <div class="d-flex align-items-center flex-wrap" style="gap:10px;margin-bottom:8px">
                  <label class="mb-0" style="white-space:nowrap">تعداد روز از امروز:</label>
                  <input type="number" id="reminder-days" class="form-control"
                         style="width:90px" min="1" max="3650" placeholder="مثلاً ۳۰">
                  <span id="reminder-jalali-display" class="text-primary" style="font-size:0.95em;font-weight:600"></span>
                </div>
                {{-- روش دوم: تاریخ مستقیم --}}
                <div class="d-flex align-items-center" style="gap:10px">
                  <label class="mb-0" style="white-space:nowrap;font-size:0.9em;color:#666">یا تاریخ مستقیم:</label>
                  <input type="date" id="reminder-date-direct" class="form-control" style="max-width:190px">
                </div>
                {{-- فیلد پنهان برای ارسال --}}
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
      var gropSelect      = document.getElementById('grop-select');
      var ugropSelect     = document.getElementById('usergrop-select');
      var userSearch      = document.getElementById('user-search');
      var preview         = document.getElementById('user-info-preview');
      var userIdInput     = document.getElementById('user-id-input');
      var allLabel        = document.getElementById('all-users-label');
      var reminderToggle  = document.getElementById('reminder-toggle');
      var reminderSection = document.getElementById('reminder-section');
      var reminderDays    = document.getElementById('reminder-days');
      var reminderJalali  = document.getElementById('reminder-jalali-display');
      var reminderDirect  = document.getElementById('reminder-date-direct');
      var reminderHidden  = document.getElementById('reminder-date-hidden');
      var debounceTimer   = null;

      var allUgropOptions = Array.from(ugropSelect.options);

      /* ── helpers ─────────────────────────────────────────── */
      function getSelectedGropIds() {
        return Array.from(gropSelect.selectedOptions).map(function (o) { return o.value; });
      }
      function getSelectedUgropIds() {
        return Array.from(ugropSelect.selectedOptions).map(function (o) { return o.value; });
      }
      function pad2(n) { return String(n).padStart(2, '0'); }

      function dateToGregorianStr(d) {
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
      }

      function dateToJalaliStr(d) {
        if (typeof jalaali === 'undefined') return '';
        var j = jalaali.toJalaali(d.getFullYear(), d.getMonth() + 1, d.getDate());
        return j.jy + '/' + pad2(j.jm) + '/' + pad2(j.jd);
      }

      /* ── user found: lock groups ─────────────────────────── */
      function lockGroups() {
        gropSelect.disabled    = true;
        ugropSelect.disabled   = true;
        allLabel.style.display = 'none';
      }
      function unlockGroups() {
        gropSelect.disabled  = false;
        var ids = getSelectedGropIds();
        ugropSelect.disabled = (ids.length !== 1);
        updateAllUsersLabel();
      }

      /* ── usergrop filter ─────────────────────────────────── */
      function filterUgropOptions(gropIds) {
        ugropSelect.innerHTML = '';
        allUgropOptions.forEach(function (opt) {
          if (gropIds.length === 0 || gropIds.indexOf(opt.dataset.gropId) !== -1) {
            ugropSelect.appendChild(opt.cloneNode(true));
          }
        });
      }

      function updateAllUsersLabel() {
        var hasGrop  = getSelectedGropIds().length > 0;
        var hasUgrop = getSelectedUgropIds().length > 0;
        var hasUser  = userIdInput.value !== '';
        allLabel.style.display = (hasGrop && !hasUgrop && !hasUser) ? 'block' : 'none';
      }

      /* ── grop change ─────────────────────────────────────── */
      gropSelect.addEventListener('change', function () {
        if (userIdInput.value !== '') return; // user already selected
        var ids = getSelectedGropIds();
        if (ids.length === 1) {
          filterUgropOptions(ids);
          ugropSelect.disabled = false;
        } else {
          ugropSelect.disabled = true;
        }
        updateAllUsersLabel();
      });

      ugropSelect.addEventListener('change', function () {
        updateAllUsersLabel();
      });

      /* ── user search ─────────────────────────────────────── */
      userSearch.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        var val = this.value.trim();

        if (val.length < 2) {
          preview.style.display = 'none';
          userIdInput.value = '';
          unlockGroups();
          return;
        }

        debounceTimer = setTimeout(function () {
          fetch('/admin/notes/user-info?id=' + encodeURIComponent(val))
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (data.found) {
                var parts = ['نام: <strong>' + data.name + '</strong>'];
                if (data.lawyer_name) parts.push('وکیل: ' + data.lawyer_name);
                parts.push('آی‌دی: <strong>' + data.id + '</strong>');
                if (data.hash) parts.push('کد اختصاصی: <strong>' + data.hash + '</strong>');
                parts.push('تلفن: ' + (data.mobile || '—'));
                if (data.group)    parts.push('مجموعه: ' + data.group);
                if (data.usergrop) parts.push('گروه: ' + data.usergrop);
                preview.innerHTML = parts.join(' &nbsp;|&nbsp; ');
                preview.style.display = 'block';
                userIdInput.value = data.id;
                lockGroups();
              } else {
                preview.innerHTML = '<span class="text-danger">کاربری با این مشخصات یافت نشد</span>';
                preview.style.display = 'block';
                userIdInput.value = '';
                unlockGroups();
              }
            })
            .catch(function () {
              preview.innerHTML = '<span class="text-danger">خطا در اتصال به سرور</span>';
              preview.style.display = 'block';
            });
        }, 400);
      });

      /* ── reminder toggle ─────────────────────────────────── */
      reminderToggle.addEventListener('change', function () {
        reminderSection.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
          reminderDays.value   = '';
          reminderDirect.value = '';
          reminderHidden.value = '';
          reminderJalali.textContent = '';
        }
      });

      /* days → jalali preview + hidden gregorian */
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

      /* direct gregorian date */
      reminderDirect.addEventListener('change', function () {
        reminderHidden.value       = this.value;
        reminderDays.value         = '';
        reminderJalali.textContent = '';
      });

    })();
  </script>

@endsection
