@extends('layouts.master')
@section('title','Site')
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
                        <h3 class="page-title">Site</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Site</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <!-- <form action="{{route('site')}}" method="GET">
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
                                            <select class="form-select" id="platform" name="platform">
                                                <option value="">- Semua Sumber Site -</option>
                                                @foreach($platform as $data)
                                                <option value="{{$data->id}}" @if($request->platform==$data->id) selected @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="platform">Sumber Site</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="status" name="status">
                                                <option value="">- Semua Status -</option>
                                                @foreach($status as $data)
                                                <option value="{{$data->id}}" @if($request->status==$data->id) selected @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status">Status</label>
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
                    </form> -->
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">SPK</th>
                                    <th class="text-center">Kontrak</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Nama Site</th>
                                    <th class="text-center">Provinsi</th>
                                    <th class="text-center">Kota</th>
                                    <th class="text-center">Penempatan</th>
                                    <th class="text-center">Created At</th>
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

    var table = $('#table-data').DataTable({
        scrollX: true,
        "iDisplayLength": 25,
        'processing': true,
    'language': {
        'loadingRecords': '&nbsp;',
        'processing': 'Loading...'
    },
            ajax: {
                url: "{{ route('site.list') }}",
                data: function (d) {
                    // d.tgl_dari = $('#tgl_dari').val();
                    // d.tgl_sampai = $('#tgl_sampai').val();
                    // d.branch = $('#branch').find(":selected").val();
                    // d.platform = $('#platform').find(":selected").val();
                    // d.status = $('#status').find(":selected").val();
                },
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
                data : 'spk',
                name : 'spk',
                className:'text-center'
            },{
                data : 'kontrak',
                name : 'kontrak',
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
                data : 'provinsi',
                name : 'provinsi',
                className:'text-center'
            },{
                data : 'kota',
                name : 'kota',
                className:'text-center'
            },{
                data : 'penempatan',
                name : 'penempatan',
                className:'text-center'
            },{
                data : 'created_at',
                name : 'created_at',
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
                }
            ],
        });

        // $('#table-data').on('click', 'tbody tr', function() {
        //     let rdata = table.row(this).data();
        //     window.location.href = "site/view/"+rdata.id;
        // })
</script>
@endsection
