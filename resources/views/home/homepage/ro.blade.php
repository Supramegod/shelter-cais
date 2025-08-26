@extends('layouts.master')
@section('title', 'Dashboard Relational Officer')
@section('pageStyle')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root {
    --primary-color: #3b82f6;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --purple-color: #8b5cf6;
    --dark-color: #1f2937;
    --light-gray: #f8fafc;
    --border-color: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }

  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: white;
    color: #334155;
  }

  .dashboard-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 24px 24px;
  }

  .dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .dashboard-subtitle {
    opacity: 0.9;
    font-size: 1rem;
  }

  .stats-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .stats-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  .stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
  }

  .stats-card.active-contracts::before {
    background: linear-gradient(90deg, #10b981, #059669);
  }

  .stats-card.unvisited-contracts::before {
    background: linear-gradient(90deg, #f59e0b, #d97706);
  }

  .stats-card.reminder-contracts::before {
    background: linear-gradient(90deg, #8b5cf6, #7c3aed);
  }

  .stats-card.customer-satisfaction::before {
    background: linear-gradient(90deg, #06b6d4, #0891b2);
  }

  .stats-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
  }

  .stats-icon.success { background: var(--success-color); }
  .stats-icon.warning { background: var(--warning-color); }
  .stats-icon.purple { background: var(--purple-color); }
  .stats-icon.info { background: var(--info-color); }

  .stats-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: var(--dark-color);
  }

  .stats-label {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 1rem;
    font-weight: 500;
  }

  .stats-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
  }

  .stats-trend.positive {
    background: #ecfdf5;
    color: var(--success-color);
  }

  .stats-trend.negative {
    background: #fef2f2;
    color: var(--danger-color);
  }

  .stats-trend.urgent {
    background: #fef3c7;
    color: var(--warning-color);
  }

  .stats-trend.info {
    background: #f0f9ff;
    color: var(--info-color);
  }

  .progress-container {
    margin-top: 1rem;
  }

  .progress-bar-custom {
    height: 6px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.5rem;
  }

  .progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1s ease;
  }

  .progress-fill.success { background: var(--success-color); }
  .progress-fill.warning { background: var(--warning-color); }

  .chart-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .chart-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #fafbfc;
    flex-shrink: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .chart-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--dark-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .chart-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .chart-container {
    flex: 1;
    position: relative;
    min-height: 300px;
  }

  .table-card {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
  }

  .table-header {
    padding: 1.5rem;
    background: #fafbfc;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .table-responsive {
    max-height: 500px;
    overflow-y: auto;
  }

  .table {
    margin: 0;
    font-size: 0.875rem;
  }

  .table thead th {
    background: #f8fafc;
    border-bottom: 2px solid var(--border-color);
    font-weight: 600;
    color: var(--dark-color);
    padding: 1rem;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
  }

  .table tbody tr:hover {
    background: #f8fafc;
  }

  .badge-custom {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .badge-success { background: #ecfdf5; color: var(--success-color); }
  .badge-warning { background: #fffbeb; color: var(--warning-color); }
  .badge-danger { background: #fef2f2; color: var(--danger-color); }
  .badge-info { background: #f0f9ff; color: var(--info-color); }
  .badge-purple { background: #f3f4f6; color: var(--purple-color); }

  .btn-custom {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
  }

  .btn-primary {
    background: var(--primary-color);
    color: white;
  }

  .btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
  }

  .btn-success {
    background: var(--success-color);
    color: white;
  }

  .btn-success:hover {
    background: #059669;
  }

  .btn-warning {
    background: var(--warning-color);
    color: white;
  }

  .btn-warning:hover {
    background: #d97706;
  }

  .btn-outline {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
  }

  .btn-outline:hover {
    background: var(--primary-color);
    color: white;
  }

  .filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }

  .filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.2s;
  }

  .filter-tab.active {
    background: var(--primary-color);
    color: white;
  }

  .loading-spinner {
    display: none;
    text-align: center;
    padding: 2rem;
  }

  .spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #f3f4f6;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .quick-actions {
    display: flex;
    gap: 1rem;
    /* margin-top: 2.5rem;  */
    flex-wrap: wrap;
    margin-bottom: 2rem;
  }

  .quick-action-btn {
    padding: 0.75rem 1.5rem;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--dark-color);
    text-decoration: none;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
  }

  .quick-action-btn:hover {
    background: var(--light-gray);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
    text-decoration: none;
    color: var(--dark-color);
  }

  .status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 0.5rem;
  }

  .status-indicator.visited { background: var(--success-color); }
  .status-indicator.pending { background: var(--warning-color); }
  .status-indicator.overdue { background: var(--danger-color); }

  .contract-priority {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .priority-high {
    color: var(--danger-color);
  }

  .priority-medium {
    color: var(--warning-color);
  }

  .priority-low {
    color: var(--success-color);
  }

  .interaction-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .interaction-item {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
  }

  .interaction-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
  }

  .interaction-label {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 0.25rem;
  }

  @media (max-width: 768px) {
    .dashboard-header {
      padding: 1.5rem 0;
    }
    
    .dashboard-title {
      font-size: 1.5rem;
    }
    
    .stats-card {
      margin-bottom: 1rem;
    }
    
    .chart-body {
      padding: 1rem;
    }
    
    .table-header {
      flex-direction: column;
      align-items: stretch;
    }
    
    .quick-actions {
      justify-content: center;
    }

    .filter-tabs {
      justify-content: center;
    }
  }
