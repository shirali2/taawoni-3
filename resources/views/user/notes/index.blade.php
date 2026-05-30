@extends("theme.user")
@section("container")

<div class="row">
    <div class="col-12 mb-3">
        <div class="card-box table-responsive">
            <div class="m-t-0 m-b-30">
                <h4 class="header-title d-inline-block">یادداشت‌های من</h4>
            </div>
            <table class="table table-striped table-bordered" style="direction:rtl;width:100%;">
                <thead>
                    <tr>
                        <th style="text-align:right;">ردیف</th>
                        <th style="text-align:right;">تاریخ ثبت</th>
                        <th style="text-align:right;">متن یادداشت</th>
                        <th style="text-align:right;">زمان مشاهده</th>
                        <th style="text-align:right;">وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $index => $note)
                    <tr>
                        <td style="text-align:right;">{{ $index + 1 }}</td>
                        <td style="text-align:right;">{{ verta($note->created_at)->format('Y/m/d H:i') }}</td>
                        <td style="text-align:right;white-space:pre-wrap;max-width:400px;">{{ $note->content }}</td>
                        <td style="text-align:right;">
                            @if($note->seen_by_user_at)
                                {{ verta($note->seen_by_user_at)->format('Y/m/d H:i') }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align:right;">
                            @if($note->seen_by_user_at)
                                <span style="color:#28a745;font-weight:bold;">مشاهده شده ✓</span>
                            @else
                                <span style="color:#fd7e14;font-weight:bold;">در انتظار مشاهده</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">یادداشتی وجود ندارد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
