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
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
          <form id="form" class="card-body overflow-hidden" action="{{route('quotation.save-edit-1')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">SITE & JENIS KONTRAK</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <!-- <div class="row">
                <h6 class="text-center">Informasi Leads</h6>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="pengusul_kerjasama">Pengusul Kerjasama</label>
                  <input type="text" name="pengusul_kerjasama" value="{{$quotation->pengusul_kerjasama}}" class="form-control" id="pengusul_kerjasama">
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="alamat_pengusul_kerjasama">Alamat Pengusul Kerjasama</label>
                  <input type="text" name="alamat_pengusul_kerjasama" value="{{$quotation->alamat_pengusul_kerjasama}}" class="form-control" id="alamat_pengusul_kerjasama">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="npwp">NPWP</label>
                  <input type="text" name="npwp" value="{{$quotation->npwp}}" class="form-control" id="npwp">
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="alamat_npwp">Alamat NPWP</label>
                  <input type="text" name="alamat_npwp" value="{{$quotation->alamat_npwp}}" class="form-control" id="alamat_npwp">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-sm-4">
                  <label class="form-label" for="pic_invoice">PIC Invoice</label>
                  <input type="text" name="pic_invoice" value="{{$quotation->pic_invoice}}" class="form-control" id="pic_invoice">
                </div>
                <div class="col-sm-4">
                  <label class="form-label" for="telp_pic_invoice">No. Telp PIC Invoice</label>
                  <input type="text" name="telp_pic_invoice" value="{{$quotation->telp_pic_invoice}}" class="form-control" id="telp_pic_invoice">
                </div>
                <div class="col-sm-4">
                  <label class="form-label" for="email_pic_invoice">Email PIC Invoice</label>
                  <input type="text" name="email_pic_invoice" value="{{$quotation->email_pic_invoice}}" class="form-control" id="email_pic_invoice">
                </div>
              </div> -->
              <div class="row mb-2">
                <h6 class="text-center">Jumlah Site</h6>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if($quotation->jumlah_site=='Single Site') checked @endif">
                    <label class="form-check-label custom-option-content" for="single_site">
                      <span class="custom-option-body">
                        <i class="mdi mdi-map-marker-outline"></i>
                        <span class="custom-option-title">Single Site</span>
                      </span>
                      <input name="jumlah_site" class="form-check-input" type="radio" value="Single Site" id="single_site" @if($quotation->jumlah_site=='Single Site') checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if($quotation->jumlah_site=='Multi Site') checked @endif">
                    <label class="form-check-label custom-option-content" for="multi_site">
                      <span class="custom-option-body">
                        <i class="mdi mdi-map-marker-multiple-outline"></i>
                        <span class="custom-option-title">Multi Site</span>
                      </span>
                      <input name="jumlah_site" class="form-check-input" type="radio" value="Multi Site" id="multi_site" @if($quotation->jumlah_site=='Multi Site') checked @endif>
                    </label>
                  </div>
                </div>
                @if($errors->has('jumlah_site'))
                  <span class="text-danger">{{$errors->first('jumlah_site')}}</span>
                @endif
              </div>
              <hr style="margin-top: 1rem;
  margin-bottom: 1rem;
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, 0.1);"/>
              <div class="row mb-2 mt-5">
                <h6 class="text-center">Jenis Kontrak</h6>
              </div>
              <div class="row mb-3">
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if($quotation->jenis_kontrak=='PKWT') checked @endif">
                    <label class="form-check-label custom-option-content" for="pkwt">
                      <span class="custom-option-body">
                        <i class="mdi mdi-file-sign"></i>
                        <span class="custom-option-title">PKWT</span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" value="PKWT" type="radio" value="" id="pkwt" @if($quotation->jenis_kontrak=='PKWT') checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if($quotation->jenis_kontrak=='PKHL') checked @endif">
                    <label class="form-check-label custom-option-content" for="pkhl">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-outline"></i>
                        <span class="custom-option-title"> PKHL </span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" value="PKHL" type="radio" value="" id="pkhl" @if($quotation->jenis_kontrak=='PKHL') checked @endif>
                    </label>
                  </div>
                </div>
                <div class="col-md mb-md-0 mb-2">
                  <div class="form-check custom-option custom-option-icon @if($quotation->jenis_kontrak=='Borongan') checked @endif">
                    <label class="form-check-label custom-option-content" for="borongan">
                      <span class="custom-option-body">
                        <i class="mdi mdi-account-group-outline"></i>
                        <span class="custom-option-title"> Borongan </span>
                      </span>
                      <input name="jenis_kontrak" class="form-check-input" value="Borongan" type="radio" value="" id="borongan" @if($quotation->jenis_kontrak=='Borongan') checked @endif>
                    </label>
                  </div>
                </div>
                @if($errors->has('jenis_kontrak'))
                  <span class="text-danger">{{$errors->first('jenis_kontrak')}}</span>
                @endif
              </div>
              @include('sales.quotation.action')
            </div>
          </form>
        </div>
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

// validasi input
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
  
  if(obj.jenis_kontrak == null || obj.jenis_kontrak == "" ){
    msg += "<b>Jenis kontrak</b> belum dipilih </br>";
  };

  if(obj.jumlah_site == null || obj.jumlah_site == ""){
    msg += "<b>Jumlah site</b> belum dipilih </br>";
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