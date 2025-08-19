@extends('layouts.master')
@section('title', 'Dashboard Manager RO')
@section('pageStyle')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
    --info-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-shadow: 0 8px 25px rgba(0,0,0,0.08);
    --card-hover-shadow: 0 15px 45px rgba(0,0,0,0.15);
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    background: #ffffff;
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #2c3e50;
    overflow-x: hidden;
  }

  .dashboard-container {
    background: #ffffff;
    min-height: 100vh;
    padding: 25px;
    position: relative;
  }

  /* Animated Background Elements */
  .dashboard-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
    z-index: -1;
    border-radius: 0 0 50px 50px;
  }

  /* Enhanced Page Header */
  .page-header {
    background: var(--primary-gradient);
    color: white;
    padding: 40px 35px;
    border-radius: 20px;
    margin-bottom: 35px;
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
  }

  .page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
  }

  .page-header h1 {
    margin: 0;
    font-size: 2.8rem;
    font-weight: 700;
    letter-spacing: -0.02em;
  }

  .page-header p {
    margin: 10px 0 0 0;
    opacity: 0.95;
    font-size: 1.2rem;
    font-weight: 300;
  }

  .page-header .header-stats {
    display: flex;
    gap: 30px;
    margin-top: 25px;
    flex-wrap: wrap;
  }

  .header-stat {
    text-align: center;
  }

  .header-stat-number {
    font-size: 2rem;
    font-weight: 700;
    display: block;
  }

  .header-stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 5px;
  }

  /* Enhanced Filter Section */
  .filter-section {
    background: white;
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 35px;
    box-shadow: var(--card-shadow);
    border: 1px solid #f1f3f4;
  }

  .filter-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .form-select, .form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 18px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #fafbfc;
  }

  .form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    background: white;
  }

  .btn-primary {
    background: var(--primary-gradient);
    border: none;
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
  }

  /* Enhanced Stats Cards */
  .stats-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    border: 1px solid #f1f3f4;
  }

  .stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--primary-gradient);
  }

  .stats-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--card-hover-shadow);
    border-color: #667eea20;
  }

  .stats-card-satisfaction::before { background: var(--success-gradient); }
  .stats-card-complaints::before { background: var(--danger-gradient); }
  .stats-card-expiring::before { background: var(--warning-gradient); }
  .stats-card-running::before { background: var(--info-gradient); }

  .stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
  }

  .stats-icon.satisfaction { background: var(--success-gradient); }
  .stats-icon.complaints { background: var(--danger-gradient); }
  .stats-icon.expiring { background: var(--warning-gradient); }
  .stats-icon.running { background: var(--info-gradient); }

  .stats-value {
    font-size: 3rem;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 8px;
    line-height: 1;
  }

  .stats-label {
    font-size: 1.1rem;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 15px;
  }

  .stats-change {
    font-size: 0.9rem;
    padding: 8px 15px;
    border-radius: 25px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
  }

  .stats-change.positive {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
  }

  .stats-change.negative {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
  }

  .stats-change.warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
  }

  /* Enhanced Chart Containers */
  .chart-wrapper {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
    border: 1px solid #f1f3f4;
    position: relative;
  }

  .chart-wrapper:hover {
    box-shadow: var(--card-hover-shadow);
    transform: translateY(-2px);
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
  }

  .chart-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .chart-badge {
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .badge-primary { background: var(--primary-gradient); color: white; }
  .badge-success { background: var(--success-gradient); color: white; }
  .badge-info { background: var(--info-gradient); color: white; }
  .badge-warning { background: var(--warning-gradient); color: white; }

  .chart-container-inner {
    height: 380px;
    position: relative;
  }

  /* Enhanced Tables */
  .table-wrapper {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--card-shadow);
    border: 1px solid #f1f3f4;
    transition: all 0.3s ease;
  }

  .table-wrapper:hover {
    box-shadow: var(--card-hover-shadow);
    transform: translateY(-2px);
  }

  .table {
    margin-bottom: 0;
    font-size: 0.95rem;
  }

  .table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    padding: 18px;
    font-weight: 700;
    color: #495057;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.8px;
    border-radius: 0;
  }

  .table tbody td {
    padding: 18px;
    border-top: 1px solid #f1f3f4;
    vertical-align: middle;
  }

  .table tbody tr {
    transition: all 0.3s ease;
  }

  .table tbody tr:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  /* Enhanced Badges */
  .badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .bg-primary { background: var(--primary-gradient) !important; }
  .bg-success { background: var(--success-gradient) !important; }
  .bg-info { background: var(--info-gradient) !important; }
  .bg-warning { background: var(--warning-gradient) !important; }
  .bg-danger { background: var(--danger-gradient) !important; }

  /* Loading States */
  .loading-spinner {
    display: none;
    text-align: center;
    padding: 40px;
    color: #6c757d;
  }

  .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 4px;
  }

  .loading-text {
    margin-top: 15px;
    font-weight: 600;
    font-size: 1.1rem;
  }

  /* Quick Actions */
  .quick-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .quick-action-btn {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .quick-action-btn.export-excel {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
  }

  .quick-action-btn.export-pdf {
    background: linear-gradient(135deg, #dc3545 0%, #ffc107 100%);
    color: white;
  }

  .quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
  }

  /* Progress Bars */
  .progress-item {
    margin-bottom: 20px;
  }

  .progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
  }

  .progress {
    height: 8px;
    border-radius: 10px;
    background: #f1f3f4;
    overflow: hidden;
  }

  .progress-bar {
    border-radius: 10px;
    transition: width 1s ease-in-out;
  }

  /* Notification Toast */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
  }

  .toast {
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: none;
    min-width: 350px;
  }

  .toast-header {
    border-radius: 12px 12px 0 0;
    font-weight: 600;
  }

  /* Responsive Design */
  @media (max-width: 1200px) {
    .header-stats { gap: 20px; }
    .chart-container-inner { height: 320px; }
  }

  @media (max-width: 768px) {
    .dashboard-container { padding: 15px; }
    .page-header { padding: 25px; }
    .page-header h1 { font-size: 2.2rem; }
    .chart-container-inner { height: 280px; }
    .stats-card { text-align: center; }
    .header-stats { justify-content: center; }
    .quick-actions { justify-content: center; }
  }

  /* Animation Classes */
  .fade-in {
    animation: fadeIn 0.8s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .slide-up {
    animation: slideUp 0.6s ease-out;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Custom Scrollbar */
  ::-webkit-scrollbar {
    width: 8px;
  }

  ::-webkit-scrollbar-track {
    background: #f1f3f4;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb {
    background: var(--primary-gradient);
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #5a67d8;
  }
</style>
@endsection

@section('content')
<div class="container-fluid dashboard-container">
  <!-- Page Header -->
  <!-- <div class="page-header animate__animated animate__fadeInDown">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1><i class="fas fa-tachometer-alt me-3"></i>Dashboard Manager RO</h1>
        <p>Monitoring dan analisis performa Regional Office secara real-time</p>
      </div>
      <div class="col-md-4">
        <div class="header-stats">
          <div class="header-stat">
            <span class="header-stat-number" id="headerTotalRO">3</span>
            <div class="header-stat-label">Total RO</div>
          </div>
          <div class="header-stat">
            <span class="header-stat-number" id="headerActiveContracts">245</span>
            <div class="header-stat-label">Kontrak Aktif</div>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  <!-- Filter Section -->
  <!-- <div class="filter-section animate__animated animate__fadeInUp">
    <div class="filter-title">
      <i class="fas fa-filter"></i>
      Filter & Kontrol Data
    </div>
    <div class="row">
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">Periode</label>
        <select class="form-select" id="filterPeriod">
          <option value="daily">Harian</option>
          <option value="weekly">Mingguan</option>
          <option value="monthly" selected>Bulanan</option>
          <option value="quarterly">Triwulanan</option>
          <option value="yearly">Tahunan</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">Regional Office</label>
        <select class="form-select" id="filterRO">
          <option value="all">Semua RO</option>
          <option value="ro_jakarta">RO Jakarta</option>
          <option value="ro_surabaya">RO Surabaya</option>
          <option value="ro_bandung">RO Bandung</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" class="form-control" id="filterDateStart" value="2024-03-01">
      </div>
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" class="form-control" id="filterDateEnd" value="2024-03-31">
      </div>
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">&nbsp;</label>
        <button class="btn btn-primary w-100 d-block" onclick="loadDashboardData()">
          <i class="fas fa-sync-alt me-2"></i>Refresh Data
        </button>
      </div>
      <div class="col-lg-2 col-md-4 mb-3">
        <label class="form-label">&nbsp;</label>
        <div class="dropdown d-block">
          <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-download me-2"></i>Export
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" onclick="exportToExcel()"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
            <li><a class="dropdown-item" onclick="exportToPDF()"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div> -->

  <!-- Stats Cards -->
  <div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6">
      <div class="stats-card stats-card-satisfaction animate__animated animate__fadeInUp" data-wow-delay="0.1s">
        <div class="stats-header">
          <div class="stats-icon satisfaction">
            <i class="fas fa-smile"></i>
          </div>
          <div class="text-end">
            <div class="stats-value" id="satisfactionValue">4.7</div>
            <div class="stats-label">Kepuasan Pelanggan</div>
          </div>
        </div>
        <div class="stats-change positive" id="satisfactionChange">
          <i class="fas fa-arrow-up"></i>
          ↑ 0.2 dari bulan lalu
        </div>
        <div class="progress-item mt-3">
          <div class="progress">
            <div class="progress-bar bg-success" style="width: 94%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
      <div class="stats-card stats-card-complaints animate__animated animate__fadeInUp" data-wow-delay="0.2s">
        <div class="stats-header">
          <div class="stats-icon complaints">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="text-end">
            <div class="stats-value" id="complaintsValue">9</div>
            <div class="stats-label">Komplain Customer</div>
          </div>
        </div>
        <div class="stats-change negative" id="complaintsChange">
          <i class="fas fa-clock"></i>
          3 belum ditangani
        </div>
        <div class="progress-item mt-3">
          <div class="progress-label">
            <span>Tertangani</span>
            <span>67%</span>
          </div>
          <div class="progress">
            <div class="progress-bar bg-danger" style="width: 67%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
      <div class="stats-card stats-card-expiring animate__animated animate__fadeInUp" data-wow-delay="0.3s">
        <div class="stats-header">
          <div class="stats-icon expiring">
            <i class="fas fa-clock"></i>
          </div>
          <div class="text-end">
            <div class="stats-value" id="expiringValue">15</div>
            <div class="stats-label">Kontrak Akan Berakhir</div>
          </div>
        </div>
        <div class="stats-change warning" id="expiringChange">
          <i class="fas fa-calendar-alt"></i>
          Dalam 30 hari
        </div>
        <div class="progress-item mt-3">
          <div class="progress-label">
            <span>Urgensi</span>
            <span>High</span>
          </div>
          <div class="progress">
            <div class="progress-bar bg-warning" style="width: 85%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
      <div class="stats-card stats-card-running animate__animated animate__fadeInUp" data-wow-delay="0.4s">
        <div class="stats-header">
          <div class="stats-icon running">
            <i class="fas fa-file-contract"></i>
          </div>
          <div class="text-end">
            <div class="stats-value" id="runningValue">245</div>
            <div class="stats-label">Kontrak Berjalan</div>
          </div>
        </div>
        <div class="stats-change positive" id="runningChange">
          <i class="fas fa-arrow-up"></i>
          ↑ 12 kontrak baru
        </div>
        <div class="progress-item mt-3">
          <div class="progress-label">
            <span>Target Tahun</span>
            <span>82%</span>
          </div>
          <div class="progress">
            <div class="progress-bar bg-info" style="width: 82%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 1 -->
  <div class="row">
    <!-- RO Performance Chart -->
    <div class="col-xl-8 col-12">
      <div class="chart-wrapper animate__animated animate__fadeInLeft">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-chart-radar"></i>
            Laporan Per RO (Kunjungan, Follow Up, dll)
          </h5>
          <span class="chart-badge badge-primary">Bulan Ini</span>
        </div>
        <div class="loading-spinner" id="roChartLoading">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="loading-text">Memuat data performa RO...</div>
        </div>
        <div class="chart-container-inner">
          <canvas id="roPerformanceChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Customer Satisfaction Chart -->
    <div class="col-xl-4 col-12">
      <div class="chart-wrapper animate__animated animate__fadeInRight">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-heart"></i>
            Kepuasan Pelanggan
          </h5>
          <span class="chart-badge badge-success">Triwulan</span>
        </div>
        <div class="loading-spinner" id="satisfactionChartLoading">
          <div class="spinner-border text-success" role="status"></div>
          <div class="loading-text">Memuat data kepuasan...</div>
        </div>
        <div class="chart-container-inner">
          <canvas id="customerSatisfactionChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="row">
    <!-- Complaint Categories Chart -->
    <div class="col-xl-6 col-12">
      <div class="chart-wrapper animate__animated animate__fadeInUp">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-chart-pie"></i>
            Kategori Komplain Customer
          </h5>
          <span class="chart-badge badge-warning">Per Kategori</span>
        </div>
        <div class="loading-spinner" id="complaintChartLoading">
          <div class="spinner-border text-warning" role="status"></div>
          <div class="loading-text">Memuat data komplain...</div>
        </div>
        <div class="chart-container-inner">
          <canvas id="complaintCategoriesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Contract Status Chart -->
    <div class="col-xl-6 col-12">
      <div class="chart-wrapper animate__animated animate__fadeInUp">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-chart-bar"></i>
            Status Kontrak Per RO
          </h5>
          <span class="chart-badge badge-info">Real-time</span>
        </div>
        <div class="loading-spinner" id="contractStatusLoading">
          <div class="spinner-border text-info" role="status"></div>
          <div class="loading-text">Memuat status kontrak...</div>
        </div>
        <div class="chart-container-inner">
          <canvas id="contractStatusChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables Row -->
  <div class="row">
    <!-- Expiring Contracts Table -->
    <div class="col-xl-6 col-12">
      <div class="table-wrapper animate__animated animate__fadeInLeft">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-calendar-times"></i>
            Kontrak Akan Berakhir
          </h5>
          <span class="chart-badge badge-warning" id="expiringBadge">15 Kontrak</span>
        </div>
        <div class="loading-spinner" id="expiringTableLoading">
          <div class="spinner-border text-warning" role="status"></div>
          <div class="loading-text">Memuat kontrak akan berakhir...</div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag me-1"></i>No Kontrak</th>
                <th><i class="fas fa-user me-1"></i>Customer</th>
                <th><i class="fas fa-building me-1"></i>RO</th>
                <th><i class="fas fa-calendar me-1"></i>Jatuh Tempo</th>
                <th><i class="fas fa-hourglass me-1"></i>Sisa Waktu</th>
              </tr>
            </thead>
            <tbody id="expiringContractsTable">
              <!-- Data will be loaded via AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Customer Complaints Table -->
    <div class="col-xl-6 col-12">
      <div class="table-wrapper animate__animated animate__fadeInRight">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-exclamation-circle"></i>
            Komplain Customer Terbaru
          </h5>
          <span class="chart-badge badge-danger" id="complaintsBadge">9 Komplain</span>
        </div>
        <div class="loading-spinner" id="complaintsTableLoading">
          <div class="spinner-border text-danger" role="status"></div>
          <div class="loading-text">Memuat data komplain...</div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th><i class="fas fa-calendar me-1"></i>Tanggal</th>
                <th><i class="fas fa-user me-1"></i>Customer</th>
                <th><i class="fas fa-tags me-1"></i>Kategori</th>
                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                <th><i class="fas fa-building me-1"></i>RO</th>
              </tr>
            </thead>
            <tbody id="complaintsTable">
              <!-- Data will be loaded via AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Running Contracts Table -->
  <div class="row">
    <div class="col-12">
      <div class="table-wrapper animate__animated animate__fadeInUp">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-file-contract"></i>
            Kontrak Berjalan
          </h5>
          <div class="d-flex gap-2">
            <span class="chart-badge badge-success" id="runningBadge">245 Kontrak Aktif</span>
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-1"></i>Filter Status
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" onclick="filterContracts('all')">Semua Status</a></li>
                <li><a class="dropdown-item" onclick="filterContracts('active')">Aktif</a></li>
                <li><a class="dropdown-item" onclick="filterContracts('pending')">Pending</a></li>
                <li><a class="dropdown-item" onclick="filterContracts('renewal')">Perpanjangan</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="loading-spinner" id="runningTableLoading">
          <div class="spinner-border text-success" role="status"></div>
          <div class="loading-text">Memuat kontrak berjalan...</div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag me-1"></i>No Kontrak</th>
                <th><i class="fas fa-user me-1"></i>Customer</th>
                <th><i class="fas fa-building me-1"></i>RO</th>
                <th><i class="fas fa-play-circle me-1"></i>Mulai</th>
                <th><i class="fas fa-stop-circle me-1"></i>Berakhir</th>
                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                <th><i class="fas fa-money-bill me-1"></i>Nilai</th>
                <th><i class="fas fa-cogs me-1"></i>Aksi</th>
              </tr>
            </thead>
            <tbody id="runningContractsTable">
              <!-- Data will be loaded via AJAX -->
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <nav class="mt-4">
          <ul class="pagination justify-content-center" id="contractsPagination">
            <!-- Pagination will be generated by JavaScript -->
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Performance Metrics -->
  <div class="row">
    <div class="col-12">
      <div class="chart-wrapper animate__animated animate__fadeInUp">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="fas fa-tachometer-alt"></i>
            Metrik Performa Bulanan
          </h5>
          <span class="chart-badge badge-primary">Trend 6 Bulan</span>
        </div>
        <div class="loading-spinner" id="performanceMetricsLoading">
          <div class="spinner-border text-primary" role="status"></div>
          <div class="loading-text">Memuat metrik performa...</div>
        </div>
        <div class="chart-container-inner">
          <canvas id="performanceMetricsChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Toast Container for Notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer">
  <!-- Toasts will be inserted here -->
</div>
@endsection

@section('pageScript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script>
// Global chart instances
let roPerformanceChart, customerSatisfactionChart, complaintCategoriesChart, contractStatusChart, performanceMetricsChart;

// Enhanced dummy data with more realistic information
const dummyData = {
  stats: {
    satisfaction: { value: 4.7, change: "↑ 0.2 dari bulan lalu", type: "positive", trend: 94 },
    complaints: { value: 9, change: "3 belum ditangani", type: "negative", resolved: 67 },
    expiring: { value: 15, change: "Dalam 30 hari", type: "warning", urgency: 85 },
    running: { value: 245, change: "↑ 12 kontrak baru", type: "positive", target: 82 }
  },
  
  roPerformance: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    datasets: [
      {
        label: 'Jumlah Kunjungan',
        data: [88, 92, 85, 95, 89, 97],
        borderColor: '#667eea',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        borderWidth: 3,
        fill: false,
        tension: 0.4,
        pointRadius: 6,
        pointBackgroundColor: '#667eea',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      },
      {
        label: 'Follow Up Rate (%)',
        data: [82, 88, 79, 91, 85, 93],
        borderColor: '#28a745',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        borderWidth: 3,
        fill: false,
        tension: 0.4,
        pointRadius: 6,
        pointBackgroundColor: '#28a745',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      },
      {
        label: 'Kepuasan Pelanggan',
        data: [4.2, 4.5, 4.3, 4.7, 4.4, 4.6],
        borderColor: '#ffc107',
        backgroundColor: 'rgba(255, 193, 7, 0.1)',
        borderWidth: 3,
        fill: false,
        tension: 0.4,
        pointRadius: 6,
        pointBackgroundColor: '#ffc107',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }
    ]
  },
  
  satisfaction: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    datasets: [{
      label: 'Kepuasan Pelanggan',
      data: [4.3, 4.5, 4.7, 4.6, 4.8, 4.7],
      borderColor: '#28a745',
      backgroundColor: 'rgba(40, 167, 69, 0.1)',
      borderWidth: 4,
      fill: true,
      tension: 0.4,
      pointRadius: 8,
      pointHoverRadius: 10,
      pointBackgroundColor: '#28a745',
      pointBorderColor: '#fff',
      pointBorderWidth: 3
    }]
  },
  
  complaintCategories: {
    labels: ['Pelayanan', 'Teknis', 'Billing', 'Jaringan', 'Sistem', 'Lainnya'],
    data: [3, 2, 1, 2, 1, 0],
    colors: ['#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', '#54a0ff', '#5f27cd']
  },
  
  contractStatus: {
    labels: ['RO Jakarta', 'RO Surabaya', 'RO Bandung'],
    datasets: [
      {
        label: 'Kontrak Aktif',
        data: [98, 85, 62],
        backgroundColor: 'rgba(40, 167, 69, 0.8)',
        borderColor: '#28a745',
        borderWidth: 2
      },
      {
        label: 'Akan Berakhir',
        data: [8, 4, 3],
        backgroundColor: 'rgba(255, 193, 7, 0.8)',
        borderColor: '#ffc107',
        borderWidth: 2
      },
      {
        label: 'Perpanjangan',
        data: [5, 3, 2],
        backgroundColor: 'rgba(23, 162, 184, 0.8)',
        borderColor: '#17a2b8',
        borderWidth: 2
      }
    ]
  },
  
  performanceMetrics: {
    labels: ['Okt 2023', 'Nov 2023', 'Des 2023', 'Jan 2024', 'Feb 2024', 'Mar 2024'],
    datasets: [
      {
        label: 'Jumlah Kunjungan',
        data: [234, 267, 289, 312, 298, 334],
        borderColor: '#667eea',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        yAxisID: 'y',
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 6
      },
      {
        label: 'Follow Up Rate (%)',
        data: [78, 82, 85, 79, 88, 84],
        borderColor: '#28a745',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        yAxisID: 'y1',
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 6
      },
      {
        label: 'Customer Satisfaction',
        data: [4.2, 4.4, 4.3, 4.6, 4.5, 4.7],
        borderColor: '#ffc107',
        backgroundColor: 'rgba(255, 193, 7, 0.1)',
        yAxisID: 'y2',
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 6
      }
    ]
  },
  
  expiringContracts: [
    { no: 'KTRK-2024-001', nama: 'PT. Maju Bersama Teknologi', ro: 'Jakarta', tempo: '2024-04-15', sisa: '15 hari', nilai: 'Rp 125.000.000', priority: 'high' },
    { no: 'KTRK-2024-002', nama: 'CV. Sukses Mandiri Digital', ro: 'Surabaya', tempo: '2024-04-20', sisa: '20 hari', nilai: 'Rp 87.500.000', priority: 'medium' },
    { no: 'KTRK-2024-003', nama: 'UD. Berkah Jaya Abadi', ro: 'Jakarta', tempo: '2024-04-25', sisa: '25 hari', nilai: 'Rp 45.000.000', priority: 'medium' },
    { no: 'KTRK-2024-004', nama: 'PT. Global Tech Solutions', ro: 'Bandung', tempo: '2024-04-28', sisa: '28 hari', nilai: 'Rp 156.000.000', priority: 'high' },
    { no: 'KTRK-2024-005', nama: 'CV. Digital Innovation', ro: 'Surabaya', tempo: '2024-05-02', sisa: '32 hari', nilai: 'Rp 67.800.000', priority: 'low' }
  ],
  
  complaints_list: [
    { tanggal: '2024-03-15', nama: 'Siti Rahayu', kategori: 'Pelayanan', status: 'Belum ditangani', ro: 'Surabaya', priority: 'High', id: 'COMP-001' },
    { tanggal: '2024-03-14', nama: 'Ahmad Fauzi', kategori: 'Teknis', status: 'Dalam proses', ro: 'Jakarta', priority: 'Medium', id: 'COMP-002' },
    { tanggal: '2024-03-13', nama: 'Linda Sari', kategori: 'Billing', status: 'Selesai', ro: 'Bandung', priority: 'Low', id: 'COMP-003' },
    { tanggal: '2024-03-12', nama: 'Budi Santoso', kategori: 'Jaringan', status: 'Belum ditangani', ro: 'Jakarta', priority: 'High', id: 'COMP-004' },
    { tanggal: '2024-03-11', nama: 'Maya Putri', kategori: 'Pelayanan', status: 'Dalam proses', ro: 'Surabaya', priority: 'Medium', id: 'COMP-005' },
    { tanggal: '2024-03-10', nama: 'Rizky Pratama', kategori: 'Sistem', status: 'Selesai', ro: 'Bandung', priority: 'Low', id: 'COMP-006' }
  ],
  
  runningContracts: [
    { no: 'KTRK-2023-150', nama: 'PT. Abadi Jaya Sentosa', ro: 'Jakarta', mulai: '2023-06-15', berakhir: '2024-06-15', status: 'Aktif', nilai: 'Rp 185.000.000', type: 'Premium' },
    { no: 'KTRK-2023-151', nama: 'CV. Mitra Sejati Mandiri', ro: 'Surabaya', mulai: '2023-07-20', berakhir: '2024-07-20', status: 'Aktif', nilai: 'Rp 125.000.000', type: 'Standard' },
    { no: 'KTRK-2023-152', nama: 'UD. Sumber Rezeki', ro: 'Bandung', mulai: '2023-08-10', berakhir: '2024-08-10', status: 'Aktif', nilai: 'Rp 67.500.000', type: 'Basic' },
    { no: 'KTRK-2023-153', nama: 'PT. Teknologi Masa Depan', ro: 'Jakarta', mulai: '2023-09-05', berakhir: '2024-09-05', status: 'Perpanjangan', nilai: 'Rp 245.000.000', type: 'Enterprise' },
    { no: 'KTRK-2023-154', nama: 'CV. Inovasi Digital Prima', ro: 'Surabaya', mulai: '2023-10-12', berakhir: '2024-10-12', status: 'Aktif', nilai: 'Rp 156.800.000', type: 'Premium' },
    { no: 'KTRK-2023-155', nama: 'PT. Solusi Bisnis Terpadu', ro: 'Bandung', mulai: '2023-11-18', berakhir: '2024-11-18', status: 'Aktif', nilai: 'Rp 98.750.000', type: 'Standard' },
    { no: 'KTRK-2023-156', nama: 'UD. Cahaya Mandiri', ro: 'Jakarta', mulai: '2023-12-22', berakhir: '2024-12-22', status: 'Pending', nilai: 'Rp 78.900.000', type: 'Basic' },
    { no: 'KTRK-2024-001', nama: 'CV. Mega Karya Sejahtera', ro: 'Surabaya', mulai: '2024-01-15', berakhir: '2025-01-15', status: 'Aktif', nilai: 'Rp 134.500.000', type: 'Standard' }
  ]
};

// Initialize dashboard with enhanced animations
function initializeDashboard() {
  console.log('Initializing Enhanced Manager RO Dashboard...');
  
  // Add entrance animations
  addEntranceAnimations();
  
  // Load all data immediately for better UX
  updateStatsCards();
  setTimeout(() => loadCharts(), 300);
  setTimeout(() => loadTables(), 600);
  
  // Initialize real-time updates
  setTimeout(() => startRealTimeUpdates(), 2000);
  
  console.log('Dashboard initialization completed');
}

// Add entrance animations
function addEntranceAnimations() {
  const elements = document.querySelectorAll('.stats-card, .chart-wrapper, .table-wrapper');
  elements.forEach((element, index) => {
    element.style.animationDelay = `${index * 0.1}s`;
  });
}

// Enhanced load dashboard data with better loading states
function loadDashboardData() {
  console.log('Refreshing dashboard data...');
  
  showLoadingSpinners();
  showToast('Memuat ulang data dashboard...', 'info');
  
  // Simulate realistic loading times
  Promise.all([
    simulateAPICall('stats', 800),
    simulateAPICall('charts', 1200),
    simulateAPICall('tables', 1000)
  ]).then(() => {
    updateStatsCards();
    loadCharts();
    loadTables();
    hideLoadingSpinners();
    showToast('Data berhasil diperbarui!', 'success');
  }).catch(error => {
    console.error('Error loading data:', error);
    hideLoadingSpinners();
    showToast('Gagal memuat data. Silakan coba lagi.', 'error');
  });
}

// Simulate API calls
function simulateAPICall(type, delay) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (Math.random() > 0.05) { // 95% success rate
        resolve(`${type} data loaded`);
      } else {
        reject(`Failed to load ${type} data`);
      }
    }, delay);
  });
}

