@extends("theme.manager")
@section("container")

<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/kamadatepicker.min.css" rel="stylesheet" />
<script src="/assets/js/modernizr.min.js"></script>

<div class="row">
    <div class="col-12 mb-3">
        <div class="card-box">
            <div class="m-t-0 m-b-20">
                <h4 class="header-title d-inline-block">
                    ثبت واریزی انبوه — {{$user['name']}} {{$user['name2']}} (کد: {{$user['hash']}})
                </h4>
                <a href="/manager/invoice/user/single/invoices/{{$user['id']}}"
                   class="btn btn-pink btn-trans ml-2 float-right">بازگشت</a>
            </div>

            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            {{-- جدول بدهی‌های جاری --}}
            <h5 class="mb-3">بدهی‌های جاری کاربر (مرتب‌شده از قدیمی‌ترین)</h5>

            @if(count($debts) === 0)
            <div class="alert alert-info">هیچ بدهی فعالی برای این کاربر یافت نشد.</div>
            @else
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped" style="direction:rtl;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان صورتحساب</th>
                            <th>تاریخ سررسید</th>
                            <th>مانده اصل (ریال)</th>
                            <th>جریمه (ریال)</th>
                            <th>مانده کل (ریال)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebt = 0; @endphp
                        @foreach($debts as $i => $debt)
                        @php $totalDebt += $debt['remaining_balance']; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $debt['title'] }}</td>
                            <td>{{ $debt['am_end'] ?? '—' }}</td>
                            <td>{{ number_format($debt['remaining_principal']) }}</td>
                            <td>{{ number_format($debt['total_penalty']) }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format($debt['remaining_balance']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="5" class="text-left">جمع کل بدهی</td>
                            <td class="text-danger">{{ number_format($totalDebt) }} ریال</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- فرم واریزی انبوه --}}
            <div class="card-box">
                <h5 class="mb-3">ثبت واریزی انبوه</h5>
                <form method="post" action="/manager/invoice/bulk-pay/{{$user['id']}}" id="bulk-pay-form">
                    @csrf

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">شماره واریزی</label>
                        <div class="col-sm-6">
                            <input type="text" required name="number" class="form-control"
                                   placeholder="شماره یکتای فیش یا واریزی را وارد کنید"
                                   value="{{ old('number') }}">
                            <small class="text-muted">این شماره پایه شناسه زیر-فیش‌ها خواهد بود (مثلاً: ۱۲۳۴-I۵)</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">مبلغ کل واریزی (ریال)</label>
                        <div class="col-sm-6">
                            <input type="text" required name="price" id="bulk-price" class="form-control"
                                   placeholder="مثلاً: ۱۰۰,۰۰۰,۰۰۰"
                                   value="{{ old('price') }}">
                            <small class="text-muted">جمع کل بدهی: <strong>{{ number_format($totalDebt) }} ریال</strong></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">تاریخ واریز</label>
                        <div class="col-sm-6">
                            <input type="text" required name="am" id="bulk-am" class="set-datepicker form-control"
                                   placeholder="۱۴۰۴/۰۱/۰۱"
                                   value="{{ old('am') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6 offset-sm-3">
                            <p class="text-info">
                                <i class="fa fa-info-circle"></i>
                                سیستم مبلغ را به‌ترتیب از قدیمی‌ترین بدهی توزیع می‌کند.
                                اگر مبلغ از کل بدهی بیشتر باشد، مازاد به عنوان اعتبار ذخیره می‌شود.
                            </p>
                            <button type="submit" id="bulk-submit-btn"
                                    class="btn btn-success waves-effect waves-light">
                                ثبت واریزی انبوه
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            {{-- اعتبارهای ذخیره‌شده --}}
            @if($credits->isNotEmpty())
            <div class="card-box mt-3">
                <h5 class="mb-3">اعتبارهای مازاد ذخیره‌شده</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" style="direction:rtl;">
                        <thead>
                            <tr>
                                <th>شماره واریزی</th>
                                <th>مبلغ اصلی (ریال)</th>
                                <th>اعتبار ذخیره‌شده (ریال)</th>
                                <th>تاریخ</th>
                                <th>ثبت‌شده در</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credits as $credit)
                            <tr>
                                <td>{{ $credit->receipt_id }}</td>
                                <td>{{ number_format($credit->original_amount) }}</td>
                                <td class="text-success font-weight-bold">{{ number_format($credit->credit_amount) }}</td>
                                <td>{{ $credit->am }}</td>
                                <td>{{ verta($credit->created_at)->format('Y/m/d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script src="/assets/js/kamadatepicker.min.js"></script>
<script>
    var customOptions = {
        nextButtonIcon: 'fa fa-arrow-circle-right',
        previousButtonIcon: 'fa fa-arrow-circle-left',
        buttonsColor: 'blue',
        markToday: true,
        markHolidays: true,
        highlightSelectedDay: true,
        gotoToday: true
    };
    $('.set-datepicker').each(function() {
        kamaDatepicker($(this).attr('id'), customOptions);
    });

    function number_format(number) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number;
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    $('#bulk-price').on('keyup', function() {
        var raw = $(this).val().replace(/,/g, '');
        if (/^\d+$/.test(raw)) {
            $(this).val(number_format(raw));
        }
    });

    $('#bulk-pay-form').on('submit', function() {
        var btn = $('#bulk-submit-btn');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> در حال ثبت...');
    });
</script>

@endsection
