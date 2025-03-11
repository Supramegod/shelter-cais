@extends('layouts.master')
@section('title','SDT Training')
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
                        <h3 class="page-title">Training</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">SDT</a></li>
							<li class="breadcrumb-item active" aria-current="page">Training</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('sdt-training')}}" method="GET">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari" value="{{$tglDari}}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai" value="{{$tglSampai}}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-2">
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
                                                <option value="">- Semua Sumber Trainings -</option>
                                                @foreach($platform as $data)
                                                <option value="{{$data->id}}" @if($request->platform==$data->id) selected @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="platform">Sumber Trainings</label>
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
                                </div> -->
                                <div class="col-md-1">
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
                                    <th class="text-center">Materi</th>
                                    <th class="text-center">Waktu Mulai</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Tempat</th>
                                    <th class="text-center">Client</th>
                                    <th class="text-center">Total Client</th>
                                    <th class="text-center">Total Peserta</th>
                                    <th class="text-center">Trainer</th>
                                    <th class="text-center">Total Trainer</th>
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
    @endif
    @if(isset($error) || session()->has('error'))  
        
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
                url: "{{ route('sdt-training.list') }}",
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
                data : 'materi',
                name : 'materi',
                className:'text-left'
            },{
                data : 'waktu_mulai',
                name : 'waktu_mulai',
                className:'text-center'
            },{
                data : 'tipe',
                name : 'tipe',
                className:'text-center'
            },{
                data : 'tempat',
                name : 'tempat',
                className:'text-center'
            },{
                data : 'client',
                name : 'client',
                className:'text-left'
            },{
                data : 'total_client',
                name : 'total_client',
                className:'text-center'
            },{
                data : 'total_peserta',
                name : 'total_peserta',
                className:'text-center'
            },{
                data : 'trainer',
                name : 'trainer',
                className:'text-left'
            },{
                data : 'total_trainer',
                name : 'total_trainer',
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
            buttons: [{
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah SDT Training</span>',
                className: 'create-new btn btn-label-primary waves-effect waves-light',
                action: function (e, dt, node, config)
                    {
                        window.location.href = '{{route("sdt-training.add")}}';
                    }
                }]
        });

        $('body').on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin hapus data ini ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'primary',
                cancelButtonColor: 'warning',
                confirmButtonText: 'Hapus'
            }).then(function (result) {
                console.log(result)
                if (result.isConfirmed) {
                    let formData = {
                        "id":id,
                        "_token": "{{ csrf_token() }}"
                    };

                    let table ='#table-data';
                    $.ajax({
                        type: "POST",
                        url: "{{route('sdt-training.delete')}}",
                        data:formData,
                        success: function(response){
                            console.log(response)
                            if (response.success) {
                                Swal.fire({
                                    title: 'Pemberitahuan',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1000,
                                    timerProgressBar: true,
                                    willClose: () => {
                                        $(table).DataTable().ajax.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    title: 'Pemberitahuan',
                                    text: response.message,
                                    icon: 'error'
                                })
                            }
                        },
                        error:function(error){
                            Swal.fire({
                                title: 'Pemberitahuan',
                                text: error,
                                icon: 'error'
                            })
                        }
                    });
                }
            });
        });

        // $('#table-data').on('click', 'tbody tr', function() {
        //     let rdata = table.row(this).data();
        //     if(rdata.can_view){
        //         window.location.href = "leads/view/"+rdata.id;
        //     }else{
        //         Swal.fire({
        //             title: 'Pemberitahuan',
        //             html: 'Anda tidak bisa melihat data ini',
        //             icon: 'warning',
        //             customClass: {
        //             confirmButton: 'btn btn-warning waves-effect waves-light'
        //             },
        //             buttonsStyling: false
        //         });
        //     }
        // })

    // Setup - add a text input to each footer cell
    // $('.dt-column-search thead tr').clone(true).appendTo('.dt-column-search thead');
    // $('.dt-column-search thead tr:eq(1) th').each(function (i) {
    //     var title = $(this).text();
    //     var $input = $('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

    //     // Add left and right border styles to the parent element
    //     $(this).css('border-left', 'none');
    //     if (i === $('.dt-column-search thead tr:eq(1) th').length - 1) {
    //         $(this).css('border-right', 'none');
    //     }

    //     $(this).html($input);

    //     $('input', this).on('keyup change', function () {
    //         if (table.column(i).search() !== this.value) {
    //             table.column(i).search(this.value).draw();
    //         }
    //     });
    // });
</script>
@endsection