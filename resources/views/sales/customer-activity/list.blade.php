@extends('layouts.master')
@section('title', 'Customer Activity')

@section('pageStyle')
    <style>
        /* Consolidate and corrected styles for the activity feed */
        .activity-feed {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feed-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .feed-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 15px;
        }

        .feed-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            flex: 1;
        }

        .feed-time {
            font-size: 0.85rem;
            color: #7e8c9a;
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .feed-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .feed-info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f2f3;
        }

        .feed-info-item:last-child {
            border-bottom: none;
        }

        .feed-info-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .feed-info-value {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
            text-align: right;
        }

        /* Styles for badges, buttons, etc. */
        .badge-custom {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .activity-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-toggle-detail {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #3b7ddd;
            font-weight: 500;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .btn-toggle-detail:hover {
            background: #e2e8f0;
            color: #2c6dca;
        }

        .btn-view-full {
            display: inline-flex;
            align-items: center;
            background: #3b7ddd;
            color: white !important;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .btn-view-full:hover {
            background: #2c6dca;
        }

        .feed-detail {
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            animation: fadeIn 0.3s ease;
        }

        /* .feed-detail-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 15px;
                } */
        .detail-item-horizontal {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            flex: 1;
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
            font-weight: 500;
        }

        .feed-detail-value {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.95rem;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row row-sm mt-2">
            <div class="col-lg-12">
                <div class="card">
                    <!-- Header -->
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

                    <!-- Filter -->
                    <div class="card-body pt-4">
                        <div class="filter-section">
                            <form id="filter-form" action="{{ route('customer-activity') }}" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari"
                                                value="{{ $tglDari }}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai"
                                                value="{{ $tglSampai }}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="branch" name="branch">
                                                <option value="">- Semua Wilayah -</option>
                                                @foreach($branch as $data)
                                                    <option value="{{ $data->id }}" @if($request->branch == $data->id) selected
                                                    @endif>
                                                        {{ $data->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="branch">Wilayah</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="user" name="user">
                                                <option value="">- Semua User -</option>
                                                @foreach($listUser as $data)
                                                    <option value="{{ $data->id }}" @if($request->user == $data->id) selected
                                                    @endif>
                                                        {{ $data->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="user">User</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="kebutuhan" name="kebutuhan">
                                                <option value="">- Semua Kebutuhan -</option>
                                                @foreach($kebutuhan as $data)
                                                    <option value="{{ $data->id }}" @if($request->kebutuhan == $data->id) selected
                                                    @endif>
                                                        {{ $data->nama }}
                                                    </option>
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
                                                    <option value="{{ $data->tipe }}" @if($request->tipe == $data->tipe) selected
                                                    @endif>
                                                        {{ $data->tipe }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="tipe">Tipe Aktivitas</label>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-lg btn-primary waves-effect waves-light me-2">
                                            <i class="mdi mdi-filter me-1"></i> Filter
                                        </button>
                                        <button type="button" id="reset-filter"
                                            class="btn btn-lg btn-outline-secondary waves-effect waves-light">
                                            <i class="mdi mdi-refresh me-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="container-fluid px-0">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                        <h4 class="mb-0">Activity Feed</h4>
                        <div class="text-muted">
                            Menampilkan <span id="activity-count">0</span> aktivitas
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading-indicator" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat aktivitas...</p>
                    </div>

                    <!-- Feed List -->
                    <div id="activity-container">
                        <ul id="activity-feed" class="list-unstyled">
                            {{-- contoh item activity, nanti diganti dengan loop/ajax --}}
                        </ul>
                    </div>

                    <!-- No Results -->
                    <div id="no-results" class="text-center py-5" style="display: none;">
                        <div class="mb-4">
                            <i class="mdi mdi-inbox-remove-outline" style="font-size: 4rem; color: #6c757d;"></i>
                        </div>
                        <h4 class="text-muted mb-2">Tidak ada aktivitas ditemukan</h4>
                        <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
                    </div>
                </div>

                <!-- Load More -->
                <div class="text-center my-4">
                    <button id="loadMore" class="btn btn-primary btn-load-more" style="display: none;">
                        <i class="mdi mdi-reload me-1"></i> Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                html: '{{$error}} {{session()->get('error')}}',
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
                tahun: 31536000, bulan: 2592000, minggu: 604800, hari: 86400, jam: 3600, menit: 60
            };
            for (const [unit, secondsInUnit] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / secondsInUnit);
                if (interval >= 1) {
                    return `${interval} ${unit} yang lalu`;
                }
            }
            return 'Baru saja';
        }

        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDetail(item) {
            // Menggunakan flexbox untuk tata letak horizontal
            let html = `
                        <div class="d-flex flex-wrap gap-3">
                    `;

            const details = [
                { label: 'Tanggal Realisasi', value: item.tgl_realisasi, format: formatDate },
                { label: 'Jam Realisasi', value: item.jam_realisasi },
                { label: 'Start', value: item.start },
                { label: 'End', value: item.end },
                { label: 'Durasi', value: item.durasi },
                { label: 'Penerima', value: item.penerima },
                { label: 'Email', value: item.email },
                { label: 'Jenis Visit', value: item.jenis_visit },
            ];

            details.forEach(detail => {
                if (detail.value) {
                    const value = detail.format
                        ? detail.format(detail.value)
                        : escapeHtml(detail.value);

                    html += `
                                <div class="detail-item-horizontal">
                                    <div class="feed-detail-label">${detail.label}</div>
                                    <div class="feed-detail-value">${value}</div>
                                </div>
                            `;
                }
            });

            if (item.link_bukti_foto) {
                html += `
                            <div class="detail-item-horizontal">
                                <div class="feed-detail-label">Bukti Foto</div>
                                <div class="feed-detail-value">
                                    <a href="${escapeHtml(item.link_bukti_foto)}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info waves-effect">
                                        <i class="mdi mdi-magnify me-1"></i> Lihat Bukti
                                    </a>
                                </div>
                            </div>
                        `;
            }

            html += `</div>`; // closing d-flex
            return html;
        }


        $(document).ready(function () {
            const perPage = 10;
            let currentPage = 1;
            let totalActivities = 0;
            let isLoading = false;
            const $loadingIndicator = $('#loading-indicator');
            const $loadMoreBtn = $('#loadMore');
            const $noResults = $('#no-results');
            const $activityFeed = $('#activity-feed');
            const $activityCount = $('#activity-count');

            function loadActivityFeed() {
                if (isLoading) return;
                isLoading = true;
                $loadingIndicator.show();
                $loadMoreBtn.hide();
                $noResults.hide();

                const filterData = {
                    page: currentPage,
                    tgl_dari: $('#tgl_dari').val(),
                    tgl_sampai: $('#tgl_sampai').val(),
                    branch: $('#branch').val(),
                    user: $('#user').val(),
                    kebutuhan: $('#kebutuhan').val(),
                    tipe: $('#tipe').val()
                };

                $.ajax({
                    url: '{{ route('customer-activity.ajaxFeedPaginated') }}',
                    method: 'GET',
                    data: filterData,
                    success: function (data) {
                        $loadingIndicator.hide();

                        if (data.length === 0 && currentPage === 1) {
                            $noResults.show();
                            $activityFeed.html('');
                            $activityCount.text(0);
                            return;
                        }

                        if (currentPage === 1) {
                            $activityFeed.empty();
                            totalActivities = 0;
                        }

                        data.forEach(item => {
                            const tgl = formatDate(item.tgl_activity);
                            const timeAgoText = timeAgo(item.created_at);
                            const tipeBadge = item.tipe
                                ? `<span class="badge bg-info text-white badge-custom">${escapeHtml(item.tipe)}</span>`
                                : '-';
                            const statusBadge = item.status_leads
                                ? `<span class="badge badge-custom" style="background-color: ${escapeHtml(item.warna_background || '#6c757d')}; color: ${escapeHtml(item.warna_font || '#fff')}">${escapeHtml(item.status_leads)}</span>`
                                : '-';

                            const feedItem = `
                                <li class="mb-3">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <!-- Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold">
                                                    <strong>${escapeHtml(item.nomor || '')}</strong> - ${escapeHtml(item.sales || 'Tidak ada sales')}
                                                </h6>
                                                <small class="text-muted">${timeAgoText}</small>
                                            </div>

                                            <!-- Info Grid -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">Tanggal Aktivitas</small>
                                                    <span>${tgl}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block">Status Leads</small>
                                                    ${statusBadge}
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <small class="text-muted d-block">Tipe Aktivitas</small>
                                                    ${tipeBadge}
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <small class="text-muted d-block">Kebutuhan</small>
                                                    <span>${escapeHtml(item.kebutuhan || '-')}</span>
                                                </div>
                                            </div>

                                            <!-- Keterangan -->
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Keterangan</small>
                                                <p class="mb-0">${escapeHtml(item.keterangan || '-')}</p>
                                            </div>

                                            <!-- Actions -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary btn-toggle-detail" data-id="${item.id}">
                                                    <i class="mdi mdi-chevron-down"></i> Detail
                                                </button>
                                                <a href="/sales/customer-activity/view/${item.id}" class="btn btn-sm btn-primary">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Lihat Lengkap
                                                </a>
                                            </div>

                                            <!-- Hidden Detail -->
                                            <div class="feed-detail mt-3" id="detail-${item.id}" style="display: none;">
                                                ${formatDetail(item)}
                                            </div>
                                        </div>
                                    </div>
                                </li>`;

                            $activityFeed.append(feedItem);
                        });

                        totalActivities += data.length;
                        $activityCount.text(totalActivities);
                        currentPage++;
                        if (data.length >= perPage) {
                            $loadMoreBtn.show();
                        } else {
                            $loadMoreBtn.hide();
                        }
                    },
                    error: function (xhr) {
                        $loadingIndicator.hide();
                        $activityFeed.empty();
                        $activityCount.text(0);
                        let errorMessage = 'Gagal memuat data aktivitas.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'Endpoint tidak ditemukan.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan server.';
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    },
                    complete: function () {
                        isLoading = false;
                    }
                });
            }

            loadActivityFeed();

            $loadMoreBtn.on('click', function (e) {
                e.preventDefault();
                loadActivityFeed();
            });

            $('#filter-form').on('submit', function (e) {
                e.preventDefault();
                resetAndLoad();
            });

            $(document).on('click', '.btn-toggle-detail', function (e) {
                e.preventDefault();
                const $btn = $(this);
                const itemId = $btn.data('id');
                const $detail = $(`#detail-${itemId}`);
                $detail.slideToggle(300, function () {
                    if ($detail.is(':visible')) {
                        $btn.html('<i class="mdi mdi-chevron-up"></i> Sembunyikan');
                    } else {
                        $btn.html('<i class="mdi mdi-chevron-down"></i> Detail');
                    }
                });
            });

            $('#reset-filter').on('click', function (e) {
                e.preventDefault();
                $('#filter-form')[0].reset();
                resetAndLoad();
            });

            function resetAndLoad() {
                currentPage = 1;
                totalActivities = 0;
                $activityFeed.empty();
                $loadMoreBtn.hide();
                $noResults.hide();
                loadActivityFeed();
            }
        });
    </script>
@endsection