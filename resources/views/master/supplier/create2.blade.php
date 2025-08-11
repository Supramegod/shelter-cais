@extends('layouts.master')
@section('title', 'Receiving Note')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Default -->
        <div class="row">
            <!-- Vertical Wizard -->
            <div class="col-12 mb-4">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="text-center">Form Receiving Note Baru</span>
                            <span class="text-center">
                                <button class="btn btn-secondary waves-effect @if(old('id') == null) d-none @endif"
                                    type="button" id="btn-lihat-po">
                                    <span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat No
                                    Purchase Order
                                </button>&nbsp;&nbsp;&nbsp;&nbsp;
                                <span>{{$now}}</span></span>

                        </div>
                    </h5>
                    <form id="form-receiving-note" class="card-body overflow-hidden" action="{{route('supplier.saveRn')}}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="account-details-1" class="content active">
                            <div id="account-details-1" class="content active">
                                <div class="content-header mb-5 text-center">
                                    <h4 class="mb-0">Receiving Note</h4>
                                    <h4>Pilih Purchase Order Untuk Dijadikan Receiving Note</h4>
                                </div>
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="row mb-3">
                                    <input type="hidden" name="kode_pr" id="kode_pr" value="{{ old('kode_pr') }}">
                                    <label class="col-sm-2 col-form-label text-sm-end"> No
                                        Purchase Request <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <button id="btn-modal" class="btn btn-info waves-effect " type="button"><span
                                                class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari
                                            Purchase Request</button>
                                    </div>
                                </div>
                                {{-- ... Bagian sebelumnya tetap ... --}}

                                <div class="row mb-3">
                                    {{-- Pastikan $po ada sebelum mengakses properti --}}
                                    <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_terbit">Tanggal Terbit
                                        <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <input type="date" id="tanggal_terbit" name="tanggal_terbit" class="form-control"
                                            value="{{ old('tanggal_terbit', date('Y-m-d')) }}">
                                    </div>
                                    <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
                                    <div class="col-sm-4">
                                        {{-- Akses properti dengan ternary operator --}}
                                        <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                                            value="{{ old('nama_perusahaan') }}" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label text-sm-end">Sales</label>
                                    <div class="col-sm-4">
                                        {{-- Akses properti dengan ternary operator --}}
                                        <input type="text" id="sales" name="sales" value="{{ old('sales') }}"
                                            class="form-control" readonly>
                                    </div>
                                </div>

                                {{-- ... Bagian selanjutnya tetap ... --}}
                                <br>
                                <div class="content-header mb-3 text-center">
                                    <h4>List Barang</h4>
                                </div>
                                <div id="d-table-po" class="row mb-3">
                                    <div class="table-responsive overflow-hidden table-po">
                                        <table id="table-po" class="dt-column-search table w-100 table-hover"
                                            style="text-wrap: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="check-all-items" class="form-check-input"
                                                            style="transform: scale(1.5); margin-right: 8px;" />
                                                    </th>
                                                    <th>No Purchase Order</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jenis Barang</th>
                                                    <th>Stok Barang</th>
                                                    <th>jumlah</th>
                                                    <th>Merk</th>
                                                    <th>DiBuat Taggal</th>
                                                    <th>DiBuat Oleh</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-pr">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 d-flex flex-row-reverse">
                                        <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20"
                                            style="color:white">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Receiving
                                                Notes</span>
                                            <i class="mdi mdi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        <hr class="container-m-nx mb-5" />
    </div>
    <!--/ Content -->
    <div class="modal fade" id="modal-po" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Purchase Order</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-nomor-pr">
                            <table id="table-nomor-pr" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nomor Purchase Request</th>
                                        <th class="text-center">Perusahaan</th>
                                        <th class="text-center">Wilayah</th>
                                        <th class="text-center">Sales</th>
                                        <th class="text-center">Jenis Barang</th>
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

        $('#btn-modal').on('click', function () {
            $('#modal-po').modal('show');

        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#table-nomor-pr').on('click', 'tr', function () {
            $('#modal-po').modal('hide');
            var data = isiTabel.row(this).data();
            $('#sales').val(data.sales);
            $('#nama_perusahaan').val(data.perusahaan);
            $('#kode_pr').val(data.nomor_pr); // Simpan ke hidden input
            fetchpo(data.nomor_pr); // Kirim ke fungsi fetchpo

        });
        let dt_filter_table = $('.dt-column-search');

        let isiTabel = $('#table-nomor-pr').DataTable({
            "initComplete": function (settings, json) {
                $("#table-nomor-pr").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "bDestroy": true,
            "iDisplayLength": 10,
            'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('supplier.get-purchase-request-list') }}",
                data: function (d) {

                }
            },
            "order": [
                [0, 'desc']
            ],
            columns: [
                { data: 'nomor_pr', name: 'nomor_pr', className: 'text-center' },
                { data: 'perusahaan', name: 'perusahaan', className: 'text-center' },
                { data: 'wilayah', name: 'wilayah', className: 'text-center' },
                { data: 'sales', name: 'sales', className: 'text-center' },
                { data: 'jenis_barang', name: 'jenis_barang', className: 'text-center' },
            ]
            //  "language": datatableLang,
        });
        $('#check-all-items').on('change', function () {
            $('.item-checkbox').prop('checked', this.checked);
        });

        $('.item-checkbox').on('change', function () {
            $('#check-all-items').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox').length);
        });


        function fetchpo(kode_pr) {
            $('#tbody-pr').html(
                '<tr><td colspan="5" class="text-center">Loading data...</td></tr>'
            );
            $.ajax({
                url: '{{ route('supplier.get-barang-by-pr') }}',
                type: 'GET',
                data: {
                    kode_pr: kode_pr
                },
                success: function (data) {
                    $('#tbody-pr').empty();

                    if (!data || data.length === 0) {
                        $('#tbody-pr').append(
                            '<tr><td colspan="5" class="text-center">Data tidak ditemukan</td></tr>'
                        );
                        return;
                    }

                    $.each(data, function (key, value) {
                        $('#tbody-pr').append(
                            '<tr>' +
                            '<td>' +
                            '<input type="checkbox" name="pr_ids[]" value="' + value.id +
                            '" class="form-check-input item-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
                            '</td>' +
                            '<td>' + (value.nomor_pr ?? '') + '</td>' +
                            '<td>' + value.nama_barang + '</td>' +
                            '<td>' + (value.jenis_barang ?? '') + '</td>' +
                            '<td>' +
                            (value.stok_barang < value.qty
                                ? '<span class="badge bg-danger">' + value.stok_barang + '</span>'
                                : value.stok_barang
                            ) +
                            '</td>' +
                            '<td>' +
                            '<input type="number" name="qty[' + value.id + ']" value="' + value.qty + '" min="1" class="form-control" style="width: 80px;" required>' +
                            '</td>' +
                            '<td>' + (value.merk ?? '') + '</td>' +
                            '<td>' + (value.tanggal_cetak ?? '') + '</td>' +
                            '<td>' + (value.created_by ?? '') + '</td>' +
                            '</tr>'

                        );
                    });
                },
                error: function () {
                    Swal.fire('Gagal mengambil data list barang', '', 'error');
                }
            });
        };


        $('form').bind("keypress", function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#btn-submit').on('click', function (e) {
            e.preventDefault(); // Mencegah form auto-submit

            let form = $('#form-receiving-note');
            let msg = "";

            let obj = form.serializeArray();

            // Jika ingin validasi misalnya harus pilih barang:
            let checked = $('input[name="pr_ids[]"]:checked').length;
            if (checked === 0) {
                msg += "Minimal pilih satu barang dari list PR!<br>";
            }

            if (msg === "") {
                form.submit();
            } else {
                Swal.fire({
                    title: "Peringatan",
                    html: msg,
                    icon: "warning"
                });
            }
        });


    </script>
@endsection