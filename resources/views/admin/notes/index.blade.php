@extends("theme.default")
@section("container")

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px">
        <h4 class="header-title">یادداشت‌ها</h4>
        <div class="d-flex flex-wrap" style="gap:6px">
          <a href="{{ route('admin.notes.create') }}" class="btn btn-primary waves-effect waves-light">+ یادداشت جدید</a>
          <a href="{{ route('admin.notes.export-excel') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}"
             class="btn btn-success waves-effect waves-light">
            <i class="fa fa-file-excel-o"></i> دانلود اکسل
          </a>
          <a href="{{ route('admin.notes.export-pdf') }}" class="btn btn-danger waves-effect waves-light">
            <i class="fa fa-file-pdf-o"></i> دانلود PDF
          </a>
          <a href="{{ route('admin.notes.import-form') }}" class="btn btn-info waves-effect waves-light">
            <i class="fa fa-upload"></i> ایمپورت یادداشت
          </a>
        </div>
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

      {{-- Filter Bar --}}
      <form method="GET" action="{{ route('admin.notes.index') }}" class="mb-3 p-3 bg-light rounded" id="filter-form">
        <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', $activeTab) }}">
        <div class="row align-items-end">
          <div class="col-sm-3">
            <label class="mb-1" style="font-size:0.85em">آی‌دی یا کد اختصاصی کاربر</label>
            <input type="text" name="user_code" class="form-control form-control-sm"
                   value="{{ request('user_code') }}" placeholder="آی‌دی یا کد اختصاصی">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">از تاریخ</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">نوع نمایش</label>
            <select name="visibility" class="form-control form-control-sm">
              <option value="">همه</option>
              <option value="admin_only" {{ request('visibility') === 'admin_only' ? 'selected' : '' }}>فقط مدیر</option>
              <option value="admin_and_user" {{ request('visibility') === 'admin_and_user' ? 'selected' : '' }}>مدیر و کاربر</option>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="mb-1" style="font-size:0.85em">مجموعه‌ها</label>
            <select name="grop_ids[]" class="form-control form-control-sm" multiple style="height:60px">
              @foreach($grops as $g)
                <option value="{{ $g->id }}" {{ in_array($g->id, (array)request('grop_ids', [])) ? 'selected' : '' }}>{{ $g->name }}</option>
              @endforeach
            </select>
            <small class="text-muted" style="font-size:0.75em">برای انتخاب چندتایی Ctrl نگه دارید</small>
          </div>
          <div class="col-sm-3 d-flex align-items-center mt-2" style="gap:8px">
            <label class="mb-0">
              <input type="checkbox" name="has_reminder" value="1" {{ request('has_reminder') ? 'checked' : '' }}>
              <span class="mr-1" style="font-size:0.85em">فقط با یادآوری</span>
            </label>
          </div>
          <div class="col-sm-9 mt-2">
            <button type="submit" class="btn btn-sm btn-info">اعمال فیلتر</button>
            <a href="{{ route('admin.notes.index') }}" class="btn btn-sm btn-secondary mr-1">حذف فیلتر</a>
          </div>
        </div>
      </form>

      {{-- Tabs --}}
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
            تایید شده – دیده نشده
            <span class="badge badge-warning">{{ $unviewedNotes->total() }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $activeTab === 'viewed' ? 'active' : '' }}"
             id="viewed-tab" data-toggle="tab" href="#viewed" role="tab" data-tab="viewed">
            تایید شده – دیده شده
            <span class="badge badge-success">{{ $viewedNotes->total() }}</span>
          </a>
        </li>
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

          {{-- Task 6: Bulk actions bar --}}
          <div class="mb-2 d-flex flex-wrap" style="gap:6px">
            <form method="POST" action="{{ route('admin.notes.approve-all') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm btn-success"
                onclick="return confirm('همه یادداشت‌های پیش‌نویس تایید شوند؟')">
                تایید همه
              </button>
            </form>
            <button type="button" class="btn btn-sm btn-outline-success" id="bulk-approve-btn">
              تایید انتخاب‌شده‌ها
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="bulk-delete-btn">
              حذف انتخاب‌شده‌ها
            </button>
            <label class="btn btn-sm btn-outline-secondary mb-0">
              <input type="checkbox" id="select-all-pending"> انتخاب همه
            </label>
          </div>

          <form id="bulk-approve-form" method="POST" action="{{ route('admin.notes.bulk-approve') }}" style="display:none">
            @csrf
          </form>
          <form id="bulk-delete-form" method="POST" action="{{ route('admin.notes.bulk-destroy') }}" style="display:none">
            @csrf
            @method('DELETE')
          </form>

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.85em">
              <thead class="thead-light">
                <tr>
                  <th style="width:28px"><input type="checkbox" id="select-all-pending-th"></th>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  {{-- Task 8: show only user ID, no registrar name column --}}
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:120px">نام کاربر</th>
                  <th>گروه</th>
                  {{-- Task 8: wider note text column with ellipsis --}}
                  <th style="min-width:260px;max-width:320px">متن یادداشت</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th>نوع نمایش</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">اقدامات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($pendingNotes as $i => $note)
                  <tr>
                    <td><input type="checkbox" class="note-checkbox" value="{{ $note->id }}"></td>
                    <td>{{ $pendingNotes->firstItem() + $i }}</td>
                    <td>{{ $note->id }}</td>
                    <td style="text-align:center;font-weight:600">{{ optional($note->user)->id }}</td>
                    <td style="font-size:0.82em">{{ optional($note->user)->hash ?? '-' }}</td>
                    <td style="max-width:120px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="font-size:0.82em">{{ optional(optional($note->user)->group)->name }}</td>
                    {{-- Task 8: ellipsis for long note text --}}
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap">
                      {{-- Task 2: "مشاهده محتوا" opens modal, no status change --}}
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <form method="POST" action="{{ route('admin.notes.approve', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-success mb-1" style="font-size:0.78em;padding:1px 5px">تایید</button>
                      </form>
                      @if($note->visibility === 'admin_only' || !$note->seen_by_user_at)
                        <a href="{{ route('admin.notes.edit', $note->id) }}" class="btn btn-xs btn-warning mb-1" style="font-size:0.78em;padding:1px 5px">ویرایش</a>
                      @endif
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-delete-url="{{ route('admin.notes.destroy', $note->id) }}">حذف</button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="12" class="text-center text-muted py-3">یادداشتی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $pendingNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /PENDING TAB --}}

        {{-- ===== UNVIEWED TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'unviewed' ? 'show active' : '' }}" id="unviewed" role="tabpanel">

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.85em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:120px">نام کاربر</th>
                  <th>گروه</th>
                  <th style="min-width:260px;max-width:320px">متن یادداشت</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th>نوع نمایش</th>
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
                    <td style="max-width:120px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="font-size:0.82em">{{ optional(optional($note->user)->group)->name }}</td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ $note->reminder_date ? verta($note->reminder_date)->format('Y/m/d') : '-' }}</td>
                    <td style="white-space:nowrap">
                      {{-- Task 2: show content modal (no status change) --}}
                      <button type="button" class="btn btn-xs btn-primary mb-1 btn-view-note" style="font-size:0.78em;padding:1px 5px"
                        data-content="{{ e($note->content) }}" data-title="یادداشت #{{ $note->id }}">
                        مشاهده
                      </button>
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-delete-url="{{ route('admin.notes.destroy', $note->id) }}">حذف</button>
                      <form method="POST" action="{{ route('admin.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-warning mb-1" style="font-size:0.78em;padding:1px 5px">بایگانی</button>
                      </form>
                      {{-- Task 2: separate visibility toggle button --}}
                      <form method="POST" action="{{ route('admin.notes.visibility', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-info mb-1" style="font-size:0.78em;padding:1px 5px">تغییر نمایش</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="11" class="text-center text-muted py-3">یادداشتی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $unviewedNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /UNVIEWED TAB --}}

        {{-- ===== VIEWED TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'viewed' ? 'show active' : '' }}" id="viewed" role="tabpanel">

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.85em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:120px">نام کاربر</th>
                  <th>گروه</th>
                  <th style="min-width:260px;max-width:320px">متن یادداشت</th>
                  <th style="white-space:nowrap">تاریخ ثبت</th>
                  <th>نوع نمایش</th>
                  <th style="white-space:nowrap">یادآوری</th>
                  <th style="white-space:nowrap">مشاهده کاربر</th>
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
                    <td style="max-width:120px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="font-size:0.82em">{{ optional(optional($note->user)->group)->name }}</td>
                    <td style="min-width:260px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.82em"
                        title="{{ $note->content }}">
                      {{ $note->content }}
                    </td>
                    <td style="white-space:nowrap;font-size:0.82em">{{ verta($note->created_at)->format('Y/m/d') }}</td>
                    <td>
                      @if($note->visibility === 'admin_and_user')
                        <span class="badge badge-success">مدیر و کاربر</span>
                      @else
                        <span class="badge badge-secondary">فقط مدیر</span>
                      @endif
                    </td>
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
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-delete-url="{{ route('admin.notes.destroy', $note->id) }}">حذف</button>
                      <form method="POST" action="{{ route('admin.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-warning mb-1" style="font-size:0.78em;padding:1px 5px">بایگانی</button>
                      </form>
                      <form method="POST" action="{{ route('admin.notes.visibility', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-info mb-1" style="font-size:0.78em;padding:1px 5px">تغییر نمایش</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="12" class="text-center text-muted py-3">یادداشتی یافت نشد.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">{{ $viewedNotes->appends(request()->query())->links() }}</div>
        </div>
        {{-- /VIEWED TAB --}}

        {{-- ===== ARCHIVED TAB ===== --}}
        <div class="tab-pane fade {{ $activeTab === 'archived' ? 'show active' : '' }}" id="archived" role="tabpanel">

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm" style="font-size:0.85em">
              <thead class="thead-light">
                <tr>
                  <th style="width:36px">#</th>
                  <th style="width:40px">ش</th>
                  <th style="width:60px;text-align:center">آی‌دی</th>
                  <th style="width:80px">کد اختصاصی</th>
                  <th style="max-width:120px">نام کاربر</th>
                  <th>گروه</th>
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
                    <td style="max-width:120px;word-break:break-word;white-space:normal">
                      {{ mb_substr(trim(optional($note->user)->name . ' ' . optional($note->user)->name2), 0, 20) }}
                    </td>
                    <td style="font-size:0.82em">{{ optional(optional($note->user)->group)->name }}</td>
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
                      <form method="POST" action="{{ route('admin.notes.archive', $note->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-xs btn-success mb-1" style="font-size:0.78em;padding:1px 5px">بازگرداندن</button>
                      </form>
                      <button type="button" class="btn btn-xs btn-danger mb-1" style="font-size:0.78em;padding:1px 5px"
                        data-delete-url="{{ route('admin.notes.destroy', $note->id) }}">حذف</button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="11" class="text-center text-muted py-3">یادداشت بایگانی‌شده‌ای یافت نشد.</td></tr>
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

