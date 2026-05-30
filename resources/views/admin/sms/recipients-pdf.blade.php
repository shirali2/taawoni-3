<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; direction: rtl; }
    h4 { font-size: 14px; margin-bottom: 6px; }
    .summary { font-size: 11px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: right; }
    thead { background: #e9ecef; }
    .badge-sent    { color: #155724; }
    .badge-failed  { color: #721c24; }
    .badge-pending { color: #856404; }
  </style>
</head>
<body>
  <h4>گزارش گیرندگان پیامک — شناسه {{ $campaign->id }}</h4>
  <div class="summary">
    نوع: {{ $campaign->title_type }} |
    تعداد کاربر: {{ number_format($campaign->total_users) }} |
    مجموع پیامک: {{ number_format($campaign->total_sms_count) }} |
    هزینه کل: {{ number_format($campaign->total_cost) }} ریال
  </div>

  <table>
    <thead>
      <tr>
        <th>ردیف</th>
        <th>شناسه</th>
        <th>نام و نام‌خانوادگی</th>
        <th>نام وکیل</th>
        <th>موبایل</th>
        <th>شناسه کاربر</th>
        <th>شماره اختصاصی</th>
        @if($isSuperadmin)<th>نام مجموعه</th>@endif
        <th>گروه کاربری</th>
        <th>متن پیام</th>
        <th>زمان ارسال</th>
        <th>وضعیت</th>
      </tr>
    </thead>
    <tbody>
      @foreach($logs as $i => $log)
        @php
          $statusMap = ['sent' => 'ارسال شده', 'failed' => 'ناموفق', 'pending' => 'در انتظار'];
          $statusClass = ['sent' => 'badge-sent', 'failed' => 'badge-failed', 'pending' => 'badge-pending'];
        @endphp
        <tr>
          <td>{{ $i + 1 }}</td>
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
          <td>{{ $log->message }}</td>
          <td>{{ $log->sent_at ? $log->sent_at->format('Y/m/d H:i') : '-' }}</td>
          <td class="{{ $statusClass[$log->status] ?? '' }}">
            {{ $statusMap[$log->status] ?? $log->status }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
