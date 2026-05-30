@extends("theme.user")
@section("container")
@php $remainder_commitment = $invoice_user['price'] + $invoice_user['price_fine'] - $price_active - $price_discount; //مانده تعهد
// dd($grop["active_pay"]);
@endphp

  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <style>
    .widget-user h2{
      font-size: 25px
    }
  </style>

  <script src="/assets/js/modernizr.min.js"></script>
    {{-- @if($grop['active_pay']==1) --}}
      @php foreach(array(
            'online-payment' => array(
              'Name Persian' => 'پرداخت آنلاین',
              'Inputs' => array(
                array(
                  'Title' => 'مبلغ (تومان)',
                  'Input' => "<input type='text' name='price' placeholder='مبلغ واریزی را وارد کنید' max-value='$remainder_commitment' class='form-control' required>"
                )
              ),
              'Text Submit' => 'پرداخت'
            ),
            'create-receipt' => array(
              'Name Persian' => 'ثبت فیش',
              'Inputs' => array(
                array(
                  'Title' => 'شماره فیش',
                  'Input' => '<input type="number" name="number" placeholder="شماره فیش را وارد کنید" class="form-control" required>'
                ),
                array(
                  'Title' => 'مبلغ (تومان)',
                  'Input' => "<input type='text' name='price' placeholder='مبلغ واریزی را وارد کنید' max-value='$remainder_commitment' class='form-control' required>"
                ),
                array(
                  'Title' => 'شرح',
                  'Input' => '<input type="text" name="description" placeholder="شرح مبلغ را وارد کنید" class="form-control" required>'
                ),
                array(
                  'Title' => 'تاریخ',
                  'Input' => '<input type="text" name="creation-date" placeholder="روز / ماه / سال" id="creation-date" class="form-control" required>'
                ),
                array(
                  'Title' => 'تصویر',
                  'Input' => '<input type="file" name="picture" accept="image/png, image/jpeg, image/gif" class="form-control">'
                )
              ),
              'Text Submit' => 'ثبت'
            )
          ) as $name_english => $info): @endphp
            <div class="modal fade {{$name_english}}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title mt-0" id="myLargeModalLabel">{{$info['Name Persian']}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                  </div>

                  <div class="modal-body">
                    <form method="post" action="/user/invoice/pay/add/{{$invoice_user["id"]}}" @if($name_english == 'create-receipt') enctype="multipart/form-data" @endif>
                      @csrf

                      @foreach($info['Inputs'] as $input)
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">{{$input['Title']}}</label>
                          <div class="col-sm-8">
                            @php echo $input['Input'] @endphp
                            <ul class='parsley-errors-list'></ul>
                          </div>
                        </div>
                      @endforeach

                      <div class="text-center">
                        <button type="submit" class="btn btn-success waves-effect waves-light">{{$info['Text Submit']}}</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
          {{-- @else --}}
@php
  // Define the 'create-receipt' form info
  $info = array(
    'Name Persian' => 'ثبت فیش',
    'Inputs' => array(
      array(
        'Title' => 'شماره فیش',
        'Input' => '<input type="number" name="number" placeholder="شماره فیش را وارد کنید" class="form-control" required>'
      ),
      array(
        'Title' => 'مبلغ (تومان)',
        'Input' => "<input type='text' name='price' placeholder='مبلغ واریزی را وارد کنید' max-value='$remainder_commitment' class='form-control' required>"
      ),
      array(
        'Title' => 'شرح',
        'Input' => '<input type="text" name="description" placeholder="شرح مبلغ را وارد کنید" class="form-control" required>'
      ),
      array(
        'Title' => 'تاریخ',
        'Input' => '<input type="text" name="creation-date" placeholder="روز / ماه / سال" id="creation-date" class="form-control" required>'
      ),
      array(
        'Title' => 'تصویر',
        'Input' => '<input type="file" name="picture" accept="image/png, image/jpeg, image/gif" class="form-control">'
      )
    ),
    'Text Submit' => 'ثبت'
  );
@endphp
<div class="modal fade create-receipt" tabindex="-1" role="dialog" aria-labelledby="createReceiptModalLabel" style="display: none" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title mt-0" id="createReceiptModalLabel">{{ $info['Name Persian'] }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body">
        <form method="post" action="/user/invoice/pay/add/{{$invoice_user["id"]}}" enctype="multipart/form-data">
          @csrf

          @foreach($info['Inputs'] as $input)
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">{{ $input['Title'] }}</label>
              <div class="col-sm-8">
                {!! $input['Input'] !!}
                <ul class='parsley-errors-list'></ul>
              </div>
            </div>
          @endforeach
          <div class="text-center">
            <button type="submit" class="btn btn-success waves-effect waves-light">{{$info['Text Submit']}}</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
    <div class="row">
        <div class="col-12 mb-3">
            <div  class="card-box table-responsive">
                <div class="float-right w-100 m-t-0 m-b-30">
                  <h4 class="header-title d-inline-block " style="line-height: 30px;" > پرداخت صورتحساب {{$invoice_user["title"]}} - {{$user["name"]}} {{$user["name2"]}}- کاربری  {{$user["id"]}} - کد {{$user["hash"]}} گزارش {{verta()->format('j-n-Y')}}</h4>
                  <a href="/user/invoice" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                  @if($grop["active_pay"]==1)
                    @if($remainder_commitment != 0)
                      <button class="btn btn-primary waves-effect waves-light float-right ml-2" data-toggle="modal" data-target=".online-payment">پرداخت آنلاین</button>
                    @endif
                  @endif
                  <button class="btn btn-primary waves-effect waves-light float-right ml-2" data-toggle="modal" data-target=".create-receipt">ثبت فیش</button>

                </div>

                <div class="float-right col-12 mb-4 pr-0 pl-0">
                    <div class="float-left col-xl-3 col-md-6 pr-0 pr-md-2 pl-0 pl-md-2">
                        <div class="card-box widget-user bg-dark">
                            <div class="text-center">
                                <h2 class="text-info" data-plugin="counterup">{{number_format($remainder_commitment)}} تومان</h2>
                                <h5 class="text-white">مانده تعهد</h5>
                            </div>
                        </div>
                    </div>
                    <div class="float-left col-xl-3 col-md-6 pr-0 pr-md-2 pl-0 pl-md-2">
                        <div class="card-box widget-user bg-dark">
                            <div class="text-center">
                                <h2 class="text-success" data-plugin="counterup">{{number_format($price_active)}} تومان</h2>
                                <h5 class="text-white">مجموع واریزی</h5>
                            </div>
                        </div>
                    </div>
                    <div class="float-left col-xl-3 col-md-6 pr-0 pr-md-2 pl-0 pl-md-2">
                        <div class="card-box widget-user bg-dark">
                            <div class="text-center">
                                @if($remainder_commitment == 0)
                                    <h2 class="text-warning" data-plugin="counterup">پرداخت شد</h2>
                                @else
                                    <h2 class="text-warning" data-plugin="counterup">{{$invoice_user["number_end"]}} روز </h2>
                                @endif
                                <h5 class="text-white">زمان باقیمانده پرداخت</h5>
                            </div>
                        </div>
                    </div>
                    <div class="float-left col-xl-3 col-md-6 pr-0 pr-md-2 pl-0 pl-md-2">
                        <div class="card-box widget-user bg-dark">
                            <div class="text-center">
                                <h2 class="text-danger" data-plugin="counterup">{{number_format($invoice_user["price_fine"])}} تومان</h2>
                                <h5 class="text-white">مبلغ جریمه</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{--<button type="button" id="basic"--}}
                            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
                    <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
                        <thead>
                        <tr role="row">
                          <th class="sorting_asc"  style="width: 15px;text-align: right !important;">شماره</th>
                          <th>مبلغ (تومان)</th>
                          <th class="sorting"  style="text-align: right !important;">شرح</th>
                          <th>تاریخ</th>
                          <th>کد پیگیری</th>
                          <th>وضعیت</th>
                          <th class="sorting"  style="text-align: right !important;">تایید شده</th>
                          {{--<th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>--}}
                        </thead>

                        <tbody>
                          @foreach($invoice_pays as $invoice_pay)
                            <tr role="row" class="odd">
                              <td>{{$invoice_pay['ID']}}</td>
                              <td style="text-align: right !important;">{{number_format($invoice_pay["price"])}}</td>
                              <td style="text-align: right !important;">{{str_replace('کاربر > ', '', $invoice_pay["text"])}}</td>
                              <td style="text-align: right !important;">@if($invoice_pay['am']){{$invoice_pay['am']}}@else{{verta($invoice_pay['created_at'])}}@endif</td>
                              <td style="text-align: right !important;" class="sorting_1">{{$invoice_pay['referenceId']}}</td>
                              <td>@if(!$invoice_pay['referenceId'] && !$invoice_pay['am']) <span class="text-danger">@if($invoice_pay['active_pay'] == 3){{'در حال پرداخت'}}@else{{'پرداخت ناموفق'}}@endif</span> @else @if($invoice_pay['active_pay'] == 1) <span class="text-success">موفقیت آمیز</span> @else <span class="text-danger">در انتظار تایید</span> @endif @endif</td>
                              <td>@if($invoice_pay['active_pay'] == 1){{'بله'}}@else{{'خیر'}}@endif</td>
                              {{--<td style="display: none;" class="d-block-s">--}}
                                {{--<a href="/user/invoice/pay/{{$invoice["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="فرم های پرشده" class="btn btn-success"><i class="fa fa-eye"></i></a>--}}
                                {{--<button class="btn btn-danger btn-delete" id="{{$invoice["id"]}}" name="{{$invoice["title"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>--}}
                              {{--</td>--}}
                            </tr>
                          @endforeach
                        </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
  <script src="/assets/js/kamadatepicker.min.js"></script>
  <script>
    function number_format (number, decimals, decPoint, thousandsSep){
      number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
      const n = !isFinite(+number) ? 0 : +number
      const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
      const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
      const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
      let s = ''

      const toFixedFix = function (n, prec){
        if(('' + n).indexOf('e') === -1){
          return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
        }else{
          const arr = ('' + n).split('e')
          let sig = ''
          if(+arr[1] + prec > 0){
            sig = '+'
          }
          return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
        }
      }

      s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
      if(s[0].length > 3){
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
      }
      if((s[1] || '').length < prec){
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
      }

      return s.join(dec)
    }


    function price22(form){
      var form = $(form),
            field_price = form.find('input[name="price"]'),
              val = parseInt(field_price.val().replace(/,/g, '')),
              max = parseInt(field_price.attr('max-value')),
            element_error = field_price.next();
      element_error.empty();

      if(val > max){
        element_error.append('<li>این مقدار باید کمتر از یا برابر با ' + parseInt(max).toLocaleString() + ' باشد.</li>').show()
        return false;
      }else if(
        max > 10000
        &&
        val < 10000
      ){
        element_error.append('<li>این مقدار باید بیشتر از یا مساوی 10,000 باشد.</li>').show()
        return false;
      }
    }

    $('input[name="price"]').keyup(function(){
      $(this).val(number_format($(this).val()))
    })

    //تنظیم دیت پیکر
    kamaDatepicker('creation-date', {
      nextButtonIcon: 'fa fa-arrow-circle-right',
      previousButtonIcon: 'fa fa-arrow-circle-left',
      buttonsColor: 'blue',
      markToday: true,
      markHolidays: true,
      highlightSelectedDay: true,
      gotoToday: true
    })
  </script>

  <script>
    // نمایش پیام های سیستم
    @if(session('message'))
        Swal.fire({
            title: 'موفقیت',
            text: "{{ session('message') }}",
            icon: 'success',
            confirmButtonText: 'باشه'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'خطا',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'باشه'
        });
    @endif

    // غیرفعال کردن دکمه ثبت موقع کلیک برای جلوگیری از ثبت تکراری و حس فوری بودن
    $('form').on('submit', function(e) {
        // ابتدا چک کردن ولیدیشن قیمت
        if (typeof price22 === 'function' && price22(this) === false) {
            e.preventDefault();
            return false;
        }

        var btn = $(this).find('button[type="submit"]');
        if (btn.length > 0 && !btn.hasClass('btn-danger')) {
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> در حال ثبت...');
        }
    });
  </script>
@endsection
