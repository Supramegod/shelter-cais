@extends('layouts.master')
@section('title', 'Notifications')

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
                    <div class="card-header d-flex justify-content-between align-items-center" style="padding-bottom: 0px !important;">
                        <div class="text-left">
                            <h3 class="page-title">Notifications</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                            </ol>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('notifications.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Notifikasi
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
                                        <th>Jenis</th>
                                        <th>Tujuan</th>
                                        <th>Title</th>
                                        <th>Body</th>
                                        <th>ID Dokumen</th>
                                        <th>Tipe Dokumen</th>
                                        <th>Lampiran</th>
                                        <th>Kirim Pada</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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
    </div>
    <!--/ Content -->
@endsection

@section('pageScript')
    <script>
        @if(session()->has('success'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ session()->get('success') }}',
                icon: 'success',
                customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
                buttonsStyling: false
            });
        @endif

        @if(session()->has('error'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{ session()->get('error') }}',
                icon: 'warning',
                customClass: { confirmButton: 'btn btn-warning waves-effect waves-light' },
                buttonsStyling: false
            });
        @endif

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
                url: "{{ route('notifications.data') }}",
                type: 'GET',
            },
            order: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id', visible: false, searchable: false },
                { data: 'jenis', name: 'jenis', className: 'text-center' },
                { data: 'tujuan', name: 'tujuan', className: 'text-center' },
                { data: 'title', name: 'title', className: 'text-center' },
                { data: 'body', name: 'body', className: 'text-center' },
                { data: 'doc_id', name: 'doc_id', className: 'text-center' },
                { data: 'doc_type', name: 'doc_type', className: 'text-center' },
                { data: 'lampiran', name: 'lampiran', className: 'text-center', orderable: false, searchable: false },
                { data: 'kirim_pada', name: 'kirim_pada', className: 'text-center' },
                { data: 'created_by', name: 'created_by', className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ]
        });

        // Delete dengan SweetAlert
        $('body').on('click', '.btn-delete', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin hapus notifikasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'primary',
                cancelButtonColor: 'warning',
                confirmButtonText: 'Hapus'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "/notifications/" + id,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Pemberitahuan',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1000,
                                    timerProgressBar: true,
                                    willClose: () => { $('#table-data').DataTable().ajax.reload(); }
                                })
                            } else {
                                Swal.fire({ title: 'Pemberitahuan', text: response.message, icon: 'error' })
                            }
                        },
                        error: function (error) {
                            Swal.fire({ title: 'Pemberitahuan', text: 'Gagal menghapus data', icon: 'error' })
                        }
                    });
                }
            });
        });
    </script>
@endsection