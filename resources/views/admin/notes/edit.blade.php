@extends("theme.default")
@section("container")

  <div class="row">
    <div class="col-12 mb-3">
      <div class="card-box">
        <div class="m-t-0 m-b-30">
          <h4 class="header-title d-inline-block">ویرایش یادداشت #{{ $note->id }}</h4>
          <a href="{{ route('admin.notes.index') }}" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
        </div>

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('error') }}
          </div>
        @endif

        @foreach($errors->all() as $error)
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ $error }}
          </div>
        @endforeach

        <div class="alert alert-info">
          کاربر: <strong>{{ trim($note->user->name . ' ' . $note->user->name2) }}</strong>
          &nbsp;|&nbsp; کد: <strong>{{ $note->user->kod }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.notes.update', $note->id) }}" class="form-horizontal">
          @csrf

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">متن یادداشت</label>
            <div class="col-sm-8">
              <textarea name="content" rows="6" class="form-control" required maxlength="5000">{{ old('content', $note->content) }}</textarea>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">یادداشت اجباری</label>
            <div class="col-sm-8 d-flex align-items-center">
              <input type="checkbox" id="is-mandatory" name="is_mandatory" value="1"
                {{ $note->is_mandatory ? 'checked' : '' }} class="mr-2">
              <label for="is-mandatory" class="mb-0 mr-2">بله، کاربر باید این یادداشت را ببیند</label>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-3 col-form-label">یادآوری</label>
            <div class="col-sm-8">
              <input type="date" name="reminder_date" class="form-control" style="max-width:200px"
                value="{{ old('reminder_date', $note->reminder_date ? $note->reminder_date->format('Y-m-d') : '') }}">
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-8 offset-sm-3">
              <button type="submit" class="btn btn-primary waves-effect w-md waves-light">ذخیره تغییرات</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

@endsection
