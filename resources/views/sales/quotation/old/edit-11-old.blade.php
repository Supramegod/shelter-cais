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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-11')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">COST STRUCTURE</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
              <div class="card-header">        </div>
        <div class="card-body">
        <div class="card-header p-0">
          <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill" role="tablist">
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-hpp" aria-controls="navs-top-hpp" aria-selected="false" tabindex="-1">
                  HPP
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-coss" aria-controls="navs-top-coss" aria-selected="false" tabindex="-1">
                  Cost Structure
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-gpm" aria-controls="navs-top-gpm" aria-selected="false" tabindex="-1">
                  Analisa GPM
                </button>
              </li>
            <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span></ul>
          </div>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <div class="tab-pane fade active show" id="navs-top-hpp" role="tabpanel">
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    <thead class="text-center">
                      <tr class="table-success">
                        <th colspan="4" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="4" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th rowspan="2" style="vertical-align: middle;">No.</th>
                        <th>Structure</th>
                        <th rowspan="2" style="vertical-align: middle;">%</th>
                        <th >{{$data->kebutuhan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th>Jumlah Head Count ( Personil ) </th>
                        <th>{{$data->totalHc}}</th>
                      </tr>
                    </thead>              
                    <tbody>
                      @php
                       $nomor = 1;
                       $snomor = "1";
                      @endphp
                      @foreach($listHPP as $ihpp => $hpp)
                        @php
                        if(in_array($hpp->kunci,['gaji_pokok','tunjanan_overtime','tunjangan_hari_raya','bpjs_jkk','bpjs_kes','provisi_seragam','provisi_devices','ohc'])){
                          $snomor = $nomor;
                          $nomor++;
                        }else{
                          $snomor = "";
                        };

                        $trclass = "";
                        if(in_array($hpp->kunci,['biaya_personil','grand_total','total_invoice','pembulatan'])){
                          $trclass="table-success";
                        }else{
                          $trclass = "";
                        }

                        $structureAlign = "left";
                        if(in_array($hpp->kunci,['biaya_personil','sub_biaya_personil','management_fee','grand_total','ppn_management_fee','pph_management_fee','total_invoice','pembulatan'])){
                          $structureAlign="right";
                        }

                        $fontWeight ="";
                        if(in_array($hpp->kunci,['biaya_personil','sub_biaya_personil','grand_total','total_invoice','pembulatan'])){
                          $fontWeight="fw-bold";
                        }
                        
                        @endphp
                        <tr class="{{$trclass}}">
                          <td style="text-align:center">{{$snomor}}</td>
                          <td style="text-align:{{$structureAlign}}" class="{{$fontWeight}}">{!!$hpp->structure!!}</td>
                          <td style="text-align:center">{{$hpp->percentage}} @if(in_array($hpp->kunci,['bpjs_jkk','bpjs_jkm','bpjs_jht','bpjs_kes','management_fee','ppn_management_fee','pph_management_fee'])) % @endif</td>
                          <td style="text-align:right" class="{{$fontWeight}}">Rp {{number_format($hpp->nominal,0,",",".")}}</td>
                        </tr>
                      @endforeach                
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <p><b><i>Note :</i></b>	<br>
Tunjangan hari raya (gaji pokok dibagi 12).		<br>
Tunjangan overtime flat		<br>
<i>Cover</i> BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). <span class="text-danger">Pengalian base on upah</span>		<br>
<i>Cover</i> BPJS Kesehatan. <span class="text-danger">Pengalian base on UMK</span>		<br>
</p>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-coss" role="tabpanel">
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    <thead class="text-center">
                      <tr class="table-success">
                        <th colspan="3" style="vertical-align: middle;">COST STRUCTURE {{$data->kebutuhan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="3" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                    </thead>              
                    <tbody>
                      <tr>
                        <td class="fw-bold">Structure</th>
                        <td class="text-center fw-bold">%</th>
                        <td class="text-center fw-bold">{{$data->kebutuhan}}</th>
                      </tr>
                      <tr>
                        <td class="fw-bold">Jumlah Personil</th>
                        <td class="text-center fw-bold"></th>
                        <td class="text-center fw-bold">{{$data->totalHc}}</th>
                      </tr>
                      <tr>
                        <td class="fw-bold">1. BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        <td class="text-center fw-bold">Unit/Month</th>
                      </tr>
                      @php
                      $total = 0;
                      @endphp
                      @foreach($listCS as $ics => $cs)
                        @php
                        $total = $total+$cs->nominal;
                        @endphp
                        @if($cs->kunci=="tunjangan_hari_raya")
                        @php
                        $total = $total-$cs->nominal;
                        @endphp
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</th>
                          <td class="text-center fw-bold"></th>
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($total,0,",",".")}}</th>
                        </tr>
                        <tr>
                          <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                          <td class="text-center fw-bold"></th>
                          <td class="text-center fw-bold">Unit/Month</th>
                          </tr>

                          @php
                          $total = $cs->nominal;
                          @endphp
                        @endif
                        @if($cs->kunci=="biaya_monitoring_kontrol")
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Exclude Base Manpower Cost</th>
                          <td class="text-center fw-bold"></th>
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($total,0,",",".")}}</th>
                        </tr>
                        <!-- <tr>
                          <td class="fw-bold">3. BIAYA MONITORING & KONTROL</th>
                          <td class="text-center fw-bold"></th>
                          <td class="text-center fw-bold">Unit/Month</th>
                        </tr>
                        <tr>
                          <td style="text-align:left">Biaya Visit & Kontrol Operasional, visit CRM</td>
                          <td style="text-align:center"></td>
                          <td rowspan="5" style="text-align:right;font-weight:bold">Rp {{number_format($cs->nominal,0,",",".")}}</td>
                        </tr>
                        <tr>
                          <td style="text-align:left">Biaya Komunikasi Rekrutmen, Pembinaan, Training Induction & Supervisi</td>
                          <td style="text-align:center"></td>
                        </tr>
                        <tr>
                          <td style="text-align:left">Biaya Proses Kontrak Karyawan, Payroll, dll</td>
                          <td style="text-align:center"></td>
                        </tr>
                        <tr>
                          <td style="text-align:left">Biaya Emergency Response Team</td>
                          <td style="text-align:center"></td>
                        </tr>
                        <tr>
                          <td style="text-align:left">Biaya Investigasi Team</td>
                          <td style="text-align:center"></td>
                        </tr> -->
                        @else
                        @php
                          $trclass = "";
                          if(in_array($cs->kunci,['biaya_personil','grand_total','total_invoice','pembulatan'])){
                            $trclass="table-success";
                          }else{
                            $trclass = "";
                          }

                          $structureAlign = "left";
                          if(in_array($cs->kunci,['biaya_personil','sub_biaya_personil','management_fee','grand_total','ppn_management_fee','pph_management_fee','total_invoice','pembulatan'])){
                            $structureAlign="right";
                          }

                          $fontWeight ="";
                          if(in_array($cs->kunci,['management_fee','ppn_management_fee','pph_management_fee','biaya_personil','sub_biaya_personil','grand_total','total_invoice','pembulatan'])){
                            $fontWeight="fw-bold";
                          }
                        @endphp
                          <tr class="{{$trclass}}">
                            <td class="{{$fontWeight}}" style="text-align:{{$structureAlign}}">{!!$cs->structure!!}</td>
                            <td style="text-align:center">{{$cs->percentage}}</td>
                            <td class="{{$fontWeight}}" style="text-align:right">Rp {{number_format($cs->nominal,0,",",".")}}</td>
                          </tr>
                        @endif
                      @endforeach                
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <p><b><i>Note :</i></b>	<br>
                  <b>Upah pokok base on Umk 2024 Karawang.</b> <br>
Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
BPJS Ketenagakerjaan 3 program (Jkk, Jkm, Jht). <span class="text-danger">*base on upah pokok</span> <br>
BPJS Kesehatan. <span class="text-danger">*base on Umk 2024</span> <br>
<br>
<span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>
</p>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-gpm" role="tabpanel">
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    <thead class="text-center">
                      <tr class="table-success">
                        <th>Keterangan</th>
                        <th>HPP</th>
                        <th>Harga Jual</th>
                      </tr>
                    </thead>              
                    <tbody>
                      @foreach($listGpm as $igpm => $gpm)
                        <tr>
                          <td style="text-align:left">{{$gpm->keterangan}}</td>
                          @if($gpm->kunci == 'gpm')
                          <td style="text-align:right">{{number_format($gpm->hpp,2,",",".")}} %</td>
                          <td style="text-align:right">{{number_format($gpm->harga_jual,2,",",".")}} %</td>
                          @else
                          <td style="text-align:right">Rp {{number_format($gpm->hpp,0,",",".")}}</td>
                          <td style="text-align:right">Rp {{number_format($gpm->harga_jual,0,",",".")}}</td>
                          @endif

                        </tr>
                      @endforeach                
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
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

</script>
@endsection