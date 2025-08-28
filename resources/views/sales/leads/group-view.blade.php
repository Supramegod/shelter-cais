@extends('layouts.master')
@section('title', 'Grup Perusahaan')
@section('content')
  <!--/ Content -->
  <div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales / </span> Lihat Grup Perusahaan</h4>
    <!-- Multi Column with Form Separator -->
    <div class="row">
      <!-- Form Label Alignment -->
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
                  value="{{ $data->nama_grup ?? old('nama_grup') }}" placeholder="Masukkan nama grup perusahaan">
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
                  value="{{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d M Y') : '-' }}" readonly>
              </div>
            </div>
          </form>
        </div>

        <!-- Perusahaan Table -->
        @if(isset($perusahaan) && count($perusahaan) > 0)
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">Daftar Perusahaan dalam Grup Ini</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover" id="perusahaan-table">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <th>Nama Perusahaan</th>
                      <th>Telepon</th>
                      <th>Jenis Perusahaan</th>
                      <th>Kota</th>
                      <th>PIC</th>
                      <th>Telepon PIC</th>
                      <th>Status</th>
                      <th>Tanggal Leads</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($perusahaan as $index => $company)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $company->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $company->telp_perusahaan ?? '-' }}</td>
                        <td>{{ $company->jenis_perusahaan ?? '-' }}</td>
                        <td>{{ $company->kota ?? '-' }}</td>
                        <td>{{ $company->pic ?? '-' }}</td>
                        <td>{{ $company->no_telp ?? '-' }}</td>
                        <td>
                          <span class="badge"
                            style="background-color: {{ $company->warna_background }}; color: {{ $company->warna_font }};">
                            {{ $company->status_leads }}
                          </span>
                        </td>
                        <td>{{ $company->tgl_leads ? \Carbon\Carbon::parse($company->tgl_leads)->format('d M Y') : '-' }}</td>
                        <td>
                          <a href="{{ route('leads.view', $company->id) }}" class="btn btn-sm btn-info">
                            <i class="mdi mdi-eye me-1"></i>View
                          </a>
                          <button type="button" class="btn btn-sm btn-danger" onclick="removeFromGroup({{ $data->id }}, {{ $company->id }}, '{{ $company->nama_perusahaan }}')">
                            <i class="mdi mdi-close me-1"></i>Remove
                         </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @else
          <div class="card">
            <div class="card-body text-center">
              <i class="mdi mdi-information-outline mdi-48px text-muted mb-3"></i>
              <h5 class="text-muted">Tidak ada perusahaan</h5>
              <p class="text-muted">Belum ada perusahaan yang terdaftar dalam grup ini.</p>
            </div>
          </div>
        @endif
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

  <!-- Modal Tambah Perusahaan -->
  <div class="modal fade" id="modalTambahPerusahaan" tabindex="-1" aria-labelledby="modalTambahPerusahaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="{{ route('leads.group.addCompany') }}" method="POST">
          @csrf
          <input type="hidden" name="group_id" value="{{ $data->id }}">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahPerusahaanLabel">
              <i class="mdi mdi-plus-circle me-2"></i>Tambah Perusahaan ke Grup
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>

          <div class="modal-body">
            <div class="alert alert-info">
              <i class="mdi mdi-information-outline me-1"></i>
              Pilih perusahaan yang ingin ditambahkan ke grup <strong>{{ $data->nama_grup }}</strong>.
            </div>
            
            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Cari Perusahaan</label>
              <div class="col-sm-9">
                <input type="text" id="searchPerusahaan" class="form-control" placeholder="Ketik nama perusahaan untuk mencari...">
              </div>
            </div>

            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Pilih Perusahaan</h6>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAllCompanies">
                  <label class="form-check-label small" for="selectAllCompanies">Pilih Semua</label>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
                  <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                      <tr>
                        <th width="50"><input type="checkbox" id="selectAllCompaniesHeader"></th>
                        <th>Nama Perusahaan</th>
                        <th>Kota</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="available-companies-body">
                      <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                          <i class="mdi mdi-loading mdi-spin me-1"></i>
                          Memuat data perusahaan...
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">
              <i class="mdi mdi-check-circle me-1"></i>Tambah ke Grup
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="mdi mdi-close me-1"></i>Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--/ Content -->
@endsection

