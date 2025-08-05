@extends('layouts.master')
@section('title', 'Purchase')
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
                            <h3 class="page-title">Purchase Order</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Purchase</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{ route('purchase-order') }}" method="GET">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="jenis_barang" name="jenis_barang">
                                                    <option value="">- Semua Jenis Barang -</option>
                                                    @foreach ($jenis_barang as $jenis)
                                                        <option value="{{ $jenis->jenis_barang }}"
                                                            @if ($request->jenis_barang == $jenis->jenis_barang) selected @endif>
                                                            {{ $jenis->jenis_barang }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="jenis_barang">Jenis Barang</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="sales" name="sales">
                                                    <option value="">- Semua Sales -</option>
                                                    @foreach ($sales as $Sales)
                                                        <option value="{{ $Sales->sales }}"
                                                            @if ($request->sales == $Sales->sales) selected @endif>
                                                            {{ $Sales->sales }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="sales">Sales</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="company" name="company">
                                                    <option value="">- Semua Perusahaan -</option>
                                                    @foreach ($company as $perusahaan)
                                                        <option value="{{ $perusahaan->perusahaan }}"
                                                            @if ($request->company == $perusahaan->perusahaan) selected @endif>
                                                            {{ $perusahaan->perusahaan }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="company">Perusahaan</label>
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

                                        <th class="text-center">ID</th>
                                        <th class="text-center">Nomor</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Sales</th>
                                        <th class="text-center">Nama Perusahaan</th>
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
    <!-- Bootstrap Modal -->


@endsection

@section('pageScript')
    <script>
        @if (isset($success) || session()->has('success'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ $success }} {{ session()->get('success') }}',
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
                url: "{{ route('purchase-order.list') }}",
                data: function (d) {
                        d.jenis_barang= $('#jenis_barang').find(":selected").val();
                        d.company = $('#company').find(":selected").val();                        
                        d.sales = $('#sales').find(":selected").val();
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
                    data: 'kode_po',
                    name: 'kode_po',
                    className: 'text-center'
                }, {
                    data: 'tanggal',
                    name: 'tanggal',
                    className: 'text-center'
                }, {
                    data: 'sales',
                    name: 'sales',
                    className: 'text-center'
                },
                {
                    data: 'perusahaan',
                    name: 'perusahaan',
                    className: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }
            ],
             "language": datatableLang,
            dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [
                 {
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Purchase Order</span>',
                className: 'create-new btn btn-label-primary waves-effect waves-light',
                action: function (e, dt, node, config)
                    {
                        //This will send the page to the location specified
                        window.location.href = '{{route("purchase-order.add")}}';
                    }
                }
            ]



        });
    </script>
@endsection
