@extends('layouts.master')
@section('title', 'Stok barang ')
@section('pageStyle')
    <style>
        .dt-buttons {
            width: 100%;
            margin-left: 10px;
        }

        /* Tabs - tanpa kotak */
        .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
            /* garis bawah abu lembut */
        }

        .nav-tabs .nav-item {
            margin-bottom: -2px;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            background-color: transparent;
            color: #4b5563;
            /* warna font abu gelap */
            font-weight: 500;
            padding: 10px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-tabs .nav-link:hover {
            color: #ea580c;
            /* orange hover */
            border-bottom: 2px solid #fdba74;
            /* orange muda hover */
            background-color: #fff7ed;
            /* latar orange sangat muda */
        }

        .nav-tabs .nav-link.active {
            color: #ea580c;
            /* orange aktif */
            border-bottom: 2px solid #ea580c;
            /* garis orange */
            font-weight: bold;
            background-color: #fff7ed;
            /* latar orange sangat muda */
            transform: translateY(-1px);
            /* sedikit lift */
            box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.1),
                0 2px 4px -1px rgba(249, 115, 22, 0.06);
            /* bayangan halus */
        }

        .nav-tabs .nav-link.active:hover {
            border-bottom: 2px solid #c2410c;
            /* orange lebih tua */
            background-color: #ffedd5;
            /* latar lebih terang */
        }

        /* Tab content */
        .tab-content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 0 0 8px 8px;
        }

        /* Select2 Styling - Kembali ke warna default */
        .select2-container--default .select2-selection--single {
            height: 58px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.375rem !important;
            background-color: #ffffff !important;
            /* putih */
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #94a3b8 !important;
            /* abu medium saat hover */
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #3b82f6 !important;
            /* biru saat fokus */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
            /* glow biru */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 56px !important;
            padding-left: 12px !important;
            color: #1e293b;
            /* warna font abu gelap */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 56px !important;
        }

        .select2-container--open .select2-dropdown--below {
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* bayangan netral */
        }

        /* Untuk form input tanggal */
        .form-control {
            background-color: #ffffff !important;
            /* putih */
            color: #1e293b;
            /* warna font abu gelap */
        }
    </style>
@endsection
@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Row -->
        <div class="row row-sm mt-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                        <div class="col-md-6 text-left col-12 my-auto">
                            <h3 class="page-title">Stok barang </h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Stok barang </li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <!-- Filter Form -->
                        <form id="filter-form">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="jenis_barang" name="jenis_barang">
                                                    <option value="">- Semua Jenis Barang -</option>
                                                    <!-- Options will be populated by AJAX -->
                                                </select>
                                                <label for="jenis_barang">Jenis Barang</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tanggal_dari"
                                                    name="tanggal_dari">
                                                <label for="tanggal_dari">Tanggal Dari</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tanggal_sampai"
                                                    name="tanggal_sampai">
                                                <label for="tanggal_sampai">Tanggal Sampai</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2" id="barang" name="barang"
                                                    data-placeholder="- Pilih Barang -" data-allow-clear="true">
                                                    <option value=""></option>
                                                </select>
                                                <label for="barang">Barang</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="d-grid">
                                            <button type="button" id="btn-filter"
                                                class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="mutasiTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="stok-per-barang-tab" data-bs-toggle="tab"
                                    data-bs-target="#stok-per-barang" type="button" role="tab"
                                    aria-controls="stok-per-barang" aria-selected="true">
                                    STOK PER BARANG
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="list-mutasi-tab" data-bs-toggle="tab"
                                    data-bs-target="#list-mutasi" type="button" role="tab" aria-controls="list-mutasi"
                                    aria-selected="false">
                                    LIST MUTASI
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="mutasiTabsContent">
                            <!-- Tab Stok Per Barang -->
                            <div class="tab-pane fade show active" id="stok-per-barang" role="tabpanel"
                                aria-labelledby="stok-per-barang-tab">
                                <div class="table-responsive overflow-hidden table-data">
                                    <table id="table-stok-barang" class="dt-column-search table w-100 table-hover"
                                        style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID Barang</th>
                                                <th class="text-center">Nama Barang</th>
                                                <th class="text-center">Jenis Barang</th>
                                                <th class="text-center">Stok</th>
                                                <th class="text-center">Satuan</th>
                                                <th class="text-center">Merk</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- data table ajax --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab List Mutasi -->
                            <div class="tab-pane fade" id="list-mutasi" role="tabpanel" aria-labelledby="list-mutasi-tab">
                                <div class="table-responsive overflow-hidden table-data">
                                    <table id="table-mutasi" class="dt-column-search table w-100 table-hover"
                                        style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">Transaksi</th>
                                                <th class="text-center">Ref ID</th>
                                                <th class="text-center">Nama Barang</th>
                                                <th class="text-center">Tanggal</th> <!-- Kolom baru -->
                                                <th class="text-center">Qty</th>
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
        </div>
        <!-- End Row -->
    </div>
    <!--/ Content -->
@endsection

