@extends('layouts.master')
@section('title', 'Purchase Order')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Default -->
        <div class="row">
            <!-- Vertical Wizard -->
            <div class="col-12 mb-4">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span class="text-center">Form Purchase Baru</span>

                        </div>
                    </h5>

                    <div id="account-details-1" class="content active">
                        <div class="content-header mb-5 text-center">
                            <h4 class="mb-0">Purchase Order</h4>

                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-1 col-form-label text-sm-end">Perusahaan</label>
                            <div class="col-sm-4">
                                <div class="position-relative">
                                    <select class="form-select" id="company" name="company">
                                        <option value="">Pilih Perusahaan</option>
                                        @foreach ($company as $perusahaan)
                                            <option value="{{ $perusahaan->perusahaan }}"
                                                @if ($request->perusahaan == $perusahaan->perusahaan) selected @endif>
                                                {{ $perusahaan->perusahaan }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end">Nomor Purchase Request</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" id="kode_pr" name="kode_pr" value="{{ old('kode_pr') }}"
                                        class="form-control rounded-end-0 @error('kode_pr') is-invalid @enderror">
                                    <button type="button" id="btn-modal-nomor"
                                        class="btn btn-primary  rounded-start-0">Cari
                                        Nomor</button>
                                </div>
                                @error('kode_pr')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <br>
                        <form class="card-body overflow-hidden" action="{{ route('purchase-order.save') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="purchase_request_id" id="purchase_request_id">
                            <div class="content-header mb-5 text-center">
                                <h4>List Barang</h4>
                            </div>

                            <ul class="nav nav-tabs mb-3" id="statusTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-status="all" data-bs-toggle="tab"
                                        type="button">All</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-status="open" data-bs-toggle="tab"
                                        type="button">Open</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-status="closed" data-bs-toggle="tab"
                                        type="button">Closed</button>
                                </li>
                            </ul>
                            <div class="row mb-3">
                                <div class="table-responsive overflow-hidden">
                                    <table class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>

                                                <th>
                                                    <input type="checkbox" id="check-all-listRequest"
                                                        class="form-check-input"
                                                        style="transform: scale(1.5); margin-right: 8px;" />
                                                </th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah Order</th>
                                                <th>Permintaan</th>
                                                <th>Stok Barang</th>
                                                <th>Satuan</th>
                                                <th>Merk</th>
                                                <th>Jenis Barang</th>

                                            </tr>
                                        </thead>
                                        <tbody id="tbody-listRequest">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 d-flex flex-row-reverse gap-3">
                                    <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20"
                                        style="color:white">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Purchase
                                            Order</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </button>
                                     <a href="{{ route('purchase-order') }}" class="btn btn-secondary waves-effect">Kembali</a>
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

    <!-- Modal Cari Nomor Purchase Request -->
    <div class="modal fade" id="modal-nomor-pr" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Nomor Purchase Request</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-nomor" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">Nomor Purchase Request</th>
                                        <th class="text-center">Jenis Barang</th>
                                        <th class="text-center">Tanggal dibuat</th>
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
        let statusTab = 'all';
        $(document).ready(function() {
            fetchListRequest();
        });
        $('#check-all-listRequest').on('change', function() {
            $('.listRequest-checkbox').prop('checked', this.checked);
        });

        $('.listRequest-checkbox').on('change', function() {
            $('#check-all-listRequest').prop('checked', $('.listRequest-checkbox:checked').length === $(
                    '.listRequest-checkbox')
                .length);
        });
          @if (session()->has('success'))

            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ session()->get('success') }}<br><br>Kode Purchase Order: <strong>{{ session('kode_po') }}</strong>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Cetak',
                cancelButtonText: 'Tutup',
                customClass: {
                    confirmButton: 'btn btn-success me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {

                    window.open("{{ route('purchase-order.print', ['id' => session('id')]) }}", "_blank");
                }
            });
        @endif
        $('#btn-modal-nomor').on('click', function() {
            if ($('#company').val() === "") {
                Swal.fire("Pilih perusahaan terlebih dahulu", "", "warning");
                return;
            }
            $('#modal-nomor-pr').modal('show');
            $('#table-nomor').DataTable().ajax.reload();
        });
        $('#table-nomor').on('click', 'tr', function() {
            $('#modal-nomor-pr').modal('hide');
            var data = isiTabel.row(this).data();
            $('#kode_pr').val(data.kode_pr);
            fetchListRequest();
        });

        $('#company, #kode_pr').on('change keyup', function() {

            if ($('#company').val().trim() !== '' && $('#kode_pr').val().trim() !== '') {
                fetchListRequest();
            }
        });
        $('#statusTabs button').on('click', function() {
            $('#statusTabs button').removeClass('active');
            $(this).addClass('active');
            statusTab = $(this).data('status');
            fetchListRequest()
        });


        let isiTabel = $('#table-nomor').DataTable({
            "initComplete": function(settings, json) {
                $("#table-nomor").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            "bDestroy": true,
            "iDisplayLength": 25,
            'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('purchase-order.no-company') }}",
                type: "GET",
                data: function(d) {
                    d.company = $('#company').find(':selected').val();
                },

            },
            "order": [
                [0, 'desc']
            ],
            columns: [{
                data: 'id',
                name: 'id',
                visible: false,
                searchable: false
            }, {
                data: 'kode_pr',
                name: 'kode_pr',
                className: 'text-center'
            }, {
                data: 'jenis_barang',
                name: 'jenis_barang',
                className: 'text-center'
            }, {
                data: 'tanggal_buat',
                name: 'tanggal_buat',
                className: 'text-center'
            }, {
                data: 'created_by',
                name: 'created_by',
                className: 'text-center'
            }],
            "language": datatableLang,
        });

        function fetchListRequest() {
            $('#tbody-listRequest').html(
                '<tr><td colspan="8" class="text-center">Loading data...</td></tr>'
            );
            $.ajax({
                url: '{{ route('purchase-order.listRequest') }}',
                type: 'GET',
                data: {
                    status: statusTab,
                    perusahaan: $('#company').val() ?? '',
                    kode_pr: $('#kode_pr').val() ?? ''

                },
                success: function(data) {
                    $('#purchase_request_id').val(data[0]?.purchase_request_id ?? '');
                    $('#tbody-listRequest').empty();

                    if (!data || data.length === 0) {
                        $('#tbody-listRequest').append(
                            '<tr><td colspan="8" class="text-center">Data tidak ditemukan</td></tr>'
                        );
                        return;
                    }

                    $.each(data, function(key, value) {
                        value.jumlah_po = (value.qty ?? 0) - value.stok_barang;

                        if (value.jumlah_po <= 0) {
                            value.jumlah_po = 0;
                        }
                          let rowClass = (value.is_open === 0) ? 'bg-danger-subtle text-dark' :
                            'bg-success-subtle text-dark';
                        $('#tbody-listRequest').append(
                            '<tr class="' + rowClass + '">' +
                            '<td>' +
                            '<input type="checkbox" name="listRequest_ids[]" value="' + value.id +
                            '" class="form-check-input listRequest-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
                            '</td>' +
                            '<td>' + value.nama_barang + '</td>' +
                            '<td>' +
                            '<input type="number" name="jumlah_po[' + value.id + ']" value="' +
                            value.jumlah_po +
                            '" class="form-control form-control-sm input-jumlah-po" data-id="' +
                            value.id + '" />' +
                            '</td>' +
                            '<td>' + (value.qty ?? '') + '</td>' +
                            '<td>' + (value.stok_barang ?? '0') + '</td>' +
                            '<td>' + (value.satuan ?? '') + '</td>' +
                            '<td>' + (value.merk ?? '') + '</td>' +
                            '<td>' + (value.jenis_barang ?? '') + '</td>' +
                            '</tr>'
                        );
                    });
                },
                error: function() {
                    Swal.fire('Gagal mengambil data list barang', '', 'error');
                }
            });
        };

        $('form').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#btn-submit').on('click', function(e) {
            e.preventDefault();
            var form = $(this).parents('form');
            let msg = "";
            let obj = $("form").serializeObject();

            if (obj['listRequest_ids[]'].length == 0) {
                msg += "Pilih minimal satu barang dari daftar barang yang tersedia.<br>";
            }


            if (msg == "") {
                form.submit();
            } else {
                Swal.fire({
                    title: "Pemberitahuan",
                    html: msg,
                    icon: "warning"
                });
            }
        });
    </script>
@endsection
