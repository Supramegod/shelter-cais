@extends('layouts.master')
@section('title','Customer Activity')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Customer Activity Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Customer Activity</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-leads"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <h6>1. Informasi Leads</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Leads / customer <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="hidden" id="leads_id" name="leads_id" value="{{old('leads_id')}}" class="form-control">
              <div class="input-group">
                <input type="text" id="leads" name="leads" value="{{old('leads')}}" class="form-control @if ($errors->any()) @if($errors->has('leads')) is-invalid @else is-valid @endif @endif" readonly>
                <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-leads"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Leads</button>
                @if($errors->has('leads'))
                  <div class="invalid-feedback">{{$errors->first('leads')}}</div>
                @endif
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal Activity <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="date" id="tgl_activity" name="tgl_activity" value="@if(old('tgl_activity')==null){{$nowd}}@else{{old('tgl_activity')}}@endif" class="form-control @if ($errors->any()) @if($errors->has('tgl_activity')) is-invalid @else is-valid @endif @endif">
                @if($errors->has('tgl_activity'))
                  <div class="invalid-feedback">{{$errors->first('tgl_activity')}}</div>
                @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Wilayah</label>
            <div class="col-sm-4">
              <input type="text" id="branch" name="branch" value="{{old('branch')}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
            <div class="col-sm-4">
              <input type="text" id="kebutuhan" name="kebutuhan" value="{{old('kebutuhan')}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tim Sales</label>
            <div class="col-sm-4">
              <input type="text" id="tim_sales_name" name="tim_sales_name" value="{{old('tim_sales_name')}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Sales</label>
            <div class="col-sm-4">
              <input type="text" id="sales_name" name="sales_name" value="{{old('sales_name')}}" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">CRM</label>
            <div class="col-sm-4">
              <input type="text" id="crm_name" name="crm_name" value="{{old('crm_name')}}" class="form-control" readonly>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">RO</label>
            <div class="col-sm-4">
              <input type="text" id="ro_name" name="ro_name" value="{{old('ro_name')}}" class="form-control" readonly>
            </div>
          </div>
          @if(!in_array(Auth::user()->role_id,[4,5]))
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Status Leads <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <select id="status_leads_id" name="status_leads_id" class="select2 form-select select2-hidden-accessible @if ($errors->any()) @if($errors->has('status_leads_id')) is-invalid @else is-valid @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih data -</option>  
                @foreach($statusLeads as $value)
                <option value="{{$value->id}}" @if(old('status_leads_id') == $value->id) selected @endif>{{$value->nama}}</option>
                @endforeach
              </select>
              @if($errors->has('status_leads_id'))
                <div class="invalid-feedback">{{$errors->first('status_leads_id')}}</div>
              @endif
            </div>
          </div>
          @endif
          
          <!-- JIKA SALES HANYA DIA SENDIRI -->
          @if(in_array(Auth::user()->role_id,[29]))
          <input type="hidden" name="tim_sales_id" value="{{$tim_sales_id}}">
          <input type="hidden" name="tim_sales_d_id" value="{{$tim_sales_d_id}}">          
          @endif
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Notes</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100" name="notes" id="notes" placeholder="">{{old('notes')}}</textarea>
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>2. Customer Activity</h6>
          <input type="hidden" name="tipe" value="" />
          <div class="row mb-3">
            <div class="offset-sm-2 col-sm-2">
              @if(in_array(Auth::user()->role_id,[30,31,32,33]))
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-sales" value="Pilih Sales">
                <label class="form-check-label" for="pilih-sales">
                  Pilih Sales
                </label>
              </div>
              @if(in_array(Auth::user()->role_id,[30]))
              <!-- <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="ubah-status" value="Ubah Status">
                <label class="form-check-label" for="ubah-status">
                  Ubah Status
                </label>
              </div> -->
              @endif
              @endif
              @if(in_array(Auth::user()->role_id,[6,8]))
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-ro" value="Pilih RO">
                <label class="form-check-label" for="pilih-ro">
                  Pilih RO
                </label>
              </div>
              @endif
              @if(in_array(Auth::user()->role_id,[55,56]))
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-crm" value="Pilih CRM">
                <label class="form-check-label" for="pilih-crm">
                  Pilih CRM
                </label>
              </div>
              @endif
              @if(in_array(Auth::user()->role_id,[29]))
              <!-- <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="ubah-status" value="Ubah Status">
                <label class="form-check-label" for="ubah-status">
                  Ubah Status
                </label>
              </div> -->
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="telepon" value="Telepon">
                <label class="form-check-label" for="telepon">
                  Telepon
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="online-meeting" value="Online Meeting">
                <label class="form-check-label" for="online-meeting">
                  Online Meeting
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="email" value="Email">
                <label class="form-check-label" for="email">
                  Email
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="kirim-berkas" value="Kirim Berkas">
                <label class="form-check-label" for="kirim-berkas">
                  Kirim Berkas
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input tipe" type="radio" name="tipe" id="visit" value="Visit">
                <label class="form-check-label" for="visit">
                  Visit
                </label>
              </div>
              @endif
              @if($errors->has('tipe'))
                <span class="text-danger">{{$errors->first('tipe')}}</span>
              @endif
            </div>
            <div class="col-sm-8">
              <!-- <div class="d-status-leads">
                <div class="row">
                  
                </div>
              </div> -->
              <div class="d-tim-sales">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end @if(in_array(Auth::user()->role_id,[31])) d-none @endif">Tim Sales <span class="text-danger">*</span></label>
                  <div class="col-sm-4 @if(in_array(Auth::user()->role_id,[31])) d-none @endif">
                    <div class="position-relative">
                      <select id="tim_sales_id" name="tim_sales_id" class="select2 form-select select2-hidden-accessible @if ($errors->any()) @if($errors->has('tim_sales_id')) is-invalid @else is-valid @endif @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>  
                        @foreach($timSales as $value)
                        <option value="{{$value->id}}" @if(old('tim_sales_id') == $value->id) selected @endif>{{$value->nama}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('tim_sales_id'))
                        <div class="invalid-feedback">{{$errors->first('tim_sales_id')}}</div>
                      @endif
                    </div>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Sales <span class="text-danger">*</span></label>
                  <div class="@if(in_array(Auth::user()->role_id,[31])) col-sm-10 @else col-sm-4 @endif">
                    <input type="hidden" name="sales_d" id="sales_d">
                    <select id="tim_sales_d_id" name="tim_sales_d_id" class="select2 form-select select2-hidden-accessible @if ($errors->any()) @if($errors->has('tim_sales_d_id')) is-invalid @else is-valid @endif @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>  
                    </select>
                    @if($errors->has('tim_sales_d_id'))
                        <div class="invalid-feedback">{{$errors->first('tim_sales_d_id')}}</div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="d-ro">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">RO</label>
                  <div class="col-sm-10">
                    <select id="ro" name="ro" class="select2 form-select select2-hidden-accessible @if ($errors->any()) is-valid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($roList as $value)
                      <option value="{{$value->id}}" @if(old('ro') == $value->id) selected @endif>{{$value->full_name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="d-crm">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">CRM </label>
                  <div class="col-sm-10">
                    <div class="position-relative">
                      <select id="crm" name="crm" class="select2 form-select select2-hidden-accessible @if ($errors->any()) is-valid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>  
                        @foreach($crmList as $value)
                        <option value="{{$value->id}}" @if(old('crm') == $value->id) selected @endif>{{$value->full_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-telepon">
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="date" id="tgl_realisasi_telepon" name="tgl_realisasi_telepon" value="{{old('tgl_realisasi_telepon')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_realisasi_telepon')) is-invalid @else is-valid @endif @endif">
                    @if($errors->has('tgl_realisasi_telepon'))
                      <div class="invalid-feedback">{{$errors->first('tgl_realisasi_telepon')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Start <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <input type="time" id="start" name="start" onchange="hitungDurasi();" value="{{old('start')}}" class="form-control @if ($errors->any()) @if($errors->has('start')) is-invalid @else is-valid @endif @endif">
                    @if($errors->has('start'))
                      <div class="invalid-feedback">{{$errors->first('start')}}</div>
                    @endif
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">End <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                  <input type="time" id="end" name="end" onchange="hitungDurasi();" value="{{old('end')}}" class="form-control @if ($errors->any()) @if($errors->has('end')) is-invalid @else is-valid @endif @endif">
                    @if($errors->has('end'))
                      <div class="invalid-feedback">{{$errors->first('end')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Durasi</label>
                  <div class="col-sm-4">
                    <input type="text" id="durasi" name="durasi" value="{{old('durasi')}}" class="form-control @if ($errors->any()) @if($errors->has('durasi')) is-invalid @else is-valid @endif @endif" readonly>
                      @if($errors->has('durasi'))
                        <div class="invalid-feedback">{{$errors->first('durasi')}}</div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="d-visit">
                <div class="row l-jenis-visit mb-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Jenis Visit <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <div class="position-relative">
                      <select id="jenis_visit" name="jenis_visit" class="select2 form-select select2-hidden-accessible @if ($errors->any()) @if($errors->has('jenis_visit')) is-invalid @else is-valid @endif @endif" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>  
                        <option value="Client Visit" @if(old('jenis_visit') == 'Client Visit') selected @endif>Client Visit</option>
                        <option value="Offline Meeting" @if(old('jenis_visit') == 'Offline Meeting') selected @endif>Offline Meeting</option>  
                      </select>
                      @if($errors->has('jenis_visit'))
                        <div class="invalid-feedback">{{$errors->first('jenis_visit')}}</div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <input type="date" id="tgl_realisasi" name="tgl_realisasi" value="{{old('tgl_realisasi')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_realisasi')) is-invalid @else is-valid @endif @endif">
                    @if($errors->has('tgl_realisasi'))
                      <div class="invalid-feedback">{{$errors->first('tgl_realisasi')}}</div>
                    @endif
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end d-visit l-jam-realisasi">Jam <span class="text-danger">*</span></label>
                  <div class="col-sm-4 d-visit l-jam-realisasi">
                    <input type="time" id="jam_realisasi"  name="jam_realisasi" value="{{old('jam_realisasi')}}" class="form-control @if ($errors->any()) @if($errors->has('jam_realisasi')) is-invalid @else is-valid @endif @endif">
                    @if($errors->has('jam_realisasi'))
                      <div class="invalid-feedback">{{$errors->first('jam_realisasi')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row l-notulen mt-3">
                  <label class="col-sm-2 col-form-label text-sm-end">Notulen / Berita Acara <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <div class="form-floating form-floating-outline">
                      <textarea class="form-control h-px-100 @if ($errors->any()) @if($errors->has('notulen')) is-invalid @else is-valid @endif @endif" name="notulen" id="notulen" placeholder="">{{old('notulen')}}</textarea>
                      @if($errors->has('notulen'))
                        <div class="invalid-feedback">{{$errors->first('notulen')}}</div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row mt-3 l-email">
                  <label class="col-sm-2 col-form-label text-sm-end">Email Penerima<span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="text" id="email" name="email" value="{{old('email')}}" class="form-control @if ($errors->any()) @if($errors->has('email')) is-invalid @else is-valid @endif @endif">
                      @if($errors->has('email'))
                        <div class="invalid-feedback">{{$errors->first('email')}}</div>
                      @endif
                  </div>
                </div>
                <div class="row mt-3 l-penerima">
                  <label class="col-sm-2 col-form-label text-sm-end">Penerima Berkas <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="text" id="penerima" name="penerima" value="{{old('penerima')}}" class="form-control @if ($errors->any()) @if($errors->has('penerima')) is-invalid @else is-valid @endif @endif">
                      @if($errors->has('penerima'))
                        <div class="invalid-feedback">{{$errors->first('penerima')}}</div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="row mt-3 l-keterangan" style="display:none">
                <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
                <div class="col-sm-10">
                  <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100 @if ($errors->any()) is-valid @endif" name="notes_tipe" id="notes_tipe" placeholder="">{{old('notes_tipe')}}</textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
                    <tr class="d-flex">
                        <td class="col-4">
                          <input type="text" placeholder="-" id="nama-file-1" name="namafiles[]" class="form-control"/>
                        </td>
                        <td class="col-8">
                          <input type="file" id="file-1" name="files[]" class="form-control">
                        </td>
                    </tr>
                    <tr class="d-flex">
                        <td class="col-4">
                          <input type="text" placeholder="-" id="nama-file-2" name="namafiles[]" class="form-control"/>
                        </td>
                        <td class="col-8">
                        <input type="file" id="file-2" name="files[]" class="form-control">
                        </td>
                    </tr>
                    <tr class="d-flex">
                        <td class="col-4">
                          <input type="text" placeholder="-" id="nama-file-3" name="namafiles[]" class="form-control"/>
                        </td>
                        <td class="col-8">
                          <input type="file" id="file-3" name="files[]" class="form-control">
                        </td>
                    </tr>
                </tbody>
                </table>
                  <!-- <center>
                      <a class="btn w-40 btn-info mt-2" style="color:white" id="addRowUpload" ><span class="mdi mdi-plus"></span>&nbsp; Tambah File </a>
                      </center> -->
              </table>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <div class="row mb-3">
            <label class="col-sm-12 col-form-label">Note : <span class="text-danger">*)</span> Wajib Diisi</label>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('customer-activity')}}" class="btn btn-secondary waves-effect">Kembali</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Payment Methods modal -->
<div class="modal fade" id="modal-leads" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Leads / Customer</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Tgl Leads</th>
                    <th class="text-center">Wilayah</th>
                    <th class="text-center">PIC</th>
                    <th class="text-center">No. Telp PIC</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                    {{-- data table ajax --}}
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / Payment Methods modal -->
  
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

  function hitungDurasi() {
    var start = $('#start').val();
    var end = $('#end').val();

    if(start !=null && end !=null && start !="" && end !=""){
      var diff = null;
      if (end < start) {
        diff = (new Date("1970-1-2 " + end) - new Date("1970-1-1 " + start) ) /1000/60;
      }else{
        diff = (new Date("1970-1-1 " + end) - new Date("1970-1-1 " + start) ) /1000/60;
      }
      var hour = Math.abs(parseInt(diff/60)) + " jam";
      parseInt(diff/60)
      var min = diff%60;
      if(Math.abs(min)<10){
          min = "0" + min;
      }
      min = min + " menit";
      $('#durasi').val(hour+" "+min);
    }else{
      $('#durasi').val("");
    }

  }
  $(document).ready(function(){
    @if(old('tim_sales_id')!=null && old('tim_sales_id')!= "")
      $('#tim_sales_d_id').find('option').remove();
      $('#tim_sales_d_id').append('<option value="">- Pilih data -</option>');

      if($('#tim_sales_id').find(":selected").val() !=""){
        var param = "tim_sales_id="+$('#tim_sales_id').find(":selected").val();
        $.ajax({
          url: "{{route('customer-activity.member-tim-sales')}}",
          type: 'GET',
          data: param,
          success: function(res) {
            res.forEach(element => {
              let selected = "";
              console.log({{old('tim_sales_d_id')}});
              @if(old('tim_sales_d_id')!=null && old('tim_sales_d_id')!= "")
              console.log(element.id);
                if(element.id == {{old('tim_sales_d_id')}}){
                  selected=true;
                  console.log(selected);
                }
              @endif

              $('#tim_sales_d_id').append('<option value="'+element.id+'" '+selected+'>'+element.nama+'</option>');
            });
          }
        });
      }
    @endif

    $('#tim_sales_id').on('change', function() {
      
      $('#tim_sales_d_id').find('option').remove();
      $('#tim_sales_d_id').append('<option value="">- Pilih data -</option>');

      if(this.value!=""){
        var param = "tim_sales_id="+this.value;
        $.ajax({
          url: "{{route('customer-activity.member-tim-sales')}}",
          type: 'GET',
          data: param,
          success: function(res) {
            res.forEach(element => {
              let selected = "";
              if($('#sales_d').val() != "" ){
                if(element.id == $('#sales_d').val()){
                  selected = "selected";
                }
              }
              $('#tim_sales_d_id').append('<option value="'+element.id+'" '+selected+'>'+element.nama+'</option>');
            });
          }
        });
      }
    });

    @if(old('tipe') == null)
      $('.d-visit').hide();
      $('.d-telepon').hide();
      // $('.d-status-leads').hide();
      $('.d-tim-sales').hide();
      $('.d-ro').hide();
      $('.d-crm').hide();
    @endif

    let dt_filter_table = $('.dt-column-search');

    var table = $('#table-data').DataTable({
        "initComplete": function (settings, json) {  
          $("#table-data").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        "bDestroy": true,
        "iDisplayLength": 25,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('leads.available-leads') }}",
            data: function (d) {
                
            },
        },   
        "order":[
            [0,'desc']
        ],
        columns:[{
                  data : 'id',
                  name : 'id',
                  visible: false,
                  searchable: false
              },{
                  data : 'nama_perusahaan',
                  name : 'nama_perusahaan',
                  className:'text-center'
              },{
                  data : 'tgl',
                  name : 'tgl',
                  className:'text-center'
              },{
                  data : 'branch',
                  name : 'branch',
                  className:'text-center'
              },{
                  data : 'pic',
                  name : 'pic',
                  className:'text-center'
              },{
                  data : 'no_telp',
                  name : 'no_telp',
                  className:'text-center'
              },{
                  data : 'email',
                  name : 'email',
                  className:'text-center'
              },{
                  data : 'status',
                  name : 'status',
                  className:'text-center'
              }],
        "language": datatableLang,
    });

    $('#btn-modal-cari-leads').on('click',function(){
      $('#modal-leads').modal('show');
    });

    @if($leads !=null)
      $('#branch').val("{{$leads->branch}}");
      $('#leads').val("{{$leads->nama_perusahaan}}");
      $('#leads_id').val("{{$leads->id}}");
      $('#kebutuhan').val("{{$leads->kebutuhan}}");
      $('#sales_d').val("");

      if(rdata.tim_sales_id !=null){
        $('#tim_sales_id').val(rdata.tim_sales_id).change();
        if(rdata.tim_sales_d_id != null){
          $('#sales_d').val(rdata.tim_sales_d_id);
        }
      }

      $('#btn-lihat-leads').removeClass('d-none');
    @endif

    $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-leads').modal('hide');
      var rdata = table.row(this).data();
      $('#branch').val(rdata.branch);
      $('#leads').val(rdata.nama_perusahaan);
      $('#leads_id').val(rdata.id);
      $('#kebutuhan').val(rdata.kebutuhan);
      $('#tim_sales_name').val(rdata.tim_sales);
      $('#sales_name').val(rdata.sales);
      $('#ro_name').val(rdata.ro);
      $('#crm_name').val(rdata.crm);

      $('#sales_d').val("");

      if(rdata.tim_sales_id !=null){
        $('#tim_sales_id').val(rdata.tim_sales_id).change();
        if(rdata.tim_sales_d_id != null){
          $('#sales_d').val(rdata.tim_sales_d_id);
        }
      }

      $('#btn-lihat-leads').removeClass('d-none');
    })

    });

    @if(old('tipe') != null)
      // $('.d-status-leads').hide();
      $('.d-tim-sales').hide();
      $('.d-ro').hide();
      $('.d-crm').hide();
      $('.d-telepon').hide();
      $('.d-visit').hide();
      $('.l-penerima').hide();
      $('.l-jenis-visit').hide();
      $('.l-notulen').hide();
      $('.l-email').hide();
      $('.l-keterangan').show();
      
      var tipe = "{{old('tipe')}}";
      if(tipe=="Telepon"){
        $('#telepon').prop("checked", true);
        $('.d-telepon').show();
      } else if(tipe=="Online Meeting"){
        $('#online-meeting').prop("checked", true);
        $('.d-telepon').show();
      } else if(tipe=="Visit"){
        $('#visit').prop("checked", true);
        $('.d-visit').show();
        $('.l-jenis-visit').show();
        $('.l-notulen').show();
        $('.l-keterangan').hide();
      } else if(tipe=="Email"){
        $('#email').prop("checked", true);
        $('.d-visit').show();
        $('.l-email').show();
        $('.l-jam-realisasi').hide();
      } else if(tipe=="Kirim Berkas"){
        $('#kirim-berkas').prop("checked", true);
        $('.d-visit').show();
        $('.l-penerima').show();
      }
      // else if(tipe=="Ubah Status"){
      //   $('#ubah-status').prop("checked", true);
      //   $('.d-status-leads').show();
      // }
      else if(tipe=="Pilih Sales"){
        $('#pilih-sales').prop("checked", true);
        $('.d-tim-sales').show();
      }else if(tipe=="Pilih RO"){
        $('#pilih-ro').prop("checked", true);
        $('.d-ro').show();
      }else if(tipe=="Pilih CRM"){
        $('#pilih-crm').prop("checked", true);
        $('.d-crm').show();
      }else{
        $('.l-keterangan').hide();
      }
    @endif

  $('.tipe').click(function() {
    $('.d-telepon').hide();
    $('.d-visit').hide();
    $('.l-penerima').hide();
    $('.l-jenis-visit').hide();
    $('.l-notulen').hide();
    $('.l-keterangan').show();
    $('.l-email').hide();
    // $('.d-status-leads').hide();
    $('.d-tim-sales').hide();
    $('.d-ro').hide();
    $('.d-crm').hide();
    
    if($('#telepon').is(':checked')) { 
      $('.d-telepon').show();
    } else if($('#online-meeting').is(':checked')) { 
      $('.d-telepon').show();
    } else if($('#offline-meeting').is(':checked')) { 
      $('.d-visit').show();
    } else if($('#visit').is(':checked')) { 
      $('.d-visit').show();
      $('.l-jenis-visit').show();
      $('.l-notulen').show();
      $('.l-keterangan').hide();
    } else if($('#email').is(':checked')) { 
      $('.d-visit').show();
      $('.l-jam-realisasi').hide();
      $('.l-email').show();
    } else if($('#kirim-berkas').is(':checked')) { 
      $('.d-visit').show();
      $('.l-penerima').show();
    }
    // else if($('#ubah-status').is(':checked')) { 
    //   $('.d-status-leads').show();
    // }
    else if($('#pilih-sales').is(':checked')) { 
      $('.d-tim-sales').show();
    }else if($('#pilih-ro').is(':checked')) { 
      $('.d-ro').show();
    }else if($('#pilih-crm').is(':checked')) { 
      $('.d-crm').show();
    }else{
        $('.l-keterangan').hide();
      }
  });

</script>
@endsection