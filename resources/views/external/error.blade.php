<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>خطا</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f4f6fb; font-family: Tahoma, Arial, sans-serif; }
        .card-box { background: #fff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
    </style>
</head>
<body>
<div class="container" style="margin-top: 80px; max-width: 480px;">
    <div class="card-box text-center">
        <i class="fa fa-exclamation-triangle text-danger" style="font-size: 48px;"></i>
        <h4 class="mt-3">{{ $message }}</h4>
        <p class="text-muted">لطفاً با مدیر سیستم تماس بگیرید.</p>
    </div>
</div>
</body>
</html>
