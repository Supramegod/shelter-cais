@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Quotation</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-leads"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('lengkapi-quotation.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">QUOTATION DARI PKS NOMOR : {{$pks->nomor}}</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Leads <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="pks_id" name="pks_id" value="{{$pks->id}}" class="form-control">
                <div class="input-group">
                    <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{$pks->nama_perusahaan}}" class="form-control" readonly>
                </div>
              </div>
            </div>
            <div id="show_isian">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Layanan <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <select id="layanan" name="layanan" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih data -</option>
                    <option value="1" @if($pks->layanan_id =="1") selected @endif>Security</option>
                    <option value="2" @if($pks->layanan_id =="2") selected @endif>Direct Labour</option>
                    <option value="3" @if($pks->layanan_id =="3") selected @endif>Cleaning Service</option>
                    <option value="4" @if($pks->layanan_id =="4") selected @endif>Logistik</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Entitas <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <select id="entitas" name="entitas" class="form-select" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                    </select>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Jumlah Site <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <select id="jumlah_site" name="jumlah_site" class="form-select" data-allow-clear="true" tabindex="-1">
                      <option value="Single Site">Single Site</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Provinsi  <span class="text-danger fw-bold">*</span></label>
                    <div class="col-sm-4">
                      <select id="provinsi" name="provinsi" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($province as $data)
                          <option value="{{$data->id}}" @if($pks->provinsi_id == $data->id) selected @endif>{{$data->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <label class="col-sm-2 col-form-label text-sm-end">Kabupaten/Kota  <span class="text-danger fw-bold">*</span></label>
                    <div class="col-sm-4">
                      <select id="kota" name="kota" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($kota as $data)
                          <option value="{{$data->id}}" @if($pks->kota_id == $data->id) selected @endif>{{$data->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Nama Site  <span class="text-danger fw-bold">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" id="nama_site" name="nama_site" value="{{$pks->nama_site}}" class="form-control">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Alamat Penempatan <span class="text-danger fw-bold">*</span></label>
                    <div class="col-sm-10">
                      <input type="text" id="penempatan" name="penempatan" value="{{$pks->alamat_site}}" class="form-control">
                    </div>
                </div>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-12 d-flex flex-row-reverse">
                <button type="button" id="btn-submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Quotation</span>
                  <i class="mdi mdi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>
</div>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();

    if(obj.layanan == null || obj.layanan == "" ){
      msg += "<b>Layanan</b> belum dipilih </br>";
    };
    if(obj.entitas == null || obj.entitas == "" ){
      msg += "<b>Entitas</b> belum dipilih </br>";
    };
    if(obj.nama_site == null || obj.nama_site == "" ){
        msg += "<b>Nama Site</b> belum diisi </br>";
    }
    if(obj.provinsi == null || obj.provinsi == "" ){
        msg += "<b>Provinsi</b> belum diisi </br>";
    }
    if(obj.kota == null || obj.kota == "" ){
        msg += "<b>Kota</b> belum diisi </br>";
    }
    if(obj.penempatan == null || obj.penempatan == "" ){
        msg += "<b>Penempatan</b> belum diisi </br>";
    }
    if(msg == ""){
      form.submit();
    }else{
      Swal.fire({
        title: "Pemberitahuan",
        html: msg,
        icon: "warning"
      });
    }
  });

  $('#provinsi').on('change', function() {
    $('#kota').find('option').remove();
    $('#kota').append('<option value="">- Pilih data -</option>');
    if(this.value!=""){
      var param = "province_id="+this.value;
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            $('#kota').append('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
    }
  });

$(document).ready(function() {
  @if($leads!=null)
    pilihLayanan($('#layanan').find(":selected")[0]);
  @endif

  function pilihLayanan(element) {
    $('#entitas').find('option').remove();
    $('#entitas').append('<option value="">- Pilih data -</option>');
    if(element.value!=""){
      if (element.value == 1) {
        @foreach($company as $value)
          @if($value->code=="GSU" || $value->code=="SN")
          $('#entitas').append('<option value="{{$value->id}}" @if($pks->company_id) selected @endif>{{$value->code}} | {{$value->name}}</option>');
          @endif
          @endforeach
      } else if (element.value == 2 || element.value == 4) {
        @foreach($company as $value)
          @if($value->code=="SIG" || $value->code=="SNI")
          $('#entitas').append('<option value="{{$value->id}}" @if($pks->company_id) selected @endif>{{$value->code}} | {{$value->name}}</option>');
          @endif
        @endforeach
      } else if (element.value == 3) {
        @foreach($company as $value)
          @if($value->code=="RCI" || $value->code=="SNI")
          $('#entitas').append('<option value="{{$value->id}}" @if($pks->company_id) selected @endif>{{$value->code}} | {{$value->name}}</option>');
          @endif
        @endforeach
      }
      $('#entitas').append('<option value="17" @if($pks->company_id) selected @endif>IONS | PT. Indah Optimal Nusantara</option>');
    }
  }
  $('#layanan').on('change', function() {
    pilihLayanan(this);
  });
});
</script>
@endsection