@section('pageScript')
    <script>
        let table_stok_barang, table_mutasi;

        $(document).ready(function () {
            // Set default tanggal (1 bulan lalu - hari ini)
            function setDefaultDates() {
                const today = new Date();
                const todayFormatted = today.toISOString().split('T')[0];

                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(today.getMonth() - 1);
                const oneMonthAgoFormatted = oneMonthAgo.toISOString().split('T')[0];

                $('#tanggal_dari').val(oneMonthAgoFormatted);
                $('#tanggal_sampai').val(todayFormatted);
            }

            setDefaultDates();

            // Load filter options
            loadJenisBarang();
            initBarangSelect2();

            // Initialize DataTables
            initStokBarangTable();
            initMutasiTable();

            // Filter button click
            $('#btn-filter').click(function () {
                if (table_stok_barang) {
                    table_stok_barang.ajax.reload();
                }
                if (table_mutasi) {
                    table_mutasi.ajax.reload();
                }
            });

            // Tab switch event
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if ($(e.target).attr('aria-controls') === 'stok-per-barang') {
                    if (table_stok_barang) {
                        table_stok_barang.columns.adjust().draw();
                    }
                } else if ($(e.target).attr('aria-controls') === 'list-mutasi') {
                    if (table_mutasi) {
                        table_mutasi.columns.adjust().draw();
                    }
                }
            });
        });

        function loadJenisBarang() {
            $.ajax({
                url: "{{ route('mutasi-stok.get-jenis-barang') }}",
                type: 'GET',
                success: function (response) {
                    let options = '<option value="">- Semua Jenis Barang -</option>';
                    response.data.forEach(function (item) {
                        options += `<option value="${item.id}">${item.nama}</option>`;
                    });
                    $('#jenis_barang').html(options);
                }
            });
        }

        function initBarangSelect2() {
            $('#barang').select2({
                placeholder: '- Pilih Barang -',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('mutasi-stok.search-barang') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            jenis_barang: $('#jenis_barang').val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: `${item.nama_barang}` +
                                        (item.merk ? ` (${item.merk})` : '')
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: function (data) {
                    if (!data.id) return data.text;

                    return $(
                        '<div class="d-flex justify-content-between">' +
                        '<span>' + data.text + '</span>' +
                        '<span class="text-muted small">Cari...</span>' +
                        '</div>'
                    );
                }
            });

            $('#jenis_barang').on('change', function () {
                $('#barang').val(null).trigger('change');
            });
        }

        function initStokBarangTable() {
            table_stok_barang = $('#table-stok-barang').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('mutasi-stok.stok-barang-data') }}",
                    data: function (d) {
                        d.jenis_barang = $('#jenis_barang').val();
                        d.tanggal_dari = $('#tanggal_dari').val();
                        d.tanggal_sampai = $('#tanggal_sampai').val();
                        d.barang = $('#barang').val();
                    },
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: 'text-center'
                    },
                    {
                        data: 'jenis_barang',
                        name: 'jenis_barang',
                        className: 'text-center'
                    },
                    {
                        data: 'stok_barang',
                        name: 'stok_barang',
                        className: 'text-center'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        className: 'text-center'
                    },
                    {
                        data: 'merk',
                        name: 'merk',
                        className: 'text-center'
                    }
                ],

                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                // Perbaikan: Tombol export dan search sejajar
                dom: '<"row d-flex justify-content-between align-items-center"<"col-auto"f><"col-auto"B>>rtip',
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
                        text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                        buttons: [
                            {
                                extend: 'csv',
                                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            }, {
                                extend: 'excel',
                                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            }, {
                                extend: 'pdf',
                                text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                                className: 'dropdown-item',
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            }
                        ]
                    }
                ]
            });
        }

        function initMutasiTable() {
            table_mutasi = $('#table-mutasi').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('mutasi-stok.mutasi-data') }}",
                    data: function (d) {
                        d.jenis_barang = $('#jenis_barang').val();
                        d.tanggal_dari = $('#tanggal_dari').val();
                        d.tanggal_sampai = $('#tanggal_sampai').val();
                        d.barang = $('#barang').val();
                    },
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                    data: 'id',
                    name: 'id',
                    className: 'text-center'
                }, {
                    data: 'transaksi',
                    name: 'transaksi',
                    className: 'text-center'
                }, {
                    data: 'ref_id',
                    name: 'ref_id',
                    className: 'text-center'
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    className: 'text-center'
                }, {
                    // Kolom tanggal baru
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center',
                    render: function (data, type, row) {
                        // Format tanggal menjadi dd-mm-yyyy
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                }, {
                    data: 'qty',
                    name: 'qty',
                    className: 'text-center'
                }],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                // Perbaikan: Tombol export dan search sejajar
                dom: '<"row d-flex justify-content-between align-items-center"<"col-auto"f><"col-auto"B>>rtip',
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
                        text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                        buttons: [
                            {
                                extend: 'csv',
                                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            }, {
                                extend: 'excel',
                                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            }, {
                                extend: 'pdf',
                                text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                                className: 'dropdown-item',
                                orientation: 'landscape',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            }
                        ]
                    }
                ]
            });
        }

        @if(session()->has('success'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{session()->get('success')}}',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

        @if(session()->has('error'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{session()->get('error')}}',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
    </script>
@endsection