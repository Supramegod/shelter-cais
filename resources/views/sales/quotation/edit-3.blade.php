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
          <div class="step active" data-target="#social-links-1">
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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-3')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">HEADCOUNT</h6>
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
                    <span class="tab-slider" style="left: 0px; width: 226.484px; bottom: 0px;"></span></ul>
                  </div>
                  <div class="tab-content p-0">
                    @foreach($quotationKebutuhan as $value)
                    <div class="tab-pane fade @if($loop->first) active show @endif" id="navs-justified-{{$value->id}}" role="tabpanel">
                      <div class="row mb-3 mt-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="jabatan_detail_{{$value->id}}">Nama Posisi/Jabatan</label>
                          <div class="input-group">
                            <select id="jabatan_detail_{{$value->id}}" name="nama_jabatan" class="select2 form-select select2-hidden-accessible" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              @foreach($value->detail as $detail)
                                <option value="{{$detail->id}}">{{$detail->nama}}</option>  
                              @endforeach  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="jumlah_hc_{{$value->id}}">Jumlah Headcount</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="jumlah_hc_{{$value->id}}">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                          <button type="button" data-qbutuhid="{{$value->id}}" id="btn-tambah-detail-{{$value->id}}" class="btn btn-info btn-back w-20">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Data</span>
                            <i class="mdi mdi-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="row mt-5">
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                            <thead class="table-light">
                              <tr>
                                <th class="text-center">Kebutuhan</th>
                                <th class="text-center">Nama Posisi/Jabatan</th>
                                <th class="text-center">Jumlah Headcount</th>
                                <th class="text-center">Action</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              @foreach($value->kebutuhan_detail as $detail)
                              <tr>
                                <td>{{$detail->kebutuhan}}</td>
                                <td>{{$detail->jabatan_kebutuhan}}</td>
                                <td class="text-center">{{$detail->jumlah_hc}}</td>
                                <td>
                                  <div class="col-12 d-flex justify-content-center">
                                    <button type="button" class="btn btn-danger btn-back w-20">
                                      <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                  </div>
                                </td>
                              </tr>
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
                <div class="col-12 d-flex justify-content-between">
                <a href="{{route('quotation.edit-2',$quotation->id)}}" class="btn btn-primary btn-back w-20">
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
    $('#btn-tambah-detail-{{$value->id}}').on('click',function () {
      let jabatanDetailId = $('#jabatan_detail_{{$value->id}}').val();
      let jumlahHc = $('#jumlah_hc_{{$value->id}}').val();

      let msg="";
      if(jabatanDetailId ==""){
        msg += "Nama Posisi / Jabatan Belum Diisi <br />";
      }
      if(jumlahHc ==""){
        msg += "Jumlah HC Belum Diisi";
      }

      if(msg!=""){
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning",
        });
      }else{
        // $.ajax({
        //   type: "POST",
        //   url: url,
        //   dataType:"jsonp"
        //   success: function(response){
        //       //if request if made successfully then the response represent the data

        //       $( "#result" ).empty().append( response );
        //   }
        // });
      }
    });
  @endforeach
</script>
@endsection