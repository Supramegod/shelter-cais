@extends('layouts.master')
@section('title','Training Area')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Trainer Area</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Training Area</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('training-area.save')}}" method="POST">
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
              <input type="text" id="area" name="area" value="{{old('area')}}" class="form-control @if ($errors->any()) @if($errors->has('area')) is-invalid @else   @endif @endif">
              @if($errors->has('area'))
                  <div class="invalid-feedback">{{$errors->first('area')}}</div>
              @endif
            </div>
          </div>
          
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('training-area')}}" class="btn btn-secondary waves-effect">Kembali</a>
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