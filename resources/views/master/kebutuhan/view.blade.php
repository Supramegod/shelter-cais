@extends('layouts.master')
@section('title','Kebutuhan')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat Kebutuhan</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <h4>Detail Kebutuhan</h4>
                <p class="mt-0">Kebutuhan : {{$kebutuhan->nama}}</p>
                <input type="text" id="kebutuhan_id" value="{{$kebutuhan->id}}" hidden>
            </div>
            <table class="w-100 mb-3">
                <tr>
                  <td style="display:flex;justify-content:end">
                    <button class="btn btn-primary btn-tambah-detail" id="btn-tambah-detail"><i class="mdi mdi-plus"></i>&nbsp; Tambah Detail</button>
                  </td>
                </tr>
            </table>
            <div class="nav-align-top">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    @foreach($detailKebutuhan as $kkd => $detail)
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link waves-effect @if($loop->first) active @endif" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-{{$detail->id}}" aria-controls="navs-top-{{$detail->id}}" aria-selected="true">
                            {{$detail->nama}}
                            </button>
                        </li>
                    @endforeach
                    <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">
                @foreach($detailKebutuhan as $kkd => $detail)
                <div class="tab-pane fade @if($loop->first) active show @endif" id="navs-top-{{$detail->id}}" role="tabpanel">
                  <div class="mb-3">
                    <table class="w-100 mb-3">
                      <tr>
                        <td style="display:flex;justify-content:space-between">
                          <h4>Tunjangan</h4>
                          <button class="btn btn-secondary btn-input-tunjangan" id="btn-input-tunjangan-{{$detail->id}}" data-id="{{$detail->id}}"><i class="mdi mdi-plus"></i>&nbsp; Tambah Tunjangan</button>
                        </td>
                      </tr>
                    </table>
                    <div class="table-responsive overflow-hidden table-data-tunjangan-{{$detail->id}}">
                      <table id="table-data-tunjangan-{{$detail->id}}" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                          <thead>
                              <tr>
                                  <th class="text-center">ID</th>
                                  <th class="text-center">Nama Tunjangan</th>
                                  <th class="text-center">Nominal</th>
                                  <th class="text-center">Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              {{-- data table ajax --}}
                          </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="mt-5">
                    <table class="w-100 mb-3">
                      <tr>
                        <td style="display:flex;justify-content:space-between">
                          <h4>Requirement</h4>
                          <button class="btn btn-secondary btn-input-requirement" id="btn-input-requirement-{{$detail->id}}" data-id="{{$detail->id}}"><i class="mdi mdi-plus"></i>&nbsp; Tambah Requirement</button>
                        </td>
                      </tr>
                    </table>
                    <div class="table-responsive overflow-hidden table-data-requirement-{{$detail->id}}">
                      <table id="table-data-requirement-{{$detail->id}}" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                          <thead>
                              <tr>
                                  <th class="text-center">ID</th>
                                  <th class="text-center">Requirement</th>
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
        <div class="pt-4">
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Content -->
@endsection

