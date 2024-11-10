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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-9')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">CHEMICAL</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                <h6>Site : {{$quotation->nama_site}} - {{$quotation->kebutuhan}}</h6>
              </div>
              <div class="row mt-1">
                <div class="row mb-3" style="display: flex;justify-content: center;">
                  <div class="col-sm-6">
                    <label class="form-label" for="barang">Nama Barang</label>
                    <div class="input-group">
                      <select id="barang" name="barang" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($listJenis as $jenis)
                          <optgroup label="{{$jenis->nama}}">
                          @foreach($listChemical as $chemical)
                            @if($chemical->jenis_barang_id == $jenis->id)
                            <option value="{{$chemical->id}}" data-harga="{{$chemical->harga}}">{{$chemical->nama}} | Harga : {{$chemical->harga}}</option>  
                            @endif
                          @endforeach  
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label" for="harga">Harga</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="harga" readonly>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label" for="jumlah">Jumlah</label>
                    <div class="input-group">
                      <input type="number" class="form-control" id="jumlah">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 d-flex justify-content-center">
                    <button type="button" id="btn-tambah-detail" class="btn btn-info btn-back w-20">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Data</span>
                      <i class="mdi mdi-plus"></i>
                    </button>
                  </div>
                </div>
                <div class="row mt-5">
                  <div class="table-responsive overflow-hidden table-data">
                    <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                        <thead>
                            <tr>
                                <th class="text-center">Jenis ID</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Harga/Unit</th>
                                <th class="text-center">Jumlah</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- data table ajax --}}
                        </tbody>
                    </table>
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
  $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });
    
  $('#btn-submit').on('click',function(e){
    e.preventDefault();
    var form = $(this).parents('form');
    form.submit();
  });

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
          url: "{{ route('quotation.list-chemical') }}",
          data: function (d) {
              d.quotation_id = {{$quotation->id}};
          },
      }, 
      rowGroup: {
          dataSrc: 'jenis_barang'
      },
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'jenis_barang_id',
          name : 'jenis_barang_id',
          className:'text-center',
          visible: false,
          searchable: false,
          orderable:false
      },{
          data : 'jenis_barang',
          name : 'jenis_barang',
          className:'text-center',
          visible: false,
          orderable:false
      },{
          data : 'nama',
          name : 'nama',
          className:'text-center',
          orderable:false
      },{
          data : 'harga',
          name : 'harga',
          className:'text-end',
          orderable:false
      },
      {
          data : 'data',
          name : 'data',
          className:'text-center',
          orderable:false
      },
      {
          data : 'aksi',
          name : 'aksi',
          width: "10%",
          orderable: false,
          searchable: false,
      }
    ],
      "language": datatableLang,
    });

    $('#btn-tambah-detail').on('click',function () {
      let barang = $('#barang').val();
      let jumlah = $('#jumlah').val();

      let msg="";
      if(barang ==""){
        msg += "Barang Belum Diisi <br />";
      }
      
      let isJumlahKeisi = false;
      if(jumlah !=null && jumlah !=""){
        isJumlahKeisi = true;
      }
      
      if(!isJumlahKeisi){
        msg += "Masukkan salah satu jumlah <br />";
      }

      if(msg!=""){
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning",
        });
      }else{
        let formData = {
          "barang":barang,
          "jumlah":jumlah,
          "quotation_id":{{$quotation->id}},
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.add-detail-chemical')}}",
          data:formData,
          success: function(response){
            if(response=="Data Berhasil Ditambahkan"){
              $('#table-data').DataTable().ajax.reload();
              $('#barang').val("").change();
              $('#jumlah').val("");
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

    $('body').on('click', '.btn-delete', function() {
    let formData = {
      "barang_id":$(this).data('barang'),
      "quotation_kebutuhan_id":$(this).data('kebutuhan'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data';
    $.ajax({
      type: "POST",
      url: "{{route('quotation.delete-detail-chemical')}}",
      data:formData,
      success: function(response){
        $(table).DataTable().ajax.reload();
      },
      error:function(error){
        console.log(error);
      }
    });
  });

  $(document).ready(function() {
    $('#barang').select2();
    
    $('#barang').on('change', function() {
                var harga = $(this).find(':selected').data('harga');
                $('#harga').val(harga);
            });
  });

</script>
@endsection