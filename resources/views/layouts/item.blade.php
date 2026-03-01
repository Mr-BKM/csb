<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>H-FlowStock | Item</title>
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
    {{-- @stack('scripts') --}}

<script>
    // --- 1. Save Modal (Initialized on document ready) ---
    $(function() {
        // Item Group (Save Modal)
        $('#itm_group_slt').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#ItemSaveModalCenter'),
            placeholder: 'Search Item Group*',
            ajax: {
                url: '{{ route('ajax.groups') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data }),
                cache: true
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $('#itm_group_id').val(data.id);
            $('#itm_group').val(data.text);
        });

        // Item Sub Group (Save Modal)
        $('#itm_subgroup_slt').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#ItemSaveModalCenter'),
            placeholder: 'Search Item Sub Group',
            ajax: {
                url: '{{ route('ajax.subgroups') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data }),
                cache: true
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $('#itm_subgroup_id').val(data.id);
            $('#itm_subgroup').val(data.text);
        });
    });

    // --- 2. Update Modals (Initialized when the modal is SHOWN) ---
// ... (Previous code)

    $('.modal').on('shown.bs.modal', function () {
        // Only target the Item Update Modals
        if (this.id.startsWith('ItemUpdateModalCenter')) {
            const itemId = this.id.replace('ItemUpdateModalCenter', '');

            // --- Group Select2 Initialization ---
            const selectId = '#itm_group_sltu_' + itemId;
            const groupIdInput = '#itm_group_id_' + itemId;
            const groupNameInput = '#itm_group_' + itemId;

            // Initialize Group Select2
            if (!$(selectId).hasClass('select2-hidden-accessible')) {
                $(selectId).select2({
                    theme: "bootstrap-5",
                    dropdownParent: $(this),
                    placeholder: 'Search Item Group*',
                    ajax: {
                        url: '{{ route('ajax.groups') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data }),
                        cache: true
                    },
                    minimumInputLength: 1
                }).on('select2:select', function (e) {
                    var data = e.params.data;
                    $(groupIdInput).val(data.id);
                    $(groupNameInput).val(data.text);
                });
            }

            // --- SUB-GROUP Select2 Initialization ---
            const selectsubId = '#itm_subgroup_sltu_' + itemId;
            const subgroupIdInput = '#itm_subgroup_id_' + itemId;
            const subgroupNameInput = '#itm_subgroup_' + itemId;

            if (!$(selectsubId).hasClass('select2-hidden-accessible')) {
                $(selectsubId).select2({
                    theme: "bootstrap-5",
                    dropdownParent: $(this),
                    placeholder: 'Search Item Sub Group',
                    ajax: {
                        url: '{{ route('ajax.subgroups') }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data }),
                        cache: true
                    },
                    minimumInputLength: 1
                }).on('select2:select', function (e) {
                    var data = e.params.data;
                    $(subgroupIdInput).val(data.id);
                    $(subgroupNameInput).val(data.text);
                });
            }
        }
    });
</script>

</body>

</html>
