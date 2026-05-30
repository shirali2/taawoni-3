@extends('theme.default')
@section('container')
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('libs/jquery/jquery-3.6.0.min.js') }}"></script>

    <script src="/assets/js/modernizr.min.js"></script>

    <div class="row">
        <div class="col-12 mb-3">

            <div class="card-box table-responsive">
                <div class="m-t-0 m-b-30">

                    <h4 class="header-title d-inline-block ">فهرست کاربران</h4>
                    <a href="/admin/user/add"
                        class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 float-right">افزودن کاربر</a>
                    <a href="/admin/user/add/xlsx"
                        class="btn btn-success btn-trans waves-effect w-md waves-success m-b-5 ml-3 float-right"> افزودن
                        کاربران با اکسل</a>

                </div>
                <button onclick="changegroup()" class="btn btn-primary">عوض کردن گروه و مجموعه</button>
                <a href="{{ request()->has('show_hidden') ? '/admin/user' : '/admin/user?show_hidden=1' }}"
                   class="btn btn-{{ request()->has('show_hidden') ? 'warning' : 'secondary' }} mr-2">
                    <i class="fa fa-{{ request()->has('show_hidden') ? 'eye-slash' : 'eye' }}"></i>
                    {{ request()->has('show_hidden') ? 'پنهان کردن کاربران مخفی' : 'نمایش کاربران مخفی' }}
                </a>
                <form method="GET" action="/admin/user" class="d-inline-block mr-2">
                    @if(request()->has('show_hidden'))<input type="hidden" name="show_hidden" value="1">@endif
                    <select name="grop_id" class="form-control d-inline-block" style="width:auto;" onchange="this.form.submit()">
                        <option value="">همه مجموعه‌ها</option>
                        @foreach($grops as $g)
                            <option value="{{ $g->id }}" {{ $selected_grop_id == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </form>
                <div id="datatable-buttons_wrapper" class="dataTables_wrapper  dt-bootstrap4 p-0 m-0 no-footer">
                    {{-- <button type="button" id="basic" --}}
                    {{-- class="btn btn-secondary btn-trans waves-effect "><i class="fa fa-print"></i></button> --}}
                    <table id="userDataTable" class="table datatable-buttons table-striped table-bordered p-0 m-0 no-footer"
                        role="grid" aria-describedby="datatable-buttons_info" style="width: 100%;direction: rtl;"
                        width="100%" cellspacing="0">
                        <thead>
                            <tr role="row">
                                <th class="sorting" style="width: 15px;text-align: right !important;"><input id="select_all"
                                        type="checkbox"></th>
                                <th class="sorting_asc" style="width: 15px;text-align: right !important;">شناسه</th>
                                <th class="sorting" style="text-align: right !important;">کداختصاصی</th>
                                <th class="sorting" style="text-align: right !important;">نام</th>
                                <th class="sorting" style="text-align: right !important;">نام خانوادگی</th>
                                <th class="sorting" style="text-align: right !important;">شماره موبایل</th>
                                <th class="sorting" style="text-align: right !important;">شماره تلفن</th>
                                <th class="sorting" style="text-align: right !important;">کدملی</th>
                                <th class="sorting" style="text-align: right !important;">استان</th>
                                <th class="sorting" style="text-align: right !important;">شهر</th>

                                <th class="sorting" style="text-align: right !important;">تاریخ اشتراک</th>
                                <th class="sorting" style="text-align: right !important;">مجموعه</th>
                                <th class="sorting" style="text-align: right !important;">تراز مالی</th>
                                <th class="sorting" style="text-align: right !important;"
                                    aria-label="Salary: activate to sort column ascending">اقدامات</th>


                            </tr>
                        </thead>


                        <tbody>
                            @foreach ($users as $user)
                                <tr role="row" class="odd" style="{{ ($user->is_hidden ?? false) ? 'background-color:#fffde7!important;opacity:0.75;' : ($user['inactive'] ? 'background-color:#f4c5c5 !important' : '') }}">
                                    <td style="text-align: right !important;" value="{{ $user['id'] }}" class="sorting_1">
                                        <input name="items[]" class="item-checkbox" value={{ $loop->index + 1 }}
                                            type="checkbox"></td>

                                    <td style="text-align: right !important;">{{ $user['id'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['hash'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['name'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['name2'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['mobile'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['phone'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['kod'] }}</td>


                                    <td style="text-align: right !important;">{{ $user['state'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['city'] }}</td>
                                    <td style="text-align: right !important;">{{ $user['active_am'] }}</td>
                                    <td style="text-align: right !important;">
                                        @foreach($user->user_grops->unique('grop_id') as $ug)
                                            <span class="badge badge-secondary">{{ $ug->grop_id }}</span><br/>
                                        @endforeach
                                    </td>
                                    <td style="text-align: right !important;">
                                        @php $balance = ($user->total_invoices + $user->total_penalties) - $user->total_paid; @endphp
                                        @if($balance == 0)
                                            <span class="badge badge-success">0</span>
                                        @else
                                            <span class="badge badge-{{ $balance > 0 ? 'danger' : 'success' }}">{{ number_format($balance) }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right !important;">
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="userActionDropdown" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                عملیات
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="userActionDropdown">
                                                <a href="/admin/user/data/{{ $user['id'] }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="اطلاعات اختصاصی کاربر"
                                                    class="dropdown-item text-info">
                                                    <i class="fa fa-address-book"></i> اطلاعات اختصاصی کاربر
                                                </a>
                                                <a href="/admin/user/invoice/{{ $user['id'] }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="لیست پرداختی کاربر"
                                                    class="dropdown-item text-info">
                                                    <i class="mdi mdi-link"></i> لیست پرداختی کاربر
                                                </a>
                                                <a href="/admin/user/grop/{{ $user['id'] }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="لیست مجموعه ها"
                                                    class="dropdown-item text-success">
                                                    <i class="fa fa-user-o"></i> لیست مجموعه ها
                                                </a>
                                                <a href="/admin/user/pay/{{ $user['id'] }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="لیست پرداخت ها"
                                                    class="dropdown-item text-primary">
                                                    <i class="fa fa-paypal"></i> لیست پرداخت ها
                                                </a>
                                                <a href="/admin/user/edit/{{ $user['id'] }}" data-toggle="tooltip"
                                                    data-placement="top" data-original-title="ویرایش"
                                                    class="dropdown-item text-warning">
                                                    <i class="fa fa-pencil"></i> ویرایش
                                                </a>
                                                <a href="/admin/user/forgotPassword/{{ $user['id'] }}"
                                                    class="dropdown-item {{ $user['password'] == null ? 'text-warning' : 'text-danger' }}"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="ریست رمز عبور">
                                                    <i class="fa fa-lock"></i> ریست رمز عبور
                                                </a>
                                                <button class="dropdown-item text-danger btn-delete"
                                                    id="{{ $user['id'] }}" name="{{ $user['name'] }}"
                                                    data-toggle="tooltip" data-placement="top" data-original-title="حذف">
                                                    <i class="fa fa-times"></i> حذف
                                                </button>
                                                <button
                                                    class="dropdown-item btn-{{ $user['inactive'] ? 'danger' : 'success' }} btn-inactive"
                                                    id="{{ $user['id'] }}" name="{{ $user['name'] }}"
                                                    data-toggle="tooltip" data-placement="top"
                                                    data-original-title="{{ $user['inactive'] ? 'فعال' : 'غیر فعال' }} شود">
                                                    <i class="fa fa-black-tie"></i>
                                                    {{ $user['inactive'] ? 'فعال' : 'غیر فعال' }} شود
                                                </button>
                                                <form method="POST" action="/admin/user/toggle-hidden/{{ $user['id'] }}" class="d-inline" style="margin:0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item {{ ($user->is_hidden ?? false) ? 'text-success' : 'text-secondary' }}"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ ($user->is_hidden ?? false) ? 'نمایش کاربر' : 'مخفی کردن کاربر' }}">
                                                        <i class="fa fa-{{ ($user->is_hidden ?? false) ? 'eye' : 'eye-slash' }}"></i>
                                                        {{ ($user->is_hidden ?? false) ? 'نمایش کاربر' : 'مخفی کردن کاربر' }}
                                                    </button>
                                                </form>
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



    </div>

    </div>
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
            // Default Datatable
            //$('#userDataTable').DataTable();

            //Buttons examples
            //$('#example').DataTable({
            //});


            var table = $('#userDataTable').DataTable({
                lengthChange: true,
                buttons: ['copy', 'excel'],
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "همه"]
                ],
            });
            table.buttons().container()
                .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

            // حذف فیلتر کلاینت‌ساید - فیلتر مجموعه به صورت سرور ساید انجام می‌شود

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

        function changegroup() {

            <?php
            
            $grops = App\grop::get();
            $user_grops = App\user_grop::get();
            
            ?>
            var grops = @json($grops);

            var selectHtml = '<select id="group-select" class="swal2-select">';
            grops.forEach(function(grop) {
                selectHtml += '<option value="' + grop.id + '">' + grop.name + '</option>';
            });
            selectHtml += '</select>';

            selectHtml +=
                '<select id="usergroup-select" class="swal2-select" disabled><option value="">Select a group first</option></select>';

            Swal.fire({
                title: "Select Group and User Group!",
                html: selectHtml,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    return {
                        groupId: document.getElementById('group-select').value,
                        userGroupId: document.getElementById('usergroup-select').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedGroupId = result.value.groupId;
                    var selectedUserGroupId = result.value.userGroupId;
                    console.log("Selected Group ID:", selectedGroupId);
                    console.log("Selected User Group ID:", selectedUserGroupId);
                    var selectedIds = getSelectedIds();
                    $.ajax({

                        url: '/admin/grop/user/changegroup', // Replace with your server endpoint
                        type: 'POST',
                        data: {
                            ids: selectedIds,
                            selectedGroupId: selectedGroupId,
                            selectedUserGroupId,
                            selectedUserGroupId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Data sent successfully:', response);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error sending data:', error);
                        }
                    });
                    // You can use the selected IDs here (e.g., send them to the server)
                }
            });
            $.ajax({
                url: '/admin/grop/user/getusergrop', // Laravel route for handling the request
                type: 'POST',
                data: {
                    groupId: 1,
                    _token: '{{ csrf_token() }}' // CSRF token for security
                },
                success: function(response) {
                    var userGroupSelect = $('#usergroup-select');
                    userGroupSelect.empty();
                    userGroupSelect.append('<option value="">Select a user group</option>');
                    userGroupSelect.prop('disabled', false);
                    response.forEach(function(userGroup) {
                        userGroupSelect.append('<option value="' + userGroup.id + '">' + userGroup
                            .name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching user groups:', error);
                }
            });
            $('#group-select').on('change', function() {
                var groupId = $(this).val();
                if (groupId) {
                    // Fetch user groups based on the selected group ID
                    $.ajax({
                        url: '/admin/grop/user/getusergrop', // Laravel route for handling the request
                        type: 'POST',
                        data: {
                            groupId: groupId,
                            _token: '{{ csrf_token() }}' // CSRF token for security
                        },
                        success: function(response) {
                            var userGroupSelect = $('#usergroup-select');
                            userGroupSelect.empty();
                            userGroupSelect.append('<option value="">Select a user group</option>');
                            userGroupSelect.prop('disabled', false);
                            response.forEach(function(userGroup) {
                                userGroupSelect.append('<option value="' + userGroup.id + '">' +
                                    userGroup.name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching user groups:', error);
                        }
                    });
                } else {
                    $('#usergroup-select').empty().append('<option value="">Select a group first</option>').prop(
                        'disabled', true);
                }
            });

        }
    </script>
    <script src="/assets/js/printThis.js"></script>
    <script>
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).attr('id'),
                email = $(this).attr('name');

            Swal.fire({
                html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف کاربر  ' +
                    email +
                    ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/user/delete/' +
                    id +
                    '" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "d-none",
                confirmButtonText: "Yes, Delete It!",
                cancelButtonClass: "d-none",
                buttonsStyling: !1,
                showCloseButton: !0
            });

            $(document).on('click', '#close-delete', function() {
                $('.swal2-close').trigger('click');
            });
        });

        $(document).on('click', '.btn-inactive', function() {
            var id = $(this).attr('id'),
                email = $(this).attr('name');

            Swal.fire({
                html: '<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از تغییر وضعیت فعالی کاربر  ' +
                    email +
                    ' مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/user/inactive/' +
                    id +
                    '" method="post">@csrf<input name="_method" type="hidden" value="post"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , تغییر وضعیت </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-inactive">لغو</button></form></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "d-none",
                confirmButtonText: "Yes, Inactive It!",
                cancelButtonClass: "d-none",
                buttonsStyling: !1,
                showCloseButton: !0
            });

            $(document).on('click', '#close-inactive', function() {
                $('.swal2-close').trigger('click');
            });
        });
    </script>

    <script>
        {{-- $('#basic').on("click", function () { --}}
        {{-- $('.demo').printThis({ --}}
        {{-- base: "https://jasonday.github.io/printThis/", --}}
        {{-- header:"<h3 style='text-align: right !important;margin-bottom: 50px;'>فهرست کاربران</h3>", --}}
        {{-- loadCSS: ["{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/bootstrap.min.css","{{\Illuminate\Support\Facades\URL::to('/')}}/assets/css/font.css"], --}}
        {{-- importCSS:true, --}}
        {{-- importStyle: true --}}
        {{-- }); --}}
        {{-- }) --}}
    </script>
@endsection
