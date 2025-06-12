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
                                    <th class="text-left">Nik</th>    
                                    <th class="text-left">Email</th>
                                    <th class="text-left">No WA</th>
                                    <th class="text-left">Jenis Pelatihan</th>
                                    <th class="text-left">Alamat</th>
                                    <th class="text-left">Register Date</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">Invoice</th>
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
                                      $('#modal-status').modal('hide');
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

    function showChangeStatus(i, status) {
        $("#status_id").val(status).change();
        $('#modal-status').modal('show');  
        $("#keterangan").val('');
        $("#register_id").val(i);
    }

    function showMessageWhatsapp(i, noWa, lastSent, jenisPelatihan, nama) {
      var message = "Dear "+nama+"\nCalon Peserta Training " + jenisPelatihan + "\n\n";
      message = message + "Mohon untuk melengkapi data melalui Form pada Link berikut :\nhttps://docs.google.com/forms/d/e/1FAIpQLSeYoyfuvBByJY-1TWOj-wLYJ-eoz-xQvOypRBh7EgUjeFrWLA/viewform";
      // message = str_replace(',', ' dan ', $message);

        $("#notif_register_id").val(i);
        $("#notif_register_wa").val(noWa);
        $("#notif_register_kirim").text(lastSent);
        $("#notif_register_message").text(message);
        $('#modal-notifikasi-pendaftaran').modal('show');
    }
    
    function showMessageWhatsappInvoice(i, noWa, lastSent, jenisPelatihan, nama) {
      let formData = {
            "pendaftar_id":i,
            "_token": "{{ csrf_token() }}"
      };
        
      $.ajax({
          type: "GET",
          url: "{{route('training-gada.dataInvoice')}}",
          data:formData,
          success: function(response){
              console.log(response);
              
              var message = "Dear "+nama+"\nCalon Peserta Training " + jenisPelatihan + "\n\n";
              message = message + "Mohon untuk segera melakukan pembayaran sejumlah Rp. "+response.totalHarga+", ke nomor rekening :\n\n";
              message = message + "BCA : 12345678910\n";
              message = message + "MANDIRI : 12345678910\n";
              message = message + "BNI : 12345678910\n\n";
              message = message + "Terima Kasih";
              // message = str_replace(',', ' dan ', $message);

              $("#notif_register_id").val(i);
              $("#notif_register_wa").val(noWa);
              $("#notif_register_kirim").text(lastSent);
              $("#notif_register_message").text(message);
              $('#modal-notifikasi-pendaftaran').modal('show');
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

    
    function showDataRegistrasi(i) {
        let formData = {
            "pendaftar_id":i,
            "_token": "{{ csrf_token() }}"
        };
         
        $.ajax({
            type: "GET",
            url: "{{route('training-gada.dataRegistrasi')}}",
            data:formData,
            success: function(response){
                console.log(response);
                $('#modal-data-registrasi').modal('show');  
                
                $("#register_date").val(response.data.register_date);
                $("#polda_provinsi").val(response.data.polda_provinsi);
                $("#polres_kabupaten").val(response.data.polres_kabupaten);
                $("#nama_lengkap").val(response.data.nama_lengkap);
                $("#tempat_lahir").val(response.data.tempat_lahir);
                $("#tanggal_lahir").val(response.data.tanggal_lahir);
                $("#alamat_rumah").val(response.data.alamat_rumah);
                $("#tinggi_badan").val(response.data.tinggi_badan);
                $("#berat_badan").val(response.data.berat_badan);
                $("#golongan_darah").val(response.data.golongan_darah);
                $("#nomor_ktp").val(response.data.nomor_ktp);
                $("#sidik_jari_1").val(response.data.sidik_jari_1);
                $("#sidik_jari_2").val(response.data.sidik_jari_2);
                $("#nama_istri").val(response.data.nama_istri);
                $("#jumlah_anak").val(response.data.jumlah_anak);
                $("#nama_bapak").val(response.data.nama_bapak);
                $("#nama_ibu").val(response.data.nama_ibu);
                $("#nama_sd").val(response.data.nama_sd);
                $("#sd_lulus").val(response.data.sd_lulus);
                $("#nama_smp").val(response.data.nama_smp);
                $("#smp_lulus").val(response.data.smp_lulus);
                $("#nama_sma").val(response.data.nama_sma);
                $("#sma_lulus").val(response.data.sma_lulus);
                $("#nama_perguruan_tinggi").val(response.data.nama_perguruan_tinggi);
                $("#perguruan_tinggi_lulus").val(response.data.perguruan_tinggi_lulus);
                $("#lokasi_penugasan").val(response.data.lokasi_penugasan);
                $("#nama_lokasi_penugasan").val(response.data.nama_lokasi_penugasan);
                $("#alamat_lokasi_penugasan").val(response.data.alamat_lokasi_penugasan);
                $("#kota_lokasi_penugasan").val(response.data.kota_lokasi_penugasan);
                $("#asal_perusahaan").val(response.data.asal_perusahaan);
                $("#status_perusahaan").val(response.data.status_perusahaan);
                $("#agama").val(response.data.agama);
                $("#nomor_whatsapp").val(response.data.nomor_whatsapp);
                $("#pendidikan_terakhir").val(response.data.pendidikan_terakhir);
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

    function sendMessage(typeNotif) {
      $('#modal-notifikasi-pendaftaran').modal('hide');
      let formData = {
            "id" : typeNotif == 'registrasi' ? $('#notif_register_id').val() : '',
            "no_wa" : typeNotif == 'registrasi' ? $('#notif_register_wa').val() : '',
            "message" : typeNotif == 'registrasi' ? $('#notif_register_message').val() : '',
            "_token": "{{ csrf_token() }}"
        };
                    
        $.ajax({
            type: "POST",
            url: "{{route('whatsapp.sendMessage')}}",
            data:formData,
            success: function(response){
                console.log(response)
                if (response.success) {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'success'
                    });
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
    };

    function showlistLog(i) {
        $('#modal-status-log').modal('show'); 
        let formData = {
            "pendaftar_id":i,
            "_token": "{{ csrf_token() }}"
        };
        
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
                width: "15%",
                className:'text-left'
            },{
                data : 'nik',
                name : 'nik',
                width: "10%",
                className:'text-left'
            },{
                data : 'email',
                name : 'email',
                width: "10%",
                className:'text-left'
            },{
                data : 'no_wa',
                name : 'no_wa',
                width: "10%",
                className:'text-left'
            },{
                data : 'jenis_pelatihan',
                name : 'jenis_pelatihan',
                width: "10%",
                className:'text-left'
            },{
                data : 'alamat',
                name : 'alamat',
                width: "10%",
                className:'text-left'
            },{
                data : 'register_date',
                name : 'register_date',
                width: "10%",
                className:'text-left'
            },{
                data : 'status_name',
                name : 'status',
                width: "10%",
                className:'text-left'
            },{
                data : 'status_bayar',
                name : 'status_bayar',
                width: "10%",
                className:'text-left'
            },{
                data : 'aksi',
                name : 'aksi',
                width: "5%",
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
                  <option value="1">New Register</option>
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

<div class="modal fade" id="modal-notifikasi-pendaftaran" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Kirim Notifikasi Whatsapp : <p id="nama"></p></h4>
        </div>
        <input hidden type="text" class="form-control" id="notif_register_id" name="notif_register_id"/>  
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">No Whatsapp </label>
            <div class="col-sm-9">
              <div class="position-relative">
                <input type="text" class="form-control" id="notif_register_wa" name="notif_register_wa"/>  
              </div>
            </div>
        </div>  
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">Isi Pesan</label>
            <div class="col-sm-9">
              <div class="position-relative">
                <textarea rows="10" class="form-control" name="notif_register_message" id="notif_register_message" placeholder="Pesan Whatsapp"></textarea>
              </div>
            </div>
        </div>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end"><span class="text-danger">Terakhir Kirim</span> </label>
            <div class="col-sm-9">
              <div class="position-relative">
                <label id ="notif_register_kirim" name ="notif_register_kirim" class="col-sm-9 col-form-label"></label>
              </div>
            </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button onclick="sendMessage('registrasi')" class="btn btn-primary">Kirim</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-notifikasi-payment" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Kirim Notifikasi Whatsapp : <p id="nama"></p></h4>
        </div>
        <input hidden type="text" class="form-control" id="notif_payment_id" name="notif_payment_id"/>  
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">No Whatsapp </label>
            <div class="col-sm-9">
              <div class="position-relative">
                <input type="text" class="form-control" id="notif_payment_wa" name="notif_payment_wa"/>  
              </div>
            </div>
        </div>  
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end">Isi Pesan</label>
            <div class="col-sm-9">
              <div class="position-relative">
                <textarea rows="10" class="form-control" name="notif_payment_message" id="notif_payment_message" placeholder="Pesan Whatsapp"></textarea>
              </div>
            </div>
        </div>
        <div class="row mb-3">    
            <label class="col-sm-3 col-form-label text-sm-end"><span class="text-danger">Terakhir Kirim</span> </label>
            <div class="col-sm-9">
              <div class="position-relative">
                <label id ="notif_payment_kirim" name ="notif_payment_kirim" class="col-sm-9 col-form-label"></label>
              </div>
            </div>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button onclick="sendMessage('payment')" class="btn btn-primary">Kirim</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-data-registrasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Data Registrasi : <p id="nama"></p></h4>
        </div>
        <!-- <input hidden type="text" class="form-control" id="register_id" name="register_id"/>   -->
         <form class="card-body overflow-hidden" action="{{route('sdt-training.save')}}" method="POST">
              @csrf
              <!-- <h6>1. Informasi Perusahaan</h6> -->
            
              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Register Date</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="register_date" name="register_date" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Polda Provinsi</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="polda_provinsi" name="polda_provinsi" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Polres Kabupaten</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="polres_kabupaten" name="polres_kabupaten" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Nama Lengkap</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_lengkap" name="nama_lengkap" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Tempat Lahir</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="tempat_lahir" name="tempat_lahir" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tanggal Lahir</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="tanggal_lahir" name="tanggal_lahir" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Alamat Rumah</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="alamat_rumah" name="alamat_rumah" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tinggi Badan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="tinggi_badan" name="tinggi_badan" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Berat Badan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="berat_badan" name="berat_badan" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Golongan Darah</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="golongan_darah" name="golongan_darah" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nomor KTP</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nomor_ktp" name="nomor_ktp" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Jumlah Anak</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="jumlah_anak" name="jumlah_anak" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Sidik Jari 1</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="sidik_jari_1" name="sidik_jari_1" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Sidik Jari 2</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="sidik_jari_2" name="sidik_jari_2" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nama Istri</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_istri" name="nama_istri" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Nomor Whatsapp</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nomor_whatsapp" name="nomor_whatsapp" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nama Bapak</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_bapak" name="nama_bapak" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Nama Ibu</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_ibu" name="nama_ibu" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nama SD</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_sd" name="nama_sd" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tahun Lulus SD</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="sd_lulus" name="sd_lulus" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nama SMP</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_smp" name="nama_smp" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tahun Lulus SMP</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="smp_lulus" name="smp_lulus" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Nama SMA</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_sma" name="nama_sma" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tahun Lulus SMA</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="sma_lulus" name="sma_lulus" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Perguruan Tinggi</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_perguruan_tinggi" name="nama_perguruan_tinggi" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Tahun Lulus P.Tinggi</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="perguruan_tinggi_lulus" name="perguruan_tinggi_lulus" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Lokasi Penugasan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="lokasi_penugasan" name="lokasi_penugasan" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Nama Lok. Tugas</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="nama_lokasi_penugasan" name="nama_lokasi_penugasan" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Alamat Lok. Tugas</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="alamat_lokasi_penugasan" name="alamat_lokasi_penugasan" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Kota Lok.Penugasan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="kota_lokasi_penugasan" name="kota_lokasi_penugasan" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Asal Perusahaan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="asal_perusahaan" name="asal_perusahaan" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Status Perusahaan</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="status_perusahaan" name="status_perusahaan" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              <div class="row mb-3">                
                <label class="col-sm-2 col-form-label text-sm-end">Agama</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="agama" name="agama" value="" class="form-control"></input>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-sm-end">Pendidikan Terkahir</label>
                <div class="col-sm-4">
                  <div class="position-relative">
                    <input readonly type="text" id="pendidikan_terakhir" name="pendidikan_terakhir" value="" class="form-control"></input>
                  </div>
                </div>
              </div>

              
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
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