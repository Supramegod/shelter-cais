@extends('layouts.master')
@section('title','Salary Rule')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Salary Rule</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Salary Rule</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('salary-rule.save')}}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{$data->id}}">
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-9">
                <input type="text" id="nama_salary_rule" name="nama_salary_rule" value="{{$data->nama_salary_rule}}" class="form-control @if ($errors->any()) @if($errors->has('nama_salary_rule')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_salary_rule'))
                  <div class="invalid-feedback">{{$errors->first('nama_salary_rule')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Cutoff <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="number" placeholder="Awal" value="{{$data->cutoff_awal}}" id="cutoff_awal" name="cutoff_awal" class="form-control @if ($errors->any()) @if($errors->has('cutoff_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff_awal'))
                  <div class="invalid-feedback">{{$errors->first('cutoff_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-4">
              <input type="number" placeholder="Akhir" value="{{$data->cutoff_akhir}}" id="cutoff_akhir" name="cutoff_akhir" class="form-control @if ($errors->any()) @if($errors->has('cutoff_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff_akhir'))
                  <div class="invalid-feedback">{{$errors->first('cutoff_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Check Absen <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="number" placeholder="Awal" value="{{$data->crosscheck_absen_awal}}" id="crosscheck_absen_awal" name="crosscheck_absen_awal" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen_awal'))
                <div class="invalid-feedback">{{$errors->first('crosscheck_absen_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-4">
              <input type="number" placeholder="Akhir" value="{{$data->crosscheck_absen_akhir}}" id="crosscheck_absen_akhir" name="crosscheck_absen_akhir" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen_akhir'))
                <div class="invalid-feedback">{{$errors->first('crosscheck_absen_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Pengiriman Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="number" placeholder="Awal" value="{{$data->pengiriman_invoice_awal}}" id="pengiriman_invoice_awal" name="pengiriman_invoice_awal" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice_awal'))
                <div class="invalid-feedback">{{$errors->first('pengiriman_invoice_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-4">
              <input type="number" placeholder="Akhir" value="{{$data->pengiriman_invoice_akhir}}" id="pengiriman_invoice_akhir" name="pengiriman_invoice_akhir" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice_akhir'))
                <div class="invalid-feedback">{{$errors->first('pengiriman_invoice_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Invoice Diterima <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="number" placeholder="Awal" value="{{$data->perkiraan_invoice_diterima_awal}}" id="perkiraan_invoice_diterima_awal" name="perkiraan_invoice_diterima_awal" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima_awal'))
                <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-4">
              <input type="number" placeholder="Akhir" value="{{$data->perkiraan_invoice_diterima_akhir}}" id="perkiraan_invoice_diterima_akhir" name="perkiraan_invoice_diterima_akhir" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima_akhir'))
                <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Pembayaran Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="number" placeholder="tanggal" value="{{$data->tgl_pembayaran_invoice}}" id="pembayaran_invoice" name="pembayaran_invoice" class="form-control @if ($errors->any()) @if($errors->has('pembayaran_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pembayaran_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pembayaran_invoice')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-3 col-form-label text-sm-end">Rilis Payroll <span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="number" placeholder="tanggal" value="{{$data->tgl_rilis_payroll}}" id="rilis_payroll" name="rilis_payroll" class="form-control @if ($errors->any()) @if($errors->has('rilis_payroll')) is-invalid @else   @endif @endif">
              @if($errors->has('rilis_payroll'))
                  <div class="invalid-feedback">{{$errors->first('rilis_payroll')}}</div>
              @endif
            </div>
          </div>
          <div class="pt-4">
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-4">
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
            <div class="col-12 text-center">
              <button id="btn-update" class="btn btn-primary w-100 waves-effect waves-light">
                <span class="me-1">Update Salary Rule</span>
                <i class="mdi mdi-content-save scaleX-n1-rtl"></i>
              </button>
            </div>
            <div class="col-12 text-center mt-2">
              <button id="btn-kembali" class="btn btn-secondary w-100 waves-effect waves-light">
                <span class="me-1">Kembali</span>
                <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
              </button>
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

  $('#btn-update').on('click',function () {
    $('form').submit();
  });
  
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('salary-rule')}}");
  });
</script>
@endsection