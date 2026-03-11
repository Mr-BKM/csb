@extends('layouts.excessitem')
<!-- Start right Content here -->
<!-- ==================================================== --> @section('content')
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
                                <li>{{ $error }}</li> {{-- This will show your custom message --}}
                            @endforeach
                        </ul>
                    </div>
                @endif
                <script>
                    // Auto-close after 7 seconds
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
                <div class="row">
                    <div class="col">
                        <!-- Order Form -->
                        <form class="row g-2" method="POST" action="{{ route('excessitem.tempsaveData') }}">
                            @csrf
                            <div class="col-sm-7">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1 anchor" id="basic"> Manual Order Place</h5>
                                        <br>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <!-- Order ID -->
                                            <div style="width: 180px;">
                                                <input type="text" class="form-control" id="order_id"
                                                    placeholder="Order Id" value="{{ $neworderId }}" @if(!empty($neworderId)) readonly @endif required>
                                                <input type="hidden" name="order_id" value="{{ $neworderId }}">
                                            </div>
                                            <div style="width: 180px;">
                                                <input type="date" id="date-picker" name="order_date" class="form-control"
                                                placeholder="Select Date" value="{{ old('order_date', $currentOrderDate) }}" @if(!empty($currentOrderDate)) readonly @endif required>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <!-- Customer Dropdown -->
                                            <div style="flex: 1;">
                                                <select id="customer_slt" class="form-select" name="customer" required>
                                                    @if (!empty($currentCustomer))
                                                        <option value="{{ $currentCustomer->cus_id }}" selected>
                                                            {{ $currentCustomer->cus_name }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>

                                            <input type="hidden" id="cus_id" name="cus_id"
                                                value="{{ $currentCustomer->cus_id ?? '' }}">
                                            <input type="hidden" id="cus_name" name="cus_name"
                                                value="{{ $currentCustomer->cus_name ?? '' }}">


                                            <!-- Item Dropdown -->

                                            <div style="flex: 1;">
                                                <select class="form-select" name="item_slt" id="item_slt" required>
                                                    <option value="" selected>Search Item*</option>
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div style="width: 150px;">
                                                <input type="number" class="form-control" name="itm_qty" id="itm_qty"
                                                    step="any" placeholder="Enter Item Qty" required>
                                            </div>

                                            <!-- Submit Button -->
                                            <div>
                                                <button class="btn btn-primary" type="submit">Submit form</button>
                                            </div>
                                            <br><br>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- End Order Form -->

                            <!-- Item Details Side -->
                            <div class="col-sm-5">
                                <div class="card">
                                    <div class="card-body">

                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-3 d-flex align-items-center">
                                                <h5 class="card-title mb-1 anchor" id="basic">Item Details</h5>
                                            </div>

                                            <div class="col-md-9 d-flex align-items-center">
                                                <input type="hidden" class="form-control" name="itm_state" id="itm_state"
                                                    readonly="readonly" style="width: 150px; margin-right: 10px;">

                                                <span id="itm_state_status" class="font-weight-bold text-danger"></span>
                                            </div>
                                        </div>
                                        <br>

                                        <!-- Row 1: Item Code & Item Barcode -->
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label class="me-2 mb-0" style="white-space: nowrap;">Item Code:</label>
                                                <input type="text" class="form-control" name="itm_code" id="itm_code"
                                                    readonly>
                                            </div>
                                            <div class="col-md-4 d-flex align-items-center">
                                                <label class="me-2 mb-0" style="white-space: nowrap;">Book Code:</label>
                                                <input type="text" class="form-control" name="itm_book_code"
                                                    id="itm_book_code" readonly>
                                            </div>

                                            <div class="col-md-4 d-flex align-items-center">
                                                <label class="me-2 mb-0" style="white-space: nowrap;">Page Num:</label>
                                                <input type="text" class="form-control" name="itm_page_num"
                                                    id="itm_page_num" readonly>
                                            </div>
                                        </div>

                                        <!-- Row 2: Unit of Measure & Item Stock -->
                                        <div class="row mb-2 align-items-center">
                                            <div class="col-md-6 d-flex align-items-center">
                                                <label class="me-2 mb-0" style="white-space: nowrap;">Unit of
                                                    Measure:</label>
                                                <input type="text" class="form-control" name="itm_unit_of_measure"
                                                    id="itm_unit_of_measure" readonly>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center">
                                                <label class="me-2 mb-0" style="white-space: nowrap;">Stock:</label>
                                                <input type="text" class="form-control" name="itm_stockinhand"
                                                    id="itm_stockinhand" readonly>
                                            </div>
                                        </div>

                                        <!-- Hidden Fields -->
                                        <input type="hidden" name="itm_name" id="itm_name">
                                        <input type="hidden" name="itm_sinhalaname" id="itm_sinhalaname">
                                        <input type="hidden" name="itm_group" id="itm_group">
                                        <input type="hidden" name="itm_subgroup" id="itm_subgroup">
                                        <input type="hidden" name="order_typ" id="order_typ" value="excessitem">

                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- End Item Details Side -->

                        <!-- Table View -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive table-centered">
                                        <table class="table text-nowrap mb-0">
                                            <thead class="bg-light bg-opacity-50">
                                                <tr>
                                                    <th class="border-0 py-2">No</th>
                                                    <th class="border-0 py-2">Customer</th>
                                                    <th class="border-0 py-2">Item Code</th>
                                                    <th class="border-0 py-2">Item Name</th>
                                                    <th class="border-0 py-2">Unit of Measure</th>
                                                    <th class="border-0 py-2">Stock in Hand</th>
                                                    <th class="border-0 py-2">QTY</th>
                                                    <th class="border-0 py-2">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($excessitems as $excessitem)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $excessitem->cus_name }}</td>
                                                        <td>{{ $excessitem->itm_code }}</td>
                                                        <td>{{ $excessitem->item->itm_name ?? '-' }}</td>
                                                        <td>{{ $excessitem->item->itm_unit_of_measure ?? '-' }}</td>
                                                        <td>{{ $excessitem->item->itm_stock ?? '-' }}</td>
                                                        <td>{{ $excessitem->itm_qty }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-soft-secondary me-1"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#UpdateModalCenter{{ $excessitem->id }}">
                                                                <i class="bx bx-edit fs-16"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-soft-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal{{ $excessitem->id }}">
                                                                <i class="bx bx-trash fs-16"></i>
                                                            </button>
                                                            <!-- Modal Update -->
                                                            <div class="modal fade"
                                                                id="UpdateModalCenter{{ $excessitem->id }}" tabindex="-1"
                                                                aria-labelledby="SaveModalCenterTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <form method="POST"
                                                                            action="{{ route('excessitem.updateData', $excessitem->id) }}">
                                                                            @csrf <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="SaveModalCenterTitle">
                                                                                    Edit Order Item</h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="mb-3">
                                                                                    <input type="hidden"
                                                                                        class="form-control w-100"
                                                                                        name="order_id" id="order_id"
                                                                                        placeholder="ID"
                                                                                        value="{{ $excessitem->order_id }}">
                                                                                </div>

                                                                                <div class="mb-3">
                                                                                    <select class="form-select w-100"
                                                                                        name="cus_sltu"
                                                                                        id="cus_sltu_{{ $excessitem->id }}"
                                                                                        required>
                                                                                        @if ($excessitem->cus_id)
                                                                                            <option
                                                                                                value="{{ $excessitem->cus_id ?? $excessitem->cus_name }}"
                                                                                                selected="selected">
                                                                                                {{ $excessitem->cus_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    </select>
                                                                                </div>

                                                                                <input type="hidden" name="cus_id"
                                                                                    id="cus_id_{{ $excessitem->id }}"
                                                                                    value="{{ $excessitem->cus_id ?? '' }}">
                                                                                <input type="hidden" name="cus_name"
                                                                                    id="cus_name_{{ $excessitem->id }}"
                                                                                    value="{{ $excessitem->cus_name ?? '' }}">



                                                                                <div class="mb-3">
                                                                                    <select class="form-select w-100"
                                                                                        name="itm_sltu"
                                                                                        id="itm_sltu_{{ $excessitem->id }}"
                                                                                        required>
                                                                                        @if ($excessitem->itm_code)
                                                                                            <option
                                                                                                value="{{ $excessitem->itm_code ?? $excessitem->itm_name }}"
                                                                                                selected="selected">
                                                                                                {{ $excessitem->item->itm_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    </select>
                                                                                </div>

                                                                                <input type="hidden" name="itm_code"
                                                                                    id="itm_code_{{ $excessitem->id }}"
                                                                                    value="{{ $excessitem->itm_code ?? '' }}">
                                                                                <input type="hidden" name="itm_name"
                                                                                    id="itm_name_{{ $excessitem->id }}"
                                                                                    value="{{ $excessitem->item->itm_name ?? '' }}">

                                                                                <div class="mb-3">
                                                                                    <input type="number"
                                                                                        class="form-control w-100"
                                                                                        name="itm_qty" id="itm_qty"
                                                                                        placeholder="Enter Item Qty*"
                                                                                        value="{{ $excessitem->itm_qty }}"
                                                                                        required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <input type="hidden"
                                                                                        class="form-control w-100"
                                                                                        name="order_typ" id="order_typ"
                                                                                        placeholder="Name"
                                                                                        value="{{ $excessitem->order_typ }}">
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <input type="hidden"
                                                                                        class="form-control w-100"
                                                                                        name="order_date" id="order_date"
                                                                                        placeholder="Name"
                                                                                        value="{{ $excessitem->order_date }}">
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
                                                            <div class="modal fade" id="deleteModal{{ $excessitem->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="deleteModalLabel{{ $excessitem->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <!-- Modal Header -->
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title fw-bold text-danger"
                                                                                id="deleteModalLabel{{ $excessitem->id }}">
                                                                                Confirm Deletion </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <!-- Modal Body -->
                                                                        <div class="modal-body text-center">
                                                                            <p class="mb-2 fw-semibold fs-5 text-danger">
                                                                                ⚠️ This action is irreversible! </p>
                                                                            <p class="mb-0 text-muted"> This
                                                                                supplier will be <strong>deleted
                                                                                    immediately</strong>. <br>You
                                                                                can't undo this action. </p>
                                                                        </div>
                                                                        <!-- Modal Footer -->
                                                                        <div
                                                                            class="modal-footer d-flex justify-content-end">
                                                                            <button type="button"
                                                                                class="btn btn-secondary btn-sm"
                                                                                data-bs-dismiss="modal"> Cancel
                                                                            </button>
                                                                            <a href="{{ route('excessitem.deleteData', $excessitem->id) }}"
                                                                                class="btn btn-danger btn-sm ms-2">
                                                                                <i class="bx bx-trash fs-16 me-1"></i>
                                                                                Delete
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">No
                                                            Orders found. </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Table View -->
                          @if ($excessitems->isNotEmpty())
                            <div class="col-12 text-end mb-3 d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#finishOrderModal{{ $neworderId }}">
                                    Finish Order
                                </button>

                                <!-- Finish Order Confirmation Modal -->
                                <div class="modal fade" id="finishOrderModal{{ $neworderId }}" tabindex="-1"
                                    aria-labelledby="finishOrderLabel{{ $neworderId }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-success"
                                                    id="finishOrderLabel{{ $neworderId }}">
                                                    Confirm Finish Order
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body text-center">
                                                <p class="mb-2 fw-semibold fs-5">✅ Are you sure you want to
                                                    finish this order?</p>
                                                <p class="mb-0 text-muted">Once confirmed, this order will be marked as
                                                    <strong>completed</strong>.
                                                </p>
                                            </div>

                                            <!-- Modal Footer -->
                                            <div class="modal-footer d-flex justify-content-end">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Cancel</button>

                                                <form action="{{ route('excessitem.finishOrder') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $neworderId }}">
                                                    <button type="submit" class="btn btn-success btn-sm ms-2">
                                                        <i class="bx bx-check-circle fs-16 me-1"></i> Confirm
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
                <!-- end row -->
    </div>
<!-- End Container Fluid --> @endsection

<style>
    #customer_slt+.select2-container .select2-selection--single {
        height: 39px;
    }

    #customer_slt+.select2-container .select2-selection__rendered {
        line-height: 26px;
    }

    #item_slt+.select2-container .select2-selection--single {
        height: 39px;
    }

    #item_slt+.select2-container .select2-selection__rendered {
        line-height: 26px;
    }

    .form-control,
    .form-select {
        color: #000000 !important;
    }
</style>

<!-- ==================================================== -->
<!-- End Page Content -->