// Enhanced loading spinner management
function showLoadingSpinners() {
  const spinners = document.querySelectorAll('.loading-spinner');
  const charts = document.querySelectorAll('.chart-container-inner');
  const tables = document.querySelectorAll('.table-responsive');
  
  spinners.forEach(spinner => {
    spinner.style.display = 'block';
    spinner.classList.add('animate__animated', 'animate__fadeIn');
  });
  
  charts.forEach(chart => chart.style.display = 'none');
  tables.forEach(table => table.style.display = 'none');
}

function hideLoadingSpinners() {
  const spinners = document.querySelectorAll('.loading-spinner');
  const charts = document.querySelectorAll('.chart-container-inner');
  const tables = document.querySelectorAll('.table-responsive');
  
  spinners.forEach(spinner => {
    spinner.classList.add('animate__animated', 'animate__fadeOut');
    setTimeout(() => spinner.style.display = 'none', 300);
  });
  
  setTimeout(() => {
    charts.forEach(chart => {
      chart.style.display = 'block';
      chart.classList.add('animate__animated', 'animate__fadeIn');
    });
    
    tables.forEach(table => {
      table.style.display = 'block';
      table.classList.add('animate__animated', 'animate__fadeIn');
    });
  }, 300);
}

// Enhanced stats cards with animations
function updateStatsCards() {
  console.log('Updating stats cards...');
  
  const stats = dummyData.stats;
  
  // Update with counter animation
  animateValue('satisfactionValue', 0, stats.satisfaction.value, 1000, 1);
  animateValue('complaintsValue', 0, stats.complaints.value, 1000, 0);
  animateValue('expiringValue', 0, stats.expiring.value, 1000, 0);
  animateValue('runningValue', 0, stats.running.value, 1000, 0);
  
  // Update change indicators
  document.getElementById('satisfactionChange').innerHTML = `<i class="fas fa-arrow-up"></i> ${stats.satisfaction.change}`;
  document.getElementById('complaintsChange').innerHTML = `<i class="fas fa-clock"></i> ${stats.complaints.change}`;
  document.getElementById('expiringChange').innerHTML = `<i class="fas fa-calendar-alt"></i> ${stats.expiring.change}`;
  document.getElementById('runningChange').innerHTML = `<i class="fas fa-arrow-up"></i> ${stats.running.change}`;
  
  // Update progress bars
  setTimeout(() => {
    updateProgressBar('.stats-card-satisfaction .progress-bar', stats.satisfaction.trend);
    updateProgressBar('.stats-card-complaints .progress-bar', stats.complaints.resolved);
    updateProgressBar('.stats-card-expiring .progress-bar', stats.expiring.urgency);
    updateProgressBar('.stats-card-running .progress-bar', stats.running.target);
  }, 500);
}

