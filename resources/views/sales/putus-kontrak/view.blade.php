@extends('layouts.master')
@section('title','Putus Kontrak')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Lihat Putus Kontrak</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
        <div class="card mb-4">
            <h5 class="card-header text-center">BERITA ACARA LAPORAN SITE PUTUS</h5>
            <form action="#">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <strong>No Kontrak: {{$data->nomor_pks}}</strong>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Nama CRM</label>
                                <div class="col-sm-9 pt-2">: {{ $data->crm }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Nama BM</label>
                                <div class="col-sm-9 pt-2">: {{ $data->bm }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Nama Perusahaan</label>
                                <div class="col-sm-9 pt-2">: {{ $data->nama_perusahaan }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9 pt-2">: {{ $data->alamat }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Awal Kontrak</label>
                                <div class="col-sm-9 pt-2">: {{ \Carbon\Carbon::parse($data->awal_kontrak)->format('d-m-Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Akhir Kontrak</label>
                                <div class="col-sm-9 pt-2">: {{ \Carbon\Carbon::parse($data->akhir_kontrak)->format('d-m-Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">RO</label>
                                <div class="col-sm-9 pt-2">: {{ $data->ro }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Layanan</label>
                                <div class="col-sm-9 pt-2">: {{ $data->layanan }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Jumlah HC</label>
                                <div class="col-sm-9 pt-2">: {{ $data->jumlah_hc }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Nominal Invoice</label>
                                <div class="col-sm-9 pt-2">: {{ number_format($data->nominal_invoice, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Kronologi</label>
                                <div class="col-sm-9 pt-2">: {!! nl2br(e($data->kronologi)) !!}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12 mb-2">
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Tindakan</label>
                                <div class="col-sm-9 pt-2">: {!! nl2br(e($data->tindakan)) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-3">
      <div class="row">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Action</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-dots-vertical mdi-24px"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="col-12 text-center mt-2">
              <!-- <a onclick="window.open('{{route('pks.cetak-pks',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" id="btn-download-pks" class="btn btn-warning w-100 waves-effect waves-light"> -->
                <a href="javascript:void(0)" id="btn-cetak-putus-kontrak" class="btn btn-primary w-100 mt-2 waves-effect waves-light">
                    <span class="me-1">Cetak Putus Kontrak</span>
                    <i class="mdi mdi-printer"></i>
                </a>
            <a href="javascript:void(0)" id="btn-kembali" class="btn btn-secondary w-100 mt-2 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left"></i>
            </a>
            </div>
        </div>
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
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('putus-kontrak')}}");
  });
</script>
@endsection
