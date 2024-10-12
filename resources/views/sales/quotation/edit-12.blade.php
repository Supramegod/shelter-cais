@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-12')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">FORM CHECKLIST NEW SITE & PERJANJIAN KERJA SAMA</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
                <div class="table-responsive overflow-hidden table-data">
                  <table id="table-data" class="dt-column-search table table-hover" style="padding-right:0px !important">
                      <tbody>
                        <tr>
                          <td>No. PKS</td>
                          <td>043/PKS/RCI-RKP/VII/2024</td>
                          <td>Tgl. Pengajuan Kerjasama</td>
                          <td>19 Juli 2024</td>
                        </tr>
                        <tr>
                          <td colspan="4">PERSONAL INFORMASI</td>
                        </tr>
                        <tr>
                          <td>Pengusul Kerjasama</td>
                          <td colspan="3">{{$quotation->nama_perusahaan}}</td>
                        </tr>
                        <tr>
                          <td>Alamat Pengusul Kerjasama</td>
                          <td colspan="3"></td>
                        </tr>
                        <tr>
                          <td>No. Telp Perusahaan</td>
                          <td colspan="3"></td>
                        </tr>
                        <tr>
                          <td>Penerima Kerjasama</td>
                          <td colspan="3"></td>
                        </tr>
                        <tr>
                          <td>Hal Kerjasama</td>
                          <td colspan="3">PERJANJIAN KERJASAMA ALIH DAYA JASA KEBERSIHAN</td>
                        </tr>
                        <tr>
                          <td>Jumlah Personel</td>
                          <td colspan="3">4 Manpower (1 Leader, 3 CS)</td>
                        </tr>
                        <tr>
                          <td>PIC</td>
                          <td colspan="3">Afifah</td>
                        </tr>
                        <tr>
                          <td>Jabatan PIC </td>
                          <td colspan="3">Project Manager.</td>
                        </tr>
                        <tr>
                          <td>No. Telp PIC </td>
                          <td colspan="3">+62 811-3579-313.</td>
                        </tr>
                        <tr>
                          <td>NPWP </td>
                          <td colspan="3"><input type="text" name="" id=""></td>
                        </tr>
                        <tr>
                          <td>Alamat NPWP </td>
                          <td colspan="3"><input type="text" name="" id=""></td>
                        </tr>
                        <tr>
                          <td>PIC Invoice </td>
                          <td>Anugrah Pammase</td>
                          <td>+62 822 1818 2594 </td>
                          <td>financeposblocjkt@gmail.com</td>
                        </tr>
                        <tr>
                          <td colspan="4">INFORMASI KERJASAMA</td>
                        </tr>
                        <tr>
                          <td>Durasi Kerjasama</td>
                          <td>1 Tahun</td>
                          <td>Evaluasi 6 Bulan</td>
                          <td>19 Juli 2024 - 18 Juli 2024.</td>
                        </tr>
                        <tr>
                          <td>Kontrak Karyawan</td>
                          <td>PKWT 6 Bulan</td>
                          <td>Evaluasi 3 Bulan</td>
                          <td>Start 19 Juli 2024</td>
                        </tr>
                        <tr>
                          <td>Materai</td>
                          <td colspan="3">Dari personil.</td>
                        </tr>
                        <tr>
                          <td>Hari Kerja dan Jam Kerja</td>
                          <td>2 Shift</td>
                          <td>8 Jam Kerja</td>
                          <td>08.00 s/d 23.00</td>
                        </tr>
                        <tr>
                          <td>System Kerja</td>
                          <td colspan="3"><i>No Work No Pay</i></td>
                        </tr>
                        <tr>
                          <td>Kebijakan Cuti </td>
                          <td colspan="3"><b>Tidak ada</b></td>
                        </tr>
                        <tr>
                          <td>Kunjungan Operasional</td>
                          <td colspan="3">1 Bulan 1 Kali bertemu dengan PIC ibu Afifah, bp Javier</td>
                        </tr>
                        <tr>
                          <td>Kunjungan Tim CRM</td>
                          <td colspan="3">1 Tahun 2 Kali bertemu dengan PIC ibu Afifah, bp Javier</td>
                        </tr>
                        <tr>
                          <td>Training</td>
                          <td colspan="3"><b>1 Tahun 3 Kali</b></td>
                        </tr>
                        <tr>
                          <td>Tunjangan Hari Raya (THR)</td>
                          <td colspan="3">Ditagihkan terpisah H-45 hari raya base on upah pokok
                            <table class="table table-bordered" style="width:100%">
                              <tr>
                                <td>No.</td>
                                <td>Schedule Plan</td>
                                <td>Time</td>
                              </tr>
                              <tr>
                                <td>1</td>
                                <td>Penagihan Invoice THR </td>
                                <td>ditagihkan H-45</td>
                              </tr>
                              <tr>
                                <td>2</td>
                                <td>Pembayaran Invoice THR</td>
                                <td>Maksimal h-14 hari raya</td>
                              </tr>
                              <tr>
                                <td>3</td>
                                <td>Rilis THR</td>
                                <td>Maksimal h-7 Hari Raya</td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                  </table>
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
  $(document).ready(function(){
    $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });
    
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    form.submit();
  });
  
  });
</script>
@endsection