@extends('layouts.master')
@section('title', 'Sales Dashboard')
@section('pageStyle')
  <style>
    :root {
      --primary-color: #2563eb;
      --secondary-color: #06b6d4;
      --success-color: #059669;
      --warning-color: #d97706;
      --danger-color: #dc2626;
      --info-color: #7c3aed;
      --dark-color: #1f2937;
      --light-color: #f8fafc;
      --white: #ffffff;
      --border-color: #e5e7eb;
      --text-muted: #6b7280;
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      --gradient-success: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
      --gradient-warning: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
      --gradient-danger: linear-gradient(135deg, var(--danger-color) 0%, #ef4444 100%);
      --gradient-info: linear-gradient(135deg, var(--info-color) 0%, #8b5cf6 100%);
    }

    * {
      box-sizing: border-box;
    }

    body {
      background-color: #f9fafb;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--dark-color);
      line-height: 1.6;
    }

    .dashboard-container {
      padding: 1.5rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    .dashboard-header {
      background: var(--white);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-color);
      position: relative;
      overflow: hidden;
    }

    .dashboard-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: var(--gradient-primary);
    }

    .dashboard-title {
      font-size: 2.25rem;
      font-weight: 900;
      color: var(--dark-color);
      margin: 0 0 0.5rem 0;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .dashboard-subtitle {
      color: var(--text-muted);
      font-size: 1.1rem;
      margin: 0;
      font-weight: 500;
    }

    .sync-indicator {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1.25rem;
      background: rgba(34, 197, 94, 0.1);
      border-radius: 12px;
      color: var(--success-color);
      font-size: 0.9rem;
      font-weight: 600;
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stats-card {
      background: var(--white);
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-lg);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }

    .stats-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-xl);
    }

    .stats-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: var(--gradient-primary);
    }

    .stats-card.success::before {
      background: var(--gradient-success);
    }

    .stats-card.warning::before {
      background: var(--gradient-warning);
    }

    .stats-card.danger::before {
      background: var(--gradient-danger);
    }

    .stats-card.info::before {
      background: var(--gradient-info);
    }

    .stats-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .stats-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      background: var(--gradient-primary);
      color: var(--white);
      box-shadow: var(--shadow);
    }

    .stats-icon.success {
      background: var(--gradient-success);
    }

    .stats-icon.warning {
      background: var(--gradient-warning);
    }

    .stats-icon.danger {
      background: var(--gradient-danger);
    }

    .stats-icon.info {
      background: var(--gradient-info);
    }

    .stats-value {
      font-size: 3rem;
      font-weight: 900;
      color: var(--dark-color);
      margin: 1rem 0 0.5rem 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      line-height: 1;
    }

    .stats-label {
      font-size: 1rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stats-trend {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 0.95rem;
      font-weight: 700;
      padding: 0.75rem 1rem;
      border-radius: 12px;
    }

    .trend-positive {
      background: rgba(34, 197, 94, 0.1);
      color: var(--success-color);
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .trend-negative {
      background: rgba(220, 38, 38, 0.1);
      color: var(--danger-color);
      border: 1px solid rgba(220, 38, 38, 0.2);
    }

    .trend-neutral {
      background: rgba(107, 114, 128, 0.1);
      color: var(--text-muted);
      border: 1px solid rgba(107, 114, 128, 0.2);
    }

    .progress-container {
      margin-top: 1.5rem;
    }

    .progress-label {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
    }

    .progress-bar-container {
      background: #f3f4f6;
      height: 12px;
      border-radius: 6px;
      overflow: hidden;
      position: relative;
    }

    .progress-bar-fill {
      height: 100%;
      border-radius: 6px;
      transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .progress-bar-fill.primary {
      background: var(--gradient-primary);
    }

    .progress-bar-fill.success {
      background: var(--gradient-success);
    }

    .progress-bar-fill.warning {
      background: var(--gradient-warning);
    }

    .progress-bar-fill.danger {
      background: var(--gradient-danger);
    }

    .chart-container {
      background: var(--white);
      border-radius: 20px;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-lg);
      margin-bottom: 2rem;
      overflow: hidden;
    }

    .chart-header {
      padding: 2rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    }

    .chart-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--dark-color);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .chart-actions {
      display: flex;
      gap: 0.75rem;
      align-items: center;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.9rem;
      border: 1px solid transparent;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn-primary {
      background: var(--gradient-primary);
      color: var(--white);
      box-shadow: var(--shadow);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-outline {
      background: var(--white);
      border-color: var(--border-color);
      color: var(--dark-color);
    }

    .btn-outline:hover {
      background: var(--light-color);
      transform: translateY(-1px);
      box-shadow: var(--shadow);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.85rem;
    }

    .chart-body {
      padding: 2rem;
      height: 450px;
    }

    .table-container {
      background: var(--white);
      border-radius: 20px;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }

    .table-header {
      padding: 2rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    }

    .table-responsive {
      overflow-x: auto;
    }

    .table {
      width: 100%;
      margin: 0;
      border-collapse: collapse;
    }

    .table th {
      background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
      padding: 1.5rem;
      text-align: left;
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--dark-color);
      border-bottom: 2px solid var(--border-color);
      position: sticky;
      top: 0;
      z-index: 10;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table td {
      padding: 1.5rem;
      border-bottom: 1px solid #f3f4f6;
      vertical-align: middle;
    }

    .table tbody tr {
      transition: all 0.2s;
    }

    .table tbody tr:hover {
      background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
      transform: scale(1.01);
      box-shadow: inset 0 0 0 1px var(--border-color);
    }

    .user-avatar {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      color: var(--white);
      font-size: 1rem;
      box-shadow: var(--shadow);
    }

    .badge {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-success {
      background: rgba(34, 197, 94, 0.1);
      color: var(--success-color);
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .badge-warning {
      background: rgba(217, 119, 6, 0.1);
      color: var(--warning-color);
      border: 1px solid rgba(217, 119, 6, 0.2);
    }

    .badge-info {
      background: rgba(124, 58, 237, 0.1);
      color: var(--info-color);
      border: 1px solid rgba(124, 58, 237, 0.2);
    }

    .badge-danger {
      background: rgba(220, 38, 38, 0.1);
      color: var(--danger-color);
      border: 1px solid rgba(220, 38, 38, 0.2);
    }

    .badge-primary {
      background: rgba(37, 99, 235, 0.1);
      color: var(--primary-color);
      border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .loading-skeleton {
      background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
      background-size: 200% 100%;
      animation: loading 2s infinite;
      border-radius: 8px;
      height: 1.5rem;
    }

    @keyframes loading {
      0% {
        background-position: 200% 0;
      }
      100% {
        background-position: -200% 0;
      }
    }

    .filter-tabs {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }

    .filter-tab {
      padding: 0.75rem 1.5rem;
      border: 2px solid var(--border-color);
      background: var(--white);
      border-radius: 12px;
      cursor: pointer;
      font-size: 0.9rem;
      font-weight: 700;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .filter-tab.active {
      background: var(--primary-color);
      color: var(--white);
      border-color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .search-box {
      position: relative;
      max-width: 350px;
    }

    .search-input {
      width: 100%;
      padding: 1rem 1.25rem 1rem 3rem;
      border: 2px solid var(--border-color);
      border-radius: 12px;
      font-size: 0.9rem;
      background: var(--white);
      font-weight: 500;
      transition: all 0.3s;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
      transform: translateY(-1px);
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 1.25rem;
    }

    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
      margin-top: 2.5rem;
    }

    .quick-action-card {
      background: var(--white);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: var(--shadow);
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .quick-action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--gradient-primary);
    }

    .quick-action-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-xl);
      border-color: var(--primary-color);
    }

    .notification-dot {
      position: relative;
    }

    .notification-dot::after {
      content: '';
      position: absolute;
      top: -4px;
      right: -4px;
      width: 12px;
      height: 12px;
      background: var(--danger-color);
      border-radius: 50%;
      border: 3px solid var(--white);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .pulse-animation {
      animation: pulse 2s infinite;
    }

    @media (max-width: 768px) {
      .dashboard-container {
        padding: 1rem;
      }

      .dashboard-title {
        font-size: 1.75rem;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .chart-body {
        height: 350px;
        padding: 1rem;
      }

      .chart-header,
      .table-header {
        flex-direction: column;
        align-items: stretch;
      }

      .chart-actions {
        justify-content: center;
      }

      .quick-actions {
        grid-template-columns: 1fr;
      }
    }

    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .slide-in-left {
      opacity: 0;
      transform: translateX(-30px);
      animation: slideInLeft 0.8s ease forwards;
    }

    @keyframes slideInLeft {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .zoom-in {
      opacity: 0;
      transform: scale(0.8);
      animation: zoomIn 0.8s ease forwards;
    }

    @keyframes zoomIn {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .performance-indicator {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 600;
    }

    .performance-excellent {
      background: rgba(34, 197, 94, 0.1);
      color: var(--success-color);
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .performance-good {
      background: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
      border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .performance-average {
      background: rgba(217, 119, 6, 0.1);
      color: var(--warning-color);
      border: 1px solid rgba(217, 119, 6, 0.2);
    }

    .performance-poor {
      background: rgba(220, 38, 38, 0.1);
      color: var(--danger-color);
      border: 1px solid rgba(220, 38, 38, 0.2);
    }
  </style>
@endsection

@section('content')
<div class="dashboard-container">
  <!-- Dashboard Header -->
  <!-- <div class="dashboard-header fade-in">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="dashboard-title">
          📊 Sales Performance Center
        </h1>
        <p class="dashboard-subtitle">
          Comprehensive sales analytics and lead management dashboard
        </p>
      </div>
      <div class="sync-indicator" id="syncIndicator">
        <i class="mdi mdi-sync mdi-spin"></i>
        <span>Synchronizing data...</span>
      </div>
    </div>
  </div> -->

  <!-- Quick Actions -->
  <div class="quick-actions slide-in-left">
    <div class="quick-action-card" onclick="quickAction('newLead')">
      <i class="mdi mdi-account-plus" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
      <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Tambah Lead Baru</div>
      <div style="font-size: 0.9rem; color: var(--text-muted);">Buat prospek pelanggan baru</div>
    </div>
    <div class="quick-action-card notification-dot" onclick="quickAction('pendingTasks')">
      <i class="mdi mdi-bell-alert" style="font-size: 2.5rem; color: var(--warning-color); margin-bottom: 1rem;"></i>
      <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Task Menunggu</div>
      <div style="font-size: 0.9rem; color: var(--text-muted);">15 item butuh perhatian</div>
    </div>
    <div class="quick-action-card" onclick="quickAction('report')">
      <i class="mdi mdi-chart-box-outline" style="font-size: 2.5rem; color: var(--success-color); margin-bottom: 1rem;"></i>
      <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Laporan Sales</div>
      <div style="font-size: 0.9rem; color: var(--text-muted);">Export data analitik</div>
    </div>
    <div class="quick-action-card" onclick="quickAction('schedule')">
      <i class="mdi mdi-calendar-plus" style="font-size: 2.5rem; color: var(--info-color); margin-bottom: 1rem;"></i>
      <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Jadwal Meeting</div>
      <div style="font-size: 0.9rem; color: var(--text-muted);">Buat appointment baru</div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid zoom-in" id="statsGrid">
    <!-- Target vs Realisasi -->
    <div class="stats-card">
      <div class="stats-header">
        <div class="stats-icon">
          <i class="mdi mdi-target"></i>
        </div>
      </div>
      <div class="stats-value" id="targetRealization">
        <span class="loading-skeleton" style="width: 80px;"></span>
      </div>
      <div class="stats-label">Target vs Realisasi</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Progress</span>
          <span id="targetPercentage">0%</span>
        </div>
        <div class="progress-bar-container">
          <div class="progress-bar-fill primary" id="targetProgress" style="width: 0%"></div>
        </div>
      </div>
      <div class="stats-trend trend-positive" id="targetTrend">
        <span class="loading-skeleton" style="width: 140px;"></span>
      </div>
    </div>

    <!-- New Leads -->
    <div class="stats-card success">
      <div class="stats-header">
        <div class="stats-icon success">
          <i class="mdi mdi-account-multiple-plus"></i>
        </div>
      </div>
      <div class="stats-value" id="newLeads">
        <span class="loading-skeleton" style="width: 60px;"></span>
      </div>
      <div class="stats-label">New Leads</div>
      <div class="stats-trend trend-positive" id="newLeadsTrend">
        <span class="loading-skeleton" style="width: 120px;"></span>
      </div>
    </div>

    <!-- Belum Ada Aktivitas -->
    <div class="stats-card warning">
      <div class="stats-header">
        <div class="stats-icon warning">
          <i class="mdi mdi-account-clock"></i>
        </div>
      </div>
      <div class="stats-value" id="noActivity">
        <span class="loading-skeleton" style="width: 50px;"></span>
      </div>
      <div class="stats-label">Belum Ada Aktivitas</div>
      <div class="stats-trend trend-negative" id="noActivityTrend">
        <span class="loading-skeleton" style="width: 100px;"></span>
      </div>
    </div>

    <!-- Belum Penawaran -->
    <div class="stats-card info">
      <div class="stats-header">
        <div class="stats-icon info">
          <i class="mdi mdi-file-document-alert"></i>
        </div>
      </div>
      <div class="stats-value" id="noQuotation">
        <span class="loading-skeleton" style="width: 50px;"></span>
      </div>
      <div class="stats-label">Belum Penawaran</div>
      <div class="stats-trend trend-neutral" id="noQuotationTrend">
        <span class="loading-skeleton" style="width: 110px;"></span>
      </div>
    </div>

    <!-- Effective Call Rate -->
    <div class="stats-card danger">
      <div class="stats-header">
        <div class="stats-icon danger">
          <i class="mdi mdi-phone-check"></i>
        </div>
      </div>
      <div class="stats-value" id="callRate">
        <span class="loading-skeleton" style="width: 70px;"></span>
      </div>
      <div class="stats-label">Effective Call Rate</div>
      <div class="progress-container">
        <div class="progress-label">
          <span>Tingkat Keberhasilan</span>
          <span id="callPercentage">0%</span>
        </div>
        <div class="progress-bar-container">
          <div class="progress-bar-fill success" id="callProgress" style="width: 0%"></div>
        </div>
      </div>
      <div class="stats-trend trend-positive" id="callTrend">
        <span class="loading-skeleton" style="width: 130px;"></span>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row fade-in">
    <div class="col-xl-8 col-12 mb-4">
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            📈 Tren Performa Sales
          </h3>
          <div class="chart-actions">
            <div class="filter-tabs">
              <div class="filter-tab active" onclick="changeChartPeriod('week')">Minggu</div>
              <div class="filter-tab" onclick="changeChartPeriod('month')">Bulan</div>
              <div class="filter-tab" onclick="changeChartPeriod('quarter')">Kuartal</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="exportChart()">
              <i class="mdi mdi-download"></i> Export
            </button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="performanceChart"></canvas>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-12 mb-4">
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            🎯 Distribusi Status Lead
          </h3>
          <button class="btn btn-outline btn-sm" onclick="refreshLeadsChart()">
            <i class="mdi mdi-refresh"></i> Refresh
          </button>
        </div>
        <div class="chart-body">
          <canvas id="leadsChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Performance Metrics Row -->
  <div class="row fade-in">
    <div class="col-12 mb-4">
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            📊 Metrik Performa Harian
          </h3>
          <div class="chart-actions">
            <button class="btn btn-primary btn-sm" onclick="updateMetrics()">
              <i class="mdi mdi-refresh"></i> Update Data
            </button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="metricsChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Leads Management Table -->
  <div class="table-container fade-in">
    <div class="table-header">
      <h3 class="chart-title">
        🗂️ Manajemen Lead Aktif
      </h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="search-box">
          <i class="mdi mdi-magnify search-icon"></i>
          <input type="text" class="search-input" placeholder="Cari leads..." id="leadsSearch">
        </div>
        <select class="btn btn-outline btn-sm" id="statusFilter" onchange="filterByStatus()">
          <option value="all">Semua Status</option>
          <option value="new">Lead Baru</option>
          <option value="contacted">Sudah Dihubungi</option>
          <option value="qualified">Qualified</option>
          <option value="proposal">Proposal Sent</option>
          <option value="negotiation">Negosiasi</option>
          <option value="closed_won">Deal Closed</option>
          <option value="closed_lost">Deal Lost</option>
        </select>
        <button class="btn btn-primary btn-sm" onclick="quickAction('newLead')">
          <i class="mdi mdi-plus"></i> Tambah Lead
        </button>
        <button class="btn btn-outline btn-sm" onclick="exportLeads()">
          <i class="mdi mdi-export"></i> Export
        </button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table" id="leadsTable">
        <thead>
          <tr>
            <th><i class="mdi mdi-account"></i> Lead Info</th>
            <th><i class="mdi mdi-calendar"></i> Tanggal</th>
            <th><i class="mdi mdi-flag"></i> Status</th>
            <th><i class="mdi mdi-phone"></i> Kontak</th>
            <th><i class="mdi mdi-chart-line"></i> Progress</th>
            <th><i class="mdi mdi-currency-usd"></i> Estimasi</th>
            <th><i class="mdi mdi-cog"></i> Aksi</th>
          </tr>
        </thead>
        <tbody id="leadsTableBody">
          <!-- Loading skeleton -->
          <tr>
            <td colspan="7">
              <div class="loading-skeleton" style="height: 2.5rem; margin: 1rem 0;"></div>
            </td>
          </tr>
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
  let performanceChart = null;
  let leadsChart = null;
  let metricsChart = null;
  let currentPeriod = 'week';
  let leadsData = [];
  let originalLeadsData = [];

  // Dummy data untuk dashboard
  const salesMetrics = {
    targetRealization: {
      target: 500000000, // 500 juta
      actual: 435000000, // 435 juta
      percentage: 87,
      trend: '+12% dari bulan lalu'
    },
    newLeads: {
      count: 42,
      trend: '+8 dari kemarin',
      weekly: 156,
      monthly: 623
    },
    noActivity: {
      count: 18,
      trend: 'Perlu tindak lanjut segera',
      urgent: 8,
      aging: '3+ hari'
    },
    noQuotation: {
      count: 25,
      trend: 'Siap untuk penawaran',
      qualified: 15,
      potential: '2.3M'
    },
    callRate: {
      percentage: 74,
      effective: 148,
      total: 200,
      trend: '+5% minggu ini'
    }
  };

  const dummyLeads = [
    {
      id: 1,
      name: 'PT. Teknologi Maju',
      contact: 'Budi Santoso',
      email: 'budi@teknologimaju.com',
      phone: '+62812-3456-7890',
      dateAdded: '2024-03-12',
      status: 'new',
      progress: 15,
      estimate: 150000000,
      lastContact: null,
      source: 'Website',
      avatar: 'TM',
      avatarColor: '#2563eb'
    },
    {
      id: 2,
      name: 'CV. Jaya Abadi',
      contact: 'Siti Rahayu',
      email: 'siti@jayaabadi.com',
      phone: '+62813-9876-5432',
      dateAdded: '2024-03-11',
      status: 'proposal',
      progress: 75,
      estimate: 250000000,
      lastContact: '2024-03-14 14:30',
      source: 'Referral',
      avatar: 'JA',
      avatarColor: '#059669'
    },
    {
      id: 3,
      name: 'PT. Solusi Digital',
      contact: 'Ahmad Wijaya',
      email: 'ahmad@solusdigital.com',
      phone: '+62814-5678-9012',
      dateAdded: '2024-03-10',
      status: 'negotiation',
      progress: 85,
      estimate: 320000000,
      lastContact: '2024-03-14 10:00',
      source: 'LinkedIn',
      avatar: 'SD',
      avatarColor: '#7c3aed'
    },
    {
      id: 4,
      name: 'UD. Berkah Mandiri',
      contact: 'Dewi Kartika',
      email: 'dewi@berkahmandiri.com',
      phone: '+62815-2468-1357',
      dateAdded: '2024-03-09',
      status: 'qualified',
      progress: 45,
      estimate: 80000000,
      lastContact: '2024-03-13 16:20',
      source: 'Cold Call',
      avatar: 'BM',
      avatarColor: '#d97706'
    },
    {
      id: 5,
      name: 'PT. Inovasi Kreatif',
      contact: 'Rizky Pratama',
      email: 'rizky@inovasikreatif.com',
      phone: '+62816-1357-2468',
      dateAdded: '2024-03-08',
      status: 'closed_won',
      progress: 100,
      estimate: 180000000,
      lastContact: '2024-03-12 09:15',
      source: 'Website',
      avatar: 'IK',
      avatarColor: '#dc2626'
    },
    {
      id: 6,
      name: 'CV. Mitra Sejahtera',
      contact: 'Linda Sari',
      email: 'linda@mitrasejahtera.com',
      phone: '+62817-8642-9753',
      dateAdded: '2024-03-07',
      status: 'contacted',
      progress: 30,
      estimate: 95000000,
      lastContact: '2024-03-11 11:45',
      source: 'Referral',
      avatar: 'MS',
      avatarColor: '#06b6d4'
    },
    {
      id: 7,
      name: 'PT. Global Vision',
      contact: 'Andi Kusuma',
      email: 'andi@globalvision.com',
      phone: '+62818-7531-8642',
      dateAdded: '2024-03-06',
      status: 'closed_lost',
      progress: 60,
      estimate: 120000000,
      lastContact: '2024-03-10 15:30',
      source: 'Trade Show',
      avatar: 'GV',
      avatarColor: '#64748b'
    }
  ];

  // Initialize dashboard when page loads
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(loadSalesMetrics, 1000);
    setTimeout(initCharts, 1500);
    setTimeout(loadLeadsData, 2000);
    
    // Add search functionality
    document.getElementById('leadsSearch').addEventListener('input', debounce(filterLeads, 300));
    
    // Add animation delays
    animateElements();
    
    // Auto refresh every 60 seconds
    setInterval(autoRefresh, 60000);
  });

  // Animate elements with staggered delays
  function animateElements() {
    const fadeElements = document.querySelectorAll('.fade-in');
    const slideElements = document.querySelectorAll('.slide-in-left');
    const zoomElements = document.querySelectorAll('.zoom-in');
    
    fadeElements.forEach((el, index) => {
      el.style.animationDelay = `${index * 0.1}s`;
    });
    
    slideElements.forEach((el, index) => {
      el.style.animationDelay = `${index * 0.15}s`;
    });
    
    zoomElements.forEach((el, index) => {
      el.style.animationDelay = `${index * 0.2}s`;
    });
  }

  // Load sales metrics with smooth animations
  function loadSalesMetrics() {
    const metrics = salesMetrics;
    
    // Target vs Realisasi
    animateValue('targetRealization', 0, metrics.targetRealization.percentage, 2000, (val) => `${val}%`);
    setTimeout(() => {
      document.getElementById('targetPercentage').textContent = `${metrics.targetRealization.percentage}%`;
      document.getElementById('targetProgress').style.width = `${metrics.targetRealization.percentage}%`;
      document.getElementById('targetTrend').innerHTML = `
        <i class="mdi mdi-trending-up"></i>
        ${metrics.targetRealization.trend}
      `;
    }, 500);

    // New Leads
    animateValue('newLeads', 0, metrics.newLeads.count, 1500);
    setTimeout(() => {
      document.getElementById('newLeadsTrend').innerHTML = `
        <i class="mdi mdi-arrow-up"></i>
        ${metrics.newLeads.trend}
      `;
    }, 800);

    // Belum Ada Aktivitas
    animateValue('noActivity', 0, metrics.noActivity.count, 1200);
    setTimeout(() => {
      document.getElementById('noActivityTrend').innerHTML = `
        <i class="mdi mdi-alert-circle"></i>
        ${metrics.noActivity.trend}
      `;
    }, 600);

    // Belum Penawaran
    animateValue('noQuotation', 0, metrics.noQuotation.count, 1800);
    setTimeout(() => {
      document.getElementById('noQuotationTrend').innerHTML = `
        <i class="mdi mdi-file-document-plus"></i>
        ${metrics.noQuotation.trend}
      `;
    }, 900);

    // Effective Call Rate
    animateValue('callRate', 0, metrics.callRate.percentage, 2200, (val) => `${val}%`);
    setTimeout(() => {
      document.getElementById('callPercentage').textContent = `${metrics.callRate.percentage}%`;
      document.getElementById('callProgress').style.width = `${metrics.callRate.percentage}%`;
      document.getElementById('callTrend').innerHTML = `
        <i class="mdi mdi-phone-check"></i>
        ${metrics.callRate.trend}
      `;
    }, 1100);

    // Update sync indicator
    setTimeout(() => {
      document.getElementById('syncIndicator').innerHTML = `
        <i class="mdi mdi-check-circle"></i>
        <span>Data terbaru</span>
      `;
      setTimeout(() => {
        document.getElementById('syncIndicator').style.opacity = '0.7';
      }, 3000);
    }, 1500);
  }

  // Animate number values
  function animateValue(elementId, start, end, duration, formatter = (val) => val) {
    const element = document.getElementById(elementId);
    const startTime = performance.now();
    
    function updateValue(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = easeOutCubic(progress);
      const currentValue = Math.round(start + (end - start) * easedProgress);
      
      element.textContent = formatter(currentValue);
      
      if (progress < 1) {
        requestAnimationFrame(updateValue);
      }
    }
    
    requestAnimationFrame(updateValue);
  }

  // Easing function
  function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
  }

  // Initialize all charts
  function initCharts() {
    initPerformanceChart();
    initLeadsChart();
    initMetricsChart();
  }

  // Performance trend chart
  function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const chartData = {
      week: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        target: [50, 50, 50, 50, 50, 35, 25],
        actual: [42, 58, 45, 65, 52, 28, 18],
        calls: [85, 92, 78, 88, 95, 45, 32]
      },
      month: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        target: [200, 200, 200, 200],
        actual: [185, 220, 195, 235],
        calls: [350, 385, 320, 410]
      },
      quarter: {
        labels: ['Januari', 'Februari', 'Maret'],
        target: [800, 800, 800],
        actual: [756, 845, 723],
        calls: [1450, 1580, 1320]
      }
    };

    performanceChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartData[currentPeriod].labels,
        datasets: [
          {
            label: 'Target',
            data: chartData[currentPeriod].target,
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#f59e0b',
            pointBorderColor: '#fff',
            pointBorderWidth: 3,
            pointRadius: 8,
            pointHoverRadius: 10,
            fill: true,
            tension: 0.4
          },
          {
            label: 'Realisasi',
            data: chartData[currentPeriod].actual,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderWidth: 3,
            pointBackgroundColor: '#2563eb',
            pointBorderColor: '#fff',
            pointBorderWidth: 3,
            pointRadius: 8,
            pointHoverRadius: 10,
            fill: true,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 25,
              color: '#1f2937',
              font: {
                family: 'Inter',
                weight: '700',
                size: 14
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.9)',
            titleColor: 'white',
            bodyColor: 'white',
            cornerRadius: 12,
            displayColors: true,
            mode: 'index',
            intersect: false,
            padding: 15,
            titleFont: {
              family: 'Inter',
              weight: '700'
            },
            bodyFont: {
              family: 'Inter',
              weight: '600'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: '#f3f4f6',
              drawBorder: false
            },
            ticks: {
              color: '#6b7280',
              font: {
                family: 'Inter',
                weight: '600'
              },
              padding: 10
            },
            title: {
              display: true,
              text: 'Jumlah Deal',
              color: '#1f2937',
              font: {
                family: 'Inter',
                weight: '700',
                size: 14
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#6b7280',
              font: {
                family: 'Inter',
                weight: '600'
              },
              padding: 10
            }
          }
        },
        interaction: {
          mode: 'index',
          intersect: false
        },
        elements: {
          point: {
            hoverBorderWidth: 4
          }
        }
      }
    });
  }

  // Leads distribution donut chart
  function initLeadsChart() {
    const ctx = document.getElementById('leadsChart').getContext('2d');
    
    leadsChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Lead Baru', 'Contacted', 'Qualified', 'Proposal', 'Negosiasi', 'Deal Won', 'Deal Lost'],
        datasets: [{
          label: 'Distribusi Lead',
          data: [42, 28, 18, 15, 8, 12, 5],
          backgroundColor: [
            '#2563eb',
            '#06b6d4', 
            '#059669',
            '#d97706',
            '#7c3aed',
            '#10b981',
            '#dc2626'
          ],
          borderWidth: 0,
          cutout: '65%',
          hoverOffset: 12
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
              color: '#1f2937',
              font: {
                family: 'Inter',
                weight: '600',
                size: 12
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.9)',
            titleColor: 'white',
            bodyColor: 'white',
            cornerRadius: 12,
            padding: 15,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.raw / total) * 100).toFixed(1);
                return `${context.label}: ${context.raw} leads (${percentage}%)`;
              }
            }
          }
        }
      }
    });
  }

  // Daily metrics chart
  function initMetricsChart() {
    const ctx = document.getElementById('metricsChart').getContext('2d');
    
    metricsChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [
          {
            label: 'Calls Made',
            data: [85, 92, 78, 88, 95, 45, 32],
            backgroundColor: 'rgba(37, 99, 235, 0.8)',
            borderColor: '#2563eb',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
          },
          {
            label: 'Effective Calls',
            data: [62, 68, 58, 65, 70, 32, 24],
            backgroundColor: 'rgba(5, 150, 105, 0.8)',
            borderColor: '#059669',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
          },
          {
            label: 'New Leads',
            data: [12, 15, 8, 18, 22, 8, 5],
            backgroundColor: 'rgba(217, 119, 6, 0.8)',
            borderColor: '#d97706',
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
            labels: {
              usePointStyle: true,
              padding: 25,
              color: '#1f2937',
              font: {
                family: 'Inter',
                weight: '700',
                size: 14
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.9)',
            titleColor: 'white',
            bodyColor: 'white',
            cornerRadius: 12,
            padding: 15,
            mode: 'index',
            intersect: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: '#f3f4f6',
              drawBorder: false
            },
            ticks: {
              color: '#6b7280',
              font: {
                family: 'Inter',
                weight: '600'
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#6b7280',
              font: {
                family: 'Inter',
                weight: '600'
              }
            }
          }
        }
      }
    });
  }

  // Load leads data
  function loadLeadsData() {
    originalLeadsData = [...dummyLeads];
    leadsData = [...dummyLeads];
    renderLeadsTable(leadsData);
  }

  // Render leads table with enhanced styling
  function renderLeadsTable(leads) {
    const tbody = document.getElementById('leadsTableBody');
    
    const statusConfig = {
      new: { badge: 'badge-primary', icon: 'mdi-account-plus', text: 'Lead Baru' },
      contacted: { badge: 'badge-info', icon: 'mdi-phone-check', text: 'Dihubungi' },
      qualified: { badge: 'badge-success', icon: 'mdi-account-check', text: 'Qualified' },
      proposal: { badge: 'badge-warning', icon: 'mdi-file-document', text: 'Proposal' },
      negotiation: { badge: 'badge-info', icon: 'mdi-handshake', text: 'Negosiasi' },
      closed_won: { badge: 'badge-success', icon: 'mdi-check-circle', text: 'Deal Won' },
      closed_lost: { badge: 'badge-danger', icon: 'mdi-close-circle', text: 'Deal Lost' }
    };

    if (leads.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <i class="mdi mdi-database-search" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
            Tidak ada data lead yang ditemukan
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = leads.map(lead => {
      const status = statusConfig[lead.status];
      const daysSince = Math.floor((new Date() - new Date(lead.dateAdded)) / (1000 * 60 * 60 * 24));
      const performanceClass = getPerformanceClass(lead.progress);
      
      return `
        <tr data-lead-id="${lead.id}">
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="user-avatar" style="background-color: ${lead.avatarColor};">
                ${lead.avatar}
              </div>
              <div>
                <div style="font-weight: 700; margin-bottom: 4px; font-size: 1rem;">${lead.name}</div>
                <div style="font-weight: 600; color: var(--text-muted); margin-bottom: 2px;">${lead.contact}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">${lead.email}</div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-weight: 600; margin-bottom: 4px;">${formatDate(lead.dateAdded)}</div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">${daysSince} hari lalu</div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">via ${lead.source}</div>
          </td>
          <td>
            <span class="badge ${status.badge}">
              <i class="mdi ${status.icon}"></i>
              ${status.text}
            </span>
          </td>
          <td>
            <div style="font-weight: 600; margin-bottom: 4px;">${lead.phone}</div>
            ${lead.lastContact ? `
              <div style="font-size: 0.85rem; color: var(--success-color);">
                <i class="mdi mdi-phone-check" style="margin-right: 4px;"></i>
                ${formatDateTime(lead.lastContact)}
              </div>
            ` : `
              <div style="font-size: 0.85rem; color: var(--danger-color);">
                <i class="mdi mdi-phone-off" style="margin-right: 4px;"></i>
                Belum dihubungi
              </div>
            `}
          </td>
          <td>
            <div class="progress-container">
              <div class="progress-label">
                <span class="performance-indicator ${performanceClass}">
                  ${getPerformanceIcon(lead.progress)}
                  ${lead.progress}%
                </span>
              </div>
              <div class="progress-bar-container" style="margin-top: 8px;">
                <div class="progress-bar-fill ${getProgressColor(lead.progress)}" style="width: ${lead.progress}%"></div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-weight: 700; color: var(--success-color); margin-bottom: 4px;">
              ${formatCurrency(lead.estimate)}
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
              Estimasi nilai
            </div>
          </td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-outline btn-sm" onclick="viewLead(${lead.id})" title="Lihat Detail">
                <i class="mdi mdi-eye"></i>
              </button>
              <button class="btn btn-outline btn-sm" onclick="callLead(${lead.id})" title="Telepon">
                <i class="mdi mdi-phone"></i>
              </button>
              <button class="btn btn-outline btn-sm" onclick="editLead(${lead.id})" title="Edit">
                <i class="mdi mdi-pencil"></i>
              </button>
              <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-outline btn-sm" onclick="toggleDropdown(${lead.id})" title="Lebih">
                  <i class="mdi mdi-dots-vertical"></i>
                </button>
                <div class="dropdown-menu" id="dropdown-${lead.id}" style="display: none;">
                  <a onclick="updateStatus(${lead.id})" class="dropdown-item">
                    <i class="mdi mdi-flag"></i> Update Status
                  </a>
                  <a onclick="scheduleFollowup(${lead.id})" class="dropdown-item">
                    <i class="mdi mdi-calendar-plus"></i> Jadwalkan Follow-up
                  </a>
                  <a onclick="sendEmail(${lead.id})" class="dropdown-item">
                    <i class="mdi mdi-email"></i> Kirim Email
                  </a>
                  <a onclick="deleteLead(${lead.id})" class="dropdown-item text-danger">
                    <i class="mdi mdi-delete"></i> Hapus Lead
                  </a>
                </div>
              </div>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Helper functions
  function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }

  function formatDateTime(dateTimeString) {
    return new Date(dateTimeString).toLocaleString('id-ID', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount);
  }

  function getPerformanceClass(progress) {
    if (progress >= 80) return 'performance-excellent';
    if (progress >= 60) return 'performance-good';
    if (progress >= 40) return 'performance-average';
    return 'performance-poor';
  }

  function getPerformanceIcon(progress) {
    if (progress >= 80) return '🔥';
    if (progress >= 60) return '⭐';
    if (progress >= 40) return '📈';
    return '⚠️';
  }

  function getProgressColor(progress) {
    if (progress >= 80) return 'success';
    if (progress >= 60) return 'primary';
    if (progress >= 40) return 'warning';
    return 'danger';
  }

  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Search and filter functions
  function filterLeads() {
    const searchTerm = document.getElementById('leadsSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    let filteredLeads = originalLeadsData.filter(lead => {
      const matchesSearch = lead.name.toLowerCase().includes(searchTerm) ||
                          lead.contact.toLowerCase().includes(searchTerm) ||
                          lead.email.toLowerCase().includes(searchTerm) ||
                          lead.phone.includes(searchTerm);
      
      const matchesStatus = statusFilter === 'all' || lead.status === statusFilter;
      
      return matchesSearch && matchesStatus;
    });
    
    renderLeadsTable(filteredLeads);
  }

  function filterByStatus() {
    filterLeads();
  }

  // Chart control functions
  function changeChartPeriod(period) {
    currentPeriod = period;
    
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => {
      tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart data
    const chartData = {
      week: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        target: [50, 50, 50, 50, 50, 35, 25],
        actual: [42, 58, 45, 65, 52, 28, 18]
      },
      month: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        target: [200, 200, 200, 200],
        actual: [185, 220, 195, 235]
      },
      quarter: {
        labels: ['Januari', 'Februari', 'Maret'],
        target: [800, 800, 800],
        actual: [756, 845, 723]
      }
    };
    
    performanceChart.data.labels = chartData[period].labels;
    performanceChart.data.datasets[0].data = chartData[period].target;
    performanceChart.data.datasets[1].data = chartData[period].actual;
    performanceChart.update('active');
    
    showNotification(`Grafik diperbarui ke periode ${period}`, 'info');
  }

  function refreshLeadsChart() {
    // Simulate data refresh with animation
    const newData = [
      Math.floor(Math.random() * 20) + 35, // Lead Baru
      Math.floor(Math.random() * 15) + 20, // Contacted
      Math.floor(Math.random() * 12) + 15, // Qualified
      Math.floor(Math.random() * 10) + 10, // Proposal
      Math.floor(Math.random() * 8) + 5,   // Negosiasi
      Math.floor(Math.random() * 8) + 8,   // Deal Won
      Math.floor(Math.random() * 5) + 3    // Deal Lost
    ];
    
    leadsChart.data.datasets[0].data = newData;
    leadsChart.update('active');
    
    showNotification('Data distribusi lead berhasil diperbarui!', 'success');
  }

  function updateMetrics() {
    // Simulate metrics update
    const newMetrics = {
      calls: Array.from({length: 7}, () => Math.floor(Math.random() * 50) + 40),
      effective: Array.from({length: 7}, () => Math.floor(Math.random() * 40) + 30),
      leads: Array.from({length: 7}, () => Math.floor(Math.random() * 15) + 5)
    };
    
    metricsChart.data.datasets[0].data = newMetrics.calls;
    metricsChart.data.datasets[1].data = newMetrics.effective;
    metricsChart.data.datasets[2].data = newMetrics.leads;
    metricsChart.update('active');
    
    showNotification('Metrik performa berhasil diperbarui!', 'success');
  }

  // Action functions with Ajax simulation
  function quickAction(action) {
    const actions = {
      newLead: {
        message: 'Membuka form lead baru...',
        type: 'info',
        delay: 1500,
        success: 'Form lead baru siap digunakan!'
      },
      pendingTasks: {
        message: 'Memuat daftar task pending...',
        type: 'warning',
        delay: 1200,
        success: 'Ditemukan 15 task yang membutuhkan perhatian'
      },
      report: {
        message: 'Menyiapkan laporan sales...',
        type: 'info',
        delay: 2000,
        success: 'Laporan sales berhasil digenerate!'
      },
      schedule: {
        message: 'Membuka kalender appointment...',
        type: 'info',
        delay: 1000,
        success: 'Kalender siap untuk penjadwalan baru'
      }
    };
    
    const currentAction = actions[action];
    showNotification(currentAction.message, currentAction.type);
    
    setTimeout(() => {
      showNotification(currentAction.success, 'success');
    }, currentAction.delay);
  }

  function exportChart() {
    showNotification('Memproses export grafik...', 'info');
    setTimeout(() => {
      showNotification('Grafik berhasil diexport ke PDF!', 'success');
    }, 2000);
  }

  function exportLeads() {
    showNotification('Memproses export data leads...', 'info');
    setTimeout(() => {
      showNotification(`${leadsData.length} data lead berhasil diexport!`, 'success');
    }, 1500);
  }

  // Lead management functions
  function viewLead(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      showNotification(`Membuka detail ${lead.name}...`, 'info');
      setTimeout(() => {
        showNotification(`Detail lead ${lead.contact} berhasil dimuat`, 'success');
      }, 1000);
    }
  }

  function callLead(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      showNotification(`Menghubungi ${lead.contact} di ${lead.phone}...`, 'info');
      
      // Simulate call process
      setTimeout(() => {
        // Update last contact
        lead.lastContact = new Date().toISOString();
        if (lead.status === 'new') {
          lead.status = 'contacted';
          lead.progress = Math.max(lead.progress, 25);
        }
        renderLeadsTable(leadsData);
        showNotification(`Panggilan ke ${lead.contact} berhasil diinisiasi`, 'success');
      }, 2000);
    }
  }

  function editLead(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      showNotification(`Membuka form edit untuk ${lead.name}...`, 'info');
      setTimeout(() => {
        showNotification('Form edit lead siap digunakan', 'success');
      }, 800);
    }
  }

  function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    const isVisible = dropdown.style.display === 'block';
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
      menu.style.display = 'none';
    });
    
    // Toggle current dropdown
    dropdown.style.display = isVisible ? 'none' : 'block';
    
    // Close dropdown when clicking outside
    if (!isVisible) {
      setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
          if (!e.target.closest('.dropdown')) {
            dropdown.style.display = 'none';
            document.removeEventListener('click', closeDropdown);
          }
        });
      }, 100);
    }
  }

  function updateStatus(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      const statuses = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
      const currentIndex = statuses.indexOf(lead.status);
      const nextIndex = (currentIndex + 1) % statuses.length;
      
      lead.status = statuses[nextIndex];
      lead.progress = Math.min(lead.progress + 15, 100);
      
      renderLeadsTable(leadsData);
      showNotification(`Status ${lead.name} berhasil diperbarui`, 'success');
      document.getElementById(`dropdown-${id}`).style.display = 'none';
    }
  }

  function scheduleFollowup(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      showNotification(`Menjadwalkan follow-up untuk ${lead.contact}...`, 'info');
      setTimeout(() => {
        showNotification('Follow-up berhasil dijadwalkan untuk besok pagi', 'success');
      }, 1200);
      document.getElementById(`dropdown-${id}`).style.display = 'none';
    }
  }

  function sendEmail(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead) {
      showNotification(`Mengirim email ke ${lead.email}...`, 'info');
      setTimeout(() => {
        showNotification(`Email berhasil dikirim ke ${lead.contact}`, 'success');
      }, 2000);
      document.getElementById(`dropdown-${id}`).style.display = 'none';
    }
  }

  function deleteLead(id) {
    const lead = leadsData.find(l => l.id === id);
    if (lead && confirm(`Yakin ingin menghapus lead ${lead.name}?`)) {
      leadsData = leadsData.filter(l => l.id !== id);
      originalLeadsData = originalLeadsData.filter(l => l.id !== id);
      renderLeadsTable(leadsData);
      showNotification(`Lead ${lead.name} berhasil dihapus`, 'success');
      document.getElementById(`dropdown-${id}`).style.display = 'none';
    }
  }

  // Auto refresh function
  function autoRefresh() {
    document.getElementById('syncIndicator').innerHTML = `
      <i class="mdi mdi-sync mdi-spin"></i>
      <span>Memperbarui data...</span>
    `;
    document.getElementById('syncIndicator').style.opacity = '1';
    
    setTimeout(() => {
      // Simulate small data changes
      const targetElement = document.getElementById('targetRealization');
      const currentTarget = parseInt(targetElement.textContent);
      const newTarget = Math.max(0, Math.min(100, currentTarget + Math.floor(Math.random() * 6) - 2));
      
      if (newTarget !== currentTarget) {
        animateValue('targetRealization', currentTarget, newTarget, 1000, (val) => `${val}%`);
        document.getElementById('targetProgress').style.width = `${newTarget}%`;
        document.getElementById('targetPercentage').textContent = `${newTarget}%`;
      }
      
      const leadsElement = document.getElementById('newLeads');
      const currentLeads = parseInt(leadsElement.textContent);
      const newLeads = Math.max(0, currentLeads + Math.floor(Math.random() * 5) - 2);
      
      if (newLeads !== currentLeads) {
        animateValue('newLeads', currentLeads, newLeads, 800);
      }
      
      document.getElementById('syncIndicator').innerHTML = `
        <i class="mdi mdi-check-circle"></i>
        <span>Data terbaru</span>
      `;
      
      setTimeout(() => {
        document.getElementById('syncIndicator').style.opacity = '0.7';
      }, 3000);
    }, 2000);
  }

  // Enhanced notification system
  function showNotification(message, type = 'info') {
    const colors = {
      success: { bg: '#059669', border: '#10b981' },
      info: { bg: '#2563eb', border: '#3b82f6' },
      warning: { bg: '#d97706', border: '#f59e0b' },
      danger: { bg: '#dc2626', border: '#ef4444' }
    };
    
    const icons = {
      success: '✅',
      info: '💡',
      warning: '⚠️',
      danger: '❌'
    };
    
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 100px;
      right: 20px;
      background: ${colors[type].bg};
      color: white;
      padding: 1.25rem 1.75rem;
      border-radius: 16px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
      z-index: 99999;
      font-weight: 600;
      max-width: 380px;
      min-width: 280px;
      transform: translateX(100%);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      font-family: 'Inter', sans-serif;
      font-size: 0.9rem;
      line-height: 1.5;
      backdrop-filter: blur(10px);
      border: 2px solid ${colors[type].border};
    `;
    
    notification.innerHTML = `
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem;">${icons[type]}</span>
        <div>
          <div style="font-weight: 700; margin-bottom: 2px;">${getNotificationTitle(type)}</div>
          <div style="font-weight: 500; opacity: 0.95;">${message}</div>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" 
                style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; padding: 4px; margin-left: auto; opacity: 0.7; transition: opacity 0.2s;" 
                onmouseover="this.style.opacity='1'" 
                onmouseout="this.style.opacity='0.7'">×</button>
      </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.transform = 'translateX(0)';
    }, 100);
    
    const autoRemove = setTimeout(() => {
      notification.style.transform = 'translateX(100%)';
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 400);
    }, 4000);
    
    // Allow manual dismiss by clicking
    notification.addEventListener('click', (e) => {
      if (e.target.tagName !== 'BUTTON') return;
      clearTimeout(autoRemove);
      notification.style.transform = 'translateX(100%)';
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 400);
    });
  }

  function getNotificationTitle(type) {
    const titles = {
      success: 'Berhasil',
      info: 'Informasi',
      warning: 'Peringatan',
      danger: 'Error'
    };
    return titles[type] || 'Notifikasi';
  }

  // Add CSS for dropdown menu
  const dropdownStyles = document.createElement('style');
  dropdownStyles.textContent = `
    .dropdown {
      position: relative;
      display: inline-block;
    }
    
    .dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border: 1px solid var(--border-color);
      border-radius: 12px;
      box-shadow: var(--shadow-lg);
      z-index: 1000;
      min-width: 200px;
      overflow: hidden;
    }
    
    .dropdown-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      color: var(--dark-color);
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .dropdown-item:last-child {
      border-bottom: none;
    }
    
    .dropdown-item:hover {
      background: var(--light-color);
      transform: translateX(4px);
    }
    
    .dropdown-item.text-danger {
      color: var(--danger-color);
    }
    
    .dropdown-item.text-danger:hover {
      background: rgba(220, 38, 38, 0.1);
    }
  `;
  document.head.appendChild(dropdownStyles);

  // Initialize tooltips and enhanced interactions
  document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects for interactive elements
    const interactiveElements = document.querySelectorAll('.stats-card, .quick-action-card, .btn');
    interactiveElements.forEach(element => {
      element.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
      });
    });
  });

  // Performance monitoring
  const performanceMetrics = {
    startTime: performance.now(),
    loadComplete: false
  };

  window.addEventListener('load', function() {
    performanceMetrics.loadComplete = true;
    performanceMetrics.loadTime = performance.now() - performanceMetrics.startTime;
    console.log(`Dashboard loaded in ${performanceMetrics.loadTime.toFixed(2)}ms`);
  });
</script>

<style>
  /* Additional responsive enhancements */
  @media (max-width: 480px) {
    .stats-value {
      font-size: 2rem;
    }
    
    .dashboard-title {
      font-size: 1.5rem;
    }
    
    .chart-header {
      padding: 1rem;
    }
    
    .table th,
    .table td {
      padding: 0.75rem;
      font-size: 0.8rem;
    }
    
    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.75rem;
    }
  }
  
  /* Loading states */
  .loading-state {
    opacity: 0.6;
    pointer-events: none;
  }
  
  /* Enhanced animations */
  .chart-container {
    animation: slideUp 0.6s ease-out;
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(40px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
@endsection