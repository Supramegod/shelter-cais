@extends('layouts.master')
@section('title','Salary Rule')
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
                        <h3 class="page-title">Salary Rule</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
							<li class="breadcrumb-item active" aria-current="page">Salary Rule</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nama Salary Rule</th>
                                    <th class="text-center">Cutoff</th>
                                    <th class="text-center">Crosscheck Absen</th>
                                    <th class="text-center">Pengiriman Invoice</th>
                                    <th class="text-center">Perkiraan Invoice Diterima</th>
                                    <th class="text-center">Pembayaran Invoice</th>
                                    <th class="text-center">Rilis Payroll</th>
                                    <th class="text-center">Dibuat Tanggal</th>
                                    <th class="text-center">Dibuat Oleh</th>
                                    <th class="text-center">Diedit Tanggal</th>
                                    <th class="text-center">Diedit Oleh</th>
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
                    url: "{{ route('salary-rule.list') }}",
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
                    data : 'nama_salary_rule',
                    name : 'nama_salary_rule',
                    className:'text-center'
                },{
                    data : 'cutoff',
                    name : 'cutoff',
                    className:'text-center'
                },{
                    data : 'crosscheck_absen',
                    name : 'crosscheck_absen',
                    className:'text-center'
                },{
                    data : 'pengiriman_invoice',
                    name : 'pengiriman_invoice',
                    className:'text-center'
                },{
                    data : 'perkiraan_invoice_diterima',
                    name : 'perkiraan_invoice_diterima',
                    className:'text-center'
                },{
                    data : 'pembayaran_invoice',
                    name : 'pembayaran_invoice',
                    className:'text-center'
                },{
                    data : 'rilis_payroll',
                    name : 'rilis_payroll',
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
                    data : 'updated_at',
                    name : 'updated_at',
                    className:'text-center'
                },{
                    data : 'updated_by',
                    name : 'updated_by',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
                buttons: [
                    {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Salary Rule</span>',
                    className: 'create-new btn btn-label-primary waves-effect waves-light',
                    action: function (e, dt, node, config)
                        {
                            //This will send the page to the location specified
                            window.location.href = '{{route("salary-rule.add")}}';
                        }
                    }
                ],
            });
    </script>
@endsection