@extends('layouts.master')
@section('title','Submission')
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
                        <h3 class="page-title">Submission</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Submission</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('submission')}}" method="GET">
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
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive overflow-hidden table-data">
                        <div id="bulk-actions" class="mb-3 text-end" style="display:none;">
                            <button id="btn-delete" class="btn btn-outline-danger btn-sm">
                                <i class="mdi mdi-delete"></i> Hapus
                            </button>
                            <button id="btn-leads" class="btn btn-outline-success btn-sm">
                                <i class="mdi mdi-plus"></i> Buat Leads
                            </button>
                        </div>
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center"><input type="checkbox" id="check-all"></th>
                                    <th class="text-center">Wilayah</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Perusahaan</th>
                                    <th class="text-center">Nama PIC</th>
                                    <th class="text-center">Telp. PIC</th>
                                    <th class="text-center">Email PIC</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Sumber Submission</th>
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
    var table = $('#table-data').DataTable({
        scrollX: true,
        paging: false,
        'processing': true,
    'language': {
        'loadingRecords': '&nbsp;',
        'processing': 'Loading...'
    },
    ajax: {
        url: "{{ route('submission.list') }}",
        data: function (d) {
            d.tgl_dari = $('#tgl_dari').val();
            d.tgl_sampai = $('#tgl_sampai').val();
            d.branch = $('#branch').find(":selected").val();
            d.platform = $('#platform').find(":selected").val();
            d.status = $('#status').find(":selected").val();
        },
    },
    "order":[
        [0,'desc']
    ],
    columns:[{
        data : 'id',
        name : 'id',
        orderable: false,
        searchable: false,
        render: function(data, type, row){
            return `<input type="checkbox" class="row-checkbox" value="${data}">`;
        }
    },{
        data : 'branch',
        name : 'branch',
        className:'text-center'
    },{
        data : 'tgl',
        name : 'tgl',
        className:'text-center'
    },{
        data : 'nama_perusahaan',
        name : 'nama_perusahaan',
        className:'text-center'
    },
    {
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
        data : 'created_by',
        name : 'created_by',
        className:'text-center'
    },{
        data : 'notes',
        name : 'notes',
        className:'text-center'
    },{
        data : 'aksi',
    name : 'aksi',
        width: "10%",
        orderable: false,
        searchable: false,
    }],
    "language": datatableLang,
});

table.on('draw', function(){
    $('#check-all').prop('checked', false);
    $('#bulk-actions').hide();
});

// Toggle all checkbox
$('#check-all').on('click', function(){
    $('.row-checkbox').prop('checked', this.checked).trigger('change');
});

// Toggle action buttons
$(document).on('change', '.row-checkbox', function(){
    let selected = $('.row-checkbox:checked').length;
    if(selected > 0){
        $('#bulk-actions').show();
    } else {
        $('#bulk-actions').hide();
    }
});

$('#btn-delete').on('click', function(){
    let selected = $('.row-checkbox:checked').map(function(){ return this.value; }).get();
    console.log('Hapus:', selected);
    Swal.fire({
          title: 'Sedang menghapus',
          text: 'Mohon tunggu...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });

    // Ajax ke route hapus atau konfirmasi swal
    $.ajax({
        url: "{{ route('submission.delete') }}",
        type: "POST",
        data: {
            id: selected,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            let jsonresponse = JSON.parse(response);

            if(jsonresponse.status == 'success'){
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: jsonresponse.message,
                }).then(() => {
                    table.ajax.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: jsonresponse.message,
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menghapus data.',
            });
        }
    });
});

$('#btn-leads').on('click', function(){
    let selected = $('.row-checkbox:checked').map(function(){ return this.value; }).get();
    console.log('Buat Leads untuk:', selected);
    Swal.fire({
          title: 'Sedang membuat Leads',
          text: 'Mohon tunggu...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });

    // Ajax ke route hapus atau konfirmasi swal
    $.ajax({
        url: "{{ route('submission.save') }}",
        type: "POST",
        data: {
            id: selected,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            let jsonresponse = JSON.parse(response);

            if(jsonresponse.status == 'success'){
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: jsonresponse.message,
                }).then(() => {
                    table.ajax.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: jsonresponse.message,
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menghapus data.',
            });
        }
    });
});

</script>
@endsection
