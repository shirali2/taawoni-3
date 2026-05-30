@extends("theme.manager")
@section("container")
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <div class="row">
    <div class="col-12 mb-3">
      <div  class="card-box table-responsive">
        <div class="m-t-0 m-b-30">
          <h4 class="header-title d-inline-block ">لیست صورت حساب کلی کاربران</h4>
        </div>

        <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
          {{--<button type="button" id="basic"--}}
          {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
          <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
            <thead>
              <tr role="row">
                <th style="width: 15px">شناسه کاربر</th>
                <th>کداختصاصی</th>
                <th>نام نام خانوادگی</th>
                <th>شماره موبایل</th>
                <th>گروه کاربری</th>
                <th>مجموع تعهدات و بدهی (تومان)</th>
                <th>مجموع جرائم (تومان)</th>
                <th>مجموع واریزی ها (تومان)</th>
                <th>جمع بدهی و جرائم (تومان)</th>
                <th>تخفیف (تومان)</th>
                <th>مانده بدهی نهایی (تومان)</th>
              </tr>
            </thead>

            <tbody>
              @foreach($users as $user)
                @php $final_debt_balance = ($user['Price_Invoice'] + $user['Price_Penalty']) - $user['Price_Paid'] @endphp
                <tr role="row" class="odd">
                  <td>{{$user["id"]}}</td>
                  <td>{{$user["hash"]}}</td>
                  <td>{{$user["name"]}} {{$user["name2"]}}</td>
                  <td>{{$user["mobile"]}}</td>
                  <td>{{$user['Name_User_Grops']}}</td>
                  <td>{{number_format($user['Price_Invoice'])}}</td>
                  <td>{{number_format($user['Price_Penalty'])}}</td>
                  <td>{{number_format($user['Price_Paid'] - $user['Price_Discount'])}}</td>
                  <td>{{number_format($user['Price_Invoice'] + $user['Price_Penalty'])}}</td>
                  <td>{{number_format($user['Price_Discount'])}}</td>
                  <td>{{number_format($final_debt_balance)}}@if($final_debt_balance > 0)<br/><span class="small">( {{$convert_number_to_persian_words->get($final_debt_balance)}} )</span>@endif</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- <script src="/assets/js/printThis.js"></script>
  <script type="text/javascript" language="JavaScript">
    {{--$('#basic').on("click", function () {--}}
        {{--$('.demo').printThis({--}}
            {{--base: "https://jasonday.github.io/printThis/",--}}
            {{--header:"<h3 style='text-align: right !important;margin-bottom: 50px;'>فهرست کاربران</h3>",--}}
            {{--loadCSS: ["{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/bootstrap.min.css","{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/font.css"],--}}
            {{--importCSS:true,--}}
            {{--importStyle: true--}}
        {{--});--}}
    {{--})--}}
  </script> -->
@endsection