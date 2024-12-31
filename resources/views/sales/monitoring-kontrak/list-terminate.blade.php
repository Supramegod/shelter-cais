@extends('layouts.master')
@section('title','PKS')
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
                        <h3 class="page-title">Kontrak Terminated</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Kontrak Terminated</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari" value="{{$tglDari}}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai" value="{{$tglSampai}}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th></th>
                                    <th class="text-center">No PKS</th>
                                    <th class="text-center">No SPK</th>
                                    <th class="text-center">No Quotation</th>
                                    <th class="text-center">Tanggal Kontrak</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Mulai Kontrak</th>
                                    <th class="text-center">Akhir Kontrak</th>
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
            url: "{{ route('monitoring-kontrak.list-terminate') }}",
            data: function (d) {
                d.tgl_dari = $('#tgl_dari').val();
                d.tgl_sampai = $('#tgl_sampai').val();
            },
        },
        "createdRow": function( row, data, dataIndex){
            $('td', row).css('background-color', data.warna_row);
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
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: ''
        },{
            data : 'nomor',
            name : 'nomor',
            className:'text-center'
        },{
            data : 'nomor_spk',
            name : 'nomor_spk',
            className:'text-center'
        },{
            data : 'nomor_quotation',
            name : 'nomor_quotation',
            className:'text-center'
        },{
            data : 'tgl_pks',
            name : 'tgl_pks',
            className:'text-center'
        },{
            data : 'nama_perusahaan',
            name : 'nama_perusahaan',
            className:'text-center'
        },{
            data : 'nama_site',
            name : 'nama_site',
            className:'text-center'
        },{
            data : 'mulai_kontrak',
            name : 'mulai_kontrak',
            className:'text-center'
        },{
            data : 's_kontrak_selesai',
            name : 's_kontrak_selesai',
            className:'text-center'
        },{
            data : 'created_by',
            name : 'created_by',
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
                        window.history.go(-1); return false;
                        //This will send the page to the location specified
                    }
                },
        ],
    });

    // Add event listener for opening and closing details
    table.on('click', 'td.dt-control', function (e) {
        let tr = e.target.closest('tr');
        let row = table.row(tr);
    
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        }
        else {
            // Open this row
            // row.child(format(row.data())).show();
        }
    });
</script>
@endsection