@extends('layouts.master')
@section('title','Dashboard Aktifitas Sales')
@section('pageStyle')
    <!-- PivotTable CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.css">
    <!-- C3 Chart CSS (Optional for charts) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css">
    <!-- jQuery UI CSS (Required for drag and drop functionality) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
            <div class="card card-border-shadow-warning h-100">
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
            <div class="card card-border-shadow-secondary h-100">
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
            <div class="card card-border-shadow-info h-100">
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
                <canvas id="polarChart" class="chartjs" data-height="337"></canvas>
            </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
            <h5 class="card-header">By Tipe Aktifitas Bulan Ini</h5>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="col-lg-4">
                        <canvas id="doughnutChart" class="chartjs mb-4" data-height="100"></canvas>
                    </div>
                </div>
                <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1">
                @foreach($tipe as $key => $value)
                    <li class="ct-series-0 d-flex flex-column">
                        <h5 class="mb-0">{{$value}}</h5>
                        <span
                        class="badge badge-dot my-2 cursor-pointer rounded-pill"
                        style="background-color: {{$warna[$key]}}; width: 35px; height: 6px"></span>
                        <div class="text-muted">{{round($jumlahAktifitasTipe[$key]/array_sum($jumlahAktifitasTipe)*100,0)}} %</div>
                    </li>
                @endforeach
                </ul>
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
                <canvas id="lineChart" class="chartjs" data-height="500" height="400"></canvas>
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
                    <ul class="d-flex justify-content-around ps-0 mb-2 pt-1">
                    @foreach($tipe as $key => $value)
                        <li class="ct-series-0 d-flex flex-column">
                            <h5 class="mb-0">{{$value}}</h5>
                            <span
                            class="badge badge-dot my-2 cursor-pointer rounded-pill"
                            style="background-color: {{$warna[$key]}}; width: 35px; height: 6px"></span>
                        </li>
                    @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <canvas id="barChart" class="chartjs" data-height="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
<script src="{{ asset('vendor/libs/chartjs/chartjs.js') }}"></script>

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
            display: false
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
            max: 100,
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
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
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
          legend: {
            display: false
          }
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
            max: 100,
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
@endsection

