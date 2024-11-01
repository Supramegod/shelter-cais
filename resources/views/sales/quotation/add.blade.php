@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form Quotation Baru</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-leads"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('quotation.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">LEADS</h4>
              <h4>Pilih Leads Untuk Quotation</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Leads / customer <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="leads_id" name="leads_id" value="{{old('leads_id')}}" class="form-control">
                <div class="input-group">
                  <input type="text" id="leads" name="leads" value="{{old('leads')}}" class="form-control @if ($errors->any()) @if($errors->has('leads')) is-invalid @else   @endif @endif" readonly>
                  <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-leads"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Leads</button>
                  @if($errors->has('leads'))
                    <div class="invalid-feedback">{{$errors->first('leads')}}</div>
                  @endif
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Entitas <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select id="entitas" name="entitas" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  @foreach($company as $value)
                  <option value="{{$value->id}}">{{$value->code}} | {{$value->name}}</option>  
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Jumlah Site <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select id="jumlah_site" name="jumlah_site" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  <option value="Single Site">Single Site</option>
                  <option value="Multi Site">Multi Site</option>
                </select>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Layanan <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select id="layanan" name="layanan" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  <option value="1">Security</option>
                  <option value="2">Direct Labour</option>
                  <option value="3">Cleaning Service</option>
                  <option value="4">Logistik</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Wilayah</label>
              <div class="col-sm-4">
                <input type="text" id="branch" name="branch" value="{{old('branch')}}" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Kebutuhan</label>
              <div class="col-sm-4">
                <input type="text" id="kebutuhan" name="kebutuhan" value="{{old('kebutuhan')}}" class="form-control" readonly>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Tim Sales</label>
              <div class="col-sm-4">
                <input type="text" id="tim_sales_name" name="tim_sales_name" value="{{old('tim_sales_name')}}" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Sales</label>
              <div class="col-sm-4">
                <input type="text" id="sales_name" name="sales_name" value="{{old('sales_name')}}" class="form-control" readonly>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">CRM</label>
              <div class="col-sm-4">
                <input type="text" id="crm_name" name="crm_name" value="{{old('crm_name')}}" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">RO</label>
              <div class="col-sm-4">
                <input type="text" id="ro_name" name="ro_name" value="{{old('ro_name')}}" class="form-control" readonly>
              </div>
            </div>
            <div class="row mb-3 d-single-site">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Site  <span class="text-danger fw-bold">*</span></label>
              <div class="col-sm-10">
                <input type="text" id="nama_site" name="nama_site" value="" class="form-control">
              </div>
            </div>
            <div class="row mb-3 d-multi-site">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Site  <span class="text-danger fw-bold">*</span></label>
              <div class="col-sm-8 col-form-label text-sm-end">
                <input type="text" class="form-control" id="siteName" placeholder="Nama Site">
              </div>
              <div class="col-sm-2">
                <button type="button" id="addSiteBtn" class="btn btn-info w-100 mt-2">Tambah Site</button>
              </div>

              <div class="row mt-4">
              <div class="offset-sm-2 col-sm-10 d-flex">
                <table class="table table-bordered mt-4">
                  <thead>
                      <tr>
                          <th>Nama Site</th>
                          <th>Aksi</th>
                      </tr>
                  </thead>
                  <tbody id="siteTableBody">
                      <!-- Rows will be added here -->
                  </tbody>
              </table>
              </div>
            </div>
            </div>
            <div class="row mt-5">
              <div class="col-12 d-flex flex-row-reverse">
                <button type="button" id="btn-submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat Quotation</span>
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

