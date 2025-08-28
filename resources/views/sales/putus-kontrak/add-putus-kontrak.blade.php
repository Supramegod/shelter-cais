@extends('layouts.master')
@section('title','Berita Acara Site Putus')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header text-center">BERITA ACARA LAPORAN SITE PUTUS</h5>
        <form action="{{ route('putus-kontrak.save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pks_id" id="pks_id" value="{{$pks->id}}">
            <div class="card-body">
                <div class="text-center mb-4">
                    <strong>No : {{$pks->nomor}}</strong>
                </div>
                <p>
                    Pada hari ini {{ \Carbon\Carbon::now()->isoFormat('dddd') }} Tanggal {{ \Carbon\Carbon::now()->format('d') }} Bulan {{ \Carbon\Carbon::now()->isoFormat('MMMM') }} Tahun {{ \Carbon\Carbon::now()->format('Y') }} ({{ \Carbon\Carbon::now()->format('d/m/Y') }}).<br>
                    Yang bertanda tangan dibawah ini :
                </p>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Nama CRM</label>
                            <div class="col-sm-8 pt-2">: {{$pks->crm}}</div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Nama BM</label>
                            <div class="col-sm-8 pt-2">: {{$pks->bm}}</div>
                        </div>
                    </div>
                </div>

                <p class="mt-4">Menerangkan bahwa</p>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">Nama Customer</label>
                            <div class="col-sm-7 pt-2">: {{$pks->nama_perusahaan}}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">Alamat</label>
                            <div class="col-sm-7 pt-2">: {{$pks->alamat_perusahaan}}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">Nomor PKS</label>
                            <div class="col-sm-7 pt-2">: {{$pks->nomor}} Periode: {{$pks->awal_kontrak}} s.d. {{$pks->akhir_kontrak}}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">PIC Operasional</label>
                            <div class="col-sm-7 pt-2">: {{$pks->ro}}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">Layanan, Jumlah HC</label>
                            <div class="col-sm-7 pt-2">: {{$pks->layanan}}, {{$pks->jumlah_hc}} orang</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="row">
                            <label class="col-sm-5 col-form-label">Nominal Invoice</label>
                            <div class="col-sm-7 pt-2">: Rp {{ number_format($pks->nominal_invoice, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <p>
                    telah mengirim surat pemutusan dengan No : - dengan penjelasan sebagai berikut :
                </p>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kronologi Berita Pemutusan :</label>
                    <textarea name="kronologi" class="form-control" rows="3" required placeholder="Masukkan kronologi pemutusan..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tindakan & Pertanggungjawaban :</label>
                    <textarea name="tindakan" class="form-control" rows="3" required placeholder="Masukkan tindakan atau pertanggungjawaban..."></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="btn-submit">Simpan Berita Acara</button>
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
    $(document).ready(function() {
        $('#btn-submit').on('click',function(e){
            e.preventDefault();
            var form = $(this).parents('form');
            let msg = "";
            let obj = $("form").serializeObject();
            console.log(obj);


            if(obj.kronologi == null || obj.kronologi == "" ){
            msg += "<b>Kronologi</b> belum diisi </br>";
            };
            if(obj.tindakan == null || obj.tindakan == "" ){
            msg += "<b>Tindakan</b> belum diisi </br>";
            };

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
    });
</script>
@endsection
