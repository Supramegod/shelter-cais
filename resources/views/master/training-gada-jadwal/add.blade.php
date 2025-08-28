@extends('layouts.master')
@section('title','Training Materi')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Training Materi Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Jadwal Training Gada </span>
            <!-- <span>{{$now}}</span> -->
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-gada-jadwal.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Business Unit <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <div class="position-relative">
                <select id="jenis" name="jenis" class="form-select @if ($errors->any()) @if($errors->has('jenis')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih Jenis Training -</option>
                    <option value="pratama" @if(old('jenis') == '1') selected @endif>Gada Pratama</option>
                    <option value="madya" @if(old('jenis') == '2') selected @endif>Gada Madya</option>
                    <option value="utama" @if(old('jenis') == '2') selected @endif>Gada Utama</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Hari <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <select id="hari" name="hari" class="form-select @if ($errors->any()) @if($errors->has('hari')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih Hari -</option>
                  <option value="minggu" @if(old('hari') == 'minggu') selected @endif>Minggu</option>
                  <option value="senin" @if(old('hari') == 'senin') selected @endif>Senin</option>
                  <option value="selasa" @if(old('hari') == 'selasa') selected @endif>Selasa</option>
                  <option value="rabu" @if(old('hari') == 'rabu') selected @endif>Rabu</option>
                  <option value="kamis" @if(old('hari') == 'kamis') selected @endif>Kamis</option>
                  <option value="jumat" @if(old('hari') == 'jumat') selected @endif>Jumat</option>
                  <option value="sabtu" @if(old('hari') == 'sabtu') selected @endif>Sabtu</option>
              </select>
              @if($errors->has('hari'))
                  <div class="invalid-feedback">{{$errors->first('hari')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <input type="date" id="tanggal" name="tanggal" value="{{old('tanggal')}}" class="form-control @if ($errors->any()) @if($errors->has('tanggal')) is-invalid @else   @endif @endif">
              @if($errors->has('tanggal'))
                  <div class="invalid-feedback">{{$errors->first('tanggal')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Keterangan <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="keterangan" id="keterangan" placeholder="">{{old('keterangan')}}</textarea>  
              @if($errors->has('keterangan'))
                  <div class="invalid-feedback">{{$errors->first('keterangan')}}</div>
              @endif
            </div>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('training-gada-jadwal')}}" class="btn btn-secondary waves-effect">Kembali</a>
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