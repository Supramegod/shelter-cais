@extends('layouts.master')
@section('title', 'Customer Activity')
@section('pageStyle')
    <style>
        .dt-buttons {
            width: 100%;
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
                    <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                        <div class="col-md-6 text-left col-12 my-auto">
                            <h3 class="page-title">Customer Activity</h3>
                            <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Customer Activity</li>
                            </ol>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <form action="{{route('customer-activity')}}" method="GET">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tgl_dari" name="tgl_dari"
                                                    value="{{$tglDari}}">
                                                <label for="tgl_dari">Tanggal Dari</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai"
                                                    value="{{$tglSampai}}">
                                                <label for="tgl_sampai">Tanggal Sampai</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
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
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
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
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-merge mb-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select" id="kebutuhan" name="kebutuhan">
                                                    <option value="">- Semua Kebutuhan -</option>
                                                    @foreach($kebutuhan as $data)
                                                        <option value="{{$data->id}}" @if($request->company == $data->id) selected
                                                        @endif>{{$data->nama}}</option>
                                                    @endforeach
                                                </select>
                                                <label for="kebutuhan">Kebutuhan</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="d-grid">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- resources/views/activity_feed.blade.php -->
                        <div class="container mt-4">
                            <h4 class="mb-4 font-semibold text-xl">Activity Feed</h4>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select id="filter-sales" class="form-select">
                                        <option value="">-- Filter by Sales --</option>
                                        @foreach($salesList as $sales)
                                            <option value="{{ $sales->id }}">{{ $sales->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="activity-container"></div>

                            <div class="text-center text-gray-400 mt-10 d-none" id="no-activity">
                                Tidak ada aktivitas ditemukan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Row -->
        <!--/ Responsive Datatable -->
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
        let dt_filter_table = $('.dt-column-search');

        // Formatting function for row details - modify as you need

        function loadActivityFeed(salesId = '') {
            $.ajax({
                url: '{{ route('activity-feed.ajax') }}',
                data: { sales_id: salesId },
                success: function (response) {
                    let container = $('#activity-container');
                    container.empty();
                    if (response.length === 0) {
                        $('#no-activity').removeClass('d-none');
                    } else {
                        $('#no-activity').addClass('d-none');
                        response.forEach(activity => {
                            container.append(`
                                <div class="card mb-3 shadow-sm rounded-xl border border-gray-200">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                                    ${activity.sales_name.charAt(0).toUpperCase()}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">${activity.sales_name}</div>
                                                    <div class="text-muted">Handled <strong>${activity.lead_name}</strong></div>
                                                </div>
                                            </div>
                                            <div class="text-muted small">${activity.time_ago}</div>
                                        </div>
                                        <div class="mt-2">
                                            <p><strong>Activity:</strong> ${activity.tipe}</p>
                                            <p><strong>Time:</strong> ${activity.date}</p>
                                            <p><strong>Notes:</strong> ${activity.notes || '-'}</p>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    }
                }
            });
        }

        $('#filter-sales').change(function () {
            const salesId = $(this).val();
            loadActivityFeed(salesId);
        });

        // Initial load
        loadActivityFeed();
        dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                                result = result + item.lastChild.firstChild.textContent;
                                            } else if (item.innerText === undefined) {
                                                result = result + item.textContent;
                                            } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                }
                            }
                        }, {
                            extend: 'excel',
                            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                            className: 'dropdown-item',
                            orientation: 'landscape',
                            customize: function (doc) {
                                doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10
                            },
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                                orientation: 'landscape',
                                customize: function (doc) {
                                    doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10
                                },
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = '';
                                        $.each(el, function (index, item) {
                                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                                result = result + item.lastChild.firstChild.textContent;
                                            } else if (item.innerText === undefined) {
                                                result = result + item.textContent;
                                            } else result = result + item.innerText;
                                        });
                                        return result;
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Customer Activity</span>',
                    className: 'create-new btn btn-label-primary waves-effect waves-light',
                    action: function (e, dt, node, config) {
                        //This will send the page to the location specified
                        window.location.href = '{{route("customer-activity.add")}}';
                    }
                }
            ],
            });

        // Add event listener for opening and closing details
        table.on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
            }
            else {
                // Open this row
                row.child(format(row.data())).show();
            }
        });
    </script>
@endsection