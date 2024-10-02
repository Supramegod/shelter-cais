<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyFastApiKey;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Fitur\ContactController;

use App\Http\Controllers\Sales\LeadsController;
use App\Http\Controllers\Sales\CustomerActivityController;
use App\Http\Controllers\Sales\QuotationController;

use App\Http\Controllers\Master\PlatformController;


Route::controller(AuthController::class)->group(function() {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/', 'dashboard')->name('home');
    Route::get('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

//form luar
Route::controller(ContactController::class)->group(function() {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact-save', 'contactSave')->name('contact.save');
    Route::post('/webhook-endpoint/{key}', 'handleWebhook')->name('webhook-endpoint');
});

Route::group(['middleware' => ['verify_leads_api']], function () {
    Route::controller(ContactController::class)->group(function() {
        Route::post('/api/contact-save', 'apiContactSave')->name('api.contact.save');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::controller(LeadsController::class)->group(function() {
        Route::get('/sales/leads', 'index')->name('leads');
        Route::get('/sales/leads/add', 'add')->name('leads.add');
        Route::get('/sales/leads/view/{id}', 'view')->name('leads.view');
        Route::get('/sales/leads/import', 'import')->name('leads.import');
        Route::get('/sales/leads/template-import', 'templateImport')->name('leads.template-import');

        Route::post('/sales/leads/inquiry-import', 'inquiryImport')->name('leads.inquiry-import');
        Route::post('/sales/leads/save-import', 'saveImport')->name('leads.save-import');
        Route::post('/sales/leads/save', 'save')->name('leads.save');
        Route::post('/sales/leads/delete', 'delete')->name('leads.delete');

        Route::get('/sales/leads/export-excel', 'exportExcel')->name('leads.export-excel');

        Route::get('/sales/leads/list', 'list')->name('leads.list'); // ajax
        Route::get('/sales/leads/leads.available-leads', 'availableLeads')->name('leads.available-leads'); // ajax

    });

    Route::controller(CustomerActivityController::class)->group(function() {
        Route::get('/sales/customer-activity', 'index')->name('customer-activity');
        Route::get('/sales/customer-activity/add', 'add')->name('customer-activity.add');
        Route::get('/sales/customer-activity/view/{id}', 'view')->name('customer-activity.view');

        Route::post('/sales/customer-activity/save', 'save')->name('customer-activity.save');
        Route::post('/sales/customer-activity/delete', 'delete')->name('customer-activity.delete');

        Route::get('/sales/customer-activity/list', 'list')->name('customer-activity.list'); // ajax
        Route::get('/sales/customer-activity/member-tim-sales', 'memberTimSales')->name('customer-activity.member-tim-sales'); // ajax
    });

    Route::controller(QuotationController::class)->group(function() {
        Route::get('/sales/quotation', 'index')->name('quotation');
        Route::get('/sales/quotation/add', 'add')->name('quotation.add');
        Route::get('/sales/quotation/step/{id}', 'step')->name('quotation.step');
        Route::post('/sales/quotation/save-step', 'saveStep')->name('quotation.save-step');

        //page quotation
        
        // Route::get('/sales/quotation/edit-1/{id}', 'edit1')->name('quotation.edit-1');
        // Route::get('/sales/quotation/edit-2/{id}', 'edit2')->name('quotation.edit-2');
        // Route::get('/sales/quotation/edit-3/{id}', 'edit3')->name('quotation.edit-3');
        // Route::get('/sales/quotation/edit-4/{id}', 'edit4')->name('quotation.edit-4');
        // Route::get('/sales/quotation/edit-5/{id}', 'edit5')->name('quotation.edit-5');
        // Route::get('/sales/quotation/edit-6/{id}', 'edit6')->name('quotation.edit-6');
        // Route::get('/sales/quotation/edit-7/{id}', 'edit7')->name('quotation.edit-7');
        // Route::get('/sales/quotation/edit-8/{id}', 'edit8')->name('quotation.edit-8');

        Route::get('/sales/quotation/view/{id}', 'view')->name('quotation.view');

        Route::post('/sales/quotation/save', 'save')->name('quotation.save');
        Route::post('/sales/quotation/saveEdit1', 'saveEdit1')->name('quotation.save-edit-1');
        Route::post('/sales/quotation/saveEdit2', 'saveEdit2')->name('quotation.save-edit-2');
        Route::post('/sales/quotation/saveEdit3', 'saveEdit3')->name('quotation.save-edit-3');
        Route::post('/sales/quotation/saveEdit4', 'saveEdit4')->name('quotation.save-edit-4');
        Route::post('/sales/quotation/saveEdit5', 'saveEdit5')->name('quotation.save-edit-5');
        Route::post('/sales/quotation/saveEdit6', 'saveEdit6')->name('quotation.save-edit-6');
        Route::post('/sales/quotation/saveEdit7', 'saveEdit7')->name('quotation.save-edit-7');
        Route::post('/sales/quotation/saveEdit8', 'saveEdit8')->name('quotation.save-edit-8');
        Route::post('/sales/quotation/saveEdit9', 'saveEdit9')->name('quotation.save-edit-9');
        Route::post('/sales/quotation/saveEdit10', 'saveEdit10')->name('quotation.save-edit-10');
        Route::post('/sales/quotation/saveEdit11', 'saveEdit11')->name('quotation.save-edit-11');
        Route::post('/sales/quotation/saveEdit12', 'saveEdit12')->name('quotation.save-edit-12');

        Route::post('/sales/quotation/delete', 'delete')->name('quotation.delete');

        Route::get('/sales/quotation/list', 'list')->name('quotation.list'); // ajax
        Route::post('/sales/quotation/add-detail-hc', 'addDetailHC')->name('quotation.add-detail-hc');
        Route::get('/sales/quotation/list-detail-hc', 'listDetailHC')->name('quotation.list-detail-hc'); // ajax
        Route::post('/sales/quotation/delete-detail-hc', 'deleteDetailHC')->name('quotation.delete-detail-hc');
        Route::get('/sales/quotation/change-kota', 'changeKota')->name('quotation.change-kota'); // ajax
        Route::get('/sales/quotation/list-quotation-kerjasama', 'listQuotationKerjasama')->name('quotation.list-quotation-kerjasama'); // ajax
        Route::post('/sales/quotation/add-quotation-kerjasama', 'addQuotationKerjasama')->name('quotation.add-quotation-kerjasama');
        Route::post('/sales/quotation/delete-quotation-kerjasama', 'deleteQuotationKerjasama')->name('quotation.delete-quotation-kerjasama');
        Route::post('/sales/quotation/delete-quotation', 'deleteQuotation')->name('quotation.delete-quotation');
        Route::post('/sales/quotation/approve-quotation', 'approveQuotation')->name('quotation.approve-quotation');

    });


    Route::controller(PlatformController::class)->group(function() {
        Route::get('/master/platform', 'index')->name('platform');
        Route::get('/master/platform/add', 'add')->name('platform.add');
        Route::get('/master/platform/view/{id}', 'view')->name('platform.view');

        Route::post('/master/platform/save', 'save')->name('platform.save');
        Route::post('/master/platform/delete', 'delete')->name('platform.delete');

        Route::get('/master/platform/list', 'list')->name('platform.list'); // ajax

    });
});