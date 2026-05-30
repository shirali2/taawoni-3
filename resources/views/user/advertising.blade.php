@extends("theme.user")
@section("container")
    <div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

    </div>
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <style>
        .parsley-errors-list {
            position: absolute;
            bottom: -18px;
            right: 18px;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">


            <div class="col-12 col-lg-8 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">خرید تبلیغ</h4>
                        <a href="/user/advertising/list" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>

                    </div>

                                <form   id="form-register" class="form-horizontal m-t-20" method="post" action="" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf

                                            <div class="row">
                                        <div class="col-12 mb-3">

                                                <label for="inputEmail34" class="col-sm-3 col-form-label">انتخاب محل تبلیغ</label>

                                                    <select id="inputEmail34" required name="product" class="form-control ">
                                                        <option selected="" disabled="">انتخاب محل تبلیغ</option>
                                                        @foreach($product_advertisings as $product_advertising)
                                                            @if($product_advertising["show"]==1)
                                                                <option value="{{$product_advertising["id"]}}" price="{{$product_advertising["price"]}}">{{$product_advertising['name']}}</option>
                                                            @endif
                                                            @if($product_advertising["show"]==2)
                                                                <option value="{{$product_advertising["id"]}}" price="{{$product_advertising["price"]}}">{{$product_advertising['name']}} ( انتشار بعد از {{$product_advertising["number"]}} روز )</option>
                                                            @endif
                                                        @endforeach


                                                    </select>
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1">تعداد روز : </label>
                                            <input  class="form-control" name="day" data-parsley-pattern="[0-9]*" data-parsley-min="1"  type="text" required=""   placeholder="تعداد روز ">
                                        </div>


                                        <div class="col-lg-6 mb-3">
                                            <label for="exampleInputEmail1"> بنر تبلیغات : </label>
                                            <input type="file" accept="image/png, image/jpeg" name="file" class="form-control" required="">
                                        </div>



                                            </div>
                                    <div class="alert alert-info mt-4">
                                        مبلغ قابل پرداخت :  <strong><span id="col_price">0</span> تومان</strong>
                                    </div>
                                    <div class="form-group text-center m-t-30">
                                        <div class="col-xs-12">
                                            <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                                                خربد
                                            </button>
                                        </div>
                                    </div>


                                </form>
                            </div>



                        </div>




                </div>
            </div>
        </div>


    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>



        var type=$(this).find("option:selected").val();


        $("input[name='day']").keyup(function () {
            var price=parseInt($("select[name='product']").find("option:selected").attr("price"));
            var day=parseInt($(this).val());
                if (day*price){

                    $("#col_price").text(parseInt(day*price).toLocaleString())
                }else {
                    $("#col_price").text(0)
                }

        })


    </script>


@endsection