// Animate number counter
function animateValue(id, start, end, duration, decimals = 0) {
  const element = document.getElementById(id);
  if (!element) return;
  
  const range = end - start;
  let current = start;
  const increment = end > start ? 1 : -1;
  const stepTime = Math.abs(Math.floor(duration / range));
  
  const timer = setInterval(() => {
    current += increment;
    element.textContent = decimals > 0 ? current.toFixed(decimals) : current;
    
    if (current === end) {
      clearInterval(timer);
    }
  }, stepTime);
}

// Update progress bar with animation
function updateProgressBar(selector, percentage) {
  const progressBar = document.querySelector(selector);
  if (progressBar) {
    progressBar.style.width = `${percentage}%`;
  }
}

// Enhanced chart loading with better error handling
function loadCharts() {
  console.log('Loading all charts...');
  
  const chartLoaders = [
    () => loadROPerformanceChart(),
    () => loadCustomerSatisfactionChart(),
    () => loadComplaintCategoriesChart(),
    () => loadContractStatusChart(),
    () => loadPerformanceMetricsChart()
  ];
  
  chartLoaders.forEach((loader, index) => {
    setTimeout(() => {
      try {
        loader();
      } catch (error) {
        console.error(`Error loading chart ${index + 1}:`, error);
        showToast(`Gagal memuat grafik ${index + 1}`, 'error');
      }
    }, index * 200);
  });
}

