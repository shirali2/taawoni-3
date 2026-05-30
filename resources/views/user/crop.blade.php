@extends("theme.user")
@section("container")
    <div class="container">

        <div class="row mt-3 justify-content-center">

                @foreach($products as $product)
                    <article class="pricing-column col-xl-3 col-md-6">
                        <div class="inner-box card-box">
                            <div class="plan-header text-center">
                                @if($product["img"]!=null)
                                <h3 class="plan-title"><img width="100%"  src="/crop_img/{{$product["img"]}}"></h3>
                                @endif
                                    <h3 class="plan-title">{{$product["name"]}}</h3>
                                <h2 class="plan-price" style="line-height: 50px;">{{number_format($product["price"])}} تومان</h2>
                                <div style="text-align: center;line-height: 29px;" class="plan-duration mt-4">{{$product["text"]}} </div>
                            </div>
                            {{--<ul class="plan-stats list-unstyled text-center">--}}
                                {{--<li>{{$product["am"]}} ماه </li>--}}
                                {{--<li>24x7 پشتیبانی</li>--}}
                            {{--</ul>--}}

                            <div class="text-center">
                                @if($product["pay"])
                                    <span class="badge badge-success py-2">خریداری شده</span>
                                    @else
                                    <a href="/user/crop/pay/{{$product["id"]}}" class="btn btn-success btn-bordred btn-rounded waves-effect waves-light">خرید</a>

                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach



        </div>
    </div>

    <!-- KNOB JS -->
    <!--[if IE]>
    <script type="text/javascript" src="/assets/plugins/jquery-knob/excanvas.js"></script>
    <![endif]-->
    <script src="/assets/plugins/jquery-knob/jquery.knob.js"></script>
@endsection
