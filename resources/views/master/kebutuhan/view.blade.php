@extends('layouts.master')
@section('title','Kebutuhan')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Kebutuhan</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <h4>Detail Kebutuhan</h4>
                <p class="mt-0">Kebutuhan : {{$kebutuhan->nama}}</p>
                <input type="text" id="kebutuhan_id" value="{{$kebutuhan->id}}" hidden>
            </div>
        </div>
        <div class="card-body">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nama Jabatan</th>
                        <th class="text-center">Tunjangan</th>
                        <th class="text-center">Requirement</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- data table ajax --}}
                </tbody>
            </table>
          </div>
        </div>
        <div class="pt-4">
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Content -->


<div class="modal fade" id="modal-tunjangan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Detail Tunjangan : <p id="nama"></p></h4>
        </div>
        <button id="btn-add-tunjangan" class="btn btn-primary waves-effect waves-light">
          <span class="me-1">Tambah Tunjangan</span>
          <i class="mdi mdi-plus scaleX-n1-rtl"></i>
        </button>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data-tunjangan">
            <table id="table-data-tunjangan" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Tunjangan</th>
                    <th class="text-center">Nominal</th>
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
  </div>
</div>

<div class="modal fade" id="modal-add-tunjangan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Add Tunjangan : <p id="nama-2"></p></h4>
        </div>
        <div class="row">
          <label class="col-sm-12 col-form-label text-sm-start">Nama Tunjangan <span class="text-danger">*</span></label>
          <div class="col-sm-12">
            <input autofocus type="text" id="tunjangan" name="tunjangan" value="{{old('tunjangan')}}" class="form-control @if ($errors->any()) @if($errors->has('tunjangan')) is-invalid @else   @endif @endif">
            @if($errors->has('tunjangan'))
                <div class="invalid-feedback">{{$errors->first('tunjangan')}}</div>
            @endif
          </div>
        </div>
        <div class="row mb-3">
          <label class="col-sm-12 col-form-label text-sm-start">Nominal <span class="text-danger">*</span></label>
          <div class="col-sm-12">
            <input type="text" id="nominal" name="nominal" value="{{old('nominal')}}" class="form-control @if ($errors->any()) @if($errors->has('nominal')) is-invalid @else   @endif @endif">
            @if($errors->has('nominal'))
                <div class="invalid-feedback">{{$errors->first('nominal')}}</div>
            @endif
          </div>
        </div>
        <center>
          <button id="btn-save-tunjangan" class="btn btn-primary mt-5">
            <span class="me-1">SIMPAN</span>
          </button>
        </center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-requirement" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Detail Requirement : <p id="nama-2"></p></h4>
        </div>
        <button id="btn-add-req" class="btn btn-primary waves-effect waves-light">
          <span class="me-1">Tambah Requirement</span>
          <i class="mdi mdi-plus scaleX-n1-rtl"></i>
        </button>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data-requirement">
            <table id="table-data-requirement" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Requirement</th>
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
  </div>
</div>

