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
          <div class="step crossed" data-target="#social-links-1">
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
          <div class="step crossed" data-target="#social-links-1">
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
          <div class="step active" data-target="#social-links-1">
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
                <h6 class="mb-3">PERJANJIAN</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : PT. Setia Hati Sejahtera Tbk.</h6>
              </div>
              <div class="row mb-3">
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Perjanjian</th>
                        <th class="text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <tr>
                        <td>1</td>
                        <td>...</td>
                        <td>
                          
                        </td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>...</td>
                        <td>
                          
                        </td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>...</td>
                        <td>
                          
                        </td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td>...</td>
                        <td>
                          
                        </td>
                      </tr>
                      <tr>
                        <td>5</td>
                        <td>...</td>
                        <td>
                          <div class="col-12 d-flex justify-content-center">
                            <button class="btn btn-danger btn-back w-20">
                              <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-12 d-flex justify-content-center">
                  <button class="btn btn-info btn-back w-50">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Perjanjian</span>
                    <i class="mdi mdi-plus"></i>
                  </button>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                <a href="{{route('quotation.edit-5',1)}}" class="btn btn-primary btn-back w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">back</span>
                    <i class="mdi mdi-arrow-left"></i>
                  </a>
                  <a href="{{route('quotation.view',1)}}" class="btn btn-primary btn-next w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Summary</span>
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