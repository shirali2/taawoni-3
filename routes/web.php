<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




// TEST ROUTES - حذف کن بعد از تست
Route::get('/db-check', function () {
    $db = DB::connection()->getDatabaseName();
    return response()->json(['database' => $db, 'host' => request()->getHost()]);
});

Route::get('/debug-tables', function () {
    $db = DB::connection()->getDatabaseName();
    $tables = DB::select('SHOW TABLES');
    $tableNames = array_map(function ($t) {
        return array_values((array) $t)[0];
    }, $tables);
    $required = ['users', 'user_grops', 'usergrops', 'grops', 'forms', 'user_forms', 'menus', 'unders', 'products', 'settings'];
    $check = [];
    foreach ($required as $t) {
        $check[$t] = in_array($t, $tableNames) ? '✓' : '✗ MISSING';
    }
    return response()->json(['database' => $db, 'tables' => $check]);
});

Route::get('/', "homeController@page");
Route::get('/admin', "homeController@admin");
Route::post('/admin', "homeController@checkLoginAdmin");
Route::get('/register', "homeController@registerPage");
Route::get('/logout', "homeController@logout");
Route::post('/', "homeController@login");
Route::post('/check/login', "homeController@checkLogin");
Route::post('/check/register', "homeController@checkRegister");
Route::post('/check/register/code', "homeController@checkRegisterCode");
Route::post('/register', "homeController@register");
Route::post('/register/code', "homeController@registerLogin");
Route::post('/login/code/pass', "homeController@loginPassCode");
Route::post('/login/code/pass/form', "homeController@loginPassCodeForm");
Route::post('/login/pass/form', "homeController@loginPassForm");
Route::post('/login/code/form', "homeController@loginCodeForm");
Route::post('/login/pass', "homeController@loginPass");
Route::post('/login/code', "homeController@loginCode");
Route::post('/contactu/add', "homeController@contactu");

Route::get('/login/forgotPassword', "homeController@forgotPassword");


Route::prefix('user')->middleware('check.required.forms')->group(function () {
     Route::get('/', "userController@home");
     Route::get('/WaitingUserFineCalc', "userController@WaitingUserFineCalc");
     Route::get('/full_fine_calc', "userController@full_fine_calc"); // محاسبه جریمه تمامی صورتحساب های کاربر
     Route::get('/grop', "userController@grop");
     Route::post('/grop', "userController@gropAdd");

     Route::get('/menu/{id}', "userController@menu");

     Route::get('/menu/{id}/{id_under}', "userController@menuUnder");

     //فرم
     Route::get('/form/check/{id}', "userController@formCheck"); //بررسی
     Route::post('/form/{hash}', "userController@formSave"); //ثبت

     Route::get('/ticket', "userController@ticketPage");

     Route::get('/ticket/add', "userController@ticketAddPage");
     Route::post('/ticket/add', "userController@ticketAdd");

     Route::get('/ticket/message/{id}', "userController@ticketMessagePage");
     Route::post('/ticket/message/{id}', "userController@ticketMessage");



     Route::get('/product', "userController@productPage");
     Route::get('/product/pay/{id}', "userController@productPay");
     Route::get('/product/pay/verify/{id}', "userController@productPayVerify");

     Route::get('/crop', "userController@cropPage");
     Route::get('/crop/pay/{id}', "userController@cropPay");
     Route::get('/crop/pay/verify/{id}', "userController@cropPayVerify");

     Route::get('/advertising/list', "userController@advertisingListPage");
     Route::get('/advertising', "userController@advertisingPage");
     Route::post('/advertising', "userController@advertising");
     Route::get('/advertising/pay/verify/{id}', "userController@advertisingPayVerify");
     Route::post('/advertising/img/{id}', "userController@advertisingImg");

     Route::get('/pm', "userController@pmPage");
     Route::get('/pm/active/{id}', "userController@pmActive");

     //Route::get('/data', "userController@data");

     Route::get('/invoice', "userController@invoicePage");
     Route::get('/invoice/pay/{id}', "userController@invoicePayPage");
     Route::post('/invoice/pay/add/{id}', "userController@invoicePayAdd");
     Route::get('/invoice/pay/add/verify/{id}', "userController@invoicePayAddVerify");
     Route::get('/invoice/all', "userController@invoiceAllPage");
     Route::get('/invoice/pay', "userController@invoiceUserPayPage");


     Route::get('/setting', "userController@settingpage");
     Route::post('/setting', "userController@setting");
     Route::post('/setting/password', "userController@settingPassword");

     Route::get('/notes', 'User\UserNoteController@index')->name('user.notes.index');
     Route::post('/notes/{id}/mark-as-seen', 'User\UserNoteController@markAsSeen')->name('user.notes.mark-seen');
});