{{-- Delete confirmation modal --}}
<form id="delete-note-form" method="POST" style="display:none">
  @csrf
  @method('DELETE')
</form>

{{-- Task 2: Note content modal --}}
<div class="modal fade" id="noteContentModal" tabindex="-1" role="dialog" aria-labelledby="noteContentModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="noteContentModalLabel">محتوای یادداشت</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
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
// Track active tab and update URL so back() preserves tab after actions
document.querySelectorAll('[data-tab]').forEach(function(tab) {
  tab.addEventListener('click', function() {
    var tabName = this.getAttribute('data-tab');
    document.getElementById('active_tab_input').value = tabName;
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

// Delete confirmation
document.querySelectorAll('[data-delete-url]').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var url = this.getAttribute('data-delete-url');
    Swal.fire({
      title: 'حذف یادداشت',
      text: 'آیا از حذف این یادداشت مطمئن هستید؟',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'بله، حذف شود',
      cancelButtonText: 'انصراف',
      reverseButtons: true
    }).then(function(result) {
      if (result.isConfirmed) {
        var form = document.getElementById('delete-note-form');
        form.action = url;
        form.submit();
      }
    });
  });
});

// Task 6: select all checkbox (pending tab)
function syncSelectAll(masterId, checkboxClass) {
  var master = document.getElementById(masterId);
  if (!master) return;
  master.addEventListener('change', function() {
    document.querySelectorAll('.' + checkboxClass).forEach(function(cb) {
      cb.checked = master.checked;
    });
  });
}
syncSelectAll('select-all-pending', 'note-checkbox');
syncSelectAll('select-all-pending-th', 'note-checkbox');

// Task 6: bulk approve
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

// Task 6: bulk delete
document.getElementById('bulk-delete-btn') && document.getElementById('bulk-delete-btn').addEventListener('click', function() {
  var ids = Array.from(document.querySelectorAll('.note-checkbox:checked')).map(function(cb) { return cb.value; });
  if (ids.length === 0) { alert('ابتدا یادداشت‌هایی را انتخاب کنید.'); return; }
  Swal.fire({
    title: 'حذف انتخاب‌شده‌ها',
    text: ids.length + ' یادداشت حذف شود؟ این عمل برگشت‌پذیر نیست.',
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
