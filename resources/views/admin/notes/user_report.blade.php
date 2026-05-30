<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>گزارش یادداشت‌های کاربر {{ $targetUser->id }}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Tahoma, Arial, sans-serif; direction: rtl; font-size: 13px; color: #222; margin: 20px; }
    h1 { font-size: 1.3em; margin-bottom: 4px; }
    .meta { color: #555; font-size: 0.88em; margin-bottom: 20px; }
    .section-title { font-size: 1em; font-weight: bold; margin: 20px 0 8px; padding: 4px 8px;
      border-right: 4px solid #007bff; background: #f0f4ff; }
    .section-title.confidential { border-right-color: #dc3545; background: #fff5f5; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: right; font-size: 0.92em; }
    thead th { background: #e9ecef; font-weight: bold; }
    .badge-confidential { background: #dc3545; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 0.78em; }
    .badge-public { background: #28a745; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 0.78em; }
    .print-btn { margin-bottom: 16px; }
    @media print {
      .print-btn { display: none; }
      body { margin: 8px; }
    }
  </style>
</head>
<body>

<div class="print-btn">
  <button onclick="window.print()" style="padding:6px 14px;cursor:pointer;">چاپ / PDF</button>
  <a href="{{ route('admin.notes.user-report-pdf', $targetUser->id) }}"
     style="padding:6px 14px;text-decoration:none;background:#dc3545;color:#fff;border-radius:4px;margin-right:8px;">
    دانلود PDF
  </a>
  <a href="{{ route('admin.notes.index') }}"
     style="padding:6px 14px;text-decoration:none;background:#6c757d;color:#fff;border-radius:4px;margin-right:8px;">
    بازگشت
  </a>
</div>

<h1>گزارش یادداشت‌های کاربر</h1>
<div class="meta">
  <strong>آی‌دی:</strong> {{ $targetUser->id }} &nbsp;|&nbsp;
  <strong>نام:</strong> {{ trim($targetUser->name . ' ' . $targetUser->name2) }} &nbsp;|&nbsp;
  <strong>کد اختصاصی:</strong> {{ $targetUser->hash ?? '-' }} &nbsp;|&nbsp;
  <strong>تاریخ چاپ:</strong> {{ verta(now())->format('Y/m/d H:i') }}
</div>

{{-- Task 7: public notes section --}}
<div class="section-title">
  <span class="badge-public">عمومی</span> یادداشت‌های قابل مشاهده توسط کاربر
  ({{ $publicNotes->count() }} مورد)
</div>

@if($publicNotes->isNotEmpty())
  <table>
    <thead>
      <tr>
        <th style="width:30px">#</th>
        <th style="width:70px">تاریخ ثبت</th>
        <th>متن یادداشت</th>
        <th style="width:80px">وضعیت</th>
        <th style="width:90px">مشاهده کاربر</th>
      </tr>
    </thead>
    <tbody>
      @foreach($publicNotes as $i => $note)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ verta($note->created_at)->format('Y/m/d') }}</td>
          <td style="white-space:pre-wrap;word-break:break-word">{{ $note->content }}</td>
          <td>{{ $note->status === 'approved' ? 'تایید شده' : 'پیش‌نویس' }}</td>
          <td>{{ $note->seen_by_user_at ? verta($note->seen_by_user_at)->format('Y/m/d') : 'ندیده' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <p style="color:#888;font-size:0.9em">یادداشت عمومی وجود ندارد.</p>
@endif

{{-- Task 7: confidential (admin-only) notes section --}}
<div class="section-title confidential">
  <span class="badge-confidential">محرمانه</span> یادداشت‌های داخلی (فقط مدیر)
  ({{ $privateNotes->count() }} مورد)
</div>

@if($privateNotes->isNotEmpty())
  <table>
    <thead>
      <tr>
        <th style="width:30px">#</th>
        <th style="width:70px">تاریخ ثبت</th>
        <th>متن یادداشت</th>
        <th style="width:80px">وضعیت</th>
        <th style="width:80px">ثبت‌کننده</th>
      </tr>
    </thead>
    <tbody>
      @foreach($privateNotes as $i => $note)
        <tr style="background:#fff9f9;">
          <td>{{ $i + 1 }}</td>
          <td>{{ verta($note->created_at)->format('Y/m/d') }}</td>
          <td style="white-space:pre-wrap;word-break:break-word">{{ $note->content }}</td>
          <td>{{ $note->status === 'approved' ? 'تایید شده' : 'پیش‌نویس' }}</td>
          <td>{{ optional($note->admin)->name ?? 'مدیر' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  <p style="color:#888;font-size:0.9em">یادداشت محرمانه‌ای وجود ندارد.</p>
@endif

</body>
</html>
