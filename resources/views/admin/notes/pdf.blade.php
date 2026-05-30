<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<style>
    @font-face {
        font-family: 'DejaVu Sans';
        font-style: normal;
        font-weight: normal;
        src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype');
    }
    body {
        font-family: 'DejaVu Sans', 'Arial', sans-serif;
        direction: rtl;
        font-size: 11px;
        color: #333;
    }
    h2 {
        text-align: center;
        margin-bottom: 16px;
        font-size: 14px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    th, td {
        border: 1px solid #bbb;
        padding: 5px 7px;
        text-align: right;
    }
    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    tr:nth-child(even) td {
        background-color: #fafafa;
    }
    .badge-approved {
        color: green;
        font-weight: bold;
    }
    .badge-pending {
        color: #999;
    }
    .footer {
        margin-top: 12px;
        font-size: 9px;
        text-align: left;
        color: #777;
    }
</style>
</head>
<body>
<h2>گزارش یادداشت‌ها</h2>
<table>
    <thead>
        <tr>
            <th>شناسه</th>
            <th>کد کاربر</th>
            <th>نام کاربر</th>
            <th>گروه</th>
            <th>متن یادداشت</th>
            <th>ثبت‌کننده</th>
            <th>تاریخ ثبت</th>
            <th>وضعیت</th>
            <th>زمان مشاهده</th>
        </tr>
    </thead>
    <tbody>
        @forelse($notes as $note)
        <tr>
            <td>{{ $note->id }}</td>
            <td>{{ optional($note->user)->kod }}</td>
            <td>{{ trim(optional($note->user)->name . ' ' . optional($note->user)->name2) }}</td>
            <td>{{ optional(optional($note->user)->group)->name }}</td>
            <td>{{ \Illuminate\Support\Str::limit($note->content, 80) }}</td>
            <td>{{ optional($note->admin)->name }}</td>
            <td>{{ $note->created_at->format('Y/m/d H:i') }}</td>
            <td>
                @if($note->status === 'approved')
                    <span class="badge-approved">تایید شده</span>
                @else
                    <span class="badge-pending">در حال بررسی</span>
                @endif
            </td>
            <td>{{ $note->seen_by_user_at ? $note->seen_by_user_at->format('Y/m/d H:i') : 'مشاهده نشده' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center">یادداشتی یافت نشد.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="footer">تاریخ تهیه گزارش: {{ now()->format('Y/m/d H:i') }}</div>
</body>
</html>
