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
            <!-- <span>SDT Training</span> -->
          </div>
        </h5>
        <div class="card-body overflow-hidden">
          @csrf
          <input type="hidden" id="training_id" value="{{$data->id_training}}">
          
          <h6>Peserta Training</h6>
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
            <div class="table-responsive overflow-hidden table-data-client">
                <table id="table-data-client" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                    <!-- no, nik, nama, no whatsapp, aksi -->
                        <tr>
                            <th class="text-center">NIK</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">No Whatsapp</th>
                            <th class="text-center">Status Kirim</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
            </div>
      
          
          <br>
          <br>
          <br>

          <h6>Trainer</h6>
          <div class="row mb-2">
            <div class="table-responsive overflow-hidden table-data-trainer">
                <table id="table-data-trainer" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                        <tr>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Divisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
            </div>
          </div>
          <hr class="my-4 mx-4">
         
          
          <hr class="my-4 mx-4">
          <div class="pt-4">
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
            @if(in_array(Auth::user()->role_id,[29,30,31,32,33,48,49]))
            <div class="col-12 text-center">
              <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Update Data</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div>
            @endif
            @if(in_array(Auth::user()->role_id,[29,31,32,33]))
            <!-- <div class="col-12 text-center mt-2">
              <button id="btn-quotation" class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Create Quotation</span>
                <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
              </button>
            </div> -->
            @endif
            @if(in_array(Auth::user()->role_id,[29,30,31,32,33]))
            <!-- <div class="col-12 text-center mt-2">
              <button id="btn-activity" class="btn btn-info w-100 waves-effect waves-light">
                <span class="me-1">Create Activity</span>
                <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
              </button>
            </div> -->
            @endif
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
              <button id="btn-add-peserta" class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Tambah Peserta</span>
                  <i class="mdi mdi-account-multiple-plus scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light">
                <span class="me-1">Tambah Trainer</span>
                  <i class="mdi mdi-account-multiple-outline scaleX-n1-rtl"></i>
              </button>
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
          className:'text-center'
      },{
          data : 'divisi',
          name : 'divisi',
          className:'text-center'
      },{
          data : 'aksi',
          name : 'aksi',
          width: "10%",
          orderable: false,
          searchable: false,
      }],
      "language": datatableLang,
      dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
      buttons: []
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
            className:'text-center'
        },{
            data : 'nama',
            name : 'nama',
            className:'text-center'
        },{
            data : 'no_whatsapp',
            name : 'no_whatsapp',
            className:'text-center'
        },{
            data : 'status_whatsapp',
            name : 'status_kirim',
            className:'text-center'
        },{
            data : 'status_hadir',
            name : 'hadir',
            className:'text-center'
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
        dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
        buttons: []
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
    window.history.go(-1); return false;
    // window.location.replace("{{route('leads')}}");
  });

  $('#btn-add-client').on('click',function(){
    $('#modal-client').modal('show');  
  });

  $('#btn-add-peserta').on('click',function(){
    $('#modal-peserta').modal('show');  
  });

  $('#btn-add-trainer').on('click',function(){
    $('#modal-trainer').modal('show');  
  });


  $(document).ready(function() {

    $('#btn-send-message').on('click',function(){
        let id = $('#training_id').val();
        Swal.fire({
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
                  <option value="{{$value->id}}"> {{$value->client . ' - ' .$value->kab_kota}}</option>
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


<!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> -->
<!-- <div id="myModal" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel">Panel</h3>
      </div>
      <div class="modal-body" style="max-height: 800px">
        <select id="select2insidemodal" multiple="multiple">
          <option value="AL">Alabama</option>
            ...
          <option value="WY">Wyoming</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->

<!-- <div class="modal" id="modal-trainer" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> -->
@endsection