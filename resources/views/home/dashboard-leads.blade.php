@extends('layouts.master')
@section('title','Dashboard Aktifitas Sales')
@section('pageStyle')
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                  <div class="d-flex align-items-center mb-2 pb-1">
                  <div class="avatar me-2">
                      <span class="avatar-initial rounded bg-label-primary"
                      ><i class="mdi mdi-account mdi-20px"></i
                      ></span>
                  </div>
                  <h4 class="ms-1 mb-0 display-6">{{$leadsBaruHariIni}}</h4>
                  </div>
                  <p class="mb-0 text-heading ">Leads Baru Hari Ini</p>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-account mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBaruMingguIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Baru Minggu Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBaruBulanIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Baru Bulan Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-info"
                    ><i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBaruTahunIni}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Baru Tahun Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBelumAdaAktifitas}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Belum ada Aktifitas</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBelumAdaCustomer}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Belum Menjadi Customer</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBelumAdaSales}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads Belum ada Sales</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-danger h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-danger">
                    <i class="mdi mdi-account mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">{{$leadsBelumAdaQuotation}}</h4>
                </div>
                <p class="mb-0 text-heading ">Leads belum Quotation</p>
            </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 mb-5">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header header-elements">
            <h5 class="card-title mb-0">Leads Baru By Kebutuhan Per Bulan</h5>
            <div class="card-header-elements py-0 ms-auto">
              <div class="dropdown">
                <button
                  type="button"
                  class="btn dropdown-toggle p-0"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <i class="mdi mdi-calendar-month-outline"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body pt-2">
            <canvas id="lineAreaChart" class="chartjs" data-height="450" height="400"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-12 mb-4">
        <div class="card">
          <div class="card-header header-elements">
            <h5 class="card-title mb-0">Summary Sumber Leads</h5>
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
            <canvas id="polarChart" class="chartjs" data-height="337" height="200"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-12 mb-4">
        <div class="card">
          <h5 class="card-header">Persentase Menjadi Customer</h5>
          <div class="card-body">
            <canvas id="doughnutChartCustomer" class="chartjs mb-4" data-height="350" height="200"></canvas>
            <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1">
              <li class="ct-series-0 d-flex flex-column">
                <h5 class="mb-0">Masih Leads</h5>
                <span
                  class="badge badge-dot my-2 cursor-pointer rounded-pill"
                  style="background-color: rgb(102, 110, 232); width: 35px; height: 6px"></span>
                  <div class="text-muted">{{round($leadsWithoutCustomer/($leadsWithCustomer+$leadsWithoutCustomer)*100,0)}} %</div>
                </li>
              <li class="ct-series-1 d-flex flex-column">
                <h5 class="mb-0">Sudah Customer</h5>
                <span
                  class="badge badge-dot my-2 cursor-pointer rounded-pill"
                  style="background-color: rgb(40, 208, 148); width: 35px; height: 6px"></span>
                <div class="text-muted">{{round($leadsWithCustomer/($leadsWithCustomer+$leadsWithoutCustomer)*100,0)}} %</div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-12 mb-4">
        <div class="card">
          <h5 class="card-header">Persentase Jenis Kebutuhan</h5>
          <div class="card-body">
            <canvas id="doughnutChartKebutuhan" class="chartjs mb-4" data-height="350"></canvas>
            <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1">
              @foreach($leadsGroupKebutuhan as $key => $value)
                  <li class="ct-series-0 d-flex flex-column">
                      <h5 class="mb-0">{{$value->kebutuhan}}</h5>
                      <span
                      class="badge badge-dot my-2 cursor-pointer rounded-pill"
                      style="background-color: {{$warna[$key]}}; width: 35px; height: 6px"></span>
                      <div class="text-muted">{{round($value->jumlah_leads/$totalLeadsKebutuhan*100,0)}} %</div>
                  </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@section('pageScript')
