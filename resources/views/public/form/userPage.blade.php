@php
$role = session()->has('admin') ? 'admin' : 'manager';
$check_access_27_manager =
$role == 'admin' || ($role == 'manager' && \App\Http\Controllers\managerController::managerAccessLevel(27))
? true
: false;
@endphp
@section('container')
<style type="text/css">
    .checkbox {
        height: 20px;
        width: 20px;
        cursor: pointer;
        cursor: hand
    }

    .file-selected-for-field {
        width: 140px
    }

    /* فایل انتخاب شده برای فیلد */

</style>

<link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<script src="/assets/js/modernizr.min.js"></script>
<div class="row">
    <div class="col-12 mb-3">
        <form method="get" action="/{{ $role }}/form/user/delete/group/1" class="card-box table-responsive">
            <div class="float-right w-100 m-t-0 m-b-30">
                <h4 class="header-title d-inline-block ">فهرست فرم های تکمیل شده {{ $name_form }}</h4>
                @if ($check_access_27_manager)
                <a href="/{{ $role }}/form/user/add/xlsx/{{ $id_form }}" class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن
                    اطلاعات با اکسل</a>
                @endif
            </div>

            <button type="button" id="select-all" class="btn btn-success mb-4">انتخاب همه</button>

            <div style="overflow: auto; width: 100%" id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 pb-3 m-0 no-footer">
                {{-- <button type="button" id="basic" --}}
                {{-- class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button> --}}
                <table id="formDataTable" class="table datatable-buttons table-striped table-bordered p-0 m-0 no-footer" role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;overflow: auto;" width="100%" cellspacing="0">
                    <thead>
                        <tr role="row">
                            <th>انتخاب</th>
                            <th class="sorting_asc" style="width: 15px;text-align: right !important;">شناسه</th>
                            <th class="sorting" style="text-align: right !important;">شناسه کاربر</th>
                            <th class="sorting" style="text-align: right !important;">کد اختصاصی</th>
                            <th class="sorting" style="text-align: right !important;">نام نام خانوادگی کاربر</th>
                            <th class="sorting" style="text-align: right !important;">شماره موبایل کاربر</th>
                            <th class="sorting" style="text-align: right !important;">کدملی کاربر</th>
                            @foreach ($form_fild as $fild)
                            <th class="sorting_asc" style="width: 15px;text-align: right !important;">
                                {{ $fild['title'] }}</th>
                            @endforeach
                            <th class="sorting" style="text-align: right !important;" aria-label="Salary: activate to sort column ascending">اقدامات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($user_forms as $user_form)
                        <tr role="row" class="odd">
                            <td class="text-center"><input type="checkbox" name="ids[{{ $user_form['id'] }}]" class="checkbox" /></td>
                            <td style="text-align: right !important" class="sorting_1">{{ $user_form['id'] }}</td>
                            <td style="text-align: right !important">{{ $user_form['ID_User'] }}</td>
                            <td style="text-align: right !important">{{ $user_form['hash'] }}</td>
                            <td style="text-align: right !important">{{ $user_form['name'] }}
                                {{ $user_form['name2'] }}</td>
                            <td style="text-align: right !important">{{ $user_form['mobile'] }}</td>
                            <td style="text-align: right !important">{{ $user_form['kod'] }}</td>
                            @foreach ($form_fild as $fild)
                            <td style="text-align: right !important">
                                @php
                                @endphp
                                @if (isset($fild['name']))
                                @if (isset($user_form['form']))
                                @foreach ($user_form['form'] as $form)
                                @if (isset($form['name_itme']) && $form['name_itme'] == $fild['name'])
                                {{ $form[$fild['name']] }}
                                @break
                                @endif
                                @endforeach
                                @endif
                                @else
                                @foreach ($form_fild as $fild)
                                @php
                                @endphp
                                @if (isset($fild['value']) && !empty($fild['value']))
                                {{ $fild['value'] }}
                                @break
                                @endif
                                @endforeach
                                @endif

                            </td>
                            @endforeach
                            <td style="display: none;" class="d-block-s">
                                @if(!isset($filledOrNoneFilled) || $filledOrNoneFilled=='Filled')
                                <a type="button" class="show-fields-filled-user btn btn-primary waves-effect waves-light" href="/{{ $role }}/form/detail/{{ $user_form['id'] }}/{{ $id_form }}"><i class="fa fa-eye"></i></a>
                                <a type="button" class="show-fields-filled-user btn btn-primary waves-effect waves-light" href="/{{ $role }}/form/editdetail/{{ $user_form['id'] }}/{{ $id_form }}"><i class="fa fa-eye"></i></a>
                                @endif


                                @if ($check_access_27_manager && (!isset($filledOrNoneFilled) || $filledOrNoneFilled=='Filled'))
                                <button type="button" class="btn btn-danger btn-delete" id="{{ $user_form['id'] }}" name="{{ $user_form['name'] }}" data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i class="fa fa-times"></i></button>
                                @elseif ($check_access_27_manager && isset($filledOrNoneFilled) && $filledOrNoneFilled=='NoneFilled')
                                <button type="button" class="btn btn-danger btn-delete-nonefill"
                                    data-user-id="{{ $user_form['ID_User'] }}"
                                    data-form-id="{{ $id_form }}"
                                    name="{{ $user_form['name'] }}"
                                    data-toggle="tooltip" data-placement="top" data-original-title="حذف تکلیف فرم"><i class="fa fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($check_access_27_manager)
            <button type="submit" name="group-deletion" class="btn btn-danger mt-4">حذف گروهی</button>
            @endif
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#formDataTable').DataTable({
            lengthChange: true
            , buttons: ['copy', 'excel']
            , "lengthMenu": [
                [10, 25, 50, 100, -1]
                , [10, 25, 50, 100, "همه"]
            ]
        , });

        table.buttons().container()
            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
    });

    /* START ثبت آیدی فرم پر شده توسط کاربر در مدال */
    $('.show-fields-filled-user').click(function() {
        var selector = $(this)
        $('#id-user-form').remove()
        $(selector.data('target') + ' .modal-content').append(
            '<input type="hidden" name="id-user-form" value="' + selector.attr('id') +
            '" id="id-user-form" class="d-none" />')
    })
    /* END ثبت آیدی فرم پر شده توسط کاربر در مدال */


    /* START چک باکس انتخاب همه */
    var button_select_all = jQuery('#select-all')
    button_select_all.click(function() {
        var status = (button_select_all.text() == 'انتخاب همه') ? true : false
        jQuery('#formDataTable [type="checkbox"]').prop('checked', status ? true : false)

        if (status) {
            button_select_all.text('برداشتن انتخاب ها')
        } else {
            button_select_all.text('انتخاب همه')
        }
    })

    jQuery(document).on('input change keyup keydown keypress', '[type="search"]', function() {
        button_select_all.text('انتخاب همه')
    })
    jQuery(document).on('click', '.paginate_button', function() {
        button_select_all.text('انتخاب همه')
    }) //بعد تغییر صفحه
    /* END چک باکس انتخاب همه */


    $('.btn-delete').click(function() {
        var id = $(this).attr('id')
            , email = $(this).attr('name')

        Swal.fire({
            html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف فرم پر شده   ' +
                email +
                ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/{{ $role }}/form/user/delete/' +
                id +
                '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>'
            , showCancelButton: !0
            , confirmButtonClass: "d-none"
            , confirmButtonText: "Yes, Delete It!"
            , cancelButtonClass: "d-none"
            , buttonsStyling: !1
            , showCloseButton: !0
        })

        $('#close-delete').click(function() {
            $('.swal2-close').trigger('click')
        })
    });

    $('.btn-delete-nonefill').click(function() {
        var userId = $(this).data('user-id')
            , formId = $(this).data('form-id')
            , name = $(this).attr('name')

        Swal.fire({
            html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟</h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">کاربر ' +
                name +
                ' از لیست فرم‌های پر نشده حذف می‌شود.</p><form class="d-inline-block" action="/{{ $role }}/form/user/nonefill/delete/' +
                formId + '/' + userId +
                '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3">بله , حذف</button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete-nonefill">لغو</button></form></div></div>'
            , showCancelButton: !1
            , confirmButtonClass: "d-none"
            , cancelButtonClass: "d-none"
            , buttonsStyling: !1
            , showCloseButton: !0
        })
        $(document).on('click', '#close-delete-nonefill', function() {
            $('.swal2-close').trigger('click')
        })
    });

</script>
@endsection
