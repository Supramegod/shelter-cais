@extends('layouts.master')
@section('title','Jenis Barang')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Jenis Barang</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-8">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span>Jenis Barang : {{$data->nama}}</span>
          </div>
        </h5>
        <h5 class="card-header">
          <div class="d-flex justify-content-center">
            <span>Detail Barang</span>
          </div>
        </h5>
        <div class="card-body">
          <div class="table-responsive overflow-hidden table-data">
              <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                      <tr>
                        <th class="text-left">Nama</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Masa Pakai</th>
                        <th class="text-center">Merk</th>
                      </tr>
                  </thead>
                  <tbody>
                      {{-- data table ajax --}}
                  </tbody>
              </table>
          </div>
        </div>
        <div class="pt-4">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">Action</h5>
            <div class="dropdown">
            </div>
          </div>
          <div class="card-body">
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
        $('#btn-kembali').on('click',function () {
          window.location.replace("{{route('jenis-barang')}}");
        });

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
                html: '{{session()->has('error')}}',
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
                "iDisplayLength": 25,
                'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
                ajax: {
                    url: "{{ route('jenis-barang.detail-barang') }}",
                    data: function (d) {
                      d.id = {{$data->id}};
                    },
                },   
                "order":[
                    [0,'desc']
                ],
                columns:[{
                    data : 'nama_barang',
                    name : 'nama_barang',
                    className:'dt-body-left'
                },{
                    data : 'harga',
                    name : 'harga',
                    className:'dt-body-right',
                    render: $.fn.dataTable.render.number('.','.', 0,'')
                },{
                    data : 'satuan',
                    name : 'satuan',
                    className:'text-center'
                },{
                    data : 'masa_pakai',
                    name : 'masa_pakai',
                    className:'text-center'
                },{
                    data : 'merk',
                    name : 'merk',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: 'frtip',
                buttons: [
                ],
            });
    </script>
@endsection