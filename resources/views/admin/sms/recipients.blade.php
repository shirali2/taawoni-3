@extends("theme.default")
@section("container")

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px">
        <h4 class="header-title">
          پیامک شناسه {{ $campaign->id }} — {{ $campaign->sender_name ?: $campaign->title_type }}
        </h4>
        <div class="d-flex flex-wrap" style="gap:6px">
          <a href="{{ route('admin.sms.export-excel', $campaign->id) }}"
             class="btn btn-sm btn-success waves-effect">
            <i class="fa fa-file-excel-o"></i> اکسل
          </a>
          <a href="{{ route('admin.sms.export-pdf', $campaign->id) }}"
             class="btn btn-sm btn-danger waves-effect">
            <i class="fa fa-file-pdf-o"></i> PDF
          </a>
          <button onclick="window.print()" class="btn btn-sm btn-info waves-effect">
            <i class="fa fa-print"></i> چاپ
          </button>
          <a href="{{ route('admin.sms.index') }}"
             class="btn btn-sm btn-secondary waves-effect">بازگشت</a>
        </div>
      </div>

      {{-- Campaign summary --}}
      <div class="mb-3 p-2 bg-light rounded" style="font-size:0.9em">
        @php
          $titleTypes = [
            'advertising'   => 'تبلیغاتی',
            'ceo'           => 'هیئت مدیره',
            'informational' => 'اطلاع‌رسانی',
            'warning'       => 'هشدار',
            'reminder'      => 'یادآوری',
            'other'         => 'سایر',
          ];
        @endphp
        <strong>نوع:</strong> {{ $titleTypes[$campaign->title_type] ?? $campaign->title_type }} |
        <strong>تعداد کاربر:</strong> {{ number_format($campaign->total_users) }} |
        <strong>مجموع پیامک:</strong> {{ number_format($campaign->total_sms_count) }} |
        <strong>هزینه کل:</strong> {{ number_format($campaign->total_cost) }} ریال
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">×</button>
          {{ session('success') }}
        </div>
      @endif

      {{-- Filter Bar --}}
      <form method="GET" action="{{ route('admin.sms.recipients', $campaign->id) }}"
            class="mb-3 p-3 bg-light rounded">
        <div class="row align-items-end">
          <div class="col-sm-3">
            <label class="mb-1" style="font-size:0.85em">از تاریخ</label>
            <input type="date" name="date_from" class="form-control form-control-sm"
              value="{{ request('date_from') }}">
          </div>
          <div class="col-sm-3">
            <label class="mb-1" style="font-size:0.85em">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control form-control-sm"
              value="{{ request('date_to') }}">
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">وضعیت</label>
            <select name="status" class="form-control form-control-sm">
              <option value="">همه</option>
              <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>ارسال شده</option>
              <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>ناموفق</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار</option>
            </select>
          </div>
          <div class="col-sm-4 mt-3">
            <button type="submit" class="btn btn-sm btn-info">اعمال فیلتر</button>
            <a href="{{ route('admin.sms.recipients', $campaign->id) }}"
               class="btn btn-sm btn-secondary mr-1">پاک</a>
          </div>
        </div>
      </form>

      {{-- Bulk delete form --}}
      <form method="POST" action="{{ route('admin.sms.logs.bulk-delete', $campaign->id) }}"
            id="bulk-form">
        @csrf

        <div class="mb-2">
          <button type="submit" class="btn btn-sm btn-danger"
                  onclick="return confirm('ردیف‌های انتخاب‌شده حذف شوند؟')">
            <i class="fa fa-trash"></i> حذف انتخاب‌شده‌ها
          </button>
          <label class="mr-3 mb-0" style="font-size:0.85em; font-weight:normal; cursor:pointer">
            <input type="checkbox" id="select-all"> انتخاب همه
          </label>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered table-sm" style="font-size:0.85em" id="recipients-table">
            <thead class="thead-light">
              <tr>
                <th style="width:32px"></th>
                <th>ردیف</th>
                <th>شناسه پیامک</th>
                <th>نام و نام‌خانوادگی</th>
                <th>نام وکیل</th>
                <th>شماره موبایل</th>
                <th>شناسه کاربر</th>
                <th>شماره اختصاصی</th>
                @if($isSuperadmin)
                  <th>نام مجموعه</th>
                @endif
                <th>گروه کاربری</th>
                <th>متن پیام</th>
                <th>زمان ارسال</th>
                <th>وضعیت</th>
                <th>حذف</th>
              </tr>
            </thead>
            <tbody>
              @php
                $statusLabels = [
                  'sent'    => ['label' => 'ارسال شده', 'class' => 'badge-success'],
                  'failed'  => ['label' => 'ناموفق',    'class' => 'badge-danger'],
                  'pending' => ['label' => 'در انتظار', 'class' => 'badge-warning'],
                ];
              @endphp

              @forelse($logs as $i => $log)
                <tr>
                  <td class="text-center">
                    <input type="checkbox" name="log_ids[]" value="{{ $log->id }}" class="row-check">
                  </td>
                  <td>{{ $logs->firstItem() + $i }}</td>
                  <td>{{ $log->id }}</td>
                  <td>{{ trim(optional($log->user)->name . ' ' . optional($log->user)->name2) }}</td>
                  <td>{{ optional($log->user)->lawyer_name }}</td>
                  <td>{{ $log->mobile }}</td>
                  <td>{{ $log->user_id }}</td>
                  <td>{{ optional($log->user)->kod }}</td>
                  @if($isSuperadmin)
                    <td>{{ optional(optional($log->user)->group)->name }}</td>
                  @endif
                  <td>{{ optional(optional($log->user)->group)->name }}</td>
                  <td style="max-width:220px; white-space:pre-wrap; word-break:break-word">{{ $log->message }}</td>
                  <td style="white-space:nowrap">{{ $log->sent_at ? $log->sent_at->format('Y/m/d H:i') : '-' }}</td>
                  <td>
                    @php $s = $statusLabels[$log->status] ?? ['label' => $log->status, 'class' => 'badge-secondary']; @endphp
                    <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                  </td>
                  <td>
                    <form method="POST"
                          action="{{ route('admin.sms.logs.delete', $log->id) }}"
                          class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-xs btn-danger"
                              onclick="return confirm('این ردیف حذف شود؟')">×</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $isSuperadmin ? 15 : 14 }}"
                      class="text-center text-muted py-3">گیرنده‌ای یافت نشد.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>

      <div class="mt-2">
        {{ $logs->appends(request()->query())->links() }}
      </div>

    </div>
  </div>
</div>

<style>
  @media print {
    .btn, form[method="POST"], #bulk-form > .mb-2, .pagination { display: none !important; }
    .card-box { box-shadow: none !important; border: none !important; }
  }
</style>

<script>
  document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(function (cb) {
      cb.checked = this.checked;
    }, this);
  });
</script>

@endsection
