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
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama_salary_rule" name="nama_salary_rule" value="{{$data->nama_salary_rule}}" class="form-control @if ($errors->any()) @if($errors->has('nama_salary_rule')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_salary_rule'))
                  <div class="invalid-feedback">{{$errors->first('nama_salary_rule')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Cutoff <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="cutoff" name="cutoff" value="{{$data->cutoff}}" class="form-control @if ($errors->any()) @if($errors->has('cutoff')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff'))
                  <div class="invalid-feedback">{{$errors->first('cutoff')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Check Absen <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="crosscheck_absen" name="crosscheck_absen" value="{{$data->crosscheck_absen}}" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen'))
                  <div class="invalid-feedback">{{$errors->first('crosscheck_absen')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pengiriman Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="pengiriman_invoice" name="pengiriman_invoice" value="{{$data->pengiriman_invoice}}" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pengiriman_invoice')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Invoice Diterima <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="perkiraan_invoice_diterima" name="perkiraan_invoice_diterima" value="{{$data->perkiraan_invoice_diterima}}" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima'))
                  <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pembayaran Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX bulan berikutnya" id="pembayaran_invoice" name="pembayaran_invoice" value="{{$data->pembayaran_invoice}}" class="form-control @if ($errors->any()) @if($errors->has('pembayaran_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pembayaran_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pembayaran_invoice')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Rilis Payroll <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX bulan berikutnya" id="rilis_payroll" name="rilis_payroll" value="{{$data->rilis_payroll}}" class="form-control @if ($errors->any()) @if($errors->has('rilis_payroll')) is-invalid @else   @endif @endif">
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
            <hr class="my-4 mx-4">
            <div class="col-12 text-center mt-2">
              <button id="btn-delete" class="btn btn-danger w-100 waves-effect waves-light">
                <span class="me-1">Delete Salary Rule</span>
                <i class="mdi mdi-trash-can scaleX-n1-rtl"></i>
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
  
  $('#btn-delete').on('click',function () {
    $('form').attr('action', '{{route("salary-rule.delete")}}');
    $('form').submit();
  });
  
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('salary-rule')}}");
  });
</script>
@endsection