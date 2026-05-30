@extends('theme.default')
@section('container')

    @if(session('external_link'))
    <div class="alert alert-success alert-dismissible mt-3">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>لینک خارجی ایجاد شد:</strong>
        <a href="{{ session('external_link') }}" target="_blank" id="ext-link-display" class="d-block mt-1 text-break">
            {{ session('external_link') }}
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
            onclick="navigator.clipboard.writeText('{{ session('external_link') }}').then(()=>alert('کپی شد!'))">
            کپی لینک
        </button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible mt-3">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
    @endif
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />


    <div class="row">
        <div class="col-12 mb-3">
            <div class="card-box table-responsive">
                <div class="m-t-0 m-b-30">
                    <h4 class="header-title d-inline-block ">فهرست فرم ها</h4>
                    <a href="/admin/form/add"
                        class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن فرم</a>
                </div>

                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{-- <button type="button" id="basic" --}}
                    {{-- class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button> --}}
                    <table id="formDataTable" class="table datatable-buttons table-striped table-bordered p-0 m-0 no-footer"
                        role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;"
                        width="100%" cellspacing="0">
                        <thead>
                            <tr role="row">
                                <th style="width: 15px">شناسه</th>
                                <th>نام فرم</th>
                                <th>توضیحات فرم</th>
                                <th>کد</th>
                                <th>فرم های <br />پرشده</th>
                                <th>نام مجموعه</th>
                                <th>اقدامات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($forms as $form)
                                <tr role="row" class="odd">
                                    <td>{{ $form['id'] }}</td>
                                    <td>{{ $form['name'] }}</td>
                                    <td>{{ $form['text'] }}</td>
                                    <td>{{ $form['hash'] }}</td>
                                    <td>{{ $form['user_forms_count'] }}</td>
                                    <td>
                                        @php
                                            $totalGroups = count($form['name_grops']);
                                        @endphp

                                        @foreach ($form['name_grops'] as $index => $name_grop)
                                            @if ($index < 2)
                                                <span
                                                    class="float-left bg-info text-white mt-1 mb-1 p-2">{{ $name_grop }}</span>
                                            @elseif($index == 2)
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-secondary dropdown-toggle mt-1 mr-2 mb-1 p-2"
                                                        type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        بیشتر
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item">{{ $name_grop }}</a>
                                                    @else
                                                        <a class="dropdown-item">{{ $name_grop }}</a>
                                            @endif
                                        @endforeach

                                        @if ($totalGroups > 2)
                </div>
            </div>
            @endif
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="actionDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        عملیات
                    </button>
                    <div class="dropdown-menu" aria-labelledby="actionDropdown">
                        <a href="/admin/form/user/{{ $form['id'] }}" data-toggle="tooltip" data-placement="top"
                            data-original-title="فرم های پرشده" class="dropdown-item text-success">
                            <i class="fa fa-eye"></i> مشاهده
                        </a>
                        <a href="/admin/formNonefill/user/{{ $form['id'] }}" data-toggle="tooltip" data-placement="top"
                            data-original-title="فرم های پرنشده" class="dropdown-item text-success">
                            <i class="fa fa-eye"></i> مشاهده فرم های پرنشده
                        </a>
                        <a href="/admin/form/edit/{{ $form['id'] }}" data-toggle="tooltip" data-original-title="ویرایش"
                            class="dropdown-item text-warning">
                            <i class="fa fa-pencil"></i> تصحیح
                        </a>
                        <button type="button"
                            class="dropdown-item text-info"
                            data-toggle="modal"
                            data-target="#externalTokenModal{{ $form['id'] }}">
                            <i class="fa fa-link"></i> ایجاد لینک خارجی
                        </button>
                        <button class="dropdown-item text-danger btn-delete" id="{{ $form['id'] }}"
                            name="{{ $form['name'] }}" data-toggle="tooltip" data-placement="top"
                            data-original-title="حذف">
                            <i class="fa fa-times"></i> حذف
                        </button>
                    </div>
                </div>
            </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>


    <script>
        $(document).ready(function() {
            var table = $('#formDataTable').DataTable({
                lengthChange: true,
                buttons: ['copy', 'excel'],
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "همه"]
                ],
            });

            table.buttons().container()
                .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

        });

        $(document).on('click', '.btn-delete', function(event) {
            event.preventDefault(); // Prevent default button behavior

            var id = $(this).attr('id');
            var email = $(this).attr('name');

            Swal.fire({
                html: `<div class="mt-3 font-sans">
               <img src="/img/clipart2619611.png" style="width: 150px;">
               <div class="mt-4 pt-2 fs-15 mx-5">
                 <h4 class="font-sans">مطمئن هستید ؟</h4>
                 <p style="direction: rtl;" class="text-muted mx-4 mb-0">
                   آیا از حذف فرم ${email} مطمئن هستید ؟
                 </p>
                 <form class="d-inline-block" action="/admin/form/delete/${id}" method="post" id="delete-form">
                   @csrf
                   <input name="_method" type="hidden" value="delete">
                   <button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3">بله, حذف</button>
                   <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button>
                 </form>
               </div>
             </div>`,
                showConfirmButton: false,
                showCancelButton: false,
                showCloseButton: true,
                didOpen: () => {
                    $('#close-delete').click(() => {
                        Swal.close(); // Close the SweetAlert dialog
                    });

                    // Ensure form submission does not refresh the page immediately
                    $('#delete-form').submit(function(event) {
                        event.preventDefault(); // Prevent default form submission
                        Swal.close(); // Optionally close the SweetAlert dialog if needed
                        // Perform form submission manually
                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                // Handle successful response (e.g., refresh the page or update the UI)
                                window.location
                                    .reload(); // Refresh the page on success
                            },
                            error: function(xhr) {
                                // Handle error
                                console.error('Error:', xhr);
                            }
                        });
                    });
                }
            });
        });
    </script>
    {{-- External Token Modals --}}
    @foreach ($forms as $form)
    <div class="modal fade" id="externalTokenModal{{ $form['id'] }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ایجاد لینک خارجی — {{ $form['name'] }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="/admin/form/{{ $form['id'] }}/external-token">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>رمز عبور لینک <span class="text-danger">*</span></label>
                            <input type="password"
                                   name="token_password"
                                   class="form-control"
                                   placeholder="حداقل ۴ کاراکتر"
                                   required
                                   minlength="4"/>
                        </div>
                        <div class="form-group">
                            <label>تاریخ انقضا (اختیاری)</label>
                            <input type="datetime-local"
                                   name="expires_at"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
                        <button type="submit" class="btn btn-primary">ایجاد لینک</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

@endsection
