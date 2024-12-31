@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="mb-4 overflow-auto text-nowrap" style="max-width:fit-content !important;width:auto !important;">
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
                @foreach($quotation->quotation_site as $site)
                  <h6>{{$site->nama_site}}</h6>
                @endforeach
              </div>
              <div class="row mt-5">
              <div class="card-header">
              </div>
        <div class="card-body">
        <div class="card-header p-0">
          <button type="button" id="btn-tambah-item" class="btn btn-info btn-back w-20 waves-effect waves-light"  data-bs-toggle="modal" data-bs-target="#basicModal" style="margin-left:20px;margin-bottom:20px">
            <i class="mdi mdi-plus"></i> &nbsp; Tunjangan
          </button>
          <div class="row mb-3" style="margin-left:20px;margin-bottom:20px;margin-right:20px;">
            <div class="col-sm-12">
              <label class="form-label" for="barang">Penagihan</label>
              <div class="input-group">
                <select id="penagihan" name="penagihan" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="" @if($quotation->penagihan=="" || $quotation->penagihan==null) selected @endif>- Pilih data -</option>
                  <option value="Tanpa Pembulatan" @if($quotation->penagihan=="Tanpa Pembulatan") selected @endif>Tanpa Pembulatan</option>
                  <option value="Dengan Pembulatan" @if($quotation->penagihan=="Dengan Pembulatan") selected @endif>Dengan Pembulatan</option>
                </select>
              </div>
            </div>
          </div>
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
                        <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}} ( Provisi = {{$quotation->provisi}} )</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="3">&nbsp;</th>
                        @foreach($quotation->quotation_site as $site)
                        <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;">{{$site->nama_site}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <th rowspan="2" style="vertical-align: middle;">No.</th>
                        <th>Structure</th>
                        <th rowspan="2" style="vertical-align: middle;">%</th>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <th >{{$detailJabatan->jabatan_kebutuhan}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <th>Jumlah Head Count ( Personil ) </th>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <th >{{$detailJabatan->jumlah_hc}}</th>
                        @endforeach
                      </tr>
                    </thead>              
                    <tbody>
                      @php $nomorUrut = 1; @endphp
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Gaji Pokok</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">{{$tunjangan->nama}} <a href="javascript:void(0)"><i class="mdi mdi-delete text-danger delete-tunjangan" data-nama="{{$tunjangan->nama}}"></i></a></td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}  &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-pencil text-warning edit-tunjangan" data-id="{{$detailJabatan->id}}" data-nama="{{$tunjangan->nama}}"></i></a></td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endforeach
                      @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$quotation->thr}} )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($quotation->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}}@elseif($quotation->thr=="Ditagihkan") Ditagihkan terpisah @endif</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endif
                      @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Kompensasi <b>( {{$quotation->kompensasi}} )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($quotation->kompensasi=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}}@elseif($quotation->kompensasi=="Ditagihkan") Ditagihkan terpisah @endif</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endif
                      @if($quotation->tunjangan_holiday=="Normatif" || $quotation->tunjangan_holiday=="Flat")
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Tunjangan Hari Libur Nasional <b>( {{$quotation->tunjangan_holiday}} @if($quotation->tunjangan_holiday=="Normatif") : {{"Rp. ".number_format($quotation->tunjangan_holiday_display,2,",",".")}} @endif )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($quotation->tunjangan_holiday=="Normatif"){{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}}@elseif($quotation->tunjangan_holiday=="Flat") {{"Rp. ".number_format($quotation->nominal_tunjangan_holiday,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endif
                      @if($quotation->lembur=="Normatif" || $quotation->lembur=="Flat")
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Lembur <b>( {{$quotation->lembur}} @if($quotation->lembur=="Normatif") : {{"Rp. ".number_format($quotation->lembur_per_jam,2,",",".")}} Per Jam @endif )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($quotation->lembur=="Normatif") {{"Rp. ".number_format(0,2,",",".")}} @elseif ($quotation->lembur=="Flat") {{"Rp. ".number_format($quotation->nominal_lembur,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endif
                      @if($quotation->penjamin=="BPJS")
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kecelakaan Kerja</td>
                          <td style="text-align:center">@if($quotation->resiko=="Sangat Rendah") 0,24 @elseif($quotation->resiko=="Rendah") 0,54 @elseif($quotation->resiko=="Sedang") 0,89 @elseif($quotation->resiko=="Tinggi") 1,27 @elseif($quotation->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kematian</td>
                          <td style="text-align:center">0,3 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotation->program_bpjs=="3 BPJS" || $quotation->program_bpjs=="4 BPJS")
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Hari Tua</td>
                          <td style="text-align:center">3,7 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->program_bpjs=="4 BPJS")
                        <tr class="">
                          <td style="text-align:center"></td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Pensiun</td>
                          <td style="text-align:center">2 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endif
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">BPJS Kesehatan </td>
                        <td style="text-align:center">4 %</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @else
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Takaful </td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      @endif
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Provisi Seragam </td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Provisi Peralatan </td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @php $nomorUrut++; @endphp
                      <tr class="">
                        <td style="text-align:center">{{$nomorUrut}}</td>
                        <td style="text-align:left" class="">Chemical </td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                        <td style="text-align:center">{{$quotation->persentase}} %</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="">Over Head Cost</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_ohc,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPn <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                        <td style="text-align:center">11 %</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                        <td style="text-align:center">-2 %</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan,2,",",".")}}</td>
                        @endforeach
                      </tr>             
                    </tbody>
                  </table>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table mt-3" >
                    <tbody>
                      <tr class="table-info">
                        <td class="text-center fw-bold" colspan="2" style="vertical-align: middle;">TOTAL KESELURUHAN</td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Total Biaya Per Personil</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalBiayaPerPersonil=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalBiayaPerPersonil+=$detailJabatan->total_personil;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalBiayaPerPersonil,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Total Biaya All Personil</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalBiayaPersonil=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalBiayaPersonil+=$detailJabatan->sub_total_personil;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalBiayaPersonil,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Grand Total Sebelum Pajak</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalGrandTotal=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalGrandTotal+=$detailJabatan->grand_total;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalGrandTotal,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">TOTAL INVOICE</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalInvoice=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalInvoice+=$detailJabatan->total_invoice;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalInvoice,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">PEMBULATAN</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalPembulatan=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalPembulatan+=$detailJabatan->pembulatan;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalPembulatan,2,",",".")}}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
