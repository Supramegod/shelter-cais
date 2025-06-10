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
                        <h3 class="page-title">PKS</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">PKS</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('pks')}}" method="GET">
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
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="company" name="company">
                                                <option value="">- Semua Entitas -</option>
                                                @foreach($company as $data)
                                                <option value="{{$data->id}}" @if($request->company==$data->id) selected @endif>{{$data->code}}  | {{$data->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="company">Entitas</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="kebutuhan" name="kebutuhan">
                                                <option value="">- Semua Kebutuhan -</option>
                                                @foreach($kebutuhan as $data)
                                                <option value="{{$data->id}}" @if($request->company==$data->id) selected @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="kebutuhan">Kebutuhan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="is_aktif" name="is_aktif">
                                                <option value="">- Semua Status -</option>
                                                <option value="0" @if($request->is_aktif=='0') selected @endif>Perlu Approval</option>
                                                <option value="1" @if($request->is_aktif=='1') selected @endif>PKS Aktif</option>
                                            </select>
                                            <label for="is_aktif">Status Data</label>
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
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Leads/Customer</th>
                                    <th class="text-center">Kebutuhan</th>
                                    <th class="text-center">Status</th>
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
        @if(isset($success) || session()->has('success'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$success}} {{session()->get('success')}}',
                icon: 'success',
                customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if(isset($error) || session()->has('error'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$error}} {{session()->has('error')}}',
                icon: 'warning',
                customClass: {
                confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
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
                    url: "{{ route('pks.list') }}",
                    data: function (d) {
                        d.tgl_dari = $('#tgl_dari').val();
                        d.tgl_sampai = $('#tgl_sampai').val();
                        d.branch = $('#branch').find(":selected").val();
                        d.company = $('#company').find(":selected").val();
                        d.kebutuhan = $('#kebutuhan').find(":selected").val();
                        d.is_aktif = $('#is_aktif').find(":selected").val();
                    },
                },
                "createdRow": function( row, data, dataIndex){
                    if(data.status_pks_id==1 || data.status_pks_id==2 || data.status_pks_id==3 || data.status_pks_id== 4 || data.status_pks_id==5){
                        $('td', row).css('background-color', '#f39c1240');
                    }
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
                    data : 'tgl_pks',
                    name : 'tgl_pks',
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
                    data : 'status',
                    name : 'status',
                    className:'text-center'
                },{
                    data : 'created_by',
                    name : 'created_by',
                    className:'text-center'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
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
                        }
                    ]
                    },
                    {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah PKS</span>',
                    className: 'create-new btn btn-label-primary waves-effect waves-light',
                    action: function (e, dt, node, config)
                        {
                            //This will send the page to the location specified
                            window.location.href = '{{route("pks.add")}}';
                        }
                    }
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
                    row.child(format(row.data())).show();
                }
            });
    </script>
@endsection