@extends("theme.manager")
@section("container")

<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<script src="/assets/js/modernizr.min.js"></script>

<div class="row">
  <div class="col-12 mb-3">
    <div class="card-box table-responsive">
      <div class="m-t-0 m-b-30">
        <h4 class="header-title d-inline-block ">فهرست صورت حساب ها</h4>

        @if(\App\Http\Controllers\managerController::managerAccessLevel(66))
          <a href="/manager/invoice/add" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن صورت حساب</a>
        @endif
      </div>

      <table id="datatable-buttons" class="table demo table-striped table-bordered p-0 m-0" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th style="width: 15px;text-align: right !important;">شناسه</th>
            <th style="text-align: right !important;">گروه های کاربری مجموعه یا کاربران</th>
            <th style="text-align: right !important;">شماره سند</th>
            <th style="text-align: right !important;">تاریخ ایجاد</th>
            <th style="text-align: right !important;">تاریخ تعهد</th>
            <th style="text-align: right !important;">عنوان</th>
            <th style="text-align: right !important;">شرح</th>
            <th style="text-align: right !important;">مبلغ تعهد (تومان)</th>
            <th style="text-align: right !important;">مبنای جریمه روزانه</th>
            <th style="text-align: right !important;">مبلغ جریمه ثابت</th>
            <th style="text-align: right !important;">نام مجموعه</th>
            <th style="text-align: right !important;">اقدامات</th>
          </tr>
        </thead>

        <tbody>
          @foreach($invoices as $invoice)
            <tr>
              <td style="text-align: right !important;">{{$invoice["id"]}}</td>
              <td style="text-align: right !important;">{{$invoice["id_users"]}}</td>
              <td style="text-align: right !important;">{{$invoice["number"]}}</td>
              <td style="text-align: right !important;">{{$invoice['am_start']}}</td>
              <td style="text-align: right !important;">{{$invoice["am_end"]}}</td>
              <td style="text-align: right !important;">{{$invoice["title"]}}</td>
              <td style="text-align: right !important;">{{$invoice["text"]}}</td>
              <td style="text-align: right !important;">{{number_format($invoice["price"])}}</td>
              <td style="text-align: right !important;">{{$invoice["daily_fine_amount"]}}</td>
              <td style="text-align: right !important;">{{ isset($invoice['fixed_penalty_amount']) ? number_format($invoice['fixed_penalty_amount']) : 0 }}</td>
              <td style="text-align: right !important;">{{ $invoice['grop']["name"] ?? '-' }}</td>
              <td class="text-center">
                @if(\App\Http\Controllers\managerController::managerAccessLevel(68))
                 <a href="/manager/invoice/user/{{$invoice["id"]}}" data-toggle="tooltip" title="فرم های پرشده" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                 <a href="/manager/invoice/edit/{{$invoice['id']}}" data-toggle="tooltip" title="ویرایش" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                @endif
                @if(\App\Http\Controllers\managerController::managerAccessLevel(67))
                  <button class="btn btn-danger btn-sm btn-delete" id="{{$invoice["id"]}}" name="{{$invoice["title"]}}" data-toggle="tooltip" title="حذف" ><i class="fa fa-times"></i></button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
  </div>
</div>
</div>

<script src="/assets/js/printThis.js"></script>
<script>
$(".btn-delete").click(function(){
  var id=$(this).attr("id"),
      email=$(this).attr("name");

  Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف صورتحساب  '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/manager/invoice/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

  $("#close-delete").click(function(){
    $(".swal2-close").trigger('click')
  });
});
</script>
@endsection