Tunjangan hari raya (gaji pokok dibagi {{$quotation->provisi}}).		<br>
<i>Cover</i> 
@if($quotation->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotation->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotation->program_bpjs=="4 BPJS")
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
                        <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">COST STRUCTURE {{$data->kebutuhan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}  ( Provisi = {{$quotation->provisi}} )</th>
                      </tr>
                    </thead>              
                    <tbody>
                      <tr>
                        <td class="fw-bold">Structure</td>
                        <td class="text-center fw-bold">%</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">Jumlah Personil</td>
                        <td class="text-center fw-bold"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-center">{{$detailJabatan->jumlah_hc}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">1. BASE MANPOWER COST</td>
                        <td class="text-center fw-bold"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-center" class="text-center fw-bold">Unit/Month</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Upah/Gaji</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr>
                        <td>{{$tunjangan->nama}} &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-delete text-danger delete-tunjangan" data-nama="{{$tunjangan->nama}}"></i></a></td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}} &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-pencil text-warning edit-tunjangan" data-id="{{$detailJabatan->id}}" data-nama="{{$tunjangan->nama}}"></i></a></td>
                        @endforeach
                      </tr>
                      @endforeach
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</td>
                        <td class="text-center fw-bold"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</td>
                        <td class="text-center fw-bold"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</td>
                        @endforeach
                      </tr>
                      @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                      <tr>
                        <td>Provisi Tunjangan Hari Raya (THR)</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                      <tr>
                        <td>Provisi Kompensasi</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->tunjangan_holiday=="Flat" || $quotation->tunjangan_holiday=="Normatif")
                      <tr>
                        <td>Tunjangan Hari Libur Nasional</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->tunjangan_holiday=="Normatif") <b>Normatif</b> @else {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->lembur=="Flat" || $quotation->lembur=="Normatif")
                      <tr>
                        <td>Lembur</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->lembur=="Normatif") <b>Normatif</b> @else {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->penjamin=="BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Kecelakaan Kerja</td>
                          <td class="text-center">@if($quotation->resiko=="Sangat Rendah") 0,24 @elseif($quotation->resiko=="Rendah") 0,54 @elseif($quotation->resiko=="Sedang") 0,89 @elseif($quotation->resiko=="Tinggi") 1,27 @elseif($quotation->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Premi BPJS TK J. Kematian</td>
                          <td class="text-center">0,30 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotation->program_bpjs=="3 BPJS" || $quotation->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Hari Tua</td>
                          <td class="text-center">3,7 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Pensiun</td>
                          <td class="text-center">2 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        <tr>
                          <td>Premi BPJS Kesehatan</td>
                          <td class="text-center">4 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</td>
                          @endforeach
                        </tr>
                      @else
                        <tr>
                          <td>Takaful</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
                          @endforeach
                        </tr>
                      @endif
                      <tr>
                        <td>Provisi Seragam</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_kaporlap / 1000) * 1000),2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Peralatan</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_devices / 1000) * 1000),2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Chemical</td>
                        <td class="text-center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_chemical / 1000) * 1000),2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Exclude Base Manpower Cost</td>
                        <td class="text-center fw-bold"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">3. BIAYA PENGAWASAN DAN PELAKSANAAN LAPANGAN</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->total_ohc,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="" class="">Bunga Bank ( {{$quotation->top}} )</td>
                        <td style="text-align:center">{{$quotation->persen_bunga_bank}} %   &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-pencil text-warning edit-persen-bunga-bank"></i></a></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bunga_bank,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="" class="">Insentif</td>
                        <td style="text-align:center">{{$quotation->persen_insentif}} %  &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-pencil text-warning edit-persen-insentif"></i></a></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->insentif,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="" class="">Management Fee</td>
                        <td style="text-align:center">{{$quotation->persentase}} %</td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                        <tr class="">
                          <td style="text-align:right" class="fw-bold">PPn <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">11 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">-2 %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>      
                    </tbody>
                  </table>
                </div>
                <div class="table-responsive text-nowrap">
                  <table class="table mt-3" >
                    <tbody>
                      <tr class="table-info">
                        <td class="text-center fw-bold" colspan="2" style="vertical-align: middle;">TOTAL KESELURUHAN</td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Total Base Manpower Cost Per Month</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalBaseManPowerCostPerMonth=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalBaseManPowerCostPerMonth+=$detailJabatan->total_base_manpower;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalBaseManPowerCostPerMonth,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Total Exclude Base Manpower Per Month</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalExcludeBaseManPowerCostPerMonth=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalExcludeBaseManPowerCostPerMonth+=$detailJabatan->total_exclude_base_manpower;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalExcludeBaseManPowerCostPerMonth,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">Grand Total Sebelum Pajak</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalGrandTotal=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalGrandTotal+=$detailJabatan->grand_total_coss;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalGrandTotal,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">TOTAL INVOICE</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalInvoice=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalInvoice+=$detailJabatan->total_invoice_coss;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalInvoice,2,",",".")}}
                        </td>
                      </tr>
                      <tr class="">
                        <td class="text-end fw-bold" colspan="" style="vertical-align: middle;">PEMBULATAN</td>
                        <td class="text-end fw-bold">
                        @php
                          $totalPembulatan=0;
                        @endphp
                        @foreach($quotation->quotation_detail as $detailJabatan)
                        @php
                          $totalPembulatan+=$detailJabatan->pembulatan_coss;
                        @endphp
                        @endforeach
                        {{"Rp. ".number_format($totalPembulatan,2,",",".")}}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
                  <b>Upah pokok base on Umk 2024 </b> <br>
Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
@if($quotation->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotation->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotation->program_bpjs=="4 BPJS")
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
                    @if($quotation->ppn_pph_dipotong=="Management Fee")
                    <tbody>
                      <tr>
                        <td style="text-align:left">Nominal</td>
                        <td style="text-align:right">
                          @php
                          $totalNominal = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $totalNominal += $detailJabatan->total_invoice;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalNominal,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalNominalCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
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
                          foreach($quotation->quotation_detail as $detailJabatan){
                                $ppn += $detailJabatan->ppn;
                            }
                          @endphp
                          @if($ppn==0)
                          <b>PPN Ditanggung Customer</b>
                          @else
                            {{"Rp. ".number_format($ppn,2,",",".")}}
                          @endif
                        </td>
                        <td style="text-align:right">
                        @php
                          $ppnCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                                $ppnCoss += $detailJabatan->ppn_coss;
                              }
                          @endphp
                          
                          @if($ppnCoss==0)
                          <b>PPN Ditanggung Customer</b>
                          @else
                            {{"Rp. ".number_format($ppnCoss,2,",",".")}}
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Total Biaya</td>
                        <td style="text-align:right">
                        @php
                          $totalBiaya = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $totalBiaya += $detailJabatan->sub_total_personil;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiaya,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalBiayaCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $totalBiayaCoss += $detailJabatan->sub_total_personil;
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
                          {{number_format($gpm,2,",",".")}} %
                        </td>
                        <td class="fw-bold" style="text-align:right">
                        @php
                            $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
                          @endphp
                          {{number_format($gpmCoss,2,",",".")}} %
                        </td>
                      </tr>
                    </tbody>
                    @else
                    <tbody>
                      <tr>
                        <td style="text-align:left">Nominal</td>
                        <td style="text-align:right">
                          @php
                          $grandTotal = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $grandTotal += $detailJabatan->grand_total;
                          }
                          @endphp
                          {{"Rp. ".number_format($grandTotal,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $grandTotalCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $grandTotalCoss += $detailJabatan->grand_total_coss;
                          }
                        @endphp
                          {{"Rp. ".number_format($grandTotalCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">PPH</td>
                        <td style="text-align:right">
                        @php
                          $pph = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                                $pph += abs($detailJabatan->pph);
                            }
                          @endphp
                          {{"Rp. ".number_format($pph,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $pphCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                                $pphCoss += abs($detailJabatan->pph_coss);
                              }
                          @endphp
                          {{"Rp. ".number_format($pphCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Total Biaya</td>
                        <td style="text-align:right">
                        @php
                          $totalBiaya = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $totalBiaya += $detailJabatan->sub_total_personil;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiaya,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalBiayaCoss = 0;
                          foreach($quotation->quotation_detail as $detailJabatan){
                            $totalBiayaCoss += $detailJabatan->sub_total_personil;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiayaCoss,2,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Margin</td>
                        <td style="text-align:right">
                          @php
                            $margin = $grandTotal-$pph-$totalBiaya;
                          @endphp
                          {{"Rp. ".number_format($margin,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                            $marginCoss = $grandTotalCoss-$pphCoss-$totalBiayaCoss;
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
                          {{number_format($gpm,2,",",".")}} %
                        </td>
                        <td class="fw-bold" style="text-align:right">
                        @php
                            $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
                          @endphp
                          {{number_format($gpmCoss,2,",",".")}} %
                        </td>
                      </tr>
                    </tbody>
                    @endif
                    
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

<div class="modal modal-lg fade" id="basicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">Tambah Tunjangan</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <div class="input-group">
                <select id="quotation-detail" class="form-select">
                  <option value="">- Pilih Posisi -</option>
                  @foreach($quotation->quotation_detail as $detail)
                  @if($quotation->jumlah_site=="Multi Site")
                  <option value="{{$detail->id}}">{{$detail->nama_site}} - {{$detail->jabatan_kebutuhan}}</option> 
                  @else
                  <option value="{{$detail->id}}">{{$detail->jabatan_kebutuhan}}</option> 
                  @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="text" id="nama-tunjangan" class="form-control" placeholder="Masukkan Nama Tunjangan" />
              <label for="nama-tunjangan">Nama Tunjangan</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="text" id="nominal-tunjangan" class="form-control mask-nominal" placeholder="Masukkan Nominal" />
              <label for="nominal-tunjangan">Nominal Tunjangan</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" id="btn-save-tambah-item" class="btn btn-primary">Tambah Item</button>
      </div>
    </div>
  </div>
</div>

<!--/ Content -->
@endsection

@section('pageScript')  
<script src="{{ asset('public/assets/js/forms-selects.js') }}"></script>
<script src="{{ asset('public/assets/vendor/libs/select2/select2.js') }}"></script>

<script>

      $("#select2Multiple").select2({ width: '100%' });
      $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });

    let extra = 0;
    $('.mask-nominal').on("keyup", function(event) {    
    // When user select text in the document, also abort.
    var selection = window.getSelection().toString();
    if (selection !== '') {
      return;
    }

    // When the arrow keys are pressed, abort.
    if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
      if (event.keyCode == 38) {
        extra = 1000;
      } else if (event.keyCode == 40) {
        extra = -1000;
      } else {
        return;
      }

    }

    var $this = $(this);
    // Get the value.
    var input = $this.val();
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt(input, 10) : 0;
    input += extra;
    extra = 0;
    $this.val(function() {
      return (input === 0) ? "" : input.toLocaleString("id-ID");
    });
  });

  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();
      
    if(obj.penagihan == null || obj.penagihan == "" ){
      msg += "<b>Penagihan</b> belum dipilih </br>";
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

  $('.edit-biaya-monitoring').on('click',function(e){
    let quotationId = $(this).data('quotation_id');
    let detailId = $(this).data('quotation_detail_id');
    
    Swal.fire({
      title: "Masukkan nominal Biaya Monitoring dan Kontrol",
      input: "number",
      showCancelButton: true,
      confirmButtonText: "Simpan",
      showLoaderOnConfirm: true,
      preConfirm: async (nominal) => {
        try {
          let formData = {
            "quotationId":$(this).data('quotation_id'),
            "nominal":nominal,
            "detailId":$(this).data('quotation_detail_id'),
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

  $('#btn-save-tambah-item').on('click',function(){
      $(this).attr('disabled', true);
      let msg="";
      let namaTunjangan = $('#nama-tunjangan').val();
      let nominalTunjangan = $('#nominal-tunjangan').val();
      let quotationDetailId = $("#quotation-detail option:selected").val();

      if(quotationDetailId==null || quotationDetailId==""){
        msg += "<b>Posisi</b> belum dipilih </br>";
      };

      if(namaTunjangan==null || namaTunjangan==""){
        msg += "<b>Nama Tunjangan</b> belum diisi </br>";
      };

      if(nominalTunjangan==null || nominalTunjangan==""){
        msg += "<b>Nominal Tunjangan</b> belum diisi </br>";
      };

      if(msg!=""){
        Swal.fire({
              title: "Pemberitahuan",
              html: msg,
              icon: "warning",
            });
        $('#nama-tunjangan').val("");
        $('#nominal-tunjangan').val("");
        $("#quotation-detail").val("").change();
        $('#basicModal').modal('toggle');
        return null;
      };

      let formData = {
        "namaTunjangan":namaTunjangan,
        "nominalTunjangan":nominalTunjangan,
        "quotationDetailId":quotationDetailId,
        "id":{{$quotation->id}},
        "_token": "{{ csrf_token() }}"
      };

      $.ajax({
        type: "POST",
        url: "{{route('quotation.add-tunjangan')}}",
        data:formData,
        success: function(response){
          if(response=="Data Berhasil Ditambahkan"){
            location.reload();
          }else{
            Swal.fire({
              title: "Pemberitahuan",
              html: response,
              icon: "warning",
            });
            $('#nama-tunjangan').val("");
            $('#nominal-tunjangan').val("");
            $("#quotation-detail").val("").change();
          }
        },
        error:function(error){
          console.log(error);
          $('#nama-tunjangan').val("");
          $('#nominal-tunjangan').val("");
          $("#quotation-detail").val("").change();
        }
      });
    });

    $('body').on('click', '.delete-tunjangan', function() {
    let formData = {
      "nama":$(this).data('nama'),
      "quotation_id":{{$quotation->id}},
      "_token": "{{ csrf_token() }}"
    };

    $.ajax({
      type: "POST",
      url: "{{route('quotation.delete-tunjangan')}}",
      data:formData,
      success: function(response){
        location.reload();
      },
      error:function(error){
        console.log(error);
      }
    });
  })
  $('body').on('click', '.edit-tunjangan', function() {    
    Swal.fire({
      title: 'Masukkan Nominal Baru',
      input: 'number',
      inputPlaceholder: 'Contoh: 10000',
      inputAttributes: {
        min: 0
      },
      showCancelButton: true,
      confirmButtonText: 'Simpan',
      cancelButtonText: 'Batal',
      preConfirm: (nominal) => {
        if (!nominal || nominal < 0) {
          Swal.showValidationMessage('Nominal harus lebih besar dari 0');
        }
        return nominal;
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const nominalBaru = result.value;

        let formData = {
          "nama":$(this).data('nama'),
          "id":$(this).data('id'),
          "quotation_id":{{$quotation->id}},
          "nominal":nominalBaru,
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.edit-tunjangan')}}",
          data:formData,
          success: function(response){
            Swal.fire(
              'Berhasil!',
              'Nominal berhasil disimpan.',
              'success'
            );
            location.reload();
          },
          error:function(error){
            Swal.fire(
              'Gagal!',
              'Terjadi kesalahan saat menyimpan data.',
              'error'
            );
          }
        });
      }
    });
  });

  $('body').on('click', '.edit-persen-insentif', function() {    
    Swal.fire({
      title: 'Masukkan Persen Insentif',
      input: 'text',
        inputPlaceholder: 'Contoh: 2,5 atau 2.5',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: (inputValue) => {
        if (!inputValue) {
          Swal.showValidationMessage('Persen tidak boleh kosong');
          return;
        }

        // Ubah koma ke titik dan parse ke angka
        const sanitizedValue = inputValue.replace(',', '.');
        const persen = parseFloat(sanitizedValue);

        if (isNaN(persen) || persen <= 0) {
          Swal.showValidationMessage('Masukkan persen yang valid (contoh: 2,5 atau 2.5)');
        }
        
        return persen;
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const persenBaru = result.value;

        let formData = {
          "quotation_id":{{$quotation->id}},
          "persen":persenBaru,
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.edit-persen-insentif')}}",
          data:formData,
          success: function(response){
            Swal.fire(
              'Berhasil!',
              'Persen Insentif berhasil disimpan.',
              'success'
            );
            location.reload();
          },
          error:function(error){
            Swal.fire(
              'Gagal!',
              'Terjadi kesalahan saat menyimpan data.',
              'error'
            );
          }
        });
      }
    });
  });

  $('body').on('click', '.edit-persen-bunga-bank', function() {    
    Swal.fire({
      title: 'Masukkan Persen Bunga Bank',
      input: 'text',
        inputPlaceholder: 'Contoh: 2,5 atau 2.5',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: (inputValue) => {
        if (!inputValue) {
          Swal.showValidationMessage('Persen tidak boleh kosong');
          return;
        }

        // Ubah koma ke titik dan parse ke angka
        const sanitizedValue = inputValue.replace(',', '.');
        const persen = parseFloat(sanitizedValue);

        if (isNaN(persen) || persen <= 0) {
          Swal.showValidationMessage('Masukkan persen yang valid (contoh: 2,5 atau 2.5)');
        }
        
        return persen;
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const persenBaru = result.value;

        let formData = {
          "quotation_id":{{$quotation->id}},
          "persen":persenBaru,
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.edit-persen-bunga-bank')}}",
          data:formData,
          success: function(response){
            Swal.fire(
              'Berhasil!',
              'Persen Bunga Bank berhasil disimpan.',
              'success'
            );
            location.reload();
          },
          error:function(error){
            Swal.fire(
              'Gagal!',
              'Terjadi kesalahan saat menyimpan data.',
              'error'
            );
          }
        });
      }
    });
  });

</script>
@endsection