<div class="modal fade" id="modal-leads" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Leads / Customer</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Tgl Leads</th>
                    <th class="text-center">Wilayah</th>
                    <th class="text-center">PIC</th>
                    <th class="text-center">No. Telp PIC</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Status</th>
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
  $('#btn-modal-cari-leads').on('click',function(){
    $('#modal-leads').modal('show');
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
          url: "{{ route('leads.available-leads') }}",
          data: function (d) {
              
          },
      },   
      "order":[
          [0,'desc']
      ],
      columns:[{
                data : 'id',
                name : 'id',
                visible: false,
                searchable: false
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'tgl',
                name : 'tgl',
                className:'text-center'
            },{
                data : 'branch',
                name : 'branch',
                className:'text-center'
            },{
                data : 'pic',
                name : 'pic',
                className:'text-center'
            },{
                data : 'no_telp',
                name : 'no_telp',
                className:'text-center'
            },{
                data : 'email',
                name : 'email',
                className:'text-center'
            },{
                data : 'status',
                name : 'status',
                className:'text-center'
            }],
      "language": datatableLang,
  });

  $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-leads').modal('hide');
      var rdata = table.row(this).data();
      $('#branch').val(rdata.branch);
      $('#leads').val(rdata.nama_perusahaan);
      $('#leads_id').val(rdata.id);
      $('#kebutuhan').val(rdata.kebutuhan);
      $('#tim_sales_name').val(rdata.tim_sales);
      $('#sales_name').val(rdata.sales);
      $('#ro_name').val(rdata.ro);
      $('#crm_name').val(rdata.crm);

      $('#sales_d').val("");

      if(rdata.tim_sales_id !=null){
        $('#tim_sales_id').val(rdata.tim_sales_id).change();
        if(rdata.tim_sales_d_id != null){
          $('#sales_d').val(rdata.tim_sales_d_id);
        }
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

  if(obj.leads_id == null || obj.leads_id == "" ){
    msg += "<b>Leads</b> belum dipilih </br>";
  };
  if(obj.entitas == null || obj.entitas == "" ){
    msg += "<b>Entitas</b> belum dipilih </br>";
  };
  if(obj.kebutuhan == null || obj.kebutuhan == "" ){
    msg += "<b>Kebutuhan</b> belum dipilih </br>";
  }; 
  if(obj.jumlah_site == null || obj.jumlah_site == "" ){
      msg += "<b>Jumlah Site</b> belum dipilih </br>";
    }else{
      if(obj.jumlah_site =="Single Site"){
        if(obj.nama_site == null || obj.nama_site == "" ){
          msg += "<b>Nama Site</b> belum diisi </br>";
        }
      }else{
        if(obj["multisite[]"] == null || obj["multisite[]"] == "" ){
          msg += "Isikan minimal 1 <b>Site</b></br>";
        }
      }
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


function showJumlahSite() {
  let selected = $("#jumlah_site option:selected").val();
  if (selected=="Single Site") {
    $('.d-single-site').removeClass('d-none');
    $('.d-multi-site').addClass('d-none');
  }else if (selected=="Multi Site") {
    $('.d-single-site').addClass('d-none');
    $('.d-multi-site').removeClass('d-none');
  }else{
    $('.d-single-site').addClass('d-none');
    $('.d-multi-site').addClass('d-none');
  }
}

$(document).ready(function() {
  $('.d-single-site').addClass('d-none');
  $('.d-multi-site').addClass('d-none');

  $('#jumlah_site').on('change', function() {
    showJumlahSite();
  });

  $('#addSiteBtn').click(function() {
      var siteName = $('#siteName').val().trim();

      if (siteName === "") {
          alert("Nama Site tidak boleh kosong.");
          return;
      }

      // Menambahkan baris baru ke tabel
      var newRow = `
          <tr>
              <td>
                  ${siteName}
                  <input type="hidden" name="multisite[]" value="${siteName}">
              </td>
              <td>
                  <button type="button" class="btn btn-danger btn-sm delete-btn">
                      <i class="mdi mdi-delete"></i>
                  </button>
              </td>
          </tr>
      `;
      $('#siteTableBody').append(newRow);
      $('#siteName').val(''); // Kosongkan input setelah tambah
  });

  // Menghapus baris ketika tombol delete diklik
  $('#siteTableBody').on('click', '.delete-btn', function() {
      $(this).closest('tr').remove();
  });
});


</script>
@endsection