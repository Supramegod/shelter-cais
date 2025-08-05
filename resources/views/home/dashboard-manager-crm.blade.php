@extends('layouts.master')
@section('title','Dashboard Manager CRM')

@section('pageStyle')
<style>
  .card-highlightable {
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .card-highlightable:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.15);
  }

  .active-card {
    border: 2px solid #007bff !important;
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.5) !important;
    transform: scale(1.02);
  }

  .nowrap {
    white-space: nowrap;
}
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-12 mb-1">
        <h4 class="fw-bold py-3 mb-1">Verifikasi Kontrak</h4>
      </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100 card-highlightable"
             onclick="openNormalDataTableSection('belum-quotation', 'KONTRAK BELUM ADA QUOTATION', this, 'container-detail-1')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-file-document-outline mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{ $countKontrakBelumAdaQuotation }}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Belum Ada Quotation</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100 card-highlightable"
             onclick="openNormalDataTableSection('belum-checklist', 'KONTRAK BELUM ISI CHECKLIST', this, 'container-detail-1')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-format-list-checks mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{ $countKontrakBelumIsiChecklist }}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Belum Isi Checklist</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100 card-highlightable"
             onclick="openNormalDataTableSection('belum-upload-pks', 'KONTRAK BELUM UPLOAD PKS', this, 'container-detail-1')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-upload mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{ $countKontrakBelumUploadPks }}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Belum Upload PKS</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100 card-highlightable"
             onclick="openNormalDataTableSection('siap-diaktifkan', 'KONTRAK SIAP DIAKTIFKAN', this, 'container-detail-1')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-success">
                    <i class="mdi mdi-check-circle-outline mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{ $countKontrakSiapDiaktifkan }}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Siap Diaktifkan</p>
            </div>
            </div>
        </div>
    </div>
    <div id="container-detail-1" class="my-4"></div>

    <div class="row">
        <div class="col-12 mb-1">
            <h4 class="fw-bold py-3 mb-1">Monitoring Kontrak</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100 card-highlightable"
             onclick="openNormalDataTableSection('siap-terminate', 'KONTRAK SIAP DI TERMINATE', this, 'container-detail-2')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-close-circle-outline mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$countKontrakSiapTerminated}}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Siap Di Terminate</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100 card-highlightable"
             onclick="openNormalDataTableSection('berakhir-1-bulan', 'KONTRAK BERAKHIR DALAM 1 BULAN', this, 'container-detail-2')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-calendar-alert mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$countKontrakKurangDari1Bulan}}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Berakhir Dalam 1 Bulan</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100 card-highlightable"
             onclick="openNormalDataTableSection('berakhir-3-bulan', 'KONTRAK BERAKHIR DALAM 3 BULAN', this, 'container-detail-2')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-calendar-clock mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$countKontrakKurangDari3Bulan}}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Berakhir Dalam 3 Bulan</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100 card-highlightable"
             onclick="openNormalDataTableSection('berjalan', 'KONTRAK BERJALAN', this, 'container-detail-2')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-success">
                    <i class="mdi mdi-play-circle-outline mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$countKontrakAktif}}</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Berjalan</p>
            </div>
            </div>
        </div>
    </div>
    <div id="container-detail-2" class="my-4"></div>
    <div class="row">
        <div class="col-12 mb-1">
            <h4 class="fw-bold py-3 mb-1">Monitoring PIC</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100 card-highlightable"
                 onclick="openNormalDataTableSection('belum-crm', 'BELUM ADA CRM', this, 'container-detail-3')">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="mdi mdi-account-off mdi-20px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $countBelumAdaCrm ?? 0 }}</h4>
                    </div>
                    <p class="mb-0 text-heading">Belum Ada CRM</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100 card-highlightable"
                 onclick="openNormalDataTableSection('belum-ro', 'BELUM ADA RO', this, 'container-detail-3')">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="mdi mdi-account-question mdi-20px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $countBelumAdaRo ?? 0 }}</h4>
                    </div>
                    <p class="mb-0 text-heading">Belum Ada RO</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100 card-highlightable"
                 onclick="openNormalDataTableSection('belum-pic-customer', 'BELUM ADA PIC CUSTOMER', this, 'container-detail-3')">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="mdi mdi-account-alert mdi-20px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $countBelumAdaPicCustomer ?? 0 }}</h4>
                    </div>
                    <p class="mb-0 text-heading">Belum Ada PIC Customer</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100 card-highlightable"
                 onclick="openNormalDataTableSection('pic-lengkap', 'PIC LENGKAP', this, 'container-detail-3')">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="mdi mdi-account-check mdi-20px"></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0 display-6">{{ $countPicLengkap ?? 0 }}</h4>
                    </div>
                    <p class="mb-0 text-heading">PIC Lengkap</p>
                </div>
            </div>
        </div>
    </div>
    <div id="container-detail-3" class="my-4"></div>
</div>
@endsection

