@extends("theme.default")
@section("container")
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <script src="/assets/js/modernizr.min.js"></script>

    <div class="row">
      <div class="col-12 mb-3">
        <div  class="card-box table-responsive">
          <div class="m-t-0 m-b-30">
            <h4 class="header-title d-inline-block ">فهرست صورت حساب های مجموعه {{$name_grop}}</h4>
            <a href="/admin/invoice/add" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن صورت حساب</a>
            <a href="/admin/invoice/xls/page" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن با اکسل</a>

          </div>

          <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
            {{--<button type="button" id="basic"--}}
            {{--class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button>--}}
            <table id="datatable-buttons" class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;" width="100%" cellspacing="0">
              <thead>
              <tr role="row">
              <th class="sorting" style="width: 15px;text-align: right !important;"><input id="select_all" type="checkbox"></th>

                <th class="sorting_asc"  style="width: 15px;text-align: right !important;" >شناسه</th>
                <th class="sorting"  style="text-align: right !important;" >گروه های کاربری مجموعه یا کاربران</th>
                <th class="sorting"  style="text-align: right !important;" >شماره سند</th>
                <th class="sorting"  style="text-align: right !important;" >تاریخ ایجاد</th>
                <th class="sorting"  style="text-align: right !important;" >تاریخ تعهد</th>
                <th class="sorting"  style="text-align: right !important;" >عنوان</th>
                <th class="sorting"  style="text-align: right !important;" >شرح</th>
                <th class="sorting"  style="text-align: right !important;" >مبلغ تعهد (تومان) </th>
                <th class="sorting"  style="text-align: right !important;" >مبنای جریمه روزانه</th>
                <th class="sorting"  style="text-align: right !important;" >مبلغ جریمه ثابت</th>
                <th class="sorting"  style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th></tr>
              </thead>
              <tbody>
                @foreach($invoices as $invoice)
                <tr role="row" class="odd">
                <td  style="text-align: right !important;" value="{{$invoice["id"]}}" class="sorting_1"><input name="items[]" class="item-checkbox" value={{$loop-> index +1}} type="checkbox"></td>

                  <td style="text-align: right !important;" class="sorting_1">{{$invoice["id"]}}</td>
                  <td style="text-align: right !important;">{{$invoice["id_users"]}}</td>
                  <td style="text-align: right !important;">{{$invoice["number"]}}</td>
                  <td style="text-align: right !important;">{{$invoice['am_start']}}</td>
                  <td style="text-align: right !important;">{{$invoice["am_end"]}}</td>
                  <td style="text-align: right !important;">{{$invoice["title"]}}</td>
                  <td style="text-align: right !important;">{{$invoice["text"]}}</td>
                  <td style="text-align: right !important;">{{number_format($invoice["price"])}}</td>
                  <td style="text-align: right !important;">{{$invoice['daily_fine_amount']}}</td>
                  <td style="text-align: right !important;">{{number_format($invoice['fixed_penalty_amount'])}}</td>
                  <td style="display: none;" class="d-block-s">
                    <a href="/admin/invoice/user/{{$invoice["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="فرم های پرشده" class="btn btn-success"><i class="fa fa-eye"></i></a>
                    <a href="/admin/invoice/edit/{{$invoice['id']}}" data-toggle="tooltip" data-placement="top" data-original-title="ویرایش" class="btn btn-success mt-2 mb-2"><i class="fa fa-edit"></i></a>
                    <button class="btn btn-danger btn-delete" id="{{$invoice["id"]}}" name="{{$invoice["title"]}}" data-toggle="tooltip" data-placement="top" data-original-title="حذف" ><i class="fa fa-times"></i></button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script src="/assets/js/printThis.js"></script>
    <script>
        function getSelectedIds() {
        var selectedIds = [];
        $('.item-checkbox:checked').each(function() {
            var tdId = $(this).closest('td').attr('value');
            selectedIds.push(tdId);
        });
        return selectedIds;
    }
$(document).ready(function() {


    // Handle "Select All" checkbox click
    $('#select_all').click(function() {
        var isChecked = this.checked;
        $('.item-checkbox').prop('checked', isChecked);
        var selectedIds = getSelectedIds();
        console.log(selectedIds); // Log the array of selected IDs to the console
    });

    // Handle individual checkbox click
    $('.item-checkbox').click(function() {
        var selectedIds = getSelectedIds();
        console.log(selectedIds); // Log the array of selected IDs to the console
        
        // Update the "Select All" checkbox status
        if ($('.item-checkbox:checked').length === $('.item-checkbox').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });

});

      $(".btn-delete").click(function(){
        var id=$(this).attr("id"),
            email=$(this).attr("name");

        Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف صورتحساب  '+email+' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/invoice/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

        $("#close-delete").click(function(e){
          $(".swal2-close").trigger('click')
        })
      });
    </script>
@endsection