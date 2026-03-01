<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>H-FlowStock | Add Invoice</title>
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
    </script>

</body>

</html>
