@extends('layouts.master')
@section('title','Customer Activity Kontrak')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Customer Activity Kontrak Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Customer Activity Kontrak</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save-activity-kontrak')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$pks->id}}" />
            <h6>1. Informasi Kontrak</h6>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Nomor Kontrak</label>
                <div class="col-sm-4">
                <input type="text" id="nomor_kontrak" name="nomor_kontrak" value="{{$pks->nomor}}" class="form-control readonly">
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Tanggal Activity <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                <input type="date" id="tgl_activity" name="tgl_activity" value="{{$nowd}}" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Awal Kontrak</label>
                <div class="col-sm-4">
                    <input type="text" id="awal_kontrak" name="awal_kontrak" value="{{$pks->s_mulai_kontrak}}" class="form-control readonly">
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Akhir Kontrak</label>
                <div class="col-sm-4">
                    <input type="text" id="akhir_kontrak" name="akhir_kontrak" value="{{$pks->s_kontrak_selesai}}" class="form-control readonly">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Leads / customer</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="text" id="leads" name="leads" value="{{$pks->nama_site}}" class="form-control readonly">
                    </div>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Status Kontrak</label>
                <div class="col-sm-4">
                    <input type="text" id="status_kontrak" name="status_kontrak" value="{{$pks->status_kontrak}}" class="form-control readonly">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Notes</label>
                <div class="col-sm-10">
                <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100" name="notes" id="notes" placeholder=""></textarea>
                </div>
                </div>
            </div>
            <hr class="my-4 mx-4">
            <h6>2. Customer Activity</h6>
            <input type="hidden" name="tipe" value="" />
            <div class="row mb-3">
                <div class="offset-sm-2 col-sm-2">
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
                </div>
                <div class="col-sm-8">
                <div class="d-telepon">
                    <div class="row">
                    <label class="col-sm-2 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" id="tgl_realisasi_telepon" name="tgl_realisasi_telepon" value="" class="form-control">
                    </div>
                    </div>
                    <div class="row mt-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Start <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="time" id="start" name="start" onchange="hitungDurasi();" value="" class="form-control">
                    </div>
                    <label class="col-sm-2 col-form-label text-sm-end">End <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                    <input type="time" id="end" name="end" onchange="hitungDurasi();" value="" class="form-control">
                    </div>
                    </div>
                    <div class="row mt-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Durasi</label>
                    <div class="col-sm-4">
                        <input type="text" id="durasi" name="durasi" value="" class="form-control" readonly>
                    </div>
                    </div>
                </div>
                <div class="d-visit">
                    <div class="row l-jenis-visit mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Jenis Visit <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="position-relative">
                        <select id="jenis_visit" name="jenis_visit" class="form-select" data-allow-clear="true" tabindex="-1">
                            <option value="">- Pilih data -</option>
                            @foreach($jenisVisit as $data)
                            <option value="{{$data->id}}">{{$data->nama}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <label class="col-sm-2 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="date" id="tgl_realisasi" name="tgl_realisasi" value="" class="form-control">
                    </div>
                    <label class="col-sm-2 col-form-label text-sm-end d-visit l-jam-realisasi">Jam <span class="text-danger">*</span></label>
                    <div class="col-sm-4 d-visit l-jam-realisasi">
                        <input type="time" id="jam_realisasi"  name="jam_realisasi" value="" class="form-control">
                    </div>
                    </div>
                    <div class="row l-notulen mt-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Notulen / Berita Acara <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="form-floating form-floating-outline">
                        <textarea class="form-control h-px-100" name="notulen" id="notulen" placeholder=""></textarea>
                        </div>
                    </div>
                    </div>
                    <div class="row mt-3 l-email">
                    <label class="col-sm-2 col-form-label text-sm-end">Email Penerima<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" id="email" name="email" value="" class="form-control">
                    </div>
                    </div>
                    <div class="row mt-3 l-penerima">
                    <label class="col-sm-2 col-form-label text-sm-end">Penerima Berkas <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" id="penerima" name="penerima" value="" class="form-control">
                    </div>
                    </div>
                    <div class="row l-jenis-visit mt-3 mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end">Kirim Email</label>
                    <div class="col-sm-10">
                        <div class="position-relative">
                        <button type="button" class="btn btn-primary" id="btn-send-email" onclick="sendEmail()">
                            <span class="tf-icons mdi mdi-email-send-outline me-1"></span> Kirim Email
                        </button>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row mt-3 l-keterangan" style="display:none">
                    <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
                    <div class="col-sm-10">
                    <div class="form-floating form-floating-outline mb-4">
                        <textarea class="form-control h-px-100" name="notes_tipe" id="notes_tipe" placeholder=""></textarea>
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
                    <button id="btn-submit" type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                    <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                    <a href="javascript:void(0)" onclick="window.history.go(-1); return false;" class="btn btn-secondary waves-effect">Kembali</a>
                </div>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<!-- Modal Kirim Email -->
<div class="modal fade" id="modal-kirim-email" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Kirim Email</h3>
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <label for="email-subject" class="form-label">Subject</label>
            <input type="text" id="email-subject" class="form-control" placeholder="Subject">
          </div>
          <div class="col-12 mb-3">
            <label for="email-body" class="form-label">Body</label>
            <textarea id="email-body" class="form-control" rows="5" placeholder="Body"></textarea>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Kirim ke:</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-leads">
              <label class="form-check-label" for="kirim-ke-leads">Leads / Customer (<span id="txt-email-leads">-</span> )</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-sales">
              <label class="form-check-label" for="kirim-ke-sales">Sales (<span id="txt-email-sales">-</span> )</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-branch-manager">
              <label class="form-check-label" for="kirim-ke-branch-manager">Branch Manager (<span id="txt-email-branch-manager">-</span> )</label>
            </div>
          </div>
          <div class="col-12 text-center">
            <button type="button" class="btn btn-primary" id="btn-kirim-email">Kirim</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    $('.d-visit').hide();
    $('.d-telepon').hide();

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

  $('.tipe').click(function() {
    $('.d-telepon').hide();
    $('.d-visit').hide();
    $('.l-penerima').hide();
    $('.l-jenis-visit').hide();
    $('.l-notulen').hide();
    $('.l-keterangan').show();
    $('.l-email').hide();

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
    }else{
        $('.l-keterangan').hide();
      }
  });
