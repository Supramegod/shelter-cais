<!-- Modal -->
<div class="modal fade" id="normalModalDatatable" tabindex="-1" aria-labelledby="normalModalDatatable" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="normalModalDatatable"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="normal-modal-datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <!-- <script src="{{ asset('public/assets/vendor/libs/jquery/jquery.js') }}"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
    <script src="{{ asset('public/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('public/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>

    <script src="{{ asset('public/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('public/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.all.min.js"></script>
    <!-- <script src="{{ asset('public/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script> -->
    <!-- <script src="{{ asset('public/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script> -->
    <!-- <script src="{{ asset('public/assets/vendor/libs/select2/select2.js') }}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.1/imask.min.js" integrity="sha512-+3RJc0aLDkj0plGNnrqlTwCCyMmDCV1fSYqXw4m+OczX09Pas5A/U+V3pFwrSyoC1svzDy40Q9RU/85yb/7D2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Flat Picker -->
    <script src="{{ asset('public/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{ asset('public/assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <!-- Main JS -->
    <script src="{{ asset('public/assets/js/main.js') }}"></script>

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

    <!-- <script src="{{ asset('public/assets/js/form-wizard-numbered.js') }}"></script> -->
    <!-- <script src="{{ asset('public/assets/js/form-wizard-validation.js') }}"></script> -->
    <script>
        var datatableLang = {
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            "scrollX" : "100%",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        }

        const showLoading = function() {
            Swal.fire({
                title: 'Now loading',
                allowEscapeKey: false,
                allowOutsideClick: false,
                timer: 2000,
                onOpen: () => {
                    Swal.showLoading();
                }
            }).then(
                () => {},
                (dismiss) => {
                if (dismiss === 'timer') {
                    console.log('closed by timer!!!!');
                    Swal.fire({ 
                    title: 'Finished!',
                    type: 'success',
                    timer: 2000,
                    showConfirmButton: false
                    })
                }
                }
            )
        };
        // $(document).ready(function(){
        //     $('.form-control').keyup(function(){
        //         $(this).removeClass('is-invalid');
        //         let errname = "#"+$(this).attr('name')+"-add-error";
        //         $(errname).html("");
        //     });
        //     $('.form-select').on('change',function(){
        //         $(this).removeClass('is-invalid');
        //         let errname = "#"+$(this).attr('name')+"-add-error";
        //         $(errname).html("");
        //     });
        // });
        
        $.fn.serializeObject = function() {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };

        $('.minimal').on('input', function() {
            var value = $(this).val();
            if (value === "" || value <= 0) {
                // Jika kosong, biarkan tetap kosong
                $(this).val("");
            }
        });
    </script>
    <!-- Custom script -->

    <script>  
  function openNormalDataTableModal(url, title) {
    $('#normalModalDatatable .modal-title').text(title);
    $('#normalModalDatatable').modal('show');
    $('#normal-modal-datatable tbody').html('<tr><td colspan="100%" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
    $.ajax({
      url: url,
      success: function(data) {
        $('#normal-modal-datatable tbody').html('');
        if (data.data.length === 0) {
            $('#normal-modal-datatable tbody').html('<tr><td colspan="100%" class="text-center">Data tidak tersedia</td></tr>');
            return;
        }
        let columns = Object.keys(data.data[0]).map(key => ({ title: key, data: key }));
        let thead = $('#normal-modal-datatable thead tr');
        thead.empty();
        columns.forEach(column => {
          thead.append(`<th>${column.title}</th>`);
        });

        let table = $('#normal-modal-datatable').DataTable({
          destroy: true,
          scrollX: true,
          "iDisplayLength": 25,
          'processing': true,
          'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
          },
          data: data.data,
          columns: columns,
          "order": [
            [0, 'desc']
          ],
          "language": datatableLang,
          dom: 'frtip',
          buttons: [],
        });
      }
    });

  }
</script>

    @yield('pageScript')
