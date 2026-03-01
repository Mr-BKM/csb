<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>H-FlowStock | Modify Order</title>
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
//         function updateItemStateDisplay() {
//     const $itemStateInput = $('#itm_state');
//     const $statusSpan = $('#itm_state_status');

//     if ($itemStateInput.length && $statusSpan.length) {
//         const stateValue = $itemStateInput.val() ? $itemStateInput.val().trim().toLowerCase() : '';

//         $statusSpan.removeClass('text-danger font-weight-bold');
//         $statusSpan.text('');

//         if (stateValue === 'ordered') {
//             $statusSpan.text('Already Ordered Item');
//             $statusSpan.addClass('text-danger font-weight-bold');
//         }
//     }
// }
        // --- 1. Save Modal (Initialized on document ready) ---
        $(function() {
            // Customer
            $('#supplier_slt').select2({
                theme: "bootstrap-5",
                // dropdownParent: $('#ItemSaveModalCenter'),
                placeholder: 'Search Supplier*',
                ajax: {
                    url: '{{ route('ajax.supliers') }}',
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
                $('#sup_id').val(data.id);
                $('#sup_name').val(data.text);
            });
        });

        $('.modal').on('shown.bs.modal', function () {
            // Only target the Customer Update Modals
            if (this.id.startsWith('UpdateModalCenter')) {
                const supId = this.id.replace('UpdateModalCenter', '');

                // --- Group Select2 Initialization ---
                const selectId = '#sup_sltu_' + supId;
                const supIdInput = '#sup_id_' + supId;
                const supNameInput = '#sup_name_' + supId;

                // Initialize Group Select2
                if (!$(selectId).hasClass('select2-hidden-accessible')) {
                    $(selectId).select2({
                        theme: "bootstrap-5",
                        dropdownParent: $(this),
                        placeholder: 'Search Supplier*',
                        ajax: {
                            url: '{{ route('ajax.supliers') }}',
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ q: params.term }),
                            processResults: data => ({ results: data }),
                            cache: true
                        },
                        minimumInputLength: 1
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                         $(supIdInput).val(data.id);
                         $(supNameInput).val(data.text);
                    });
                }
            }
        });
    </script>

</body>

</html>
