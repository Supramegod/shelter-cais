@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
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
          <div class="step active" data-target="#personal-info-1">
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
          <div class="step" data-target="#social-links-1">
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
          <div class="step" data-target="#social-links-1">
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
          <form onSubmit="return false">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">DETAIL KONTRAK</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : PT. Setia Hati Sejahtera Tbk.</h6>
              </div>
              <div class="row mb-2">
                <h6 class="text-center">Jumlah Site</h6>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon checked">
                    <label class="form-check-label custom-option-content" for="customRadioIcon1">
                      <span class="custom-option-body">
                        <i class="mdi mdi-map-marker-outline"></i>
                        <span class="custom-option-title">Single Site</span>
                      </span>
                      <input name="site" class="form-check-input" type="radio" value="" id="customRadioIcon1" checked="">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="customRadioIcon2">
                      <span class="custom-option-body">
                        <i class="mdi mdi-map-marker-multiple-outline"></i>
                        <span class="custom-option-title">Multi Site</span>
                      </span>
                      <input name="site" class="form-check-input" type="radio" value="" id="customRadioIcon2">
                    </label>
                  </div>
                </div>
              </div>
              <hr style="margin-top: 1rem;
  margin-bottom: 1rem;
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, 0.1);"/>
              <div class="row mb-2 mt-5">
                <h6 class="text-center">Jenis Kontrak</h6>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon checked">
                    <label class="form-check-label custom-option-content" for="pkwt">
                      <span class="custom-option-body">
                        <i class="mdi mdi-file-sign"></i>
                        <span class="custom-option-title">PKWT</span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" type="radio" value="" id="pkwt" checked="">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="pkhl">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-outline"></i>
                        <span class="custom-option-title"> PKHL </span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" type="radio" value="" id="pkhl">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="borongan">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-group-outline"></i>
                        <span class="custom-option-title"> Borongan </span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" type="radio" value="" id="borongan">
                    </label>
                  </div>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                <a href="{{route('quotation.edit-1',1)}}" class="btn btn-primary btn-back w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">back</span>
                    <i class="mdi mdi-arrow-left"></i>
                  </a>
                  <a href="{{route('quotation.edit-3',1)}}" class="btn btn-primary btn-next w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                    <i class="mdi mdi-arrow-right"></i>
                  </a>
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
@endsection