// RO Performance Horizontal Bar Chart (Fixed)
function loadROPerformanceChart() {
  const ctx = document.getElementById('roPerformanceChart');
  if (!ctx) return;
  
  if (roPerformanceChart) roPerformanceChart.destroy();
  
  roPerformanceChart = new Chart(ctx, {
    type: 'bar',
    data: dummyData.roPerformance,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'x', // This makes it horizontal
      plugins: {
        legend: {
          position: 'top',
          labels: {
            padding: 25,
            usePointStyle: true,
            font: { size: 12, weight: '600' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: '#667eea',
          borderWidth: 1
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          max: 100,
          grid: {
            color: 'rgba(0,0,0,0.1)',
            lineWidth: 1
          },
          ticks: {
            font: { size: 11 }
          }
        },
        y: {
          grid: {
            display: false
          },
          ticks: {
            font: { size: 11, weight: '600' }
          }
        }
      },
      elements: {
        bar: {
          borderRadius: 8,
          borderWidth: 2
        }
      }
    }
  });
  
  console.log('RO Performance Chart loaded successfully');
}

// Customer Satisfaction Gauge Chart (Fixed)
function loadCustomerSatisfactionChart() {
  const ctx = document.getElementById('customerSatisfactionChart');
  if (!ctx) return;
  
  if (customerSatisfactionChart) customerSatisfactionChart.destroy();
  
  customerSatisfactionChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sangat Puas', 'Puas', 'Cukup', 'Kurang'],
      datasets: [{
        data: [47, 35, 15, 3],
        backgroundColor: [
          'rgba(40, 167, 69, 0.8)',
          'rgba(23, 162, 184, 0.8)',
          'rgba(255, 193, 7, 0.8)',
          'rgba(220, 53, 69, 0.8)'
        ],
        borderWidth: 0,
        hoverBorderWidth: 4,
        hoverBorderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'right',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: { size: 11, weight: '600' },
            generateLabels: function(chart) {
              const data = chart.data;
              if (data.labels.length && data.datasets.length) {
                return data.labels.map((label, i) => {
                  const value = data.datasets[0].data[i];
                  const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return {
                    text: `${label} (${percentage}%)`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    pointStyle: 'circle',
                    hidden: false,
                    index: i
                  };
                });
              }
              return [];
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed || 0;
              return `${label}: ${value}%`;
            }
          }
        }
      }
    }
  });
  
  console.log('Customer Satisfaction Chart loaded successfully');
}

