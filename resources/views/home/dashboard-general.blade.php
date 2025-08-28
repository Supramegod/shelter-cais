@extends('layouts.master')
@section('title', 'Dashboard General')
@section('pageStyle')
  <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://pivottable.js.org/dist/pivot.css">
  <script src="https://pivottable.js.org/dist/pivot.js"></script>
  <script src="https://pivottable.js.org/dist/plotly_renderers.js"></script>
  <style>
    /* Base Styles & Reset */
    * {
      box-sizing: border-box;
    }

    .dashboard-container {
      padding: 1rem;
      background: #f8fafc;
      min-height: 100vh;
    }

    .dashboard-row {
      margin-bottom: 1.5rem;
    }

    /* Enhanced Card Styles */
    .dashboard-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.05);
      height: 100%;
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .dashboard-card:hover {
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
      transform: translateY(-2px);
    }

    /* Stats Cards with Improved Gradients */
    .stats-card-new {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .stats-card-existing {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .stats-card-target {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .stats-card-leads {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .stats-card-new::before,
    .stats-card-existing::before,
    .stats-card-target::before,
    .stats-card-leads::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100px;
      height: 100px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      z-index: 0;
    }

    .stats-card-body {
      position: relative;
      z-index: 1;
      padding: 1.5rem;
    }

    .stats-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      opacity: 0.9;
    }

    .stats-value {
      font-size: 2.2rem;
      font-weight: 700;
      margin: 0.5rem 0;
      line-height: 1;
    }

    .stats-label {
      font-size: 0.9rem;
      opacity: 0.9;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 500;
    }

    .stats-change {
      font-size: 0.85rem;
      margin-top: 0.5rem;
      opacity: 0.8;
    }

    /* Section Headers */
    .section-header {
      font-size: 1.75rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1.5rem;
      position: relative;
      padding-left: 20px;
      display: flex;
      align-items: center;
    }

    .section-header::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 24px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 2px;
    }

    /* Chart Container */
    .chart-wrapper {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.05);
      height: 100%;
      min-height: 350px;
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid #f1f5f9;
    }

    .chart-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #1e293b;
      margin: 0;
    }

    .chart-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-primary {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
    }

    .badge-success {
      background: linear-gradient(135deg, #28dac6, #20c997);
      color: white;
    }

    /* Enhanced Progress Bar */
    .custom-progress {
      height: 8px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 4px;
      overflow: hidden;
      margin-top: 0.75rem;
      position: relative;
    }

    .custom-progress-bar {
      height: 100%;
      background: linear-gradient(90deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 1) 100%);
      border-radius: 4px;
      transition: width 0.6s ease;
      position: relative;
      overflow: hidden;
    }

    .custom-progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      background: linear-gradient(45deg, transparent 33%, rgba(255, 255, 255, 0.3) 33%, rgba(255, 255, 255, 0.3) 66%, transparent 66%);
      background-size: 30px 30px;
      animation: progress-stripe 2s linear infinite;
    }

    @keyframes progress-stripe {
      0% {
        background-position: 0 0;
      }

      100% {
        background-position: 30px 0;
      }
    }

    /* Branch Cards */
    .branch-card {
      transition: all 0.3s ease;
    }

    .branch-card:hover {
      transform: translateY(-3px);
    }

    .branch-avatar {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .branch-avatar.success {
      background: linear-gradient(135deg, #28dac6, #20c997);
    }

    .branch-avatar.warning {
      background: linear-gradient(135deg, #ffd93d, #ffb74d);
    }

    .branch-avatar.info {
      background: linear-gradient(135deg, #4facfe, #299AFF);
    }

    .branch-value {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.25rem;
    }

    .branch-name {
      font-size: 1rem;
      color: #64748b;
      margin-bottom: 0.25rem;
    }

    .branch-target {
      font-size: 0.85rem;
      padding: 0.15rem 0.5rem;
      border-radius: 12px;
      display: inline-block;
    }

    .target-success {
      background: #dcfce7;
      color: #166534;
    }

    .target-warning {
      background: #fef3c7;
      color: #92400e;
    }

    .target-danger {
      background: #fecaca;
      color: #991b1b;
    }

    /* Ranking List */
    .ranking-list {
      height: 320px;
      overflow-y: auto;
      padding-right: 0.5rem;
    }

    .ranking-list::-webkit-scrollbar {
      width: 6px;
    }

    .ranking-list::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 3px;
    }

    .ranking-list::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 3px;
    }

    .ranking-list::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    .ranking-item {
      display: flex;
      align-items: center;
      padding: 1rem;
      margin-bottom: 0.75rem;
      background: #f8fafc;
      border-radius: 12px;
      transition: all 0.3s ease;
      border: 1px solid #e2e8f0;
    }

    .ranking-item:hover {
      background: #e2e8f0;
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .rank-number {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: white;
      margin-right: 1rem;
      font-size: 0.9rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .rank-1 {
      background: linear-gradient(135deg, #FFD700, #FFA500);
    }

    .rank-2 {
      background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
    }

    .rank-3 {
      background: linear-gradient(135deg, #CD7F32, #A0522D);
    }

    .rank-other {
      background: linear-gradient(135deg, #6c757d, #495057);
    }

    .ranking-info {
      flex-grow: 1;
    }

    .ranking-name {
      font-weight: 600;
      color: #1e293b;
      font-size: 0.95rem;
      margin-bottom: 0.25rem;
    }

    .ranking-branch {
      color: #64748b;
      font-size: 0.8rem;
    }

    .ranking-stats {
      text-align: right;
    }

    .ranking-sales {
      font-weight: 700;
      color: #059669;
      font-size: 1rem;
      margin-bottom: 0.25rem;
    }

    .ranking-achievement {
      color: #64748b;
      font-size: 0.8rem;
    }

    /* Chart Container Inner */
    .chart-container-inner {
      position: relative;
      height: 250px;
      width: 100%;
    }

    .chart-container-inner canvas {
      max-height: 250px !important;
    }

    /* Pivot Table Styles */
    #output {
      border-radius: 8px;
      overflow: hidden;
    }

    .pvtTable {
      font-size: 0.85rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 0.5rem;
      }

      .section-header {
        font-size: 1.5rem;
        padding-left: 15px;
      }

      .section-header::before {
        height: 20px;
      }

      .stats-value {
        font-size: 1.8rem;
      }

      .chart-wrapper {
        padding: 1rem;
        min-height: 300px;
      }

      .chart-container-inner {
        height: 200px;
      }

      .chart-container-inner canvas {
        max-height: 200px !important;
      }

      .ranking-list {
        height: 250px;
      }

      .branch-value {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 576px) {
      .chart-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      .stats-card-body {
        padding: 1rem;
      }

      .stats-value {
        font-size: 1.6rem;
      }
    }

    /* Loading Animation */
    .loading-shimmer {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% {
        background-position: -200% 0;
      }

      100% {
        background-position: 200% 0;
      }
    }

    /* Improved Focus States */
    .dashboard-card:focus-within {
      outline: 2px solid #667eea;
      outline-offset: 2px;
    }

    /* Better Typography */
    .fw-semibold {
      font-weight: 600;
    }

    .text-success {
      color: #059669 !important;
    }

    .text-warning {
      color: #d97706 !important;
    }

    .text-danger {
      color: #dc2626 !important;
    }

    .text-muted {
      color: #64748b !important;
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid dashboard-container">

    <!-- Summary Stats Row -->
    <div class="row dashboard-row">
      <div class="col-12 mb-4">
        <h2 class="section-header">
          Dashboard Overview
          <small class="ms-3 text-muted" style="font-size: 0.75rem; font-weight: 400;">
            Last updated: {{ date('d M Y, H:i') }}
          </small>
        </h2>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card stats-card-new">
          <div class="stats-card-body text-center">
            <div class="stats-icon">
              <i class="mdi mdi-account-plus"></i>
            </div>
            <div class="stats-value">245</div>
            <div class="stats-label">New Sales</div>
            <div class="stats-change">+12% dari bulan lalu</div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card stats-card-existing">
          <div class="stats-card-body text-center">
            <div class="stats-icon">
              <i class="mdi mdi-account-group"></i>
            </div>
            <div class="stats-value">180</div>
            <div class="stats-label">Existing Sales</div>
            <div class="stats-change">+8% dari bulan lalu</div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card stats-card-target">
          <div class="stats-card-body text-center">
            <div class="stats-icon">
              <i class="mdi mdi-target"></i>
            </div>
            <div class="stats-value">87%</div>
            <div class="stats-label">Achievement</div>
            <div class="custom-progress">
              <div class="custom-progress-bar" style="width: 87%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card stats-card-leads">
          <div class="stats-card-body text-center">
            <div class="stats-icon">
              <i class="mdi mdi-chart-line"></i>
            </div>
            <div class="stats-value">1,250</div>
            <div class="stats-label">Total Leads</div>
            <div class="stats-change">Bulan ini</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Branch Leads Row -->
    <div class="row dashboard-row">
      <div class="col-12 mb-4">
        <h2 class="section-header">Performance Per Branch</h2>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">325</div>
                <div class="branch-name">Jakarta Pusat</div>
                <span class="branch-target target-success">Target: 350</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar success">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">280</div>
                <div class="branch-name">Surabaya</div>
                <span class="branch-target target-success">Target: 300</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar warning">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">195</div>
                <div class="branch-name">Bandung</div>
                <span class="branch-target target-warning">Target: 250</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar warning">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">245</div>
                <div class="branch-name">Bandung</div>
                <span class="branch-target target-warning">Target: 250</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar warning">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">140</div>
                <div class="branch-name">Bandung</div>
                <span class="branch-target target-warning">Target: 250</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar warning">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">300</div>
                <div class="branch-name">Bandung</div>
                <span class="branch-target target-warning">Target: 250</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar warning">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">195</div>
                <div class="branch-name">Bandung</div>
                <span class="branch-target target-warning">Target: 250</span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-lg-3 col-md-6 mb-4">
        <div class="dashboard-card branch-card">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="branch-avatar info">
                <i class="mdi mdi-office-building text-white" style="font-size: 1.5rem;"></i>
              </div>
              <div class="flex-grow-1">
                <div class="branch-value">160</div>
                <div class="branch-name">Medan</div>
                <span class="branch-target target-danger">Target: 200</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row dashboard-row">
      <div class="col-12 mb-4">
        <h2 class="section-header">Analisa Performance</h2>
      </div>

      <div class="col-xl-6 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">Target vs Actual Per Branch</h5>
            <span class="chart-badge badge-primary">Bulan Ini</span>
          </div>
          <div class="chart-container-inner">
            <canvas id="barChartTargetActual"></canvas>
          </div>
        </div>
      </div>

      <div class="col-xl-6 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">Kebutuhan Leads Per Branch</h5>
            <span class="chart-badge badge-success">Real-time</span>
          </div>
          <div class="chart-container-inner">
            <canvas id="barChartKebutuhanPerBranch"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance & Ranking Row -->
    <div class="row dashboard-row">
      <div class="col-xl-8 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">Trend Performance Penjualan</h5>
            <span class="chart-badge badge-success">Achievement %</span>
          </div>
          <div class="chart-container-inner">
            <canvas id="performanceChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-xl-4 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">🏆 Top Sales Ranking</h5>
          </div>
          <div class="ranking-list" id="salesRanking">
            <!-- Ranking content akan diisi via JavaScript -->
          </div>
        </div>
      </div>
    </div>

    <!-- Overall Achievement Row -->
    <div class="row dashboard-row">
      <div class="col-12 mb-4">
        <h2 class="section-header">Overall Achievement Analysis</h2>
      </div>

      <div class="col-lg-6 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">Total Achievement Overview</h5>
            <span class="chart-badge badge-success" id="achievementPercentage">85%</span>
          </div>
          <div class="chart-container-inner">
            <canvas id="overallTargetChart"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6 col-12 mb-4">
        <div class="chart-wrapper">
          <div class="chart-header">
            <h5 class="chart-title">Performance Summary</h5>
          </div>
          <div class="p-3">
            <div class="row text-center">
              <div class="col-4">
                <div class="stats-value text-primary" id="totalTarget">1,100</div>
                <div class="stats-label text-muted">Total Target</div>
              </div>
              <div class="col-4">
                <div class="stats-value text-success" id="totalActual">960</div>
                <div class="stats-label text-muted">Total Actual</div>
              </div>
              <div class="col-4">
                <div class="stats-value text-warning" id="totalGap">140</div>
                <div class="stats-label text-muted">Gap</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Interactive Analytics Row -->
    <!-- <div class="row dashboard-row">
        <div class="col-12 mb-4">
          <h2 class="section-header">Interactive Data Analytics</h2>
        </div>

        <div class="col-12">
          <div class="chart-wrapper">
            <div class="chart-header">
              <h5 class="chart-title">📊 Pivot Table Analysis</h5>
              <small class="text-muted">Drag and drop untuk analisa data interaktif</small>
            </div>
            <div id="output"></div>
          </div>
        </div>
      </div>

    </div> -->
@endsection

  @section('pageScript')
    <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>

    <!-- Enhanced Data Variables -->
    <script>
      // Enhanced Color Palette
      const colors = {
        primary: '#667eea',
        secondary: '#764ba2',
        success: '#28dac6',
        warning: '#ffd93d',
        danger: '#ff6b6b',
        info: '#4facfe',
        purple: '#836AF9',
        orange: '#FF8132',
        blue: '#2B9AFF',
        green: '#4BC0C0',
        red: '#FF6384',
        gradient: {
          primary: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
          success: 'linear-gradient(135deg, #28dac6 0%, #20c997 100%)',
          warning: 'linear-gradient(135deg, #ffd93d 0%, #ffb74d 100%)',
          danger: 'linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%)'
        }
      };

      // Enhanced Data with more realistic values
      const branchData = [
        { branch: 'Jakarta Pusat', target: 350, actual: 325, leads: 325 },
        { branch: 'Surabaya', target: 300, actual: 280, leads: 280 },
        { branch: 'Bandung', target: 250, actual: 195, leads: 195 },
        { branch: 'Bandung', target: 250, actual: 195, leads: 195 },
        { branch: 'Bandung', target: 250, actual: 195, leads: 195 },
        { branch: 'Bandung', target: 250, actual: 195, leads: 195 },
        { branch: 'Medan', target: 200, actual: 160, leads: 160 }
      ];

      const kebutuhanData = [
        { nama: 'KPR', color: colors.primary },
        { nama: 'KTA', color: colors.success },
        { nama: 'Investasi', color: colors.warning },
        { nama: 'Asuransi', color: colors.danger }
      ];

      const leadsByKebutuhan = [
        { branch: 'Jakarta Pusat', KPR: 120, KTA: 85, Investasi: 95, Asuransi: 20 },
        { branch: 'Surabaya', KPR: 95, KTA: 75, Investasi: 80, Asuransi: 30 },
        { branch: 'Bandung', KPR: 70, KTA: 45, Investasi: 60, Asuransi: 20 },
        { branch: 'Medan', KPR: 55, KTA: 40, Investasi: 45, Asuransi: 20 }
      ];

      const salesRanking = [
        { name: 'Ahmad Budi Santoso', branch: 'Jakarta Pusat', sales: 150, achievement: 95 },
        { name: 'Siti Nurhaliza', branch: 'Surabaya', sales: 142, achievement: 88 },
        { name: 'Rizki Pratama', branch: 'Bandung', sales: 138, achievement: 85 },
        { name: 'Maya Sari Dewi', branch: 'Medan', sales: 125, achievement: 78 },
        { name: 'Indra Wijaya', branch: 'Jakarta Pusat', sales: 118, achievement: 74 },
        { name: 'Putri Maharani', branch: 'Surabaya', sales: 112, achievement: 70 }
      ];

      // Update summary values
      const totalTarget = branchData.reduce((sum, d) => sum + d.target, 0);
      const totalActual = branchData.reduce((sum, d) => sum + d.actual, 0);
      const totalGap = totalTarget - totalActual;
      const achievementPercentage = ((totalActual / totalTarget) * 100).toFixed(1);

      // Update DOM elements
      document.addEventListener('DOMContentLoaded', function () {
        const totalTargetEl = document.getElementById('totalTarget');
        const totalActualEl = document.getElementById('totalActual');
        const totalGapEl = document.getElementById('totalGap');
        const achievementPercentageEl = document.getElementById('achievementPercentage');

        if (totalTargetEl) totalTargetEl.textContent = totalTarget.toLocaleString();
        if (totalActualEl) totalActualEl.textContent = totalActual.toLocaleString();
        if (totalGapEl) totalGapEl.textContent = totalGap.toLocaleString();
        if (achievementPercentageEl) achievementPercentageEl.textContent = achievementPercentage + '%';
      });
    </script>

    <!-- Enhanced Target vs Actual Chart -->
    <script>
      const targetActualChart = document.getElementById('barChartTargetActual');
      if (targetActualChart) {
        new Chart(targetActualChart, {
          type: 'bar',
          data: {
            labels: branchData.map(d => d.branch),
            datasets: [
              {
                label: 'Target',
                data: branchData.map(d => d.target),
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: '#ffc107',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
              },
              {
                label: 'Actual',
                data: branchData.map(d => d.actual),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: colors.primary,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
                align: 'end',
                labels: {
                  usePointStyle: true,
                  padding: 20,
                  font: {
                    size: 12,
                    weight: '600'
                  }
                }
              },
              tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: colors.primary,
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                  label: function (context) {
                    const percentage = ((context.raw / branchData[context.dataIndex].target) * 100).toFixed(1);
                    return context.dataset.label + ': ' + context.raw.toLocaleString() +
                      (context.dataset.label === 'Actual' ? ` (${percentage}%)` : '');
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  color: 'rgba(0,0,0,0.1)',
                  drawBorder: false
                },
                ticks: {
                  font: {
                    size: 11
                  },
                  callback: function (value) {
                    return value.toLocaleString();
                  }
                }
              },
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  font: {
                    size: 11,
                    weight: '600'
                  }
                }
              }
            },
            animation: {
              duration: 2000,
              easing: 'easeInOutQuart'
            }
          }
        });
      }
    </script>

    <!-- Enhanced Kebutuhan Leads Chart -->
    <script>
      const kebutuhanChart = document.getElementById('barChartKebutuhanPerBranch');
      if (kebutuhanChart) {
        const datasets = kebutuhanData.map(kebutuhan => ({
          label: kebutuhan.nama,
          data: leadsByKebutuhan.map(branch => branch[kebutuhan.nama]),
          backgroundColor: kebutuhan.color + 'CC',
          borderColor: kebutuhan.color,
          borderWidth: 2,
          borderRadius: 6,
          borderSkipped: false,
        }));

        new Chart(kebutuhanChart, {
          type: 'bar',
          data: {
            labels: leadsByKebutuhan.map(d => d.branch),
            datasets: datasets
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
                align: 'end',
                labels: {
                  usePointStyle: true,
                  padding: 20,
                  font: {
                    size: 12,
                    weight: '600'
                  }
                }
              },
              tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: colors.primary,
                borderWidth: 1,
                cornerRadius: 8,
                mode: 'index',
                intersect: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                stacked: false,
                grid: {
                  color: 'rgba(0,0,0,0.1)',
                  drawBorder: false
                },
                ticks: {
                  font: {
                    size: 11
                  }
                }
              },
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  font: {
                    size: 11,
                    weight: '600'
                  }
                }
              }
            },
            animation: {
              duration: 2000,
              easing: 'easeInOutQuart'
            }
          }
        });
      }
    </script>

    <!-- Enhanced Performance Line Chart -->
    <script>
      const performanceChart = document.getElementById('performanceChart');
      if (performanceChart) {
        const achievements = branchData.map(d => ((d.actual / d.target) * 100).toFixed(1));

        new Chart(performanceChart, {
          type: 'line',
          data: {
            labels: branchData.map(d => d.branch),
            datasets: [{
              label: 'Achievement %',
              data: achievements,
              borderColor: colors.success,
              backgroundColor: colors.success + '20',
              borderWidth: 4,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: colors.success,
              pointBorderColor: '#fff',
              pointBorderWidth: 3,
              pointRadius: 8,
              pointHoverRadius: 10,
              pointHoverBackgroundColor: colors.success,
              pointHoverBorderColor: '#fff',
              pointHoverBorderWidth: 3,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: colors.success,
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                  label: function (context) {
                    return 'Achievement: ' + context.raw + '%';
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                max: 100,
                grid: {
                  color: 'rgba(0,0,0,0.1)',
                  drawBorder: false
                },
                ticks: {
                  font: {
                    size: 11
                  },
                  callback: function (value) {
                    return value + '%';
                  }
                }
              },
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  font: {
                    size: 11,
                    weight: '600'
                  }
                }
              }
            },
            animation: {
              duration: 2000,
              easing: 'easeInOutQuart'
            }
          }
        });
      }
    </script>

    <!-- Enhanced Overall Target Doughnut Chart -->
    <script>
      const overallChart = document.getElementById('overallTargetChart');
      if (overallChart) {
        const totalTarget = branchData.reduce((sum, d) => sum + d.target, 0);
        const totalActual = branchData.reduce((sum, d) => sum + d.actual, 0);
        const achievement = ((totalActual / totalTarget) * 100).toFixed(1);

        new Chart(overallChart, {
          type: 'doughnut',
          data: {
            labels: ['Achieved', 'Remaining'],
            datasets: [{
              data: [totalActual, totalTarget - totalActual],
              backgroundColor: [
                colors.success + 'CC',
                '#e2e8f0'
              ],
              borderColor: [
                colors.success,
                '#cbd5e1'
              ],
              borderWidth: 3,
              hoverBorderWidth: 4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  usePointStyle: true,
                  padding: 20,
                  font: {
                    size: 12,
                    weight: '600'
                  }
                }
              },
              tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: colors.primary,
                borderWidth: 1,
                cornerRadius: 8,
                callbacks: {
                  label: function (context) {
                    const percentage = ((context.raw / totalTarget) * 100).toFixed(1);
                    return context.label + ': ' + context.raw.toLocaleString() + ` (${percentage}%)`;
                  }
                }
              }
            },
            animation: {
              animateRotate: true,
              animateScale: true,
              duration: 2000,
              easing: 'easeInOutQuart'
            }
          },
          plugins: [{
            beforeDraw: function (chart) {
              const width = chart.width;
              const height = chart.height;
              const ctx = chart.ctx;

              ctx.restore();
              const fontSize = (height / 100).toFixed(2);
              ctx.font = fontSize + "em sans-serif";
              ctx.textBaseline = "middle";
              ctx.fillStyle = colors.success;

              const text = achievement + "%";
              const textX = Math.round((width - ctx.measureText(text).width) / 2);
              const textY = height / 2;

              ctx.fillText(text, textX, textY);
              ctx.save();
            }
          }]
        });
      }
    </script>

    <!-- Enhanced Sales Ranking -->
    <script>
      const rankingContainer = document.getElementById('salesRanking');
      if (rankingContainer) {
        let html = '';
        salesRanking.forEach((sales, index) => {
          const rankClass = index < 3 ? `rank-${index + 1}` : 'rank-other';
          html += `
            <div class="ranking-item">
              <div class="rank-number ${rankClass}">${index + 1}</div>
              <div class="ranking-info flex-grow-1">
                <div class="ranking-name">${sales.name}</div>
                <div class="ranking-branch">${sales.branch}</div>
              </div>
              <div class="ranking-stats">
                <div class="ranking-sales">${sales.sales}</div>
                <div class="ranking-achievement">${sales.achievement}%</div>
              </div>
            </div>
          `;
        });
        rankingContainer.innerHTML = html;

        // Add animation delay
        const items = rankingContainer.querySelectorAll('.ranking-item');
        items.forEach((item, index) => {
          item.style.opacity = '0';
          item.style.transform = 'translateX(-20px)';
          setTimeout(() => {
            item.style.transition = 'all 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
          }, index * 100);
        });
      }
    </script>

    <!-- Enhanced Pivot Table -->
    <script>
      $(function () {
        const pivotData = [
          { branch: "Jakarta Pusat", month: "Jan 2024", target: 350, actual: 325, type: "New", product: "KPR" },
          { branch: "Jakarta Pusat", month: "Feb 2024", target: 360, actual: 340, type: "New", product: "KTA" },
          { branch: "Jakarta Pusat", month: "Mar 2024", target: 355, actual: 335, type: "Existing", product: "Investasi" },
          { branch: "Surabaya", month: "Jan 2024", target: 300, actual: 280, type: "Existing", product: "KPR" },
          { branch: "Surabaya", month: "Feb 2024", target: 310, actual: 295, type: "New", product: "KTA" },
          { branch: "Surabaya", month: "Mar 2024", target: 305, actual: 285, type: "Mixed", product: "Asuransi" },
          { branch: "Bandung", month: "Jan 2024", target: 250, actual: 195, type: "New", product: "KPR" },
          { branch: "Bandung", month: "Feb 2024", target: 260, actual: 220, type: "Existing", product: "Investasi" },
          { branch: "Bandung", month: "Mar 2024", target: 255, actual: 210, type: "New", product: "KTA" },
          { branch: "Medan", month: "Jan 2024", target: 200, actual: 160, type: "Mixed", product: "Asuransi" },
          { branch: "Medan", month: "Feb 2024", target: 210, actual: 180, type: "New", product: "KPR" },
          { branch: "Medan", month: "Mar 2024", target: 205, actual: 175, type: "Existing", product: "KTA" }
        ];

      //   $("#output").pivotUI(pivotData, {
      //     renderers: $.extend($.pivotUtilities.renderers, $.pivotUtilities.plotly_renderers),
      //     rendererName: "Table",
      //     rows: ["branch"],
      //     cols: ["month"],
      //     aggregatorName: "Sum",
      //     vals: ["actual"],
      //     rendererOptions: {
      //       table: {
      //         clickCallback: function(e, value, filters, pivotData) {
      //           console.log("Cell clicked:", value, filters);
      //         }
      //       }
      //     }
      //   });
      // });
    </script>

    <!-- Loading Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
          // Add loading animation to charts
          const chartContainers = document.querySelectorAll('.chart-container-inner');
          chartContainers.forEach(container => {
            container.classList.add('loading-shimmer');
            setTimeout(() => {
              container.classList.remove('loading-shimmer');
            }, 1500);
          });

          // Add fade-in animation to cards
          const cards = document.querySelectorAll('.dashboard-card');
          cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
              card.style.transition = 'all 0.6s ease';
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, index * 100);
          });
        });
    </script>
  @endsection