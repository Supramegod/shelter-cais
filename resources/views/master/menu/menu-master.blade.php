@extends('layouts.master')
@section('title', 'Menu Master')
@section('content')
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #10b981;
            --accent: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --card-bg: #ffffff;
            --card-border: rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            /* Latar belakang putih/abu-abu terang */
            min-height: 100vh;
            padding: 2rem 1rem;
            color: var(--dark);
            /* Warna teks utama menjadi gelap */
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(79, 70, 229, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.5) 0%, transparent 40%);
            z-index: -1;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 2rem;
            /* Menambahkan padding di bagian atas */
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.5px;
            color: var(--dark);
            display: inline-block;
        }

        .header p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-container {
            max-width: 500px;
            margin: 0 auto 3rem auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: var(--card-bg);
            color: var(--dark);
            font-size: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }

        .search-input::placeholder {
            color: var(--gray);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.5rem;
        }

        .cards-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
            align-items: flex-start;
        }

        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--card-border);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            height: auto;
            display: flex;
            flex-direction: column;
            /* Menyesuaikan lebar kartu agar pas 3 kolom */
            flex: 1 1 calc(33.333% - 2rem);
            min-width: 300px;
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow:
                0 15px 35px rgba(0, 0, 0, 0.1),
                0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 2rem 1.8rem 1.8rem;
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .card-icon {
            font-size: 2.8rem;
            margin-right: 1.2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title-container {
            flex: 1;
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
            color: var(--dark);
        }

        .card-badge {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .card-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.215, 0.610, 0.355, 1);
        }

        .card.expanded .card-content {
            max-height: 500px;
        }

        .submenu-list {
            padding: 0 1.8rem 1.8rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            padding: 16px 18px;
            border-radius: 14px;
            background: rgba(0, 0, 0, 0.03);
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .submenu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
            transition: left 0.7s ease;
        }

        .submenu-item:hover::before {
            left: 100%;
        }

        .submenu-item:hover {
            background: rgba(0, 0, 0, 0.05);
            transform: translateX(8px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .submenu-icon {
            margin-right: 14px;
            font-size: 1.4rem;
            width: 24px;
            text-align: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
        }

        .submenu-item:hover .submenu-icon {
            transform: scale(1.2);
        }

        .submenu-text {
            font-weight: 500;
            flex: 1;
            font-size: 0.95rem;
        }

        .toggle-indicator {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            color: var(--gray);
            font-size: 1.5rem;
        }

        .card.expanded .toggle-indicator {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            opacity: 0;
            animation: fadeInUp 0.6s forwards;
        }

        .card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .card:nth-child(6) {
            animation-delay: 0.6s;
        }

        .card:nth-child(7) {
            animation-delay: 0.7s;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .card {
                flex: 1 1 calc(50% - 2rem);
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.2rem;
            }

            .cards-grid {
                gap: 1.5rem;
            }

            .card {
                flex: 1 1 100%;
                min-width: unset;
            }

            .card-header {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1.5rem 0.8rem;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .header p {
                font-size: 1rem;
            }

            .search-input {
                padding: 14px 45px 14px 16px;
            }

            .card-icon {
                font-size: 2rem;
            }

            .card-title {
                font-size: 1.2rem;
            }

            .submenu-item {
                padding: 12px 14px;
            }

            .submenu-text {
                font-size: 0.9rem;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="header">
            <h1>Master Menu </h1>
            <p>Kelola semua data master</p>
        </div>
        <div class="cards-grid">
            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-database card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Data</h3>
                        <span class="card-badge">3 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('tim-sales') }}" class="submenu-item">
                            <i class="mdi mdi-account-group submenu-icon"></i>
                            <span class="submenu-text">Tim Sales</span>
                        </a>
                        <a href="{{ route('kebutuhan') }}" class="submenu-item">
                            <i class="mdi mdi-clipboard-list submenu-icon"></i>
                            <span class="submenu-text">Kebutuhan</span>
                        </a>
                        <a href="{{ route('training') }}" class="submenu-item">
                            <i class="mdi mdi-school submenu-icon"></i>
                            <span class="submenu-text">Training</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-package-variant-closed card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Barang</h3>
                        <span class="card-badge">5 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('barang') }}" class="submenu-item">
                            <i class="mdi mdi-package-variant submenu-icon"></i>
                            <span class="submenu-text">Semua Barang</span>
                        </a>
                        <a href="{{ route('barang.kaporlap') }}" class="submenu-item">
                            <i class="mdi mdi-shield-check submenu-icon"></i>
                            <span class="submenu-text">Kaporlap</span>
                        </a>
                        <a href="{{ route('barang.devices') }}" class="submenu-item">
                            <i class="mdi mdi-cellphone submenu-icon"></i>
                            <span class="submenu-text">Devices</span>
                        </a>
                        <a href="{{ route('barang.ohc') }}" class="submenu-item">
                            <i class="mdi mdi-medical-bag submenu-icon"></i>
                            <span class="submenu-text">OHC</span>
                        </a>
                        <a href="{{ route('barang.chemical') }}" class="submenu-item">
                            <i class="mdi mdi-flask submenu-icon"></i>
                            <span class="submenu-text">Chemical</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-truck card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Supplier</h3>
                        <span class="card-badge">3 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('supplier') }}" class="submenu-item">
                            <i class="mdi mdi-format-list-bulleted submenu-icon"></i>
                            <span class="submenu-text">Semua Supplier</span>
                        </a>
                        <a href="{{ route('supplier.Gr') }}" class="submenu-item">
                            <i class="mdi mdi-check-circle submenu-icon"></i>
                            <span class="submenu-text">Good Receipt</span>
                        </a>
                        <a href="{{ route('supplier.Rn') }}" class="submenu-item">
                            <i class="mdi mdi-clipboard-check submenu-icon"></i>
                            <span class="submenu-text">Receiving Notes</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-school-outline card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Training SDT</h3>
                        <span class="card-badge">5 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('training-materi') }}" class="submenu-item">
                            <i class="mdi mdi-book-open submenu-icon"></i>
                            <span class="submenu-text">Training Materi</span>
                        </a>
                        <a href="{{ route('training-divisi') }}" class="submenu-item">
                            <i class="mdi mdi-sitemap submenu-icon"></i>
                            <span class="submenu-text">Training Divisi</span>
                        </a>
                        <a href="{{ route('training-trainer') }}" class="submenu-item">
                            <i class="mdi mdi-account-tie submenu-icon"></i>
                            <span class="submenu-text">Training Trainer</span>
                        </a>
                        <a href="{{ route('training-area') }}" class="submenu-item">
                            <i class="mdi mdi-map-marker submenu-icon"></i>
                            <span class="submenu-text">Training Area</span>
                        </a>
                        <a href="{{ route('training-client') }}" class="submenu-item">
                            <i class="mdi mdi-account-box submenu-icon"></i>
                            <span class="submenu-text">Training Client</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-currency-usd card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Keuangan</h3>
                        <span class="card-badge">6 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('management-fee') }}" class="submenu-item">
                            <i class="mdi mdi-percent submenu-icon"></i>
                            <span class="submenu-text">Management Fee</span>
                        </a>
                        <a href="{{ route('top') }}" class="submenu-item">
                            <i class="mdi mdi-clock-outline submenu-icon"></i>
                            <span class="submenu-text">TOP</span>
                        </a>
                        <a href="{{ route('salary-rule') }}" class="submenu-item">
                            <i class="mdi mdi-calculator submenu-icon"></i>
                            <span class="submenu-text">Salary Rule</span>
                        </a>
                        <a href="{{ route('tunjangan') }}" class="submenu-item">
                            <i class="mdi mdi-gift submenu-icon"></i>
                            <span class="submenu-text">Tunjangan</span>
                        </a>
                        <a href="{{ route('ump') }}" class="submenu-item">
                            <i class="mdi mdi-chart-line submenu-icon"></i>
                            <span class="submenu-text">UMP</span>
                        </a>
                        <a href="{{ route('umk') }}" class="submenu-item">
                            <i class="mdi mdi-chart-bar submenu-icon"></i>
                            <span class="submenu-text">UMK</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" onclick="toggleCard(this.parentNode)">
                    <i class="mdi mdi-domain card-icon"></i>
                    <div class="card-title-container">
                        <h3 class="card-title">Master Lainnya</h3>
                        <span class="card-badge">2 Submenu</span>
                    </div>
                    <i class="mdi mdi-chevron-down toggle-indicator"></i>
                </div>
                <div class="card-content">
                    <div class="submenu-list">
                        <a href="{{ route('bidang-perusahaan') }}" class="submenu-item">
                            <i class="mdi mdi-office-building submenu-icon"></i>
                            <span class="submenu-text">Bidang Perusahaan</span>
                        </a>
                        <a href="{{ route('mutasi-stok') }}" class="submenu-item">
                            <i class="mdi mdi-swap-horizontal submenu-icon"></i>
                            <span class="submenu-text">Stok Barang</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCard(card) {
            // Logika sederhana untuk membuka/menutup kartu yang diklik
            card.classList.toggle('expanded');
        }

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.card');

            cards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const submenuItems = card.querySelectorAll('.submenu-text');
                let hasMatch = title.includes(searchTerm);

                // Check if any submenu item matches
                if (!hasMatch) {
                    submenuItems.forEach(item => {
                        if (item.textContent.toLowerCase().includes(searchTerm)) {
                            hasMatch = true;
                        }
                    });
                }

                // Show or hide card based on search
                if (hasMatch || searchTerm === '') {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                // Close all cards when escape is pressed
                document.querySelectorAll('.card').forEach(card => {
                    card.classList.remove('expanded');
                });
            }
        });
    </script>
@endsection