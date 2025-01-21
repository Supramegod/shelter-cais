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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-5')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">BPJS</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                @foreach($quotation->quotation_site as $site)
                  <h6>{{$site->nama_site}}</h6>
                @endforeach
              </div>
              <div class="row mb-3">
                <div class="row mb-3 mt-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="jenis-perusahaan">Jenis Perusahaan</label>
                    <div class="input-group">
                      <select id="jenis-perusahaan" name="jenis-perusahaan" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($jenisPerusahaan as $data)
                        <option value="{{$data->id}}" data-resiko="{{$data->resiko}}" @if($quotation->jenis_perusahaan_id == $data->id) selected @endif>{{$data->nama}}</option>  
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="resiko">Resiko</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="resiko" id="resiko" value="{{$quotation->resiko}}" readonly>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-sm-6">
                    <label class="form-label" for="penjamin">Penjamin</label>
                    <div class="input-group">
                      <select id="penjamin" name="penjamin" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        <option value="BPJS" @if($quotation->penjamin == 'BPJS') selected @elseif($quotation->penjamin ==null) selected @endif>BPJS</option>
                        <option value="Takaful" @if($quotation->penjamin == 'Takaful') selected @endif>Takaful</option>
                      </select>
                    </div>
                  </div>
                  <div id="d-bpjs" class="col-sm-6 d-none">
                    <label class="form-label" for="program-bpjs">Program BPJS</label>
                    <div class="input-group">
                      <select id="program-bpjs" name="program-bpjs" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        <option value="2 BPJS" @if($quotation->program_bpjs == '2 BPJS') selected @endif>2 BPJS ( BPJS JKK , BPJS JKM ) </option>  
                        <option value="3 BPJS" @if($quotation->program_bpjs == '3 BPJS') selected @endif>3 BPJS ( BPJS JKK , BPJS JKM , BPJS JHT )</option>
                        <option value="4 BPJS" @if($quotation->program_bpjs == '4 BPJS') selected @elseif($quotation->program_bpjs ==null) selected @endif>4 BPJS ( BPJS JKK , BPJS JKM , BPJS JHT , BPJS JP )</option>
                      </select>
                    </div>
                    <span class="text-warning">*Program BPJS selain 4 program membutuhkan persetujuan</span>
                  </div>
                  <div id="d-nominal-takaful" class="col-sm-6 d-none">
                    <label class="form-label" for="nominal-takaful">Nominal takaful</label>
                    <div class="input-group">
                      <input type="text" class="form-control mask-nominal text-end" value="{{$quotation->nominal_takaful}}" id="nominal-takaful" name="nominal-takaful">
                    </div>
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
  $(document).ready(function(){

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
        
    $(document).ready(function() {
      $('#jenis-perusahaan').select2();
    });

    $('#jenis-perusahaan').on('change', function() {
      let id = '#resiko';
      
      $(id).val($(this).find(':selected').data('resiko'));
    });

    showBpjs(1);

    function showBpjs(first) {
      let selected = $("#penjamin option:selected").val();
      console.log(selected);
      
      if (selected=="BPJS") {
        $('#d-bpjs').removeClass('d-none');
        $('#d-nominal-takaful').addClass('d-none');
      }else{
        $('#d-bpjs').addClass('d-none');
        $('#d-nominal-takaful').removeClass('d-none');
      }
    }
    $('#penjamin').on('change', function() {
      showBpjs(2);
    });


    $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });
    $('#btn-submit').on('click',function(e){
      e.preventDefault();
      var form = $(this).parents('form');
      let msg = "";
      let obj = $("form").serializeObject();
        
      if(obj['jenis-perusahaan'] == null || obj['jenis-perusahaan'] == ""){
        msg += "<b>Jenis Perusahaan</b> belum dipilih </br>";
      }
      if(obj['penjamin'] == null || obj['penjamin'] == ""){
        msg += "<b>Penjamin </b> belum dipilih </br>";
      }else if(obj['penjamin'] == "BPJS" ){
        if(obj['program-bpjs'] == null || obj['program-bpjs'] == ""){
          msg += "<b>Program BPJS </b> belum dipilih </br>";
        }
      }else if(obj['penjamin'] == "Takaful" ){
        if(obj['nominal-takaful'] == null || obj['nominal-takaful'] == ""){
          msg += "<b>Nominal Takaful </b> belum dipilih </br>";
        }
      }
      

      if(obj['resiko'] == null || obj['resiko'] == ""){
        msg += "<b>Resiko </b> belum dipilih </br>";
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
  });
  
</script>
@endsection