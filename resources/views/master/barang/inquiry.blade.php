@extends('layouts.master')
@section('title','Import Barang')

@section('pageStyle')
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}" />
@endsection

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Inquiry Barang</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Import Barang</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <!-- Multi  -->
        <div class="card-body">
            <div class="pb-4">
                <div class="row justify-content-end">
                <div class="col-sm-12 d-flex justify-content-center">
                    <button id="btn-save" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan Data</button>
                    <a href="{{route('barang.import')}}" class="btn btn-secondary waves-effect">Kembali</a>
                </div>
                </div>
            </div>
            <div class="row">
              <div class="col-sm-6 offset-lg-1 col-lg-3 mb-2">
                <div class="card card-border-shadow-success h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                      <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-success"><i class="mdi mdi-check-bold mdi-20px"></i></span>
                      </div>
                      <h4 class="ms-1 mb-0 display-4">{{$jumlahSuccess}}</h4>
                    </div>
                    <p class="mb-0 text-heading">Data Berhasil di validasi</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3 mb-2">
                <div class="card card-border-shadow-warning h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                      <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i class="mdi mdi-alert-box-outline mdi-20px"></i></span>
                      </div>
                      <h4 class="ms-1 mb-0 display-4">{{$jumlahWarning}}</h4>
                    </div>
                    <p class="mb-0 text-heading">Data kurang lengkap</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3 mb-2">
                <div class="card card-border-shadow-danger h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                      <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-danger"><i class="mdi mdi-alert-box-outline mdi-20px"></i></span>
                      </div>
                      <h4 class="ms-1 mb-0 display-4">{{$jumlahError}}</h4>
                    </div>
                    <p class="mb-0 text-heading">Data tidak bisa diimport</p>
                  </div>
                </div>
              </div>
            </div>
            <form enctype="multipart/form-data" id="upload-form" style="opacity:1 !important" action="{{route('barang.save-import')}}" method="POST">
                @csrf
                <div class="table-responsive overflow-hidden tabel-import">
                    <input type="hidden" name="importId" value="{{$importId}}">
                  <table id="tabel-import" class="dt-column-search table w-100 table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Import ID</th>
                        <th>Barang ID</th>
                        <th>Nama Barang</th>
                        <th>Jenis Barang ID</th>
                        <th>Jenis Barang</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th>Masa Pakai</th>
                        <th>Merk</th>
                        <th>Jumlah Default</th>
                        <th>Urutan</th>
                        <th>Created At</th>
                        <th>Created By</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($datas as $data)
                      <tr>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->import_id }}</td>
                        <td>{{ $data->barang_id }}</td>
                        <td>{{ $data->nama }}</td>
                        <td>{{ $data->jenis_barang_id }}</td>
                        <td>{{ $data->jenis_barang }}</td>
                        <td>{{ $data->harga }}</td>
                        <td>{{ $data->satuan }}</td>
                        <td>{{ $data->masa_pakai }}</td>
                        <td>{{ $data->merk }}</td>
                        <td>{{ $data->jumlah_default }}</td>
                        <td>{{ $data->urutan }}</td>
                        <td>{{ $data->created_at }}</td>
                        <td>{{ $data->created_by }}</td>
                      </tr>
                    @endforeach
                    </tbody>
                  </table>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<!-- Vendors JS -->
<script>
    $('#btn-save').on('click',function(){
        $('form').submit();
    });

    $('#tabel-import').DataTable({
      scrollX: true,
      "paging": false,
      'processing': true
    });
</script>

@endsection
