@extends('layouts.master')
@section('title','Aplikasi Pendukung')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Aplikasi Pendukung</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Aplikasi Pendukung</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('aplikasi-pendukung.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" id="nama" name="nama" value="{{$data->nama}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Link Icon</label>
            <div class="col-sm-9">
              <input type="text" id="link_icon" name="link_icon" value="{{$data->link_icon}}" class="form-control @if ($errors->any()) @if($errors->has('link_icon')) is-invalid @else   @endif @endif">
              @if($errors->has('link_icon'))
                  <div class="invalid-feedback">{{$errors->first('link_icon')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Harga <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" id="harga" name="harga" value="{{$data->harga}}" class="form-control @if ($errors->any()) @if($errors->has('harga')) is-invalid @else   @endif @endif">
              @if($errors->has('harga'))
                  <div class="invalid-feedback">{{$errors->first('harga')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Barang <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <div class="position-relative">
                <select id="barang_id" name="barang_id" class="form-select @if ($errors->any()) @if($errors->has('barang_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih barang -</option>  
                  @foreach($listBarang as $value)
                  <option value="{{$value->id}}" @if($data->barang_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('barang_id'))
                  <div class="invalid-feedback">{{$errors->first('barang_id')}}</div>
                @endif
              </div>
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
                <span class="me-1">Update Aplikasi Pendukung</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>
            <hr class="my-4 mx-4">
            <div class="col-12 text-center mt-2">
              <button id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Delete Aplikasi Pendukung</span>
                <i class="mdi mdi-trash-can scaleX-n1-rtl"></i>
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
  
  $('#btn-delete').on('click',function () {
    $('form').attr('action', '{{route("aplikasi-pendukung.delete")}}');
    $('form').submit();
  });
  
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('aplikasi-pendukung')}}");
  });
</script>

<script>
  var elem = document.getElementById("harga");
  let harga = elem.value;
  var caret = harga.length-1;
  while((caret-3)>-1)
  {
    caret -= 3;
    harga = harga.split('');
    harga.splice(caret+1,0,",");
    harga = harga.join('');
  }
  this.harga = harga;
  $("#harga").val(harga);

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