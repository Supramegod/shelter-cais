@extends('layouts.master')
@section('title', 'Leads')
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
        <label class="col-sm-2 col-form-label text-sm-end">
          Grup Perusahaan <span class="text-danger">*</span>
        </label>

        <div class="col-sm-10">
          <select id="perusahaan_group_id" name="perusahaan_group_id"
          class="form-select select2 @error('perusahaan_group_id') is-invalid @enderror">
          <option value="">- Pilih Grup -</option>
          @foreach($grupPerusahaan as $grup)
        <option value="{{ $grup->id }}" @selected(old('perusahaan_group_id') == $grup->id)>
        {{ $grup->nama_grup }}
        </option>
      @endforeach
          <option value="__new__" @selected(old('perusahaan_group_id') == '__new__')>
            + Tambah Grup Baru
          </option>
          </select>

          @error('perusahaan_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror

          <div id="new_grup_input" class="mt-2" style="display: none;">
          <input type="text" name="new_nama_grup" class="form-control" placeholder="Masukkan nama grup baru"
            value="{{ old('new_nama_grup') }}">
          </div>
        </div>
        </div>


        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan <span
          class="text-danger">*</span></label>
        <div class="col-sm-10">
          <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{old('nama_perusahaan')}}"
          class="form-control @if ($errors->any()) @if($errors->has('nama_perusahaan')) is-invalid @else   @endif @endif">
          @if($errors->has('nama_perusahaan'))
        <div class="invalid-feedback">{{$errors->first('nama_perusahaan')}}</div>
      @endif
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Kategori Perusahaan</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="bidang_perusahaan" name="bidang_perusahaan"
            class="form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($bidangPerusahaan as $value)
        <option value="{{$value->id}}" @if(old('bidang_perusahaan') == $value->id) selected @endif>
        {{$value->nama}}
        </option>
        @endforeach
          </select>
          </div>
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Jenis Perusahaan</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="jenis_perusahaan" name="jenis_perusahaan" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($jenisPerusahaan as $value)
        <option value="{{$value->id}}" @if(old('jenis_perusahaan') == $value->id) selected @endif>
        {{$value->nama}}
        </option>
        @endforeach
          </select>
          </div>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Provinsi <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="provinsi" name="provinsi"
            class="form-select @if ($errors->any()) @if($errors->has('provinsi')) is-invalid @else   @endif @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($provinsi as $value)
        <option value="{{$value->id}}" @if(old('provinsi') == $value->id) selected @endif>{{$value->name}}
        </option>
        @endforeach
          </select>
          @if($errors->has('provinsi'))
        <div class="invalid-feedback">{{$errors->first('provinsi')}}</div>
      @endif
          </div>
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Kota <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="kota" name="kota"
            class="form-select @if ($errors->any()) @if($errors->has('kota')) is-invalid @else   @endif @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
          </select>
          @if($errors->has('kota'))
        <div class="invalid-feedback">{{$errors->first('kota')}}</div>
      @endif
          </div>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Kecamatan</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="kecamatan" name="kecamatan" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
          </select>
          </div>
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Kelurahan</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="kelurahan" name="kelurahan" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
          </select>
          </div>
        </div>
        <script>

          $(document).ready(function () {
          $('#perusahaan_group_id').select2({
            placeholder: "- Pilih Grup -",
            width: '100%'
          });

          $('#perusahaan_group_id').on('change', function () {
            if ($(this).val() === '__new__') {
            $('#new_grup_input').show();
            } else {
            $('#new_grup_input').hide();
            }
          }).trigger('change');
          });


          $(document).ready(function () {
          $('#provinsi').on('change', function () {
            let provinsiId = $(this).val();
            $('#kota').empty().append('<option value="">- Pilih data -</option>');
            $('#kecamatan').empty().append('<option value="">- Pilih data -</option>');
            $('#kelurahan').empty().append('<option value="">- Pilih data -</option>');
            if (provinsiId) {
            let getKotaUrl = "{{ route('leads.get-kota', ':provinsiId') }}";
            getKotaUrl = getKotaUrl.replace(':provinsiId', provinsiId);
            $.ajax({
              url: getKotaUrl,
              type: 'GET',
              success: function (data) {
              $.each(data, function (key, value) {
                $('#kota').append('<option value="' + value.id + '">' + value.name + '</option>');
              });
              }
            });
            }
          });

          $('#kota').on('change', function () {
            let kotaId = $(this).val();
            $('#kecamatan').empty().append('<option value="">- Pilih data -</option>');
            $('#kelurahan').empty().append('<option value="">- Pilih data -</option>');
            if (kotaId) {
            let getKecamatanUrl = "{{ route('leads.get-kecamatan', ':kotaId') }}";
            getKecamatanUrl = getKecamatanUrl.replace(':kotaId', kotaId);
            $.ajax({
              url: getKecamatanUrl,
              type: 'GET',
              success: function (data) {
              $.each(data, function (key, value) {
                $('#kecamatan').append('<option value="' + value.id + '">' + value.name + '</option>');
              });
              }
            });
            }
          });

          $('#kecamatan').on('change', function () {
            let kecamatanId = $(this).val();
            $('#kelurahan').empty().append('<option value="">- Pilih data -</option>');

            if (kecamatanId) {
            let getKelurahanUrl = "{{ route('leads.get-kelurahan', ':kecamatanId') }}";
            getKelurahanUrl = getKelurahanUrl.replace(':kecamatanId', kecamatanId);
            $.ajax({
              url: getKelurahanUrl,
              type: 'GET',
              success: function (data) {
              $.each(data, function (key, value) {
                $('#kelurahan').append('<option value="' + value.id + '">' + value.name + '</option>');
              });
              }
            });
            }
          });
          });
        </script>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Wilayah <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="branch" name="branch"
            class="form-select @if ($errors->any()) @if($errors->has('branch')) is-invalid @else   @endif @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($branch as $value)
        <option value="{{$value->id}}" @if(old('branch') == $value->id) selected @endif>{{$value->name}}
        </option>
        @endforeach
          </select>
          @if($errors->has('branch'))
        <div class="invalid-feedback">{{$errors->first('branch')}}</div>
      @endif
          </div>
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Telepon Perusahaan</label>
        <div class="col-sm-4">
          <input type="number" id="telp_perusahaan" name="telp_perusahaan" value="{{old('telp_perusahaan')}}"
          class="form-control @if ($errors->any())   @endif">
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Alamat Perusahaan</label>
        <div class="col-sm-10">
          <div class="form-floating form-floating-outline mb-2">
          <textarea class="form-control mt-3 h-px-100 @if ($errors->any())   @endif" name="alamat_perusahaan"
            id="alamat_perusahaan" placeholder="">{{old('alamat_perusahaan')}}</textarea>
          </div>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">PMA / PMDN</label>
        <div class="col-sm-10">
          <select id="pma" name="pma" class="form-select @if ($errors->any())   @endif" data-allow-clear="true"
          tabindex="-1">
          <option value="">- Pilih data -</option>
          <option value="PMA" @if(old('pma') == 'PMA') selected @endif>PMA</option>
          <option value="PMDN" @if(old('pma', 'PMDN') == 'PMDN') selected @endif>PMDN</option>
          </select>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Benua <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="benua" name="benua" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($benua as $value)
        <option value="{{$value->id_benua}}" @if(old('benua', 2) == $value->id_benua) selected @endif>
        {{$value->nama_benua}}
        </option>
        @endforeach
          </select>
          </div>
        </div>
        <script>
          $(document).ready(function () {
          $('#benua').on('change', function () {
            let benuaId = $(this).val();
            $('#negara').empty().append('<option value="">- Pilih data -</option>');
            if (benuaId) {
            let getNegaraUrl = "{{ route('leads.get-negara', ':benuaId') }}";
            getNegaraUrl = getNegaraUrl.replace(':benuaId', benuaId);
            $.ajax({
              url: getNegaraUrl,
              type: 'GET',
              success: function (data) {
              $.each(data, function (key, value) {
                $('#negara').append('<option value="' + value.id_negara + '">' + value.nama_negara + '</option>');
              });
              }
            });
            }
          });
          });
        </script>
        <label class="col-sm-2 col-form-label text-sm-end">Negara <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="negara" name="negara" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @if(isset($negaraDefault))
          @foreach($negaraDefault as $value)
        <option value="{{$value->id_negara}}" @if(old('negara', 79) == $value->id_negara) selected @endif>
        {{$value->nama_negara}}
        </option>
        @endforeach
        @endif
          </select>
          </div>
        </div>
        </div>
        <hr class="my-4 mx-4">
        <h6>2. Kebutuhan Leads</h6>
        <div class="row mb-2">
        <label class="col-sm-2 col-form-label text-sm-end">Sumber Leads</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="platform" name="platform" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($platform as $value)
        <option value="{{$value->id}}" @if(old('platform') == $value->id) selected @endif>{{$value->nama}}
        </option>
        @endforeach
          </select>
          </div>
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="kebutuhan" name="kebutuhan"
            class="form-select @if ($errors->any()) @if($errors->has('kebutuhan')) is-invalid @else   @endif @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($kebutuhan as $value)
        @if($value->id == 99) @continue @endif
        <option value="{{$value->id}}" @if(old('kebutuhan') == $value->id) selected @endif>{{$value->nama}}
        </option>
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
        <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
        <div class="col-sm-4">
          <input type="text" id="pic" name="pic" value="{{old('pic')}}"
          class="form-control @if ($errors->any()) @if($errors->has('pic')) is-invalid @else   @endif @endif">
          @if($errors->has('pic'))
        <div class="invalid-feedback">{{$errors->first('pic')}}</div>
      @endif
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Jabatan</label>
        <div class="col-sm-4">
          <div class="position-relative">
          <select id="jabatan_pic" name="jabatan_pic" class="form-select @if ($errors->any())   @endif"
            data-allow-clear="true" tabindex="-1">
            <option value="">- Pilih data -</option>
            @foreach($jabatanPic as $value)
        <option value="{{$value->id}}" @if(old('jabatan_pic') == $value->id) selected @endif>{{$value->nama}}
        </option>
        @endforeach
          </select>
          </div>
        </div>
        </div>
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Nomor Telepon</label>
        <div class="col-sm-4">
          <input type="number" id="no_telp" name="no_telp" value="{{old('no_telp')}}"
          class="form-control @if ($errors->any())   @endif">
        </div>
        <label class="col-sm-2 col-form-label text-sm-end">Email</label>
        <div class="col-sm-4">
          <input type="text" id="email" name="email" value="{{old('email')}}"
          class="form-control @if ($errors->any())   @endif">
        </div>
        </div>
        <hr class="my-4 mx-4">
        <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-sm-end">Detail Leads</label>
        <div class="col-sm-10">
          <div class="form-floating form-floating-outline mb-4">
          <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="detail_leads"
            id="detail_leads" placeholder="">{{old('detail_leads')}}</textarea>
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
    $(document).ready(function () {
    $('#jenis_perusahaan').select2();
    });
  </script>
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