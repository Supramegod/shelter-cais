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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-7')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">KAPORLAP / SERAGAM</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @foreach($listJenis as $data)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th rowspan="2" style="vertical-align: middle;">{{$data->nama}}</th>
                        <th rowspan="2" style="vertical-align: middle;">Harga / Unit</th>
                        <th colspan="2">Kebutuhan</th>
                      </tr>
                      <tr class="table-primary">
                        <th>Security Guard</th>
                        <th>SC</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listKaporlap as $detail)
                      @if($detail->jenis_barang_id == $data->id)
                        <tr>
                          <td>{{$detail->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($detail->harga,0,",",".")}}</td>
                          <td class="sg">
                            <button type="button" type="button" class="min-sg btn rounded-pill btn-danger waves-effect waves-light">
                              <span class="mdi mdi-minus"></span> &nbsp;
                            </button>
                            <input type="hidden" name="barang[]" value="{{$detail->id}}">
                            <input type="number" class="input-sg text-center" name="sg_{{$detail->id}}" value="{{$detail->jumlah_sg}}" style="max-width:50px;margin-left:5px;margin-right:5px" readonly>
                            <button type="button" type="button" class="add-sg btn rounded-pill btn-primary waves-effect waves-light">
                              <span class="mdi mdi-plus"></span> &nbsp;
                            </button>
                          </td>
                          <td class="sc">
                            <button type="button" type="button" class="min-sc btn rounded-pill btn-danger waves-effect waves-light">
                              <span class="mdi mdi-minus"></span> &nbsp;
                            </button>
                            <input type="number" class="input-sc text-center" name="sc_{{$detail->id}}" value="{{$detail->jumlah_sc}}" style="max-width:50px;margin-left:5px;margin-right:5px" readonly>
                            <button type="button" type="button" class="add-sc btn rounded-pill btn-primary waves-effect waves-light">
                              <span class="mdi mdi-plus"></span> &nbsp;
                            </button>
                          </td>
                        </tr>
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
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
  $('.min-sg').on('click',function(){
    let val = $(this).closest('.sg').find('.input-sg').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)-1;
    }
    if(newVal <0){
      newVal = 0;
    }
    $(this).closest('.sg').find('.input-sg').val(newVal);
  });

  $('.add-sg').on('click',function(){
    let val = $(this).closest('.sg').find('.input-sg').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)+1;
    }
    $(this).closest('.sg').find('.input-sg').val(newVal);
  });

  $('.min-sc').on('click',function(){
    let val = $(this).closest('.sc').find('.input-sc').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)-1;
    }
    if(newVal <0){
      newVal = 0;
    }
    $(this).closest('.sc').find('.input-sc').val(newVal);
  });

  $('.add-sc').on('click',function(){
    let val = $(this).closest('.sc').find('.input-sc').val();
    let newVal = 0;

    if(val!=null && val !=""){
      newVal = parseInt(val)+1;
    }
    $(this).closest('.sc').find('.input-sc').val(newVal);
  });
</script>
@endsection