@section('pageScript')
    <script>
        $('#btn-kembali').on('click',function () {
          window.location.replace("{{route('kebutuhan')}}");
        });

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
        @if(session()->has('error'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{session()->has('error')}}',
                icon: 'warning',
                customClass: {
                confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif

    @foreach($detailKebutuhan as $kkd => $detail)
      let tableTunjangan{{$detail->id}} = $('#table-data-tunjangan-{{$detail->id}}').DataTable({
        scrollX: true,
        "bPaginate": false,
      "bLengthChange": false,
      "sScrollXInner": "100%",
      "bFilter": false,
      "bInfo": false,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('kebutuhan.list-detail-tunjangan') }}",
            data: function (d) {
                d.kebutuhan_detail_id = {{$detail->id}};
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
            data : 'nama',
            name : 'nama',
            className:'text-center',
        },{
            data : 'nominal',
            name : 'nominal',
            className:'text-center',
            render: $.fn.dataTable.render.number('.','.', 0,'')
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
      });

      $('#btn-input-tunjangan-{{$detail->id}}').on('click', function() {
        Swal.fire({
            title: 'Tambah Tunjangan',
            html: '<input type="text" id="nama" class="form-control" placeholder="Masukkan Nama Tunjangan"><br></input><input type="text" id="nominal" name="nominal" class="form-control" placeholder="Masukkan nominal"></input>',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
                const nama = $('#nama').val();
                const nominal = $('#nominal').val();
                if (!nama) {
                    Swal.showValidationMessage('Nama Tunjangan Harus Diisi !');
                }
                if (!nominal) {
                    Swal.showValidationMessage('Nominal Harus Diisi !');
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: "{{route('kebutuhan.add-detail-tunjangan')}}",
                data: { 
                  "_token": "{{ csrf_token() }}",
                  nama: nama.value,
                  nominal: nominal.value,
                  kebutuhan_detail_id:{{$detail->id}}
                },
                success: function(response){
                  if(response=="Data Berhasil Ditambahkan"){
                    $('#table-data-tunjangan-{{$detail->id}}').DataTable().ajax.reload();
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
      });
    @endforeach

    @foreach($detailKebutuhan as $kkd => $detail)
      let tableRequirement{{$detail->id}} = $('#table-data-requirement-{{$detail->id}}').DataTable({
        scrollX: true,
        "bPaginate": false,
      "bLengthChange": false,
      "sScrollXInner": "100%",
      "bFilter": false,
      "bInfo": false,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('kebutuhan.list-detail-requirement') }}",
            data: function (d) {
                d.kebutuhan_detail_id = {{$detail->id}};
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
            data : 'requirement',
            name : 'requirement',
            className:'text-center',
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
      });

      $('#btn-input-requirement-{{$detail->id}}').on('click', function() {
        Swal.fire({
            title: 'requirement',
            html: '<textarea id="textareaInput" class="swal2-textarea" placeholder="Masukkan requirement" style="height: 100px;"></textarea>',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
                const text = $('#textareaInput').val();
                if (!text) {
                    Swal.showValidationMessage('Requirement Harus Diisi !');
                }
                return text;
            }
        }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: "{{route('kebutuhan.add-detail-requirement')}}",
                data: { 
                  "_token": "{{ csrf_token() }}",
                  requirement: result.value,
                  kebutuhan_detail_id:{{$detail->id}}
                },
                success: function(response){
                  if(response=="Data Berhasil Ditambahkan"){
                    $('#table-data-requirement-{{$detail->id}}').DataTable().ajax.reload();
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
      });
    @endforeach

    $('body').on('click', '.btn-delete-requirement', function() {
      let formData = {
        "id":$(this).data('id'),
        "_token": "{{ csrf_token() }}"
      };

      let table ='#table-data-requirement-'+$(this).data('detail');
      $.ajax({
        type: "POST",
        url: "{{route('kebutuhan.delete-detail-requirement')}}",
        data:formData,
        success: function(response){
          $(table).DataTable().ajax.reload();
        },
        error:function(error){
          console.log(error);
        }
      });
    });

    $('body').on('click', '.btn-delete-tunjangan', function() {
      let formData = {
        "id":$(this).data('id'),
        "_token": "{{ csrf_token() }}"
      };

      let table ='#table-data-tunjangan-'+$(this).data('detail');
      $.ajax({
        type: "POST",
        url: "{{route('kebutuhan.delete-detail-tunjangan')}}",
        data:formData,
        success: function(response){
          $(table).DataTable().ajax.reload();
        },
        error:function(error){
          console.log(error);
        }
      });
    });

    $('#btn-tambah-detail').on('click', function() {
        const kebutuhanId = $('#kebutuhan_id').val();
        console.log(kebutuhanId)
        Swal.fire({
            title: 'Tambah Detail',
            html: '<textarea id="textareaInput" class="swal2-textarea" placeholder="Masukkan nama detail kebutuhan" style="height: 100px;"></textarea>',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
                const text = $('#textareaInput').val();
                if (!text) {
                    Swal.showValidationMessage('Nama detail kebutuhan Harus Diisi !');
                }
                return text;
            }
        }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: "{{route('kebutuhan.add-detail')}}",
                data: { 
                  "_token": "{{ csrf_token() }}",
                  nama: result.value,
                  kebutuhan_id: kebutuhanId
                },
                success: function(response){
                  if(response=="Data Berhasil Ditambahkan"){
                    window.location.reload();
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
      });

    </script>
@endsection