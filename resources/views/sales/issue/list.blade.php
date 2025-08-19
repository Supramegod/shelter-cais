@extends('layouts.master')
@section('title', 'Menu Issue')
@section('pageStyle')
    <style>
        .dt-buttons {
            width: 100%;
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
                            <h3 class="page-title">Menu Issue</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Menu Issue</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-data" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Leads</th>
                                        <th class="text-center">Nomor PKS</th>
                                        <th class="text-center">Nama Site</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Judul</th>
                                        <th class="text-center">Jenis Keluhan</th>
                                        <th class="text-center">Kolaborator</th>
                                        <th class="text-center">Lampiran</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Created at</th>
                                        <th class="text-center">Created By</th>
                                        <th class="text-center">Aksi</th>
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
        <!-- End Row -->
        <!--/ Responsive Datatable -->
    </div>
    <!--/ Content -->

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
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',

                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'text-start'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }


        var table = $('#table-data').DataTable({
            scrollX: true,
            "iDisplayLength": 25,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            },
            ajax: {
                url: "{{ route('issue.list') }}",
                data: function(d) {
                },
            },
            "order": [
                [1, 'desc']
            ],
            columns: [ {
                    data: 'id',
                    name: 'id',
                    visible: false,
                    searchable: false,
                    className: 'text-center'
                },{
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                },{
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                },
                  {
                    data: 'nama_site',
                    name: 'nama_site',
                    className: 'text-center'
                }, {
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center'
                }, {
                    data: 'judul',
                    name: 'judul',
                    className: 'text-center'
                }, {
                    data: 'jenis_keluhan',
                    name: 'jenis_keluhan',
                    className: 'text-center'
                }, {
                    data: 'kolaborator',
                    name: 'kolaborator',
                    className: 'text-center'
                }, {
                    data: 'url_lampiran',
                    name: 'url_lampiran',
                    className: 'text-center'
                }, {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center'
                }, 
                {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, 
               {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }
            ],
            "language": datatableLang,
            dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [{
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Issue</span>',
                className: 'create-new btn btn-label-primary waves-effect waves-light',
                action: function(e, dt, node, config) {
                    window.location.href = '{{ route('issue.add') }}';

                }
            }]



        });
    </script>
@endsection
