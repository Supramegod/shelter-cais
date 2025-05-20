@extends('layouts.master')
@section('title','SDT Training Gada')
@section('pageStyle')
<style>
    .dt-buttons {width: 100%;}
</style>
@endsection
@section('content')
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Row -->
    <div class="row row-sm mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                    <div class="col-md-6 text-left col-12 my-auto">
                        <h3 class="page-title">Data Pendaftar Training Gada</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">SDT</a></li>
							<li class="breadcrumb-item active" aria-current="page">Training Gada</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('sdt-training')}}" method="GET">
                        <div class="col-md-12">
                            <div class="row">
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-left">Nama</th>    
                                    <th class="text-left">Email</th>
                                    <th class="text-left">No WA</th>
                                    <th class="text-left">Jenis Pelatihan</th>
                                    <th class="text-left">Alamat</th>
                                    <th class="text-left">Register Date</th>
                                    <th class="text-left">Status</th>
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
    </div>
    <!-- End Row -->
    <!--/ Responsive Datatable -->
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
    
    function saveData() {
        Swal.fire({
          target: document.getElementById('modal-status'),
          title: 'Konfirmasi',
          text: 'Apakah anda ingin mengubah status?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: 'primary',
          cancelButtonColor: 'warning',
          confirmButtonText: 'Ubah'
      }).then(function (result) {
        $('#modal-status').modal('hide');
      
        let id = $('#register_id').val();
        let status_id = $('#status_id').val();
        let keterangan = $('#keterangan').val();
        
        if(status_id == ''){
            Swal.fire({
                    title: 'Pemberitahuan',
                    text: "Mohon untuk memilih data status",
                    icon: 'error'
                })
        }else{
            if (result.isConfirmed) {
                    let formData = {
                        "id":id,
                        "status_id":status_id,
                        "keterangan":keterangan,
                        "_token": "{{ csrf_token() }}"
                    };

                    let table ='#table-data';
                    $.ajax({
                        type: "POST",
                        url: "{{route('training-gada.updateStatus')}}",
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
        }
        });
    };

    function showChangeStatus(i) {
        $("#status_id").val("0").change();
        $('#modal-status').modal('show');  
        $("#keterangan").val('');
        $("#register_id").val(i);
    }

    function showlistLog(i) {
        $('#modal-status-log').modal('show'); 
        let formData = {
            "pendaftar_id":i,
            "_token": "{{ csrf_token() }}"
        };

        // let table ='#table-data-log';
        // $.ajax({
        //     type: "GET",
        //     url: "{{route('training-gada.listLog')}}",
        //     data:formData,
        //     success: function(response){
        //         console.log(response)
        //         $(table).DataTable().ajax.reload();
        //     },
        //     error:function(error){
        //         Swal.fire({
        //             title: 'Pemberitahuan',
        //             text: error,
        //             icon: 'error'
        //         })
        //     }
        // }); 
        
        $("#table-data-log").dataTable().fnDestroy();
        var table = $('#table-data-log').DataTable({
        scrollX: true,
            "iDisplayLength": 25,
            'processing': true,
            'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...',
            "bDestroy": true
        },
        ajax: {
            url: "{{ route('training-gada.listLog') }}",
            data: function (d) {
                d.pendaftar_id = i;
            },
        },
        columns:[
            {
            data : 'created_date',
            name : 'created_date',
            className:'text-left'
        },{
            data : 'status_name',
            name : 'status_name',
            className:'text-left'
        },{
            data : 'keterangan',
            name : 'keterangan',
            className:'text-left'
        }],
            "language": datatableLang
        });
    }

    @if(isset($success) || session()->has('success'))  
    @endif
    @if(isset($error) || session()->has('error'))  
        
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
                url: "{{ route('training-gada.list') }}",
                data: function (d) {},
            },
            "createdRow": function( row, data, dataIndex){
                $('td', row).css('background-color', data.warna_background);
                $('td', row).css('color', data.warna_font);
            },      
            "order":[
                [0,'desc']
            ],
            columns:[
                {
                data : 'nama',
                name : 'nama',
                className:'text-left'
            },{
                data : 'email',
                name : 'email',
                className:'text-left'
            },{
                data : 'no_wa',
                name : 'no_wa',
                className:'text-left'
            },{
                data : 'jenis_pelatihan',
                name : 'jenis_pelatihan',
                className:'text-left'
            },{
                data : 'alamat',
                name : 'alamat',
                className:'text-left'
            },{
                data : 'register_date',
                name : 'register_date',
                className:'text-left'
            },{
                data : 'status_name',
                name : 'status',
                className:'text-left'
            },{
                data : 'aksi',
                name : 'aksi',
                width: "10%",
                orderable: false,
                searchable: false,
            }],
            "language": datatableLang
        });
</script>

<div class="modal fade" id="modal-status" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Ubah Status : <p id="nama"></p></h4>
        </div>
        <input hidden type="text" class="form-control" id="register_id" name="register_id"/>  
        <div class="row mb-3">    
            <label class="col-sm-4 col-form-label text-sm-end">Status <span class="text-danger">*</span></label>
            <div class="col-sm-7">
              <div class="position-relative">
                <select id="status_id" name="status_id" class="select2 form-select">
                  <option value="0" >- Pilih Status -</option>
                  <option value="2">Leads</option>
                  <option value="3">Cold Prospect</option>
                  <option value="4">Hot Prospect</option>
                  <option value="5">Peserta</option>
                </select>
              </div>
            </div>
        </div>  
        <div class="row mb-3">    
            <label class="col-sm-4 col-form-label text-sm-end">Keterangan <span class="text-danger">*</span></label>
            <div class="col-sm-7">
              <div class="position-relative">
                <textarea class="form-control h-px-100" name="keterangan" id="keterangan" placeholder="Mohon isi keterangan"></textarea>
              </div>
            </div>
        </div>  
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-status-save" onclick="saveData()" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-status-log" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <br>
        <div class="table-responsive overflow-hidden table-data">
            <table id="table-data-log" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                    <tr>
                        <th class="text-left">Jam</th>    
                        <th class="text-left">Status</th>
                        <th class="text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

@endsection