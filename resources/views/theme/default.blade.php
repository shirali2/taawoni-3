<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />

    <title>{{ \App\setting::where('name', 'site_title')->value('input1') ?? 'سامانه تعاونیهای مسکن بیمه ایران' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- App css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('libs/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="/js/xlsx.js"></script>
    <script src="/assets/js/modernizr.min.js"></script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/popper.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
</head>

<body>

    <!-- Navigation Bar-->
    <header id="topnav">
        <div class="topbar-main">
            <div class="container-fluid">

                <!-- Logo container-->
                <div class="logo">
                    <!-- Text Logo -->
                    <!--<a href="index.html" class="logo">-->
                    <!--<span class="logo-small"><i class="mdi mdi-radar"></i></span>-->
                    <!--<span class="logo-large"><i class="mdi mdi-radar"></i> Adminto</span>-->
                    <!--</a>-->
                    <!-- Image Logo -->
                    <a href="/" class="logo">
                        <strong>خوش آمدید {{ session('name') }}</strong>
                    </a>
                </div>
                <!-- End Logo container-->

                <div class="menu-extras topbar-custom">

                    <ul class="list-unstyled topbar-right-menu float-right mb-0">

                        <li class="menu-item">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle nav-link">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </li>

                        <li>
                            <!-- Notification -->
                            {{-- <div class="notification-box"> --}}
                            {{-- <ul class="list-inline mb-0"> --}}
                            {{-- @if (session()->has('announcement')) --}}
                            {{-- <li class="position-relative"> --}}
                            {{-- <a href="javascript:void(0);" class="right-bar-toggle"> --}}
                            {{-- <i class="mdi mdi-bell-outline noti-icon"></i> --}}
                            {{-- </a> --}}
                            {{-- @php --}}
                            {{-- $announcements_count=count(\App\announcement::where("active",0)->get()); --}}
                            {{-- @endphp --}}
                            {{-- <span style="bottom: 0; left: 0;" class="badge position-absolute badge-danger">{{$announcements_count}}</span> --}}

                            {{-- </li> --}}
                            {{-- @endif --}}
                            {{-- </ul> --}}
                            {{-- </div> --}}
                            <!-- End Notification bar -->
                        </li>

                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i style="font-size: 34px; line-height: 63px; color: black;"
                                    class="fa d-block fa-gear"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">

                                <!-- item-->
                                <a href="/logout" class="dropdown-item notify-item">
                                    <i class="ti-power-off m-r-5"></i> خروج
                                </a>

                            </div>
                        </li>

                    </ul>
                </div>
                <!-- end menu-extras -->

                <div class="clearfix"></div>

            </div> <!-- end container -->
        </div>
        <!-- end topbar-main -->

        <div class="navbar-custom">
            <div class="container-fluid">
                <div id="navigation">
                    <!-- Navigation Menu-->
                    <ul class="navigation-menu">
                        <li class="has-submenu ">
                            <a href="/admin"><i class="mdi mdi-view-dashboard"></i> <span> پیشخوان </span> </a>
                        </li>



                        <li class="has-submenu">


                            <a href="#"><i class="mdi mdi-texture"></i><span> مجموعه ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/grop">لیست مجموعه ها</a></li>
                                <li><a href="/admin/grop/add">ایجاد مجموعه جدید</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span> منو ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/menu">لیست منو ها</a></li>
                                <li><a href="/admin/menu/add">ایجاد منو جدید</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span>فرم ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/form">لیست فرم ها</a></li>
                                <li><a href="/admin/form/add">ایجاد فرم جدید</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span>تیکت ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/ticket">لیست تیکت ها</a></li>
                                <li><a href="/admin/issue">لیست موضوع تیکت ها</a></li>
                                <li><a href="/admin/contactu">لیست پیام تماس با ما</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span>صندوق پیام ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/pm">لیست پیام ها</a></li>
                                <li><a href="/admin/pm/add">ایجاد پیام جدید</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">
                            <a href="#"><i class="mdi mdi-texture"></i><span>اشتراک</span></a>
                            <ul class="submenu">
                                <li><a href="/admin/product">لیست اشتراک</a></li>
                                <li><a href="/admin/product/add">ایجاد محصول</a></li>
                                <li><a href="/admin/product/purchased">خریداری شده</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">
                            <a href="#"><i class="mdi mdi-texture"></i><span>محصولات</span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/crop">لیست محصولات </a></li>
                                <li><a href="/admin/crop/add">ایجاد محصول</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span>صورت حساب ها</span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/invoice">لیست صورت حساب </a></li>
                                <li><a href="/admin/invoice/add">ایجاد صورت حساب</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu">

                            <a href="#"><i class="mdi mdi-texture"></i><span>آگهی</span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/advertising/product">لیست محصول اگهی </a></li>
                                <li><a href="/admin/advertising">اگهی ها</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu ">


                            <a href="#"><i class="mdi mdi-texture"></i><span> کاربران </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/user">لیست کاربران</a></li>
                                <li><a href="/admin/user/add">ایجاد کاربر جدید</a></li>
                                <li><a href="/admin/user/add/xlsx">ایجاد کاربران با اکسل</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu ">
                            <a href="#"><i class="mdi mdi-note-text"></i><span> یادداشت ها </span> </a>
                            <ul class="submenu">
                                <li><a href="/admin/notes">لیست یادداشت ها</a></li>
                                <li><a href="/admin/notes/create">ثبت یادداشت جدید</a></li>
                            </ul>
                        </li>
                        <li class="has-submenu ">
                            <a href="/admin/setting"><i class="fa fa-cog fa-fw"></i><span> تنظیمات </span> </a>
                        </li>

                    </ul>
                    <!-- End navigation menu -->
                </div> <!-- end #navigation -->
            </div> <!-- end container -->
        </div> <!-- end navbar-custom -->
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="side-bar right-bar">
            <a href="javascript:void(0);" class="right-bar-toggle">
                <i class="mdi mdi-close-circle-outline"></i>
            </a>

            <h4 class=" pb-0 pt-0 ">اعلانات <a href="/announcement"
                    class="btn btn-purple w-sm btn-sm waves-effect waves-light m-b-5">نمایش همه </a></h4>


            <div class="notification-list nicescroll">
                <ul class="list-group w-100 list-no-border user-list">

                    <li class="list-group-item">
                        <a href="/announcement/active/1 class="user-list-item">

                            <div class="user-desc m-0">
                                <p style="color: #435966!important;" class="mb-2">gggggg</p>
                                <span class="time">2321312434</span>
                            </div>
                        </a>
                    </li>






                </ul>
            </div>
        </div>
        <div class="container-fluid">

            @yield('container')


        </div> <!-- end container -->


        <!-- Right Sidebar -->

        <!-- /Right-bar -->

    </div>
    <!-- end wrapper -->


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    Accounting Admin Panel
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer -->



    <script src="/js/xlsx.js"></script>

    <script src="/assets/js/waves.js"></script>
    <script src="/assets/js/jquery.slimscroll.js"></script>
    <!-- Required datatable js -->
    <script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="/assets/plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="/assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
    <script src="/assets/plugins/datatables/jszip.min.js"></script>
    <script src="/assets/plugins/datatables/pdfmake.min.js"></script>
    <script src="/assets/plugins/datatables/vfs_fonts.js"></script>
    <script src="/assets/plugins/datatables/buttons.html5.min.js"></script>
    <script src="/assets/plugins/datatables/buttons.print.min.js"></script>
    <script src="/assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>

    <!-- Key Tables -->
    <script src="/assets/plugins/datatables/dataTables.keyTable.min.js"></script>

    <!-- Responsive examples -->
    <script src="/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>

    <!-- Selection table -->
    <script src="/assets/plugins/datatables/dataTables.select.min.js"></script>
    <script src="/assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/assets/plugins/parsleyjs/dist/parsley.min.js"></script>

    <script src="/assets/plugins/tiny-editable/mindmup-editabletable.js"></script>
    <script src="/assets/plugins/tiny-editable/numeric-input-example.js"></script>
    <!-- App js -->
    <script src="/assets/js/jquery.core.js"></script>
    <script src="/assets/js/jquery.app.js"></script>
    <script src="/assets/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            // Default Datatable
            $('.datatable').DataTable();

            //Buttons examples


            var table = $('.demo').DataTable({
                lengthChange: true,
                buttons: ['copy', 'excel']
            });
            // Key Tables

            $('#key-table').DataTable({
                keys: true
            });

            // Responsive Datatable
            $('#responsive-datatable').DataTable();

            // Multi Selection Datatable
            $('#selection-datatable').DataTable({
                select: {
                    style: 'multi'
                }
            });
            table.buttons().container()
                .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
            if (typeof(no_select3_in_main_layout) == "undefined") {
                $(".select2").select2();
            }

            $(".select2-limiting").select2({
                maximumSelectionLength: 2
            });
            $('textarea#textarea').maxlength({
                alwaysShow: true,
                warningClass: "badge badge-success",
                limitReachedClass: "badge badge-danger"
            });


            $('form').each(function() {
                $(this).parsley();
            })


        });
    </script>
</body>

</html>