<script src="{{ asset('public/assets/vendor/libs/chartjs/chartjs.js') }}"></script>

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
  const chartList = document.querySelectorAll('.chartjs');
  chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
  });

  const arrData = @json($leadsGroupedByKebutuhan);
  let dataSet = [];
  arrData.forEach(function(element, index) {
    let arr = [];
    let jumlahLeads = element.jumlah_leads;
    let resultLeads = Object.keys(jumlahLeads).map((key) => [key, jumlahLeads[key]]);
    
    resultLeads.forEach(eld => {      
      arr.push(eld[1].leads);  
    });

    let objData = {
            label: element.kebutuhan,
            data: arr,
            tension: 0,
            fill: true,
            backgroundColor: bankWarna[index],
            pointStyle: 'circle',
            borderColor: 'transparent',
            pointRadius: 0.5,
            pointHoverRadius: 5,
            pointHoverBorderWidth: 5,
            pointBorderColor: 'transparent',
            pointHoverBackgroundColor: bankWarna[index],
            pointHoverBorderColor: cardColor
          };
          dataSet.push(objData);
  });

  const lineAreaChart = document.getElementById('lineAreaChart');
  if (lineAreaChart) {
    const lineAreaChartVar = new Chart(lineAreaChart, {
      type: 'line',
      data: {
        labels: [
          'Januari',
          'Februari',
          'Maret',
          'April',
          'Mei',
          'Juni',
          'Juli',
          'Agustus',
          'September',
          'Oktober',
          'November',
          'Desember',
        ],
        datasets: dataSet
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            rtl: isRtl,
            align: 'start',
            labels: {
              usePointStyle: true,
              padding: 35,
              boxWidth: 6,
              boxHeight: 6,
              color: legendColor,
              font: {
                family: 'Inter'
              }
            }
          },
          tooltip: {
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        },
        scales: {
          x: {
            grid: {
              color: 'transparent',
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: 100,
            grid: {
              color: 'transparent',
              borderColor: borderColor
            },
            ticks: {
              stepSize: 4,
              color: labelColor
            }
          }
        }
      }
    });
  }

  const arrDataPolar = @json($leadsBySumber);
  let arrLabelPolar = [];
  let arrDataPolarChart = [];
  arrDataPolar.forEach(function(element) {
    arrLabelPolar.push(element.platform);
    arrDataPolarChart.push(element.jumlah_leads);
  });
  
  const polarChart = document.getElementById('polarChart');
  if (polarChart) {
    const polarChartVar = new Chart(polarChart, {
      type: 'polarArea',
      data: {
        labels: arrLabelPolar,
        datasets: [
          {
            label: 'Leads',
            backgroundColor: bankWarna,
            data: arrDataPolarChart,
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
            position: 'right',
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

  const doughnutChartCustomer = document.getElementById('doughnutChartCustomer');
  if (doughnutChartCustomer) {
    const doughnutChartCustomerVar = new Chart(doughnutChartCustomer, {
      type: 'doughnut',
      data: {
        labels: ['Masih Leads', 'Sudah Customer'],
        datasets: [
          {
            data: [{{$leadsWithoutCustomer}}, {{$leadsWithCustomer}}],
            backgroundColor: ['#666ee8', '#28d094'],
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
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value + ' %';
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


  const arrDougData = @json($leadsGroupKebutuhan);
  let labelDoug = [];
  let dataDoug = [];
  arrDougData.forEach(function(element) {
    labelDoug.push(element.kebutuhan);
    dataDoug.push(element.jumlah_leads);
  });
  console.log(labelDoug);
  console.log(dataDoug);
  
  
  const doughnutChartKebutuhan = document.getElementById('doughnutChartKebutuhan');
  if (doughnutChartKebutuhan) {
    const doughnutChartKebutuhanVar = new Chart(doughnutChartKebutuhan, {
      type: 'doughnut',
      data: {
        labels: labelDoug,
        datasets: [
          {
            data: dataDoug,
            backgroundColor: bankWarna,
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
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value + ' %';
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

    </script>
@endsection

