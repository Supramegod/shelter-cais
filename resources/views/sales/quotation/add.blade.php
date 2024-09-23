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
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button type="submit" class="btn btn-primary btn-next w-20" style="color:white">
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

</script>
@endsection