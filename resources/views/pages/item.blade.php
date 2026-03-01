        @extends('layouts.item')
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        @section('content')
            <!-- Start Container Fluid -->
            <div class="container-fluid">
                {{-- Show success and error messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                        {{ session('success') }}
                    </div>
                    @endif @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
                            {{ session('error') }}
                        </div>
                        @endif @if ($errors->any())
                            <div class="alert alert-danger" id="msg-alert">
                                <strong>There were some problems with your input:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        {{-- This will show your custom message --}}
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <script>
                            // Auto-close after 4 seconds
                            setTimeout(() => {
                                const successAlert = document.getElementById('success-alert');
                                if (successAlert) {
                                    bootstrap.Alert.getOrCreateInstance(successAlert).close();
                                }
                                const errorAlert = document.getElementById('error-alert');
                                if (errorAlert) {
                                    bootstrap.Alert.getOrCreateInstance(errorAlert).close();
                                }
                                const msgAlert = document.getElementById('msg-alert');
                                if (msgAlert) {
                                    bootstrap.Alert.getOrCreateInstance(msgAlert).close();
                                }
                            }, 4000); // 4 seconds
                        </script>
                        {{-- End Of Show success and error messages --}}

                        <div class="row">
                            <div class="col">
                                <div class="card-body">
                                    <!-- Nav Tabs -->
                                    <ul class="nav nav-tabs" id="itemTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link {{ !session('showGroupTable') && !session('showSubGroupTable') ? 'active' : '' }}"
                                                id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button"
                                                role="tab" aria-controls="items" aria-selected="true">
                                                Items
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ session('showGroupTable') ? 'active' : '' }}"
                                                id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button"
                                                role="tab" aria-controls="groups" aria-selected="false">
                                                Groups
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ session('showSubGroupTable') ? 'active' : '' }}"
                                                id="subgroups-tab" data-bs-toggle="tab" data-bs-target="#subgroups"
                                                type="button" role="tab" aria-controls="subgroups"
                                                aria-selected="false">
                                                Sub Groups
                                            </button>
                                        </li>
                                    </ul>
                                    <!-- End Nav Tabs -->

                                    <div class="tab-content" id="itemTabsContent">

                                        <!-- Item Tab -->

                                        <div class="tab-pane fade {{ !session('showGroupTable') && !session('showSubGroupTable') ? 'show active' : '' }}"
                                            id="items" role="tabpanel" aria-labelledby="items-tab">
                                            {{-- ✅ Item Table Here --}}
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5 class="card-title mb-1 anchor" id="basic"> Item Registration
                                                    </h5>
                                                    <br>
                                                    <div class="d-flex flex-wrap justify-content-between gap-3">
                                                        <form action="{{ URL('/item') }}" method="GET"
                                                            class="d-flex gap-2">
                                                            <div class="search-bar">
                                                                <span>
                                                                    <i class="bx bx-search-alt"></i>
                                                                </span>
                                                                <input type="search" class="form-control" id="search"
                                                                    placeholder="Search Item..." name="search">
                                                            </div>
                                                            <!-- 🔹 New Button Next to Search -->
                                                            {{-- <button type="submit" class="btn btn-primary">
                                                                <i class="bx bx-search me-1"></i> Search</button> --}}
                                                        </form>
                                                        <form class="d-flex gap-2">
                                                            @if (auth()->user()->role === 'Admin')
                                                                <button type="button" class="btn btn-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#confirmDeleteModalfor">
                                                                    <i class="bx bx-trash me-1"></i>Delete All Items
                                                                    ForeignKey
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-danger"
                                                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                                                <i class="bx bx-trash me-1"></i>Delete All Items
                                                            </button>
                                                            <button type="button" class="btn btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#ItemSaveModalCenter">
                                                                <i class="bx bx-plus me-1"></i>Create Item</button>
                                                        </form>
                                                    </div>

                                                    <!-- Modal All Delete-->
                                                    <div class="modal fade" id="confirmDeleteModal" tabindex="-1"
                                                        aria-labelledby="confirmDeleteModal" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold text-danger"
                                                                        id="confirmDeleteModal">
                                                                        Confirm Deletion </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <!-- Modal Body -->
                                                                <div class="modal-body text-center">
                                                                    <p class="mb-2 fw-semibold fs-5 text-danger">
                                                                        ⚠️ This action is irreversible! </p>
                                                                    <p class="mb-0 text-muted"> All Items will be
                                                                        <strong>deleted
                                                                            immediately</strong>. <br>You
                                                                        can't undo this action. </p>
                                                                </div>
                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="{{ route('items.deleteAll') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">Yes,
                                                                            Delete Everything</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal All forign key Delete-->
                                                    <div class="modal fade" id="confirmDeleteModalfor" tabindex="-1"
                                                        aria-labelledby="confirmDeleteModalfor" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <!-- Modal Header -->
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold text-danger"
                                                                        id="confirmDeleteModalfor">
                                                                        Confirm Deletion </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <!-- Modal Body -->
                                                                <div class="modal-body text-center">
                                                                    <p class="mb-2 fw-semibold fs-5 text-danger">
                                                                        ⚠️ This action is irreversible! </p>
                                                                    <p class="mb-0 text-muted"> All Items will be
                                                                        <strong>deleted
                                                                            immediately</strong>. <br>You
                                                                        can't undo this action. </p>
                                                                </div>
                                                                <!-- Modal Footer -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <form action="{{ route('items.deleteAllForeignKey') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">Yes,
                                                                            Delete Everything</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                    {{-- <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you absolutely sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                This action cannot be undone. This will permanently delete all items from the database.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete Everything</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}


                                                    <div class="table-responsive table-centered">
                                                        <table class="table text-nowrap mb-0">
                                                            <thead class="bg-light bg-opacity-50">
                                                                <tr>
                                                                    <th class="border-0 py-2">No</th>
                                                                    <th class="border-0 py-2">Item Code</th>
                                                                    <th class="border-0 py-2">Item Name</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Book Code</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Page Number</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Item Group</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Stock</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Reorder Level</th>
                                                                    <th class="border-0 py-2" style="text-align: center;">
                                                                        Reorder Status</th>
                                                                    <th class="border-0 py-2">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <!-- end thead-->
                                                            <tbody>
                                                                @forelse ($items as $item)
                                                                    <tr>
                                                                        <td>{{ $items->firstItem() + $loop->index }}</td>
                                                                        <td>{{ $item->itm_code }}</td>
                                                                        <td>{{ $item->itm_name }}</td>
                                                                        <td style="text-align: center;">
                                                                            {{ $item->itm_book_code }}</td>
                                                                        <td style="text-align: center;">
                                                                            {{ $item->itm_page_num }}</td>
                                                                        <td style="text-align: center;">
                                                                            {{ $item->itm_group }}</td>
                                                                        <td style="text-align: center;">
                                                                            {{ $item->itm_stock }}</td>
                                                                        <td style="text-align: center;">
                                                                            {{ $item->itm_reorder_level }}</td>
                                                                        <td style="text-align: center;"><span
                                                                                class="badge {{ $item->itm_reorder_flag == 'Yes' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                                                {{ $item->itm_reorder_flag }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <button
                                                                                class="btn btn-sm btn-soft-secondary me-1"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#ItemUpdateModalCenter{{ $item->id }}">
                                                                                <i class="bx bx-edit fs-16"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-soft-danger"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#ItemdeleteModal{{ $item->id }}">
                                                                                <i class="bx bx-trash fs-16"></i>
                                                                            </button>
                                                                            <!-- Modal Update -->
                                                                            <div class="modal fade"
                                                                                id="ItemUpdateModalCenter{{ $item->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="SaveModalCenterTitle"
                                                                                aria-hidden="true"
                                                                                data-bs-backdrop="static"
                                                                                data-bs-keyboard="false">
                                                                                <div
                                                                                    class="modal-dialog modal-lg modal-dialog-centered">
                                                                                    <div class="modal-content">
                                                                                        <form method="POST"
                                                                                            action="{{ route('item.updateData', $item->id) }}">
                                                                                            @csrf <div
                                                                                                class="modal-header">
                                                                                                <h5 class="modal-title"
                                                                                                    id="SaveModalCenterTitle">
                                                                                                    Edit Item
                                                                                                </h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_code"
                                                                                                        id="itm_code"
                                                                                                        placeholder="Enter Item Code*"
                                                                                                        value="{{ $item->itm_code }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_barcode"
                                                                                                        id="itm_barcode"
                                                                                                        placeholder="Enter Item Barcode"
                                                                                                        value="{{ $item->itm_barcode }}">
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_name"
                                                                                                        id="itm_name"
                                                                                                        placeholder="Enter Item Name*"
                                                                                                        value="{{ $item->itm_name }}"
                                                                                                        required>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_sinhalaname"
                                                                                                        id="itm_sinhalaname"
                                                                                                        placeholder="Enter Item Sinhala Name"
                                                                                                        value="{{ $item->itm_sinhalaname }}">
                                                                                                </div>

                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_book_code"
                                                                                                        id="itm_book_code"
                                                                                                        placeholder="Enter Item Book Code*"
                                                                                                        value="{{ $item->itm_book_code }}"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_page_num"
                                                                                                        id="itm_page_num"
                                                                                                        placeholder="Enter Item Page Number*"
                                                                                                        value="{{ $item->itm_page_num }}"
                                                                                                        required>
                                                                                                </div>

                                                                                                <div class="mb-3">
                                                                                                    <select
                                                                                                        class="form-select w-100"
                                                                                                        name="itm_group_sltu"
                                                                                                        id="itm_group_sltu_{{ $item->id }}"
                                                                                                        required>
                                                                                                        @if ($item->itm_group)
                                                                                                            <option
                                                                                                                value="{{ $item->itm_group_id ?? $item->itm_group }}"
                                                                                                                selected="selected">
                                                                                                                {{ $item->itm_group }}
                                                                                                            </option>
                                                                                                        @endif
                                                                                                    </select>
                                                                                                </div>
                                                                                                <input type="hidden"
                                                                                                    name="itm_group_id"
                                                                                                    id="itm_group_id_{{ $item->id }}"
                                                                                                    value="{{ $item->itm_group_id ?? '' }}">
                                                                                                <input type="hidden"
                                                                                                    name="itm_group"
                                                                                                    id="itm_group_{{ $item->id }}"
                                                                                                    value="{{ $item->itm_group ?? '' }}">
                                                                                                <div class="mb-3">
                                                                                                    <select
                                                                                                        class="form-select w-100"
                                                                                                        name="itm_subgroup_sltu"
                                                                                                        id="itm_subgroup_sltu_{{ $item->id }}">
                                                                                                        @if ($item->itm_subgroup)
                                                                                                            <option
                                                                                                                value="{{ $item->itm_subgroup_id ?? $item->itm_subgroup }}"
                                                                                                                selected="selected">
                                                                                                                {{ $item->itm_subgroup }}
                                                                                                            </option>
                                                                                                        @endif
                                                                                                    </select>
                                                                                                </div>
                                                                                                <input type="hidden"
                                                                                                    name="itm_subgroup_id"
                                                                                                    id="itm_subgroup_id_{{ $item->id }}"
                                                                                                    value="{{ $item->itm_subgroup_id ?? '' }}">
                                                                                                <input type="hidden"
                                                                                                    name="itm_subgroup"
                                                                                                    id="itm_subgroup_{{ $item->id }}"
                                                                                                    value="{{ $item->itm_subgroup ?? '' }}">
                                                                                                <div class="mb-3">
                                                                                                    <select
                                                                                                        class="form-select w-100"
                                                                                                        name="itm_unit_of_measure"
                                                                                                        id="itm_unit_of_measure"
                                                                                                        required>
                                                                                                        <option
                                                                                                            value=""
                                                                                                            disabled
                                                                                                            {{ $item->itm_unit_of_measure == '' ? 'selected' : '' }}>
                                                                                                            Select Unit of
                                                                                                            Measure</option>

                                                                                                        {{-- Standard Measurements --}}
                                                                                                        <option
                                                                                                            value="Nos"
                                                                                                            {{ $item->itm_unit_of_measure == 'Nos' ? 'selected' : '' }}>
                                                                                                            Nos (Numbers)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="m"
                                                                                                            {{ $item->itm_unit_of_measure == 'm' ? 'selected' : '' }}>
                                                                                                            m (Meter)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="cm"
                                                                                                            {{ $item->itm_unit_of_measure == 'cm' ? 'selected' : '' }}>
                                                                                                            cm (Centimeter)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="feet"
                                                                                                            {{ $item->itm_unit_of_measure == 'feet' ? 'selected' : '' }}>
                                                                                                            feet</option>
                                                                                                        <option
                                                                                                            value="inch"
                                                                                                            {{ $item->itm_unit_of_measure == 'inch' ? 'selected' : '' }}>
                                                                                                            inch</option>
                                                                                                        <option
                                                                                                            value="sqft"
                                                                                                            {{ $item->itm_unit_of_measure == 'sqft' ? 'selected' : '' }}>
                                                                                                            Sqft (Square
                                                                                                            Feet)</option>

                                                                                                        {{-- Weight & Volume --}}
                                                                                                        <option
                                                                                                            value="g"
                                                                                                            {{ $item->itm_unit_of_measure == 'g' ? 'selected' : '' }}>
                                                                                                            g (Gram)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="Kg"
                                                                                                            {{ $item->itm_unit_of_measure == 'Kg' ? 'selected' : '' }}>
                                                                                                            Kg (Kilogram)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="Ltr"
                                                                                                            {{ $item->itm_unit_of_measure == 'Ltr' ? 'selected' : '' }}>
                                                                                                            Ltr (Litre)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="ml"
                                                                                                            {{ $item->itm_unit_of_measure == 'ml' ? 'selected' : '' }}>
                                                                                                            ml (Millilitre)
                                                                                                        </option>

                                                                                                        {{-- Bulk & Packaging --}}
                                                                                                        <option
                                                                                                            value="Pkt"
                                                                                                            {{ $item->itm_unit_of_measure == 'Pkt' ? 'selected' : '' }}>
                                                                                                            Pkt (Packet)
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="Box"
                                                                                                            {{ $item->itm_unit_of_measure == 'Box' ? 'selected' : '' }}>
                                                                                                            Box</option>
                                                                                                        <option
                                                                                                            value="Bag"
                                                                                                            {{ $item->itm_unit_of_measure == 'Bag' ? 'selected' : '' }}>
                                                                                                            Bag</option>
                                                                                                        <option
                                                                                                            value="Can"
                                                                                                            {{ $item->itm_unit_of_measure == 'Can' ? 'selected' : '' }}>
                                                                                                            Can</option>
                                                                                                        <option
                                                                                                            value="Bottle"
                                                                                                            {{ $item->itm_unit_of_measure == 'Bottle' ? 'selected' : '' }}>
                                                                                                            Bottle</option>

                                                                                                        {{-- Specific Hardware/Plumbing --}}
                                                                                                        <option
                                                                                                            value="Set"
                                                                                                            {{ $item->itm_unit_of_measure == 'Set' ? 'selected' : '' }}>
                                                                                                            Set</option>
                                                                                                        <option
                                                                                                            value="Pair"
                                                                                                            {{ $item->itm_unit_of_measure == 'Pair' ? 'selected' : '' }}>
                                                                                                            Pair</option>
                                                                                                        <option
                                                                                                            value="Sheet"
                                                                                                            {{ $item->itm_unit_of_measure == 'Sheet' ? 'selected' : '' }}>
                                                                                                            Sheet</option>
                                                                                                        <option
                                                                                                            value="Roll"
                                                                                                            {{ $item->itm_unit_of_measure == 'Roll' ? 'selected' : '' }}>
                                                                                                            Roll</option>
                                                                                                        <option
                                                                                                            value="Bundle"
                                                                                                            {{ $item->itm_unit_of_measure == 'Bundle' ? 'selected' : '' }}>
                                                                                                            Bundle</option>
                                                                                                        <option
                                                                                                            value="Coil"
                                                                                                            {{ $item->itm_unit_of_measure == 'Coil' ? 'selected' : '' }}>
                                                                                                            Coil</option>
                                                                                                        <option
                                                                                                            value="Books"
                                                                                                            {{ $item->itm_unit_of_measure == 'Books' ? 'selected' : '' }}>
                                                                                                            Books</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_stock"
                                                                                                        id="itm_stock_{{ $item->id }}"
                                                                                                        oninput="syncUpdateStock(this.value, '{{ $item->id }}')"
                                                                                                        placeholder="Enter Item Stock"
                                                                                                        value="{{ $item->itm_stock }}">

                                                                                                    <input type="hidden"
                                                                                                        name="itm_book_stock"
                                                                                                        id="itm_book_stock_{{ $item->id }}"
                                                                                                        value="{{ $item->itm_book_stock }}">
                                                                                                </div>

                                                                                                <div
                                                                                                    class="mb-3 d-flex align-items-center gap-2">
                                                                                                    <input type="number"
                                                                                                        class="form-control"
                                                                                                        name="itm_reorder_level"
                                                                                                        id="itm_reorder_level_{{ $item->id }}"
                                                                                                        value="{{ $item->itm_reorder_level }}"
                                                                                                        onblur='""===this.value&&(this.value=0)'
                                                                                                        onfocus='0==this.value&&(this.value="")'
                                                                                                        placeholder="Enter Item Reorder Level">

                                                                                                    <div
                                                                                                        class="form-check form-switch">
                                                                                                        <input
                                                                                                            type="hidden"
                                                                                                            name="itm_reorder_flag"
                                                                                                            value="No">

                                                                                                        <input
                                                                                                            class="form-check-input"
                                                                                                            name="itm_reorder_flag"
                                                                                                            type="checkbox"
                                                                                                            role="switch"
                                                                                                            id="flexSwitchCheckChecked_{{ $item->id }}"
                                                                                                            value="Yes"
                                                                                                            {{ $item->itm_reorder_flag == 'Yes' || $item->itm_reorder_flag == '1' ? 'checked' : '' }}>

                                                                                                        <label
                                                                                                            class="form-check-label"
                                                                                                            for="flexSwitchCheckChecked_{{ $item->id }}">Reorder
                                                                                                            Level</label>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="itm_description"
                                                                                                        id="itm_description"
                                                                                                        placeholder="Enter Item Description"
                                                                                                        value="{{ $item->itm_description }}">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary"
                                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">Update
                                                                                                    Now</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Modal Delete-->
                                                                            <div class="modal fade"
                                                                                id="ItemdeleteModal{{ $item->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="deleteModalLabel{{ $item->id }}"
                                                                                aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <!-- Modal Header -->
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title fw-bold text-danger"
                                                                                                id="deleteModalLabel{{ $item->id }}">
                                                                                                Confirm Deletion </h5>
                                                                                            <button type="button"
                                                                                                class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <!-- Modal Body -->
                                                                                        <div
                                                                                            class="modal-body text-center">
                                                                                            <p
                                                                                                class="mb-2 fw-semibold fs-5 text-danger">
                                                                                                ⚠️ This action is
                                                                                                irreversible!
                                                                                            </p>
                                                                                            <p>Item
                                                                                                <strong>{{ $item->itm_name }}</strong>
                                                                                                will be <strong>deleted
                                                                                                    immediately</strong>.<br>You
                                                                                                can't undo this action.</p>
                                                                                        </div>
                                                                                        <!-- Modal Footer -->
                                                                                        <div
                                                                                            class="modal-footer d-flex justify-content-end">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary btn-sm"
                                                                                                data-bs-dismiss="modal">
                                                                                                Cancel </button>
                                                                                            <a href="{{ route('item.deleteData', $item->id) }}"
                                                                                                class="btn btn-danger btn-sm ms-2">
                                                                                                <i
                                                                                                    class="bx bx-trash fs-16 me-1"></i>
                                                                                                Delete </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                </tr> @empty <tr>
                                                                        <td colspan="8" class="text-center text-muted">
                                                                            No
                                                                            Item found. </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <!-- end tbody -->
                                                        </table>
                                                        <br>
                                                        {{ $items->appends(request()->except('item_page'))->links() }}

                                                        <!-- end table -->
                                                    </div>
                                                    <!-- end row -->
                                                </div>
                                            </div>
                                            <!-- Modal Save -->
                                            <div class="modal fade" id="ItemSaveModalCenter" tabindex="-1"
                                                aria-labelledby="ItemSaveModalCenterTitle" aria-hidden="true"
                                                data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('item.saveData') }}"> @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="ItemSaveModalCenterTitle">Item
                                                                    Registration</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_code" id="itm_code"
                                                                        placeholder="Enter Item Code*" autofocus required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_barcode" id="itm_barcode"
                                                                        placeholder="Enter Item Barcode*">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_name" id="itm_name"
                                                                        placeholder="Enter Item Name*" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_sinhalaname" id="itm_sinhalaname"
                                                                        placeholder="Enter Item Sinhala Name">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_book_code" id="itm_book_code"
                                                                        placeholder="Enter Item Book Code*" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_page_num" id="itm_page_num"
                                                                        placeholder="Enter Item Page Number*" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <select class="form-select w-100" name="itm_group_slt"
                                                                        id="itm_group_slt" required></select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <select class="form-select w-100"
                                                                        name="itm_subgroup_slt"
                                                                        id="itm_subgroup_slt"></select>
                                                                </div>
                                                                <input type="hidden" name="itm_group_id"
                                                                    id="itm_group_id">
                                                                <input type="hidden" name="itm_group" id="itm_group">

                                                                <input type="hidden" name="itm_subgroup_id"
                                                                    id="itm_subgroup_id">
                                                                <input type="hidden" name="itm_subgroup"
                                                                    id="itm_subgroup">
                                                                <div class="mb-3">
                                                                    <select class="form-select w-100"
                                                                        name="itm_unit_of_measure"
                                                                        id="itm_unit_of_measure" required>
                                                                        <option value="" disabled selected>Select
                                                                            Unit of Measure*</option>

                                                                        <option value="Nos">Nos</option>
                                                                        <option value="m">m (Meter)</option>
                                                                        <option value="cm">cm (Centimeter)</option>
                                                                        <option value="feet">feet</option>
                                                                        <option value="inch">inch</option>
                                                                        <option value="sqft">Sqft (Square Feet)</option>

                                                                        <option value="g">g (Gram)</option>
                                                                        <option value="Kg">Kg (Kilogram)</option>
                                                                        <option value="Ltr">Ltr (Litre)</option>
                                                                        <option value="ml">ml (Millilitre)</option>

                                                                        <option value="Pkt">Pkt (Packet)</option>
                                                                        <option value="Box">Box</option>
                                                                        <option value="Bag">Bag (Cement/Putty)</option>
                                                                        <option value="Can">Can (Paint/Chemicals)
                                                                        </option>
                                                                        <option value="Bottle">Bottle</option>

                                                                        <option value="Set">Set</option>
                                                                        <option value="Pair">Pair (Hinges/Gloves)
                                                                        </option>
                                                                        <option value="Sheet">Sheet (Plywood/Roofing)
                                                                        </option>
                                                                        <option value="Roll">Roll (Wire/Mesh)</option>
                                                                        <option value="Bundle">Bundle (Pipes/Rods)</option>
                                                                        <option value="Coil">Coil (Poly Pipe)</option>
                                                                        <option value="Books">Books</option>
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <input type="number" class="form-control w-100"
                                                                        name="itm_stock" id="itm_stock"
                                                                        onblur="if(this.value===''){this.value=0}"
                                                                        onfocus="if(this.value==0){this.value=''}"
                                                                        oninput="syncStock(this.value)"
                                                                        placeholder="Enter Item Stock">

                                                                    <input type="hidden" name="itm_book_stock"
                                                                        id="itm_book_stock" value="0">
                                                                </div>

                                                                <div class="mb-3 d-flex align-items-center gap-2">
                                                                    <input type="number" class="form-control"
                                                                        name="itm_reorder_level" id="itm_reorder_level"
                                                                        onblur="if(this.value===''){this.value=0}"
                                                                        onfocus="if(this.value==0){this.value=''}"
                                                                        placeholder="Enter Item Reorder Level">

                                                                    <div class="form-check form-switch">
                                                                        <input type="hidden" name="itm_reorder_flag"
                                                                            value="No">

                                                                        <input class="form-check-input"
                                                                            name="itm_reorder_flag" type="checkbox"
                                                                            role="switch" id="flexSwitchCheckChecked"
                                                                            value="Yes" checked>

                                                                        <label class="form-check-label"
                                                                            for="flexSwitchCheckChecked">Reorder
                                                                            Level</label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <input type="text" class="form-control w-100"
                                                                        name="itm_description" id="itm_description"
                                                                        placeholder="Enter Item Description">
                                                                </div>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Add
                                                                    Now</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Group Tab -->
                                        <div class="tab-pane fade {{ session('showGroupTable') ? 'show active' : '' }}"
                                            id="groups" role="tabpanel" aria-labelledby="groups-tab">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5 class="card-title mb-1 anchor" id="basic"> Group Registration
                                                    </h5>
                                                    <br>
                                                    <div class="d-flex flex-wrap justify-content-between gap-3">
                                                        <form action="{{ URL('/item') }}" method="GET"
                                                            class="d-flex gap-2">
                                                            <div class="search-bar">
                                                                <span>
                                                                    <i class="bx bx-search-alt"></i>
                                                                </span>
                                                                <input type="search" class="form-control" id="search"
                                                                    placeholder="Search Group..." name="search">
                                                            </div>
                                                            <input type="hidden" name="tab" value="groups">
                                                        </form>
                                                        <form class="d-flex gap-2">
                                                            <button type="button" class="btn btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#GroupSaveModalCenter">
                                                                <i class="bx bx-plus me-1"></i>Create Group</button>
                                                        </form>
                                                    </div>
                                                    <div class="table-responsive table-centered">
                                                        <table class="table text-nowrap mb-0">
                                                            <thead class="bg-light bg-opacity-50">
                                                                <tr>
                                                                    <th class="border-0 py-2">No</th>
                                                                    <th class="border-0 py-2">Group ID</th>
                                                                    <th class="border-0 py-2">Group Name</th>
                                                                    <th class="border-0 py-2">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($groups as $group)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $group->grp_id }}</td>
                                                                        <td>{{ $group->grp_name }}</td>
                                                                        <td>
                                                                            <button
                                                                                class="btn btn-sm btn-soft-secondary me-1"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#GroupUpdateModalCenter{{ $group->id }}">
                                                                                <i class="bx bx-edit fs-16"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-soft-danger"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#GroupdeleteModal{{ $group->id }}">
                                                                                <i class="bx bx-trash fs-16"></i>
                                                                            </button>
                                                                            <!-- Modal Update -->
                                                                            <div class="modal fade"
                                                                                id="GroupUpdateModalCenter{{ $group->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="SaveModalCenterTitle"
                                                                                aria-hidden="true"
                                                                                data-bs-backdrop="static"
                                                                                data-bs-keyboard="false">
                                                                                <div
                                                                                    class="modal-dialog modal-lg modal-dialog-centered">
                                                                                    <div class="modal-content">
                                                                                        <form method="POST"
                                                                                            action="{{ route('group.updateData', $group->id) }}">
                                                                                            @csrf <div
                                                                                                class="modal-header">
                                                                                                <h5 class="modal-title"
                                                                                                    id="SaveModalCenterTitle">
                                                                                                    Edit Group
                                                                                                </h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="grp_id"
                                                                                                        id="grp_id"
                                                                                                        placeholder="Enter Group Id*"
                                                                                                        value="{{ $group->grp_id }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="grp_name"
                                                                                                        id="grp_name"
                                                                                                        placeholder="Enter Group Name*"
                                                                                                        value="{{ $group->grp_name }}"
                                                                                                        required>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary"
                                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">Update
                                                                                                    Now</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Modal Delete-->
                                                                            <div class="modal fade"
                                                                                id="GroupdeleteModal{{ $group->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="deleteModalLabel{{ $group->id }}"
                                                                                aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <!-- Modal Header -->
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title fw-bold text-danger"
                                                                                                id="deleteModalLabel{{ $group->id }}">
                                                                                                Confirm Deletion </h5>
                                                                                            <button type="button"
                                                                                                class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <!-- Modal Body -->
                                                                                        <div
                                                                                            class="modal-body text-center">
                                                                                            <p
                                                                                                class="mb-2 fw-semibold fs-5 text-danger">
                                                                                                ⚠️ This action is
                                                                                                irreversible! </p>
                                                                                            <p>Group
                                                                                                <strong>{{ $group->grp_name }}</strong>
                                                                                                will be <strong>deleted
                                                                                                    immediately</strong>.<br>You
                                                                                                can't undo this action.</p>
                                                                                        </div>
                                                                                        <!-- Modal Footer -->
                                                                                        <div
                                                                                            class="modal-footer d-flex justify-content-end">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary btn-sm"
                                                                                                data-bs-dismiss="modal">
                                                                                                Cancel </button>
                                                                                            <a href="{{ route('group.deleteData', $group->id) }}"
                                                                                                class="btn btn-danger btn-sm ms-2">
                                                                                                <i
                                                                                                    class="bx bx-trash fs-16 me-1"></i>
                                                                                                Delete </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                </tr> @empty <tr>
                                                                        <td colspan="8" class="text-center text-muted">
                                                                            No Groups found. </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <!-- end tbody -->
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="GroupSaveModalCenter" tabindex="-1"
                                                    aria-labelledby="ItemSaveModalCenterTitle" aria-hidden="true"
                                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <form method="POST" action="{{ route('group.saveData') }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="ItemSaveModalCenterTitle">
                                                                        Group
                                                                        Registration</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <input type="text" class="form-control w-100"
                                                                            id="grp_id" placeholder="Enter Group Id*"
                                                                            value="{{ $newGroupId }}" disabled>
                                                                        <input type="hidden" name="grp_id"
                                                                            value="{{ $newGroupId }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <input type="text" class="form-control w-100"
                                                                            name="grp_name" id="grp_name"
                                                                            placeholder="Enter Group Name*" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Add
                                                                        Now</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub Group Tab -->
                                        <div class="tab-pane fade {{ session('showSubGroupTable') ? 'show active' : '' }}"
                                            id="subgroups" role="tabpanel" aria-labelledby="subgroups-tab">
                                            <div class="card">
                                                <div class="card-body">

                                                    <h5 class="card-title mb-1 anchor" id="basic"> Sub Group
                                                        Registration
                                                    </h5>
                                                    <br>
                                                    <div class="d-flex flex-wrap justify-content-between gap-3">
                                                        <form action="{{ URL('/item') }}" method="GET"
                                                            class="d-flex gap-2">
                                                            <div class="search-bar">
                                                                <span>
                                                                    <i class="bx bx-search-alt"></i>
                                                                </span>
                                                                <input type="search" class="form-control" id="search"
                                                                    placeholder="Search Sub Group..." name="search">
                                                            </div>
                                                            <input type="hidden" name="tab" value="subgroups">
                                                        </form>
                                                        <form class="d-flex gap-2">
                                                            <button type="button" class="btn btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#SubGroupSaveModalCenter">
                                                                <i class="bx bx-plus me-1"></i>Create Sub Group</button>
                                                        </form>
                                                    </div>
                                                    <div class="table-responsive table-centered">
                                                        <table class="table text-nowrap mb-0">
                                                            <thead class="bg-light bg-opacity-50">
                                                                <tr>
                                                                    <th class="border-0 py-2">No</th>
                                                                    <th class="border-0 py-2">Sub Group ID</th>
                                                                    <th class="border-0 py-2">Sub Group Name</th>
                                                                    <th class="border-0 py-2">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <!-- end thead-->
                                                            <tbody>
                                                                @forelse ($subgroups as $subgroup)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $subgroup->subgrp_id }}</td>
                                                                        <td>{{ $subgroup->subgrp_name }}</td>
                                                                        <td>
                                                                            <button
                                                                                class="btn btn-sm btn-soft-secondary me-1"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#SubGroupUpdateModalCenter{{ $subgroup->id }}">
                                                                                <i class="bx bx-edit fs-16"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-soft-danger"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#SubGroupdeleteModal{{ $subgroup->id }}">
                                                                                <i class="bx bx-trash fs-16"></i>
                                                                            </button>
                                                                            <!-- Modal Update -->
                                                                            <div class="modal fade"
                                                                                id="SubGroupUpdateModalCenter{{ $subgroup->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="SaveModalCenterTitle"
                                                                                aria-hidden="true"
                                                                                data-bs-backdrop="static"
                                                                                data-bs-keyboard="false">
                                                                                <div
                                                                                    class="modal-dialog modal-lg modal-dialog-centered">
                                                                                    <div class="modal-content">
                                                                                        <form method="POST"
                                                                                            action="{{ route('subgroup.updateData', $subgroup->id) }}">
                                                                                            @csrf <div
                                                                                                class="modal-header">
                                                                                                <h5 class="modal-title"
                                                                                                    id="SaveModalCenterTitle">
                                                                                                    Edit Sub Group
                                                                                                </h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="subgrp_id"
                                                                                                        id="subgrp_id"
                                                                                                        placeholder="Enter Sub Group Id*"
                                                                                                        value="{{ $subgroup->subgrp_id }}"
                                                                                                        disabled>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <input type="text"
                                                                                                        class="form-control w-100"
                                                                                                        name="subgrp_name"
                                                                                                        id="subgrp_name"
                                                                                                        placeholder="Enter Sub Group Name*"
                                                                                                        value="{{ $subgroup->subgrp_name }}"
                                                                                                        required>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary"
                                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-primary">Update
                                                                                                    Now</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Modal Delete-->
                                                                            <div class="modal fade"
                                                                                id="SubGroupdeleteModal{{ $subgroup->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="deleteModalLabel{{ $subgroup->id }}"
                                                                                aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <!-- Modal Header -->
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title fw-bold text-danger"
                                                                                                id="deleteModalLabel{{ $subgroup->id }}">
                                                                                                Confirm Deletion </h5>
                                                                                            <button type="button"
                                                                                                class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <!-- Modal Body -->
                                                                                        <div
                                                                                            class="modal-body text-center">
                                                                                            <p
                                                                                                class="mb-2 fw-semibold fs-5 text-danger">
                                                                                                ⚠️ This action is
                                                                                                irreversible! </p>
                                                                                            <p>Sub Group
                                                                                                <strong>{{ $subgroup->subgrp_name }}</strong>
                                                                                                will be <strong>deleted
                                                                                                    immediately</strong>.<br>You
                                                                                                can't undo this action.</p>

                                                                                        </div>
                                                                                        <!-- Modal Footer -->
                                                                                        <div
                                                                                            class="modal-footer d-flex justify-content-end">
                                                                                            <button type="button"
                                                                                                class="btn btn-secondary btn-sm"
                                                                                                data-bs-dismiss="modal">
                                                                                                Cancel </button>
                                                                                            <a href="{{ route('subgroup.deleteData', $subgroup->id) }}"
                                                                                                class="btn btn-danger btn-sm ms-2">
                                                                                                <i
                                                                                                    class="bx bx-trash fs-16 me-1"></i>
                                                                                                Delete </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </td>
                                                                </tr> @empty <tr>
                                                                        <td colspan="8" class="text-center text-muted">
                                                                            No suppliers found. </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <!-- end tbody -->
                                                        </table>
                                                        <!-- end table -->
                                                    </div>
                                                    <div class="modal fade" id="SubGroupSaveModalCenter" tabindex="-1"
                                                        aria-labelledby="ItemSaveModalCenterTitle" aria-hidden="true"
                                                        data-bs-backdrop="static" data-bs-keyboard="false">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <form method="POST"
                                                                    action="{{ route('subgroup.saveData') }}">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="ItemSaveModalCenterTitle">
                                                                            Sub Group
                                                                            Registration</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <input type="text"
                                                                                class="form-control w-100" id="subgrp_id"
                                                                                placeholder="Enter Sub Group Id*"
                                                                                value="{{ $newsubGroupId }}" disabled>
                                                                            <input type="hidden" name="subgrp_id"
                                                                                value="{{ $newsubGroupId }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <input type="text"
                                                                                class="form-control w-100"
                                                                                name="subgrp_name" id="subgrp_name"
                                                                                placeholder="Enter Sub Group Name*"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" class="btn btn-primary">Add
                                                                            Now</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
            </div>
            <!-- End Container Fluid -->
        @endsection
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const setupReorderLevelToggle = (modal) => {
                    const reorderInput = modal.querySelector('input[name="itm_reorder_level"]');
                    // Select the visible checkbox specifically
                    const checkbox = modal.querySelector('input[name="itm_reorder_flag"][type="checkbox"]');

                    // We don't strictly need hiddenInput anymore, but we'll keep the check for safety
                    const hiddenInput = modal.querySelector('input[type="hidden"][name="itm_reorder_flag"]');
                    const form = modal.querySelector('form');

                    if (!reorderInput || !checkbox || !hiddenInput) return;

                    // --- Simplified Logic: ONLY Manage UI State (Enabled/Disabled) ---
                    const updateState = () => {
                        if (checkbox.checked) {
                            reorderInput.disabled = false; // ENABLE reorder input
                            if (reorderInput.value === '0') {
                                reorderInput.value = ''; // clear 0 for user input
                            }
                        } else {
                            reorderInput.disabled = true; // DISABLE reorder input
                            reorderInput.value = 0; // force 0 when disabled/flag is No
                        }
                        // DANGER: We no longer manipulate the hiddenInput.value or checkbox.checked here.
                        // The HTML/Blade handles the value submission automatically.
                    };

                    // Ensure reorder level is 0 if checkbox checked but field empty on submit
                    if (form && !form.dataset.reorderListenerAdded) {
                        form.addEventListener('submit', () => {
                            if (checkbox.checked && (reorderInput.value === '' || reorderInput.value ===
                                    null)) {
                                reorderInput.value = 0;
                            }
                        });
                        form.dataset.reorderListenerAdded = true;
                    }

                    if (!checkbox.dataset.listenerAdded) {
                        checkbox.addEventListener('change', updateState);
                        checkbox.dataset.listenerAdded = true;
                    }

                    // Initialize the UI state based on the initial state set by Blade
                    updateState();
                };

                // Apply for all relevant modals (Note: using a better selector for update modals)
                document.querySelectorAll('.modal[id^="ItemUpdateModalCenter"], .modal[id^="ItemSaveModalCenter"]')
                    .forEach(modal => {
                        modal.addEventListener('shown.bs.modal', e => setupReorderLevelToggle(e.currentTarget));
                    });

                // Auto-focus logic remains the same
                document.getElementById('ItemSaveModalCenter')?.addEventListener('shown.bs.modal', () =>
                    document.querySelector('#ItemSaveModalCenter input[name="itm_code"]')?.focus()
                );
            });
        </script>
        <script>
            function syncStock(value) {
                document.getElementById('itm_book_stock').value = value;
            }
        </script>

        <script>
            function syncUpdateStock(value, itemId) {
                // itemId eka use karala hariyata unique hidden field eka select karagannawa
                document.getElementById('itm_book_stock_' + itemId).value = value;
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const saveModal = document.getElementById('ItemSaveModalCenter');
                saveModal.addEventListener('shown.bs.modal', function() {
                    document.getElementById('itm_code').focus();
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const grpsaveModal = document.getElementById('GroupSaveModalCenter');
                grpsaveModal.addEventListener('shown.bs.modal', function() {
                    document.getElementById('grp_name').focus();
                });
            });
        </script>
        <style>
            #itm_subgroup option[disabled] {
                display: none;
            }
        </style>

        <style>
            #itm_unit_of_measure option[disabled] {
                display: none;
            }

            .tab-content {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
        </style>
        <!-- End Page Content -->
