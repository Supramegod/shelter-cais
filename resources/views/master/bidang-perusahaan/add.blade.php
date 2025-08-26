@extends('layouts.master')
@section('title', 'Tambah Bidang Perusahaan')

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master / </span> Bidang Perusahaan Baru</h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Tambah Bidang Perusahaan</span>
            <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
          </div>
        </h5>

        <form class="card-body" action="{{ route('bidang-perusahaan.save') }}" method="POST">
          @csrf

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama Bidang Perusahaan <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama bidang perusahaan">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">
                  <i class="mdi mdi-content-save"></i> Simpan
                </button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">
                  <i class="mdi mdi-refresh"></i> Reset
                </button>
                <a href="{{ route('bidang-perusahaan') }}" class="btn btn-secondary waves-effect">
                  <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
<script>
  @if(session()->has('success'))
    Swal.fire({
      title: 'Berhasil!',
      html: '{!! session()->get('success') !!}',
      icon: 'success',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    }).then(function() {
      // Redirect ke halaman index setelah sukses
      window.location.href = "{{ route('bidang-perusahaan.index') }}";
    });
  @endif

  @if(session()->has('error'))
    Swal.fire({
      title: 'Error!',
      html: '{!! session()->get('error') !!}',
      icon: 'error',
      customClass: {
        confirmButton: 'btn btn-danger waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif

  @if($errors->any())
    Swal.fire({
      title: 'Validasi Error!',
      html: '@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach',
      icon: 'warning',
      customClass: {
        confirmButton: 'btn btn-warning waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif
</script>
@endsection