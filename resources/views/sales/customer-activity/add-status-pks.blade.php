@extends('layouts.master')
@section('title','Customer Activity')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Customer Activity Update Status</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Update Status</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save-activity-status-kontrak')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$pks->id}}" />
            <h6>1. Informasi Kontrak</h6>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Nomor Kontrak</label>
                <div class="col-sm-4">
                <input type="text" id="nomor_kontrak" name="nomor_kontrak" value="{{$pks->nomor}}" class="form-control readonly">
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
                        <input class="form-check-input tipe" type="radio" name="tipe" id="update-status" value="Update Status" checked>
                        <label class="form-check-label" for="update-status">
                        Update Status
                        </label>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="d-ro">
                        <div class="row">
                            <label class="col-sm-2 col-form-label text-sm-end">Status PKS <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select id="status_pks" name="status_pks" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="">- Pilih data -</option>
                                @foreach($listStatus as $value)
                                <option value="{{$value->id}}">{{$value->nama}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
@endsection

@section('pageScript')

<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let obj = $("form").serializeObject();

    let errors = [];

    if($('#status_pks').val()==""||$('#status_pks').val()==null){
        errors.push('Belum memilih Status PKS');
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
@endsection
