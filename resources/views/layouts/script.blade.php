<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('public/assets/vendor/libs/jquery/jquery.js') }}"></script>
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

    <!-- Flat Picker -->
    <script src="{{ asset('public/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{ asset('public/assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <!-- Main JS -->
    <script src="{{ asset('public/assets/js/main.js') }}"></script>

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
        
    </script>
    <!-- Custom script -->
    @yield('pageScript')