// Complaint Categories Pie Chart (Fixed)
function loadComplaintCategoriesChart() {
  const ctx = document.getElementById('complaintCategoriesChart');
  if (!ctx) return;
  
  if (complaintCategoriesChart) complaintCategoriesChart.destroy();
  
  complaintCategoriesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: dummyData.complaintCategories.labels,
      datasets: [{
        data: dummyData.complaintCategories.data,
        backgroundColor: dummyData.complaintCategories.colors,
        borderWidth: 0,
        hoverBorderWidth: 4,
        hoverBorderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: { size: 11, weight: '600' },
            generateLabels: function(chart) {
              const data = chart.data;
              if (data.labels.length && data.datasets.length) {
                return data.labels.map((label, i) => {
                  const value = data.datasets[0].data[i];
                  const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                  const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                  return {
                    text: `${label} (${value}) - ${percentage}%`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    pointStyle: 'circle',
                    hidden: false,
                    index: i
                  };
                });
              }
              return [];
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
              return `${label}: ${value} komplain (${percentage}%)`;
            }
          }
        }
      }
    }
  });
  
  console.log('Complaint Categories Chart loaded successfully');
}

// Contract Status Bar Chart
function loadContractStatusChart() {
  const ctx = document.getElementById('contractStatusChart');
  if (!ctx) return;
  
  if (contractStatusChart) contractStatusChart.destroy();
  
  contractStatusChart = new Chart(ctx, {
    type: 'bar',
    data: dummyData.contractStatus,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: { size: 12, weight: '600' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: '#667eea',
          borderWidth: 1
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0,0,0,0.1)',
            lineWidth: 1
          },
          ticks: {
            font: { size: 11 }
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: { size: 11, weight: '600' }
          }
        }
      },
      elements: {
        bar: {
          borderRadius: 8,
          borderWidth: 2
        }
      }
    }
  });
  
  console.log('Contract Status Chart loaded successfully');
}