</script>

<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let obj = $("form").serializeObject();

    let errors = [];

    if ($('#tgl_activity').val() === '') {
      errors.push('Tanggal Activity wajib diisi');
    }

    if (!$('input[name="tipe"]:checked').val()) {
      errors.push('Tipe wajib dipilih');
    }

    if ($('#telepon').is(':checked')) {
      if ($('#tgl_realisasi_telepon').val() === '' || $('#start').val() === '' || $('#end').val() === '') {
        errors.push('Tanggal, Start, dan End wajib diisi untuk Telepon');
      }
    } else if ($('#online-meeting').is(':checked')) {
      if ($('#tgl_realisasi_telepon').val() === '' || $('#start').val() === '' || $('#end').val() === '') {
        errors.push('Tanggal, Start, dan End wajib diisi untuk Online Meeting');
      }
    } else if ($('#email').is(':checked')) {
      if ($('#tgl_realisasi').val() === '' || $('#email').val() === '') {
        errors.push('Tanggal dan Email Penerima wajib diisi untuk Email');
      } else {
        let email = $('#email').val();
        let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
          errors.push('Format Email Penerima tidak valid');
        }
      }
    } else if ($('#kirim-berkas').is(':checked')) {
      if ($('#tgl_realisasi').val() === '' || $('#jam_realisasi').val() === '' || $('#penerima').val() === '') {
        errors.push('Tanggal, Jam, dan Penerima Berkas wajib diisi untuk Kirim Berkas');
      }
    } else if ($('#visit').is(':checked')) {
      if ($('#jenis_visit').val() === '' || $('#tgl_realisasi').val() === '' || $('#jam_realisasi').val() === '' || $('#notulen').val() === '') {
        errors.push('Jenis Visit, Tanggal, Jam, dan Notulen wajib diisi untuk Visit');
      }
    }

    if (errors.length > 0) {
      Swal.fire({
        title: 'Peringatan',
        html: errors.join('<br>'),
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    Swal.fire({
      title: 'Menyimpan Data',
      text: 'Mohon tunggu...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });

    var formData = form.serialize();
    formData += '&_token={{ csrf_token() }}';

    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: formData,
      success: function(response) {
        console.log(response);

        if (response.status === 'success') {
          Swal.fire({
            title: 'Berhasil',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = "{{ route('monitoring-kontrak') }}";
          });
        } else {
          Swal.fire({
            title: 'Gagal',
            text: response.message,
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    });
  });
</script>

<script>
  function sendEmail() {
    if ($('#leads_id').val()=== '' || $('#jenis_visit').val() === '' || $('#tgl_realisasi').val() === '' || $('#jam_realisasi').val() === '' || $('#notulen').val() === '') {
      Swal.fire({
        title: 'Peringatan',
        text: 'Leads, Jenis Visit, Tanggal Realisasi, Jam Realisasi, dan Notulen harus diisi',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    $('#email-subject').val('Notulensi dan Berita Acara Meeting');
    let bodyEmail = `Berikut kami sampaikan notulensi dan berita acara meeting yang telah dilaksanakan Pada :
    Visit : `+$('#jenis_visit').val()+`
    Tanggal : `+ formatDate($('#tgl_realisasi').val()) +`
    Jam : `+$('#jam_realisasi').val()+`
    Notulen : `+$('#notulen').val()+`
    \nAtas Perhatiannya kami ucapkan Terima kasih.`;
    $('#email-body').val(bodyEmail);

    $('#txt-email-leads').text($('#email_leads').val());
    $('#txt-email-sales').text($('#email_sales').val());
    $('#txt-email-branch-manager').text($('#email_branch_manager').val());
    $('#modal-kirim-email').modal('show');
  }

  function formatDate(date) {
    const [year, month, day] = date.split('-');
    return `${day}-${month}-${year}`;
  }

  $('#btn-kirim-email').on('click', function() {
    $('#modal-kirim-email').modal('hide');

    let subject = $('#email-subject').val();
    let body = $('#email-body').val();
    let recipients = [];
    let msg = "";

    if ($('#kirim-ke-leads').is(':checked')){
      recipients.push('Leads / Customer')
      if ($('#email_leads').val() === '') {
        msg += "Email Leads tidak ditemukan <br>";
      }
    };
    if ($('#kirim-ke-sales').is(':checked')){
      recipients.push('Sales');
      if ($('#email_sales').val() === '') {
        msg += "Email Sales tidak ditemukan <br>";
     }
    };
    if ($('#kirim-ke-branch-manager').is(':checked')){
      recipients.push('Branch Manager');
      if ($('#email_branch_manager').val() === '') {
        msg += "Email Branch Manager tidak ditemukan <br>";
      }
    }

    if (recipients.length === 0) {
      Swal.fire({
        title: 'Peringatan',
        text: 'Minimal ada 1 penerima',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }
    if (msg !== "") {
      Swal.fire({
        title: 'Peringatan',
        html: msg,
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    Swal.fire({
      title: 'Konfirmasi',
      text: `Kirim email ke: ${recipients.join(', ')}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Kirim',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Mengirim Email',
          text: 'Mohon tunggu...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });
        // Logic to send email
        $.ajax({
          url: "{{ route('customer-activity.send-email') }}",
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            subject: subject,
            body: body,
            email_leads: $('#email_leads').val(),
            email_sales: $('#email_sales').val(),
            email_branch_manager: $('#email_branch_manager').val(),
            is_kirim_leads: $('#kirim-ke-leads').is(':checked'),
            is_kirim_sales: $('#kirim-ke-sales').is(':checked'),
            is_kirim_branch_manager: $('#kirim-ke-branch-manager').is(':checked')
          },
          success: function(res) {
            Swal.fire({
              title: 'Berhasil',
              text: 'Email berhasil dikirim',
              icon: 'success',
              confirmButtonText: 'OK'
            });
          },
      });
      }
    });
  });
</script>
@endsection
