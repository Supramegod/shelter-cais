@extends('layouts.master')
@section('title', 'Customer Activity')
@section('pageStyle')
    <style>
        /* Custom Activity Feed Styles */
        .activity-feed {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-feed .feed-item {
            position: relative;
            padding-bottom: 20px;
            padding-left: 30px;
            border-left: 2px solid #e9ecef;
            margin-bottom: 30px;
        }

        .activity-feed .feed-item:last-child {
            border-left: 2px solid transparent;
        }

        .activity-feed .feed-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -7px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #3b7ddd;
        }

        .feed-item .feed-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .feed-item .feed-content:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .feed-item .feed-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .feed-header {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }


        .filter-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .feed-title {
            font-size: 1.2rem;
            margin-bottom: 0;
        }

        .feed-time {
            font-size: 0.9rem;
            padding: 5px 12px;
        }

        .feed-body {
            margin-top: 15px;
            padding-top: 15px;
        }

        .btn-toggle-detail,
        .btn-view-full {
            margin-top: 10px;
        }

        .feed-item .feed-title {
            flex: 1;
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        .feed-item .feed-time {
            font-size: 0.85rem;
            color: #7e8c9a;
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .feed-item .feed-body {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .feed-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .feed-info-item {
            display: flex;
            flex-direction: column;
        }

        .feed-info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .feed-info-value {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .feed-detail {
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .feed-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .feed-detail-item {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .feed-detail-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .feed-detail-value {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .btn-toggle-detail {
            position: relative;
            padding: 6px 15px 6px 35px;
            background: #f1f5f9;
            border: none;
            color: #3b7ddd;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-toggle-detail:hover {
            background: #e2e8f0;
            color: #2c6dca;
        }

        .btn-toggle-detail i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }

        .btn-toggle-detail.collapsed i {
            transform: translateY(-50%) rotate(-90deg);
        }

        .btn-view-full {
            background: #3b7ddd;
            color: white;
            font-weight: 500;
            padding: 6px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-view-full:hover {
            background: #2c6dca;
            color: white;
        }

        .badge-custom {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
    </style>
@endsection
@section('content')
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Row -->
        <div class="row row-sm mt-2">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-md-row align-items-center justify-content-between py-3">
                        <div class="mb-3 mb-md-0">
                            <h3 class="page-title mb-1">Customer Activity</h3>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Customer Activity</li>
                            </ol>
                        </div>
                        <a href="{{ route('customer-activity.add') }}"
                            class="btn btn-label-primary waves-effect waves-light">
                            <i class="mdi mdi-plus me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Tambah Customer Activity</span>
                        </a>
                    </div>
                    <div class="card-body pt-4">
                        <!-- Filter Section -->
                        <!-- Ganti bagian filter dengan layout grid yang lebih baik -->
                        <div class="filter-section">
                            <form action="{{route('customer-activity')}}" method="GET">
                                <div class="row g-3">
                                    <!-- Baris Pertama: 3 filter per baris -->
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari"
                                                value="{{$tglDari}}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai"
                                                value="{{$tglSampai}}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="branch" name="branch">
                                                <option value="">- Semua Wilayah -</option>
                                                @foreach($branch as $data)
                                                    <option value="{{$data->id}}" @if($request->branch == $data->id) selected
                                                    @endif>{{$data->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="branch">Wilayah</label>
                                        </div>
                                    </div>

                                    <!-- Baris Kedua: 3 filter per baris -->
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="user" name="user">
                                                <option value="">- Semua User -</option>
                                                @foreach($listUser as $data)
                                                    <option value="{{$data->id}}" @if($request->user == $data->id) selected
                                                    @endif>{{$data->full_name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="company">User</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="kebutuhan" name="kebutuhan">
                                                <option value="">- Semua Kebutuhan -</option>
                                                @foreach($kebutuhan as $data)
                                                    <option value="{{$data->id}}" @if($request->kebutuhan == $data->id) selected
                                                    @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="kebutuhan">Kebutuhan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="tipe" name="tipe">
                                                <option value="">- Semua Tipe -</option>
                                                @foreach($tipe as $data)
                                                    <option value="{{$data->tipe}}" @if($request->tipe == $data->tipe) selected
                                                    @endif>{{$data->tipe}}</option>
                                                @endforeach
                                            </select>
                                            <label for="tipe">Tipe Aktivitas</label>
                                        </div>
                                    </div>

                                    <!-- Baris Ketiga: Tombol filter -->
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-lg btn-primary waves-effect waves-light">
                                            <i class="mdi mdi-filter me-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Activity Feed Section -->
                        <div class="container mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0">Activity Feed</h3>
                                <div class="text-muted">
                                    Menampilkan <span id="activity-count">0</span> aktivitas
                                </div>
                            </div>

                            <div id="loading-indicator" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat aktivitas...</p>
                            </div>

                            <div id="activity-container">
                                <ul id="activity-feed" class="activity-feed" style="display: none;"></ul>
                            </div>

                            <div class="text-center my-4">
                                <button id="loadMore" class="btn btn-primary btn-load-more">
                                    <i class="mdi mdi-reload me-1"></i> Muat Lebih Banyak
                                </button>
                            </div>

                            <div id="no-results" class="text-center py-5" style="display: none;">
                                <i class="mdi mdi-inbox-remove-outline display-4 text-muted mb-3"></i>
                                <h4 class="text-muted">Tidak ada aktivitas ditemukan</h4>
                                <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!--/ Content -->
@endsection

@section('pageScript')
    <script>
        @if(isset($success) || session()->has('success'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$success}} {{session()->get('success')}}',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if(isset($error) || session()->has('error'))
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$error}} {{session()->has('error')}}',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

            function formatDate(dateString) {
                if (!dateString) return '-';
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

        function timeAgo(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            const intervals = {
                tahun: 31536000,
                bulan: 2592000,
                minggu: 604800,
                hari: 86400,
                jam: 3600,
                menit: 60
            };

            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                if (interval >= 1) {
                    return `${interval} ${unit} yang lalu`;
                }
            }

            return 'Baru saja';
        }

        function formatDetail(item) {
            let html = `<div class="feed-detail-grid">`;

            // Status Leads
            // if (item.status_leads) {
            //     html += `
            //     <div class="feed-detail-item">
            //         <div class="feed-detail-label">Status Leads</div>
            //         <div class="feed-detail-value">${item.status_leads}</div>
            //     </div>`;
            // }

            // Tanggal dan Jam
            // if (item.tgl_activity) {
            //     html += `
            //     <div class="feed-detail-item">
            //         <div class="feed-detail-label">Tanggal Aktivitas</div>
            //         <div class="feed-detail-value">${formatDate(item.tgl_activity)}</div>
            //     </div>`;
            // }

            if (item.tgl_realisasi) {
                html += `
                                                        <div class="feed-detail-item">
                                                            <div class="feed-detail-label">Tanggal Realisasi</div>
                                                            <div class="feed-detail-value">${formatDate(item.tgl_realisasi)}</div>
                                                        </div>`;
            }

            if (item.jam_realisasi) {
                html += `
                                                        <div class="feed-detail-item">
                                                            <div class="feed-detail-label">Jam Realisasi</div>
                                                            <div class="feed-detail-value">${item.jam_realisasi}</div>
                                                        </div>`;
            }

            // Durasi
            if (item.start || item.end || item.durasi) {
                html += `<div class="feed-detail-item">`;

                if (item.start) {
                    html += `
                                                            <div class="feed-detail-label">Start</div>
                                                            <div class="feed-detail-value">${item.start}</div>`;
                }

                if (item.end) {
                    html += `
                                                            <div class="feed-detail-label">End</div>
                                                            <div class="feed-detail-value">${item.end}</div>`;
                }

                if (item.durasi) {
                    html += `
                                                            <div class="feed-detail-label">Durasi</div>
                                                            <div class="feed-detail-value">${item.durasi}</div>`;
                }

                html += `</div>`;
            }

            // Informasi Kontak
            if (item.penerima || item.email) {
                html += `<div class="feed-detail-item">`;

                if (item.penerima) {
                    html += `
                                                            <div class="feed-detail-label">Penerima</div>
                                                            <div class="feed-detail-value">${item.penerima}</div>`;
                }

                if (item.email) {
                    html += `
                                                            <div class="feed-detail-label">Email</div>
                                                            <div class="feed-detail-value">${item.email}</div>`;
                }

                html += `</div>`;
            }

            // Jenis dan Notulen
            if (item.jenis_visit || item.notulen) {
                html += `<div class="feed-detail-item">`;

                if (item.jenis_visit) {
                    html += `
                                                            <div class="feed-detail-label">Jenis Visit</div>
                                                            <div class="feed-detail-value">${item.jenis_visit}</div>`;
                }

                // if (item.notulen) {
                //     html += `
                //     <div class="feed-detail-label">Notulen</div>
                //     <div class="feed-detail-value">${item.notulen}</div>`;
                // }

                html += `</div>`;
            }

            // Bukti Foto
            if (item.link_bukti_foto) {
                html += `
                                                        <div class="feed-detail-item">
                                                            <div class="feed-detail-label">Bukti Foto</div>
                                                            <div class="feed-detail-value">
                                                                <a href="${item.link_bukti_foto}" target="_blank" 
                                                                   class="btn btn-sm btn-info waves-effect">
                                                                    <i class="mdi mdi-magnify me-1"></i> Lihat Bukti
                                                                </a>
                                                            </div>
                                                        </div>`;
            }

            // Notes Tipe
            // if (item.notes_tipe) {
            //     html += `
            //     <div class="feed-detail-item">
            //         <div class="feed-detail-label">Catatan Tambahan</div>
            //         <div class="feed-detail-value">${item.notes_tipe}</div>
            //     </div>`;
            // }

            html += `</div>`;
            return html;
        }

        $(document).ready(function () {
            const perPage = 10;
            let currentPage = 1;
            let totalActivities = 0;
            let isLoading = false;

            function loadActivityFeed() {
                if (isLoading) return;
                isLoading = true;

                $('#loading-indicator').show();
                $('#loadMore').prop('disabled', true);

                // Ambil nilai filter
                const filterData = {
                    page: currentPage,
                    tgl_dari: $('#tgl_dari').val(),
                    tgl_sampai: $('#tgl_sampai').val(),
                    branch: $('#branch').val(),
                    user: $('#user').val(),
                    kebutuhan: $('#kebutuhan').val(),
                    tipe: $('#tipe').val() // Tambahkan parameter ini
                };

                $.ajax({
                    url: '/sales/customer-activity/ajax',
                    method: 'GET',
                    data: filterData,
                    success: function (data) {
                        $('#loading-indicator').hide();
                        $('#activity-feed').show();

                        if (data.length === 0 && currentPage === 1) {
                            $('#no-results').show();
                            $('#loadMore').hide();
                            return;
                        } else {
                            $('#no-results').hide();
                        }

                        totalActivities += data.length;
                        $('#activity-count').text(totalActivities);

                        data.forEach(item => {
                            const tgl = formatDate(item.tgl_activity);
                            const timeAgoText = timeAgo(item.created_at);
                            const tipeBadge = item.tipe ?
                                `<span class="badge bg-info text-white badge-custom">${item.tipe}</span>` :
                                '-';

                            const statusBadge = item.status_leads ?
                                `<span class="badge badge-custom" style="background-color: ${item.warna_background}; color: ${item.warna_font}">
                                                                            ${item.status_leads}
                                                                        </span>` :
                                '-';

                            // Di dalam function yang membuat feed item
                            const feedItem = `
    <li class="feed-item">
        <div class="feed-content">
            <div class="feed-header">
                <h4 class="feed-title">
                    <strong>${item.nomor}</strong> - ${item.sales || 'Tidak ada sales'}
                </h4>
                <span class="feed-time">${timeAgoText}</span>
            </div>

            <div class="feed-body">
                <div class="feed-info-grid">
                    <div class="feed-info-item">
                        <span class="feed-info-label">Nama Perusahaan</span>
                        <span class="feed-info-value">${item.nama}</span>
                    </div>
                    <div class="feed-info-item">
                        <span class="feed-info-label">Tanggal Aktivitas</span>
                        <span class="feed-info-value">${tgl}</span>
                    </div>
                    <div class="feed-info-item">
                        <span class="feed-info-label">Status Leads</span>
                        <span class="feed-info-value">${statusBadge}</span>
                    </div>

                    <div class="feed-info-item">
                        <span class="feed-info-label">Tipe Aktivitas</span>
                        <span class="feed-info-value">${tipeBadge}</span>
                    </div>
                    <div class="feed-info-item">
                        <span class="feed-info-label">Kebutuhan</span>
                        <span class="feed-info-value">${item.kebutuhan || '-'}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="feed-info-label">Keterangan</span>
                    <p class="feed-info-value">${item.keterangan || '-'}</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button class="btn-toggle-detail">
                        <i class="mdi mdi-chevron-down"></i> Detail
                    </button>
                    <a href="/sales/customer-activity/view/${item.id}" 
                       class="btn-view-full">
                        <i class="mdi mdi-eye-outline me-1"></i> Lihat Lengkap
                    </a>
                </div>

                <div class="feed-detail">
                    ${formatDetail(item)}
                </div>
            </div>
        </div>
    </li>`;

                            $('#activity-feed').append(feedItem);
                        });

                        currentPage++;
                        $('#loadMore').toggle(data.length >= perPage);
                    },
                    error: function (err) {
                        console.error('Error:', err);
                        $('#loading-indicator').hide();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal memuat data aktivitas',
                            icon: 'error'
                        });
                    },
                    complete: function () {
                        isLoading = false;
                        $('#loadMore').prop('disabled', false);
                    }
                });
            }

            // Load data awal
            loadActivityFeed();

            // Handle Load More
            $('#loadMore').on('click', loadActivityFeed);

            // Handle Filter Submit
            $('form').on('submit', function (e) {
                e.preventDefault();
                currentPage = 1;
                totalActivities = 0;
                $('#activity-feed').empty();
                $('#loadMore').show();
                loadActivityFeed();
            });

            // Event delegation for toggle buttons
            $(document).on('click', '.btn-toggle-detail', function () {
                const $btn = $(this);
                const $detail = $btn.closest('.feed-body').find('.feed-detail');

                $btn.toggleClass('collapsed');
                $detail.slideToggle(300);

                // Ganti teks tombol
                if ($btn.hasClass('collapsed')) {
                    $btn.html('<i class="mdi mdi-chevron-down"></i> Sembunyikan');
                } else {
                    $btn.html('<i class="mdi mdi-chevron-down"></i> Detail');
                }
            });
        });
    </script>
@endsection