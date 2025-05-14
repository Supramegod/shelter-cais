@extends('layouts.master')
@section('title','Monitoring Kontrak')
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
                        <h3 class="page-title">Monitoring Kontrak</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Monitoring Kontrak</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Aksi</th>
                                    <th class="text-center">Quotation</th>
                                    <!-- <th class="text-center">Progress</th> -->
                                    <th class="text-center">Status Berlaku</th>
                                    <th class="text-center">No PKS</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Awal Kontrak</th>
                                    <th class="text-center">Akhir Kontrak</th>
                                    <th class="text-center">Berakhir Dalam</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">CRM</th>
                                    <th class="text-center">RO</th>
                                    <th class="text-center">Aktifitas</th>
                                    <th class="text-center">Issue</th>
                                    <th class="text-center">Status</th>
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

<!-- Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressModalLabel">Progress Tracking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 1</strong>
                                <p class="mb-0">Pembuat: John Doe</p>
                                <p class="mb-0">Waktu: 2023-01-01 10:00</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Completed</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 2</strong>
                                <p class="mb-0">Pembuat: Jane Smith</p>
                                <p class="mb-0">Waktu: 2023-01-02 11:00</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Completed</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 3</strong>
                                <p class="mb-0">Pembuat: John Doe</p>
                                <p class="mb-0">Waktu: 2023-01-03 12:00</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Completed</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 4</strong>
                                <p class="mb-0">Pembuat: Jane Smith</p>
                                <p class="mb-0">Waktu: 2023-01-04 13:00</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Completed</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 5</strong>
                                <p class="mb-0">Pembuat: John Doe</p>
                                <p class="mb-0">Waktu: 2023-01-05 14:00</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Completed</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 6</strong>
                                <p class="mb-0">Pembuat: Jane Smith</p>
                                <p class="mb-0">Waktu: 2023-01-06 15:00</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill">In Progress</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 7</strong>
                                <p class="mb-0">Pembuat: John Doe</p>
                                <p class="mb-0">Waktu: 2023-01-07 16:00</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill">Pending</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 8</strong>
                                <p class="mb-0">Pembuat: Jane Smith</p>
                                <p class="mb-0">Waktu: 2023-01-08 17:00</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill">Pending</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 9</strong>
                                <p class="mb-0">Pembuat: John Doe</p>
                                <p class="mb-0">Waktu: 2023-01-09 18:00</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill">Pending</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Proses 10</strong>
                                <p class="mb-0">Pembuat: Jane Smith</p>
                                <p class="mb-0">Waktu: 2023-01-10 19:00</p>
                            </div>
                            <span class="badge bg-secondary rounded-pill">Pending</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
    // $(document).ready(function () {
    //     $('#progressModal').modal('show');
    // });
    // let dt_filter_table = $('.dt-column-search');
    // Formatting function for row details - modify as you need
    // function format(d) {
    //     return (
    //         '<dl>' +
    //         '<dt>Status Leads Saat Ini :</dt>' +
    //         '<dd style="font-weight:bold;color:#000056">trial</dd>' +
    //         '</dl>'
    //     );
    // }

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
            url: "{{ route('monitoring-kontrak.list') }}",
            data: function (d) {
                d.tgl_dari = $('#tgl_dari').val();
                d.tgl_sampai = $('#tgl_sampai').val();
            },
        },
        "createdRow": function( row, data, dataIndex){
            // $('td', row).css('background-color', data.warna_row);
            // $('td', row).css('color', data.warna_font);
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
            data : 'aksi',
            name : 'aksi',
            orderable: false,
            searchable: false,
        },
        {
            data : 'quotation',
            name : 'quotation',
            className:'text-center'
        },
        // {
        //     data : 'progress',
        //     name : 'progress',
        //     className:'text-center'
        // },
        {
            data : 'status_berlaku',
            name : 'status_berlaku',
            className:'text-center'
        },
        {
            data : 'nomor',
            name : 'nomor',
            className:'text-center'
        },{
            data : 'nama_site',
            name : 'nama_site',
            className:'text-center'
        },{
            data : 's_mulai_kontrak',
            name : 's_mulai_kontrak',
            className:'text-center'
        },{
            data : 's_kontrak_selesai',
            name : 's_kontrak_selesai',
            className:'text-center'
        },{
            data : 'berakhir_dalam',
            name : 'berakhir_dalam',
            className:'text-center'
        },{
            data : 'sales',
            name : 'sales',
            className:'text-center'
        },{
            data : 'crm',
            name : 'crm',
            className:'text-center'
        },{
            data : 'ro',
            name : 'ro',
            className:'text-center'
        },{
            data : 'aktifitas',
            name : 'aktifitas',
            className:'text-center',
            searchable: false
        },{
            data : 'issue',
            name : 'issue',
            className:'text-center',
            searchable: false
        },{
            data : 'status',
            name : 'status',
            className:'text-center'
        }],
        "language": datatableLang,
        dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
        buttons: [
            {
                extend: 'collection',
                className: 'btn btn-label-warning dropdown-toggle mx-3 waves-effect waves-light',
                text: '<i class="mdi mdi-file-import-outline me-sm-1"></i> <span class="d-none d-sm-inline-block">Import</span>',
                buttons: [
                    {
                        text: '<i class="mdi mdi-file-import-outline"></i> Import',
                        className: 'dropdown-item',
                        action: function (e, dt, node, config)
                            {
                                //This will send the page to the location specified
                                window.location.href = '{{route("monitoring-kontrak.import")}}';
                            }
                    },
                    {
                        text: '<i class="mdi mdi-file-excel"></i> Download Template',
                        className: 'dropdown-item',
                        action: function (e, dt, node, config)
                            {
                                //This will send the page to the location specified
                                window.location.href = '{{route("monitoring-kontrak.template-import")}}';
                            }
                    },
                ]
            },
            {
            extend: 'collection',
            className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
            text: '<i class="mdi mdi-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
            buttons: [
                {
                extend: 'csv',
                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                className: 'dropdown-item',
                exportOptions: {
                    columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                    // prevent avatar to be display
                    format: {
                    body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                            result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                            result = result + item.textContent;
                        } else result = result + item.innerText;
                        });
                        return result;
                    }
                    }
                }
                },{
                extend: 'excel',
                text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                className: 'dropdown-item',
                exportOptions: {
                    columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                }
                },
                {
                extend: 'pdf',
                text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                className: 'dropdown-item',
                orientation: 'landscape',
                customize: function(doc) {
                        doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10
                    },
                exportOptions: {
                    columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                    orientation: 'landscape',
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10
                    },
                    // prevent avatar to be display
                    format: {
                    body: function (inner, coldex, rowdex) {
                        if (inner.length <= 0) return inner;
                        var el = $.parseHTML(inner);
                        var result = '';
                        $.each(el, function (index, item) {
                        if (item.classList !== undefined && item.classList.contains('user-name')) {
                            result = result + item.lastChild.firstChild.textContent;
                        } else if (item.innerText === undefined) {
                            result = result + item.textContent;
                        } else result = result + item.innerText;
                        });
                        return result;
                    }
                    }
                }
            }]},

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
