@extends('layouts.master')
@section('title', 'Leads')
@section('pageStyle')
    <style>
        .dt-buttons {
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row row-sm mt-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                        <div class="col-md-6 text-left col-12 my-auto">
                            <h3 class="page-title">Leads</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Leads</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{route('leads')}}" method="GET">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tgl_dari" name="tgl_dari"
                                                    value="{{$tglDari}}">
                                                <label for="tgl_dari">Tanggal Dari</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai"
                                                    value="{{$tglSampai}}">
                                                <label for="tgl_sampai">Tanggal Sampai</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="branch" name="branch">
                                                    <option value="">- Semua Wilayah -</option>
                                                    @foreach($branch as $data)
                                                        <option value="{{$data->id}}" @if($request->branch == $data->id) selected
                                                        @endif>{{$data->name}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="branch">Wilayah</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="platform" name="platform">
                                                    <option value="">- Semua Sumber Leads -</option>
                                                    @foreach($platform as $data)
                                                        <option value="{{$data->id}}" @if($request->platform == $data->id)
                                                        selected @endif>{{$data->nama}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="platform">Sumber Leads</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="status" name="status">
                                                    <option value="">- Semua Status -</option>
                                                    @foreach($status as $data)
                                                        <option value="{{$data->id}}" @if($request->status == $data->id) selected
                                                        @endif>{{$data->nama}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="status">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-grid">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Button Actions Row - Diletakkan di atas tabs -->
                        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                            <div></div>
                            <div class="d-flex gap-2">
                                <!-- Button untuk Leads -->
                                <a href="{{route('leads.add')}}" class="btn btn-success waves-effect waves-light">
                                    <i class="mdi mdi-plus-circle me-1"></i> Tambah Leads
                                </a>

                                <!-- Button untuk Grup -->
                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                    onclick="$('#modalRekomendasi').modal('show');">
                                    <i class="mdi mdi-group-plus me-1"></i> Buat Grup Baru
                                </button>

                            </div>
                        </div>

                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">
                                        <i class="mdi mdi-table-large me-1"></i> Tabel Data Leads
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-top-profile" aria-controls="navs-top-profile"
                                        aria-selected="false">
                                        <i class="mdi mdi-group me-1"></i> Grup Perusahaan
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
                                <div class="table-responsive overflow-hidden table-data">
                                    <table id="table-data" class="dt-column-search table w-100 table-hover"
                                        style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">No. Leads</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Wilayah</th>
                                                <th class="text-center">Sales</th>
                                                <th class="text-center">Perusahaan</th>
                                                <th class="text-center">Telp. Perusahaan</th>
                                                <th class="text-center">Provinsi</th>
                                                <th class="text-center">Kota</th>
                                                <th class="text-center">Nama PIC</th>
                                                <th class="text-center">Telp. PIC</th>
                                                <th class="text-center">Email PIC</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Sumber Leads</th>
                                                <th class="text-center">Created By</th>
                                                <th class="text-center">Keterangan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- data table ajax --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                <div class="table-responsive overflow-hidden">
                                    <table id="groupsTable" class="table table-bordered table-sm dataTable-custom">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Nama Grup</th>
                                                <th class="text-center">Jumlah Perusahaan</th>
                                                <th class="text-center">Dibuat Oleh</th>
                                                <th class="text-center">Tanggal Dibuat</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Rekomendasi -->
        <div class="modal fade" id="modalRekomendasi" tabindex="-1" aria-labelledby="modalRekomendasiLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('leads.groupkan') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalRekomendasiLabel">
                                <i class="mdi mdi-group-plus me-2"></i>Rekomendasi Pengelompokan Perusahaan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Ketik nama grup untuk mencari perusahaan yang cocok untuk dikelompokkan bersama.
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label text-sm-end">Nama Grup</label>
                                <div class="col-sm-6">
                                    <input type="text" id="namaGrupInput" name="nama_grup_manual" class="form-control"
                                        placeholder="Ketik nama grup untuk cari..." required>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" class="btn btn-primary w-100" id="btnFilterRekomendasi">
                                        <i class="mdi mdi-magnify me-1"></i>Cari
                                    </button>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Pilih Perusahaan untuk Dikelompokkan</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label small" for="selectAll">Pilih Semua</label>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 400px;">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="50"><input type="checkbox" id="selectAllHeader"></th>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Kota</th>
                                                    <th>Kategori</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rekomendasi-body">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        <i class="mdi mdi-information-outline me-1"></i>
                                                        Silakan ketik nama grup untuk melihat saran perusahaan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check-circle me-1"></i>Simpan Pengelompokan
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="mdi mdi-close me-1"></i>Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    <script>
        @if(isset($success) || session()->has('success'))
            Swal.fire({
                title: 'Berhasil!',
                html: '{{$success}} {{session()->get('success')}}',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if(isset($error) || session()->has('error'))
            Swal.fire({
                title: 'Perhatian!',
                html: '{{$error}} {{session()->get('error')}}',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

        let dt_filter_table = $('.dt-column-search');

        // DataTable for Leads Table
        var table = $('#table-data').DataTable({
            scrollX: true,
            'processing': true,
            serverSide: true,
            'pageLength': 25,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('leads.list') }}",
                data: function (d) {
                    d.tgl_dari = $('#tgl_dari').val();
                    d.tgl_sampai = $('#tgl_sampai').val();
                    d.branch = $('#branch').find(":selected").val();
                    d.platform = $('#platform').find(":selected").val();
                    d.status = $('#status').find(":selected").val();
                },
            },
            "createdRow": function (row, data, dataIndex) {
                $('td', row).css('background-color', data.warna_background);
                $('td', row).css('color', data.warna_font);
            },
            "order": [
                [0, 'desc']
            ],
            columns: [
                { data: 'id', name: 'id', visible: false, searchable: false },
                { data: 'nomor', name: 'nomor', className: 'text-center' },
                { data: 'tgl', name: 'tgl', className: 'text-center' },
                { data: 'branch', name: 'branch', className: 'text-center' },
                { data: 'sales', name: 'sales', className: 'text-center' },
                { data: 'nama_perusahaan', name: 'nama_perusahaan', className: 'text-center' },
                { data: 'telp_perusahaan', name: 'telp_perusahaan', className: 'text-center' },
                { data: 'provinsi', name: 'provinsi', className: 'text-center' },
                { data: 'kota', name: 'kota', className: 'text-center' },
                { data: 'pic', name: 'pic', className: 'text-center' },
                { data: 'no_telp', name: 'no_telp', className: 'text-center' },
                { data: 'email', name: 'email', className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'platform', name: 'platform', className: 'text-center' },
                { data: 'created_by', name: 'created_by', className: 'text-center' },
                { data: 'notes', name: 'notes', className: 'text-center' },
                { data: 'aksi', name: 'aksi', width: "10%", orderable: false, searchable: false },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"f><"dt-action-buttons"B>>rtip',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-1"></i>Export',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV',
                            className: 'dropdown-item',
                        }, {
                            extend: 'excel',
                            text: '<i class="mdi mdi-file-document-outline me-1"></i>Excel',
                            className: 'dropdown-item',
                        }, {
                            extend: 'pdf',
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>PDF',
                            className: 'dropdown-item',
                            orientation: 'landscape',
                            customize: function (doc) {
                                doc.defaultStyle.fontSize = 9;
                            },
                        },
                    ]
                }
            ],
        });

        // DataTable for Groups Table
        var groupsTable = $('#groupsTable').DataTable({
            scrollX: true,
            'processing': true,
            serverSide: true,
            'pageLength': 25,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('leads.groups.list') }}",
            },
            "order": [
                [0, 'desc']
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_grup', name: 'nama_grup', className: 'text-center' },
                {
                    data: 'jumlah_perusahaan',
                    name: 'jumlah_perusahaan',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                { data: 'created_by', name: 'created_by', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'aksi', name: 'aksi', width: "15%", orderable: false, searchable: false },
            ],
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"f><"dt-action-buttons"B>>rtip',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-1"></i>Export Grup',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV',
                            className: 'dropdown-item',
                        }, {
                            extend: 'excel',
                            text: '<i class="mdi mdi-file-document-outline me-1"></i>Excel',
                            className: 'dropdown-item',
                        }, {
                            extend: 'pdf',
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>PDF',
                            className: 'dropdown-item',
                            orientation: 'landscape',
                            customize: function (doc) {
                                doc.defaultStyle.fontSize = 9;
                            },
                        },
                    ]
                }
            ],
        });

        // Event listener for tab clicks to reload the DataTables
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr("data-bs-target");
            if (target === '#navs-top-home') {
                table.columns.adjust().draw();
            } else if (target === '#navs-top-profile') {
                groupsTable.ajax.reload();
                groupsTable.columns.adjust().draw();
            }
        });

        // Modal functionality
        const namaGrupInput = document.getElementById('namaGrupInput');
        const btnFilterRekomendasi = document.getElementById('btnFilterRekomendasi');
        const rekomendasiBody = document.getElementById('rekomendasi-body');
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');

        // Search functionality
        btnFilterRekomendasi.addEventListener('click', function () {
            const query = namaGrupInput.value.trim();
            if (query === '') {
                rekomendasiBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-information-outline me-1"></i>Silakan ketik nama grup untuk melihat saran perusahaan.</td></tr>`;
                return;
            }


            // Show loading
            rekomendasiBody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><i class="mdi mdi-loading mdi-spin me-1"></i>Mencari perusahaan...</td></tr>`;

            fetch(`{{ route('leads.filter-rekomendasi') }}?nama_grup=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    rekomendasiBody.innerHTML = "";
                    if (!Array.isArray(data) || data.length === 0) {
                        rekomendasiBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-alert-circle-outline me-1"></i>Tidak ada perusahaan ditemukan untuk kata kunci "${query}".</td></tr>`;
                        return;
                    }

                    data.forEach(perusahaan => {
                        rekomendasiBody.innerHTML += `
                                <tr>
                                    <td><input type="checkbox" name="perusahaan_terpilih[]" value="${perusahaan.id}" checked class="form-check-input"></td>
                                    <td class="fw-medium">${perusahaan.nama_perusahaan}</td>
                                    <td>${perusahaan.kota || '-'}</td>
                                    <td><span class="badge bg-light-primary text-primary">${perusahaan.jenis_perusahan || '-'}</span></td>
                                </tr>
                            `;
                    });

                    // Update checkbox events
                    updateCheckboxEvents();
                })
                .catch(err => {
                    console.error('Gagal mengambil data:', err);
                    rekomendasiBody.innerHTML = `<tr><td colspan="4" class="text-danger text-center py-4"><i class="mdi mdi-alert-circle me-1"></i>Terjadi kesalahan saat memuat data.</td></tr>`;
                });
        });

        // Enter key support for search
        namaGrupInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnFilterRekomendasi.click();
            }
        });

        // Update checkbox events
        function updateCheckboxEvents() {
            const checkboxes = rekomendasiBody.querySelectorAll('input[type="checkbox"]');

            // Select all functionality
            [selectAll, selectAllHeader].forEach(selectAllBtn => {
                selectAllBtn.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    // Sync both select all checkboxes
                    [selectAll, selectAllHeader].forEach(btn => {
                        if (btn !== this) btn.checked = this.checked;
                    });
                });
            });

            // Individual checkbox change
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);

                    [selectAll, selectAllHeader].forEach(btn => {
                        btn.checked = allChecked;
                        btn.indeterminate = !allChecked && !noneChecked;
                    });
                });
            });
        }

        // Initialize checkbox events on modal show
        $('#modalRekomendasi').on('shown.bs.modal', function () {
            namaGrupInput.focus();
            updateCheckboxEvents();
        });

        // Clear form on modal hide
        $('#modalRekomendasi').on('hidden.bs.modal', function () {
            namaGrupInput.value = '';
            rekomendasiBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-information-outline me-1"></i>Silakan ketik nama grup untuk melihat saran perusahaan.</td></tr>`;
            [selectAll, selectAllHeader].forEach(btn => {
                btn.checked = false;
                btn.indeterminate = false;
            });
        });

        // Delete group functionality
        $(document).on('click', '.delete-btn', function () {
            const groupId = $(this).data('id');

            Swal.fire({
                title: 'Hapus Grup?',
                text: 'Grup yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger waves-effect waves-light',
                    cancelButton: 'btn btn-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + groupId).submit();
                }
            });
        });

        // Generate null kode function
        function generateNullKode() {
            Swal.fire({
                title: 'Generate Nomor Leads?',
                text: 'Sistem akan membuat nomor untuk leads yang belum memiliki nomor.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Generate!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light',
                    cancelButton: 'btn btn-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membuat nomor leads.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route("leads.generateNullKode") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect waves-light'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'btn btn-danger waves-effect waves-light'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses permintaan.',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-danger waves-effect waves-light'
                                },
                                buttonsStyling: false
                            });
                        });
                }
            });
        }
        // Event listener for row click
        $('#table-data').on('click', 'tbody tr', function () {
            let rdata = table.row(this).data();
            if (rdata.can_view) {
                window.location.href = "{{ route('leads') }}/view/" + rdata.id;
            } else {
                Swal.fire({
                    title: 'Pemberitahuan',
                    html: 'Anda tidak bisa melihat data ini',
                    icon: 'warning',
                    customClass: {
                        confirmButton: 'btn btn-warning waves-effect waves-light'
                    },
                    buttonsStyling: false
                });
            }
        });
    </script>
@endsection