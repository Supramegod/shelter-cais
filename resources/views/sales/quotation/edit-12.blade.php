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
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-12')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">PERJANJIAN</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                <h6>Site : {{$quotation->nama_site}} - {{$quotation->kebutuhan}}</h6>
              </div>
              <div class="row mt-5">
                <div class="table-responsive overflow-hidden table-data">
                  <table id="table-data" class="dt-column-search table table-hover" style="padding-right:0px !important">
                      <thead>
                          <tr>
                              <th class="text-center">ID</th>
                              <th class="text-center">Nomor</th>
                              <th class="text-center">Perjanjian</th>
                              <th class="text-center">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                          {{-- data table ajax --}}
                      </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-12 d-flex justify-content-center">
                  <button type="button" class="btn btn-info btn-back w-50" id="btn-tambah-kerjasama">
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Tambah Perjanjian</span>
                    <i class="mdi mdi-plus"></i>
                  </button>
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
  $(document).ready(function(){
    $('#table-data').DataTable({
      scrollX: true,
      "bPaginate": false,
      "bFilter": false,
      "bInfo": false,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('quotation.list-quotation-kerjasama') }}",
            data: function (d) {
                d.quotation_id = {{$quotation->id}};
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
            data : 'nomor',
            name : 'nomor',
            width: "10%",
        },{
            data : 'perjanjian',
            name : 'perjanjian',
            width: "70%",
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
      });

      $('body').on('click', '.btn-delete', function() {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        let table ='#table-data';
        $.ajax({
          type: "POST",
          url: "{{route('quotation.delete-quotation-kerjasama')}}",
          data:formData,
          success: function(response){
            $(table).DataTable().ajax.reload();
          },
          error:function(error){
            console.log(error);
          }
        });
      });
      
      $('#btn-tambah-kerjasama').on('click',function () {
        Swal.fire({
          title: "Masukkan Perjanjian",
          input: "textarea",
          inputAttributes: {
            autocapitalize: "off"
          },
          showCancelButton: true,
          confirmButtonText: "Simpan",
          showLoaderOnConfirm: true,
          preConfirm: async (value) => {
            let formData = {
              "quotation_id":{{$quotation->id}},
              "perjanjian":value,
              "_token": "{{ csrf_token() }}"
            };

            $.ajax({
              type: "POST",
              url: "{{route('quotation.add-quotation-kerjasama')}}",
              data:formData,
              success: function(response){
                  $('#table-data').DataTable().ajax.reload();
              },
              error:function(error){
                console.log(error);
              }
            });
          },
        }).then((result) => {
          $(table).DataTable().ajax.reload();
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
  
  });
</script>
@endsection