@extends('layouts.master')
@section('title','Training Site')
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
                        <h3 class="page-title">Training Site</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">SDT</a></li>
							<li class="breadcrumb-item active" aria-current="page">Training Site</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Client</th>
                                    <th class="text-center">Area</th>
                                    <th class="text-center">Kab/Kota</th>
                                    <th class="text-center">Tanggal Gabung</th>
                                    <th class="text-center">Target/Thn</th>
                                    <th class="text-center">Jumlah Training</th>
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
        @if(session()->has('success'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{session()->get('success')}}',
                icon: 'success',
                customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if(session()->has('error'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{session()->has('error')}}',
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
                    url: "{{ route('training-site.list') }}",
                    data: function (d) {
                        
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
                    data : 'client',
                    name : 'client',
                    className:'text-left'
                },{
                    data : 'area',
                    name : 'area',
                    className:'text-center'
                },{
                    data : 'kab_kota',
                    name : 'kab_kota',
                    className:'text-center'
                },{
                    data : 'tgl_gabung',
                    name : 'tgl_gabung',
                    className:'text-center'
                },{
                    data : 'target_per_tahun',
                    name : 'target_per_tahun',
                    className:'text-center'
                },{
                    data : 'jml_training',
                    name : 'jml_training',
                    className:'text-center'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    className:'text-center'
                }],
                "language": datatableLang
            });
        
        $('body').on('click', '.btn-detail', function() {
            $('#modal-training').modal('show');  
            let id = $(this).data('id');
            
            // let dt_filter_table = $('.dt-column-search');
            $("#table-training").dataTable().fnDestroy();
            var table = $('#table-training').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...',
                "bDestroy": true
            },
                ajax: {
                    url: "{{ route('training-site.history') }}",
                    data: function (d) {
                        d.client_id = id;
                    },
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
                    data : 'total_peserta',
                    name : 'total_peserta',
                    className:'text-center'
                },{
                    data : 'trainer',
                    name : 'trainer',
                    className:'text-left'
                }],
                "language": datatableLang
            });

            // let table2 ='#table-training';
            // $(table2).DataTable().ajax.reload();
        });
    </script>

    <div class="modal fade" id="modal-training" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <div class="table-responsive overflow-hidden">
                        <table id="table-training" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">Materi</th>
                                    <th class="text-center">Waktu Mulai</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Tempat</th>
                                    <th class="text-center">Total Peserta</th>
                                    <th class="text-center">Trainer</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table ajax --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection