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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-10')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">OVER HEAD COST ( OHC )</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                @foreach($quotation->quotation_site as $site)
                  <h6>{{$site->nama_site}}</h6>
                @endforeach
              </div>
              <div class="row mt-1">
                <div class="content-header mb-3 text-center">
                  <h6 class="mb-0">Kunjungan Tim Operasional , CRM dan Training</h6>
                </div>
                <div class="row mb-3">
                  <div class="col-md-3">
                    Kunjungan Operasional <span class="text-danger fw-bold">*</span>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group"> 
                      <input type="number" placeholder="jumlah kunjungan" name="jumlah_kunjungan_operasional" value="@if($quotation->kunjungan_operasional!=null){{explode(' ',$quotation->kunjungan_operasional)[0]}}@endif" id="jumlah_kunjungan_operasional" class="form-control minimal">
                      <span class="input-group-text" id="basic-addon41">Kali Dalam 1</span>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <select id="bulan_tahun_kunjungan_operasional" name="bulan_tahun_kunjungan_operasional" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                      <option value="" @if($quotation->kunjungan_operasional=='') selected @endif>- Pilih Data -</option>  
                      <option value="Bulan" @if($quotation->kunjungan_operasional!=null)@if(explode(' ',$quotation->kunjungan_operasional)[1]=='Bulan') selected @endif @endif>Bulan</option>
                      <option value="Tahun" @if($quotation->kunjungan_operasional!=null)@if(explode(' ',$quotation->kunjungan_operasional)[1]=='Tahun') selected @endif @endif>Tahun</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input type="text" placeholder="keterangan" name="keterangan_kunjungan_operasional" value="{{$quotation->keterangan_kunjungan_operasional}}" id="keterangan_kunjungan_operasional" class="form-control w-100">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-3">
                    Kunjungan Tim CRM <span class="text-danger fw-bold">*</span>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group"> 
                      <input type="number" placeholder="jumlah kunjungan" name="jumlah_kunjungan_tim_crm" value="@if($quotation->kunjungan_tim_crm!=null){{explode(' ',$quotation->kunjungan_tim_crm)[0]}}@endif" id="jumlah_kunjungan_tim_crm" class="form-control minimal">
                      <span class="input-group-text" id="basic-addon41">Kali Dalam 1</span>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <select id="bulan_tahun_kunjungan_tim_crm" name="bulan_tahun_kunjungan_tim_crm" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                      <option value="" @if($quotation->kunjungan_tim_crm=='') selected @endif>- Pilih Data -</option>  
                      <option value="Bulan" @if($quotation->kunjungan_tim_crm!=null)@if(explode(' ',$quotation->kunjungan_tim_crm)[1]=='Bulan') selected @endif @endif>Bulan</option>
                      <option value="Tahun" @if($quotation->kunjungan_tim_crm!=null)@if(explode(' ',$quotation->kunjungan_tim_crm)[1]=='Tahun') selected @endif @endif>Tahun</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input type="text" placeholder="keterangan" name="keterangan_kunjungan_tim_crm" value="{{$quotation->keterangan_kunjungan_tim_crm}}" id="keterangan_kunjungan_tim_crm" class="form-control w-100">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-3">
                    Training <span class="text-danger fw-bold">*</span>
                  </div>
                  <div class="col-md-2">
                    <select id="ada_training" name="ada_training" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                      <option value="" @if($quotation->training=='' || $quotation->training==null) selected @endif>- Pilih Data -</option>  
                      <option value="Ada" @if($quotation->training!='' && $quotation->training!=null && $quotation->training!='0') selected @endif>Ada</option>
                      <option value="Tidak Ada" @if($quotation->training=='0') selected @endif>Tidak Ada</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group d-training" id="d-training"> 
                      <input type="number" min="0" max="100" name="training" placeholder="input jumlah" value="{{$quotation->training}}" class="form-control minimal" id="training">
                      <span class="input-group-text" id="basic-addon41">Kali Dalam 1 Tahun</span>
                    </div>
                  </div>
                  <div class="col-md-3 d-training">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#basicModalTraining">
                      Isi Training
                    </button>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-3">
                    List Training
                  </div>
                  <div class="col-md-9" id="data-list-training">
                    @foreach($listTrainingQ as $training) {{$training->nama}} @if(!$loop->last), @endif @endforeach
                  </div>
                </div>
                <hr class="my-4 mx-4">
                <div class="content-header mb-3 text-center">
                  <h6 class="mb-0">Over Head Cost</h6>
                </div>
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

