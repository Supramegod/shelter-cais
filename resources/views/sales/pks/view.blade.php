@extends('layouts.master')
@section('title','PKS')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Lihat PKS</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form PKS <span class="badge @if($data->status_pks_id==1 || $data->status_pks_id==2 || $data->status_pks_id==3 || $data->status_pks_id==4 || $data->status_pks_id==5 ) bg-label-warning @elseif($data->status_pks_id==6 ) bg-label-info @elseif($data->status_pks_id==7 ) bg-label-success @endif rounded-pill mt-1">{{$data->status}}</span></span>
            <span style="font-weight:bold;color:#000">{{$data->nomor}} - {{$data->stgl_pks}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('leads.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <h6>1. Informasi Leads / Customer</h6>
          <div class="row mb-3">
            <div class="table-responsive">
              <table class="table">
                <tbody>
                    <tr>
                        <td>Nomor</td>
                        <td colspan="3">: <a href="#"><b>{{$leads->nomor ?? '-'}}</b></a></td>
                    </tr>
                  <tr>
                    <td>Nama Perusahaan</td>
                    <td>: {{$leads->nama_perusahaan}}</td>
                    <td>Bidang Perusahaan</td>
                    <td>: {{$leads->bidang_perusahaan}}</td>
                  </tr>
                <tr>
                    <td>PMA</td>
                    <td>: {{$leads->pma ?? '-'}}</td>
                    <td>Negara</td>
                    <td>: {{$leads->negara ?? '-'}}</td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>: {{$leads->provinsi ?? '-'}}</td>
                    <td>Kota</td>
                    <td>: {{$leads->kota ?? '-'}}</td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>: {{$leads->kecamatan ?? '-'}}</td>
                    <td>Kelurahan</td>
                    <td>: {{$leads->kelurahan ?? '-'}}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td colspan="3">: {{$leads->alamat ?? '-'}}</td>
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
                    <th class="text-center">No.</th>
                    <th class="text-center">Nomor</th>
                    <th class="text-center">Kebutuhan</th>
                    <th class="text-center">Jenis Kontrak</th>
                    <th class="text-center">Checklist</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($listQuotation as $index => $quotation)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td><b><a href="{{route('quotation.view',[$quotation->id])}}">{{$quotation->nomor}}</a></b></td>
                        <td>{{$quotation->kebutuhan}}</td>
                        <td>{{$quotation->jenis_kontrak}}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <a href="{{ route('pks.isi-checklist', ['id' => $quotation->id, 'pks_id' => $data->id]) }}" class="btn btn-primary" title="Isi Checklist">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                @if($quotation->materai !=null)
                                <a onclick="window.open('{{route('quotation.cetak-checklist', ['id' => $quotation->id, 'pks_id' => $data->id])}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" class="btn btn-warning" title="Cetak Checklist">
                                    <i class="mdi mdi-printer"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <h6>3. Informasi SPK</h6>
          <div class="row mb-3">
            <div class="table-responsive overflow-hidden table-spk">
              <table id="table-spk" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                     <th class="text-center">No.</th>
                     <th class="text-center">Nomor</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($listSpk as $index => $spk)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td><b><a href="{{route('spk.view',[$spk->id])}}">{{$spk->nomor}}</a></b></td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <h6>4. Informasi Site</h6>
          <div class="row mb-3">
            <div class="table-responsive overflow-hidden table-site">
              <table id="table-site" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                     <th class="text-center">No.</th>
                     <th class="text-center">Nama Site</th>
                     <th class="text-center">Kota</th>
                     <th class="text-center">Penempatan</th>
                  </tr>
                </thead>
                <tbody id="tbody-site">
                    @foreach($data->site as $index => $site)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$site->nama_site}}</td>
                        <td>{{$site->kota}}</td>
                        <td>{{$site->penempatan}}</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <h6>3. Perjanjian Kerja Sama</h6>
            @foreach($perjanjian as $key => $value)
            <div class="row mb-1">
              <div class="table-responsive overflow-hidden table-quotation">
                <table id="table-quotation" class="dt-column-search table w-100 table-hover">
                  <thead>
                    <tr>
                    <th style="text-align:center">{{$value->pasal}} &nbsp; &nbsp; <a href="{{route('pks.edit-perjanjian',$value->id)}}" class="btn btn-warning"><i class="mdi mdi-pencil"></i></a></th>
                    </tr>
                    <tr>
                      <th style="text-align:center">{{$value->judul}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{!!$value->raw_text!!}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            @endforeach
        </form>
      </div>
    </div>
    <div class="col-md-3">
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
          <!-- <div class="col-12 text-center mt-2">
              <a onclick="window.open('{{route('pks.cetak-pks',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" id="btn-download-pks" class="btn btn-warning w-100 waves-effect waves-light">
                <span class="me-1">Download PKS</span>
                <i class="mdi mdi-download scaleX-n1-rtl"></i>
              </a>
            </div> -->
          <!-- <hr class="my-4 mx-4"> -->
           @if($data->link_pks_disetujui !=null)
          <div class="col-12 text-center mt-2">
            <a target="_blank" href="{{$data->link_pks_disetujui}}" id="btn-lihat-pks" class="btn btn-success w-100 waves-effect waves-light">
              <span class="me-1">Lihat PKS</span>
              <i class="mdi mdi-download scaleX-n1-rtl"></i>
            </a>
          </div>
          @else
          <div class="col-12 text-center mt-2">
            <a onclick="window.open('{{route('pks.cetak-pks',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" id="btn-download-pks" class="btn btn-warning w-100 waves-effect waves-light">
                <span class="me-1">Download PKS</span>
                <i class="mdi mdi-download scaleX-n1-rtl"></i>
              </a>
          </div>
          @endif
          @if($data->status_pks_id == 1 && Auth::user()->role_id==96)
          <div class="col-12 text-center mt-2">
            <button class="btn btn-primary w-100 waves-effect waves-light" id="approve-pks" data-id="{{$data->id}}" data-ot="1"><i class="mdi mdi-draw-pen"></i>&nbsp; Approval Direktur Sales</button>
          </div>
          @elseif($data->status_pks_id == 2 && in_array(Auth::user()->role_id,[97,40]))
          <div class="col-12 text-center mt-2">
            <button class="btn btn-primary w-100 waves-effect waves-light" id="approve-pks" data-id="{{$data->id}}" data-ot="2"><i class="mdi mdi-draw-pen"></i>&nbsp; Approval Direktur Keuangan</button>
          </div>
          @elseif($data->status_pks_id == 3 && Auth::user()->role_id==53)
          <div class="col-12 text-center mt-2">
            <button class="btn btn-primary w-100 waves-effect waves-light" id="approve-pks" data-id="{{$data->id}}" data-ot="3"><i class="mdi mdi-draw-pen"></i>&nbsp; Approval GM HRM</button>
          </div>
          @elseif($data->status_pks_id == 4 && Auth::user()->role_id==99)
          <!-- <div class="col-12 text-center mt-2">
            <button class="btn btn-primary w-100 waves-effect waves-light" id="approve-pks" data-id="{{$data->id}}" data-ot="4"><i class="mdi mdi-draw-pen"></i>&nbsp; Approval Direktur Utama</button>
          </div> -->
          @elseif($data->status_pks_id == 5)
          <div class="col-12 text-center mt-2">
            <button id="btn-upload-pks" class="btn btn-primary w-100 waves-effect waves-light">
              <span class="me-1">Upload PKS</span>
              <i class="mdi mdi-upload scaleX-n1-rtl"></i>
            </button>
          </div>
          @elseif($data->status_pks_id == 6 && in_array(Auth::user()->role_id, [56, 2]))
          <div class="col-12 text-center mt-2">
            <button class="btn btn-info w-100 waves-effect waves-light" id="aktifkan-site" data-id="{{$data->id}}">
              <span class="me-1">Aktifkan Site</span>
              <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
            </button>
          </div>
          @elseif($data->status_pks_id == 7 && in_array(Auth::user()->role_id, [56, 2]))
            <div class="col-12 text-center mt-2">
                <button class="btn btn-success w-100 waves-effect waves-light">
                    <span class="me-1">Site Aktif</span>
                    <i class="mdi mdi-check"></i>
                </button>
            </div>
          @endif
          <div class="col-12 text-center mt-2">
            @if(!$data->isLowongan)
            <button class="btn btn-warning w-100 waves-effect waves-light" id="buat-lowongan" data-id="{{$data->id}}">
              <span class="me-1">Buat Lowongan</span>
              <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
            </button>
            @else
            <button class="btn btn-success w-100 waves-effect waves-light">
                <span class="me-1">Lowongan Terbentuk</span>
                <i class="mdi mdi-check"></i>
            </button>
            @endif
          </div>
          <hr class="my-4 mx-4">
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
            </div>
            <br>
            <hr>
            @if($data->status_pks_id != 7)
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
    window.location.replace("{{route('pks')}}");
  });

  $('#btn-ajukan-ulang').on('click',function () {
    Swal.fire({
      title: 'Konfirmasi',
      text: `Apakah Anda ingin mengajukan quotation ulang untuk PKS nomor {{$data->nomor}} ?`,
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
            let baseUrl = "{{ route('pks.ajukan-ulang-quotation', ['pks' => ':pks']) }}";
            let url = baseUrl.replace(':pks', {{$data->id}});
            // Tambahkan alasan sebagai parameter URL
            url += `?alasan=${encodeURIComponent(alasan)}`;

            location.href = url;
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
  });


  $('#btn-upload-pks').on('click', function() {
        @if(!$data->isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum Upload PKS.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
        @endif
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

                // Validasi jika file tidak dipilih
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('Silakan pilih file terlebih dahulu');
                    return false;
                }

                const file = fileInput.files[0];
                const maxSize = 4 * 1024 * 1024; // 4MB dalam byte

                // Validasi ukuran file
                if (file.size > maxSize) {
                    Swal.showValidationMessage('Ukuran file terlalu besar! Maksimum 4MB.');
                    return false;
                }

                return file; // Mengembalikan file yang dipilih
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Ambil file dari form SweetAlert
                var file = result.value;
                var formData = new FormData();
                formData.append('file', file);
                formData.append('id',{{$data->id}});
                formData.append('_token', '{{ csrf_token() }}'); // Pastikan ada CSRF token

                // Kirim file menggunakan AJAX
                $.ajax({
                    url: '{{route("pks.upload-pks")}}',  // URL untuk upload di Laravel
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

    $('body').on('click', '#approve-pks', function() {
        @if(!$data->isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum approve PKS.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
        @endif
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menyetujui data ini ?",
      showCancelButton: true,
      confirmButtonText: "Approve",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "ot":$(this).data('ot'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('pks.approve')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil disetujui.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });

$('body').on('click', '#buat-lowongan', function() {
    @if($data->status_pks_id != 7)
        Swal.fire({
            icon: 'warning',
            title: 'Site Belum Aktif',
            text: 'Silakan aktifkan site terlebih dahulu sebelum membuat lowongan.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
    @endif
    Swal.fire({
        icon: "question",
        title: "Apakah anda yakin ingin membuat lowongan untuk PKS ini?",
        showCancelButton: true,
        confirmButtonText: "Buat Lowongan",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Now loading',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            axios.post("{{ route('pks.buat-lowongan') }}", {
                id: $(this).data('id'),
                _token: "{{ csrf_token() }}"
            })
            .then(function(response) {
                Swal.close();
                Swal.fire({
                    title: "Pemberitahuan",
                    html: "Lowongan berhasil dibuat.",
                    icon: "success",
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer) {
                        location.reload();
                    }
                });
            })
            .catch(function(error) {
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    html: "Terjadi kesalahan saat membuat lowongan.",
                    icon: "error",
                });
                Swal.close();
            });
        }
    });
});
  $('body').on('click', '#aktifkan-site', function() {
    @if(!$data->isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum mengaktifkan site.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
    @endif
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin mengaktifkan data ini ?",
      showCancelButton: true,
      confirmButtonText: "Aktifkan",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Now loading',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });

        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('pks.aktifkan-site')}}",
          data:formData,
          success: function(response){
            if (response.status == 'error') {
              Swal.fire({
                title: "Pemberitahuan",
                html: response.message,
                icon: "error",
              });
              return;
            }

            Swal.close();
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil diaktifkan.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          },
          error:function(error){
            Swal.fire({
                title: "Terjadi Kesalahan",
                html: "Terjadi kesalahan saat memproses permintaan.",
                icon: "error",
            });
            console.log(error);
            Swal.close();
          }
        });
      }
    });
  });
</script>
@endsection
