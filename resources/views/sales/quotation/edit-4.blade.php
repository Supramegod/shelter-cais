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
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-4')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">UPAH DAN MANAGEMENT FEE</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mb-3">
                <div class="col-xl-12">
                  <div class="nav-align-top">
                    <ul class="nav nav-fill nav-tabs" role="tablist" >
                      @foreach($quotationKebutuhan as $value)
                        <li class="nav-item" role="presentation">
                          <button type="button" class="nav-link waves-effect @if($loop->first) active @endif" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-{{$value->id}}" aria-controls="navs-justified-{{$value->id}}" aria-selected="true">
                            <i class="tf-icons {{$value->icon}} me-1"></i> 
                            {{$value->kebutuhan}}
                          </button>
                        </li>
                      @endforeach
                      <span class="tab-slider" style="left: 0px; width: 226.484px; bottom: 0px;"></span>
                    </ul>
                  </div>
                  <div class="tab-content p-0">
                    @foreach($quotationKebutuhan as $value)
                      <div class="tab-pane fade @if($loop->first) active show @endif" id="navs-justified-{{$value->id}}" role="tabpanel">
                        <div class="row mb-3 mt-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="provinsi-{{$value->id}}">Provinsi</label>
                            <div class="input-group">
                              <select id="provinsi-{{$value->id}}" name="provinsi-{{$value->id}}" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="">- Pilih data -</option>
                                @foreach($province as $data)
                                  <option value="{{$data->id}}" data-ump="{{$data->ump}}" @if($value->provinsi_id == $data->id) selected @endif>{{$data->name}}</option>  
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <label class="form-label" for="kota-{{$value->id}}">Kabupaten / Kota</label>
                            <div class="input-group">
                              <select id="kota-{{$value->id}}" name="kota-{{$value->id}}" class="form-select" data-allow-clear="true" tabindex="-1">
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-2">
                          <h4 class="text-center">Upah</h4>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon hide-custom-{{$value->id}} @if($value->upah == 'UMP') checked @endif">
                              <label class="form-check-label custom-option-content" for="ump">
                                <span class="custom-option-body">
                                  <span class="custom-option-title">UMP</span>
                                  <span id="label-provinsi">&nbsp;</span>
                                </span>
                                <input name="upah-{{$value->id}}" class="form-check-input" type="radio" value="UMP" id="ump-{{$value->id}}" @if($value->upah == 'UMP') checked @endif>
                              </label>
                            </div>
                          </div>
                          <div class="col-md mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon hide-custom-{{$value->id}} @if($value->upah == 'UMK') checked @endif">
                              <label class="form-check-label custom-option-content" for="umk">
                                <span class="custom-option-body">
                                  <span class="custom-option-title">UMK</span>
                                  <span id="label-kota">&nbsp;</span>
                                </span>
                                <input name="upah-{{$value->id}}" class="form-check-input" type="radio" value="UMK" id="umk-{{$value->id}}" @if($value->upah == 'UMK') checked @endif>
                              </label>
                            </div>
                          </div>
                          <div class="col-md mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon show-custom-{{$value->id}} @if($value->upah == 'Custom') checked @endif">
                              <label class="form-check-label custom-option-content" for="custom">
                                <span class="custom-option-body">
                                  <span class="custom-option-title">Custom</span>
                                  <span>&nbsp;</span>
                                </span>
                                <input name="upah-{{$value->id}}" class="form-check-input" type="radio" value="Custom" id="custom-{{$value->id}}" @if($value->upah == 'Custom') checked @endif>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-3 d-none" id="d-custom-upah-{{$value->id}}">
                          <div class="col-sm-12">
                            <label class="form-label" for="custom-upah-{{$value->id}}">Masukkan Upah</label>
                            <div class="input-group">
                              <input type="text" class="form-control mask-nominal" value="{{$value->nominal_upah}}" name="custom-upah-{{$value->id}}" id="custom-upah-{{$value->id}}">
                            </div>
                          </div>
                          <span class="text-warning">*Gaji dibawah UMK membutuhkan persetujuan</span>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="manajemen_fee_{{$value->id}}">Manajemen Fee</label>
                            <div class="input-group">
                              <select id="manajemen_fee_{{$value->id}}" name="manajemen_fee_{{$value->id}}" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="">- Pilih data -</option>
                                @foreach($manfee as $data)
                                  <option value="{{$data->id}}" @if($value->management_fee_id == $data->id) selected @endif>{{$data->nama}}</option>  
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <label class="form-label" for="persentase_{{$value->id}}">Persentase</label>
                            <div class="input-group">
                              <input type="number" class="form-control" name="persentase_{{$value->id}}" value="{{$value->persentase}}">
                              <span class="input-group-text">%</span>
                            </div>
                            @if($value->kebutuhan=="Security")
                              <span class="text-warning">*MF dibawah 7% membutuhkan persetujuan</span>
                            @else
                            <span class="text-warning">*MF dibawah 6% membutuhkan persetujuan</span>
                            @endif
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="ada_thr">Tunjangan Hari Raya</label>
                              <select id="ada_thr" name="ada_thr" class="form-select" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->thr=="") selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->thr!="") selected @endif>Ada</option>  
                              <option value="Tidak Ada" @if($quotation->thr=="" && $quotation->thr!=null) selected @endif>Tidak Ada</option>  
                              </select>
                          </div>
                          <div class="col-sm-6 ada_thr">
                            <label class="form-label" for="thr">Provisi / Ditagihkan</label>
                              <select id="thr" name="thr" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="Diprovisikan" @if($quotation->thr=="Diprovisikan") selected @endif>Diprovisikan</option>  
                                <option value="Ditagihkan" @if($quotation->thr=="Ditagihkan") selected @endif>Ditagihkan</option>  
                              </select>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="ada_kompensasi">Kompensasi</label>
                              <select id="ada_kompensasi" name="ada_kompensasi" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="" @if($quotation->kompensasi=="" || $quotation->kompensasi==null) selected @endif>- Pilih Data -</option>  
                                <option value="Ada" @if($quotation->kompensasi!=null && $quotation->kompensasi!="" && $quotation->kompensasi!="Tidak Ada") selected @endif>Ada</option>  
                                <option value="Tidak Ada" @if($quotation->kompensasi=="Tidak Ada") selected @endif>Tidak Ada</option>  
                              </select>
                          </div>
                          <div class="col-sm-6 ada_kompensasi">
                            <label class="form-label" for="kompensasi">Provisi / Ditagihkan</label>
                              <select id="kompensasi" name="kompensasi" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="Diprovisikan" @if($quotation->kompensasi=="Diprovisikan") selected @endif>Diprovisikan</option>  
                                <option value="Ditagihkan" @if($quotation->kompensasi=="Ditagihkan") selected @endif>Ditagihkan</option>  
                              </select>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="ada_tunjangan_holiday">Tunjangan Holiday</label>
                              <select id="ada_tunjangan_holiday" name="ada_tunjangan_holiday" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="" @if($quotation->tunjangan_holiday=="" || $quotation->tunjangan_holiday==null) selected @endif>- Pilih Data -</option>  
                                <option value="Ada" @if($quotation->tunjangan_holiday!=null && $quotation->tunjangan_holiday!="" && $quotation->tunjangan_holiday!="Tidak Ada") selected @endif>Ada</option>  
                                <option value="Tidak Ada" @if($quotation->tunjangan_holiday=="Tidak Ada") selected @endif>Tidak Ada</option>  
                              </select>
                          </div>
                          <div class="col-sm-6 ada_tunjangan_holiday">
                            <label class="form-label" for="tunjangan_holiday">Provisi / Ditagihkan</label>
                              <select id="tunjangan_holiday" name="tunjangan_holiday" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="Diprovisikan" @if($quotation->tunjangan_holiday=="Diprovisikan") selected @endif>Diprovisikan</option>  
                                <option value="Ditagihkan" @if($quotation->tunjangan_holiday=="Ditagihkan") selected @endif>Ditagihkan</option>  
                              </select>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-4">
                            <label class="form-label" for="ada_lembur">Lembur</label>
                              <select id="ada_lembur" name="ada_lembur" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="" @if($quotation->lembur=="" || $quotation->lembur==null) selected @endif>- Pilih Data -</option>  
                                <option value="Ada" @if($quotation->lembur!=null && $quotation->lembur!="" && $quotation->lembur!="Tidak Ada") selected @endif>Ada</option>  
                                <option value="Tidak Ada" @if($quotation->lembur=="Tidak Ada") selected @endif>Tidak Ada</option>  
                              </select>
                          </div>
                          <div class="col-sm-4 ada_lembur">
                            <label class="form-label" for="lembur">Flat / Tidak Flat</label>
                              <select id="lembur" name="lembur" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="" @if($quotation->lembur==null || $quotation->lembur=="" ) selected @endif>- Pilih data -</option>  
                                <option value="Flat" @if($quotation->lembur=="Flat") selected @endif>Flat</option>  
                                <option value="Tidak Flat" @if($quotation->lembur=="Tidak Flat") selected @endif>Tidak Flat</option>  
                              </select>
                          </div>
                          <div class="col-sm-4 d-nominal-lembur">
                            <label class="form-label" for="nominal_lembur">Nominal Lembur</label>
                            <input type="text" class="form-control mask-nominal" value="{{$quotation->nominal_lembur}}" name="nominal_lembur" id="nominal_lembur">
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
              @include('sales.quotation.action')
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
@foreach($quotationKebutuhan as $value)
  $('.show-custom-{{$value->id}}').on('click',function(){
    $('#d-custom-upah-{{$value->id}}').removeClass('d-none');
    $('#custom-upah-{{$value->id}}').val('');
  });

  $('.hide-custom-{{$value->id}}').on('click',function(){
    $('#d-custom-upah-{{$value->id}}').addClass('d-none');
  });

  $('#kota-{{$value->id}}').on('change', function() {
    $('#label-kota').html($('#kota-{{$value->id}} option:selected').text()+" : "+$('#kota-{{$value->id}} option:selected').data("umk"));
  });

  $('#provinsi-{{$value->id}}').on('change', function() {
    $('#kota-{{$value->id}}').find('option').remove();
    $('#kota-{{$value->id}}').append('<option value="">- Pilih data -</option>');
    $('#label-provinsi').html($('#provinsi-{{$value->id}} option:selected').text()+" : "+$('#provinsi-{{$value->id}} option:selected').data("ump"));
    if(this.value!=""){
      var param = "province_id="+this.value;
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            $('#kota-{{$value->id}}').append('<option data-umk="'+element.umk+'" value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
    }
  });

  @if($value->provinsi_id != null)

  $('#label-provinsi').html($('#provinsi-{{$value->id}} option:selected').text()+" : "+$('#provinsi-{{$value->id}} option:selected').data("ump"));

    var param = "province_id="+{{$value->provinsi_id}};
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            if(element.id == {{$value->kota_id}}){
              selected = "selected";
              $('#label-kota').html(element.name+" : "+element.umk);
            };

            $('#kota-{{$value->id}}').append('<option data-umk="'+element.umk+'" value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
  @endif

  @if($value->upah=="Custom")
    $('#d-custom-upah-{{$value->id}}').removeClass('d-none');

    var $this = $('#custom-upah-{{$value->id}}');
    // Get the value.
    var input = $this.val();
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt(input, 10) : 0;
    input += 0;
    $this.val(function() {
      return (input === 0) ? "" : input.toLocaleString("id-ID");
    });

  @endif

  $('#btn-submit').on('click',function(e){
  e.preventDefault();
  var form = $(this).parents('form');
  let msg = "";
  let obj = $("form").serializeObject();

  if(obj['provinsi-{{$value->id}}'] == null || obj['provinsi-{{$value->id}}'] == ""){
    msg += "<b>Provinsi</b> belum dipilih </br>";
  }
  if(obj['kota-{{$value->id}}'] == null || obj['kota-{{$value->id}}'] == ""){
    msg += "<b>Kabupaten / Kota </b> belum dipilih </br>";
  }

  if(obj['upah-{{$value->id}}'] == null || obj['upah-{{$value->id}}'] == ""){
    msg += "<b>Jenis Upah</b> belum dipilih </br>";
  }
  if(obj['upah-{{$value->id}}'] =="Custom"){
    if(obj['custom-upah-{{$value->id}}'] == null || obj['custom-upah-{{$value->id}}'] == ""){
      msg += "<b>Costum Upah</b> belum dipilih </br>";
    }
  }
  if(obj['manajemen_fee_{{$value->id}}'] == null || obj['manajemen_fee_{{$value->id}}'] == ""){
    msg += "<b>Manajemen Fee </b> belum dipilih </br>";
  }
  if(obj['persentase_{{$value->id}}'] == null || obj['persentase_{{$value->id}}'] == ""){
    msg += "<b>Persentase </b> belum diisi </br>";
  }
  if(obj.ada_thr==null || obj.ada_thr==""){
    msg += "<b>THR</b> belum dipilih </br>";
  }else{
    if(obj.ada_thr=="Ada"){
      if(obj.thr==null || obj.thr==""){
        msg += "<b>THR</b> belum dipilih </br>";
      }
    }
  }
  if(obj.ada_kompensasi==null || obj.ada_kompensasi==""){
    msg += "<b>Kompensasi</b> belum dipilih </br>";
  }else{
    if(obj.ada_kompensasi=="Ada"){
      if(obj.kompensasi==null || obj.kompensasi==""){
        msg += "<b>Kompensasi</b> belum dipilih </br>";
      }
    }
  }

  if(obj.ada_tunjangan_holiday==null || obj.ada_tunjangan_holiday==""){
    msg += "<b>Tunjangan Holiday</b> belum dipilih </br>";
  }else{
    if(obj.ada_tunjangan_holiday=="Ada"){
      if(obj.tunjangan_holiday==null || obj.tunjangan_holiday==""){
        msg += "<b>Tunjangan Holiday</b> belum dipilih </br>";
      }
    }
  }
  
  if(obj.ada_lembur==null || obj.ada_lembur==""){
    msg += "<b>Lembur</b> belum dipilih </br>";
  }else{
    if(obj.ada_lembur=="Ada"){
      if(obj.lembur==null || obj.lembur==""){
        msg += "<b>Tipe Lembur</b> belum dipilih </br>";
      }else{
        if(obj.lembur =="Flat"){
          if(obj.nominal_lembur==null || obj.nominal_lembur==""){
            msg += "<b>Nominal Lembur</b> belum diisi </br>";
          }
        }
      }
    }
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

@endforeach

 // validasi input

  $('form').bind("keypress", function(e) {
    if (e.keyCode == 13) {               
      e.preventDefault();
      return false;
    }
  });
  
  let extra = 0;
  $('.mask-nominal').on("keyup", function(event) {    
    // When user select text in the document, also abort.
    var selection = window.getSelection().toString();
    if (selection !== '') {
      return;
    }

    // When the arrow keys are pressed, abort.
    if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
      if (event.keyCode == 38) {
        extra = 1000;
      } else if (event.keyCode == 40) {
        extra = -1000;
      } else {
        return;
      }

    }

    var $this = $(this);
    // Get the value.
    var input = $this.val();
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt(input, 10) : 0;
    input += extra;
    extra = 0;
    $this.val(function() {
      return (input === 0) ? "" : input.toLocaleString("id-ID");
    });
  });


// script sendiri
showThr(1);
function showThr(first) {
  let selected = $("#ada_thr option:selected").val();
  
  if (selected!="Ada") {
    $('.ada_thr').addClass('d-none');
  }else{
    $('.ada_thr').removeClass('d-none');
    if(first!=1){
      $("#thr").val("").change();
    }
  }
}

$('#ada_thr').on('change', function() {
  showThr(2);
});

showKompensasi(1);
function showKompensasi(first) {
  let selected = $("#ada_kompensasi option:selected").val();
  
  if (selected!="Ada") {
    $('.ada_kompensasi').addClass('d-none');
  }else{
    $('.ada_kompensasi').removeClass('d-none');
    if(first!=1){
      $("#kompensasi").val("").change();
    }
  }
}

$('#ada_kompensasi').on('change', function() {
  showKompensasi(2);
});

showLembur(1);
function showLembur(first) {
  let selected = $("#ada_lembur option:selected").val();
  
  if (selected!="Ada") {
    $('.ada_lembur').addClass('d-none');
  }else{
    $('.ada_lembur').removeClass('d-none');
    if(first!=1){
      $("#lembur").val("").change();
    }
  }
}

$('#ada_lembur').on('change', function() {
  showLembur(2);
});

lemburFlat(1);
function lemburFlat(first) {
  let selected = $("#lembur option:selected").val();
    
  if (selected!="Flat") {
    $('.d-nominal-lembur').addClass('d-none');
  }else{
    $('.d-nominal-lembur').removeClass('d-none');
  }
}

$('#lembur').on('change', function() {
  lemburFlat(2);
});

showTunjanganHoliday(1);
function showTunjanganHoliday(first) {
  let selected = $("#ada_tunjangan_holiday option:selected").val();
  
  if (selected!="Ada") {
    $('.ada_tunjangan_holiday').addClass('d-none');
  }else{
    $('.ada_tunjangan_holiday').removeClass('d-none');
    if(first!=1){
      $("#tunjangan_holiday").val("").change();
    }
  }
}

$('#ada_tunjangan_holiday').on('change', function() {
  showTunjanganHoliday(2);
});

</script>
@endsection