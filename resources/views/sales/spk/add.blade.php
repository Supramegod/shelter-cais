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
                <input type="hidden" name="leads_id" id="leads_id" value="{{$leads->id}}">
                <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_spk">Tanggal SPK <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" id="tanggal_spk" name="tanggal_spk" class="form-control" value="{{ old('tanggal_spk', date('Y-m-d')) }}">
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
                <div class="col-sm-4">
                    <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="{{$leads->nama_perusahaan}}" class="form-control" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Provinsi</label>
                <div class="col-sm-4">
                    <input type="text" id="provinsi" name="provinsi" value="{{$leads->provinsi}}" class="form-control" readonly>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Kota</label>
                <div class="col-sm-4">
                    <input type="text" id="kota" name="kota" value="{{$leads->kota}}" class="form-control" readonly>
                </div>
            </div>
            <br>
            <div class="content-header mb-3 text-center">
              <h4>List Site</h4>
            </div>
            <div id="d-table-quotation" class="row mb-3">
              <div class="table-responsive overflow-hidden table-quotation">
                <table id="table-quotation" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                  <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="check-all-sites" class="form-check-input" style="transform: scale(1.5); margin-right: 8px;" />
                        </th>
                        <th>Quotation</th>
                        <th>Nama Site</th>
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
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#check-all-sites').on('change', function() {
        $('.site-checkbox').prop('checked', this.checked);
    });

    $('.site-checkbox').on('change', function() {
        $('#check-all-sites').prop('checked', $('.site-checkbox:checked').length === $('.site-checkbox').length);
    });

    $('#leads_id').val('{{$leads->id}}');
    $('#nama_perusahaan').val('{{$leads->nama_perusahaan}}');
    $('#provinsi').val('{{$leads->provinsi}}');
    $('#kota').val('{{$leads->kota}}');

    $.ajax({
    url: '{{route("spk.get-site-available-list")}}',
    type: 'GET',
    data: { leads: {{$leads->id}} },
    success: function(data) {
    $('#tbody-quotation').empty();
    $('#tbody-quotation').append('');

    $.each(data, function(key, value) {
        $('#tbody-quotation').append(
        '<tr>' +
            '<td>' +
        '<input type="checkbox" name="site_ids[]" value="' + value.id + '" class="form-check-input site-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
            '</td>' +
            '<td>' + value.quotation + '</td>' +
            '<td>' + value.nama_site + '</td>' +
        //   '<td>' + value.provinsi + '</td>' +
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

    if (!obj.leads_id) {
        msg += "Leads / Customer tidak boleh kosong.<br>";
    }
    if (!obj['site_ids[]']) {
        msg += "Silakan pilih minimal satu site untuk membuat SPK.<br>";
    }

    // Check if tanggal_spk is empty
    if (!$('#tanggal_spk').val()) {
        msg += "Tanggal tidak boleh kosong.<br>";
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

</script>
@endsection
