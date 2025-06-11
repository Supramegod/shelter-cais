@extends('layouts.master')
@section('title','SPK')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Lihat SPK</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form SPK <span class="badge @if($data->status_spk_id==1 ) bg-label-warning @elseif($data->status_spk_id==2 ) bg-label-info @elseif($data->status_spk_id==3 ) bg-label-success @endif rounded-pill mt-1">{{$data->status}}</span></span>
            <span style="font-weight:bold;color:#000">{{$data->nomor}} - {{$data->stgl_spk}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('leads.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <h6>1. Informasi SPK</h6>
          <div class="row mb-3">
            <div class="table-responsive">
              <table class="table">
                <tbody>
                  <tr>
                    <td>Nama Perusahaan</td>
                    <td>: {{$quotation->nama_perusahaan}}</td>
                    <td>Kebutuhan</td>
                    <td>: {{$quotation->kebutuhan}}</td>
                  </tr>
                  <tr>
                    <td>Entitas</td>
                    <td>: {{$quotation->company}}</td>
                    <td>Jumlah Site</td>
                    <td>: {{$quotation->jumlah_site}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <h6>2. Informasi Quotation</h6>
          <div class="row mb-3">
            <div class="table-responsive overflow-hidden table-quotation">
              <table id="table-quotation" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Nomor</th>
                    <th>Kebutuhan</th>
                  </tr>
                </thead>
                <tbody id="tbody-quotation">
                  <tr>
                    <td>1</td>
                    <td><b><a href="{{route('quotation.view',[$quotation->id])}}">{{$quotation->nomor}}</a></b></td>
                    <td>{{$quotation->kebutuhan}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <h6>3. Informasi Site</h6>
          <div class="row mb-3">
            <div class="table-responsive overflow-hidden table-site">
              <table id="table-site" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Nama Site</th>
                    <th>Provinsi</th>
                    <th>Kota</th>
                    <th>Penempatan</th>
                  </tr>
                </thead>
                <tbody id="tbody-site">
                    @foreach($data->site as $index => $site)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$site->nama_site}}</td>
                        <td>{{$site->provinsi}}</td>
                        <td>{{$site->kota}}</td>
                        <td>{{$site->penempatan}}</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Action</h5>
            <div class="dropdown">
              <button class="btn p-0" type="button" id="upgradePlanCard" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-dots-vertical mdi-24px"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="upgradePlanCard">
              </div>
            </div>
          </div>
          <div class="card-body">
            @if($data->status_spk_id == 1)
            <div class="col-12 text-center mt-2">
              <a onclick="window.open('{{route('spk.cetak-spk',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" id="btn-download-spk" class="btn btn-warning w-100 waves-effect waves-light">
                <span class="me-1">Download Template SPK</span>
                <i class="mdi mdi-download scaleX-n1-rtl"></i>
              </a>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-upload-spk" class="btn btn-info w-100 waves-effect waves-light">
                <span class="me-1">Upload SPK</span>
                <i class="mdi mdi-upload scaleX-n1-rtl"></i>
              </button>
            </div>
            @elseif($data->status_spk_id == 2 || $data->status_spk_id == 3 || $data->status_spk_id == 4)
            <div class="col-12 text-center mt-2">
              <a target="_blank" href="{{$data->link_spk_disetujui}}" id="btn-lihat-spk" class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Lihat SPK</span>
                <i class="mdi mdi-download scaleX-n1-rtl"></i>
              </a>
            </div>
            @endif
            @if($data->status_spk_id == 2)
            <div class="col-12 text-center mt-2">
            <a href="{{route('pks.add',['id'=> $data->id])}}" class="btn btn-info w-100 waves-effect waves-light"><i class="mdi mdi-arrow-right"></i>&nbsp;  Create PKS</a>
            </div>
            @endif
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>
            <br>
            <hr>
            @if($data->status_spk_id == 1)
            <div class="col-12 text-center mt-2">
              <button id="btn-ajukan-ulang" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Ajukan Ulang Quotation</span>
                <i class="mdi mdi-reload scaleX-n1-rtl"></i>
              </button>
            </div>
            @endif
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
  @if(session()->has('success'))
    Swal.fire({
      title: 'Pemberitahuan',
      html: '{{session()->get('success')}}',
      icon: 'success',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('spk')}}");
  });

  $("#btn-ajukan-ulang").on("click",function(){
    Swal.fire({
      title: 'Konfirmasi',
      text: `Apakah Anda ingin mengajukan quotation ulang untuk SPK nomor {{$data->nomor}}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Ajukan Ulang',
      cancelButtonText: 'Batal',
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        // Memunculkan prompt untuk mengisi alasan
        Swal.fire({
          title: 'Masukkan Alasan',
          input: 'textarea',
          inputPlaceholder: 'Tuliskan alasan pengajuan ulang...',
          inputAttributes: {
            'aria-label': 'Tuliskan alasan pengajuan ulang'
          },
          showCancelButton: true,
          confirmButtonText: 'Ajukan',
          cancelButtonText: 'Batal',
          reverseButtons: true,
          preConfirm: (alasan) => {
            if (!alasan) {
              Swal.showValidationMessage('Alasan tidak boleh kosong');
              return false;
            }
            return alasan;
          }
        }).then((result) => {
          if (result.isConfirmed) {
            // Logika untuk memproses pengajuan ulang
            let alasan = result.value;
            Swal.fire({
              title: 'Berhasil!',
              text: 'Quotation diajukan ulang.',
              icon: 'success',
              timer: 2000,
              showConfirmButton: false
            });

            // Bangun URL dengan alasan
            let baseUrl = "{{ route('spk.ajukan-ulang-quotation', ['spk' => ':spk']) }}";
            let url = baseUrl.replace(':spk', {{$data->id}});
            // Tambahkan alasan sebagai parameter URL
            url += `?alasan=${encodeURIComponent(alasan)}`;

            location.href = url;
            console.log("gogo");

          } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire({
              title: 'Dibatalkan',
              text: 'Pengajuan ulang dibatalkan.',
              icon: 'info',
              timer: 2000,
              showConfirmButton: false
            });
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Dibatalkan',
          text: 'Pengajuan ulang dibatalkan.',
          icon: 'info',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  })

  $('#btn-upload-spk').on('click', function() {
        // Menampilkan SweetAlert dengan form upload
        Swal.fire({
            title: 'Upload File',
            html: `
                <form id="uploadForm" enctype="multipart/form-data">
                    <input style="width:80%" type="file" id="file" name="file" class="swal2-input" accept="application/pdf">
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            preConfirm: () => {
                const fileInput = document.getElementById('file');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('Silakan pilih file terlebih dahulu');
                    return false;
                }
                return fileInput.files[0];  // Mengembalikan file yang dipilih
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Mohon tunggu, file sedang diupload.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // Ambil file dari form SweetAlert
                var file = result.value;
                var formData = new FormData();
                formData.append('file', file);
                formData.append('id',{{$data->id}});
                formData.append('_token', '{{ csrf_token() }}'); // Pastikan ada CSRF token

                // Kirim file menggunakan AJAX
                $.ajax({
                    url: '{{route("spk.upload-spk")}}',  // URL untuk upload di Laravel
                    type: 'POST',
                    data: formData,
                    contentType: false,  // Jangan menetapkan tipe konten
                    processData: false,  // Jangan memproses data yang dikirim
                    success: function(response) {
                        Swal.fire('Berhasil!', 'File berhasil diupload', 'success');
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat upload', 'error');
                    }
                });
            }
        });
    });

</script>
@endsection
