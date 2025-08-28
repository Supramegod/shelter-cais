@extends('layouts.master')
@section('title', 'Dashboard Aktifitas Sales')
@section('pageStyle')
@endsection
@section('content')
  <div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-primary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-account mdi-20px"></i></span>
              </div>
              <h4 class="ms-1 mb-0 display-6">{{$totalNewRegister}}</h4>
            </div>
            <p class="mb-0 text-heading ">New Register</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-warning h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                <span class="avatar-initial rounded bg-label-warning">
                  <i class="mdi mdi-account mdi-20px"></i>
                </span>
              </div>
              <h4 class="ms-1 mb-0 display-6">{{$totalLead}}</h4>
            </div>
            <p class="mb-0 text-heading ">Lead</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-secondary h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                <span class="avatar-initial rounded bg-label-secondary">
                  <i class="mdi mdi-account mdi-20px"></i></span>
              </div>
              <h4 class="ms-1 mb-0 display-6">{{$totalNewCold}}</h4>
            </div>
            <p class="mb-0 text-heading ">Cold Prospect</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-info h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                <span class="avatar-initial rounded bg-label-info"><i class="mdi mdi-account mdi-20px"></i></span>
              </div>
              <h4 class="ms-1 mb-0 display-6">{{$totalNewHot}}</h4>
            </div>
            <p class="mb-0 text-heading ">Hot Prospect</p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-2 mb-4">
        <div class="card card-border-shadow-danger h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-2 pb-1">
              <div class="avatar me-2">
                <span class="avatar-initial rounded bg-label-danger">
                  <i class="mdi mdi-account mdi-20px"></i></span>
              </div>
              <h4 class="ms-1 mb-0 display-6">{{$totalNewPeserta}}</h4>
            </div>
            <p class="mb-0 text-heading ">Peserta</p>
          </div>
        </div>
      </div>

    </div>
    <div class="row gy-4 mb-5">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header header-elements">
            <h5 class="card-title mb-0">Pembayaran Peserta Training Gada</h5>
            <div class="card-header-elements py-0 ms-auto">
              <div class="dropdown">
                <button type="button" class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" aria-expanded="false">
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

      <div class="col-lg-4 col-12 mb-4">
        <div class="card">
          <div class="card-header header-elements">
            <h5 class="card-title mb-0">Status Pembayaran Calon Peserta</h5>
            <div class="card-header-elements ms-auto py-0 dropdown">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" id="heat-chart-dd"
                data-bs-toggle="dropdown" aria-expanded="false">
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
    </div>
  </div>
@endsection

@section('pageScript')
  <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>

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
    const backgroundColor = [purpleColor, yellowColor, cyanColor, orangeColor, orangeLightColor, oceanBlueColor, greyColor, greyLightColor, blueColor, blueLightColor, redColor, greenColor, pinkColor, limeColor, tealColor, magentaColor, violetColor, indigoColor, amberColor, deepOrangeColor];
    const bankWarna = @json($warna);
    const chartList = document.querySelectorAll('.chartjs');
    chartList.forEach(function (chartListItem) {
      chartListItem.height = chartListItem.dataset.height;
    });

    const arrData = @json($pembayaranPesertaGada);
    let dataSet = [];
    arrData.forEach(function (element, index) {
      let arr = [];
      let jumlahLeads = element.jumlah_data;
      let resultLeads = Object.keys(jumlahLeads).map((key) => [key, jumlahLeads[key]]);

      resultLeads.forEach(eld => {
        arr.push(eld[1].data);
      });

      let objData = {
        label: element.status,
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
              // min: 0,
              // max: 10,
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

    const arrDataPolar = @json($statusPembayaranPesertaPie);
    let arrLabelPolar = [];
    let arrDataPolarChart = [];
    arrDataPolar.forEach(function (element) {
      arrLabelPolar.push(element.status);
      arrDataPolarChart.push(element.jumlah_data);
    });

    const polarChart = document.getElementById('polarChart');
    if (polarChart) {
      const polarChartVar = new Chart(polarChart, {
        type: 'polarArea',
        data: {
          labels: arrLabelPolar,
          datasets: [
            {
              label: 'Status Bayar',
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

  </script>
@endsection