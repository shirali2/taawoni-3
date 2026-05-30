@extends('theme.manager')
@section('container')
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <div class="row">
        <div class="col-12 mb-3">
            <div class="card-box table-responsive">
                <div class="m-t-0 m-b-30">
                    <h4 class="header-title d-inline-block ">فهرست فرم ها</h4>

                    @if (\App\Http\Controllers\managerController::managerAccessLevel(22))
                        <a href="/manager/form/add"
                            class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن
                            فرم</a>
                    @endif
                </div>

                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{-- <button type="button" id="basic" --}}
                    {{-- class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button> --}}
                    <table id="datatable-buttons"
                        class="table demo datatable-buttons table-striped table-bordered p-0 m-0 dataTable no-footer"
                        role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;"
                        width="100%" cellspacing="0">
                        <thead>
                            <tr role="row">
                                <th style="width: 15px">شناسه</th>
                                <th>نام فرم</th>
                                <th>توضیحات فرم</th>
                                <th>کد</th>
                                <th>فرم های <br />پرشده</th>
                                <th>اقدامات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($forms as $form)
                                <tr role="row" class="odd">
                                    <td style="text-align: right !important;" class="sorting_1">{{ $form->id }}</td>
                                    <td style="text-align: right !important;">{{ $form->name }}</td>
                                    <td style="text-align: right !important;">{{ $form->text }}</td>
                                    <td style="text-align: right !important;">{{ $form->hash }}</td>
                                    <td>{{ $form->user_count }}</td>
                                    <td style="display: none;" class="d-block-s">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="actionDropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            عملیات
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                            @if (\App\Http\Controllers\managerController::managerAccessLevel(25))
                                                <a href="/manager/form/user/{{ $form->id }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="فرم های پرشده"
                                                    class="btn btn-success"><i class="fa fa-eye"></i></a>
                                            @endif
                                            @if (\App\Http\Controllers\managerController::managerAccessLevel(25))
                                                </a>
                                                <a href="/manager/formNonefill/user/{{ $form->id }}"
                                                    data-toggle="tooltip" data-placement="top" class="btn btn-success"
                                                    data-original-title="فرم های پرنشده" class="dropdown-item text-success">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                            @if (\App\Http\Controllers\managerController::managerAccessLevel(22))
                                                <a href="/manager/form/edit/{{ $form->id }}" data-toggle="tooltip"
                                                    data-original-title="ویرایش" class="btn btn-warning"><i
                                                        class="fa fa-pencil"></i></a>
                                            @endif

                                            @if (\App\Http\Controllers\managerController::managerAccessLevel(24))
                                                <button class="btn btn-danger btn-delete" id="{{ $form->id }}"
                                                    name="{{ $form->name }}" data-toggle="tooltip" data-placement="top"
                                                    data-original-title="حذف"><i class="fa fa-times"></i></button>
                                            @endif
                                    </td>
                                </tr>
                            @endforeach
                </div>
            </div>
            </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>

    <script>
        $('.btn-delete').click(function() {
            var id = $(this).attr('id'),
                email = $(this).attr('name');

            Swal.fire({
                html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف فرم  ' +
                    email +
                    ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/manager/form/delete/' +
                    id +
                    '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "d-none",
                confirmButtonText: "Yes, Delete It!",
                cancelButtonClass: "d-none",
                buttonsStyling: !1,
                showCloseButton: !0
            })

            $('#close-delete').click(function() {
                $('.swal2-close').trigger('click')
            })
        });
    </script>
@endsection
