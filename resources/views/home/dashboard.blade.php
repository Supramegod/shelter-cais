@extends('layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row gy-4 mb-5">
            <!-- Congratulations card -->
            <div class="col-xl-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">

                                <h4 class="card-title mb-5 d-flex gap-2 flex-wrap">Selamat Datang di CAIS ( Customer and
                                    Activity Information System ) 🎉</h4>
                            </div>
                            @if (in_array(Auth::user()->role_id, [2]))
                                <div class="col-md-2 text-end">
                                    <a href="{{ route('change.log') }}" class="btn btn-sm btn-primary">Change Log</a>
                                </div>
                            @endif
                        </div>
                        <div class="card card-body" style="color: black; width: 90%;">
                            <h5>
                                Version Patch : {{ $patch->version }}
                                ({{ \Carbon\Carbon::parse($patch->created_at)->format('d-m-Y') }})
                            </h5>
                            <hr>
                            <div>
                                {!! $patch->keterangan !!}
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <a id="toggleLink" style="font-size: 12px" href="javascript:void(0);"
                                    class="text-primary text-decoration-underline" data-bs-toggle="collapse"
                                    data-bs-target="#morePatch" aria-expanded="false" aria-controls="morePatch">
                                    See More
                                </a>
                            </div>
                        </div>
                        <div class="collapse" id="morePatch">
                            @foreach ($allPatch as $patches)
                                <div class="card card-body" style="color: black; margin-top:15px; width: 90%;">
                                    <h5 >
                                        Version Patch : {{ $patches->version }} 
                                        ({{ \Carbon\Carbon::parse($patches->created_at)->format('d-m-Y') }})
                                    </h5>
                                    <hr>
                                    <div>{!! $patches->keterangan !!}</div>
                                </div>
                            @endforeach
                        </div><br>
                        <a href="javascript:;" class="btn btn-sm btn-primary">Mulai Eksplorasi</a>
                    </div>
                    <img src="assets/img/illustrations/trophy.png" class="position-absolute bottom-0 end-0 me-3"
                        height="140" alt="view sales" />
                </div>
            </div>
            <!--/ Congratulations card -->
        </div>
    </div>
@endsection

@section('pageScript')
    <script>
        $(document).ready(function() {
            @if (in_array($userData->role_id, [29, 31]))
                @if ($userData->tim_sales_d_id == null)
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
        document.addEventListener("DOMContentLoaded", function() {
            const morePatch = document.getElementById("morePatch");
            const toggleBtn = document.getElementById("toggleLink");

            morePatch.addEventListener("shown.bs.collapse", function() {
                toggleBtn.textContent = "See Less";
            });

            morePatch.addEventListener("hidden.bs.collapse", function() {
                toggleBtn.textContent = "See More";
            });
        });
    </script>
    <script src="{{ asset('assets/js/dashboards-crm.js') }}"></script>
@endsection
