@extends('layouts.master')
@section('title', 'View Issue ')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Menu Issue / </span>View Issue</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header d-flex justify-content-between">
                    <span>View Issue</span>
                    <span>{{ $now }}</span>
                </h5>

                <form class="card-body overflow-hidden" action="{{ route('issue.update',$data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Leads</label>
                        <div class="col-sm-5">
                            <input type="text" id="lead" name="lead" readonly value="{{ old('lead', $data->nama_perusahaan) }}" class="form-control">
                        </div>
                        <label class="col-sm-1 col-form-label text-sm-end">Nomor PKS</label>
                        <div class="col-sm-4">
                            <input type="text" id="pks" name="pks" readonly value="{{ old('pks', $data->nomor) }}" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Site</label>
                        <div class="col-sm-10">
                            <input type="text" id="site" name="site" readonly value="{{ old('site', $data->nama_site) }}" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Judul Issue <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" id="judul" name="judul" value="{{ old('judul', $data->judul) }}" class="form-control">
                        </div>
                    </div>

                    {{-- Jenis Keluhan & Status --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Jenis Keluhan <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select id="jenis_keluhan" name="jenis_keluhan" class="form-control">
                        <option value="">-- Pilih Jenis Keluhan --</option>
                        <option value="COMPLAINT" {{ $data->jenis_keluhan == 'COMPLAINT'? 'selected':'' }}>COMPLAINT</option>
                        <option value="REFRESH PERSONIL"{{ $data->jenis_keluhan == '"REFRESH PERSONIL'? 'selected':'' }}>REFRESH PERSONIL</option>
                        <option value="PERGANTIAN KAPORLAP"{{ $data->jenis_keluhan == 'PERGANTIAN KAPORLAP'? 'selected':'' }}>PERGANTIAN KAPORLAP</option>
                        <option value="PERFORMANCE PERSONIL"{{ $data->jenis_keluhan == 'PERFORMANCE PERSONIL'? 'selected':'' }}>PERFORMANCE PERSONIL</option>
                        <option value="PERFORMANCE MANAGEMENT"{{ $data->jenis_keluhan == 'PERFORMANCE MANAGEMENT'? 'selected':'' }}>PERFORMANCE MANAGEMENT</option>
                        <option value="INVOICE"{{ $data->jenis_keluhan == 'INVOICE'? 'selected':'' }}>INVOICE</option>
                        <option value="KEHILANGAN BARANG"{{ $data->jenis_keluhan == 'KEHILANGAN BARANG'? 'selected':'' }}>KEHILANGAN BARANG</option>
                        <option value="TRAINING"{{ $data->jenis_keluhan == 'TRAINING'? 'selected':'' }}>TRAINING</option>
                    </select>
                        </div>

                        <label class="col-sm-2 col-form-label text-sm-end">Status <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select id="status" name="status" class="form-control">
                                <option value="">-- Pilih Status --</option>
                                <option value="Open" {{ $data->status == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Closed" {{ $data->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>

                    {{-- Kolaborator --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Kolaborator</label>
                        <div class="col-sm-10">
                            <input type="text" id="kolaborator" name="kolaborator" value="{{ old('kolaborator', $data->kolaborator) }}" class="form-control">
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Deskripsi <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control h-px-100" name="deskripsi" id="deskripsi">{{ old('deskripsi', $data->deskripsi) }}</textarea>
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label text-sm-end">Lampiran</label>
                        <div class="col-sm-10">
                            <input type="file" id="lampiran" name="lampiran" value="{{ $data->url_lampiran }}" class="form-control">

                           @if($data->url_lampiran)
    <div class="mt-2">
        @php
            $ext = strtolower(pathinfo($data->url_lampiran, PATHINFO_EXTENSION));
            $fileUrl = $data->url_lampiran; // Karena di DB sudah full URL
        @endphp

        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
            <img src="{{ $fileUrl }}" alt="Lampiran" 
                 style="max-height: 150px; cursor:pointer;"
                 onclick="previewLampiran('{{ $fileUrl }}')">
        @elseif($ext === 'pdf')
          
            <a onclick="window.open('{{ $fileUrl }}','name','width=600,height=400');" rel="noopener noreferrer" href="javascript:void(0)" class="btn btn-sm btn-info">
                <i class="mdi mdi-file-pdf"></i> Lihat PDF
            </a>
        @else
            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-secondary">
                <i class="mdi mdi-file"></i> Download Lampiran
            </a>
        @endif
    </div>
@endif

                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="row justify-content-end">
                        <div class="col-sm-12 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary me-sm-2 me-1 waves-effect waves-light">Simpan</button>
                            <a href="{{ route('issue') }}" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
<script>
@if (session()->has('success'))
    Swal.fire({
        title: 'Pemberitahuan',
        html: '{{ session()->get('success') }}',
        icon: 'success',
        customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
        buttonsStyling: false
    });
@endif
@if (isset($error) || session()->has('error'))
    Swal.fire({
        title: 'Pemberitahuan',
        html: '{{ $error }} {{ session()->has('error') }}',
        icon: 'warning',
        customClass: { confirmButton: 'btn btn-warning waves-effect waves-light' },
        buttonsStyling: false
    });
@endif

function previewLampiran(url) {
    Swal.fire({
        
        imageUrl: url,
        imageAlt: 'Lampiran',
        showCloseButton: true,
        confirmButtonText: 'Tutup'
    });
}
</script>
@endsection
