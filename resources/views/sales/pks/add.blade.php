@extends('layouts.master')
@section('title','PKS')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form PKS Baru</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('pks.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">PKS</h4>
              <h4>SPK Untuk Dijadikan PKS</h4>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">SPK <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="hidden" id="spk_id" name="spk_id" value="@if($spk !=null) {{$spk->id}} @endif" class="form-control">
                    <div class="input-group">
                    <input type="text" id="spk" name="spk" value="@if($spk !=null) {{$spk->nomor}} @endif" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end" for="kategoriHC">Kategori Sesuai HC <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select id="kategoriHC" name="kategoriHC" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriHC as $kategori)
                            <option value="{{$kategori->id}}" {{ old('kategoriHC') == $kategori->id ? 'selected' : '' }}>{{$kategori->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_pks">Tanggal PKS <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" id="tanggal_pks" name="tanggal_pks" class="form-control" value="{{ old('tanggal_pks', date('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
              <div class="col-sm-4">
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="@if($spk !=null) {{$spk->nama_perusahaan}} @endif" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
              <div class="col-sm-4">
                <input type="text" id="kebutuhan" name="kebutuhan" value="@if($spk !=null) {{$spk->kebutuhan}} @endif" class="form-control" readonly>
              </div>
            </div>
            <div class="content-header mb-3 text-center">
              <h4>List Site</h4>
            </div>
            <div id="d-table-quotation" class="row mb-3">
              <div class="table-responsive overflow-hidden table-quotation">
                <table id="table-quotation" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="check-all-sites" class="form-check-input" style="transform: scale(1.5); margin-right: 8px;" />
                        </th>
                      <th>No.</th>
                      <th>Nama Site</th>
                      <th>Provinsi</th>
                      <th>Kota</th>
                      <th>Penempatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($siteList as $key => $site)
                      <tr>
                        <td>
                            <input type="checkbox" name="site_ids[]" value="{{$site->id}}" class="form-check-input site-checkbox" style="transform: scale(1.5); margin-right: 8px;" />
                        </td>
                        <td>{{$key+1}}</td>
                        <td>{{$site->nama_site}}</td>
                        <td>{{$site->provinsi}}</td>
                        <td>{{$site->kota}}</td>
                        <td>{{$site->penempatan}}</td>
                      </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat PKS</span>
                  <i class="mdi mdi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
    $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
        return false;
      }
    });

  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();

    if(obj.spk_id == null || obj.spk_id == "" ){
      msg += "<b>Spk</b> belum dipilih </br>";
    };

    if(obj.tanggal_pks == null || obj.tanggal_pks == ""){
      msg += "<b>Tanggal PKS</b> tidak boleh kosong </br>";
    }
    if(obj.kategoriHC == null || obj.kategoriHC == ""){
      msg += "<b>Kategori Sesuai HC</b> belum dipilih </br>";
    }
    if (!obj['site_ids[]']) {
        msg += "Silakan pilih minimal satu site untuk membuat SPK.<br>";
    }



    if(msg == ""){
      form.submit();
    }else{
      Swal.fire({
        title: "Pemberitahuan",
        html: msg,
        icon: "warning"
      });
    }
  });

</script>
@endsection
