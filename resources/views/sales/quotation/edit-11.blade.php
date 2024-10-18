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
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th rowspan="2" style="vertical-align: middle;">No.</th>
                        <th>Structure</th>
                        <th rowspan="2" style="vertical-align: middle;">%</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th >{{$detailJabatan->jabatan_kebutuhan}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <th>Jumlah Head Count ( Personil ) </th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th >{{$detailJabatan->jumlah_hc}}</th>
                        @endforeach
                      </tr>
                    </thead>              
                    <tbody>
                      <tr class="">
                        <td style="text-align:center">1</td>
                        <td style="text-align:left" class="">Gaji Pokok</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($quotationKebutuhan[0]->nominal_upah,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr class="">
                        <td style="text-align:center">{{2+$it}}</td>
                        <td style="text-align:left" class="">{{$tunjangan->nama}}</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @endforeach
                      @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                      <tr class="">
                        <td style="text-align:center">{{2+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$quotation->thr}} )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($quotation->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}}@elseif($quotation->thr=="Ditagihkan") Ditagihkan terpisah @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      @php
                      $nomorUrut = 0;
                      @endphp
                      @if($quotationKebutuhan[0]->penjamin=="BPJS")
                      @php
                      $nomorUrut = 5;
                      @endphp
                        <tr class="">
                          <td style="text-align:center">{{3+count($daftarTunjangan)}}</td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kecelakaan Kerja</td>
                          <td style="text-align:center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kematian</td>
                          <td style="text-align:center">0,3 %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Hari Tua</td>
                          <td style="text-align:center">3,7 %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Pensiun</td>
                          <td style="text-align:center">2 %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                      <tr class="">
                        <td style="text-align:center">{{4+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">BPJS Kesehatan </td>
                        <td style="text-align:center">4 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @else
                      @php
                      $nomorUrut = 4;
                      @endphp
                      <tr class="">
                        <td style="text-align:center">{{3+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Takaful </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @endif
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Provisi Seragam </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut+1+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Provisi Peralatan </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut+2+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Over Head Cost </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_ohc,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut+3+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Chemical </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="">Management Fee (MF)</td>
                        <td style="text-align:center">{{$quotationKebutuhan[0]->persentase}} %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPn <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">11 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">-2 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan,2,",",".")}}</td>
                        @endforeach
                      </tr>             
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
Tunjangan hari raya (gaji pokok dibagi 12).		<br>
Tunjangan overtime flat		<br>
<i>Cover</i> 
@if($quotationKebutuhan[0]->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotationKebutuhan[0]->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP). 
@endif
<span class="text-danger">Pengalian base on upah</span>		<br>
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
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">COST STRUCTURE {{$data->kebutuhan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                    </thead>              
                    <tbody>
                      <tr>
                        <td class="fw-bold">Structure</th>
                        <td class="text-center fw-bold">%</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">Jumlah Personil</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th class="text-center">{{$detailJabatan->jumlah_hc}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">1. BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center" class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Upah/Gaji</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($quotationKebutuhan[0]->nominal_upah,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr>
                        <td>{{$tunjangan->nama}}</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endforeach
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                      <tr>
                        <td>Provisi Tunjangan Hari Raya (THR)</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotationKebutuhan[0]->penjamin=="BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Kecelakaan Kerja</th>
                          <td class="text-center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Premi BPJS TK J. Kematian</th>
                          <td class="text-center">0,30 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Hari Tua</th>
                          <td class="text-center">3,7 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Pensiun</th>
                          <td class="text-center">2 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @endif
                        <tr>
                          <td>Premi BPJS Kesehatan</th>
                          <td class="text-center">4 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</th>
                          @endforeach
                        </tr>
                      @else
                        <tr>
                          <td>Takaful</th>
                          <td class="text-center"></th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</th>
                          @endforeach
                        </tr>
                      @endif
                      <tr>
                        <td>Provisi Seragam</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Peralatan</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Chemical</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Exclude Base Manpower Cost</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">3. BIAYA MONITORING & KONTROL</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Visit & Kontrol Operasional, visit CRM</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td rowspan="5" style="text-align:right;font-weight:bold"><span data-kebutuhan_detail_id="{{$detailJabatan->id}}" data-quotation_kebutuhan_id="{{$detailJabatan->quotation_kebutuhan_id}}" class="edit-biaya-monitoring">Rp {{number_format($detailJabatan->biaya_monitoring_kontrol,2,",",".")}} <i class="mdi mdi-pencil text-warning"></i></span></td>
                        @endforeach
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
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Total Biaya per Personil <span class="text-danger">(1+2+3)</span></td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari sub total biaya</span></td>
                        <td style="text-align:center">{{$quotationKebutuhan[0]->persentase}} %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">PPn <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">11 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">-2 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>      
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
                  <b>Upah pokok base on Umk 2024 </b> <br>
Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
@if($quotationKebutuhan[0]->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotationKebutuhan[0]->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP). 
@endif
<span class="text-danger">Pengalian base on upah</span>		<br>
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
                      <tr>
                        <td style="text-align:left">Nominal</td>
                        <td style="text-align:right">
                          @php
                          $totalNominal = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalNominal += $detailJabatan->total_invoice;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalNominal,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalNominalCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalNominalCoss += $detailJabatan->total_invoice_coss;
                          }
                        @endphp
                          {{"Rp. ".number_format($totalNominalCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">PPN</td>
                        <td style="text-align:right">
                        @php
                          $ppn = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $ppn += $detailJabatan->ppn;
                          }
                          @endphp
                          {{"Rp. ".number_format($ppn,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $ppnCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $ppnCoss += $detailJabatan->ppn_coss;
                          }
                          @endphp
                          {{"Rp. ".number_format($ppnCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Total Biaya</td>
                        <td style="text-align:right">
                        @php
                          $totalBiaya = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalBiaya += $detailJabatan->sub_total_personil;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiaya,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalBiayaCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalBiayaCoss += $detailJabatan->sub_total_personil_coss;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiayaCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Margin</td>
                        <td style="text-align:right">
                          @php
                            $margin = $totalNominal-$ppn-$totalBiaya;
                          @endphp
                          {{"Rp. ".number_format($margin,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                            $marginCoss = $totalNominalCoss-$ppnCoss-$totalBiayaCoss;
                          @endphp
                          {{"Rp. ".number_format($marginCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td class="fw-bold" style="text-align:left">GPM</td>
                        <td class="fw-bold" style="text-align:right">
                          @php
                            $gpm = ($margin/$totalBiaya)*100;
                          @endphp
                          {{$gpm}} %
                        </td>
                        <td class="fw-bold" style="text-align:right">
                        @php
                            $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
                          @endphp
                          {{$gpmCoss}} %
                        </td>
                      </tr>
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

  $('.edit-biaya-monitoring').on('click',function(e){
    let kebutuhanId = $(this).data('quotation_kebutuhan_id');
    let detailId = $(this).data('kebutuhan_detail_id');
    
    Swal.fire({
      title: "Masukkan nominal Biaya Monitoring dan Kontrol",
      input: "number",
      showCancelButton: true,
      confirmButtonText: "Simpan",
      showLoaderOnConfirm: true,
      preConfirm: async (nominal) => {
        try {
          let formData = {
            "kebutuhanId":$(this).data('quotation_kebutuhan_id'),
            "nominal":nominal,
            "detailId":$(this).data('kebutuhan_detail_id'),
            "_token": "{{ csrf_token() }}"
          };
          
          $.ajax({
            type: "POST",
            url: "{{route('quotation.add-biaya-monitoring')}}",
            data:formData,
            success: function(response){
              window.location.reload();
            },
            error:function(error){
              console.log(error);
            }
          });
        } catch (error) {
          Swal.showValidationMessage(`
            Request failed: ${error}
          `);
        }
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
    });
  });
</script>
@endsection