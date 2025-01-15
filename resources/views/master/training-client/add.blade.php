@extends('layouts.master')
@section('title','Training Client')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Trainer Client</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training Client</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-client.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Bussiness Unit <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <select id="laman_id" name="laman_id" class="form-select @if ($errors->any()) @if($errors->has('laman_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
              <option value="">- Pilih Bussiness Unit -</option>  
                  @foreach($listBu as $value)
                  <option value="{{$value->id}}" @if(old('laman_id') == $value->id) selected @endif>{{$value->laman}}</option>
                  @endforeach
                </select>
                @if($errors->has('laman_id'))
                  <div class="invalid-feedback">{{$errors->first('laman_id')}}</div>
                @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Area <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <select id="area_id" name="area_id" class="form-select @if ($errors->any()) @if($errors->has('area_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
              <option value="">- Pilih Area -</option>  
                  @foreach($listArea as $value)
                  <option value="{{$value->id}}" @if(old('area_id') == $value->id) selected @endif>{{$value->area}}</option>
                  @endforeach
                </select>
                @if($errors->has('area_id'))
                  <div class="invalid-feedback">{{$errors->first('area_id')}}</div>
                @endif
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Client <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <input type="text" id="client" name="client" value="{{old('client')}}" class="form-control @if ($errors->any()) @if($errors->has('client')) is-invalid @else   @endif @endif">
              @if($errors->has('client'))
                  <div class="invalid-feedback">{{$errors->first('client')}}</div>
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Kab / Kota <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="text" id="kab_kota" name="kab_kota" value="{{old('kab_kota')}}" class="form-control @if ($errors->any()) @if($errors->has('kab_kota')) is-invalid @else   @endif @endif">
              @if($errors->has('kab_kota'))
                  <div class="invalid-feedback">{{$errors->first('kab_kota')}}</div>
              @endif
            </div>

            <label class="col-sm-2 col-form-label text-sm-end">Jumlah Karyawan <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="number" id="jml_karyawan" name="jml_karyawan" value="{{old('jml_karyawan')}}" class="form-control @if ($errors->any()) @if($errors->has('jml_karyawan')) is-invalid @else   @endif @endif">
              @if($errors->has('jml_karyawan'))
                  <div class="invalid-feedback">{{$errors->first('jml_karyawan')}}</div>
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal Gabung <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="date" id="tgl_gabung" name="tgl_gabung" value="{{old('tgl_gabung')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_gabung')) is-invalid @else   @endif @endif">
              @if($errors->has('tgl_gabung'))
                  <div class="invalid-feedback">{{$errors->first('tgl_gabung')}}</div>
              @endif
            </div>

            <label class="col-sm-2 col-form-label text-sm-end">Target Per Tahun <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="number" id="target_per_tahun" name="target_per_tahun" value="{{old('target_per_tahun')}}" class="form-control @if ($errors->any()) @if($errors->has('target_per_tahun')) is-invalid @else   @endif @endif">
              @if($errors->has('target_per_tahun'))
                  <div class="invalid-feedback">{{$errors->first('target_per_tahun')}}</div>
              @endif
            </div>
          </div>
          
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('training-client')}}" class="btn btn-secondary waves-effect">Kembali</a>
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