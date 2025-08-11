@extends('layouts.master')
@section('title', 'supplier')
@section('content')
  <!--/ Content -->
  <div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat supplier</h4>
    <!-- Multi Column with Form Separator -->
    <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
      <h5 class="card-header">
        <div class="d-flex justify-content-between">
        <span>Form supplier</span>
        </div>
      </h5>
      <form class="form-supplier" action="{{ route('supplier.save') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Nama Supplier <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="nama_supplier" value="{{ $data->nama_supplier ?? '' }}">
          @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>

        <label class="col-sm-2 col-form-label text-sm-end">PIC</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="pic" value="{{ $data->pic ?? '' }}">
          @error('pic')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>
        </div>

        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Alamat Lengkap</label>
        <div class="col-sm-4">
          <textarea class="form-control" name="alamat">{{ $data->alamat ?? '' }}</textarea>
          @error('alamat')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>

        <label class="col-sm-2 col-form-label text-sm-end">No. Telp / Email</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="kontak" value="{{ $data->kontak ?? '' }}">
          @error('kontak')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>
        </div>

        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">NPWP</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="npwp" value="{{ $data->npwp ?? '' }}">
          @error('npwp')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>

        <label class="col-sm-2 col-form-label text-sm-end">Kategori Barang</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="kategori_barang" value="{{ $data->kategori_barang ?? '' }}">
          @error('kategori_barang')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>
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
         <button type="submit" id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
          <span class="me-1">Update supplier</span>
          <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
          </button>
        </div>
        <!-- <div class="col-12 text-center mt-2">
      <button id="btn-print" class="btn btn-primary w-100 waves-effect waves-light">
      <span class="me-1">Cetak Good Receipt</span>
      <i class="mdi mdi-printer"></i>
      </button>
      </div> -->
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

    <!-- Modal -->
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
    $('#form-supplier').submit();
    });
    //     $('#btn-print').on('click', function() {
    // const url = "{/{ route('supplier.print-gr', $data->id) }}";

    //     // Buka di tab baru
    //     window.open(url, '_blank');

    //     // Atau langsung download
    //     // window.location.href = url;
    // });
    $('#btn-kembali').on('click', function () {
    history.back();
    // window.location.replace("{{route('supplier')}}");
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


@endsection