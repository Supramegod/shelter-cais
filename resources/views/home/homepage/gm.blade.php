@extends('layouts.master')
@section('title', 'Dashboard General Manager')
@section('pageStyle')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root {
    --primary-color: #3b82f6;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
  }

  .stats-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  .stats-card-header {
    display: flex;
    justify-content: between;
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
  .stats-icon.info { background: var(--info-color); }
  .stats-icon.primary { background: var(--primary-color); }

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

  .progress-container {
    margin-top: 1rem;
  }

  .progress-bar-custom {
    height: 8px;
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
  .progress-fill.primary { background: var(--primary-color); }

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
    min-height: 400px;
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
    justify-content: between;
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

  .badge-primary { background: #eff6ff; color: var(--primary-color); }
  .badge-success { background: #ecfdf5; color: var(--success-color); }
  .badge-warning { background: #fffbeb; color: var(--warning-color); }
  .badge-danger { background: #fef2f2; color: var(--danger-color); }
  .badge-info { background: #f0f9ff; color: var(--info-color); }

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
            <i class="fas fa-chart-line me-2"></i>
            Dashboard General Manager
          </h1>
          <p class="dashboard-subtitle">
            Selamat datang! Pantau performa dan analisa bisnis secara real-time
          </p>
        </div>
      </div>
    </div>
  </div> -->

  <div class="container">
    <!-- Quick Actions -->
    <div class="quick-actions">
      <a href="#" class="quick-action-btn" onclick="exportReport()">
        <i class="fas fa-download"></i>
        Export Laporan
      </a>
      <a href="#" class="quick-action-btn" onclick="refreshData()">
        <i class="fas fa-sync"></i>
        Refresh Data
      </a>
      <a href="#" class="quick-action-btn" onclick="showSettings()">
        <i class="fas fa-cog"></i>
        Pengaturan KPI
      </a>
      <a href="#" class="quick-action-btn" onclick="sendReport()">
        <i class="fas fa-paper-plane"></i>
        Kirim Laporan
      </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card" id="contractCard">
          <div class="stats-card-header">
            <div class="stats-icon success">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="stats-value" id="contractValue">-</div>
          <div class="stats-label">Target vs Realisasi Kontrak</div>
          <div class="progress-container">
            <div class="progress-bar-custom">
              <div class="progress-fill success" id="contractProgress" style="width: 0%"></div>
            </div>
          </div>
          <div class="stats-trend positive" id="contractTrend">
            <i class="fas fa-arrow-up"></i>
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card" id="complaintCard">
          <div class="stats-card-header">
            <div class="stats-icon warning">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
          <div class="stats-value" id="complaintValue">-</div>
          <div class="stats-label">Total Komplain Bulan Ini</div>
          <div class="stats-trend" id="complaintTrend">
            <i class="fas fa-arrow-down"></i>
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card" id="serviceCard">
          <div class="stats-card-header">
            <div class="stats-icon info">
              <i class="fas fa-star"></i>
            </div>
          </div>
          <div class="stats-value" id="serviceValue">-</div>
          <div class="stats-label">Top Layanan</div>
          <div class="stats-trend positive" id="serviceTrend">
            Loading...
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card" id="productivityCard">
          <div class="stats-card-header">
            <div class="stats-icon primary">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="stats-value" id="productivityValue">-</div>
          <div class="stats-label">Produktivitas Tim</div>
          <div class="progress-container">
            <div class="progress-bar-custom">
              <div class="progress-fill primary" id="productivityProgress" style="width: 0%"></div>
            </div>
          </div>
          <div class="stats-trend positive" id="productivityTrend">
            <i class="fas fa-trophy"></i>
            Loading...
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
      <div class="col-lg-8 mb-4">
        <div class="chart-card">
          <div class="chart-header">
            <h3 class="chart-title">
              <i class="fas fa-chart-bar"></i>
              Analisa Komplain Detail
            </h3>
          </div>
          <div class="chart-body">
            <div class="filter-tabs">
              <button class="filter-tab active" onclick="filterComplaints('month')">Bulan Ini</button>
              <button class="filter-tab" onclick="filterComplaints('quarter')">Kuartal Ini</button>
              <button class="filter-tab" onclick="filterComplaints('year')">Tahun Ini</button>
            </div>
            <div class="loading-spinner" id="complaintChartLoading">
              <div class="spinner"></div>
              <p>Memuat data komplain...</p>
            </div>
            <div class="chart-container">
              <canvas id="complaintChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="chart-card">
          <div class="chart-header">
            <h3 class="chart-title">
              <i class="fas fa-trophy"></i>
              Distribusi Top Layanan
            </h3>
          </div>
          <div class="chart-body">
            <div class="loading-spinner" id="serviceChartLoading">
              <div class="spinner"></div>
              <p>Memuat data layanan...</p>
            </div>
            <div class="chart-container">
              <canvas id="serviceChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Table -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="table-card">
          <div class="table-header">
            <h3 class="chart-title">
              <i class="fas fa-file-alt"></i>
              Report Tidak Deal - Analisis Mendalam
            </h3>
            <div class="d-flex gap-2">
              <button class="btn-custom btn-primary" onclick="downloadReport()">
                <i class="fas fa-download"></i>
                Download
              </button>
              <button class="btn-custom btn-outline" onclick="openFilter()">
                <i class="fas fa-filter"></i>
                Filter
              </button>
            </div>
          </div>
          
          <div class="loading-spinner" id="reportLoading">
            <div class="spinner"></div>
            <p>Memuat data report...</p>
          </div>

          <div class="table-responsive" id="reportTable" style="display: none;">
            <table class="table">
              <thead>
                <tr>
                  <th><i class="fas fa-calendar"></i> Tanggal</th>
                  <th><i class="fas fa-user"></i> Nama Customer</th>
                  <th><i class="fas fa-briefcase"></i> Layanan</th>
                  <th><i class="fas fa-times-circle"></i> Alasan Tidak Deal</th>
                  <th><i class="fas fa-user-tie"></i> Sales</th>
                  <th><i class="fas fa-building"></i> Cabang</th>
                  <th><i class="fas fa-dollar-sign"></i> Potensi Nilai</th>
                  <th><i class="fas fa-cogs"></i> Aksi</th>
                </tr>
              </thead>
              <tbody id="reportTableBody">
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
let complaintChart, serviceChart;
let currentComplaintFilter = 'month';

// Initialize dashboard
$(document).ready(function() {
  loadDashboardData();
  initializeCharts();
  loadReportData();
  
  // Auto refresh every 5 minutes
  setInterval(refreshData, 300000);
});

// Load main dashboard statistics
function loadDashboardData() {
  // Simulate AJAX call with dummy data
  setTimeout(function() {
    // Contract statistics
    updateContractStats({
      value: 92,
      trend: '+8% dari bulan lalu',
      isPositive: true
    });
    
    // Complaint statistics
    updateComplaintStats({
      value: 15,
      trend: '↓ 20% dari bulan lalu',
      isPositive: true
    });
    
    // Service statistics
    updateServiceStats({
      value: 'KPR',
      percentage: 45,
      trend: '45% dari total kontrak'
    });
    
    // Productivity statistics
    updateProductivityStats({
      value: 95,
      trend: 'Excellent Performance',
      isPositive: true
    });
  }, 1000);
}

// Update contract statistics
function updateContractStats(data) {
  $('#contractValue').text(data.value + '%');
  $('#contractProgress').css('width', data.value + '%');
  $('#contractTrend').removeClass('positive negative').addClass(data.isPositive ? 'positive' : 'negative');
  $('#contractTrend').html(`<i class="fas fa-arrow-${data.isPositive ? 'up' : 'down'}"></i> ${data.trend}`);
}

// Update complaint statistics
function updateComplaintStats(data) {
  $('#complaintValue').text(data.value);
  $('#complaintTrend').removeClass('positive negative').addClass(data.isPositive ? 'positive' : 'negative');
  $('#complaintTrend').html(`<i class="fas fa-arrow-${data.isPositive ? 'down' : 'up'}"></i> ${data.trend}`);
}

// Update service statistics
function updateServiceStats(data) {
  $('#serviceValue').text(data.value);
  $('#serviceTrend').text(data.trend);
}

// Update productivity statistics
function updateProductivityStats(data) {
  $('#productivityValue').text(data.value + '%');
  $('#productivityProgress').css('width', data.value + '%');
  $('#productivityTrend').removeClass('positive negative').addClass(data.isPositive ? 'positive' : 'negative');
  $('#productivityTrend').html(`<i class="fas fa-trophy"></i> ${data.trend}`);
}

// Initialize charts
function initializeCharts() {
  $('#complaintChartLoading').show();
  $('#serviceChartLoading').show();
  
  setTimeout(function() {
    initComplaintChart();
    initServiceChart();
    $('#complaintChartLoading').hide();
    $('#serviceChartLoading').hide();
  }, 1500);
}

// Initialize complaint analysis chart
function initComplaintChart() {
  const ctx = document.getElementById('complaintChart').getContext('2d');
  
  complaintChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Pelayanan', 'Produk', 'Proses', 'Sistem', 'Komunikasi', 'Waktu'],
      datasets: [{
        label: 'Jumlah Komplain',
        data: [8, 4, 2, 1, 3, 2],
        backgroundColor: [
          '#ef4444',
          '#f59e0b', 
          '#06b6d4',
          '#8b5cf6',
          '#10b981',
          '#6b7280'
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
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.raw / total) * 100).toFixed(1);
              return `${context.raw} komplain (${percentage}%)`;
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

// Initialize service distribution chart
function initServiceChart() {
  const ctx = document.getElementById('serviceChart').getContext('2d');
  
  serviceChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['KPR', 'KTA', 'Investasi', 'Asuransi', 'Tabungan'],
      datasets: [{
        label: 'Distribusi Layanan',
        data: [45, 25, 15, 10, 5],
        backgroundColor: [
          '#3b82f6',
          '#10b981',
          '#f59e0b',
          '#ef4444',
          '#6b7280'
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
              return `${context.label}: ${context.raw}% dari total`;
            }
          }
        }
      }
    }
  });
}

// Load report data
function loadReportData() {
  $('#reportLoading').show();
  $('#reportTable').hide();
  
  // Simulate AJAX call
  setTimeout(function() {
    const dummyData = [
      {
        date: '15 Mar 2024',
        name: 'Budi Santoso',
        service: 'KPR',
        reason: 'Harga tidak sesuai',
        sales: 'Andi Wijaya',
        branch: 'Jakarta Pusat',
        potential: 'Rp 500.000.000',
        initials: 'BS',
        color: 'primary'
      },
      {
        date: '14 Mar 2024',
        name: 'Siti Rahayu',
        service: 'KTA',
        reason: 'Butuh waktu pikir',
        sales: 'Budi Hartono',
        branch: 'Jakarta Selatan',
        potential: 'Rp 75.000.000',
        initials: 'SR',
        color: 'success'
      },
      {
        date: '13 Mar 2024',
        name: 'Ahmad Wijaya',
        service: 'Investasi',
        reason: 'Kurang informasi',
        sales: 'Citra Dewi',
        branch: 'Bekasi',
        potential: 'Rp 200.000.000',
        initials: 'AW',
        color: 'info'
      },
      {
        date: '12 Mar 2024',
        name: 'Maria Gonzales',
        service: 'Asuransi',
        reason: 'Premi terlalu tinggi',
        sales: 'Dedy Kurniawan',
        branch: 'Jakarta Barat',
        potential: 'Rp 25.000.000',
        initials: 'MG',
        color: 'warning'
      },
      {
        date: '11 Mar 2024',
        name: 'Rizky Pratama',
        service: 'KPR',
        reason: 'Dokumen belum lengkap',
        sales: 'Eni Sari',
        branch: 'Tangerang',
        potential: 'Rp 800.000.000',
        initials: 'RP',
        color: 'danger'
      }
    ];
    
    populateReportTable(dummyData);
    $('#reportLoading').hide();
    $('#reportTable').show();
  }, 2000);
}

// Populate report table
function populateReportTable(data) {
  let html = '';
  
  data.forEach(function(item, index) {
    html += `
      <tr>
        <td>
          <span class="text-muted">${item.date}</span>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="user-avatar bg-${item.color} me-2">
              ${item.initials}
            </div>
            <div>
              <div class="fw-medium">${item.name}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="badge-custom badge-${getServiceBadgeColor(item.service)}">
            ${item.service}
          </span>
        </td>
        <td>
          <span class="text-${getReasonColor(item.reason)}">
            <i class="fas fa-exclamation-circle me-1"></i>
            ${item.reason}
          </span>
        </td>
        <td>
          <span class="badge-custom badge-primary">${item.sales}</span>
        </td>
        <td>${item.branch}</td>
        <td>
          <span class="fw-bold text-success">${item.potential}</span>
        </td>
        <td>
          <button class="btn-custom btn-outline" onclick="viewDetail(${index})">
            <i class="fas fa-eye"></i>
            Detail
          </button>
        </td>
      </tr>
    `;
  });
  
  $('#reportTableBody').html(html);
}

// Helper functions
function getServiceBadgeColor(service) {
  const colors = {
    'KPR': 'primary',
    'KTA': 'warning',
    'Investasi': 'info',
    'Asuransi': 'success',
    'Tabungan': 'secondary'
  };
  return colors[service] || 'primary';
}

function getReasonColor(reason) {
  if (reason.includes('harga') || reason.includes('mahal') || reason.includes('tinggi')) {
    return 'danger';
  } else if (reason.includes('waktu') || reason.includes('pikir')) {
    return 'warning';
  } else if (reason.includes('informasi') || reason.includes('dokumen')) {
    return 'info';
  }
  return 'muted';
}

// Filter complaints by period
function filterComplaints(period) {
  currentComplaintFilter = period;
  
  // Update active tab
  $('.filter-tab').removeClass('active');
  event.target.classList.add('active');
  
  // Show loading
  $('#complaintChartLoading').show();
  $('#complaintChart').hide();
  
  // Simulate AJAX call with different data based on period
  setTimeout(function() {
    let newData;
    
    switch(period) {
      case 'month':
        newData = [8, 4, 2, 1, 3, 2];
        break;
      case 'quarter':
        newData = [25, 12, 8, 5, 9, 6];
        break;
      case 'year':
        newData = [95, 48, 32, 20, 35, 24];
        break;
      default:
        newData = [8, 4, 2, 1, 3, 2];
    }
    
    complaintChart.data.datasets[0].data = newData;
    complaintChart.update('active');
    
    $('#complaintChartLoading').hide();
    $('#complaintChart').show();
  }, 1000);
}

// View detail function
function viewDetail(index) {
  alert(`Menampilkan detail untuk record ke-${index + 1}`);
  // Implementasi modal atau redirect ke halaman detail
}

// Export report function
function exportReport() {
  // Show loading indicator
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
  btn.disabled = true;
  
  // Simulate export process
  setTimeout(function() {
    alert('Laporan berhasil diexport ke Excel!');
    btn.innerHTML = originalText;
    btn.disabled = false;
  }, 2000);
}

// Download report function
function downloadReport() {
  // Show loading indicator
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
  btn.disabled = true;
  
  // Simulate download process
  setTimeout(function() {
    alert('Report sedang didownload!');
    btn.innerHTML = originalText;
    btn.disabled = false;
  }, 1500);
}

// Open filter modal
function openFilter() {
  alert('Membuka filter modal - implementasi sesuai kebutuhan');
  // Implementasi modal filter
}

// Refresh all data
function refreshData() {
  // Show loading indicators
  $('#contractValue, #complaintValue, #serviceValue, #productivityValue').text('-');
  $('#contractTrend, #complaintTrend, #serviceTrend, #productivityTrend').text('Loading...');
  
  // Reload all data
  loadDashboardData();
  
  // Refresh charts
  if (complaintChart) {
    $('#complaintChartLoading').show();
    setTimeout(function() {
      complaintChart.update('active');
      $('#complaintChartLoading').hide();
    }, 1000);
  }
  
  if (serviceChart) {
    $('#serviceChartLoading').show();
    setTimeout(function() {
      serviceChart.update('active');
      $('#serviceChartLoading').hide();
    }, 1000);
  }
  
  // Reload report data
  loadReportData();
  
  // Show success notification
  setTimeout(function() {
    showNotification('Data berhasil direfresh!', 'success');
  }, 2000);
}

// Show settings modal
function showSettings() {
  alert('Membuka pengaturan KPI - implementasi sesuai kebutuhan');
  // Implementasi modal settings
}

// Send report function
function sendReport() {
  // Show loading indicator
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
  btn.disabled = true;
  
  // Simulate sending process
  setTimeout(function() {
    alert('Laporan berhasil dikirim via email!');
    btn.innerHTML = originalText;
    btn.disabled = false;
    showNotification('Laporan berhasil dikirim!', 'success');
  }, 2000);
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

// Real-time data update simulation
function simulateRealTimeUpdates() {
  setInterval(function() {
    // Randomly update some statistics
    if (Math.random() > 0.7) {
      const randomValue = Math.floor(Math.random() * 5) + 15;
      $('#complaintValue').text(randomValue);
      
      // Update trend
      const isIncrease = Math.random() > 0.5;
      const trendText = isIncrease ? '↑ +2 dari kemarin' : '↓ -1 dari kemarin';
      $('#complaintTrend').removeClass('positive negative').addClass(isIncrease ? 'negative' : 'positive');
      $('#complaintTrend').html(`<i class="fas fa-arrow-${isIncrease ? 'up' : 'down'}"></i> ${trendText}`);
    }
  }, 30000); // Update every 30 seconds
}

// Initialize real-time updates
$(document).ready(function() {
  simulateRealTimeUpdates();
});

// Advanced filtering for report table
function applyAdvancedFilter() {
  const filters = {
    dateRange: $('#dateRange').val(),
    service: $('#serviceFilter').val(),
    branch: $('#branchFilter').val(),
    sales: $('#salesFilter').val(),
    reason: $('#reasonFilter').val()
  };
  
  // Show loading
  $('#reportLoading').show();
  $('#reportTable').hide();
  
  // Simulate AJAX filtering
  setTimeout(function() {
    // Filter logic would go here
    loadReportData(); // Reload with filters
  }, 1000);
}

// Export to different formats
function exportToFormat(format) {
  const formats = {
    'excel': 'Excel (.xlsx)',
    'pdf': 'PDF (.pdf)',
    'csv': 'CSV (.csv)'
  };
  
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Exporting to ${formats[format]}...`;
  btn.disabled = true;
  
  setTimeout(function() {
    alert(`Data berhasil diexport ke ${formats[format]}!`);
    btn.innerHTML = originalText;
    btn.disabled = false;
  }, 2000);
}

// Print report
function printReport() {
  window.print();
}

// Schedule report
function scheduleReport() {
  alert('Fitur penjadwalan laporan otomatis - implementasi sesuai kebutuhan');
  // Implementasi scheduling
}

// Dashboard analytics tracking
function trackAction(action, data = {}) {
  console.log('Analytics:', action, data);
  // Implementasi tracking analytics
}

// Handle responsive table on mobile
function handleResponsiveTable() {
  if (window.innerWidth < 768) {
    $('.table-responsive').addClass('table-mobile');
  } else {
    $('.table-responsive').removeClass('table-mobile');
  }
}

// Window resize handler
$(window).resize(function() {
  handleResponsiveTable();
  if (complaintChart) complaintChart.resize();
  if (serviceChart) serviceChart.resize();
});

// Initial responsive check
$(document).ready(function() {
  handleResponsiveTable();
});

// Keyboard shortcuts
$(document).keydown(function(e) {
  // Ctrl+R for refresh
  if (e.ctrlKey && e.keyCode === 82) {
    e.preventDefault();
    refreshData();
  }
  
  // Ctrl+E for export
  if (e.ctrlKey && e.keyCode === 69) {
    e.preventDefault();
    exportReport();
  }
});

// Initialize tooltips if using Bootstrap
$(document).ready(function() {
  if (typeof bootstrap !== 'undefined') {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }
});

// Performance monitoring
function monitorPerformance() {
  const startTime = performance.now();
  
  return function(operation) {
    const endTime = performance.now();
    console.log(`${operation} took ${endTime - startTime} milliseconds`);
  };
}

// Error handling for AJAX calls
function handleAjaxError(xhr, status, error) {
  console.error('AJAX Error:', error);
  showNotification('Terjadi kesalahan saat memuat data. Silakan coba lagi.', 'danger');
}

// Data validation
function validateData(data) {
  if (!data || typeof data !== 'object') {
    throw new Error('Invalid data format');
  }
  return true;
}

// Local storage management
function saveToLocalStorage(key, data) {
  try {
    localStorage.setItem(key, JSON.stringify(data));
  } catch (e) {
    console.warn('Could not save to localStorage:', e);
  }
}

function getFromLocalStorage(key) {
  try {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
  } catch (e) {
    console.warn('Could not read from localStorage:', e);
    return null;
  }
}

// Initialize everything when document is ready
$(document).ready(function() {
  console.log('Dashboard GM initialized successfully');
  trackAction('dashboard_loaded', { timestamp: new Date().toISOString() });
});
</script>
@endsection