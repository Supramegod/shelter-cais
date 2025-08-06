@extends('layouts.master')
@section('title','Edit Syarat dan Ketentuan Kerjasama')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Syarat dan Ketentuan Kerjasama</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-quotation-kerjasama')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-3">EDIT SYARAT DAN KETENTUAN KERJASAMA</h4>
              <h4>{{$quotation->nomor}}</h4>
              <input type="hidden" name="id" value="{{$data->id}}">
            </div>
            <div class="row mb-3">
                <label class="col-form-label text-center">ISI</label>
                <div id="summernote"><p></p></div>
            </div>
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Simpan Perubahan</span>
                  <i class="mdi mdi-arrow-right"></i>
                </button>
                &nbsp;&nbsp;
                <button type="button" onclick="window.history.back()" class="btn btn-secondary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Kembali</span>
                  <i class="mdi mdi-arrow-left"></i>
                </button>
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
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
<script>
    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
        }
    });
    
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    let form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();
    let content = $('#summernote').summernote('code');

    // Periksa jika field hidden sudah ada, jika tidak tambahkan
    if ($('input[name="raw_text"]').length === 0) {
        form.append('<input type="hidden" name="raw_text">');
    }

    // Isi value dari field hidden
    $('input[name="raw_text"]').val(content);

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

    $('#summernote').summernote({
        height: 300,
    });

    var initialContent = `{!! $data->perjanjian !!}`;
    $('#summernote').summernote('code', initialContent);

</script>
@endsection