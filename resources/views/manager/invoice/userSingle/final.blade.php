@extends("theme.manager")
@section("container")
@php
$first_invoice = isset($invoices[0]) ? $invoices[0] : array(
  'name' => '',
  'name2' => '',
  'ID_User' => '',
  'hash' => ''
);
$sum_prices_invoices = 0; //جمع کل
$price_penalty = 0; //قیمت جریمه
$price_paid = 0; //قیمت پرداخت شده
$price_discount = 0; //قیمت تخفیف
@endphp
<button onclick="generatePDF()">دانلود PDF</button>
  <div class="card-box table-responsive" id="divFinal">
    <div class="m-b-30 text-center">
      <h4 class="header-title d-inline-block">{{ $grop['name'] }}</h4>

    </div>
    <div class="m-b-30 text-center">
      <h4 class="header-title d-inline-block">فهرست صورت حساب نهایی {{$first_invoice['name']}} {{$first_invoice['name2']}} - شناسه کاربری {{$first_invoice['ID_User']}} - کد {{$first_invoice['hash']}} - تاریخ گزارش {{verta()->format('j-n-Y')}}</h4>
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
          @foreach($invoices as $invoice)
            @php
              $sum_prices_invoices += $invoice['price'];
              $price_penalty += $invoice['Price_Penalty'];
              $price_paid += $invoice['Price_Paid'];
              $price_discount += $invoice['Price_Discount'];
            @endphp
            <tr>
              <td>{{$invoice['id']}}</td>
              <td>{{$invoice['am_start']}}</td>
              <td>{{$invoice['title']}}</td>
              <td>{{number_format($invoice['price'])}}</td>
              <td></td>
            </tr>
          @endforeach

          <tr class="text-white">
            <td class="bg-danger"></td>
            <td class="bg-danger"></td>
            <td class="bg-danger">مجموع جرائم</td>
            <td class="bg-danger">{{number_format($price_penalty)}}</td>
            <td class="bg-danger"></td>
          </tr>

          <tr class="text-white">
            <td class="bg-success"></td>
            <td class="bg-success"></td>
            <td class="bg-success">مجموع واریزی ها</td>
            <td class="bg-success"></td>
            <td class="bg-success">{{number_format($price_paid - $price_discount)}}</td>
          </tr>

          @if($price_discount > 0)
          <tr class="text-white">
            <td class="bg-info"></td>
            <td class="bg-info"></td>
            <td class="bg-info">تخفیفات</td>
            <td class="bg-info"></td>
            <td class="bg-info">{{number_format($price_discount)}}</td>
          </tr>
          @endif

          <tr>
            <td class="bg-warning"></td>
            <td class="bg-warning"></td>
            <td class="bg-warning">جمع</td>
            <td class="bg-warning">{{number_format($sum_prices_invoices + $price_penalty)}}</td>
            <td class="bg-warning">{{number_format($price_paid)}}</td>
          </tr>

          <tr>
            <td></td>
            <td></td>
            <td class="bg-dark text-white text-nowrap">مانده بدهی: <span class="bg-white p-1 h6" style="color: red">{{number_format(($sum_prices_invoices + $price_penalty) - $price_paid)}} تومان</span></td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <script src="/assets/js/jspdf.umd2.5.1.min.js"></script>
  <script src="/assets/js/html2canvas1.4.1.min.js"></script>

  <script>
    async function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        const content = document.getElementById('divFinal');
    
        html2canvas(content).then((canvas) => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 190; // عرض تصویر در PDF
            const imgHeight = (canvas.height * imgWidth) / canvas.width; // نسبت تصویر
            
            doc.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            doc.save("output.pdf");
        });
    }
    </script>
@endsection