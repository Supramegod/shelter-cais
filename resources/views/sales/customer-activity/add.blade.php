@extends('layouts.master')
@section('title','Customer Activity')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Customer Activity Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Customer Activity</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-leads"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('customer-activity.save')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <h6>1. Informasi Leads</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Leads / customer <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="hidden" id="jumlah_site" name="jumlah_site" value="0" class="form-control">
              <input type="hidden" id="leads_id" name="leads_id" value="{{old('leads_id')}}" class="form-control">
              <input type="hidden" id="email_leads" name="email_leads" value="{{old('email_leads')}}" class="form-control">
              <input type="hidden" id="email_sales" name="email_sales" value="{{old('email_sales')}}" class="form-control">
              <input type="hidden" id="email_branch_manager" name="email_branch_manager" value="{{old('email_branch_manager')}}" class="form-control">
              <input type="hidden" id="leads" name="leads" value="{{old('leads')}}">
              <div class="input-group">
                <div class="form-control bg-light d-flex align-items-center" style="border-right: 0;">
                  <span id="leads-display" class="text-muted">Pilih leads/customer</span>
                </div>
                <button class="btn btn-outline-secondary" type="button" id="btn-modal-cari-leads">
                  <i class="mdi mdi-magnify"></i>
                </button>
              </div>
              @if($errors->has('leads'))
                <div class="text-danger small mt-1">{{$errors->first('leads')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Tanggal Activity <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="date" id="tgl_activity" name="tgl_activity" value="@if(old('tgl_activity')==null){{$nowd}}@else{{old('tgl_activity')}}@endif" class="form-control @if ($errors->any()) @if($errors->has('tgl_activity')) is-invalid @else   @endif @endif">
                @if($errors->has('tgl_activity'))
                  <div class="invalid-feedback">{{$errors->first('tgl_activity')}}</div>
                @endif
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Wilayah</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="branch-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="branch" name="branch" value="{{old('branch')}}">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="kebutuhan-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="kebutuhan" name="kebutuhan" value="{{old('kebutuhan')}}">
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Tim Sales</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="tim-sales-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="tim_sales_name" name="tim_sales_name" value="{{old('tim_sales_name')}}">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Sales</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="sales-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="sales_name" name="sales_name" value="{{old('sales_name')}}">
            </div>
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">CRM</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="crm-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="crm_name" name="crm_name" value="{{old('crm_name')}}">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">RO</label>
            <div class="col-sm-4">
              <div class="form-control bg-light d-flex align-items-center">
                <span id="ro-display" class="text-dark">-</span>
              </div>
              <input type="hidden" id="ro_name" name="ro_name" value="{{old('ro_name')}}">
            </div>
          </div>
          @if(in_array(Auth::user()->role_id,[29,30,31,32,33]))
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Status Leads <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <select id="status_leads_id" name="status_leads_id" class="form-select @if ($errors->any()) @if($errors->has('status_leads_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih data -</option>
                @foreach($statusLeads as $value)
                <option value="{{$value->id}}" @if(old('status_leads_id') == $value->id) selected @endif>{{$value->nama}}</option>
                @endforeach
              </select>
              @if($errors->has('status_leads_id'))
                <div class="invalid-feedback">{{$errors->first('status_leads_id')}}</div>
              @endif
            </div>
          </div>
          @endif

          <!-- JIKA SALES HANYA DIA SENDIRI -->
          @if(in_array(Auth::user()->role_id,[29]))
          <input type="hidden" name="tim_sales_id" value="{{$tim_sales_id}}">
          <input type="hidden" name="tim_sales_d_id" value="{{$tim_sales_d_id}}">
          @endif

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Notes</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100" name="notes" id="notes" placeholder="">{{old('notes')}}</textarea>
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>2. Customer Activity</h6>
          <input type="hidden" name="tipe" value="" />
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pilih Activity <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <select id="tipe-select" name="tipe" class="form-select @if ($errors->any()) @if($errors->has('tipe')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih Activity -</option>
                @if(in_array(Auth::user()->role_id,[2,30,31,33]))
                <option value="Pilih Sales" @if(old('tipe') == 'Pilih Sales') selected @endif>Pilih Sales</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,54,55,8,52,10,53]))
                <option value="Pilih RO" @if(old('tipe') == 'Pilih RO') selected @endif>Pilih RO</option>
                @endif
                @if(in_array(Auth::user()->role_id,[55,52,10,53]))
                <option value="Pilih CRM" @if(old('tipe') == 'Pilih CRM') selected @endif>Pilih CRM</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,30,29,31,33,4,5,6,8,52,10,53]))
                <option value="Telepon" @if(old('tipe') == 'Telepon') selected @endif>Telepon</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,29,31,33,4,5,6,8,52,10,53]))
                <option value="Online Meeting" @if(old('tipe') == 'Online Meeting') selected @endif>Online Meeting</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,29,31,33,4,5,6,8,52,10,53]))
                <option value="Email" @if(old('tipe') == 'Email') selected @endif>Email</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,29,31,33,4,5,6,8,52,10,53]))
                <option value="Kirim Berkas" @if(old('tipe') == 'Kirim Berkas') selected @endif>Kirim Berkas</option>
                @endif
                @if(in_array(Auth::user()->role_id,[2,29,31,33,4,5,6,8,52,10,53]))
                <option value="Visit" @if(old('tipe') == 'Visit') selected @endif>Visit</option>
                @endif
              </select>
              @if($errors->has('tipe'))
                <div class="invalid-feedback">{{$errors->first('tipe')}}</div>
              @endif
            </div>
            <div class="row mb-3" id="activity-form-container" style="display: none;">
            <div class="col-sm-12">
              <div class="d-tim-sales">
                <div class="row">
                  <label class="col-sm-3 col-form-label text-sm-end @if(in_array(Auth::user()->role_id,[31])) d-none @endif">Tim Sales <span class="text-danger">*</span></label>
                  <div class="col-sm-4 @if(in_array(Auth::user()->role_id,[31])) d-none @endif">
                    <div class="position-relative">
                      <select id="tim_sales_id" name="tim_sales_id" class="form-select @if ($errors->any()) @if($errors->has('tim_sales_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                        @foreach($timSales as $value)
                        <option value="{{$value->id}}" @if(old('tim_sales_id') == $value->id) selected @endif>{{$value->nama}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('tim_sales_id'))
                        <div class="invalid-feedback">{{$errors->first('tim_sales_id')}}</div>
                      @endif
                    </div>
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">Sales <span class="text-danger">*</span></label>
                  <div class="@if(in_array(Auth::user()->role_id,[31])) col-sm-10 @else col-sm-3 @endif">
                    <input type="hidden" name="sales_d" id="sales_d">
                    <select id="tim_sales_d_id" name="tim_sales_d_id" class="form-select @if ($errors->any()) @if($errors->has('tim_sales_d_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                    </select>
                    @if($errors->has('tim_sales_d_id'))
                        <div class="invalid-feedback">{{$errors->first('tim_sales_d_id')}}</div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="d-ro">
                <div class="row">
                  <label class="col-sm-3 col-form-label text-sm-end">Supervisor</label>
                  <div class="col-sm-9">
                    <select id="spv_ro" name="spv_ro" class="form-select @if ($errors->any()) @endif" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($spvRoList as $value)
                      <option value="{{$value->id}}" @if(old('ro') == $value->id) selected @endif>{{$value->full_name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-3 col-form-label text-sm-end">RO</label>
                  <div class="col-sm-9">
                    <div class="row">
                      <div class="col-md-8">
                      <select id="ro" name="ro" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($roList as $value)
                        <option value="{{$value->id}}" @if(old('ro') == $value->id) selected @endif>{{$value->full_name}}</option>
                        @endforeach
                      </select>
                      </div>
                      <div class="col-md-4">
                        <button type="button" id="addButton" class="btn btn-primary w-100">Tambah</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-sm-12">
                    <table class="table table-bordered" style="">
                      <thead class="table-light">
                        <tr>
                          <th>#</th>
                          <th>Nama RO</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="itemTable">
                        <!-- Data akan ditambahkan di sini -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="d-crm">
                <div class="row mt-3">
                  <label class="col-sm-3 col-form-label text-sm-end">CRM</label>
                  <div class="col-sm-9">
                    <div class="row">
                      <div class="col-md-8">
                      <select id="crm" name="crm" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($crmList as $value)
                        <option value="{{$value->id}}" @if(old('crm') == $value->id) selected @endif>{{$value->full_name}}</option>
                        @endforeach
                      </select>
                      </div>
                      <div class="col-md-4">
                        <button type="button" id="addButtonCrm" class="btn btn-primary w-100">Tambah</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="col-sm-12">
                    <table class="table table-bordered" style="">
                      <thead class="table-light">
                        <tr>
                          <th>#</th>
                          <th>Nama CRM</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="itemTableCrm">
                        <!-- Data akan ditambahkan di sini -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="d-telepon">
                <div class="row">
                  <label class="col-sm-3 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="date" id="tgl_realisasi_telepon" name="tgl_realisasi_telepon" value="{{old('tgl_realisasi_telepon')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_realisasi_telepon')) is-invalid @else   @endif @endif">
                    @if($errors->has('tgl_realisasi_telepon'))
                      <div class="invalid-feedback">{{$errors->first('tgl_realisasi_telepon')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-3 col-form-label text-sm-end">Start <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <input type="time" id="start" name="start" onchange="hitungDurasi();" value="{{old('start')}}" class="form-control @if ($errors->any()) @if($errors->has('start')) is-invalid @else   @endif @endif">
                    @if($errors->has('start'))
                      <div class="invalid-feedback">{{$errors->first('start')}}</div>
                    @endif
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end">End <span class="text-danger">*</span></label>
                  <div class="col-sm-3">
                  <input type="time" id="end" name="end" onchange="hitungDurasi();" value="{{old('end')}}" class="form-control @if ($errors->any()) @if($errors->has('end')) is-invalid @else   @endif @endif">
                    @if($errors->has('end'))
                      <div class="invalid-feedback">{{$errors->first('end')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row mt-3">
                  <label class="col-sm-3 col-form-label text-sm-end">Durasi</label>
                  <div class="col-sm-4">
                    <input type="text" id="durasi" name="durasi" value="{{old('durasi')}}" class="form-control @if ($errors->any()) @if($errors->has('durasi')) is-invalid @else   @endif @endif" readonly>
                      @if($errors->has('durasi'))
                        <div class="invalid-feedback">{{$errors->first('durasi')}}</div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="d-visit">
                <div class="row l-jenis-visit mb-3">
                  <label class="col-sm-3 col-form-label text-sm-end">Jenis Visit <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <div class="position-relative">
                      <select id="jenis_visit" name="jenis_visit" class="form-select @if ($errors->any()) @if($errors->has('jenis_visit')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($jenisVisit as $data)
                        <option value="{{$data->id}}" @if(old('jenis_visit') == $data->id) selected @endif>{{$data->nama}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('jenis_visit'))
                        <div class="invalid-feedback">{{$errors->first('jenis_visit')}}</div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-3 col-form-label text-sm-end">Tanggal <span class="text-danger">*</span></label>
                  <div class="col-sm-4">
                    <input type="date" id="tgl_realisasi" name="tgl_realisasi" value="{{old('tgl_realisasi')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_realisasi')) is-invalid @else   @endif @endif">
                    @if($errors->has('tgl_realisasi'))
                      <div class="invalid-feedback">{{$errors->first('tgl_realisasi')}}</div>
                    @endif
                  </div>
                  <label class="col-sm-2 col-form-label text-sm-end d-visit l-jam-realisasi">Jam <span class="text-danger">*</span></label>
                  <div class="col-sm-3 d-visit l-jam-realisasi">
                    <input type="time" id="jam_realisasi"  name="jam_realisasi" value="{{old('jam_realisasi')}}" class="form-control @if ($errors->any()) @if($errors->has('jam_realisasi')) is-invalid @else   @endif @endif">
                    @if($errors->has('jam_realisasi'))
                      <div class="invalid-feedback">{{$errors->first('jam_realisasi')}}</div>
                    @endif
                  </div>
                </div>
                <div class="row l-notulen mt-3">
                  <label class="col-sm-3 col-form-label text-sm-end">Notulen / Berita Acara <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <div class="form-floating form-floating-outline">
                      <textarea class="form-control h-px-100 @if ($errors->any()) @if($errors->has('notulen')) is-invalid @else   @endif @endif" name="notulen" id="notulen" placeholder="">{{old('notulen')}}</textarea>
                      @if($errors->has('notulen'))
                        <div class="invalid-feedback">{{$errors->first('notulen')}}</div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row mt-3 l-email">
                  <label class="col-sm-3 col-form-label text-sm-end">Email Penerima<span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text" id="email" name="email" value="{{old('email')}}" class="form-control @if ($errors->any()) @if($errors->has('email')) is-invalid @else   @endif @endif">
                      @if($errors->has('email'))
                        <div class="invalid-feedback">{{$errors->first('email')}}</div>
                      @endif
                  </div>
                </div>
                <div class="row mt-3 l-penerima">
                  <label class="col-sm-3 col-form-label text-sm-end">Penerima Berkas <span class="text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text" id="penerima" name="penerima" value="{{old('penerima')}}" class="form-control @if ($errors->any()) @if($errors->has('penerima')) is-invalid @else   @endif @endif">
                      @if($errors->has('penerima'))
                        <div class="invalid-feedback">{{$errors->first('penerima')}}</div>
                      @endif
                  </div>
                </div>
                <div class="row l-jenis-visit mt-3 mb-3">
                  <label class="col-sm-3 col-form-label text-sm-end">Kirim Email</label>
                  <div class="col-sm-9">
                    <div class="position-relative">
                      <button type="button" class="btn btn-primary" id="btn-send-email" onclick="sendEmail()">
                        <span class="tf-icons mdi mdi-email-send-outline me-1"></span> Kirim Email
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mt-3 l-keterangan" style="display:none">
                <label class="col-sm-3 col-form-label text-sm-end">Keterangan</label>
                <div class="col-sm-9">
                  <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="notes_tipe" id="notes_tipe" placeholder="">{{old('notes_tipe')}}</textarea>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>3. Berkas Pendukung</h6>
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="card border-dashed border-2" id="dropzone-card">
                <div class="card-body text-center py-5" id="dropzone-area">
                  <div id="drop-area">
                    <i class="mdi mdi-cloud-upload-outline text-muted mb-3" style="font-size: 48px;"></i>
                    <h5 class="text-muted mb-2">Drop files here or click to browse</h5>
                    <p class="text-muted small mb-3">Maximum 3 files • PDF, DOC, DOCX, JPG, PNG (Max 10MB each)</p>
                    <button type="button" class="btn btn-outline-primary" id="browse-files">
                      <i class="mdi mdi-folder-open-outline me-2"></i>Browse Files
                    </button>
                    <input type="file" id="file-input" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;" max="3">
                  </div>
                  
                  <div id="file-preview" class="mt-4" style="display: none;">
                    <div class="row" id="file-list">
                      <!-- Files will be displayed here -->
                    </div>
                  </div>
                  
                  <div id="upload-progress" style="display: none;">
                    <div class="progress mb-3">
                      <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="text-muted small">Uploading files...</p>
                  </div>
                </div>
              </div>
              
              <!-- Hidden inputs for file names -->
              <div id="hidden-inputs">
                <!-- Hidden inputs will be generated here -->
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <div class="row mb-3">
            <label class="col-sm-12 col-form-label">Note : <span class="text-danger">*)</span> Wajib Diisi</label>
          </div>
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button id="btn-submit" type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="javascript:void(0)" onclick="window.history.go(-1); return false;" class="btn btn-secondary waves-effect">Kembali</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<!-- Modal Kirim Email -->
<div class="modal fade" id="modal-kirim-email" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Kirim Email</h3>
        </div>
        <div class="row">
          <div class="col-12 mb-3">
            <label for="email-subject" class="form-label">Subject</label>
            <input type="text" id="email-subject" class="form-control" placeholder="Subject">
          </div>
          <div class="col-12 mb-3">
            <label for="email-body" class="form-label">Body</label>
            <textarea id="email-body" class="form-control" rows="5" placeholder="Body"></textarea>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Kirim ke:</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-leads">
              <label class="form-check-label" for="kirim-ke-leads">Leads / Customer (<span id="txt-email-leads">-</span> )</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-sales">
              <label class="form-check-label" for="kirim-ke-sales">Sales (<span id="txt-email-sales">-</span> )</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="kirim-ke-branch-manager">
              <label class="form-check-label" for="kirim-ke-branch-manager">Branch Manager (<span id="txt-email-branch-manager">-</span> )</label>
            </div>
          </div>
          <div class="col-12 text-center">
            <button type="button" class="btn btn-primary" id="btn-kirim-email">Kirim</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Payment Methods modal -->
<div class="modal fade" id="modal-leads" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Leads / Customer</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Tgl Leads</th>
                    <th class="text-center">Wilayah</th>
                    <th class="text-center">PIC</th>
                    <th class="text-center">No. Telp PIC</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Status</th>
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
  </div>
</div>
<!-- / Payment Methods modal -->

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

  function hitungDurasi() {
    var start = $('#start').val();
    var end = $('#end').val();

    if(start !=null && end !=null && start !="" && end !=""){
      var diff = null;
      if (end < start) {
        diff = (new Date("1970-1-2 " + end) - new Date("1970-1-1 " + start) ) /1000/60;
      }else{
        diff = (new Date("1970-1-1 " + end) - new Date("1970-1-1 " + start) ) /1000/60;
      }
      var hour = Math.abs(parseInt(diff/60)) + " jam";
      parseInt(diff/60)
      var min = diff%60;
      if(Math.abs(min)<10){
          min = "0" + min;
      }
      min = min + " menit";
      $('#durasi').val(hour+" "+min);
    }else{
      $('#durasi').val("");
    }
  }

  // Function to update display fields
  function updateDisplayFields() {
    $('#branch-display').text($('#branch').val() || '-');
    $('#kebutuhan-display').text($('#kebutuhan').val() || '-');
    $('#tim-sales-display').text($('#tim_sales_name').val() || '-');
    $('#sales-display').text($('#sales_name').val() || '-');
    $('#crm-display').text($('#crm_name').val() || '-');
    $('#ro-display').text($('#ro_name').val() || '-');
    
    // Update leads display with better formatting
    const leadsValue = $('#leads').val();
    if (leadsValue && leadsValue !== '') {
      $('#leads-display').text(leadsValue).removeClass('text-muted').addClass('text-dark fw-medium');
    } else {
      $('#leads-display').text('Pilih leads/customer').removeClass('text-dark fw-medium').addClass('text-muted');
    }
  }

  $(document).ready(function(){
    // Initialize display fields
    updateDisplayFields();

    @if(old('tim_sales_id')!=null && old('tim_sales_id')!= "")
      $('#tim_sales_d_id').find('option').remove();
      $('#tim_sales_d_id').append('<option value="">- Pilih data -</option>');

      if($('#tim_sales_id').find(":selected").val() !=""){
        var param = "tim_sales_id="+$('#tim_sales_id').find(":selected").val();
        $.ajax({
          url: "{{route('customer-activity.member-tim-sales')}}",
          type: 'GET',
          data: param,
          success: function(res) {
            res.forEach(element => {
              let selected = "";
              console.log({{old('tim_sales_d_id')}});
              @if(old('tim_sales_d_id')!=null && old('tim_sales_d_id')!= "")
              console.log(element.id);
                if(element.id == {{old('tim_sales_d_id')}}){
                  selected=true;
                  console.log(selected);
                }
              @endif

              $('#tim_sales_d_id').append('<option value="'+element.id+'" '+selected+'>'+element.nama+'</option>');
            });
          }
        });
      }
    @endif

    $('#tim_sales_id').on('change', function() {
      $('#tim_sales_d_id').find('option').remove();
      $('#tim_sales_d_id').append('<option value="">- Pilih data -</option>');

      if(this.value!=""){
        var param = "tim_sales_id="+this.value;
        $.ajax({
          url: "{{route('customer-activity.member-tim-sales')}}",
          type: 'GET',
          data: param,
          success: function(res) {
            res.forEach(element => {
              let selected = "";
              if($('#sales_d').val() != "" ){
                if(element.id == $('#sales_d').val()){
                  selected = "selected";
                }
              }
              $('#tim_sales_d_id').append('<option value="'+element.id+'" '+selected+'>'+element.nama+'</option>');
            });
          }
        });
      }
    });

    @if(old('tipe') == null)
      $('.d-visit').hide();
      $('.d-telepon').hide();
      $('.d-tim-sales').hide();
      $('.d-ro').hide();
      $('.d-crm').hide();
    @endif

    let dt_filter_table = $('.dt-column-search');

    var table = $('#table-data').DataTable({
        "initComplete": function (settings, json) {
          $("#table-data").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        "bDestroy": true,
        "iDisplayLength": 25,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('leads.available-leads') }}",
            data: function (d) {

            },
        },
        "order":[
            [0,'desc']
        ],
        columns:[{
                  data : 'id',
                  name : 'id',
                  visible: false,
                  searchable: false
              },{
                  data : 'nama_perusahaan',
                  name : 'nama_perusahaan',
                  className:'text-center'
              },{
                  data : 'tgl',
                  name : 'tgl',
                  className:'text-center'
              },{
                  data : 'branch',
                  name : 'branch',
                  className:'text-center'
              },{
                  data : 'pic',
                  name : 'pic',
                  className:'text-center'
              },{
                  data : 'no_telp',
                  name : 'no_telp',
                  className:'text-center'
              },{
                  data : 'email',
                  name : 'email',
                  className:'text-center'
              },{
                  data : 'status',
                  name : 'status',
                  className:'text-center'
              }],
        "language": datatableLang,
    });

    $('#btn-modal-cari-leads').on('click',function(){
      $('#modal-leads').modal('show');
    });

    @if($leads !=null)
      $('#branch').val("{{$leads->branch}}");
      $('#leads').val("{{$leads->nama_perusahaan}}");
      $('#leads_id').val("{{$leads->id}}");
      $('#email_leads').val("{{$leads->email}}");
      $('#kebutuhan').val("{{$leads->kebutuhan}}");
      $('#tim_sales_name').val("{{$leads->timSalesName}}");
      $('#sales_name').val("{{$leads->salesName}}");
      $('#email_sales').val("{{$leads->salesEmail}}");
      $('#email_branch_manager').val("{{$leads->branchManagerEmail}}");
      $('#crm_name').val("{{$leads->crm}}");
      $('#ro_name').val("{{$leads->ro}}");

      $('#sales_d').val("");

      @if($leads->tim_sales_id != null)
        $('#tim_sales_id').val({{$leads->tim_sales_id}}).change();
            if({{$leads->tim_sales_d_id}} != null){
            $('#sales_d').val({{$leads->tim_sales_d_id}});
            }
      @endif

      $('#btn-lihat-leads').removeClass('d-none');
      updateDisplayFields();
    @endif

    $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-leads').modal('hide');
      var rdata = table.row(this).data();
      console.log(rdata);

      $('#branch').val(rdata.branch);
      $('#leads').val(rdata.nama_perusahaan);
      $('#leads_id').val(rdata.id);
      $('#email_leads').val(rdata.email);
      $('#kebutuhan').val(rdata.kebutuhan);
      $('#tim_sales_name').val(rdata.tim_sales);
      $('#sales_name').val(rdata.sales);
      $('#email_sales').val(rdata.salesEmail);
      $('#email_branch_manager').val(rdata.branchManagerEmail);
      $('#ro_name').val(rdata.ro);
      $('#crm_name').val(rdata.crm);

      $('#sales_d').val("");

      if(rdata.tim_sales_id !=null){
        $('#tim_sales_id').val(rdata.tim_sales_id).change();
        if(rdata.tim_sales_d_id != null){
          $('#sales_d').val(rdata.tim_sales_d_id);
        }
      }

      $('#btn-lihat-leads').removeClass('d-none');
      updateDisplayFields();
    });

    // Handle dropdown change for activity type
    $('#tipe-select').on('change', function() {
      var selectedValue = $(this).val();
      // Tampilkan container form activity jika ada pilihan
      if(selectedValue) {
        $('#activity-form-container').show();
      } else {
        $('#activity-form-container').hide();
      }
      
      // Hide all sections first
      $('.d-telepon').hide();
      $('.d-visit').hide();
      $('.l-penerima').hide();
      $('.l-jenis-visit').hide();
      $('.l-notulen').hide();
      $('.l-keterangan').show();
      $('.l-email').hide();
      $('.d-tim-sales').hide();
      $('.d-ro').hide();
      $('.d-crm').hide();

      // Show relevant sections based on selection
      if(selectedValue === 'Telepon' || selectedValue === 'Online Meeting') {
        $('.d-telepon').show();
      } else if(selectedValue === 'Visit') {
        $('.d-visit').show();
        $('.l-jenis-visit').show();
        $('.l-notulen').show();
        $('.l-keterangan').hide();
      } else if(selectedValue === 'Email') {
        $('.d-visit').show();
        $('.l-jam-realisasi').hide();
        $('.l-email').show();
      } else if(selectedValue === 'Kirim Berkas') {
        $('.d-visit').show();
        $('.l-penerima').show();
      } else if(selectedValue === 'Pilih Sales') {
        $('.d-tim-sales').show();
      } else if(selectedValue === 'Pilih RO') {
        $('.d-ro').show();
      } else if(selectedValue === 'Pilih CRM') {
        $('.d-crm').show();
      } else {
        $('.l-keterangan').hide();
      }
    });

    @if(old('tipe') != null)
      $('.d-tim-sales').hide();
      $('.d-ro').hide();
      $('.d-crm').hide();
      $('.d-telepon').hide();
      $('.d-visit').hide();
      $('.l-penerima').hide();
      $('.l-jenis-visit').hide();
      $('.l-notulen').hide();
      $('.l-email').hide();
      $('.l-keterangan').show();

      var tipe = "{{old('tipe')}}";
      $('#tipe-select').val(tipe).trigger('change');
    @endif
  });

</script>

<script>
  // Inisialisasi elemen
  const itemSelect = document.getElementById('ro');
  const addButton = document.getElementById('addButton');
  const itemTable = document.getElementById('itemTable');
  let itemCount = 0;

  // Event untuk menambahkan item ke tabel
  addButton.addEventListener('click', () => {
    const selectedValue = itemSelect.value;
    const selectedText = itemSelect.options[itemSelect.selectedIndex].text;

    if (selectedValue) {
      // Cek apakah item sudah ada di tabel
      const existingInputs = Array.from(itemTable.querySelectorAll('input[name="selected_ro[]"]'));
      const isDuplicate = existingInputs.some(input => input.value === selectedValue);

      if (isDuplicate) {
        Swal.fire({
          title: "Pemberitahuan",
          html: 'RO sudah ditambahkan!',
          icon: "warning"
        });
        return;
      }

      itemCount++;

      if(itemCount > 3 ){
        Swal.fire({
          title: "Pemberitahuan",
          html: 'RO tidak bisa lebih dari 3',
          icon: "warning"
        });
        return;
      };

      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${itemCount}</td>
        <td> <input type="hidden" name="selected_ro[]" value="${selectedValue}"> ${selectedText}</td>
        <td><button class="btn btn-danger btn-sm delete-button">Hapus</button></td>
      `;
      itemTable.appendChild(row);

      // Event untuk tombol hapus
      row.querySelector('.delete-button').addEventListener('click', () => {
        row.remove();
        itemCount--;
        updateTableIndices();
      });
    } else {
      Swal.fire({
          title: "Pemberitahuan",
          html: 'Silakan pilih Nama RO terlebih dahulu.',
          icon: "warning"
        });
    }
  });

  // Fungsi untuk memperbarui nomor indeks tabel
  function updateTableIndices() {
    let index = 1;
    itemTable.querySelectorAll('tr').forEach(row => {
      row.querySelector('td:first-child').innerText = index++;
    });
  }
</script>

<script>
  // Inisialisasi elemen
  const itemSelectCrm = document.getElementById('crm');
  const addButtonCrm = document.getElementById('addButtonCrm');
  const itemTableCrm = document.getElementById('itemTableCrm');
  let itemCountCrm = 0;

  // Event untuk menambahkan item ke tabel
  addButtonCrm.addEventListener('click', () => {
    const selectedValue = itemSelectCrm.value;
    const selectedText = itemSelectCrm.options[itemSelectCrm.selectedIndex].text;

    if (selectedValue) {
      // Cek apakah item sudah ada di tabel
      const existingInputs = Array.from(itemTableCrm.querySelectorAll('input[name="selected_crm[]"]'));
      const isDuplicate = existingInputs.some(input => input.value === selectedValue);

      if (isDuplicate) {
        Swal.fire({
          title: "Pemberitahuan",
          html: 'CRM sudah ditambahkan!',
          icon: "warning"
        });
        return;
      }

      itemCountCrm++;

      if(itemCountCrm > 3 ){
        Swal.fire({
          title: "Pemberitahuan",
          html: 'CRM tidak bisa lebih dari 3',
          icon: "warning"
        });
        return;
      };

      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${itemCountCrm}</td>
        <td> <input type="hidden" name="selected_crm[]" value="${selectedValue}"> ${selectedText}</td>
        <td><button class="btn btn-danger btn-sm delete-button-crm">Hapus</button></td>
      `;
      itemTableCrm.appendChild(row);

      // Event untuk tombol hapus
      row.querySelector('.delete-button-crm').addEventListener('click', () => {
        row.remove();
        itemCountCrm--;
        updateTableIndicesCrm();
      });
    } else {
      Swal.fire({
          title: "Pemberitahuan",
          html: 'Silakan pilih Nama CRM terlebih dahulu.',
          icon: "warning"
        });
    }
  });

  // Fungsi untuk memperbarui nomor indeks tabel
  function updateTableIndicesCrm() {
    let index = 1;
    itemTableCrm.querySelectorAll('tr').forEach(row => {
      row.querySelector('td:first-child').innerText = index++;
    });
  }
</script>

<script>
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();

    if($('#tipe-select').val() === 'Pilih RO') {
      if($('#spv_ro').val()==""||$('#spv_ro').val()==null){
        msg +="Belum memilih SPV </br>";
      }else{
        const rowCount = itemTable.querySelectorAll('tr').length;
        if (rowCount==null || rowCount == 0) {
          msg +="Belum memilih RO </br>";
        }
      }
    }
    if($('#tipe-select').val() === 'Pilih CRM') {
      const rowCountCrm = itemTableCrm.querySelectorAll('tr').length;
        if (rowCountCrm==null || rowCountCrm == 0) {
          msg +="Belum memilih CRM </br>";
        }
    }

    if(msg == ""){
      form.submit();
    }else{
      Swal.fire({
        title: "Pemberitahuan",
        html: msg,
        icon: "warning"
      });
    }
  });
</script>

<script>
// File Upload Drag & Drop Functionality
$(document).ready(function() {
  let selectedFiles = [];
  const maxFiles = 3;
  const maxFileSize = 10 * 1024 * 1024; // 10MB
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];

  // Drag and drop events
  $('#dropzone-area').on({
    'dragover': function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass('border-primary bg-light');
    },
    'dragleave': function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass('border-primary bg-light');
    },
    'drop': function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass('border-primary bg-light');
      
      const files = e.originalEvent.dataTransfer.files;
      handleFiles(files);
    }
  });

  // Browse files button
  $('#browse-files').on('click', function() {
    $('#file-input').click();
  });

  // File input change
  $('#file-input').on('change', function() {
    const files = this.files;
    handleFiles(files);
  });

  function handleFiles(files) {
    if (files.length === 0) return;

    // Check if adding these files would exceed the limit
    if (selectedFiles.length + files.length > maxFiles) {
      Swal.fire({
        title: 'Peringatan',
        text: `Maksimal ${maxFiles} file yang diizinkan`,
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    Array.from(files).forEach(file => {
      // Validate file type
      if (!allowedTypes.includes(file.type)) {
        Swal.fire({
          title: 'File Tidak Valid',
          text: `File ${file.name} tidak didukung. Hanya PDF, DOC, DOCX, JPG, PNG yang diizinkan.`,
          icon: 'error',
          confirmButtonText: 'OK'
        });
        return;
      }

      // Validate file size
      if (file.size > maxFileSize) {
        Swal.fire({
          title: 'File Terlalu Besar',
          text: `File ${file.name} melebihi batas 10MB`,
          icon: 'error',
          confirmButtonText: 'OK'
        });
        return;
      }

      // Check for duplicate files
      if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
        Swal.fire({
          title: 'File Duplikat',
          text: `File ${file.name} sudah dipilih`,
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        return;
      }

      selectedFiles.push(file);
    });

    updateFilePreview();
    updateFormData();
  }

  function updateFilePreview() {
    const fileList = $('#file-list');
    fileList.empty();

    if (selectedFiles.length === 0) {
      $('#file-preview').hide();
      $('#drop-area').show();
      return;
    }

    $('#drop-area').hide();
    $('#file-preview').show();

    selectedFiles.forEach((file, index) => {
      const fileIcon = getFileIcon(file.type);
      const fileSize = formatFileSize(file.size);
      
      const fileCard = `
        <div class="col-md-4 mb-3">
          <div class="card h-100">
            <div class="card-body text-center position-relative">
              <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2" onclick="removeFile(${index})">
                <i class="mdi mdi-close"></i>
              </button>
              <i class="${fileIcon} text-primary mb-2" style="font-size: 32px;"></i>
              <h6 class="card-title text-truncate" title="${file.name}">${file.name}</h6>
              <p class="card-text small text-muted">${fileSize}</p>
              <input type="text" class="form-control form-control-sm mt-2" placeholder="Nama file (opsional)" 
                     id="filename-${index}" name="namafiles[]" value="${file.name.replace(/\.[^/.]+$/, '')}">
            </div>
          </div>
        </div>
      `;
      
      fileList.append(fileCard);
    });

    // Add upload more button if less than max files
    if (selectedFiles.length < maxFiles) {
      const addMoreCard = `
        <div class="col-md-4 mb-3">
          <div class="card h-100 border-dashed">
            <div class="card-body text-center d-flex flex-column justify-content-center">
              <i class="mdi mdi-plus text-muted mb-2" style="font-size: 32px;"></i>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="$('#file-input').click()">
                Add More Files
              </button>
              <small class="text-muted mt-2">${selectedFiles.length}/${maxFiles} files</small>
            </div>
          </div>
        </div>
      `;
      fileList.append(addMoreCard);
    }
  }

  function updateFormData() {
    // Create a new DataTransfer object
    const dt = new DataTransfer();
    
    // Add all selected files to the DataTransfer object
    selectedFiles.forEach(file => {
      dt.items.add(file);
    });
    
    // Update the file input
    document.getElementById('file-input').files = dt.files;
  }

  function getFileIcon(fileType) {
    switch(fileType) {
      case 'application/pdf':
        return 'mdi mdi-file-pdf-box';
      case 'application/msword':
      case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        return 'mdi mdi-file-word-box';
      case 'image/jpeg':
      case 'image/jpg':
      case 'image/png':
        return 'mdi mdi-file-image-box';
      default:
        return 'mdi mdi-file-document-box';
    }
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  // Global function to remove file
  window.removeFile = function(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
    updateFormData();
  };
});
</script>

<script>
  function sendEmail() {
    if ($('#leads_id').val()=== '' || $('#jenis_visit').val() === '' || $('#tgl_realisasi').val() === '' || $('#jam_realisasi').val() === '' || $('#notulen').val() === '') {
      Swal.fire({
        title: 'Peringatan',
        text: 'Leads, Jenis Visit, Tanggal Realisasi, Jam Realisasi, dan Notulen harus diisi',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    $('#email-subject').val('Notulensi dan Berita Acara Meeting');
    let bodyEmail = `Berikut kami sampaikan notulensi dan berita acara meeting yang telah dilaksanakan Pada :
    Visit : `+$('#jenis_visit').val()+`
    Tanggal : `+ formatDate($('#tgl_realisasi').val()) +`
    Jam : `+$('#jam_realisasi').val()+`
    Notulen : `+$('#notulen').val()+`
    \nAtas Perhatiannya kami ucapkan Terima kasih.`;
    $('#email-body').val(bodyEmail);

    $('#txt-email-leads').text($('#email_leads').val());
    $('#txt-email-sales').text($('#email_sales').val());
    $('#txt-email-branch-manager').text($('#email_branch_manager').val());
    $('#modal-kirim-email').modal('show');
  }

  function formatDate(date) {
    const [year, month, day] = date.split('-');
    return `${day}-${month}-${year}`;
  }

  $('#btn-kirim-email').on('click', function() {
    $('#modal-kirim-email').modal('hide');

    let subject = $('#email-subject').val();
    let body = $('#email-body').val();
    let recipients = [];
    let msg = "";

    if ($('#kirim-ke-leads').is(':checked')){
      recipients.push('Leads / Customer')
      if ($('#email_leads').val() === '') {
        msg += "Email Leads tidak ditemukan <br>";
      }
    };
    if ($('#kirim-ke-sales').is(':checked')){
      recipients.push('Sales');
      if ($('#email_sales').val() === '') {
        msg += "Email Sales tidak ditemukan <br>";
     }
    };
    if ($('#kirim-ke-branch-manager').is(':checked')){
      recipients.push('Branch Manager');
      if ($('#email_branch_manager').val() === '') {
        msg += "Email Branch Manager tidak ditemukan <br>";
      }
    }

    if (recipients.length === 0) {
      Swal.fire({
        title: 'Peringatan',
        text: 'Minimal ada 1 penerima',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }
    if (msg !== "") {
      Swal.fire({
        title: 'Peringatan',
        html: msg,
        icon: 'warning',
        confirmButtonText: 'OK'
      });
      return;
    }

    Swal.fire({
      title: 'Konfirmasi',
      text: `Kirim email ke: ${recipients.join(', ')}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Kirim',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Mengirim Email',
          text: 'Mohon tunggu...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });
        // Logic to send email
        $.ajax({
          url: "{{ route('customer-activity.send-email') }}",
          type: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            subject: subject,
            body: body,
            email_leads: $('#email_leads').val(),
            email_sales: $('#email_sales').val(),
            email_branch_manager: $('#email_branch_manager').val(),
            is_kirim_leads: $('#kirim-ke-leads').is(':checked'),
            is_kirim_sales: $('#kirim-ke-sales').is(':checked'),
            is_kirim_branch_manager: $('#kirim-ke-branch-manager').is(':checked')
          },
          success: function(res) {
            Swal.fire({
              title: 'Berhasil',
              text: 'Email berhasil dikirim',
              icon: 'success',
              confirmButtonText: 'OK'
            });
          },
      });
      }
    });
  });
</script>
@endsection