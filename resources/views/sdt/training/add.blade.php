@extends('layouts.master')
@section('title','SDT Training')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">SDT/ </span> SDT Training Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form SDT Training</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('sdt-training.save')}}" method="POST">
          @csrf
          <!-- <h6>1. Informasi Perusahaan</h6> -->
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Business Unit</label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select id="laman_id" name="laman_id" class="select2 form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1" onchange="setValueArea(this)";>
                  <option value="">- Pilih data -</option>
                  @foreach($listBu as $value)
                  <option value="{{$value->id}}" @if(old('laman_id') == $value->id) selected @endif>{{$value->laman}}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <label class="col-sm-1 col-form-label text-sm-end">Client <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select multiple id="client_id" name="client_id[]" class="select2 form-select @if ($errors->any()) @if($errors->has('branch')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih Area dulu -</option>
                </select>
                @if($errors->has('client_id'))
                  <div class="invalid-feedback">{{$errors->first('client_id')}}</div>
                @endif
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Area</label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select id="area_id" name="area_id" class="select2 form-select @if ($errors->any())   @endif" data-allow-clear="true" tabindex="-1" onchange="setValueClient(this)">
                  <option value="">- Pilih Business Unit dulu -</option>
                </select>
              </div>
            </div>

            <label class="col-sm-1 col-form-label text-sm-end">Trainer <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select id="trainer_id" multiple name="trainer_id[]" class="select2 form-select @if ($errors->any()) @if($errors->has('trainer_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($listTrainer as $value)
                  @if($value->id==99) @continue @endif
                  <option value="{{$value->id}}" @if(old('trainer_id') == $value->id) selected @endif>{{$value->trainer}}</option>
                  @endforeach
                </select>
                @if($errors->has('trainer_id'))
                  <div class="invalid-feedback">{{$errors->first('trainer_id')}}</div>
                @endif
              </div>
            </div>

            <!-- <label class="col-sm-1 col-form-label text-sm-end">Peserta</label>
            <div class="col-sm-2">
              <input type="number" id="peserta" name="peserta" value="{{old('peserta')}}" class="form-control @if ($errors->any())   @endif">
            </div> -->
            
          </div>
          
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Materi <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select id="materi_id" name="materi_id" class="select2 form-select @if ($errors->any()) @if($errors->has('materi_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($listMateri as $value)
                  <option value="{{$value->id}}" @if(old('materi_id') == $value->id) selected @endif>{{$value->nama}}</option>
                  @endforeach
                </select>
                @if($errors->has('materi_id'))
                  <div class="invalid-feedback">{{$errors->first('materi_id')}}</div>
                @endif
              </div>
            </div>

            <label class="col-sm-1 col-form-label text-sm-end">Tempat <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
                <select id="tempat_id" name="tempat_id" class="select2 form-select @if ($errors->any()) @if($errors->has('tempat_id')) is-invalid @else   @endif @endif" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih Tempat -</option>
                    <option value="1" @if(old('tempat_id') == '1') selected @endif>IN DOOR</option>
                    <option value="2" @if(old('tempat_id') == '2') selected @endif>OUT DOOR</option>
                </select>
                @if($errors->has('tempat_id'))
                  <div class="invalid-feedback">{{$errors->first('tempat_id')}}</div>
                @endif
              </div>
            </div>
            
          </div>   

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Waktu Mulai <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
              <input type="datetime-local" id="start_date" name="start_date" value="{{old('start_date')}}" class="form-control @if ($errors->any())   @endif">
                @if($errors->has('start_date'))
                  <div class="invalid-feedback">{{$errors->first('start_date')}}</div>
                @endif
              </div>
            </div>

            <label class="col-sm-1 col-form-label text-sm-end">Waktu Selesai <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <div class="position-relative">
              <input type="datetime-local" id="end_date" name="end_date" value="{{old('end_date')}}" class="form-control @if ($errors->any())   @endif">
                @if($errors->has('end_date'))
                  <div class="invalid-feedback">{{$errors->first('end_date')}}</div>
                @endif
              </div>
            </div>
          </div>  

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Alamat</label>
            <div class="col-sm-3">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="alamat" id="alamat" placeholder="">{{old('alamat')}}</textarea>
              </div>
            </div>

            <label class="col-sm-1 col-form-label text-sm-end">Link Zoom</label>
            <div class="col-sm-3">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="link_zoom" id="link_zoom" placeholder="">{{old('link_zoom')}}</textarea>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Keterangan</label>
            <div class="col-sm-7">
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="keterangan" id="keterangan" placeholder="">{{old('keterangan')}}</textarea>
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
                <a href="{{route('sdt-training')}}" class="btn btn-secondary waves-effect">Kembali</a>
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
  $(document).ready(function() {
    $('.select2').select2();
  });

  function setValueArea(sel)
  {
    var id = sel.value;
    let formData = {
        "id":id,
        "_token": "{{ csrf_token() }}"
    };

    $.ajax({
        type: 'GET',
        url: "{{route('sdt-training.list-area')}}",
        data: {
            'id': id
        },
        success: function (response) {
            // the next thing you want to do 
            var $area = $('#area_id');
            $area.empty();
            
            $area.append('<option id=0 value=0> - Pilih Area - </option>');
            for (var i = 0; i < response.data.length; i++) {
                $area.append('<option id=' + response.data[i].id + ' value=' + response.data[i].id + '>' + response.data[i].area + '</option>');
            }
        }
    });
  }

  function setValueClient(sel)
  {
    var area_id = sel.value;
    var laman_id = $('#laman_id').val();
    
    let formData = {
        "area_id":area_id,
        "laman_id":laman_id,
        "_token": "{{ csrf_token() }}"
    };

    $.ajax({
        type: 'GET',
        url: "{{route('sdt-training.list-client')}}",
        data: {
            'area_id':area_id,
            'laman_id':laman_id,
        },
        success: function (response) {
            // the next thing you want to do 
            var $client = $('#client_id');
            $client.empty();
            
            // $client.append('<option id=0 value=0> - Pilih Client - </option>');
            for (var i = 0; i < response.data.length; i++) {
                $client.append('<option value=' + response.data[i].id + '>' + response.data[i].client + '</option>');
            }
        }
    });
  }
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
    }).then(function() {
        window.location.href = '{{route("sdt-training")}}';
    });
  @endif
</script>
@endsection