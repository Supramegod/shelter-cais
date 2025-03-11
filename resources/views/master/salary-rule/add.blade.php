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
            <div class="col-sm-10">
              <input type="text" id="nama_salary_rule" name="nama_salary_rule" value="{{old('nama_salary_rule')}}" class="form-control @if ($errors->any()) @if($errors->has('nama_salary_rule')) is-invalid @else   @endif @endif">
              @if($errors->has('nama_salary_rule'))
                  <div class="invalid-feedback">{{$errors->first('nama_salary_rule')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Cutoff <span class="text-danger">*</span></label>
            <div class="col-sm-1">
              <input type="number" placeholder="Awal" id="cutoff_awal" name="cutoff_awal" value="{{old('cutoff_awal')}}" class="form-control @if ($errors->any()) @if($errors->has('cutoff_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff_awal'))
                  <div class="invalid-feedback">{{$errors->first('cutoff_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-1">
              <input type="number" placeholder="Akhir" id="cutoff_akhir" name="cutoff_akhir" value="{{old('cutoff_akhir')}}" class="form-control @if ($errors->any()) @if($errors->has('cutoff_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('cutoff_akhir'))
                  <div class="invalid-feedback">{{$errors->first('cutoff_akhir')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Check Absen <span class="text-danger">*</span></label>
            <div class="col-sm-1">
              <input type="number" placeholder="Awal" id="crosscheck_absen_awal" name="crosscheck_absen_awal" value="{{old('crosscheck_absen_awal')}}" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen_awal'))
                <div class="invalid-feedback">{{$errors->first('crosscheck_absen_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-1">
              <input type="number" placeholder="Akhir" id="crosscheck_absen_akhir" name="crosscheck_absen_akhir" value="{{old('crosscheck_absen_akhir')}}" class="form-control @if ($errors->any()) @if($errors->has('crosscheck_absen_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('crosscheck_absen_akhir'))
                <div class="invalid-feedback">{{$errors->first('crosscheck_absen_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pengiriman Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-1">
              <input type="number" placeholder="Awal" id="pengiriman_invoice_awal" name="pengiriman_invoice_awal" value="{{old('pengiriman_invoice_awal')}}" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice_awal'))
                <div class="invalid-feedback">{{$errors->first('pengiriman_invoice_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-1">
              <input type="number" placeholder="Akhir" id="pengiriman_invoice_akhir" name="pengiriman_invoice_akhir" value="{{old('pengiriman_invoice_akhir')}}" class="form-control @if ($errors->any()) @if($errors->has('pengiriman_invoice_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('pengiriman_invoice_akhir'))
                <div class="invalid-feedback">{{$errors->first('pengiriman_invoice_akhir')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Invoice Diterima <span class="text-danger">*</span></label>
            <div class="col-sm-1">
              <input type="number" placeholder="Awal" id="perkiraan_invoice_diterima_awal" name="perkiraan_invoice_diterima_awal" value="{{old('perkiraan_invoice_diterima_awal')}}" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima_awal')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima_awal'))
                <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima_awal')}}</div>
              @endif
            </div>
            <div class="col-sm-1 d-flex align-items-center justify-content-center">
              <span>s/d</span>
            </div>
            <div class="col-sm-1">
              <input type="number" placeholder="Akhir" id="perkiraan_invoice_diterima_akhir" name="perkiraan_invoice_diterima_akhir" value="{{old('perkiraan_invoice_diterima_akhir')}}" class="form-control @if ($errors->any()) @if($errors->has('perkiraan_invoice_diterima_akhir')) is-invalid @else   @endif @endif">
              @if($errors->has('perkiraan_invoice_diterima_akhir'))
                <div class="invalid-feedback">{{$errors->first('perkiraan_invoice_diterima_akhir')}}</div>
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Pembayaran Invoice <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <input type="number" placeholder="tanggal" id="pembayaran_invoice" name="pembayaran_invoice" value="{{old('pembayaran_invoice')}}" class="form-control @if ($errors->any()) @if($errors->has('pembayaran_invoice')) is-invalid @else   @endif @endif">
              @if($errors->has('pembayaran_invoice'))
                  <div class="invalid-feedback">{{$errors->first('pembayaran_invoice')}}</div>
              @endif
            </div>
            <label class="col-sm-2 col-form-label text-sm-end">Rilis Payroll <span class="text-danger">*</span></label>
            <div class="col-sm-3">
              <input type="number" placeholder="tanggal" id="rilis_payroll" name="rilis_payroll" value="{{old('rilis_payroll')}}" class="form-control @if ($errors->any()) @if($errors->has('rilis_payroll')) is-invalid @else   @endif @endif">
              @if($errors->has('rilis_payroll'))
                  <div class="invalid-feedback">{{$errors->first('rilis_payroll')}}</div>
              @endif
            </div>
          </div>
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