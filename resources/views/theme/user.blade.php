

<!DOCTYPE html>
<html>
@php
    // ریدایرکت‌ها به Middleware منتقل شدند.
@endphp

@php

    $_default_menu_access = json_decode('[{"menu":"bills","hide":"1"},{"menu":"ticket","hide":"1"},{"menu":"advertisment","hide":"1"},{"menu":"products","hide":"1"}]');

    $grop = null;
    $user_grop_id = session('grop_user');
    if ($user_grop_id) {
        try {
            $user_grop = App\user_grop::find($user_grop_id);
            if ($user_grop) {
                $grop = App\grop::find($user_grop['grop_id']);
                if ($grop) {
                    if (!$grop->user_menu_access || trim($grop->user_menu_access) === '' || $grop->user_menu_access == '[]') {
                        $grop->user_menu_access = $_default_menu_access;
                    } else {
                        $decoded = json_decode($grop->user_menu_access);
                        // ensure all 4 indices exist
                        for ($i = 0; $i < 4; $i++) {
                            if (!isset($decoded[$i])) {
                                $decoded[$i] = (object)['menu' => '', 'hide' => '1'];
                            }
                        }
                        $grop->user_menu_access = $decoded;
                    }
                }
            }
        } catch (\Throwable $e) {
            $grop = null;
        }
    }

    if (!$grop) {
        $grop = new \stdClass();
        $grop->user_menu_access = $_default_menu_access;
        $grop->name = '';
        $grop->img1 = '';
    }

@endphp

@php
    try {
        $site_title = \App\setting::where('name', 'title_tag')->value('input1') ?: 'مدیریت تعاونیها';
        $site_logo_file = \App\setting::where('name', 'site_logo')->value('input1');
    } catch (\Throwable $e) {
        $site_title = 'مدیریت تعاونیها';
        $site_logo_file = null;
    }
