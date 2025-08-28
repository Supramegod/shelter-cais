@extends('layouts.master')
@section('title', 'Position')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Position/ </span> Position Baru</h4>
        <!-- Multi Column with Form Separator -->
        <div class="row">
            <!-- Form Label Alignment -->
            <div class="col-md-12">
                <div class="card mb-4">

                    <form class="card-body overflow-hidden" action="{{ route('position.edit', $data->id) }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Entitas<span class="text-danger">
                                    *</span></label>
                            <div class="col-sm-4">
                                <div class="position-relative">
                                    <select id="entitas" name="entitas" required
                                        class="form-select @if ($errors->any())  @endif"
                                        data-allow-clear="true" tabindex="-1">
                                        <option value="">- Pilih Entitas -</option>
                                        @foreach ($company as $entitas)
                                            <option value="{{ $entitas->id }}"
                                                {{ $data->company_id == $entitas->id ? 'selected' : '' }}>
                                                {{ $entitas->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label text-sm-end">Layanan<span class="text-danger">
                                    *</span></label>
                            <div class="col-sm-4">
                                <div class="position-relative">
                                    <select id="layanan" name="layanan" required
                                        class="form-select @if ($errors->any())  @endif"
                                        data-allow-clear="true" tabindex="-1">
                                        <option value="">- Pilih Layanan -</option>
                                        @foreach ($service as $layanan)
                                            <option value="{{ $layanan->id }}"
                                                {{ $data->layanan_id == $layanan->id ? 'selected' : '' }}>
                                                {{ $layanan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Nama Posisi<span class="text-danger">
                                    *</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="nama" required value="{{ old('nama', $data->name) }}"
                                    class="form-control @error('nama') is-invalid @enderror">
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="form-floating form-floating-outline mb-2">
                                    <textarea name="deskripsi" required class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="pt-4">
                            <div class="row justify-content-start">
                                <div class="col-sm-6 d-flex justify-content-center">
                                    <button type="submit"
                                        class="btn btn-primary me-sm-2 me-3 waves-effect waves-light">Simpan</button>

                                    {{-- <a href="{{ route('position') }}" class="btn btn-secondary waves-effect">Kembali</a> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="container px-5 py-5">
                        <h3>List Requirement</h3>


                        <div id="requirement-wrapper">

                            <button type="button" id="btn-tambah-requirement" class="btn btn-success mb-3">
                                + Tambah Requirement
                            </button>
                        </div>

                        <template id="form-template" style="display: none;">
                            <form action="{{ route('requirement.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="layanan_id" value="{{ $data->layanan_id }}">
                                <input type="hidden" name="position_id" value="{{ $data->id }}">
                                <div class="row mb-3 ">
                                    <label class="col-sm-2 col-form-label text-sm-center">
                                        Requirement
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" name="nama" value="{{ old('nama') }}"
                                            class="form-control @error('nama') is-invalid @enderror">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-primary me-sm-2 waves-effect waves-light">
                                            Tambah
                                        </button>
                                        <button type="button" id="btn-batal"
                                            class="btn btn-danger me-sm-2 waves-effect waves-light">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </template>
                        <div class=" col-sm-8 table-responsive overflow-hidden table-data">
                            <table id="table-data" class=" table w-100 table-hover table-bordered"
                                style="text-wrap: nowrap;width:60%">
                                <thead>
                                    <tr>

                                        <th class="text-center">ID</th>
                                        <th class="text-center">Requirement</th>
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
        $(document).on('click', '#btn-tambah-requirement', function() {
            var formContent = $('#form-template').html();
            $('#requirement-wrapper').html(formContent);
        });
        $(document).on('click', '#btn-batal', function(e) {
            e.preventDefault();
            $('#requirement-wrapper').html(`
        <button type="button" id="btn-tambah-requirement" class="btn btn-success mb-3">
            + Tambah Requirement
        </button>
    `);
        });


        var table = $('#table-data').DataTable({
            scrollX: true,

            'processing': true,
            ajax: {
                url: "{{ route('requirement.list') }}",
                data: function(d) {
                    d.id = {{ $data->id }};
                },
            },
            "order": [
                [0, 'desc']
            ],
            columns: [{
                data: 'id',
                name: 'id',
                className: 'text-center'
            }, {
                data: 'requirement',
                name: 'requirement',
                className: 'text-center'
            }, {
                data: 'aksi',
                name: 'aksi',
                width: "10%",
                orderable: false,
                searchable: false,
            }],
            "language": datatableLang,
            dom: 'rti',

        });
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var requirement = $('input[data-id="' + id + '"]').val();

            $.ajax({
                url: "{{ route('requirement.edit') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    requirement: requirement
                },
                success: function(res) {

                    if (res.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data berhasil diubah',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        });
                        $('#table-data').DataTable().ajax.reload(null, false);
                    }
                },

            });
        });
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('requirement.delete') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('#table-data').DataTable().ajax.reload(null,
                                    false);
                            } else {
                                Swal.fire('Gagal', res.message ||
                                    'Gagal menghapus data.',
                                    'error');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
