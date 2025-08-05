@extends('layouts.master')
@section('title','Customer Activity')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Customer Activity Pilih RO</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Pilih RO</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save-activity-ro-kontrak')}}" method="POST" enctype="multipart/form-data">
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
                        <input class="form-check-input tipe" type="radio" name="tipe" id="pilih-ro" value="Pilih RO" checked>
                        <label class="form-check-label" for="pilih-ro">
                        Pilih RO
                        </label>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="d-ro">
                        <div class="row">
                        <label class="col-sm-2 col-form-label text-sm-end">Supervisor <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select id="spv_ro" name="spv_ro" class="form-select" data-allow-clear="true" tabindex="-1">
                            <option value="">- Pilih data -</option>
                            @foreach($spvRoList as $value)
                            <option value="{{$value->id}}">{{$value->full_name}}</option>
                            @endforeach
                            </select>
                        </div>
                        </div>
                        <div class="row mt-3">
                        <label class="col-sm-2 col-form-label text-sm-end">RO <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="row">
                            <div class="col-md-8">
                            <select id="ro" name="ro" class="form-select" data-allow-clear="true" tabindex="-1">
                                <option value="">- Pilih data -</option>
                                @foreach($roList as $value)
                                <option value="{{$value->id}}">{{$value->full_name}}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="addButton" class="btn btn-primary w-100">Tambah</button>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="row mt-3">
                        <table class="table table-bordered" style="">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama RO</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody id="itemTable">
                            <!-- Data akan ditambahkan di sini -->
                            </tbody>
                        </table>
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
  // Inisialisasi elemen
  const itemSelect = document.getElementById('ro');
  const addButton = document.getElementById('addButton');
  const itemTable = document.getElementById('itemTable');
  let itemCount = 0;

  // Event untuk menambahkan item ke tabel
  addButton.addEventListener('click', () => {
    const selectedValue = itemSelect.value;
    const selectedText = itemSelect.options[itemSelect.selectedIndex].text;

    if (selectedValue) {
      // Cek apakah item sudah ada di tabel
      const existingInputs = Array.from(itemTable.querySelectorAll('input[name="selected_ro[]"]'));
      const isDuplicate = existingInputs.some(input => input.value === selectedValue);

      if (isDuplicate) {
        Swal.fire({
          title: "Pemberitahuan",
          html: 'RO sudah ditambahkan!',
          icon: "warning"
        });
        return;
      }

      itemCount++;

      if(itemCount > 3 ){
        Swal.fire({
          title: "Pemberitahuan",
          html: 'RO tidak bisa lebih dari 3',
          icon: "warning"
        });
        return;
      };

      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${itemCount}</td>
        <td> <input type="hidden" name="selected_ro[]" value="${selectedValue}"> ${selectedText}</td>
        <td><button class="btn btn-danger btn-sm delete-button">Hapus</button></td>
      `;
      itemTable.appendChild(row);

      // Event untuk tombol hapus
      row.querySelector('.delete-button').addEventListener('click', () => {
        row.remove();
        itemCount--;
        updateTableIndices();
      });
    } else {
      Swal.fire({
          title: "Pemberitahuan",
          html: 'Silakan pilih Nama RO terlebih dahulu.',
          icon: "warning"
        });
    }
  });

  // Fungsi untuk memperbarui nomor indeks tabel
  function updateTableIndices() {
    let index = 1;
    itemTable.querySelectorAll('tr').forEach(row => {
      row.querySelector('td:first-child').innerText = index++;
    });
  }
</script>

<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let obj = $("form").serializeObject();

    let errors = [];

    if($('#pilih-ro').is(':checked')) {
      if($('#spv_ro').val()==""||$('#spv_ro').val()==null){
        errors.push('Belum memilih SPV');
      }else{
        const rowCount = itemTable.querySelectorAll('tr').length;
        if (rowCount==null || rowCount == 0) {
            errors.push('Belum memilih RO');
        }
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
@endsection
