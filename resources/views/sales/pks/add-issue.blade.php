@extends('layouts.master')
@section('title','Issue Kontrak')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales / Kontrak / </span> Issue Kontrak Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Issue Kontrak</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('monitoring-kontrak.save-issue')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$pks->id}}" />
            <h6>1. Informasi Kontrak</h6>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Nomor Kontrak</label>
                <div class="col-sm-4">
                <input type="text" id="nomor_kontrak" name="nomor_kontrak" value="{{$pks->nomor}}" class="form-control readonly">
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Tanggal Issue <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                <input type="date" id="tgl" name="tgl" value="{{$nowd}}" class="form-control">
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
            <hr class="my-4 mx-4">
            <h6>2. Form Issue</h6>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Judul <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="text" id="judul" name="judul" value="" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Jenis Keluhan <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" id="jenis_keluhan" name="jenis_keluhan" value="" class="form-control">
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Status <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="text" id="status" name="status" value="" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Kolaborator</label>
                <div class="col-sm-10">
                    <input type="text" id="kolaborator" name="kolaborator" value="" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Deskripsi <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <div class="form-floating form-floating-outline mb-4">
                        <textarea class="form-control h-px-100" name="deskripsi" id="deskripsi" placeholder=""></textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Lampiran</label>
                <div class="col-sm-10">
                    <input type="file" id="lampiran" name="lampiran" class="form-control">
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
<!--/ Content -->
@endsection

@section('pageScript')

<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let obj = $("form").serializeObject();

    let errors = [];

    if ($('#judul').val() === '') {
      errors.push('<b>Judul</b> wajib diisi');
    }
    if ($('#jenis_keluhan').val() === '') {
      errors.push('<b>Jenis Keluhan</b> wajib diisi');
    }
    if ($('#status').val() === '') {
      errors.push('<b>Status</b> wajib diisi');
    }
    if ($('#deskripsi').val() === '') {
      errors.push('<b>Deskripsi</b> wajib diisi');
    }
    if ($('#lampiran').val() === '') {
      errors.push('<b>Lampiran</b> wajib diisi');
    } else {
      let file = $('#lampiran')[0].files[0];
      let fileSize = file.size / 1024 / 1024; // Convert to MB
      if (fileSize > 2) {
        errors.push('Ukuran file lampiran tidak boleh lebih dari 2MB');
      }
    }
    if ($('#tgl').val() === '') {
      errors.push('<b>Tanggal Issue</b> wajib diisi');
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

    var formData = new FormData(form[0]);
    formData.append('_token', '{{ csrf_token() }}');
    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log(response);

        if (response.status === 'success') {
          Swal.fire({
            title: 'Berhasil',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            sessionStorage.setItem('forceRefresh', 'true');
            window.history.go(-1);
            return false;
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
@endsection
