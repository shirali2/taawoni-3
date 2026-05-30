@extends("theme.manager")
@section("container")

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px">
        <h4 class="header-title">یادداشت‌های کاربران</h4>
        <a href="{{ route('manager.notes.create') }}" class="btn btn-primary waves-effect waves-light">
          + ثبت یادداشت جدید
        </a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('error') }}
        </div>
      @endif

      {{-- Filter --}}
      <form method="GET" action="{{ route('manager.notes.index') }}" class="mb-3 p-3 bg-light rounded" id="filter-form">
        <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', $activeTab) }}">
        <div class="row align-items-end">
          <div class="col-sm-3">
            <label class="mb-1" style="font-size:0.85em">آی‌دی یا کد اختصاصی کاربر</label>
            <input type="text" name="user_code" class="form-control form-control-sm"
                   value="{{ request('user_code') }}" placeholder="آی‌دی یا کد اختصاصی">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">گروه کاربری</label>
            <select name="usergrop_id" class="form-control form-control-sm">
              <option value="">همه گروه‌ها</option>
              @foreach($usergrops as $ug)
                <option value="{{ $ug->id }}" {{ request('usergrop_id') == $ug->id ? 'selected' : '' }}>{{ $ug->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">نوع نمایش</label>
            <select name="visibility" class="form-control form-control-sm">
              <option value="">همه</option>
              <option value="admin_only" {{ request('visibility') === 'admin_only' ? 'selected' : '' }}>فقط مدیر</option>
              <option value="admin_and_user" {{ request('visibility') === 'admin_and_user' ? 'selected' : '' }}>مدیر و کاربر</option>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">از تاریخ</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
          </div>
          <div class="col-sm-1 d-flex align-items-center mt-3">
            <label class="mb-0" style="font-size:0.8em">
              <input type="checkbox" name="has_reminder" value="1" {{ request('has_reminder') ? 'checked' : '' }}>
              یادآوری
            </label>
          </div>
          <div class="col-sm-3 mt-3">
            <button type="submit" class="btn btn-sm btn-info">جستجو</button>
            <a href="{{ route('manager.notes.index') }}" class="btn btn-sm btn-secondary mr-1">حذف فیلتر</a>
          </div>
        </div>
      </form>

      {{-- Tabs (Task 3: added archive tab) --}}
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}"
             id="pending-tab" data-toggle="tab" href="#pending" role="tab" data-tab="pending">
            پیش‌نویس
            <span class="badge badge-secondary">{{ $pendingNotes->total() }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $activeTab === 'unviewed' ? 'active' : '' }}"
             id="unviewed-tab" data-toggle="tab" href="#unviewed" role="tab" data-tab="unviewed">
            دیده نشده
            <span class="badge badge-warning">{{ $unviewedNotes->total() }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $activeTab === 'viewed' ? 'active' : '' }}"
             id="viewed-tab" data-toggle="tab" href="#viewed" role="tab" data-tab="viewed">
            دیده شده توسط کاربر
            <span class="badge badge-success">{{ $viewedNotes->total() }}</span>
          </a>
        </li>
        {{-- Task 3: archive tab --}}
        <li class="nav-item">
          <a class="nav-link {{ $activeTab === 'archived' ? 'active' : '' }}"
             id="archived-tab" data-toggle="tab" href="#archived" role="tab" data-tab="archived">
            بایگانی
            <span class="badge badge-dark">{{ $archivedNotes->total() }}</span>
          </a>
        </li>
      </ul>

      <div class="tab-content mt-3">

        {{-- ===== PENDING TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="pending" role="tabpanel">
          <p class="text-muted" style="font-size:0.85em">یادداشت‌های ثبت‌شده که در انتظار تایید هستند.</p>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.88em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">
                    <input type="checkbox" id="check-all-notes">
                  </th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:130px">نام کاربر</th>
                  <th style="min-width:260px;max-width:320px">متن یادداشت</th>
                  <th>نوع نمایش</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">اقدامات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($pendingNotes as $i => $note)
                  <tr>
                    <td>
                      <input type="checkbox" class="note-checkbox" value="{{ $note->id }}">
                    </td>
                    <td>{{ $note->id }}</td>
                    <td style="text-align:center;font-weight:600">{{ optional($note->user)->id }}</td>
                    <td style="font-size:0.82em">{{ optional($note->user)->hash ?? '-' }}</td>
                    <td style="max-width:130px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap">
                      {{-- Task 2: content modal button --}}
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <form method="POST" action="{{ route('manager.notes.approve', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-success mb-1" style="font-size:0.78em;padding:1px 5px">تایید</button>
                      </form>
                      {{-- Task 2: renamed visibility toggle --}}
                      <form method="POST" action="{{ route('manager.notes.visibility', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-info mb-1" style="font-size:0.78em;padding:1px 5px">تغییر نمایش</button>
                      </form>
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-action="delete"
                        data-url="{{ route('manager.notes.destroy', $note->id) }}"
                        data-message="آیا از حذف این یادداشت مطمئن هستید؟">
                        حذف
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="10" class="text-center text-muted py-3">یادداشت پیش‌نویسی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2 d-flex justify-content-between align-items-center">
            <div>
              <button type="button" class="btn btn-sm btn-success" id="bulk-approve-btn">تایید انتخاب‌شده‌ها</button>
              <button type="button" class="btn btn-sm btn-danger" id="bulk-delete-btn">حذف انتخاب‌شده‌ها</button>
            </div>
            {{ $pendingNotes->appends(request()->query())->links() }}
          </div>
        </div>
        {{-- /PENDING TAB --}}

        {{-- ===== UNVIEWED TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'unviewed' ? 'show active' : '' }}" id="unviewed" role="tabpanel">
          <p class="text-muted" style="font-size:0.85em">پیام‌های تایید‌شده که هنوز توسط کاربر مشاهده نشده‌اند.</p>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.88em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:130px">نام کاربر</th>
                  <th style="min-width:260px;max-width:320px">متن پیام</th>
                  <th>نوع نمایش</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">اقدامات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($unviewedNotes as $i => $note)
                  <tr>
                    <td>{{ $unviewedNotes->firstItem() + $i }}</td>
                    <td>{{ $note->id }}</td>
                    <td style="text-align:center;font-weight:600">{{ optional($note->user)->id }}</td>
                    <td style="font-size:0.82em">{{ optional($note->user)->hash ?? '-' }}</td>
                    <td style="max-width:130px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap">
                      {{-- Task 2: content modal, no status change --}}
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <form method="POST" action="{{ route('manager.notes.visibility', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-info mb-1" style="font-size:0.78em;padding:1px 5px">تغییر نمایش</button>
                      </form>
                      {{-- Task 3: archive button --}}
                      <form method="POST" action="{{ route('manager.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-warning mb-1" style="font-size:0.78em;padding:1px 5px">بایگانی</button>
                      </form>
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-action="delete"
                        data-url="{{ route('manager.notes.destroy', $note->id) }}"
                        data-message="آیا از حذف این پیام مطمئن هستید؟">
                        حذف
                      </button>
                      <a href="{{ route('manager.notes.edit', $note->id) }}" class="btn btn-xs btn-success mb-1" style="font-size:0.78em;padding:1px 5px">
                        ویرایش
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="10" class="text-center text-muted py-3">پیامی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $unviewedNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /UNVIEWED TAB --}}

        {{-- ===== VIEWED TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'viewed' ? 'show active' : '' }}" id="viewed" role="tabpanel">
          <p class="text-muted" style="font-size:0.85em">
            بازنشانی مشاهده باعث می‌شود کاربر پیام را مجدداً ببیند.
          </p>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.88em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:130px">نام کاربر</th>
                  <th style="min-width:260px;max-width:320px">متن پیام</th>
                  <th>نوع نمایش</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">زمان مشاهده</th>
                  <th style="white-space:nowrap">اقدامات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($viewedNotes as $i => $note)
                  <tr>
                    <td>{{ $viewedNotes->firstItem() + $i }}</td>
                    <td>{{ $note->id }}</td>
                    <td style="text-align:center;font-weight:600">{{ optional($note->user)->id }}</td>
                    <td style="font-size:0.82em">{{ optional($note->user)->hash ?? '-' }}</td>
                    <td style="max-width:130px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap;font-size:0.82em">
                      <span class="text-success">
                        {{ $note->seen_by_user_at ? verta($note->seen_by_user_at)->format('Y/m/d H:i') : '-' }}
                      </span>
                    </td>
                    <td style="white-space:nowrap">
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <button type="button" class="btn btn-xs btn-warning mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-action="reset"
                        data-url="{{ route('manager.notes.reset-viewed', $note->id) }}"
                        data-message="آیا از بازنشانی وضعیت مشاهده این پیام مطمئن هستید؟ کاربر مجدداً آن را خواهد دید.">
                        بازنشانی
                      </button>
                      {{-- Task 3: archive from viewed tab --}}
                      <form method="POST" action="{{ route('manager.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-secondary mb-1" style="font-size:0.78em;padding:1px 5px">بایگانی</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="11" class="text-center text-muted py-3">پیامی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $viewedNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /VIEWED TAB --}}

        {{-- ===== ARCHIVED TAB (Task 3) ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'archived' ? 'show active' : '' }}" id="archived" role="tabpanel">
          <p class="text-muted" style="font-size:0.85em">یادداشت‌های بایگانی‌شده مجموعه شما.</p>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.88em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:130px">نام کاربر</th>
                  <th style="min-width:260px;max-width:320px">متن یادداشت</th>
                  <th>وضعیت</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">اقدامات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($archivedNotes as $i => $note)
                  <tr>
                    <td>{{ $archivedNotes->firstItem() + $i }}</td>
                    <td>{{ $note->id }}</td>
                    <td style="text-align:center;font-weight:600">{{ optional($note->user)->id }}</td>
                    <td style="font-size:0.82em">{{ optional($note->user)->hash ?? '-' }}</td>
                    <td style="max-width:130px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td>
                      @if($note->status === 'approved')
                        <span class="badge badge-success">تایید شده</span>
                      @else
                        <span class="badge badge-secondary">پیش‌نویس</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap">
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <form method="POST" action="{{ route('manager.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-success mb-1" style="font-size:0.78em;padding:1px 5px">بازگرداندن</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="10" class="text-center text-muted py-3">یادداشت بایگانی‌شده‌ای یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $archivedNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /ARCHIVED TAB --}}

      </div>{{-- /tab-content --}}
    </div>{{-- /card-box --}}
  </div>