@section('pageScript')
<script>
function openNormalDataTableSection(id, title, element,container) {
    // Hilangkan highlight dari semua card
    $('.card-highlightable').removeClass('active-card');

    // Tambahkan ke yang dipilih
    $(element).addClass('active-card');

    // Tampilkan loading dulu
    $(`#${container}`).html(`
        <div class="text-center my-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Memuat data ${title}...</p>
        </div>
    `);

    setTimeout(function() {
        if (id === 'belum-quotation') {
            let data = @json($listKontrakBelumAdaQuotation);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="table-belum-quotation" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th>Aksi</th>
                        <th>Nomor</th>
                        <th>Nama Perusahaan</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan</th>
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th>Kategori HC</th>
                        <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-quotation').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'belum-checklist') {
            let data = @json($listKontrakBelumIsiChecklist);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="table-belum-checklist" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th>Aksi</th>
                        <th>Nomor</th>
                        <th>Nama Perusahaan</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan</th>
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th>Kategori HC</th>
                        <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-checklist').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'belum-upload-pks') {
            let data = @json($listKontrakBelumUploadPks);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="table-belum-upload-pks" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th>Aksi</th>
                        <th>Nomor</th>
                        <th>Nama Perusahaan</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan</th>
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th>Kategori HC</th>
                        <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-upload-pks').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'siap-diaktifkan') {
            let data = @json($listKontrakSiapDiaktifkan);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="table-siap-diaktifkan" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th>Aksi</th>
                        <th>Nomor</th>
                        <th>Nama Perusahaan</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan</th>
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th>Kategori HC</th>
                        <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-siap-diaktifkan').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'siap-terminate') {
            let data = @json($listKontrakSiapTerminated);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive">
                    <table id="table-siap-terminate" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th>Aksi</th>
                        <th>Nomor</th>
                        <th>Nama Perusahaan</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan</th>
                        <th>Provinsi</th>
                        <th>Kota</th>
                        <th>Kategori HC</th>
                        <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-siap-terminate').DataTable({
                data: data,
                columns: [
                    { data: 'aksi', className: 'text-center nowrap' },
                    { data: 'nomor', className: 'text-center nowrap' },
                    { data: 'nama_perusahaan', className: 'text-center nowrap' },
                    { data: 'cabang', className: 'text-center nowrap' },
                    { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                    { data: 'layanan', className: 'text-center nowrap' },
                    { data: 'bidang_usaha', className: 'text-center nowrap' },
                    { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                    { data: 'provinsi', className: 'text-center nowrap' },
                    { data: 'kota', className: 'text-center nowrap' },
                    { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                    { data: 'created_by', className: 'text-center nowrap' }
                ],
                scrollX: true,
                iDisplayLength: 10,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...'
                }
            });
        } else if (id === 'berakhir-1-bulan') {
            let data = @json($listKontrakKurangDari1Bulan);
            let tableHtml = `
            <div class="card">
            <div class="card-header">
            <h5 class="mb-0">${title}</h5>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="table-berakhir-1-bulan" class="table table-bordered table-striped" style="width:100%">
                <thead>
                <tr>
                <th>Aksi</th>
                <th>Nomor</th>
                <th>Nama Perusahaan</th>
                <th>Cabang</th>
                <th>Alamat</th>
                <th>Layanan</th>
                <th>Bidang Usaha</th>
                <th>Jenis Perusahaan</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Kategori HC</th>
                <th>Created By</th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>
            </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-berakhir-1-bulan').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'berakhir-3-bulan') {
            let data = @json($listKontrakKurangDari3Bulan);
            let tableHtml = `
            <div class="card">
            <div class="card-header">
            <h5 class="mb-0">${title}</h5>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="table-berakhir-3-bulan" class="table table-bordered table-striped" style="width:100%">
                <thead>
                <tr>
                <th>Aksi</th>
                <th>Nomor</th>
                <th>Nama Perusahaan</th>
                <th>Cabang</th>
                <th>Alamat</th>
                <th>Layanan</th>
                <th>Bidang Usaha</th>
                <th>Jenis Perusahaan</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Kategori HC</th>
                <th>Created By</th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>
            </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-berakhir-3-bulan').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        } else if (id === 'berjalan') {
            let data = @json($listKontrakAktif);
            let tableHtml = `
            <div class="card">
            <div class="card-header">
            <h5 class="mb-0">${title}</h5>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="table-kontrak-aktif" class="table table-bordered table-striped" style="width:100%">
                <thead>
                <tr>
                <th>Aksi</th>
                <th>Nomor</th>
                <th>Nama Perusahaan</th>
                <th>Cabang</th>
                <th>Alamat</th>
                <th>Layanan</th>
                <th>Bidang Usaha</th>
                <th>Jenis Perusahaan</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Kategori HC</th>
                <th>Created By</th>
                </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>
            </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-kontrak-aktif').DataTable({
            data: data,
            columns: [
                { data: 'aksi', className: 'text-center nowrap' },
                { data: 'nomor', className: 'text-center nowrap' },
                { data: 'nama_perusahaan', className: 'text-center nowrap' },
                { data: 'cabang', className: 'text-center nowrap' },
                { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                { data: 'layanan', className: 'text-center nowrap' },
                { data: 'bidang_usaha', className: 'text-center nowrap' },
                { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                { data: 'provinsi', className: 'text-center nowrap' },
                { data: 'kota', className: 'text-center nowrap' },
                { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                { data: 'created_by', className: 'text-center nowrap' }
            ],
            scrollX: true,
            iDisplayLength: 10,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            }
            });
        }
        else if (id === 'belum-crm') {
            let data = @json($listBelumAdaCrm);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-belum-crm" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Nomor</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Cabang</th>
                                    <th>Alamat</th>
                                    <th>Layanan</th>
                                    <th>Bidang Usaha</th>
                                    <th>Jenis Perusahaan</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Kategori HC</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-crm').DataTable({
                data: data,
                columns: [
                    { data: 'aksi', className: 'text-center nowrap' },
                    { data: 'nomor', className: 'text-center nowrap' },
                    { data: 'nama_perusahaan', className: 'text-center nowrap' },
                    { data: 'cabang', className: 'text-center nowrap' },
                    { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                    { data: 'layanan', className: 'text-center nowrap' },
                    { data: 'bidang_usaha', className: 'text-center nowrap' },
                    { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                    { data: 'provinsi', className: 'text-center nowrap' },
                    { data: 'kota', className: 'text-center nowrap' },
                    { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                    { data: 'created_by', className: 'text-center nowrap' }
                ],
                scrollX: true,
                iDisplayLength: 10,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...'
                }
            });
        } else if (id === 'belum-ro') {
            let data = @json($listBelumAdaRo);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-belum-ro" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Nomor</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Cabang</th>
                                    <th>Alamat</th>
                                    <th>Layanan</th>
                                    <th>Bidang Usaha</th>
                                    <th>Jenis Perusahaan</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Kategori HC</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-ro').DataTable({
                data: data,
                columns: [
                    { data: 'aksi', className: 'text-center nowrap' },
                    { data: 'nomor', className: 'text-center nowrap' },
                    { data: 'nama_perusahaan', className: 'text-center nowrap' },
                    { data: 'cabang', className: 'text-center nowrap' },
                    { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                    { data: 'layanan', className: 'text-center nowrap' },
                    { data: 'bidang_usaha', className: 'text-center nowrap' },
                    { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                    { data: 'provinsi', className: 'text-center nowrap' },
                    { data: 'kota', className: 'text-center nowrap' },
                    { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                    { data: 'created_by', className: 'text-center nowrap' }
                ],
                scrollX: true,
                iDisplayLength: 10,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...'
                }
            });
        } else if (id === 'belum-pic-customer') {
            let data = @json($listBelumAdaPicCustomer);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-belum-pic-customer" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Nomor</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Cabang</th>
                                    <th>Alamat</th>
                                    <th>Layanan</th>
                                    <th>Bidang Usaha</th>
                                    <th>Jenis Perusahaan</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Kategori HC</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-belum-pic-customer').DataTable({
                data: data,
                columns: [
                    { data: 'aksi', className: 'text-center nowrap' },
                    { data: 'nomor', className: 'text-center nowrap' },
                    { data: 'nama_perusahaan', className: 'text-center nowrap' },
                    { data: 'cabang', className: 'text-center nowrap' },
                    { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                    { data: 'layanan', className: 'text-center nowrap' },
                    { data: 'bidang_usaha', className: 'text-center nowrap' },
                    { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                    { data: 'provinsi', className: 'text-center nowrap' },
                    { data: 'kota', className: 'text-center nowrap' },
                    { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                    { data: 'created_by', className: 'text-center nowrap' }
                ],
                scrollX: true,
                iDisplayLength: 10,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...'
                }
            });
        } else if (id === 'pic-lengkap') {
            let data = @json($listPicLengkap);
            let tableHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">${title}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-pic-lengkap" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Nomor</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Cabang</th>
                                    <th>Alamat</th>
                                    <th>Layanan</th>
                                    <th>Bidang Usaha</th>
                                    <th>Jenis Perusahaan</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Kategori HC</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            `;
            $(`#${container}`).html(tableHtml);

            $('#table-pic-lengkap').DataTable({
                data: data,
                columns: [
                    { data: 'aksi', className: 'text-center nowrap' },
                    { data: 'nomor', className: 'text-center nowrap' },
                    { data: 'nama_perusahaan', className: 'text-center nowrap' },
                    { data: 'cabang', className: 'text-center nowrap' },
                    { data: 'alamat_perusahaan', className: 'text-center nowrap' },
                    { data: 'layanan', className: 'text-center nowrap' },
                    { data: 'bidang_usaha', className: 'text-center nowrap' },
                    { data: 'jenis_perusahaan', className: 'text-center nowrap' },
                    { data: 'provinsi', className: 'text-center nowrap' },
                    { data: 'kota', className: 'text-center nowrap' },
                    { data: 'kategori_sesuai_hc', className: 'text-center nowrap' },
                    { data: 'created_by', className: 'text-center nowrap' }
                ],
                scrollX: true,
                iDisplayLength: 10,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...'
                }
            });
        }
        else {
            $(`#${container}`).html('');
        }
        // Scroll ke bawah
        $('html, body').animate({
            scrollTop: $(`#${container}`).offset().top - 80
        }, 500);
    }, 300);
}
</script>
@endsection
