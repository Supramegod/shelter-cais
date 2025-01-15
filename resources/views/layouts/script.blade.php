<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
    <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('vendor/libs/bs-stepper/bs-stepper.js') }}"></script>

    <script src="{{ asset('vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.all.min.js"></script>
    <!-- <script src="{{ asset('vendor/libs/sweetalert2/sweetalert2.js') }}"></script> -->
    <!-- <script src="{{ asset('vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script> -->
    <!-- <script src="{{ asset('vendor/libs/select2/select2.js') }}"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
    <!-- Flat Picker -->
    <script src="{{ asset('vendor/libs/moment/moment.js')}}"></script>
    <script src="{{ asset('vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <!-- Main JS -->
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

    <!-- <script src="{{ asset('js/form-wizard-numbered.js') }}"></script> -->
    <!-- <script src="{{ asset('js/form-wizard-validation.js') }}"></script> -->
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
    @yield('pageScript')
