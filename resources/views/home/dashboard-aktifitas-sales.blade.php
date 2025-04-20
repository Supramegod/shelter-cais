@extends('layouts.master')
@section('title','Dashboard Aktifitas Sales')
@section('pageStyle')
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://pivottable.js.org/dist/pivot.css">
<script src="https://pivottable.js.org/dist/pivot.js"></script>
<script src="https://pivottable.js.org/dist/plotly_renderers.js"></script>
<style>
    #table-data tbody tr {
        cursor: pointer;
    }
    #table-data-bulanan tbody tr {
        cursor: pointer;
    }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100" id="aktifitasSalesHariIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-hari-ini') }}','AKTIFITAS SALES HARI INI')">
              <div class="card-body">
                  <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                      <span class="avatar-initial rounded bg-label-primary"
                      ><i class="mdi mdi-finance mdi-20px"></i
                      ></span>
                  </div>
                  <h4 class="ms-1 mb-0 display-6">{{$aktifitasSalesHariIni}}</h4>
                  </div>
                  <p class="mb-0 text-heading ">Aktifitas Sales Hari Ini</p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100" id="aktifitasSalesMingguIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-minggu-ini') }}','AKTIFITAS SALES MINGGU INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-finance mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$aktifitasSalesMingguIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Minggu Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100" id="aktifitasSalesBulanIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-bulan-ini') }}','AKTIFITAS SALES BULAN INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$aktifitasSalesBulanIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Bulan Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100" id="aktifitasSalesTahunIni" onclick="openNormalDataTableModal('{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-tahun-ini') }}','AKTIFITAS SALES TAHUN INI')">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-info"
                    ><i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$aktifitasSalesTahunIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Tahun Ini</p>
            </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 mb-5">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Laporan Bulanan Aktifitas Penjualan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="cabangBulanan" class="form-label">Cabang</label>
                            <select class="form-control" id="cabangBulanan" name="cabang">
                                <option value="">- Semua Cabang -</option>
                                @foreach($cabangList as $cabang)
                                    <option value="{{ $cabang->id }}">{{ $cabang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="bulanBulanan" class="form-label">Bulan</label>
                            <select class="form-control" id="bulanBulanan" name="bulan">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tahunBulanan" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="tahunBulanan" name="tahun" min="2000" max="2100" value="{{ date('Y') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="filterButtonBulanan">
                                <i class="mdi mdi-magnify"></i> Filter
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive overflow-hidden table-data-bulanan">
                        <table id="table-data-bulanan" class="dt-column-search table w-100 table-hover" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="3">No.</th>
                                    <th class="text-center" rowspan="3">Nama Sales</th>
                                    <th class="text-center" rowspan="3">Cabang</th>
                                    <th class="text-center" colspan="16" id="label-laporan-bulanan"></th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="2">Jumlah Appt</th>
                                    <th class="text-center" colspan="2">Jumlah visit</th>
                                    <th class="text-center" colspan="2">Jumlah Penawaran</th>
                                    <th class="text-center" colspan="2">Jumlah SPK ( Closed )</th>
                                    <th class="text-center" colspan="2">% Conversion Appt to Visit</th>
                                    <th class="text-center" colspan="2">% Conversion Visit to Quot</th>
                                    <th class="text-center" colspan="2">% Conversion Quot to Closed</th>
                                    <th class="text-center" colspan="2">Jumlah Aktual Penempatan</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 8; $i++)
                                        <th class="text-center label-bulan-lalu bg-warning"></th>
                                        <th class="text-center label-bulan-sekarang"></th>
                                    @endfor
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
    <div class="row gy-4 mb-5">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Laporan Mingguan Aktifitas Penjualan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="cabang" class="form-label">Cabang</label>
                            <select class="form-control" id="cabang" name="cabang">
                                <option value="">- Semua Cabang -</option>
                                @foreach($cabangList as $cabang)
                                    <option value="{{ $cabang->id }}">{{ $cabang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select class="form-control" id="bulan" name="bulan">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="tahun" name="tahun" min="2000" max="2100" value="{{ date('Y') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="filterButton">
                                <i class="mdi mdi-magnify"></i> Filter
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="3">No.</th>
                                    <th class="text-center" rowspan="3">Nama Sales</th>
                                    <th class="text-center" rowspan="3">Cabang</th>
                                    <th class="text-center" colspan="16" id="label-laporan-mingguan"></th>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="4">W1</th>
                                    <th class="text-center" colspan="4">W2</th>
                                    <th class="text-center" colspan="4">W3</th>
                                    <th class="text-center" colspan="4">W4</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 4; $i++)
                                        <th class="text-center">Appt</th>
                                        <th class="text-center">Visit</th>
                                        <th class="text-center">Quot</th>
                                        <th class="text-center">SPK</th>
                                    @endfor
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
    <div class="row gy-4 mb-5">
        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Aktifitas Sales Bulan ini</h5>
                    <div class="card-header-elements ms-auto py-0 dropdown">
                        <button
                            type="button"
                            class="btn dropdown-toggle hide-arrow p-0"
                            id="heat-chart-dd"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="heat-chart-dd">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($jumlahAktifitas))
                        <div class="text-center">
                            <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center">Tidak ditemukan data</p>
                    @else
                        <canvas id="polarChart" class="chartjs" data-height="337"></canvas>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
          <div class="card">
            <h5 class="card-header">By Tipe Aktifitas Bulan Ini</h5>
            <div class="card-body">
                @if(empty($jumlahAktifitasTipe))
                    <div class="text-center">
                        <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                    </div>
                    <p class="text-center">Tidak ditemukan data</p>
                @else
                <div class="d-flex justify-content-center">
                    <div class="col-lg-8">
                        <canvas id="doughnutChart" class="chartjs mb-4" data-height="100" height="200"></canvas>
                    </div>
                </div>
                @endif
            </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
          <div class="card">
            <h5 class="card-header">By Status Leads Bulan Ini</h5>
            <div class="card-body">
                @if(empty($jumlahAktifitasStatusLeads))
                    <div class="text-center">
                        <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                    </div>
                    <p class="text-center">Tidak ditemukan data</p>
                @else
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-8">
                            <canvas id="doughnutChartStatus" class="chartjs mb-4" data-height="100" height="200"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <h5 class="card-header">Visit Bulan Ini</h5>
                <div class="card-body">
                    @if(empty($jumlahAktifitasVisit))
                        <div class="text-center">
                            <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center">Tidak ditemukan data</p>
                    @else
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-8">
                                <canvas id="doughnutChartVisit" class="chartjs mb-4" data-height="100" height="200"></canvas>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <div>
                    <h5 class="card-title mb-0">Aktifitas Sales Bulan Ini</h5>
                    </div>
                </div>
                <div class="card-body pt-2">
                    @if(empty($aktifitasSalesPerTanggal))
                        <div class="text-center">
                            <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center">Tidak ditemukan data</p>
                    @else
                        <canvas id="lineChart" class="chartjs" data-height="500" height="400"></canvas>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Aktifitas By Tipe Bulan ini</h5>
                    <div class="card-action-element ms-auto py-0">
                        <div class="dropdown">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($aktifitasSalesByTipePerTanggal))
                        <div class="text-center">
                            <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center">Tidak ditemukan data</p>
                    @else
                    <canvas id="barChart" class="chartjs" data-height="400" height="400"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="card w-100">
        <div class="card-header header-elements">
            <h5 class="card-title mb-0">Pivot Summary Data Aktifitas Sales</h5>
            <div class="card-header-elements ms-auto py-0 dropdown">
            <button
                type="button"
                class="btn dropdown-toggle hide-arrow p-0"
                id="heat-chart-dd"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="mdi mdi-dots-vertical"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="heat-chart-dd" id="dropdown-pivot-summary">
                <button class="dropdown-item" id="saveToExcel"><i class="mdi mdi-file-excel"></i> Save to Excel</button>
                <button class="dropdown-item" id="saveConfig"><i class="mdi mdi-content-save"></i> Save Config</button>
                <button class="dropdown-item" id="clearConfig"><i class="mdi mdi-delete"></i> Clear Config</button>
            </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="tanggalDari" class="form-label">Tanggal Dari</label>
                    <input type="date" class="form-control" id="tanggalDari" name="tanggalDari">
                </div>
                <div class="col-md-4">
                    <label for="tanggalSampai" class="form-label">Tanggal Sampai</label>
                    <input type="date" class="form-control" id="tanggalSampai" name="tanggalSampai">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" id="filterButton">
                        <i class="mdi mdi-magnify"></i> Filter
                    </button>
                </div>
                @php
                    $currentMonth = date('Y-m');
                    $startDate = $currentMonth . '-01';
                    $endDate = date('Y-m-t', strtotime($currentMonth));
                @endphp
                <script>
                    document.getElementById('tanggalDari').value = '{{ $startDate }}';
                    document.getElementById('tanggalSampai').value = '{{ $endDate }}';
                </script>
            </div>
            <div class="row">
            <div id="output" style="overflow-x: auto; width: 100%;"></div>
            </div>
        </div>
      </div>
    </div> -->
</div>
@endsection

@section('pageScript')
<script src="{{ asset('public/assets/vendor/libs/chartjs/chartjs.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.6/xlsx.full.min.js"></script>
    <script>
          const purpleColor = '#836AF9',
            yellowColor = '#ffe800',
            cyanColor = '#28dac6',
            orangeColor = '#FF8132',
            orangeLightColor = '#ffcf5c',
            oceanBlueColor = '#299AFF',
            greyColor = '#4F5D70',
            greyLightColor = '#EDF1F4',
            blueColor = '#2B9AFF',
            blueLightColor = '#84D0FF',
            redColor = '#FF6384',
            greenColor = '#4BC0C0',
            pinkColor = '#FF9F40',
            limeColor = '#B9FF00',
            tealColor = '#00FFB9',
            magentaColor = '#FF00B9',
            violetColor = '#B900FF',
            indigoColor = '#4B00FF',
            amberColor = '#FFC107',
            deepOrangeColor = '#FF5722';
            let cardColor, headingColor, labelColor, borderColor, legendColor;
            const backgroundColor= [purpleColor,yellowColor,cyanColor,orangeColor,orangeLightColor,oceanBlueColor,greyColor,greyLightColor,blueColor,blueLightColor,redColor,greenColor,pinkColor,limeColor,tealColor,magentaColor,violetColor,indigoColor,amberColor,deepOrangeColor];
            const bankWarna = @json($warna);
            function getRandomColor() {
                return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`;
            }

        const polarChart = document.getElementById('polarChart');
            if (polarChart) {
                const polarChartVar = new Chart(polarChart, {
                type: 'polarArea',
                data: {
                    labels: @json($sales),
                    datasets: [
                    {
                        label: 'Aktifitas',
                        backgroundColor: backgroundColor,
                        data: @json($jumlahAktifitas),
                        borderWidth: 0
                    }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                    duration: 500
                    },
                    scales: {
                    r: {
                        ticks: {
                        display: false,
                        color: labelColor
                        },
                        grid: {
                        display: false
                        }
                    }
                    },
                    plugins: {
                    tooltip: {
                        // Updated default tooltip UI
                        rtl: isRtl,
                        backgroundColor: cardColor,
                        titleColor: headingColor,
                        bodyColor: legendColor,
                        borderWidth: 1,
                        borderColor: borderColor
                    },
                    legend: {
                        rtl: isRtl,
                        position: 'bottom',
                        labels: {
                        usePointStyle: true,
                        padding: 25,
                        boxWidth: 8,
                        boxHeight: 8,
                        color: legendColor,
                        font: {
                            family: 'Inter'
                        }
                        }
                    }
                    }
                }
                });
            }

  const doughnutChart = document.getElementById('doughnutChart');
  if (doughnutChart) {
    const doughnutChartVar = new Chart(doughnutChart, {
      type: 'doughnut',
      data: {
        labels: @json($tipe),
        datasets: [
          {
            data: @json($jumlahAktifitasTipe),
            backgroundColor: backgroundColor,
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '68%',
        plugins: {
          legend: {
            position: 'right'
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value;
                return output;
              }
            },
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  const doughnutChartStatus = document.getElementById('doughnutChartStatus');
  if (doughnutChartStatus) {
    const doughnutChartStatusVar = new Chart(doughnutChartStatus, {
      type: 'doughnut',
      data: {
        labels: @json($statusLeads),
        datasets: [
          {
            data: @json($jumlahAktifitasStatusLeads),
            backgroundColor: backgroundColor,
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '68%',
        plugins: {
          legend: {
            position: 'right'
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value;
                return output;
              }
            },
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  const doughnutChartVisit = document.getElementById('doughnutChartVisit');
  if (doughnutChartVisit) {
    const doughnutChartVisitVar = new Chart(doughnutChartVisit, {
      type: 'doughnut',
      data: {
        labels: @json($jenisVisit),
        datasets: [
          {
            data: @json($jumlahAktifitasVisit),
            backgroundColor: backgroundColor,
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '68%',
        plugins: {
          legend: {
            position: 'right'
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value;
                return output;
              }
            },
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  const arrAct = @json($aktifitasSalesPerTanggal);
  let arrDataSet = [];
  arrAct.forEach(function(element, index) {

    let objAct = {
        data: element.jumlah_aktifitas,
        label: element.user,
        borderColor: bankWarna[index],
        tension: 0.5,
        pointStyle: 'circle',
        backgroundColor: bankWarna[index],
        fill: false,
        pointRadius: 1,
        pointHoverRadius: 5,
        pointHoverBorderWidth: 5,
        pointBorderColor: 'transparent',
        pointHoverBorderColor: cardColor,
        pointHoverBackgroundColor: bankWarna[index]
    }
    arrDataSet.push(objAct);
  });

  const lineChart = document.getElementById('lineChart');
  if (lineChart) {
    const lineChartVar = new Chart(lineChart, {
      type: 'line',
      data: {
        labels: [],
        datasets: arrDataSet
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            scaleLabel: {
              display: true
            },
            min: 0,
            max: 25,
            ticks: {
              color: labelColor,
              stepSize: 5
            },
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            }
          }
        },
        plugins: {
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            position: 'top',
            align: 'start',
            rtl: isRtl,
            labels: {
              font: {
                family: 'Inter'
              },
              usePointStyle: true,
              padding: 35,
              boxWidth: 6,
              boxHeight: 6,
              color: legendColor
            }
          }
        }
      }
    });
  }

  const actByTipe = @json($aktifitasSalesByTipePerTanggal);
  let dataSetBar = [];
  actByTipe.forEach(function(element, index) {
    let arrData = [];
    element.jumlah_aktifitas.forEach(eld => {
        arrData.push(eld.aktifitas);
    });

    let objBar = {
        data: arrData,
        label:element.tipe,
        backgroundColor: bankWarna[index],
        borderColor: 'transparent',
        maxBarThickness: 15,
        borderRadius: {
            topRight: 15,
            topLeft: 15
        }
        }
    dataSetBar.push(objBar);
  });

  const barChart = document.getElementById('barChart');
  if (barChart) {
    const barChartVar = new Chart(barChart, {
      type: 'bar',
      data: {
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,31],
        datasets: dataSetBar
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
        },
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: 20,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: 5,
              color: labelColor
            }
          }
        }
      }
    });
  }

  const chartList = document.querySelectorAll('.chartjs');
  chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
  });
    </script>

<!-- <script>
    function fetchPivotData(tanggalDari, tanggalSampai) {
        $('#output').html('<div class="d-flex justify-content-center align-items-center" style="height: 200px;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $.ajax({
            url: '{{ route('dashboard.aktifitas-sales.pivot.aktifitas-sales') }}',
            method: 'GET',
            data: {
                tanggalDari: tanggalDari,
                tanggalSampai: tanggalSampai
            },
            dataType: 'json',
            success: function(pivotData) {
                if (pivotData.length === 0) {
                    $('#output').html(`
                        <div class="text-center">
                            <img src="{{ asset('public/assets/img/empty_data.png') }}" alt="Tidak ditemukan data" class="img-fluid" style="max-width: 200px;">
                        </div>
                        <p class="text-center">Tidak ditemukan data</p>
                    `);
                    return;
                }
                pivotData.forEach(element => {
                    Object.keys(element).forEach(key => {
                        const newKey = key.replaceAll('_', ' ');
                        element[newKey] = element[key];
                        if (newKey !== key){
                            delete element[key];
                        }
                    });
                });


                var derivers = $.pivotUtilities.derivers;
                var renderers = $.extend($.pivotUtilities.renderers, $.pivotUtilities.plotly_renderers);

                // Render PivotTable with Plotly Renderer
                // $("#output").pivotUI(pivotData, {
                //     renderers: renderers,
                //     rendererName: "Table",
                //     aggregatorName: "Count"
                // });
                let savedConfig = localStorage.getItem("pivotConfig");
                let localConfig = {
                    renderers: renderers,
                    rendererName: "Table",
                    aggregatorName: "Count"
                };
                if (savedConfig) {
                    savedConfig = JSON.parse(savedConfig);
                    localConfig.rows = savedConfig.rows;
                    localConfig.cols = savedConfig.cols;
                    localConfig.rendererName = savedConfig.rendererName;
                    localConfig.aggregatorName = savedConfig.aggregatorName;
                    localConfig.vals = savedConfig.vals;
                }

                $("#output").pivotUI(pivotData, localConfig);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    $(function() {
        // Initial fetch with default dates
        fetchPivotData($('#tanggalDari').val(), $('#tanggalSampai').val());

        // Fetch data on filter button click
        $('#filterButton').on('click', function() {
            var tanggalDari = $('#tanggalDari').val();
            var tanggalSampai = $('#tanggalSampai').val();
            fetchPivotData(tanggalDari, tanggalSampai);
        });

        $("#saveConfig").on("click", function() {
            var config = $("#output").data("pivotUIOptions");
            localStorage.setItem("pivotConfig", JSON.stringify(config));
            Swal.fire({
                icon: 'success',
                title: 'Konfigurasi disimpan!',
                showConfirmButton: false,
                timer: 1500
            });
        });
        $("#saveToExcel").on("click", function() {
            var table = $("#output table .pvtTable").clone();

            table.find('thead th').each(function() {
                $(this).text($(this).text().trim());
            });

            var tanggalDari = $('#tanggalDari').val().split('-').reverse().join('-');
            var tanggalSampai = $('#tanggalSampai').val().split('-').reverse().join('-');
            table.find('tr:first').before('<tr><th colspan="' + table.find('thead th').length + '">Aktifitas Sales</th></tr><tr><th colspan="' + table.find('thead th').length + '">' + tanggalDari + ' s/d ' + tanggalSampai + '</th></tr><tr></tr>');
            var wb = XLSX.utils.table_to_book(table[0], {sheet: "Aktifitas Sales"});

            XLSX.writeFile(wb, "Aktifitas Sales " + new Date().toISOString().slice(0, 10) + ".xlsx");
        });
        $("#clearConfig").on("click", function() {
            localStorage.removeItem("pivotConfig");
            Swal.fire({
            icon: 'success',
            title: 'Konfigurasi dihapus!',
            showConfirmButton: false,
            timer: 1500
            });
            location.reload();
            });
        });
</script> -->
<script>
    let month = new Date().getMonth() + 1;
    $('#bulan').val(month < 10 ? '0' + month : month);
    $(document).ready(function() {
        function loadDataTable() {
            $('#label-laporan-mingguan').text($('#bulan option:selected').text() + ' - ' + $('#tahun').val());

            var table = $('#table-data').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard.aktifitas-sales.tabel.laporan-mingguan-sales') }}",
                    data: function (d) {
                        d.bulan = $('#bulan').val();
                        d.tahun = $('#tahun').val();
                        d.branch_id = $('#cabang').val();
                    },
                },
                "order":[
                    [0,'asc']
                ],
                columns:[{
                    data : 'nomor',
                    name : 'nomor',
                },{
                    data : 'nama_sales',
                    name : 'nama_sales',
                },{
                    data : 'cabang',
                    name : 'cabang',
                },{
                    data : 'w1_appt',
                    name : 'w1_appt',
                    className:'text-center'
                },{
                    data : 'w1_visit',
                    name : 'w1_visit',
                    className:'text-center'
                },{
                    data : 'w1_quot',
                    name : 'w1_quot',
                    className:'text-center'
                },{
                    data : 'w1_spk',
                    name : 'w1_spk',
                    className:'text-center'
                },{
                    data : 'w2_appt',
                    name : 'w2_appt',
                    className:'text-center'
                },{
                    data : 'w2_visit',
                    name : 'w2_visit',
                    className:'text-center'
                },{
                    data : 'w2_quot',
                    name : 'w2_quot',
                    className:'text-center'
                },{
                    data : 'w2_spk',
                    name : 'w2_spk',
                    className:'text-center'
                },{
                    data : 'w3_appt',
                    name : 'w3_appt',
                    className:'text-center'
                },{
                    data : 'w3_visit',
                    name : 'w3_visit',
                    className:'text-center'
                },{
                    data : 'w3_quot',
                    name : 'w3_quot',
                    className:'text-center'
                },{
                    data : 'w3_spk',
                    name : 'w3_spk',
                    className:'text-center'
                },{
                    data : 'w4_appt',
                    name : 'w4_appt',
                    className:'text-center'
                },{
                    data : 'w4_visit',
                    name : 'w4_visit',
                    className:'text-center'
                },{
                    data : 'w4_quot',
                    name : 'w4_quot',
                    className:'text-center'
                },{
                    data : 'w4_spk',
                    name : 'w4_spk',
                    className:'text-center'
                }],
                "language": datatableLang
            });

            $('#table-data tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('button, a, i')) return; // biar tombol tetap jalan
                let data = table.row(this).data();
                if (data === undefined) {
                    return;
                }

                console.log(data);

                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();
                let user = data.user_id;
                let url = '{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-bulanan-detail') }}';
                let urlParam = '?bulan=' + bulan + '&tahun=' + tahun+'&user_id=' + user;
                url += urlParam;
                openNormalDataTableModal(url,'AKTIFITAS '+data.nama_sales.toUpperCase()+' PADA CABANG : '+data.cabang.toUpperCase()+' BULAN : '+$('#bulan option:selected').text().toUpperCase()+' TAHUN : '+tahun);
            });
        }

        loadDataTable();

        $('#filterButton').on('click', function() {
            $('#table-data').DataTable().destroy();
            loadDataTable();
        });
    });
</script>
<script>
    let monthBulanan = new Date().getMonth() + 1;
    $('#bulanBulanan').val(monthBulanan < 10 ? '0' + monthBulanan : monthBulanan);
    $(document).ready(function() {
        function loadDataTableBulanan() {
            $('#label-laporan-bulanan').text($('#bulanBulanan option:selected').text() + ' - ' + $('#tahunBulanan').val());

            $('.label-bulan-sekarang').text($('#bulanBulanan option:selected').val() + ' - ' + $('#tahunBulanan').val());
            let bulanLalu = $('#bulanBulanan').val() - 1;
            let tahunLalu = $('#tahunBulanan').val();
            if (bulanLalu === 0) {
            bulanLalu = 12;
            tahunLalu--;
            }
            $('.label-bulan-lalu').text((bulanLalu < 10 ? '0' + bulanLalu : bulanLalu) + ' - ' + tahunLalu);

            var table = $('#table-data-bulanan').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard.aktifitas-sales.tabel.laporan-bulanan-sales') }}",
                    data: function (d) {
                        d.bulan = $('#bulanBulanan').val();
                        d.tahun = $('#tahunBulanan').val();
                        d.branch_id = $('#cabangBulanan').val();
                    },
                },
                "order":[
                    [0,'asc']
                ],
                columns:[{
                    data : 'nomor',
                    name : 'nomor',
                },{
                    data : 'nama_sales',
                    name : 'nama_sales',
                },{
                    data : 'cabang',
                    name : 'cabang',
                },{
                    data : 'jumlah_appt_lalu',
                    name : 'jumlah_appt_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'jumlah_appt',
                    name : 'jumlah_appt',
                    className:'text-center'
                },{
                    data : 'jumlah_visit_lalu',
                    name : 'jumlah_visit_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'jumlah_visit',
                    name : 'jumlah_visit',
                    className:'text-center'
                },{
                    data : 'jumlah_quot_lalu',
                    name : 'jumlah_quot_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'jumlah_quot',
                    name : 'jumlah_quot',
                    className:'text-center'
                },{
                    data : 'jumlah_spk_lalu',
                    name : 'jumlah_spk_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'jumlah_spk',
                    name : 'jumlah_spk',
                    className:'text-center'
                },{
                    data : 'persen_appt_to_visit_lalu',
                    name : 'persen_appt_to_visit_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'persen_appt_to_visit',
                    name : 'persen_appt_to_visit',
                    className:'text-center'
                },{
                    data : 'persen_visit_to_quot_lalu',
                    name : 'persen_visit_to_quot_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'persen_visit_to_quot',
                    name : 'persen_visit_to_quot',
                    className:'text-center'
                },{
                    data : 'persen_quot_to_spk_lalu',
                    name : 'persen_quot_to_spk_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'persen_quot_to_spk',
                    name : 'persen_quot_to_spk',
                    className:'text-center'
                },{
                    data : 'jumlah_aktual_spk_lalu',
                    name : 'jumlah_aktual_spk_lalu',
                    className:'text-center bg-warning text-white'
                },{
                    data : 'jumlah_aktual_spk',
                    name : 'jumlah_aktual_spk',
                    className:'text-center'
                }],
                "language": datatableLang
            });

            $('#table-data-bulanan tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('button, a, i')) return; // biar tombol tetap jalan
                let data = table.row(this).data();
                if (data === undefined) {
                    return;
                }

                console.log(data);

                let bulan = $('#bulanBulanan').val();
                let tahun = $('#tahunBulanan').val();
                let user = data.user_id;
                let url = '{{ route('dashboard.aktifitas-sales.modal.aktifitas-sales-bulanan-detail') }}';
                let urlParam = '?bulan=' + bulan + '&tahun=' + tahun+'&user_id=' + user;
                url += urlParam;
                openNormalDataTableModal(url,'AKTIFITAS '+data.nama_sales.toUpperCase()+' PADA CABANG : '+data.cabang.toUpperCase()+' BULAN : '+$('#bulanBulanan option:selected').text().toUpperCase()+' TAHUN : '+tahun);
            });
        }

        loadDataTableBulanan();

        $('#filterButtonBulanan').on('click', function() {
            $('#table-data-bulanan').DataTable().destroy();
            loadDataTableBulanan();
        });
    });

</script>
@endsection

