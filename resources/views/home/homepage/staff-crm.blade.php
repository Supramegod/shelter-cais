@extends('layouts.master')
@section('title', 'Dashboard Staff CRM')
@section('pageStyle')
<style>
  :root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #48bb78;
    --warning-color: #ed8936;
    --danger-color: #f56565;
    --info-color: #4299e1;
    --light-bg: #f8fafc;
    --white: #ffffff;
    --text-primary: #2d3748;
    --text-secondary: #718096;
    --border-color: #e2e8f0;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }

  body {
    background-color: var(--white);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text-primary);
  }

  .dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  .page-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-top: 2.5rem; 
    margin-bottom: 2rem;
    color: white;
    box-shadow: var(--shadow-lg);
  }

  .page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.025em;
  }

  .page-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    margin-top: 2.5rem; 
  }

  .stat-card {
    background: var(--white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  }

  .stat-card.danger::before {
    background: linear-gradient(90deg, var(--danger-color), #ff8a80);
  }

  .stat-card.success::before {
    background: linear-gradient(90deg, var(--success-color), #81c784);
  }

  .stat-card.warning::before {
    background: linear-gradient(90deg, var(--warning-color), #ffb74d);
  }

  .stat-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
  }

  .stat-icon.danger {
    background: rgba(245, 101, 101, 0.1);
    color: var(--danger-color);
  }

  .stat-icon.success {
    background: rgba(72, 187, 120, 0.1);
    color: var(--success-color);
  }

  .stat-icon.warning {
    background: rgba(237, 137, 54, 0.1);
    color: var(--warning-color);
  }

  .stat-info h6 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
  }

  .stat-change {
    font-size: 0.875rem;
    margin-top: 0.5rem;
  }

  .stat-change.positive {
    color: var(--success-color);
  }

  .stat-change.negative {
    color: var(--danger-color);
  }

  .chart-container {
    background: var(--white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
  }

  .chart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .chart-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
  }

  .chart-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .btn-filter {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--white);
    color: var(--text-secondary);
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-filter:hover, .btn-filter.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
  }

  .table-container {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
  }

  .table-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .table-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
  }

  .table-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
  }

  .search-input {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.875rem;
    width: 250px;
    transition: border-color 0.2s ease;
  }

  .search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow);
  }

  .custom-table {
    width: 100%;
    border-collapse: collapse;
  }

  .custom-table th,
  .custom-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
  }

  .custom-table th {
    background: var(--light-bg);
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .custom-table tbody tr {
    transition: background-color 0.2s ease;
  }

  .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.05);
  }

  .badge {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .badge-success {
    background: rgba(72, 187, 120, 0.1);
    color: var(--success-color);
  }

  .badge-warning {
    background: rgba(237, 137, 54, 0.1);
    color: var(--warning-color);
  }

  .badge-danger {
    background: rgba(245, 101, 101, 0.1);
    color: var(--danger-color);
  }

  .badge-info {
    background: rgba(66, 153, 225, 0.1);
    color: var(--info-color);
  }

  .loading {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    color: var(--text-secondary);
  }

  .spinner {
    width: 2rem;
    height: 2rem;
    border: 2px solid var(--border-color);
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 0.5rem;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .chart-canvas {
    position: relative;
    height: 300px;
    width: 100%;
  }

  .tab-container {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 1.5rem;
  }

  .tab-button {
    padding: 0.75rem 1.5rem;
    border: none;
    background: none;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    font-weight: 500;
  }

  .tab-button.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
  }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
  }

  .progress-bar {
    width: 100%;
    height: 8px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 0.5rem;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 4px;
    transition: width 0.3s ease;
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 1rem;
    }

    .stats-grid {
      grid-template-columns: 1fr;
    }

    .search-input {
      width: 100%;
    }

    .table-controls {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  <!-- Page Header -->
  <!-- <div class="page-header">
    <h1 class="page-title">Dashboard Staff CRM</h1>
    <p class="page-subtitle">Kelola dan monitor keluhan customer dengan efisien</p>
  </div> -->

  <!-- Statistics Cards -->
  <div class="stats-grid" id="statsGrid">
    <!-- Cards will be loaded via AJAX -->
  </div>

  <!-- Charts Row -->
  <div class="row">
    <div class="col-xl-6 col-12 mb-4">
      <div class="chart-container">
        <div class="chart-header">
          <h5 class="chart-title">Statistik Penyelesaian Keluhan</h5>
          <div class="chart-controls">
            <button class="btn-filter active" data-period="week">Minggu Ini</button>
            <button class="btn-filter" data-period="month">Bulan Ini</button>
            <button class="btn-filter" data-period="year">Tahun Ini</button>
          </div>
        </div>
        <div class="chart-canvas">
          <canvas id="resolutionChart"></canvas>
        </div>
      </div>
    </div>
    
    <div class="col-xl-6 col-12 mb-4">
      <div class="chart-container">
        <div class="chart-header">
          <h5 class="chart-title">Statistik Per Kategori Keluhan</h5>
          <div class="chart-controls">
            <button class="btn-filter active" data-category-period="month">Bulan Ini</button>
            <button class="btn-filter" data-category-period="quarter">Quarter</button>
            <button class="btn-filter" data-category-period="year">Tahun Ini</button>
          </div>
        </div>
        <div class="chart-canvas">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Complaints Table -->
  <div class="table-container">
    <div class="table-header">
      <h5 class="table-title">Daftar Keluhan Customer</h5>
      <div class="table-controls">
        <input type="text" class="search-input" placeholder="Cari keluhan..." id="searchInput">
        <select class="btn-filter" id="statusFilter">
          <option value="">Semua Status</option>
          <option value="active">Aktif</option>
          <option value="solved">Solved</option>
          <option value="pending">Pending</option>
        </select>
        <button class="btn-primary" onclick="showAddComplaintModal()">
          <i class="mdi mdi-plus"></i> Tambah Keluhan
        </button>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div style="padding: 0 1.5rem;">
      <div class="tab-container">
        <button class="tab-button active" onclick="switchTab('all')">Semua Keluhan</button>
        <button class="tab-button" onclick="switchTab('active')">Keluhan Aktif</button>
        <button class="tab-button" onclick="switchTab('solved')">Keluhan Solved</button>
      </div>
    </div>

    <!-- Table Content -->
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID Keluhan</th>
            <th>Tanggal</th>
            <th>Nama Customer</th>
            <th>Kategori</th>
            <th>Prioritas</th>
            <th>Status</th>
            <th>Petugas</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="complaintsTableBody">
          <!-- Table data will be loaded via AJAX -->
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global variables
let resolutionChart, categoryChart;
let complaintsData = [];
let currentTab = 'all';

// Initialize dashboard
$(document).ready(function() {
  loadDashboardData();
  initializeEventListeners();
});

function initializeEventListeners() {
  // Period filter buttons
  $('.btn-filter[data-period]').on('click', function() {
    $('.btn-filter[data-period]').removeClass('active');
    $(this).addClass('active');
    updateResolutionChart($(this).data('period'));
  });

  $('.btn-filter[data-category-period]').on('click', function() {
    $('.btn-filter[data-category-period]').removeClass('active');
    $(this).addClass('active');
    updateCategoryChart($(this).data('category-period'));
  });

  // Search functionality
  $('#searchInput').on('input', function() {
    filterComplaints();
  });

  $('#statusFilter').on('change', function() {
    filterComplaints();
  });
}

function loadDashboardData() {
  // Show loading state
  $('#statsGrid').html('<div class="loading"><div class="spinner"></div>Memuat data...</div>');
  $('#complaintsTableBody').html('<tr><td colspan="8" class="loading"><div class="spinner"></div>Memuat data...</td></tr>');

  // Simulate AJAX call with timeout
  setTimeout(function() {
    loadStatistics();
    loadComplaints();
    initializeCharts();
  }, 1000);
}

function loadStatistics() {
  const stats = [
    {
      title: 'Keluhan Aktif',
      value: 18,
      change: '+3 dari kemarin',
      icon: 'mdi-alert-circle',
      type: 'danger',
      changeType: 'positive'
    },
    {
      title: 'Keluhan Solved',
      value: 25,
      change: 'Bulan ini',
      icon: 'mdi-check-circle',
      type: 'success',
      changeType: 'positive'
    },
    {
      title: 'Rata-rata Penyelesaian',
      value: '2.3 hari',
      change: '-0.5 hari dari bulan lalu',
      icon: 'mdi-clock-outline',
      type: 'warning',
      changeType: 'positive'
    },
    {
      title: 'Tingkat Kepuasan',
      value: '4.8/5',
      change: '+0.2 dari bulan lalu',
      icon: 'mdi-star',
      type: 'success',
      changeType: 'positive'
    }
  ];

  let statsHtml = '';
  stats.forEach(stat => {
    statsHtml += `
      <div class="stat-card ${stat.type}">
        <div class="stat-header">
          <div class="stat-icon ${stat.type}">
            <i class="mdi ${stat.icon}"></i>
          </div>
          <div class="stat-info">
            <h6>${stat.title}</h6>
          </div>
        </div>
        <div class="stat-value">${stat.value}</div>
        <div class="stat-change ${stat.changeType}">
          <i class="mdi ${stat.changeType === 'positive' ? 'mdi-trending-up' : 'mdi-trending-down'}"></i>
          ${stat.change}
        </div>
      </div>
    `;
  });

  $('#statsGrid').html(statsHtml);
}

function loadComplaints() {
  // Dummy data for complaints
  complaintsData = [
    {
      id: 'CMP-00123',
      date: '2024-03-12',
      customer: 'Budi Santoso',
      category: 'Pelayanan',
      priority: 'Tinggi',
      status: 'active',
      officer: 'Staff A',
      description: 'Pelayanan kurang memuaskan'
    },
    {
      id: 'CMP-00124',
      date: '2024-03-11',
      customer: 'Siti Aminah',
      category: 'Produk',
      priority: 'Sedang',
      status: 'solved',
      officer: 'Staff B',
      description: 'Produk tidak sesuai deskripsi'
    },
    {
      id: 'CMP-00125',
      date: '2024-03-10',
      customer: 'Ahmad Rahman',
      category: 'Proses',
      priority: 'Rendah',
      status: 'active',
      officer: 'Staff C',
      description: 'Proses pengiriman lambat'
    },
    {
      id: 'CMP-00126',
      date: '2024-03-09',
      customer: 'Dewi Lestari',
      category: 'Dokumen',
      priority: 'Tinggi',
      status: 'solved',
      officer: 'Staff A',
      description: 'Dokumen tidak lengkap'
    },
    {
      id: 'CMP-00127',
      date: '2024-03-08',
      customer: 'Rudi Hartono',
      category: 'Lainnya',
      priority: 'Sedang',
      status: 'pending',
      officer: 'Staff D',
      description: 'Pertanyaan umum'
    }
  ];

  renderComplaints(complaintsData);
}

function renderComplaints(data) {
  let html = '';
  
  data.forEach(complaint => {
    const statusBadge = getStatusBadge(complaint.status);
    const priorityBadge = getPriorityBadge(complaint.priority);
    
    html += `
      <tr>
        <td><strong>${complaint.id}</strong></td>
        <td>${formatDate(complaint.date)}</td>
        <td>${complaint.customer}</td>
        <td>${complaint.category}</td>
        <td>${priorityBadge}</td>
        <td>${statusBadge}</td>
        <td>${complaint.officer}</td>
        <td>
          <button class="btn btn-sm btn-outline-primary" onclick="viewComplaint('${complaint.id}')">
            <i class="mdi mdi-eye"></i>
          </button>
          <button class="btn btn-sm btn-outline-success" onclick="editComplaint('${complaint.id}')">
            <i class="mdi mdi-pencil"></i>
          </button>
        </td>
      </tr>
    `;
  });

  $('#complaintsTableBody').html(html || '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
}

function getStatusBadge(status) {
  const badges = {
    'active': '<span class="badge badge-warning">Aktif</span>',
    'solved': '<span class="badge badge-success">Solved</span>',
    'pending': '<span class="badge badge-info">Pending</span>'
  };
  return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
}

function getPriorityBadge(priority) {
  const badges = {
    'Tinggi': '<span class="badge badge-danger">Tinggi</span>',
    'Sedang': '<span class="badge badge-warning">Sedang</span>',
    'Rendah': '<span class="badge badge-success">Rendah</span>'
  };
  return badges[priority] || '<span class="badge badge-secondary">Unknown</span>';
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}

function switchTab(tab) {
  $('.tab-button').removeClass('active');
  $(`.tab-button`).filter(function() {
    return $(this).text().toLowerCase().includes(tab === 'all' ? 'semua' : tab);
  }).addClass('active');
  
  currentTab = tab;
  filterComplaints();
}

function filterComplaints() {
  let filteredData = [...complaintsData];
  
  // Filter by tab
  if (currentTab !== 'all') {
    filteredData = filteredData.filter(complaint => complaint.status === currentTab);
  }
  
  // Filter by status dropdown
  const statusFilter = $('#statusFilter').val();
  if (statusFilter) {
    filteredData = filteredData.filter(complaint => complaint.status === statusFilter);
  }
  
  // Filter by search
  const searchTerm = $('#searchInput').val().toLowerCase();
  if (searchTerm) {
    filteredData = filteredData.filter(complaint => 
      complaint.id.toLowerCase().includes(searchTerm) ||
      complaint.customer.toLowerCase().includes(searchTerm) ||
      complaint.category.toLowerCase().includes(searchTerm)
    );
  }
  
  renderComplaints(filteredData);
}

function initializeCharts() {
  // Resolution Chart
  const resolutionCtx = document.getElementById('resolutionChart').getContext('2d');
  resolutionChart = new Chart(resolutionCtx, {
    type: 'bar',
    data: {
      labels: ['<24 jam', '1-3 hari', '4-7 hari', '>7 hari'],
      datasets: [{
        label: 'Jumlah Keluhan',
        data: [15, 8, 2, 1],
        backgroundColor: [
          'rgba(102, 126, 234, 0.8)',
          'rgba(118, 75, 162, 0.8)',
          'rgba(237, 137, 54, 0.8)',
          'rgba(245, 101, 101, 0.8)'
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
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0,0,0,0.05)'
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });

  // Category Chart
  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: ['Pelayanan', 'Produk', 'Proses', 'Dokumen', 'Lainnya'],
      datasets: [{
        data: [45, 25, 15, 10, 5],
        backgroundColor: [
          'rgba(245, 101, 101, 0.8)',
          'rgba(66, 153, 225, 0.8)',
          'rgba(72, 187, 120, 0.8)',
          'rgba(255, 217, 61, 0.8)',
          'rgba(203, 213, 225, 0.8)'
        ],
        borderWidth: 0,
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
          }
        }
      }
    }
  });
}

function updateResolutionChart(period) {
  // Simulate different data for different periods
  const data = {
    week: [12, 6, 1, 0],
    month: [15, 8, 2, 1],
    year: [180, 95, 24, 12]
  };
  
  resolutionChart.data.datasets[0].data = data[period];
  resolutionChart.update();
}

function updateCategoryChart(period) {
  // Simulate different data for different periods
  const data = {
    month: [45, 25, 15, 10, 5],
    quarter: [135, 75, 45, 30, 15],
    year: [540, 300, 180, 120, 60]
  };
  
  categoryChart.data.datasets[0].data = data[period];
  categoryChart.update();
}

// Action functions
function viewComplaint(id) {
  alert(`Melihat detail keluhan: ${id}`);
}

function editComplaint(id) {
  alert(`Edit keluhan: ${id}`);
}

function showAddComplaintModal() {
  alert('Modal tambah keluhan akan ditampilkan');
}
</script>
@endsection