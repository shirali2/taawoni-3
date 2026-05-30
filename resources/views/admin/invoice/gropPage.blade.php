@extends("theme.default")
@section("container")
  <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <div class="row">
    <div class="col-12">
      <div  class="card-box table-responsive">
        <div class="m-b-30">
          <h4 class="header-title d-inline-block">فهرست مجموعه ها</h4>
        </div>

        <table id="datatable-buttons" class="table demo table-bordered" cellspacing="0">
          <thead>
            <tr>
              <th style="width: 15px">شناسه</th>
              <th>نام مجموعه</th>
              <th>اقدامات</th>
            </tr>
          </thead>

          <tbody>
            @foreach($grops as $grop)
              <tr>
                <td>{{$grop["id"]}}</td>
                <td>{{$grop["name"]}}</td>
                <td><a href="/admin/invoice/list/{{$grop["id"]}}" data-toggle="tooltip" data-placement="top" data-original-title="نمایش صورتحساب ها" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection