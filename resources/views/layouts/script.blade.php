<!-- Modal -->
<div class="modal fade" id="normalModalDatatable" tabindex="-1" aria-labelledby="normalModalDatatable"
    aria-hidden="true">
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
<<<<<<< HEAD
    <!-- build:js assets/vendor/js/core.js -->
    <!-- build:js assets/assets/vendor/js/core.js -->
    <script src="{{ asset('public/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
    <script src="{{ asset('public/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('public/assets/vendor/js/menu.js') }}"></script>
=======
<!-- build:js assets/vendor/js/core.js -->
<!-- build:js assets/assets/vendor/js/core.js -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
>>>>>>> shelter-cais/developer_jalu

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>

<script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.1/dist/sweetalert2.all.min.js"></script>
<!-- <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script> -->
<!-- <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script> -->
<!-- <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.6.1/imask.min.js"
    integrity="sha512-+3RJc0aLDkj0plGNnrqlTwCCyMmDCV1fSYqXw4m+OczX09Pas5A/U+V3pFwrSyoC1svzDy40Q9RU/85yb/7D2A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Flat Picker -->
<script src="{{ asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

<script src="https://cdn.datatables.net/colreorder/1.6.2/js/dataTables.colReorder.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-resizable-columns@0.2.3/jquery.resizableColumns.min.js"></script>
<script
    src="https://cdn.jsdelivr.net/npm/jquery-resizable-columns@0.2.3/jquery.resizableColumns.dataTables.min.js"></script>
<!-- <script src="{{ asset('js/form-wizard-numbered.js') }}"></script> -->
<!-- <script src="{{ asset('js/form-wizard-validation.js') }}"></script> -->
<script>
    var datatableLang = {
        "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
        "scrollX": "100%",
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

    const showLoading = function () {
        Swal.fire({
            title: 'Now loading',
            allowEscapeKey: false,
            allowOutsideClick: false,
            timer: 2000,
            onOpen: () => {
                Swal.showLoading();
            }
        }).then(
            () => { },
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

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
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

    $('.minimal').on('input', function () {
        var value = $(this).val();
        if (value === "" || value <= 0) {
            // Jika kosong, biarkan tetap kosong
            $(this).val("");
        }
    });
</script>
<!-- Custom script -->

<script>
    let currentTable = null;

    function openNormalDataTableModal(url, title) {
        $('#normalModalDatatable .modal-title').text(title);
        $('#normalModalDatatable').modal('show');

        // Spinner sementara
        $('#normal-modal-datatable thead tr').empty();
        $('#normal-modal-datatable tbody').html(`
        <tr><td colspan="100%" class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        </td></tr>
    `);

        $.ajax({
            url: url,
            success: function (data) {
                if (currentTable) {
                    currentTable.destroy();
                    $('#normal-modal-datatable').empty();
                    $('#normal-modal-datatable').html(`
                    <thead><tr></tr></thead>
                    <tbody></tbody>
                `);
                }

                if (!data.data || data.data.length === 0) {
                    $('#normal-modal-datatable tbody').html('<tr><td colspan="100%" class="text-center">Data tidak tersedia</td></tr>');
                    return;
                }

                // Buat kolom dari data
                const columns = Object.keys(data.data[0]).map(key => ({ title: key, data: key }));
                columns.forEach(col => {
                    $('#normal-modal-datatable thead tr').append(`<th>${col.title}</th>`);
                });

                // Tunggu layout stabil (opsional)
                setTimeout(() => {
                    currentTable = $('#normal-modal-datatable').DataTable({
                        scrollX: true,
                        destroy: true,
                        iDisplayLength: 25,
                        processing: true,
                        language: {
                            loadingRecords: '&nbsp;',
                            processing: 'Loading...'
                        },
                        data: data.data,
                        columns: columns,
                        order: [[0, 'desc']],
                        language: datatableLang,
                        dom: 'frtip',
                        buttons: [],
                    });
                }, 50);
            }
        });
    }

// ! Removed following code if you do't wish to use jQuery. Remember that navbar search functionality will stop working on removal.
if (typeof $ !== 'undefined') {
  $(function () {
    // ! TODO: Required to load after DOM is ready, did this now with jQuery ready.
    window.Helpers.initSidebarToggle();
    // Toggle Universal Sidebar

    // Navbar Search with autosuggest (typeahead)
    // ? You can remove the following JS if you don't want to use search functionality.
    //----------------------------------------------------------------------------------

    var searchToggler = $('.search-toggler'),
      searchInputWrapper = $('.search-input-wrapper'),
      searchInput = $('.search-input'),
      contentBackdrop = $('.content-backdrop');

    // Open search input on click of search icon
    if (searchToggler.length) {
      searchToggler.on('click', function () {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      });
    }
    // Open search on 'CTRL+/'
    $(document).on('keydown', function (event) {
      let ctrlKey = event.ctrlKey,
        slashKey = event.which === 191;

      if (ctrlKey && slashKey) {
        if (searchInputWrapper.length) {
          searchInputWrapper.toggleClass('d-none');
          searchInput.focus();
        }
      }
    });
    // Note: Following code is required to update container class of typeahead dropdown width on focus of search input. setTimeout is required to allow time to initiate Typeahead UI.
    setTimeout(function () {
      var twitterTypeahead = $('.twitter-typeahead');
      searchInput.on('focus', function () {
        if (searchInputWrapper.hasClass('container-xxl')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-xxl');
          twitterTypeahead.removeClass('container-fluid');
        } else if (searchInputWrapper.hasClass('container-fluid')) {
          searchInputWrapper.find(twitterTypeahead).addClass('container-fluid');
          twitterTypeahead.removeClass('container-xxl');
        }
      });
    }, 10);

    if (searchInput.length) {
      // Filter config
      var filterConfig = function (data) {
        return function findMatches(q, cb) {
          let matches;
          matches = [];
          data.filter(function (i) {
            // Filter by name or subtitle
            const nameMatch = i.name && i.name.toLowerCase().includes(q.toLowerCase());
            const subtitleMatch = i.subtitle && i.subtitle.toLowerCase().includes(q.toLowerCase());

            if (nameMatch || subtitleMatch) {
              matches.push(i);
            } else if (
              (!i.name.toLowerCase().startsWith(q.toLowerCase()) && nameMatch) ||
              (!i.subtitle.toLowerCase().startsWith(q.toLowerCase()) && subtitleMatch)
            ) {
              matches.push(i);
              matches.sort(function (a, b) {
                return b.name < a.name ? 1 : -1;
              });
            } else {
              return [];
            }
          });
          cb(matches);
        };
      };

      // Search JSON
    //   var searchJson = 'search-vertical.json'; // For vertical layout
    //   if ($('#layout-menu').hasClass('menu-horizontal')) {
    //     var searchJson = 'search-horizontal.json'; // For vertical layout
    //   }
      // Search API AJAX call
        var searchData = $.ajax({
            url: "{{ route('dashboard.search') }}", // Laravel route helper
            dataType: 'json',
            async: false
        }).responseJSON;
      // Init typeahead on searchInput
      searchInput.each(function () {
        var $this = $(this);
        searchInput
          .typeahead(
            {
              hint: false,
              classNames: {
                menu: 'tt-menu navbar-search-suggestion',
                cursor: 'active',
                suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
              }
            },
            // ? Add/Update blocks as per need
            // Pages
            {
              name: 'leads',
              display: 'name',
              limit: 5,
              source: filterConfig(searchData.leads),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Leads</h6>',
                suggestion: function ({ url, name, src = '', subtitle = '' }) {
                  return (
                    '<a href="' + url + '">' +
                    '<div class="d-flex align-items-center">' +
                    '<img class="rounded-circle me-3" src="' +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="user-info">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-3 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Leads</h6>' +
                  '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                  '</div>'
              }
            },
            {
              name: 'quotation',
              display: 'name',
              limit: 5,
              source: filterConfig(searchData.quotation),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Quotation</h6>',
                suggestion: function ({ url, name, src = '', subtitle = '' }) {
                  return (
                    '<a href="' + url + '">' +
                    '<div class="d-flex align-items-center">' +
                    '<img class="rounded-circle me-3" src="' +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="user-info">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-3 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Quotation</h6>' +
                  '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                  '</div>'
              }
            },
            {
              name: 'spk',
              display: 'name',
              limit: 5,
              source: filterConfig(searchData.spk),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">SPK</h6>',
                suggestion: function ({ url, name, src = '', subtitle = '' }) {
                  return (
                    '<a href="' + url + '">' +
                    '<div class="d-flex align-items-center">' +
                    '<img class="rounded-circle me-3" src="' +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="user-info">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-3 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">SPK</h6>' +
                  '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                  '</div>'
              }
            },
            {
              name: 'kontrak',
              display: 'name',
              limit: 5,
              source: filterConfig(searchData.kontrak),
              templates: {
                header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Kontrak</h6>',
                suggestion: function ({ url, name, src = '', subtitle = '' }) {
                  return (
                    '<a href="' + url + '">' +
                    '<div class="d-flex align-items-center">' +
                    '<img class="rounded-circle me-3" src="' +
                    src +
                    '" alt="' +
                    name +
                    '" height="32">' +
                    '<div class="user-info">' +
                    '<h6 class="mb-0">' +
                    name +
                    '</h6>' +
                    '<small class="text-muted">' +
                    subtitle +
                    '</small>' +
                    '</div>' +
                    '</div>' +
                    '</a>'
                  );
                },
                notFound:
                  '<div class="not-found px-3 py-2">' +
                  '<h6 class="suggestions-header text-primary mb-2">Kontrak</h6>' +
                  '<p class="py-2 mb-0"><i class="mdi mdi-alert-circle-outline me-2 mdi-14px"></i> No Results Found</p>' +
                  '</div>'
              }
            }
          )
          //On typeahead result render.
          .bind('typeahead:render', function () {
            // Show content backdrop,
            contentBackdrop.addClass('show').removeClass('fade');
          })
          // On typeahead select
          .bind('typeahead:select', function (ev, suggestion) {
            // Open selected page
            if (suggestion.url) {
              window.location = suggestion.url;
            }
          })
          // On typeahead close
          .bind('typeahead:close', function () {
            // Clear search
            searchInput.val('');
            $this.typeahead('val', '');
            // Hide search input wrapper
            searchInputWrapper.addClass('d-none');
            // Fade content backdrop
            contentBackdrop.addClass('fade').removeClass('show');
          });

        // On searchInput keyup, Fade content backdrop if search input is blank
        searchInput.on('keyup', function () {
          if (searchInput.val() == '') {
            contentBackdrop.addClass('fade').removeClass('show');
          }
        });
      });

      // Init PerfectScrollbar in search result
      var psSearch;
      $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
          wheelPropagation: false,
          suppressScrollX: true
        });
      });

      searchInput.on('keyup', function () {
        psSearch.update();
      });
    }
  });
}

</script>

@yield('pageScript')