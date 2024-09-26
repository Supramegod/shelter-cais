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
          <div class="step crossed" data-target="#account-details-1">
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
                  <span class="bs-stepper-title">Aplikasi Pendukung</span>
                  <span class="bs-stepper-subtitle">Informasi Aplikasi Pendukung</span>
                </span>
              </span>
            </button>
          </div>
          <div class="line"></div>
          <div class="step" data-target="#social-links-1">
            <button type="button" class="step-trigger">
              <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
              <span class="bs-stepper-label">
                <span class="bs-stepper-number">07</span>
                <span class="d-flex flex-column gap-1 ms-2">
                  <span class="bs-stepper-title">Perjanjian</span>
                  <span class="bs-stepper-subtitle">Informasi Perjanjian</span>
                </span>
              </span>
            </button>
          </div>
        </div>
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-6')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">APLIKASI PENDUKUNG</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
                @foreach($aplikasiPendukung as $value)
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon @if(in_array($value->id,$arrAplikasiSel)) checked @endif">
                      <label class="form-check-label custom-option-content">
                        <span class="custom-option-body">
                            <img src="{{$value->link_icon}}" alt="{{$value->nama}}" style="max-width:60px">
                          <span class="custom-option-title">{{$value->nama}}</span>
                        </span>
                        <input name="aplikasi_pendukung[]" class="form-check-input" type="checkbox" value="{{$value->id}}" @if(in_array($value->id,$arrAplikasiSel)) checked @endif>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                <a href="{{route('quotation.edit-5',$quotation->id)}}" class="btn btn-primary btn-back w-20">
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
</script>
@endsection