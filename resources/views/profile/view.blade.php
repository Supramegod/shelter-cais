@extends('layouts.master')

@section('title', 'Profil Pengguna')

@section('pageStyle')
<style>
    .profile-fixed {
        position: sticky;
         top: 9rem;
        
    }
</style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <!-- Profil Pengguna (Sticky) -->
        <div class="col-md-4">
            <div class="card shadow-lg profile-fixed">
                <div class="card-header bg-primary ">
                    <h5 class="mb-0 text-white">Profil Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle" width="120" alt="Foto Profil">
                    </div>
                    <table class="table table-borderless">
                        <tr><th>Nama</th><td>: {{ $data->full_name }}</td></tr>
                        <tr><th>Email</th><td>: {{ $data->email }}</td></tr>
                        <tr><th>Username</th><td>: {{ $data->username ?? '-' }}</td></tr>
                        <tr><th>Role</th><td>: {{ $role->name ?? '-' }}</td></tr>
                        <tr><th>Cabang</th><td>: {{ $branch->name ?? '-' }}</td></tr>
                        <tr><th>Tanggal Bergabung</th><td>: {{ \Carbon\Carbon::parse($data->created_at)->isoFormat('D MMMM Y') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Aktivitas (AJAX DataTable) -->
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-info ">
                    <h5 class="mb-0 text-white">Customer Activity</h5>
                </div>
                <div class="card-body">
                    <table id="activityTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Perusahaan</th>
                                <th>Tipe</th>
                                <th>Kebutuhan</th>
                                <th>keterangan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
<script>
$(document).ready(function() {
    var table = $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('profile.activities') }}",
         order: [[0, 'desc']],
        columns: [
             { data: 'id', visible: false },
            { data: 'nama_perusahaan', name: 'nama_perusahaan' },
            { data: 'tipe', name: 'tipe' },
            { data: 'kebutuhan', name: 'kebutuhan' },
            { data: 'notes', name: 'notes' },
            { data: 'created_at', name: 'created_at' },
        ],
        "language": datatableLang,
        
        dom:'frtip'
        
    });
     $('#activityTable tbody').on('click', 'tr', function () {
        var data = table.row(this).data();
        if (data) {
            window.location.href = "sales/customer-activity/view/" + data.id;
        }
    });
});
</script>
@endsection
