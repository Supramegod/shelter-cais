@extends('layouts.master')
@section('title','Putus Kontrak')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Putus Kontrak Baru</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden">
          @csrf
          <input type="hidden" name="pks_id" id="pks_id">
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4>Putus Kontrak</h4>
              <h4>Silahkan Pilih Kontrak</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Kontrak <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <div class="input-group">
                    <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-kontrak"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Kontrak</button>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nomor Kontrak</label>
              <div class="col-sm-4">
                <input type="text" id="nomor" name="nomor" value="" class="form-control" disabled>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
              <div class="col-sm-4">
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Putus Kontrak</span>
                  <i class="mdi mdi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<div class="modal fade" id="modal-kontrak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-simple modal-enable-otp modal-dialog-centered" style="max-width:90vw;">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Daftar Kontrak</h3>
                </div>
                <div class="row">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nomor</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Nama Site</th>
                                    <th class="text-center">Nama Proyek</th>
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
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#btn-modal-cari-kontrak').on('click',function(){
    $('#modal-kontrak').modal('show');
  });

  let dt_filter_table = $('.dt-column-search');

  var table = $('#table-data').DataTable({
      "initComplete": function (settings, json) {
        $("#table-data").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
      },
      "bDestroy": true,
      "iDisplayLength": 25,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('putus-kontrak.available-kontrak') }}",
          data: function (d) {

          },
      },
      "order":[
          [0,'desc']
      ],
      columns:[{
                data : 'id',
                name : 'id',
                searchable: false
            },{
                data : 'nomor',
                name : 'nomor',
                className:'text-center'
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'nama_site',
                name : 'nama_site',
                className:'text-center'
            },{
                data : 'nama_proyek',
                name : 'nama_proyek',
                className:'text-center'
            }],
      "language": datatableLang,
  });

  $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-kontrak').modal('hide');
      var rdata = table.row(this).data();
      $('#pks_id').val(rdata.id);
      $('#nomor').val(rdata.nomor);
      $('#nama_perusahaan').val(rdata.nama_perusahaan);
    });


    $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {
        e.preventDefault();
        return false;
      }
    });

  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    let msg = "";
    let obj = $("form").serializeObject();

    if(obj.pks_id == null || obj.pks_id == "" ){
      msg += "<b>Kontrak</b> belum dipilih </br>";
    };

    if(msg == ""){
    window.location.href = "{{ url('/sales/putus-kontrak/add-putus-kontrak') }}/" + obj.pks_id;
    }else{
      Swal.fire({
        title: "Pemberitahuan",
        html: msg,
        icon: "warning"
      });
    }
  });

</script>
@endsection
