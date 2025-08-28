@extends('layouts.master')
@section('title','SDT Training')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">SDT/ </span> Detail SDT Training</h4>

  <div class="row">
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <span>SDT Training Detail</span>
          </div>
        </h5>
        <div class="row">
          <div class="col-md-5">
            <div id="carouselExample" class="carousel slide" style="margin: 15px;">
              <div class="carousel-indicators">
                @foreach($listImage as $value)
                  <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{ $loop->index }}"
                          class="@if($loop->index == 0) active @endif" aria-current="true"
                          aria-label="Slide {{ $loop->index }}"></button>
                @endforeach
              </div>

              <div class="carousel-inner">
                @foreach($listImage as $value)
                <div class="carousel-item @if($loop->index == 0) active @endif" style="height: 450px; width:700px">
                  <img style="border-radius: 1%; width: 100%;max-height: 100%" src="{{$value->path}}" class="d-block w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>{{$value->nama}}</h5>
                    <p>{{$value->keterangan}}</p>
                  </div>
                </div>

                <div class="carousel-inner">
                @foreach($listImage as $value)
                  <div class="carousel-item @if($loop->index == 0) active @endif" style="height: 450px; width:700px">
                    <img style="border-radius: 1%; width: 100%;max-height: 100%"
                      src="{{$value->path}}"
                      class="d-block w-100 img-thumbnail preview-trigger"
                      data-index="{{ $loop->index }}"
                      alt="...">
                    <div class="carousel-caption d-none d-md-block">
                      <h5>{{$value->nama}}</h5>
                      <p>{{$value->keterangan}}</p>
                    </div>
                  </div>
                  <!-- <div class="col-md-3 mb-3">
                      <img src="{{$value->path}}"
                          class="img-thumbnail"
                          style="cursor:pointer"
                          data-bs-toggle="modal"
                          data-bs-target="#imageModal"
                          onclick="showImage('{{$value->path}}')"
                          >
                  </div> -->
                  @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>

              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>

          <div class="col-md-7">
            <form class="card-body" action="{{route('sdt-training.save')}}" method="POST">
              @csrf
              <input type="hidden" name="id" value="{{$data->id_training}}">

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Absensi Active</label>
                <div class="col-sm-9">
                  <div class="form-check form-switch">
                    <input style="width: 60px; height: 30px;" class="form-check-input" type="checkbox"
                           role="switch" id="enable" name="enable" @if($data->enable == '1') checked @endif>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Area</label>
                <div class="col-sm-4">
                  <select disabled id="area_id" name="area_id" class="form-select">
                    @foreach($listArea as $value)
                    <option value="{{$value->id}}" @if($data->id_area == $value->id) selected @endif>{{$value->area}}</option>
                    @endforeach
                  </select>
                </div>

                <label class="col-sm-2 col-form-label">Business Unit</label>
                <div class="col-sm-3">
                  <select disabled id="laman_id" name="laman_id" class="form-select">
                    @foreach($listBu as $value)
                    <option value="{{$value->id}}" @if($data->id_laman == $value->id) selected @endif>{{$value->laman}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Materi <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <select id="materi_id" name="materi_id" class="form-select @error('materi_id') is-invalid @enderror">
                    <option value="">- Pilih data -</option>
                    @foreach($listMateri as $value)
                    <option value="{{$value->id}}" @if($data->id_materi == $value->id) selected @endif>{{$value->nama}}</option>
                    @endforeach
                  </select>
                </div>

                <label class="col-sm-2 col-form-label">Tempat <span class="text-danger">*</span></label>
                <div class="col-sm-3">
                  <select id="tempat_id" name="tempat_id" class="form-select @error('tempat_id') is-invalid @enderror">
                    <option value="">- Pilih Tempat -</option>
                    <option value="1" @if($data->id_pel_tempat == '1') selected @endif>IN DOOR</option>
                    <option value="2" @if($data->id_pel_tempat == '2') selected @endif>OUT DOOR</option>
                  </select>
                  @error('tempat_id')
                    <div class="invalid-feedback">{{$message}}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Waktu Mulai <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <input type="datetime-local" id="start_date" name="start_date" value="{{$data->waktu_mulai}}"
                         class="form-control @error('start_date') is-invalid @enderror">
                  @error('start_date')
                    <div class="invalid-feedback">{{$message}}</div>
                  @enderror
                </div>

                <label class="col-sm-2 col-form-label">Waktu Selesai <span class="text-danger">*</span></label>
                <div class="col-sm-3">
                  <input type="datetime-local" id="end_date" name="end_date" value="{{$data->waktu_selesai}}"
                         class="form-control @error('end_date') is-invalid @enderror">
                  @error('end_date')
                    <div class="invalid-feedback">{{$message}}</div>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Alamat <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <textarea class="form-control" name="alamat" id="alamat" rows="3"
                            placeholder="Masukkan alamat">{{$data->alamat}}</textarea>
                </div>

                <label class="col-sm-2 col-form-label">Link Zoom <span class="text-danger">*</span></label>
                <div class="col-sm-3">
                  <textarea class="form-control" name="link_zoom" id="link_zoom" rows="3"
                            placeholder="Masukkan link zoom">{{$data->link_zoom}}</textarea>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Link Undangan</label>
                <div class="col-sm-7">
                  <input readonly type="text" id="link" name="link" value="{{$linkInvite}}" class="form-control">
                </div>
                <div class="col-sm-2">
                  <button type="button" class="btn btn-warning w-100" onclick="copyToClipboard('#link')">
                    <i class="mdi mdi-content-copy me-1"></i>Salin
                  </button>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Keterangan</label>
                <div class="col-sm-9">
                  <textarea class="form-control" name="keterangan" id="keterangan" rows="3"
                            placeholder="Masukkan keterangan">{{$data->keterangan}}</textarea>
                </div>
              </div>

              <div class="row">
                <div class="col-12 d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Update
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Action</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button id="btn-send-message" class="btn btn-info w-100">
              <span class="me-1">Kirim Undangan</span>
              <i class="mdi mdi-send scaleX-n1-rtl"></i>
            </button>

            <button id="btn-pesan-undangan" class="btn btn-success w-100">
              <span class="me-1">Pesan Undangan</span>
              <i class="mdi mdi-account-multiple-plus scaleX-n1-rtl"></i>
            </button>

            <form target="_blank" action="{{route('invite-pdf')}}" method="POST">
              @csrf
              <input type="hidden" name="training_id" value="{{$data->id_training}}"/>
              <button type="submit" class="btn btn-warning w-100">
                <span class="me-1">Generate PDF</span>
                <i class="mdi mdi-file-pdf-box scaleX-n1-rtl"></i>
              </button>
            </form>

            <button id="btn-kembali" class="btn btn-secondary w-100">
              <span class="me-1">Kembali</span>
              <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
            </button>

            <hr class="my-4 mx-4">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <span>Trainer</span>
          </div>
        </h5>
        <div class="card-body">
          <div class="table-responsive">
            <table id="table-data-trainer" class="dt-column-search table table-hover" style="text-wrap: nowrap;">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Divisi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {{-- data table ajax --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3"></div>
  </div>

  <div class="row">
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <span>Peserta Training</span>
          </div>
        </h5>
        <div class="card-body">
          @csrf
          <input type="hidden" id="training_id" value="{{$data->id_training}}">

          <div class="mb-3">
            <label class="form-label">Filter Perusahaan</label>
            <select id="nama_perusahaan" name="nama_perusahaan" class="form-select">
              <option value="">- Pilih Perusahaan -</option>
              @foreach($namaPerusahaan as $value)
              <option value="{{$value->id}}">{{$value->client}}</option>
              @endforeach
            </select>
          </div>

          <div class="table-responsive">
            <table id="table-data-client" class="dt-column-search table table-hover" style="text-wrap: nowrap;">
              <thead>
                <tr>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>No Whatsapp</th>
                  <th>Status Kirim</th>
                  <th>Hadir</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {{-- data table ajax --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3"></div>
  </div>

  <div class="row">
    <div class="col-md-9">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <span>Galeri Kegiatan</span>
          </div>
        </h5>
        <div class="card-body">
          @csrf
          <input type="hidden" id="training_id" value="{{$data->id_training}}">

          <div class="table-responsive">
            <table id="table-data-gallery" class="dt-column-search table table-hover" style="text-wrap: nowrap;">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Keterangan</th>
                  <th>Gambar</th>
                  <th>Created Date</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {{-- data table ajax --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3"></div>
  </div>
</div>

<div class="modal fade" id="modal-gallery" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Gallery</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{route('sdt-training.upload-image')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" value="{{$data->id_training}}">

          <div class="row mb-4">
            <label class="col-sm-3 col-form-label">Nama</label>
            <div class="col-sm-9">
              <input type="text" name="nama" class="form-control" placeholder="Masukkan nama gambar">
            </div>
          </div>

          <div class="row mb-4">
            <label class="col-sm-3 col-form-label">File</label>
            <div class="col-sm-9">
              <div class="file-upload-container">
                <div id="drop-zone" class="drop-zone">
                  <div class="drop-zone-content">
                    <i class="mdi mdi-cloud-upload-outline drop-zone-icon"></i>
                    <h6 class="drop-zone-title">Drag & Drop file di sini</h6>
                    <p class="drop-zone-subtitle">atau</p>
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file-input').click()">
                      <i class="mdi mdi-file-plus-outline me-1"></i>Choose Files
                    </button>
                    <input type="file" id="file-input" name="image" accept="image/*" style="display: none;">
                    <p class="drop-zone-info mt-2">
                      <small class="text-muted">Maksimal ukuran file: 5MB. Format: JPG, PNG, GIF</small>
                    </p>
                  </div>

                  <div id="file-preview" class="file-preview" style="display: none;">
                    <div class="preview-item">
                      <img id="preview-image" src="" alt="Preview" class="preview-img">
                      <div class="preview-info">
                        <p id="preview-filename" class="preview-filename"></p>
                        <p id="preview-filesize" class="preview-filesize"></p>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                          <i class="mdi mdi-delete-outline"></i> Hapus
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-4">
            <label class="col-sm-3 col-form-label">Keterangan</label>
            <div class="col-sm-9">
              <textarea class="form-control" name="keterangan" rows="4"
                        placeholder="Masukkan keterangan gambar"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-upload me-1"></i>Upload Gallery
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-link" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kirim Link Training</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <label class="col-sm-3 col-form-label">No Whatsapp <span class="text-danger">delimiter comma</span></label>
          <div class="col-sm-9">
            <input type="text" id="link-wa" class="form-control" placeholder="Masukkan nomor whatsapp">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btn-kirim-wa" class="btn btn-primary">Kirim</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-client" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <label class="col-sm-3 col-form-label">Client <span class="text-danger">*</span></label>
          <div class="col-sm-9">
            <select id="client_id" name="client_id" class="form-select">
              <option value="">- Pilih data -</option>
              @foreach($listClient as $value)
              <option value="{{$value->id}}">{{$value->client}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btn-add-client-save" class="btn btn-primary">Add Client</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-add-reminder" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-0">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Tambah Penerima Penerima</p></h4>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label text-sm-end">Nama</label>
            <div class="col-sm-8">
              <div class="position-relative">
                <input type="text" id="reminder-add-nama" name="reminder-add-nama" class="form-control"></input>
              </div>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label text-sm-end">Whatsapp</label>
            <div class="col-sm-8">
              <div class="position-relative" >
                <input type="text" id="reminder-add-wa" name="reminder-add-wa" class="form-control"></input>
              </div>
            </div>
        </div>

        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-add-reminder-penerima" class="btn btn-primary">Add Reminder</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-pesan-undangan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pesan Undangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <textarea class="form-control" name="pesan-undangan" id="pesan-undangan" rows="12"
                      placeholder="Masukkan pesan undangan">{{$message}}</textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btn-add-pesan-undangan-save" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-reminder-whatsapp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2">Reminder Whatsapp : <p id="nama"></p></h4>
        </div>
        <br>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label text-sm-end">Dikirim Sebelum (Hari)</label>
            <div class="col-sm-8">
              <div class="position-relative">
                <input type="number" id="reminder-days" name="reminder-days" class="form-control"></input>
              </div>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label text-sm-end">Status</label>
            <div class="col-sm-8">
              <div class="position-relative" >
                <input readonly type="text" id="reminder-status" name="reminder-status" class="reminder-status form-control"></input>
              </div>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label text-sm-end">Reminder</label>
            <div class="col-sm-8">
              <div class="position-relative">
                <textarea style="height: 100% !important;" rows="12" cols="50" class="form-control h-px-100 @if ($errors->any())   @endif" name="pesan-reminder" id="pesan-reminder" placeholder=""></textarea>
              </div>
            </div>
        </div>

        <div class="table-responsive overflow-hidden">
          <table id="table-data-reminder" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
              <thead>
              <!-- no, nik, nama, no whatsapp, aksi -->
                  <tr>
                      <th class="text-left">Nama</th>
                      <th class="text-left">No Whatsapp</th>
                      <th class="text-center">Aksi</th>
                  </tr>
              </thead>
              <tbody>
                  {{-- data table ajax --}}
              </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="btn-reminder-save" class="btn btn-primary">Save Notif</button>
        <button id="btn-reminder-penerima" class="btn btn-success">Tambah Penerima</button>
        <!-- <button id="btn-add-trainer" class="btn btn-warning w-100 waves-effect waves-light"> -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-peserta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Peserta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <label class="col-sm-3 col-form-label">Peserta <span class="text-danger">*</span></label>
          <div class="col-sm-9">
            <select id="peserta_id" name="peserta_id" class="form-select">
              <option value="">- Pilih data -</option>
              @foreach($listPeserta as $value)
              <option value="{{$value->id}}">{{$value->full_name . ' - ' .$value->position}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btn-add-peserta-save" class="btn btn-primary">Add Peserta</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-trainer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Trainer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <label class="col-sm-3 col-form-label">Trainer <span class="text-danger">*</span></label>
          <div class="col-sm-9">
            <select id="trainer_id" name="trainer_id" class="form-select">
              <option value="">- Pilih data -</option>
              @foreach($listTrainer as $value)
              @if($value->id==99) @continue @endif
              <option value="{{$value->id}}">{{$value->trainer}}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button id="btn-add-trainer-save" class="btn btn-primary">Add Trainer</button>
      </div>
    </div>
  </div>
</div>

<style>
.drop-zone {
  border: 2px dashed #d4d4d8;
  border-radius: 8px;
  padding: 40px 20px;
  text-align: center;
  transition: all 0.3s ease;
  background-color: #fafafa;
}

.drop-zone:hover {
  border-color: #3b82f6;
  background-color: #f8faff;
}

.drop-zone.dragover {
  border-color: #3b82f6;
  background-color: #eff6ff;
  transform: scale(1.02);
}

.drop-zone-icon {
  font-size: 3rem;
  color: #9ca3af;
  margin-bottom: 1rem;
}

.drop-zone-title {
  color: #374151;
  margin-bottom: 0.5rem;
}

.drop-zone-subtitle {
  color: #6b7280;
  margin-bottom: 1rem;
}

.drop-zone-info {
  color: #6b7280;
}

.file-preview {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background-color: #f9fafb;
}

.preview-item {
  display: flex;
  align-items: center;
  gap: 15px;
}

.preview-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
  border: 2px solid #e5e7eb;
}

.preview-info {
  flex: 1;
}

.preview-filename {
  font-weight: 600;
  color: #374151;
  margin-bottom: 5px;
}

.preview-filesize {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 10px;
}

.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.btn {
  border-radius: 6px;
}

.form-control, .form-select {
  border-radius: 6px;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  border-top: 1px solid #dee2e6;
}
</style>

@endsection

@section('pageScript')
<script>
// Copy to clipboard function
function copyToClipboard(element) {
  navigator.clipboard.writeText($(element).val());
  Swal.fire({
    title: 'Pemberitahuan',
    text: 'Berhasil Copy Link',
    icon: 'success',
    customClass: {
      confirmButton: 'btn btn-primary waves-effect waves-light'
    },
    buttonsStyling: false
  });
}

// Drag & Drop functionality
document.addEventListener('DOMContentLoaded', function() {
  const dropZone = document.getElementById('drop-zone');
  const fileInput = document.getElementById('file-input');
  const filePreview = document.getElementById('file-preview');
  const previewImage = document.getElementById('preview-image');
  const previewFilename = document.getElementById('preview-filename');
  const previewFilesize = document.getElementById('preview-filesize');

  // Prevent default drag behaviors
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
  });

  // Highlight drop zone when item is dragged over it
  ['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
  });

  // Handle dropped files
  dropZone.addEventListener('drop', handleDrop, false);
  fileInput.addEventListener('change', handleFiles, false);

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  function highlight(e) {
    dropZone.classList.add('dragover');
  }

  function unhighlight(e) {
    dropZone.classList.remove('dragover');
    }

  function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files; // Assign files to the input element
    handleFiles();
  }

  function handleFiles() {
    const files = fileInput.files;
    if (files.length > 0) {
      const file = files[0];
      const reader = new FileReader();

      reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewFilename.textContent = file.name;
        previewFilesize.textContent = (file.size / 1024).toFixed(2) + ' KB';
        filePreview.style.display = 'flex';
        dropZone.querySelector('.drop-zone-content').style.display = 'none';
      };

      reader.readAsDataURL(file);
    }
  }

  window.removeFile = function() {
    fileInput.value = ''; // Clear the input
    filePreview.style.display = 'none';
    dropZone.querySelector('.drop-zone-content').style.display = 'block';
  }
});

// DataTables for Trainer, Client, and Gallery
$(document).ready(function() {
  const trainingId = $('#training_id').val();
  const csrfToken = $('input[name="_token"]').val();

  // Initialize Trainer table
  const trainerTable = $('#table-data-trainer').DataTable({
    processing: true,
    serverSide: true,
    scrollX: true,
    ajax: {
      url: "{{ route('sdt-training.data-trainer') }}",
      data: function(d) {
        d.training_id = trainingId;
      }
    },
    columns: [{
      data: 'nama',
      name: 'nama'
    }, {
      data: 'divisi',
      name: 'divisi'
    }, {
      data: 'aksi',
      name: 'aksi',
      orderable: false,
      searchable: false,
      className: 'text-center'
    }],
    dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
    buttons: [{
      text: '<i class="mdi mdi-account-multiple-outline me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Trainer</span>',
      className: 'btn btn-primary waves-effect waves-light',
      action: function() {
        $('#modal-trainer').modal('show');
      }
    }]
  });

  // Initialize Client table
  const clientTable = $('#table-data-client').DataTable({
    processing: true,
    serverSide: true,
    scrollX: true,
    ajax: {
      url: "{{ route('sdt-training.client-peserta') }}",
      data: function(d) {
        d.training_id = trainingId;
        d.client_id = $('#nama_perusahaan').val();
      }
    },
    columns: [{
      data: 'nik',
      name: 'nik'
    }, {
      data: 'nama',
      name: 'nama'
    }, {
      data: 'no_whatsapp',
      name: 'no_whatsapp'
    }, {
      data: 'status_whatsapp',
      name: 'status_whatsapp'
    }, {
      data: 'status_hadir',
      name: 'status_hadir'
    }, {
      data: 'aksi',
      name: 'aksi',
      orderable: false,
      searchable: false,
      className: 'text-center'
    }],
    dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
    buttons: [{
      text: '<i class="mdi mdi-account-multiple-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Peserta</span>',
      className: 'btn btn-primary waves-effect waves-light',
      action: function() {
        $('#modal-peserta').modal('show');
      }
    }]
  });

  $('#nama_perusahaan').on('change', function() {
    clientTable.draw();
  });

  // Initialize Gallery table
  const galleryTable = $('#table-data-gallery').DataTable({
    processing: true,
    serverSide: true,
    scrollX: true,
    ajax: {
      url: "{{ route('sdt-training.data-galeri') }}",
      data: function(d) {
        d.training_id = trainingId;
      }
    },
    columns: [{
      data: 'nama',
      name: 'nama'
    }, {
      data: 'keterangan',
      name: 'keterangan'
    }, {
      data: 'path',
      name: 'path'
    }, {
      data: 'created_at',
      name: 'created_at'
    }, {
      data: 'aksi',
      name: 'aksi',
      orderable: false,
      searchable: false,
      className: 'text-center'
    }],
    dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
    buttons: [{
      text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Gambar Kegiatan</span>',
      className: 'btn btn-primary waves-effect waves-light',
      action: function() {
        $('#modal-gallery').modal('show');
      }
    }]
  });

  // Action Buttons
  $('#btn-pesan-undangan').on('click', function() {
    $('#modal-pesan-undangan').modal('show');
  });

  $('#btn-kembali').on('click', function() {
    window.location.href = "{{ route('sdt-training') }}";
  });

  $('#btn-add-client').on('click', function() {
    $('#modal-client').modal('show');
  });

  $('#btn-send-message').on('click', function() {
    $('#modal-link').modal('show');
  });

  // Modal Save actions
  $('#btn-add-pesan-undangan-save').on('click', function() {
    const pesan = $('#pesan-undangan').val();
    $.ajax({
      url: "{{ route('sdt-training.save-message') }}",
      method: 'POST',
      data: {
        _token: csrfToken,
        id: trainingId,
        pesan_undangan: pesan
      },
      success: function(response) {
        Swal.fire({
          title: 'Berhasil',
          text: response.message,
          icon: 'success',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        }).then(() => {
          $('#modal-pesan-undangan').modal('hide');
        });
      },
      error: function() {
        Swal.fire({
          title: 'Error!',
          text: 'Terjadi kesalahan saat menyimpan pesan.',
          icon: 'error',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      }
    });
  });

  $('#btn-kirim-wa').on('click', function() {
    const noWa = $('#link-wa').val();

    // Tutup modal terlebih dahulu
    $('#modal-link').modal('hide');

    // Tunda SweetAlert sebentar untuk memastikan modal sudah tertutup
    setTimeout(() => {
        Swal.fire({
          title: 'Konfirmasi',
          text: 'Apakah anda ingin kirim undangan whatsapp ?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: 'info',
          cancelButtonColor: 'warning',
          confirmButtonText: 'Kirim Whatsapp'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              type: "POST",
              url: "{{ route('sdt-training.send-message') }}",
              data: {
                id: trainingId,
                no_wa: noWa,
                _token: csrfToken
              },
              success: function(response) {
                Swal.fire({
                  title: 'Pemberitahuan',
                  text: response.message,
                  icon: 'success',
                  timer: 1000,
                  timerProgressBar: true
                });
              },
              error: function() {
                Swal.fire({
                  title: 'Pemberitahuan',
                  text: 'Terjadi kesalahan saat mengirim pesan.',
                  icon: 'error'
                });
              }
            });
          }
        });
    }, 300); // Penundaan 300ms untuk memastikan modal benar-benar hilang
  });

  $('#btn-add-client-save').on('click', function() {
    const clientId = $('#client_id').val();
    if (clientId === '') {
      Swal.fire({
        title: 'Pemberitahuan',
        text: "Mohon untuk memilih data client yang akan di tambahkan",
        icon: 'error'
      });
      return;
    }
    $.ajax({
      type: "POST",
      url: "{{ route('sdt-training.add-client') }}",
      data: {
        id: trainingId,
        client_id: clientId,
        _token: csrfToken
      },
      success: function(response) {
        if (response.success) {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'success',
            timer: 1000,
            timerProgressBar: true,
            willClose: () => {
              location.reload();
            }
          });
        } else {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Pemberitahuan',
          text: 'Terjadi kesalahan saat menambahkan client.',
          icon: 'error'
        });
      }
    });
  });

  $('#btn-add-peserta-save').on('click', function() {
    const pesertaId = $('#peserta_id').val();
    const clientId = $('#nama_perusahaan').val();
    if (clientId === '') {
      Swal.fire({
        title: 'Pemberitahuan',
        text: "Mohon untuk memilih nama perusahaan terlebih dahulu",
        icon: 'error'
      });
      return;
    }
    $.ajax({
      type: "POST",
      url: "{{ route('sdt-training.add-peserta') }}",
      data: {
        id: trainingId,
        client_id: clientId,
        employee_id: pesertaId,
        _token: csrfToken
      },
      success: function(response) {
        if (response.success) {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'success',
            timer: 1000,
            timerProgressBar: true,
            willClose: () => {
              clientTable.ajax.reload();
              $('#modal-peserta').modal('hide');
            }
          });
        } else {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Pemberitahuan',
          text: 'Terjadi kesalahan saat menambahkan peserta.',
          icon: 'error'
        });
      }
    });
  });

  $('#btn-add-trainer-save').on('click', function() {
    const trainerId = $('#trainer_id').val();
    $.ajax({
      type: "POST",
      url: "{{ route('sdt-training.add-trainer') }}",
      data: {
        id: trainingId,
        trainer_id: trainerId,
        _token: csrfToken
      },
      success: function(response) {
        if (response.success) {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'success',
            timer: 1000,
            timerProgressBar: true,
            willClose: () => {
              trainerTable.ajax.reload();
              $('#modal-trainer').modal('hide');
            }
          });
        } else {
          Swal.fire({
            title: 'Pemberitahuan',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Pemberitahuan',
          text: 'Terjadi kesalahan saat menambahkan trainer.',
          icon: 'error'
        });
      }
    });
  });

  // Delete actions
  $('body').on('click', '.btn-delete-trainer', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah anda ingin hapus trainer?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "{{ route('sdt-training.delete-trainer') }}",
          data: {
            id: id,
            _token: csrfToken
          },
          success: function(response) {
            Swal.fire({
              title: 'Pemberitahuan',
              text: response.message,
              icon: 'success',
              timer: 1000,
              timerProgressBar: true,
              willClose: () => {
                trainerTable.ajax.reload();
              }
            });
          },
          error: function() {
            Swal.fire({
              title: 'Pemberitahuan',
              text: 'Terjadi kesalahan saat menghapus trainer.',
              icon: 'error'
            });
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-delete-gallery', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah anda ingin hapus gallery?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "{{ route('sdt-training.delete-gallery') }}",
          data: {
            id: id,
            _token: csrfToken
          },
          success: function(response) {
            Swal.fire({
              title: 'Pemberitahuan',
              text: response.message,
              icon: 'success',
              timer: 1000,
              timerProgressBar: true,
              willClose: () => {
                location.reload();
              }
            });
          },
          error: function() {
            Swal.fire({
              title: 'Pemberitahuan',
              text: 'Terjadi kesalahan saat menghapus galeri.',
              icon: 'error'
            });
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-delete-peserta', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Konfirmasi',
      text: 'Apakah anda ingin hapus peserta?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Hapus'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: "{{ route('sdt-training.delete-peserta') }}",
          data: {
            id: id,
            _token: csrfToken
          },
          success: function(response) {
            Swal.fire({
              title: 'Pemberitahuan',
              text: response.message,
              icon: 'success',
              timer: 1000,
              timerProgressBar: true,
              willClose: () => {
                clientTable.ajax.reload();
              }
            });
          },
          error: function() {
            Swal.fire({
              title: 'Pemberitahuan',
              text: 'Terjadi kesalahan saat menghapus peserta.',
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
