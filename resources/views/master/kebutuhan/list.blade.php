@extends('layouts.master')
@section('title','Kebutuhan')
@section('pageStyle')
<style>
    .dt-buttons {width: 100%;}
    thead input, thead select {
        width: 100% !important;
        box-sizing: border-box;
    }
</style>
@endsection
@section('content')
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Row -->
    <div class="row row-sm mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                    <div class="col-md-6 text-left col-12 my-auto">
                        <h3 class="page-title">Kebutuhan</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kebutuhan</li>
                        </ol>
                    </div>
                </div>
                <div class="card-body">
                    <div class="overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;" data-resizable>
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nama Kebutuhan</th>
                                    <th class="text-center">Icon</th>
                                    <th class="text-center">Dibuat Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <select class="form-control form-control-sm column-filter" data-column="1">
                                            <option value="">-- Semua --</option>
                                        </select>
                                    </th>
                                    <th><input type="text" class="form-control form-control-sm column-filter" data-column="2" placeholder="Cari Icon"></th>
                                    <th><input type="text" class="form-control form-control-sm column-filter" data-column="3" placeholder="Cari Tanggal"></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table ajax --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
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
    @if(session()->has('error'))
        Swal.fire({
            title: 'Pemberitahuan',
            html: '{{session()->get('error')}}',
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-warning waves-effect waves-light'
            },
            buttonsStyling: false
        });
    @endif

    let dt_filter_table = $('.dt-column-search');

    var table = $('#table-data').DataTable({
        scrollX: true,
        orderCellsTop: true,
        colReorder: true,
        colResize: true,
        "iDisplayLength": 25,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('kebutuhan.list') }}",
            data: function (d) {
                // Optional: tambah parameter kustom di sini
            },
        },
        "order":[
            [0,'desc']
        ],
        columns:[
            {
                data : 'id',
                name : 'id',
                visible: false,
                searchable: false
            },
            {
                data : 'nama',
                name : 'nama',
                className:'dt-body-left'
            },
            {
                data : 'icon',
                name : 'icon',
                className:'text-center'
            },
            {
                data : 'created_at',
                name : 'created_at',
                className:'text-center'
            },
            {
                data : 'aksi',
                name : 'aksi',
                orderable: false,
                searchable: false,
            }
        ],
        "language": datatableLang,
        dom: 'frtip',
        buttons: [],
        initComplete: function () {
            // Apply filter logic
            $('.column-filter').on('change keyup', function () {
                let i = $(this).data('column');
                let v = $(this).val();
                table.column(i).search(v).draw();
            });

            $('#table-data').resizableColumns();
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'colvis',
                text: 'Tampilkan/Sembunyikan Kolom',
                className: 'btn btn-secondary btn-sm'
            }
        ],
    });

    // Populate select option for Nama Kebutuhan (column index 1)
    table.on('xhr', function () {
        let data = table.ajax.json().data;
        let select = $('select[data-column="1"]');
        let unique = [...new Set(data.map(item => item.nama))];
        select.find('option:not(:first)').remove();
        unique.forEach(val => {
            select.append(`<option value="${val}">${val}</option>`);
        });
    });

</script>
@endsection