// Performance Metrics Multi-line Chart
function loadPerformanceMetricsChart() {
  const ctx = document.getElementById('performanceMetricsChart');
  if (!ctx) return;
  
  if (performanceMetricsChart) performanceMetricsChart.destroy();
  
  performanceMetricsChart = new Chart(ctx, {
    type: 'line',
    data: dummyData.performanceMetrics,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false
      },
      plugins: {
        legend: {
          position: 'top',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: { size: 12, weight: '600' }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: '#667eea',
          borderWidth: 1
        }
      },
      scales: {
        x: {
          display: true,
          grid: {
            display: false
          },
          ticks: {
            font: { size: 11 }
          }
        },
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Jumlah Kunjungan',
            font: { size: 12, weight: '600' }
          },
          grid: {
            color: 'rgba(0,0,0,0.1)'
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Follow Up Rate (%)',
            font: { size: 12, weight: '600' }
          },
          grid: {
            drawOnChartArea: false
          }
        },
        y2: {
          type: 'linear',
          display: false,
          min: 3,
          max: 5
        }
      }
    }
  });
  
  console.log('Performance Metrics Chart loaded successfully');
}

// Enhanced table loading with better formatting
function loadTables() {
  console.log('Loading all tables...');
  
  const tableLoaders = [
    () => loadExpiringContractsTable(),
    () => loadComplaintsTable(),
    () => loadRunningContractsTable()
  ];
  
  tableLoaders.forEach((loader, index) => {
    setTimeout(() => {
      try {
        loader();
      } catch (error) {
        console.error(`Error loading table ${index + 1}:`, error);
        showToast(`Gagal memuat tabel ${index + 1}`, 'error');
      }
    }, index * 200);
  });
}

