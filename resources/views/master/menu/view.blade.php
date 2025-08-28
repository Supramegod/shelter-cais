@extends('layouts.master')
@section('title', 'View Menu ')
@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master Menu/ </span> Menu</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span>Form Menu</span>
                            <span>{{ $now }}</span>
                        </div>
                    </h5>
                    <form class="card-body overflow-hidden" action="{{ route('master.menu.update', $data->id) }}"
                        method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Nama Menu<span class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="nama" name="nama" value="{{ old('nama', $data->nama) }}" class="form-control @if ($errors->any()) @if ($errors->has('nama')) is-invalid @else @endif @endif">
                                        @if ($errors->has('nama'))
                                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Kode <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="kode" name="kode" value="{{ old('kode', $data->kode) }}" class="form-control @if ($errors->any()) @if ($errors->has('kode')) is-invalid @else @endif @endif">
                                        @if ($errors->has('kode'))
                                            <div class="invalid-feedback">{{ $errors->first('kode') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">URL<span class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                            <input type="text" id="url" name="url" value="{{ old('url', $data->url) }}" class="form-control @if ($errors->any()) @if ($errors->has('url')) is-invalid @else @endif @endif">
                                        @if ($errors->has('url'))
                                            <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row mb-3" id="parentMenuGroup"
                                    style="display: {{ !is_null($data->parent_id) ? 'flex' : 'none' }};">
                                    <label class="col-sm-3 col-form-label text-sm-end">Sub Menu Dari<span
                                            class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                        <div class="position-relative">
                                            <select id="menu_parent" name="menu_parent" disabled
                                                class="form-select @if ($errors->any())  @endif"
                                                data-allow-clear="true" tabindex="-1">
                                                @foreach ($parent as $menu)
                                                    <option value="{{ $menu->id }}"
                                                        @if ($data->parent_id == $menu->id) selected @endif>
                                                        {{ $menu->nama }}
                                                        @if ($errors->has('menu'))
                                                            <div class="invalid-feedback">{{ $errors->first('menu') }}
                                                            </div>
                                                        @endif

                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Icon <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" id="icon" name="icon"
                                                value="{{ old('icon', $data->icon) }}"
                                                class="form-control @error('icon') is-invalid @enderror"
                                                placeholder="Klik untuk memilih icon" onclick="openIconModal()">
                                            <span class="input-group-text"><i id="icon-preview"
                                                    class="{{ old('icon', $data->icon) }}"></i></span>
                                        </div>
                                        @error('icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <div class="row justify-content-end">
                                <div class="col-sm-12 d-flex justify-content-center">
                                    <button type="submit"
                                        class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>

                                    <a href="{{ route('master.menu') }}" class="btn btn-secondary waves-effect">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <input type="hidden" id="sysmenu_id"name="sysmenu_id" value="{{ $data->id }}">
                    <div class="container px-2 py-5">
                        <h4>Role Akses</h4>
                        <div class=" col-sm-12 table-responsive overflow-hidden table-data">
                            <table id="table-data" class=" table w-100 table-hover table-bordered"
                                style="text-wrap: nowrap;width:60%">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Nama Role</th>
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
                            <div class="mt-3">
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
    <!-- Modal Icon Picker -->
    <div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Icon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="iconContainer"></div>
                </div>
            </div>
        </div>
    </div>

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


        $('#pilih_menu').on('change', function() {
            if ($('#pilih_menu').val() === 'no') {
                $('#parentMenuGroup').show();
            } else {
                $('#parentMenuGroup').hide();
                $('#menu_parent').val('');
            }

        });

        $('form').on('submit', function() {

            let prefix = $('#url_prefix').text().trim().replace(/\s+/g, '');
            let suffix = $('#url_suffix').val().trim();
            $('#full_url').val(prefix + suffix);


        });

        // Optional: auto-slugify saat ketik nama menu
        $('#nama').on('input', function() {
            $('#url_suffix').val(slugify($(this).val()));
        });

        function slugify(text) {
            return text.toString().trim().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }
        var table = $('#table-data').DataTable({
            scrollX: true,
            'processing': true,
            'paging' : false,
            ajax: {
                url: "{{ route('master.menu.list-role') }}",
                data: function(d) {
                    d.sysmenu_id =$('#sysmenu_id').val()
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
                data: 'name',
                name: 'name',
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
                    role_id: $(this).data('id'),
                    field: $(this).data('field'),
                    value: $(this).is(':checked') ? 1 : 0
                });
            });

            $.ajax({
                url: "{{ route('master.menu.simpan-akses') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    akses: data,
                    sysmenu_id: $('#sysmenu_id').val()
                },
                success: function(res) {
                    Swal.fire('Berhasil', 'Akses berhasil disimpan', 'success');
                }
            });
        });

        function openIconModal() {
            const iconContainer = document.getElementById('iconContainer');
            iconContainer.innerHTML = '';

            mdiIcons.forEach(iconClass => {
                const iconCol = document.createElement('div');
                iconCol.className = 'col-2 text-center mb-3';

                const iconBtn = document.createElement('button');
                iconBtn.className = 'btn btn-outline-secondary';
                iconBtn.style.width = '100%';
                iconBtn.innerHTML = `<i class="${iconClass} mdi-24px"></i>`;
                iconBtn.onclick = () => selectIcon(iconClass);

                iconCol.appendChild(iconBtn);
                iconContainer.appendChild(iconCol);
            });

            new bootstrap.Modal(document.getElementById('iconModal')).show();
        }

        function selectIcon(iconClass) {
            document.getElementById('icon').value = iconClass;
            document.getElementById('icon-preview').className = iconClass;
            bootstrap.Modal.getInstance(document.getElementById('iconModal')).hide();
        }
    </script>
@endsection
