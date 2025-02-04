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
                  <div class="col-sm-12">
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
                  <div class="col-sm-12">
                    <label class="form-label" for="resiko">Resiko</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="resiko" id="resiko" value="{{$quotation->resiko}}" readonly>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-12">
                    <label class="form-label">Detail Program</label>
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                            <tr class="text-center fw-bold align-middle">
                              <th rowspan="2">No</th>
                              <th rowspan="2">Posisi</th>
                              <th colspan="4">BPJS</th>
                              <th rowspan="2">Kesehatan</th>
                              <th rowspan="2">Nominal Takaful</th>
                            </tr>
                            <tr class="text-center fw-bold align-middle">
                              <th>JKK</th>
                              <th>JKM</th>
                              <th>JHT</th>
                              <th>JP</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($quotation->quotation_detail as $index => $detail)
                          <tr class="text-center align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->jabatan_kebutuhan }}</td>
                            <td><input class="check-bpjs" type="checkbox" name="jkk[{{ $detail->id }}]" @if($detail->penjamin_kesehatan==null || $detail->is_bpjs_jkk=='1' ) checked @endif></td>
                            <td><input class="check-bpjs" type="checkbox" name="jkm[{{ $detail->id }}]" @if($detail->penjamin_kesehatan==null || $detail->is_bpjs_jkm=='1') checked @endif></td>
                            <td><input class="check-bpjs" type="checkbox" name="jht[{{ $detail->id }}]" @if($detail->penjamin_kesehatan==null || $detail->is_bpjs_jht=='1') checked @endif></td>
                            <td><input class="check-bpjs" type="checkbox" name="jp[{{ $detail->id }}]" @if($detail->penjamin_kesehatan==null || $detail->is_bpjs_jp=='1') checked @endif></td>
                            <td>
                              <select name="penjamin[{{ $detail->id }}]" class="form-select penjamin-select" data-detail="{{ $detail->id }}">
                                <option value="">- Pilih Penjamin -</option>
                                <option value="BPJS" @if($detail->penjamin_kesehatan == 'BPJS') selected @elseif($detail->penjamin_kesehatan==null) selected @endif>BPJS</option>
                                <option value="Takaful" @if($detail->penjamin_kesehatan == 'Takaful') selected @endif>Takaful</option>
                              </select>
                            </td>
                            <td><input type="number" value="{{$detail->nominal_takaful}}" name="nominal_takaful[{{ $detail->id }}]" class="form-control text-end nominal-takaful" disabled></td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <span class="text-warning">*Program BPJS selain 4 program membutuhkan persetujuan</span>
                  </div>

                  <script>
                    $(document).ready(function() {
                      $('.penjamin-select').on('change', function() {
                        let selected = $(this).val();
                        if (selected == 'Takaful') {
                          $(this).closest('tr').find('.nominal-takaful').prop('disabled', false);
                        } else {
                          $(this).closest('tr').find('.nominal-takaful').prop('disabled', true).val('');
                        }
                      });
                    });
                  </script>
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

    // let extra = 0;
    // $('.mask-nominal').on("keyup", function(event) {    
    //   // When user select text in the document, also abort.
    //   var selection = window.getSelection().toString();
    //   if (selection !== '') {
    //     return;
    //   }

    //   // When the arrow keys are pressed, abort.
    //   if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
    //     if (event.keyCode == 38) {
    //       extra = 1000;
    //     } else if (event.keyCode == 40) {
    //       extra = -1000;
    //     } else {
    //       return;
    //     }

    //   }

    //   var $this = $(this);
    //   // Get the value.
    //   var input = $this.val();
    //   var input = input.replace(/[\D\s\._\-]+/g, "");
    //   input = input ? parseInt(input, 10) : 0;
    //   input += extra;
    //   extra = 0;
    //   $this.val(function() {
    //     return (input === 0) ? "" : input.toLocaleString("id-ID");
    //   });
    // });
    
    // IMask(
    //         document.getElementById('mask-nominal'),
    //         {
    //             mask: 'Rp.num',
    //             blocks: {
    //             num: {
    //                 // nested masks are available!
    //                 mask: Number,
    //                 thousandsSeparator: '.'
    //             }
    //             }
    //         }
    //         )

    $(document).ready(function() {
      $('#jenis-perusahaan').select2();
    });

    $('#jenis-perusahaan').on('change', function() {
      let id = '#resiko';
      
      $(id).val($(this).find(':selected').data('resiko'));
    });

    showTakaful();

    function showTakaful(first) {
      let selected = $("#is_takaful option:selected").val();
      if (selected=="0" || selected==null || selected=="") {
        $('#d-nominal-takaful').addClass('d-none');
      }else{
        $('#d-nominal-takaful').removeClass('d-none');
      }
    }
    $('#is_takaful').on('change', function() {
      showTakaful();
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

      $('.penjamin-select').each(function() {
        let index = $(this).data('detail');
        let value = $(this).val();
        if (value == 'Takaful') {
          if (obj['nominal_takaful[' + index + ']'] == null || obj['nominal_takaful[' + index + ']'] == "") {
        msg += "<b>Nominal Takaful</b> belum diisi </br>";
          }
        }
      });

      // if(obj['is_takaful'] == null || obj['is_takaful'] == ""){
      //   msg += "<b>Takaful</b> belum dipilih </br>";
      // }else{
      //   if(obj['is_takaful'] == "1"){
      //     if(obj['nominal-takaful'] == null || obj['nominal-takaful'] == ""){
      //       msg += "<b>Nominal Takaful</b> belum diisi </br>";
      //     }
      //   }
      // }
      // if(obj['program-bpjs'] == null || obj['program-bpjs'] == ""){
      //   msg += "<b>Program BPJS</b> belum dipilih </br>";
      // }

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