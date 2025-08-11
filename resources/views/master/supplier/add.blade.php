@extends('layouts.master')
@section('title','Supplier')

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Supplier Baru</h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Tambah Supplier</span>
            <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
          </div>
        </h5>

        <form class="card-body" action="{{ route('supplier.save') }}" method="POST">
          @csrf

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama Supplier <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <label class="col-sm-2 col-form-label text-sm-end">PIC</label>
            <div class="col-sm-4">
              <input type="text" name="pic" value="{{ old('pic') }}" class="form-control @error('pic') is-invalid @enderror">
              @error('pic')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Alamat Lengkap</label>
            <div class="col-sm-4">
              <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
              @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <label class="col-sm-2 col-form-label text-sm-end">No. Telp / Email</label>
            <div class="col-sm-4">
              <input type="text" name="kontak" value="{{ old('kontak') }}" class="form-control @error('kontak') is-invalid @enderror">
              @error('kontak')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">NPWP</label>
            <div class="col-sm-4">
              <input type="text" name="npwp" value="{{ old('npwp') }}" class="form-control @error('npwp') is-invalid @enderror">
              @error('npwp')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <label class="col-sm-2 col-form-label text-sm-end">Kategori Barang</label>
            <div class="col-sm-4">
              <input type="text" name="kategori_barang" value="{{ old('kategori_barang') }}" class="form-control @error('kategori_barang') is-invalid @enderror">
              @error('kategori_barang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <button type="button" class="btn btn-secondary waves-effect" onclick="history.back();">Kembali</button>
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
</script>
<script>
  var elem = document.getElementById("harga");

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