</style>
@endsection

@section('content')
<div class="container-fluid">
  <!-- Dashboard Header -->
  <!-- <div class="dashboard-header">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1 class="dashboard-title">
            <i class="fas fa-user-tie me-2"></i>
            Dashboard Relational Officer
          </h1>
          <p class="dashboard-subtitle">
            Kelola kontrak aktif, jadwalkan kunjungan, dan pantau interaksi customer secara efektif
          </p>
        </div>
      </div>
    </div>
  </div> -->

  <div class="container">
    <!-- Quick Actions -->
    <div class="quick-actions">
      <a href="#" class="quick-action-btn" onclick="scheduleVisit()">
        <i class="fas fa-calendar-plus"></i>
        Jadwal Kunjungan
      </a>
      <a href="#" class="quick-action-btn" onclick="addContract()">
        <i class="fas fa-file-contract"></i>
        Tambah Kontrak
      </a>
      <a href="#" class="quick-action-btn" onclick="exportData()">
        <i class="fas fa-download"></i>
        Export Data
      </a>
      <a href="#" class="quick-action-btn" onclick="generateReport()">
        <i class="fas fa-chart-bar"></i>
        Laporan Bulanan
      </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card active-contracts" id="activeContractsCard">
          <div class="stats-card-header">
            <div class="stats-icon success">
              <i class="fas fa-file-contract"></i>
            </div>
          </div>
          <div class="stats-value" id="activeContractsValue">-</div>
          <div class="stats-label">Daftar Kontrak Aktif</div>
          <div class="progress-container">
            <div class="progress-bar-custom">
              <div class="progress-fill success" id="activeContractsProgress" style="width: 0%"></div>
            </div>
          </div>
          <div class="stats-trend positive" id="activeContractsTrend">
            <i class="fas fa-arrow-up"></i>
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card unvisited-contracts" id="unvisitedCard">
          <div class="stats-card-header">
            <div class="stats-icon warning">
              <i class="fas fa-map-marker-alt"></i>
            </div>
          </div>
          <div class="stats-value" id="unvisitedValue">-</div>
          <div class="stats-label">Kontrak Belum Dikunjungi</div>
          <div class="stats-trend urgent" id="unvisitedTrend">
            <i class="fas fa-exclamation-triangle"></i>
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card reminder-contracts" id="reminderCard">
          <div class="stats-card-header">
            <div class="stats-icon purple">
              <i class="fas fa-bell"></i>
            </div>
          </div>
          <div class="stats-value" id="reminderValue">-</div>
          <div class="stats-label">Reminder Kontrak</div>
          <div class="stats-trend negative" id="reminderTrend">
            <i class="fas fa-clock"></i>
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card customer-satisfaction" id="satisfactionCard">
          <div class="stats-card-header">
            <div class="stats-icon info">
              <i class="fas fa-smile"></i>
            </div>
          </div>
          <div class="stats-value" id="satisfactionValue">-</div>
          <div class="stats-label">Kepuasan Customer</div>
          <div class="stats-trend info" id="satisfactionTrend">
            <i class="fas fa-star"></i>
            Loading...
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Interaction Row -->
    <div class="row mb-4">
      <div class="col-lg-6 mb-4">
        <div class="chart-card">
          <div class="chart-header">
            <h3 class="chart-title">
              <i class="fas fa-comments"></i>
              Interaksi Customer
            </h3>
            <div class="filter-tabs">
              <button class="filter-tab active" onclick="filterInteractions('week')">Minggu Ini</button>
              <button class="filter-tab" onclick="filterInteractions('month')">Bulan Ini</button>
            </div>
          </div>
          <div class="chart-body">
            <div class="loading-spinner" id="interactionChartLoading">
              <div class="spinner"></div>
              <p>Memuat data interaksi...</p>
            </div>
            <div class="chart-container">
              <canvas id="interactionChart"></canvas>
            </div>
            <div class="interaction-stats" id="interactionStats">
              <!-- Will be populated via AJAX -->
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="chart-card">
          <div class="chart-header">
            <h3 class="chart-title">
              <i class="fas fa-chart-pie"></i>
              Status Kunjungan Kontrak
            </h3>
          </div>
          <div class="chart-body">
            <div class="loading-spinner" id="visitStatusLoading">
              <div class="spinner"></div>
              <p>Memuat status kunjungan...</p>
            </div>
            <div class="chart-container">
              <canvas id="visitStatusChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
      <div class="col-lg-7 mb-4">
        <div class="table-card">
          <div class="table-header">
            <h3 class="chart-title">
              <i class="fas fa-list"></i>
              Daftar Kontrak Aktif
            </h3>
            <div class="d-flex gap-2">
              <button class="btn-custom btn-primary" onclick="refreshContracts()">
                <i class="fas fa-sync"></i>
                Refresh
              </button>
              <button class="btn-custom btn-outline" onclick="filterContracts()">
                <i class="fas fa-filter"></i>
                Filter
              </button>
            </div>
          </div>
          
          <div class="loading-spinner" id="contractsLoading">
            <div class="spinner"></div>
            <p>Memuat daftar kontrak...</p>
          </div>

          <div class="table-responsive" id="contractsTable" style="display: none;">
            <table class="table">
              <thead>
                <tr>
                  <th><i class="fas fa-hashtag"></i> No Kontrak</th>
                  <th><i class="fas fa-user"></i> Nama Customer</th>
                  <th><i class="fas fa-calendar-alt"></i> Periode</th>
                  <th><i class="fas fa-map-pin"></i> Status Kunjungan</th>
                  <th><i class="fas fa-star"></i> Prioritas</th>
                  <th><i class="fas fa-cogs"></i> Aksi</th>
                </tr>
              </thead>
              <tbody id="contractsTableBody">
                <!-- Data akan dimuat via AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-5 mb-4">
        <div class="table-card">
          <div class="table-header">
            <h3 class="chart-title">
              <i class="fas fa-bell"></i>
              Reminder Kontrak
            </h3>
            <button class="btn-custom btn-warning" onclick="markAllSeen()">
              <i class="fas fa-check"></i>
              Tandai Semua
            </button>
          </div>
          
          <div class="loading-spinner" id="remindersLoading">
            <div class="spinner"></div>
            <p>Memuat reminder...</p>
          </div>

          <div class="table-responsive" id="remindersTable" style="display: none;">
            <table class="table">
              <thead>
                <tr>
                  <th><i class="fas fa-hashtag"></i> Kontrak</th>
                  <th><i class="fas fa-user"></i> Customer</th>
                  <th><i class="fas fa-clock"></i> Jatuh Tempo</th>
                  <th><i class="fas fa-cogs"></i> Aksi</th>
                </tr>
              </thead>
              <tbody id="remindersTableBody">
                <!-- Data akan dimuat via AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
