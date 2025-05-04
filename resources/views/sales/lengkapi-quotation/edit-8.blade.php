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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-8')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">DEVICES ( PERALATAN ) </h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                @foreach($quotation->quotation_site as $site)
                  <h6>{{$site->nama_site}}</h6>
                @endforeach
              </div>
              <div class="row">
                <div class="col-md-12 mb-4 mb-md-0">
                  <div class="accordion accordion-popout mt-3" id="accordionPopout">
                    @foreach($listJenis as $data)
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading-{{$data->id}}">
                        <button type="button" class="accordion-button" style="background-color:#e0e2ff" data-bs-toggle="collapse" data-bs-target="#accordionPopout-{{$data->id}}" aria-expanded="false" aria-controls="accordionPopout-{{$data->id}}">
                          {{$data->nama}}
                        </button>
                      </h2>

                      <div id="accordionPopout-{{$data->id}}" class="accordion-collapse collapse" aria-labelledby="heading-{{$data->id}}" data-bs-parent="#accordionPopout" style="">
                        <div class="accordion-body table-responsive text-nowrap">
                          <table class="table">
                            <thead class="text-center">
                              <tr class="table-primary">
                                <th>Nama</th>
                                <th>Harga / Unit</th>
                                <th class="text-center">Jumlah</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($listDevices as $detail)
                                @if($detail->jenis_barang_id == $data->id)
                                @if($data->id==17 && $detail->jumlah==0)
                                @continue
                                @endif
                                <tr>
                                  <td>{{$detail->nama}}</td>
                                  <td style="text-align:right">Rp {{number_format($detail->harga,0,",",".")}}<input type="hidden" name="barang[]" value="{{$detail->id}}">                          </td>
                                  @if($data->id == 17)
                                    <td class="text-center" colspan="{{count($quotation->quotation_detail)}}">{{$detail->jumlah}}</td>
                                  @else
                                  <td class="jumlah" style="min-width:150px">
                                    <button type="button" type="button" class="min-jumlah btn rounded-pill btn-danger waves-effect waves-light">
                                      <span class="mdi mdi-minus"></span> &nbsp;
                                    </button>
                                      <input type="number" class="input-jumlah text-center" name="jumlah_{{$detail->id}}" value="@php echo $detail->jumlah @endphp" data-harga="{{$detail->harga}}" style="max-width:50px;margin-left:5px;margin-right:5px">
                                    <button type="button" type="button" class="add-jumlah btn rounded-pill btn-primary waves-effect waves-light">
                                      <span class="mdi mdi-plus"></span> &nbsp;
                                    </button>
                                  </td>
                                  @endif
                                </tr>
                                @endif
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="row mt-5">
                <table class="table w-100">
                  <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td class="total-semua" style="text-align:right"></td>
                    </tr>
                  </tbody>
                </table>
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

  hitungJumlah();

  function hitungJumlah() {
    let jumlah =0;
    let jumlahAplikasi =0;

    $('.input-jumlah').each(function( index ) {
        let qty = parseInt($(this).val()) || 0;
        let harga = parseInt($(this).data('harga')) || 0;
        let subtotal = qty * harga;
        jumlah += subtotal;
    });

    @foreach($listDevices as $detail)
      @if($detail->jenis_barang_id == 17 && $detail->jumlah>0)
        jumlahAplikasi += {{$detail->harga*$detail->jumlah}};
      @endif
    @endforeach

    jumlah += jumlahAplikasi;
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
