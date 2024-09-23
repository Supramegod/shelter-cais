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
                    <ul class="nav nav-tabs nav-fill" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-home" aria-controls="navs-justified-home" aria-selected="true">
                          <i class="tf-icons mdi mdi-account-hard-hat-outline me-1"></i> Direct Labour
                        </button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false" tabindex="-1">
                          <i class="tf-icons mdi mdi-security me-1"></i> Security
                        </button>
                      </li>
                    <span class="tab-slider" style="left: 0px; width: 226.484px; bottom: 0px;"></span></ul>
                  </div>
                  <div class="tab-content p-0">
                    <div class="tab-pane fade active show" id="navs-justified-home" role="tabpanel">
                      <div class="row mb-3 mt-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Provinsi</label>
                          <div class="input-group">
                            <select id="provinsi" name="provinsi" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Aceh</option>  
                              <option value="">Jawa Timur</option>
                              <option value="">Jawa Barat</option>  
                              <option value="">Jawa Tengah</option>  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Kabupaten / Kota</label>
                          <div class="input-group">
                            <select id="kota" name="kota" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Surabaya</option>  
                              <option value="">Sidoarjo</option>
                              <option value="">Jombang</option>  
                              <option value="">Gresik</option>  
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-2">
                        <h4 class="text-center">Upah</h4>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon checked">
                            <label class="form-check-label custom-option-content" for="umk">
                              <span class="custom-option-body">
                                <span class="custom-option-title">UMK</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="umk-1" checked="">
                            </label>
                          </div>
                        </div>
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon">
                            <label class="form-check-label custom-option-content" for="ump">
                              <span class="custom-option-body">
                                <span class="custom-option-title">UMP</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="ump-1">
                            </label>
                          </div>
                        </div>
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon">
                            <label class="form-check-label custom-option-content" for="custom">
                              <span class="custom-option-body">
                                <span class="custom-option-title">Custom</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="custom-1">
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-3 d-none" id="d-custom-upah-1">
                        <div class="col-sm-12">
                          <label class="form-label" for="basic-default-password42">Masukkan Upah</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="basic-default-password42">
                          </div>
                        </div>
                        <span>*Gaji dibawah UMP membutuhkan persetujuan terlebih dahulu</span>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Manajemen Fee</label>
                          <div class="input-group">
                            <select id="manajemen_fee" name="manajemen_fee" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Total Upah</option>  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Persentase</label>
                          <div class="input-group">
                            <input type="number" class="form-control" placeholder="">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                    <div class="row mb-3 mt-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Provinsi</label>
                          <div class="input-group">
                            <select id="provinsi" name="provinsi" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Aceh</option>  
                              <option value="">Jawa Timur</option>
                              <option value="">Jawa Barat</option>  
                              <option value="">Jawa Tengah</option>  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Kabupaten / Kota</label>
                          <div class="input-group">
                            <select id="kota" name="kota" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Surabaya</option>  
                              <option value="">Sidoarjo</option>
                              <option value="">Jombang</option>  
                              <option value="">Gresik</option>  
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-2">
                        <h4 class="text-center">Upah</h4>
                      </div>
                      <div class="row mb-3">
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon checked">
                            <label class="form-check-label custom-option-content" for="umk">
                              <span class="custom-option-body">
                                <span class="custom-option-title">UMK</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="umk-2" checked="">
                            </label>
                          </div>
                        </div>
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon">
                            <label class="form-check-label custom-option-content" for="ump">
                              <span class="custom-option-body">
                                <span class="custom-option-title">UMP</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="ump-2">
                            </label>
                          </div>
                        </div>
                        <div class="col-md mb-md-0 mb-2">
                          <div class="form-check custom-option custom-option-icon">
                            <label class="form-check-label custom-option-content" for="custom">
                              <span class="custom-option-body">
                                <span class="custom-option-title">Custom</span>
                              </span>
                              <input name="upah" class="form-check-input" type="radio" value="" id="custom-2">
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="row mb-3 d-none" id="d-custom-upah-2">
                        <div class="col-sm-12">
                          <label class="form-label" for="basic-default-password42">Masukkan Upah</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="basic-default-password42">
                          </div>
                        </div>
                        <span>*Gaji dibawah UMP membutuhkan persetujuan terlebih dahulu</span>
                      </div>
                      <div class="row mb-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Manajemen Fee</label>
                          <div class="input-group">
                            <select id="manajemen_fee" name="manajemen_fee" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              <option value="">Total Upah</option>  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="basic-default-password42">Persentase</label>
                          <div class="input-group">
                            <input type="number" class="form-control" placeholder="">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                  <a href="{{route('quotation.edit-3',1)}}" class="btn btn-primary btn-back w-20">
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
  $('#custom-1').click(function() {
    if($('#custom-1').is(':checked')) { 
      $('#d-custom-upah-1').removeClass('d-none');
    }
  });
  $('#ump-1').click(function() {
    if($('#ump-1').is(':checked')) {
      $('#d-custom-upah-1').addClass('d-none');
    }
  });
  $('#umk-1').click(function() {
    if($('#umk-1').is(':checked')) {
      $('#d-custom-upah-1').addClass('d-none');
    }
  });

  $('#custom-2').click(function() {
    if($('#custom-2').is(':checked')) { 
      $('#d-custom-upah-2').removeClass('d-none');
    }
  });
  $('#ump-2').click(function() {
    if($('#ump-2').is(':checked')) {
      $('#d-custom-upah-2').addClass('d-none');
    }
  });
  $('#umk-2').click(function() {
    if($('#umk-2').is(':checked')) {
      $('#d-custom-upah-2').addClass('d-none');
    }
  });
  
</script>
@endsection