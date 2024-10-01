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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-3')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">HEADCOUNT</h6>
                <!--<h4>Pilih Site dan Jenis Kontrak</h4>-->
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mb-3">
                <div class="col-xl-12">
                  <div class="nav-align-top">
                    <ul class="nav nav-fill nav-tabs" role="tablist" >
                      @foreach($quotationKebutuhan as $value)
                        <li class="nav-item" role="presentation">
                          <button type="button" class="nav-link waves-effect @if($loop->first) active @endif" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-{{$value->id}}" aria-controls="navs-justified-{{$value->id}}" aria-selected="true">
                            <i class="tf-icons {{$value->icon}} me-1"></i> 
                            {{$value->kebutuhan}}
                          </button>
                        </li>
                      @endforeach
                      <span class="tab-slider" style="left: 0px; width: 226.484px; bottom: 0px;"></span>
                    </ul>
                  </div>
                  <div class="tab-content p-0">
                    @foreach($quotationKebutuhan as $value)
                    <div class="tab-pane fade @if($loop->first) active show @endif" id="navs-justified-{{$value->id}}" role="tabpanel">
                      <div class="row mb-3 mt-3">
                        <div class="col-sm-6">
                          <label class="form-label" for="jabatan_detail_{{$value->id}}">Nama Posisi/Jabatan</label>
                          <div class="input-group">
                            <select id="jabatan_detail_{{$value->id}}" name="nama_jabatan" class="form-select" data-allow-clear="true" tabindex="-1">
                              <option value="">- Pilih data -</option>
                              @foreach($value->detail as $detail)
                                <option value="{{$detail->id}}">{{$detail->nama}}</option>  
                              @endforeach  
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <label class="form-label" for="jumlah_hc_{{$value->id}}">Jumlah Headcount</label>
                          <div class="input-group">
                            <input type="number" class="form-control" id="jumlah_hc_{{$value->id}}">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                          <button type="button" data-qbutuhid="{{$value->id}}" id="btn-tambah-detail-{{$value->id}}" class="btn btn-info btn-back w-20">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Data</span>
                            <i class="mdi mdi-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="row mt-5">
                        <div class="table-responsive overflow-hidden table-data-{{$value->id}}">
                          <table id="table-data-{{$value->id}}" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                              <thead>
                                  <tr>
                                      <th class="text-center">ID</th>
                                      <th class="text-center">Kebutuhan</th>
                                      <th class="text-center">Nama Posisi/Jabatan</th>
                                      <th class="text-center">Jumlah Headcount</th>
                                      <th class="text-center">Aksi</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  {{-- data table ajax --}}
                              </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
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

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  @foreach($quotationKebutuhan as $value)
    $('#table-data-{{$value->id}}').DataTable({
      scrollX: true,
      "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('quotation.list-detail-hc') }}",
          data: function (d) {
              d.quotation_kebutuhan_id = {{$value->id}};
          },
      },   
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },{
          data : 'kebutuhan',
          name : 'kebutuhan',
          className:'text-center'
      },{
          data : 'jabatan_kebutuhan',
          name : 'jabatan_kebutuhan',
          className:'text-center'
      },{
          data : 'jumlah_hc',
          name : 'jumlah_hc',
          className:'text-center'
      },{
          data : 'aksi',
          name : 'aksi',
          width: "10%",
          orderable: false,
          searchable: false,
      }],
      "language": datatableLang,
    });

    $('#btn-tambah-detail-{{$value->id}}').on('click',function () {
      let jabatanDetailId = $('#jabatan_detail_{{$value->id}}').val();
      let jumlahHc = $('#jumlah_hc_{{$value->id}}').val();

      let msg="";
      if(jabatanDetailId ==""){
        msg += "Nama Posisi / Jabatan Belum Diisi <br />";
      }
      if(jumlahHc ==""){
        msg += "Jumlah HC Belum Diisi";
      }

      if(msg!=""){
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning",
        });
      }else{
        let formData = {
          "jabatan_detail_id":jabatanDetailId,
          "jumlah_hc":jumlahHc,
          "quotation_kebutuhan_id":{{$value->id}},
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.add-detail-hc')}}",
          data:formData,
          success: function(response){
              $('#table-data-{{$value->id}}').DataTable().ajax.reload();
              $('#jabatan_detail_{{$value->id}}').val("");
              $('#jumlah_hc_{{$value->id}}').val("");
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  @endforeach

  $('body').on('click', '.btn-delete', function() {
    let formData = {
      "id":$(this).data('id'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data-'+$(this).data('kebutuhan');
    $.ajax({
      type: "POST",
      url: "{{route('quotation.delete-detail-hc')}}",
      data:formData,
      success: function(response){
        $(table).DataTable().ajax.reload();
      },
      error:function(error){
        console.log(error);
      }
    });
  });
</script>
@endsection