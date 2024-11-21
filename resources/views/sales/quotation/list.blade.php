@extends('layouts.master')
@section('title','Quotation')
@section('pageStyle')
<style>
    .dt-buttons {width: 100%;}
</style>
@endsection
@section('content')
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Row -->
    <div class="row row-sm mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex" style="padding-bottom: 0px !important;">
                    <div class="col-md-6 text-left col-12 my-auto">
                        <h3 class="page-title">Quotation</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Sales</a></li>
							<li class="breadcrumb-item active" aria-current="page">Quotation</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('quotation')}}" method="GET">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari" value="{{$tglDari}}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai" value="{{$tglSampai}}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="company" name="company">
                                                <option value="">- Semua Entitas -</option>
                                                @foreach($company as $data)
                                                <option value="{{$data->id}}" @if($request->company==$data->id) selected @endif>{{$data->code}}  | {{$data->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="company">Entitas</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="kebutuhan" name="kebutuhan">
                                                <option value="">- Semua Kebutuhan -</option>
                                                @foreach($kebutuhan as $data)
                                                <option value="{{$data->id}}" @if($request->company==$data->id) selected @endif>{{$data->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="kebutuhan">Kebutuhan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="status" name="status">
                                                <option value="">- Semua Status -</option>
                                                @foreach($listStatus as $status)
                                                <option value="{{$status->id}}" @if($request->status==$status->id) selected @endif>{{$status->nama}}</option>
                                                @endforeach
                                            </select>
                                            <label for="status">Status Data</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-lg btn-primary waves-effect waves-light">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Leads/Customer</th>
                                    <th class="text-center">Kebutuhan</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Entitas</th>
                                    <th class="text-center">Created By</th>
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
        </div>
    </div>
    <!-- End Row -->
    <!--/ Responsive Datatable -->
</div>
<!--/ Content -->
<!-- Bootstrap Modal -->
<div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quotationModalLabel">Pilih Quotation Asal dan Tujuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="quotationAsal" class="form-label">Quotation Asal</label>
            <select id="quotationAsal" class="form-select">
              <option value="" selected>Pilih Quotation Asal</option>
              @foreach($quotationAsal as $qasal)
              <option value="{{$qasal->id}}">{{$qasal->nomor}}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="quotationTujuan" class="form-label">Quotation Tujuan</label>
            <select id="quotationTujuan" class="form-select">
              <option value="" selected>Pilih Quotation Tujuan</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="simpan-copy-quotation" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="quotationModal2" tabindex="-1" aria-labelledby="quotationModalLabel2" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quotationModalLabel2">Pilih Quotation Asal untuk Quotation : <span id="nomorQuotationTujuan"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
            <input type="hidden" id="quotationTujuan2">
          <div class="mb-3">
            <label for="quotationAsal2" class="form-label">Quotation Asal</label>
            <select id="quotationAsal2" class="form-select">
              <option value="" selected>Pilih Quotation Asal</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="simpan-copy-quotation-asal" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pageScript')
    <script>
        @if(isset($success) || session()->has('success'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$success}} {{session()->get('success')}}',
                icon: 'success',
                customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
        @if(isset($error) || session()->has('error'))  
            Swal.fire({
                title: 'Pemberitahuan',
                html: '{{$error}} {{session()->has('error')}}',
                icon: 'warning',
                customClass: {
                confirmButton: 'btn btn-warning waves-effect waves-light'
                },
                buttonsStyling: false
            });
        @endif
            let dt_filter_table = $('.dt-column-search');

            // Formatting function for row details - modify as you need
            function format(d) {
                return (
                    '<dl>' +
                    '<dt>Status Leads Saat Ini :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">trial</dd>' +
                    '</dl>'
                );
            }

            var table = $('#table-data').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('quotation.list') }}",
                    data: function (d) {
                        d.tgl_dari = $('#tgl_dari').val();
                        d.tgl_sampai = $('#tgl_sampai').val();
                        d.company = $('#company').find(":selected").val();
                        d.kebutuhan = $('#kebutuhan').find(":selected").val();
                        d.status = $('#status').find(":selected").val();
                    },
                },
                "createdRow": function( row, data, dataIndex){
                    if(data.step!=100){
                        $('td', row).css('background-color', '#f39c1240');
                        // $('td', row).css('color', '#fff');
                    }else if(data.is_aktif==0){
                        $('td', row).css('background-color', '#27ae6040');
                        // $('td', row).css('color', '#fff');
                    }
                    
                },     
                "order":[
                    [0,'desc']
                ],
                columns:[{
                    data : 'quotation_id',
                    name : 'quotation_id',
                    visible: false,
                    searchable: false
                },{
                    data : 'nomor',
                    name : 'nomor',
                    className:'text-center'
                },{
                    data : 'tgl',
                    name : 'tgl',
                    className:'text-center'
                },{
                    data : 'nama_perusahaan',
                    name : 'nama_perusahaan',
                    className:'text-center'
                },{
                    data : 'kebutuhan',
                    name : 'kebutuhan',
                    className:'text-center'
                },{
                    data : 'nama_site',
                    name : 'nama_site',
                    className:'text-center'
                },{
                    data : 'status',
                    name : 'status',
                    className:'text-center'
                },{
                    data : 'company',
                    name : 'company',
                    className:'text-center'
                },{
                    data : 'created_by',
                    name : 'created_by',
                    className:'text-center'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
                dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
                buttons: [
                    {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                        extend: 'csv',
                        text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                            // prevent avatar to be display
                            format: {
                            body: function (inner, coldex, rowdex) {
                                if (inner.length <= 0) return inner;
                                var el = $.parseHTML(inner);
                                var result = '';
                                $.each(el, function (index, item) {
                                if (item.classList !== undefined && item.classList.contains('user-name')) {
                                    result = result + item.lastChild.firstChild.textContent;
                                } else if (item.innerText === undefined) {
                                    result = result + item.textContent;
                                } else result = result + item.innerText;
                                });
                                return result;
                            }
                            }
                        }
                        },{
                        extend: 'excel',
                        text: '<i class="mdi mdi-file-document-outline" ></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                        }
                        },
                        {
                        extend: 'pdf',
                        text: '<i class="mdi mdi-file-pdf-box"></i>Pdf',
                        className: 'dropdown-item',
                        orientation: 'landscape',
                        customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10 
                            },
                        exportOptions: {
                            columns: [1,2,3, 4, 5, 6, 7,8,9,10,11],
                            orientation: 'landscape',
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10 
                            },
                            // prevent avatar to be display
                            format: {
                            body: function (inner, coldex, rowdex) {
                                if (inner.length <= 0) return inner;
                                var el = $.parseHTML(inner);
                                var result = '';
                                $.each(el, function (index, item) {
                                if (item.classList !== undefined && item.classList.contains('user-name')) {
                                    result = result + item.lastChild.firstChild.textContent;
                                } else if (item.innerText === undefined) {
                                    result = result + item.textContent;
                                } else result = result + item.innerText;
                                });
                                return result;
                            }
                            }
                        }
                        }
                    ]
                    },
                    {
                    text: '<i class="mdi mdi-content-copy mr-1"></i> <span class="d-none d-sm-inline-block">Copy Quotation</span>',
                    className: 'create-new btn btn-label-warning waves-effect waves-light',
                    action: function (e, dt, node, config)
                        {
                            $("#quotationModal").modal("show");
                        }
                    },
                    {
                    extend: 'collection',
                    className: 'btn btn-label-primary dropdown-toggle waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Quotation</span>',
                    buttons: [
                        {
                        text: '<i class="mdi mdi-file-document-outline me-1" ></i>Quotation Baru',
                        className: 'dropdown-item',
                        action: function (e, dt, node, config)
                            {
                                //This will send the page to the location specified
                                window.location.href = "{{route('quotation.add',['tipe'=>'Quotation Baru'])}}";
                            }
                        },{
                        text: '<i class="mdi mdi-file-document-outline me-1" ></i>Adendum',
                        className: 'dropdown-item',
                        action: function (e, dt, node, config)
                            {
                                //This will send the page to the location specified
                                window.location.href = "{{route('quotation.add',['tipe'=>'Adendum'])}}";
                            }
                        },{
                        text: '<i class="mdi mdi-file-document-outline me-1" ></i>Quotation Lanjutan',
                        className: 'dropdown-item',
                        action: function (e, dt, node, config)
                            {
                                //This will send the page to the location specified
                                window.location.href = "{{route('quotation.add',['tipe'=>'Quotation Lanjutan'])}}";
                            }
                        }
                    ]
                    },
                ],
            });

            let baseUrl = "{{ route('quotation.copy-quotation', ['qasal' => ':qasal', 'qtujuan' => ':qtujuan']) }}";

            function redirectToQuotationCopy(qasal, qtujuan) {
                // Ganti placeholder `:qasal` dan `:qtujuan` dengan nilai aktual
                let url = baseUrl.replace(':qasal', qasal).replace(':qtujuan', qtujuan);
                location.href = url;
            }
            
            $("#simpan-copy-quotation-asal").on('click',function() {
                let msg = "";
                let qasal = $("#quotationAsal2").val();
                let qtujuan = $("#quotationTujuan2").val();

                if(qasal==null || qasal==""){
                    msg += "<b>Quotation Asal</b> belum dipilih </br>";
                }

                if(qtujuan==null || qtujuan==""){
                    msg += "<b>Quotation Tujuan</b> belum dipilih </br>";
                }

                if(msg == ""){
                    $("#quotationModal2").modal("hide");                           

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Apakah Anda yakin ingin mengcopy Quotation '+$('#quotationAsal2 option:selected').text()+' ke Quotation '+$('#nomorQuotationTujuan').text()+'?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Copy',
                        cancelButtonText: 'Batal'
                        }).then((result) => {
                        // Jika user mengklik "Ya"
                        if (result.isConfirmed) {
                            redirectToQuotationCopy(qasal,qtujuan);
                        }
                    });
                }else{
                    $("#quotationModal2").modal("hide");                           
                    Swal.fire({
                    title: "Pemberitahuan",
                    html: msg,
                    icon: "warning"
                    });
                }
            });

            $('body').on('click', '.copy-quotation', function() {
                $("#quotationModal2").modal("show"); 
                $("#quotationTujuan2").val($(this).data('id'));
                $("#nomorQuotationTujuan").text($(this).data('nomor'));
                let quotationTujuan = $(this).data('id');
                if(quotationTujuan) {
                $.ajax({
                    url: '{{route("quotation.get-quotation-asal")}}',
                    type: 'GET',
                    data: { quotationTujuan: quotationTujuan },
                    success: function(data) {
                    $('#quotationAsal2').empty();
                    $('#quotationAsal2').append('<option value="">Pilih Quotation Asal</option>');

                    $.each(data, function(key, value) {
                        $('#quotationAsal2').append('<option value="' + value.id + '">' + value.nomor + '</option>');
                    });
                    },
                    error: function() {
                    alert('Gagal mengambil data');
                    }
                });
                } else {
                $('#quotationTujuan').empty();
                $('#quotationTujuan').append('<option value="">Pilih Quotation Tujuan</option>');
                }
            });
            
            $("#simpan-copy-quotation").on('click',function() {
                let msg = "";
                let qasal = $("#quotationAsal").val();
                let qtujuan = $("#quotationTujuan").val();

                if(qasal==null || qasal==""){
                    msg += "<b>Quotation Asal</b> belum dipilih </br>";
                }

                if(qtujuan==null || qtujuan==""){
                    msg += "<b>Quotation Tujuan</b> belum dipilih </br>";
                }

                if(msg == ""){
                    $("#quotationModal").modal("hide");                           

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Apakah Anda yakin ingin mengcopy Quotation '+$('#quotationAsal option:selected').text()+' ke Quotation '+$('#quotationTujuan option:selected').text()+'?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Copy',
                        cancelButtonText: 'Batal'
                        }).then((result) => {
                        // Jika user mengklik "Ya"
                        if (result.isConfirmed) {
                            redirectToQuotationCopy(qasal,qtujuan);
                        }
                    });
                }else{
                    $("#quotationModal").modal("hide");                           
                    Swal.fire({
                    title: "Pemberitahuan",
                    html: msg,
                    icon: "warning"
                    });
                }
            });

            $('#quotationAsal').change(function() {
                var quotationAsal = $(this).val(); 

                if(quotationAsal) {
                $.ajax({
                    url: '{{route("quotation.get-quotation-tujuan")}}',
                    type: 'GET',
                    data: { quotationAsal: quotationAsal },
                    success: function(data) {
                    $('#quotationTujuan').empty();
                    $('#quotationTujuan').append('<option value="">Pilih Quotation Tujuan</option>');

                    $.each(data, function(key, value) {
                        $('#quotationTujuan').append('<option value="' + value.id + '">' + value.nomor + '</option>');
                    });
                    },
                    error: function() {
                    alert('Gagal mengambil data');
                    }
                });
                } else {
                $('#quotationTujuan').empty();
                $('#quotationTujuan').append('<option value="">Pilih Quotation Tujuan</option>');
                }
            })
    </script>
@endsection