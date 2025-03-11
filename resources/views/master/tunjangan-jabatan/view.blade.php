@extends('layouts.master')
@section('title','Tunjangan Jabatan')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Tunjangan Jabatan</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Tunjangan Jabatan</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('tunjangan-jabatan.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama" name="nama" value="{{$data->nama}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="kebutuhan_id" name="kebutuhan_id" class="form-select @if ($errors->any()) @if($errors->has('kebutuhan_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih Kebutuhan -</option>  
                  @foreach($listKebutuhan as $value)
                  <option value="{{$value->id}}" @if($data->kebutuhan_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('kebutuhan_id'))
                  <div class="invalid-feedback">{{$errors->first('kebutuhan_id')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan Detail <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <input name="kebutuhan_detail_id" id="kebutuhan_detail_id" type="number" class="form-control" value="{{$data->kebutuhan_detail_id}}" hidden readonly>
                <input name="kebutuhan_detail_nama" id="kebutuhan_detail_nama" type="text" class="form-control" value="{{$data->nama_kebutuhan_detail}}" readonly>
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Tunjangan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="tunjangan_id" name="tunjangan_id" class="form-select @if ($errors->any()) @if($errors->has('tunjangan_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih Tunjangan -</option>  
                  @foreach($listTunjangan as $value)
                  <option value="{{$value->id}}" @if($data->tunjangan_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('tunjangan_id'))
                  <div class="invalid-feedback">{{$errors->first('tunjangan_id')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nominal <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nominal" name="nominal" value="{{$data->nominal}}" class="form-control @if ($errors->any()) @if($errors->has('nominal')) is-invalid @else   @endif @endif">
              @if($errors->has('nominal'))
                  <div class="invalid-feedback">{{$errors->first('nominal')}}</div>
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
                <span class="me-1">Update Tunjangan Jabatan</span>
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
    window.location.replace("{{route('tunjangan-jabatan')}}");
  });
</script>

<script>
  var elem = document.getElementById("nominal");
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
  $("#nominal").val(harga);

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
<script>
  $('#kebutuhan_id').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
    
    Swal.fire({
      title: 'Now loading',
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });
    var param = "kebutuhan_id="+valueSelected;
    $.ajax({
      url: "{{route('tunjangan-jabatan.get-kebutuhan-detail')}}",
      type: 'GET',
      data: param,
      success: function(res) {
        $('#kebutuhan_detail_id').val(res.id);
        $('#kebutuhan_detail_nama').val(res.nama);
        Swal.close();
      }
    });
  });
</script>
@endsection