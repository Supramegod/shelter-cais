@extends('layouts.master')
@section('title', 'Purchase Request')
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
                            <span class="text-center">Form Purchase Request Baru</span>

                        </div>
                    </h5>
                    <form class="card-body overflow-hidden" action="{{ route('purchase-request.save') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_pks" id="id_pks" value="{{ old('id_pks') }}">
                        <div id="account-details-1" class="content active">
                            <div class="content-header mb-5 text-center">
                                <h4 class="mb-0">Purchase Request</h4>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-2 col-form-label text-sm-end">Nomor Purchase Request</label>
                                <div class="col-sm-4">
                                    <input type="text" id="kode_pr" name="kode_pr" readonly required
                                        value="{{ $kode_pr }}"
                                        class="form-control rounded-end-0 @error('nama_perusahaan') is-invalid @enderror">
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <label class="col-sm-2 col-form-label text-sm-end">Perusahaan</label>
                                <div class="col-sm-3">
                                    <input type="text" id="nama_perusahaan" name="nama_perusahaan" readonly required
                                        value="{{ old('nama_perusahaan') }}"
                                        class="form-control rounded-end-0 @error('nama_perusahaan') is-invalid @enderror">
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label text-sm-end">Nomor PKS</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" id="nomor-pks" name="nomor-pks" readonly
                                            value="{{ old('nomor-pks') }}"
                                            class="form-control rounded-end-0 @error('nomor-pks') is-invalid @enderror">
                                        <button type="button" id="btn-modal-nomor"
                                            class="btn btn-primary  rounded-start-0">Cari
                                            Nomor </button>
                                    </div>
                                    @error('nomor_pks')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <label class="col-sm-2 col-form-label text-sm-end">Jenis Barang</label>
                                <div class="col-sm-3">
                                    <div class="position-relative">
                                        <select class="form-select" id="jenis_barang" name="jenis_barang">
                                            <option value="">Pilih Jenis Barang</option>
                                            <option value="Kaporlap">Kaporlap</option>
                                            <option value="Devices">Devices</option>
                                            <option value="Chemical">Chemical</option>

                                        </select>

                                    </div>
                                </div>

                            </div>

                            <br>

                            <div class="content-header mb-4 text-center">
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


                            <!-- Table -->
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
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Merk</th>
                                                <th>Jenis Barang</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-listBarang">
                                            {{-- data table ajax --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mb-3">
                                <div class="col-12 d-flex flex-row-reverse gap-3">
                                    <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20"
                                        style="color:white">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Purchase
                                            Request</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </button>
                                    <a href="{{ route('purchase-request') }}" class="btn btn-secondary waves-effect">Kembali</a>
                                </div>
                            </div>
                    </form>

                </div>
            </div>
        </div>
        <hr class="container-m-nx mb-5" />
    </div>
    <!--/ Content -->

    <!-- Modal Cari Nomor PKS -->
    <div class="modal fade" id="modal-nomor-pks" tabindex="-1" aria-hidden="true">
        <input type="hidden" id="id_pks" name="id_pks" value="{{ old('id_pks') }}">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Nomor PKS</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-nomor" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">Nomor PKS</th>
                                        <th class="text-center">Nama Perusahaan</th>
                                        <th class="text-center">Created At</th>
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
        @if (session()->has('success'))

            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ session()->get('success') }}<br><br>Kode Purchase Request: <strong>{{ session('kode_pr') }}</strong>',
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

                    window.open("{{ route('purchase-request.print', ['id' => session('id')]) }}", "_blank");
                }
            });
        @endif

        let statusTab = 'all';
        $('#check-all-listRequest').on('change', function() {
            $('.listRequest-checkbox').prop('checked', this.checked);
        });

        $('.listRequest-checkbox').on('change', function() {
            $('#check-all-listRequest').prop('checked', $('.listRequest-checkbox:checked').length === $(
                    '.listRequest-checkbox')
                .length);
        });
        $('#btn-modal-nomor').on('click', function() {
            $('#modal-nomor-pks').modal('show');
            $('#table-nomor').DataTable().ajax.reload();
        });
        $('#jenis_barang').on('change keyup', function() {
            if ($('#nomor-pks').val() === "") {
                Swal.fire("Pilih PKS terlebih dahulu", "", "warning");
                return;
            }
            if ($('#jenis_barang').val() === "") {
                Swal.fire("Pilih Jenis Barang terlebih dahulu", "", "warning");
                return;
            }
            fetchListBarang();
        });

        $('#table-nomor').on('click', 'tr', function() {
            $('#modal-nomor-pks').modal('hide');
            var data = isiTabel.row(this).data();
            $('#id_pks').val(data.id);
            $('#nomor-pks').val(data.nomor);
            $('#nama_perusahaan').val(data.nama_perusahaan);
            if ($('#jenis_barang').val() != "") {

                fetchListBarang()

            }
        });

        $('#statusTabs button').on('click', function() {
            statusTab = $(this).data('status');
            fetchListBarang()
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
                url: "{{ route('purchase-request.list-PKS') }}",
                type: "GET",
                data: function(d) {}

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
                data: 'nomor',
                name: 'nomor',
                className: 'text-center'
            }, {
                data: 'nama_perusahaan',
                name: 'nama_perusahaan',
                className: 'text-center'
            }, {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center'
            }, {
                data: 'created_by',
                name: 'created_by',
                className: 'text-center'
            }],
            "language": datatableLang,
        });

        function fetchListBarang() {
            $('#tbody-listBarang').html(
                '<tr><td colspan="6" class="text-center">Loading data...</td></tr>'
            );

            $.ajax({
                url: '{{ route('purchase-request.list-barang') }}',
                type: 'GET',
                data: {
                    status: statusTab,
                    id_pks: $('#id_pks').val(),
                    jenis_barang: $('#jenis_barang').val()
                },
                success: function(data) {
                    $('#tbody-listBarang').empty(); // kosongkan dulu isinya

                    if (!data || data.length === 0) {
                        $('#tbody-listBarang').html(
                            '<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>'
                        );
                        return;
                    }

                    $.each(data, function(key, value) {
                        // Tentukan class baris berdasarkan status is_open
                        let rowClass = (value.is_open === 0) ? 'bg-danger-subtle text-dark' :
                            'bg-success-subtle text-dark';

                        $('#tbody-listBarang').append(
                            '<tr class="' + rowClass + '">' +
                            '<td>' +
                            '<input type="checkbox" name="listBarang_ids[]" value="' + value.id +
                            '" class="form-check-input listRequest-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
                            '</td>' +
                            '<td>' + value.nama + '</td>' +
                            '<td>' +
                            '<input type="number" name="jumlah_pr[' + value.id + ']" value="' +
                            value.jumlah +
                            '" class="form-control form-control-sm input-jumlah-pr" data-id="' +
                            value.id + '" />' +
                            '</td>' +
                            '<td>' + (value.satuan ?? '') + '</td>' +
                            '<td>' + (value.merk ?? '') + '</td>' +
                            '<td>' + (value.jenis_barang ?? '') + '</td>' +
                            '</tr>'
                        );
                    });
                },
                error: function() {
                    $('#tbody-listBarang').html(
                        '<tr><td colspan="6" class="text-center text-danger">Gagal mengambil data list barang</td></tr>'
                    );
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

            if (obj['listBarang_ids[]'].length == 0) {
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
