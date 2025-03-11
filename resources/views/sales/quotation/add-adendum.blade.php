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
            <span class="text-center">Form Quotation ( {{$tipe}} )</span>
            <span class="text-center"><button class="btn btn-secondary waves-effect @if(old('leads_id')==null) d-none @endif" type="button" id="btn-lihat-leads"><span class="tf-icons mdi mdi-arrow-right-circle-outline me-1"></span>&nbsp; Lihat Leads</button>&nbsp;&nbsp;&nbsp;&nbsp; <span>{{$now}}</span></span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('quotation.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">QUOTATION</h4>
              <h4>Pilih Quotation Untuk Adendum</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Quotation <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="tipe" name="tipe" class="form-control">
                <input type="hidden" id="leads_id" name="leads_id" class="form-control">
                <input type="hidden" id="quotation_id" name="quotation_id" class="form-control">
                <div class="input-group">
                  <input type="text" id="leads" name="leads"  class="form-control" readonly>
                  <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-leads"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Leads</button>
                </div>
              </div>
            </div>
            <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-sm-end">Layanan <span class="text-danger">*</span></label>
              <div class="col-sm-10">
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
            <label class="col-sm-2 col-form-label text-sm-end">Entitas <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select id="entitas" name="entitas" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                </select>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Jumlah Site <span class="text-danger">*</span></label>
              <div class="col-sm-4">
                <select id="jumlah_site" name="jumlah_site" class="form-select" data-allow-clear="true" tabindex="-1">
                  <option value="">- Pilih data -</option>
                  <option value="Single Site">Single Site</option>
                  <option value="Multi Site">Multi Site</option>
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
            <div class="d-single-site">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Provinsi  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-4">
                  <select id="provinsi" name="provinsi" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih data -</option>
                    @foreach($province as $data)
                      <option value="{{$data->id}}" data-ump="{{$data->ump}}">{{$data->name}}</option>  
                    @endforeach
                  </select>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Kabupaten/Kota  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-4">
                  <select id="kota" name="kota" class="form-select" data-allow-clear="true" tabindex="-1">
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Nama Site  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-10">
                  <input type="text" id="nama_site" name="nama_site" value="" class="form-control">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Alamat Penempatan <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-10">
                  <input type="text" id="penempatan" name="penempatan" value="" class="form-control">
                </div>
              </div>
            </div>
            <div class="row mb-3 d-multi-site">
              <hr class="mb-3">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Provinsi  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-4">
                  <select id="provinsiMulti" name="provinsiMulti" class="form-select" data-allow-clear="true" tabindex="-1">
                    <option value="">- Pilih data -</option>
                    @foreach($province as $data)
                      <option value="{{$data->id}}" data-ump="{{$data->ump}}">{{$data->name}}</option>  
                    @endforeach
                  </select>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end">Kabupaten/Kota  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-4">
                  <select id="kotaMulti" name="kotaMulti" class="form-select" data-allow-clear="true" tabindex="-1">
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Nama Site  <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-10 col-form-label text-sm-end">
                  <input type="text" class="form-control" id="siteName" placeholder="Nama Site">
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Alamat Penempatan <span class="text-danger fw-bold">*</span></label>
                <div class="col-sm-10">
                  <input type="text" id="penempatanMulti" name="penempatanMulti" value="" class="form-control">
                </div>
              </div>
              <div class="col-sm-12 d-flex justify-content-center">
                <button type="button" id="addSiteBtn" class="btn btn-info w-50 mt-2">Tambah Site</button>
              </div>

              <div class="row mt-4">
              <div class="offset-sm-2 col-sm-10 d-flex">
                <table class="table table-bordered mt-4">
                  <thead>
                      <tr>
                          <th>Nama Site</th>
                          <th>Provinsi</th>
                          <th>Kota</th>
                          <th>Penempatan</th>
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
          url: "{{ route('leads.available-quotation') }}",
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

    generateNama();
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
        if(obj.provinsi == null || obj.provinsi == "" ){
          msg += "<b>Provinsi</b> belum diisi </br>";
        }
        if(obj.kota == null || obj.kota == "" ){
          msg += "<b>Kota</b> belum diisi </br>";
        }
        if(obj.penempatan == null || obj.penempatan == "" ){
          msg += "<b>Penempatan</b> belum diisi </br>";
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

$('#provinsi').on('change', function() {
    $('#kota').find('option').remove();
    $('#kota').append('<option value="">- Pilih data -</option>');
    if(this.value!=""){
      var param = "province_id="+this.value;
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            $('#kota').append('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
      });
    }
  });

  $('#kota').on('change', function() {
    generateNama();
  })
  $('#kotaMulti').on('change', function() {
    generateNama();
  })

  $('#provinsiMulti').on('change', function() {
    $('#kotaMulti').find('option').remove();
    $('#kotaMulti').append('<option value="">- Pilih data -</option>');
    if(this.value!=""){
      var param = "province_id="+this.value;
      $.ajax({
        url: "{{route('quotation.change-kota')}}",
        type: 'GET',
        data: param,
        success: function(res) {
          res.forEach(element => {
            let selected = "";
            $('#kotaMulti').append('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>');
          });
        }
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

  $('#layanan').on('change', function() {
    $('#entitas').find('option').remove();
    $('#entitas').append('<option value="">- Pilih data -</option>');
    if(this.value!=""){
      if (this.value == 1) {
        @foreach($company as $value)
          @if($value->code=="GSU" || $value->code=="SN")
          $('#entitas').append('<option value="{{$value->id}}">{{$value->code}} | {{$value->name}}</option>');
          @endif
          @endforeach
      } else if (this.value == 2 || this.value == 4) {
        @foreach($company as $value)
          @if($value->code=="SIG" || $value->code=="SNI")
          $('#entitas').append('<option value="{{$value->id}}">{{$value->code}} | {{$value->name}}</option>');
          @endif
        @endforeach
      } else if (this.value == 3) {
        @foreach($company as $value)
          @if($value->code=="RCI" || $value->code=="SNI")
          $('#entitas').append('<option value="{{$value->id}}">{{$value->code}} | {{$value->name}}</option>');
          @endif
        @endforeach
      }
    }
  });



  $('.d-single-site').addClass('d-none');
  $('.d-multi-site').addClass('d-none');

  $('#jumlah_site').on('change', function() {
    showJumlahSite();
  });

  $('#addSiteBtn').click(function() {
      var siteName = $('#siteName').val().trim();
      var province = $('#provinsiMulti').val();
      var city = $('#kotaMulti').val();
      var penempatan = $('#penempatanMulti').val();

      let msg = "";

      if(siteName == null || siteName == "" ){
        msg += "<b>Nama Site</b> belum diisi </br>";
      };
      if(province == null || province == "" ){
        msg += "<b>Provinsi</b> belum dipilih </br>";
      };
      if(city == null || city == "" ){
        msg += "<b>Kota</b> belum dipilih </br>";
      };
      if(penempatan == null || penempatan == "" ){
        msg += "<b>Penempatan</b> belum diisi </br>";
      };
      if(msg == ""){
        // Menambahkan baris baru ke tabel
        var newRow = `
            <tr>
                <td>
                    ${siteName}
                    <input type="hidden" name="multisite[]" value="${siteName}">
                    <input type="hidden" name="provinsi_multi[]" value="${province}">
                    <input type="hidden" name="kota_multi[]" value="${city}">
                    <input type="hidden" name="penempatan_multi[]" value="${penempatan}">
                </td>
                <td>
                    ${$('#provinsiMulti option:selected').text()}
                </td>
                <td>
                    ${$('#kotaMulti option:selected').text()}
                </td>
                <td>
                    ${penempatan}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm delete-btn">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#siteTableBody').append(newRow);
        // $('#siteName').val(''); // Kosongkan input setelah tambah
      }else{
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning"
        });
        return;
      }
      
      if (siteName === "") {
          alert("Nama Site tidak boleh kosong.");
      }
  });

  // Menghapus baris ketika tombol delete diklik
  $('#siteTableBody').on('click', '.delete-btn', function() {
      $(this).closest('tr').remove();
  });
});

function generateNama() {
    let nama = "";
    nama += $("#leads").val();
    nama += " - ";

    $("#nama_site").val(nama+$('#kota option:selected').text());
    $("#siteName").val(nama+$('#kotaMulti option:selected').text());
  }
</script>
@endsection