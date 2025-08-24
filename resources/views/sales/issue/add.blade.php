@extends('layouts.master')
@section('title', 'Menu Issue Baru')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Menu Issue/ </span> Issue Baru</h4>
        <!-- Multi Column with Form Separator -->
        <div class="row">
            <!-- Form Label Alignment -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span>Form Issue</span>
                            <span>{{ $now }}</span>
                        </div>
                    </h5>
                    <form class="card-body overflow-hidden" action="{{ route('issue.save') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <input type="hidden" name="lead_id" id="lead_id" value="{{ old('lead_id') }}">
                            <label class="col-sm-2 col-form-label text-sm-end">Leads<span class="text-danger">
                                    *</span></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" id="lead" name="lead" value="{{ old('lead') }}" required
                                        class="form-control rounded-end-0 @error('lead') is-invalid @enderror"
                                        style="pointer-events: none;" tabindex="-1">

                                    <button type="button" id="btn-modal-leads"
                                        class="btn btn-primary  rounded-start-0">Cari
                                        Leads</button>
                                </div>
                                @error('lead')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="pks_id" id="pks_id" value="{{ old('pks_id') }}">
                            <label class="col-sm-2 col-form-label text-sm-end">Nomor PKS</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" id="nomor_pks" name="nomor_pks" value="{{ old('nomor_pks') }}"
                                        readonly
                                        class="form-control rounded-end-0 @error('nomor_pks') is-invalid @enderror">
                                    <button type="button" id="btn-modal-nomor-pks"
                                        class="btn btn-primary  rounded-start-0">Cari
                                        Nomor PKS</button>
                                </div>
                                @error('nomor_pks')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <input type="hidden" name="site_id" id="site_id" value="{{ old('site_id') }}">
                            <label class="col-sm-2 col-form-label text-sm-end">Site</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" id="site" name="site" value="{{ old('site') }}" readonly
                                        class="form-control rounded-end-0 @error('site') is-invalid @enderror">
                                    <button type="button" id="btn-modal-site" class="btn btn-primary  rounded-start-0">Cari
                                        Site</button>
                                </div>
                                @error('site')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end">Status Issue</span></label>
                            <div class="col-sm-4">
                                <select id="status" name="status" class="form-control">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Judul Issue <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" id="judul" name="judul" required value=""
                                    class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end">Jenis Keluhan <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select id="jenis_keluhan" name="jenis_keluhan" required class="form-control">
                                    <option value="">-- Pilih Jenis Keluhan --</option>
                                    <option value="COMPLAINT">COMPLAINT</option>
                                    <option value="REFRESH PERSONIL">REFRESH PERSONIL</option>
                                    <option value="PERGANTIAN KAPORLAP">PERGANTIAN KAPORLAP</option>
                                    <option value="PERFORMANCE PERSONIL">PERFORMANCE PERSONIL</option>
                                    <option value="PERFORMANCE MANAGEMENT">PERFORMANCE MANAGEMENT</option>
                                    <option value="INVOICE">INVOICE</option>
                                    <option value="KEHILANGAN BARANG">KEHILANGAN BARANG</option>
                                    <option value="TRAINING">TRAINING</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Kolaborator</label>
                            <div class="col-sm-4">
                                <input type="text" id="kolaborator" name="kolaborator" value=""
                                    class="form-control">
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end">Lampiran</label>
                            <div class="col-sm-4">
                                <input type="file" id="lampiran" name="lampiran" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="form-floating form-floating-outline mb-4">
                                    <textarea class="form-control h-px-100" name="deskripsi" id="deskripsi" required placeholder=""></textarea>
                                </div>
                            </div>
                        </div>



                        <div class="pt-4">
                            <div class="row justify-content-end">
                                <div class="col-sm-12 d-flex justify-content-center">
                                    <button type="submit"
                                        class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>

                                    <a href="{{ route('issue') }}" class="btn btn-secondary waves-effect">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ Content -->
    <!--/ Modal Cari Leads -->
    <div class="modal fade" id="modal-leads" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Leads</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-leads">
                            <table id="table-leads" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">Nama Perusahaan</th>
                                        <th class="text-center">Created at</th>
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
    <!--/ Modal Cari Nomor PKS -->
    <div class="modal fade" id="modal-pks" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Nomor PKS</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-pks">
                            <table id="table-pks" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">Nomor PKS</th>
                                        <th class="text-center">Created at</th>
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

    <!--/ Modal Cari Site -->
    <div class="modal fade" id="modal-site" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Daftar Site</h3>
                    </div>
                    <div class="row">
                        <div class="table-responsive overflow-hidden table-site">
                            <table id="table-site" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">Nomor Site</th>
                                        <th class="text-center">Nama Site</th>
                                        <th class="text-center">Kebutuhan</th>
                                        <th class="text-center">Kota</th>


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
                html: '{{ session()->get('success') }}',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if (isset($error) || session()->has('error'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ $error }} {{ session()->has('error') }}',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

        // Leads

        $('#btn-modal-leads').on('click', function() {
            $('#modal-leads').modal('show');
            $('#table-leads').DataTable().ajax.reload();
        });
        $('#table-leads').on('click', 'tr', function() {
            $('#modal-leads').modal('hide');
            var data = tabelLeads.row(this).data();
            $('#lead').val(data.nama_perusahaan);
            $('#lead_id').val(data.id);

        });
        var tabelLeads = $('#table-leads').DataTable({
            processing: true,
            serverSide: true,
            language: {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('issue.leads-list') }}",
                type: 'GET',
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false,
                    searchable: false

                },
                {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center'
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
        });

        // Nomor PKS

        $('#btn-modal-nomor-pks').on('click', function() {
            if ($('#lead').val() === "") {
                Swal.fire("Pilih Leads terlebih dahulu", "", "warning");
                return;
            }
            $('#modal-pks').modal('show');
            $('#table-pks').DataTable().ajax.reload();
        });
        $('#table-pks').on('click', 'tr', function() {
            $('#modal-pks').modal('hide');
            var data = tabelPks.row(this).data();
            $('#nomor_pks').val(data.nomor);
            $('#pks_id').val(data.id);

        });
        var tabelPks = $('#table-pks').DataTable({
            processing: true,
            serverSide: true,
            language: {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('issue.pks-list') }}",
                type: 'GET',
                data: function(d) {
                    d.lead_id = $('#lead_id').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false,
                    searchable: false

                },
                {
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center'
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                },
            ],
            order: [
                [0, 'desc']
            ],
        });


        // Site

        $('#btn-modal-site').on('click', function() {
            if ($('#lead').val() === "") {
                Swal.fire("Pilih Leads terlebih dahulu", "", "warning");
                return;
            }
            $('#modal-site').modal('show');
            $('#table-site').DataTable().ajax.reload();
        });
        $('#table-site').on('click', 'tr', function() {
            $('#modal-site').modal('hide');
            var data = tabelSite.row(this).data();
            $('#site').val(data.nama_site);
            $('#site_id').val(data.id);

        });
        var tabelSite = $('#table-site').DataTable({
            processing: true,
            serverSide: true,
            language: {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('issue.site-list') }}",
                type: 'GET',
                data: function(d) {
                    d.lead_id = $('#lead_id').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false,
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
                    data: 'kebutuhan',
                    name: 'kebutuhan',
                    className: 'text-center'
                },
                {
                    data: 'kota',
                    name: 'kota',
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
        });
    </script>
@endsection