let interactionChart, visitStatusChart;
let currentInteractionFilter = 'week';

// Initialize dashboard
$(document).ready(function() {
  loadDashboardData();
  initializeCharts();
  loadContractsData();
  loadRemindersData();
  
  // Auto refresh every 5 minutes
  setInterval(refreshAllData, 300000);
});

// Load main dashboard statistics
function loadDashboardData() {
  // Simulate AJAX call with dummy data
  setTimeout(function() {
    // Active contracts statistics
    updateActiveContractsStats({
      value: 42,
      trend: '+5 dari bulan lalu',
      percentage: 85,
      isPositive: true
    });
    
    // Unvisited contracts statistics
    updateUnvisitedStats({
      value: 8,
      trend: 'Segera jadwalkan!',
      isUrgent: true
    });
    
    // Reminder statistics
    updateReminderStats({
      value: 5,
      trend: '3 akan berakhir minggu ini',
      isNegative: true
    });
    
    // Customer satisfaction
    updateSatisfactionStats({
      value: '4.8/5',
      trend: 'Rating kepuasan excellent',
      isPositive: true
    });
  }, 1000);
}

// Update statistics functions
function updateActiveContractsStats(data) {
  $('#activeContractsValue').text(data.value);
  $('#activeContractsProgress').css('width', data.percentage + '%');
  $('#activeContractsTrend').removeClass('positive negative').addClass(data.isPositive ? 'positive' : 'negative');
  $('#activeContractsTrend').html(`<i class="fas fa-arrow-${data.isPositive ? 'up' : 'down'}"></i> ${data.trend}`);
}