</div>

<form id="delete-form" method="POST" style="display:none">@csrf @method('DELETE')</form>
<form id="reset-form"  method="POST" style="display:none">@csrf</form>
<form id="bulk-approve-form" method="POST" action="{{ route('manager.notes.bulk-approve') }}" style="display:none">@csrf</form>
<form id="bulk-delete-form" method="POST" action="{{ route('manager.notes.bulk-destroy') }}" style="display:none">@csrf @method('DELETE')</form>

{{-- Task 2: Note content modal --}}
<div class="modal fade" id="noteContentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="noteContentModalLabel">محتوای یادداشت</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <p id="noteContentModalBody" style="white-space:pre-wrap;direction:rtl;text-align:right;line-height:1.8;font-size:1em;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
      </div>
    </div>
  </div>
</div>

<script>
// Track active tab for filter form AND update URL so back() preserves tab
document.querySelectorAll('[data-tab]').forEach(function(tab) {
  tab.addEventListener('click', function() {
    var tabName = this.getAttribute('data-tab');
    document.getElementById('active_tab_input').value = tabName;
    // Update URL so HTTP_REFERER includes active_tab after actions (delete, approve, etc.)
    var url = new URL(window.location.href);
    url.searchParams.set('active_tab', tabName);
    history.replaceState(null, '', url.toString());
  });
});

