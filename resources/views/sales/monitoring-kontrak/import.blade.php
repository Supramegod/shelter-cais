@extends('layouts.master')
@section('title','Import Monitoring Kontrak')

@section('pageStyle')
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}" />
@endsection

@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/ </span> Import Monitoring Kontrak</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Import Monitoring Kontrak</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <!-- Multi  -->
        <div class="card-body">
            <form enctype="multipart/form-data" id="upload-form" style="opacity:1 !important" action="{{route('monitoring-kontrak.inquiry-import')}}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="file" name="file" id="file" accept=".xlsx, .xls" class="form-control @if ($errors->any()) @if($errors->has('file')) is-invalid @else   @endif @endif" >
                    <label class="input-group-text" for="file">Pilih file ( Disarankan max 500 baris )</label>
                    @if($errors->has('file'))
                      <div class="invalid-feedback">{{$errors->first('file')}}</div>
                    @endif
                  </div>

            </form>
            <div class="pt-4">
                <div class="row justify-content-end">
                <div class="col-sm-12 d-flex justify-content-center">
                    <button id="btn-import" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Import</button>
                    <a href="{{route('monitoring-kontrak')}}" class="btn btn-secondary waves-effect">Kembali</a>
                </div>
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
<!-- Vendors JS -->
<script>
      window.pressed = function(){
        var a = document.getElementById('file');
        if(a.value == "")
        {
            fileLabel.innerHTML = "ssss";
        }
        else
        {
            var theSplit = a.value.split('\\');
            fileLabel.innerHTML = theSplit[theSplit.length-1];
        }
    };
    $('#btn-import').on('click',function(){
        $('form').submit();
    });
</script>

@endsection
