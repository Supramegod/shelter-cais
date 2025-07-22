@extends('layouts.master')
@section('title','PKS')
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
            <span class="text-center">Form PKS Baru</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('pks.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">PKS</h4>
              <h4>Pilih SPK Untuk Dijadikan PKS</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">SPK <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="spk_id" name="spk_id" value="" class="form-control">
                <div class="input-group">
                  @if($spk ==null)
                    <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-spk"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Spk</button>
                  @endif
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nomor SPK <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <div class="input-group">
                  <input type="text" id="spk" name="spk" value="" class="form-control" readonly>
                </div>
              </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end" for="kategoriHC">Kategori Sesuai HC <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select id="kategoriHC" name="kategoriHC" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriHC as $kategori)
                            <option value="{{$kategori->id}}" {{ old('kategoriHC') == $kategori->id ? 'selected' : '' }}>{{$kategori->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_pks">Tanggal PKS <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" id="tanggal_pks" name="tanggal_pks" class="form-control" value="{{ old('tanggal_pks', date('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
              <div class="col-sm-4">
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
              <div class="col-sm-4">
                <input type="text" id="kebutuhan" name="kebutuhan" value="" class="form-control" readonly>
              </div>
            </div>
            <div class="content-header mb-3 text-center">
              <h4>List Site</h4>
            </div>
            <div id="d-table-spk" class="row mb-3">
              <div class="table-responsive overflow-hidden table-spk">
                <table id="table-spk" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="check-all-sites" class="form-check-input" style="transform: scale(1.5); margin-right: 8px;" />
                        </th>
                      <th>No.</th>
                      <th>Nama Site</th>
                      <th>Provinsi</th>
                      <th>Kota</th>
                      <th>Penempatan</th>
                    </tr>
                  </thead>
                  <tbody class="tbody-spk" id="tbody-spk">
                    {{-- data table ajax --}}
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat PKS</span>
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

<div class="modal fade" id="modal-spk" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Spk</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nomor</th>
                    <th class="text-center">Tgl Spk</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Kebutuhan</th>
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
  $('#btn-modal-cari-spk').on('click',function(){
    $('#modal-spk').modal('show');
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
          url: "{{ route('pks.available-spk') }}",
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
                data : 'tgl_spk',
                name : 'tgl_spk',
                className:'text-center'
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'kebutuhan',
                name : 'kebutuhan',
                className:'text-center'
            }],
      "language": datatableLang,
  });

  $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-spk').modal('hide');
      var rdata = table.row(this).data();
      $('#spk_id').val(rdata.id);
      $('#spk').val(rdata.nomor);
      $('#nama_perusahaan').val(rdata.nama_perusahaan);
      $('#kebutuhan').val(rdata.kebutuhan);

      $("#d-table-spk").removeClass("d-none");
      $.ajax({
        url: '{{route("spk.get-site-list")}}',
        type: 'GET',
        data: { spk_id: rdata.id },
        success: function(data) {
        $('#tbody-spk').empty();
        $('#tbody-spk').append('');

        $.each(data, function(key, value) {
          $('#tbody-spk').append(
            '<tr>' +
              '<td>' +
            '<input type="checkbox" name="site_ids[]" value="' + value.id + '" class="form-check-input site-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
              '</td>' +
              '<td>' + value.no + '</td>' +
              '<td>' + value.nama_site + '</td>' +
              '<td>' + value.provinsi + '</td>' +
              '<td>' + value.kota + '</td>' +
              '<td>' + value.penempatan + '</td>' +
            '</tr>'
          );
        });
        },
        error: function() {
          alert('Gagal mengambil data');
        }
      });
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

    if(obj.spk_id == null || obj.spk_id == "" ){
      msg += "<b>Spk</b> belum dipilih </br>";
    };

    if(obj.tanggal_pks == null || obj.tanggal_pks == ""){
      msg += "<b>Tanggal PKS</b> tidak boleh kosong </br>";
    }
    if(obj.kategoriHC == null || obj.kategoriHC == ""){
      msg += "<b>Kategori Sesuai HC</b> belum dipilih </br>";
    }
    if (!obj['site_ids[]']) {
        msg += "Silakan pilih minimal satu site untuk membuat SPK.<br>";
    }



    if(msg == ""){
      form.submit();
    }else{
      Swal.fire({
        title: "Pemberitahuan",
        html: msg,
        icon: "warning"
      });
    }
  });

$('#check-all-sites').on('change', function() {
        $('.site-checkbox').prop('checked', this.checked);
    });

    $('.site-checkbox').on('change', function() {
        $('#check-all-sites').prop('checked', $('.site-checkbox:checked').length === $('.site-checkbox').length);
    });
</script>
@endsection