function updateUnvisitedStats(data) {
  $('#unvisitedValue').text(data.value);
  $('#unvisitedTrend').removeClass('urgent positive negative').addClass(data.isUrgent ? 'urgent' : 'positive');
  $('#unvisitedTrend').html(`<i class="fas fa-exclamation-triangle"></i> ${data.trend}`);
}

function updateReminderStats(data) {
  $('#reminderValue').text(data.value);
  $('#reminderTrend').removeClass('positive negative').addClass(data.isNegative ? 'negative' : 'positive');
  $('#reminderTrend').html(`<i class="fas fa-clock"></i> ${data.trend}`);
}

function updateSatisfactionStats(data) {
  $('#satisfactionValue').text(data.value);
  $('#satisfactionTrend').removeClass('positive negative info').addClass('info');
  $('#satisfactionTrend').html(`<i class="fas fa-star"></i> ${data.trend}`);
}

// Initialize charts
function initializeCharts() {
  $('#interactionChartLoading').show();
  $('#visitStatusLoading').show();
  
  setTimeout(function() {
    initInteractionChart();
    initVisitStatusChart();
    updateInteractionStats();
    $('#interactionChartLoading').hide();
    $('#visitStatusLoading').hide();
  }, 1500);
}

// Initialize interaction chart
function initInteractionChart() {
  const ctx = document.getElementById('interactionChart').getContext('2d');
  
  interactionChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Kunjungan', 'Telepon', 'Email', 'Video Call', 'WhatsApp'],
      datasets: [{
        label: 'Jumlah Interaksi',
        data: [25, 18, 12, 8, 15],
        backgroundColor: [
          '#10b981',
          '#3b82f6',
          '#f59e0b',
          '#8b5cf6',
          '#06b6d4'
        ],
        borderRadius: 8,
        borderSkipped: false,
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
          cornerRadius: 8,
          callbacks: {
            label: function(context) {
              return `${context.raw} interaksi`;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0,0,0,0.1)'
          },
          ticks: {
            color: '#6b7280'
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#6b7280'
          }
        }
      }
    }
  });
}

