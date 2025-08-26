@extends('layouts.master')
@section('title', 'Dashboard Manager Sales')
@section('pageStyle')
<style>
  body {
    background-color: #ffffff !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  
  .dashboard-container {
    background-color: #ffffff;
    min-height: 100vh;
    padding: 20px;
  }
  
  .section-header {
    color: #1a1a1a;
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    text-align: center;
  }
  
  .section-header::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
  }
  
  .welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  }
  
  .welcome-text {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 10px;
  }
  
  .welcome-subtext {
    font-size: 1.1rem;
    opacity: 0.9;
  }
  
  /* Enhanced Stats Cards */
  .stats-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 25px;
    margin-top: 50px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
    height: 200px;
  }
  
  .stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }
  
  .stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  }
  
  .stats-card-leads::before {
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
  }
  
  .stats-card-deals::before {
    background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
  }
  
  .stats-card-complaints::before {
    background: linear-gradient(90deg, #ff6b6b 0%, #ffa500 100%);
  }
  
  .stats-card-revenue::before {
    background: linear-gradient(90deg, #96e6a1 0%, #4ecdc4 100%);
  }
  
  .stats-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 32px;
    color: #ffffff;
  }
  
  .stats-icon.performance {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }
  
  .stats-icon.leads {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }
  
  .stats-icon.deals {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  }
  
  .stats-icon.complaints {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
  }
  
  .stats-icon.revenue {
    background: linear-gradient(135deg, #96e6a1 0%, #4ecdc4 100%);
  }
  
  .stats-value {
    font-size: 2.8rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 8px;
    line-height: 1;
  }
  
  .stats-label {
    font-size: 1rem;
    color: #666;
    font-weight: 600;
    margin-bottom: 15px;
  }
  
  .stats-change {
    font-size: 0.9rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 25px;
    display: inline-block;
  }
  
  .stats-change.positive {
    background-color: #e8f5e8;
    color: #28a745;
  }
  
  .stats-change.negative {
    background-color: #ffeaea;
    color: #dc3545;
  }
  
  .stats-change.warning {
    background-color: #fff8e1;
    color: #f57c00;
  }
  
  /* Chart Containers */
  .chart-wrapper {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
  }
  
  .chart-wrapper:hover {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
  }
  
  .chart-header {
    padding: 25px 30px;
    border-bottom: 2px solid #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  }
  
  .chart-title {
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
  }
  
  .chart-title i {
    margin-right: 10px;
    color: #667eea;
  }
  
  .chart-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .badge-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  
  .badge-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
  }
  
  .badge-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
  }
  
  .badge-warning {
    background: linear-gradient(135deg, #ffd93d 0%, #ff9a9e 100%);
    color: white;
  }
  
  .chart-container-inner {
    padding: 30px;
    height: 350px;
    position: relative;
  }
  
  .chart-container-inner.small {
    height: 300px;
  }
  
  /* Table Styles */
  .table-wrapper {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    overflow: hidden;
    margin-bottom: 30px;
  }
  
  .table {
    background-color: #ffffff;
    margin-bottom: 0;
    border-radius: 0;
  }
  
  .table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-color: #e9ecef;
    font-weight: 700;
    color: #1a1a1a;
    font-size: 0.95rem;
    padding: 20px 15px;
    border-bottom: 2px solid #e9ecef;
  }
  
  .table tbody tr {
    transition: all 0.2s ease;
  }
  
  .table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
  }
  
  .table td {
    vertical-align: middle;
    padding: 18px 15px;
    border-color: #f0f0f0;
  }
  
  /* Action Buttons */
  .btn-action {
    padding: 8px 16px;
    font-size: 0.85rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }
  
  /* Status Badges */
  .status-badge {
    padding: 6px 12px;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .status-new {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
  }
  
  .status-quotation {
    background: linear-gradient(135deg, #ffd93d 0%, #ff9a9e 100%);
    color: white;
  }
  
  .status-contract {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
  }
  
  .status-pending {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    color: #fff;
  }
  
  .status-resolved {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
  }
  
  .status-urgent {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
    color: white;
  }
  
  .priority-high {
    color: #dc3545 !important;
    font-weight: 700;
  }
  
  .priority-medium {
    color: #ffc107 !important;
    font-weight: 700;
  }
  
  .priority-low {
    color: #28a745 !important;
    font-weight: 700;
  }
  
  /* Loading Animation */
  .loading {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  
  .spinner {
    border: 4px solid #f0f0f0;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* Refresh Button */
  .refresh-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    z-index: 1000;
  }
  
  .refresh-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
  }
  
  .refresh-btn:active {
    transform: scale(0.95);
  }
  
  /* Progress Ring */
  .progress-ring {
    transform: rotate(-90deg);
  }
  
  .progress-ring__circle {
    stroke: #e6e6e6;
    stroke-width: 8;
    fill: transparent;
    transition: stroke-dasharray 0.5s ease-in-out;
  }
  
  .progress-ring__circle.active {
    stroke: #667eea;
  }
  
  /* Sales Performance Cards */
  .sales-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }
  
  .sales-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
  }
  
  .sales-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
  }
  
  /* Responsive Design */
  @media (max-width: 768px) {
    .dashboard-container {
      padding: 10px;
    }
    
    .section-header {
      font-size: 2rem;
    }
    
    .stats-card {
      padding: 20px;
      margin-top: 25px;
      height: auto;
    }
    
    .stats-value {
      font-size: 2.2rem;
    }
    
    .chart-container-inner {
      padding: 20px;
      height: 250px;
    }
  }
  
  /* Animation Classes */
  .fade-in {
    animation: fadeIn 0.5s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .slide-up {
    animation: slideUp 0.6s ease-out;
  }
  
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  <!-- Welcome Section -->
  <!-- <div class="row">
    <div class="col-12">
      <div class="welcome-card fade-in">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="welcome-text">
              <i class="mdi mdi-chart-line me-2"></i>Dashboard Manager Sales
            </div>
            <div class="welcome-subtext">
              Monitor performa tim sales dan tingkatkan produktivitas secara real-time
            </div>
          </div>
          <div class="col-md-4 text-end">
            <div class="d-flex justify-content-end">
              <button class="btn btn-light btn-lg me-2" onclick="exportReport()">
                <i class="mdi mdi-download"></i> Export
              </button>
              <button class="btn btn-outline-light btn-lg" onclick="refreshDashboard()">
                <i class="mdi mdi-refresh"></i> Refresh
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->
  
  <!-- Key Metrics Cards -->
  <div class="row slide-up" id="metricsCards">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
      <div class="stats-card">
        <div class="stats-icon performance">
          <i class="mdi mdi-account-group"></i>
        </div>
        <div class="stats-value" id="totalSales">-</div>
        <div class="stats-label">Total Sales Team</div>
        <div class="stats-change positive" id="salesGrowth">Loading...</div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
      <div class="stats-card stats-card-leads">
        <div class="stats-icon leads">
          <i class="mdi mdi-account-plus"></i>
        </div>
        <div class="stats-value" id="totalLeads">-</div>
        <div class="stats-label">Total Leads Aktif</div>
        <div class="stats-change positive" id="leadsGrowth">Loading...</div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
      <div class="stats-card stats-card-deals">
        <div class="stats-icon deals">
          <i class="mdi mdi-handshake"></i>
        </div>
        <div class="stats-value" id="totalDeals">-</div>
        <div class="stats-label">Deals Closed</div>
        <div class="stats-change positive" id="dealsGrowth">Loading...</div>
      </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
      <div class="stats-card stats-card-revenue">
        <div class="stats-icon revenue">
          <i class="mdi mdi-currency-usd"></i>
        </div>
        <div class="stats-value" id="totalRevenue">-</div>
        <div class="stats-label">Revenue Bulan Ini</div>
        <div class="stats-change positive" id="revenueGrowth">Loading...</div>
      </div>
    </div>
  </div>

  <!-- Charts Row 1 -->
  <div class="row">
    <div class="col-xl-8 col-12">
      <div class="chart-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-chart-bar"></i>Performance Per Sales
          </h5>
          <div>
            <span class="chart-badge badge-primary">Bulan Ini</span>
            <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadSalesPerformance()">
              <i class="mdi mdi-refresh"></i>
            </button>
          </div>
        </div>
        <div class="chart-container-inner">
          <canvas id="salesPerformanceChart"></canvas>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-12">
      <div class="chart-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-chart-donut"></i>Deal Per Tahap
          </h5>
          <span class="chart-badge badge-success">Real-time</span>
        </div>
        <div class="chart-container-inner small">
          <canvas id="dealStagesChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="row">
    <div class="col-xl-6 col-12">
      <div class="chart-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-account-multiple"></i>Distribusi Leads Per Sales
          </h5>
          <span class="chart-badge badge-info">Live Data</span>
        </div>
        <div class="chart-container-inner small">
          <canvas id="leadsDistributionChart"></canvas>
        </div>
      </div>
    </div>
    
    <div class="col-xl-6 col-12">
      <div class="chart-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-alert-circle"></i>Customer Complaints Overview
          </h5>
          <div>
            <span class="chart-badge badge-warning" id="urgentComplaints">0 Urgent</span>
          </div>
        </div>
        <div class="chart-container-inner small">
          <canvas id="complaintsChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tables Section -->
  <div class="row">
    <div class="col-12">
      <div class="table-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-clock-alert"></i>Leads Aging Report
          </h5>
          <div>
            <span class="chart-badge badge-warning" id="agingCount">0 Aging</span>
            <button class="btn btn-sm btn-outline-warning ms-2" onclick="loadAgingLeads()">
              <i class="mdi mdi-refresh"></i>
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><i class="mdi mdi-account me-1"></i>Lead Name</th>
                <th><i class="mdi mdi-account-tie me-1"></i>Sales Person</th>
                <th><i class="mdi mdi-calendar me-1"></i>Days Aging</th>
                <th><i class="mdi mdi-phone me-1"></i>Last Contact</th>
                <th><i class="mdi mdi-flag me-1"></i>Priority</th>
                <th><i class="mdi mdi-chart-line me-1"></i>Potential Value</th>
                <th><i class="mdi mdi-cog me-1"></i>Actions</th>
              </tr>
            </thead>
            <tbody id="agingLeadsTable">
              <tr>
                <td colspan="7" class="loading">
                  <div class="spinner"></div>
                  Loading aging leads data...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-12">
      <div class="table-wrapper">
        <div class="chart-header">
          <h5 class="chart-title">
            <i class="mdi mdi-alert-circle"></i>Customer Complaints Management
          </h5>
          <div>
            <span class="chart-badge badge-warning" id="unhandledCount">0 Unhandled</span>
            <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadComplaints()">
              <i class="mdi mdi-refresh"></i>
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><i class="mdi mdi-calendar me-1"></i>Date</th>
                <th><i class="mdi mdi-account me-1"></i>Customer</th>
                <th><i class="mdi mdi-tag me-1"></i>Category</th>
                <th><i class="mdi mdi-speedometer me-1"></i>Priority</th>
                <th><i class="mdi mdi-flag me-1"></i>Status</th>
                <th><i class="mdi mdi-account-tie me-1"></i>Handled By</th>
                <th><i class="mdi mdi-clock me-1"></i>Response Time</th>
                <th><i class="mdi mdi-cog me-1"></i>Actions</th>
              </tr>
            </thead>
            <tbody id="complaintsTable">
              <tr>
                <td colspan="8" class="loading">
                  <div class="spinner"></div>
                  Loading complaints data...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Refresh Button -->
<button class="refresh-btn" onclick="refreshAllData()" title="Refresh All Data">
  <i class="mdi mdi-refresh" id="refreshIcon"></i>
</button>
@endsection

@section('pageScript')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
  // Initialize charts variables
  window.charts = {};
  
  // Enhanced Dummy Data
  const dummyData = {
    metrics: {
      totalSales: 8,
      totalLeads: 156,
      totalDeals: 23,
      totalRevenue: '4.2M'
    },
    salesPerformance: {
      labels: ['Ahmad S.', 'Budi P.', 'Citra M.', 'Dika R.', 'Eka L.', 'Fina K.', 'Gita N.', 'Hadi W.'],
      datasets: [
        {
          label: 'Leads Generated',
          data: [25, 32, 28, 35, 22, 29, 31, 26],
          backgroundColor: 'rgba(102, 126, 234, 0.8)',
          borderColor: '#667eea',
          borderWidth: 2
        },
        {
          label: 'Deals Closed',
          data: [8, 12, 9, 15, 6, 11, 13, 9],
          backgroundColor: 'rgba(67, 233, 123, 0.8)',
          borderColor: '#43e97b',
          borderWidth: 2
        },
        {
          label: 'Revenue (M)',
          data: [0.8, 1.2, 0.9, 1.5, 0.6, 1.1, 1.3, 0.9],
          backgroundColor: 'rgba(150, 230, 161, 0.8)',
          borderColor: '#96e6a1',
          borderWidth: 2
        }
      ]
    },
    dealStages: {
      labels: ['Aktivitas', 'Quotation', 'Kontrak'],
      data: [45, 32, 23],
      colors: ['#4facfe', '#ffd93d', '#43e97b']
    },
    leadsDistribution: {
      labels: ['Ahmad S.', 'Budi P.', 'Citra M.', 'Dika R.', 'Eka L.', 'Fina K.', 'Gita N.', 'Hadi W.'],
      data: [22, 18, 16, 14, 12, 10, 8, 6],
      colors: ['#667eea', '#4facfe', '#43e97b', '#ffd93d', '#ff9a9e', '#96e6a1', '#764ba2', '#38f9d7']
    },
    complaintsOverview: {
      labels: ['Resolved', 'In Progress', 'Pending', 'Escalated'],
      data: [65, 20, 10, 5],
      colors: ['#43e97b', '#ffd93d', '#ff9a9e', '#ff6b6b']
    },
    agingLeads: [
      { 
        name: 'PT. Maju Teknologi', 
        sales: 'Ahmad S.', 
        days: 15, 
        lastContact: '5 hari lalu', 
        priority: 'High',
        value: 'Rp 150M',
        phone: '+62 812-3456-7890'
      },
      { 
        name: 'CV. Digital Solusi', 
        sales: 'Budi P.', 
        days: 12, 
        lastContact: '3 hari lalu', 
        priority: 'High',
        value: 'Rp 89M',
        phone: '+62 813-9876-5432'
      },
      { 
        name: 'UD. Berkah Jaya', 
        sales: 'Citra M.', 
        days: 11, 
        lastContact: '2 hari lalu', 
        priority: 'Medium',
        value: 'Rp 75M',
        phone: '+62 814-5555-1234'
      },
      { 
        name: 'Toko Elektronik Makmur', 
        sales: 'Dika R.', 
        days: 9, 
        lastContact: '1 hari lalu', 
        priority: 'Medium',
        value: 'Rp 45M',
        phone: '+62 815-7777-8888'
      },
      { 
        name: 'PT. Harapan Bangsa', 
        sales: 'Eka L.', 
        days: 14, 
        lastContact: '4 hari lalu', 
        priority: 'High',
        value: 'Rp 200M',
        phone: '+62 816-1111-2222'
      },
      { 
        name: 'CV. Sumber Rejeki', 
        sales: 'Fina K.', 
        days: 8, 
        lastContact: '6 hari lalu', 
        priority: 'Low',
        value: 'Rp 30M',
        phone: '+62 817-3333-4444'
      }
    ],
    complaints: [
      { 
        date: '2024-03-15', 
        customer: 'Siti Rahayu', 
        category: 'Pelayanan', 
        priority: 'High', 
        status: 'Pending', 
        handler: '-',
        responseTime: '2 jam'
      },
      { 
        date: '2024-03-14', 
        customer: 'Budi Santoso', 
        category: 'Produk', 
        priority: 'Medium', 
        status: 'In Progress', 
        handler: 'CS Team',
        responseTime: '1 jam'
      },
      { 
        date: '2024-03-13', 
        customer: 'Dewi Sartika', 
        category: 'Pengiriman', 
        priority: 'Low', 
        status: 'Resolved', 
        handler: 'Logistik',
        responseTime: '30 menit'
      },
      { 
        date: '2024-03-12', 
        customer: 'Andi Wijaya', 
        category: 'Billing', 
        priority: 'High', 
        status: 'Pending', 
        handler: '-',
        responseTime: '3 jam'
      },
      { 
        date: '2024-03-11', 
        customer: 'Maya Sari', 
        category: 'Teknis', 
        priority: 'Medium', 
        status: 'Resolved', 
        handler: 'IT Support',
        responseTime: '45 menit'
      },
      { 
        date: '2024-03-10', 
        customer: 'Rudi Hartono', 
        category: 'Pelayanan', 
        priority: 'High', 
        status: 'Escalated', 
        handler: 'Manager',
        responseTime: '4 jam'
      }
    ]
  };

  // Load initial data
  loadDashboardData();

  // Auto refresh every 5 minutes
  setInterval(() => {
    loadDashboardData();
  }, 300000);

  // Main data loading function
  function loadDashboardData() {
    loadMetrics();
    loadCharts();
    loadAgingLeads();
    loadComplaints();
  }

  // Load metrics cards with animation
  function loadMetrics() {
    const metrics = dummyData.metrics;
    
    // Animate counters
    animateCounter('#totalSales', metrics.totalSales);
    animateCounter('#totalLeads', metrics.totalLeads);
    animateCounter('#totalDeals', metrics.totalDeals);
    $('#totalRevenue').text('Rp ' + metrics.totalRevenue);
    
    // Update growth indicators
    $('#salesGrowth').html('<i class="mdi mdi-trending-up me-1"></i>+2 dari bulan lalu');
    $('#leadsGrowth').html('<i class="mdi mdi-trending-up me-1"></i>+18% dari target');
    $('#dealsGrowth').html('<i class="mdi mdi-trending-up me-1"></i>+15% dari bulan lalu');
    $('#revenueGrowth').html('<i class="mdi mdi-trending-up me-1"></i>+22% dari target');
  }

  // Animate counter numbers
  function animateCounter(selector, target, suffix = '') {
    const element = $(selector);
    const current = parseInt(element.text()) || 0;
    const increment = (target - current) / 30;
    let counter = current;
    
    const timer = setInterval(() => {
      counter += increment;
      if (counter >= target) {
        element.text(target + suffix);
        clearInterval(timer);
      } else {
        element.text(Math.floor(counter) + suffix);
      }
    }, 30);
  }

  // Load all charts
  function loadCharts() {
    loadSalesPerformance();
    loadDealStages();
    loadLeadsDistribution();
    loadComplaintsOverview();
  }

  // Sales Performance Chart
  function loadSalesPerformance() {
    destroyChart('salesPerformanceChart');
    
    const ctx = document.getElementById('salesPerformanceChart');
    if (!ctx) return;
    
    window.charts.salesPerformanceChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: dummyData.salesPerformance.labels,
        datasets: dummyData.salesPerformance.datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              padding: 20,
              usePointStyle: true,
              font: {
                size: 12,
                weight: 'bold'
              }
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#667eea',
            borderWidth: 1,
            cornerRadius: 10,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.datasetIndex === 2) {
                  label += 'Rp ' + context.parsed.y + 'M';
                } else {
                  label += context.parsed.y;
                }
                return label;
              }
            }
          }
        },
        interaction: {
          mode: 'nearest',
          axis: 'x',
          intersect: false
        },
        scales: {
          x: {
            display: true,
            grid: {
              display: false
            },
            ticks: {
              color: '#666',
              font: {
                weight: 'bold'
              }
            }
          },
          y: {
            display: true,
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              color: '#666'
            }
          }
        }
      }
    });
  }

  // Deal Stages Chart
  function loadDealStages() {
    destroyChart('dealStagesChart');
    
    const ctx = document.getElementById('dealStagesChart');
    if (!ctx) return;
    
    window.charts.dealStagesChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: dummyData.dealStages.labels,
        datasets: [{
          data: dummyData.dealStages.data,
          backgroundColor: dummyData.dealStages.colors,
          borderWidth: 0,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true,
              font: {
                size: 12,
                weight: 'bold'
              },
              generateLabels: function(chart) {
                const data = chart.data;
                if (data.labels.length && data.datasets.length) {
                  return data.labels.map(function(label, i) {
                    const dataset = data.datasets[0];
                    const value = dataset.data[i];
                    const total = dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((value / total) * 100);
                    return {
                      text: `${label}: ${value} (${percentage}%)`,
                      fillStyle: dataset.backgroundColor[i],
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
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 10,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((context.parsed / total) * 100);
                return `${context.label}: ${context.parsed} deals (${percentage}%)`;
              }
            }
          }
        },
        cutout: '60%'
      }
    });
  }

  // Leads Distribution Chart
  function loadLeadsDistribution() {
    destroyChart('leadsDistributionChart');
    
    const ctx = document.getElementById('leadsDistributionChart');
    if (!ctx) return;
    
    window.charts.leadsDistributionChart = new Chart(ctx, {
      type: 'polarArea',
      data: {
        labels: dummyData.leadsDistribution.labels,
        datasets: [{
          data: dummyData.leadsDistribution.data,
          backgroundColor: dummyData.leadsDistribution.colors.map(color => color + '80'),
          borderColor: dummyData.leadsDistribution.colors,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              usePointStyle: true,
              font: {
                size: 11,
                weight: 'bold'
              },
              generateLabels: function(chart) {
                const data = chart.data;
                if (data.labels.length && data.datasets.length) {
                  return data.labels.map(function(label, i) {
                    const dataset = data.datasets[0];
                    const value = dataset.data[i];
                    return {
                      text: `${label}: ${value} leads`,
                      fillStyle: dataset.backgroundColor[i],
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
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 10,
            callbacks: {
              label: function(context) {
                return `${context.label}: ${context.parsed.r} leads`;
              }
            }
          }
        },
        scales: {
          r: {
            beginAtZero: true,
            ticks: {
              display: false
            },
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          }
        }
      }
    });
  }

  // Complaints Overview Chart
  function loadComplaintsOverview() {
    destroyChart('complaintsChart');
    
    const ctx = document.getElementById('complaintsChart');
    if (!ctx) return;
    
    window.charts.complaintsChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: dummyData.complaintsOverview.labels,
        datasets: [{
          data: dummyData.complaintsOverview.data,
          backgroundColor: dummyData.complaintsOverview.colors,
          borderWidth: 3,
          borderColor: '#ffffff',
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              usePointStyle: true,
              font: {
                size: 11,
                weight: 'bold'
              },
              generateLabels: function(chart) {
                const data = chart.data;
                if (data.labels.length && data.datasets.length) {
                  return data.labels.map(function(label, i) {
                    const dataset = data.datasets[0];
                    const value = dataset.data[i];
                    return {
                      text: `${label}: ${value}%`,
                      fillStyle: dataset.backgroundColor[i],
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
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 10,
            callbacks: {
              label: function(context) {
                return `${context.label}: ${context.parsed}%`;
              }
            }
          }
        }
      }
    });

    // Update urgent complaints count
    const urgentCount = dummyData.complaints.filter(c => c.priority === 'High' && c.status === 'Pending').length;
    $('#urgentComplaints').text(`${urgentCount} Urgent`);
  }

  // Load aging leads table
  function loadAgingLeads() {
    const tbody = $('#agingLeadsTable');
    tbody.html('<tr><td colspan="7" class="loading"><div class="spinner"></div>Loading aging leads...</td></tr>');
    
    setTimeout(() => {
      let html = '';
      let agingCount = 0;
      
      dummyData.agingLeads.forEach((lead, index) => {
        const isAging = lead.days >= 7;
        const priorityClass = `priority-${lead.priority.toLowerCase()}`;
        const urgentAlert = lead.days >= 10 ? '<i class="mdi mdi-alert text-danger ms-1" title="Urgent!"></i>' : '';
        
        if (isAging) agingCount++;
        
        html += `
          <tr class="${isAging ? 'table-warning' : ''}" style="animation-delay: ${index * 0.1}s">
            <td>
              <strong>${lead.name}</strong>
              ${urgentAlert}
            </td>
            <td>
              <div class="d-flex align-items-center">
                <div class="sales-avatar me-2">
                  ${lead.sales.charAt(0)}
                </div>
                <span class="fw-bold">${lead.sales}</span>
              </div>
            </td>
            <td>
              <span class="badge ${lead.days >= 10 ? 'bg-danger' : lead.days >= 7 ? 'bg-warning' : 'bg-success'} fs-6">
                ${lead.days} hari
              </span>
            </td>
            <td>
              <i class="mdi mdi-clock-outline me-1"></i>${lead.lastContact}
            </td>
            <td>
              <span class="fw-bold ${priorityClass}">
                <i class="mdi mdi-flag me-1"></i>${lead.priority}
              </span>
            </td>
            <td>
              <span class="fw-bold text-success">${lead.value}</span>
            </td>
            <td>
              <div class="btn-group" role="group">
                <button class="btn btn-action btn-primary btn-sm" onclick="followUpLead('${lead.name}', '${lead.phone}')" title="Follow Up">
                  <i class="mdi mdi-phone"></i>
                </button>
                <button class="btn btn-action btn-info btn-sm" onclick="viewLeadDetails('${lead.name}')" title="Detail">
                  <i class="mdi mdi-eye"></i>
                </button>
                <button class="btn btn-action btn-success btn-sm" onclick="scheduleCall('${lead.name}')" title="Schedule">
                  <i class="mdi mdi-calendar"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      
      tbody.html(html);
      $('#agingCount').text(`${agingCount} Aging`);
    }, 800);
  }

  // Load complaints table
  function loadComplaints() {
    const tbody = $('#complaintsTable');
    tbody.html('<tr><td colspan="8" class="loading"><div class="spinner"></div>Loading complaints...</td></tr>');
    
    setTimeout(() => {
      let html = '';
      let unhandledCount = 0;
      
      dummyData.complaints.forEach((complaint, index) => {
        const statusClass = getStatusClass(complaint.status);
        const priorityClass = `priority-${complaint.priority.toLowerCase()}`;
        const urgentAlert = complaint.priority === 'High' ? '<i class="mdi mdi-alert text-danger ms-1" title="High Priority!"></i>' : '';
        
        if (complaint.status === 'Pending') unhandledCount++;
        
        html += `
          <tr style="animation-delay: ${index * 0.1}s">
            <td>
              <span class="fw-bold">${complaint.date}</span>
            </td>
            <td>
              <strong>${complaint.customer}</strong>
              ${urgentAlert}
            </td>
            <td>
              <span class="badge bg-secondary">${complaint.category}</span>
            </td>
            <td>
              <span class="fw-bold ${priorityClass}">
                <i class="mdi mdi-flag me-1"></i>${complaint.priority}
              </span>
            </td>
            <td>
              <span class="status-badge ${statusClass}">${complaint.status}</span>
            </td>
            <td>
              <span class="fw-bold">${complaint.handler}</span>
            </td>
            <td>
              <span class="badge ${getResponseTimeBadge(complaint.responseTime)}">${complaint.responseTime}</span>
            </td>
            <td>
              <div class="btn-group" role="group">
                <button class="btn btn-action btn-warning btn-sm" onclick="assignHandler('${complaint.customer}')" title="Assign">
                  <i class="mdi mdi-account-check"></i>
                </button>
                <button class="btn btn-action btn-info btn-sm" onclick="viewComplaintDetails('${complaint.customer}')" title="Detail">
                  <i class="mdi mdi-eye"></i>
                </button>
                <button class="btn btn-action btn-success btn-sm" onclick="resolveComplaint('${complaint.customer}')" title="Resolve">
                  <i class="mdi mdi-check-circle"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      
      tbody.html(html);
      $('#unhandledCount').text(`${unhandledCount} Unhandled`);
    }, 800);
  }

  // Helper functions
  function getStatusClass(status) {
    switch(status.toLowerCase()) {
      case 'resolved': return 'status-resolved';
      case 'in progress': return 'status-quotation';
      case 'pending': return 'status-pending';
      case 'escalated': return 'status-urgent';
      default: return 'status-pending';
    }
  }

  function getResponseTimeBadge(responseTime) {
    const timeValue = parseInt(responseTime);
    if (responseTime.includes('menit') && timeValue <= 60) return 'bg-success';
    if (responseTime.includes('jam') && timeValue <= 2) return 'bg-warning';
    return 'bg-danger';
  }

  function destroyChart(chartId) {
    if (window.charts[chartId] instanceof Chart) {
      window.charts[chartId].destroy();
      window.charts[chartId] = null;
    }
  }

  // Global functions
  window.refreshDashboard = function() {
    $('#refreshIcon').addClass('fa-spin');
    loadDashboardData();
    
    Swal.fire({
      title: 'Dashboard Updated!',
      text: 'Data telah berhasil diperbarui',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false,
      toast: true,
      position: 'top-end'
    });
    
    setTimeout(() => {
      $('#refreshIcon').removeClass('fa-spin');
    }, 1500);
  };

  window.refreshAllData = function() {
    refreshDashboard();
  };

  window.exportReport = function() {
    Swal.fire({
      title: 'Export Report',
      html: `
        <div class="text-start">
          <div class="mb-3">
            <label class="form-label">Format Export:</label>
            <select class="form-select" id="exportFormat">
              <option value="pdf">PDF Report</option>
              <option value="excel">Excel Spreadsheet</option>
              <option value="csv">CSV Data</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Periode:</label>
            <select class="form-select" id="exportPeriod">
              <option value="today">Hari Ini</option>
              <option value="week">7 Hari Terakhir</option>
              <option value="month" selected>Bulan Ini</option>
              <option value="quarter">Quarter Ini</option>
            </select>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Export',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#667eea',
      preConfirm: () => {
        const format = document.getElementById('exportFormat').value;
        const period = document.getElementById('exportPeriod').value;
        return { format, period };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Exporting...',
          html: 'Sedang memproses export report',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        setTimeout(() => {
          Swal.fire({
            title: 'Export Berhasil!',
            text: `Report ${result.value.format.toUpperCase()} untuk periode ${result.value.period} telah diunduh`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });
        }, 2000);
      }
    });
  };

  // Action functions for aging leads
  window.followUpLead = function(leadName, phone) {
    Swal.fire({
      title: 'Follow Up Lead',
      html: `
        <div class="text-start">
          <h6 class="mb-3">${leadName}</h6>
          <p><strong>Phone:</strong> ${phone}</p>
          <div class="mb-3">
            <label class="form-label">Metode Follow Up:</label>
            <select class="form-select" id="followUpMethod">
              <option value="call">Phone Call</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="email">Email</option>
              <option value="visit">Site Visit</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan:</label>
            <textarea class="form-control" id="followUpNotes" rows="3" placeholder="Catatan follow up..."></textarea>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Follow Up',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#667eea',
      preConfirm: () => {
        const method = document.getElementById('followUpMethod').value;
        const notes = document.getElementById('followUpNotes').value;
        return { method, notes };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Follow Up Dijadwalkan!',
          text: `Follow up ${result.value.method} untuk ${leadName} telah dijadwalkan`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
        setTimeout(() => loadAgingLeads(), 2000);
      }
    });
  };

  window.viewLeadDetails = function(leadName) {
    const lead = dummyData.agingLeads.find(l => l.name === leadName);
    if (!lead) return;
    
    Swal.fire({
      title: 'Detail Lead',
      html: `
        <div class="text-start">
          <div class="row">
            <div class="col-md-6">
              <h6 class="text-primary">${lead.name}</h6>
              <p><strong>Sales Person:</strong> ${lead.sales}</p>
              <p><strong>Phone:</strong> ${lead.phone}</p>
              <p><strong>Potential Value:</strong> <span class="text-success fw-bold">${lead.value}</span></p>
              <p><strong>Priority:</strong> <span class="priority-${lead.priority.toLowerCase()} fw-bold">${lead.priority}</span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Days Aging:</strong> <span class="badge ${lead.days >= 10 ? 'bg-danger' : 'bg-warning'}">${lead.days} hari</span></p>
              <p><strong>Last Contact:</strong> ${lead.lastContact}</p>
              <p><strong>Source:</strong> Website Inquiry</p>
              <p><strong>Interest:</strong> Enterprise Package</p>
            </div>
          </div>
          <hr>
          <h6>Activity Timeline:</h6>
          <div class="timeline">
            <div class="timeline-item">
              <small class="text-muted">5 hari lalu</small> - Last follow up call
            </div>
            <div class="timeline-item">
              <small class="text-muted">8 hari lalu</small> - Proposal sent
            </div>
            <div class="timeline-item">
              <small class="text-muted">15 hari lalu</small> - Initial contact
            </div>
          </div>
        </div>
      `,
      width: '700px',
      confirmButtonColor: '#667eea',
      confirmButtonText: 'Tutup'
    });
  };

  window.scheduleCall = function(leadName) {
    Swal.fire({
      title: 'Schedule Call',
      html: `
        <div class="text-start">
          <h6 class="mb-3">${leadName}</h6>
          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Tanggal:</label>
              <input type="date" class="form-control" id="callDate" min="${new Date().toISOString().split('T')[0]}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Waktu:</label>
              <input type="time" class="form-control" id="callTime">
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">Agenda:</label>
            <textarea class="form-control" id="callAgenda" rows="3" placeholder="Agenda pembicaraan..."></textarea>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Schedule',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#667eea',
      preConfirm: () => {
        const date = document.getElementById('callDate').value;
        const time = document.getElementById('callTime').value;
        const agenda = document.getElementById('callAgenda').value;
        
        if (!date || !time) {
          Swal.showValidationMessage('Silakan isi tanggal dan waktu');
          return false;
        }
        
        return { date, time, agenda };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Call Terjadwal!',
          text: `Call dengan ${leadName} dijadwalkan pada ${result.value.date} ${result.value.time}`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  };

  // Action functions for complaints
  window.assignHandler = function(customerName) {
    const complaint = dummyData.complaints.find(c => c.customer === customerName);
    if (!complaint) return;
    
    Swal.fire({
      title: 'Assign Handler',
      html: `
        <div class="text-start">
          <h6 class="mb-3">${customerName}</h6>
          <p><strong>Category:</strong> ${complaint.category}</p>
          <p><strong>Priority:</strong> <span class="priority-${complaint.priority.toLowerCase()} fw-bold">${complaint.priority}</span></p>
          <div class="mb-3">
            <label class="form-label">Assign to:</label>
            <select class="form-select" id="handlerSelect">
              <option value="">Pilih Handler</option>
              <option value="CS Team">Customer Service Team</option>
              <option value="Technical Support">Technical Support</option>
              <option value="Sales Manager">Sales Manager</option>
              <option value="IT Support">IT Support</option>
              <option value="Logistics">Logistics Team</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Priority Level:</label>
            <select class="form-select" id="priorityLevel">
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High" ${complaint.priority === 'High' ? 'selected' : ''}>High</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes:</label>
            <textarea class="form-control" id="assignNotes" rows="3" placeholder="Catatan untuk handler..."></textarea>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Assign',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#667eea',
      preConfirm: () => {
        const handler = document.getElementById('handlerSelect').value;
        const priority = document.getElementById('priorityLevel').value;
        const notes = document.getElementById('assignNotes').value;
        
        if (!handler) {
          Swal.showValidationMessage('Silakan pilih handler');
          return false;
        }
        
        return { handler, priority, notes };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Handler Assigned!',
          text: `Complaint dari ${customerName} telah di-assign ke ${result.value.handler}`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
        setTimeout(() => loadComplaints(), 2000);
      }
    });
  };

  window.viewComplaintDetails = function(customerName) {
    const complaint = dummyData.complaints.find(c => c.customer === customerName);
    if (!complaint) return;
    
    Swal.fire({
      title: 'Detail Complaint',
      html: `
        <div class="text-start">
          <div class="row">
            <div class="col-md-6">
              <h6 class="text-primary">${complaint.customer}</h6>
              <p><strong>Date:</strong> ${complaint.date}</p>
              <p><strong>Category:</strong> <span class="badge bg-secondary">${complaint.category}</span></p>
              <p><strong>Priority:</strong> <span class="priority-${complaint.priority.toLowerCase()} fw-bold">${complaint.priority}</span></p>
              <p><strong>Status:</strong> <span class="status-badge ${getStatusClass(complaint.status)}">${complaint.status}</span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Handler:</strong> ${complaint.handler}</p>
              <p><strong>Response Time:</strong> <span class="badge ${getResponseTimeBadge(complaint.responseTime)}">${complaint.responseTime}</span></p>
              <p><strong>Channel:</strong> Phone Call</p>
              <p><strong>Customer Type:</strong> Premium</p>
            </div>
          </div>
          <hr>
          <h6>Deskripsi Masalah:</h6>
          <div class="bg-light p-3 rounded mb-3">
            ${getComplaintDescription(complaint.category)}
          </div>
          <h6>Timeline:</h6>
          <div class="timeline">
            <div class="timeline-item mb-2">
              <small class="text-muted">${complaint.date} 09:30</small> - Complaint received
            </div>
            <div class="timeline-item mb-2">
              <small class="text-muted">${complaint.date} 09:45</small> - Acknowledged by system
            </div>
            <div class="timeline-item mb-2">
              <small class="text-muted">${complaint.date} 10:00</small> - ${complaint.handler !== '-' ? `Assigned to ${complaint.handler}` : 'Pending assignment'}
            </div>
          </div>
        </div>
      `,
      width: '800px',
      confirmButtonColor: '#667eea',
      confirmButtonText: 'Tutup'
    });
  };

  window.resolveComplaint = function(customerName) {
    const complaint = dummyData.complaints.find(c => c.customer === customerName);
    if (!complaint) return;
    
    Swal.fire({
      title: 'Resolve Complaint',
      html: `
        <div class="text-start">
          <h6 class="mb-3">${customerName}</h6>
          <div class="mb-3">
            <label class="form-label">Resolution Status:</label>
            <select class="form-select" id="resolutionStatus">
              <option value="Resolved">Resolved</option>
              <option value="Partially Resolved">Partially Resolved</option>
              <option value="Escalated">Escalated</option>
              <option value="Closed">Closed</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Solution Provided:</label>
            <textarea class="form-control" id="solutionText" rows="4" placeholder="Jelaskan solusi yang diberikan..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Customer Satisfaction:</label>
            <select class="form-select" id="satisfactionRating">
              <option value="5">Very Satisfied (5/5)</option>
              <option value="4">Satisfied (4/5)</option>
              <option value="3">Neutral (3/5)</option>
              <option value="2">Dissatisfied (2/5)</option>
              <option value="1">Very Dissatisfied (1/5)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Follow-up Required:</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="followUpRequired">
              <label class="form-check-label" for="followUpRequired">
                Schedule follow-up call
              </label>
            </div>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Resolve',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#28a745',
      preConfirm: () => {
        const status = document.getElementById('resolutionStatus').value;
        const solution = document.getElementById('solutionText').value;
        const satisfaction = document.getElementById('satisfactionRating').value;
        const followUp = document.getElementById('followUpRequired').checked;
        
        if (!solution.trim()) {
          Swal.showValidationMessage('Silakan jelaskan solusi yang diberikan');
          return false;
        }
        
        return { status, solution, satisfaction, followUp };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Complaint Resolved!',
          text: `Complaint dari ${customerName} telah diselesaikan dengan rating ${result.value.satisfaction}/5`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
        setTimeout(() => loadComplaints(), 2000);
      }
    });
  };

  // Helper function for complaint descriptions
  function getComplaintDescription(category) {
    const descriptions = {
      'Pelayanan': 'Customer mengeluh tentang keterlambatan respon dari customer service. Sudah menunggu 2 hari tidak ada feedback untuk pertanyaan produk.',
      'Produk': 'Produk yang diterima tidak sesuai dengan spesifikasi yang dijanjikan. Quality control perlu ditingkatkan.',
      'Pengiriman': 'Keterlambatan pengiriman lebih dari 3 hari dari jadwal yang dijanjikan. Customer butuh kepastian jadwal.',
      'Billing': 'Terdapat kesalahan perhitungan pada invoice. Customer meminta penjelasan detail biaya tambahan.',
      'Teknis': 'Sistem mengalami error dan customer tidak bisa mengakses dashboard. Technical support diperlukan segera.'
    };
    return descriptions[category] || 'Deskripsi complaint tidak tersedia.';
  }

  // Initialize tooltips and animations
  $(function() {
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add animation classes
    setTimeout(() => {
      $('.stats-card').addClass('fade-in');
      $('.chart-wrapper').addClass('slide-up');
    }, 100);
  });

  // Custom AJAX simulation for real-world integration
  function simulateAjaxCall(endpoint, data = {}) {
    return new Promise((resolve) => {
      setTimeout(() => {
        console.log(`AJAX call to ${endpoint}:`, data);
        resolve({ success: true, data: dummyData });
      }, Math.random() * 1000 + 500);
    });
  }

  // Example AJAX integration points
  window.loadSalesData = function() {
    simulateAjaxCall('/api/sales-performance').then(response => {
      if (response.success) {
        // Update charts with real data
        loadSalesPerformance();
      }
    });
  };

  window.loadLeadsData = function() {
    simulateAjaxCall('/api/leads-distribution').then(response => {
      if (response.success) {
        loadLeadsDistribution();
      }
    });
  };

  window.loadComplaintsData = function() {
    simulateAjaxCall('/api/complaints').then(response => {
      if (response.success) {
        loadComplaints();
      }
    });
  };

  // Keyboard shortcuts
  $(document).keydown(function(e) {
    // Ctrl + R untuk refresh
    if (e.ctrlKey && e.which === 82) {
      e.preventDefault();
      refreshDashboard();
    }
    
    // Ctrl + E untuk export
    if (e.ctrlKey && e.which === 69) {
      e.preventDefault();
      exportReport();
    }
  });

  // Real-time updates simulation
  function startRealTimeUpdates() {
    setInterval(() => {
      // Simulate random metric updates
      const metrics = ['totalLeads', 'totalDeals'];
      const randomMetric = metrics[Math.floor(Math.random() * metrics.length)];
      const currentValue = parseInt($('#' + randomMetric).text()) || 0;
      const newValue = currentValue + Math.floor(Math.random() * 3);
      
      animateCounter('#' + randomMetric, newValue);
      
      // Show toast notification for updates
      if (Math.random() > 0.7) {
        Swal.fire({
          title: 'Data Updated',
          text: 'New lead atau deal telah masuk',
          icon: 'info',
          toast: true,
          position: 'bottom-end',
          timer: 3000,
          showConfirmButton: false,
          timerProgressBar: true
        });
      }
    }, 30000); // Update every 30 seconds
  }

  // Start real-time updates
  startRealTimeUpdates();

  // Performance monitoring
  function trackPerformance() {
    const loadTime = performance.now();
    console.log(`Dashboard loaded in ${loadTime.toFixed(2)}ms`);
    
    // Monitor chart rendering performance
    const chartLoadTimes = {};
    Object.keys(window.charts).forEach(chartId => {
      const start = performance.now();
      // Chart rendering happens here
      const end = performance.now();
      chartLoadTimes[chartId] = end - start;
    });
    
    console.log('Chart load times:', chartLoadTimes);
  }

  // Call performance tracking
  setTimeout(trackPerformance, 1000);

  // Error handling for failed AJAX calls
  window.addEventListener('error', function(e) {
    console.error('Dashboard error:', e.error);
    
    Swal.fire({
      title: 'Terjadi Kesalahan',
      text: 'Ada masalah dalam memuat data. Silakan refresh halaman.',
      icon: 'error',
      confirmButtonText: 'Refresh',
      confirmButtonColor: '#dc3545'
    }).then((result) => {
      if (result.isConfirmed) {
        location.reload();
      }
    });
  });

  // Service Worker registration for offline capability
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        console.log('Service Worker registered:', registration);
      })
      .catch(error => {
        console.log('Service Worker registration failed:', error);
      });
  }
});

// Additional utility functions
function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
}

function formatDate(dateString) {
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(new Date(dateString));
}

function calculateGrowthPercentage(current, previous) {
  if (previous === 0) return 0;
  return ((current - previous) / previous * 100).toFixed(1);
}

// Export functions for external use
window.DashboardAPI = {
  refresh: refreshDashboard,
  export: exportReport,
  loadSalesData: loadSalesData,
  loadLeadsData: loadLeadsData,
  loadComplaintsData: loadComplaintsData
};
</script>
@endsection