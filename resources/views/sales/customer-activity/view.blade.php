@extends('layouts.master')
@section('title','Customer Activity')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span>View Customer Activity</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Customer Activity</span>
            <span class="text-center"><a href="{{route('leads.view',[$leads->id])}}" class="btn btn-secondary waves-effect" id="btn-lihat-leads" style="color:white"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</a>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <input type="hidden" id="leads_email" value="{{$leads->email}}">
          <div class="d-flex justify-content-between">
            <h5><span class="text-center">Nomor : {{$data->nomor}}</span></h5>
          </div>
          <h6>1. Informasi Leads</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Leads / customer</label>
            <div class="col-sm-4">
              <div class="input-group">
                <input type="text" id="leads" name="leads" value="{{$leads->nama_perusahaan}}" class="form-control @if ($errors->any()) @if($errors->has('leads')) is-invalid @else is-valid @endif @endif" readonly>
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal Activity</label>
            <div class="col-sm-4">
              <input type="text" id="tgl_activity" name="tgl_activity" value="{{$data->stgl_activity}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Wilayah</label>
            <div class="col-sm-4">
              <input type="text" id="branch" name="branch" value="{{$leads->branch}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
            <div class="col-sm-4">
              <input type="text" id="kebutuhan" name="kebutuhan" value="{{$leads->kebutuhan}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tim Sales</label>
            <div class="col-sm-4">
              <input type="text" id="tim_sales_name" name="tim_sales_name" value="{{$data->tim_sales}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Sales</label>
            <div class="col-sm-4">
              <input type="text" id="sales_name" name="sales_name" value="{{$data->nama_sales}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">CRM</label>
            <div class="col-sm-4">
              <input type="text" id="crm_name" name="crm_name" value="{{$data->crm}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">RO</label>
            <div class="col-sm-4">
              <input type="text" id="ro_name" name="ro_name" value="{{$data->ro}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Notes</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100" name="notes" id="notes" placeholder="" readonly>{{$data->notes}}</textarea>
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>2. Customer Activity</h6>
          <input type="hidden" name="tipe" value="" />
          <div class="row mb-3">
            <div class="offset-sm-2 col-sm-2">
              <!-- <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="ubah-status" value="Ubah Status" @if($data->tipe=="Ubah Status") checked @endif disabled>
                <label class="form-check-label" for="ubah-status" style="color: black;font-weight: bold;">
                  Ubah Status
                </label>
              </div> -->
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-sales" value="Pilih Sales" @if($data->tipe=="Pilih Sales") checked @endif disabled>
                <label class="form-check-label" for="pilih-sales" style="color: black;font-weight: bold;">
                  Pilih Sales
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-ro" value="Pilih RO" @if($data->tipe=="Pilih RO") checked @endif disabled>
                <label class="form-check-label" for="pilih-ro" style="color: black;font-weight: bold;">
                  Pilih RO
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-crm" value="Pilih CRM" @if($data->tipe=="Pilih CRM") checked @endif disabled>
                <label class="form-check-label" for="pilih-crm" style="color: black;font-weight: bold;">
                  Pilih CRM
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="telepon" value="Telepon" @if($data->tipe=="Telepon") checked @endif disabled>
                <label class="form-check-label" for="telepon" style="color: black;font-weight: bold;">
                  Telepon
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="online-meeting" value="Online Meeting" @if($data->tipe=="Online Meeting") checked @endif disabled>
                <label class="form-check-label" for="online-meeting" style="color: black;font-weight: bold;">
                  Online Meeting
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="email" value="Email" @if($data->tipe=="Email") checked @endif disabled>
                <label class="form-check-label" for="email" style="color: black;font-weight: bold;">
                  Email
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="kirim-berkas" value="Kirim Berkas" @if($data->tipe=="Kirim Berkas") checked @endif disabled>
                <label class="form-check-label" for="kirim-berkas" style="color: black;font-weight: bold;">
                  Kirim Berkas
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="visit" value="Visit" @if($data->tipe=="Visit") checked @endif disabled>
                <label class="form-check-label" for="visit" style="color: black;font-weight: bold;">
                  Visit
                </label>
              </div>
            </div>
            <div class="col-sm-8">
              @if($data->tipe=="Ubah Status")
              <!-- <div class="d-status">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Status</label>
                  <div class="col-sm-10">
                    <input type="text" id="status-leads" name="status-leads" value="{{$data->status_leads}}" class="form-control" readonly>
                  </div>
                </div>
              </div> -->
              @endif
              @if($data->tipe=="Pilih Sales")
              <div class="d-pilih-sales">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Tim Sales</label>
                  <div class="col-sm-4">
                    <input type="text" id="tim-sales" name="tim-sales" value="{{$data->tim_sales}}" class="form-control" readonly>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Nama Sales</label>
                  <div class="col-sm-4">
                    <input type="text" id="nama-sales" name="nama-sales" value="{{$data->nama_sales}}" class="form-control" readonly>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Pilih RO")
              <div class="d-pilih-ro">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">RO</label>
                  <div class="col-sm-10">
                    <input type="text" id="ro" name="ro" value="{{$data->ro}}" class="form-control" readonly>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Pilih CRM")
              <div class="d-pilih-crm">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">CRM</label>
                  <div class="col-sm-10">
                    <input type="text" id="crm" name="crm" value="{{$data->crm}}" class="form-control" readonly>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Telepon")
              <div class="d-telepon">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Start</label>
                  <div class="col-sm-4">
                    <input type="time" id="start" name="start" value="{{$data->start}}" class="form-control" readonly>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">End</label>
                  <div class="col-sm-4">
                  <input type="time" id="end" name="end" value="{{$data->end}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Durasi</label>
                  <div class="col-sm-10">
                    <input type="text" id="durasi" name="durasi" value="{{$data->durasi}}" class="form-control" readonly>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Email")
              <div class="d-email">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Tanggal</label>
                  <div class="col-sm-3">
                    <input type="text" id="tgl_realisasi" name="tgl_realisasi" value="{{$data->tgl_realisasi}}" class="form-control" readonly>
                  </div>
                  <label class="col-sm-3 col-form-label text-sm-end">Email Penerima</label>
                  <div class="col-sm-4">
                    <input type="text" id="email" name="email" value="{{$data->email}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3 l-keterangan">
                  <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
                  <div class="col-sm-10">
                    <div class="form-floating form-floating-outline mb-4">
                      <textarea class="form-control h-px-100 @" name="notes_tipe" id="notes_tipe" placeholder="" readonly>{{$data->notes_tipe}}</textarea>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Kirim Berkas")
              <div class="d-kirim-berkas">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Tanggal</label>
                  <div class="col-sm-4">
                    <input type="text" id="tgl_realisasi" name="tgl_realisasi" value="{{$data->tgl_realisasi}}" class="form-control" readonly>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Jam</label>
                  <div class="col-sm-4">
                    <input type="text" id="jam_realisasi" name="jam_realisasi" value="{{$data->jam_realisasi}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Penerima</label>
                  <div class="col-sm-10">
                    <input type="text" id="penerima" name="penerima" value="{{$data->penerima}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3 l-keterangan">
                  <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
                  <div class="col-sm-10">
                    <div class="form-floating form-floating-outline mb-4">
                      <textarea class="form-control h-px-100 @" name="notes_tipe" id="notes_tipe" placeholder="" readonly>{{$data->notes_tipe}}</textarea>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              @if($data->tipe=="Visit")
              <div class="d-kirim-berkas">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Jenis Visit</label>
                  <div class="col-sm-10">
                    <input type="text" id="jenis_visit" name="jenis_visit" value="{{$data->jenis_visit}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Tanggal</label>
                  <div class="col-sm-4">
                    <input type="text" id="tgl_realisasi" name="tgl_realisasi" value="{{$data->tgl_realisasi}}" class="form-control" readonly>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Jam</label>
                  <div class="col-sm-4">
                    <input type="text" id="jam_realisasi" name="jam_realisasi" value="{{$data->jam_realisasi}}" class="form-control" readonly>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Notulen / Berita Acara</label>
                  <div class="col-sm-10">
                    <div class="form-floating form-floating-outline mb-4">
                      <textarea class="form-control h-px-100 @" name="notulen" id="notulen" placeholder="" readonly>{{$data->notulen}}</textarea>
                    </div>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
          @if(count($dataFile) >0 ){
          <hr class="my-4 mx-4">
          <h6>3. Berkas Pendukung</h6>
          <div class="row mb-3">
              <div class="col-sm-12">
              <table class="table table-hover" id="tabelUpload">
                <thead>
                    <tr class="d-flex">
                    <th scope="col" class="col-4">Nama File</th>
                    <th scope="col" class="col-8">File</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($dataFile as $file)
                    <tr class="d-flex">
                        <td class="col-4">
                          {{$file->nama_file}}
                        </td>
                        <td class="col-8">
                        <a href="{{$file->url_file}}" class="btn btn-info waves-effect waves-light">
                          <i class="mdi mdi-file-document-outline scaleX-n1-rtl"></i> &nbsp;
                          <span class="me-1">Lihat File</span>
                        </a>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
                </table>
              </table>
            </div>
          </div>
          @endif
        </form>
      </div>
    </div>
    <div class="col-md-3">
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
            <!-- <div class="col-12 text-center">
              <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Update Data</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div> -->
            <div class="col-12 text-center mt-2">
              <button id="btn-kirim-email" class="btn btn-info w-100 waves-effect waves-light">
                <span class="me-1">Kirimkan Email</span>
                <i class="mdi mdi-email-fast-outline scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>
            <hr class="my-4 mx-4">
            <div class="col-12 text-center mt-2">
              <button id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Delete Activity</span>
                <i class="mdi mdi-trash-can scaleX-n1-rtl"></i>
              </button>
            </div>
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
  
  $('#btn-kirim-email').on('click',function () {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-outline-primary",
        cancelButton: "btn btn-outline-danger",
        denyButton: "btn btn-outline-secondary"
      },
      buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
      title: "Kirimkan Email ke ?",
      icon: "question",
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: "PIC Leads",
      denyButtonText: "Sales",
    }).then((result) => {
      if (result.isConfirmed) {
        // email ke PIC Leads
        kirimEmail("PIC Leads");
      } else if (result.isDenied) {
        // email ke Sales
        kirimEmail("Sales");
      }
    });
  });

  function kirimEmail(tipe) {
    location.href= "https://google.com";
    
    showLoading();
    // Swal.fire({
    //   title: 'Pemberitahuan',
    //   html: tipe,
    //   icon: 'success',
    //   customClass: {
    //     confirmButton: 'btn btn-primary waves-effect waves-light'
    //   },
    //   buttonsStyling: false
    // });
  }

  $('#btn-delete').on('click',function () {
    $('form').attr('action', '{{route("customer-activity.delete")}}');
    $('form').submit();
  });

  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('customer-activity')}}");
  });
</script>
@endsection