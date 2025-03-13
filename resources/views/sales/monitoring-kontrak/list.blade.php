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
                                    <th class="text-center">Aksi</th>
                                    <th class="text-center">No PKS</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Awal Kontrak</th>
                                    <th class="text-center">Akhir Kontrak</th>
                                    <th class="text-center">Berakhir Dalam</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">CRM</th>
                                    <th class="text-center">RO</th>
                                    <th class="text-center">Aktifitas</th>
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
<!--/ Content -->
@endsection

@section('pageScript')
<script>
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
        "iDisplayLength": 25,
        'processing': true,
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
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        },{
            data : 'nomor',
            name : 'nomor',
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
            className:'text-center'
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
            className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
            text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
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
                },
            ]
            },{
                text: '<i class="mdi mdi-delete me-sm-1"></i> <span class="d-none d-sm-inline-block">List Kontrak Terminated</span>',
                className: 'btn btn-label-danger waves-effect waves-light',
                action: function (e, dt, node, config)
                    {
                        //This will send the page to the location specified
                        window.location.href = '{{route("monitoring-kontrak.index-terminate")}}';
                    }
                },
        ],
    });

    // Add event listener for opening and closing details
    // table.on('click', 'td.dt-control', function (e) {
    //     let tr = e.target.closest('tr');
    //     let row = table.row(tr);

    //     if (row.child.isShown()) {
    //         // This row is already open - close it
    //         row.child.hide();
    //     }
    //     else {
    //         // Open this row
    //         // row.child(format(row.data())).show();
    //     }
    // });

    // ajax terminate kontrak dengan swal konfirmasi terminate kontrak
    function terminateKontrak(id){
        Swal.fire({
            title: 'Loading',
            text: 'Melakukan Terminate kontrak...',
            showConfirmButton: false,
            allowOutsideClick: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('monitoring-kontrak.terminate') }}",
            type: "POST",
            data: {
                id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function(response){
                if(response.status == 'success'){
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            table.ajax.reload();
                        }
                    });
                }else{
                    Swal.fire({
                        title: 'Gagal',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }

    $(document).on('click', '.btn-terminate-kontrak', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah anda yakin ingin terminate kontrak ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, terminate'
        }).then((result) => {
            if (result.isConfirmed) {
                terminateKontrak(id);
            }
        });
    });
</script>
@endsection
