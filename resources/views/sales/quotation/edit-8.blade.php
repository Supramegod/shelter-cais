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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-8')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">OVER HEAD COST ( OHC )</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                <h6>Site : {{$quotation->nama_site}} - {{$quotation->kebutuhan}}</h6>
              </div>
              <div class="row mt-1">
                <div class="row mb-3" style="display: flex;justify-content: center;">
                  <div class="col-sm-1">
                  <label class="form-label">&nbsp;</label>
                    <button type="button" id="btn-tambah-item" class="btn btn-warning btn-back w-20 waves-effect waves-light"  data-bs-toggle="modal" data-bs-target="#basicModal" style="margin-right:10px">
                      <i class="mdi mdi-plus"></i>
                    </button>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label" for="barang">Nama Item</label>
                    <div class="input-group">
                      <select id="barang" name="barang" class="form-select" data-allow-clear="true" tabindex="-1">
                        <option value="">- Pilih data -</option>
                        @foreach($listJenis as $jenis)
                          <optgroup label="{{$jenis->nama}}">
                          @foreach($listOhc as $ohc)
                            @if($ohc->jenis_barang_id == $jenis->id)
                            <option value="{{$ohc->id}}" data-harga="{{$ohc->harga}}">{{$ohc->nama}}</option>  
                            @endif
                          @endforeach  
                        @endforeach
                        
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label" for="harga">Harga</label>
                    <div class="input-group">
                      <input type="text" class="form-control mask-nominal text-end" id="harga">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <label class="form-label" for="jumlah">Jumlah</label>
                    <div class="input-group">
                      <input type="number" class="form-control minimal" id="jumlah">
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

<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">Tambah Item</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="text" id="nama-barang" class="form-control" placeholder="Masukkan Nama" />
              <label for="nama-barang">Nama Item</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <div class="input-group">
                <select id="jenis_barang" class="form-select">
                  <option value="">- Pilih Jenis -</option>
                  @foreach($listJenis as $jenis)
                    <option value="{{$jenis->id}}">{{$jenis->nama}}</option> 
                  @endforeach 
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" id="btn-save-tambah-item" class="btn btn-primary">Tambah Item</button>
      </div>
    </div>
  </div>
</div>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#btn-save-tambah-item').on('click',function(){
      let msg="";
      let barang = $('#nama-barang').val();
      let jenis = $("#jenis_barang option:selected").val();

      if(barang==null || barang==""){
        msg += "<b>Barang</b> belum diisi </br>";
      };

      if(jenis==null || jenis==""){
        msg += "<b>Jenis</b> belum dipilih </br>";
      };

      if(msg!=""){
        Swal.fire({
              title: "Pemberitahuan",
              html: msg,
              icon: "warning",
            });
        $('#nama-barang').val("");
        $("#jenis_barang").val("").change();
        $('#basicModal').modal('toggle');
        return null;
      };

      let formData = {
        "barang":barang,
        "jenis":jenis,
        "_token": "{{ csrf_token() }}"
      };

      $.ajax({
        type: "POST",
        url: "{{route('quotation.add-barang')}}",
        data:formData,
        success: function(response){
          if(response=="Data Berhasil Ditambahkan"){
            location.reload();
          }else{
            Swal.fire({
              title: "Pemberitahuan",
              html: response,
              icon: "warning",
            });
            $('#nama-barang').val("");
            $("#jenis_barang").val("").change();
          }
        },
        error:function(error){
          console.log(error);
          $('#nama-barang').val("");
          $("#jenis_barang").val("").change();
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
          url: "{{ route('quotation.list-ohc') }}",
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
          data : 'jumlah',
          name : 'jumlah',
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
      let harga = $('#harga').val();
      let jumlah = $('#jumlah').val();

      let msg="";
      if(barang ==""){
        msg += "Barang Belum Diisi <br />";
      }
      if(harga ==""){
        msg += "Harga masih kosong <br />";
      }
      if(jumlah ==""){
        msg += "Jumlah masih kosong <br />";
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
          "harga":harga,
          "jumlah":jumlah,
          "quotation_id":{{$quotation->id}},
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.add-detail-ohc')}}",
          data:formData,
          success: function(response){
            if(response=="Data Berhasil Ditambahkan"){
              $('#table-data').DataTable().ajax.reload();
              $('#barang').val("").change();
              $('#harga').val("")
              $('#jumlah').val("")
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
      "quotation_id":$(this).data('quotation'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data';
    $.ajax({
      type: "POST",
      url: "{{route('quotation.delete-detail-ohc')}}",
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
  });


  let extra = 0;
  $('.mask-nominal').on("keyup", function(event) {    
    // When user select text in the document, also abort.
    var selection = window.getSelection().toString();
    if (selection !== '') {
      return;
    }

    // When the arrow keys are pressed, abort.
    if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
      if (event.keyCode == 38) {
        extra = 1000;
      } else if (event.keyCode == 40) {
        extra = -1000;
      } else {
        return;
      }

    }

    var $this = $(this);
    // Get the value.
    var input = $this.val();
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt(input, 10) : 0;
    input += extra;
    extra = 0;
    $this.val(function() {
      return (input === 0) ? "" : input.toLocaleString("id-ID");
    });
  });

  $('#barang').on('change', function() {    
    if($('#barang option:selected').val() !=""){
      $('#harga').val($('#barang option:selected').data("harga"));
    }
  });

</script>
@endsection