// Initialize visit status chart
function initVisitStatusChart() {
  const ctx = document.getElementById('visitStatusChart').getContext('2d');
  
  visitStatusChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sudah Dikunjungi', 'Belum Dikunjungi', 'Dijadwalkan'],
      datasets: [{
        label: 'Status Kunjungan',
        data: [34, 8, 12],
        backgroundColor: [
          '#10b981',
          '#f59e0b',
          '#3b82f6'
        ],
        borderWidth: 0,
        cutout: '60%'
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
            color: '#6b7280',
            font: {
              size: 12
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          cornerRadius: 8,
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.raw / total) * 100).toFixed(1);
              return `${context.label}: ${context.raw} kontrak (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}

// Update interaction statistics
function updateInteractionStats() {
  const statsData = [
    { label: 'Kunjungan', count: 25, icon: 'fas fa-home' },
    { label: 'Telepon', count: 18, icon: 'fas fa-phone' },
    { label: 'Email', count: 12, icon: 'fas fa-envelope' },
    { label: 'Video Call', count: 8, icon: 'fas fa-video' },
    { label: 'WhatsApp', count: 15, icon: 'fab fa-whatsapp' }
  ];

  let html = '';
  statsData.forEach(item => {
    html += `
      <div class="interaction-item">
        <i class="${item.icon}" style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 0.5rem;"></i>
        <div class="interaction-count">${item.count}</div>
        <div class="interaction-label">${item.label}</div>
      </div>
    `;
  });

  $('#interactionStats').html(html);
}

// Load contracts data
function loadContractsData() {
  $('#contractsLoading').show();
  $('#contractsTable').hide();
  
  // Simulate AJAX call
  setTimeout(function() {
    const dummyContracts = [
      {
        contractNo: 'KTRK-00123',
        customerName: 'Budi Santoso',
        startDate: '01 Jan 2024',
        endDate: '31 Des 2024',
        visitStatus: 'visited',
        priority: 'high',
        initials: 'BS',
        color: 'success'
      },
      {
        contractNo: 'KTRK-00124',
        customerName: 'Siti Rahayu',
        startDate: '15 Feb 2024',
        endDate: '15 Feb 2025',
        visitStatus: 'pending',
        priority: 'medium',
        initials: 'SR',
        color: 'warning'
      },
      {
        contractNo: 'KTRK-00125',
        customerName: 'Ahmad Wijaya',
        startDate: '10 Mar 2024',
        endDate: '10 Mar 2025',
        visitStatus: 'overdue',
        priority: 'high',
        initials: 'AW',
        color: 'danger'
      },
      {
        contractNo: 'KTRK-00126',
        customerName: 'Maria Gonzales',
        startDate: '05 Apr 2024',
        endDate: '05 Apr 2025',
        visitStatus: 'visited',
        priority: 'low',
        initials: 'MG',
        color: 'info'
      },
      {
        contractNo: 'KTRK-00127',
        customerName: 'Rizky Pratama',
        startDate: '20 May 2024',
        endDate: '20 May 2025',
        visitStatus: 'pending',
        priority: 'medium',
        initials: 'RP',
        color: 'primary'
      }
    ];
    
    populateContractsTable(dummyContracts);
    $('#contractsLoading').hide();
    $('#contractsTable').show();
  }, 2000);
}

// Populate contracts table
function populateContractsTable(data) {
  let html = '';
  
  data.forEach(function(contract, index) {
    const visitStatusBadge = getVisitStatusBadge(contract.visitStatus);
    const priorityBadge = getPriorityBadge(contract.priority);
    
    html += `
      <tr>
        <td>
          <span class="fw-medium">${contract.contractNo}</span>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="user-avatar bg-${contract.color} me-2">
              ${contract.initials}
            </div>
            <div>
              <div class="fw-medium">${contract.customerName}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="text-sm">
            <div>${contract.startDate}</div>
            <div class="text-muted">s/d ${contract.endDate}</div>
          </div>
        </td>
        <td>
          <span class="status-indicator ${contract.visitStatus}"></span>
          ${visitStatusBadge}
        </td>
        <td>
          ${priorityBadge}
        </td>
        <td>
          <div class="d-flex gap-1">
            <button class="btn-custom btn-primary btn-sm" onclick="visitContract('${contract.contractNo}')">
              <i class="fas fa-map-marker-alt"></i>
            </button>
            <button class="btn-custom btn-outline btn-sm" onclick="viewContract('${contract.contractNo}')">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  });
  
  $('#contractsTableBody').html(html);
}

// Load reminders data
function loadRemindersData() {
  $('#remindersLoading').show();
  $('#remindersTable').hide();
  
  // Simulate AJAX call
  setTimeout(function() {
    const dummyReminders = [
      {
        contractNo: 'KTRK-00456',
        customerName: 'Siti Rahayu',
        dueDate: '31 Mar 2024',
        daysLeft: 15,
        priority: 'high'
      },
      {
        contractNo: 'KTRK-00457',
        customerName: 'Ahmad Budi',
        dueDate: '15 Apr 2024',
        daysLeft: 30,
        priority: 'medium'
      },
      {
        contractNo: 'KTRK-00458',
        customerName: 'Lisa Permata',
        dueDate: '10 Apr 2024',
        daysLeft: 25,
        priority: 'medium'
      },
      {
        contractNo: 'KTRK-00459',
        customerName: 'Dedi Kurniawan',
        dueDate: '28 Mar 2024',
        daysLeft: 12,
        priority: 'high'
      },
      {
        contractNo: 'KTRK-00460',
        customerName: 'Rani Setia',
        dueDate: '05 May 2024',
        daysLeft: 50,
        priority: 'low'
      }
    ];
    
    populateRemindersTable(dummyReminders);
    $('#remindersLoading').hide();
    $('#remindersTable').show();
  }, 1800);
}

