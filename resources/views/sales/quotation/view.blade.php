@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y"> 
  <div class="col-12 col-lg-12">
    <div class="card mb-2">
      <div class="card-header pb-1">
        <h5 class="card-title m-0">Quotation Information</h5>
      </div>
      <div class="card-body pt-1">
        <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
          <div class="d-flex flex-column justify-content-center">
            <h5 class="mb-1 mt-3">
              {{$data->nomor}} 
              <!-- <span class="badge bg-label-success me-2 ms-2 rounded-pill">Paid</span> -->
               @if($data->quotation_status !="" && $data->quotation_status !=null )
               <span class="badge bg-label-warning rounded-pill">{{$data->quotation_status}}</span>
               @endif
               @if($data->success_status !="" && $data->success_status !=null )
               <span class="badge bg-label-success rounded-pill">{{$data->success_status}}</span>
               @endif
               @if($data->info_status !="" && $data->info_status !=null )
               <span class="badge bg-label-success rounded-pill">{{$data->success_status}}</span>
               @endif
            </h5>
            <p class="text-body">{{$master->nama_perusahaan}}</p>
          </div>
          <div class="d-flex align-content-center flex-wrap gap-2">
            <button class="btn btn-warning" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-file-refresh"></i>&nbsp; Ajukan Ulang ( Ubah )</button>
            @if(in_array(Auth::user()->role_id,[2,31,32,33,50,51,52,97,98,99,100]))
            <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
            @endif
            <button id="cetak-quotation" class="btn btn-info" @if($data->is_aktif==0) disabled @endif><i class="mdi mdi-printer"></i>&nbsp; Print</button>
            <button id="delete-quotation" class="btn btn-danger" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-trash-can"></i>&nbsp;  Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Details Table -->

  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Quotation Info</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Jumlah Site</label>
              <input type="text" value="{{$master->jumlah_site}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Jenis Kontrak</label>
              <input type="text" value="{{$master->jenis_kontrak}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Mulai Kontrak</label>
              <input type="text" value="{{$master->smulai_kontrak}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Kontrak Selesai</label>
              <input type="text" value="{{$master->skontrak_selesai}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Tanggal Penempatan</label>
              <input type="text" value="{{$master->stgl_penempatan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Salary Rule</label>
              <input type="text" value="{{$master->salary_rule}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Dibuat Oleh</label>
              <input type="text" value="{{$master->created_by}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Dibuat Tanggal</label>
              <input type="text" value="{{$master->screated_at}}" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Service Info</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Kebutuhan</label>
              <input type="text" value="{{$data->kebutuhan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Entitas</label>
              <input type="text" value="{{$data->company}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Provinsi</label>
              <input type="text" value="{{$data->provinsi}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Kabupaten / Kota</label>
              <input type="text" value="{{$data->kota}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Upah</label>
              <input type="text" value="{{$data->upah}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Nominal Upah</label>
              <input type="text" value="{{$data->custom_upah}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Manajemen Fee</label>
              <input type="text" value="{{$data->manajemen_fee}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Persentase</label>
              <input type="text" value="{{$data->persentase}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">Jenis Perusahaan</label>
              <input type="text" value="{{$data->jenis_perusahaan}}" class="form-control">
            </div>
            <div class="col-sm-6">
              <label class="form-label">Resiko</label>
              <input type="text" value="{{$data->resiko}}" class="form-control">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-6">
              <label class="form-label">BPJS</label>
              <input type="text" value="{{$data->program_bpjs}}" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Quotation details</h5>
          <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
        </div>
        <div class="table-responsive overflow-hidden table-data card-datatable table-responsive">
          <table id="table-data" class="dt-column-search table w-100 table-hover datatables-quotation-details" style="text-wrap: nowrap;">
            <thead class="table-light">
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Kebutuhan</th>
                    <th class="text-center">Nama Posisi/Jabatan</th>
                    <th class="text-center">Jumlah Headcount</th>
                </tr>
            </thead>
            <tbody>
                {{-- data table ajax --}}
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end align-items-center m-3 p-1">
          <div class="order-calculations">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-heading">Total Headcount : {{$data->totalHc}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Customer details</h5>
          <h6 class="m-0"><a href="javascript:void(0)">Edit</a></h6>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-start align-items-center mb-4">
            <div class="d-flex flex-column">
              <a href="javascript:void(0)">
                <h6 class="mb-1">{{$leads->nama_perusahaan}}</h6>
              </a>
              <small>Customer ID: {{$leads->nomor}}</small>
            </div>
          </div>
          <div class="d-flex justify-content-between">
            <h6 class="mb-2">PIC info</h6>
          </div>
          <p class="mb-0">PIC : {{$leads->pic}}</p>
          <p class="mb-0">Jabatan : {{$leads->jabatan}}</p>
          <p class="mb-0">No. Telp : {{$leads->no_telp}}</p>
          <p class="mb-0">Email : {{$leads->email}}</p>
          
          <div class="mt-3 d-flex justify-content-between">
            <h6 class="mb-2">CRM / RO info</h6>
          </div>
          <p class="mb-1">CRM : {{$leads->crm}}</p>
          <p class="mb-1">RO : {{$leads->ro}}</p>
        </div>
      </div>

      <!-- <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Shipping address</h6>
          <h6 class="m-0">
            <a href=" javascript:void(0)">Edit</a>
          </h6>
        </div>
        <div class="card-body">
          <p class="mb-0">45 Roker Terrace <br />Latheronwheel <br />KW5 8NW,London <br />UK</p>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Billing address</h6>
          <h6 class="m-0">
            <a href=" javascript:void(0)">Edit</a>
          </h6>
        </div>
        <div class="card-body">
          <p class="mb-4">45 Roker Terrace <br />Latheronwheel <br />KW5 8NW,London <br />UK</p>
          <h6 class="mb-0 pb-2">Mastercard</h6>
          <p class="mb-0">Card Number: ******4291</p>
        </div>
      </div> -->
    </div>
    <div class="col-12 col-lg-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Cost Structure</h5>
        </div>
        <div class="card-body">
        <div class="card-header p-0">
          <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill" role="tablist">
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-kaporlap" aria-controls="navs-top-kaporlap" aria-selected="true">
                  Kaporlap
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-devices" aria-controls="navs-top-devices" aria-selected="false" tabindex="-1">
                  Devices
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-ohc" aria-controls="navs-top-ohc" aria-selected="false" tabindex="-1">
                  OHC
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-hpp" aria-controls="navs-top-hpp" aria-selected="false" tabindex="-1">
                  HPP
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-coss" aria-controls="navs-top-coss" aria-selected="false" tabindex="-1">
                  COSS
                </button>
              </li>
            <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span></ul>
          </div>
        </div>
        <div class="card-body">
          <div class="tab-content p-0">
            <div class="tab-pane fade active show" id="navs-top-kaporlap" role="tabpanel">
              
            </div>
            <div class="tab-pane fade" id="navs-top-devices" role="tabpanel">
              
            </div>
            <div class="tab-pane fade" id="navs-top-ohc" role="tabpanel">
              
            </div>
            <div class="tab-pane fade" id="navs-top-hpp" role="tabpanel">
              
            </div>
            <div class="tab-pane fade" id="navs-top-coss" role="tabpanel">
              
            </div>
          </div>
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
  $('#table-data').DataTable({
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
              d.quotation_kebutuhan_id = {{$data->id}};
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
      }],
      "language": datatableLang,
    });

  $('body').on('click', '#delete-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menghapus data ini ?",
      showCancelButton: true,
      confirmButtonText: "Hapus",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.delete-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil dihapus.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href="{{route('quotation')}}";
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });
  $('body').on('click', '#approve-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menyetujui data ini ?",
      showCancelButton: true,
      confirmButtonText: "Approve",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.approve-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil disetujui.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });
  $('body').on('click', '#cetak-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin mencetak dokumen ini ?",
      showCancelButton: true,
      confirmButtonText: "Cetak",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        alert("berhasil mencetak");
        location("reload");
      }
    });
  });
</script>
@endsection