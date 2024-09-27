@extends('layouts.master')
@section('title','Leads')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Leads Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Leads</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('leads.save')}}" method="POST">
          @csrf
          <h6>1. Informasi Perusahaan</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{old('nama_perusahaan')}}" class="form-control @if ($errors->any()) @if($errors->has('nama_perusahaan')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_perusahaan'))
                  <div class="invalid-feedback">{{$errors->first('nama_perusahaan')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Jenis Perusahaan</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="jenis_perusahaan" name="jenis_perusahaan" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($jenisPerusahaan as $value)
                  <option value="{{$value->id}}" @if(old('jenis_perusahaan') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Wilayah <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="branch" name="branch" class="form-select @if ($errors->any()) @if($errors->has('branch')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($branch as $value)
                  <option value="{{$value->id}}" @if(old('branch') == $value->id) selected @endif>{{$value->name}}</option>
                  @endforeach
                </select>
                @if($errors->has('branch'))
                  <div class="invalid-feedback">{{$errors->first('branch')}}</div>
                @endif
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Telepon Perusahaan</label>
            <div class="col-sm-4">
              <input type="number" id="telp_perusahaan" name="telp_perusahaan" value="{{old('telp_perusahaan')}}" class="form-control @if ($errors->any())   @endif">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Alamat Perusahaan</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-2">
                <textarea class="form-control mt-3 h-px-100 @if ($errors->any())   @endif" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="">{{old('alamat_perusahaan')}}</textarea>
              </div>
            </div>
          </div>          
          <hr class="my-4 mx-4">
          <h6>2. Kebutuhan Leads</h6>
          <div class="row mb-2">
            <label class="col-sm-2 col-form-label text-sm-end">Sumber Leads</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="platform" name="platform" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($platform as $value)
                  <option value="{{$value->id}}" @if(old('platform') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="kebutuhan" name="kebutuhan" class="form-select @if ($errors->any()) @if($errors->has('kebutuhan')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($kebutuhan as $value)
                  <option value="{{$value->id}}" @if(old('kebutuhan') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('kebutuhan'))
                  <div class="invalid-feedback">{{$errors->first('kebutuhan')}}</div>
                @endif
              </div>
            </div>
          </div>
          <hr class="my-4 mx-4">
          <h6>3. Informasi PIC</h6>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">PIC <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="pic" name="pic" value="{{old('pic')}}" class="form-control @if ($errors->any()) @if($errors->has('pic')) is-invalid @else   @endif @endif">
              @if($errors->has('pic'))
                  <div class="invalid-feedback">{{$errors->first('pic')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Jabatan</label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="jabatan_pic" name="jabatan_pic" class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($jabatanPic as $value)
                  <option value="{{$value->id}}" @if(old('jabatan_pic') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nomor Telepon</label>
            <div class="col-sm-4">
              <input type="number" id="no_telp" name="no_telp" value="{{old('no_telp')}}" class="form-control @if ($errors->any())   @endif">
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Email</label>
            <div class="col-sm-4">
              <input type="text" id="email" name="email" value="{{old('email')}}" class="form-control @if ($errors->any())   @endif">
            </div>
          </div>
          <hr class="my-4 mx-4">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Detail Leads</label>
            <div class="col-sm-10">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="detail_leads" id="detail_leads" placeholder="">{{old('detail_leads')}}</textarea>
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
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('leads')}}" class="btn btn-secondary waves-effect">Kembali</a>
              </div>
            </div>
          </div>
        </form>
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
</script>
@endsection