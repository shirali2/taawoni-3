@extends('theme.default')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">کیف پول مدیر</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">موجودی فعلی</h5>
                    <h2 class="text-primary">{{ number_format($wallet->balance) }} ریال</h2>
                    <p class="text-muted">حداقل موجودی: {{ number_format($wallet->min_threshold) }} ریال</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">شارژ کیف پول</h5>
                    <form method="POST" action="/admin/wallets/{{ $managerId }}/credit">
                        @csrf
                        <div class="form-group">
                            <label>مبلغ (ریال)</label>
                            <input type="number" name="amount" class="form-control" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>توضیحات</label>
                            <input type="text" name="description" class="form-control" maxlength="255">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">شارژ</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">تعیین حداقل موجودی</h5>
                    <form method="POST" action="/admin/wallets/{{ $managerId }}/threshold">
                        @csrf
                        <div class="form-group">
                            <label>حداقل موجودی (ریال)</label>
                            <input type="number" name="min_threshold" class="form-control" min="0" value="{{ $wallet->min_threshold }}" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">ذخیره</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">تاریخچه تراکنش‌ها</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نوع</th>
                                <th>مبلغ (ریال)</th>
                                <th>توضیحات</th>
                                <th>تاریخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($tx->type === 'credit')
                                        <span class="badge badge-success">واریز</span>
                                    @else
                                        <span class="badge badge-danger">برداشت</span>
                                    @endif
                                </td>
                                <td>{{ number_format($tx->amount) }}</td>
                                <td>{{ $tx->description ?? '—' }}</td>
                                <td>{{ $tx->created_at }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">تراکنشی یافت نشد.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
