@extends('layouts.master')
@section('title','Dashboard General')
@section('pageStyle')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://pivottable.js.org/dist/pivot.css">
<script src="https://pivottable.js.org/dist/pivot.js"></script>
<script src="https://pivottable.js.org/dist/plotly_renderers.js"></script>
<style>
  .card {
    padding: 20px; /* Sesuaikan dengan kebutuhan */
    width: 100%; /* Menyesuaikan lebar dengan konten */
    box-sizing: border-box; /* Agar padding termasuk dalam ukuran total */
  }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100" id="kontrakSiapDiaktifkan" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-hari-ini') }}','KONTRAK SIAP DIAKTIFKAN')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-success">
                    <i class="mdi mdi-file-document-outline mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">0</h4>
                </div>
                <p class="mb-0 text-heading">Kontrak Siap Diaktifkan</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100" id="aktifitasSalesMingguIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-minggu-ini') }}','AKTIFITAS SALES MINGGU INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-finance mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">4</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Minggu Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100" id="aktifitasSalesBulanIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-bulan-ini') }}','AKTIFITAS SALES BULAN INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">5</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Bulan Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100" id="aktifitasSalesTahunIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-tahun-ini') }}','AKTIFITAS SALES TAHUN INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-info"
                    ><i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">6</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Tahun Ini</p>
            </div>
            </div>
        </div>
    </div> -->
    <div class="row">
      <div class="col-12 mb-1">
        <h4 class="fw-bold py-3 mb-1">DASHBOARD PKS</h4>
      </div>
    </div>
    <div class="row">
    <!-- Bar Charts -->
      <div class="col-xl-12 col-12 mb-4">
        <div class="card">
          <div class="card-header header-elements">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">PKS SIAP DIAKTIFKAN</h5>
                <button id="btn-aktifkan-site" class="btn btn-primary mb-3">
                    <i class="mdi mdi-check-circle-outline"></i> &nbsp; Aktifkan Site
                </button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive overflow-hidden table-data">
                <table id="table-data" class="dt-column-search table w-100 table-hover" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-center">
                                <input type="checkbox" id="check-all" />
                            </th>
                            <th class="text-center">Nomor</th>
                            <th class="text-center">Nama Perusahaan</th>
                            <th class="text-center">Nama Site</th>
                            <th class="text-center">Provinsi</th>
                            <th class="text-center">Kota</th>
                            <th class="text-center">Kebutuhan</th>
                            <th class="text-center">Awal Kontrak</th>
                            <th class="text-center">Akhir Kontrak</th>
                            <th class="text-center">Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@section('pageScript')
<script>
$(document).ready(function() {
    var table = $('#table-data').DataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        pageLength: 25,
        language: {
            loadingRecords: '&nbsp;',
            processing: 'Loading...'
        },
        ajax: {
            url: "{{ route('dashboard-pks-siap-aktif.list') }}",
            data: function (d) {
            // Add filter params if needed
            }
        },
        createdRow: function(row, data, dataIndex) {
            // You can color rows based on status_pks_id if needed
        },
        order: [[0, 'desc']],
        columns: [
            {
                data: 'id',
                name: 'id',
                visible: false,
                searchable: false
            },
            {
                data: 'check',
                name: 'check',
                className: 'text-center',
                orderable: false,
                searchable: false
            },
            {
                data: 'nomor',
                name: 'nomor',
                className: 'text-center'
            },
            {
                data: 'nama_site',
                name: 'nama_site',
                className: 'text-center'
            },
            {
                data: 'provinsi',
                name: 'provinsi',
                className: 'text-center'
            },
            {
                data: 'kota',
                name: 'kota',
                className: 'text-center'
            },
            {
                data: 'kebutuhan',
                name: 'kebutuhan',
                className: 'text-center'
            },
            {
                data: 'nomor_pks',
                name: 'nomor_pks',
                className: 'text-center'
            }
        ]
    });

    // Check all functionality
    $('#check-all').on('click', function() {
        var checked = $(this).is(':checked');
        $('.check-site').prop('checked', checked);
    });

    // Redraw checkboxes after table draw
    $('#table-data').on('draw.dt', function() {
        $('#check-all').prop('checked', false);
    });
});

$(document).ready(function() {
    $('#btn-aktifkan-site').on('click', function() {
        var selected = [];
        $('.check-site:checked').each(function() {
            selected.push($(this).val());
        });
        if(selected.length === 0) {
            alert('Pilih minimal satu site untuk diaktifkan.');
            return;
        }
        if(confirm('Aktifkan site terpilih?')) {
            $.ajax({
                url: "#",
                method: 'POST',
                data: {
                    ids: selected,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#table-data').DataTable().ajax.reload();
                    alert('Site berhasil diaktifkan.');
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        }
    });
});

</script>
@endsection

