<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتورهای کاربران</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
        }

        .invoice-box {
            width: 100%;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .page-break {
            page-break-before: always;
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .info {
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    @foreach ($user_ids as $user_id)
        @php
            $user_invoices = $invoices->where('ID_User', $user_id)->values();
            $first_invoice = isset($user_invoices[0])
                ? $user_invoices[0]
                : [
                    'name' => '',
                    'name2' => '',
                    'ID_User' => '',
                    'hash' => '',
                ];
            $sum_prices_invoices = 0; //جمع کل
            $price_penalty = 0; //قیمت جریمه
            $price_paid = 0; //قیمت پرداخت شده
        @endphp
        <div class="card-box table-responsive" id="divFinal">
            <div class="m-b-30 text-center">
                <h4 class="header-title d-inline-block">{{ $grop['name'] }}</h4>

            </div>
            <div class="m-b-30 text-center">
                <h4 class="header-title d-inline-block">فهرست صورت حساب نهایی {{ $first_invoice['name'] }}
                    {{ $first_invoice['name2'] }} - شناسه کاربری {{ $first_invoice['ID_User'] }} - کد
                    {{ $first_invoice['hash'] }} - تاریخ گزارش {{ verta()->format('j-n-Y') }}</h4>
            </div>

            <div>
                <table class="table table-striped table-bordered" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 15px">شناسه</th>
                            <th>تاریخ ایجاد</th>
                            <th>شرح</th>
                            <th>بدهکار (تومان) </th>
                            <th>بستانکار (تومان)</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($user_invoices as $invoice)
                            @php
                                $sum_prices_invoices += $invoice['price'];
                                $price_penalty += $invoice['Price_Penalty'];
                                $price_paid += $invoice['Price_Paid'];
                            @endphp
                            <tr>
                                <td>{{ $invoice['id'] }}</td>
                                <td>{{ $invoice['am_start'] }}</td>
                                <td>{{ $invoice['title'] }}</td>
                                <td>{{ number_format($invoice['price']) }}</td>
                                <td></td>
                            </tr>
                        @endforeach

                        <tr class="text-white">
                            <td class="bg-danger"></td>
                            <td class="bg-danger"></td>
                            <td class="bg-danger">مجموع جرائم</td>
                            {{-- <td class="bg-danger">{{number_format($price_penalty)}}</td> --}}
                            <td class="bg-danger"></td>
                        </tr>

                        <tr class="text-white">
                            <td class="bg-success"></td>
                            <td class="bg-success"></td>
                            <td class="bg-success">مجموع واریزی ها</td>
                            <td class="bg-success"></td>
                            <td class="bg-success">{{ number_format($price_paid) }}</td>
                        </tr>

                        <tr>
                            <td class="bg-warning"></td>
                            <td class="bg-warning"></td>
                            <td class="bg-warning">جمع</td>
                            <td class="bg-warning">{{ number_format($sum_prices_invoices + $price_penalty) }}</td>
                            <td class="bg-warning">{{ number_format($price_paid) }}</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td class="bg-dark text-white text-nowrap">مانده بدهی: <span class="bg-white p-1 h6"
                                    style="color: red">{{ number_format($sum_prices_invoices + $price_penalty - $price_paid) }}
                                    تومان</span></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

</body>

</html>