@section('pageScript')
  <script>
    $(document).ready(function () {
      // Initialize DataTable for perusahaan
      @if(isset($perusahaan) && count($perusahaan) > 0)
        $('#perusahaan-table').DataTable({
          responsive: true,
          pageLength: 10,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
          }
        });
      @endif

      // Sweet Alert untuk notifikasi
      @if(session()->has('success'))
        Swal.fire({
          title: 'Berhasil!',
          html: '{!! session()->get('success') !!}',
          icon: 'success',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      @endif

      @if(session()->has('error'))
        Swal.fire({
          title: 'Error!',
          html: '{!! session()->get('error') !!}',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-danger waves-effect waves-light'
          },
          buttonsStyling: false
        });
      @endif

      // Event handler untuk tombol update
      $('#btn-update').on('click', function () {
        // Validasi form sebelum submit
        let namaGrup = $('input[name="nama_grup"]').val().trim();

        if (namaGrup === '') {
          Swal.fire({
            title: 'Validasi Error!',
            text: 'Nama grup harus diisi',
            icon: 'warning',
            customClass: {
              confirmButton: 'btn btn-warning waves-effect waves-light'
            },
            buttonsStyling: false
          });
          return;
        }

        // Konfirmasi sebelum update
        Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah Anda yakin ingin memperbarui data grup ini?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#007bff',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, Update!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $('#form-grup-perusahaan').submit();
          }
        });
      });

      // Event handler untuk tombol delete
      $('#btn-delete').on('click', function () {
        let id = $(this).data('id');
        let totalPerusahaan = {{ $totalPerusahaan ?? 0 }};

        // Check if there are companies associated
        if (totalPerusahaan > 0) {
          Swal.fire({
            title: 'Tidak dapat menghapus!',
            text: `Grup ini masih memiliki ${totalPerusahaan} perusahaan. Hapus atau pindahkan perusahaan terlebih dahulu.`,
            icon: 'warning',
            customClass: {
              confirmButton: 'btn btn-warning waves-effect waves-light'
            },
            buttonsStyling: false
          });
          return;
        }

        Swal.fire({
          title: 'Konfirmasi Hapus',
          text: 'Apakah Anda yakin ingin menghapus grup ini? Data yang dihapus tidak dapat dikembalikan.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
              title: 'Menghapus grup...',
              allowOutsideClick: false,
              allowEscapeKey: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading()
              }
            });

            // AJAX delete request
            $.ajax({
              type: "DELETE",
              url: "{{ route('leads.group.delete', $data->id) }}",
              data: {
                "_token": "{{ csrf_token() }}"
              },
              success: function (response) {
                if (response.success) {
                  Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true,
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
                console.error('Delete Error:', xhr.responseText);
                Swal.fire({
                  title: 'Error!',
                  text: 'Terjadi kesalahan saat menghapus grup',
                  icon: 'error'
                });
              }
            });
          }
        });
      });

      // Load available companies when modal is shown
      $('#modalTambahPerusahaan').on('shown.bs.modal', function() {
        loadAvailableCompanies();
        $('#searchPerusahaan').focus();
      });

      // Search functionality
      $('#searchPerusahaan').on('input', function() {
        const query = $(this).val().toLowerCase();
        filterCompanies(query);
      });

      // Load available companies
      function loadAvailableCompanies() {
        const companiesBody = $('#available-companies-body');
        companiesBody.html('<tr><td colspan="4" class="text-center py-4"><i class="mdi mdi-loading mdi-spin me-1"></i>Memuat data perusahaan...</td></tr>');

        fetch('{{ route("leads.group.availableCompanies", $data->id) }}')
          .then(response => response.json())
          .then(data => {
            companiesBody.empty();
            if (data.length === 0) {
              companiesBody.html('<tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-alert-circle-outline me-1"></i>Tidak ada perusahaan yang tersedia untuk ditambahkan.</td></tr>');
              return;
            }

            data.forEach(company => {
              companiesBody.append(`
                <tr class="company-row" data-company="${company.nama_perusahaan.toLowerCase()}">
                  <td><input type="checkbox" name="perusahaan_ids[]" value="${company.id}" class="form-check-input company-checkbox"></td>
                  <td class="fw-medium">${company.nama_perusahaan}</td>
                  <td>${company.kota || '-'}</td>
                  <td><span class="badge bg-light-secondary text-secondary">${company.status_leads || '-'}</span></td>
                </tr>
              `);
            });

            updateCompanyCheckboxEvents();
          })
          .catch(err => {
            console.error('Gagal mengambil data:', err);
            companiesBody.html('<tr><td colspan="4" class="text-danger text-center py-4"><i class="mdi mdi-alert-circle me-1"></i>Terjadi kesalahan saat memuat data.</td></tr>');
          });
      }

      // Filter companies based on search
      function filterCompanies(query) {
        $('.company-row').each(function() {
          const companyName = $(this).data('company');
          if (companyName.includes(query)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }

      // Update checkbox events
      function updateCompanyCheckboxEvents() {
        const checkboxes = $('.company-checkbox');
        
        // Select all functionality
        $('#selectAllCompanies, #selectAllCompaniesHeader').on('change', function() {
          const isChecked = this.checked;
          checkboxes.each(function() {
            if ($(this).closest('tr').is(':visible')) {
              this.checked = isChecked;
            }
          });
          $('#selectAllCompanies, #selectAllCompaniesHeader').prop('checked', isChecked);
        });

        // Individual checkbox change
        checkboxes.on('change', function() {
          const visibleCheckboxes = checkboxes.filter(function() {
            return $(this).closest('tr').is(':visible');
          });
          const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.toArray().every(cb => cb.checked);
          $('#selectAllCompanies, #selectAllCompaniesHeader').prop('checked', allChecked);
        });
      }

      // Clear form on modal hide
      $('#modalTambahPerusahaan').on('hidden.bs.modal', function() {
        $('#searchPerusahaan').val('');
        $('#selectAllCompanies, #selectAllCompaniesHeader').prop('checked', false);
      });
    });

    // Remove company from group
    // Remove company from group
function removeFromGroup(groupId, companyId, companyName) {
  Swal.fire({
    title: 'Hapus dari Grup?',
    text: `Apakah Anda yakin ingin menghapus "${companyName}" dari grup ini?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Build the URL dynamically with the required parameters
      let removeUrl = "{{ url('sales/leads/group') }}/" + groupId + "/company/" + companyId;

      $.ajax({
        type: "POST",
        url: removeUrl,
        data: {
          "group_id": groupId,
          "company_id": companyId,
          "_token": "{{ csrf_token() }}"
        },
        success: function (response) {
          if (response.success) {
            Swal.fire({
              title: 'Berhasil!',
              text: response.message,
              icon: 'success',
              timer: 1500,
              timerProgressBar: true,
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
          console.error('Remove Error:', xhr.responseText);
          Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghapus perusahaan dari grup',
            icon: 'error'
          });
        }
      });
    }
  });
}
  </script>
@endsection