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
      <div class="bs-stepper-header gap-lg-2">
        <div class="step crossed" data-target="#account-details-1">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-number">01</span>
              <span class="d-flex flex-column gap-1 ms-2">
                <span class="bs-stepper-title">Site & Jenis Kontrak</span>
                <span class="bs-stepper-subtitle">Pilih Site Dan Jenis Kontrak</span>
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
                <span class="bs-stepper-subtitle">Informasi Headcount & Posisi</span>
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
                <span class="bs-stepper-subtitle">Upah dan Management Fee</span>
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
                <span class="bs-stepper-subtitle">Informasi Detail Program BPJS</span>
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
                <h1 class="mb-0">DETAIL KONTRAK</h1>
                <h4>Informasi detail kontrak untuk quotation yang akan dibuat</h4>
                <h4>Leads/Customer : PT. Setia Hati Sejahtera Tbk.</h4>
              </div>
              <div class="row mb-2">
                <h4 class="text-center">Kebutuhan</h4>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon checked">
                    <label class="form-check-label custom-option-content" for="direct_labour">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-hard-hat-outline"></i>
                        <span class="custom-option-title">Direct Labour</span>
                      </span>
                      <input name="kebutuhan" class="form-check-input" type="checkbox" value="" id="direct_labour" checked="">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="security">
                      <span class="custom-option-body">
                        <i class="mdi mdi-security"></i>
                        <span class="custom-option-title">Security</span>
                      </span>
                      <input name="kebutuhan" class="form-check-input" type="checkbox" value="" id="security">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="cleaning_service">
                      <span class="custom-option-body">
                        <i class="mdi mdi-spray-bottle"></i>
                        <span class="custom-option-title">Cleaning Service</span>
                      </span>
                      <input name="kebutuhan" class="form-check-input" type="checkbox" value="" id="cleaning_service">
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon">
                    <label class="form-check-label custom-option-content" for="logistik">
                      <span class="custom-option-body">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span class="custom-option-title">Logistik</span>
                      </span>
                      <input name="kebutuhan" class="form-check-input" type="checkbox" value="" id="logistik">
                    </label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-12">
                  <label class="form-label" for="basic-default-password42">Entitas</label>
                  <div class="input-group">
                    <select id="entitas" name="entitas" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      <option value="">PT. SIG</option>  
                      <option value="">PT. Shelter Indonesia</option>  
                      <option value="">PT. Shelter Nusantara</option>  
                    </select>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="basic-default-password42">Tanggal Penempatan</label>
                  <div class="input-group">
                    <input type="date" class="form-control" id="basic-default-password42">
                  </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="basic-default-password42">Salary Rule</label>
                  <div class="input-group">
                    <select id="salary_rule" name="salary_rule" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      <option value="">1 Bulan</option>  
                      <option value="">Mingguan</option>  
                    </select>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="basic-default-password42">Mulai Kontrak</label>
                  <div class="input-group">
                    <input type="date" class="form-control" id="basic-default-password42">
                  </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="basic-default-password42">Kontrak Selesai</label>
                  <div class="input-group">
                    <input type="date" class="form-control" id="basic-default-password42">
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