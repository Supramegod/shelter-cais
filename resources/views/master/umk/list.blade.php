@extends('layouts.master')
@section('title','UMK')
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
                        <h3 class="page-title">UMK</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
							<li class="breadcrumb-item active" aria-current="page">UMK</li>
						</ol>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Provinsi</th>
                                    <th class="text-center">UMK</th>
                                    <th class="text-center">Tanggal Berlaku</th>
                                    <th class="text-center">Sumber</th>
                                    <th class="text-center">Diubah Tanggal</th>
                                    <th class="text-center">Diubah Oleh</th>
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
                    url: "{{ route('umk.list') }}",
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
                    data : 'city_name',
                    name : 'city_name',
                    className:'dt-body-left'
                },{
                    data : 'umk',
                    name : 'umk',
                    className:'dt-body-right',
                    render: $.fn.dataTable.render.number('.','.', 2,'')
                },{
                    data : 'tgl_berlaku',
                    name : 'tgl_berlaku',
                    className:'dt-body-center'
                },{
                    data : 'sumber',
                    name : 'sumber',
                    className:'dt-body-center'
                },{
                    data : 'updated_at',
                    name : 'updated_at',
                    className:'text-center'
                },{
                    data : 'updated_by',
                    name : 'updated_by',
                    className:'text-center'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
                buttons: [
                ],
            });
    </script>
@endsection