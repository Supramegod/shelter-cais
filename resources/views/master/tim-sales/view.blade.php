@extends('layouts.master')
@section('title','Tim Sales')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Tim Sales</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex flex-column justify-content-center align-items-center">
            <h4>Tim Sales</h4>
          </div>
          <div class="card-body overflow-hidden">
            <input type="hidden" name="id" value="{{$data->id}}">
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Tim</label>
              <div class="col-sm-4">
                <input readonly type="text" id="nama" name="nama" value="{{$data->nama}}" class="form-control @if ($errors->any()) @if($errors->has('nama')) is-invalid @else   @endif @endif">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">{{$errors->first('nama')}}</div>
                @endif
              </div>
              <label class="col-sm-2 col-form-label text-sm-end">Branch</label>
              <div class="col-sm-4">
                <input readonly type="text" id="branch" name="branch" value="{{$data->branch}}" class="form-control @if ($errors->any()) @if($errors->has('branch')) is-invalid @else   @endif @endif">
                @if($errors->has('branch'))
                    <div class="invalid-feedback">{{$errors->first('branch')}}</div>
                @endif
              </div>
            </div>
          </div>
          <div class="d-flex flex-column justify-content-center align-items-center">
            <h4>Detail Tim Sales</h4>
          </div>
          <table class="w-100 mb-3">
            <tr>
              <td style="display:flex;justify-content:end">
                <button class="btn btn-primary btn-tambah-detail" id="btn-tambah-detail" data-bs-toggle="modal" data-bs-target="#tambahSales"><i class="mdi mdi-plus"></i>&nbsp; Tambah Sales</button>
              </td>
            </tr>
          </table>
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">User ID</th>
                        <th class="text-center">Username</th>
                        <th class="text-center">Is Leader</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- data table ajax --}}
                </tbody>
            </table>
          </div>
          <div class="d-flex flex-column justify-content-center align-items-center mt-5">
            <button id="btn-kembali" class="btn btn-secondary waves-effect waves-light">
              <span class="me-1">Kembali</span>
              <i class="mdi mdi-arrow-left scaleX-n1-rtl"></i>
            </button>
          </div>
          <div class="pt-4">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="tambahSales" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">Tambah Sales</h4>
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
              <input disabled type="text" id="nama_tim" class="form-control" value="{{$data->nama}}" />
              <label for="nama_pic">Nama Tim</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <div class="input-group">
                <select id="user_id" class="form-select">
                  <option value="">- Pilih Sales -</option>
                  @foreach($listUser as $user)
                    <option value="{{$user->id}}">{{$user->full_name}}</option> 
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
        <button type="button" id="btn-tambah-sales" class="btn btn-primary">Tambah Sales</button>
      </div>
    </div>
  </div>
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  @if(session()->has('success'))  
    Swal.fire({
      title: 'Pemberitahuan',
      html: '{{session()->get('success')}}',
      icon: 'success',
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  @endif

  $('#btn-update').on('click',function () {
    $('form').submit();
  });
  
  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('tim-sales')}}");
  });

  $('#btn-tambah-sales').on('click',function(){
    let msg="";
    let sales_id = $("#user_id option:selected").val();

    if(sales_id==null || sales_id==""){
      msg += "<b>Sales</b> belum dipilih </br>";
    };

    if(msg!=""){
      Swal.fire({
            title: "Pemberitahuan",
            html: msg,
            icon: "warning",
          });
      $("#user_id").val("").change();
      $('#tambahSales').modal('toggle');
      return null;
    };

    let formData = {
      "tim_sales_id":{{$data->id}},
      "user_id": sales_id,
      "_token": "{{ csrf_token() }}"
    };

    $.ajax({
      type: "POST",
      url: "{{route('tim-sales.add-detail-sales')}}",
      data:formData,
      success: function(response){
        if(response=="Data Berhasil Ditambahkan"){
          let table ='#table-data';
          $(table).DataTable().ajax.reload();
          $('#tambahSales').modal('toggle');
        }else{
          Swal.fire({
            title: "Pemberitahuan",
            html: response,
            icon: "warning",
          });
          $("#user_id").val("").change();
        }
      },
      error:function(error){
        console.log(error);
        $("#user_id").val("").change();
      }
    });
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
        url: "{{ route('tim-sales.list-detail-sales') }}",
        data: function (d) {
            d.tim_sales_id = {{$data->id}};
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
    },{
        data : 'nama',
        name : 'nama',
        className:'dt-body-center',
    },{
        data : 'user_id',
        name : 'user_id',
        className:'dt-body-center',
    },{
        data : 'username',
        name : 'username',
        className:'dt-body-center',
    },{
        data : 'is_leader',
        name : 'is_leader',
        className:'dt-body-center',
        width: "10%",
        orderable: false,
        searchable: false,
    },{
        data : 'aksi',
        name : 'aksi',
        width: "10%",
        orderable: false,
        searchable: false,
    }],
    "language": datatableLang,
  });

  
  $('body').on('change', '.set-is-leader', function() {
      if ($(this).is(':checked')) {
        let formData = {
          "id":$(this).data('id'),
          "tim_sales_id":{{$data->id}},
          "_token": "{{ csrf_token() }}"
        };
        $.ajax({
          type: "POST",
          url: "{{route('tim-sales.change-is-leader')}}",
          data:formData,
          success: function(response){
            if(response=="Data Berhasil Ditambahkan"){
              let table ='#table-data';
              $(table).DataTable().ajax.reload();
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
      };
  });

  $('body').on('click', '.btn-delete', function() {
    let formData = {
      "id":$(this).data('id'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data';
    $.ajax({
      type: "POST",
      url: "{{route('tim-sales.delete-detail-sales')}}",
      data:formData,
      success: function(response){
        $(table).DataTable().ajax.reload();
      },
      error:function(error){
        console.log(error);
      }
    });
  });

  $('#btn-kembali').on('click',function () {
    window.location.replace("{{route('tim-sales')}}");
  });

</script>

@endsection