<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyFastApiKey;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Fitur\ContactController;
use App\Http\Controllers\Fitur\SdtTrainingInviteController;

use App\Http\Controllers\Sales\LeadsController;
use App\Http\Controllers\Sales\SubmissionController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\SiteController;
use App\Http\Controllers\Sales\CustomerActivityController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\PksKelengkapanController;
use App\Http\Controllers\Sales\QuotationSandboxController;
use App\Http\Controllers\Sales\SpkController;
use App\Http\Controllers\Sales\PksController;
use App\Http\Controllers\Sales\MonitoringKontrakController;
use App\Http\Controllers\Sales\PutusKontrakController;
use App\Http\Controllers\Sales\WhatsappController;
use App\Http\Controllers\Sales\IssueController;

use App\Http\Controllers\Master\PlatformController;
use App\Http\Controllers\Master\AplikasiPendukungController;
use App\Http\Controllers\Master\JenisBarangController;
use App\Http\Controllers\Master\JabatanController;
use App\Http\Controllers\Master\JenisPerusahaanController;
use App\Http\Controllers\Master\ManagementFeeController;
use App\Http\Controllers\Master\TopController;
use App\Http\Controllers\Master\JenisVisitController;
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
use App\Http\Controllers\Master\TrainingMateriController;
use App\Http\Controllers\Master\TrainingDivisiController;
use App\Http\Controllers\Master\TrainingTrainerController;
use App\Http\Controllers\Master\TrainingAreaController;
use App\Http\Controllers\Master\TrainingClientController;
use App\Http\Controllers\Master\TrainingGadaHargaController;
use App\Http\Controllers\Master\TrainingGadaJadwalController;

use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Sdt\SdtTrainingController;
use App\Http\Controllers\Sdt\TrainingSiteController;

use App\Http\Controllers\Gada\TrainingGadaController;
use App\Http\Controllers\Gada\TrainingGadaPembayaranController;

use App\Http\Controllers\Setting\EntitasController;

use App\Http\Controllers\Log\NotifikasiController;
use App\Http\Controllers\Master\LoyaltyController;
use App\Http\Controllers\Master\MasterMenuController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Sales\PurchaseController;

use App\Http\Controllers\Dashboard\DashboardManagerCrmController;

Route::controller(AuthController::class)->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/', 'dashboard')->name('home');
    Route::get('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
});

Route::controller(MasterMenuController::class)->group(function() {
    Route::get('/master/menu', 'index')->name('master.menu');
    Route::get('/master/menu/list', 'list')->name('master.menu.list');
    Route::get('/master/menu/add', 'add')->name('master.menu.add');
    Route::get('/master/menu/list/role', 'listRole')->name('master.menu.list-role');
    Route::post('/master/menu/simpan-akses', 'simpanRole')->name('master.menu.simpan-akses');
    Route::post('/master/menu/save', 'save')->name('master.menu.save');
    Route::get('/master/menu/view/{id}', 'view')->name('master.menu.view');
    Route::post('/master/menu/update/{id}', 'update')->name('master.menu.update');
    Route::post('/master/menu/delete/{id}', 'delete')->name('master.menu.delete');
});

//form luar
Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact-save', 'contactSave')->name('contact.save');
    Route::post('/webhook-endpoint/{key}', 'handleWebhook')->name('webhook-endpoint');
});

Route::controller(SdtTrainingInviteController::class)->group(function () {
    Route::get('/sdt-training', 'invite')->name('invite');
    Route::get('/sdt-training/nik', 'dataNik')->name('invite-nik');
    Route::post('/sdt-training-save', 'pesertaSave')->name('invite-save');

    Route::post('/sdt-training-pdf', 'testPdf')->name('invite-pdf');
    Route::post('/sdt-training-pdf-web', 'testPdfWeb')->name('invite-pdf-web');

    // Route::post('/webhook-endpoint/{key}', 'handleWebhook')->name('webhook-endpoint');
});

Route::controller(QuotationController::class)->group(function () {
    Route::get('/view/checklist/{id}/{key}', 'viewChecklist')->name('quotation.view-checklist');
});

