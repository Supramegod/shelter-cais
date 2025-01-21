@extends('layouts.master')
@section('title','Training')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Training Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Jenis Training <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <div class="position-relative">
                <select id="jenis" name="jenis" class="form-select @if ($errors->any()) @if($errors->has('jenis')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih Jenis Training -</option>
                    <option value="Pengetahuan Pengamanan" @if(old('jenis') == 'Pengetahuan Pengamanan') selected @endif>Pengetahuan Pengamanan</option>
                    <option value="Pengetahuan Pendukung" @if(old('jenis') == 'Pengetahuan Pendukung') selected @endif>Pengetahuan Pendukung</option>
                    <option value="Peraturan Baris Berbaris dan Penghormatan" @if(old('jenis') == 'Peraturan Baris Berbaris dan Penghormatan') selected @endif>Peraturan Baris Berbaris dan Penghormatan</option>
                </select>
                @if($errors->has('branch_id'))
                  <div class="invalid-feedback">{{$errors->first('jenis')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <input type="text" id="nama" name="nama" value="{{old('nama')}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">JP <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="number" id="jp" name="jp" value="{{old('jp')}}" class="form-control @if ($errors->any()) @if($errors->has('jp')) is-invalid @else   @endif @endif">
              @if($errors->has('jp'))
                  <div class="invalid-feedback">{{$errors->first('jp')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Menit <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="number" id="menit" name="menit" value="{{old('menit')}}" class="form-control @if ($errors->any()) @if($errors->has('menit')) is-invalid @else   @endif @endif">
              @if($errors->has('menit'))
                  <div class="invalid-feedback">{{$errors->first('menit')}}</div>
              @endif
            </div>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('training')}}" class="btn btn-secondary waves-effect">Kembali</a>
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