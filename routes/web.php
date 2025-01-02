<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyFastApiKey;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\Fitur\ContactController;

use App\Http\Controllers\Sales\LeadsController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\CustomerActivityController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\SpkController;
use App\Http\Controllers\Sales\PksController;
use App\Http\Controllers\Sales\MonitoringKontrakController;

use App\Http\Controllers\Master\PlatformController;
use App\Http\Controllers\Master\AplikasiPendukungController;
use App\Http\Controllers\Master\JenisBarangController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\JenisPerusahaanController;
use App\Http\Controllers\Master\ManagementFeeController;
use App\Http\Controllers\Master\SalaryRuleController;
use App\Http\Controllers\Master\StatusLeadsController;
use App\Http\Controllers\Master\TunjanganController;
use App\Http\Controllers\Master\TunjanganJabatanController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\KebutuhanController;
use App\Http\Controllers\Master\TimSalesController;
use App\Http\Controllers\Master\TrainingController;
use App\Http\Controllers\Master\UmpController;
use App\Http\Controllers\Master\UmkController;


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

Route::controller(QuotationController::class)->group(function() {
    Route::get('/view/checklist/{id}/{key}', 'viewChecklist')->name('quotation.view-checklist');
});

Route::group(['middleware' => ['verify_leads_api']], function () {
    Route::controller(ContactController::class)->group(function() {
        Route::post('/api/contact-save', 'apiContactSave')->name('api.contact.save');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::controller(DashboardController::class)->group(function() {
        Route::get('/dashboard/approval', 'dashboardApproval')->name('dashboard-approval');
        Route::get('/dashboard/aktifitas-sales', 'dashboardAktifitasSales')->name('dashboard-aktifitas-sales');
        Route::get('/dashboard/leads', 'dashboardLeads')->name('dashboard-leads');


        // list
        Route::get('/dashboard/approval/list', 'getListDashboardApprovalData')->name('dashboard-approval.list');
        Route::get('/dashboard/aktifkan/list', 'getListDashboardAktifkanData')->name('dashboard-aktifkan.list');
    });

    Route::controller(LeadsController::class)->group(function() {
        Route::get('/sales/leads', 'index')->name('leads');
        Route::get('/sales/leads/index-terhapus', 'indexTerhapus')->name('leads.index-terhapus');
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
        Route::get('/sales/leads/list-terhapus', 'listTerhapus')->name('leads.list-terhapus'); // ajax
        Route::get('/sales/leads/leads-available-leads', 'availableLeads')->name('leads.available-leads'); // ajax
        Route::get('/sales/leads/leads-available-quotation', 'availableQuotation')->name('leads.available-quotation'); // ajax

        Route::get('/sales/leads/child-leads', 'childLeads')->name('leads.child-leads'); // ajax
        Route::post('/sales/leads/save-leads', 'saveChildLeads')->name('leads.save-leads'); // ajax
    });

    Route::controller(CustomerController::class)->group(function() {
        Route::get('/sales/customer', 'index')->name('customer');
        Route::get('/sales/customer/view/{id}', 'view')->name('customer.view');

        Route::get('/sales/customer/list', 'list')->name('customer.list'); // ajax
        Route::get('/sales/customer/available-customer', 'availableCustomer')->name('customer.available-customer'); // ajax

    });

    Route::controller(CustomerActivityController::class)->group(function() {
        Route::get('/sales/customer-activity', 'index')->name('customer-activity');
        Route::get('/sales/customer-activity/add', 'add')->name('customer-activity.add');
        Route::get('/sales/customer-activity/view/{id}', 'view')->name('customer-activity.view');

        Route::post('/sales/customer-activity/save', 'save')->name('customer-activity.save');
        Route::post('/sales/customer-activity/delete', 'delete')->name('customer-activity.delete');

        Route::get('/sales/customer-activity/track/{leadsId}', 'trackActivity')->name('customer-activity.track');

        Route::get('/sales/customer-activity/list', 'list')->name('customer-activity.list'); // ajax
        Route::get('/sales/customer-activity/member-tim-sales', 'memberTimSales')->name('customer-activity.member-tim-sales'); // ajax
    });

    
    Route::controller(SpkController::class)->group(function() {
        Route::get('/sales/spk', 'index')->name('spk');
        Route::get('/sales/spk/add', 'add')->name('spk.add');

        Route::get('/sales/spk/list', 'list')->name('spk.list'); // ajax
        Route::get('/sales/spk/available-quotation', 'availableQuotation')->name('spk.available-quotation'); // ajax
        Route::post('/sales/spk/save', 'save')->name('spk.save');
        Route::get('/sales/spk/view/{id}', 'view')->name('spk.view');
        Route::post('/sales/spk/upload-spk', 'uploadSPK')->name('spk.upload-spk');
        Route::get('/sales/spk/cetak-spk/{id}', 'cetakSpk')->name('spk.cetak-spk');

        // Ajukan Ulang
        Route::get('/sales/spk/ajukan-ulang-quotation/{spk}', 'ajukanUlangQuotation')->name('spk.ajukan-ulang-quotation');

    });

    Route::controller(PksController::class)->group(function() {
        Route::get('/sales/pks', 'index')->name('pks');
        Route::get('/sales/pks/add', 'add')->name('pks.add');

        Route::get('/sales/pks/list', 'list')->name('pks.list'); // ajax
        Route::get('/sales/pks/available-spk', 'availableSpk')->name('pks.available-spk'); // ajax
        Route::post('/sales/pks/save', 'save')->name('pks.save');
        Route::get('/sales/pks/view/{id}', 'view')->name('pks.view');
        Route::post('/sales/pks/upload-pks', 'uploadPks')->name('pks.upload-pks');

        Route::post('/sales/pks/approve', 'approve')->name('pks.approve');
        Route::post('/sales/pks/aktifkan-site', 'aktifkanSite')->name('pks.aktifkan-site');
        Route::get('/sales/pks/cetak-pks/{id}', 'cetakPks')->name('pks.cetak-pks');
        Route::get('/sales/pks/isi-checklist/{id}', 'isiChecklist')->name('pks.isi-checklist');
        Route::post('/sales/pks/save-checklist', 'saveChecklist')->name('pks.save-checklist');

        Route::get('/sales/pks/edit-perjanjian/{id}', 'editPerjanjian')->name('pks.edit-perjanjian');
        Route::post('/sales/pks/save-edit-perjanjian/{id}', 'saveEditPerjanjian')->name('pks.save-edit-perjanjian');

        // Ajukan Ulang
        Route::get('/sales/pks/ajukan-ulang-quotation/{pks}', 'ajukanUlangQuotation')->name('pks.ajukan-ulang-quotation');
    });

    Route::controller(QuotationController::class)->group(function() {
        Route::get('/sales/quotation', 'index')->name('quotation');
        Route::get('/sales/quotation/add', 'add')->name('quotation.add');
        Route::get('/sales/quotation/step/{id}', 'step')->name('quotation.step');
        Route::post('/sales/quotation/save-step', 'saveStep')->name('quotation.save-step');

        //page quotation
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
        Route::post('/sales/quotation/saveEdit13', 'saveEdit13')->name('quotation.save-edit-13');

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

        //KAPORLAP
        Route::get('/sales/quotation/list-kaporlap', 'listKaporlap')->name('quotation.list-kaporlap'); // ajax
        Route::post('/sales/quotation/add-detail-kaporlap', 'addDetailKaporlap')->name('quotation.add-detail-kaporlap');
        Route::post('/sales/quotation/delete-detail-kaporlap', 'deleteDetailKaporlap')->name('quotation.delete-detail-kaporlap');

        //OHC
        Route::get('/sales/quotation/list-ohc', 'listOhc')->name('quotation.list-ohc'); // ajax
        Route::post('/sales/quotation/add-detail-ohc', 'addDetailOhc')->name('quotation.add-detail-ohc');
        Route::post('/sales/quotation/delete-detail-ohc', 'deleteDetailOhc')->name('quotation.delete-detail-ohc');

        //DEVICES
        Route::get('/sales/quotation/list-devices', 'listDevices')->name('quotation.list-devices'); // ajax
        Route::post('/sales/quotation/add-detail-devices', 'addDetailDevices')->name('quotation.add-detail-devices');
        Route::post('/sales/quotation/delete-detail-devices', 'deleteDetailDevices')->name('quotation.delete-detail-devices');

        //CHEMICAL
        Route::get('/sales/quotation/list-chemical', 'listChemical')->name('quotation.list-chemical'); // ajax
        Route::post('/sales/quotation/add-detail-chemical', 'addDetailChemical')->name('quotation.add-detail-chemical');
        Route::post('/sales/quotation/delete-detail-chemical', 'deleteDetailChemical')->name('quotation.delete-detail-chemical');

        Route::post('/sales/quotation/add-biaya-monitoring', 'addBiayaMonitoring')->name('quotation.add-biaya-monitoring');

        Route::get('/sales/quotation/cetak-checklist/{id}', 'cetakChecklist')->name('quotation.cetak-checklist');
        Route::get('/sales/quotation/cetak-coss/{id}', 'cetakCoss')->name('quotation.cetak-coss');
        Route::get('/sales/quotation/cetak-quotation/{id}', 'cetakQuotation')->name('quotation.cetak-quotation');
        Route::post('/sales/quotation/add-quotation-training', 'addQuotationTraining')->name('quotation.add-quotation-training');
        Route::post('/sales/quotation/add-barang', 'addBarang')->name('quotation.add-barang');

        Route::post('/sales/quotation/add-tunjangan', 'addTunjangan')->name('quotation.add-tunjangan');
        Route::post('/sales/quotation/delete-tunjangan', 'deleteTunjangan')->name('quotation.delete-tunjangan');
        Route::post('/sales/quotation/edit-tunjangan', 'editTunjangan')->name('quotation.edit-tunjangan');
        Route::post('/sales/quotation/edit-persen-insentif', 'editPersenInsentif')->name('quotation.edit-persen-insentif');
        Route::post('/sales/quotation/edit-persen-bunga-bank', 'editPersenBungaBank')->name('quotation.edit-persen-bunga-bank');

        //PIC
        Route::get('/sales/quotation/list-detail-pic', 'listDetailPic')->name('quotation.list-detail-pic'); // ajax
        Route::post('/sales/quotation/delete-detail-pic', 'deleteDetailPic')->name('quotation.delete-detail-pic');
        Route::post('/sales/quotation/add-detail-pic', 'addDetailPic')->name('quotation.add-detail-pic');
        Route::post('/sales/quotation/change-kuasa-pic', 'changeKuasaPic')->name('quotation.change-kuasa-pic');

        Route::get('/sales/quotation/list-detail-requirement', 'listDetailRequirement')->name('quotation.list-detail-requirement'); // ajax
        Route::post('/sales/quotation/delete-detail-requirement', 'deleteDetailRequirement')->name('quotation.delete-detail-requirement');
        Route::post('/sales/quotation/add-detail-requirement', 'addDetailRequirement')->name('quotation.add-detail-requirement');

        // Copy Quotation
        Route::get('/sales/quotation/get-quotation-tujuan', 'getQuotationTujuan')->name('quotation.get-quotation-tujuan'); // ajax
        Route::get('/sales/quotation/get-quotation-asal', 'getQuotationAsal')->name('quotation.get-quotation-asal'); // ajax
        // Route::get('/sales/quotation/get-quotation-list', 'getQuotationList')->name('quotation.get-quotation-list'); // ajax
        Route::get('/sales/quotation/copy-quotation/{qasal}/{qtujuan}', 'copyQuotation')->name('quotation.copy-quotation');

        // Ajukan Ulang Quotation
        Route::get('/sales/quotation/ajukan-ulang-quotation/{quotation}', 'ajukanUlangQuotation')->name('quotation.ajukan-ulang-quotation');

        // Site
        Route::post('/sales/quotation/save-add-site', 'saveAddSite')->name('quotation.save-add-site');
        Route::get('/sales/quotation/get-site-list', 'getSiteList')->name('quotation.get-site-list'); // ajax

    });


    Route::controller(PlatformController::class)->group(function() {
        Route::get('/master/platform', 'index')->name('platform');
        Route::get('/master/platform/add', 'add')->name('platform.add');
        Route::get('/master/platform/view/{id}', 'view')->name('platform.view');

        Route::post('/master/platform/save', 'save')->name('platform.save');
        Route::post('/master/platform/delete', 'delete')->name('platform.delete');

        Route::get('/master/platform/list', 'list')->name('platform.list'); // ajax

    });

    Route::controller(AplikasiPendukungController::class)->group(function() {
        Route::get('/master/aplikasi-pendukung', 'index')->name('aplikasi-pendukung');
        Route::get('/master/aplikasi-pendukung/add', 'add')->name('aplikasi-pendukung.add');
        Route::get('/master/aplikasi-pendukung/view/{id}', 'view')->name('aplikasi-pendukung.view');

        Route::post('/master/aplikasi-pendukung/save', 'save')->name('aplikasi-pendukung.save');
        Route::post('/master/aplikasi-pendukung/delete', 'delete')->name('aplikasi-pendukung.delete');

        Route::get('/master/aplikasi-pendukung/list', 'list')->name('aplikasi-pendukung.list'); // ajax

    });
    
    Route::controller(JenisBarangController::class)->group(function() {
        Route::get('/master/jenis-barang', 'index')->name('jenis-barang');
        Route::get('/master/jenis-barang/view/{id}', 'view')->name('jenis-barang.view');

        Route::get('/master/jenis-barang/list', 'list')->name('jenis-barang.list'); // ajax
        Route::get('/master/jenis-barang/detail-barang', 'detailBarang')->name('jenis-barang.detail-barang'); // ajax

    });

    Route::controller(JabatanController::class)->group(function() {
        Route::get('/master/jabatan', 'index')->name('jabatan');
        Route::get('/master/jabatan/add', 'add')->name('jabatan.add');
        Route::get('/master/jabatan/view/{id}', 'view')->name('jabatan.view');

        Route::post('/master/jabatan/save', 'save')->name('jabatan.save');
        Route::post('/master/jabatan/delete', 'delete')->name('jabatan.delete');

        Route::get('/master/jabatan/list', 'list')->name('jabatan.list'); // ajax

    });

    Route::controller(JenisPerusahaanController::class)->group(function() {
        Route::get('/master/perusahaan', 'index')->name('perusahaan');
        Route::get('/master/perusahaan/add', 'add')->name('perusahaan.add');
        Route::get('/master/perusahaan/view/{id}', 'view')->name('perusahaan.view');

        Route::post('/master/perusahaan/save', 'save')->name('perusahaan.save');
        Route::post('/master/perusahaan/delete', 'delete')->name('perusahaan.delete');

        Route::get('/master/perusahaan/list', 'list')->name('perusahaan.list'); // ajax

    });
    
    Route::controller(ManagementFeeController::class)->group(function() {
        Route::get('/master/management-fee', 'index')->name('management-fee');
        Route::get('/master/management-fee/add', 'add')->name('management-fee.add');
        Route::get('/master/management-fee/view/{id}', 'view')->name('management-fee.view');

        Route::post('/master/management-fee/save', 'save')->name('management-fee.save');
        Route::post('/master/management-fee/delete', 'delete')->name('management-fee.delete');

        Route::get('/master/management-fee/list', 'list')->name('management-fee.list'); // ajax

    });
    
    Route::controller(SalaryRuleController::class)->group(function() {
        Route::get('/master/salary-rule', 'index')->name('salary-rule');
        Route::get('/master/salary-rule/add', 'add')->name('salary-rule.add');
        Route::get('/master/salary-rule/view/{id}', 'view')->name('salary-rule.view');

        Route::post('/master/salary-rule/save', 'save')->name('salary-rule.save');
        Route::post('/master/salary-rule/delete', 'delete')->name('salary-rule.delete');

        Route::get('/master/salary-rule/list', 'list')->name('salary-rule.list'); // ajax

    });
    
    Route::controller(StatusLeadsController::class)->group(function() {
        Route::get('/master/status-leads', 'index')->name('status-leads');

        Route::get('/master/status-leads/list', 'list')->name('status-leads.list'); // ajax

    });
    
    Route::controller(TunjanganController::class)->group(function() {
        Route::get('/master/tunjangan', 'index')->name('tunjangan');
        Route::get('/master/tunjangan/add', 'add')->name('tunjangan.add');
        Route::get('/master/tunjangan/view/{id}', 'view')->name('tunjangan.view');

        Route::post('/master/tunjangan/save', 'save')->name('tunjangan.save');
        Route::post('/master/tunjangan/delete', 'delete')->name('tunjangan.delete');

        Route::get('/master/tunjangan/list', 'list')->name('tunjangan.list'); // ajax

    });
    
    Route::controller(TunjanganJabatanController::class)->group(function() {
        Route::get('/master/tunjangan-jabatan', 'index')->name('tunjangan-jabatan');
        Route::get('/master/tunjangan-jabatan/add', 'add')->name('tunjangan-jabatan.add');
        Route::get('/master/tunjangan-jabatan/view/{id}', 'view')->name('tunjangan-jabatan.view');

        Route::post('/master/tunjangan-jabatan/save', 'save')->name('tunjangan-jabatan.save');
        Route::post('/master/tunjangan-jabatan/delete', 'delete')->name('tunjangan-jabatan.delete');

        Route::get('/master/tunjangan-jabatan/list', 'list')->name('tunjangan-jabatan.list'); // ajax
        Route::get('/master/tunjangan-jabatan/get-kebutuhan-detail', 'getKebutuhanDetail')->name('tunjangan-jabatan.get-kebutuhan-detail'); // ajax

    });
    
    Route::controller(BarangController::class)->group(function() {
        Route::get('/master/barang', 'index')->name('barang');
        Route::get('/master/barang/add', 'add')->name('barang.add');
        Route::get('/master/barang/view/{id}', 'view')->name('barang.view');

        Route::post('/master/barang/save', 'save')->name('barang.save');
        Route::post('/master/barang/delete', 'delete')->name('barang.delete');

        Route::get('/master/barang/list', 'list')->name('barang.list'); // ajax
    });
    
    Route::controller(KebutuhanController::class)->group(function() {
        Route::get('/master/kebutuhan', 'index')->name('kebutuhan');
        Route::get('/master/kebutuhan/view/{id}', 'view')->name('kebutuhan.view');

        Route::get('/master/kebutuhan/list', 'list')->name('kebutuhan.list'); // ajax
        Route::get('/master/kebutuhan/list-detail', 'listDetail')->name('kebutuhan.list-detail'); // ajax
        
        Route::get('/master/kebutuhan/list-detail-tunjangan', 'listDetailTunjangan')->name('kebutuhan.list-detail-tunjangan'); // ajax
        Route::post('/master/kebutuhan/delete-detail-tunjangan', 'deleteDetailTunjangan')->name('kebutuhan.delete-detail-tunjangan');
        Route::post('/master/kebutuhan/add-detail-tunjangan', 'addDetailTunjangan')->name('kebutuhan.add-detail-tunjangan');

        Route::get('/master/kebutuhan/list-detail-requirement', 'listDetailRequirement')->name('kebutuhan.list-detail-requirement'); // ajax
        Route::post('/master/kebutuhan/delete-detail-requirement', 'deleteDetailRequirement')->name('kebutuhan.delete-detail-requirement');
        Route::post('/master/kebutuhan/add-detail-requirement', 'addDetailrequiRement')->name('kebutuhan.add-detail-requirement');
    });
    
    Route::controller(TimSalesController::class)->group(function() {
        Route::get('/master/tim-sales', 'index')->name('tim-sales');
        Route::get('/master/tim-sales/add', 'add')->name('tim-sales.add');
        Route::get('/master/tim-sales/view/{id}', 'view')->name('tim-sales.view');
        Route::post('/master/tim-sales/save', 'save')->name('tim-sales.save');
        
        Route::post('/master/tim-sales/delete', 'delete')->name('tim-sales.delete');

        Route::post('/master/tim-sales/add-detail-sales', 'addDetailSales')->name('tim-sales.add-detail-sales');
        Route::get('/master/tim-sales/list-detail-sales', 'listDetailSales')->name('tim-sales.list-detail-sales'); // ajax
        Route::post('/master/tim-sales/change-is-leader', 'changeIsLeader')->name('tim-sales.change-is-leader');
        Route::post('/master/tim-sales/delete-detail-sales', 'deleteDetailSales')->name('tim-sales.delete-detail-sales');

        Route::get('/master/tim-sales/list', 'list')->name('tim-sales.list'); // ajax
    });

    
    Route::controller(TrainingController::class)->group(function() {
        Route::get('/master/training', 'index')->name('training');
        Route::get('/master/training/add', 'add')->name('training.add');
        Route::get('/master/training/view/{id}', 'view')->name('training.view');
        Route::post('/master/training/save', 'save')->name('training.save');
        
        Route::post('/master/training/delete', 'delete')->name('training.delete');

        Route::get('/master/training/list', 'list')->name('training.list'); // ajax
    });
    
    Route::controller(UmpController::class)->group(function() {
        Route::get('/master/ump', 'index')->name('ump');
        Route::get('/master/ump/add', 'add')->name('ump.add');
        Route::get('/master/ump/view/{id}', 'view')->name('ump.view');
        Route::post('/master/ump/save', 'save')->name('ump.save');

        Route::get('/master/ump/list', 'list')->name('ump.list'); // ajax
        Route::get('/master/ump/list-ump', 'listUmp')->name('ump.list-ump'); // ajax
    });

    Route::controller(UmkController::class)->group(function() {
        Route::get('/master/umk', 'index')->name('umk');
        Route::get('/master/umk/add', 'add')->name('umk.add');
        Route::get('/master/umk/view/{id}', 'view')->name('umk.view');
        Route::post('/master/umk/save', 'save')->name('umk.save');

        Route::get('/master/umk/list', 'list')->name('umk.list'); // ajax
        Route::get('/master/umk/list-umk', 'listUmk')->name('umk.list-umk'); // ajax
    });

    Route::controller(MonitoringKontrakController::class)->group(function() {
        Route::get('/sales/monitoring-kontrak', 'index')->name('monitoring-kontrak');
        Route::get('/sales/monitoring-kontrak/list', 'list')->name('monitoring-kontrak.list');
        Route::post('/sales/monitoring-kontrak/terminate', 'terminate')->name('monitoring-kontrak.terminate');
        Route::get('/sales/monitoring-kontrak/index-terminate', 'indexTerminate')->name('monitoring-kontrak.index-terminate');
        Route::get('/sales/monitoring-kontrak/list-terminate', 'listTerminate')->name('monitoring-kontrak.list-terminate');
    });
});