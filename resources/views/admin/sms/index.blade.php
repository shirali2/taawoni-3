@extends("theme.default")
@section("container")

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box">

      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px">
        <h4 class="header-title">کمپین‌های پیامک</h4>
        <div class="d-flex" style="gap:6px">
          <a href="{{ route('admin.sms.settings') }}" class="btn btn-sm btn-secondary waves-effect">
            <i class="fa fa-cog"></i> تنظیمات پیامک
          </a>
          <a href="{{ route('admin.sms.create') }}" class="btn btn-sm btn-primary waves-effect waves-light">
            + کمپین جدید
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
      <form method="GET" action="{{ route('admin.sms.index') }}" class="mb-3 p-3 bg-light rounded">
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
            <label class="mb-1" style="font-size:0.85em">نوع عنوان</label>
            <select name="title_type" class="form-control form-control-sm">
              <option value="">همه</option>
              <option value="advertising"   {{ request('title_type') === 'advertising'   ? 'selected' : '' }}>تبلیغاتی</option>
              <option value="ceo"           {{ request('title_type') === 'ceo'           ? 'selected' : '' }}>هیئت مدیره</option>
              <option value="informational" {{ request('title_type') === 'informational' ? 'selected' : '' }}>اطلاع‌رسانی</option>
              <option value="warning"       {{ request('title_type') === 'warning'       ? 'selected' : '' }}>هشدار</option>
              <option value="reminder"      {{ request('title_type') === 'reminder'      ? 'selected' : '' }}>یادآوری</option>
              <option value="other"         {{ request('title_type') === 'other'         ? 'selected' : '' }}>سایر</option>
            </select>
          </div>
          <div class="col-sm-2">
            <label class="mb-1" style="font-size:0.85em">وضعیت</label>
            <select name="status" class="form-control form-control-sm">
              <option value="">همه</option>
              <option value="draft"   {{ request('status') === 'draft'   ? 'selected' : '' }}>پیش‌نویس</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار</option>
              <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>در حال ارسال</option>
              <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>ارسال شده</option>
              <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>ناموفق</option>
            </select>
          </div>
          <div class="col-sm-2 mt-3">
            <button type="submit" class="btn btn-sm btn-info">اعمال فیلتر</button>
            <a href="{{ route('admin.sms.index') }}" class="btn btn-sm btn-secondary mr-1">پاک</a>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm" style="font-size:0.88em">
          <thead class="thead-light">
            <tr>
              <th>ردیف</th>
              <th>شناسه</th>
              <th>شماره پیامک</th>
              <th>عنوان</th>
              <th>تعداد کاربر</th>
              <th>تعداد کل پیامک</th>
              <th>هزینه (ریال)</th>
              <th>وضعیت</th>
              <th>تاریخ ثبت</th>
              <th>اقدامات</th>
            </tr>
          </thead>
          <tbody>
            @php
              $titleTypes = [
                'advertising'   => 'تبلیغاتی',
                'ceo'           => 'هیئت مدیره',
                'informational' => 'اطلاع‌رسانی',
                'warning'       => 'هشدار',
                'reminder'      => 'یادآوری',
                'other'         => 'سایر',
              ];
              $statusLabels = [
                'draft'   => ['label' => 'پیش‌نویس',    'class' => 'badge-secondary'],
                'pending' => ['label' => 'در انتظار',    'class' => 'badge-warning'],
                'sending' => ['label' => 'در حال ارسال', 'class' => 'badge-info'],
                'sent'    => ['label' => 'ارسال شده',    'class' => 'badge-success'],
                'failed'  => ['label' => 'ناموفق',       'class' => 'badge-danger'],
              ];
            @endphp

            @forelse($campaigns as $i => $campaign)
              <tr>
                <td>{{ $campaigns->firstItem() + $i }}</td>
                <td>{{ $campaign->id }}</td>
                <td>{{ $campaign->campaign_number }}</td>
                <td>{{ $titleTypes[$campaign->title_type] ?? $campaign->title_type }}</td>
                <td>{{ number_format($campaign->total_users) }}</td>
                <td>{{ number_format($campaign->total_sms_count) }}</td>
                <td>{{ number_format($campaign->total_cost) }}</td>
                <td>
                  @php $s = $statusLabels[$campaign->status] ?? ['label' => $campaign->status, 'class' => 'badge-secondary']; @endphp
                  <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                </td>
                <td>{{ $campaign->created_at->format('Y/m/d H:i') }}</td>
                <td style="white-space:nowrap">
                  <a href="{{ route('admin.sms.recipients', $campaign->id) }}" class="btn btn-xs btn-info mb-1">مشاهده گیرندگان</a>
                  <form method="POST" action="{{ route('admin.sms.destroy', $campaign->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-danger mb-1" onclick="return confirm('این کمپین حذف شود؟')">حذف</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted py-3">هیچ کمپینی یافت نشد.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-2">
        {{ $campaigns->appends(request()->query())->links() }}
      </div>

    </div>
  </div>
</div>

@endsection