/* START فرم خارجی */
Route::get('/external/form/{token}',         'ExternalFormController@showPassword');
Route::post('/external/form/{token}/verify', 'ExternalFormController@verifyPassword');
Route::get('/external/form/{token}/search',  'ExternalFormController@search');
Route::post('/external/form/{token}/submit', 'ExternalFormController@submit');
/* END فرم خارجی */

Route::prefix('admin')->group(function () {
     Route::get('/panel', "adminController@home");


     Route::get('/grop', "adminController@grops");
     Route::get('/grop/add', "adminController@gropAdd");
     Route::post('/grop/add', "adminController@addGrop");

     Route::get('/grop/edit/{id}', "adminController@gropEditPage");
     Route::post('/grop/edit/{id}', "adminController@gropEdit");
     Route::get('/grop/edit/code/{id}', "adminController@gropCodeEdit");
     Route::get('/grop/activePay/{id}', "adminController@gropActivePay");
     Route::delete('/grop/delete/{id}', "adminController@gropDelete");

     Route::get('/grop/manager/{id}', "adminController@manager");
     Route::get('/grop/manager/add/{id}', "adminController@gropManagerAddPage");
     Route::post('/grop/manager/add/{id}', "adminController@gropManagerAdd");
     Route::get('/grop/manager/edit/{id}', "adminController@gropManagerEditPage");
     Route::post('/grop/manager/edit/{id}', "adminController@gropManagerEdit");
     Route::delete('/grop/manager/delete/{id}', "adminController@deleteManager");

     Route::get('/grop/user/{id}', "adminController@gropUser");
     Route::get('/grop/user/add/{id}', "adminController@gropUserAddPage");
     Route::post('/grop/user/add/{id}', "adminController@gropUserAdd");
     Route::get('/grop/user/edit/{id}', "adminController@gropUserEditPage");
     Route::post('/grop/user/edit/{id}', "adminController@gropUserEdit");
     Route::delete('/grop/user/delete/{id}', "adminController@deleteGropUser");
     Route::post('/grop/user/changegroup', "adminController@userChangeGrop");
     Route::post('/grop/user/getusergrop', "adminController@getUserGrop");

     Route::get('/grop/access/{id}', "adminController@access");
     Route::get('/grop/access/accessGropUser/{idGrop}/{idGropUser}', "adminController@accessGropUser");
     Route::delete('/grop/access/accessGropUser/delete/{idManagers}/{idUsergrop}', "adminController@accessGropUserDelete");
     Route::get('/grop/access/page/{idGrop}/{idSetting}', "adminController@accessSetting");
     Route::delete('/grop/access/delete/{idManagers}/{idSetting}', "adminController@accessSettingDelete");


     // add for details
     Route::get('/form/detail/{id}/{idf}', "adminController@showform"); //ویرایش
     Route::get('/form/editdetail/{id}/{idf}', "adminController@editform"); //ویرایش


     Route::get('/menu/add', "adminController@menuAddPage");
     Route::post('/menu/add/gropUser', "adminController@menuGropUser");
     Route::post('/menu/add/gropUsers', "adminController@menuGropUsers");
     Route::post('/menu/add', "adminController@menuAdd");
     Route::get('/menu', "adminController@menuPage");
     Route::get('/menu/edit/{id}', "adminController@menuEditPage");
     Route::post('/menu/edit/{id}', "adminController@menuEdit");
     Route::delete('/menu/delete/{id}', "adminController@menuDelete");

     Route::get('/menu/under/{id}', "adminController@menuUnderPage");
     Route::get('/menu/under/add/{id}', "adminController@menuUnderAddPage");
     Route::post('/menu/under/add/{id}', "adminController@menuUnderAdd");
     Route::get('/menu/under/edit/{id}', "adminController@menuUnderEditPage");
     Route::post('/menu/under/edit/{id}', "adminController@menuUnderEdit");
     Route::delete('/menu/under/delete/{id}', "adminController@underDelete");


     Route::get('/user', "adminController@userPage");
     Route::get('/user/add', "adminController@userAdd");
     Route::post('/user/add', "adminController@user");
     Route::get('/user/edit/{id}', "adminController@userEditPage");
     Route::post('/user/edit/{id}', "adminController@userEdit");
     Route::get('/user/add/xlsx', "adminController@userAddXlsxPage");
     Route::post('/user/add/xlsx', "adminController@userAddXlsx");
     Route::delete('/user/delete/{id}', "adminController@userDelete");
     Route::post('/user/check/registerEdit', "adminController@checkRegisterEdit");
     Route::get('/user/pay/{id}', "adminController@userPayPage");
     Route::get('/user/forgotPassword/{id}', "adminController@userForgotPassword");
     Route::post('/user/inactive/{id}', "adminController@userInactive");
     Route::post('/user/toggle-hidden/{id}', "adminController@userToggleHidden");

     Route::get('/user/data/{id}', "adminController@userDataPage");
     Route::get('/user/data/edit/{id}', "adminController@userDataEditPage");
     Route::post('/user/data/edit/{id}', "adminController@userDataEdit");

     Route::get('/user/invoice/{id}', "adminController@userInvoicePage");

     Route::get('/user/grop/{id}', "adminController@userGropPage");
     Route::delete('/user/grop/delete/{id}', "adminController@userGropDelete");
     Route::get('/user/grop/add/{id}', "adminController@userGropAddPage");
     Route::post('/user/grop/add/{id}', "adminController@userGropAdd");
     Route::get('/user-group', "adminController@getUserGroup");


     /* START فرم */
     Route::get('/form', "adminController@formPage");
     Route::get('/form/add', "adminController@formAddPage"); //ایجاد
     Route::post('/form/add', "adminController@formAdd"); //ذخیره
     Route::get('/form/edit/{id}', "adminController@formEditPage"); //ویرایش
     Route::post('/form/edit/{id}', "adminController@formEdit"); //ذخیره
     Route::delete('/form/delete/{id}', "adminController@formDelete");

     Route::get('/form/extra-fields/get/{id}', "adminController@formFieldsGet"); //دریافت فیلدهای اضافی فرم بر اساس آیدی فرم

     Route::get('/form/user/{id}', "adminController@formUserPage")->where('id', '[0-9]+');
     Route::get('/formNonefill/user/{id}', "adminController@formNoneUserPage")->where('id', '[0-9]+');

     Route::post('/form/user/edit', "adminController@formUserEdit"); //ویرایش
     Route::post('/form/copy/{id}', "adminController@copy_form"); //ویرایش

     Route::get('/form/user/add/xlsx/{id}', "adminController@formUserAddPage");
     Route::post('/form/user/add/xlsx/{id}', "adminController@formUserAdd");
     Route::get('/form/user/add/xlsx/download-example/{id}', "adminController@formUserAddDownloadExcelExample"); //خروجی گرفتن از ستون های فرم برای اکسل نمونه
     Route::delete('/form/user/delete/{id}', "adminController@formUserDelete");
     Route::delete('/form/user/nonefill/delete/{formId}/{userId}', "adminController@formUserNoneFillDelete");
     Route::get('/form/user/delete/group/{id}', "adminController@formUserDeleteGroup"); //حذف گروهی
     /* END فرم */

     /* START لینک خارجی فرم */
     Route::post('/form/{formId}/external-token', 'adminController@createExternalToken');
     Route::post('/external-token/{tokenId}/deactivate', 'adminController@deactivateExternalToken');
     /* END لینک خارجی فرم */


     Route::get('/ticket', "adminController@ticketPage");
     Route::get('/ticket/{id}', "adminController@ticketGropPage");
     Route::get('/ticket/message/{id}', "adminController@ticketMessagePage");
     Route::post('/ticket/message/{id}', "adminController@ticketMessage");
     Route::get('/ticket/active/{id}', "adminController@ticketActive");
     Route::delete('/ticket/message/delete/{id}', "adminController@ticketMessageDelete");
     Route::delete('/ticket/delete/{id}', "adminController@ticketDelete");

     /* Cron Job */
     Route::get('/ticket/all/delete', "adminController@ticketDeleteDay");
     /* end Cron Job */

     Route::get('/issue', "adminController@ticketIssuePage");
     Route::get('/issue/add', "adminController@ticketIssueAdd");
     Route::post('/issue/add', "adminController@ticketIssue");
     Route::delete('/issue/delete/{id}', "adminController@ticketIssueDelete");

     Route::get('/contactu', "adminController@contactuPage");
     Route::delete('/contactu/delete/{id}', "adminController@contactuDelete");

     Route::get('/product', "adminController@productPage");
     Route::get('/product/add', "adminController@productAddPage");
     Route::post('/product/add', "adminController@productAdd");
     Route::get('/product/edit/{id}', "adminController@productEditPage");
     Route::post('/product/edit/{id}', "adminController@productEdit");
     Route::get('/product/status/change/{id}', "adminController@productChangeStatus");
     Route::delete('/product/delete/{id}', "adminController@productDelete");
     Route::get('/product/purchased', "adminController@productsPurchased");
     Route::delete('/product/purchased/delete/{id}', "adminController@productDeletePurchased");

     Route::get('/crop', "adminController@cropPage");
     Route::get('/crop/add', "adminController@cropAddPage");
     Route::post('/crop/add', "adminController@cropAdd");
     Route::get('/crop/edit/{id}', "adminController@cropEditPage");
     Route::post('/crop/edit/{id}', "adminController@cropEdit");
     Route::get('/crop/active/{id}', "adminController@cropActive");
     Route::get('/crop/pay/{id}', "adminController@cropPay");
     Route::delete('/crop/pay/delete/{id}', "adminController@cropPayDelete");



     Route::get('/advertising/product', "adminController@advertisingProductPage");
     Route::get('/advertising/product/edit/{id}', "adminController@advertisingProductEdit");
     Route::post('/advertising/product/edit/{id}', "adminController@advertisingProduct");
     Route::post('/advertising/product/img/{id}', "adminController@advertisingProductImg");

     Route::get('/advertising', "adminController@advertisingPage");
     Route::get('/advertising/img/{id}', "adminController@advertisingImg");
     Route::get('/advertising/active/{id}', "adminController@advertisingActive");
     Route::delete('/advertising/delete/{id}', "adminController@AdvertisingDelete");
     /* Cron Job */
     Route::get('/advertising/cronJob/end', "adminController@advertisingCronJobEnd");
     /* end Cron Job */

     Route::get('/setting', "adminController@settingPage");
     Route::post('/setting', "adminController@setting");



     Route::get('/pm', "adminController@pmPage");
     Route::get('/pm/show/{id}', "adminController@pmShowPage")->name('pm.show');
     Route::get('/pm/dontshow/{id}', "adminController@pmNotShow")->name('pm.dontshow');

     Route::get('/pm/edit/{id}', "adminController@pmEditPage");
     Route::get('/pm/add', "adminController@pmAddPage");
     Route::post('/pm/add', "adminController@pmAdd");
     Route::post('/pm/edit/{id}', "adminController@pmEdit");
     Route::post('/pm/add/user', "adminController@pmAddUser");
     Route::delete('/pm/delete/{id}', "adminController@pmDelete");
     Route::delete('/pm/show/delete/{id}', "adminController@pmShowDelete");
     // Task 4: register PM as note after admin approval
     Route::post('/pm/{id}/register-note', 'adminController@pmRegisterAsNote')->name('pm.register-note');


     Route::get('/invoice', "adminController@invoicePage");
     Route::get('/invoice/list/{id}', "adminController@invoiceGropPage");
     Route::get('/invoice/add', "adminController@invoiceAddPage");
     Route::post('/invoice/add', "adminController@invoiceAdd");
     Route::get('/invoice/edit/{id}', "adminController@invoiceEditPage");
     Route::post('/invoice/edit/{id}', "adminController@invoiceEdit");
     Route::get('/invoice/user/{id}', "adminController@invoiceUserPage");
     Route::get('/invoice/user/{id}/{id_user}', "adminController@invoiceUser");
     Route::get('/invoice/user/delete/{id_invoice}/{id_user}', "AdminController@invoiceUserPaysFinesDelete")->name('admin.invoiceUserPaysFinesDelete'); // حذف کاربر از یک فاکتور به همراه تمامی پرداخت ها و جرائم اون
     Route::get('/invoice/user/remaining_commitment/calc_daily_fine/{id_invoice}/{id_user}', 'managerController@calc_remaining_commitment_fine');
     Route::post('/invoice/pay/add/{id}/{id_user}', "adminController@invoiceUserPay"); //ثبت فیش
     Route::delete('/invoice/pay/delete/{id}', "adminController@invoicePayDelete");
     Route::delete('/invoice/delete/{id}', "adminController@invoiceDelete");
     Route::get('/invoice/xls/page', "adminController@invoicexlspage");

     ////////cornjab//////////
     Route::get('/penalty/day', "adminController@day_penalty");

     Route::prefix('/notes')->group(function () {
         Route::get('/',             'Admin\NoteController@index')->name('admin.notes.index');
         Route::get('/create',       'Admin\NoteController@create')->name('admin.notes.create');
         Route::post('/store',       'Admin\NoteController@store')->name('admin.notes.store');
         Route::get('/user-info',    'Admin\NoteController@userInfo')->name('admin.notes.user-info');
         Route::post('/{id}/approve',   'Admin\NoteController@approve')->name('admin.notes.approve');
         Route::post('/approve-all',    'Admin\NoteController@approveAll')->name('admin.notes.approve-all');
         Route::post('/{id}/archive',    'Admin\NoteController@toggleArchive')->name('admin.notes.archive');
         Route::post('/{id}/visibility', 'Admin\NoteController@toggleVisibility')->name('admin.notes.visibility');
         Route::get('/{id}/edit',        'Admin\NoteController@edit')->name('admin.notes.edit');
         Route::post('/{id}/update',     'Admin\NoteController@update')->name('admin.notes.update');
         Route::delete('/{id}',          'Admin\NoteController@destroy')->name('admin.notes.destroy');
         Route::get('/export/excel',     'Admin\NoteController@exportExcel')->name('admin.notes.export-excel');
         Route::get('/export/pdf',       'Admin\NoteController@exportPdf')->name('admin.notes.export-pdf');
         Route::get('/import',           'Admin\NoteController@importForm')->name('admin.notes.import-form');
         Route::post('/import',          'Admin\NoteController@importExcel')->name('admin.notes.import');
         // Task 6: bulk operations
         Route::post('/bulk-approve',  'Admin\NoteController@bulkApprove')->name('admin.notes.bulk-approve');
         Route::delete('/bulk-destroy','Admin\NoteController@bulkDestroy')->name('admin.notes.bulk-destroy');
         // Task 7: per-user report
         Route::get('/user-report/{userId}',     'Admin\NoteController@userReport')->name('admin.notes.user-report');
         Route::get('/user-report/{userId}/pdf', 'Admin\NoteController@userReportPdf')->name('admin.notes.user-report-pdf');
     });

     Route::get('/wallets/{managerId}',            'adminController@walletShow');
     Route::post('/wallets/{managerId}/credit',    'adminController@walletCredit');
     Route::post('/wallets/{managerId}/threshold', 'adminController@walletSetThreshold');

     Route::prefix('/sms')->group(function () {
         Route::get('/',                                  'Admin\SmsCampaignController@index')->name('admin.sms.index');
         Route::get('/create',                            'Admin\SmsCampaignController@create')->name('admin.sms.create');
         Route::post('/store',                            'Admin\SmsCampaignController@store')->name('admin.sms.store');
         Route::post('/draft-save',                       'Admin\SmsCampaignController@draftSave')->name('admin.sms.draft-save');
         Route::get('/settings',                          'Admin\SmsCampaignController@smsSettings')->name('admin.sms.settings');
         Route::post('/settings',                         'Admin\SmsCampaignController@smsSettingsSave')->name('admin.sms.settings.save');
         Route::get('/{id}/recipients',                   'Admin\SmsCampaignController@recipients')->name('admin.sms.recipients');
         Route::get('/{id}/export-excel',                 'Admin\SmsCampaignController@exportExcel')->name('admin.sms.export-excel');
         Route::get('/{id}/export-pdf',                   'Admin\SmsCampaignController@exportPdf')->name('admin.sms.export-pdf');
         Route::post('/{id}/logs/bulk-delete',            'Admin\SmsCampaignController@bulkDeleteLogs')->name('admin.sms.logs.bulk-delete');
         Route::delete('/logs/{logId}',                   'Admin\SmsCampaignController@deleteLog')->name('admin.sms.logs.delete');
         Route::delete('/{id}',                           'Admin\SmsCampaignController@destroy')->name('admin.sms.destroy');
     });
});


