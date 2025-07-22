@extends('layouts.master')
@section('title','PKS')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="card mb-4">
        <h5 class="card-header">
          <div class="d-flex justify-content-between">
            <span class="text-center">Form PKS Baru</span>
          </div>
        </h5>
        <form class="card-body overflow-hidden" action="{{route('pks.save')}}" method="POST" enctype="multipart/form-data">        <!-- Account Details -->
          @csrf
          <div id="account-details-1" class="content active">
            <div class="content-header mb-5 text-center">
              <h4 class="mb-0">PKS</h4>
              <h4>Pilih SPK Untuk Dijadikan PKS</h4>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Leads / Customer <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" id="leads_id" name="leads_id" value="" class="form-control">
                <div class="input-group">
                    <button class="btn btn-info waves-effect" type="button" id="btn-modal-cari-leads"><span class="tf-icons mdi mdi-magnify me-1"></span>&nbsp; Cari Leads / Customer</button>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label text-sm-end">Nama Perusahaan</label>
              <div class="col-sm-4">
                <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="" class="form-control" readonly>
              </div>
              <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_pks">Tanggal PKS <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <input type="date" id="tanggal_pks" name="tanggal_pks" class="form-control" value="{{ old('tanggal_pks', date('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label text-sm-end" for="kategoriHC">Kategori Sesuai HC <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select id="kategoriHC" name="kategoriHC" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriHC as $kategori)
                            <option value="{{$kategori->id}}" {{ old('kategoriHC') == $kategori->id ? 'selected' : '' }}>{{$kategori->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="col-sm-2 col-form-label text-sm-end" for="loyalty">Loyalty <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select id="loyalty" name="loyalty" class="form-select">
                        <option value="">-- Pilih Loyalty --</option>
                        @foreach($loyalty as $item)
                            <option value="{{$item->id}}" {{ old('loyalty') == $item->id ? 'selected' : '' }}>{{$item->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="d-list-site" class="d-none">
                <hr class="my-4" />
                <div class="content-header mt-3 mb-3 text-center">
                <h4>List Site</h4>
                </div>
                <div id="d-table-spk" class="row mb-3">
                <div class="table-responsive overflow-hidden table-spk">
                    <table id="table-spk" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="check-all-sites" class="form-check-input" style="transform: scale(1.5); margin-right: 8px;" />
                            </th>
                        <th>SPK</th>
                        <th>Nama Site</th>
                        <th>Kota</th>
                        <th>Penempatan</th>
                        </tr>
                    </thead>
                    <tbody class="tbody-spk" id="tbody-spk">
                        {{-- data table ajax --}}
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div id="d-crosscheck-data" class="d-none">
                <hr class="my-4" />
                <div class="content-header mt-3 mb-3 text-center">
                <h4>Crosscheck Data</h4>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end" for="entitas">Entitas <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select id="entitas" name="entitas" class="form-select">
                            <option value="">-- Pilih Entitas --</option>
                            @foreach($companyList as $entitas)
                                <option value="{{ $entitas->id }}" {{ old('entitas') == $entitas->id ? 'selected' : '' }}>{{ $entitas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_awal_kontrak">Tanggal Awal Kontrak <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="date" id="tanggal_awal_kontrak" name="tanggal_awal_kontrak" class="form-control" value="{{ old('tanggal_awal_kontrak') }}">
                    </div>
                    <label class="col-sm-2 col-form-label text-sm-end" for="tanggal_akhir_kontrak">Tanggal Akhir Kontrak <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <input type="date" id="tanggal_akhir_kontrak" name="tanggal_akhir_kontrak" class="form-control" value="{{ old('tanggal_akhir_kontrak') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label text-sm-end" for="rule_thr">Rule THR <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select id="rule_thr" name="rule_thr" class="form-select">
                            <option value="">-- Pilih Rule THR --</option>
                            @foreach($ruleThrs as $thr)
                                <option value="{{ $thr->id }}" {{ old('rule_thr') == $thr->id ? 'selected' : '' }}>{{ $thr->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="col-sm-2 col-form-label text-sm-end" for="salary_rule">Salary Rule <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select id="salary_rule" name="salary_rule" class="form-select">
                            <option value="">-- Pilih Salary Rule --</option>
                            @foreach($salaryRules as $rule)
                                <option value="{{ $rule->id }}" {{ old('salary_rule') == $rule->id ? 'selected' : '' }}>{{ $rule->nama_salary_rule }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
              <div class="col-12 d-flex flex-row-reverse">
                <button id="btn-submit" type="submit" class="btn btn-primary btn-next w-20" style="color:white">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Buat PKS</span>
                  <i class="mdi mdi-arrow-right"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<div class="modal fade" id="modal-leads" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Daftar Leads / Customer SPK</h3>
        </div>
        <div class="row">
          <div class="table-responsive overflow-hidden table-data">
            <table id="table-data" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                <thead>
                  <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nomor</th>
                    <th class="text-center">Nama Perusahaan</th>
                    <th class="text-center">Provinsi</th>
                    <th class="text-center">Kota</th>
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
</div>
<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#btn-modal-cari-leads').on('click',function(){
    $('#modal-leads').modal('show');
  });

  let dt_filter_table = $('.dt-column-search');

  var table = $('#table-data').DataTable({
      "initComplete": function (settings, json) {
        $("#table-data").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
      },
      "bDestroy": true,
      "iDisplayLength": 25,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('pks.available-leads') }}",
          data: function (d) {

          },
      },
      "order":[
          [0,'desc']
      ],
      columns:[{
                data : 'id',
                name : 'id',
                searchable: false
            },{
                data : 'nomor',
                name : 'nomor',
                className:'text-center'
            },{
                data : 'nama_perusahaan',
                name : 'nama_perusahaan',
                className:'text-center'
            },{
                data : 'provinsi',
                name : 'provinsi',
                className:'text-center'
            },{
                data : 'kota',
                name : 'kota',
                className:'text-center'
            }],
      "language": datatableLang,
  });


  $('#table-data').on('click', 'tbody tr', function() {
      $('#modal-leads').modal('hide');
    $('#d-list-site').removeClass('d-none');
      var rdata = table.row(this).data();
      $('#leads_id').val(rdata.id);
      $('#nama_perusahaan').val(rdata.nama_perusahaan);
      $('#provinsi').val(rdata.provinsi);
      $('#kota').val(rdata.kota);
      $.ajax({
        url: '{{route("pks.get-site-available-list")}}',
        type: 'GET',
        data: { leads: rdata.id },
        success: function(data) {
        $('#tbody-spk').empty();
        $('#tbody-spk').append('');

        $.each(data, function(key, value) {
          $('#tbody-spk').append(
            '<tr>' +
              '<td>' +
            '<input type="checkbox" name="site_ids[]" value="' + value.id + '" class="form-check-input site-checkbox" style="transform: scale(1.5); margin-right: 8px;" />' +
              '</td>' +
              '<td>' + value.spk + '</td>' +
              '<td>' + value.nama_site + '</td>' +
              '<td>' + value.kota + '</td>' +
              '<td>' + value.penempatan + '</td>' +
            '</tr>'
          );
        });
        },
        error: function() {
          alert('Gagal mengambil data');
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

    if(obj.leads_id == null || obj.leads_id == "" ){
      msg += "<b>Leads / Customer</b> belum dipilih </br>";
    };

    if(obj.tanggal_pks == null || obj.tanggal_pks == ""){
      msg += "<b>Tanggal PKS</b> tidak boleh kosong </br>";
    }
    if(obj.kategoriHC == null || obj.kategoriHC == ""){
      msg += "<b>Kategori Sesuai HC</b> belum dipilih </br>";
    }
    if(obj.loyalty == null || obj.loyalty == ""){
        msg += "<b>Loyalty</b> belum dipilih </br>";
    }
    if(obj.tanggal_awal_kontrak == null || obj.tanggal_awal_kontrak == ""){
        msg += "<b>Tanggal Awal Kontrak</b> tidak boleh kosong </br>";
    }
    if(obj.tanggal_akhir_kontrak == null || obj.tanggal_akhir_kontrak == ""){
        msg += "<b>Tanggal Akhir Kontrak</b> tidak boleh kosong </br>";
    }
    if(obj.entitas == null || obj.entitas == ""){
        msg += "<b>Entitas</b> belum dipilih </br>";
    }
    if(obj.rule_thr == null || obj.rule_thr == ""){
        msg += "<b>Rule THR</b> belum dipilih </br>";
    }
    if(obj.salary_rule == null || obj.salary_rule == ""){
        msg += "<b>Salary Rule</b> belum dipilih </br>";
    }
    if (!obj['site_ids[]']) {
        msg += "Silakan pilih minimal satu site untuk membuat SPK.<br>";
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

    $('#check-all-sites').on('change', function() {
        $('.site-checkbox').prop('checked', this.checked);
    });

    $('.site-checkbox').on('change', function() {
        $('#check-all-sites').prop('checked', $('.site-checkbox:checked').length === $('.site-checkbox').length);
    });

    $('#tbody-spk').on('change', '.site-checkbox', function() {
        let checkedSites = $('.site-checkbox:checked');
        if (checkedSites.length > 0) {
            $('#d-crosscheck-data').removeClass('d-none');
        } else {
            $('#d-crosscheck-data').addClass('d-none');
        }
    });

    $('#check-all-sites').on('change', function() {
        $('.site-checkbox').prop('checked', this.checked);
        if (this.checked && $('.site-checkbox').length > 0) {
            $('#d-crosscheck-data').removeClass('d-none');
        } else {
            $('#d-crosscheck-data').addClass('d-none');
        }
    });

    $('#tbody-spk').on('change', '.site-checkbox', function() {
        let checkedSites = $('.site-checkbox:checked');
        if (checkedSites.length > 0) {
            let firstCheckedId = checkedSites.first().val();
            $.ajax({
                url: '{{ route("pks.get-detail-quotation") }}',
                type: 'GET',
                data: { id: firstCheckedId },
                success: function(response) {
                    if(response.status =="success"){
                        let data = response.data;
                        $('#tanggal_awal_kontrak').val(data.mulai_kontrak);
                        $('#tanggal_akhir_kontrak').val(data.kontrak_selesai);
                        $('#entitas').val(data.company_id).trigger('change');
                        $('#rule_thr').val(data.rule_thr_id).trigger('change');
                        $('#salary_rule').val(data.salary_rule_id).trigger('change');
                    }else{
                        Swal.fire({
                            title: "Pemberitahuan",
                            html: response.message,
                            icon: "warning"
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: "Error",
                        text: "Gagal mengambil detail quotation",
                        icon: "error"
                    });
                }
            });
        }
    });
</script>
@endsection
