@extends('layouts.master')
@section('title','Whastapp')
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
                        <h3 class="page-title">Whatsapp</h3>
                        <ol class="breadcrumb" style="background-color:white !important;padding:0 !important">
							<li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
							<li class="breadcrumb-item active" aria-current="page">Whatsapp</li>
						</ol>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{route('sdt-training')}}" method="GET">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_dari" name="tgl_dari" value="{{$tglDari}}">
                                            <label for="tgl_dari">Tanggal Dari</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="input-group input-group-merge mb-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="tgl_sampai" name="tgl_sampai" value="{{$tglSampai}}">
                                            <label for="tgl_sampai">Tanggal Sampai</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
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
                                    <th class="text-center">Nomor Whatsapp</th>
                                    <th class="text-center">Pesan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Created Date</th>
                                    <th class="text-center">Send Date</th>
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
@endsection

@section('pageScript')
<script>
    @if(isset($success) || session()->has('success'))  
    @endif
    @if(isset($error) || session()->has('error'))  
        
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
                url: "{{ route('whatsapp.list') }}",
                data: function (d) {
                    // d.tgl_dari = $('#tgl_dari').val();
                    // d.tgl_sampai = $('#tgl_sampai').val();
                    // d.branch = $('#branch').find(":selected").val();
                    // d.platform = $('#platform').find(":selected").val();
                    // d.status = $('#status').find(":selected").val();
                },
            },
            "createdRow": function( row, data, dataIndex){
                $('td', row).css('background-color', data.warna_background);
                $('td', row).css('color', data.warna_font);
            },      
            "order":[
                [0,'desc']
            ],
            columns:[{
                data : 'nomor_wa',
                name : 'nomor_whatsapp',
                className:'text-center'
            },{
                data : 'message',
                name : 'message',
                className:'text-center'
            },{
                data : 'status',
                name : 'status',
                className:'text-center'
            },{
                data : 'created_date',
                name : 'created_date',
                className:'text-center'
            },{
                data : 'send_date',
                name : 'send_date',
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
            buttons: [{
                text: '<i class="mdi mdi-lan-disconnect me-sm-1"></i> <span class="d-none d-sm-inline-block">Status Sender {{$status}} </span>',
                className: 'create-new btn @if($status == "Close") btn-label-danger @else btn-label-success @endif waves-effect waves-light btn-qr',
                // action: function (e, dt, node, config)
                //     {
                //         window.location.href = '{{route("sdt-training.add")}}';
                //     }

                }
                @if($status == "Open") 
                ,{
                    text: '<i class="mdi mdi-whatsapp me-sm-1"></i> <span class="d-none d-sm-inline-block">Test Sender </span>',
                    className: 'btn btn-label-primary waves-effect waves-light btn-message',
                }]
                @else
                ]
                @endif
        });

        $('body').on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin hapus data ini ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'primary',
                cancelButtonColor: 'warning',
                confirmButtonText: 'Hapus'
            }).then(function (result) {
                console.log(result)
                if (result.isConfirmed) {
                    let formData = {
                        "id":id,
                        "_token": "{{ csrf_token() }}"
                    };

                    let table ='#table-data';
                    $.ajax({
                        type: "POST",
                        url: "{{route('sdt-training.delete')}}",
                        data:formData,
                        success: function(response){
                            console.log(response)
                            if (response.success) {
                                Swal.fire({
                                    title: 'Pemberitahuan',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1000,
                                    timerProgressBar: true,
                                    willClose: () => {
                                        $(table).DataTable().ajax.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    title: 'Pemberitahuan',
                                    text: response.message,
                                    icon: 'error'
                                })
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
                }
            });
        });

        // $('#table-data').on('click', 'tbody tr', function() {
        //     let rdata = table.row(this).data();
        //     if(rdata.can_view){
        //         window.location.href = "leads/view/"+rdata.id;
        //     }else{
        //         Swal.fire({
        //             title: 'Pemberitahuan',
        //             html: 'Anda tidak bisa melihat data ini',
        //             icon: 'warning',
        //             customClass: {
        //             confirmButton: 'btn btn-warning waves-effect waves-light'
        //             },
        //             buttonsStyling: false
        //         });
        //     }
        // })

    // Setup - add a text input to each footer cell
    // $('.dt-column-search thead tr').clone(true).appendTo('.dt-column-search thead');
    // $('.dt-column-search thead tr:eq(1) th').each(function (i) {
    //     var title = $(this).text();
    //     var $input = $('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

    //     // Add left and right border styles to the parent element
    //     $(this).css('border-left', 'none');
    //     if (i === $('.dt-column-search thead tr:eq(1) th').length - 1) {
    //         $(this).css('border-right', 'none');
    //     }

    //     $(this).html($input);

    //     $('input', this).on('keyup change', function () {
    //         if (table.column(i).search() !== this.value) {
    //             table.column(i).search(this.value).draw();
    //         }
    //     });
    // });

    $('.btn-qr').on('click',function(){
        // $('#modal-qr').modal('show');
        let id = $(this).data('id');
        let formData = {
                        "id":id,
                        "_token": "{{ csrf_token() }}"
                    };
        // let i = 1;
        // while (i < 10) {
        //     console.log(i);
        //     setTimeout(function timer() {

        //         checkStatus(function(data){
        //             // var BDate = data;
        //             // var BookingDate = Bdate;
        //             //continue your function here, inside of the callback
        //             // console.log(data.data['is_aktif'] == 0);
        //             // console.log(i);
        //             // i = 10;
        //             console.log(i);
        //             if(data.data['is_aktif'] == 0){i = 10};
        //         });

        //         // console.log(checkStatus());
        //         // if(checkStatus()){
        //         //     return;
        //         // }
        //         i++;
        //     }, i * 1000);
        // }
        
                    
        $.ajax({
            type: "POST",
            url: "{{route('whatsapp.connectQr')}}",
            data:formData,
            success: function(response){
                console.log(response)
                if (response.success) {
                    $("#picQr").attr("src", response.data);
                    $('#modal-qr').modal('show');
                    console.log(checkStatus());
                    // var cek = true;
                    // while(cek){
                    //     cek = checkStatus();
                    // }
                } else {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'error'
                    })
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

    $('.btn-message').on('click',function(){
        let id = $(this).data('id');
        let formData = {
                        "id":id,
                        "_token": "{{ csrf_token() }}"
                    };
                    
        $.ajax({
            type: "POST",
            url: "{{route('whatsapp.message')}}",
            data:formData,
            success: function(response){
                console.log(response)
                if (response.success) {
                    // $("#picQr").attr("src", response.data);
                    // $('#modal-qr').modal('show');
                    // console.log(checkStatus());
                    // var cek = true;
                    // while(cek){
                    //     cek = checkStatus();
                    // }
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Pemberitahuan',
                        text: response.message,
                        icon: 'error'
                    })
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

    function checkStatus(callBack) {
        let id = $(this).data('id');
        let formData = {
                        "id":id,
                        "_token": "{{ csrf_token() }}"
                    };
        $.ajax({
            type: "POST",
            url: "{{route('whatsapp.connectStatus')}}",
            data:formData,
            success: function(response){
                // console.log(response.data);
                // console.log(response.data['is_aktif'] == 0);
                if (response.success) {
                    // return response.data['is_aktif'] == 0;
                } 
                // console.log('aaa');
                callBack(response);
            },
            error:function(error){
                Swal.fire({
                    title: 'Pemberitahuan',
                    text: error,
                    icon: 'error'
                })
            }
        });
        // .then(function(response){
            // console.log("getRecord response: "+JSON.stringify(response));
            // console.log(response.data['is_aktif'] == 0);
        //     return response.data;
        // });

        // setTimeout(function () {
        //     // if (newState == -1) {
        //     alert('CHECK status');
            
        //     // }
        // }, 5000);
    }
</script>


<div class="modal fade" id="modal-qr" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">SCAN QR</h3>
        </div>
        <div class="row">
          <!-- <div class="table-responsive overflow-hidden table-data"> -->
            <img 
                id='picQr'
                alt="Red dot" 
            />
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection