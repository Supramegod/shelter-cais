@extends('layouts.master')
@section('title','Leads')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Lihat Leads</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Leads</span>
            <span style="font-weight:bold;color:#000">{{$data->nomor}} - {{$data->stgl_leads}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('leads.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <h6>1. Informasi Perusahaan</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{$data->nama_perusahaan}}" class="form-control @if ($errors->any()) @if($errors->has('nama_perusahaan')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_perusahaan'))
                  <div class="invalid-feedback">{{$errors->first('nama_perusahaan')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Jenis Perusahaan</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="jenis_perusahaan" name="jenis_perusahaan" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($jenisPerusahaan as $value)
                  <option value="{{$value->id}}" @if($data->jenis_perusahaan_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Wilayah <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="branch" name="branch" class="form-select @if ($errors->any()) @if($errors->has('branch')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($branch as $value)
                  <option value="{{$value->id}}" @if($data->branch_id == $value->id) selected @endif>{{$value->name}}</option>
                  @endforeach
                </select>
                @if($errors->has('branch'))
                  <div class="invalid-feedback">{{$errors->first('branch')}}</div>
                @endif
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Telpon Perusahaan</label>
            <div class="col-sm-4">
              <input type="number" id="telp_perusahaan" name="telp_perusahaan" value="{{$data->telp_perusahaan}}" class="form-control @if ($errors->any())   @endif">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Alamat Perusahaan</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-2">
                <textarea class="form-control mt-3 h-px-100 @if ($errors->any())   @endif" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="">{{$data->alamat}}</textarea>
              </div>
            </div>
          </div>          
          <hr class="my-4 mx-4">
          <h6>2. Kebutuhan Leads</h6>
          <div class="row mb-2">
            <label class="col-sm-2 col-form-label text-sm-end">Sumber Leads</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="platform" name="platform" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($platform as $value)
                  <option value="{{$value->id}}" @if($data->platform_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="kebutuhan" name="kebutuhan" class="form-select @if ($errors->any()) @if($errors->has('kebutuhan')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($kebutuhan as $value)
                  <option value="{{$value->id}}" @if($data->kebutuhan_id == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('kebutuhan'))
                  <div class="invalid-feedback">{{$errors->first('kebutuhan')}}</div>
                @endif
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>3. Informasi PIC</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">PIC <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="pic" name="pic" value="{{$data->pic}}" class="form-control @if ($errors->any()) @if($errors->has('pic')) is-invalid @else   @endif @endif">
              @if($errors->has('pic'))
                  <div class="invalid-feedback">{{$errors->first('pic')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Jabatan</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="jabatan_pic" name="jabatan_pic" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($jabatanPic as $value)
                  <option value="{{$value->id}}" @if($data->jabatan == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nomor Telepon</label>
            <div class="col-sm-4">
              <input type="number" id="no_telp" name="no_telp" value="{{$data->no_telp}}" class="form-control @if ($errors->any())   @endif">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Email</label>
            <div class="col-sm-4">
              <input type="text" id="email" name="email" value="{{$data->email}}" class="form-control @if ($errors->any())   @endif">
            </div>
          </div>
          <hr class="my-4 mx-4">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Detail Leads</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="detail_leads" id="detail_leads" placeholder="">{{$data->notes}}</textarea>
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <div class="row mb-3">
            <label class="col-sm-12 col-form-label">Note : <span class="text-danger">*)</span> Wajib Diisi</label>
          </div>
          <div class="pt-4">
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Action</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-dots-vertical mdi-24px"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
              </div>
            </div>
          </div>
          <div class="card-body">
            @if(in_array(Auth::user()->role_id,[30,48,49]))
            <div class="col-12 text-center">
              <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Update Data</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div>
            @endif
            <!-- <div class="col-12 text-center mt-2">
              <button id="btn-quotation" class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Create Quotation</span>
                <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
              </button>
            </div> -->
            @if(in_array(Auth::user()->role_id,[30]))
            <div class="col-12 text-center mt-2">
              <button id="btn-activity" class="btn btn-info w-100 waves-effect waves-light">
                <span class="me-1">Create Activity</span>
                <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
              </button>
            </div>
            @endif
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>
            <hr class="my-4 mx-4">
            @if(in_array(Auth::user()->role_id,[30,48,49]))
            <div class="col-12 text-center mt-2">
              <button id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Delete Leads</span>
                <i class="mdi mdi-trash-can scaleX-n1-rtl"></i>
              </button>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Leads Activity</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-dots-vertical mdi-24px"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="btn">
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="timeline card-timeline mb-0">
              @foreach($activity as $value)
              <li class="timeline-item timeline-item-transparent border-transparent">
                <span class="timeline-point timeline-point-info"></span>
                <div class="timeline-event pb-1">
                  <div class="timeline-header mb-1">
                    <h6 class="mb-0">{{$value->tipe}}</h6>
                    <small class="text-muted">{{$value->stgl_activity}}</small>
                  </div>
                  <p class="mb-0">
                    Nomor : <b>{{$value->nomor}}</b>
                    <br> oleh : {{$value->created_by}}<b></b>
                    <br> pada : {{$value->screated_at}}
                    <br> keterangan : <b>{{$value->notes}}</b>
                  </p>
                  <div class="d-flex justify-content-center">
                    <a href="{{route('customer-activity.view',$value->id)}}" class="btn btn-sm rounded-pill btn-outline-info waves-effect w-50 mt-1">Selengkapnya</a>
                  </div>
                </div>
              </li>
              @endforeach
              <li class="timeline-item timeline-item-transparent border-transparent">
                <span class="timeline-point timeline-point-primary"></span>
                <div class="timeline-event pb-1">
                  <div class="timeline-header mb-1">
                    <h6 class="mb-0">Leads</h6>
                    <small class="text-muted">{{$data->screated_at}}</small>
                  </div>
                  <p class="mb-0">Leads Terbentuk</p>
                </div>
              </li>
              <!-- <li class="timeline-item timeline-item-transparent border-transparent">
                <span class="timeline-point timeline-point-info"></span>
                <div class="timeline-event pb-1">
                  <div class="timeline-header mb-1">
                    <h6 class="mb-0">Quotation</h6>
                    <small class="text-muted">24 Juli 2024</small>
                  </div>
                  <p class="mb-0">Terbentuk Quotation dengan Nomor #QUO/01/01/2024.</p>
                  <button type="button" class="btn btn-sm rounded-pill btn-outline-info waves-effect w-100 mt-1">Selengkapnya</button>
                </div>
              </li>
              <li class="timeline-item timeline-item-transparent border-transparent">
                <span class="timeline-point timeline-point-success"></span>
                <div class="timeline-event pb-1">
                  <div class="timeline-header mb-1">
                    <h6 class="mb-0">SPK</h6>
                    <small class="text-muted">25 Juli 2024</small>
                  </div>
                  <p class="mb-0">SPK Terbentuk dengan Nomor SPK : #SPK/29/01/2024.</p>
                  <button type="button" class="btn btn-sm rounded-pill btn-outline-info waves-effect w-100 mt-1">Selengkapnya</button>
                </div>
              </li> -->
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  @if(session()->has('success'))  
    Swal.fire({
      title: 'Pemberitahuan',
      html: '{{session()->get('success')}}',
      icon: 'success',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif

  $('#btn-update').on('click',function () {
    $('form').submit();
  });
  
  $('#btn-delete').on('click',function () {
    $('form').attr('action', '{{route("leads.delete")}}');
    $('form').submit();
  });
  
  // $('#btn-quotation').on('click',function () {
  //   Swal.fire({
  //     title: 'Pemberitahuan',
  //     html: 'Fitur belum siap',
  //     icon: 'warning',
  //     customClass: {
  //       confirmButton: 'btn btn-warning waves-effect waves-light'
  //     },
  //     buttonsStyling: false
  //   });
  // });

  $('#btn-activity').on('click',function () {
    window.location.replace("{{route('customer-activity.add',['leads_id'=>$data->id])}}");
  });

  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('leads')}}");
  });
</script>
@endsection