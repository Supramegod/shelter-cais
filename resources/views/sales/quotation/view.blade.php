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
            </h5>
            <p class="text-body">{{$master->nama_perusahaan}}</p>
            <p class="text-body">Revisi Ke : {{$master->revisi}}</p>
          </div>
          <div class="d-flex align-content-center flex-wrap gap-2">
            <button class="btn btn-warning" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-file-refresh"></i>&nbsp; Ajukan Ulang ( Ubah )</button>
            @if(in_array(Auth::user()->role_id,[2,31,32,33,50,51,52,97,98,99,100]))
            <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
            @endif
            <button id="cetak-quotation" class="btn btn-info" @if($data->is_aktif==0) disabled @endif><i class="mdi mdi-printer"></i>&nbsp; Print</button>
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
          <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
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
          <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
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
          <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
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
          <h6 class="m-0"><a href="{{route('leads.view',$master->leads_id)}}">Edit</a></h6>
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
            <a href=" javascript:void(0)">Edit</a>
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
                      @foreach($listHPP as $ihpp => $hpp)
                        <tr>
                          <td style="text-align:center">{{$ihpp+1}}</td>
                          <td style="text-align:left">{{$hpp->structure}}</td>
                          <td style="text-align:center">{{$hpp->percentage}}</td>
                          <td style="text-align:right">Rp {{number_format($hpp->nominal,0,",",".")}}</td>
                        </tr>
                      @endforeach                
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-coss" role="tabpanel">
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    <thead class="text-center">
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
                      @foreach($listCS as $ics => $cs)
                        <tr>
                          <td style="text-align:center">{{$ics+1}}</td>
                          <td style="text-align:left">{{$cs->structure}}</td>
                          <td style="text-align:center">{{$cs->percentage}}</td>
                          <td style="text-align:right">Rp {{number_format($cs->nominal,0,",",".")}}</td>
                        </tr>
                      @endforeach                
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-gpm" role="tabpanel">
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    <thead class="text-center">
                      <tr class="table-success">
                        <th>No.</th>
                        <th>Keterangan</th>
                        <th>HPP</th>
                        <th>Harga Jual</th>
                      </tr>
                    </thead>              
                    <tbody>
                      @foreach($listGpm as $igpm => $gpm)
                        <tr>
                          <td style="text-align:center">{{$ics+1}}</td>
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
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-chemical" aria-controls="navs-top-chemical" aria-selected="false" tabindex="-1">
                  Chemical
                </button>
              </li>
              <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <div class="tab-pane fade active show" id="navs-top-kaporlap" role="tabpanel">
              <div class="row mb-5">
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="#" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Kaporlap</span>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
                    @foreach($listJenisKaporlap as $jenisKaporlap)
                    <thead class="text-center">
                      <tr class="table-primary">
                        <th rowspan="2" style="vertical-align: middle;">{{$jenisKaporlap->jenis_barang}}</th>
                        <th rowspan="2" style="vertical-align: middle;">Harga / Unit</th>
                        <th colspan="2">Kebutuhan</th>
                      </tr>
                      <tr class="table-primary">
                        <th>Security Guard</th>
                        <th>SC</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($listKaporlap as $kaporlap)
                      @if($kaporlap->jenis_barang == $jenisKaporlap->jenis_barang)
                        <tr>
                          <td>{{$kaporlap->nama}}</td>
                          <td style="text-align:right">Rp {{number_format($kaporlap->harga,0,",",".")}}</td>
                          <td style="text-align:center">{{$kaporlap->jumlah_sg}}</td>
                          <td style="text-align:center">{{$kaporlap->jumlah_sc}}</td>
                        </tr>
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-ohc" role="tabpanel">
              <div class="row mb-5">
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="#" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit OHC</span>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
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
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-devices" role="tabpanel">
              <div class="row mb-5">
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="#" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Devices</span>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
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
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="navs-top-chemical" role="tabpanel">
              <div class="row mb-5">
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="#" class="btn btn-primary btn-next w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Edit Chemical</span>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="table-responsive text-nowrap">
                  <table class="table" >
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
                      @endif
                      @endforeach
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
      <div class="col-12 col-lg-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center pb-0">
          <h5 class="card-title m-0">Perjanjian Kerjasama</h5>
            <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
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