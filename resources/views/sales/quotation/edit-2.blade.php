@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
  <div class="col-12 mb-4">
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
            <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-2')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">DETAIL KONTRAK</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mb-2">
                <h4 class="text-center">Kebutuhan</h4>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '1')) checked @endif">
                    <label class="form-check-label custom-option-content" for="direct_labour">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-hard-hat-outline"></i>
                        <span class="custom-option-title">Direct Labour</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="1" id="direct_labour" @if(str_contains($quotation->kebutuhan_id, '1')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '2')) checked @endif">
                    <label class="form-check-label custom-option-content" for="security">
                      <span class="custom-option-body">
                        <i class="mdi mdi-security"></i>
                        <span class="custom-option-title">Security</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="2" id="security" @if(str_contains($quotation->kebutuhan_id, '2')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '3')) checked @endif">
                    <label class="form-check-label custom-option-content" for="cleaning_service">
                      <span class="custom-option-body">
                        <i class="mdi mdi-spray-bottle"></i>
                        <span class="custom-option-title">Cleaning Service</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="3" id="cleaning_service" @if(str_contains($quotation->kebutuhan_id, '3')) checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if(str_contains($quotation->kebutuhan_id, '4')) checked @endif">
                    <label class="form-check-label custom-option-content" for="logistik">
                      <span class="custom-option-body">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span class="custom-option-title">Logistik</span>
                      </span>
                      <input name="kebutuhan[]" class="form-check-input" type="radio" value="4" id="logistik" @if(str_contains($quotation->kebutuhan_id, '4')) checked @endif>
                    </label>
                  </div>
                </div>
                @if($errors->has('kebutuhan'))
                  <span class="text-danger">{{$errors->first('kebutuhan')}}</span>
                @endif
              </div>
              <div class="row mb-3">
                <div class="col-sm-12">
                  <label class="form-label" for="basic-default-password42">Entitas</label>
                  <select id="entitas" name="entitas" class="form-select @if($errors->has('entitas')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($company as $value)
                      <option value="{{$value->id}}" @if($quotation->company_id==$value->id) selected @endif>{{$value->code}} | {{$value->name}}</option>  
                      @endforeach
                    </select>
                    @if($errors->has('entitas'))
                      <span class="text-danger">{{$errors->first('entitas')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="mulai-kontrak">Mulai Kontrak</label>
                  <input type="date" name="mulai_kontrak" value="{{$quotation->mulai_kontrak}}" class="form-control @if($errors->has('mulai_kontrak')) is-invalid @endif" id="mulai-kontrak">
                    @if($errors->has('mulai_kontrak'))
                      <span class="text-danger">{{$errors->first('mulai_kontrak')}}</span>
                    @endif
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="kontrak-selesai">Kontrak Selesai</label>
                  <input type="date" name="kontrak_selesai" value="{{$quotation->kontrak_selesai}}" class="form-control @if($errors->has('kontrak_selesai')) is-invalid @endif" id="kontrak-selesai">
                    @if($errors->has('kontrak_selesai'))
                      <span class="text-danger">{{$errors->first('kontrak_selesai')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="evaluasi_kontrak">Evaluasi Kontrak</label>
                  <select id="evaluasi_kontrak" name="evaluasi_kontrak" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="" @if($quotation->evaluasi_kontrak=='') selected @endif>- Pilih Data -</option>  
                    <option value="1 Bulan" @if($quotation->evaluasi_kontrak=='1 Bulan') selected @endif>1 Bulan</option>  
                    <option value="3 Bulan" @if($quotation->evaluasi_kontrak=='3 Bulan') selected @endif>3 Bulan</option>  
                    <option value="6 Bulan" @if($quotation->evaluasi_kontrak=='6 Bulan') selected @endif>6 Bulan</option>  
                    <option value="1 Tahun" @if($quotation->evaluasi_kontrak=='1 Tahun') selected @endif>1 Tahun</option>  
                    <option value="2 Tahun" @if($quotation->evaluasi_kontrak=='2 Tahun') selected @endif>2 Tahun</option>  
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="durasi_kerjasama">Durasi Kerjasama</label>
                  <input type="text" name="durasi_kerjasama" value="{{$quotation->durasi_kerjasama}}" class="form-control" id="durasi_kerjasama" readonly>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="tgl_penempatan">Tanggal Penempatan</label>
                  <input type="date" name="tgl_penempatan" value="{{$quotation->tgl_penempatan}}" class="form-control @if($errors->has('tgl_penempatan')) is-invalid @endif" id="tgl-penempatan">
                  @if($errors->has('tgl_penempatan'))
                    <span class="text-danger">{{$errors->first('tgl_penempatan')}}</span>
                  @endif
                  @if($errors->has('tgl_penempatan_kurang'))
                    <span class="text-danger">{{$errors->first('tgl_penempatan_kurang')}}</span>
                  @endif
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="durasi_karyawan">Durasi Karyawan</label>
                  <select id="durasi_karyawan" name="durasi_karyawan" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="" @if($quotation->durasi_karyawan=='') selected @endif>- Pilih Data -</option>  
                    <option value="1 Bulan" @if($quotation->durasi_karyawan=='1 Bulan') selected @endif>1 Bulan</option>  
                    <option value="3 Bulan" @if($quotation->durasi_karyawan=='3 Bulan') selected @endif>3 Bulan</option>  
                    <option value="6 Bulan" @if($quotation->durasi_karyawan=='6 Bulan') selected @endif>6 Bulan</option>  
                    <option value="1 Tahun" @if($quotation->durasi_karyawan=='1 Tahun') selected @endif>1 Tahun</option>  
                    <option value="2 Tahun" @if($quotation->durasi_karyawan=='2 Tahun') selected @endif>2 Tahun</option>
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="evaluasi_karyawan">Evaluasi Karyawan</label>
                  <select id="evaluasi_karyawan" name="evaluasi_karyawan" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="" @if($quotation->evaluasi_karyawan=='') selected @endif>- Pilih Data -</option>  
                    <option value="1 Bulan" @if($quotation->evaluasi_karyawan=='1 Bulan') selected @endif>1 Bulan</option>  
                    <option value="3 Bulan" @if($quotation->evaluasi_karyawan=='3 Bulan') selected @endif>3 Bulan</option>  
                    <option value="6 Bulan" @if($quotation->evaluasi_karyawan=='6 Bulan') selected @endif>6 Bulan</option>  
                    <option value="1 Tahun" @if($quotation->evaluasi_karyawan=='1 Tahun') selected @endif>1 Tahun</option>  
                    <option value="2 Tahun" @if($quotation->evaluasi_karyawan=='2 Tahun') selected @endif>2 Tahun</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="basic-default-password42">Salary Rule</label>
                  <select id="salary_rule" name="salary_rule" class="form-select @if($errors->has('salary_rule')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($salaryRule as $value)
                      <option value="{{$value->id}}" @if($quotation->salary_rule_id==$value->id) selected @endif>{{$value->nama_salary_rule}} | Cut Off : {{$value->cutoff}} | Tgl Gajian : {{$value->rilis_payroll}} | Pengiriman Invoice : {{$value->pengiriman_invoice}}</option>  
                      @endforeach
                    </select>
                    @if($errors->has('salary_rule'))
                      <span class="text-danger">{{$errors->first('salary_rule')}}</span>
                    @endif
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4">
                  <label class="form-label" for="basic-default-password42">TOP Invoice</label>
                    <select id="top" name="top" class="form-select @if($errors->has('top')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                    <option value="Kurang Dari 7 Hari" @if($quotation->top=='Kurang Dari 7 Hari') selected @endif>Kurang Dari 7 Hari</option>  
                    <option value="Lebih Dari 7 Hari" @if($quotation->top=='Lebih Dari 7 Hari') selected @endif>Lebih Dari 7 Hari</option>  
                    </select>
                    @if($errors->has('top'))
                      <span class="text-danger">{{$errors->first('top')}}</span>
                    @endif
                </div>
                <div class="col-sm-4 d-top-invoice">
                  <label class="form-label">&nbsp;</label>
                  <select id="jumlah_hari_invoice" name="jumlah_hari_invoice" class="form-select @if($errors->has('jumlah_hari_invoice')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                    <option value=""></option>  
                    <option value="7" @if($quotation->jumlah_hari_invoice=='7') selected @endif>7</option>  
                    <option value="14" @if($quotation->jumlah_hari_invoice=='14') selected @endif>14</option>  
                    <option value="15" @if($quotation->jumlah_hari_invoice=='15') selected @endif>15</option>  
                    <option value="21" @if($quotation->jumlah_hari_invoice=='21') selected @endif>21</option>  
                    <option value="30" @if($quotation->jumlah_hari_invoice=='30') selected @endif>30</option>  
                    </select>
                    @if($errors->has('jumlah_hari_invoice'))
                      <span class="text-danger">{{$errors->first('jumlah_hari_invoice')}}</span>
                    @endif
                </div>
                <div class="col-sm-4 d-top-invoice d-top-tipe">
                  <label class="form-label" for="">&nbsp;</label>
                  <select id="tipe_hari_invoice" name="tipe_hari_invoice" class="form-select @if($errors->has('tipe_hari_invoice')) is-invalid @endif" data-allow-clear="true" tabindex="-1">
                    <option value=""></option>    
                    <option value="Kalender" @if($quotation->tipe_hari_invoice=='Kalender') selected @endif>Hari Kalender</option>  
                    <option class="opt_tipe_hari_kerja" value="Kerja" @if($quotation->tipe_hari_invoice=='Kerja') selected @endif>Hari Kerja</option>  
                    </select>
                    @if($errors->has('tipe_hari_invoice'))
                      <span class="text-danger">{{$errors->first('tipe_hari_invoice')}}</span>
                    @endif
                </div>
                <span class="text-warning mt-3">*TOP invoice lebih dari 7 hari membutuhkan approval dari direksi</span>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="ada_thr">Tunjangan Hari Raya</label>
                    <select id="ada_thr" name="ada_thr" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="Ada" @if($quotation->thr!="") selected @endif>Ada</option>  
                    <option value="Tidak Ada" @if($quotation->thr=="" && $quotation->thr!=null) selected @endif>Tidak Ada</option>  
                    </select>
                </div>
                <div class="col-sm-6 ada_thr">
                  <label class="form-label" for="thr">Provisi / Ditagihkan</label>
                    <select id="thr" name="thr" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="Diprovisikan" @if($quotation->thr=="Diprovisikan") selected @endif>Diprovisikan</option>  
                    <option value="Ditagihkan" @if($quotation->thr=="Ditagihkan") selected @endif>Ditagihkan</option>  
                    </select>
                </div>
              </div>
              @include('sales.quotation.action')
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<!--/ Content -->
@endsection
@section('pageScript')
<script>
  // validasi input
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
    
  if(obj['kebutuhan[]'] == null || obj['kebutuhan[]'] == "" ){
    msg += "<b>Kebutuhan</b> belum dipilih </br>";
  };

  if(obj.entitas == null || obj.entitas == "" ){
    msg += "<b>Entitas</b> belum dipilih </br>";
  };

  if(obj.mulai_kontrak == null || obj.mulai_kontrak == ""){
    msg += "<b>Mulai Kontrak</b> belum dipilih </br>";
  }

  if(obj.kontrak_selesai == null || obj.kontrak_selesai == ""){
    msg += "<b>Kontrak Selesai</b> belum dipilih </br>";
  }

  if(obj.evaluasi_kontrak == null || obj.evaluasi_kontrak == ""){
    msg += "<b>Evaluasi Kontrak</b> belum dipilih </br>";
  }
  if(obj.tgl_penempatan == null || obj.tgl_penempatan == ""){
    msg += "<b>Tanggal Penempatan</b> belum dipilih </br>";
  }
  if(obj.durasi_karyawan == null || obj.durasi_karyawan == ""){
    msg += "<b>Durasi Karyawan</b> belum dipilih </br>";
  }
  if(obj.evaluasi_karyawan == null || obj.evaluasi_karyawan == ""){
    msg += "<b>Evaluasi Karyawan</b> belum dipilih </br>";
  }
  if(obj.salary_rule == null || obj.salary_rule == ""){
    msg += "<b>Salary Rule</b> belum dipilih </br>";
  }
  if(obj.top == null || obj.top == ""){
    msg += "<b>TOP Invoice</b> belum dipilih </br>";
  }
  if(obj.ada_thr == "Ada"){
    if(obj.thr == null || obj.thr == ""){
      msg += "<b>THR</b> belum dipilih </br>";
    }
  }

  if (obj.top != null && obj.top != "") {
    if(obj.top=="Lebih Dari 7 Hari"){
      if(obj.jumlah_hari_invoice==""){
        msg += "<b>Jumlah Hari TOP Invoice</b> belum dipilih </br>";
      }

      if(obj.tipe_hari_invoice==""){
        msg += "<b>Tipe Hari TOP Invoice</b> belum dipilih </br>";
      }
    }
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

function validasiKontrak() {
  let awal = $('#mulai-kontrak').val();
  let akhir = $('#kontrak-selesai').val();

  if (awal != null && awal != "" && akhir != null && akhir != "") {
    let dtAwal = new Date($('#mulai-kontrak').val());
    let dtAkhir = new Date($('#kontrak-selesai').val());
    if(dtAwal>dtAkhir){
      Swal.fire({
        title: "Pemberitahuan",
        html: "Mulai Kontrak melebihi Kontrak Selesai",
        icon: "warning"
      });

      $('#mulai-kontrak').val('').attr('type', 'text').attr('type', 'date');
      $('#kontrak-selesai').val('').attr('type', 'text').attr('type', 'date');
    }else{      
      var mulai = $('#mulai-kontrak').val();
      var selesai = $('#kontrak-selesai').val();

      if (mulai && selesai) {
          // Konversi nilai input menjadi objek Date
          var mulaiDate = new Date(mulai);
          var selesaiDate = new Date(selesai);

          // Hitung perbedaan tahun dan bulan
          var tahun = selesaiDate.getFullYear() - mulaiDate.getFullYear();
          var bulan = selesaiDate.getMonth() - mulaiDate.getMonth();

          // Jika bulan negatif, kurangi satu tahun dan tambahkan 12 bulan
          if (bulan < 0) {
              tahun--;
              bulan += 12;
          }

          // Siapkan string hasil
          var hasil = '';

          // Hanya tampilkan tahun jika tidak 0
          if (tahun > 0) {
              hasil += tahun + ' tahun';
          }

          // Hanya tampilkan bulan jika tidak 0
          if (bulan > 0) {
              // Tambahkan spasi jika ada tahun sebelumnya
              if (hasil !== '') {
                  hasil += ', ';
              }
              hasil += bulan + ' bulan';
          }

          // Tampilkan hasil
          $('#durasi_kerjasama').val(hasil);
      }
    }
  }
}

function validasiPenempatan() {
  let awal = $('#mulai-kontrak').val();
  let akhir = $('#kontrak-selesai').val();
  let penempatan = $('#tgl-penempatan').val();

  if (awal != null && awal != "" && akhir != null && akhir != "" && penempatan != null && penempatan != "") {
    let dtAwal = new Date($('#mulai-kontrak').val());
    let dtAkhir = new Date($('#kontrak-selesai').val());
    let dtPenempatan = new Date($('#tgl-penempatan').val());

    if(dtPenempatan>dtAkhir){
      Swal.fire({
        title: "Pemberitahuan",
        html: "Tanggal Kontrak melebihi Kontrak Selesai",
        icon: "warning"
      });

      $('#tgl-penempatan').val('').attr('type', 'text').attr('type', 'date');
    } else if(dtPenempatan<dtAwal){
      Swal.fire({
        title: "Pemberitahuan",
        html: "Tanggal Kontrak kurang dari Mulai Kontrak",
        icon: "warning"
      });

      $('#tgl-penempatan').val('').attr('type', 'text').attr('type', 'date');
    }
  }
}

$('#mulai-kontrak').on('focusout', function() {
  validasiKontrak();
});
$('#kontrak-selesai').on('focusout', function() {
  validasiKontrak();
});
$('#tgl-penempatan').on('focusout', function() {
  validasiPenempatan();
});

function showDTop() {
  let selected = $("#top option:selected").val();
  if (selected=="Lebih Dari 7 Hari") {
    $('.d-top-invoice').removeClass('d-none');
  }else{
    $('.d-top-invoice').addClass('d-none');
    $("#jumlah_hari_invoice").val("").change();
    $("#tipe_hari_invoice").val("").change();
  }
}

function showDTipeHari(first) {
  let selected = $("#jumlah_hari_invoice option:selected").val();
  
  if (selected=="21" || selected=="30") {
    $('.opt_tipe_hari_kerja').addClass('d-none');
    $("#tipe_hari_invoice").val("Kalender").change();
  }else{
    $('.opt_tipe_hari_kerja').removeClass('d-none');
    if(first != 1 ){
          $("#tipe_hari_invoice").val("").change();
    }
  }
}
showDTop();
showDTipeHari(1);
showThr(1); 
$('#top').on('change', function() {
  showDTop();
});

function showThr(first) {
  let selected = $("#ada_thr option:selected").val();
  
  if (selected!="Ada") {
    $('.ada_thr').addClass('d-none');
  }else{
    $('.ada_thr').removeClass('d-none');
    if(first!=1){
      $("#thr").val("").change();
    }
  }
}

$('#ada_thr').on('change', function() {
  showThr(2);
});

$('#jumlah_hari_invoice').on('change', function() {
  showDTipeHari(2);
});

</script>
@endsection