// Task 2: note content modal
document.querySelectorAll('.btn-view-note').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.getElementById('noteContentModalLabel').textContent = this.getAttribute('data-title');
    document.getElementById('noteContentModalBody').textContent = this.getAttribute('data-content');
    $('#noteContentModal').modal('show');
  });
});

// Confirm actions (delete / reset-viewed)
document.querySelectorAll('[data-action]').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var action  = this.getAttribute('data-action');
    var url     = this.getAttribute('data-url');
    var message = this.getAttribute('data-message');

    Swal.fire({
      title: 'تأیید عملیات',
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: action === 'delete' ? '#d33' : '#f0ad4e',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'بله، انجام شود',
      cancelButtonText: 'انصراف',
      reverseButtons: true
    }).then(function(result) {
      if (result.isConfirmed) {
        var form = document.getElementById(action === 'delete' ? 'delete-form' : 'reset-form');
        form.action = url;
        form.submit();
      }
    });
  });
});

// Bulk actions
var checkAll = document.getElementById('check-all-notes');
if (checkAll) {
  checkAll.addEventListener('change', function() {
    var isChecked = this.checked;
    document.querySelectorAll('.note-checkbox').forEach(function(cb) {
      cb.checked = isChecked;
    });
  });
}

document.getElementById('bulk-approve-btn') && document.getElementById('bulk-approve-btn').addEventListener('click', function() {
  var ids = Array.from(document.querySelectorAll('.note-checkbox:checked')).map(function(cb) { return cb.value; });
  if (ids.length === 0) { alert('ابتدا یادداشت‌هایی را انتخاب کنید.'); return; }
  Swal.fire({
    title: 'تایید انتخاب‌شده‌ها',
    text: ids.length + ' یادداشت تایید شود؟',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonText: 'انصراف',
    confirmButtonText: 'بله'
  }).then(function(result) {
    if (result.isConfirmed) {
      var form = document.getElementById('bulk-approve-form');
      ids.forEach(function(id) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        form.appendChild(inp);
      });
      form.submit();
    }
  });
});

document.getElementById('bulk-delete-btn') && document.getElementById('bulk-delete-btn').addEventListener('click', function() {
  var ids = Array.from(document.querySelectorAll('.note-checkbox:checked')).map(function(cb) { return cb.value; });
  if (ids.length === 0) { alert('ابتدا یادداشت‌هایی را انتخاب کنید.'); return; }
  Swal.fire({
    title: 'حذف انتخاب‌شده‌ها',
    text: ids.length + ' یادداشت حذف شود؟ (این عمل غیرقابل بازگشت است)',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonText: 'انصراف',
    confirmButtonText: 'بله، حذف شوند'
  }).then(function(result) {
    if (result.isConfirmed) {
      var form = document.getElementById('bulk-delete-form');
      ids.forEach(function(id) {
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        form.appendChild(inp);
      });
      form.submit();
    }
  });
});
</script>

@endsection
