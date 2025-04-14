@extends('layouts.master')
@section('title','SDT Training')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">SDT/ </span> Detail SDT Training</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>SDT Training Detail</span>
          </div>
        </h5>
        <div class="row">
          <div class="col-md-5">
            <div id="carouselExample" class="carousel slide" style="margin: 15px;">
              <div class="carousel-indicators">
                @foreach($listImage as $value)
                  <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{ $loop->index }}" class="@if($loop->index == 0) active @endif" aria-current="true" aria-label="Slide {{ $loop->index }}"></button>
                @endforeach
                <!-- <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2" aria-label="Slide 3"></button> -->
              </div>

              <div class="carousel-inner">
                @foreach($listImage as $value)
                <div class="carousel-item @if($loop->index == 0) active @endif" style="height: 450px; width:700px">
                  <img style="border-radius: 1%; width: 100%;max-height: 100%" src="{{$value->path}}" class="d-block w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>{{$value->nama}}</h5>
                    <p>{{$value->keterangan}}</p>
                  </div>
                </div>
                @endforeach
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
          <div class="col-md-6">
              <form class="card-body overflow-hidden" action="{{route('sdt-training.save')}}" method="POST">
              @csrf
              <!-- <h6>1. Informasi Perusahaan</h6> -->
              <input type="hidden" name="id" value="{{$data->id_training}}">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Absensi Active</label>
                <div class="col-sm-4">
                  <div class="position-relative">  
                    <div class="form-check form-switch" >
                      <input style="width: 60px; height: 30px;" class="form-check-input form-control" type="checkbox" role="switch" id="enable" name="enable" @if($data->enable == '1') checked @endif>
                    </div>
                  </div>
                </div>

              </div>

              <div class="row mb-3">                
              <label class="col-sm-2 col-form-label text-sm-end">Area</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <select disabled id="area_id" name="area_id" class="select2 form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                      @foreach($listArea as $value)
                      <option disabled value="{{$value->id}}" @if($data->id_area == $value->id) selected @endif>{{$value->area}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Business Unit</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <select disabled id="laman_id" name="laman_id" class="select2 form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                      @foreach($listBu as $value)
                      <option disabled value="{{$value->id}}" @if($data->id_laman == $value->id) selected @endif>{{$value->laman}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Materi <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <select id="materi_id" name="materi_id" class="select2 form-select @if ($errors->any()) @if($errors->has('materi_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($listMateri as $value)
                      <option value="{{$value->id}}" @if($data->id_materi == $value->id) selected @endif>{{$value->nama}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tempat <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <select id="tempat_id" name="tempat_id" class="select2 form-select @if ($errors->any()) @if($errors->has('tempat_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih Tempat -</option>
                        <option value="1" @if($data->id_pel_tempat == '1') selected @endif>IN DOOR</option>
                        <option value="2" @if($data->id_pel_tempat == '2') selected @endif>OUT DOOR</option>
                    </select>
                    @if($errors->has('tempat_id'))
                      <div class="invalid-feedback">{{$errors->first('tempat_id')}}</div>
                    @endif
                  </div>
                </div>
                
              </div>  

              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Waktu Mulai <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="position-relative">
                  <input type="datetime-local" id="start_date" name="start_date" value="{{$data->waktu_mulai}}" class="form-control @if ($errors->any())   @endif">
                    @if($errors->has('start_date'))
                      <div class="invalid-feedback">{{$errors->first('start_date')}}</div>
                    @endif
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Waktu Selesai <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="position-relative">
                  <input type="datetime-local" id="end_date" name="end_date" value="{{$data->waktu_selesai}}" class="form-control @if ($errors->any())   @endif">
                    @if($errors->has('end_date'))
                      <div class="invalid-feedback">{{$errors->first('end_date')}}</div>
                    @endif
                  </div>
                </div>
              </div>  

              <div class="row">
                <label class="col-sm-2 col-form-label text-sm-end">Alamat <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="alamat" id="alamat" placeholder="">{{$data->alamat}}</textarea>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Link Zoom <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="link_zoom" id="link_zoom" placeholder="">{{$data->link_zoom}}</textarea>
                  </div>
                </div>
              </div>  

              <div class="row">
                <label class="col-sm-2 col-form-label text-sm-end">Link Undangan</label>
                <div class="col-sm-7">
                  <div class="form-floating form-floating-outline mb-4">
                    <input readonly type="text" id="link" name="link" value="{{$linkInvite}}" class="link form-control"></input>
                  </div>
                </div>
                <div class="col-sm-3">
                  <!-- <button id="cp_btn">Copy</button> -->
                  <div class="form-floating form-floating-outline mb-4">
                    <button type="button" class="btn btn-warning" onclick="copyToClipboard('#link')" >
                      <span class="me-1">Salin Link</span>
                      <i class="mdi mdi-content-copy"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="row">
                <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
                <div class="col-sm-10">
                  <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="keterangan" id="keterangan" placeholder="">{{$data->keterangan}}</textarea>
                  </div>
                </div>
              </div>
              <div class="pt-4">
                <div class="row justify-content-end">
                  <div class="col-sm-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Update</button>
                  </div>
                </div>
              </div>
            </form>
          <div>
        </div>
        
        <div class="card-body overflow-hidden">
          @csrf
        </div>
      </div>
    </div>
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
            <div class="col-12 text-center mt-2">
              <button id="btn-send-message" class="btn btn-info w-100 waves-effect waves-light">
                <span class="me-1">Kirim Undangan</span>
                  <i class="mdi mdi-send scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-add-client" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Tambah Client</span>
                  <i class="mdi mdi-account-multiple-outline scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-pesan-undangan" class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Pesan Undangan</span>
                  <i class="mdi mdi-account-multiple-plus scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <form target="_blank" action="{{route('invite-pdf')}}" method="POST">
              @csrf
                <input hidden type="text" class="form-control" id="training_id" name="training_id" placeholder="Training id" value="{{$data->id_training}}"/>  
                <button type="submit"  class="btn btn-warning w-100 waves-effect waves-light">
                  <span class="me-1">Generate PDF</span>
                    <i class="mdi mdi-file-pdf-box scaleX-n1-rtl"></i>
                </button>
              </form>
            </div>

            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>

            <hr class="my-4 mx-4">
            <!-- <div class="col-12 text-center mt-2">
              <button id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Delete Leads</span>
                <i class="mdi mdi-trash-can scaleX-n1-rtl"></i>
              </button>
            </div> -->
          </div>
        </div>
      </div>
      </div>
    </div>
  
    <div class="row">
      <!-- Form Label Alignment -->
      <div class="col-md-9">
        <div class="card mb-4">
          <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span>Trainer</span>
            </div>
          </h5>
          <div class="card-body overflow-hidden">
            @csrf  
              <div class="table-responsive overflow-hidden">
                  <table id="table-data-trainer" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                        <tr>
                            <th class="text-left">Nama</th>
                            <th class="text-left">Divisi</th>
                            <th class="text-left">Aksi</th>
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
      <div class="col-md-3"></div>
    </div>

    <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Peserta Training</span>
          </div>
        </h5>
        <div class="card-body overflow-hidden">
          @csrf
          <input type="hidden" id="training_id" value="{{$data->id_training}}">
          <div class="position-relative">
              <!-- <label class="col-sm-2 col-form-label text-center">Nama Perusahaan / Client</label> -->
              <div class="col-sm-2 ">
                  <select id="nama_perusahaan" name="nama_perusahaan" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih Perusahaan -</option>
                    @foreach($namaPerusahaan as $value)
                    <option value="{{$value->id}}"> {{$value->client}}</option>
                    @endforeach
                  </select>
              </div>  
            </div>  
            <div class="table-responsive overflow-hidden">
              <table id="table-data-client" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                  <!-- no, nik, nama, no whatsapp, aksi -->
                      <tr>
                          <th class="text-left">NIK</th>
                          <th class="text-left">Nama</th>
                          <th class="text-left">No Whatsapp</th>
                          <th class="text-left">Status Kirim</th>
                          <th class="text-left">Hadir</th>
                          <th class="text-center">Aksi</th>
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
    <div class="col-md-3"></div>
  </div>

  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Galeri Kegiatan</span>
          </div>
        </h5>
        <div class="card-body overflow-hidden">
          @csrf
          <input type="hidden" id="training_id" value="{{$data->id_training}}">
            
            <div class="table-responsive overflow-hidden">
                <table id="table-data-gallery" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                    <!-- no, nik, nama, no whatsapp, aksi -->
                        <tr>
                            <th class="text-left">Nama</th>
                            <th class="text-left">Keterangan</th>
                            <th class="text-left">Gambar</th>
                            <th class="text-left">Created Date</th>
                            <th class="text-center">Aksi</th>
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
    <div class="col-md-3"></div>
  </div>
  

</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  function copyToClipboard(element) {
      // Copy the text inside the text field
      navigator.clipboard.writeText($(element).val());
      Swal.fire({
        title: 'Pemberitahuan',
        text: 'Berhasil Copy Link',
        icon: 'success',
        customClass: {
          confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
      });
    }

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
  
  var table = $('#table-data-trainer').DataTable({
      scrollX: true,
      "iDisplayLength": 25,
      'processing': true,
      'language': {
      'loadingRecords': '&nbsp;',
      'processing': 'Loading...'
  },
      ajax: {
          url: "{{ route('sdt-training.data-trainer') }}",
          data: function (d) {
              d.client_id = $('#nama_perusahaan').val();
              d.training_id = $('#training_id').val();
              // d.branch = $('#branch').find(":selected").val();
              // d.platform = $('#platform').find(":selected").val();
              // d.status = $('#status').find(":selected").val();
          },
      },
      "createdRow": function( row, data, dataIndex){
          $('td', row).css('background-color', data.warna_background);
          $('td', row).css('color', data.warna_font);
      },      
      "order":[
          [0,'desc']
      ],
      columns:[{
          data : 'nama',
          name : 'nama',
          className:'text-left'
      },{
          data : 'divisi',
          name : 'divisi',
          className:'text-left'
      },{
          data : 'aksi',
          name : 'aksi',
          width: "10%",
          orderable: false,
          searchable: false,
      }],
      "language": datatableLang,
      dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
      buttons: [
            {
            text: '<i class="mdi mdi-account-multiple-outline me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Trainer</span>',
            className: 'btn-add-trainer btn btn-label-primary waves-effect waves-light',
            action: function (e, dt, node, config)
                {
                    $('#modal-gallery').modal('show');
                }
            }
        ]
  });

  var table = $('#table-data-gallery').DataTable({
      scrollX: true,
      "iDisplayLength": 25,
      'processing': true,
      'language': {
      'loadingRecords': '&nbsp;',
      'processing': 'Loading...'
  },
      ajax: {
          url: "{{ route('sdt-training.data-galeri') }}",
          data: function (d) {
              d.training_id = $('#training_id').val();
          },
      },      
      "order":[
          [0,'desc']
      ],
      columns:[{
          data : 'nama',
          name : 'nama',
          className:'text-left'
      },{
          data : 'keterangan',
          name : 'keterangan',
          className:'text-left'
      },{
          data : 'path',
          name : 'path',
          className:'text-left'
      },{
          data : 'created_at',
          name : 'created_at',
          className:'text-left'
      },{
          data : 'aksi',
          name : 'aksi',
          width: "10%",
          orderable: false,
          searchable: false,
      }],
      "language": datatableLang,
      dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
      buttons: [
            {
            text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Gambar Kegiatan</span>',
            className: 'create-new btn btn-label-primary waves-effect waves-light',
            action: function (e, dt, node, config)
                {
                    $('#modal-gallery').modal('show');
                }
            }
        ],
  });

  $('#nama_perusahaan').on('change', function() {
    // alert( this.value );
    $('#table-data-client').dataTable().fnDestroy();
    var table = $('#table-data-client').DataTable({
        scrollX: true,
        "iDisplayLength": 25,
        'processing': true,
        'language': {
        'loadingRecords': '&nbsp;',
        'processing': 'Loading...'
    },
        ajax: {
            url: "{{ route('sdt-training.client-peserta') }}",
            data: function (d) {
                d.client_id = $('#nama_perusahaan').val();
                d.training_id = $('#training_id').val();
                // d.branch = $('#branch').find(":selected").val();
                // d.platform = $('#platform').find(":selected").val();
                // d.status = $('#status').find(":selected").val();
            },
        },
        "createdRow": function( row, data, dataIndex){
            $('td', row).css('background-color', data.warna_background);
            $('td', row).css('color', data.warna_font);
        },      
        "order":[
            [0,'desc']
        ],
        columns:[{
            data : 'nik',
            name : 'nik',
            className:'text-left'
        },{
            data : 'nama',
            name : 'nama',
            className:'text-left'
        },{
            data : 'no_whatsapp',
            name : 'no_whatsapp',
            className:'text-left'
        },{
            data : 'status_whatsapp',
            name : 'status_kirim',
            className:'text-left'
        },{
            data : 'status_hadir',
            name : 'hadir',
            className:'text-left'
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
        dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
        buttons: [
            {
            text: '<i class="mdi mdi-account-multiple-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Peserta</span>',
            className: 'btn-add-peserta btn btn-label-primary waves-effect waves-light',
            action: function (e, dt, node, config)
                {
                   $('#modal-peserta').modal('show');  
                }
            }
        ]
    });

  });

  $('body').on('click', '.btn-delete-trainer', function() {
      let id = $(this).data('id');
      Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah anda ingin hapus trainer?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: 'primary',
          cancelButtonColor: 'warning',
          confirmButtonText: 'Hapus'
      }).then(function (result) {
          console.log(result)
          if (result.isConfirmed) {
              let formData = {
                  "id":id,
                  "_token": "{{ csrf_token() }}"
              };

              let table ='#table-data-trainer';
              $.ajax({
                  type: "POST",
                  url: "{{route('sdt-training.delete-trainer')}}",
                  data:formData,
                  success: function(response){
                      console.log(response)
                      if (response.success) {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'success',
                              timer: 1000,
                              timerProgressBar: true,
                              willClose: () => {
                                  $(table).DataTable().ajax.reload();
                              }
                          })
                      } else {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'error'
                          })
                      }
                  },
                  error:function(error){
                      Swal.fire({
                          title: 'Pemberitahuan',
                          text: error,
                          icon: 'error'
                      })
                  }
              });
          }
      });
  });

  $('body').on('click', '.btn-delete-gallery', function() {
      let id = $(this).data('id');
      Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah anda ingin hapus gallery?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: 'primary',
          cancelButtonColor: 'warning',
          confirmButtonText: 'Hapus'
      }).then(function (result) {
          console.log(result)
          if (result.isConfirmed) {
              let formData = {
                  "id":id,
                  "_token": "{{ csrf_token() }}"
              };

              let table ='#table-data-gallery';
              $.ajax({
                  type: "POST",
                  url: "{{route('sdt-training.delete-gallery')}}",
                  data:formData,
                  success: function(response){
                      console.log(response)
                      if (response.success) {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'success',
                              timer: 1000,
                              timerProgressBar: true,
                              willClose: () => {
                                  // $(table).DataTable().ajax.reload();
                                  location.reload();
                              }
                          })
                      } else {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'error'
                          })
                      }
                  },
                  error:function(error){
                      Swal.fire({
                          title: 'Pemberitahuan',
                          text: error,
                          icon: 'error'
                      })
                  }
              });
          }
      });
  });

  $('body').on('click', '.btn-delete-peserta', function() {
      let id = $(this).data('id');
      Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah anda ingin hapus peserta?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: 'primary',
          cancelButtonColor: 'warning',
          confirmButtonText: 'Hapus'
      }).then(function (result) {
          console.log(result)
          if (result.isConfirmed) {
              let formData = {
                  "id":id,
                  "_token": "{{ csrf_token() }}"
              };

              let table ='#table-data-client';
              $.ajax({
                  type: "POST",
                  url: "{{route('sdt-training.delete-peserta')}}",
                  data:formData,
                  success: function(response){
                      console.log(response)
                      if (response.success) {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'success',
                              timer: 1000,
                              timerProgressBar: true,
                              willClose: () => {
                                  $(table).DataTable().ajax.reload();
                              }
                          })
                      } else {
                          Swal.fire({
                              title: 'Pemberitahuan',
                              text: response.message,
                              icon: 'error'
                          })
                      }
                  },
                  error:function(error){
                      Swal.fire({
                          title: 'Pemberitahuan',
                          text: error,
                          icon: 'error'
                      })
                  }
              });
          }
      });
  });

  // $('#btn-update').on('click',function () {
  //   $('form').submit();
  // });
  
  // $('#btn-delete').on('click',function () {
  //   $('form').attr('action', '{{route("leads.delete")}}');
  //   $('form').submit();
  // });
  
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

  // $('#btn-activity').on('click',function () {
  
  // });


  // $('#btn-quotation').on('click',function () {
  
  // });


  $('#btn-kembali').on('click',function () {
    // alert('jljljjj');
    window.location.href = '{{route("sdt-training")}}';
  });

  $('#btn-add-client').on('click',function(){
    $('#modal-client').modal('show');  
  });

  $('#btn-pesan-undangan').on('click',function(){
    $('#modal-pesan-undangan').modal('show');  
  });

  // $('#btn-add-gallery').on('click',function(){
  //   $('#modal-gallery').modal('show');  
  // });

  // $('#btn-add-peserta').on('click',function(){
  //   $('#modal-peserta').modal('show');  
  // });

  $('#btn-add-trainer').on('click',function(){
    $('#modal-trainer').modal('show');  
  });

  $('#btn-send-message').on('click',function(){
    $('#modal-link').modal('show');  
  });

  $(document).ready(function() {

  
    $('#btn-kirim-wa').on('click',function(){
        let id = $('#training_id').val();
        let noWa = $('#link-wa').val();
      
        Swal.fire({
            target: document.getElementById('modal-link'),
            title: 'Konfirmasi',
            text: 'Apakah anda ingin kirim undangan whatsapp ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'info',
            cancelButtonColor: 'warning',
            confirmButtonText: 'Kirim Whatsapp'
        }).then(function (result) {
            console.log(result)
            if (result.isConfirmed) {
                let formData = {
                    "id":id,
                    "no_wa" :noWa,
                    "_token": "{{ csrf_token() }}"
                };

                let table ='#table-data';
                $.ajax({
                    type: "POST",
                    url: "{{route('sdt-training.send-message')}}",
                    data:formData,
                    success: function(response){
                        console.log(response)
                        if (response.success) {
                            Swal.fire({
                                title: 'Pemberitahuan',
                                text: response.message,
                                icon: 'success',
                                timer: 1000,
                                timerProgressBar: true,
                                willClose: () => {
                                    // $(table).DataTable().ajax.reload();
                                    $('#modal-link').modal('hide');  
                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'Pemberitahuan',
                                text: response.message,
                                icon: 'error'
                            })
                        }
                    },
                    error:function(error){
                        Swal.fire({
                            title: 'Pemberitahuan',
                            text: error,
                            icon: 'error'
                        })
                    }
                });
            }
        });
    });

    $('#btn-add-client-save').on('click',function(){
      $('#modal-client').modal('hide');
      let id = $('#training_id').val();
      let client_id = $('#client_id').val();
    
      if(client_id == ''){
        Swal.fire({
                  title: 'Pemberitahuan',
                  text: "Mohon untuk memilih data client yang akan di tambahkan",
                  icon: 'error'
              })
      }else{
        let formData = {
            "id":id,
            "client_id":client_id,
            "_token": "{{ csrf_token() }}"
        };

        let table ='#table-data-client';
        $.ajax({
            type: "POST",
            url: "{{route('sdt-training.add-client')}}",
            data:formData,
            success: function(response){
                // console.log(response);
                // alert(response)
                if (response.success) {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                        willClose: () => {
                          location.reload();
                        }
                    })
                } else {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'error'
                    })
                }
            },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
      }
    });

    function downloadFile(response) {
      alert(response);
      var blob = new Blob([response], {type: 'application/pdf'})
      var url = URL.createObjectURL(blob);
      location.assign(url);
    } 

    $('#btn-generate-pdf').on('click',function(){
      let id = $('#training_id').val();
      let formData = {
            "training_id": id,
            "_token": "{{ csrf_token() }}"
        };

        $.ajax({
            type: "GET",
            url: "{{route('invite-pdf')}}",
            data:formData,
            success: function(response){
              downloadFile(response);
              // Create a link element to download the file
                // var blob = new Blob([response], { type: 'application/pdf' });
                // var link = document.createElement('a');
                // link.href = URL.createObjectURL(blob);
                // link.download = 'your-pdf-file.pdf';
                // link.click();
            },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
    });
    
    $('#btn-add-peserta-save').on('click',function(){
      $('#modal-peserta').modal('hide');
      let id = $('#training_id').val();
      let trainer_id = $('#peserta_id').val();
      let client_id = $('#nama_perusahaan').val()
    
      if(client_id == ''){
        Swal.fire({
                  title: 'Pemberitahuan',
                  text: "Mohon untuk memilih nama perusahaan terlebih dahulu",
                  icon: 'error'
              })
      }else{
        let formData = {
            "id":id,
            "client_id":client_id,
            "employee_id": trainer_id,
            "_token": "{{ csrf_token() }}"
        };

        let table ='#table-data-client';
        $.ajax({
            type: "POST",
            url: "{{route('sdt-training.add-peserta')}}",
            data:formData,
            success: function(response){
                // console.log(response);
                // alert(response)
                if (response.success) {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                        willClose: () => {
                            $(table).DataTable().ajax.reload();
                        }
                    })
                } else {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'error'
                    })
                }
            },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
      }
    });

    $('#btn-add-trainer-save').on('click',function(){
      $('#modal-trainer').modal('hide');
      let id = $('#training_id').val();
      let trainer_id = $('#trainer_id').val();
      // alert(id + ' ' + trainer_id);
      let formData = {
          "id":id,
          "trainer_id": trainer_id,
          "_token": "{{ csrf_token() }}"
      };

      let table ='#table-data-trainer';
      $.ajax({
          type: "POST",
          url: "{{route('sdt-training.add-trainer')}}",
          data:formData,
          success: function(response){
              // console.log(response);
              // alert(response)
              if (response.success) {
                  Swal.fire({
                      title: 'Pemberitahuan',
                      text: response.message,
                      icon: 'success',
                      timer: 1000,
                      timerProgressBar: true,
                      willClose: () => {
                          $(table).DataTable().ajax.reload();
                      }
                  })
              } else {
                  Swal.fire({
                      title: 'Pemberitahuan',
                      text: response.message,
                      icon: 'error'
                  })
              }
          },
          error:function(error){
              Swal.fire({
                  title: 'Pemberitahuan',
                  text: error,
                  icon: 'error'
              })
          }
      });
    });

    $('#btn-add-pesan-undangan-save').on('click',function(){
      $('#modal-pesan-undangan').modal('hide');
      
      let id = $('#training_id').val();
      let pesan = $('#pesan-undangan').val();
      // alert(id + ' ' + pesan);
      let formData = {
          "id":id,
          "pesan_undangan": pesan,
          "_token": "{{ csrf_token() }}"
      };

      $.ajax({
            type: "POST",
            url: "{{route('sdt-training.save-message')}}",
            data:formData,
            success: function(response){
                // console.log(response);
                // alert(response)
                if (response.success) {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                        willClose: () => {
                          location.reload();
                        }
                    })
                } else {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'error'
                    })
                }
            },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
    });

    // $('#select2insidemodal').select2();
    // $("#trainer_id").select2({
    //   dropdownParent: $("#modal-trainer")
    // });
  });


  
  
  
  // $("#select2Input").select2({ dropdownParent: "#modal-container" });


  // $('.select2insidemodal').each(function() { 
  //   $(this).select2({ dropdownParent: $(this).parent()});
  // })
