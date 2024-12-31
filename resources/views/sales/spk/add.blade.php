@extends('layouts.master')
@section('title','SPK')
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
            <span class="text-center">Form SPK Baru</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-quotation"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Quotation</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('spk.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">SPK</h4>
              <h4>Pilih Quotation Untuk Dijadikan SPK</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Quotation <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="quotation_id" name="quotation_id" value="@if($quotation !=null) {{$quotation->id}} @endif" class="form-control">
                <div class="input-group">
                  <input type="text" id="quotation" name="quotation" value="@if($quotation !=null) {{$quotation->nomor}} @endif" class="form-control" readonly>
                  @if($quotation ==null)
                    <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-quotation"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Quotation</button>
                    @if($errors->has('quotation'))
                      <div class="invalid-feedback">{{$errors->first('quotation')}}</div>
                    @endif
                  @endif
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
              <div class="col-sm-4">
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="@if($quotation !=null) {{$quotation->nama_perusahaan}} @endif" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
              <div class="col-sm-4">
                <input type="text" id="kebutuhan" name="kebutuhan" value="@if($quotation !=null) {{$quotation->kebutuhan}} @endif" class="form-control" readonly>
              </div>
            </div>
            <br>
            <div class="content-header mb-3 text-center">
              <h4>List Site</h4>
            </div>
            <div id="d-table-quotation" class="row mb-3 d-none">
              <div class="table-responsive overflow-hidden table-quotation">
                <table id="table-quotation" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Nama Site</th>
                      <th>Provinsi</th>
                      <th>Kota</th>
                      <th>Penempatan</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-quotation">
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat SPK</span>
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

<div class="modal fade" id="modal-quotation" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Quotation</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Tgl Quotation</th>
                    <th class="text-center">Jumlah Site</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Kebutuhan</th>
                    <th class="text-center">Quotation</th>
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
  $('#btn-modal-cari-quotation').on('click',function(){
    $('#modal-quotation').modal('show');
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
          url: "{{ route('spk.available-quotation') }}",
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
                data : 'tgl_quotation',
                name : 'tgl_quotation',
                className:'text-center'
            },{
                data : 'jumlah_site',
                name : 'jumlah_site',
                className:'text-center'
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'layanan',
                name : 'layanan',
                className:'text-center'
            },{
                data : 'quotation',
                name : 'quotation',
                className:'text-center'
            }],
      "language": datatableLang,
  });

  @if($quotation != null)
    $("#d-table-quotation").removeClass("d-none");
    $.ajax({
      url: '{{route("quotation.get-site-list")}}',
      type: 'GET',
      data: { quotation_id: {{$quotation->id}} },
      success: function(data) {
      $('#tbody-quotation').empty();
      $('#tbody-quotation').append('');

      $.each(data, function(key, value) {
          $('#tbody-quotation').append('<tr><td>'+value.no+'</td><td>'+value.nama_site+'</td><td>'+value.provinsi+'</td><td>'+value.kota+'</td><td>'+value.penempatan+'</td></tr>');
      });
      },
      error: function() {
        alert('Gagal mengambil data');
      }
    });
  @endif

  $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-quotation').modal('hide');
      var rdata = table.row(this).data();
      $('#quotation_id').val(rdata.id);
      $('#quotation').val(rdata.quotation);
      $('#nama_perusahaan').val(rdata.nama_perusahaan);
      $('#layanan').val(rdata.layanan);

      $("#d-table-quotation").removeClass("d-none");
      $.ajax({
        url: '{{route("quotation.get-site-list")}}',
        type: 'GET',
        data: { quotation_id: rdata.id },
        success: function(data) {
        $('#tbody-quotation').empty();
        $('#tbody-quotation').append('');

        $.each(data, function(key, value) {
          $('#tbody-quotation').append('<tr><td>'+value.no+'</td><td>'+value.nama_site+'</td><td>'+value.provinsi+'</td><td>'+value.kota+'</td><td>'+value.penempatan+'</td></tr>');
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
      
    if(obj.quotation_id == null || obj.quotation_id == "" ){
      msg += "<b>Quotation</b> belum dipilih </br>";
    };

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

</script>
@endsection