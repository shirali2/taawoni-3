@extends("theme.manager")
@section("container")
@php
$first_invoice = isset($invoice_pays[0]) ? $invoice_pays[0] : array(
  'name' => '',
  'name2' => '',
  'ID_User' => '',
  'hash' => '',
  'SUM_Prices' => 0
);
@endphp
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <div class="card-box table-responsive">
    <h4 class="header-title d-inline-block">لیست پرداختی های {{$first_invoice["name"]}} {{$first_invoice["name2"]}} - شناسه کاربری {{$first_invoice['ID_User']}} - کد {{$first_invoice["hash"]}} - تاریخ گزارش {{verta()->format('j-n-Y')}}</h4>

    <div class="text-center p-3">
      <h2 class="text-info text-break" style="font-size: 25px">{{number_format($first_invoice['SUM_Prices'])}} تومان</h2>
      <h5>جمع واریزی</h5>
    </div>

    <table class="table demo table-striped table-bordered" cellspacing="0">
      <thead>
        <tr>
          <th style="width: 15px">شناسه</th>
          <th>شماره سند</th>
          <th>تاریخ ایجاد</th>
          <th>تاریخ پرداخت</th>
          <th>شرح</th>
          <th>مبلغ واریزی (تومان)</th>
          <th>کد پیگیری پرداخت</th>
        </tr>
      </thead>

      <tbody>
        @foreach($invoice_pays as $invoice_pay)
          <tr>
            <td>{{$invoice_pay['id']}}</td>
            <td>{{$invoice_pay['number']}}</td>
            <td>{{$invoice_pay['am_start']}}</td>
            <td>@if($invoice_pay['am']){{$invoice_pay['am']}}@else{{verta($invoice_pay['created_at'])}}@endif</td>
            <td>{{$invoice_pay['text']}}</td>
            <td>{{number_format($invoice_pay['price'])}}</td>
            <td>{{$invoice_pay['referenceId']}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection