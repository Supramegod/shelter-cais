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
                @foreach($quotation->quotation_site as $site)
                  <h6>{{$site->nama_site}}</h6>
                @endforeach
              </div>
              <div class="row mb-3 mt-3">
                <div class="col-sm-12">
                  <label class="form-label" for="site">Site</label>
                  <div class="input-group">
                    @if($quotation->jumlah_site=="Single Site")
                    <div class="d-none">
                      <select id="site" name="site" class="form-select select2" data-allow-clear="true" tabindex="-1">
                        @foreach($quotation->quotation_site as $site)
                          <option value="{{$site->id}}" selected>{{$site->nama_site}}</option>  
                        @endforeach  
                      </select>
                    </div>
                    @else
                      <select id="site" name="site" class="form-select select2" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($quotation->quotation_site as $site)
                          <option value="{{$site->id}}">{{$site->nama_site}}</option>  
                        @endforeach  
                      </select>
                    @endif
                  </div>
                </div>
              </div>
              <div class="row mb-3 mt-3">
                <div class="col-sm-6">
                  <label class="form-label" for="jabatan_detail">Nama Posisi/Jabatan</label>
                  <div class="input-group">
                    <select id="jabatan_detail" name="nama_jabatan" class="form-select select2" data-allow-clear="true" tabindex="-1">
                      <option value="">- Pilih data -</option>
                      @foreach($quotation->detail as $detail)
                        <option value="{{$detail->id}}">{{$detail->name}}</option>  
                      @endforeach  
                    </select>
                  </div>
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="jumlah_hc">Jumlah Headcount</label>
                  <div class="input-group">
                    <input type="number" class="form-control minimal" id="jumlah_hc">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12 d-flex justify-content-center">
                  <button type="button" data-qbutuhid="" id="btn-tambah-detail" class="btn btn-info btn-back w-20">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Data</span>
                    <i class="mdi mdi-plus"></i>
                  </button>
                </div>
              </div>
              <div class="row mt-5">
                <div class="">
                  <table id="table-data" class="dt-column-search table w-100 table-hover">
                      <thead>
                          <tr>
                              <th class="text-center">ID</th>
                              @if($quotation->jumlah_site=="Multi Site")
                              <th class="text-center">Site</th>
                              @endif
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
let table = $('#table-data').DataTable({
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
              d.quotation_id = {{$quotation->id}};
          },
      },   
      "order":[
          [1,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },
      @if($quotation->jumlah_site=="Multi Site")
      {
          data : 'nama_site',
          name : 'nama_site',
          className:'text-center'
      },
      @endif
      {
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

    $(document).ready(function() {
    $('#jabatan_detail').select2();
  });

    $('#btn-tambah-detail').on('click',function () {
      let jabatanDetailId = $('#jabatan_detail').val();
      let jumlahHc = $('#jumlah_hc').val();
      let site = $('#site').val();
      
      let msg="";
      if(site ==""){
        msg += "Site Belum Dipilih <br />";
      }

      if(jabatanDetailId ==""){
        msg += "Nama Posisi / Jabatan Belum Diisi <br />";
      }
      if(jumlahHc ==""){
        msg += "Jumlah HC Belum Diisi <br />";
      }

      if(msg!=""){
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning",
        });
      }else{
        let formData = {
          "position_id":jabatanDetailId,
          "jumlah_hc":jumlahHc,
          "site_id":site,
          "quotation_id":{{$quotation->id}},
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.add-detail-hc')}}",
          data:formData,
          success: function(response){
            if(response=="Data Berhasil Ditambahkan"){
              $('#table-data').DataTable().ajax.reload();
              $('#jabatan_detail').val("");
              $('#jumlah_hc').val("");
            }else{
              Swal.fire({
                title: "Pemberitahuan",
                html: response,
                icon: "warning",
              });
            }
          },
          error:function(error){
            console.log(error);
          }
        });
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
      if(table.page.info().recordsTotal==0){
        msg += "Isikan minimal 1 data";
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


  $('body').on('click', '.btn-delete', function() {
    let formData = {
      "id":$(this).data('id'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data';
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