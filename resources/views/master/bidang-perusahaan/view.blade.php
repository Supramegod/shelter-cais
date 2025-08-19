@extends('layouts.master')
@section('title', 'Bidang Perusahaan')
@section('content')
  <!--/ Content -->
  <div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master / </span> Lihat Bidang Perusahaan</h4>
    <!-- Multi Column with Form Separator -->
    <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
      <h5 class="card-header">
        <div class="d-flex justify-content-between">
        <span>Detail Bidang Perusahaan</span>
        <span>{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
        </div>
      </h5>
      <form class="card-body" id="form-bidang-perusahaan" action="{{ route('bidang-perusahaan.save') }}"
        method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id ?? '' }}">

        <div class="row mb-3">
        <label class="col-sm-3 col-form-label text-sm-end">Nama Bidang Perusahaan <span
          class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama"
          value="{{ $data->nama ?? old('nama') }}" placeholder="Masukkan nama bidang perusahaan">
          @error('nama')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
        </div>
        </div>

        <div class="row mb-3">
        <label class="col-sm-3 col-form-label text-sm-end">Total Leads</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" value="{{ $totalLeads ?? 0 }}" readonly>
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

      <!-- Leads Table -->
      @if(isset($leads) && count($leads) > 0)
      <div class="card">
      <div class="card-header">
      <h5 class="card-title mb-0">Daftar Leads di Bidang Perusahaan Ini</h5>
      </div>
      <div class="card-body">
      <div class="table-responsive">
      <table class="table table-striped table-hover" id="leads-table">
        <thead class="table-dark">
        <tr>
        <th>No</th>
        <th>Nama Perusahaan</th>
        <th>Telepon</th>
        <th>Jenis Perusahaan</th>
        <th>Branch</th>
        <th>Platform</th>
        <th>Status</th>
        <th>Tanggal Dibuat</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($leads as $index => $lead)
      <tr>
      <td>{{ $index + 1 }}</td>
      <td>{{ $lead->nama_perusahaan ?? '-' }}</td>
      <td>{{ $lead->telp_perusahaan ?? '-' }}</td>
      <td>{{ $lead->jenis_perusahaan ?? '-' }}</td>
      <td>{{ $lead->branch_id ?? '-' }}</td>
      <td>{{ $lead->platform_id ?? '-' }}</td>
      <td>
      <span class="badge"
        style="background-color: {{ $lead->warna_background }}; color: {{ $lead->warna_font }};">
        {{ $lead->status_leads }}
      </span>
      </td>
      <td>{{ $lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('d M Y') : '-' }}</td>
      <td>
      <a href="{{ route('leads.view', $lead->id) }}" class="btn btn-sm btn-info">
        <i class="mdi mdi-eye me-1"></i>View
      </a>
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
      <h5 class="text-muted">Tidak ada leads</h5>
      <p class="text-muted">Belum ada leads yang terdaftar untuk bidang perusahaan ini.</p>
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
          <a class="dropdown-item" href="{{ route('bidang-perusahaan.add') }}">
            <i class="mdi mdi-plus me-1"></i> Tambah Baru
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
            <h4 class="card-title text-white mb-1">{{ $totalLeads ?? 0 }}</h4>
            <p class="card-text">Total Leads</p>
            </div>
            <div class="avatar">
            <span class="avatar-initial rounded bg-white text-primary">
              <i class="mdi mdi-account-group mdi-24px"></i>
            </span>
            </div>
          </div>
          </div>
        </div>

        <div class="col-12 text-center">
          <button type="button" id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
          <span class="me-1">Update Bidang Perusahaan</span>
          <i class="mdi mdi-content-save"></i>
          </button>
        </div>

        <div class="col-12 text-center mt-2">
          <button type="button" id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light"
          data-id="{{ $data->id }}">
          <span class="me-1">Hapus Data</span>
          <i class="mdi mdi-trash-can"></i>
          </button>
        </div>

        <div class="col-12 text-center mt-2">
          <a href="{{ route('bidang-perusahaan') }}" class="btn btn-secondary w-100 waves-effect waves-light">
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
  <!--/ Content -->
@endsection

@section('pageScript')
  <script>
    $(document).ready(function () {
    // Initialize DataTable for leads
    @if(isset($leads) && count($leads) > 0)
    $('#leads-table').DataTable({
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
      let nama = $('input[name="nama"]').val().trim();

      if (nama === '') {
      Swal.fire({
        title: 'Validasi Error!',
        text: 'Nama bidang perusahaan harus diisi',
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
      text: 'Apakah Anda yakin ingin memperbarui data ini?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#007bff',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Update!',
      cancelButtonText: 'Batal'
      }).then((result) => {
      if (result.isConfirmed) {
        $('#form-bidang-perusahaan').submit();
      }
      });
    });

    // Event handler untuk tombol delete
    $('#btn-delete').on('click', function () {
      let id = $(this).data('id');
      let totalLeads = {{ $totalLeads ?? 0 }};

      // Check if there are leads associated
      if (totalLeads > 0) {
      Swal.fire({
        title: 'Tidak dapat menghapus!',
        text: `Bidang perusahaan ini masih memiliki ${totalLeads} leads. Hapus atau pindahkan leads terlebih dahulu.`,
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
      text: 'Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.',
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
        title: 'Menghapus data...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading()
        }
        });

        // AJAX delete request
        $.ajax({
        type: "POST",
        url: "{{ route('bidang-perusahaan.delete') }}",
        data: {
          "id": id,
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
            window.location.href = "{{ route('bidang-perusahaan') }}";
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
          text: 'Terjadi kesalahan saat menghapus data',
          icon: 'error'
          });
        }
        });
      }
      });
    });
    });
  </script>
@endsection