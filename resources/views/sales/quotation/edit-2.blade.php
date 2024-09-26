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
            <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-2')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">DETAIL KONTRAK</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mb-2">
                <h4 class="text-center">Kebutuhan</h4>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '1')) checked @endif">
                    <label class="form-check-label custom-option-content" for="direct_labour">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-hard-hat-outline"></i>
                        <span class="custom-option-title">Direct Labour</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="1" id="direct_labour" @if(str_contains($quotation->kebutuhan_id, '1')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '2')) checked @endif">
                    <label class="form-check-label custom-option-content" for="security">
                      <span class="custom-option-body">
                        <i class="mdi mdi-security"></i>
                        <span class="custom-option-title">Security</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="2" id="security" @if(str_contains($quotation->kebutuhan_id, '2')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '3')) checked @endif">
                    <label class="form-check-label custom-option-content" for="cleaning_service">
                      <span class="custom-option-body">
                        <i class="mdi mdi-spray-bottle"></i>
                        <span class="custom-option-title">Cleaning Service</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="3" id="cleaning_service" @if(str_contains($quotation->kebutuhan_id, '3')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '4')) checked @endif">
                    <label class="form-check-label custom-option-content" for="logistik">
                      <span class="custom-option-body">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span class="custom-option-title">Logistik</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="4" id="logistik" @if(str_contains($quotation->kebutuhan_id, '4')) checked @endif>
                    </label>
                  </div>
                </div>
                @if($errors->has('kebutuhan'))
                  <span class="text-danger">{{$errors->first('kebutuhan')}}</span>
                @endif
              </div>
              <div class="row mb-3">
                <div class="col-sm-12">
                  <label class="form-label" for="basic-default-password42">Entitas</label>
                  <select id="entitas" name="entitas" class="select2 form-select select2-hidden-accessible @if($errors->has('entitas')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($company as $value)
                      <option value="{{$value->id}}" @if($quotation->company_id==$value->id) selected @endif>{{$value->code}} | {{$value->name}}</option>  
                      @endforeach
                    </select>
                    @if($errors->has('entitas'))
                      <span class="text-danger">{{$errors->first('entitas')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="mulai-kontrak">Mulai Kontrak</label>
                  <input type="date" name="mulai_kontrak" value="{{$quotation->mulai_kontrak}}" class="form-control @if($errors->has('mulai_kontrak')) is-invalid @endif" id="mulai-kontrak">
                    @if($errors->has('mulai_kontrak'))
                      <span class="text-danger">{{$errors->first('mulai_kontrak')}}</span>
                    @endif
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="kontrak-selesai">Kontrak Selesai</label>
                  <input type="date" name="kontrak_selesai" value="{{$quotation->kontrak_selesai}}" class="form-control @if($errors->has('kontrak_selesai')) is-invalid @endif" id="kontrak-selesai">
                    @if($errors->has('kontrak_selesai'))
                      <span class="text-danger">{{$errors->first('kontrak_selesai')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="tgl_penempatan">Tanggal Penempatan</label>
                  <input type="date" name="tgl_penempatan" value="{{$quotation->tgl_penempatan}}" class="form-control @if($errors->has('tgl_penempatan')) is-invalid @endif" id="tgl_penempatan">
                  @if($errors->has('tgl_penempatan'))
                    <span class="text-danger">{{$errors->first('tgl_penempatan')}}</span>
                  @endif
                  @if($errors->has('tgl_penempatan_kurang'))
                    <span class="text-danger">{{$errors->first('tgl_penempatan_kurang')}}</span>
                  @endif
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="salary_rule">Salary Rule</label>
                  <select id="salary_rule" name="salary_rule" class="select2 form-select select2-hidden-accessible @if($errors->has('salary_rule')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($salaryRule as $value)
                      <option value="{{$value->id}}"  @if($quotation->salary_rule_id==$value->id) selected @endif>{{$value->nama_salary_rule}}</option>  
                      @endforeach
                    </select>
                    @if($errors->has('salary_rule'))
                      <span class="text-danger">{{$errors->first('salary_rule')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 d-flex justify-content-between">
                <a href="{{route('quotation.edit-1',$quotation->id)}}" class="btn btn-primary btn-back w-20">
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
@endsection