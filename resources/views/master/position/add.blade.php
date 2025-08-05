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
                    <h5 class="card-header">
                        <div class="d-flex justify-content-between">
                            <span>Form Position</span>
                            <span>{{ $now }}</span>
                        </div>
                    </h5>
                    <form class="card-body overflow-hidden" action="{{ route('position.save') }}" method="POST">
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
                                                @if ($request->entitas == $entitas->id) selected @endif>{{ $entitas->name }}
                                                @if ($errors->has('entitas'))
                                                    <div class="invalid-feedback">{{ $errors->first('entitas') }}</div>
                                                @endif

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
                                                @if ($request->layanan == $layanan->id) selected @endif>{{ $layanan->nama }}
                                                @if ($errors->has('layanan'))
                                                    <div class="invalid-feedback">{{ $errors->first('layanan') }}</div>
                                                @endif

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
                                <input type="text" id="nama" name="nama" required value="{{ old('nama') }}"
                                    class="form-control @if ($errors->any()) @if ($errors->has('nama')) is-invalid @else @endif @endif">
                                @if ($errors->has('nama'))
                                    <div class="invalid-feedback">{{ $errors->first('nama') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-sm-end">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="form-floating form-floating-outline mb-2">
                                    <textarea class="form-control mt-3 h-px-100 @error('deskripsi') is-invalid @enderror" name="deskripsi" id="deskripsi"
                                        placeholder="" required>{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
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

                                    <a href="{{ route('position') }}" class="btn btn-secondary waves-effect">Kembali</a>
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
    </script>
@endsection
