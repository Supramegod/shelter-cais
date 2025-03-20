@extends('layouts.master')
@section('title','Import Monitoring Kontrak')

@section('pageStyle')
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}" />
@endsection

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Inquiry Monitoring Kontrak</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Import Monitoring Kontrak</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <!-- Multi  -->
        <div class="card-body">
            <div class="pb-4">
                <div class="row justify-content-end">
                <div class="col-sm-12 d-flex justify-content-center">
                    <button id="btn-save" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan Data</button>
                    <a href="{{route('monitoring-kontrak.import')}}" class="btn btn-secondary waves-effect">Kembali</a>
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
            <form enctype="multipart/form-data" id="upload-form" style="opacity:1 !important" action="{{route('monitoring-kontrak.save-import')}}" method="POST">
                @csrf
                <div class="table-responsive overflow-hidden tabel-import">
                    <input type="hidden" name="importId" value="{{$importId}}">
                  <table id="tabel-import" class="dt-column-search table w-100 table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Import ID</th>
                        <th>Company ID</th>
                        <th>Kode Site</th>
                        <th>Nomor</th>
                        <th>Tgl PKS</th>
                        <th>Nama Site</th>
                        <th>Alamat Site</th>
                        <th>Nama Proyek</th>
                        <th>Layanan ID</th>
                        <th>Layanan</th>
                        <th>Bidang Usaha ID</th>
                        <th>Bidang Usaha</th>
                        <th>Jenis Perusahaan ID</th>
                        <th>Jenis Perusahaan</th>
                        <th>Status PKS ID</th>
                        <th>Provinsi ID</th>
                        <th>Provinsi</th>
                        <th>Kota ID</th>
                        <th>Kota</th>
                        <th>PMA</th>
                        <th>CRM ID 1</th>
                        <th>CRM ID 2</th>
                        <th>CRM ID 3</th>
                        <th>SPV RO ID</th>
                        <th>RO ID 1</th>
                        <th>RO ID 2</th>
                        <th>RO ID 3</th>
                        <th>Loyalty ID</th>
                        <th>Loyalty</th>
                        <th>Kontrak Awal</th>
                        <th>Kontrak Akhir</th>
                        <th>Jumlah HC</th>
                        <th>Total Sebelum Pajak</th>
                        <th>Dasar Pengenaan Pajak</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>Total Invoice</th>
                        <th>Persen MF</th>
                        <th>Nominal MF</th>
                        <th>Persen BPJS TK</th>
                        <th>Nominal BPJS TK</th>
                        <th>Persen BPJS KS</th>
                        <th>Nominal BPJS KS</th>
                        <th>AS TK</th>
                        <th>AS KS</th>
                        <th>OHC</th>
                        <th>THR Provisi</th>
                        <th>THR Ditagihkan</th>
                        <th>Penagihan Selisih THR</th>
                        <th>Kaporlap</th>
                        <th>Device</th>
                        <th>Chemical</th>
                        <th>Training</th>
                        <th>Biaya Training</th>
                        <th>Tgl Kirim Invoice</th>
                        <th>Jumlah Hari TOP</th>
                        <th>Tipe Hari TOP</th>
                        <th>Tgl Gaji</th>
                        <th>PIC 1</th>
                        <th>Jabatan PIC 1</th>
                        <th>Email PIC 1</th>
                        <th>Telp PIC 1</th>
                        <th>PIC 2</th>
                        <th>Jabatan PIC 2</th>
                        <th>Email PIC 2</th>
                        <th>Telp PIC 2</th>
                        <th>PIC 3</th>
                        <th>Jabatan PIC 3</th>
                        <th>Email PIC 3</th>
                        <th>Telp PIC 3</th>
                        <th>Kategori Sesuai HC ID</th>
                        <th>Kategori Sesuai HC</th>
                        <th>Created At</th>
                        <th>Created By</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($datas as $data)
                      <tr>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->import_id }}</td>
                        <td>{{ $data->company_id }}</td>
                        <td>{{ $data->kode_site }}</td>
                        <td>{{ $data->nomor }}</td>
                        <td>{{ $data->tgl_pks }}</td>
                        <td>{{ $data->nama_site }}</td>
                        <td>{{ $data->alamat_site }}</td>
                        <td>{{ $data->nama_proyek }}</td>
                        <td>{{ $data->layanan_id }}</td>
                        <td>{{ $data->layanan }}</td>
                        <td>{{ $data->bidang_usaha_id }}</td>
                        <td>{{ $data->bidang_usaha }}</td>
                        <td>{{ $data->jenis_perusahaan_id }}</td>
                        <td>{{ $data->jenis_perusahaan }}</td>
                        <td>{{ $data->status_pks_id }}</td>
                        <td>{{ $data->provinsi_id }}</td>
                        <td>{{ $data->provinsi }}</td>
                        <td>{{ $data->kota_id }}</td>
                        <td>{{ $data->kota }}</td>
                        <td>{{ $data->pma }}</td>
                        <td>{{ $data->crm_id_1 }}</td>
                        <td>{{ $data->crm_id_2 }}</td>
                        <td>{{ $data->crm_id_3 }}</td>
                        <td>{{ $data->spv_ro_id }}</td>
                        <td>{{ $data->ro_id_1 }}</td>
                        <td>{{ $data->ro_id_2 }}</td>
                        <td>{{ $data->ro_id_3 }}</td>
                        <td>{{ $data->loyalty_id }}</td>
                        <td>{{ $data->loyalty }}</td>
                        <td>{{ $data->kontrak_awal }}</td>
                        <td>{{ $data->kontrak_akhir }}</td>
                        <td>{{ $data->jumlah_hc }}</td>
                        <td>{{ $data->total_sebelum_pajak }}</td>
                        <td>{{ $data->dasar_pengenaan_pajak }}</td>
                        <td>{{ $data->ppn }}</td>
                        <td>{{ $data->pph }}</td>
                        <td>{{ $data->total_invoice }}</td>
                        <td>{{ $data->persen_mf }}</td>
                        <td>{{ $data->nominal_mf }}</td>
                        <td>{{ $data->persen_bpjs_tk }}</td>
                        <td>{{ $data->nominal_bpjs_tk }}</td>
                        <td>{{ $data->persen_bpjs_ks }}</td>
                        <td>{{ $data->nominal_bpjs_ks }}</td>
                        <td>{{ $data->as_tk }}</td>
                        <td>{{ $data->as_ks }}</td>
                        <td>{{ $data->ohc }}</td>
                        <td>{{ $data->thr_provisi }}</td>
                        <td>{{ $data->thr_ditagihkan }}</td>
                        <td>{{ $data->penagihan_selisih_thr }}</td>
                        <td>{{ $data->kaporlap }}</td>
                        <td>{{ $data->device }}</td>
                        <td>{{ $data->chemical }}</td>
                        <td>{{ $data->training }}</td>
                        <td>{{ $data->biaya_training }}</td>
                        <td>{{ $data->tgl_kirim_invoice }}</td>
                        <td>{{ $data->jumlah_hari_top }}</td>
                        <td>{{ $data->tipe_hari_top }}</td>
                        <td>{{ $data->tgl_gaji }}</td>
                        <td>{{ $data->pic_1 }}</td>
                        <td>{{ $data->jabatan_pic_1 }}</td>
                        <td>{{ $data->email_pic_1 }}</td>
                        <td>{{ $data->telp_pic_1 }}</td>
                        <td>{{ $data->pic_2 }}</td>
                        <td>{{ $data->jabatan_pic_2 }}</td>
                        <td>{{ $data->email_pic_2 }}</td>
                        <td>{{ $data->telp_pic_2 }}</td>
                        <td>{{ $data->pic_3 }}</td>
                        <td>{{ $data->jabatan_pic_3 }}</td>
                        <td>{{ $data->email_pic_3 }}</td>
                        <td>{{ $data->telp_pic_3 }}</td>
                        <td>{{ $data->kategori_sesuai_hc_id }}</td>
                        <td>{{ $data->kategori_sesuai_hc }}</td>
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
