@extends('layouts.master')
@section('title', 'Tambah Menu ')

@section('content')
    <!--/ Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master Menu/ </span> Menu Baru</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span>Form Menu</span>
                            <span>{{ $now }}</span>
                        </div>
                    </h5>
                    <form class="card-body overflow-hidden" action="{{ route('master.menu.save') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Opsi Menu</label>
                                    <div class="col-sm-9">
                                        <div class="position-relative">
                                            <select id="pilih_menu" name="pilih_menu"
                                                class="form-select @if ($errors->any())  @endif"
                                                data-allow-clear="true" tabindex="-1">
                                                <option value="parent">Menu Utama</option>
                                                <option value="child">Sub Menu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3" id="parentMenuGroup" style="display: none;">
                                    <label class="col-sm-3 col-form-label text-sm-end">Menu Utama<span
                                            class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                        <div class="position-relative">
                                            <select id="menu_parent" name="menu_parent"
                                                class="form-select @if ($errors->any())  @endif"
                                                data-allow-clear="true" tabindex="-1">
                                                <option value="">Pilih Menu Utama</option>
                                                @foreach ($parent as $menu)
                                                    <option value="{{ $menu->id }}" data-kode="{{ $menu->kode }}"
                                                        @if ($request->menu == $menu->id) selected @endif>
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
                                    <label class="col-sm-3 col-form-label text-sm-end">Nama Menu<span class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control @if ($errors->any()) @if ($errors->has('nama')) is-invalid @else @endif @endif">
                                        @if ($errors->has('nama'))
                                            <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                                        @endif
                                    </div>
                                </div>


                            </div>
                            <div class="col-sm-6">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Kode<span class="text-danger">
                                            *</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="kode" name="kode" value="{{ old('kode') }}" class="form-control @if ($errors->any()) @if ($errors->has('kode')) is-invalid @else @endif @endif">
                                        @if ($errors->has('kode'))
                                            <div class="invalid-feedback">{{ $errors->first('kode') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">URL<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="url" name="url" value="{{ old('url') }}" class="form-control @if ($errors->any()) @if ($errors->has('url')) is-invalid @else @endif @endif">
                                        @if ($errors->has('url'))
                                            <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label text-sm-end">Icon</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" id="icon" name="icon" readonly
                                                class="form-control @error('icon') is-invalid @enderror"
                                                value="{{ old('icon') }}"
                                                placeholder="Klik untuk memilih icon" onclick="openIconModal()">
                                            <span class="input-group-text"><i id="icon-preview"></i></span>
                                        </div>
                                        @error('icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <div class="row justify-content-end">
                                    <div class="col-sm-12 d-flex justify-content-center">
                                        <button type="submit"
                                            class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>

                                        <a href="{{ route('master.menu') }}"
                                            class="btn btn-secondary waves-effect">Kembali</a>
                                    </div>
                                </div>
                            </div>
                    </form>
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
        let parentUrls = @json($parent->pluck('url', 'id'));
        $(document).ready(function() {
            // Trigger change jika form dibuka ulang
            $('#pilih_menu').trigger('change');
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

        $('#pilih_menu').val('{{ old('pilih_menu', 'parent') }}');
        $('#menu_parent').val('{{ old('menu_parent') }}');
        // Set preview icon jika ada nilai lama
        $('#icon-preview').attr('class', '{{ old('icon') }}');
        // Logic menu child/parent
        $('#pilih_menu').on('change', function() {
            if ($(this).val() === 'child') {
                $('#parentMenuGroup').show();
            } else {
                $('#parentMenuGroup').hide();
                $('#kode').val('');
            }
        });

        // Saat pilih parent menu
        $('#menu_parent').on('change', function() {
            $kode = $(this).find('option:selected').data('kode')+ "." || '' ;
            console.log($kode);

            $('#kode').val($kode);
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
