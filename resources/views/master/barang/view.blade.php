@extends('layouts.master')
@section('title', 'Barang')
@section('content')
  <!--/ Content -->
  <div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Barang</h4>
    <!-- Multi Column with Form Separator -->
    <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
      <h5 class="card-header">
        <div class="d-flex justify-content-between">
        <span>Form Barang</span>
        </div>
      </h5>
      <form class="card-body overflow-hidden" action="{{route('barang.save')}}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{$data->id}}">
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <input type="text" id="nama" name="nama" value="{{$data->nama}}"
          class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
          @if($errors->has('nama'))
        <div class="invalid-feedback">{{$errors->first('nama')}}</div>
      @endif
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Jenis Barang <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="jenis_barang_id" name="jenis_barang_id"
            class="form-select @if ($errors->any()) @if($errors->has('jenis_barang_id')) is-invalid @else   @endif @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih Jenis Barang -</option>
            @foreach($listJenisBarang as $value)
        <option value="{{$value->id}}" @if($data->jenis_barang_id == $value->id) selected @endif>
        {{$value->nama}}</option>
        @endforeach
          </select>
          @if($errors->has('jenis_barang_id'))
        <div class="invalid-feedback">{{$errors->first('jenis_barang_id')}}</div>
      @endif
          </div>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Harga <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <input type="text" id="harga" name="harga" value="{{$data->harga}}"
          class="form-control @if ($errors->any()) @if($errors->has('harga')) is-invalid @else   @endif @endif">
          @if($errors->has('harga'))
        <div class="invalid-feedback">{{$errors->first('harga')}}</div>
      @endif
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Satuan</label>
        <div class="col-sm-4">
          <input type="text" id="satuan" name="satuan" value="{{$data->satuan}}"
          class="form-control @if ($errors->any()) @if($errors->has('satuan')) is-invalid @else   @endif @endif">
          @if($errors->has('satuan'))
        <div class="invalid-feedback">{{$errors->first('satuan')}}</div>
      @endif
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Masa Pakai</label>
        <div class="col-sm-4">
          <input type="number" id="masa_pakai" name="masa_pakai" value="{{$data->masa_pakai}}"
          class="form-control @if ($errors->any()) @if($errors->has('masa_pakai')) is-invalid @else   @endif @endif">
          @if($errors->has('masa_pakai'))
        <div class="invalid-feedback">{{$errors->first('masa_pakai')}}</div>
      @endif
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Merk</label>
        <div class="col-sm-4">
          <input type="text" id="merk" name="merk" value="{{$data->merk}}"
          class="form-control @if ($errors->any()) @if($errors->has('merk')) is-invalid @else   @endif @endif">
          @if($errors->has('merk'))
        <div class="invalid-feedback">{{$errors->first('merk')}}</div>
      @endif
        </div>
        </div>
        <div class="row mb-3">
        <!-- <label class="col-sm-2 col-form-label text-sm-end">Jumlah Default</label>
        <div class="col-sm-4">
          <input type="jumlah_default" id="jumlah_default" name="jumlah_default" value="{{$data->jumlah_default}}" class="form-control @if ($errors->any()) @if($errors->has('jumlah_default')) is-invalid @else   @endif @endif">
          @if($errors->has('jumlah_default'))
            <div class="invalid-feedback">{{$errors->first('jumlah_default')}}</div>
          @endif
        </div> -->
        <label class="col-sm-2 col-form-label text-sm-end">Urutan</label>
        <div class="col-sm-4">
          <input type="urutan" id="urutan" name="urutan" value="{{$data->urutan}}"
          class="form-control @if ($errors->any()) @if($errors->has('urutan')) is-invalid @else   @endif @endif">
          @if($errors->has('urutan'))
        <div class="invalid-feedback">{{$errors->first('urutan')}}</div>
      @endif
        </div>
        </div>
        <div class="pt-4">
        </div>
      </form>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Action</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true"
          aria-expanded="false">
          <i class="mdi mdi-dots-vertical mdi-24px"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
          </div>
        </div>
        </div>
        <div class="card-body">
        <div class="col-12 text-center">
          <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
          <span class="me-1">Update Barang</span>
          <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
          </button>
        </div>
        <div class="col-12 text-center mt-2">
          <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
          <span class="me-1">Kembali</span>
          <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
          </button>
        </div>
        </div>
      </div>
      </div>
    </div>
    <div class="col-md-8 mt-4">
      <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Form Default Quantity</h5>
        <button class="btn btn-success btn-sm" id="btn-add-default-qty">
        <i class="mdi mdi-plus"></i> Tambah
        </button>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="table-default-qty">
        <thead>
          <tr>
          <th>Layanan</th>
          <th>Qty Default</th>
          <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data will be loaded by DataTables -->
        </tbody>
        </table>
      </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal-default-qty" tabindex="-1" aria-labelledby="modalDefaultQtyLabel"
      aria-hidden="true">
      <div class="modal-dialog">
      <form id="form-default-qty">
        <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDefaultQtyLabel">Tambah Default Quantity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
          <label for="layanan_id" class="form-label">Layanan <span class="text-danger">*</span></label>
          <select class="form-select" id="layanan_id" name="layanan_id" required>
            <option value="">- Pilih Layanan -</option>
            @foreach($listLayanan as $layanan)
        <option value="{{$layanan->id}}">{{$layanan->nama}}</option>
        @endforeach
          </select>
          </div>
          <div class="mb-3">
          <label for="qty_default" class="form-label">Qty Default <span class="text-danger">*</span></label>
          <input type="number" class="form-control" id="qty_default" name="qty_default" min="1" required>
          </div>
          <input type="hidden" name="barang_id" value="{{$data->id}}">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
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

    $('#btn-update').on('click', function () {
    $('form').submit();
    });
    $('#btn-kembali').on('click', function () {
    history.back();
    // window.location.replace("{{route('barang')}}");
    });
  </script>

  <script>
    var elem = document.getElementById("harga");
    let harga = elem.value;
    var caret = harga.length - 1;
    while ((caret - 3) > -1) {
    caret -= 3;
    harga = harga.split('');
    harga.splice(caret + 1, 0, ",");
    harga = harga.join('');
    }
    this.harga = harga;
    $("#harga").val(harga);

    elem.addEventListener("keydown", function (event) {
    var key = event.which;
    if ((key < 48 || key > 57) && key != 8) event.preventDefault();
    });

    elem.addEventListener("keyup", function (event) {
    var value = this.value.replace(/,/g, "");
    this.dataset.currentValue = parseInt(value);
    var caret = value.length - 1;
    while ((caret - 3) > -1) {
      caret -= 3;
      value = value.split('');
      value.splice(caret + 1, 0, ",");
      value = value.join('');
    }
    this.value = value;
    });
  </script>

  <script>
    $(function () {
    var tableDefaultQty = $('#table-default-qty').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
      url: "{{ route('barang.defaultqty.data', $data->id) }}",
      type: "GET"
      },
      columns: [
      { data: 'layanan', name: 'layanan', className: 'text-center' },
      { data: 'qty_default', name: 'qty_default', className: 'text-center' },
      { data: 'aksi', name: 'aksi', className: 'text-center' }
      ]
    });

    $('#btn-add-default-qty').on('click', function () {
      $('#form-default-qty')[0].reset();
      $('#modal-default-qty').modal('show');
    });

    $('#form-default-qty').on('submit', function (e) {
      e.preventDefault();
      let layanan = $('#layanan_id').val();
      let qty = $('#qty_default').val();
      if (!layanan || !qty) {
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: 'Layanan dan Qty Default harus diisi!',
        customClass: { confirmButton: 'btn btn-primary' },
        buttonsStyling: false
      });
      return;
      }
      $('#modal-default-qty').modal('hide');
      $.ajax({
      url: "{{ route('barang.defaultqty.save') }}",
      method: "POST",
      data: $(this).serialize(),
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      success: function (res) {
        Swal.fire({
        icon: res.success ? 'success' : 'error',
        title: res.success ? 'Berhasil' : 'Gagal',
        text: res.message,
        customClass: { confirmButton: 'btn btn-primary' },
        buttonsStyling: false
        });
        if (res.success) {
        $('#modal-default-qty').modal('hide');
        tableDefaultQty.ajax.reload();
        }
      },
      error: function (xhr) {
        Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Terjadi kesalahan saat menyimpan data.',
        customClass: { confirmButton: 'btn btn-primary' },
        buttonsStyling: false
        });
      }
      });
    });
    });

    $('#table-default-qty').on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Hapus Data',
      text: 'Apakah Anda yakin ingin menghapus data ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
      customClass: {
      confirmButton: 'btn btn-danger me-2',
      cancelButton: 'btn btn-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
      $.ajax({
        url: "{{ route('barang.defaultqty.delete') }}",
        method: "POST",
        data: { id: id, _token: '{{ csrf_token() }}' },
        success: function (res) {
        Swal.fire({
          icon: res.success ? 'success' : 'error',
          title: res.success ? 'Berhasil' : 'Gagal',
          text: res.message,
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
        if (res.success) {
          $('#table-default-qty').DataTable().ajax.reload();
        }
        },
        error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menghapus data.',
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        });
        }
      });
      }
    });
    });
  </script>

@endsection