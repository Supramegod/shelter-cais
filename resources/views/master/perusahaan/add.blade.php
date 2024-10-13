@extends('layouts.master')
@section('title','Jenis Perusahaan')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Jenis Perusahaan Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Jenis Perusahaan</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('perusahaan.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" id="nama" name="nama" value="{{old('nama')}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Resiko <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <div class="position-relative">
                <select id="resiko" name="resiko" class="form-select @if ($errors->any()) @if($errors->has('resiko')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih resiko -</option>
                <option value="Sangat Rendah" @if(old('resiko') == 'Sangat Rendah') selected @endif>Sangat Rendah</option>
                <option value="Rendah" @if(old('resiko') == 'Rendah') selected @endif>Rendah</option>
                <option value="Sedang" @if(old('resiko') == 'Sedang') selected @endif>Sedang</option>
                <option value="Tinggi" @if(old('resiko') == 'Tinggi') selected @endif>Tinggi</option>
                <option value="Sangat Tinggi" @if(old('resiko') == 'Sangat Tinggi') selected @endif>Sangat Tinggi</option>
                </select>
                @if($errors->has('resiko'))
                  <div class="invalid-feedback">{{$errors->first('resiko')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('perusahaan')}}" class="btn btn-secondary waves-effect">Kembali</a>
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
</script>
@endsection