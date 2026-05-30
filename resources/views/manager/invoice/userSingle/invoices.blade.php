@extends('theme.manager')
@section('container')
    @php
        $first_invoice = isset($invoices[0])
            ? $invoices[0]
            : [
                'name' => '',
                'name2' => '',
                'ID_User' => '',
                'hash' => '',
            ];
    @endphp
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <div class="card-box table-responsive mb-4">
        <div class="m-b-30">
            <h4 class="header-title d-inline-block">فهرست صورت حساب {{ $first_invoice['name'] }}
                {{ $first_invoice['name2'] }} - شناسه کاربری {{ $first_invoice['ID_User'] }} - کد
                {{ $first_invoice['hash'] }} - تاریخ گزارش {{ verta()->format('j-n-Y') }}</h4>
            @if(\App\Http\Controllers\managerController::managerAccessLevel(70) && $invoices->contains(function ($inv) { return $inv['remainder_commitment'] > 0; }))
            <a href="/manager/invoice/bulk-pay/{{ $id_user }}"
               class="btn btn-info waves-effect waves-light float-right ml-2">واریزی انبوه</a>
            @endif
        </div>

        <div>
            <table id="datatable-buttons" class="table demo table-striped table-bordered" cellspacing="0">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>شماره سند</th>
                        <th>تاریخ ایجاد</th>
                        <th>تاریخ تعهد</th>
                        <th>عنوان</th>
                        <th>شرح</th>
                        <th>مبلغ تعهد (تومان) </th>
                        <th>مانده تعهد (تومان)</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice['id'] }}</td>
                            <td>{{ $invoice['number'] }}</td>
                            <td>{{ $invoice['am_start'] }}</td>
                            <td>{{ $invoice['am_end'] }}</td>
                            <td>{{ $invoice['title'] }}</td>
                            <td>{{ $invoice['text'] }}</td>
                            <td>{{ number_format($invoice['price']) }}</td>
                            <td>{{ number_format($invoice['remainder_commitment']) }}</td>
                            <td>
                                <a href="/manager/invoice/user/{{ $invoice['id'] }}/{{ $first_invoice['ID_User'] }}"
                                    data-toggle="tooltip" data-original-title="فرم های پرشده" class="btn btn-success"><i
                                        class="fa fa-eye"></i></a>
                                @if (\App\Http\Controllers\managerController::managerAccessLevel(75))
                                    <button class="btn btn-danger btn-delete" data-id="{{ $invoice['id'] }}"
                                        data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i
                                            class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).on("click", ".btn-delete", function() {
            let id_invoice = $(this).data("id"),
                id_user = {{ $id_user }};

            Swal.fire({
                title: "آیا از انجام عملیات حذف مطمئن هستید؟",
                text: "این عملیات قابل بازگشت نیست!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "بله، حذف شود!",
                cancelButtonText: "لغو"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/manager/invoice/invoiceUserDelete/${id_invoice}/${id_user}`, // مسیر حذف در سرور
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            _method: "delete", // روش حذف برای Laravel
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("اطلاع رسانی!",
                                    "عملیات حذف صورتحساب کاربر با موفقیت انجام شد.",
                                    "success");
                                location.reload();
                            } else {
                                Swal.fire({
                                    title: "آیا از انجام عملیات حذف مطمئن هستید؟",
                                    text: "در این صورتحساب برای کاربر مبالغ پرداختی و یا جریمه ثبت شده است.!",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#d33",
                                    cancelButtonColor: "#3085d6",
                                    confirmButtonText: "بله، حذف شود!",
                                    cancelButtonText: "لغو"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: `/manager/invoice/invoiceUserDelete/${id_invoice}/${id_user}`, // مسیر حذف در سرور
                                            type: "POST",
                                            data: {
                                                check_pays_fines: false,
                                                _token: $(
                                                    'meta[name="csrf-token"]'
                                                ).attr("content"),
                                                _method: "delete", // روش حذف برای Laravel
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire(
                                                        "اطلاع رسانی!",
                                                        "عملیات حذف صورتحساب کاربر با موفقیت انجام شد.",
                                                        "success"
                                                    );
                                                    location.reload();
                                                }
                                            },
                                            error: function() {
                                                Swal.fire(
                                                    "خطا!",
                                                    "مشکلی در حذف فرم پیش آمد.",
                                                    "error");
                                            }
                                        });
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire("خطا!", "مشکلی در حذف فرم پیش آمد.", "error");
                        }
                    });
                }
            });

            $('#close-delete').click(function() {
                $('.swal2-close').trigger('click')
            })
        });
    </script>
@endsection
