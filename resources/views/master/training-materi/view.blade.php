@extends('layouts.master')
@section('title','Training')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Training</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-materi.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Jenis Training <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <input type="text" id="jenis" name="jenis" value="{{$data->jenis}}" class="form-control @if ($errors->any()) @if($errors->has('jenis')) is-invalid @else   @endif @endif">
              @if($errors->has('jenis'))
                  <div class="invalid-feedback">{{$errors->first('jenis')}}</div>
              @endif
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama Training <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <textarea class="form-control h-px-100 @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif" name="nama" id="nama" placeholder="">{{$data->nama}}</textarea>    
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
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
                <span class="me-1">Update Materi</span>
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
    window.location.replace("{{route('training-materi')}}");
  });
</script>
@endsection