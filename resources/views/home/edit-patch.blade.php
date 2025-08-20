@extends('layouts.master')
@section('title', 'Edit Syarat dan Ketentuan Kerjasama')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Default -->
        <div class="row">
            <!-- Vertical Wizard -->
            <div class="col-12 mb-4">
                <div class="card mb-4">
                    <form class="card-body overflow-hidden" action="{{ route('update.log') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div id="account-details-1" class="content active">
                            <div class="content-header mb-5 text-center">
                                <h4 class="mb-3">Change Log Version Patch</h4>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-1 col-form-label text-sm-end">Version</label>
                                <div class="col-sm-3">
                                    <input type="text" id="version" name="version"
                                        value="{{ old('version', $data->version ?? '') }}" class="form-control">
                                </div>
                                <label class="col-form-label text-center">KETERANGAN</label>

                                <div id="keterangan" name ="keterangan">
                                    <p></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex flex-row-reverse">
                                    <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20"
                                        style="color:white">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Simpan Perubahan</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </button>
                                    &nbsp;&nbsp;
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-next w-20">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">Kembali</span>
                                        <i class="mdi mdi-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--/ Content -->
@endsection

@section('pageScript')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
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

        $('form').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#btn-submit').on('click', function(e) {
            e.preventDefault();
            let form = $(this).parents('form');
            let msg = "";
            let obj = $("form").serializeObject();
            let content = $('#keterangan').summernote('code');


            if ($('input[name="keterangan"]').length === 0) {
                form.append('<input type="hidden" name="keterangan">');
            }

            $('input[name="keterangan"]').val(content);

            if (msg == "") {
                form.submit();
            } else {
                Swal.fire({
                    title: "Pemberitahuan",
                    html: msg,
                    icon: "warning"
                });
            }
        });

        $('#keterangan').summernote({
            height: 300,
        });

        var initialContent = `{!! $data->keterangan ?? '' !!}`;
        $('#keterangan').summernote('code', initialContent);
    </script>
@endsection
