@extends('layouts.master')
@section('title','Training Client')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Training Client</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training Client</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-client.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          
        
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Client </label>
            <div class="col-sm-5">
              <input readonly type="text" id="client" name="client" value="{{$data->nama_site}}" class="form-control @if ($errors->any()) @if($errors->has('client')) is-invalid @else   @endif @endif">
              @if($errors->has('client'))
                  <div class="invalid-feedback">{{$errors->first('client')}}</div>
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Bisnis Unit </label>
            <div class="col-sm-5">
              <input readonly type="text" id="area" name="area" value="{{$data->bu}}" class="form-control @if ($errors->any()) @if($errors->has('area')) is-invalid @else   @endif @endif">
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Area </label>
            <div class="col-sm-5">
              <input readonly type="text" id="area" name="area" value="{{$data->area}}" class="form-control @if ($errors->any()) @if($errors->has('area')) is-invalid @else   @endif @endif">
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Kab / Kota </label>
            <div class="col-sm-5">
              <input readonly type="text" id="kab_kota" name="kab_kota" value="{{$data->kota}}" class="form-control @if ($errors->any()) @if($errors->has('kab_kota')) is-invalid @else   @endif @endif">
              @if($errors->has('kab_kota'))
                  <div class="invalid-feedback">{{$errors->first('kab_kota')}}</div>
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal Gabung </label>
            <div class="col-sm-5">
              <input readonly type="text" id="kab_kota" name="kab_kota" value="{{$data->tgl_gabung}}" class="form-control @if ($errors->any()) @if($errors->has('kab_kota')) is-invalid @else   @endif @endif">
              @if($errors->has('kab_kota'))
                  <div class="invalid-feedback">{{$errors->first('kab_kota')}}</div>
              @endif
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Target Per Tahun <span class="text-danger">*</span></label>
            <div class="col-sm-2">
              <input type="number" id="target_per_tahun" name="target_per_tahun" value="{{$data->training_tahun}}" class="form-control @if ($errors->any()) @if($errors->has('target_per_tahun')) is-invalid @else   @endif @endif">
              @if($errors->has('target_per_tahun'))
                  <div class="invalid-feedback">{{$errors->first('target_per_tahun')}}</div>
              @endif
            </div>
          </div>

          <div class="pt-4">
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Action</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-dots-vertical mdi-24px"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="col-12 text-center">
              <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Update Target Training</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
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

  $('#btn-update').on('click',function () {
    $('form').submit();
  });
  
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('training-client')}}");
  });
</script>
@endsection