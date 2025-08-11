@extends('layouts.master')
@section('title', 'Supplier')
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
                            <h3 class="page-title">Supplier</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Supplier</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-data" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Alamat</th>
                                        <th class="text-center">Kontak</th>
                                        <th class="text-center">PIC</th>
                                        <th class="text-center">NPWP</th>
                                        <th class="text-center">Kategori Barang</th>
                                        <th class="text-center">Di Buat Tanggal</th>
                                        <th class="text-center">Di Buat Oleh By</th> 
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
            iDisplayLength: 10,
            serverSide: true,
            processing: true,
            language: {
                loadingRecords: '&nbsp;',
                processing: 'Loading...'
            },
            ajax: {
                url: "{{ route('supplier.data') }}",
                type: 'GET',
                data: function (d) {
                    // Optional: filter tambahan
                },
            },
            order: [
                [0, 'desc']
            ],
            columns: [
                { data: 'id', name: 'id', visible: false, searchable: false },
                { data: 'nama_supplier', name: 'nama_supplier', className: 'text-center' },
                { data: 'alamat', name: 'alamat', className: 'text-center' },
                { data: 'kontak', name: 'kontak', className: 'text-center' },
                { data: 'pic', name: 'pic', className: 'text-center' },
                { data: 'npwp', name: 'npwp', className: 'text-center' },
                { data: 'kategori_barang', name: 'kategori_barang', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'created_by', name: 'created_by', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ]
        });


        $('body').on('click', '.btn-delete', function () {
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
                        "id": id,
                        "_token": "{{ csrf_token() }}"
                    };

                    let table = '#table-data';
                    $.ajax({
                        type: "POST",
                        url: "{{route('supplier.delete')}}",
                        data: formData,
                        success: function (response) {
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
                        error: function (error) {
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
    </script>
@endsection