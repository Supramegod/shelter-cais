@extends('layouts.master')
@section('title','Training Trainer')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Trainer Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training Trainer</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-trainer.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Divisi <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <select id="divisi_id" name="divisi_id" class="form-select @if ($errors->any()) @if($errors->has('divisi_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
              <option value="">- Pilih Divisi -</option>  
                  @foreach($listDivisi as $value)
                  <option value="{{$value->id}}" @if(old('divisi_id') == $value->id) selected @endif>{{$value->divisi}}</option>
                  @endforeach
                </select>
                @if($errors->has('divisi_id'))
                  <div class="invalid-feedback">{{$errors->first('divisi_id')}}</div>
                @endif
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Trainer <span class="text-danger">*</span></label>
            <div class="col-sm-6">
              <input type="text" id="trainer" name="trainer" value="{{old('trainer')}}" class="form-control @if ($errors->any()) @if($errors->has('trainer')) is-invalid @else   @endif @endif">
              @if($errors->has('trainer'))
                  <div class="invalid-feedback">{{$errors->first('trainer')}}</div>
              @endif
            </div>
          </div>
          
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('training-trainer')}}" class="btn btn-secondary waves-effect">Kembali</a>
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