@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-6')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">APLIKASI PENDUKUNG</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
                @foreach($aplikasiPendukung as $value)
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-icon @if(in_array($value->id,$arrAplikasiSel)) checked @endif">
                      <label class="form-check-label custom-option-content">
                        <span class="custom-option-body">
                            <img src="{{$value->link_icon}}" alt="{{$value->nama}}" style="max-width:60px">
                          <span class="custom-option-title">{{$value->nama}}</span>
                        </span>
                        <input name="aplikasi_pendukung[]" class="form-check-input" type="checkbox" value="{{$value->id}}" @if(in_array($value->id,$arrAplikasiSel)) checked @endif>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
              @include('sales.quotation.action')
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#btn-submit').on('click',function(e){
      e.preventDefault();
      var form = $(this).parents('form');
      form.submit();
    });
</script>
@endsection