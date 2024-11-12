@extends('layouts.master')
@section('title','Import Leads')

@section('pageStyle')
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
@endsection

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Inquiry Leads</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Import Leads</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <!-- Multi  -->
        <div class="card-body">
            <div class="pb-4">
                <div class="row justify-content-end">
                <div class="col-sm-12 d-flex justify-content-center">
                    <button id="btn-save" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan Data</button>
                    <a href="{{route('leads.import')}}" class="btn btn-secondary waves-effect">Kembali</a>
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
            <form enctype="multipart/form-data" id="upload-form" style="opacity:1 !important" action="{{route('leads.save-import')}}" method="POST">
                @csrf
                <div class="table-responsive overflow-hidden tabel-import">
                  <table id="tabel-import" class="dt-column-search table w-100 table-hover">
                    <thead>
                      <tr>
                        <th>Status Data</th>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nama Perusahaan</th>
                        <th>Jenis Perusahaan</th>
                        <th>No Telp Perusahaan</th>
                        <th>PIC</th>
                        <th>Jabatan PIC</th>
                        <th>No. Telp PIC</th>
                        <th>Email PIC</th>
                        <th>Kebutuhan</th>
                        <th>Wilayah</th>
                        <th>Sumber Leads</th>
                        <th>Alamat</th>
                        <th>Keterangan</th>
                        <th>Username Sales</th>
                        <th class="d-none">status</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($datas[0] as $data)
                      <tr>
                        <td>{{$data[15]}}</td>
                        <input type="hidden" name="value[]" value="{{$data[0]}}||{{$data[1]}}||{{$data[2]}}||{{$data[3]}}||{{$data[4]}}||{{$data[5]}}||{{$data[6]}}||{{$data[7]}}||{{$data[8]}}||{{$data[9]}}||{{$data[10]}}||{{$data[11]}}||{{$data[12]}}||{{$data[13]}}||{{$data[14]}}">
                        <td>{{$data[0]}}</td>
                        <td>{{$data[1]}}</td>
                        <td>{{$data[2]}}</td>
                        <td>{{$data[3]}}</td>
                        <td>{{$data[4]}}</td>
                        <td>{{$data[5]}}</td>
                        <td>{{$data[6]}}</td>
                        <td>{{$data[7]}}</td>
                        <td>{{$data[8]}}</td>
                        <td>{{$data[9]}}</td>
                        <td>{{$data[10]}}</td>
                        <td>{{$data[11]}}</td>
                        <td>{{$data[12]}}</td>
                        <td>{{$data[13]}}</td>
                        <td>{{$data[14]}}</td>
                        <td class="d-none">{{$data[16]}}</td>
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
      'processing': true,
      "createdRow": function( row, data, dataIndex){
                  if(data[16]=="2"){
                    $('td', row).css('background-color', "#fff4df");

                  }else if(data[16]=="3"){
                    $('td', row).css('background-color', "#ffe4e4");
                  }
                },  
    });
</script>

@endsection