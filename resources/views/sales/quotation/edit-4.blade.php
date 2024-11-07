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
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                <h6>Site : {{$quotation->nama_site}} - {{$quotation->kebutuhan}}</h6>
              </div>
              <div class="row mb-3">
                <div class="row mb-2">
                  <h4 class="text-center">Upah</h4>
                </div>
                <div class="row mb-3">
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon hide-custom @if($quotation->upah == 'UMP') checked @endif">
                      <label class="form-check-label custom-option-content" for="ump">
                        <span class="custom-option-body">
                          <span class="custom-option-title">UMP</span>
                          <span class="label-provinsi">{{$dataProvinsi->name}}</span><br>
                          <span class="label-provinsi">{{$dataProvinsi->ump}}</span>
                        </span>
                        <input name="upah" class="form-check-input" type="radio" value="UMP" id="ump" @if($quotation->upah == 'UMP') checked @endif>
                      </label>
                    </div>
                  </div>
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon hide-custom @if($quotation->upah == 'UMK') checked @endif">
                      <label class="form-check-label custom-option-content" for="umk">
                        <span class="custom-option-body">
                          <span class="custom-option-title">UMK</span>
                          <span class="label-kota">{{$dataKota->name}}</span><br>
                          <span class="label-kota">{{$dataKota->umk}}</span>
                        </span>
                        <input name="upah" class="form-check-input" type="radio" value="UMK" id="umk" @if($quotation->upah == 'UMK') checked @endif>
                      </label>
                    </div>
                  </div>
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon show-custom @if($quotation->upah == 'Custom') checked @endif">
                      <label class="form-check-label custom-option-content" for="custom">
                        <span class="custom-option-body">
                          <span class="custom-option-title">Custom</span><br>
                          <span>&nbsp;</span>
                        </span>
                        <input name="upah" class="form-check-input" type="radio" value="Custom" id="custom" @if($quotation->upah == 'Custom') checked @endif>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="row mb-3 d-none" id="d-custom-upah">
                  <div class="col-sm-12">
                    <label class="form-label" for="custom-upah">Masukkan Upah</label>
                    <div class="input-group">
                      <input type="text" class="form-control mask-nominal" value="{{$quotation->nominal_upah}}" name="custom-upah" id="custom-upah">
                    </div>
                  </div>
                  <span class="text-warning">*Gaji dibawah UMK membutuhkan persetujuan</span>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="manajemen_fee">Manajemen Fee</label>
                    <div class="input-group">
                      <select id="manajemen_fee" name="manajemen_fee" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($manfee as $data)
                          <option value="{{$data->id}}" @if($quotation->management_fee_id == $data->id) selected @endif>{{$data->nama}}</option>  
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="persentase">Persentase</label>
                    <div class="input-group">
                      <input type="number" class="form-control" name="persentase" value="{{$quotation->persentase}}">
                      <span class="input-group-text">%</span>
                    </div>
                    @if($quotation->kebutuhan=="SECURITY")
                      <span class="text-warning">*MF dibawah 7% membutuhkan persetujuan</span>
                    @else
                    <span class="text-warning">*MF dibawah 6% membutuhkan persetujuan</span>
                    @endif
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-12">
                    <label class="form-label" for="ppn_pph_dipotong">Hitungan PPn & PPh</label>
                    <div class="input-group">
                      <select id="ppn_pph_dipotong" name="ppn_pph_dipotong" class="form-select" data-allow-clear="true" tabindex="-1">
                      <option value="Management Fee" @if($quotation->ppn_pph_dipotong==null || $quotation->ppn_pph_dipotong=="" ||$quotation->ppn_pph_dipotong=="Management Fee") selected @endif>Management Fee</option>  
                      <option value="Total Invoice" @if($quotation->ppn_pph_dipotong=="Total Invoice") selected @endif>Total Invoice</option>  
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="ada_thr">Tunjangan Hari Raya</label>
                      <select id="ada_thr" name="ada_thr" class="form-select" data-allow-clear="true" tabindex="-1">
                      <option value="" @if($quotation->thr=="") selected @endif>- Pilih Data -</option>  
                      <option value="Ada" @if($quotation->thr!="" && $quotation->thr!="Tidak Ada") selected @endif>Ada</option>  
                      <option value="Tidak Ada" @if($quotation->thr=="Tidak Ada") selected @endif>Tidak Ada</option>  
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
                  <div class="col-sm-4">
                    <label class="form-label" for="ada_tunjangan_holiday">Tunjangan Holiday</label>
                      <select id="ada_tunjangan_holiday" name="ada_tunjangan_holiday" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="" @if($quotation->tunjangan_holiday=="" || $quotation->tunjangan_holiday==null) selected @endif>- Pilih Data -</option>  
                        <option value="Ada" @if($quotation->tunjangan_holiday!=null && $quotation->tunjangan_holiday!="" && $quotation->tunjangan_holiday!="Tidak Ada") selected @endif>Ada</option>  
                        <option value="Tidak Ada" @if($quotation->tunjangan_holiday=="Tidak Ada") selected @endif>Tidak Ada</option>  
                      </select>
                  </div>
                  <div class="col-sm-4 ada_tunjangan_holiday">
                    <label class="form-label" for="tunjangan_holiday">Normatif / Flat</label>
                      <select id="tunjangan_holiday" name="tunjangan_holiday" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="Normatif" @if($quotation->tunjangan_holiday=="Normatif") selected @endif>Normatif</option>  
                        <option value="Flat" @if($quotation->tunjangan_holiday=="Flat") selected @endif>Flat</option>  
                      </select>
                  </div>
                  <div class="col-sm-4 d-nominal-tunjangan-holiday">
                    <label class="form-label" for="nominal_tunjangan_holiday">Nominal Tunjangan Holiday</label>
                    <input type="text" class="form-control mask-nominal" value="{{$quotation->nominal_tunjangan_holiday}}" name="nominal_tunjangan_holiday" id="nominal_tunjangan_holiday">
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
                    <label class="form-label" for="lembur">Normatif / Flat</label>
                      <select id="lembur" name="lembur" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="" @if($quotation->lembur==null || $quotation->lembur=="" ) selected @endif>- Pilih data -</option>  
                        <option value="Normatif" @if($quotation->lembur=="Normatif") selected @endif>Normatif</option>  
                        <option value="Flat" @if($quotation->lembur=="Flat") selected @endif>Flat</option>  
                      </select>
                  </div>
                  <div class="col-sm-4 d-nominal-lembur">
                    <label class="form-label" for="nominal_lembur">Nominal Lembur</label>
                    <input type="text" class="form-control mask-nominal" value="{{$quotation->nominal_lembur}}" name="nominal_lembur" id="nominal_lembur">
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
$('.show-custom').on('click',function(){
    $('#d-custom-upah').removeClass('d-none');
    $('#custom-upah').val('');
  });

  $('.hide-custom').on('click',function(){
    $('#d-custom-upah').addClass('d-none');
  });

  @if($quotation->upah=="Custom")
    $('#d-custom-upah').removeClass('d-none');

    var $this = $('#custom-upah');
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

  if(obj['upah'] == null || obj['upah'] == ""){
    msg += "<b>Jenis Upah</b> belum dipilih </br>";
  }
  if(obj['upah'] =="Custom"){
    if(obj['custom-upah'] == null || obj['custom-upah'] == ""){
      msg += "<b>Costum Upah</b> belum dipilih </br>";
    }
  }
  if(obj['manajemen_fee'] == null || obj['manajemen_fee'] == ""){
    msg += "<b>Manajemen Fee </b> belum dipilih </br>";
  }
  if(obj['persentase'] == null || obj['persentase'] == ""){
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
    }else{
      if(obj.tunjangan_holiday =="Flat"){
        if(obj.nominal_tunjangan_holiday==null || obj.nominal_tunjangan_holiday==""){
          msg += "<b>Nominal Tunjangan Holiday</b> belum diisi </br>";
        }
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

tunjanganHolidayFlat(1);
function tunjanganHolidayFlat(first) {
  let selected = $("#tunjangan_holiday option:selected").val();
    
  if (selected!="Flat") {
    $('.d-nominal-tunjangan-holiday').addClass('d-none');
  }else{
    $('.d-nominal-tunjangan-holiday').removeClass('d-none');
  }
}

$('#tunjangan_holiday').on('change', function() {
  tunjanganHolidayFlat(2);
});

</script>
@endsection