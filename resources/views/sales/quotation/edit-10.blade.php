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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-10')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">CHEMICAL</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
              <div class="table-responsive text-nowrap">
                <table class="table" >
                  @foreach($listJenis as $data)
                  <thead class="text-center">
                    <tr class="table-primary">
                      <th style="vertical-align: middle;">{{$data->nama}}</th>
                      <th style="vertical-align: middle;">Harga / Unit</th>
                      <th>Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($listChemical as $detail)
                    @if($detail->jenis_barang_id == $data->id)
                      <tr>
                        <td>{{$detail->nama}}</td>
                        <td style="text-align:right">Rp {{number_format($detail->harga,0,",",".")}}</td>
                        <td class="jumlah" style="display:flex;flex=1;justify-content:center">
                          <button type="button" type="button" class="min-jumlah btn rounded-pill btn-danger waves-effect waves-light">
                            <span class="mdi mdi-minus"></span> &nbsp;
                          </button>
                          <input type="hidden" name="barang[]" value="{{$detail->id}}">
                          <input type="number" class="input-jumlah text-center" name="jumlah_{{$detail->id}}" value="{{$detail->jumlah}}" data-harga="{{$detail->harga}}" style="max-width:50px;margin-left:5px;margin-right:5px" readonly>
                          <button type="button" type="button" class="add-jumlah btn rounded-pill btn-primary waves-effect waves-light">
                            <span class="mdi mdi-plus"></span> &nbsp;
                          </button>
                        </td>
                      </tr>
                    @endif
                    @endforeach
                  </tbody>
                  @endforeach
                  <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td class="total-semua" style="text-align:right"></td>
                      <td>

                      </td>
                    </tr>
                  </tbody>
                </table>
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
  $('.min-jumlah').on('click',function(){
    let val = $(this).closest('.jumlah').find('.input-jumlah').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)-1;
    }
    if(newVal <0){
      newVal = 0;
    }
    $(this).closest('.jumlah').find('.input-jumlah').val(newVal);

    hitungJumlah();

  });

  $('.add-jumlah').on('click',function(){
    let val = $(this).closest('.jumlah').find('.input-jumlah').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)+1;
    }
    $(this).closest('.jumlah').find('.input-jumlah').val(newVal);

    hitungJumlah();
  });


  function hitungJumlah() {
    let jumlah =0;    
    $('.input-jumlah').each(function( index ) {
      let harga = parseInt($(this).val())*parseInt($(this).data('harga'));
      jumlah = jumlah+parseInt(harga);
    });
    $('.total-semua').text("Rp "+jumlah.toLocaleString('id-ID'));
  }

  $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });
    
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    form.submit();
  });
</script>
@endsection