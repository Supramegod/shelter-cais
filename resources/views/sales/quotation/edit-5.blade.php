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
                          <label class="form-label" for="jenis-perusahaan-{{$value->id}}">Jenis Perusahaan</label>
                          <div class="input-group">
                            <select id="jenis-perusahaan-{{$value->id}}" name="jenis-perusahaan-{{$value->id}}" class="form-select" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              @foreach($jenisPerusahaan as $data)
                              <option value="{{$data->id}}" data-resiko="{{$data->resiko}}" @if($value->jenis_perusahaan_id == $data->id) selected @endif>{{$data->nama}}</option>  
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="resiko-{{$value->id}}">Resiko</label>
                          <div class="input-group">
                            <input type="text" class="form-control" name="resiko-{{$value->id}}" id="resiko-{{$value->id}}" value="{{$value->resiko}}" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-12">
                          <label class="form-label" for="program-bpjs-{{$value->id}}">Program BPJS</label>
                          <div class="input-group">
                            <select id="program-bpjs-{{$value->id}}" name="program-bpjs-{{$value->id}}" class="form-select" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="2 BPJS" @if($value->program_bpjs == '2 BPJS') selected @endif>2 BPJS</option>  
                              <option value="3 BPJS" @if($value->program_bpjs == '3 BPJS') selected @endif>3 BPJS</option>
                              <option value="4 BPJS" @if($value->program_bpjs == '4 BPJS') selected @endif>4 BPJS</option>
                            </select>
                          </div>
                          <span class="text-warning">*Program BPJS selain 4 program membutuhkan persetujuan</span>
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
  $(document).ready(function(){
    @foreach($quotationKebutuhan as $value)
    
    $(document).ready(function() {
      $('#jenis-perusahaan-{{$value->id}}').select2();
    });
    

    $('#jenis-perusahaan-{{$value->id}}').on('change', function() {
      let id = '#resiko-{{$value->id}}';
      
      $(id).val($(this).find(':selected').data('resiko'));
    });
  @endforeach
  });
  
</script>
@endsection