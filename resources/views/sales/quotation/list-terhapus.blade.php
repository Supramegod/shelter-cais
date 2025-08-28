@extends('layouts.master')
@section('title','Quotation Dihapus')
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
                        <h3 class="page-title">Quotation Dihapus</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Quotation Dihapus</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Leads/Customer</th>
                                    <th class="text-center">Kebutuhan</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Entitas</th>
                                    <th class="text-center">Alasan Hapus</th>
                                    <th class="text-center">Deleted At</th>
                                    <th class="text-center">Deleted By</th>
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
@endsection

@section('pageScript')
<script>
    let dt_filter_table = $('.dt-column-search');

    // Formatting function for row details - modify as you need
    function format(d) {
        return (
            '<dl>' +
            '<dt>Status Leads Saat Ini :</dt>' +
            '<dd style="font-weight:bold;color:#000056">trial</dd>' +
            '</dl>'
        );
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
            url: "{{ route('quotation.list-terhapus') }}",
            data: function (d) {
            },
        },
        "createdRow": function( row, data, dataIndex){

        },
        "order":[
            [0,'desc']
        ],
        columns:[{
            data : 'quotation_id',
            name : 'quotation_id',
            visible: false,
            searchable: false
        },{
            data : 'nomor',
            name : 'nomor',
            className:'text-center'
        },{
            data : 'tgl',
            name : 'tgl',
            className:'text-center'
        },{
            data : 'nama_perusahaan',
            name : 'nama_perusahaan',
            className:'text-center'
        },{
            data : 'kebutuhan',
            name : 'kebutuhan',
            className:'text-center'
        },{
            data : 'nama_site',
            name : 'nama_site',
            className:'text-center'
        },{
            data : 'status',
            name : 'status',
            className:'text-center'
        },{
            data : 'company',
            name : 'company',
            className:'text-center'
        },{
            data : 'alasan_revisi',
            name : 'alasan_revisi',
            className:'text-center'
        },{
            data : 'deleted_at',
            name : 'deleted_at',
            className:'text-center'
        },{
            data : 'deleted_by',
            name : 'deleted_by',
            className:'text-center'
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang
    });
</script>
@endsection
