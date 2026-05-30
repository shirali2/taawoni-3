@extends("theme.user")
@section("container")
  <div class="container">
    <div class="row mt-3 justify-content-center">
      @if(isset($user["day_am"]) and $user["day_am"]>0)
        <div class="col-12 col-lg-6">
          <div class="card-box">
            <h4 class="header-title mt-0 m-b-30">اشتراک باقی مانده شما</h4>

            <div class="widget-chart-1 pb-4">
              {{--<div class="widget-chart-box-1" dir="ltr">--}}
                {{--<input data-plugin="knob" data-width="100" data-height="100" data-fgColor="#ffbd4a"--}}
                  {{--data-bgColor="#FFE6BA"--}}
                  {{--data-skin="tron" data-min="0"--}}
                  {{--data-max="{{$user["number_am"]}}" data-step="10"--}}
                  {{--value="{{$user["day_am"]}}" data-readOnly=true--}}
                  {{--data-thickness=".15"/>--}}
              {{--</div>--}}

              <div class="widget-detail-1">
                <h2 class="p-t-10 mb-0"> {{$user["day_am"]}} </h2>
                <p class="text-muted m-b-10">روز های باقی مانده</p>
              </div>
            </div>
          </div>
        </div>
      @else
        @foreach($products as $product)
          <article class="pricing-column col-xl-3 col-md-6">
            <div class="inner-box card-box">
              <div class="plan-header text-center">
                <h3 class="plan-title">{{$product["name"]}}</h3>
                <h2 class="plan-price" style="line-height: 50px;">{{number_format($product['number_days'] * $product['amount_1_day'])}} تومان</h2>
                <div class="plan-duration">{{$product['number_days']}} روزه</div>
              </div>

              {{--<ul class="plan-stats list-unstyled text-center">--}}
                {{--<li>{{$product['number_days']}} روزه</li>--}}
                {{--<li>24x7 پشتیبانی</li>--}}
              {{--</ul>--}}

              <div class="text-center">
                  @php $text_button_subscription = ($product['package_type'] != 3) ? 'خرید' : 'دریافت بسته' @endphp
                <a href="/user/product/pay/{{$product["id"]}}" class="btn btn-success btn-bordred btn-rounded waves-effect waves-light">{{$text_button_subscription}}</a>
              </div>
            </div>
          </article>
        @endforeach
      @endif
    </div>
  </div>

  <!-- KNOB JS -->
  <!--[if IE]>
  <script type="text/javascript" src="/assets/plugins/jquery-knob/excanvas.js"></script>
  <![endif]-->
  <script src="/assets/plugins/jquery-knob/jquery.knob.js"></script>
@endsection