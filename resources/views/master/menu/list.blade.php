@extends('layouts.master')
@section('title', 'Master Menu')
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
                            <h3 class="page-title">Master Menu</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Menu</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">

                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-data" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>

                                        <th class="text-center">Aksi</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">Menu</th>
                                        <th class="text-center">Icon</th>
                                        <th class="text-center">URL</th>
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
        <!-- End Row -->
        <!--/ Responsive Datatable -->
    </div>
    <!--/ Content -->
    <!-- Bootstrap Modal -->


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
        function confirmDelete(id, childMenus = []) {
            let warningText = '';
            if (childMenus.length > 0) {
                warningText = "Menu ini memiliki Sub Menu.<br>Menu berikut juga akan ikut terhapus:<br><ul>";
                childMenus.forEach((name) => {
                    warningText += "<span style=' color:blue;'>" + name + "</span><br>";
                });
                warningText += "</ul>Apakah anda yakin ingin menghapusnya?";
            } else {
                warningText = "Menu ini akan dihapus.";
            }

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: warningText,
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
            'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('master.menu.list') }}",
                data: function(d) {

                },
            },
            "order": [
                [2, 'asc']
            ],
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    visible: false,
                    searchable: false,
                    className: 'text-center'
                },{
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                },  {
                    data: 'kode',
                    name: 'kode',
                },  {
                    data: 'nama',
                    name: 'nama',
                },{
                    data: 'icon',
                    name: 'icon',
                    className: 'text-center'
                },
                {
                    data: 'url',
                    name: 'url',
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
            ],
            "language": datatableLang,
            dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [{
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Menu</span>',
                className: 'create-new btn btn-label-primary waves-effect waves-light',
                action: function(e, dt, node, config) {
                    window.location.href = '{{ route('master.menu.add') }}';

                }
            }]



        });
    </script>
@endsection
