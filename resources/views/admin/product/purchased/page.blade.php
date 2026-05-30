@extends("theme.default")
@section("container")
  <!-- DataTables -->
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <div class="row">
    <div class="col-12 mb-3">
      <div  class="card-box table-responsive">
        <div class="m-b-30">
          <h4 class="header-title d-inline-block">فهرست اشتراک های خریداری شده</h4>
        </div>

        <table id="datatable-buttons" class="table demo table-bordered dataTable" role="grid" cellspacing="0">
          <thead>
            <tr role="row">
              <th style="width: 15px" >شناسه خرید</th>
              @foreach(array(
                'نام بسته',
                'شناسه کاربر',
                'نام و نام خانوادگی کاربر',
                'نام مجموعه',
                'تاریخ خرید و شروع',
                'تاریخ خاتمه اعتبار',
                'روش پرداخت',
                'شماره فیش',
                'مبلغ',
                'اقدامات'
              ) as $column_name)
                <th>{{$column_name}}</th>
              @endforeach
            </tr>
          </thead>

          <tbody>
            @foreach($pays as $pay)
              <tr role="row" class="odd">
                <td>{{$pay['id']}}</td>
                <td>{{$pay['name_product']}}</td>
                <td>{{$pay['id_user']}}</td>
                <td>{{$pay['name_and_family_user']}}</td>
                <td>{{$pay['name_grop']}}</td>
                <td>{{verta(explode(' ', $pay['created_at'])[0])->formatDate()}}</td>
                <td>{{$pay['expiry_date']}}</td>
                <td></td>
                <td>{{$pay['referenceId']}}</td>
                <td>{{number_format($pay['price'])}} تومان</td>
                <td>
                  <button class="btn btn-danger btn-delete" id="{{$pay["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script type="text/javascript" language="JavaScript">
    $('.btn-delete').click(function(){ //حذف
      var id = $(this).attr('id'); //شناسه خرید

      Swal.fire({
        html: '<div class="mt-3"><img src="/img/clipart2619611.png" style="width: 150px"><div class="mt-5 mx-5"><h4>مطمئن هستید؟</h4><p class="text-muted mx-4 mb-0">آیا از حذف شناسه ' + id + ' مطمئن هستید؟</p><form action="/admin/product/purchased/delete/' + id + '" method="post">@csrf<input name="_method" type="hidden" value="delete" class="d-none" /><button type="submit" class="btn btn-danger waves-effect w-md mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md mt-3" id="close-delete">لغو</button></form></div></div>',
        showConfirmButton: 0 //مخفی بودن دکمه تایید پیشفرض
      })

      $('#close-delete').click(function(e){ //بستن پاپ آپ
        $('.swal2-close').trigger('click')
      })
    })
  </script>
@endsection