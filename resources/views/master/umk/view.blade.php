@extends('layouts.master')
@section('title','UMK')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master/ </span> Lihat UMK</h4>
  <!-- Multi Column with Form Separator -->
  <div class="row">
    <!-- Form Label Alignment -->
    <div class="col-md-12">
      <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <h4>Detail UMK</h4>
                <h6 class="mt-0">Kota : {{$data->city_name}}</h6>
                <input type="text" id="city_id" value="{{$data->city_id}}" hidden>
                <input type="text" id="city_name" value="{{$data->city_name}}" hidden>
                <input type="text" id="id" value="{{$data->id}}" hidden>
            </div>
        </div>
        <div class="card-body">
            <table class="w-100 mb-3">
                <tr>
                  <td style="display:flex;justify-content:end">
                    <button class="btn btn-primary btn-update" id="btn-update" data-bs-toggle="modal" data-bs-target="#updateData"><i class="mdi mdi-plus"></i>&nbsp; Update Data</button>
                  </td>
                </tr>
            </table>
            <div class="table-responsive overflow-hidden table-data">
                <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Kota</th>
                            <th class="text-center">UMK</th>
                            <th class="text-center">Tanggal Berlaku</th>
                            <th class="text-center">Sumber</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Dibuat Tanggal</th>
                            <th class="text-center">Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
            </div>
          </div>
        <div class="pt-4">
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="updateData" tabindex="-1" aria-labelledby="updateDataLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateDataLabel">Update Data UMK</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">UMK <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="text" id="umk" name="umk" value="{{old('umk')}}" class="form-control @if ($errors->any()) @if($errors->has('umk')) is-invalid @else   @endif @endif">
                    @if($errors->has('umk'))
                        <div class="invalid-feedback">{{$errors->first('umk')}}</div>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Tgl Berlaku <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="date" id="tgl_berlaku" name="tgl_berlaku" value="{{old('tgl_berlaku')}}" class="form-control @if ($errors->any()) @if($errors->has('tgl_berlaku')) is-invalid @else   @endif @endif">
                    @if($errors->has('tgl_berlaku'))
                        <div class="invalid-feedback">{{$errors->first('tgl_berlaku')}}</div>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end">Sumber <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <textarea class="form-control h-px-100 @if ($errors->any())   @endif" name="sumber" id="sumber" placeholder="">{{old('sumber')}}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" id="btn-simpan" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>
<!--/ Content -->
@endsection

@section('pageScript')
    <script>
        $('#btn-kembali').on('click',function () {
          window.location.replace("{{route('umk')}}");
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

            let dt_filter_table = $('.dt-column-search');

            var table = $('#table-data').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
                ajax: {
                    url: "{{ route('umk.list-umk') }}",
                    data: function (d) {
                      d.id = {{$data->city_id}};
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
                    data : 'city_name',
                    name : 'city_name',
                    className:'dt-body-left'
                },{
                    data : 'umk',
                    name : 'umk',
                    className:'dt-body-right',
                    render: $.fn.dataTable.render.number('.','.', 0,'')
                },{
                    data : 'tgl_berlaku',
                    name : 'tgl_berlaku',
                    className:'dt-body-center'
                },{
                    data : 'sumber',
                    name : 'sumber',
                    className:'dt-body-center'
                },{
                    data : 'is_aktif',
                    name : 'is_aktif',
                    className:'dt-body-center'
                },{
                    data : 'created_by',
                    name : 'created_by',
                    className:'text-center'
                },{
                    data : 'created_at',
                    name : 'created_at',
                    className:'text-center'
                }],
                "language": datatableLang,
                dom: 'frtip',
                buttons: [
                ],
            });

            $('#btn-simpan').on('click',function(){
                let msg="";
                let umk = $("#umk").val();
                let tgl_berlaku = $("#tgl_berlaku").val();
                let sumber = $("#sumber").val();
                let city_id = $("#city_id").val();
                let city_name = $("#city_name").val();
                let id = $("#id").val();

                if(umk==null || umk==""){
                    msg += "<b>umk</b> belum diisi </br>";
                };
                if(tgl_berlaku==null || tgl_berlaku==""){
                    msg += "<b>Tanggal Berlaku</b> belum diisi </br>";
                };
                if(sumber==null || sumber==""){
                    msg += "<b>Sumber</b> belum diisi </br>";
                };

                if(msg!=""){
                    Swal.fire({
                            title: "Pemberitahuan",
                            html: msg,
                            icon: "warning",
                        });
                    $('#updateData').modal('toggle');
                    return null;
                };

                let formData = {
                    "id":id,
                    "city_id":city_id,
                    "city_name":city_name,
                    "umk":umk,
                    "tgl_berlaku":tgl_berlaku,
                    "sumber":sumber,
                    "_token": "{{ csrf_token() }}"
                };

                $.ajax({
                    type: "POST",
                    url: "{{route('umk.save')}}",
                    data:formData,
                    success: function(response){
                        if(response=="Data Berhasil Ditambahkan"){
                            let table ='#table-data';
                            $(table).DataTable().ajax.reload();
                            $('#updateData').modal('toggle');
                        }else{
                            Swal.fire({
                                title: "Pemberitahuan",
                                html: response,
                                icon: "warning",
                            });
                        }
                    },
                    error:function(error){
                        Swal.fire({
                            title: "Pemberitahuan",
                            html: error,
                            icon: "warning",
                        });
                    }
                });
            });
    </script>
    <script>
        var elem = document.getElementById("umk");
      
        elem.addEventListener("keydown",function(event){
            var key = event.which;
            if((key<48 || key>57) && key != 8) event.preventDefault();
        });
      
        elem.addEventListener("keyup",function(event){
            var value = this.value.replace(/,/g,"");
            this.dataset.currentValue=parseInt(value);
            var caret = value.length-1;
            while((caret-3)>-1)
            {
                caret -= 3;
                value = value.split('');
                value.splice(caret+1,0,",");
                value = value.join('');
            }
            this.value = value;
        });
      </script>
@endsection