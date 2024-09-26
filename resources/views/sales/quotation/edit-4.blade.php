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
        <div class="bs-stepper-header gap-lg-3 pt-5"  style="border-right:1px solid rgba(0, 0, 0, 0.1);">
          <div class="mt-5 step crossed" data-target="#account-details-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">01</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Site & Jenis Kontrak</span>
                  <span class="bs-stepper-subtitle">Informasi Site & Kontrak</span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step crossed" data-target="#personal-info-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">02</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Detail Kontrak</span>
                  <span class="bs-stepper-subtitle">Informasi detail kontrak</span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step crossed" data-target="#social-links-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">03</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Headcount</span>
                  <span class="bs-stepper-subtitle">Informasi Headcount </span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step active" data-target="#social-links-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">04</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Upah dan MF</span>
                  <span class="bs-stepper-subtitle">Informasi Upah dan MF</span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" data-target="#social-links-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">05</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">BPJS</span>
                  <span class="bs-stepper-subtitle">Informasi Program BPJS</span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" data-target="#social-links-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">06</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Perjanjian</span>
                  <span class="bs-stepper-subtitle">Informasi Perjanjian</span>
                </span>
              </span>
            </button>
          </div>
        </div>
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
                              <select id="provinsi-{{$value->id}}" name="provinsi-{{$value->id}}" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                                <option value="">- Pilih data -</option>
                                @foreach($province as $data)
                                  <option value="{{$data->id}}" @if($value->provinsi_id == $data->id) selected @endif>{{$data->name}}</option>  
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <label class="form-label" for="kota-{{$value->id}}">Kabupaten / Kota</label>
                            <div class="input-group">
                              <select id="kota-{{$value->id}}" name="kota-{{$value->id}}" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-2">
                          <h4 class="text-center">Upah</h4>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon hide-custom-{{$value->id}} @if($value->upah == 'UMK') checked @endif">
                              <label class="form-check-label custom-option-content" for="umk">
                                <span class="custom-option-body">
                                  <span class="custom-option-title">UMK</span>
                                  <span>Surabaya : Rp. 1.800.000</span>
                                </span>
                                <input name="upah-{{$value->id}}" class="form-check-input" type="radio" value="UMK" id="umk-{{$value->id}}" @if($value->upah == 'UMK') checked @endif>
                              </label>
                            </div>
                          </div>
                          <div class="col-md mb-md-0 mb-2">
                            <div class="form-check custom-option custom-option-icon hide-custom-{{$value->id}} @if($value->upah == 'UMP') checked @endif">
                              <label class="form-check-label custom-option-content" for="ump">
                                <span class="custom-option-body">
                                  <span class="custom-option-title">UMP</span>
                                  <span>Jawa Timur : Rp. 1.800.000</span>
                                </span>
                                <input name="upah-{{$value->id}}" class="form-check-input" type="radio" value="UMP" id="ump-{{$value->id}}" @if($value->upah == 'UMP') checked @endif>
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
                              <input type="number" class="form-control" value="{{$value->custom_upah}}" name="custom-upah-{{$value->id}}" id="custom-upah-{{$value->id}}">
                            </div>
                          </div>
                          <span class="text-warning">*Gaji dibawah UMP membutuhkan persetujuan</span>
                        </div>
                        <div class="row mb-3">
                          <div class="col-sm-6">
                            <label class="form-label" for="manajemen_fee_{{$value->id}}">Manajemen Fee</label>
                            <div class="input-group">
                              <select id="manajemen_fee_{{$value->id}}" name="manajemen_fee_{{$value->id}}" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
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
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                  <a href="{{route('quotation.edit-3',$quotation->id)}}" class="btn btn-primary btn-back w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">back</span>
                    <i class="mdi mdi-arrow-left"></i>
                  </a>
                  <button type="submit" class="btn btn-primary btn-next w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                    <i class="mdi mdi-arrow-right"></i>
                  </button>
                </div>
              </div>
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
  });
  $('.hide-custom-{{$value->id}}').on('click',function(){
    $('#d-custom-upah-{{$value->id}}').addClass('d-none');
  });

  $('#provinsi-{{$value->id}}').on('change', function() {
    $('#kota-{{$value->id}}').find('option').remove();
    $('#kota-{{$value->id}}').append('<option value="">- Pilih data -</option>');

    if(this.value!=""){
      var param = "province_id="+this.value;
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            $('#kota-{{$value->id}}').append('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
    }
  });

  @if($value->provinsi_id != null)
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
            };

            $('#kota-{{$value->id}}').append('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
  @endif

  @if($value->upah=="Custom")
    $('#d-custom-upah-{{$value->id}}').removeClass('d-none');
  @endif
@endforeach
</script>
@endsection