Route::prefix('manager')->group(function () {
     Route::get('/panel', "managerController@home");


     Route::get('/grop', "managerController@grops");

     Route::get('/grop/add', "managerController@gropAddPage");
     Route::post('/grop/add', "managerController@addGrop");

     Route::get('/grop/edit/{id}', "managerController@gropEditPage");
     Route::post('/grop/edit/{id}', "managerController@gropEdit");
     Route::get('/grop/edit/code/{id}', "managerController@gropCodeEdit");

     Route::delete('/grop/delete/{id}', "managerController@gropDelete");

     Route::get('/grop/manager/{id}', "managerController@manager");
     Route::get('/grop/manager/add/{id}', "managerController@gropManagerAddPage");
     Route::post('/grop/manager/add/{id}', "managerController@gropManagerAdd");
     Route::get('/grop/manager/edit/{id}', "managerController@gropManagerEditPage");
     Route::post('/grop/manager/edit/{id}', "managerController@gropManagerEdit");
     Route::delete('/grop/manager/delete/{id}', "managerController@deleteManager");

     Route::get('/grop/user/{id}', "managerController@gropUser");
     Route::get('/grop/user/add/{id}', "managerController@gropUserAddPage");
     Route::post('/grop/user/add/{id}', "managerController@gropUserAdd");
     Route::get('/grop/user/edit/{id}', "managerController@gropUserEditPage");
     Route::post('/grop/user/edit/{id}', "managerController@gropUserEdit");
     Route::delete('/grop/user/delete/{id}', "managerController@deleteGropUser");

     Route::get('/grop/access/{id}', "managerController@access");
     Route::get('/grop/access/accessGropUser/{idGrop}/{idGropUser}', "managerController@accessGropUser");
     Route::delete('/grop/access/accessGropUser/delete/{idManagers}/{idUsergrop}', "managerController@accessGropUserDelete");
     Route::get('/grop/access/page/{idGrop}/{idSetting}', "managerController@accessSetting");
     Route::delete('/grop/access/delete/{idManagers}/{idSetting}', "managerController@accessSettingDelete");
     Route::post('/grop/user/changegroup', "managerController@userChangeGrop");
     Route::post('/grop/user/getusergrop', "managerController@getUserGrop");


     Route::get('/menu/add', "managerController@menuAddPage");
     Route::post('/menu/add/gropUser', "managerController@menuGropUser");
     Route::post('/menu/add', "managerController@menuAdd");
     Route::get('/menu', "managerController@menuPage");
     Route::get('/menu/edit/{id}', "managerController@menuEditPage");
     Route::post('/menu/edit/{id}', "managerController@menuEdit");
     Route::delete('/menu/delete/{id}', "managerController@menuDelete");

     Route::get('/menu/under/{id}', "managerController@menuUnderPage");
     Route::get('/menu/under/add/{id}', "managerController@menuUnderAddPage");
     Route::post('/menu/under/add/{id}', "managerController@menuUnderAdd");
     Route::get('/menu/under/edit/{id}', "managerController@menuUnderEditPage");
     Route::post('/menu/under/edit/{id}', "managerController@menuUnderEdit");
     Route::delete('/menu/under/delete/{id}', "managerController@underDelete");



     Route::get('/user', "managerController@userPage");
     Route::get('/user/add', "managerController@userAdd");
     Route::post('/user/add', "managerController@user");
     Route::get('/user/edit/{id}', "managerController@userEditPage");
     Route::post('/user/edit/{id}', "managerController@userEdit");
     Route::get('/user/add/xlsx', "managerController@userAddXlsxPage");
     Route::post('/user/add/xlsx', "managerController@userAddXlsx");
     Route::post('/user/check/registerEdit', "managerController@checkRegisterEdit");
     //     Route::get('/user/pay/{id}', "managerController@userPayPage");
     Route::delete('/user/delete/{id}', "managerController@userDelete");
     Route::get('/user/forgotPassword/{id}', "managerController@userForgotPassword");
     Route::post('/user/changePassword/{id}', "managerController@userChangePassword");
     Route::post('/user/inactive/{id}', "managerController@userInactive");
     Route::post('/user/toggle-hidden/{id}', "managerController@userToggleHidden");

     Route::get('/user/dataAll', "managerController@userDataAllPage");
     Route::get('/user/data/{id}', "managerController@userDataPage");
     Route::get('/user/data/edit/{id}', "managerController@userDataEditPage");
     Route::post('/user/data/edit/{id}', "managerController@userDataEdit");

     Route::get('/user/grop/{id}', "managerController@userGropPage");
     Route::delete('/user/grop/delete/{id}', "managerController@userGropDelete");
     Route::get('/user/grop/add/{id}', "managerController@userGropAddPage");
     Route::post('/user/grop/add/{id}', "managerController@userGropAdd");


     /* START فرم */
     Route::get('/form', "managerController@formPage");
     Route::get('/form/add', "managerController@formAddPage"); //ایجاد
     Route::post('/form/add', "managerController@formAdd"); //ذخیره
     Route::get('/form/edit/{id}', "managerController@formEditPage"); //ویرایش
     Route::post('/form/edit/{id}', "managerController@formEdit"); //ذخیره
     Route::delete('/form/delete/{id}', "managerController@formDelete");

     Route::post('/form/copy/{id}', "managerController@copy_form"); // کپی فرم
     Route::get('/form/extra-fields/get/{id}', "managerController@formFieldsGet"); //دریافت فیلدهای اضافی فرم بر اساس آیدی فرم

     Route::get('/form/user/{id}', "managerController@formUserPage")->where('id', '[0-9]+');
     Route::get('/formNonefill/user/{id}', "managerController@formNoneUserPage")->where('id', '[0-9]+');


     Route::post('/form/user/edit', "managerController@formUserEdit"); //ویرایش
     Route::get('/form/user/add/xlsx/{id}', "managerController@formUserAddPage");
     Route::post('/form/user/add/xlsx/{id}', "managerController@formUserAdd");
     Route::get('/form/user/add/xlsx/download-example/{id}', "managerController@formUserAddDownloadExcelExample"); //خروجی گرفتن از ستون های فرم برای اکسل نمونه
     Route::delete('/form/user/delete/{id}', "managerController@formUserDelete");
     Route::delete('/form/user/nonefill/delete/{formId}/{userId}', "managerController@formUserNoneFillDelete");
     Route::get('/form/user/delete/group/{id}', "managerController@formUserDeleteGroup"); //حذف گروهی
     /* END فرم */
     Route::get('/form/detail/{id}/{idf}', "managerController@showform"); //ویرایش
     Route::get('/form/editdetail/{id}/{idf}', "managerController@editform"); //ویرایش


     Route::get('/ticket', "managerController@ticketPage");
     Route::get('/ticket/message/{id}', "managerController@ticketMessagePage");
     Route::post('/ticket/message/{id}', "managerController@ticketMessage");
     Route::get('/ticket/active/{id}', "managerController@ticketActive");
     Route::delete('/ticket/message/delete/{id}', "managerController@ticketMessageDelete");
     Route::delete('/ticket/delete/{id}', "managerController@ticketDelete");



     Route::get('/issue', "managerController@ticketIssuePage");
     Route::get('/issue/add', "managerController@ticketIssueAdd");
     Route::post('/issue/add', "managerController@ticketIssue");
     Route::delete('/issue/delete/{id}', "managerController@ticketIssueDelete");

     Route::get('/contactu', "managerController@contactuPage");
     Route::delete('/contactu/delete/{id}', "managerController@contactuDelete");


     //     Route::get('/product', "managerController@productPage");
     //     Route::get('/product/add', "managerController@productAddPage");
     //     Route::post('/product/add', "managerController@productAdd");
     //     Route::get('/product/edit/{id}', "managerController@productEditPage");
     //     Route::post('/product/edit/{id}', "managerController@productEdit");
     //     Route::get('/product/active/{id}', "managerController@productActive");



     Route::get('/advertising/product', "managerController@advertisingProductPage");
     Route::get('/advertising/product/edit/{id}', "managerController@advertisingProductEdit");
     Route::post('/advertising/product/edit/{id}', "managerController@advertisingProduct");
     Route::post('/advertising/product/img/{id}', "managerController@advertisingProductImg");

     Route::get('/advertising', "managerController@advertisingPage");
     Route::get('/advertising/img/{id}', "managerController@advertisingImg");
     Route::get('/advertising/active/{id}', "managerController@advertisingActive");


     //     Route::get('/setting', "managerController@settingPage");
     //     Route::post('/setting', "managerController@setting");





     Route::get('/pm', "managerController@pmPage");
     Route::get('/pm/show/{id}', "managerController@pmShowPage");
     Route::get('/pm/edit/{id}', "managerController@pmEditPage");
     Route::get('/pm/add', "managerController@pmAddPage");
     Route::post('/pm/add', "managerController@pmAdd");
     Route::post('/pm/edit/{id}', "managerController@pmEdit");
     Route::post('/pm/add/user', "managerController@pmAddUser");
     Route::delete('/pm/delete/{id}', "managerController@pmDelete");
     Route::delete('/pm/show/delete/{id}', "managerController@pmShowDelete");

     Route::get('/invoice', "managerController@invoicePage");
     Route::get('/invoice/user/all', "managerController@invoiceUserAllUserGrops");
     Route::get('/invoice/user/all/{id}', "managerController@invoiceUserAllList");
     Route::get('/invoice/unconfirmed-receipts', "managerController@invoiceUnconfirmedReceipts");
     //صورتحساب تکی کاربر
     Route::get('/invoice/user/single', "managerController@invoiceUserSingle");
     Route::get('/invoice/user/single/{category}/{id}', "managerController@invoiceUserSingleInfo")->where('id', '[0-9]+'); //نمایش بر اساس دسته انتخاب شده
     Route::get('/invoice/add', "managerController@invoiceAddPage");
     Route::post('/invoice/add', "managerController@invoiceAdd");
     Route::get('/invoice/edit/{id}', "managerController@invoiceEditPage");
     Route::post('/invoice/edit/{id}', "managerController@invoiceEdit");
     Route::get('/invoice/user/{id}', "managerController@invoiceUserPage");
     Route::get('/invoice/user/{id}/{id_user}', "managerController@invoiceUser");
     Route::get('/invoice/user/delete/{id_invoice}/{id_user}', "managerController@invoiceUserPaysFinesDelete")->name('invoiceUserPaysFinesDelete'); // حذف کاربر از یک فاکتور به همراه تمامی پرداخت ها و جرائم اون
     Route::get('/invoice/user/remaining_commitment/calc_daily_fine/{id_invoice}/{id_user}', 'managerController@calc_remaining_commitment_fine');
     Route::post('/invoice/pay/add/{id}/{id_user}', "managerController@invoiceUserPay"); //ثبت فیش
     Route::get('/invoice/bulk-pay/{id_user}', "managerController@invoiceBulkPayPage"); //واریزی انبوه
     Route::post('/invoice/bulk-pay/{id_user}', "managerController@invoiceBulkPay"); //ثبت واریزی انبوه
     Route::post('/invoice/pay/active/{string}', "managerController@invoicePayActive"); //تغییر وضعیت فعال/غیرفعال به بالعکس
     Route::delete('/invoice/pay/delete/{id}', "managerController@invoicePayDelete");
     Route::delete('/invoice/delete/{id}', "managerController@invoiceDelete");
     Route::delete('/invoice/invoiceUserDelete/{id_invoice}/{id_user}', "managerController@invoiceUserDelete");

     Route::get('/user/invoice/{id}', "managerController@userInvoicePage");

     Route::get('/crop', "managerController@cropPage");
     Route::get('/crop/add', "managerController@cropAddPage");
     Route::post('/crop/add', "managerController@cropAdd");
     Route::get('/crop/edit/{id}', "managerController@cropEditPage");
     Route::post('/crop/edit/{id}', "managerController@cropEdit");
     Route::get('/crop/active/{id}', "managerController@cropActive");
     Route::get('/crop/pay/{id}', "managerController@cropPay");
     Route::delete('/crop/pay/delete/{id}', "managerController@cropPayDelete");

     Route::prefix('/notes')->group(function () {
         Route::get('/',          'Manager\NoteController@index')->name('manager.notes.index');
         Route::get('/create',    'Manager\NoteController@create')->name('manager.notes.create');
         Route::post('/store',    'Manager\NoteController@store')->name('manager.notes.store');
         Route::get('/{id}/edit', 'Manager\NoteController@edit')->name('manager.notes.edit');
         Route::post('/{id}/update', 'Manager\NoteController@update')->name('manager.notes.update');
         Route::get('/user-info', 'Manager\NoteController@userInfo')->name('manager.notes.user-info');
         Route::post('/{id}/approve',      'Manager\NoteController@approve')->name('manager.notes.approve');
         Route::post('/{id}/visibility',   'Manager\NoteController@toggleVisibility')->name('manager.notes.visibility');
         Route::post('/bulk-approve',        'Manager\NoteController@bulkApprove')->name('manager.notes.bulk-approve');
         Route::delete('/bulk-destroy',      'Manager\NoteController@bulkDestroy')->name('manager.notes.bulk-destroy');
         Route::post('/{id}/archive',        'Manager\NoteController@toggleArchive')->name('manager.notes.archive');
         Route::post('/{id}/reset-viewed',   'Manager\NoteController@resetViewed')->name('manager.notes.reset-viewed');
         Route::delete('/{id}',              'Manager\NoteController@destroy')->name('manager.notes.destroy');
     });
});

Route::get('/test', "testController@test");
Route::get('/test/pay/{id}', "testController@pay");


// Route::prefix('user')->group(function () {
//     Route::get('scoreboard',"userController@scoreboard");
//     Route::get('scoreboard/{id}',"userController@scoreboardUser");


//     Route::get('addIdea',"userController@pageAddIdea");
//     Route::post('addIdea/key',"userController@addIdeaKey");
//     Route::post('addIdea',"userController@addIdea");

//     Route::get('proces',"userController@proces");
//     Route::get('idea/view/{id}',"userController@viewIdea");
//     Route::patch('idea/rate/{id}',"userController@addRate");
//     Route::patch('proces/idea/activity/{id}',"userController@ideaActivity");

//     Route::get('idea/edit/{id}',"userController@pageEditIdea");
//     Route::post('idea/edit/{id}',"userController@editIdea");

//     Route::get('waiting',"userController@pageWaiting");
//     Route::get('waiting/idea/view/{id}',"userController@viewIdeaWaiting");
//     Route::patch('waiting/idea/activity/{id}',"userController@waitingIdeaActivity");
//     Route::patch('waiting/idea/end/{id}',"userController@waitingIdeaEnd");


//     Route::get('/ideas',"userController@pageIdeas");


// });