@endphp
<head>
    <meta charset="utf-8" />

    <title>{{ $site_title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- App css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/main.css" rel="stylesheet" type="text/css" />

    <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/templatemo-style.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/fontawesome.min.css" rel="stylesheet" type="text/css" />
		<link href="/assets/css/style.bundle.rtl.css" rel="stylesheet" type="text/css" />
		<script src="/assets/js/plugins.bundle.js"></script>
		<script src="/assets/js/scripts.bundle.js"></script>
    <script src="/assets/js/modernizr.min.js"></script>
    <link rel="stylesheet" href="{{ asset('libs/bootstrap-icons/bootstrap-icons.min.css') }}">

    <style>
    @media (max-width: 991.98px) {
        #kt_quick_user {
            padding: 1.25rem !important;
        }
        #kt_quick_user .offcanvas-header {
            padding-bottom: 0.5rem !important;
        }
        #kt_quick_user .separator.separator-dashed {
            margin-top: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }
        .aside-menu-wrapper {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        .aside-workspace {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
    }
    </style>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/popper.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
</head>
@php
function getCookie($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

// Get the screen width flag from the cookie
$wide_screen = getCookie('wide_screen');

    // خطاها رو می‌گیریم تا صفحه crash نکنه
    try { $pms = \App\Http\Controllers\userController::pm(); } catch (\Throwable $e) { $pms = collect([]); \Illuminate\Support\Facades\Log::error('pm() error: ' . $e->getMessage()); }

    $menus = \App\Http\Controllers\userController::get_menus();
    $menus = !empty($menus) ? $menus : [];
@endphp

<body id="kt_body" class="header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-secondary-enabled page-loading @if (count($pms) > 0) modal-open @endif">
    <script>
        function setScreenWidthCookie() {
            const width = window.innerWidth;
            const flag = width > 990 ? 1 : 0;
            const cookieName = "wide_screen";
            const existingCookie = document.cookie.split('; ').find(row => row.startsWith(cookieName));
            if (!existingCookie || existingCookie.split('=')[1] != flag) {
                document.cookie = cookieName + "=" + flag + "; path=/";
                window.location.reload();
            }
        }

        // Set the cookie and reload the page only if necessary
        setScreenWidthCookie();
    </script>

    <!-- Navigation Bar-->

			<div class="grop_name_class" style="
    
">
    <h3>{{ session('grop_name').' _ '.session('name_grop_user') }}</h3>
</div>
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile">
			<!--begin::Logo-->
                    <a href="/" class="logo">
                        <img style="padding-top:3px;width: 45px;
    height: 45px;"
                            src="{{ $site_logo_file ? '/img_login_register/'.$site_logo_file : '/grop_img/'.session('logo') }}">

                    </a>
			<!--end::Logo-->
			<!--begin::Toolbar-->
			
			<div class="d-flex align-items-center">
				<div class="notification-box">
					<ul class="list-inline mb-0">
						<li>
							<a href="javascript:void(0);" class="right-bar-toggle">
								<i class="dripicons-broadcast"></i>
							</a>
							<div class="noti-dot">
								<span class="dot"></span>
								<span class="pulse"></span>
							</div>
						</li>
					</ul>
				</div>
											@if (!$wide_screen)

				<a href="#" class="btn btn-icon btn-clean btn-lg w-40px h-40px" id="kt_quick_user_toggle" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="پروفایل">
					<span class="symbol symbol-30 symbol-lg-40">
						<span class="svg-icon svg-icon-xl">
							<!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<polygon points="0 0 24 0 24 24 0 24" />
									<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
									<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>
						<!--<span class="symbol-label font-size-h5 font-weight-bold">S</span>-->
					</span>
				</a>
										@endif

				<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
					<span></span>
				</button>
			</div>
			<!--end::Toolbar-->
		</div>
		<!--end::Header Mobile-->
		<div class="d-flex flex-column ">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Aside-->
				<div class="aside aside-left d-flex aside-fixed" id="kt_aside">
					<!--begin::Primary-->
					<div class="aside-primary d-flex flex-column align-items-center flex-row-auto">
						<!--begin::Brand-->
						<div class="aside-brand d-flex flex-column align-items-center flex-column-auto py-5 py-lg-12">
							<!--begin::Logo-->
                    <a href="/" class="logo">
                        <img style="padding-top:3px;width:60px;
    height: 60px;"
                            src="{{ $site_logo_file ? '/img_login_register/'.$site_logo_file : '/grop_img/'.session('logo') }}">

                    </a>
							<!--end::Logo-->
						</div>
						<!--end::Brand-->
						<!--begin::Nav Wrapper-->
						<div class="aside-nav d-flex flex-column align-items-center flex-column-fluid py-5 scroll scroll-pull">
							<!--begin::Nav-->
							<ul class="nav flex-column" role="tablist">
								<!--begin::Item-->

								<!--end::Item-->
								<!--begin::Item-->

		         <div class="notification-box">
                                <ul class="list-inline mb-0">
                                    <li>
                                        <a href="javascript:void(0);" class="right-bar-toggle">
                                            <i class="dripicons-broadcast"></i>
                                        </a>
                                        <div class="noti-dot">
                                            <span class="dot"></span>
                                            <span class="pulse"></span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
							@if ($wide_screen)
							<a href="#" class="btn btn-icon btn-clean btn-lg w-40px h-40px" id="kt_quick_user_toggle" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="پروفایل">
								<span class="symbol symbol-30 symbol-lg-40">
									<span class="svg-icon svg-icon-xl">
										<!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<polygon points="0 0 24 0 24 24 0 24" />
												<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
												<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
									<!--<span class="symbol-label font-size-h5 font-weight-bold">S</span>-->
								</span>
							</a>
							@endif

								<!--end::Item-->
							</ul>
							<!--end::Nav-->
						</div>
						<!--end::Nav Wrapper-->
						<!--begin::Footer-->
						<div class="aside-footer d-flex flex-column align-items-center flex-column-auto py-4 py-lg-10">
							<!--begin::Aside Toggle-->
							<span class="aside-toggle btn btn-icon btn-primary btn-hover-primary shadow-sm" id="kt_aside_toggle" data-toggle="tooltip" data-placement="right" data-container="body" data-boundary="window" title="Toggle Aside">
								<i class="bi bi-arrows-expand-vertical"></i>
							</span>
							<!--end::Aside Toggle-->
       
							<!--begin::User-->
							<!--end::User-->
						</div>
						<!--end::Footer-->
					</div>
					<!--end::Primary-->
					<!--begin::Secondary-->
					<div class="aside-secondary d-flex flex-row-fluid">
						<!--begin::Workspace-->
						<div class="aside-workspace scroll scroll-push my-2 ">
							<!--begin::Tab Content-->
							<div class="tab-content">
								<!--begin::Tab Pane-->

								<!--end::Tab Pane-->
								<!--begin::Tab Pane-->
								<div class="tab-pane fade active show" id="kt_aside_tab_1">
									<!--begin::Aside Menu-->
									<div class="aside-menu-wrapper flex-column-fluid px-3 px-lg-10 py-5" id="kt_aside_menu_wrapper">
										<!--begin::Menu Container-->
										<div id="kt_aside_menu" class="aside-menu min-h-lg-800px" data-menu-vertical="1" data-menu-scroll="1">
											<!--begin::Menu Nav-->
											<ul class="menu-nav">
												@foreach ($menus as $menu)
													@if (session('pending_required_form') && !request()->is('user/menu/'.$menu['id'].'*'))
														@continue
													@endif

													@if (!empty($menu['info_unders']))
															<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																<a href="javascript:;" class="menu-link menu-toggle">
																	<i class="menu-icon flaticon-cogwheel-1"></i>
																	<span class="menu-text">{{ $menu['name'] }}</span>
																	<i class="bi bi-arrow-down-short"></i>
																</a>
																<div class="menu-submenu">
																	<ul class="menu-subnav">
																		@foreach ($menu['info_unders'] as $info_under)
																		@if (session('pending_required_form') && !request()->is('user/menu/'.$menu['id'].'/'.$info_under['ID']))
																			@continue
																		@endif
																		<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																			<a href="/user/menu/{{ $menu['id'] }}/{{ $info_under['ID'] }}" href="javascript:;" class="menu-link menu-toggle">
																				<i class="menu-bullet menu-bullet-line">
																					<span></span>
																				</i>
																				<span class="menu-text">{{ $info_under['Name'] }}</span>
																			</a>
																		</li>
																		@endforeach
						
																	</ul>
																</div>
															</li>
																						@else
															<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																<a href="/user/menu/{{ $menu['id'] }}" href="javascript:;" class="menu-link menu-toggle">
																	<i class="menu-icon flaticon-users-1"></i>
																	<span class="menu-text"> {{ $menu['name'] }}</span>
																</a>
															</li> 
																					@endif
						
																				@endforeach
							
															<!--TICKET CONDITION-->
															@if (!session('pending_required_form'))
																					@if ($grop->user_menu_access[1]->hide == '0')
															<li class="menu-item" aria-haspopup="true">
																<a href="/user/ticket" class="menu-link">
																	<i class="menu-icon flaticon-users-1"></i>
																	<span class="menu-text"> تیکت/گفتگو</span>
																</a>
															</li>
																					@endif

															@if ($grop->user_menu_access[1]->hide == '0')
															<li class="menu-item" aria-haspopup="true">
																<a href="/user/pm" class="menu-link">
																	<i class="menu-icon flaticon2-send"></i>
																	<span class="menu-text"> صندوق پیام</span>
																</a>
															</li>
															@endif

															@if (isset($grop->user_menu_access[3]) && $grop->user_menu_access[3]->hide == '0')
															<li class="menu-item" aria-haspopup="true">
																<a href="/user/crop" class="menu-link">
																	<i class="menu-icon flaticon2-delivery-package"></i>
																	<span class="menu-text"> محصولات</span>
																</a>
															</li>
															@endif


															@if ($grop->user_menu_access[2]->hide == '0')
															<li class="menu-item" aria-haspopup="true">
																<a href="/user/advertising/list" class="menu-link">
																	<i class="menu-icon flaticon2-digital-marketing"></i>
																	<span class="menu-text"> تبلیغات</span>
																</a>
															</li>
															@endif
						
												<li class="menu-item" aria-haspopup="true">
																<a href="/user/notes" class="menu-link">
																	<i class="menu-icon flaticon2-bell-2"></i>
																	<span class="menu-text"> یادداشت‌های من</span>
																</a>
															</li>

												@if ($grop->user_menu_access[0]->hide == '0')
															<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																<a href="javascript:;" class="menu-link menu-toggle">
																	<i class="menu-icon flaticon-cogwheel-1"></i>
																	<span class="menu-text">صورت حساب ها</span>
																	<i class="bi bi-arrow-down-short"></i>
																</a>
																<div class="menu-submenu">
																	
																	<ul class="menu-subnav">
																		<li class="menu-item menu-item-parent" aria-haspopup="true">
																			<span class="menu-link">
																				<span class="menu-text">صورت حساب ها</span>
																			</span>
																		</li>
																		<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																			<a href="/user/invoice" href="javascript:;" class="menu-link menu-toggle">
																				<i class="menu-bullet menu-bullet-line">
																					<span></span>
																				</i>
																				<span class="menu-text">صورت حساب ها</span>
																			
																			</a>
																		</li>
																		<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																			<a href="/user/invoice/all" href="javascript:;" class="menu-link menu-toggle">
																				<i class="menu-bullet menu-bullet-line">
																					<span></span>
																				</i>
																				<span class="menu-text">صورت حساب نهایی</span>
																			
																			</a>
																		</li>
																		<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
																			<a href="/user/invoice/pay" href="javascript:;" class="menu-link menu-toggle">
																				<i class="menu-bullet menu-bullet-line">
																					<span></span>
																				</i>
																				<span class="menu-text">لیست پرداختی ها</span>
																
																				<i class="menu-arrow"></i>
																			</a>
																		</li>
																	</ul>
																</div>
															</li>
																					@endif
															@endif

						
														</ul>
											<!--end::Menu Nav-->
										</div>
										<!--end::Menu Container-->
									</div>
									<!--end::Aside Menu-->
								</div>
								<!--end::Tab Pane-->
							</div>
							<!--end::Tab Content-->
						</div>
						<!--end::Workspace-->
					</div>
					<!--end::Secondary-->
				</div>
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Main-->

		<!-- begin::User Panel-->
		<div id="kt_quick_user" class="offcanvas offcanvas-left p-10">
			<!--begin::Header-->
			<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
				<h3 class="font-weight-bold m-0">پنل کاربری
				</h3>
				<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
					<i class="ki ki-close icon-xs text-muted"></i>
				</a>
			</div>
			<!--end::Header-->
			<!--begin::Content-->
			<div class="offcanvas-content pr-5 mr-n5">
				<!--begin::Header-->
				<!--end::Header-->
				<!--begin::Separator-->
				<div class="separator separator-dashed mt-8 mb-5"></div>
				<!--end::Separator-->
				<!--begin::Nav-->
				<div class="navi navi-spacer-x-0 p-0">
					<!--begin::Item-->
					<a href="/user" class="navi-item">
						<div class="navi-link">
							<div class="navi-text">
								<div class="font-weight-bold">پیشخوان من</div>
							</div>
						</div>
					</a>
					<!--end:Item-->
					<!--begin::Item-->
					<a href="/user/setting" class="navi-item">
						<div class="navi-link">
							<div class="navi-text">
								<div class="font-weight-bold">تنظمیات</div>
							</div>
						</div>
					</a>
					<!--end:Item-->
					<!--begin::Item-->
					<a href="/user/product" class="navi-item">
						<div class="navi-link">
							<div class="navi-text">
								<div class="font-weight-bold">اشتراک</div>
							</div>
						</div>
					</a>
					<!--end:Item-->
			<div class="navi mt-2 d-flex justify-content-center">
							<a href="/logout" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">خروج</a>
						</div>
				</div>
				<!--end::Nav-->
				<!--begin::Separator-->
				<div class="separator separator-dashed my-3"></div>
				<!--end::Separator-->
				<!--begin::Notifications-->

				<!--end::Notifications-->
			</div>
			<!--end::Content-->
		</div>

		<!--begin::Scrolltop-->

		<script>var HOST_URL = "/";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#1BC5BD", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#6993FF", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#1BC5BD", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#E1E9FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Theme Bundle-->
		<!--begin::Page Vendors(used by this page)-->
	
		<!--end::Page Vendors-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="assets/js/pages/widgets.js"></script>

		<!--end::Page Scripts-->


    <!-- Navigation Bar-->

    <!-- End Navigation Bar-->

    @if (count($pms) > 0)
        <div id="myModal" class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            style="display: block; padding-left: 17px;background-color: #000000db;" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title mt-0" id="myModalLabel">{{ $pms[0]['title'] }}</h4>
                    </div>
                    <div class="modal-body py-5">
                        @if ($pms[0]['img'])
                            <div class="text-center">
                                <img style="max-width: 440px;" src="/pm_img/{{ $pms[0]['img'] }}">
                            </div>
                        @endif
                        {{ $pms[0]['text'] }}
                    </div>
                    <div class="modal-footer">
                        <a href="/user/pm/active/{{ $pms[0]['id'] }}" type="button"
                            class="btn btn-primary waves-effect waves-light">مشاهده کردم</a>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    @endif

    <div class="wrapper" style="padding-bottom: 500px;min-height: 100%;">
        <div class="side-bar right-bar" style="width: 336px;">
            <a href="javascript:void(0);" class="right-bar-toggle">
                <i class="mdi mdi-close-circle-outline"></i>
            </a>

            <h4 class="  ">تبلبغات </h4>


            <div class="notification-list nicescroll" style="overflow: auto;width: 101%;">
                <ul class="list-group w-100 list-no-border user-list">
                    @foreach (\App\Http\Controllers\userController::img_advertising(25) as $advertising)
                        <li class="list-group-item">
                            <img width="300px" height="100px" src="/advertising_img/{{ $advertising['img'] }}">
                        </li>
                    @endforeach





                </ul>
            </div>
        </div>



        <div class="container-fluid page-contetnt">

            @yield('container')


        </div> <!-- end container -->


        <!-- Right Sidebar -->

        <!-- /Right-bar -->
    <footer class="footer" style="padding-top: 5px;padding-bottom: 5px">
        <div class="container footer-content">
            <div class="row">
                <div class="col-12 col-lg-4 text-center">
                    <img width="300px" height="100px" id="img_advertising1" img_active="1"
                        img1="{{ \App\Http\Controllers\userController::img_advertising(13) }}"
                        img2="{{ \App\Http\Controllers\userController::img_advertising(14) }}"
                        img3="{{ \App\Http\Controllers\userController::img_advertising(15) }}"
                        img4="{{ \App\Http\Controllers\userController::img_advertising(16) }}"
                        src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(13) }}">

                </div>
                <div class="col-12 col-lg-4 text-center">
                    <img width="300px" height="100px" id="img_advertising2" img_active="1"
                        img1="{{ \App\Http\Controllers\userController::img_advertising(17) }}"
                        img2="{{ \App\Http\Controllers\userController::img_advertising(18) }}"
                        img3="{{ \App\Http\Controllers\userController::img_advertising(19) }}"
                        img4="{{ \App\Http\Controllers\userController::img_advertising(20) }}"
                        src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(17) }}">
                </div>
                <div class="col-12 col-lg-4 text-center">
                    <img width="300px" height="100px" id="img_advertising3" img_active="1"
                        img1="{{ \App\Http\Controllers\userController::img_advertising(21) }}"
                        img2="{{ \App\Http\Controllers\userController::img_advertising(22) }}"
                        img3="{{ \App\Http\Controllers\userController::img_advertising(23) }}"
                        img4="{{ \App\Http\Controllers\userController::img_advertising(24) }}"
                        src="/advertising_img/{{ \App\Http\Controllers\userController::img_advertising(21) }}">
                </div>
            </div>
        </div>
    </footer>

    </div>
    <!-- end wrapper -->


    <!-- Footer -->
    <!-- End Footer -->





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
            $('#btnMenuToggler').click(function() {
                $('#new-navigation').toggleClass('d-none');
            })
            setTimeout(img_advertising1, 2000);


            function img_advertising1() {

                var img_active = $("#img_advertising1").attr("img_active");

                if (img_active == 1) {
                    var img = $("#img_advertising1").attr("img2");
                    $("#img_advertising1").attr("src", "/advertising_img/" + img);
                    $("#img_advertising1").attr("img_active", "2");
                }
                if (img_active == 2) {
                    var img = $("#img_advertising1").attr("img3");
                    $("#img_advertising1").attr("src", "/advertising_img/" + img);
                    $("#img_advertising1").attr("img_active", "3");
                }
                if (img_active == 3) {
                    var img = $("#img_advertising1").attr("img4");
                    $("#img_advertising1").attr("src", "/advertising_img/" + img);
                    $("#img_advertising1").attr("img_active", "4");
                }
                if (img_active == 4) {
                    var img = $("#img_advertising1").attr("img1");
                    $("#img_advertising1").attr("src", "/advertising_img/" + img);
                    $("#img_advertising1").attr("img_active", "1");
                }
                setTimeout(img_advertising1, 2000);
            }

            setTimeout(img_advertising2, 2000);


            function img_advertising2() {

                var img_active = $("#img_advertising2").attr("img_active");

                if (img_active == 1) {
                    var img = $("#img_advertising2").attr("img2");
                    $("#img_advertising2").attr("src", "/advertising_img/" + img);
                    $("#img_advertising2").attr("img_active", "2");
                }
                if (img_active == 2) {
                    var img = $("#img_advertising2").attr("img3");
                    $("#img_advertising2").attr("src", "/advertising_img/" + img);
                    $("#img_advertising2").attr("img_active", "3");
                }
                if (img_active == 3) {
                    var img = $("#img_advertising2").attr("img4");
                    $("#img_advertising2").attr("src", "/advertising_img/" + img);
                    $("#img_advertising2").attr("img_active", "4");
                }
                if (img_active == 4) {
                    var img = $("#img_advertising2").attr("img1");
                    $("#img_advertising2").attr("src", "/advertising_img/" + img);
                    $("#img_advertising2").attr("img_active", "1");
                }
                setTimeout(img_advertising2, 2000);
            }

            setTimeout(img_advertising3, 2000);


            function img_advertising3() {

                var img_active = $("#img_advertising3").attr("img_active");

                if (img_active == 1) {
                    var img = $("#img_advertising3").attr("img2");
                    $("#img_advertising3").attr("src", "/advertising_img/" + img);
                    $("#img_advertising3").attr("img_active", "2");
                }
                if (img_active == 2) {
                    var img = $("#img_advertising3").attr("img3");
                    $("#img_advertising3").attr("src", "/advertising_img/" + img);
                    $("#img_advertising3").attr("img_active", "3");
                }
                if (img_active == 3) {
                    var img = $("#img_advertising3").attr("img4");
                    $("#img_advertising3").attr("src", "/advertising_img/" + img);
                    $("#img_advertising3").attr("img_active", "4");
                }
                if (img_active == 4) {
                    var img = $("#img_advertising3").attr("img1");
                    $("#img_advertising3").attr("src", "/advertising_img/" + img);
                    $("#img_advertising3").attr("img_active", "1");
                }
                setTimeout(img_advertising3, 2000);
            }


            // Default Datatable
            $('.datatable').DataTable();

            //Buttons examples
            var table = $('.datatable-buttons').DataTable();


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

            $(".select2").select2();

            $(".select2-limiting").select2({
                maximumSelectionLength: 2
            });
            $('textarea#textarea').maxlength({
                alwaysShow: true,
                warningClass: "badge badge-success",
                limitReachedClass: "badge badge-danger"
            });


            $('form[not="menu-with-form"]').each(function() {
                $(this).parsley();
            })
        });
    </script>
@if(session('id_user'))
@include('user.partials.note-modal')
@endif
</body>

</html>