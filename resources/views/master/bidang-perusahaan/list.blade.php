@extends('layouts.master')
@section('title', 'Bidang Perusahaan')
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
                        <div class="col-md-10 text-left col-12 my-auto">
                            <h3 class="page-title">Bidang Perusahaan</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Bidang Perusahaan</li>
                            </ol>
                        </div>
                        <div class="col-md-2 text-right col-12 my-auto">
                            <a href="{{ route('bidang-perusahaan.add') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="table-responsive overflow-hidden table-data">
                            <table id="table-data" class="dt-column-search table w-100 table-hover"
                                style="text-wrap: nowrap;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Total Leads</th>
                                        <th>Dibuat Tanggal</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Action</th>
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
        $(document).ready(function () {
            // Sweet Alert untuk notifikasi
            @if(session()->has('success'))
                Swal.fire({
                    title: 'Pemberitahuan',
                    html: '{!! session()->get('success') !!}',
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
                    html: '{!! session()->get('error') !!}',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-warning waves-effect waves-light'
                    },
                    buttonsStyling: false
                });
            @endif

                // Inisialisasi DataTable
                var table = $('#table-data').DataTable({
                scrollX: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                pageLength: 10,
                serverSide: true,
                processing: true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: 'Loading...',
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                ajax: {
                    url: "{{ route('bidang-perusahaan.data') }}",
                    type: 'GET',
                    data: function (d) {
                        // Optional: filter tambahan jika diperlukan
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTable Ajax Error:', xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memuat data. Silakan refresh halaman.',
                            icon: 'error'
                        });
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    { data: 'id', name: 'id', width: '60px' },
                    { data: 'nama', name: 'nama' },
                    {
                        data: 'total_leads',
                        name: 'total_leads',
                        width: '100px',
                        className: 'text-center',
                        orderable: true,
                        searchable: false
                    },
                    { data: 'created_at', name: 'created_at', width: '150px' },
                    { data: 'created_by', name: 'created_by', width: '150px' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '200px' }
                ]
            });

            // Event handler untuk tombol delete
            $('body').on('click', '.btn-delete', function () {
                let id = $(this).data('id');
                let totalLeads = $(this).data('leads');

                let confirmText = 'Apakah anda yakin ingin menghapus data ini?';
                if (totalLeads > 0) {
                    confirmText = `Bidang perusahaan ini memiliki ${totalLeads} leads. Apakah anda yakin ingin menghapus data ini?`;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "{{ route('bidang-perusahaan.delete') }}",
                            data: {
                                "id": id,
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function (xhr) {
                                console.error('Delete Error:', xhr.responseText);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus data',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection