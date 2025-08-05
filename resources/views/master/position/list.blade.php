@extends('layouts.master')
@section('title', 'Position')
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
                            <h3 class="page-title">Position</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Position</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{ route('position') }}" method="GET">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="entitas" name="entitas">
                                                    <option value="">- Entitas -</option>
                                                    @foreach ($company as $entitas)
                                                        <option value="{{ $entitas->id }}"
                                                            @if ($request->entitas == $entitas->id) selected @endif>
                                                            {{ $entitas->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="entitas">Entitas</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="layanan" name="layanan">
                                                    <option value="">- Layanan -</option>
                                                    @foreach ($service as $layanan)
                                                        <option value="{{ $layanan->id }}"
                                                            @if ($request->layanan == $layanan->id) selected @endif>
                                                            {{ $layanan->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="layanan">Layanan</label>
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
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-data" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>

                                        <th class="text-center">Aksi</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Entitas</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Layanan</th>
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

        var table = $('#table-data').DataTable({
            scrollX: true,
            "iDisplayLength": 25,
            'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('position.list') }}",
                data: function(d) {
                    d.entitas = $('#entitas').find(":selected").val();
                    d.layanan = $('#layanan').find(":selected").val();
                },
            },
            "order": [
                [0, 'desc']
            ],
            columns: [
                 {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                },{
                    data: 'id',
                    name: 'id',
                    className:'text-center'
                }, {
                    data: 'entitas',
                    name: 'entitas',
                    className: 'text-center'
                }, {
                    data: 'name',
                    name: 'name',
                    className: 'text-center'
                }, {
                    data: 'description',
                    name: 'description',
                    className: 'text-center'
                },
                {
                    data: 'layanan',
                    name: 'layanan',
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
            ],
            "language": datatableLang,
            dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [{
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Position</span>',
                className: 'create-new btn btn-label-primary waves-effect waves-light',
                action: function(e, dt, node, config) {
                     window.location.href = '{{route("position.add")}}';
                  
                }
            }]



        });
    </script>
@endsection