<div class="modal fade" id="modal-add-requirement" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Add Requirement : <p id="nama-2"></p></h4>
        </div>
        <div class="row">
          <label class="col-sm-12 col-form-label text-sm-start">Requirement <span class="text-danger">*</span></label>
          <div class="col-sm-12">
            <input autofocus type="text" id="requirement" name="requirement" value="{{old('requirement')}}" class="form-control @if ($errors->any()) @if($errors->has('requirement')) is-invalid @else   @endif @endif">
            @if($errors->has('requirement'))
                <div class="invalid-feedback">{{$errors->first('requirement')}}</div>
            @endif
          </div>
        </div>
        <center>
          <button id="btn-save-req" class="btn btn-primary mt-5">
            <span class="me-1">SIMPAN</span>
          </button>
        </center>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
  <script>
      $('#btn-kembali').on('click',function () {
        window.location.replace("{{route('kebutuhan')}}");
      });

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
      @if(session()->has('error'))  
          Swal.fire({
              title: 'Pemberitahuan',
              html: '{{session()->has('error')}}',
              icon: 'warning',
              customClass: {
              confirmButton: 'btn btn-warning waves-effect waves-light'
              },
              buttonsStyling: false
          });
      @endif

      let dt_filter_table = $('.dt-column-search');

            var table = $('#table-data').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
                ajax: {
                    url: "{{ route('kebutuhan.list-detail') }}",
                    data: function (d) {
                        d.kebutuhan_id = {{$kebutuhan->id}};
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
                    data : 'nama',
                    name : 'nama',
                    className:'dt-body-left'
                },{
                    data : 'tunjangan',
                    name : 'tunjangan',
                    className:'text-center'
                },{
                    data : 'requirement',
                    name : 'requirement',
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
                dom: 'frtip',
                buttons: [
                ],
            });

        $('body').on('click', '.btn-view-tunjangan', function() {
            let nama = $(this).data('nama');
            $('#nama').text(nama);

            let dt_filter_tables = $('.dt-column-search');
            var tables = $('#table-data-tunjangan').DataTable({
                retrieve: true,
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
                ajax: {
                    url: "{{ route('kebutuhan.list-detail-tunjangan') }}",
                    data: function (d) {
                        d.kebutuhan_id = {{$kebutuhan->id}};
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
                    data : 'nama',
                    name : 'nama',
                    className:'dt-body-left'
                },{
                    data : 'nominal',
                    name : 'nominal',
                    className:'dt-body-right',
                    render: $.fn.dataTable.render.number('.','.', 0,'')
                },{
                    data : 'aksi',
                    name : 'aksi',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: 'frtip',
                buttons: [
                ],
            });
            
            // tables.destroy();
            $('#modal-tunjangan').modal('toggle');
            
        });
        
        $('body').on('click', '.btn-view-requirement', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            $('#nama-2').text(nama);

            let dt_filter_tables = $('.dt-column-search');
            var tables = $('#table-data-requirement').DataTable({
                retrieve: true,
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
                ajax: {
                    url: "{{ route('kebutuhan.list-detail-requirement') }}",
                    data: function (d) {
                        d.kebutuhan_id = {{$kebutuhan->id}};
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
                    data : 'requirement',
                    name : 'requirement',
                    className:'dt-body-left'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: 'frtip',
                buttons: [
                ],
            });

            $('#modal-requirement').modal('toggle');
            
        });

        $('#table-data-requirement').on('click', '.btn-delete', function() {
          let id = $(this).data('id');
          $('#modal-requirement').modal('toggle');
          
          Swal.fire({
              title: 'Konfirmasi',
              text: 'Apakah anda ingin hapus data ini ?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: 'primary',
              cancelButtonColor: 'warning',
              confirmButtonText: 'Hapus'
          }).then(function (result) {
              if (result.isConfirmed) {
                  let formData = {
                      "id":id,
                      "_token": "{{ csrf_token() }}"
                  };

                  let table ='#table-data-requirement';
                  $.ajax({
                      type: "POST",
                      url: "{{route('kebutuhan.delete-detail-requirement')}}",
                      data:formData,
                      success: function(response){
                          if (response.success) {
                              Swal.fire({
                                  title: 'Pemberitahuan',
                                  text: response.message,
                                  icon: 'success',
                                  timer: 1000,
                                  timerProgressBar: true,
                                  willClose: () => {
                                    $('#modal-requirement').modal('toggle');
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
        
        $('#table-data-tunjangan').on('click', '.btn-delete', function() {
          let id = $(this).data('id');
          $('#modal-tunjangan').modal('toggle');
          
          Swal.fire({
              title: 'Konfirmasi',
              text: 'Apakah anda ingin hapus data ini ?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: 'primary',
              cancelButtonColor: 'warning',
              confirmButtonText: 'Hapus'
          }).then(function (result) {
              if (result.isConfirmed) {
                  let formData = {
                      "id":id,
                      "_token": "{{ csrf_token() }}"
                  };

                  let table ='#table-data-tunjangan';
                  $.ajax({
                      type: "POST",
                      url: "{{route('kebutuhan.delete-detail-tunjangan')}}",
                      data:formData,
                      success: function(response){
                          if (response.success) {
                              Swal.fire({
                                  title: 'Pemberitahuan',
                                  text: response.message,
                                  icon: 'success',
                                  timer: 1000,
                                  timerProgressBar: true,
                                  willClose: () => {
                                    $('#modal-tunjangan').modal('toggle');
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

        $('#btn-add-tunjangan').on('click', function() {
          $('#modal-tunjangan').modal('toggle');
          $('#modal-add-tunjangan').modal('toggle');
        });

        $('#btn-save-tunjangan').on('click', function() {
          let table ='#table-data-tunjangan';
          const tunjangan = $('#tunjangan').val();
          const nominal = $('#nominal').val();

          if(tunjangan == '' || tunjangan == null || nominal == '' || nominal == null){
            $('#modal-add-tunjangan').modal('toggle');
            Swal.fire({
              title: "Pemberitahuan",
              html: 'Field tidak boleh kosong',
              icon: "warning",
            });
          }else{
            $('#modal-add-tunjangan').modal('toggle');
            $.ajax({
              type: "POST",
              url: "{{route('kebutuhan.add-detail-tunjangan')}}",
              data: {
                "_token": "{{ csrf_token() }}",
                nama: tunjangan,
                nominal: nominal,
                kebutuhan_id: {{$kebutuhan->id}}
              },
              success: function(response){
                if (response.success) {
                  Swal.fire({
                    title: 'Pemberitahuan',
                    text: response.message,
                    icon: 'success',
                    timer: 1000,
                    timerProgressBar: true,
                    willClose: () => {
                      $('#modal-tunjangan').modal('toggle');
                      $('#table-data-tunjangan').DataTable().ajax.reload();
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
        
        $('#btn-add-req').on('click', function() {
          $('#modal-requirement').modal('toggle');
          $('#modal-add-requirement').modal('toggle');
        });

        $('#btn-save-req').on('click', function() {
          let table ='#table-data-requirement';
          const requirement = $('#requirement').val();

          if(requirement == '' || requirement == null){
            $('#modal-add-requirement').modal('toggle');
            Swal.fire({
              title: "Pemberitahuan",
              html: 'Field tidak boleh kosong',
              icon: "warning",
            });
          }else{
            $('#modal-add-requirement').modal('toggle');
            $.ajax({
              type: "POST",
              url: "{{route('kebutuhan.add-detail-requirement')}}",
              data: {
                "_token": "{{ csrf_token() }}",
                requirement: requirement,
                kebutuhan_id: {{$kebutuhan->id}}
              },
              success: function(response){
                if (response.success) {
                  Swal.fire({
                    title: 'Pemberitahuan',
                    text: response.message,
                    icon: 'success',
                    timer: 1000,
                    timerProgressBar: true,
                    willClose: () => {
                      $('#modal-requirement').modal('toggle');
                      $('#table-data-requirement').DataTable().ajax.reload();
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
  </script>
  
<script>
  var elem = document.getElementById("nominal");

  elem.addEventListener("keydown",function(event){
      var key = event.which;
      if((key<48 || key>57) && key != 8) event.preventDefault();
  });

  elem.addEventListener("keyup",function(event){
      var value = this.value.replace(/,/g,"");
      this.dataset.currentValue=parseInt(value);
      var caret = value.length-1;
      while((caret-3)>-1)
      {
          caret -= 3;
          value = value.split('');
          value.splice(caret+1,0,",");
          value = value.join('');
      }
      this.value = value;
  });
</script>
@endsection