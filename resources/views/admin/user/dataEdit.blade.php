@extends("theme.default")
@section("container")
    <div id="alert_login" style="position: fixed;bottom: 0;right: 15px;width: 300px;z-index: 1000;">

    </div>
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block ">اطلاعات اختصاصی کاربر</h4>
                        <a href="/admin/user/data/{{$user['id']}}"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form id="form-register" class="form-horizontal m-t-20" method="post" action="">
                        @csrf

                          <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1" class="d-block">جنسیت : </label>
                                <div class="radio radio-info form-check-inline mr-2">
                                    <input type="radio" id="inlineRadio1" @if($user['gender']==1) checked @endif value="1" name="gender">
                                    <label for="inlineRadio1"> زن </label>
                                </div>
                                <div class="radio radio-info form-check-inline mr-2">
                                    <input type="radio" id="inlineRadio2" @if($user['gender']==2) checked @endif value="2" name="gender" >
                                    <label for="inlineRadio2"> مرد </label>
                                </div>
                            </div>
                              <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1" class="d-block">متاهل / مجرد : </label>
                                <div class="radio radio-info form-check-inline mr-2">
                                    <input type="radio" id="inlineRadio3" @if($user['marriage']==1) checked @endif value="1" name="marriage">
                                    <label for="inlineRadio3"> متاهل </label>
                                </div>
                                <div class="radio radio-info form-check-inline mr-2">
                                    <input type="radio" id="inlineRadio4" @if($user['marriage']==2) checked @endif value="2" name="marriage" >
                                    <label for="inlineRadio4"> مجرد </label>
                                </div>
                            </div>


                            <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1"> تاریخ تولد : </label>
                                <input  class="form-control" value="{{$user["birth"]}}" id="test-date-id" name="birth" type="text"    >
                            </div>


                            <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1">نام پدر : </label>
                                <input  class="form-control" value="{{$user["father"]}}" name="father" type="text"  placeholder="نام پدر">
                            </div>

                              <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1">کد پستی : </label>
                                <input  class="form-control" value="{{$user["mail"]}}" name="mail" type="text"  placeholder="کد پستی">
                            </div>

                              <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1">شماره واحد : </label>
                                <input  class="form-control" value="{{$user["number_vahed"]}}" name="number_vahed" type="text"  placeholder="شماره واحد">
                            </div>

                              <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1">شماره داخلی بلوک : </label>
                                <input  class="form-control" value="{{$user["number_block"]}}" name="number_block" type="text"  placeholder="شماره داخلی بلوک">
                            </div>
                              <div class="col-lg-6 mb-3">
                                <label for="exampleInputEmail1">شماره ثبتی بلوک : </label>
                                <input  class="form-control" value="{{$user["number_block2"]}}" name="number_block2" type="text"  placeholder="شماره ثبتی بلوک">
                            </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1" class="d-block">انباری : </label>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio32" @if($user['warehouse']==1) checked @endif value="1" name="warehouse">
                                      <label for="inlineRadio32"> دارد </label>
                                  </div>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio42" @if($user['warehouse']==2) checked @endif value="2" name="warehouse" >
                                      <label for="inlineRadio42"> ندارد </label>
                                  </div>
                            </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1" class="d-block">پارکینگ : </label>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio321" @if($user['parking']==1) checked @endif value="1" name="parking">
                                      <label for="inlineRadio321"> دارد </label>
                                  </div>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio421" @if($user['parking']==2) checked @endif value="2" name="parking" >
                                      <label for="inlineRadio421"> ندارد </label>
                                  </div>
                            </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">محل پروژه : </label>
                                  <input  class="form-control" value="{{$user["location"]}}" name="location" type="text"  placeholder="محل پروژه">
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1" class="d-block">فرم جیم : </label>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio3211" @if($user['form_j']==1) checked @endif value="1" name="form_j">
                                      <label for="inlineRadio3211"> دارد </label>
                                  </div>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio4211" @if($user['form_j']==2) checked @endif value="2" name="form_j" >
                                      <label for="inlineRadio4211"> ندارد </label>
                                  </div>
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1" class="d-block">قرارداد : </label>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio32111" @if($user['contract']==1) checked @endif value="1" name="contract">
                                      <label for="inlineRadio32111"> دارد </label>
                                  </div>
                                  <div class="radio radio-info form-check-inline mr-2">
                                      <input type="radio" id="inlineRadio42111" @if($user['contract']==2) checked @endif value="2" name="contract" >
                                      <label for="inlineRadio42111"> ندارد </label>
                                  </div>
                              </div>

                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1"> تاریخ شروع قرارداد : </label>
                                  <input  class="form-control" value="{{$user["am_start_contract"]}}" id="test-date-id2" name="am_start_contract" type="text"    >
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1"> تاریخ پایان قرارداد : </label>
                                  <input  class="form-control" value="{{$user["am_end_contract"]}}" id="test-date-id3" name="am_end_contract" type="text"    >
                              </div>

                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">سند واحد : </label>
                                  <input  class="form-control" name="unit" type="text" value="{{$user["unit"]}}"   placeholder="سند واحد">
                              </div>

                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">نام نام خانوادگی وکیل : </label>
                                  <input  class="form-control" name="namename2" type="text" value="{{$user["namename2"]}}"   placeholder="نام نام خانوادگی وکیل">
                              </div>

                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">کدملی وکیل : </label>
                                  <input  class="form-control" name="kodkod2" type="text" value="{{$user["kodkod2"]}}"   placeholder="کدملی وکیل">
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">شماره وکالتنامه وکیل : </label>
                                  <input  class="form-control" name="numbernumber2" type="text" value="{{$user["numbernumber2"]}}"   placeholder="شماره وکالتنامه وکیل">
                              </div>
                              <div class="col-12 mb-3">
                                  <label for="exampleInputEmail1">قابل توجه کاربر/عضو : </label>
                                  <textarea  class="form-control" name="significant" type="text"   placeholder="قابل توجه کاربر/عضو">{{$user["significant"]}}</textarea>
                              </div>
                              <div class="col-12 mb-3">
                                  <label for="exampleInputEmail1">نظریه کارشناس : </label>
                                  <textarea  class="form-control" name="theory" type="text"  placeholder="نظریه کارشناس">{{$user["theory"]}}</textarea>
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">نشانی : </label>
                                  <input  class="form-control" name="tashati" type="text" value="{{$user["tashati"]}}"  placeholder="تشاتی">
                              </div>
                              <div class="col-lg-6 mb-3">
                                  <label for="exampleInputEmail1">سایر : </label>
                                  <input  class="form-control" name="other" type="text" value="{{$user["other"]}}"  placeholder="سایر">
                              </div>
                          </div>


                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                                    ذخیره
                                </button>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>


    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>





        var customOptions = {
            placeholder: "روز / ماه / سال"
            , twodigit: true
            , closeAfterSelect: true
            , nextButtonIcon: "fa fa-arrow-circle-right"
            , previousButtonIcon: "fa fa-arrow-circle-left"
            , buttonsColor: "blue"
            , forceFarsiDigits: true
            , markToday: true
            , markHolidays: true
            , highlightSelectedDay: true
            , sync: true
            , gotoToday: true
        }


        kamaDatepicker('test-date-id', customOptions);
        kamaDatepicker('test-date-id2', customOptions);
        kamaDatepicker('test-date-id3', customOptions);






    </script>


@endsection
