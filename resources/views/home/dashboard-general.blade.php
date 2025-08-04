@extends('layouts.master')
@section('title', 'Dashboard General')
@section('pageStyle')
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://pivottable.js.org/dist/pivot.css">
  <script src="https://pivottable.js.org/dist/pivot.js"></script>
  <script src="https://pivottable.js.org/dist/plotly_renderers.js"></script>
  <style>
    .card {
    padding: 20px;
    /* Sesuaikan dengan kebutuhan */
    width: 100%;
    /* Menyesuaikan lebar dengan konten */
    box-sizing: border-box;
    /* Agar padding termasuk dalam ukuran total */
    }
  </style>
@endsection
@section('content')
  <div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
    <div class="col-12 mb-1">
      <h4 class="fw-bold py-3 mb-1">Leads Per Branch Bulan Ini</h4>
    </div>
    @foreach($leadsByBranch as $branch)
    <div class="col-sm-6 col-lg-3 mb-4">
      <div class="card card-border-shadow-primary h-100">
      <div class="card-body">
      <div class="d-flex align-items-center mb-2 pb-1">
      <div class="avatar me-2">
        <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-account mdi-20px"></i></span>
      </div>
      <h4 class="ms-1 mb-0 display-6">{{$branch->jumlah_leads}}</h4>
      </div>
      <p class="mb-0 text-heading ">{{$branch->branch}}</p>
      </div>
      </div>
    </div>
    @endforeach
    </div>
    <div class="row">
    <!-- Bar Charts -->
    <div class="col-xl-6 col-6 mb-4">
      <div class="card">
      <div class="card-header header-elements">
        <h5 class="card-title mb-0">Target Vs Actual Per Branch Bulan Ini</h5>
      </div>
      <div class="card-body">
        <canvas id="barChartTargetActual" class="chartjs" data-height="400" height="350"></canvas>
      </div>
      </div>
    </div>
    <div class="col-xl-6 col-6 mb-4">
      <div class="card">
      <div class="card-header header-elements">
        <h5 class="card-title mb-0">Kebutuhan Leads Per Branch Bulan Ini</h5>
      </div>
      <div class="card-body">
        <canvas id="barChartKebutuhanPerBranch" class="chartjs" data-height="400" height="350"></canvas>
      </div>
      </div>
    </div>
    <!-- /Bar Charts -->
    <div class="card container-fluid" style="width: auto;">
      <div class="row">
      <div id="output"></div>
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
  </script>
  <script>
    const dataKebutuhanPerBranch = @json($leadsByBranchAndKebutuhan);
    const kebutuhanList = @json($kebutuhanList);
    let arrBranch = [];

    dataKebutuhanPerBranch.forEach(element => {
    arrBranch.push(element.branch);
    });

    let arrBranchDs = [];
    kebutuhanList.forEach(function (element, index) {
    let dataDsBranch = [];
    dataKebutuhanPerBranch.forEach(eld => {
      eld.data.forEach(elv => {
      if (element.nama === elv.kebutuhan) {
        dataDsBranch.push(elv.jumlah_leads)
      }
      });
    });

    let obj = {
      label: element.nama,
      data: dataDsBranch,
      backgroundColor: bankWarna[index],
      borderColor: 'transparent',
      maxBarThickness: 15,
      borderRadius: {
      topRight: 15,
      topLeft: 15
      }
    }
    arrBranchDs.push(obj);
    });

    const barChartKebutuhanPerBranch = document.getElementById('barChartKebutuhanPerBranch');


    if (barChartKebutuhanPerBranch) {
    const barChartKebutuhanPerBranchVar = new Chart(barChartKebutuhanPerBranch, {
      type: 'bar',
      data: {
      labels: arrBranch,
      datasets: arrBranchDs
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
        max: 100,
        ticks: {
          color: labelColor,
          stepSize: 5
        },
        grid: {
          color: borderColor,
          drawBorder: false,
          borderColor: borderColor
        },
        ticks: {
          stepSize: 100,
          color: labelColor
        }
        }
      }
      }
    });
    }
  </script>
  <script>
    const branchesWithCustomerData = @json($branchesWithCustomerData);
    let arrTarget = [];
    let arrActual = [];

    branchesWithCustomerData.forEach(element => {
    arrTarget.push(element.data.target);
    arrActual.push(element.data.actual);
    });

    const barChartTargetActual = document.getElementById('barChartTargetActual');


    if (barChartTargetActual) {
    const barChartTargetActualVar = new Chart(barChartTargetActual, {
      type: 'bar',
      data: {
      labels: arrBranch,
      datasets: [
        {
        label: 'Target',
        data: arrTarget,
        backgroundColor: orangeLightColor,
        borderColor: 'transparent',
        maxBarThickness: 15,
        borderRadius: {
          topRight: 15,
          topLeft: 15
        }
        }, {
        label: 'Actual',
        data: arrActual,
        backgroundColor: purpleColor,
        borderColor: 'transparent',
        maxBarThickness: 15,
        borderRadius: {
          topRight: 15,
          topLeft: 15
        }
        }
      ]
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
        max: 100,
        ticks: {
          color: labelColor,
          stepSize: 5
        },
        grid: {
          color: borderColor,
          drawBorder: false,
          borderColor: borderColor
        },
        ticks: {
          stepSize: 100,
          color: labelColor
        }
        }
      }
      }
    });
    }
  </script>
  <script>
    $(function () {
    var derivers = $.pivotUtilities.derivers;
    var renderers = $.extend($.pivotUtilities.renderers,
      $.pivotUtilities.plotly_renderers);
    // Sample Data
    const data = [
      { region: "North", year: "2020", sales: 1000 },
      { region: "North", year: "2021", sales: 1200 },
      { region: "South", year: "2020", sales: 800 },
      { region: "South", year: "2021", sales: 950 },
      { region: "East", year: "2020", sales: 1500 },
      { region: "East", year: "2021", sales: 1700 },
    ];

    // Render PivotTable with Plotly Renderer
    $("#output").pivotUI(data, {
      renderers: renderers,
      rendererName: "Bar Chart",
      rows: ["region"], // Set rows
      cols: ["year"], // Set columns
      aggregatorName: "Sum",
      vals: ["sales"]
    });
    });
  </script>
@endsection