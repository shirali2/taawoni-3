@extends("theme.default")
@section("container")

<div class="row">
  <div class="col-md-8 col-lg-6">
    <div class="card-box">
      <div class="m-t-0 m-b-20 d-flex justify-content-between align-items-center">
        <h4 class="header-title">ایمپورت یادداشت از اکسل</h4>
        <a href="{{ route('admin.notes.index') }}" class="btn btn-secondary btn-sm">بازگشت</a>
      </div>

      @if($errors->has('import'))
        <div class="alert alert-danger">
          <strong>خطاهای اعتبارسنجی:</strong>
          <ul class="mb-0 mt-1">
            @foreach((array)$errors->get('import') as $rowErrors)
              @foreach((array)$rowErrors as $err)
                <li>{{ $err }}</li>
              @endforeach
            @endforeach
          </ul>
        </div>
      @endif

      <div class="alert alert-info" style="font-size:0.88em">
        <strong>فرمت فایل اکسل:</strong><br>
        ستون‌های مورد نیاز: <code>user_id</code>، <code>content</code>، <code>is_mandatory</code>، <code>visibility</code><br>
        مقادیر مجاز برای <code>visibility</code>: <code>admin_only</code> یا <code>admin_and_user</code>
      </div>

      <form method="POST" action="{{ route('admin.notes.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>فایل اکسل <span class="text-danger">*</span></label>
          <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
          @error('file')
            <span class="text-danger" style="font-size:0.85em">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary waves-effect waves-light">
          <i class="fa fa-upload"></i> شروع ایمپورت
        </button>
      </form>
    </div>
  </div>
</div>

@endsection