<!-- Modal -->
<div class="modal fade" id="basicModalTraining" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">List Training</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="quotation_id" value="{{$quotation->id}}" >
        <div class="table-responsive">
          <table class="table table-stripped table-hover">
            <thead>
              <tr>
                <td>No.</td>
                <td>Jenis</td>
                <td>Nama Training</td>
                <td>Harga</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              @foreach($listTraining as $tr)
              <tr>
                <td>{{$tr->id}}</td>
                <td>{{$tr->jenis}}</td>
                <td>{{$tr->nama}}</td>
                <td style="text-align:right" class="">{{number_format($tr->harga,2,",",".")}}</td>
                <td>
                  <input class="form-check-input training-pilihan" type="checkbox" value="{{$tr->id}}" name="trainingList[]" @foreach($listTrainingQ as $trq) @if($trq->training_id==$tr->id) checked @endif @endforeach>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" id="btn-simpan-training" class="btn btn-primary">Simpan Training</button>
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
      let msg = "";
      let obj = $("form").serializeObject();
      console.log(obj);

    if(obj.jumlah_kunjungan_operasional==null || obj.jumlah_kunjungan_operasional==""){
        msg += "<b>Jumlah Kunjungan Operasional</b> belum diisi </br>";
      }
    if(obj.bulan_tahun_kunjungan_operasional==null || obj.bulan_tahun_kunjungan_operasional==""){
      msg += "<b>Bulan / Tahun Kunjungan Operasional</b> belum diisi </br>";
    }
    if(obj.jumlah_kunjungan_tim_crm==null || obj.jumlah_kunjungan_tim_crm==""){
      msg += "<b>Jumlah Kunjungan Tim CRM</b> belum diisi </br>";
    }
    if(obj.bulan_tahun_kunjungan_tim_crm==null || obj.bulan_tahun_kunjungan_tim_crm==""){
      msg += "<b>Bulan / Tahun Kunjungan Tim CRM</b> belum diisi </br>";
    }
    if(obj.ada_training==null || obj.ada_training==""){
      msg += "<b>Training</b> belum dipilih </br>";
    }else{
      if(obj.ada_training=="Ada"){
        if(obj.training==null || obj.training==""){
          msg += "<b>Durasi Training</b> belum dipilih </br>";
        }
      }
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

    $(document).ready(function(){

  $('#btn-simpan-training').on('click',function(){
    var checkedCount = $('.training-pilihan:checked').length;
    var jumlahTraining = $('#training').val();

    if(jumlahTraining==""){
      Swal.fire({
            title: "Pemberitahuan",
            html: "Belum memasukkan jumlah training per tahun",
            icon: "warning",
          });
          $('#basicModalTraining').modal('toggle');

      return null;
    }
    
    if (jumlahTraining<checkedCount) {
      Swal.fire({
            title: "Pemberitahuan",
            html: "Training yang dipilih lebih dari jumlah training dalam 1 tahun",
            icon: "warning",
          });
          $('#basicModalTraining').modal('toggle');

      return null;
    }
    
    var checkedValues = [];
    $('.training-pilihan:checked').each(function() {
        checkedValues.push($(this).val());
    });

    if(checkedValues.length==0){
      Swal.fire({
            title: "Pemberitahuan",
            html: "Belum ada training yang dipilih",
            icon: "warning",
          });
      return null;
    };

    let formData = {
      "training_id":checkedValues.join(", "),
      "quotation_id":$('#quotation_id').val(),
      "_token": "{{ csrf_token() }}"
    };

    $.ajax({
      type: "POST",
      url: "{{route('quotation.add-quotation-training')}}",
      data:formData,
      success: function(response){
        table.ajax.reload();
        // location.reload();
        $('#data-list-training').text(response);
          $('#basicModalTraining').modal('toggle');
      },
      error:function(error){
        console.log(error);
      }
    });
  });
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

  showTraining(1);
  function showTraining(first) {
    let selected = $("#ada_training option:selected").val();
    if (selected!="Ada") {
      $('.d-training').addClass('d-none');
      $('#list-training').addClass('d-none');
      
    }else{
      $('.d-training').removeClass('d-none');
      $('#list-training').removeClass('d-none');
      if(first!=1){
        $(".d-training").val("");
      }
    }
  }
  $('#ada_training').on('change', function() {
    showTraining(2);
  });
</script>
@endsection