// Enhanced Expiring Contracts Table
function loadExpiringContractsTable() {
  const tbody = document.getElementById('expiringContractsTable');
  if (!tbody) return;
  
  tbody.innerHTML = '';
  
  dummyData.expiringContracts.forEach((contract, index) => {
    const priorityClass = contract.priority === 'high' ? 'bg-danger' : 
                         contract.priority === 'medium' ? 'bg-warning text-dark' : 'bg-info';
    
    const sisaClass = parseInt(contract.sisa) <= 15 ? 'text-danger fw-bold' : 
                     parseInt(contract.sisa) <= 25 ? 'text-warning fw-bold' : 'text-muted';
    
    const row = `
      <tr class="animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
        <td>
          <strong class="text-primary">${contract.no}</strong>
          <br><small class="text-muted">${contract.nilai}</small>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-building text-primary"></i>
            </div>
            <div>
              <strong>${contract.nama}</strong>
              <br><small class="text-muted">Enterprise Client</small>
            </div>
          </div>
        </td>
        <td><span class="badge bg-primary">${contract.ro}</span></td>
        <td>
          <strong>${contract.tempo}</strong>
          <br><small class="text-muted">Tanggal berakhir</small>
        </td>
        <td>
          <span class="badge ${priorityClass}">${contract.sisa}</span>
          <br><small class="${sisaClass}">Prioritas ${contract.priority}</small>
        </td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
  
  // Update badge count
  document.getElementById('expiringBadge').textContent = `${dummyData.expiringContracts.length} Kontrak`;
  
  console.log('Expiring contracts table loaded with', dummyData.expiringContracts.length, 'records');
}

// Enhanced Complaints Table
function loadComplaintsTable() {
  const tbody = document.getElementById('complaintsTable');
  if (!tbody) return;
  
  tbody.innerHTML = '';
  
  dummyData.complaints_list.forEach((complaint, index) => {
    let statusClass = 'bg-success';
    let statusIcon = 'fas fa-check-circle';
    if (complaint.status === 'Belum ditangani') {
      statusClass = 'bg-danger';
      statusIcon = 'fas fa-exclamation-triangle';
    }
    if (complaint.status === 'Dalam proses') {
      statusClass = 'bg-warning text-dark';
      statusIcon = 'fas fa-clock';
    }
    
    const priorityClass = complaint.priority === 'High' ? 'text-danger' : 
                         complaint.priority === 'Medium' ? 'text-warning' : 'text-info';
    
    const row = `
      <tr class="animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
        <td>
          <strong>${complaint.tanggal}</strong>
          <br><small class="text-muted">${complaint.id}</small>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-user text-secondary"></i>
            </div>
            <div>
              <strong>${complaint.nama}</strong>
              <br><small class="${priorityClass}">${complaint.priority} Priority</small>
            </div>
          </div>
        </td>
        <td><span class="badge bg-info">${complaint.kategori}</span></td>
        <td>
          <span class="badge ${statusClass}">
            <i class="${statusIcon} me-1"></i>${complaint.status}
          </span>
        </td>
        <td><span class="badge bg-primary">${complaint.ro}</span></td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
  
  // Update badge count
  document.getElementById('complaintsBadge').textContent = `${dummyData.complaints_list.length} Komplain`;
  
  console.log('Complaints table loaded with', dummyData.complaints_list.length, 'records');
}

// Enhanced Running Contracts Table with Pagination
let currentPage = 1;
const itemsPerPage = 5;

function loadRunningContractsTable() {
  const tbody = document.getElementById('runningContractsTable');
  if (!tbody) return;
  
  tbody.innerHTML = '';
  
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const currentData = dummyData.runningContracts.slice(startIndex, endIndex);
  
  currentData.forEach((contract, index) => {
    let statusClass = 'bg-success';
    let statusIcon = 'fas fa-check-circle';
    if (contract.status === 'Pending') {
      statusClass = 'bg-warning text-dark';
      statusIcon = 'fas fa-clock';
    }
    if (contract.status === 'Perpanjangan') {
      statusClass = 'bg-info';
      statusIcon = 'fas fa-sync-alt';
    }
    
    const typeClass = contract.type === 'Enterprise' ? 'bg-dark' :
                     contract.type === 'Premium' ? 'bg-primary' :
                     contract.type === 'Standard' ? 'bg-secondary' : 'bg-light text-dark';
    
    const row = `
      <tr class="animate__animated animate__fadeInUp" style="animation-delay: ${index * 0.1}s">
        <td>
          <strong class="text-primary">${contract.no}</strong>
          <br><span class="badge ${typeClass}">${contract.type}</span>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-building text-primary"></i>
            </div>
            <div>
              <strong>${contract.nama}</strong>
              <br><small class="text-muted">Active Client</small>
            </div>
          </div>
        </td>
        <td><span class="badge bg-primary">${contract.ro}</span></td>
        <td>
          <strong>${contract.mulai}</strong>
          <br><small class="text-muted">Start date</small>
        </td>
        <td>
          <strong>${contract.berakhir}</strong>
          <br><small class="text-muted">End date</small>
        </td>
        <td>
          <span class="badge ${statusClass}">
            <i class="${statusIcon} me-1"></i>${contract.status}
          </span>
        </td>
        <td>
          <strong class="text-success">${contract.nilai}</strong>
          <br><small class="text-muted">Contract value</small>
        </td>
        <td>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary btn-sm" onclick="viewContract('${contract.no}')" title="Lihat Detail">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="editContract('${contract.no}')" title="Edit">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="renewContract('${contract.no}')" title="Perpanjang">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
  
  // Update badge count
  document.getElementById('runningBadge').textContent = `${dummyData.runningContracts.length} Kontrak Aktif`;
  
  // Generate pagination
  generatePagination();
  
  console.log('Running contracts table loaded with', currentData.length, 'records (page', currentPage, ')');
}

// Generate pagination
function generatePagination() {
  const totalPages = Math.ceil(dummyData.runningContracts.length / itemsPerPage);
  const pagination = document.getElementById('contractsPagination');
  
  if (!pagination) return;
  
  let paginationHTML = '';
  
  // Previous button
  paginationHTML += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">
        <i class="fas fa-chevron-left"></i>
      </a>
    </li>
  `;
  
  // Page numbers
  for (let i = 1; i <= totalPages; i++) {
    if (i === currentPage || i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
      paginationHTML += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
          <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
        </li>
      `;
    } else if (i === currentPage - 2 || i === currentPage + 2) {
      paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
  }
  
  // Next button
  paginationHTML += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">
        <i class="fas fa-chevron-right"></i>
      </a>
    </li>
  `;
  
  pagination.innerHTML = paginationHTML;
}

// Change page function
function changePage(page) {
  const totalPages = Math.ceil(dummyData.runningContracts.length / itemsPerPage);
  if (page < 1 || page > totalPages) return;
  
  currentPage = page;
  loadRunningContractsTable();
}

// Contract action functions
function viewContract(contractNo) {
  showToast(`Melihat detail kontrak: ${contractNo}`, 'info');
  // Add modal or redirect logic here
}

function editContract(contractNo) {
  showToast(`Mengedit kontrak: ${contractNo}`, 'info');
  // Add edit logic here
}

function renewContract(contractNo) {
  showToast(`Memperpanjang kontrak: ${contractNo}`, 'success');
  // Add renewal logic here
}

// Filter contracts
function filterContracts(status) {
  // Implement filtering logic
  showToast(`Filter diterapkan: ${status}`, 'info');
}

// Enhanced toast notification system
function showToast(message, type = 'info', duration = 5000) {
  const toastContainer = document.getElementById('toastContainer');
  if (!toastContainer) return;
  
  const toastId = 'toast-' + Date.now();
  const iconMap = {
    success: 'fas fa-check-circle',
    error: 'fas fa-exclamation-triangle',
    warning: 'fas fa-exclamation-circle',
    info: 'fas fa-info-circle'
  };
  
  const colorMap = {
    success: 'text-success',
    error: 'text-danger',
    warning: 'text-warning',
    info: 'text-primary'
  };
  
  const toastHTML = `
    <div class="toast animate__animated animate__slideInRight" role="alert" id="${toastId}" data-bs-autohide="true" data-bs-delay="${duration}">
      <div class="toast-header">
        <i class="${iconMap[type]} ${colorMap[type]} me-2"></i>
        <strong class="me-auto">Dashboard Manager RO</strong>
        <small class="text-muted">Sekarang</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    </div>
  `;
  
  toastContainer.insertAdjacentHTML('beforeend', toastHTML);
  
  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement);
  toast.show();
  
  // Remove toast after it's hidden
  toastElement.addEventListener('hidden.bs.toast', () => {
    toastElement.remove();
  });
}

// Export functions
function exportToExcel() {
  showToast('Memproses export ke Excel...', 'info');
  
  // Simulate export process
  setTimeout(() => {
    showToast('Data berhasil diexport ke Excel!', 'success');
    // In real implementation, trigger actual Excel download
  }, 2000);
}

function exportToPDF() {
  showToast('Memproses export ke PDF...', 'info');
  
  // Simulate export process
  setTimeout(() => {
    showToast('Laporan PDF berhasil digenerate!', 'success');
    // In real implementation, trigger actual PDF download
  }, 2500);
}

// Real-time updates simulation
function startRealTimeUpdates() {
  console.log('Starting real-time updates...');
  
  setInterval(() => {
    // Simulate random data updates
    const randomSatisfaction = (4.5 + Math.random() * 0.5).toFixed(1);
    const randomComplaints = Math.floor(Math.random() * 15) + 5;
    const randomExpiring = Math.floor(Math.random() * 20) + 10;
    const randomRunning = Math.floor(Math.random() * 50) + 220;
    
    // Update header stats
    if (document.getElementById('headerActiveContracts')) {
      document.getElementById('headerActiveContracts').textContent = randomRunning;
    }
    
    // Update charts with new data
    if (customerSatisfactionChart && Math.random() > 0.7) {
      // Occasionally update satisfaction chart
      const datasets = customerSatisfactionChart.data.datasets[0].data;
      datasets[0] = Math.floor(Math.random() * 10) + 40; // Sangat Puas
      datasets[1] = Math.floor(Math.random() * 10) + 30; // Puas  
      datasets[2] = Math.floor(Math.random() * 10) + 10; // Cukup
      datasets[3] = Math.floor(Math.random() * 5) + 2;   // Kurang
      customerSatisfactionChart.update('none');
    }
    
    // Show periodic update notification
    if (Math.random() > 0.8) {
      showToast('Data telah diperbarui secara otomatis', 'info', 3000);
    }
    
  }, 30000); // Update every 30 seconds
}

// Filter event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Filter Period
  const filterPeriod = document.getElementById('filterPeriod');
  if (filterPeriod) {
    filterPeriod.addEventListener('change', function() {
      const period = this.value;
      showToast(`Filter periode diubah ke: ${period}`, 'info');
      setTimeout(() => loadDashboardData(), 500);
    });
  }
  
  // Filter RO
  const filterRO = document.getElementById('filterRO');
  if (filterRO) {
    filterRO.addEventListener('change', function() {
      const ro = this.options[this.selectedIndex].text;
      showToast(`Filter RO diubah ke: ${ro}`, 'info');
      setTimeout(() => loadDashboardData(), 500);
    });
  }
  
  // Filter Date Start
  const filterDateStart = document.getElementById('filterDateStart');
  if (filterDateStart) {
    filterDateStart.addEventListener('change', function() {
      showToast('Filter tanggal mulai diperbarui', 'info');
      setTimeout(() => loadDashboardData(), 500);
    });
  }
  
  // Filter Date End
  const filterDateEnd = document.getElementById('filterDateEnd');
  if (filterDateEnd) {
    filterDateEnd.addEventListener('change', function() {
      showToast('Filter tanggal akhir diperbarui', 'info');
      setTimeout(() => loadDashboardData(), 500);
    });
  }
  
  // Initialize dashboard
  console.log('DOM loaded, initializing dashboard...');
  setTimeout(() => initializeDashboard(), 100);
});

// Handle window resize for responsive charts
window.addEventListener('resize', function() {
  setTimeout(() => {
    if (roPerformanceChart) roPerformanceChart.resize();
    if (customerSatisfactionChart) customerSatisfactionChart.resize();
    if (complaintCategoriesChart) complaintCategoriesChart.resize();
    if (contractStatusChart) contractStatusChart.resize();
    if (performanceMetricsChart) performanceMetricsChart.resize();
  }, 100);
});

// Performance monitoring
console.log('Enhanced Manager RO Dashboard script loaded successfully');
</script>
@endsection 
        <!-- hoverBorderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: { size: 11, weight: '600' }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.parsed || 0;
              return `${label}: ${value}%`;
            }
          }
        }
      }
    }
  });
  
  // Add center text
  const centerText = {
    id: 'centerText',
    beforeDatasetsDraw: function(chart) {
      const ctx = chart.ctx;
      ctx.save();
      
      const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
      const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
      
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.font = 'bold 24px Inter';
      ctx.fillStyle = '#2c3e50';
      ctx.fillText('4.7', centerX, centerY - 5);
      
      ctx.font = '12px Inter';
      ctx.fillStyle = '#6c757d';
      ctx.fillText('Rating', centerX, centerY + 20);
      
      ctx.restore();
    }
  };
  
  Chart.register(centerText);
  
  console.log('Customer Satisfaction Chart loaded successfully');
}

// Complaint Categories Pie Chart
function loadComplaintCategoriesChart() {
  const ctx = document.getElementById('complaintCategoriesChart');
  if (!ctx) return;
  
  if (complaintCategoriesChart) complaintCategoriesChart.destroy();
  
  complaintCategoriesChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: dummyData.complaintCategories.labels,
      datasets: [{
        data: dummyData.complaintCategories.data,
        backgroundColor: dummyData.complaintCategories.colors,
        borderWidth: 0,
        hoverBorderWidth: -->