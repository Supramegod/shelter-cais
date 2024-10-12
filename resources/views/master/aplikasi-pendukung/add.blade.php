@extends('layouts.master')
@section('title','Aplikasi Pendukung')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Aplikasi Pendukung Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Aplikasi Pendukung</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('aplikasi-pendukung.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama" name="nama" value="{{old('nama')}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Link Icon</label>
            <div class="col-sm-4">
              <input type="text" id="link_icon" name="link_icon" value="{{old('link_icon')}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Harga <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="harga" name="harga" value="{{old('harga')}}" class="form-control @if ($errors->any()) @if($errors->has('harga')) is-invalid @else   @endif @endif">
              @if($errors->has('harga'))
                  <div class="invalid-feedback">{{$errors->first('harga')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Barang <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="barang_id" name="barang_id" class="form-select @if ($errors->any()) @if($errors->has('barang_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih barang -</option>  
                  @foreach($listBarang as $value)
                  <option value="{{$value->id}}" @if(old('barang_id') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('barang_id'))
                  <div class="invalid-feedback">{{$errors->first('barang_id')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('aplikasi-pendukung')}}" class="btn btn-secondary waves-effect">Kembali</a>
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
<script>
  var elem = document.getElementById("harga");

  elem.addEventListener("keydown",function(event){
      var key = event.which;
      if((key<48 || key>57) && key != 8) event.preventDefault();
  });

  elem.addEventListener("keyup",function(event){
      var value = this.value.replace(/,/g,"");
      this.dataset.currentValue=parseInt(value);
      var caret = value.length-1;
      while((caret-3)>-1)
      {
          caret -= 3;
          value = value.split('');
          value.splice(caret+1,0,",");
          value = value.join('');
      }
      this.value = value;
  });
</script>
@endsection