@extends('layouts.master')
@section('title', 'Dashboard Telesales')
@section('pageStyle')
  <style>
    :root {
      --primary-color: #4f46e5;
      --primary-light: #818cf8;
      --primary-dark: #3730a3;
      --secondary-color: #06b6d4;
      --success-color: #10b981;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --info-color: #3b82f6;
      --purple-color: #8b5cf6;
      --pink-color: #ec4899;
      --indigo-color: #6366f1;
      --teal-color: #14b8a6;
      --orange-color: #f97316;
      
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      
      --white: #ffffff;
      --text-primary: #1f2937;
      --text-secondary: #6b7280;
      --border-color: #e5e7eb;
      
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
      --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
      
      --radius-sm: 8px;
      --radius: 12px;
      --radius-md: 16px;
      --radius-lg: 20px;
      --radius-xl: 24px;
      
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: var(--white);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--text-primary);
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }

    .dashboard-container {
      padding: 2rem;
      max-width: 1800px;
      margin: 0 auto;
      min-height: 100vh;
      background: var(--white);
    }

    /* New Minimalist Header */
    .dashboard-header-minimal {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2.5rem;
      padding: 1.5rem;
      background: var(--white);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-md);
      border: 2px solid var(--gray-100);
    }

    .dashboard-title-minimal {
      font-size: 1.8rem;
      font-weight: 800;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: var(--text-primary);
    }

    .header-stats-minimal {
      display: flex;
      gap: 1.5rem;
    }

    .header-stat-minimal {
      text-align: center;
      padding: 1rem 1.5rem;
      background: var(--gray-50);
      border-radius: var(--radius);
      border: 1px solid var(--gray-200);
    }

    .header-stat-value-minimal {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 0.25rem;
      color: var(--primary-color);
    }

    .header-stat-label-minimal {
      font-size: 0.875rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    /* Enhanced Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .quick-action-card {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-xl);
      padding: 2rem;
      box-shadow: var(--shadow);
      cursor: pointer;
      transition: var(--transition);
      text-align: center;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .quick-action-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-color);
    }

    .quick-action-icon {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      display: block;
      transition: var(--transition);
      opacity: 0.9;
    }

    .quick-action-card:hover .quick-action-icon {
      transform: scale(1.1);
      opacity: 1;
    }

    .quick-action-title {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .quick-action-desc {
      font-size: 0.875rem;
      color: var(--text-secondary);
      line-height: 1.5;
    }

    .notification-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: var(--danger-color);
      color: white;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.875rem;
      font-weight: 700;
      animation: bounce-gentle 2s infinite;
      box-shadow: var(--shadow);
    }

    @keyframes bounce-gentle {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Enhanced Section Styling */
    .section {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      margin-bottom: 2.5rem;
      padding: 2rem;
      transition: var(--transition);
    }

    .section:hover {
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-color);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 0;
    }

    .section-icon {
      font-size: 2rem;
      opacity: 0.9;
      color: var(--primary-color);
    }

    .section-actions {
      display: flex;
      gap: 0.75rem;
      align-items: center;
    }

    /* Enhanced Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stats-card {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-lg);
      padding: 1.5rem;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow);
      border-color: var(--primary-color);
    }

    .stats-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .stats-icon {
      width: 60px;
      height: 60px;
      border-radius: var(--radius);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: white;
      box-shadow: var(--shadow);
    }

    .stats-content {
      flex: 1;
    }

    .stats-value {
      font-size: 2.5rem;
      font-weight: 800;
      margin: 0.5rem 0;
      line-height: 1;
      color: var(--text-primary);
    }

    .stats-label {
      font-size: 1rem;
      color: var(--text-secondary);
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .stats-trend {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      padding: 0.75rem 1rem;
      border-radius: var(--radius-sm);
      background: var(--gray-50);
      border: 1px solid var(--gray-200);
    }

    .trend-positive {
      color: var(--success-color);
      background: #dcfce7;
      border-color: #bbf7d0;
    }

    .trend-negative {
      color: var(--danger-color);
      background: #fee2e2;
      border-color: #fecaca;
    }

    .trend-urgent {
      color: var(--danger-color);
      background: #fee2e2;
      border-color: #fecaca;
      animation: pulse-urgent 2s infinite;
    }

    @keyframes pulse-urgent {
      0%, 100% { background: #fee2e2; }
      50% { background: #fecaca; }
    }

    /* Enhanced Chart Container */
    .chart-container {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      margin-bottom: 2rem;
      overflow: hidden;
      transition: var(--transition);
    }

    .chart-container:hover {
      box-shadow: var(--shadow-lg);
      border-color: var(--primary-color);
    }

    .chart-header {
      padding: 1.5rem;
      border-bottom: 2px solid var(--gray-100);
      background: var(--gray-50);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .chart-title {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .chart-body {
      padding: 1.5rem;
      min-height: 350px;
    }

    /* Enhanced Buttons */
    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius);
      font-weight: 600;
      font-size: 0.875rem;
      border: 2px solid transparent;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--purple-color));
      color: white;
      box-shadow: var(--shadow);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success-color), var(--teal-color));
      color: white;
      box-shadow: var(--shadow);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-outline {
      background: var(--white);
      border-color: var(--gray-300);
      color: var(--text-secondary);
      box-shadow: var(--shadow-sm);
    }

    .btn-outline:hover {
      background: var(--gray-50);
      border-color: var(--primary-color);
      color: var(--primary-color);
      transform: translateY(-1px);
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.8rem;
    }

    /* Enhanced Table */
    .table-container {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      overflow: hidden;
      transition: var(--transition);
    }

    .table-container:hover {
      box-shadow: var(--shadow-xl);
      border-color: var(--primary-color);
    }

    .table-header {
      padding: 1.5rem;
      border-bottom: 2px solid var(--gray-100);
      background: var(--gray-50);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .table-responsive {
      overflow-x: auto;
      max-height: 600px;
    }

    .table {
      width: 100%;
      margin: 0;
      border-collapse: collapse;
    }

    .table th {
      background: var(--white);
      padding: 1.25rem;
      text-align: left;
      font-weight: 700;
      font-size: 0.875rem;
      color: var(--text-primary);
      border-bottom: 2px solid var(--gray-100);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .table td {
      padding: 1.25rem;
      border-bottom: 1px solid var(--gray-100);
      vertical-align: middle;
    }

    .table tbody tr {
      transition: background-color 0.2s;
    }

    .table tbody tr:hover {
      background: var(--gray-50);
    }

    /* Enhanced Badges */
    .badge {
      padding: 0.5rem 1rem;
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid;
    }

    .badge-success {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
      border-color: #22c55e;
    }

    .badge-warning {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      color: #92400e;
      border-color: #f59e0b;
    }

    .badge-info {
      background: linear-gradient(135deg, #dbeafe, #bfdbfe);
      color: #1e40af;
      border-color: #3b82f6;
    }

    .badge-danger {
      background: linear-gradient(135deg, #fee2e2, #fecaca);
      color: #991b1b;
      border-color: #ef4444;
    }

    /* Search Box */
    .search-box {
      position: relative;
      max-width: 320px;
    }

    .search-input {
      width: 100%;
      padding: 0.875rem 1.25rem 0.875rem 3.5rem;
      border: 2px solid var(--gray-200);
      border-radius: var(--radius);
      font-size: 0.875rem;
      background: var(--white);
      transition: var(--transition);
      box-shadow: var(--shadow-sm);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 1.25rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray-400);
      font-size: 1.125rem;
    }

    /* Priority Indicators */
    .priority-indicator {
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
      font-size: 0.75rem;
      font-weight: 700;
      padding: 0.5rem 0.75rem;
      border-radius: var(--radius-sm);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid;
    }

    .priority-high {
      background: linear-gradient(135deg, #fee2e2, #fecaca);
      color: #991b1b;
      border-color: #ef4444;
    }

    .priority-medium {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      color: #92400e;
      border-color: #f59e0b;
    }

    .priority-low {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
      border-color: #22c55e;
    }

    /* Enhanced Activity Stats */
    .activity-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.5rem;
    }

    .activity-item {
      background: var(--white);
      border: 2px solid var(--gray-100);
      border-radius: var(--radius-lg);
      padding: 1.5rem;
      text-align: center;
      transition: var(--transition);
      box-shadow: var(--shadow-sm);
      position: relative;
      overflow: hidden;
    }

    .activity-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-color), var(--purple-color));
    }

    .activity-item:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow);
      border-color: var(--primary-color);
    }

    .activity-number {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      color: var(--primary-color);
    }

    .activity-label {
      font-size: 0.875rem;
      color: var(--text-secondary);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Toast Notifications */
    .toast {
      position: fixed;
      top: 2rem;
      right: 2rem;
      background: var(--white);
      border: 2px solid var(--gray-200);
      border-radius: var(--radius-md);
      padding: 1.25rem 1.75rem;
      box-shadow: var(--shadow-2xl);
      z-index: 99999;
      font-weight: 600;
      max-width: 400px;
      min-width: 320px;
      transform: translateX(100%);
      transition: var(--transition);
      backdrop-filter: blur(10px);
    }

    .toast.show {
      transform: translateX(0);
    }

    .toast-success { border-color: var(--success-color); }
    .toast-info { border-color: var(--info-color); }
    .toast-warning { border-color: var(--warning-color); }
    .toast-danger { border-color: var(--danger-color); }

    /* Fade in animations */
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeIn 0.8s ease forwards;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Loading Overlay */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(8px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
    }

    .loading-content {
      background: var(--white);
      padding: 2.5rem;
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-2xl);
      text-align: center;
      max-width: 400px;
      width: 90%;
    }

    .loading-text {
      font-weight: 600;
      color: var(--text-primary);
      margin-top: 1rem;
      font-size: 1.125rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .dashboard-container { padding: 1.5rem; }
      .stats-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
      .quick-actions { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      .dashboard-container { padding: 1rem; }
      .dashboard-header-minimal { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
      .header-stats-minimal { width: 100%; flex-wrap: wrap; }
      .quick-actions { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: 1fr; }
      .chart-body { padding: 1rem; min-height: 300px; }
      .section-header { flex-direction: column; align-items: stretch; }
      .chart-header, .table-header { flex-direction: column; align-items: stretch; }
      .activity-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
      .activity-grid { grid-template-columns: 1fr; }
      .header-stats-minimal { grid-template-columns: 1fr; }
      .section { padding: 1.5rem 1rem; }
    }
  </style>
@endsection

@section('content')
  <div class="dashboard-container">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
      <div class="loading-content">
        <div class="spinner"></div>
        <div class="loading-text">Memuat Data...</div>
      </div>
    </div>

    <!-- Minimal Dashboard Header -->
    <div class="dashboard-header-minimal fade-in">
      <h1 class="dashboard-title-minimal">
        <i class="mdi mdi-phone"></i> Dashboard Telesales
      </h1>
      <div class="header-stats-minimal">
        <div class="header-stat-minimal">
          <div class="header-stat-value-minimal" id="headerTotalLeads">-</div>
          <div class="header-stat-label-minimal">Total Leads</div>
        </div>
        <div class="header-stat-minimal">
          <div class="header-stat-value-minimal" id="headerTotalCalls">-</div>
          <div class="header-stat-label-minimal">Total Panggilan</div>
        </div>
        <div class="header-stat-minimal">
          <div class="header-stat-value-minimal" id="headerSuccessRate">-%</div>
          <div class="header-stat-label-minimal">Success Rate</div>
        </div>
        <div class="header-stat-minimal">
          <div class="header-stat-value-minimal" id="headerActiveAgents">-</div>
          <div class="header-stat-label-minimal">Agent Aktif</div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions fade-in">
      <div class="quick-action-card" onclick="startCalling()">
        <i class="mdi mdi-phone-plus quick-action-icon" style="color: var(--success-color);"></i>
        <div class="quick-action-title">Mulai Panggilan</div>
        <div class="quick-action-desc">Memulai sesi panggilan baru dengan prioritas tinggi</div>
      </div>
      <div class="quick-action-card" onclick="viewPendingFollowups()">
        <span class="notification-badge" id="pendingCount">-</span>
        <i class="mdi mdi-clock-alert quick-action-icon" style="color: var(--danger-color);"></i>
        <div class="quick-action-title">Follow-up Tertunda</div>
        <div class="quick-action-desc">Leads yang memerlukan tindakan segera</div>
      </div>
      <div class="quick-action-card" onclick="viewCallHistory()">
        <i class="mdi mdi-phone-log quick-action-icon" style="color: var(--info-color);"></i>
        <div class="quick-action-title">Riwayat Panggilan</div>
        <div class="quick-action-desc">Analisis panggilan dan interaksi sebelumnya</div>
      </div>
      <div class="quick-action-card" onclick="generateReport()">
        <i class="mdi mdi-chart-box-outline quick-action-icon" style="color: var(--purple-color);"></i>
        <div class="quick-action-title">Laporan Analytics</div>
        <div class="quick-action-desc">Generate laporan performa dan insights</div>
      </div>
    </div>

    <!-- Section 1: Leads yang Belum Dihubungi -->
    <div class="section fade-in">
      <div class="section-header">
        <h2 class="section-title">
          <span class="section-icon">📋</span>
          Leads yang Belum Dihubungi
        </h2>
        <div class="section-actions">
          <button class="btn btn-outline btn-sm" onclick="refreshUncontactedData()">
            <i class="mdi mdi-refresh"></i> Refresh
          </button>
          <button class="btn btn-primary btn-sm" onclick="prioritizeLeads()">
            <i class="mdi mdi-sort"></i> Prioritaskan
          </button>
        </div>
      </div>
      
      <div class="stats-grid">
        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--danger-color), #f87171);">
              <i class="mdi mdi-phone-missed"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="totalUncontacted">-</div>
            <div class="stats-label">Total Leads Belum Dihubungi</div>
            <div class="stats-trend" id="totalUncontactedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--warning-color), #fbbf24);">
              <i class="mdi mdi-account-clock"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="todayUncontacted">-</div>
            <div class="stats-label">Leads Hari Ini Belum Dihubungi</div>
            <div class="stats-trend" id="todayUncontactedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--info-color), #60a5fa);">
              <i class="mdi mdi-calendar-clock"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="oldestLead">-</div>
            <div class="stats-label">Leads Terlama (Hari)</div>
            <div class="stats-trend" id="oldestTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--purple-color), #a855f7);">
              <i class="mdi mdi-fire"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="urgentUncontacted">-</div>
            <div class="stats-label">Prioritas Tinggi</div>
            <div class="stats-trend" id="urgentTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Hasil Follow Up -->
    <div class="section fade-in">
      <div class="section-header">
        <h2 class="section-title">
          <span class="section-icon">🎯</span>
          Hasil Follow Up
        </h2>
        <div class="section-actions">
          <button class="btn btn-outline btn-sm" onclick="refreshFollowupData()">
            <i class="mdi mdi-refresh"></i> Refresh
          </button>
          <button class="btn btn-success btn-sm" onclick="exportFollowupReport()">
            <i class="mdi mdi-download"></i> Export
          </button>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--success-color), #34d399);">
              <i class="mdi mdi-phone-check"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="successfulFollowup">-</div>
            <div class="stats-label">Follow Up Berhasil</div>
            <div class="stats-trend" id="successTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--info-color), #60a5fa);">
              <i class="mdi mdi-heart"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="interestedLeads">-</div>
            <div class="stats-label">Leads Tertarik</div>
            <div class="stats-trend" id="interestedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--warning-color), #fbbf24);">
              <i class="mdi mdi-phone-hangup"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="notInterestedLeads">-</div>
            <div class="stats-label">Leads Tidak Tertarik</div>
            <div class="stats-trend" id="notInterestedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--purple-color), #a855f7);">
              <i class="mdi mdi-account-convert"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="convertedLeads">-</div>
            <div class="stats-label">Leads Terkonversi</div>
            <div class="stats-trend" id="convertedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>
      </div>

      <!-- Follow-up Results Chart -->
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            <i class="mdi mdi-chart-donut"></i>
            Distribusi Hasil Follow Up
          </h3>
          <div class="section-actions">
            <button class="btn btn-outline btn-sm" onclick="toggleFollowupChartType()">
              <i class="mdi mdi-chart-bar"></i> <span id="followupChartTypeBtn">Bar Chart</span>
            </button>
            <button class="btn btn-outline btn-sm" onclick="exportFollowupChart()">
              <i class="mdi mdi-download"></i> Export
            </button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="followupChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Section 3: Statistik Per Aktivitas -->
    <div class="section fade-in">
      <div class="section-header">
        <h2 class="section-title">
          <span class="section-icon">📈</span>
          Statistik Per Aktivitas
        </h2>
        <div class="section-actions">
          <button class="btn btn-outline btn-sm" onclick="refreshActivityData()">
            <i class="mdi mdi-refresh"></i> Refresh
          </button>
          <button class="btn btn-primary btn-sm" onclick="viewDetailedActivity()">
            <i class="mdi mdi-eye"></i> Detail
          </button>
        </div>
      </div>

      <!-- Daily Activity Summary -->
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            <i class="mdi mdi-calendar-today"></i>
            Ringkasan Aktivitas Hari Ini
          </h3>
          <div class="section-actions">
            <button class="btn btn-success btn-sm" onclick="exportActivityData()">
              <i class="mdi mdi-export"></i> Export Data
            </button>
          </div>
        </div>
        <div style="padding: 1.5rem;">
          <div class="activity-grid" id="activityGrid">
            <!-- Loading skeletons -->
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
            <div class="activity-item">
              <div class="loading-skeleton" style="height: 3rem; margin-bottom: 0.5rem;"></div>
              <div class="loading-skeleton" style="height: 0.875rem;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity Chart -->
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">
            <i class="mdi mdi-chart-line"></i>
            Trend Aktivitas Panggilan (7 Hari Terakhir)
          </h3>
          <div class="section-actions">
            <button class="btn btn-outline btn-sm" onclick="toggleActivityChartType()">
              <i class="mdi mdi-chart-bar"></i> <span id="activityChartTypeBtn">Line Chart</span>
            </button>
            <button class="btn btn-outline btn-sm" onclick="exportActivityChart()">
              <i class="mdi mdi-download"></i> Export
            </button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="activityChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Section 4: Leads Assign yang Belum Follow Up -->
    <div class="section fade-in">
      <div class="section-header">
        <h2 class="section-title">
          <span class="section-icon">🗂️</span>
          Leads Assign yang Belum Follow Up
        </h2>
        <div class="section-actions">
          <button class="btn btn-outline btn-sm" onclick="refreshAssignedData()">
            <i class="mdi mdi-refresh"></i> Refresh
          </button>
          <button class="btn btn-primary btn-sm" onclick="assignLeads()">
            <i class="mdi mdi-account-plus"></i> Assign New
          </button>
        </div>
      </div>

      <!-- Stats for Assigned Leads -->
      <div class="stats-grid">
        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--danger-color), #f87171);">
              <i class="mdi mdi-account-alert"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="totalAssignedNotFollowed">-</div>
            <div class="stats-label">Total Leads Assign Belum FU</div>
            <div class="stats-trend" id="assignedTotalTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--warning-color), #fbbf24);">
              <i class="mdi mdi-fire"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="urgentAssigned">-</div>
            <div class="stats-label">Prioritas Tinggi</div>
            <div class="stats-trend" id="urgentAssignedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--info-color), #60a5fa);">
              <i class="mdi mdi-calendar-plus"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="todayAssigned">-</div>
            <div class="stats-label">Assign Hari Ini</div>
            <div class="stats-trend" id="todayAssignedTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-header">
            <div class="stats-icon" style="background: linear-gradient(135deg, var(--success-color), #34d399);">
              <i class="mdi mdi-account-check"></i>
            </div>
          </div>
          <div class="stats-content">
            <div class="stats-value" id="activeAgents">-</div>
            <div class="stats-label">Agent Aktif</div>
            <div class="stats-trend" id="activeAgentsTrend">
              <i class="mdi mdi-loading mdi-spin"></i> Memuat...
            </div>
          </div>
        </div>
      </div>

      <!-- Leads Table -->
      <div class="table-container">
        <div class="table-header">
          <h3 class="chart-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            Daftar Leads yang Perlu Follow Up
          </h3>
          <div class="section-actions">
            <div class="search-box">
              <i class="mdi mdi-magnify search-icon"></i>
              <input type="text" class="search-input" placeholder="Cari leads..." id="leadsSearch">
            </div>
            <button class="btn btn-success btn-sm" onclick="refreshLeadsTable()">
              <i class="mdi mdi-refresh"></i> Refresh
            </button>
            <button class="btn btn-outline btn-sm" onclick="exportLeadsData()">
              <i class="mdi mdi-export"></i> Export
            </button>
            <button class="btn btn-primary btn-sm" onclick="bulkAction()">
              <i class="mdi mdi-account-multiple-plus"></i> Bulk Action
            </button>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table" id="leadsTable">
            <thead>
              <tr>
                <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                <th><i class="mdi mdi-account"></i> Nama Lead</th>
                <th><i class="mdi mdi-calendar"></i> Tanggal Assign</th>
                <th><i class="mdi mdi-tag"></i> Kategori</th>
                <th><i class="mdi mdi-source-branch"></i> Sumber Lead</th>
                <th><i class="mdi mdi-flag"></i> Prioritas</th>
                <th><i class="mdi mdi-phone"></i> Kontak</th>
                <th><i class="mdi mdi-account-tie"></i> Sales Agent</th>
                <th><i class="mdi mdi-cog"></i> Aksi</th>
              </tr>
            </thead>
            <tbody id="leadsTableBody">
              <!-- Loading skeleton -->
              <tr>
                <td colspan="9" style="padding: 3rem; text-align: center;">
                  <div class="spinner"></div>
                  <div style="margin-top: 1rem; color: var(--text-secondary);">Memuat data leads...</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 1.5rem; border-top: 2px solid var(--gray-100); background: var(--gray-50);">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
              Menampilkan <span id="showingStart">0</span>-<span id="showingEnd">0</span> dari <span id="totalLeads">0</span> leads
            </div>
            <div style="display: flex; gap: 0.5rem;" id="pagination">
              <!-- Pagination buttons will be inserted here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('pageScript')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Global variables
    let followupChart = null;
    let activityChart = null;
    let leadsData = [];
    let filteredLeadsData = [];
    let currentPage = 1;
    let leadsPerPage = 10;
    let followupChartType = 'doughnut';
    let activityChartType = 'bar';

    // Enhanced dummy data
    const dummyData = {
      header: {
        totalLeads: 1247,
        totalCalls: 3856,
        successRate: 67,
        activeAgents: 12
      },
      uncontacted: {
        total: 234,
        today: 45,
        oldest: 18,
        urgent: 67,
        trends: {
          total: { value: 'Meningkat 12% dari minggu lalu', type: 'negative' },
          today: { value: 'Bertambah 8 leads hari ini', type: 'negative' },
          oldest: { value: 'Perlu segera ditindaklanjuti!', type: 'urgent' },
          urgent: { value: 'Prioritas tinggi meningkat', type: 'urgent' }
        }
      },
      followup: {
        successful: 145,
        interested: 89,
        notInterested: 56,
        converted: 34,
        trends: {
          successful: { value: 'Naik 23% dari kemarin', type: 'positive' },
          interested: { value: 'Tertarik meningkat 15%', type: 'positive' },
          notInterested: { value: 'Turun 5% dari kemarin', type: 'positive' },
          converted: { value: 'Konversi naik 18%', type: 'positive' }
        }
      },
      activity: {
        totalCalls: 186,
        successfulCalls: 124,
        failedCalls: 32,
        voicemails: 23,
        noAnswer: 30,
        wrongNumbers: 7,
        weeklyData: {
          labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
          datasets: [
            { label: 'Total Panggilan', data: [145, 167, 189, 156, 186, 134, 98], color: '#4f46e5' },
            { label: 'Berhasil', data: [98, 112, 134, 105, 124, 89, 65], color: '#10b981' },
            { label: 'Gagal', data: [47, 55, 55, 51, 62, 45, 33], color: '#ef4444' }
          ]
        }
      },
      assigned: {
        total: 156,
        urgent: 43,
        today: 28,
        activeAgents: 12,
        trends: {
          total: { value: 'Perlu tindakan segera!', type: 'urgent' },
          urgent: { value: 'Prioritas tinggi bertambah', type: 'urgent' },
          today: { value: 'Assign baru hari ini', type: 'positive' },
          activeAgents: { value: 'Semua agent aktif', type: 'positive' }
        }
      }
    };

    // Enhanced leads dummy data
    const leadsData_sample = [
      {
        id: 1, name: 'Andi Wijaya', assignDate: '2024-03-12', category: 'KPR Syariah',
        source: 'Website', priority: 'high', phone: '+62812-3456-7890',
        email: 'andi.wijaya@email.com', sales: 'Sarah Lestari', daysSince: 3,
        status: 'new', notes: 'Tertarik produk KPR untuk rumah pertama'
      },
      {
        id: 2, name: 'Sari Dewi Kusuma', assignDate: '2024-03-11', category: 'Investasi Properti',
        source: 'Facebook Ads', priority: 'medium', phone: '+62813-9876-5432',
        email: 'sari.dewi@email.com', sales: 'Budi Santoso', daysSince: 4,
        status: 'contacted', notes: 'Perlu follow up untuk penawaran khusus'
      },
      {
        id: 3, name: 'Budi Harmanto', assignDate: '2024-03-10', category: 'KPR Konvensional',
        source: 'Referral', priority: 'high', phone: '+62814-5678-9012',
        email: 'budi.harmanto@email.com', sales: 'Maya Sari', daysSince: 5,
        status: 'interested', notes: 'Siap survey lokasi minggu depan'
      },
      {
        id: 4, name: 'Maya Kusuma Wardani', assignDate: '2024-03-09', category: 'Refinancing',
        source: 'Google Ads', priority: 'low', phone: '+62815-2468-1357',
        email: 'maya.kusuma@email.com', sales: 'Rizki Pratama', daysSince: 6,
        status: 'new', notes: 'Ingin refinancing KPR existing'
      },
      {
        id: 5, name: 'Rizki Indra Pratama', assignDate: '2024-03-08', category: 'KPR Subsidi',
        source: 'Instagram', priority: 'medium', phone: '+62816-1357-2468',
        email: 'rizki.indra@email.com', sales: 'Diana Putri', daysSince: 7,
        status: 'follow_up', notes: 'Menunggu konfirmasi dokumen'
      },
      {
        id: 6, name: 'Diana Sari Melati', assignDate: '2024-03-07', category: 'Investasi Tanah',
        source: 'WhatsApp', priority: 'high', phone: '+62817-9876-5432',
        email: 'diana.sari@email.com', sales: 'Eko Prasetyo', daysSince: 8,
        status: 'hot_lead', notes: 'Sangat tertarik, siap closing minggu ini'
      },
      {
        id: 7, name: 'Eko Wijaya Kusuma', assignDate: '2024-03-06', category: 'KPR Syariah',
        source: 'Broker', priority: 'medium', phone: '+62818-1234-5678',
        email: 'eko.wijaya@email.com', sales: 'Fitri Handayani', daysSince: 9,
        status: 'contacted', notes: 'Membutuhkan penjelasan detail syariah'
      },
      {
        id: 8, name: 'Fitri Dewi Sartika', assignDate: '2024-03-05', category: 'Apartemen',
        source: 'Exhibition', priority: 'low', phone: '+62819-8765-4321',
        email: 'fitri.dewi@email.com', sales: 'Agus Setiawan', daysSince: 10,
        status: 'new', notes: 'Mencari apartemen untuk investasi'
      },
      {
        id: 9, name: 'Agus Setiawan Budi', assignDate: '2024-03-04', category: 'Ruko',
        source: 'Website', priority: 'high', phone: '+62821-1111-2222',
        email: 'agus.setiawan@email.com', sales: 'Linda Sari', daysSince: 11,
        status: 'interested', notes: 'Butuh ruko untuk usaha kuliner'
      },
      {
        id: 10, name: 'Linda Sari Dewi', assignDate: '2024-03-03', category: 'Villa',
        source: 'Referral', priority: 'medium', phone: '+62822-3333-4444',
        email: 'linda.sari@email.com', sales: 'Tommy Wijaya', daysSince: 12,
        status: 'follow_up', notes: 'Ingin villa di area Puncak'
      }
    ];

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
      initializeDashboard();
      setupEventListeners();
      startAutoRefresh();
    });

    async function initializeDashboard() {
      showLoading(true);
      
      try {
        // Load all data with staggered timing
        await loadHeaderData();
        await sleep(300);
        await loadUncontactedData();
        await sleep(300);
        await loadFollowupData();
        await sleep(300);
        await loadActivityData();
        await sleep(300);
        await loadAssignedData();
        await sleep(300);
        await loadLeadsTable();
        await sleep(300);
        initializeCharts();
        
        // Animate elements
        animateElements();
        
        showToast('Dashboard berhasil dimuat!', 'success');
      } catch (error) {
        console.error('Error initializing dashboard:', error);
        showToast('Gagal memuat dashboard', 'danger');
      } finally {
        showLoading(false);
      }
    }

    function setupEventListeners() {
      // Search functionality
      document.getElementById('leadsSearch').addEventListener('input', debounce(filterLeads, 300));
      
      // Keyboard shortcuts
      document.addEventListener('keydown', handleKeyboardShortcuts);
    }

    function handleKeyboardShortcuts(e) {
      if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
          case 'r':
            e.preventDefault();
            refreshAllData();
            break;
          case 'f':
            e.preventDefault();
            document.getElementById('leadsSearch').focus();
            break;
        }
      }
    }

    function startAutoRefresh() {
      // Auto refresh every 2 minutes
      setInterval(async () => {
        await refreshRandomData();
        showToast('Data otomatis diperbarui', 'info');
      }, 120000);
    }

    // Utility functions
    function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    }

    function showLoading(show) {
      document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
    }

    function animateElements() {
      const elements = document.querySelectorAll('.fade-in');
      elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
      });
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

    // Data loading functions
    async function loadHeaderData() {
      await sleep(500); // Simulate API call
      const data = dummyData.header;
      
      document.getElementById('headerTotalLeads').textContent = data.totalLeads.toLocaleString();
      document.getElementById('headerTotalCalls').textContent = data.totalCalls.toLocaleString();
      document.getElementById('headerSuccessRate').textContent = data.successRate;
      document.getElementById('headerActiveAgents').textContent = data.activeAgents;
    }

    async function loadUncontactedData() {
      await sleep(800);
      const data = dummyData.uncontacted;
      
      document.getElementById('totalUncontacted').textContent = data.total;
      updateTrend('totalUncontactedTrend', data.trends.total);
      
      document.getElementById('todayUncontacted').textContent = data.today;
      updateTrend('todayUncontactedTrend', data.trends.today);
      
      document.getElementById('oldestLead').textContent = data.oldest;
      updateTrend('oldestTrend', data.trends.oldest);
      
      document.getElementById('urgentUncontacted').textContent = data.urgent;
      updateTrend('urgentTrend', data.trends.urgent);
    }

    async function loadFollowupData() {
      await sleep(600);
      const data = dummyData.followup;
      
      document.getElementById('successfulFollowup').textContent = data.successful;
      updateTrend('successTrend', data.trends.successful);
      
      document.getElementById('interestedLeads').textContent = data.interested;
      updateTrend('interestedTrend', data.trends.interested);
      
      document.getElementById('notInterestedLeads').textContent = data.notInterested;
      updateTrend('notInterestedTrend', data.trends.notInterested);
      
      document.getElementById('convertedLeads').textContent = data.converted;
      updateTrend('convertedTrend', data.trends.converted);
    }

    async function loadActivityData() {
      await sleep(700);
      const data = dummyData.activity;
      
      document.getElementById('activityGrid').innerHTML = `
        <div class="activity-item">
          <div class="activity-number">${data.totalCalls}</div>
          <div class="activity-label">Total Panggilan</div>
        </div>
        <div class="activity-item">
          <div class="activity-number">${data.successfulCalls}</div>
          <div class="activity-label">Panggilan Berhasil</div>
        </div>
        <div class="activity-item">
          <div class="activity-number">${data.failedCalls}</div>
          <div class="activity-label">Panggilan Gagal</div>
        </div>
        <div class="activity-item">
          <div class="activity-number">${data.voicemails}</div>
          <div class="activity-label">Voicemail</div>
        </div>
        <div class="activity-item">
          <div class="activity-number">${data.noAnswer}</div>
          <div class="activity-label">Tidak Diangkat</div>
        </div>
        <div class="activity-item">
          <div class="activity-number">${data.wrongNumbers}</div>
          <div class="activity-label">Salah Nomor</div>
        </div>
      `;
    }

    async function loadAssignedData() {
      await sleep(650);
      const data = dummyData.assigned;
      
      document.getElementById('totalAssignedNotFollowed').textContent = data.total;
      updateTrend('assignedTotalTrend', data.trends.total);
      
      document.getElementById('urgentAssigned').textContent = data.urgent;
      updateTrend('urgentAssignedTrend', data.trends.urgent);
      
      document.getElementById('todayAssigned').textContent = data.today;
      updateTrend('todayAssignedTrend', data.trends.today);
      
      document.getElementById('activeAgents').textContent = data.activeAgents;
      updateTrend('activeAgentsTrend', data.trends.activeAgents);
      
      // Update pending count in quick action
      document.getElementById('pendingCount').textContent = data.total;
    }

    async function loadLeadsTable() {
      await sleep(900);
      leadsData = [...leadsData_sample];
      filteredLeadsData = [...leadsData];
      renderLeadsTable();
      updatePagination();
    }

    function updateTrend(elementId, trend) {
      const element = document.getElementById(elementId);
      const iconClass = getTrendIcon(trend.type);
      element.innerHTML = `<i class="mdi ${iconClass}"></i> ${trend.value}`;
      element.className = `stats-trend trend-${trend.type}`;
    }

    function getTrendIcon(type) {
      const icons = {
        positive: 'mdi-trending-up',
        negative: 'mdi-trending-down',
        urgent: 'mdi-alert-circle'
      };
      return icons[type] || 'mdi-minus';
    }

    // Chart initialization
    function initializeCharts() {
      initFollowupChart();
      initActivityChart();
    }

    function initFollowupChart() {
      const ctx = document.getElementById('followupChart').getContext('2d');
      const data = dummyData.followup;

      followupChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Tertarik', 'Tidak Tertarik', 'Terkonversi', 'Perlu Follow Up'],
          datasets: [{
            data: [data.interested, data.notInterested, data.converted, data.successful - data.interested - data.converted],
            backgroundColor: [
              '#10b981', // success
              '#ef4444', // danger  
              '#8b5cf6', // purple
              '#f59e0b'  // warning
            ],
            borderWidth: 0,
            cutout: '60%',
            hoverOffset: 8
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
                color: '#374151',
                font: { family: 'Inter', weight: '500', size: 12 }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: 'white',
              bodyColor: 'white',
              cornerRadius: 12,
              padding: 16,
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.raw / total) * 100).toFixed(1);
                  return `${context.label}: ${context.raw} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
    }

    function initActivityChart() {
      const ctx = document.getElementById('activityChart').getContext('2d');
      const data = dummyData.activity.weeklyData;

      activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: data.datasets.map(dataset => ({
            ...dataset,
            backgroundColor: dataset.color,
            borderColor: dataset.color,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
          }))
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
                color: '#374151',
                font: { family: 'Inter', weight: '500', size: 12 }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: 'white',
              bodyColor: 'white',
              cornerRadius: 12,
              padding: 16
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f3f4f6', drawBorder: false },
              ticks: { color: '#6b7280', font: { family: 'Inter', weight: '500' } }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#6b7280', font: { family: 'Inter', weight: '500' } }
            }
          }
        }
      });
    }

    // Table functions
    function renderLeadsTable() {
      const tbody = document.getElementById('leadsTableBody');
      const startIndex = (currentPage - 1) * leadsPerPage;
      const endIndex = startIndex + leadsPerPage;
      const pageLeads = filteredLeadsData.slice(startIndex, endIndex);

      if (pageLeads.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
              <i class="mdi mdi-account-search" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
              <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">Tidak ada leads ditemukan</div>
              <div>Coba ubah kata kunci pencarian atau refresh data</div>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = pageLeads.map(lead => {
        const priority = getPriorityConfig(lead.priority);
        const sourceIcon = getSourceIcon(lead.source);
        const statusBadge = getStatusBadge(lead.status);

        return `
          <tr>
            <td>
              <input type="checkbox" class="lead-checkbox" value="${lead.id}">
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--purple-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem;">
                  ${lead.name.split(' ').map(n => n[0]).join('').substring(0, 2)}
                </div>
                <div>
                  <div style="font-weight: 700; margin-bottom: 2px; color: var(--text-primary);">${lead.name}</div>
                  <div style="font-size: 0.875rem; color: var(--text-secondary);">${lead.email}</div>
                </div>
              </div>
            </td>
            <td>
              <div style="font-weight: 600; margin-bottom: 2px;">${formatDate(lead.assignDate)}</div>
              <div style="font-size: 0.875rem; color: var(--text-secondary);">${lead.daysSince} hari lalu</div>
            </td>
            <td>
              <span class="badge badge-info">
                <i class="mdi mdi-tag"></i> ${lead.category}
              </span>
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="mdi ${sourceIcon}" style="color: var(--text-secondary);"></i>
                <span style="font-weight: 500;">${lead.source}</span>
              </div>
            </td>
            <td>
              <span class="priority-indicator ${priority.class}">
                <i class="mdi ${priority.icon}"></i> ${priority.text}
              </span>
            </td>
            <td>
              <div style="font-weight: 600; margin-bottom: 2px;">${lead.phone}</div>
              <div style="font-size: 0.875rem; color: var(--text-secondary);">
                <i class="mdi mdi-whatsapp" style="color: #25d366;"></i> WhatsApp Ready
              </div>
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--success-color), var(--teal-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem;">
                  ${lead.sales.split(' ').map(n => n[0]).join('')}
                </div>
                <div>
                  <div style="font-weight: 600; font-size: 0.875rem;">${lead.sales}</div>
                  <div style="font-size: 0.75rem; color: var(--text-secondary);">Sales Agent</div>
                </div>
              </div>
            </td>
            <td>
              <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button class="btn btn-success btn-sm" onclick="callLead(${lead.id})" title="Hubungi Sekarang">
                  <i class="mdi mdi-phone"></i>
                </button>
                <button class="btn btn-primary btn-sm" onclick="whatsappLead(${lead.id})" title="WhatsApp">
                  <i class="mdi mdi-whatsapp"></i>
                </button>
                <button class="btn btn-outline btn-sm" onclick="viewLeadDetail(${lead.id})" title="Detail Lead">
                  <i class="mdi mdi-eye"></i>
                </button>
                <button class="btn btn-outline btn-sm" onclick="scheduleLead(${lead.id})" title="Jadwalkan">
                  <i class="mdi mdi-calendar-plus"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');

      updateShowingInfo();
    }

    function updateShowingInfo() {
      const totalFiltered = filteredLeadsData.length;
      const startIndex = (currentPage - 1) * leadsPerPage;
      const showingStart = totalFiltered > 0 ? startIndex + 1 : 0;
      const showingEnd = Math.min(startIndex + leadsPerPage, totalFiltered);

      document.getElementById('showingStart').textContent = showingStart;
      document.getElementById('showingEnd').textContent = showingEnd;
      document.getElementById('totalLeads').textContent = totalFiltered;
    }

    function updatePagination() {
      const totalPages = Math.ceil(filteredLeadsData.length / leadsPerPage);
      const pagination = document.getElementById('pagination');

      if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
      }

      let paginationHTML = '';

      // Previous button
      if (currentPage > 1) {
        paginationHTML += `
          <button class="btn btn-outline btn-sm" onclick="changePage(${currentPage - 1})">
            <i class="mdi mdi-chevron-left"></i> Prev
          </button>
        `;
      }

      // Page numbers
      const startPage = Math.max(1, currentPage - 2);
      const endPage = Math.min(totalPages, currentPage + 2);

      if (startPage > 1) {
        paginationHTML += `<button class="btn btn-outline btn-sm" onclick="changePage(1)">1</button>`;
        if (startPage > 2) paginationHTML += `<span style="padding: 0.5rem;">...</span>`;
      }

      for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
          <button class="btn ${i === currentPage ? 'btn-primary' : 'btn-outline'} btn-sm" onclick="changePage(${i})">
            ${i}
          </button>
        `;
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) paginationHTML += `<span style="padding: 0.5rem;">...</span>`;
        paginationHTML += `<button class="btn btn-outline btn-sm" onclick="changePage(${totalPages})">${totalPages}</button>`;
      }

      // Next button
      if (currentPage < totalPages) {
        paginationHTML += `
          <button class="btn btn-outline btn-sm" onclick="changePage(${currentPage + 1})">
            Next <i class="mdi mdi-chevron-right"></i>
          </button>
        `;
      }

      pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
      currentPage = page;
      renderLeadsTable();
      updatePagination();
      
      // Smooth scroll to table
      document.querySelector('.table-container').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'nearest' 
      });
    }

    function filterLeads() {
      const searchTerm = document.getElementById('leadsSearch').value.toLowerCase();
      filteredLeadsData = leadsData.filter(lead =>
        lead.name.toLowerCase().includes(searchTerm) ||
        lead.email.toLowerCase().includes(searchTerm) ||
        lead.category.toLowerCase().includes(searchTerm) ||
        lead.source.toLowerCase().includes(searchTerm) ||
        lead.sales.toLowerCase().includes(searchTerm) ||
        lead.phone.includes(searchTerm)
      );
      currentPage = 1;
      renderLeadsTable();
      updatePagination();
    }

    // Utility functions for table
    function formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }

    function getPriorityConfig(priority) {
      const configs = {
        high: { class: 'priority-high', icon: 'mdi-fire', text: 'Tinggi' },
        medium: { class: 'priority-medium', icon: 'mdi-minus-circle', text: 'Sedang' },
        low: { class: 'priority-low', icon: 'mdi-check-circle', text: 'Rendah' }
      };
      return configs[priority] || configs.medium;
    }

    function getSourceIcon(source) {
      const icons = {
        'Website': 'mdi-web',
        'Facebook Ads': 'mdi-facebook',
        'Facebook': 'mdi-facebook',
        'Instagram': 'mdi-instagram',
        'WhatsApp': 'mdi-whatsapp',
        'Referral': 'mdi-account-group',
        'Google Ads': 'mdi-google',
        'Broker': 'mdi-handshake',
        'Exhibition': 'mdi-store'
      };
      return icons[source] || 'mdi-help-circle';
    }

    function getStatusBadge(status) {
      const configs = {
        new: { class: 'badge-info', icon: 'mdi-new-box', text: 'Baru' },
        contacted: { class: 'badge-warning', icon: 'mdi-phone', text: 'Dihubungi' },
        interested: { class: 'badge-success', icon: 'mdi-heart', text: 'Tertarik' },
        follow_up: { class: 'badge-warning', icon: 'mdi-clock', text: 'Follow Up' },
        hot_lead: { class: 'badge-danger', icon: 'mdi-fire', text: 'Hot Lead' }
      };
      return configs[status] || configs.new;
    }

    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.querySelectorAll('.lead-checkbox');
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
    }

    // Action functions
    async function startCalling() {
      showToast('🔥 Memulai sesi panggilan prioritas tinggi...', 'success');
      
      // Simulate call preparation
      showLoading(true);
      await sleep(2000);
      showLoading(false);
      
      if (leadsData.length > 0) {
        const nextLead = leadsData.find(lead => lead.priority === 'high') || leadsData[0];
        showToast(`📞 Memanggil ${nextLead.name} - ${nextLead.phone}`, 'info');
        
        // Simulate call progress
        setTimeout(() => {
          const outcomes = [
            { message: '✅ Panggilan berhasil! Lead tertarik dengan penawaran', type: 'success' },
            { message: '📧 Tidak ada jawaban - voicemail terkirim', type: 'warning' },
            { message: '🔄 Nomor sibuk - akan dicoba kembali dalam 15 menit', type: 'info' },
            { message: '🎉 Call berhasil! Lead siap untuk survei lokasi', type: 'success' }
          ];
          const outcome = outcomes[Math.floor(Math.random() * outcomes.length)];
          showToast(outcome.message, outcome.type);
        }, 3000);
      }
    }

    function viewPendingFollowups() {
      const pendingCount = dummyData.assigned.total;
      showToast(`📋 Menampilkan ${pendingCount} leads yang perlu follow-up segera`, 'warning');
      
      // Filter to show only high priority leads
      document.getElementById('leadsSearch').value = '';
      filteredLeadsData = leadsData.filter(lead => lead.priority === 'high');
      currentPage = 1;
      renderLeadsTable();
      updatePagination();
      
      // Scroll to table
      setTimeout(() => {
        document.querySelector('.table-container').scrollIntoView({ 
          behavior: 'smooth',
          block: 'start' 
        });
      }, 500);
    }

    function viewCallHistory() {
      showToast('📊 Membuka panel analisis riwayat panggilan...', 'info');
      setTimeout(() => {
        showToast('📈 Data riwayat panggilan siap ditampilkan!', 'success');
      }, 1500);
    }

    function generateReport() {
      showToast('📊 Generating laporan analytics komprehensif...', 'info');
      showLoading(true);
      
      setTimeout(() => {
        showLoading(false);
        showToast('📄 Laporan analytics berhasil dibuat dan siap diunduh!', 'success');
      }, 3000);
    }

    // Refresh functions
    async function refreshUncontactedData() {
      showToast('🔄 Memperbarui data leads yang belum dihubungi...', 'info');
      await loadUncontactedData();
      showToast('✅ Data leads berhasil diperbarui!', 'success');
    }

    async function refreshFollowupData() {
      showToast('🔄 Memperbarui data hasil follow-up...', 'info');
      await loadFollowupData();
      if (followupChart) {
        const data = dummyData.followup;
        followupChart.data.datasets[0].data = [
          data.interested + Math.floor(Math.random() * 10) - 5,
          data.notInterested + Math.floor(Math.random() * 8) - 4,
          data.converted + Math.floor(Math.random() * 6) - 3,
          data.successful - data.interested - data.converted + Math.floor(Math.random() * 5) - 2
        ];
        followupChart.update();
      }
      showToast('✅ Data follow-up berhasil diperbarui!', 'success');
    }

    async function refreshActivityData() {
      showToast('🔄 Memperbarui statistik aktivitas...', 'info');
      await loadActivityData();
      
      if (activityChart) {
        const newData = dummyData.activity.weeklyData.datasets.map(dataset => ({
          ...dataset,
          data: dataset.data.map(value => Math.max(0, value + Math.floor(Math.random() * 20) - 10))
        }));
        activityChart.data.datasets = newData;
        activityChart.update();
      }
      showToast('✅ Statistik aktivitas berhasil diperbarui!', 'success');
    }

    async function refreshAssignedData() {
      showToast('🔄 Memperbarui data leads yang di-assign...', 'info');
      await loadAssignedData();
      showToast('✅ Data leads assign berhasil diperbarui!', 'success');
    }

    async function refreshLeadsTable() {
      showToast('🔄 Memperbarui tabel leads...', 'info');
      showLoading(true);
      await sleep(1500);
      
      // Add some variation to the data
      leadsData.forEach(lead => {
        if (Math.random() > 0.8) {
          lead.daysSince += 1;
        }
      });
      
      await loadLeadsTable();
      showLoading(false);
      showToast('✅ Tabel leads berhasil diperbarui!', 'success');
    }

    async function refreshAllData() {
      showToast('🔄 Memperbarui semua data dashboard...', 'info');
      showLoading(true);
      
      try {
        await loadHeaderData();
        await loadUncontactedData();
        await loadFollowupData();
        await loadActivityData();
        await loadAssignedData();
        await refreshLeadsTable();
        
        showToast('✅ Semua data berhasil diperbarui!', 'success');
      } catch (error) {
        showToast('❌ Gagal memperbarui data', 'danger');
      } finally {
        showLoading(false);
      }
    }

    async function refreshRandomData() {
      const refreshFunctions = [
        refreshUncontactedData,
        refreshFollowupData, 
        refreshActivityData,
        refreshAssignedData
      ];
      
      // Randomly refresh 1-2 sections
      const toRefresh = refreshFunctions.sort(() => 0.5 - Math.random()).slice(0, Math.floor(Math.random() * 2) + 1);
      
      for (const refreshFunc of toRefresh) {
        await refreshFunc();
        await sleep(500);
      }
    }

    // Chart toggle functions
    function toggleFollowupChartType() {
      if (followupChart) {
        followupChartType = followupChartType === 'doughnut' ? 'bar' : 'doughnut';
        followupChart.config.type = followupChartType;
        
        if (followupChartType === 'bar') {
          followupChart.config.options.plugins.legend.position = 'top';
          delete followupChart.config.data.datasets[0].cutout;
          followupChart.config.data.datasets[0].borderRadius = 8;
        } else {
          followupChart.config.options.plugins.legend.position = 'bottom';
          followupChart.config.data.datasets[0].cutout = '60%';
          delete followupChart.config.data.datasets[0].borderRadius;
        }
        
        document.getElementById('followupChartTypeBtn').textContent = 
          followupChartType === 'doughnut' ? 'Bar Chart' : 'Doughnut Chart';
        followupChart.update();
      }
    }

    function toggleActivityChartType() {
      if (activityChart) {
        activityChartType = activityChartType === 'bar' ? 'line' : 'bar';
        activityChart.config.type = activityChartType;
        
        if (activityChartType === 'line') {
          activityChart.data.datasets.forEach(dataset => {
            dataset.fill = false;
            dataset.tension = 0.4;
            delete dataset.borderRadius;
            delete dataset.borderSkipped;
          });
        } else {
          activityChart.data.datasets.forEach(dataset => {
            delete dataset.fill;
            delete dataset.tension;
            dataset.borderRadius = 8;
            dataset.borderSkipped = false;
          });
        }
        
        document.getElementById('activityChartTypeBtn').textContent = 
          activityChartType === 'bar' ? 'Line Chart' : 'Bar Chart';
        activityChart.update();
      }
    }

    // Export functions
    function exportFollowupChart() {
      showToast('📊 Mengekspor grafik follow-up...', 'info');
      setTimeout(() => {
        showToast('✅ Grafik follow-up berhasil diekspor ke PDF!', 'success');
      }, 1500);
    }

    function exportFollowupReport() {
      showToast('📄 Mengekspor laporan follow-up lengkap...', 'info');
      setTimeout(() => {
        showToast('✅ Laporan follow-up berhasil diekspor ke Excel!', 'success');
      }, 2000);
    }

    function exportActivityChart() {
      showToast('📊 Mengekspor grafik aktivitas...', 'info');
      setTimeout(() => {
        showToast('✅ Grafik aktivitas berhasil diekspor ke PNG!', 'success');
      }, 1500);
    }

    function exportActivityData() {
      showToast('📊 Mengekspor data aktivitas...', 'info');
      setTimeout(() => {
        showToast('✅ Data aktivitas berhasil diekspor ke CSV!', 'success');
      }, 1800);
    }

    function exportLeadsData() {
      const selectedLeads = document.querySelectorAll('.lead-checkbox:checked');
      const count = selectedLeads.length || filteredLeadsData.length;
      
      showToast(`📄 Mengekspor ${count} data leads...`, 'info');
      setTimeout(() => {
        showToast(`✅ ${count} data leads berhasil diekspor ke Excel!`, 'success');
      }, 2000);
    }

    // Lead action functions
    function callLead(id) {
      const lead = leadsData.find(l => l.id === id);
      if (!lead) return;
      
      showToast(`📞 Memanggil ${lead.name} - ${lead.phone}`, 'info');
      
      // Simulate realistic call scenarios
      setTimeout(() => {
        const scenarios = [
          { 
            message: `✅ Panggilan berhasil! ${lead.name} tertarik dengan ${lead.category}`, 
            type: 'success',
            followUp: `🎯 ${lead.name} akan survey lokasi minggu depan`
          },
          { 
            message: `📧 ${lead.name} tidak mengangkat - voicemail terkirim`, 
            type: 'warning',
            followUp: `⏰ Akan mencoba lagi dalam 2 jam`
          },
          { 
            message: `🔄 Nomor ${lead.name} sibuk - akan dicoba kembali`, 
            type: 'info',
            followUp: `📅 Dijadwalkan ulang untuk sore ini`
          },
          { 
            message: `🎉 Hot lead! ${lead.name} siap untuk closing`, 
            type: 'success',
            followUp: `💰 Estimasi closing: ${lead.category} senilai 500jt`
          }
        ];
        
        const scenario = scenarios[Math.floor(Math.random() * scenarios.length)];
        showToast(scenario.message, scenario.type);
        
        // Show follow-up message
        setTimeout(() => {
          showToast(scenario.followUp, 'info');
        }, 2000);
        
      }, Math.random() * 2000 + 1000); // Random delay 1-3 seconds
    }

    function whatsappLead(id) {
      const lead = leadsData.find(l => l.id === id);
      if (!lead) return;
      
      showToast(`💬 Membuka WhatsApp untuk ${lead.name}`, 'success');
      
      // Simulate WhatsApp scenarios
      setTimeout(() => {
        const messages = [
          `✅ Pesan WhatsApp terkirim ke ${lead.name}`,
          `🔄 ${lead.name} sedang mengetik balasan...`,
          `💬 Template message untuk ${lead.category} terkirim`,
          `📎 Brosur digital terkirim ke ${lead.name}`
        ];
        
        const message = messages[Math.floor(Math.random() * messages.length)];
        showToast(message, 'info');
      }, 1500);
    }

    function viewLeadDetail(id) {
      const lead = leadsData.find(l => l.id === id);
      if (!lead) return;
      
      showToast(`👁️ Membuka detail lengkap ${lead.name}`, 'info');
      
      setTimeout(() => {
        const details = [
          `📊 Riwayat interaksi: ${Math.floor(Math.random() * 10) + 1} kali kontak`,
          `💰 Estimasi budget: ${lead.category} hingga ${Math.floor(Math.random() * 500) + 200}jt`,
          `📍 Area interest: ${['Jakarta Selatan', 'Tangerang', 'Bekasi', 'Bogor'][Math.floor(Math.random() * 4)]}`,
          `⭐ Lead score: ${Math.floor(Math.random() * 40) + 60}/100 - ${lead.priority === 'high' ? 'Hot Lead' : 'Warm Lead'}`
        ];
        
        const detail = details[Math.floor(Math.random() * details.length)];
        showToast(detail, 'info');
      }, 1000);
    }

    function scheduleLead(id) {
      const lead = leadsData.find(l => l.id === id);
      if (!lead) return;
      
      showToast(`📅 Membuka penjadwal untuk ${lead.name}`, 'info');
      
      setTimeout(() => {
        const schedules = [
          `⏰ ${lead.name} dijadwalkan follow-up besok jam 10:00`,
          `📞 Reminder call untuk ${lead.name} diset hari Rabu`,
          `🏠 Survey lokasi dengan ${lead.name} dijadwalkan weekend`,
          `💼 Meeting presentasi dengan ${lead.name} hari Jumat`
        ];
        
        const schedule = schedules[Math.floor(Math.random() * schedules.length)];
        showToast(schedule, 'success');
      }, 1500);
    }

    // Bulk actions
    function bulkAction() {
      const selectedLeads = document.querySelectorAll('.lead-checkbox:checked');
      
      if (selectedLeads.length === 0) {
        showToast('⚠️ Pilih minimal satu lead untuk bulk action!', 'warning');
        return;
      }
      
      const count = selectedLeads.length;
      showToast(`🔄 Memproses bulk action untuk ${count} leads...`, 'info');
      
      setTimeout(() => {
        const actions = [
          `📞 ${count} leads ditambahkan ke queue panggilan`,
          `📧 Email follow-up terkirim ke ${count} leads`,  
          `📋 Status ${count} leads diupdate ke "In Progress"`,
          `👥 ${count} leads di-assign ulang ke available agents`
        ];
        
        const action = actions[Math.floor(Math.random() * actions.length)];
        showToast(`✅ ${action}`, 'success');
      }, 2000);
    }

    function prioritizeLeads() {
      showToast('🔄 Menganalisis dan memprioritaskan leads...', 'info');
      
      setTimeout(() => {
        // Simulate lead prioritization
        leadsData.sort((a, b) => {
          const priorityOrder = { high: 3, medium: 2, low: 1 };
          if (priorityOrder[a.priority] !== priorityOrder[b.priority]) {
            return priorityOrder[b.priority] - priorityOrder[a.priority];
          }
          return b.daysSince - a.daysSince; // Then by days since assigned
        });
        
        filteredLeadsData = [...leadsData];
        currentPage = 1;
        renderLeadsTable();
        updatePagination();
        
        showToast('✅ Leads berhasil diprioritaskan berdasarkan urgency dan potensi!', 'success');
      }, 2000);
    }

    function assignLeads() {
      showToast('👥 Membuka panel assign leads ke agents...', 'info');
      
      setTimeout(() => {
        const newAssigns = Math.floor(Math.random() * 15) + 5;
        showToast(`✅ ${newAssigns} leads baru berhasil di-assign ke available agents!`, 'success');
        
        // Update the assigned count
        setTimeout(async () => {
          await loadAssignedData();
        }, 1000);
      }, 2500);
    }

    function viewDetailedActivity() {
      showToast('📊 Membuka analisis detail aktivitas...', 'info');
      
      setTimeout(() => {
        const insights = [
          '🎯 Peak time panggilan: 10:00-12:00 & 14:00-16:00',
          '📈 Success rate tertinggi: Selasa & Kamis (72%)',
          '⭐ Best performer: Sarah Lestari (85% conversion)',
          '🔥 Hot category: KPR Syariah (67% interest rate)'
        ];
        
        const insight = insights[Math.floor(Math.random() * insights.length)];
        showToast(insight, 'info');
      }, 1500);
    }

    // Toast notification system
    function showToast(message, type = 'info') {
      const colors = {
        success: { border: 'var(--success-color)', icon: '✅' },
        info: { border: 'var(--info-color)', icon: 'ℹ️' },
        warning: { border: 'var(--warning-color)', icon: '⚠️' },
        danger: { border: 'var(--danger-color)', icon: '❌' }
      };

      const config = colors[type] || colors.info;
      const toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      toast.style.borderColor = config.border;

      toast.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
          <span style="font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;">${config.icon}</span>
          <div style="flex: 1;">
            <div style="font-weight: 700; margin-bottom: 4px; text-transform: capitalize; color: var(--text-primary);">
              ${type}
            </div>
            <div style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.4;">
              ${message}
            </div>
          </div>
          <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.25rem; padding: 0; flex-shrink: 0;">
            ×
          </button>
        </div>
      `;

      document.body.appendChild(toast);

      // Show animation
      setTimeout(() => toast.classList.add('show'), 100);

      // Auto remove after 5 seconds
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast);
          }
        }, 300);
      }, 5000);

      // Click to dismiss
      toast.addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => {
          if (document.body.contains(toast)) {
            document.body.removeChild(toast);
          }
        }, 300);
      });
    }

    // Simulated real-time updates
    function startRealtimeUpdates() {
      setInterval(() => {
        if (Math.random() > 0.85) {
          const notifications = [
            { message: '🆕 Lead baru masuk dari website - KPR Syariah', type: 'info' },
            { message: '📞 Panggilan masuk dari existing customer', type: 'success' },
            { message: '⏰ Reminder: 5 leads perlu follow-up dalam 1 jam', type: 'warning' },
            { message: '🎉 Closing berhasil! Maya Sari - Investasi Properti 450jt', type: 'success' },
            { message: '🔔 Agent Sarah mencapai target harian!', type: 'success' },
            { message: '📊 Weekly target tercapai 78% - keep going!', type: 'info' }
          ];
          
          const notif = notifications[Math.floor(Math.random() * notifications.length)];
          showToast(notif.message, notif.type);
        }
      }, 30000); // Every 30 seconds
    }

    // Start real-time updates after dashboard loads
    setTimeout(startRealtimeUpdates, 5000);

    // Performance monitoring
    function trackPerformance() {
      console.log('📊 Dashboard Performance Metrics:');
      console.log(`• Total leads loaded: ${leadsData.length}`);
      console.log(`• Filtered results: ${filteredLeadsData.length}`);
      console.log(`• Current page: ${currentPage}/${Math.ceil(filteredLeadsData.length / leadsPerPage)}`);
      console.log(`• Charts initialized: ${followupChart && activityChart ? 'Yes' : 'No'}`);
    }

    // Expose functions for debugging
    window.dashboardDebug = {
      refreshAllData,
      trackPerformance,
      showToast,
      leadsData: () => leadsData,
      dummyData: () => dummyData
    };

    // Welcome message
    setTimeout(() => {
      showToast('🎉 Selamat datang di Dashboard Telesales! Semua sistem siap digunakan.', 'success');
    }, 1000);

    // Keyboard shortcuts help
    setTimeout(() => {
      if (Math.random() > 0.7) {
        showToast('💡 Tips: Gunakan Ctrl+R untuk refresh, Ctrl+F untuk pencarian cepat', 'info');
      }
    }, 10000);
  </script>
@endsection