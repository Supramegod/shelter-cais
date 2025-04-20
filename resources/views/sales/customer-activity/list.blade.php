@extends('layouts.master')
@section('title','Customer Activity')
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
                        <h3 class="page-title">Customer Activity</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Customer Activity</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('customer-activity')}}" method="GET">
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
                                            <select class="form-select" id="branch" name="branch">
                                                <option value="">- Semua Wilayah -</option>
                                                @foreach($branch as $data)
                                                <option value="{{$data->id}}" @if($request->branch==$data->id) selected @endif>{{$data->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="branch">Wilayah</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="user" name="user">
                                                <option value="">- Semua User -</option>
                                                @foreach($listUser as $data)
                                                <option value="{{$data->id}}" @if($request->user==$data->id) selected @endif>{{$data->full_name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="company">User</label>
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
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Leads/Customer</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Kebutuhan</th>
                                    <th class="text-center">Wilayah</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Created By</th>
                                    <th class="text-center">Keterangan</th>
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
                // `d` is the original data object for the row
                let start = "";
                let end = "";
                let durasi = "";
                let tipeNotes = "";
                let tglRealisasi = "";
                let jamRealisasi = "";
                let penerima = "";
                let linkBuktiFoto = "";
                let jenisVisit = "";
                let notulen = "";
                let email = "";

                if(d.start != null ){
                    start = '<dt>Start :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' +d.start+
                    '</dd>';
                };
                if(d.end != null ){
                    end = '<dt>End :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' +d.end+
                    '</dd>';
                };
                if(d.durasi != null){
                    durasi = '<dt>Durasi :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.durasi+
                    '</dd>' ;
                }
                if(d.jam_realisasi != null){
                    jamRealisasi = '<dt>Jam Realisasi :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.jam_realisasi+
                    '</dd>' ;
                }
                if(d.penerima != null){
                    penerima = '<dt>Penerima :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.penerima+
                    '</dd>' ;
                }
                if(d.jenis_visit != null){
                    jenisVisit = '<dt>Jenis Visit :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.jenis_visit+
                    '</dd>' ;
                }
                if(d.notulen != null){
                    notulen = '<dt>Notulen :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.notulen+
                    '</dd>' ;
                }
                if(d.email != null){
                    email = '<dt>Email :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.email+
                    '</dd>' ;
                }
                if(d.link_bukti_foto != null){
                    linkBuktiFoto = '<dt>Lihat Bukti :</dt>' +
                    '<dd><a href="'+d.link_bukti_foto+'" target="_blank" class="mt-2 btn rounded-pill btn-info waves-effect"><span class="tf-icons mdi mdi-magnify me-1"></span> &nbsp; Lihat Bukti</a>'+
                    '</dd>' ;
                }
                if(d.notes_tipe !=null){
                    tipeNotes = d.notes_tipe;
                }

                if(d.tgl_r !=null){
                    tglRealisasi = '<dt>Tanggal Realisasi :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + d.tgl_r+
                    '</dd>' ;
                }

                return (
                    '<dl>' +
                    '<dt>Status Leads Saat Ini :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' +d.status_leads+
                    '</dd>' +
                    start+
                    end +
                    durasi +
                    tglRealisasi +
                    jamRealisasi +
                    penerima+
                    jenisVisit+
                    notulen+
                    email+
                    linkBuktiFoto+
                    '<dt>Keterangan :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">' + tipeNotes +
                    '</dd>' +
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
                    url: "{{ route('customer-activity.list') }}",
                    data: function (d) {
                        d.tgl_dari = $('#tgl_dari').val();
                        d.tgl_sampai = $('#tgl_sampai').val();
                        d.branch = $('#branch').find(":selected").val();
                        d.company = $('#company').find(":selected").val();
                        d.kebutuhan = $('#kebutuhan').find(":selected").val();
                        d.user = $('#user').find(":selected").val();
                    },
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
                    data : 'tgl',
                    name : 'tgl',
                    className:'text-center'
                },{
                    data : 'nama',
                    name : 'nama',
                    className:'text-center'
                },{
                    data : 'tipe',
                    name : 'tipe',
                    className:'text-center'
                },{
                    data : 'kebutuhan',
                    name : 'kebutuhan',
                    className:'text-center'
                },{
                    data : 'branch',
                    name : 'branch',
                    className:'text-center'
                },{
                    data : 'sales',
                    name : 'sales',
                    className:'text-center'
                },{
                    data : 'role',
                    name : 'role',
                    className:'text-center'
                },{
                    data : 'created_by',
                    name : 'created_by',
                    className:'text-center'
                },{
                    data : 'keterangan',
                    name : 'keterangan',
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
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Customer Activity</span>',
                    className: 'create-new btn btn-label-primary waves-effect waves-light',
                    action: function (e, dt, node, config)
                        {
                            //This will send the page to the location specified
                            window.location.href = '{{route("customer-activity.add")}}';
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
