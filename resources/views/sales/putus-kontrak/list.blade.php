@extends('layouts.master')
@section('title','Putus Kontrak')
@section('pageStyle')
<style>
    .dt-buttons {width: 100%;}
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
                        <h3 class="page-title">Putus Kontrak</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Putus Kontrak</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Aksi</th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Alamat</th>
                                    <th class="text-center">CRM</th>
                                    <th class="text-center">BM</th>
                                    <th class="text-center">RO</th>
                                    <th class="text-center">Layanan</th>
                                    <th class="text-center">Jumlah HC</th>
                                    <th class="text-center">Kronologi</th>
                                    <th class="text-center">Tindakan</th>
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
    <!-- End Row -->
    <!--/ Responsive Datatable -->
</div>
@endsection

@section('pageScript')
<script>
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
            url: "{{ route('putus-kontrak.list') }}",
            data: function (d) {
                d.tgl_dari = $('#tgl_dari').val();
                d.tgl_sampai = $('#tgl_sampai').val();
            },
        },
        "createdRow": function( row, data, dataIndex){
        },
        "order":[
            [0,'desc']
        ],

        columns: [
            {
                data : 'aksi',
                name : 'aksi',
                orderable: false,
                searchable: false,
            },
            {
            data: 'id',
            name: 'id',
            className: 'text-center',
            visible: false,
            searchable: false
            },
            {
            data: 'nama_perusahaan',
            name: 'nama_perusahaan',
            className: 'text-center'
            },
            {
            data: 'alamat',
            name: 'alamat',
            className: 'text-center'
            },
            {
            data: 'crm',
            name: 'crm',
            className: 'text-center'
            },
            {
            data: 'bm',
            name: 'bm',
            className: 'text-center'
            },
            {
            data: 'ro',
            name: 'ro',
            className: 'text-center'
            },
            {
            data: 'layanan',
            name: 'layanan',
            className: 'text-center'
            },
            {
            data: 'jumlah_hc',
            name: 'jumlah_hc',
            className: 'text-center'
            },
            {
            data: 'kronologi',
            name: 'kronologi',
            className: 'text-center'
            },
            {
            data: 'tindakan',
            name: 'tindakan',
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
        "language": datatableLang,
        dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
                buttons: [
                    {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-sm-inline-block">Tambah Putus Kontrak</span>',
                    className: 'create-new btn btn-label-primary waves-effect waves-light',
                    action: function (e, dt, node, config)
                        {
                            //This will send the page to the location specified
                            window.location.href = '{{route("putus-kontrak.add")}}';
                        }
                    }
                ],
    });
    window.addEventListener('pageshow', function (event) {
        if (sessionStorage.getItem('forceRefresh') === 'true') {
            sessionStorage.removeItem('forceRefresh');
            location.reload();
        }
    });
</script>
@endsection
