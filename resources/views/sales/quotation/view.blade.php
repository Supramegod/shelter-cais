@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y"> 
  <div class="col-12 col-lg-12">
    <div class="card mb-2">
      <div class="card-header pb-1">
        <h5 class="card-title m-0">Quotation Information</h5>
      </div>
      <div class="card-body pt-1">
        <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
          <div class="d-flex flex-column justify-content-center">
            <h5 class="mb-1 mt-3">
              {{$data->nomor}}
              <!-- <span class="badge bg-label-success me-2 ms-2 rounded-pill">Paid</span> -->
               @if($data->quotation_status !="" && $data->quotation_status !=null )
               <span class="badge bg-label-warning rounded-pill">{{$data->quotation_status}}</span>
               @endif
               @if($data->success_status !="" && $data->success_status !=null )
               <span class="badge bg-label-success rounded-pill">{{$data->success_status}}</span>
               @endif
               @if($data->info_status !="" && $data->info_status !=null )
               <span class="badge bg-label-success rounded-pill">{{$data->success_status}}</span>
               @endif

               @if($master->step != 100)
                <span class="badge bg-label-warning rounded-pill">Data Belum Terisi Lengkap</span>
               @endif
            </h5>
            <p class="text-body">{{$master->nama_perusahaan}}</p>
            <p class="text-body">Revisi Ke : {{$master->revisi}}</p>
          </div>
          <div class="d-flex align-content-center flex-wrap gap-2">
            @if($master->step != 100)
            <a href="{{route('quotation.step',['id'=>$master->id,'step'=>$master->step])}}" class="btn btn-primary"><i class="mdi mdi-list-box-outline"></i>&nbsp; Lanjutkan Pengisian</a>
            @else
            <button class="btn btn-warning" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-file-refresh"></i>&nbsp; Ajukan Ulang ( Ubah )</button>
            @if(in_array(Auth::user()->role_id,[2,31,32,33,50,51,52,97,98,99,100]))
            <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
            @endif
            <button id="cetak-quotation" class="btn btn-info" @if($data->is_aktif==0) disabled @endif><i class="mdi mdi-printer"></i>&nbsp; Print</button>
            @endif
            <button id="delete-quotation" class="btn btn-danger" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-trash-can"></i>&nbsp;  Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Details Table -->

  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Quotation Info</h5>
        @if($master->step == 100 && $data->is_aktif == 0)
        <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'1','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Jumlah Site</label>
              <input type="text" value="{{$master->jumlah_site}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Jenis Kontrak</label>
              <input type="text" value="{{$master->jenis_kontrak}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Mulai Kontrak</label>
              <input type="text" value="{{$master->smulai_kontrak}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Kontrak Selesai</label>
              <input type="text" value="{{$master->skontrak_selesai}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Tanggal Penempatan</label>
              <input type="text" value="{{$master->stgl_penempatan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Salary Rule</label>
              <input type="text" value="{{$master->salary_rule}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Dibuat Oleh</label>
              <input type="text" value="{{$master->created_by}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Dibuat Tanggal</label>
              <input type="text" value="{{$master->screated_at}}" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Service Info</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'2','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Kebutuhan</label>
              <input type="text" value="{{$data->kebutuhan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Entitas</label>
              <input type="text" value="{{$data->company}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Provinsi</label>
              <input type="text" value="{{$data->provinsi}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Kabupaten / Kota</label>
              <input type="text" value="{{$data->kota}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Upah</label>
              <input type="text" value="{{$data->upah}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Nominal Upah</label>
              <input type="text" value="{{$data->nominal_upah}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Manajemen Fee</label>
              <input type="text" value="{{$data->manajemen_fee}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Persentase</label>
              <input type="text" value="{{$data->persentase}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Jenis Perusahaan</label>
              <input type="text" value="{{$data->jenis_perusahaan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Resiko</label>
              <input type="text" value="{{$data->resiko}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">BPJS</label>
              <input type="text" value="{{$data->program_bpjs}}" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Quotation details</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'3','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="table-responsive overflow-hidden table-data card-datatable table-responsive">
          <table id="table-data" class="dt-column-search table w-100 table-hover datatables-quotation-details" style="text-wrap: nowrap;">
            <thead class="table-light">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Kebutuhan</th>
                    <th class="text-center">Nama Posisi/Jabatan</th>
                    <th class="text-center">Jumlah Headcount</th>
                </tr>
            </thead>
            <tbody>
                {{-- data table ajax --}}
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end align-items-center m-3 p-1">
          <div class="order-calculations">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-heading">Total Headcount : {{$data->totalHc}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Customer details</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('leads.view',$master->leads_id)}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-start align-items-center mb-4">
            <div class="d-flex flex-column">
              <a href="javascript:void(0)">
                <h6 class="mb-1">{{$leads->nama_perusahaan}}</h6>
              </a>
              <small>Customer ID: {{$leads->nomor}}</small>
            </div>
          </div>
          <div class="d-flex justify-content-between">
            <h6 class="mb-2">PIC info</h6>
          </div>
          <p class="mb-0">PIC : {{$leads->pic}}</p>
          <p class="mb-0">Jabatan : {{$leads->jabatan}}</p>
          <p class="mb-0">No. Telp : {{$leads->no_telp}}</p>
          <p class="mb-0">Email : {{$leads->email}}</p>
          
          <div class="mt-3 d-flex justify-content-between">
            <h6 class="mb-2">CRM / RO info</h6>
          </div>
          <p class="mb-1">CRM : {{$leads->crm}}</p>
          <p class="mb-1">RO : {{$leads->ro}}</p>
        </div>
      </div>

       <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Aplikasi Pendukung</h6>
          <h6 class="m-0">
          @if($master->step == 100 && $data->is_aktif == 0)
          <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'6','edit'=>1])}}">Edit</a>
          @endif
          </h6>
        </div>
        <div class="card-body">
          <div class="table-responsive text-nowrap">
            <table class="table table-hover">
              <tbody class="table-border-bottom-0">
                @foreach($aplikasiPendukung as $value)
                <tr>
                  <td>
                    <img src="{{$value->link_icon}}" alt="{{$value->aplikasi_pendukung}}" style="max-width:60px">
                  </td>
                  <td><span class="fw-medium">{{$value->aplikasi_pendukung}}</span></td>
                </tr>
                @endforeach

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Cost Structure</h5>
        </div>
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
                        <tr>
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
                        </tr>
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
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Cost Structure</h5>
        </div>
        <div class="card-body">
        <div class="card-header p-0">
          <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill" role="tablist">
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-kaporlap" aria-controls="navs-top-kaporlap" aria-selected="true">
                  Kaporlap
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-ohc" aria-controls="navs-top-ohc" aria-selected="false" tabindex="-1">
                  OHC
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-devices" aria-controls="navs-top-devices" aria-selected="false" tabindex="-1">
                  Devices
                </button>
              </li>
              @if($data->kebutuhan_id != 2)
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-chemical" aria-controls="navs-top-chemical" aria-selected="false" tabindex="-1">
                  Chemical
                </button>
              </li>
              @endif
              <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <div class="tab-pane fade active show" id="navs-top-kaporlap" role="tabpanel">
              <div class="row mb-5">
              @if($master->step == 100 && $data->is_aktif == 0)
              <div class="col-12 d-flex justify-content-between">
                <div></div>
                <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'7','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Kaporlap</span>
                </a>
              </div>
              @endif
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @php
                    $totalKaporlap = 0;
                    @endphp
                    @foreach($listJenisKaporlap as $jenisKaporlap)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th>{{$jenisKaporlap->jenis_barang}}</th>
                        <th>Harga / Unit</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listKaporlap as $kaporlap)
                      @if($kaporlap->jenis_barang == $jenisKaporlap->jenis_barang)
                        <tr>
                          <td>{{$kaporlap->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($kaporlap->harga,0,",",".")}}</td>
                          <td style="text-align:center">{{$kaporlap->jumlah}}</td>
                        </tr>

                        @php
                          $totalKaporlap += ($kaporlap->jumlah*$kaporlap->harga);
                        @endphp
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                    <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td style="text-align:right">Rp {{number_format($totalKaporlap,0,",",".")}}</td>
                      <td class="total-semua" style="text-align:right"></td>
                    </tr>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-ohc" role="tabpanel">
              <div class="row mb-5">
              @if($master->step == 100 && $data->is_aktif == 0)
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'8','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit OHC</span>
                  </a>
                </div>
                @endif
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @php
                      $totalOhc = 0;
                    @endphp
                    @foreach($listJenisOhc as $jenisOhc)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th style="vertical-align: middle;">{{$jenisOhc->jenis_barang}}</th>
                        <th style="vertical-align: middle;">Harga / Unit</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listOhc as $ohc)
                      @if($ohc->jenis_barang == $jenisOhc->jenis_barang)
                        <tr>
                          <td>{{$ohc->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($ohc->harga,0,",",".")}}</td>
                          <td style="text-align:center">{{$ohc->jumlah}}</td>
                        </tr>
                        @php
                          $totalOhc += ($ohc->jumlah*$ohc->harga);
                        @endphp
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                    <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td style="text-align:right">Rp {{number_format($totalOhc,0,",",".")}}</td>
                      <td class="total-semua" style="text-align:right"></td>
                    </tr>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-devices" role="tabpanel">
              <div class="row mb-5">
              @if($master->step == 100 && $data->is_aktif == 0)
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'9','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Devices</span>
                  </a>
                </div>
              @endif
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @php
                      $totalDevices = 0;
                    @endphp
                    @foreach($listJenisDevices as $jenisDevices)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th style="vertical-align: middle;">{{$jenisDevices->jenis_barang}}</th>
                        <th style="vertical-align: middle;">Harga / Unit</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listDevices as $devices)
                      @if($devices->jenis_barang == $jenisDevices->jenis_barang)
                        <tr>
                          <td>{{$devices->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($devices->harga,0,",",".")}}</td>
                          <td style="text-align:center">{{$devices->jumlah}}</td>
                        </tr>
                        @php
                          $totalDevices += ($devices->jumlah*$devices->harga);
                        @endphp
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                    <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td style="text-align:right">Rp {{number_format($totalDevices,0,",",".")}}</td>
                      <td class="total-semua" style="text-align:right"></td>
                    </tr>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            @if($data->kebutuhan_id != 2)
            <div class="tab-pane fade" id="navs-top-chemical" role="tabpanel">
              <div class="row mb-5">
              @if($master->step == 100 && $data->is_aktif == 0)
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'10','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Chemical</span>
                  </a>
                </div>
              @endif
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @php
                      $totalChemical = 0;
                    @endphp
                    @foreach($listJenisChemical as $jenisChemical)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th style="vertical-align: middle;">{{$jenisChemical->jenis_barang}}</th>
                        <th style="vertical-align: middle;">Harga / Unit</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listChemical as $chemical)
                      @if($chemical->jenis_barang == $jenisChemical->jenis_barang)
                        <tr>
                          <td>{{$chemical->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($chemical->harga,0,",",".")}}</td>
                          <td style="text-align:center">{{$chemical->jumlah}}</td>
                        </tr>
                        @php
                          $totalChemical += ($chemical->jumlah*$chemical->harga);
                        @endphp
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                    <tbody>
                    <tr class="table-success">
                      <td><b>TOTAL</b> </td>
                      <td style="text-align:right">Rp {{number_format($totalChemical,0,",",".")}}</td>
                      <td></td>
                    </tr>
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        </div>
      </div>
      <div class="col-12 col-lg-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center pb-0">
          <h5 class="card-title m-0">Perjanjian Kerjasama</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'12','edit'=>1])}}">Edit</a></h6>
            @endif
          </div>
          <div class="card-body pt-0">
            <div class="row mt-5">
              <div class="table-responsive overflow-hidden table-data">
                <table id="table-data-perjanjian" class="dt-column-search table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Nomor</th>
                            <th class="text-center">Perjanjian</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
              </div>
            </div>
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
  $('#table-data').DataTable({
      scrollX: true,
      "bPaginate": false,
      "bLengthChange": false,
      "bFilter": false,
      "bInfo": false,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('quotation.list-detail-hc') }}",
          data: function (d) {
              d.quotation_kebutuhan_id = {{$data->id}};
          },
      },   
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },{
          data : 'kebutuhan',
          name : 'kebutuhan',
          className:'text-center'
      },{
          data : 'jabatan_kebutuhan',
          name : 'jabatan_kebutuhan',
          className:'text-center'
      },{
          data : 'jumlah_hc',
          name : 'jumlah_hc',
          className:'text-center'
      }],
      "language": datatableLang,
    });

  $('body').on('click', '#delete-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menghapus data ini ?",
      showCancelButton: true,
      confirmButtonText: "Hapus",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.delete-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil dihapus.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href="{{route('quotation')}}";
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });
  $('body').on('click', '#approve-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menyetujui data ini ?",
      showCancelButton: true,
      confirmButtonText: "Approve",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.approve-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil disetujui.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });
  $('body').on('click', '#cetak-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin mencetak dokumen ini ?",
      showCancelButton: true,
      confirmButtonText: "Cetak",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        alert("berhasil mencetak");
        location("reload");
      }
    });
  });

  $('#table-data-perjanjian').DataTable({
    scrollX: true,
    "bPaginate": false,
    "bFilter": false,
    "bInfo": false,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('quotation.list-quotation-kerjasama') }}",
          data: function (d) {
              d.quotation_id = {{$master->id}};
          },
      },   
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },{
          data : 'nomor',
          name : 'nomor',
          className:'text-center',
          width: "10%",
      },{
          data : 'perjanjian',
          name : 'perjanjian',
          width: "70%",
      }],
      "language": datatableLang,
    });
  
</script>
@endsection