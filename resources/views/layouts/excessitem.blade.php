<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>H-FlowStock | Manual Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    @include('libraries.style')

</head>

<body>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        @include('components.topbar')

        <!-- Right Sidebar (Theme Settings) -->
        @include('components.rightsidebar')

        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        @include('components.appmenu')

        <!-- ========== App Menu End ========== -->

        <div class="page-content">
            <!-- ==================================================== -->
            <!-- Start right Content here -->
            <!-- ==================================================== -->
            @yield('content')
            <!-- ==================================================== -->
            <!-- End Page Content -->
            <!-- ==================================================== -->
            <!-- ========== Footer Start ========== -->
            @include('components.footer')
            <!-- ========== Footer End ========== -->
        </div>

    </div>
    <!-- END Wrapper -->

    @include('libraries.scripts')

    <script>
        // --- 1. Save Modal (Initialized on document ready) ---
        $(function() {

        $(document).on('select2:open', function(e) {
            window.setTimeout(function () {
                const searchField = document.querySelector('.select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            }, 10); // Miliseconds 10 k delay ekak dunnama browser eken focus eka block karanne na
        });
            // Customer
            $('#customer_slt').select2({
                theme: "bootstrap-5",
                // dropdownParent: $('#ItemSaveModalCenter'),
                placeholder: 'Search Customer*',
                ajax: {
                    url: '{{ route('ajax.customers') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                },
                minimumInputLength: 1
            }).on('select2:select', function(e) {
                var data = e.params.data;
                $('#cus_id').val(data.id);
                $('#cus_name').val(data.text);
                $('#item_slt').select2('open');
            });

            // Item
            $('#item_slt').select2({
                theme: "bootstrap-5",
                // dropdownParent: $('#ItemSaveModalCenter'),
                placeholder: 'Search Item*',
                ajax: {
                    url: '{{ route('ajax.items') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                },
                minimumInputLength: 1
            }).on('select2:select', function(e) {
                var data = e.params.data;
                $('#itm_code').val(data.id);
                $('#itm_name').val(data.text);
                $('#itm_barcode').val(data.barcode);
                $('#itm_sinhalaname').val(data.sinhalaname);
                $('#itm_group').val(data.group);
                $('#itm_book_code').val(data.bookcode);
                $('#itm_page_num').val(data.pagenum);
                $('#itm_subgroup').val(data.subgroup);
                $('#itm_unit_of_measure').val(data.unit_of_measure);
                $('#itm_stockinhand').val(data.stockinhand);
                $('#itm_state').val(data.status);

                updateItemStateDisplay();

                $('#itm_qty').focus();
            });
        });

        // --- 2. Update Modals (Initialized when the modal is SHOWN) ---
        // ... (Previous code)

        $('.modal').on('shown.bs.modal', function () {
            // Only target the Customer Update Modals
            if (this.id.startsWith('UpdateModalCenter')) {
                const cusId = this.id.replace('UpdateModalCenter', '');

                // --- Group Select2 Initialization ---
                const selectId = '#cus_sltu_' + cusId;
                const cusIdInput = '#cus_id_' + cusId;
                const cusNameInput = '#cus_name_' + cusId;

                // Initialize Group Select2
                if (!$(selectId).hasClass('select2-hidden-accessible')) {
                    $(selectId).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $(this),
                        placeholder: 'Search Customer*',
                        ajax: {
                            url: '{{ route('ajax.customers') }}',
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ q: params.term }),
                            processResults: data => ({ results: data }),
                            cache: true
                        },
                        minimumInputLength: 1
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                         $(cusIdInput).val(data.id);
                         $(cusNameInput).val(data.text);
                    });
                }

                // --- Item Select2 Initialization ---
                const selectitemId = '#itm_sltu_' + cusId;
                const itemIdInput = '#itm_code_' + cusId;
                const itemNameInput = '#itm_name_' + cusId;

                if (!$(selectitemId).hasClass('select2-hidden-accessible')) {
                    $(selectitemId).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $(this),
                        placeholder: 'Search Item*',
                        ajax: {
                            url: '{{ route('ajax.items') }}',
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ q: params.term }),
                            processResults: data => ({ results: data }),
                            cache: true
                        },
                        minimumInputLength: 1
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                        $(itemIdInput).val(data.id);
                        $(itemNameInput).val(data.text);
                    });
                }
            }
        });


    </script>

</body>

</html>
