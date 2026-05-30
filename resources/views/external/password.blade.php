<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ورود به فرم</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body { background: #f4f6fb; font-family: Tahoma, Arial, sans-serif; }
        .card-box {
            background: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }
        .btn-primary { background-color: #188ae2; border-color: #188ae2; }
        .form-control { text-align: right; }
    </style>
</head>
<body>
<div class="container" style="margin-top: 80px; max-width: 440px;">
    <div class="card-box">
        <h4 class="text-center mb-4">ورود به فرم</h4>
        <p class="text-muted text-center mb-4">برای دسترسی به این فرم، رمز عبور را وارد کنید.</p>

        @if($errors->has('password'))
            <div class="alert alert-danger">
                {{ $errors->first('password') }}
            </div>
        @endif

        <form method="POST" action="/external/form/{{ $token }}/verify">
            @csrf
            <div class="form-group">
                <label for="password">رمز عبور</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="رمز عبور را وارد کنید"
                       required
                       autofocus/>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3">ورود</button>
        </form>
    </div>
</div>
</body>
</html>
