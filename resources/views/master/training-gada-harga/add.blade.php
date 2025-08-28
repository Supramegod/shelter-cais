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
            <span>Form Harga Training Gada </span>
            <!-- <span>{{$now}}</span> -->
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-gada-harga.save')}}" method="POST">
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
            <label class="col-sm-2 col-form-label text-sm-end">Harga <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <input type="number" id="harga" name="harga" value="{{old('harga')}}" class="form-control @if ($errors->any()) @if($errors->has('harga')) is-invalid @else   @endif @endif">
              @if($errors->has('harga'))
                  <div class="invalid-feedback">{{$errors->first('harga')}}</div>
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
                <a href="{{route('training-gada-harga')}}" class="btn btn-secondary waves-effect">Kembali</a>
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