Route::group(['middleware' => ['verify_leads_api']], function () {
    Route::controller(ContactController::class)->group(function () {
        Route::post('/api/contact-save', 'apiContactSave')->name('api.contact.save');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard/approval', 'dashboardApproval')->name('dashboard-approval');
        Route::get('/dashboard/aktifitas-sales', 'dashboardAktifitasSales')->name('dashboard-aktifitas-sales');
        Route::get('/dashboard/aktifitas-telesales', 'dashboardAktifitasTelesales')->name('dashboard-aktifitas-telesales');
        Route::get('/dashboard/leads', 'dashboardLeads')->name('dashboard-leads');
        Route::get('/dashboard/general', 'dashboardGeneral')->name('dashboard-general');
        Route::get('/dashboard/sdt-training', 'dashboardSdtTraining')->name('dashboard-sdt-training');
        Route::get('/dashboard/training-gada', 'dashboardTrainingGada')->name('dashboard-training-gada');
        Route::get('/dashboard/manager-crm', 'dashboardManagerCrm')->name('dashboard-manager-crm');
        Route::get('/dashboard/edit-patch', 'editPatch')->name('change.log');
        Route::post('/dashboard/update-patch', 'updatePatch')->name('update.log');

        // list
        Route::get('/dashboard/approval/list', 'getListDashboardApprovalData')->name('dashboard-approval.list');
        Route::get('/dashboard/aktifkan/list', 'getListDashboardAktifkanData')->name('dashboard-aktifkan.list');

        // ajax modal
        Route::get('/dashboard/aktifitas-sales/modal/aktifitas-sales-hari-ini', 'listAktifitasSalesHariIni')->name('dashboard.aktifitas-sales.modal.aktifitas-sales-hari-ini');
        Route::get('/dashboard/aktifitas-sales/modal/aktifitas-sales-minggu-ini', 'listAktifitasSalesMingguIni')->name('dashboard.aktifitas-sales.modal.aktifitas-sales-minggu-ini');
        Route::get('/dashboard/aktifitas-sales/modal/aktifitas-sales-bulan-ini', 'listAktifitasSalesBulanIni')->name('dashboard.aktifitas-sales.modal.aktifitas-sales-bulan-ini');
        Route::get('/dashboard/aktifitas-sales/modal/aktifitas-sales-tahun-ini', 'listAktifitasSalesTahunIni')->name('dashboard.aktifitas-sales.modal.aktifitas-sales-tahun-ini');
        Route::get('/dashboard/aktifitas-sales/pivot/aktifitas-sales', 'pivotAktifitasSales')->name('dashboard.aktifitas-sales.pivot.aktifitas-sales');
        Route::get('/dashboard/aktifitas-sales/tabel/laporan-mingguan-sales', 'laporanMingguanSales')->name('dashboard.aktifitas-sales.tabel.laporan-mingguan-sales');
        Route::get('/dashboard/aktifitas-sales/tabel/laporan-bulanan-sales', 'laporanBulananSales')->name('dashboard.aktifitas-sales.tabel.laporan-bulanan-sales');

        Route::get('/dashboard/aktifitas-telesales/modal/aktifitas-telesales-hari-ini', 'listAktifitasTelesalesHariIni')->name('dashboard.aktifitas-telesales.modal.aktifitas-telesales-hari-ini');
        Route::get('/dashboard/aktifitas-telesales/modal/aktifitas-telesales-minggu-ini', 'listAktifitasTelesalesMingguIni')->name('dashboard.aktifitas-telesales.modal.aktifitas-telesales-minggu-ini');
        Route::get('/dashboard/aktifitas-telesales/modal/aktifitas-telesales-bulan-ini', 'listAktifitasTelesalesBulanIni')->name('dashboard.aktifitas-telesales.modal.aktifitas-telesales-bulan-ini');
        Route::get('/dashboard/aktifitas-telesales/modal/aktifitas-telesales-tahun-ini', 'listAktifitasTelesalesTahunIni')->name('dashboard.aktifitas-telesales.modal.aktifitas-telesales-tahun-ini');
        Route::get('/dashboard/aktifitas-telesales/pivot/aktifitas-telesales', 'pivotAktifitasTelesales')->name('dashboard.aktifitas-telesales.pivot.aktifitas-telesales');
        Route::get('/dashboard/aktifitas-telesales/tabel/laporan-mingguan-telesales', 'laporanMingguanTelesales')->name('dashboard.aktifitas-telesales.tabel.laporan-mingguan-telesales');
        Route::get('/dashboard/aktifitas-telesales/tabel/laporan-bulanan-telesales', 'laporanBulananTelesales')->name('dashboard.aktifitas-telesales.tabel.laporan-bulanan-telesales');

        Route::get('/dashboard/aktifitas-sales/modal/aktifitas-sales-bulanan-detail', 'listAktifitasSalesBulananDetail')->name('dashboard.aktifitas-sales.modal.aktifitas-sales-bulanan-detail');
    });

    Route::controller(DashboardManagerCrmController::class)->group(function() {
        Route::get('/dashboard/manager-crm', 'dashboardManagerCrm')->name('dashboard-manager-crm');
        Route::get('/dashboard/pks-siap-aktif/list', 'listPksSiapAktif')->name('dashboard-pks-siap-aktif.list');
    });

    Route::controller(LeadsController::class)->group(function () {
        Route::get('/sales/leads', 'index')->name('leads');
        Route::get('/sales/leads/terhapus', 'indexTerhapus')->name('leads.terhapus');
        Route::get('/sales/leads/add', 'add')->name('leads.add');
        Route::get('/sales/leads/view/{id}', 'view')->name('leads.view');
        Route::get('/sales/leads/import', 'import')->name('leads.import');
        Route::get('/sales/leads/template-import', 'templateImport')->name('leads.template-import');
        Route::post('/sales/leads/groupkan', 'groupkan')->name('leads.groupkan');
        Route::post('/sales/leads/filter-rekomendasi', 'filterRekomendasi')->name('leads.rekomendasi.filter');
        Route::get('/sales/leads/group-modal', 'showGrupModal')->name('leads.group_modal');


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

        Route::get('/sales/leads/get-kota/{provinsiId}', 'getKota')->name('leads.get-kota'); // ajax
        Route::get('/sales/leads/get-kecamatan/{kotaId}', 'getKecamatan')->name('leads.get-kecamatan'); // ajax
        Route::get('/sales/leads/get-kelurahan/{kecamatanId}', 'getKelurahan')->name('leads.get-kelurahan'); // ajax

        Route::get('/sales/leads/aktifkan', 'aktifkanLeads')->name('leads.aktifkan'); // ajax
        Route::get('/sales/leads/leads-belum-aktif', 'leadsBelumAktif')->name('sales.leads.leads-belum-aktif');

        Route::get('/sales/leads/get-negara/{kecamatanId}', 'getNegara')->name('leads.get-negara'); // ajax

        // generate null kode
        Route::get('/sales/leads/generate-null-kode', 'generateNullKode')->name('leads.generate-null-kode'); // ajax
    });

    Route::controller(SubmissionController::class)->group(function () {
        Route::get('/sales/submission', 'index')->name('submission');

        Route::post('/sales/submission/save', 'save')->name('submission.save');
        Route::post('/sales/submission/delete', 'delete')->name('submission.delete');

        Route::get('/sales/submission/list', 'list')->name('submission.list'); // ajax
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/sales/customer', 'index')->name('customer');
        Route::get('/sales/customer/view/{id}', 'view')->name('customer.view');

        Route::get('/sales/customer/list', 'list')->name('customer.list'); // ajax
        Route::get('/sales/customer/available-customer', 'availableCustomer')->name('customer.available-customer'); // ajax

    });

    Route::controller(SiteController::class)->group(function () {
        Route::get('/sales/site', 'index')->name('site');
        Route::get('/sales/site/view/{id}', 'view')->name('site.view');

        Route::get('/sales/site/list', 'list')->name('site.list'); // ajax
    });

    Route::controller(CustomerActivityController::class)->group(function () {
        Route::get('/sales/customer-activity', 'index')->name('customer-activity');
        Route::get('/sales/customer-activity/add', 'add')->name('customer-activity.add');
        Route::get('/sales/customer-activity/add-activity-kontrak/{id}', 'addActivityKontrak')->name('customer-activity.add-activity-kontrak');
        Route::get('/sales/customer-activity/add-ro-kontrak/{id}', 'addRoKontrak')->name('customer-activity.add-ro-kontrak');
        Route::get('/sales/customer-activity/add-pic-kontrak/{id}', 'addPicKontrak')->name('customer-activity.add-pic-kontrak');
        Route::get('/sales/customer-activity/add-crm-kontrak/{id}', 'addCrmKontrak')->name('customer-activity.add-crm-kontrak');
        Route::get('/sales/customer-activity/add-status-kontrak/{id}', 'addStatusKontrak')->name('customer-activity.add-status-kontrak');
        Route::get('/sales/customer-activity/view/{id}', 'view')->name('customer-activity.view');

        Route::post('/sales/customer-activity/save', 'save')->name('customer-activity.save');
        Route::post('/sales/customer-activity/save-activity-kontrak', 'saveActivityKontrak')->name('customer-activity.save-activity-kontrak');
        Route::post('/sales/customer-activity/save-activity-ro-kontrak', 'saveActivityRoKontrak')->name('customer-activity.save-activity-ro-kontrak');
        Route::post('/sales/customer-activity/save-activity-crm-kontrak', 'saveActivityCrmKontrak')->name('customer-activity.save-activity-crm-kontrak');
        Route::post('/sales/customer-activity/save-activity-status-kontrak', 'saveActivityStatusKontrak')->name('customer-activity.save-activity-status-kontrak');
        Route::post('/sales/customer-activity/delete', 'delete')->name('customer-activity.delete');

        Route::get('/sales/customer-activity/track/{leadsId}', 'trackActivity')->name('customer-activity.track');
        Route::get('/sales/customer-activity/ajax', 'ajaxFeedPaginated')->name('customer-activity.ajaxFeedPaginated');

        Route::get('/sales/customer-activity/list', 'list')->name('customer-activity.list'); // ajax
        Route::get('/sales/customer-activity/member-tim-sales', 'memberTimSales')->name('customer-activity.member-tim-sales'); // ajax
        Route::post('/sales/customer-activity/send-email', 'sendEmail')->name('customer-activity.send-email');

        Route::get('/sales/customer-activity/modal/list-activity-kontrak', 'listActivityKontrak')->name('customer-activity.modal.list-activity-kontrak'); // ajax
        Route::get('/sales/customer-activity/modal/list-issue', 'listIssue')->name('customer-activity.modal.list-issue'); // ajax
    });


    Route::controller(SpkController::class)->group(function () {
        Route::get('/sales/spk', 'index')->name('spk');
        Route::get('/sales/spk/terhapus', 'indexTerhapus')->name('spk.terhapus');
        Route::get('/sales/spk/add', 'add')->name('spk.add');

        Route::get('/sales/spk/list', 'list')->name('spk.list'); // ajax
        Route::get('/sales/spk/list-terhapus', 'listTerhapus')->name('spk.list-terhapus'); // ajax
        Route::get('/sales/spk/available-leads', 'availableLeads')->name('spk.available-leads'); // ajax
        Route::get('/sales/spk/get-site-available-list', 'getSiteAvailableList')->name('spk.get-site-available-list'); // ajax
        Route::get('/sales/spk/available-quotation', 'availableQuotation')->name('spk.available-quotation'); // ajax
        Route::post('/sales/spk/save', 'save')->name('spk.save');
        Route::get('/sales/spk/view/{id}', 'view')->name('spk.view');
        Route::post('/sales/spk/upload-spk', 'uploadSPK')->name('spk.upload-spk');
        Route::get('/sales/spk/cetak-spk/{id}', 'cetakSpk')->name('spk.cetak-spk');

        Route::get('/sales/spk/get-site-list', 'getSiteList')->name('spk.get-site-list'); // ajax
        // Ajukan Ulang
        Route::get('/sales/spk/ajukan-ulang-quotation/{spk}', 'ajukanUlangQuotation')->name('spk.ajukan-ulang-quotation');

    });

    Route::controller(PksController::class)->group(function () {
        Route::get('/sales/pks', 'index')->name('pks');
        Route::get('/sales/pks/terhapus', 'indexTerhapus')->name('pks.terhapus');
        Route::get('/sales/pks/add', 'add')->name('pks.add');

        Route::get('/sales/pks/list', 'list')->name('pks.list'); // ajax
        Route::get('/sales/pks/list-terhapus', 'listTerhapus')->name('pks.list-terhapus'); // ajax
        Route::get('/sales/pks/available-spk', 'availableSpk')->name('pks.available-spk'); // ajax
        Route::post('/sales/pks/save', 'save')->name('pks.save');
        Route::get('/sales/pks/view/{id}', 'viewNew')->name('pks.view');
        Route::get('/sales/pks/view-new/{id}', 'viewNew')->name('pks.view-new');
        Route::post('/sales/pks/upload-pks', 'uploadPks')->name('pks.upload-pks');

        Route::post('/sales/pks/approve', 'approve')->name('pks.approve');
        Route::post('/sales/pks/aktifkan-site', 'aktifkanSite')->name('pks.aktifkan-site');
        Route::post('/sales/pks/buat-lowongan', 'buatLowongan')->name('pks.buat-lowongan');
        Route::get('/sales/pks/cetak-pks/{id}', 'cetakPks')->name('pks.cetak-pks');
        Route::get('/sales/pks/isi-checklist/{id}', 'isiChecklist')->name('pks.isi-checklist');
        Route::post('/sales/pks/save-checklist', 'saveChecklist')->name('pks.save-checklist');

        Route::get('/sales/pks/edit-perjanjian/{id}', 'editPerjanjian')->name('pks.edit-perjanjian');
        Route::post('/sales/pks/save-edit-perjanjian/{id}', 'saveEditPerjanjian')->name('pks.save-edit-perjanjian');

        // Ajukan Ulang
        Route::get('/sales/pks/ajukan-ulang-quotation/{pks}', 'ajukanUlangQuotation')->name('pks.ajukan-ulang-quotation');

        Route::get('/sales/pks/available-leads', 'availableLeads')->name('pks.available-leads'); // ajax
        Route::get('/sales/pks/get-site-available-list', 'getSiteAvailableList')->name('pks.get-site-available-list'); // ajax
        Route::get('/sales/pks/get-detail-quotation', 'getDetailQuotation')->name('pks.get-detail-quotation'); // ajax
    });

    Route::controller(IssueController::class)->group(function() {
        Route::get('/sales/issue', 'index')->name('issue');
        Route::get('/sales/issue/list', 'list')->name('issue.list');
        Route::get('/sales/issue/lead-list', 'leadsList')->name('issue.leads-list');
        Route::get('/sales/issue/pks-list', 'pksList')->name('issue.pks-list');
        Route::get('/sales/issue/site-list', 'siteList')->name('issue.site-list');
        Route::get('/sales/issue/add', 'add')->name('issue.add');
        Route::get('/sales/issue/view/{id}', 'view')->name('issue.view');
        Route::post('/sales/issue/save', 'save')->name('issue.save');
        Route::post('/sales/issue/update/{id}', 'update')->name('issue.update');
        Route::post('/sales/issue/delete/{id}', 'delete')->name('issue.delete');
    });
    Route::controller(PksKelengkapanController::class)->group(function() {
        Route::get('/sales/lengkapi-quotation/add/{pksId}', 'add')->name('lengkapi-quotation.add');
        Route::post('/sales/lengkapi-quotation/save', 'save')->name('lengkapi-quotation.save');
        Route::get('/sales/lengkapi-quotation/step/{id}', 'step')->name('lengkapi-quotation.step');
        Route::post('/sales/lengkapi-quotation/saveEdit1', 'saveEdit1')->name('lengkapi-quotation.save-edit-1');
        Route::post('/sales/lengkapi-quotation/saveEdit2', 'saveEdit2')->name('lengkapi-quotation.save-edit-2');
        Route::post('/sales/lengkapi-quotation/saveEdit3', 'saveEdit3')->name('lengkapi-quotation.save-edit-3');
        Route::post('/sales/lengkapi-quotation/saveEdit4', 'saveEdit4')->name('lengkapi-quotation.save-edit-4');
        Route::post('/sales/lengkapi-quotation/saveEdit5', 'saveEdit5')->name('lengkapi-quotation.save-edit-5');
        Route::post('/sales/lengkapi-quotation/saveEdit6', 'saveEdit6')->name('lengkapi-quotation.save-edit-6');
        Route::post('/sales/lengkapi-quotation/saveEdit7', 'saveEdit7')->name('lengkapi-quotation.save-edit-7');
        Route::post('/sales/lengkapi-quotation/saveEdit8', 'saveEdit8')->name('lengkapi-quotation.save-edit-8');
        Route::post('/sales/lengkapi-quotation/saveEdit9', 'saveEdit9')->name('lengkapi-quotation.save-edit-9');
        Route::post('/sales/lengkapi-quotation/saveEdit10', 'saveEdit10')->name('lengkapi-quotation.save-edit-10');
        Route::post('/sales/lengkapi-quotation/saveEdit11', 'saveEdit11')->name('lengkapi-quotation.save-edit-11');
        Route::post('/sales/lengkapi-quotation/saveEdit12', 'saveEdit12')->name('lengkapi-quotation.save-edit-12');
        Route::post('/sales/lengkapi-quotation/saveEdit13', 'saveEdit13')->name('lengkapi-quotation.save-edit-13');
        Route::get('/sales/lengkapi-quotation/edit-note-harga-jual/{id}', 'editNoteHargaJual')->name('lengkapi-quotation.edit-note-harga-jual');
        Route::post('/sales/lengkapi-quotation/save-edit-note-harga-jual', 'saveEditNoteHargaJual')->name('lengkapi-quotation.save-edit-note-harga-jual');
        Route::get('/sales/lengkapi-quotation/edit-quotation-kerjasama/{id}', 'editQuotationKerjasama')->name('lengkapi-quotation.edit-quotation-kerjasama');
        Route::get('/sales/lengkapi-quotation/add-quotation-kerjasama/{id}', 'addQuotationKerjasama')->name('lengkapi-quotation.add-quotation-kerjasama');
        Route::post('/sales/lengkapi-quotation/save-add-quotation-kerjasama', 'saveAddQuotationKerjasama')->name('lengkapi-quotation.save-add-quotation-kerjasama');
        Route::post('/sales/lengkapi-quotation/save-edit-quotation-kerjasama', 'saveEditQuotationKerjasama')->name('lengkapi-quotation.save-edit-quotation-kerjasama');
        Route::get('/sales/lengkapi-quotation/list-quotation-kerjasama', 'listQuotationKerjasama')->name('lengkapi-quotation.list-quotation-kerjasama'); // ajax

    });

    Route::controller(QuotationSandboxController::class)->group(function () {
        Route::get('/sales/quotation-sandbox/add/{pksId}', 'add')->name('quotation-sandbox.add');
        Route::post('/sales/quotation-sandbox/save', 'save')->name('quotation-sandbox.save');
        Route::get('/sales/quotation-sandbox/step/{id}', 'step')->name('quotation-sandbox.step');
        Route::post('/sales/quotation-sandbox/saveEdit1', 'saveEdit1')->name('quotation-sandbox.save-edit-1');
        Route::post('/sales/quotation-sandbox/saveEdit2', 'saveEdit2')->name('quotation-sandbox.save-edit-2');
        Route::post('/sales/quotation-sandbox/saveEdit3', 'saveEdit3')->name('quotation-sandbox.save-edit-3');
        Route::post('/sales/quotation-sandbox/saveEdit4', 'saveEdit4')->name('quotation-sandbox.save-edit-4');
        Route::post('/sales/quotation-sandbox/saveEdit5', 'saveEdit5')->name('quotation-sandbox.save-edit-5');
        Route::post('/sales/quotation-sandbox/saveEdit6', 'saveEdit6')->name('quotation-sandbox.save-edit-6');
        Route::post('/sales/quotation-sandbox/saveEdit7', 'saveEdit7')->name('quotation-sandbox.save-edit-7');
        Route::post('/sales/quotation-sandbox/saveEdit8', 'saveEdit8')->name('quotation-sandbox.save-edit-8');
        Route::post('/sales/quotation-sandbox/saveEdit9', 'saveEdit9')->name('quotation-sandbox.save-edit-9');
        Route::post('/sales/quotation-sandbox/saveEdit10', 'saveEdit10')->name('quotation-sandbox.save-edit-10');
        Route::post('/sales/quotation-sandbox/saveEdit11', 'saveEdit11')->name('quotation-sandbox.save-edit-11');
        Route::post('/sales/quotation-sandbox/saveEdit12', 'saveEdit12')->name('quotation-sandbox.save-edit-12');
        Route::post('/sales/quotation-sandbox/saveEdit13', 'saveEdit13')->name('quotation-sandbox.save-edit-13');
        Route::get('/sales/quotation-sandbox/edit-note-harga-jual/{id}', 'editNoteHargaJual')->name('quotation-sandbox.edit-note-harga-jual');
        Route::post('/sales/quotation-sandbox/save-edit-note-harga-jual', 'saveEditNoteHargaJual')->name('quotation-sandbox.save-edit-note-harga-jual');
        Route::get('/sales/quotation-sandbox/edit-quotation-kerjasama/{id}', 'editQuotationKerjasama')->name('quotation-sandbox.edit-quotation-kerjasama');
        Route::get('/sales/quotation-sandbox/add-quotation-kerjasama/{id}', 'addQuotationKerjasama')->name('quotation-sandbox.add-quotation-kerjasama');
        Route::post('/sales/quotation-sandbox/save-add-quotation-kerjasama', 'saveAddQuotationKerjasama')->name('quotation-sandbox.save-add-quotation-kerjasama');
        Route::post('/sales/quotation-sandbox/save-edit-quotation-kerjasama', 'saveEditQuotationKerjasama')->name('quotation-sandbox.save-edit-quotation-kerjasama');
        Route::get('/sales/quotation-sandbox/list-quotation-kerjasama', 'listQuotationKerjasama')->name('quotation-sandbox.list-quotation-kerjasama'); // ajax
    });

    Route::controller(QuotationController::class)->group(function () {
        Route::get('/sales/quotation', 'index')->name('quotation');
        Route::get('/sales/quotation/terhapus', 'indexTerhapus')->name('quotation.terhapus');
        Route::get('/sales/quotation/add', 'add')->name('quotation.add');
        Route::get('/sales/quotation/step/{id}', 'step')->name('quotation.step');
        Route::post('/sales/quotation/save-step', 'saveStep')->name('quotation.save-step');
        ;
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
        Route::get('/sales/quotation/list-terhapus', 'listTerhapus')->name('quotation.list-terhapus'); // ajax
        Route::post('/sales/quotation/add-detail-hc', 'addDetailHC')->name('quotation.add-detail-hc');
        Route::get('/sales/quotation/list-detail-hc', 'listDetailHC')->name('quotation.list-detail-hc'); // ajax
        Route::post('/sales/quotation/delete-detail-hc', 'deleteDetailHC')->name('quotation.delete-detail-hc');
        Route::get('/sales/quotation/change-kota', 'changeKota')->name('quotation.change-kota'); // ajax
        Route::get('/sales/quotation/list-quotation-kerjasama', 'listQuotationKerjasama')->name('quotation.list-quotation-kerjasama'); // ajax
        Route::get('/sales/quotation/edit-quotation-kerjasama/{id}', 'editQuotationKerjasama')->name('quotation.edit-quotation-kerjasama');
        Route::get('/sales/quotation/add-quotation-kerjasama/{id}', 'addQuotationKerjasama')->name('quotation.add-quotation-kerjasama');
        Route::post('/sales/quotation/save-add-quotation-kerjasama', 'saveAddQuotationKerjasama')->name('quotation.save-add-quotation-kerjasama');
        Route::post('/sales/quotation/save-edit-quotation-kerjasama', 'saveEditQuotationKerjasama')->name('quotation.save-edit-quotation-kerjasama');
        Route::post('/sales/quotation/delete-quotation-kerjasama', 'deleteQuotationKerjasama')->name('quotation.delete-quotation-kerjasama');
        Route::post('/sales/quotation/delete-quotation', 'deleteQuotation')->name('quotation.delete-quotation');
        Route::post('/sales/quotation/approve-quotation', 'approveQuotation')->name('quotation.approve-quotation');

        Route::get('/sales/quotation/edit-note-harga-jual/{id}', 'editNoteHargaJual')->name('quotation.edit-note-harga-jual');
        Route::post('/sales/quotation/save-edit-note-harga-jual', 'saveEditNoteHargaJual')->name('quotation.save-edit-note-harga-jual');

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
        Route::post('/sales/quotation/add-quotation-training', 'addQuotationTraining')->name('quotation.add-quotation-training');
        Route::post('/sales/quotation/add-barang', 'addBarang')->name('quotation.add-barang');

        Route::post('/sales/quotation/add-tunjangan', 'addTunjangan')->name('quotation.add-tunjangan');
        Route::post('/sales/quotation/delete-tunjangan', 'deleteTunjangan')->name('quotation.delete-tunjangan');
        Route::post('/sales/quotation/edit-tunjangan', 'editTunjangan')->name('quotation.edit-tunjangan');
        Route::post('/sales/quotation/edit-persen-insentif', 'editPersenInsentif')->name('quotation.edit-persen-insentif');
        Route::post('/sales/quotation/edit-persen-bunga-bank', 'editPersenBungaBank')->name('quotation.edit-persen-bunga-bank');
        Route::post('/sales/quotation/edit-nominal', 'editNominal')->name('quotation.edit-nominal');
        Route::post('/sales/quotation/edit-jumlah-ohc', 'editJumlahOhc')->name('quotation.edit-jumlah-ohc');
        Route::post('/sales/quotation/edit-harga-ohc', 'editHargaOhc')->name('quotation.edit-harga-ohc');

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

        // CETAKAN
        Route::get('/sales/quotation/cetak-hpp/{id}', 'cetakHpp')->name('quotation.cetak-hpp');
        Route::get('/sales/quotation/cetak-coss/{id}', 'cetakCoss')->name('quotation.cetak-coss');
        Route::get('/sales/quotation/cetak-gpm/{id}', 'cetakGpm')->name('quotation.cetak-gpm');
        Route::get('/sales/quotation/cetak-quotation/{id}/{mode?}', 'cetakQuotation')->name('quotation.cetak-quotation');
        Route::get('/sales/quotation/cetak-kaporlap/{id}', 'cetakKaporlap')->name('quotation.cetak-kaporlap');
        Route::get('/sales/quotation/cetak-devices/{id}', 'cetakDevices')->name('quotation.cetak-devices');
        Route::get('/sales/quotation/cetak-chemical/{id}', 'cetakChemical')->name('quotation.cetak-chemical');



        // EXPORT
        Route::get('/sales/quotation/export/detail-coss/{id}/{jenis}', 'exportDetailCoss')->name('quotation.export.detail-coss');

    });
    Route::controller(SupplierController::class)->group(function () {
        Route::get('/master/supplier', 'index')->name('supplier');
        Route::get('/master/supplier/Gr', 'indexGr')->name('supplier.Gr');
        Route::get('/master/supplier/Rn', 'indexRn')->name('supplier.Rn');
        Route::get('/master/supplier/addGr', 'addGr')->name('supplier.addGr');
        Route::get('/master/supplier/addRn', 'addRn')->name('supplier.addRn');
        Route::get('/master/supplier/add', 'add')->name('supplier.add');
        Route::post('/master/supplier/save', 'save')->name('supplier.save');
        Route::post('/sales/supplier/delete', 'delete')->name('supplier.delete');
        Route::get('/master/supplier/view/{id}', 'view')->name('supplier.view');
        Route::get('/master/supplier/viewGr/{id}', 'viewGr')->name('supplier.viewGr');
        Route::get('/master/supplier/viewRn/{id}', 'viewRn')->name('supplier.viewRn');
        Route::get('/master/supplier/create', 'addGr')->name('supplier.create');
        Route::get('/master/supplier/create2', 'addRn')->name('supplier.create2');
        Route::get('/master/supplier/get-purchase-order-list', 'getPurchaseOrderList')->name('supplier.get-purchase-order-list');
        Route::get('/master/supplier/get-barang-by-po', 'availableBarangPO')->name('supplier.get-barang-by-po');
        Route::get('/master/supplier/get-request-order-list', 'getPurchaseRequestList')->name('supplier.get-purchase-request-list');
        Route::get('/master/supplier/get-barang-by-pr', 'availableBarangPR')->name('supplier.get-barang-by-pr');
        Route::post('/master/supplier/print-gr', 'saveGr')->name('supplier.saveGr');
        Route::post('/master/supplier/print-rn', 'saveRn')->name('supplier.saveRn');
        // ajax
        Route::get('/sales/supplier/data', 'list')->name('supplier.data');
        Route::get('/master/supplier/listGr', 'listGr')->name('supplier.listGr');
        Route::get('/master/supplier/listRn', 'listRn')->name('supplier.listRn');
    });


    Route::controller(PlatformController::class)->group(function () {
        Route::get('/master/platform', 'index')->name('platform');
        Route::get('/master/platform/add', 'add')->name('platform.add');
        Route::get('/master/platform/view/{id}', 'view')->name('platform.view');

        Route::post('/master/platform/save', 'save')->name('platform.save');
        Route::post('/master/platform/delete', 'delete')->name('platform.delete');

        Route::get('/master/platform/list', 'list')->name('platform.list'); // ajax

    });

    Route::controller(AplikasiPendukungController::class)->group(function () {
        Route::get('/master/aplikasi-pendukung', 'index')->name('aplikasi-pendukung');
        Route::get('/master/aplikasi-pendukung/add', 'add')->name('aplikasi-pendukung.add');
        Route::get('/master/aplikasi-pendukung/view/{id}', 'view')->name('aplikasi-pendukung.view');

        Route::post('/master/aplikasi-pendukung/save', 'save')->name('aplikasi-pendukung.save');
        Route::post('/master/aplikasi-pendukung/delete', 'delete')->name('aplikasi-pendukung.delete');

        Route::get('/master/aplikasi-pendukung/list', 'list')->name('aplikasi-pendukung.list'); // ajax

    });
    Route::controller(RoleController::class)->group(function() {
        Route::get('/master/role', 'index')->name('role');
        Route::get('/master/role/list', 'list')->name('role.list');
        Route::get('/master/role/list-menu', 'listMenu')->name('role.list-menu');
        Route::get('/master/role/view/{id}', 'view')->name('role.view');
        Route::post('/master/role/update', 'updateAkses')->name('role.update-akses');
    });

    Route::controller(JenisBarangController::class)->group(function () {
        Route::get('/master/jenis-barang', 'index')->name('jenis-barang');
        Route::get('/master/jenis-barang/view/{id}', 'view')->name('jenis-barang.view');

        Route::get('/master/jenis-barang/list', 'list')->name('jenis-barang.list'); // ajax
        Route::get('/master/jenis-barang/detail-barang', 'detailBarang')->name('jenis-barang.detail-barang'); // ajax
    });

    Route::controller(JabatanController::class)->group(function () {
        Route::get('/master/jabatan', 'index')->name('jabatan');
        Route::get('/master/jabatan/add', 'add')->name('jabatan.add');
        Route::get('/master/jabatan/view/{id}', 'view')->name('jabatan.view');

        Route::post('/master/jabatan/save', 'save')->name('jabatan.save');
        Route::post('/master/jabatan/delete', 'delete')->name('jabatan.delete');

        Route::get('/master/jabatan/list', 'list')->name('jabatan.list'); // ajax

    });

    Route::controller(JenisPerusahaanController::class)->group(function () {
        Route::get('/master/perusahaan', 'index')->name('perusahaan');
        Route::get('/master/perusahaan/add', 'add')->name('perusahaan.add');
        Route::get('/master/perusahaan/view/{id}', 'view')->name('perusahaan.view');

        Route::post('/master/perusahaan/save', 'save')->name('perusahaan.save');
        Route::post('/master/perusahaan/delete', 'delete')->name('perusahaan.delete');

        Route::get('/master/perusahaan/list', 'list')->name('perusahaan.list'); // ajax

    });

    Route::controller(ManagementFeeController::class)->group(function () {
        Route::get('/master/management-fee', 'index')->name('management-fee');
        Route::get('/master/management-fee/add', 'add')->name('management-fee.add');
        Route::get('/master/management-fee/view/{id}', 'view')->name('management-fee.view');

        Route::post('/master/management-fee/save', 'save')->name('management-fee.save');
        Route::post('/master/management-fee/delete', 'delete')->name('management-fee.delete');

        Route::get('/master/management-fee/list', 'list')->name('management-fee.list'); // ajax

    });

    Route::controller(TopController::class)->group(function () {
        Route::get('/master/top', 'index')->name('top');
        Route::get('/master/top/add', 'add')->name('top.add');
        Route::get('/master/top/view/{id}', 'view')->name('top.view');

        Route::post('/master/top/save', 'save')->name('top.save');
        Route::post('/master/top/delete', 'delete')->name('top.delete');

        Route::get('/master/top/list', 'list')->name('top.list'); // ajax
    });

    Route::controller(JenisVisitController::class)->group(function () {
        Route::get('/master/jenis-visit', 'index')->name('jenis-visit');
        Route::get('/master/jenis-visit/add', 'add')->name('jenis-visit.add');
        Route::get('/master/jenis-visit/view/{id}', 'view')->name('jenis-visit.view');

        Route::post('/master/jenis-visit/save', 'save')->name('jenis-visit.save');
        Route::post('/master/jenis-visit/delete', 'delete')->name('jenis-visit.delete');

        Route::get('/master/jenis-visit/list', 'list')->name('jenis-visit.list'); // ajax

    });

    Route::controller(SalaryRuleController::class)->group(function () {
        Route::get('/master/salary-rule', 'index')->name('salary-rule');
        Route::get('/master/salary-rule/add', 'add')->name('salary-rule.add');
        Route::get('/master/salary-rule/view/{id}', 'view')->name('salary-rule.view');

        Route::post('/master/salary-rule/save', 'save')->name('salary-rule.save');
        Route::post('/master/salary-rule/delete', 'delete')->name('salary-rule.delete');

        Route::get('/master/salary-rule/list', 'list')->name('salary-rule.list'); // ajax

    });

    Route::controller(StatusLeadsController::class)->group(function () {
        Route::get('/master/status-leads', 'index')->name('status-leads');

        Route::get('/master/status-leads/list', 'list')->name('status-leads.list'); // ajax

    });

    Route::controller(TunjanganController::class)->group(function () {
        Route::get('/master/tunjangan', 'index')->name('tunjangan');
        Route::get('/master/tunjangan/add', 'add')->name('tunjangan.add');
        Route::get('/master/tunjangan/view/{id}', 'view')->name('tunjangan.view');

        Route::post('/master/tunjangan/save', 'save')->name('tunjangan.save');
        Route::post('/master/tunjangan/delete', 'delete')->name('tunjangan.delete');

        Route::get('/master/tunjangan/list', 'list')->name('tunjangan.list'); // ajax

    });

    Route::controller(TunjanganJabatanController::class)->group(function () {
        Route::get('/master/tunjangan-jabatan', 'index')->name('tunjangan-jabatan');
        Route::get('/master/tunjangan-jabatan/add', 'add')->name('tunjangan-jabatan.add');
        Route::get('/master/tunjangan-jabatan/view/{id}', 'view')->name('tunjangan-jabatan.view');

        Route::post('/master/tunjangan-jabatan/save', 'save')->name('tunjangan-jabatan.save');
        Route::post('/master/tunjangan-jabatan/delete', 'delete')->name('tunjangan-jabatan.delete');

        Route::get('/master/tunjangan-jabatan/list', 'list')->name('tunjangan-jabatan.list'); // ajax
        Route::get('/master/tunjangan-jabatan/get-kebutuhan-detail', 'getKebutuhanDetail')->name('tunjangan-jabatan.get-kebutuhan-detail'); // ajax

    });

    Route::controller(BarangController::class)->group(function () {
        Route::get('/master/barang', 'index')->name('barang');
        Route::get('/master/barang/add', 'add')->name('barang.add');
        Route::get('/master/barang/view/{id}', 'view')->name('barang.view');

        Route::post('/master/barang/save', 'save')->name('barang.save');
        Route::post('/master/barang/delete', 'delete')->name('barang.delete');
        Route::get('/master/barang/list', 'list')->name('barang.list'); // ajax
        Route::get('/master/barang/template-import', 'templateImport')->name('barang.template-import');
        Route::get('/master/barang/import', 'import')->name('barang.import');
        Route::post('/master/barang/inquiry-import', 'inquiryImport')->name('barang.inquiry-import');
        Route::post('/master/barang/save-import', 'saveImport')->name('barang.save-import');

        Route::get('/master/barang/kaporlap', 'indexKaporlap')->name('barang.kaporlap');
        Route::get('/master/barang/list-kaporlap', 'listKaporlap')->name('barang.list-kaporlap'); // ajax

        Route::get('/master/barang/ohc', 'indexOhc')->name('barang.ohc');
        Route::get('/master/barang/list-ohc', 'listOhc')->name('barang.list-ohc'); // ajax

        Route::get('/master/barang/devices', 'indexDevices')->name('barang.devices');
        Route::get('/master/barang/list-devices', 'listDevices')->name('barang.list-devices'); // ajax

        Route::get('/master/barang/chemical', 'indexChemical')->name('barang.chemical');
        Route::get('/master/barang/list-chemical', 'listChemical')->name('barang.list-chemical'); // ajax
        Route::get('/master/barang/defaultqty/{id}', 'defaultQtyData')->name('barang.defaultqty.data');
        Route::post('/master/barang/defaultqty/save', 'saveDefaultQty')->name('barang.defaultqty.save');
        Route::post('/master/barang/defaultqty/delete', 'deleteDefaultQty')->name('barang.defaultqty.delete');
    });

    Route::controller(KebutuhanController::class)->group(function () {
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

    Route::controller(TimSalesController::class)->group(function () {
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


    Route::controller(TrainingController::class)->group(function () {
        Route::get('/master/training', 'index')->name('training');
        Route::get('/master/training/add', 'add')->name('training.add');
        Route::get('/master/training/view/{id}', 'view')->name('training.view');
        Route::post('/master/training/save', 'save')->name('training.save');

        Route::post('/master/training/delete', 'delete')->name('training.delete');

        Route::get('/master/training/list', 'list')->name('training.list'); // ajax
    });
     Route::controller(PositionController::class)->group(function() {
        Route::get('/master/position', 'index')->name('position');
        Route::get('/master/position/list', 'list')->name('position.list');
        Route::get('/master/position/add', 'add')->name('position.add');
        Route::get('/master/position/view/{id}', 'view')->name('position.view');
        Route::get('/master/position/requirement-list', 'requirementList')->name('requirement.list');
        Route::post('/master/position/save', 'save')->name('position.save');
        Route::post('/master/position/add-requirement', 'addRequirement')->name('requirement.add');
        Route::post('/master/position/requirement-edit', 'requirementEdit')->name('requirement.edit');
        Route::post('/master/position/requirement-delete', 'requirementDelete')->name('requirement.delete');
        Route::post('/master/position/edit/{id}', 'edit')->name('position.edit');
        Route::post('/master/position/delete/{id}', 'delete')->name('position.delete');
     });

    Route::controller(UmpController::class)->group(function () {
        Route::get('/master/ump', 'index')->name('ump');
        Route::get('/master/ump/add', 'add')->name('ump.add');
        Route::get('/master/ump/view/{id}', 'view')->name('ump.view');
        Route::post('/master/ump/save', 'save')->name('ump.save');

        Route::get('/master/ump/list', 'list')->name('ump.list'); // ajax
        Route::get('/master/ump/list-ump', 'listUmp')->name('ump.list-ump'); // ajax
    });

    Route::controller(UmkController::class)->group(function () {
        Route::get('/master/umk', 'index')->name('umk');
        Route::get('/master/umk/add', 'add')->name('umk.add');
        Route::get('/master/umk/view/{id}', 'view')->name('umk.view');
        Route::post('/master/umk/save', 'save')->name('umk.save');

        Route::get('/master/umk/list', 'list')->name('umk.list'); // ajax
        Route::get('/master/umk/list-umk', 'listUmk')->name('umk.list-umk'); // ajax
    });
    Route::controller(LoyaltyController::class)->group(function() {
        Route::get('/master/loyalty', 'index')->name('loyalty');
        Route::get('/master/loyalty/list','list')->name('loyalty.list'); // ajax
        Route::get('/master/loyalty/add', 'add')->name('loyalty.add');
        Route::get('/master/loyalty/view/{id}', 'view')->name('loyalty.view');
        Route::post('/master/loyalty/save', 'save')->name('loyalty.save');
        Route::post('/master/loyalty/update/{id}', 'update')->name('loyalty.update');
        Route::post('/master/loyalty/delete/{id}', 'delete')->name('loyalty.delete');

    });

    Route::controller(TrainingMateriController::class)->group(function () {
        Route::get('/master/training-materi', 'index')->name('training-materi');
        Route::get('/master/training-materi/add', 'add')->name('training-materi.add');
        Route::get('/master/training-materi/view/{id}', 'view')->name('training-materi.view');
        Route::post('/master/training-materi/save', 'save')->name('training-materi.save');

        Route::get('/master/training-materi/list', 'list')->name('training-materi.list'); // ajax
        Route::post('/master/training-materi/delete', 'delete')->name('training-materi.delete');

        Route::get('/master/training-materi/history', 'historyTrainingByMateri')->name('training-materi.history');
        // Route::get('/master/training-materi/list-training-materi', 'listUmk')->name('training-materi.list-umk'); // ajax
    });

    Route::controller(TrainingGadaHargaController::class)->group(function() {
        Route::get('/master/training-gada-harga', 'index')->name('training-gada-harga');
        Route::get('/master/training-gada-harga/add', 'add')->name('training-gada-harga.add');
        Route::get('/master/training-gada-harga/view/{id}', 'view')->name('training-gada-harga.view');
        Route::get('/master/training-gada-harga/history', 'historyTrainingByMateri')->name('training-gada-harga.history');

        Route::get('/master/training-gada-harga/list', 'list')->name('training-gada-harga.list'); // ajax
        Route::post('/master/training-gada-harga/delete', 'delete')->name('training-gada-harga.delete');
        Route::post('/master/training-gada-harga/save', 'save')->name('training-gada-harga.save');
    });

    Route::controller(TrainingGadaJadwalController::class)->group(function() {
        Route::get('/master/training-gada-jadwal', 'index')->name('training-gada-jadwal');
        Route::get('/master/training-gada-jadwal/add', 'add')->name('training-gada-jadwal.add');
        Route::get('/master/training-gada-jadwal/view/{id}', 'view')->name('training-gada-jadwal.view');

        Route::get('/master/training-gada-jadwal/list', 'list')->name('training-gada-jadwal.list'); // ajax
        Route::post('/master/training-gada-jadwal/delete', 'delete')->name('training-gada-jadwal.delete');
        Route::post('/master/training-gada-jadwal/save', 'save')->name('training-gada-jadwal.save');
    });

    Route::controller(TrainingDivisiController::class)->group(function () {
        Route::get('/master/training-divisi', 'index')->name('training-divisi');
        Route::get('/master/training-divisi/add', 'add')->name('training-divisi.add');
        Route::get('/master/training-divisi/view/{id}', 'view')->name('training-divisi.view');
        Route::post('/master/training-divisi/save', 'save')->name('training-divisi.save');
        Route::post('/master/training-divisi/delete', 'delete')->name('training-divisi.delete');
        Route::get('/master/training-divisi/list', 'list')->name('training-divisi.list'); // ajax
    });

    Route::controller(TrainingTrainerController::class)->group(function () {
        Route::get('/master/training-trainer', 'index')->name('training-trainer');
        Route::get('/master/training-trainer/add', 'add')->name('training-trainer.add');
        Route::get('/master/training-trainer/view/{id}', 'view')->name('training-trainer.view');
        Route::post('/master/training-trainer/save', 'save')->name('training-trainer.save');
        Route::post('/master/training-trainer/delete', 'delete')->name('training-trainer.delete');
        Route::get('/master/training-trainer/list', 'list')->name('training-trainer.list'); // ajax
    });

    Route::controller(TrainingAreaController::class)->group(function () {
        Route::get('/master/training-area', 'index')->name('training-area');
        Route::get('/master/training-area/add', 'add')->name('training-area.add');
        Route::get('/master/training-area/view/{id}', 'view')->name('training-area.view');
        Route::post('/master/training-area/save', 'save')->name('training-area.save');
        Route::post('/master/training-area/delete', 'delete')->name('training-area.delete');
        Route::get('/master/training-area/list', 'list')->name('training-area.list'); // ajax
    });

    Route::controller(TrainingClientController::class)->group(function () {
        Route::get('/master/training-client', 'index')->name('training-client');
        Route::get('/master/training-client/add', 'add')->name('training-client.add');
        Route::get('/master/training-client/view/{id}', 'view')->name('training-client.view');
        Route::post('/master/training-client/save', 'save')->name('training-client.save');
        Route::post('/master/training-client/delete', 'delete')->name('training-client.delete');
        Route::get('/master/training-client/list', 'list')->name('training-client.list'); // ajax
    });

    Route::controller(SdtTrainingController::class)->group(function () {

        Route::get('/sdt/sdt-training', 'index')->name('sdt-training');
        Route::get('/sdt/sdt-training/add', 'add')->name('sdt-training.add');
        Route::get('/sdt/sdt-training/view/{id}', 'view')->name('sdt-training.view');
        Route::get('/sdt/sdt-training/data-notification', 'dataNotification')->name('sdt-training.dataNotification');
        Route::get('/sdt/sdt-training/data-notification-table', 'dataNotificationTable')->name('sdt-training.dataNotificationTable');
        Route::post('/sdt/sdt-training/data-notification', 'saveNotification')->name('sdt-training.saveNotification');
        Route::post('/sdt/sdt-training/delete-notification', 'deleteNotification')->name('sdt-training.deleteNotification');
        Route::post('/sdt/sdt-training/penerima-notification', 'saveNotificationPenerima')->name('sdt-training.saveNotificationPenerima');

        Route::post('/sdt/sdt-training/save', 'save')->name('sdt-training.save');
        Route::post('/sdt/sdt-training/delete', 'delete')->name('sdt-training.delete');
        Route::post('/sdt/sdt-training/delete-trainer', 'deleteTrainer')->name('sdt-training.delete-trainer');
        Route::post('/sdt/sdt-training/add-client', 'addClient')->name('sdt-training.add-client');
        Route::post('/sdt/sdt-training/add-peserta', 'addPeserta')->name('sdt-training.add-peserta');
        Route::post('/sdt/sdt-training/add-trainer', 'addTrainer')->name('sdt-training.add-trainer');
        Route::post('/sdt/sdt-training/delete-peserta', 'deletePeserta')->name('sdt-training.delete-peserta');
        Route::post('/sdt/sdt-training/delete-gallery', 'deleteGallery')->name('sdt-training.delete-gallery');
        Route::post('/sdt/sdt-training/send-message', 'sendMessage')->name('sdt-training.send-message');
        Route::post('/sdt/sdt-training/save-message', 'saveMessage')->name('sdt-training.save-message');

        Route::post('/sdt/sdt-training/upload-image', 'uploadImage')->name('sdt-training.upload-image');

        Route::get('/sdt/sdt-training/data-galeri', 'dataGaleri')->name('sdt-training.data-galeri');
        Route::get('/sdt/sdt-training/client-peserta', 'clientpeserta')->name('sdt-training.client-peserta');
        Route::get('/sdt/sdt-training/data-trainer', 'dataTrainer')->name('sdt-training.data-trainer');
        Route::get('/sdt/sdt-training/list', 'list')->name('sdt-training.list');

        Route::get('/sdt/sdt-training/list-area', 'listArea')->name('sdt-training.list-area');
        Route::get('/sdt/sdt-training/list-client', 'listClient')->name('sdt-training.list-client');

    });

    Route::controller(TrainingGadaController::class)->group(function() {
        Route::get('/gada/training', 'index')->name('training-gada');
        Route::get('/gada/training/list', 'list')->name('training-gada.list');
        Route::get('/gada/training/list-log', 'listLog')->name('training-gada.listLog');
        Route::get('/gada/training/data-registrasi', 'dataRegistrasi')->name('training-gada.dataRegistrasi');
        Route::get('/gada/training/data-invoice', 'dataInvoice')->name('training-gada.dataInvoice');
        Route::get('/gada/training/generate-invoice/{id}', 'generateInvoice')->name('training-gada.generateInvoice');
        // Route::get('/gada/training/terbilang', 'terbilang')->name('training-gada.terbilang');

        Route::post('/gada/training/upload-bukti-bayar', 'uploadBuktiBayar')->name('training-gada.upload-bukti-bayar');
        Route::post('/gada/training/status', 'updateStatus')->name('training-gada.updateStatus');
    });

    Route::controller(TrainingGadaPembayaranController::class)->group(function() {
        Route::get('/gada/training-pembayaran', 'index')->name('training-gada-pembayaran');
        Route::get('/gada/training-pembayaran/list', 'list')->name('training-gada-pembayaran.list');
        // Route::get('/gada/training/list-log', 'listLog')->name('training-gada.listLog');
        // Route::get('/gada/training/data-registrasi', 'dataRegistrasi')->name('training-gada.dataRegistrasi');
        // Route::get('/gada/training/data-invoice', 'dataInvoice')->name('training-gada.dataInvoice');
        // Route::get('/gada/training/generate-invoice/{id}', 'generateInvoice')->name('training-gada.generateInvoice');
        // Route::get('/gada/training/terbilang', 'terbilang')->name('training-gada.terbilang');

        // Route::post('/gada/training/upload-bukti-bayar', 'uploadBuktiBayar')->name('training-gada.upload-bukti-bayar');
        // Route::post('/gada/training/status', 'updateStatus')->name('training-gada.updateStatus');
    });

    Route::controller(PurchaseController::class)->group(function() {
        Route::get('/purchase/purchase-request', 'purchaseRequestIndex')->name('purchase-request');
        Route::get('/purchase/purchase-request/add', 'purchaseRequestAdd')->name('purchase-request.add');
         Route::get('/purchase/purchase-request/list', 'purchaseRequestList')->name('purchase-request.list');
         Route::get('/purchase/purchase-request/list-barang', 'getListBarang')->name('purchase-request.list-barang');
         Route::get('/purchase/purchase-request/listPKS', 'cariNomorPKS')->name('purchase-request.list-PKS');
         Route::get('/purchase-request/print/{id}', 'printRequestPdf')->name('purchase-request.print');
         Route::get('/purchase/purchase-request/view/{id}', 'purchaseRequestView')->name('purchase-request.view');
         Route::post('/purchase/purchase-request/save', 'purchaseRequestSave')->name('purchase-request.save');
         Route::get('/purchase/purchase-order', 'purchaseOrderIndex')->name('purchase-order');
         Route::get('/purchase/purchase-order/add', 'purchaseOrderAdd')->name('purchase-order.add');
         Route::post('/purchase/purchase-order/save', 'purchaseOrderSave')->name('purchase-order.save');
         Route::get('/purchase/purchase-order/list', 'purchaseOrderList')->name('purchase-order.list');
         Route::get('/purchase/purchase-order/no-company', 'cariNomorRequest')->name('purchase-order.no-company');
         Route::get('/purchase/purchase-order/list-request', 'getRequestList')->name('purchase-order.listRequest');
         Route::get('/purchase-order/print/{id}', 'cetakOrderPdf')->name('purchase-order.print');
         Route::get('/purchase/purchase-order/view/{id}', 'purchaseOrderView')->name('purchase-order.view');
    });
    Route::controller(TrainingSiteController::class)->group(function () {
        Route::get('/sdt/training-site', 'index')->name('training-site');
        // Route::get('/master/training-client/add', 'add')->name('training-client.add');
        // Route::get('/master/training-client/view/{id}', 'view')->name('training-client.view');
        // Route::post('/master/training-client/save', 'save')->name('training-client.save');
        // Route::post('/master/training-client/delete', 'delete')->name('training-client.delete');
        Route::get('/sdt/training-site/list', 'list')->name('training-site.list'); // ajax
        Route::get('/sdt/training-site/history', 'historyTrainingByClient')->name('training-site.history');
    });

    Route::controller(MonitoringKontrakController::class)->group(function () {
        Route::get('/sales/monitoring-kontrak', 'index')->name('monitoring-kontrak');
        Route::get('/sales/monitoring-kontrak/list', 'list')->name('monitoring-kontrak.list');
        Route::get('/sales/monitoring-kontrak/view/{id}', 'view')->name('monitoring-kontrak.view');
        Route::post('/sales/monitoring-kontrak/terminate', 'terminate')->name('monitoring-kontrak.terminate');
        Route::get('/sales/monitoring-kontrak/index-terminate', 'indexTerminate')->name('monitoring-kontrak.index-terminate');
        Route::get('/sales/monitoring-kontrak/list-terminate', 'listTerminate')->name('monitoring-kontrak.list-terminate');

        Route::get('/sales/monitoring-kontrak/import', 'import')->name('monitoring-kontrak.import');
        Route::get('/sales/monitoring-kontrak/template-import', 'templateImport')->name('monitoring-kontrak.template-import');

        Route::post('/sales/monitoring-kontrak/inquiry-import', 'inquiryImport')->name('monitoring-kontrak.inquiry-import');
        Route::post('/sales/monitoring-kontrak/save-import', 'saveImport')->name('monitoring-kontrak.save-import');

        Route::get('/sales/monitoring-kontrak/add-issue/{id}', 'addIssue')->name('monitoring-kontrak.add-issue');
        Route::post('/sales/monitoring-kontrak/save-issue', 'saveIssue')->name('monitoring-kontrak.save-issue');
        Route::post('/sales/monitoring-kontrak/delete-issue', 'deleteIssue')->name('monitoring-kontrak.delete-issue');
        Route::post('/sales/monitoring-kontrak/delete-activity', 'deleteActivity')->name('monitoring-kontrak.delete-activity');

        Route::get('/sales/monitoring-kontrak/modal/list-site', 'listSite')->name('monitoring-kontrak.modal.list-site'); // ajax
    });

    Route::controller(PutusKontrakController::class)->group(function () {
        Route::get('/sales/putus-kontrak', 'index')->name('putus-kontrak');
        Route::get('/sales/putus-kontrak/list', 'list')->name('putus-kontrak.list');
        Route::get('/sales/putus-kontrak/view/{id}', 'view')->name('putus-kontrak.view');
        Route::get('/sales/putus-kontrak/add', 'add')->name('putus-kontrak.add');
        Route::get('/sales/putus-kontrak/add-putus-kontrak/{id}', 'addPutusKontrak')->name('putus-kontrak.add-putus-kontrak');
        Route::post('/sales/putus-kontrak/save', 'save')->name('putus-kontrak.save');
        Route::get('/sales/putus-kontrak/available-kontrak', 'availableKontrak')->name('putus-kontrak.available-kontrak'); // ajax

    });

    Route::controller(EntitasController::class)->group(function () {
        Route::get('/setting/entitas', 'index')->name('entitas');
        Route::get('/setting/entitas/view/{id}', 'view')->name('entitas.view');

        Route::post('/setting/entitas/save', 'save')->name('entitas.save');

        Route::get('/setting/entitas/list', 'list')->name('entitas.list'); // ajax

    });
     Route::controller(ProfileController::class)->group(function() {
        Route::get('/profile', 'index')->name('profile');
        Route::get('/profile/list-activity', 'listActivity')->name('profile.activities');
     });

    // LOG
    //NOTIFIKASI
    Route::controller(NotifikasiController::class)->group(function () {
        Route::get('/log/notifikasi', 'index')->name('notifikasi');
        Route::get('/log/notifikasi/list', 'list')->name('notifikasi.list'); // ajax
        Route::post('/log/notifikasi/read', 'read')->name('notifikasi.read');
    });

    Route::controller(WhatsappController::class)->group(function () {
        Route::get('/whatsapp/login', 'login')->name('whatsapp.login');
        Route::get('/whatsapp', 'index')->name('whatsapp');
        Route::get('/whatsapp/list', 'list')->name('whatsapp.list');
        Route::post('/whatsapp/connectQr', 'connectQr')->name('whatsapp.connectQr');
        Route::post('/whatsapp/connectStatus', 'connectStatus')->name('whatsapp.connectStatus');
        Route::post('/whatsapp/message', 'message')->name('whatsapp.message');
        Route::post('/whatsapp/sendMessage', 'sendMessage')->name('whatsapp.sendMessage');
    });

    Route::get('/search', [DashboardController::class, 'search'])->name('dashboard.search');
});
