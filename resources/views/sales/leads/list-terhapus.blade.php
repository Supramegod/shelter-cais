@extends('layouts.master')
@section('title','Leads')
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
                        <h3 class="page-title">Leads Terhapus</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Leads Terhapus</li>
						</ol>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Wilayah</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">Perusahaan</th>
                                    <th class="text-center">Telp. Perusahaan</th>
                                    <th class="text-center">Nama PIC</th>
                                    <th class="text-center">Telp. PIC</th>
                                    <th class="text-center">Email PIC</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Sumber Leads</th>
                                    <th class="text-center">Deleted By</th>
                                    <th class="text-center">Deleted At</th>
                                    <th class="text-center">Keterangan</th>
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
    let dt_filter_table = $('.dt-column-search');

    var table = $('#table-data').DataTable({
        scrollX: true,
        "iDisplayLength": 25,
        'processing': true,
    'language': {
        'loadingRecords': '&nbsp;',
        'processing': 'Loading...'
    },
            ajax: {
                url: "{{ route('leads.list-terhapus') }}",
                data: function (d) {},
            },
            "createdRow": function( row, data, dataIndex){
                $('td', row).css('background-color', data.warna_background);
                $('td', row).css('color', data.warna_font);
            },
            "order":[
                [0,'desc']
            ],
            columns:[{
                data : 'id',
                name : 'id',
                visible: false,
                searchable: false
            },{
                data : 'nomor',
                name : 'nomor',
                className:'text-center'
            },{
                data : 'branch',
                name : 'branch',
                className:'text-center'
            },{
                data : 'tgl',
                name : 'tgl',
                className:'text-center'
            },{
                data : 'sales',
                name : 'sales',
                className:'text-center'
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'telp_perusahaan',
                name : 'telp_perusahaan',
                className:'text-center'
            },{
                data : 'pic',
                name : 'pic',
                className:'text-center'
            },{
                data : 'no_telp',
                name : 'no_telp',
                className:'text-center'
            },{
                data : 'email',
                name : 'email',
                className:'text-center'
            },{
                data : 'status',
                name : 'status',
                className:'text-center'
            },{
                data : 'platform',
                name : 'platform',
                className:'text-center'
            },{
                data : 'deleted_by',
                name : 'deleted_by',
                className:'text-center'
            },{
                data : 'deleted_at',
                name : 'deleted_at',
                className:'text-center'
            },{
                data : 'notes',
                name : 'notes',
                className:'text-center'
            }],
            "language": datatableLang,
            dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [
                {
                text: '<i class="mdi mdi-arrow-left me-sm-1"></i> <span class="d-none d-sm-inline-block">Kembali</span>',
                className: 'btn btn-label-secondary waves-effect waves-light',
                action: function (e, dt, node, config)
                    {
                        history.back();
                    }
                }
            ],
        });
</script>
@endsection
