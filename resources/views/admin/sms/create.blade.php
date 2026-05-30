@extends("theme.default")
@section("container")

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-30">
        <h4 class="header-title d-inline-block">ایجاد کمپین پیامک جدید</h4>
        <a href="{{ route('admin.sms.index') }}" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
      </div>

      {{-- SMS System Status Bar --}}
      <div class="alert {{ $smsStatus === 'active' ? 'alert-success' : ($smsStatus === 'test' ? 'alert-warning' : 'alert-danger') }} p-2 mb-3">
        <strong>وضعیت سیستم پیامک:</strong>
        @if($smsStatus === 'active')
          <span class="badge badge-success">فعال</span>
        @elseif($smsStatus === 'test')
          <span class="badge badge-warning">آزمایشی (Mock)</span>
        @else
          <span class="badge badge-danger">غیرفعال</span>
        @endif
      </div>

      @foreach($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ $error }}
        </div>
      @endforeach

      <form method="POST" action="{{ route('admin.sms.store') }}" id="campaign-form" class="form-horizontal">
        @csrf

        {{-- Campaign Number --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">شماره کمپین</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" value="(خودکار)" readonly style="background:#f8f9fa">
          </div>
        </div>

        {{-- Title Type --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">نوع عنوان پیامک <span class="text-danger">*</span></label>
          <div class="col-sm-5">
            <select name="title_type" class="form-control" required>
              <option value="">انتخاب کنید...</option>
              <option value="advertising"   {{ old('title_type') === 'advertising'   ? 'selected' : '' }}>تبلیغاتی</option>
              <option value="ceo"           {{ old('title_type') === 'ceo'           ? 'selected' : '' }}>هیئت مدیره</option>
              <option value="informational" {{ old('title_type') === 'informational' ? 'selected' : '' }}>اطلاع‌رسانی</option>
              <option value="warning"       {{ old('title_type') === 'warning'       ? 'selected' : '' }}>هشدار</option>
              <option value="reminder"      {{ old('title_type') === 'reminder'      ? 'selected' : '' }}>یادآوری</option>
              <option value="other"         {{ old('title_type') === 'other'         ? 'selected' : '' }}>سایر</option>
            </select>
          </div>
        </div>

        {{-- Collection Selector --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">مجموعه</label>
          <div class="col-sm-8">
            <select id="grop-select" name="grop_ids[]" multiple class="form-control" size="5">
              @foreach($grops as $grop)
                <option value="{{ $grop->id }}">{{ $grop->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">برای انتخاب چند مورد، Ctrl نگه دارید</small>
          </div>
        </div>

        {{-- User Group Selector --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">
            گروه کاربری
            <span id="all-users-label" class="text-info" style="display:none;font-size:12px;display:block"></span>
          </label>
          <div class="col-sm-8">
            <select id="usergrop-select" name="group_ids[]" multiple class="form-control" size="5" disabled>
              @foreach($usergrops as $ug)
                <option value="{{ $ug->id }}" data-grop-id="{{ $ug->id_grop }}">{{ $ug->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">ابتدا یک مجموعه انتخاب کنید</small>
          </div>
        </div>

        {{-- Single User Search --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">کد یا شماره کاربر</label>
          <div class="col-sm-8">
            <input type="text" id="user-search" class="form-control" placeholder="شناسه یا موبایل کاربر را وارد کنید" disabled>
            <div id="user-info-preview" class="mt-2" style="display:none;padding:8px;background:#f8f9fa;border-radius:4px;"></div>
            <input type="hidden" id="user-id-input" name="user_ids[]">
          </div>
        </div>

        {{-- Active/Inactive Filter --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">وضعیت کاربران</label>
          <div class="col-sm-8">
            <div class="d-flex align-items-center" style="gap:24px">
              <label class="mb-0">
                @if(session()->hasOldInput())
                  <input type="checkbox" name="include_active" value="1" id="include-active" {{ old('include_active') ? 'checked' : '' }}>
                @else
                  <input type="checkbox" name="include_active" value="1" id="include-active" checked>
                @endif
                <span class="mr-1">کاربران فعال</span>
              </label>
              <label class="mb-0">
                <input type="checkbox" name="include_inactive" value="1" id="include-inactive" {{ old('include_inactive') ? 'checked' : '' }}>
                <span class="mr-1">کاربران غیرفعال</span>
              </label>
            </div>
            <div id="recipient-count" class="mt-1 text-muted" style="font-size:0.85em"></div>
          </div>
        </div>

        {{-- Sender Name --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">نام فرستنده</label>
          <div class="col-sm-6">
            @if(optional(session('admin'))->type === 'admin')
              <input type="text" name="sender_name" id="sender-name" class="form-control"
                value="{{ old('sender_name') }}" placeholder="نام فرستنده (اختیاری)">
            @else
              <input type="text" name="sender_name" id="sender-name" class="form-control"
                value="{{ old('sender_name') }}" placeholder="نام مجموعه" readonly>
              <small class="text-muted">نام فرستنده از مجموعه انتخابی تکمیل می‌شود</small>
            @endif
          </div>
        </div>

        {{-- Variable Insert Buttons --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">درج متغیر</label>
          <div class="col-sm-8">
            <div style="display:flex;flex-wrap:wrap;gap:5px">
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{نام_کامل}">نام کامل</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{نام}">نام</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{نام_خانوادگی}">نام خانوادگی</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{کد_ملی}">کد ملی</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{شناسه}">شناسه</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{شماره_اختصاصی}">شماره اختصاصی</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{تاریخ_امروز}">تاریخ امروز</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{شماره_واحد}">شماره واحد</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{بدهی_کل}">بدهی کل</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{واریزی_کل}">واریزی کل</button>
              <button type="button" class="btn btn-xs btn-outline-secondary var-btn" data-var="{جریمه_کل}">جریمه کل</button>
            </div>
          </div>
        </div>

        {{-- Message Textarea --}}
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">متن پیامک <span class="text-danger">*</span></label>
          <div class="col-sm-8">
            <textarea name="message_text" id="message_text" rows="6" class="form-control"
              placeholder="متن پیامک را وارد کنید..." required>{{ old('message_text') }}</textarea>
            <div class="mt-1 p-2 bg-light rounded" style="font-size:0.85em">
              تعداد حروف: <strong><span id="char_count">0</span></strong> |
              پیامک هر نفر: <strong><span id="sms_count">1</span></strong> |
              تعداد کاربران: <strong><span id="user_count">0</span></strong> |
              مجموع پیامک: <strong><span id="total_sms_count">0</span></strong> |
              هزینه هر پیامک: <strong>{{ number_format($costPerSms) }}</strong> ریال |
              هزینه کل: <strong class="text-danger"><span id="total_cost_display">0</span> ریال</strong>
            </div>
          </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="form-group row">
          <div class="col-sm-8 offset-sm-3">
            <button type="submit" name="action" value="draft" class="btn btn-secondary waves-effect w-md">
              <i class="fa fa-save"></i> ذخیره پیش‌نویس
            </button>
            <button type="submit" name="action" value="send" id="send-btn"
              class="btn btn-danger waves-effect w-md mr-2">
              <i class="fa fa-send"></i> ارسال پیامک
            </button>
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
  var senderNameInput = document.getElementById('sender-name');
  var debounceTimer   = null;
  var allUgropOptions = Array.from(ugropSelect.options);

  var costPerSms = {{ $costPerSms }};
  var userCount  = 0;

  function getSelectedGropIds() {
    return Array.from(gropSelect.selectedOptions).map(function (o) { return o.value; });
  }
  function getSelectedUgropIds() {
    return Array.from(ugropSelect.selectedOptions).map(function (o) { return o.value; });
  }

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
    if (hasGrop && !hasUgrop && !hasUser) {
      allLabel.textContent   = 'برای تمام کاربران مجموعه';
      allLabel.style.display = 'block';
    } else {
      allLabel.style.display = 'none';
    }
  }

  gropSelect.addEventListener('change', function () {
    var ids = getSelectedGropIds();
    if (ids.length === 1) {
      filterUgropOptions(ids);
      ugropSelect.disabled = false;
      // Auto-fill sender name from collection for managers (readonly field)
      if (senderNameInput && senderNameInput.readOnly) {
        var selectedOpt = gropSelect.querySelector('option:checked');
        senderNameInput.value = selectedOpt ? selectedOpt.textContent.trim() : '';
      }
    } else if (ids.length === 0) {
      filterUgropOptions([]);
      ugropSelect.disabled  = true;
      userSearch.disabled   = true;
      userIdInput.value     = '';
      preview.style.display = 'none';
      if (senderNameInput && senderNameInput.readOnly) senderNameInput.value = '';
    } else {
      ugropSelect.disabled  = true;
      userSearch.disabled   = true;
      userIdInput.value     = '';
      preview.style.display = 'none';
    }
    updateAllUsersLabel();
  });

  ugropSelect.addEventListener('change', function () {
    var ugropSelected = getSelectedUgropIds().length === 1;
    var gropSelected  = getSelectedGropIds().length === 1;
    userSearch.disabled = !(gropSelected && ugropSelected);
    if (userSearch.disabled) {
      userIdInput.value     = '';
      preview.style.display = 'none';
    }
    updateAllUsersLabel();
  });

  userSearch.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    var val = userSearch.value.trim();
    if (val.length < 2) {
      preview.style.display = 'none';
      userIdInput.value     = '';
      return;
    }
    debounceTimer = setTimeout(function () {
      fetch('/admin/notes/user-info?id=' + encodeURIComponent(val))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.found) {
            preview.innerHTML =
              '<strong>' + data.name + '</strong>' +
              (data.group    ? ' | مجموعه: ' + data.group    : '') +
              (data.usergrop ? ' | گروه: '   + data.usergrop : '');
            preview.style.display = 'block';
            userIdInput.value     = val;
            userCount = 1;
          } else {
            preview.innerHTML     = '<span class="text-danger">کاربری یافت نشد</span>';
            preview.style.display = 'block';
            userIdInput.value     = '';
            userCount = 0;
          }
          updateAllUsersLabel();
          updateCostDisplay();
        })
        .catch(function () {
          preview.innerHTML     = '<span class="text-danger">خطا در اتصال</span>';
          preview.style.display = 'block';
        });
    }, 400);
  });

  // ---- SMS Counter ----
  function updateCostDisplay() {
    var text   = document.getElementById('message_text').value;
    var len    = text.length;
    var perSms = Math.max(1, Math.ceil(len / 70));
    var total  = userCount * perSms;
    var cost   = total * costPerSms;

    document.getElementById('char_count').textContent        = len;
    document.getElementById('sms_count').textContent         = perSms;
    document.getElementById('user_count').textContent        = userCount;
    document.getElementById('total_sms_count').textContent   = total;
    document.getElementById('total_cost_display').textContent = cost.toLocaleString();
  }

  document.getElementById('message_text').addEventListener('input', updateCostDisplay);

  // ---- Variable Insert Buttons ----
  document.querySelectorAll('.var-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var ta    = document.getElementById('message_text');
      var tag   = btn.getAttribute('data-var');
      var start = ta.selectionStart;
      var end   = ta.selectionEnd;
      ta.value  = ta.value.substring(0, start) + tag + ta.value.substring(end);
      ta.selectionStart = ta.selectionEnd = start + tag.length;
      ta.focus();
      updateCostDisplay();
    });
  });

  // ---- Send Button Confirmation ----
  document.getElementById('send-btn').addEventListener('click', function (e) {
    var count = document.getElementById('user_count').textContent;
    var cost  = document.getElementById('total_cost_display').textContent;
    if (!confirm('پیامک به ' + count + ' کاربر ارسال خواهد شد.\nهزینه کل: ' + cost + ' ریال\n\nآیا اطمینان دارید؟')) {
      e.preventDefault();
    }
  });

  // ---- Auto-draft Save every 30 seconds ----
  setInterval(function () {
    if (!document.getElementById('message_text').value.trim()) return;
    var formData = new FormData(document.getElementById('campaign-form'));
    fetch('{{ route('admin.sms.draft-save') }}', {
      method: 'POST',
      body: formData,
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        console.log('[SMS] پیش‌نویس ذخیره شد — شناسه:', data.id);
      }
    })
    .catch(function () {});
  }, 30000);

  updateCostDisplay();
})();
</script>

@endsection
