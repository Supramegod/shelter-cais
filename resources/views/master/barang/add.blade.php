@extends('layouts.master')
@section('title','Barang')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Barang Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Barang</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('barang.save')}}" method="POST">
          @csrf
        <input type="hidden" name="jenis" value="{{$jenis}}">
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama" name="nama" value="{{old('nama')}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
              @if($errors->has('nama'))
                  <div class="invalid-feedback">{{$errors->first('nama')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Jenis Barang <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <div class="position-relative">
                <select id="jenis_barang_id" name="jenis_barang_id" class="form-select @if ($errors->any()) @if($errors->has('jenis_barang_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                <option value="">- Pilih Jenis Barang -</option>
                  @foreach($listJenisBarang as $value)
                    @if($jenis=='kaporlap')
                    @if(!in_array($value->id, [1,2,3,4,5])) @continue @endif
                    @elseif($jenis=='devices')
                    @if(!in_array($value->id, [9,10,11,12,17])) @continue @endif
                    @elseif($jenis=='ohc')
                    @if(!in_array($value->id, [6,7,8])) @continue @endif
                    @elseif($jenis=='chemical')
                    @if(!in_array($value->id, [13,14,15,16,18,19])) @continue @endif
                    @endif
                    <option value="{{$value->id}}" @if(old('jenis_barang_id') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('jenis_barang_id'))
                  <div class="invalid-feedback">{{$errors->first('jenis_barang_id')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Harga <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="harga" name="harga" value="{{old('harga')}}" class="form-control @if ($errors->any()) @if($errors->has('harga')) is-invalid @else   @endif @endif">
              @if($errors->has('harga'))
                  <div class="invalid-feedback">{{$errors->first('harga')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Satuan</label>
            <div class="col-sm-4">
              <input type="text" id="satuan" name="satuan" value="{{old('satuan')}}" class="form-control @if ($errors->any()) @if($errors->has('satuan')) is-invalid @else   @endif @endif">
              @if($errors->has('satuan'))
                  <div class="invalid-feedback">{{$errors->first('satuan')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Masa Pakai</label>
            <div class="col-sm-4">
              <input type="number" id="masa_pakai" name="masa_pakai" value="{{old('masa_pakai')}}" class="form-control @if ($errors->any()) @if($errors->has('masa_pakai')) is-invalid @else   @endif @endif">
              @if($errors->has('masa_pakai'))
                  <div class="invalid-feedback">{{$errors->first('masa_pakai')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Merk</label>
            <div class="col-sm-4">
              <input type="text" id="merk" name="merk" value="{{old('merk')}}" class="form-control @if ($errors->any()) @if($errors->has('merk')) is-invalid @else   @endif @endif">
              @if($errors->has('merk'))
                  <div class="invalid-feedback">{{$errors->first('merk')}}</div>
              @endif
            </div>
          </div>
          <!-- <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Jumlah Default</label>
            <div class="col-sm-4">
              <input type="jumlah_default" id="jumlah_default" name="jumlah_default" value="{{old('jumlah_default')}}" class="form-control @if ($errors->any()) @if($errors->has('jumlah_default')) is-invalid @else   @endif @endif">
              @if($errors->has('jumlah_default'))
                  <div class="invalid-feedback">{{$errors->first('jumlah_default')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Urutan</label>
            <div class="col-sm-4">
              <input type="urutan" id="urutan" name="urutan" value="{{old('urutan')}}" class="form-control @if ($errors->any()) @if($errors->has('urutan')) is-invalid @else   @endif @endif">
              @if($errors->has('urutan'))
                  <div class="invalid-feedback">{{$errors->first('urutan')}}</div>
              @endif
            </div>
          </div> -->
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <button type="button" class="btn btn-secondary waves-effect" onclick="history.back();">Kembali</button>
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
<script>
  var elem = document.getElementById("harga");

  elem.addEventListener("keydown",function(event){
      var key = event.which;
      if((key<48 || key>57) && key != 8) event.preventDefault();
  });

  elem.addEventListener("keyup",function(event){
      var value = this.value.replace(/,/g,"");
      this.dataset.currentValue=parseInt(value);
      var caret = value.length-1;
      while((caret-3)>-1)
      {
          caret -= 3;
          value = value.split('');
          value.splice(caret+1,0,",");
          value = value.join('');
      }
      this.value = value;
  });
</script>
@endsection