</script>

<div class="modal fade" id="modal-link" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Kirim Link Training : <p id="nama"></p></h4>
        </div>
        <br>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">No Whatsapp <span class="text-danger">delimiter comma</span></label>
            <div class="col-sm-9">
              <div class="position-relative">
                <input type="text" id="link-wa" value="" class="form-control @if ($errors->any())   @endif">
              </div>
            </div>
        </div>  
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-kirim-wa" class="btn btn-primary">Kirim</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-client" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Tambah Client : <p id="nama"></p></h4>
        </div>
        <br>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">Client <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <div class="position-relative">
                <select id="client_id" name="client_id" class="select2 form-select">
                  <option value="">- Pilih data -</option>
                  @foreach($listClient as $value)
                  <option value="{{$value->id}}"> {{$value->client}}</option>
                  @endforeach
                </select>
                @if($errors->has('client_id'))
                  <div class="invalid-feedback">{{$errors->first('client_id')}}</div>
                @endif
              </div>
            </div>
        </div>  
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-add-client-save" class="btn btn-primary">Add Peserta</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-pesan-undangan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Isi Pesan Undangan : <p id="nama"></p></h4>
        </div>
        
        <div class="row">    
            <!-- <label class="col-sm-3 col-form-label text-sm-end">Client <span class="text-danger">*</span></label> -->
            <div class="col-sm-12">
              <div class="position-relative">
                <textarea style="height: 100% !important;" rows="12" cols="50" class="form-control h-px-100 @if ($errors->any())   @endif" name="pesan-undangan" id="pesan-undangan" placeholder="">{{$message}}</textarea>
              </div>
            </div>
        </div>  
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-add-pesan-undangan-save" class="btn btn-primary">Save</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-peserta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Tambah Peserta : <p id="nama"></p></h4>
        </div>
        <br>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">Peserta <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <div class="position-relative">
                <select id="peserta_id" name="peserta_id" class="select2 form-select">
                  <option value="">- Pilih data -</option>
                  @foreach($listPeserta as $value)
                  <option value="{{$value->id}}"> {{$value->full_name . ' - ' .$value->position }}</option>
                  @endforeach
                </select>
                @if($errors->has('peserta_id'))
                  <div class="invalid-feedback">{{$errors->first('peserta_id')}}</div>
                @endif
              </div>
            </div>
        </div>  
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-add-peserta-save" class="btn btn-primary">Add Peserta</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-gallery" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Tambah Gallery <p id="nama"></p></h4>
        </div>
        <br>
        <div class="row mb-3">    
          <form action="{{route('sdt-training.upload-image')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- <h6>1. Informasi Perusahaan</h6> -->
            <input type="hidden" name="id" value="{{$data->id_training}}">
            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">Nama</label>
              <div class="col-sm-9">
                <div class="position-relative">
                  <input type="text" name="nama" value="" class="form-control @if ($errors->any())   @endif">
                </div>
              </div>
            </div> 
            
            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">File</label>
              <div class="col-sm-9">
                <div class="position-relative">
                  <input type="file" name="image" class="form-control @if ($errors->any())   @endif">
                </div>
              </div>
            </div> 

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">Keterangan</label>
              <div class="col-sm-9">
                <div class="form-floating form-floating-outline mb-4">
                  <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="keterangan" placeholder=""></textarea>
                </div>
              </div>
            </div>
            <div class="pt-4">
              <div class="row justify-content-end">
                <div class="col-sm-12 d-flex justify-content-end">
                  <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Add Gallery</button>
                </div>
              </div>
            </div>
          </form>
        </div>  
        <!-- </div> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-trainer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Tambah Trainer : <p id="nama"></p></h4>
        </div>
        <br>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">Trainer <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <div class="position-relative">
                <select id="trainer_id" name="trainer_id" class="select2 form-select">
                  <option value="">- Pilih data -</option>
                  @foreach($listTrainer as $value)
                  @if($value->id==99) @continue @endif
                  <option value="{{$value->id}}" @if(old('trainer_id') == $value->id) selected @endif>{{$value->trainer}}</option>
                  @endforeach
                </select>
                @if($errors->has('trainer_id'))
                  <div class="invalid-feedback">{{$errors->first('trainer_id')}}</div>
                @endif
              </div>
            </div>
        </div>  
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-add-trainer-save" class="btn btn-primary">Add Trainer</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

@endsection