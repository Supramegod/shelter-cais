@extends('layouts.master')
@section('title', 'View Role ')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master Role/ </span> Role</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span>View Role</span>
                            <span>{{ $now }}</span>
                        </div>
                    </h5>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">ID</label>
                        <div class="col-sm-7">
                            <input type="text" id="id" name="id" readonly value="{{ old('id', $data->id) }}"
                                class="form-control @if ($errors->any()) @if ($errors->has('id')) is-invalid @else @endif @endif">
                            @if ($errors->has('nama'))
                                <div class="invalid-feedback">{{ $errors->first('id') }}</div>
                            @endif
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Nama Role</label>
                        <div class="col-sm-7">
                            <input type="text" id="name" name="name" readonly
                                value="{{ old('name', $data->name) }}"
                                class="form-control @if ($errors->any()) @if ($errors->has('name')) is-invalid @else @endif @endif">
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                    </div>

                    <div class="container px-2 py-5">
                        <h4>Role Akses</h4>
                        <div class=" col-sm-8 table-responsive overflow-hidden table-data">
                            <table id="table-data" class=" table w-100 table-hover table-bordered"
                                style="text-wrap: nowrap;width:60%">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Menu</th>
                                        <th class="text-center">Kode</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Add</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- data table ajax --}}
                                </tbody>
                            </table>
                            <div class="mt-3 text-center">
                                <button type="button" id="btnSimpanRole" class="btn btn-primary">
                                    Simpan Akses
                                </button>
                            </div>

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
        $(document).ready(function() {
            $('.icon-option').click(function() {
                var iconClass = $(this).data('value');
                $('#icon').val(iconClass);
                $('#icon-preview i').attr('class', iconClass);
            });
        });

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
            'processing': true,
            ajax: {
                url: "{{ route('role.list-menu') }}",
                data: function(d) {
                    d.id = $('#id').val()
                },
            },
            "order": [
                [0, 'desc']
            ],
            columns: [{
                data: 'id',
                name: 'id',
                visible: false,
                searchable: false

            }, {
                data: 'nama',
                name: 'nama',
                className: 'text-center'
            }, {
                data: 'kode',
                name: 'kode',
                className: 'text-center'
            }, {
                data: 'is_view',
                name: 'is_view',
                className: 'text-center'
            }, {
                data: 'is_add',
                name: 'is_add',
                className: 'text-center'
            }, {
                data: 'is_edit',
                name: 'is_edit',
                className: 'text-center'
            }, {
                data: 'is_delete',
                name: 'is_delete',
                className: 'text-center'
            }],
            "language": datatableLang,
            dom: 'frtip',

        });

        $('#btnSimpanRole').click(function() {
            let data = [];
            $('.perm-check').each(function() {
                data.push({
                    sysmenu_id: $(this).data('id'),
                    field: $(this).data('field'),
                    value: $(this).is(':checked') ? 1 : 0
                });
            });

            $.ajax({
                url: "{{ route('role.update-akses') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    role_id: $('#id').val(),
                    akses: data
                },
                success: function(res) {
                    Swal.fire('Berhasil', 'Akses berhasil disimpan', 'success');
                }
            });
        });;
    </script>
@endsection
