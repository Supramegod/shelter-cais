@extends('layouts.master')
@section('title','Dashboard')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row gy-4 mb-5">
        <!-- Congratulations card -->
        <div class="col-xl-12">
            <div class="card h-100">
            <div class="card-body">
                <h4 class="card-title mb-5 d-flex gap-2 flex-wrap">Selamat Datang di CAIS ( Customer and Activity Information System ) 🎉</h4>
                <br><br><br><br><br><br><br><br>
                <a href="javascript:;" class="btn btn-sm btn-primary">Mulai Eksplorasi</a>
            </div>
            <img
                src="public/assets/img/illustrations/trophy.png"
                class="position-absolute bottom-0 end-0 me-3"
                height="140"
                alt="view sales" />
            </div>
        </div>
        <!--/ Congratulations card -->
    </div>
</div>
@endsection

@section('pageScript')
<script>
    $(document).ready(function() {
        @if(in_array($userData->role_id, [29,31]))
            @if($userData->tim_sales_d_id == null)
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Anda belum terdaftar dalam tim sales. Silahkan hubungi IT untuk informasi lebih lanjut.',
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                });
            @endif
        @endif
    });
</script>
<script src="{{ asset('public/assets/js/dashboards-crm.js') }}"></script>
@endsection
