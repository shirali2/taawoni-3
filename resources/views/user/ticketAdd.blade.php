@extends("theme.user")
@section("container")
  <!-- DataTables -->
  <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>

  <div class="container">
    <div class="row justify-content-center">
        <div class="card-box ">
          <div class="m-t-0 m-b-30">
            <h4 class="header-title d-inline-block ">ایجاد تیکت </h4>
            <a href="/user/ticket/" class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
          </div>

          <form class="form-horizontal form" method="post"  action="/user/ticket/add" enctype="multipart/form-data" data-parsley-validate novalidate>
            @csrf

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-3 col-form-label">موضوع تیکت </label>
              <div class="col-sm-8">
                <select id="inputEmail34" required name="name" class="form-control">
                  <option selected="" disabled="">انتخاب موضوع</option>
                  @foreach($issues as $issue)
                    <option value="{{$issue["name"]}}">{{$issue["name"]}}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label for="inputEmail3" class="col-sm-3 col-form-label">متن پیام </label>
              <div class="col-sm-8">
                <textarea required name="text" type="text" class="form-control" id="inputEmail3" placeholder="متن پیام را وارد کنید"></textarea>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">فایل</label>
              <div class="col-sm-8">
                <input type="file" name="file" class="form-control">
              </div>
            </div>

            <div class="col-12 mt-4">
              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5"> ارسال  </button>
              </div>
            </div>
          </form>
        </div>
    </div>
  </div>

  <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
  <script src="/assets/js/kamadatepicker.min.js"></script>
  <script type="text/javascript" language="JavaScript">
      // function register() {
      //     var error = 0;
      //
      //     var username = $("input[name='username']").val();
      //     var email = $("input[name='email']").val();
      //     var token = $("input[name='_token']").val();
      //
      //         $.ajax('/admin/addUser/check',
      //             {
      //                 type: 'post',
      //                 async: false
      //                 , data: {_token: token,username: username}
      //                 , success: function (data1) {
      //                     if (data1 == 0) {
      //                         error = error + 1;
      //                         $("input[name='username']").addClass('red rtl').attr("placeholder", "این نام کاربری قبلا ثبت شده است").val('');
      //                     } else {
      //                     }
      //                 }
      //             })
      //
      //     $.ajax('/admin/addUser/checkEmail',
      //             {
      //                 type: 'post',
      //                 async: false
      //                 , data: {_token: token,email: email}
      //                 , success: function (data1) {
      //                     if (data1 == 0) {
      //                         error = error + 1;
      //                         $("input[name='email']").addClass('red rtl').attr("placeholder", "این ایمیل قبلا ثبت شده است").val('');
      //                     } else {
      //                     }
      //                 }
      //             })
      //
      //
      //     if (error > 0) {
      //         return false;
      //     }
      // }
  </script>
@endsection