// Populate reminders table
function populateRemindersTable(data) {
  let html = '';
  
  data.forEach(function(reminder, index) {
    const urgencyClass = reminder.daysLeft <= 15 ? 'danger' : 
                        reminder.daysLeft <= 30 ? 'warning' : 'success';
    
    html += `
      <tr>
        <td>
          <span class="fw-medium">${reminder.contractNo}</span>
        </td>
        <td>
          <span class="text-sm">${reminder.customerName}</span>
        </td>
        <td>
          <div class="text-sm">
            <div>${reminder.dueDate}</div>
            <span class="badge-custom badge-${urgencyClass}">
              ${reminder.daysLeft} hari lagi
            </span>
          </div>
        </td>
        <td>
          <div class="d-flex gap-1">
            <button class="btn-custom btn-warning btn-sm" onclick="followUp('${reminder.contractNo}')">
              <i class="fas fa-phone"></i>
            </button>
            <button class="btn-custom btn-success btn-sm" onclick="scheduleVisit('${reminder.contractNo}')">
              <i class="fas fa-calendar"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  });
  
  $('#remindersTableBody').html(html);
}

// Helper functions
function getVisitStatusBadge(status) {
  const badges = {
    'visited': '<span class="badge-custom badge-success">Sudah Dikunjungi</span>',
    'pending': '<span class="badge-custom badge-warning">Belum Dikunjungi</span>',
    'overdue': '<span class="badge-custom badge-danger">Terlambat</span>'
  };
  return badges[status] || '<span class="badge-custom badge-info">Unknown</span>';
}

function getPriorityBadge(priority) {
  const badges = {
    'high': '<div class="contract-priority priority-high"><i class="fas fa-exclamation-circle"></i> High</div>',
    'medium': '<div class="contract-priority priority-medium"><i class="fas fa-minus-circle"></i> Medium</div>',
    'low': '<div class="contract-priority priority-low"><i class="fas fa-check-circle"></i> Low</div>'
  };
  return badges[priority] || '<div class="contract-priority"><i class="fas fa-question-circle"></i> Unknown</div>';
}

// Filter interactions by period
function filterInteractions(period) {
  currentInteractionFilter = period;
  
  // Update active tab
  $('.filter-tab').removeClass('active');
  event.target.classList.add('active');
  
  // Show loading
  $('#interactionChartLoading').show();
  
  // Simulate AJAX call with different data based on period
  setTimeout(function() {
    let newData;
    
    switch(period) {
      case 'week':
        newData = [25, 18, 12, 8, 15];
        break;
      case 'month':
        newData = [95, 72, 48, 32, 58];
        break;
      default:
        newData = [25, 18, 12, 8, 15];
    }
    
    interactionChart.data.datasets[0].data = newData;
    interactionChart.update('active');
    
    // Update stats
    updateInteractionStats();
    
    $('#interactionChartLoading').hide();
  }, 1000);
}

// Action functions
function scheduleVisit() {
  showNotification('Membuka form penjadwalan kunjungan...', 'info');
}

function addContract() {
  showNotification('Membuka form tambah kontrak baru...', 'info');
}

function exportData() {
  const btn = event.target.closest('a');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
  
  setTimeout(function() {
    showNotification('Data berhasil diexport ke Excel!', 'success');
    btn.innerHTML = originalText;
  }, 2000);
}

function generateReport() {
  showNotification('Generating laporan bulanan...', 'info');
}

function refreshContracts() {
  loadContractsData();
  showNotification('Daftar kontrak berhasil direfresh!', 'success');
}

function filterContracts() {
  showNotification('Membuka filter kontrak...', 'info');
}

function markAllSeen() {
  showNotification('Semua reminder telah ditandai sebagai dibaca', 'success');
}

function visitContract(contractNo) {
  showNotification(`Mencatat kunjungan untuk kontrak ${contractNo}`, 'success');
}

function viewContract(contractNo) {
  showNotification(`Membuka detail kontrak ${contractNo}`, 'info');
}

function followUp(contractNo) {
  showNotification(`Melakukan follow up kontrak ${contractNo}`, 'warning');
}

function scheduleVisit(contractNo) {
  showNotification(`Menjadwalkan kunjungan kontrak ${contractNo}`, 'info');
}

// Refresh all data
function refreshAllData() {
  loadDashboardData();
  
  // Refresh charts
  if (interactionChart) {
    $('#interactionChartLoading').show();
    setTimeout(function() {
      interactionChart.update('active');
      $('#interactionChartLoading').hide();
    }, 1000);
  }
  
  if (visitStatusChart) {
    $('#visitStatusLoading').show();
    setTimeout(function() {
      visitStatusChart.update('active');
      $('#visitStatusLoading').hide();
    }, 1000);
  }
  
  // Reload tables
  loadContractsData();
  loadRemindersData();
  
  showNotification('Semua data berhasil direfresh!', 'success');
}

// Show notification
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = $(`
    <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `);
  
  // Add to body
  $('body').append(notification);
  
  // Auto remove after 5 seconds
  setTimeout(function() {
    notification.fadeOut(function() {
      $(this).remove();
    });
  }, 5000);
}

// Real-time updates simulation
function simulateRealTimeUpdates() {
  setInterval(function() {
    // Randomly update unvisited contracts
    if (Math.random() > 0.8) {
      const randomValue = Math.floor(Math.random() * 3) + 6;
      $('#unvisitedValue').text(randomValue);
    }
    
    // Update reminder count
    if (Math.random() > 0.9) {
      const reminderCount = Math.floor(Math.random() * 2) + 4;
      $('#reminderValue').text(reminderCount);
    }
  }, 30000); // Update every 30 seconds
}

// Advanced filtering
function applyAdvancedFilter(filterType, filterValue) {
  showNotification(`Menerapkan filter ${filterType}: ${filterValue}`, 'info');
  // Implement filtering logic here
}

// Export functions
function exportToExcel() {
  showNotification('Exporting data ke Excel...', 'info');
  // Implement Excel export
}

function exportToPDF() {
  showNotification('Generating PDF report...', 'info');
  // Implement PDF export
}

// Keyboard shortcuts
$(document).keydown(function(e) {
  // Ctrl+R for refresh
  if (e.ctrlKey && e.keyCode === 82) {
    e.preventDefault();
    refreshAllData();
  }
  
  // Ctrl+S for schedule visit
  if (e.ctrlKey && e.keyCode === 83) {
    e.preventDefault();
    scheduleVisit();
  }
});

// Performance monitoring
function trackPerformance(operation) {
  const startTime = performance.now();
  return function() {
    const endTime = performance.now();
    console.log(`${operation} completed in ${endTime - startTime} ms`);
  };
}

// Initialize everything
$(document).ready(function() {
  console.log('RO Dashboard initialized successfully');
  simulateRealTimeUpdates();
  
  // Initialize tooltips
  $('[data-bs-toggle="tooltip"]').tooltip();
});

// Handle window resize
$(window).resize(function() {
  if (interactionChart) interactionChart.resize();
  if (visitStatusChart) visitStatusChart.resize();
});

// Error handling
window.addEventListener('error', function(e) {
  console.error('Dashboard error:', e.error);
  showNotification('Terjadi kesalahan. Silakan refresh halaman.', 'danger');
});

// Local storage for user preferences
function saveUserPreferences(key, value) {
  try {
    localStorage.setItem(`ro_dashboard_${key}`, JSON.stringify(value));
  } catch (e) {
    console.warn('Could not save preferences:', e);
  }
}

function getUserPreferences(key) {
  try {
    const data = localStorage.getItem(`ro_dashboard_${key}`);
    return data ? JSON.parse(data) : null;
  } catch (e) {
    console.warn('Could not load preferences:', e);
    return null;
  }
}
</script>
@endsection