@extends('layouts.master')
@section('title', 'Grup Perusahaan')
@section('content')
  <div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales / </span> Lihat Grup Perusahaan</h4>
    <div class="row">
      <div class="col-md-8">
        <div class="card mb-4">
          <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span>Detail Grup Perusahaan</span>
              <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
            </div>
          </h5>
          <form class="card-body" id="form-grup-perusahaan" action="{{ route('leads.group.save') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $data->id ?? '' }}">

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">Nama Grup <span class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="text" class="form-control @error('nama_grup') is-invalid @enderror" name="nama_grup"
                  value="{{ $data->nama_grup ?? '' }}" placeholder="Masukkan nama grup" autofocus />
                @error('nama_grup')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">Total Perusahaan</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" value="{{ $totalPerusahaan ?? 0 }}" readonly>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label text-sm-end">Dibuat Oleh</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" value="{{ $data->created_by ?? '-' }}" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Tanggal Dibuat</label>
              <div class="col-sm-3">
                <input type="text" class="form-control"
                  value="{{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d M Y') : '-' }}"
                  readonly>
              </div>
            </div>
          </form>
        </div>



        <div class="card mb-4">
          <h5 class="card-header">
            <div class="d-flex justify-content-between">
              <span>Daftar Perusahaan dalam Grup</span>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahPerusahaan">
                <i class="mdi mdi-plus me-1"></i> Tambah Perusahaan
              </button>
            </div>
          </h5>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Nama Perusahaan</th>
                    <th>Kota</th>
                    <th>Jenis Perusahaan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @forelse ($perusahaan as $company)
                    <tr>
                      <td>{{ $company->nama_perusahaan }}</td>
                      <td>{{ $company->kota }}</td>
                      <td>{{ $company->jenis_perusahaan }}</td>
                      <td>
                        <button class="btn btn-danger btn-sm remove-company-btn" data-group-id="{{ $data->id ?? '' }}"
                          data-company-id="{{ $company->id }}">Hapus</button>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center">Belum ada perusahaan dalam grup ini.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="row">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Action</h5>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <i class="mdi mdi-dots-vertical mdi-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="actionDropdown">
              <a class="dropdown-item" href="javascript:void(0)" onclick="$('#modalTambahPerusahaan').modal('show');">
                <i class="mdi mdi-plus me-1"></i> Tambah Perusahaan
              </a>
              <a class="dropdown-item" href="{{ route('leads') }}">
                <i class="mdi mdi-group me-1"></i> Lihat Semua Grup
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Statistics Card -->
          <div class="card bg-primary text-white mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h4 class="card-title text-white mb-1">{{ $totalPerusahaan ?? 0 }}</h4>
                  <p class="card-text">Total Perusahaan</p>
                </div>
                <div class="avatar">
                  <span class="avatar-initial rounded bg-white text-primary">
                    <i class="mdi mdi-domain mdi-24px"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 text-center">
            <button type="button" id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
              <span class="me-1">Update Grup</span>
              <i class="mdi mdi-content-save"></i>
            </button>
          </div>

          <div class="col-12 text-center mt-2">
            <button type="button" id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light"
              data-id="{{ $data->id }}">
              <span class="me-1">Hapus Grup</span>
              <i class="mdi mdi-trash-can"></i>
            </button>
          </div>

          <div class="col-12 text-center mt-2">
            <a href="{{ route('leads') }}" class="btn btn-secondary w-100 waves-effect waves-light">
              <span class="me-1">Kembali</span>
              <i class="mdi mdi-arrow-left"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
  <div class="modal fade" id="modalTambahPerusahaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Perusahaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="input-group mb-3">
                <input type="text" class="form-control" id="searchPerusahaanInput"
                  placeholder="Cari perusahaan berdasarkan nama atau kota...">
                <button class="btn btn-outline-primary" type="button" id="btnCariPerusahaan">
                  <i class="mdi mdi-magnify me-1"></i> Cari
                </button>
              </div>
              
              <!-- Results Info -->
              <div id="search-results-info" class="mb-2" style="display: none;">
                <small class="text-muted">
                  Menampilkan <span id="showing-from">0</span> - <span id="showing-to">0</span> dari <span id="total-results">0</span> hasil
                </small>
              </div>
              
             <div class="table-responsive">
                <table class="table table-hover table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th><input type="checkbox" id="selectAllPerusahaan"></th>
                      <th>Nama Perusahaan</th>
                      <th>Kota</th>
                      <th>Jenis</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="add-company-container">
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Silakan ketik nama perusahaan untuk melihat hasil pencarian.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-3" id="pagination-container">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary" id="btnSimpanPerusahaan">Simpan</button>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('pageScript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    console.log('=== DEBUG: Page loaded ===');
    console.log('Group ID:', "{{ $data->id ?? 'NOT FOUND' }}");
    
    // Global pagination variables
    let currentPage = 1;
    let lastSearchKeyword = '';
    
    // Event listener untuk tombol "Simpan" di modal
    $('#btnSimpanPerusahaan').on('click', function(e) {
        e.preventDefault();
        console.log('=== DEBUG: Save button clicked ===');

        // Collect selected companies
        let selectedCompanies = [];
        $('input[name="perusahaan_terpilih[]"]:checked').each(function() {
            const value = parseInt($(this).val());
            if (!isNaN(value)) {
                selectedCompanies.push(value);
            }
        });

        console.log('Selected companies:', selectedCompanies);

        // Validation
        if (selectedCompanies.length === 0) {
            console.log('No companies selected');
            Swal.fire({
                title: 'Peringatan!',
                text: 'Silakan pilih setidaknya satu perusahaan.',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
            return;
        }

        // Get group ID
        const groupId = "{{ $data->id ?? '' }}";
        if (!groupId) {
            console.error('Group ID is empty!');
            Swal.fire({
                title: 'Error!',
                text: 'ID Grup tidak ditemukan.',
                icon: 'error'
            });
            return;
        }

        // Disable button
        const btnSimpan = $(this);
        btnSimpan.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
        if (!csrfToken) {
            console.error('CSRF token not found');
            btnSimpan.prop('disabled', false).html('Simpan');
            Swal.fire({
                title: 'Error!',
                text: 'Token keamanan tidak ditemukan.',
                icon: 'error'
            });
            return;
        }

        // Prepare request
        const requestData = {
            _token: csrfToken,
            companies: selectedCompanies
        };
        
        const url = `/sales/leads/group/${groupId}/add-company`;
        
        console.log('=== AJAX REQUEST DEBUG ===');
        console.log('URL:', url);
        console.log('Data:', requestData);
        console.log('CSRF Token:', csrfToken);

        // AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: requestData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            beforeSend: function(xhr) {
                console.log('=== AJAX BEFORE SEND ===');
                console.log('XHR readyState:', xhr.readyState);
            },
            success: function(response, textStatus, xhr) {
                console.log('=== AJAX SUCCESS ===');
                console.log('Response:', response);
                console.log('Status:', textStatus);
                console.log('XHR Status:', xhr.status);
                
                btnSimpan.prop('disabled', false).html('Simpan');
                
                if (response && response.success) {
                    $('#modalTambahPerusahaan').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Perusahaan berhasil ditambahkan ke grup.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    console.error('Server returned success=false:', response);
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data.',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-danger waves-effect waves-light'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log('=== AJAX ERROR ===');
                console.log('XHR:', xhr);
                console.log('Status:', textStatus);
                console.log('Error:', errorThrown);
                console.log('Response Text:', xhr.responseText);
                console.log('Status Code:', xhr.status);
                
                btnSimpan.prop('disabled', false).html('Simpan');
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                
                if (xhr.status === 404) {
                    errorMessage = 'URL tidak ditemukan (404). Periksa route.';
                } else if (xhr.status === 422) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                } else if (xhr.status === 419) {
                    errorMessage = 'Token keamanan expired. Silakan refresh halaman.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error (500). Periksa log Laravel.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ' Detail: ' + xhr.responseJSON.message;
                    }
                }
                
                console.error('Final error message:', errorMessage);
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-danger waves-effect waves-light'
                    },
                    buttonsStyling: false
                });
            },
            complete: function(xhr) {
                console.log('=== AJAX COMPLETE ===');
                console.log('Final XHR status:', xhr.status);
                console.log('Response headers:', xhr.getAllResponseHeaders());
            }
        });
    });

    // Function to create pagination
    function createPagination(pagination) {
        let paginationHtml = '';
        
        if (pagination && pagination.last_page > 1) {
            paginationHtml += '<nav aria-label="Page navigation">';
            paginationHtml += '<ul class="pagination pagination-sm justify-content-center mb-0">';
            
            // Previous button
            if (pagination.current_page > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link pagination-link" href="#" data-page="${pagination.current_page - 1}">
                            <i class="mdi mdi-chevron-left"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="mdi mdi-chevron-left"></i>
                        </span>
                    </li>
                `;
            }
            
            // Page numbers
            let startPage = Math.max(1, pagination.current_page - 2);
            let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
            
            // Always show first page
            if (startPage > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link pagination-link" href="#" data-page="1">1</a>
                    </li>
                `;
                if (startPage > 2) {
                    paginationHtml += `
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    `;
                }
            }
            
            // Page range
            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    paginationHtml += `
                        <li class="page-item active">
                            <span class="page-link">${i}</span>
                        </li>
                    `;
                } else {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link pagination-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
            }
            
            // Always show last page
            if (endPage < pagination.last_page) {
                if (endPage < pagination.last_page - 1) {
                    paginationHtml += `
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    `;
                }
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link pagination-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a>
                    </li>
                `;
            }
            
            // Next button
            if (pagination.current_page < pagination.last_page) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link pagination-link" href="#" data-page="${pagination.current_page + 1}">
                            <i class="mdi mdi-chevron-right"></i>
                        </a>
                    </li>
                `;
            } else {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="mdi mdi-chevron-right"></i>
                        </span>
                    </li>
                `;
            }
            
            paginationHtml += '</ul>';
            paginationHtml += '</nav>';
        }
        
        $('#pagination-container').html(paginationHtml);
    }

    // Function to update results info
    function updateResultsInfo(pagination) {
        if (pagination && pagination.total > 0) {
            const from = ((pagination.current_page - 1) * pagination.per_page) + 1;
            const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);
            
            $('#showing-from').text(from);
            $('#showing-to').text(to);
            $('#total-results').text(pagination.total);
            $('#search-results-info').show();
        } else {
            $('#search-results-info').hide();
        }
    }

    // Event untuk search perusahaan dengan pagination
    function performSearch(page = 1) {
        const keyword = $('#searchPerusahaanInput').val().trim();
        const groupId = "{{ $data->id ?? '' }}";
        
        console.log('=== DEBUG: Performing search ===');
        console.log('Keyword:', keyword);
        console.log('Group ID:', groupId);
        console.log('Page:', page);
        
        // Update current page and keyword
        currentPage = page;
        lastSearchKeyword = keyword;
        
        if (keyword.length < 2) {
            $('#add-company-container').html(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline me-1"></i> 
                        Silakan ketik minimal 2 karakter untuk mencari.
                    </td>
                </tr>
            `);
            $('#pagination-container').html('');
            $('#search-results-info').hide();
            return;
        }

        // Clear previous selections only on new search (page 1)
        if (page === 1) {
            $('#selectAllPerusahaan').prop('checked', false);
        }

        $.ajax({
            url: `/sales/leads/group/${groupId}/add-company`,
            method: 'GET',
            data: { 
                keyword: keyword,
                page: page
            },
            dataType: 'json',
            beforeSend: function() {
                console.log('Search request starting...');
                if (page === 1) {
                    $('#add-company-container').html(`
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="mt-2">Sedang mencari perusahaan...</div>
                            </td>
                        </tr>
                    `);
                    $('#pagination-container').html('');
                    $('#search-results-info').hide();
                }
            },
            success: function(response) {
                console.log('Search response:', response);
                
                if (response.success && response.data) {
                    let html = '';
                    if (response.data.length > 0) {
                        response.data.forEach(company => {
                            const backgroundColor = company.warna_background || '#6c757d';
                            const fontColor = company.warna_font || '#ffffff';
                            const status = company.status_leads || 'Unknown';
                            const jenis = company.jenis_perusahaan || '-';
                            const kota = company.kota || '-';
                            
                            html += `
                                <tr>
                                    <td>
                                        <input type="checkbox" name="perusahaan_terpilih[]" value="${company.id}" class="company-checkbox">
                                    </td>
                                    <td class="fw-semibold">${company.nama_perusahaan}</td>
                                    <td>${kota}</td>
                                    <td>${jenis}</td>
                                    <td>
                                        <span class="badge" style="background-color: ${backgroundColor}; color: ${fontColor};">
                                            ${status}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Update pagination if provided
                        if (response.pagination) {
                            createPagination(response.pagination);
                            updateResultsInfo(response.pagination);
                        }
                        
                    } else {
                        html = `
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> 
                                    Tidak ada perusahaan yang ditemukan untuk "${keyword}".
                                </td>
                            </tr>
                        `;
                        $('#pagination-container').html('');
                        $('#search-results-info').hide();
                    }
                    $('#add-company-container').html(html);
                } else {
                    console.error('Search failed:', response);
                    $('#add-company-container').html(`
                        <tr>
                            <td colspan="5" class="text-center text-danger py-4">
                                <i class="mdi mdi-alert me-1"></i> 
                                ${response.message || 'Gagal memuat data perusahaan.'}
                            </td>
                        </tr>
                    `);
                    $('#pagination-container').html('');
                    $('#search-results-info').hide();
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('Search error:', xhr);
                let errorMsg = 'Terjadi kesalahan saat mencari.';
                
                if (xhr.status === 404) {
                    errorMsg = 'URL pencarian tidak ditemukan.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                $('#add-company-container').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger py-4">
                            <i class="mdi mdi-alert me-1"></i> 
                            ${errorMsg}
                        </td>
                    </tr>
                `);
                $('#pagination-container').html('');
                $('#search-results-info').hide();
            }
        });
    }

    // Event listener untuk pagination
    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        console.log('Pagination clicked, page:', page);
        
        if (page && lastSearchKeyword) {
            performSearch(page);
        }
    });

    // Bind search events
    $('#btnCariPerusahaan').on('click', function(e) {
        e.preventDefault();
        currentPage = 1; // Reset to first page on new search
        performSearch(currentPage);
    });
    
    $('#searchPerusahaanInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            currentPage = 1; // Reset to first page on new search
            performSearch(currentPage);
        }
    });

    // Input event untuk real-time search (opsional)
    let searchTimeout;
    $('#searchPerusahaanInput').on('input', function() {
        clearTimeout(searchTimeout);
        const keyword = $(this).val().trim();
        
        if (keyword.length >= 2) {
            searchTimeout = setTimeout(function() {
                currentPage = 1; // Reset to first page on new search
                performSearch(currentPage);
            }, 500); // Delay 500ms
        } else if (keyword.length === 0) {
            $('#add-company-container').html(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Silakan ketik nama perusahaan untuk melihat hasil pencarian.
                    </td>
                </tr>
            `);
            $('#selectAllPerusahaan').prop('checked', false);
            $('#pagination-container').html('');
            $('#search-results-info').hide();
            currentPage = 1;
            lastSearchKeyword = '';
        }
    });

    // Select all functionality
    $('#selectAllPerusahaan').on('change', function() {
        const isChecked = this.checked;
        $('input[name="perusahaan_terpilih[]"]').prop('checked', isChecked);
        console.log('Select all changed:', isChecked);
    });

    // Individual checkbox change
    $(document).on('change', 'input[name="perusahaan_terpilih[]"]', function() {
        const totalCheckboxes = $('input[name="perusahaan_terpilih[]"]').length;
        const checkedCheckboxes = $('input[name="perusahaan_terpilih[]"]:checked').length;
        
        // Update select all checkbox
        $('#selectAllPerusahaan').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        
        console.log('Individual checkbox changed. Total:', totalCheckboxes, 'Checked:', checkedCheckboxes);
    });

    // Event untuk button hapus perusahaan dari grup
    $('.remove-company-btn').on('click', function() {
        const groupId = $(this).data('group-id');
        const companyId = $(this).data('company-id');
        const row = $(this).closest('tr');
        const companyName = row.find('td:first').text();

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Yakin ingin menghapus "${companyName}" dari grup?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger waves-effect waves-light',
                cancelButton: 'btn btn-secondary waves-effect waves-light'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request untuk hapus
                $.ajax({
                    url: `/sales/leads/group/${groupId}/remove-company/${companyId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Remove error:', xhr);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus perusahaan.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Auto-load search on modal show
    $('#modalTambahPerusahaan').on('shown.bs.modal', function() {
        console.log('Modal opened');
        $('#searchPerusahaanInput').focus();
        
        // Reset pagination variables
        currentPage = 1;
        lastSearchKeyword = '';
        
        // Load initial data (empty search)
        $('#add-company-container').html(`
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Silakan ketik nama perusahaan untuk melihat hasil pencarian.
                </td>
            </tr>
        `);
        $('#pagination-container').html('');
        $('#search-results-info').hide();
    });

    // Reset modal on hide
    $('#modalTambahPerusahaan').on('hidden.bs.modal', function() {
        console.log('Modal closed');
        $('#searchPerusahaanInput').val('');
        $('#selectAllPerusahaan').prop('checked', false);
        $('#add-company-container').html(`
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Silakan ketik nama perusahaan untuk melihat hasil pencarian.
                </td>
            </tr>
        `);
        $('#pagination-container').html('');
        $('#search-results-info').hide();
        
        // Reset pagination variables
        currentPage = 1;
        lastSearchKeyword = '';
    });
});
</script>
@endsection