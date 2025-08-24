@extends('layouts.master')
@section('title','Training Site')
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
                        <h3 class="page-title">Training Site</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">SDT</a></li>
							<li class="breadcrumb-item active" aria-current="page">Training Site</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-left">ID</th>
                                    <th class="text-left">Client</th>
                                    <th class="text-left">B. Unit</th>
                                    <th class="text-left">Area</th>
                                    <th class="text-left">Kab/Kota</th>
                                    <th class="text-left">Tanggal Gabung</th>
                                    <th class="text-left">Target/Thn</th>
                                    <th class="text-left">Jumlah Training</th>
                                    <th class="text-left">Aksi</th>
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
                "iDisplayLength": 100,
                'processing': true,
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...'
            },
                ajax: {
                    url: "{{ route('training-site.list') }}",
                    data: function (d) {
                        
                    },
                },   
                "order":[
                    [7,'desc']
                ],
                columns:[{
                    data : 'id',
                    name : 'id',
                    visible: false,
                    searchable: false
                },{
                    data : 'nama_site',
                    name : 'client',
                    className:'text-left'
                },{
                    data : 'bu',
                    name : 'laman',
                    className:'text-left'
                },{
                    data : 'branch',
                    name : 'area',
                    className:'text-left'
                },{
                    data : 'kota',
                    name : 'kab_kota',
                    className:'text-left'
                },{
                    data : 'tanggal_gabung',
                    name : 'tgl_gabung',
                    className:'text-left'
                },{
                    data : 'training_tahun',
                    name : 'target_per_tahun',
                    className:'text-left'
                },{
                    data : 'jml_training',
                    name : 'jml_training',
                    className:'text-left'
                },{
                    data : 'aksi',
                    name : 'aksi',
                    className:'text-left'
                }],
                "language": datatableLang,
                dom: '<"card-header flex-column flex-md-row px-0"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>frtip',
                buttons: [
                    {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle me-2 waves-effect waves-light',
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                        extend: 'csv',
                        text: '<i class="mdi mdi-file-document-outline me-1" ></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: {
                            columns: [1,2,3,4,5,6,7]
                        }
                        },
                        {
                        extend: 'pdf',
                        text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                        className: 'dropdown-item',
                        orientation: 'landscape',
                        customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10 
                            },
                        exportOptions: {
                            columns: [1,2,3,4,5,6,7],
                            orientation: 'landscape',
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 9; //<-- set fontsize to 16 instead of 10 
                            }
                        }
                        }
                    ]
                    }
                ],
            });
        
        $('body').on('click', '.btn-detail', function() {
            $('#modal-training').modal('show');  
            let id = $(this).data('id');
            
            // let dt_filter_table = $('.dt-column-search');
            $("#table-training").dataTable().fnDestroy();
            var table = $('#table-training').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                'loadingRecords': '&nbsp;',
                'processing': 'Loading...',
                "bDestroy": true
            },
                ajax: {
                    url: "{{ route('training-site.history') }}",
                    data: function (d) {
                        d.client_id = id;
                    },
                },   
                "order":[
                    [0,'desc']
                ],
                columns:[{
                    data : 'materi',
                    name : 'materi',
                    className:'text-left'
                },{
                    data : 'waktu_mulai',
                    name : 'waktu_mulai',
                    className:'text-center'
                },{
                    data : 'tipe',
                    name : 'tipe',
                    className:'text-center'
                },{
                    data : 'tempat',
                    name : 'tempat',
                    className:'text-center'
                },{
                    data : 'total_peserta',
                    name : 'total_peserta',
                    className:'text-center'
                },{
                    data : 'trainer',
                    name : 'trainer',
                    className:'text-left'
                },{
                    data : 'report',
                    name : 'report',
                    className:'text-left'
                }],
                "language": datatableLang
            });

            // let table2 ='#table-training';
            // $(table2).DataTable().ajax.reload();
        });


    function downloadFile(response) {
    //   alert(response);
      var blob = new Blob([response], {type: 'application/pdf'})
      var url = URL.createObjectURL(blob);
      location.assign(url);
    } 

    $('body').on('click', '.btn-report', function() {
    // $('btn-report').on('click',function(){
        let id = $(this).data('id');

        // alert(id);
        let formData = {
            "training_id":id,
            "_token": "{{ csrf_token() }}"
        };

        let table ='#table-data';
        $.ajax({
            type: "POST",
            url: "{{route('invite-pdf')}}",
            data:formData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (response, status, xhr) {

                var filename = "";                   
                var disposition = xhr.getResponseHeader('Content-Disposition');

                if (disposition) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                } 
                var linkelem = document.createElement('a');
                try {
                    var blob = new Blob([response], { type: 'application/octet-stream' });                        

                    if (typeof window.navigator.msSaveBlob !== 'undefined') {
                        //   IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                        window.navigator.msSaveBlob(blob, filename);
                    } else {
                        var URL = window.URL || window.webkitURL;
                        var downloadUrl = URL.createObjectURL(blob);

                        if (filename) { 
                            // use HTML5 a[download] attribute to specify filename
                            var a = document.createElement("a");

                            // safari doesn't support this yet
                            if (typeof a.download === 'undefined') {
                                window.location = downloadUrl;
                            } else {
                                a.href = downloadUrl;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.target = "_blank";
                                a.click();
                            }
                        } else {
                            window.location = downloadUrl;
                        }
                    }   

                } catch (ex) {
                    console.log(ex);
                } 
                },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
    });

    </script>

    <div class="modal fade" id="modal-training" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <div class="table-responsive overflow-hidden">
                        <table id="table-training" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">Materi</th>
                                    <th class="text-center">Waktu Mulai</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Tempat</th>
                                    <th class="text-center">Total Peserta</th>
                                    <th class="text-center">Trainer</th>
                                    <th class="text-center">Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table ajax --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection