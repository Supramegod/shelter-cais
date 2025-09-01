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
              <!-- Search Bar -->
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
                  Menampilkan <span id="showing-from">0</span> - <span id="showing-to">0</span> dari <span
                    id="total-results">0</span> hasil
                </small>
              </div>

              <!-- Table -->
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

              <!-- Pagination (AJAX inject di sini) -->
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

    // PERBAIKAN: Tambahkan variabel global untuk menyimpan selected items across pages
    let globalSelectedCompanies = new Set();
    let isSelectAllActive = false;
    let currentPage = 1;
    let lastSearchKeyword = '';

    $(document).ready(function () {
      // ... kode lainnya tetap sama ...

      // PERBAIKAN: Select all functionality yang diperbaiki
      $('#selectAllPerusahaan').on('change', function () {
        const isChecked = this.checked;
        isSelectAllActive = isChecked;

        console.log('=== SELECT ALL CHANGED ===');
        console.log('Is checked:', isChecked);

        if (isChecked) {
          // Jika select all dicentang, ambil SEMUA data yang tersedia (tidak hanya halaman saat ini)
          performSelectAllAcrossPages();
        } else {
          // Jika uncheck, kosongkan semua
          globalSelectedCompanies.clear();
          $('input[name="perusahaan_terpilih[]"]').prop('checked', false);
        }

        updateSelectAllIndicator();
      });

      // PERBAIKAN: Individual checkbox change yang diperbaiki
      $(document).on('change', 'input[name="perusahaan_terpilih[]"]', function () {
        const companyId = parseInt($(this).val());
        const isChecked = this.checked;

        if (isChecked) {
          globalSelectedCompanies.add(companyId);
        } else {
          globalSelectedCompanies.delete(companyId);
          // Jika ada yang uncheck, matikan select all
          isSelectAllActive = false;
          $('#selectAllPerusahaan').prop('checked', false);
        }

        updateSelectAllIndicator();
        console.log('Individual checkbox changed. Global selected:', Array.from(globalSelectedCompanies));
      });

      // PERBAIKAN: Fungsi untuk select all across pages
      function performSelectAllAcrossPages() {
        const keyword = $('#searchPerusahaanInput').val().trim();
        const groupId = "{{ $data->id ?? '' }}";

        console.log('=== PERFORMING SELECT ALL ACROSS PAGES ===');
        console.log('Keyword:', keyword);

        // Tampilkan loading
        Swal.fire({
          title: 'Memuat Data...',
          text: 'Sedang mengambil semua data perusahaan',
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          }
        });

        // AJAX untuk mengambil SEMUA data (tanpa pagination)
        $.ajax({
          url: `/sales/leads/group/${groupId}/add-company`,
          method: 'GET',
          data: {
            keyword: keyword,
            get_all: true // Parameter khusus untuk ambil semua data
          },
          dataType: 'json',
          success: function (response) {
            Swal.close();
            console.log('Select all response:', response);

            if (response.success && response.data) {
              // Tambahkan semua ID ke globalSelectedCompanies
              globalSelectedCompanies.clear();
              response.data.forEach(company => {
                globalSelectedCompanies.add(company.id);
              });

              // Update checkbox yang terlihat di halaman saat ini
              $('input[name="perusahaan_terpilih[]"]').each(function () {
                const companyId = parseInt($(this).val());
                if (globalSelectedCompanies.has(companyId)) {
                  $(this).prop('checked', true);
                }
              });

              updateSelectAllIndicator();

              // Tampilkan notifikasi berapa yang terpilih
              console.log('Select all completed. Total selected:', globalSelectedCompanies.size);

              if (globalSelectedCompanies.size > 0) {
                Swal.fire({
                  title: 'Berhasil!',
                  text: `${globalSelectedCompanies.size} perusahaan dipilih dari semua halaman.`,
                  icon: 'success',
                  timer: 2000,
                  showConfirmButton: false
                });
              }
            } else {
              Swal.fire({
                title: 'Peringatan!',
                text: 'Tidak ada data yang dapat dipilih.',
                icon: 'warning'
              });
            }
          },
          error: function (xhr) {
            Swal.close();
            console.error('Select all failed:', xhr);
            Swal.fire({
              title: 'Error!',
              text: 'Gagal memuat semua data untuk select all.',
              icon: 'error'
            });
          }
        });
      }

      // PERBAIKAN: Fungsi untuk update indikator select all
      function updateSelectAllIndicator() {
        const totalVisible = $('input[name="perusahaan_terpilih[]"]').length;
        const checkedVisible = $('input[name="perusahaan_terpilih[]"]:checked').length;

        // Update checkbox select all berdasarkan status
        if (isSelectAllActive && globalSelectedCompanies.size > 0) {
          $('#selectAllPerusahaan').prop('checked', true);
        } else if (totalVisible > 0 && totalVisible === checkedVisible && checkedVisible > 0) {
          $('#selectAllPerusahaan').prop('checked', true);
        } else {
          $('#selectAllPerusahaan').prop('checked', false);
        }
      }

      // PERBAIKAN: Function performSearch yang diperbaiki untuk maintain selection
      function performSearch(page = 1) {
        const keyword = $('#searchPerusahaanInput').val().trim();
        const groupId = "{{ $data->id ?? '' }}";

        console.log('=== DEBUG: Performing search ===');
        console.log('Keyword:', keyword);
        console.log('Group ID:', groupId);
        console.log('Page:', page);
        console.log('Global selected before search:', Array.from(globalSelectedCompanies));

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

          // Reset selection untuk search baru
          if (page === 1) {
            globalSelectedCompanies.clear();
            isSelectAllActive = false;
            $('#selectAllPerusahaan').prop('checked', false);
          }
          return;
        }

        // Reset selection hanya untuk search baru (page 1)
        if (page === 1) {
          globalSelectedCompanies.clear();
          isSelectAllActive = false;
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
          beforeSend: function () {
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
          success: function (response) {
            console.log('=== SEARCH SUCCESS ===');
            console.log('Response:', response);

            if (response.success && response.data) {
              let html = '';
              if (response.data.length > 0) {
                response.data.forEach(company => {
                  const backgroundColor = company.warna_background || '#6c757d';
                  const fontColor = company.warna_font || '#ffffff';
                  const status = company.status_leads || 'Unknown';
                  const jenis = company.jenis_perusahaan || '-';
                  const kota = company.kota || '-';

                  // PERBAIKAN: Periksa apakah company ini sudah terpilih di globalSelectedCompanies
                  const isChecked = globalSelectedCompanies.has(company.id) ? 'checked' : '';

                  html += `
                                              <tr>
                                                  <td>
                                                      <input type="checkbox" name="perusahaan_terpilih[]" value="${company.id}" class="company-checkbox" ${isChecked}>
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

                // Update pagination jika ada
                if (response.pagination) {
                  console.log('=== PAGINATION DEBUG ===');
                  console.log('Pagination data:', response.pagination);
                  createPagination(response.pagination);
                  updateResultsInfo(response.pagination);
                } else {
                  console.log('No pagination data received');
                  $('#pagination-container').html('');
                  $('#search-results-info').hide();
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

              // PERBAIKAN: Update select all indicator setelah load data
              updateSelectAllIndicator();
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
          error: function (xhr, textStatus, errorThrown) {
            // ... error handling tetap sama ...
            console.log('=== SEARCH ERROR ===');
            console.log('XHR:', xhr);
            console.log('Status:', textStatus);
            console.log('Error:', errorThrown);
            console.log('Response Text:', xhr.responseText);
            console.log('Status Code:', xhr.status);

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

      // PERBAIKAN: Event listener untuk save yang diperbaiki
      $('#btnSimpanPerusahaan').on('click', function (e) {
        e.preventDefault();
        console.log('=== DEBUG: Save button clicked ===');

        // PERBAIKAN: Gunakan globalSelectedCompanies sebagai sumber data
        let selectedCompanies = Array.from(globalSelectedCompanies);

        console.log('Selected companies from global set:', selectedCompanies);

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
        console.log('Total companies to save:', selectedCompanies.length);

        // AJAX request tetap sama seperti sebelumnya...
        $.ajax({
          url: url,
          method: 'POST',
          data: requestData,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          success: function (response, textStatus, xhr) {
            console.log('=== AJAX SUCCESS ===');
            console.log('Response:', response);

            btnSimpan.prop('disabled', false).html('Simpan');

            if (response && response.success) {
              // Reset global selection
              globalSelectedCompanies.clear();
              isSelectAllActive = false;

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
                icon: 'error'
              });
            }
          },
          error: function (xhr, textStatus, errorThrown) {
            // ... error handling tetap sama ...
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
          }
        });
      });

      // Event untuk button hapus perusahaan dari grup
      $('.remove-company-btn').on('click', function () {
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
              success: function (response) {
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
              error: function (xhr) {
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

      // Event untuk button Update Grup
      $('#btn-update').on('click', function (e) {
        e.preventDefault();
        $('#form-grup-perusahaan').submit();
      });

      // Event untuk button Hapus Grup
      $('#btn-delete').on('click', function (e) {
        e.preventDefault();
        const groupId = $(this).data('id');
        const groupName = $('input[name="nama_grup"]').val();

        Swal.fire({
          title: 'Konfirmasi Hapus',
          text: `Yakin ingin menghapus grup "${groupName}"?`,
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
            $.ajax({
              url: `/sales/leads/group/${groupId}`,
              method: 'DELETE',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                if (response.success) {
                  Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                  }).then(() => {
                    window.location.href = "{{ route('leads') }}";
                  });
                } else {
                  Swal.fire({
                    title: 'Gagal!',
                    text: response.message,
                    icon: 'error'
                  });
                }
              },
              error: function (xhr) {
                console.error('Delete group error:', xhr);
                let errorMessage = 'Terjadi kesalahan saat menghapus grup.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                  title: 'Error!',
                  text: errorMessage,
                  icon: 'error'
                });
              }
            });
          }
        });
      });

      // Function to create pagination - DIPERBAIKI
      function createPagination(pagination) {
        console.log('=== CREATE PAGINATION ===');
        console.log('Pagination object:', pagination);

        let paginationHtml = '';

        if (pagination && pagination.last_page > 1) {
          console.log('Creating pagination with', pagination.last_page, 'pages');

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

          // Page numbers logic
          let startPage = Math.max(1, pagination.current_page - 2);
          let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

          console.log('Page range:', startPage, 'to', endPage);

          // Always show first page if we're not starting from page 1
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

          // Always show last page if we're not ending at last page
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

          console.log('Pagination HTML created:', paginationHtml.length, 'characters');
        } else {
          console.log('No pagination needed - only one page or no data');
        }

        $('#pagination-container').html(paginationHtml);
        console.log('Pagination container updated');
      }

      // Function to update results info - DIPERBAIKI
      function updateResultsInfo(pagination) {
        console.log('=== UPDATE RESULTS INFO ===');
        console.log('Pagination for results info:', pagination);

        if (pagination && pagination.total > 0) {
          console.log('Showing results info');

          $('#showing-from').text(pagination.from || 0);
          $('#showing-to').text(pagination.to || 0);
          $('#total-results').text(pagination.total || 0);
          $('#search-results-info').show();

          console.log('Results info updated:', `${pagination.from} - ${pagination.to} of ${pagination.total}`);
        } else {
          console.log('Hiding results info - no data');
          $('#search-results-info').hide();
        }
      }

      // Bind search events
      $('#btnCariPerusahaan').on('click', function (e) {
        e.preventDefault();
        console.log('Search button clicked');
        currentPage = 1; // Reset to first page on new search
        performSearch(currentPage);
      });

      $('#searchPerusahaanInput').on('keypress', function (e) {
        if (e.which === 13) {
          e.preventDefault();
          console.log('Enter key pressed');
          currentPage = 1; // Reset to first page on new search
          performSearch(currentPage);
        }
      });

      // Input event untuk real-time search (opsional)
      let searchTimeout;
      $('#searchPerusahaanInput').on('input', function () {
        clearTimeout(searchTimeout);
        const keyword = $(this).val().trim();

        if (keyword.length >= 2) {
          searchTimeout = setTimeout(function () {
            console.log('Real-time search triggered');
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

      // Auto-load search on modal show
      $('#modalTambahPerusahaan').on('shown.bs.modal', function () {
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

      // PERBAIKAN: Reset modal on hide yang diperbaiki
      $('#modalTambahPerusahaan').on('hidden.bs.modal', function () {
        console.log('Modal closed');
        $('#searchPerusahaanInput').val('');

        // Reset global selection
        globalSelectedCompanies.clear();
        isSelectAllActive = false;
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