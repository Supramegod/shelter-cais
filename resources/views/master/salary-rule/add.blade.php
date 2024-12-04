@extends('layouts.master')
@section('title','Salary Rule')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Salary Rule Baru</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Form Salary Rule</span>
            <span>{{$now}}</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('salary-rule.save')}}" method="POST">
          @csrf
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Nama <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" id="nama_salary_rule" name="nama_salary_rule" value="{{old('nama_salary_rule')}}" class="form-control @if ($errors->any()) @if($errors->has('nama_salary_rule')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_salary_rule'))
                  <div class="invalid-feedback">{{$errors->first('nama_salary_rule')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Cutoff <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="cutoff" name="cutoff" value="{{old('cutoff')}}" class="form-control @if ($errors->any()) @if($errors->has('cutoff')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff'))
                  <div class="invalid-feedback">{{$errors->first('cutoff')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Check Absen <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="crosscheck_absen" name="crosscheck_absen" value="{{old('crosscheck_absen')}}" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen'))
                  <div class="invalid-feedback">{{$errors->first('crosscheck_absen')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pengiriman Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="pengiriman_invoice" name="pengiriman_invoice" value="{{old('pengiriman_invoice')}}" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pengiriman_invoice')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Invoice Diterima <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX - XX" id="perkiraan_invoice_diterima" name="perkiraan_invoice_diterima" value="{{old('perkiraan_invoice_diterima')}}" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima'))
                  <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima')}}</div>
              @endif
            </div>
          </div>
          <!-- <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pembayaran Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX bulan berikutnya" id="pembayaran_invoice" name="pembayaran_invoice" value="{{old('pembayaran_invoice')}}" class="form-control @if ($errors->any()) @if($errors->has('pembayaran_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pembayaran_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pembayaran_invoice')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Rilis Payroll <span class="text-danger">*</span></label>
            <div class="col-sm-4">
              <input type="text" placeholder="Tanggal XX bulan berikutnya" id="rilis_payroll" name="rilis_payroll" value="{{old('rilis_payroll')}}" class="form-control @if ($errors->any()) @if($errors->has('rilis_payroll')) is-invalid @else   @endif @endif">
              @if($errors->has('rilis_payroll'))
                  <div class="invalid-feedback">{{$errors->first('rilis_payroll')}}</div>
              @endif
            </div>
          </div> -->
          <div class="pt-4">
            <div class="row justify-content-end">
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                <button type="reset" class="btn btn-warning me-sm-2 me-1 waves-effect">Reset</button>
                <a href="{{route('salary-rule')}}" class="btn btn-secondary waves-effect">Kembali</a>
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