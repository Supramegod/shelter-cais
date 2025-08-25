@extends('layouts.master')
@section('title', 'Role')
@section('pageStyle')
    <style>
        .dt-buttons {
            width: 100%;
        }
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
                            <h3 class="page-title">Role</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Role</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <h3 style="text-align: center">List Role</h3>
                        <div class="table-responsive overflow-hidden table-data" style="margin: 0 auto; max-width: 90%; ">
                            <table id="table-data" class="dt-column-search table table-bordered"
                                style="text-wrap: nowrap; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Aksi</th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Role</th>
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
        @if (session()->has('success'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ session()->get('success') }}',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

        @if (isset($error) || session()->has('error'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ $error }} {{ session()->has('error') }}',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

        var table = $('#table-data').DataTable({
            scrollX: true,
            "iDisplayLength": 25,
            'processing': true,
            'paging' : false,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
            ajax: {
                url: "{{ route('role.list') }}",


            },
            "order": [
                [1, 'desc']
            ],
            columns: [
                 {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                },{
                    data: 'id',
                    name: 'id',
                    className:'text-center'
                }, {
                    data: 'name',
                    name: 'name',
                    className: 'text-center'
                },
            ],
            "language": datatableLang,
            dom: 'frtip',

        });
    </script>
@endsection
