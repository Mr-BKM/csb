@extends('layouts.issuing')
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
                        <!-- Issue Form -->
                        <form class="row g-2" method="POST" action="{{ route('issuing.tempsaveData') }}">
                            @csrf
                            <div class="col-sm-7">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1 anchor" id="basic"> Item Issuing </h5>
                                        <br>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div style="width: 180px;">
                                                <input type="date" id="date-picker" name="issue_date"
                                                    class="form-control" placeholder="Select Date"
                                                    value="{{ old('issue_date', $currentIssueDate) }}"
                                                    @if (!empty($currentIssueDate)) readonly @endif required>
                                            </div>

                                            <div style="width: 200px;">
                                                {{-- Visible input (no name, no duplicate id) --}}
                                                <input type="text" class="form-control" name="issue_id" id="issue_id"
                                                    placeholder="Issuing Number" value="{{ $issueId ?? '' }}"
                                                    @if (!empty($issueId)) readonly @endif>

                                                {{-- Hidden input (this is what submits to DB) --}}
                                                {{-- <input type="hidden" name="issue_id" value="{{ $issueId ?? '' }}"> --}}
                                            </div>

                                            {{-- <div style="width: 200px;">

                                                <input type="text" class="form-control" name="issue_id" id="issue_id"
                                                    placeholder="Issuing Number" value="{{ $issueId ?? '' }}">


                                                <input type="hidden" name="issue_id" value="{{ $issueId ?? '' }}">
                                            </div> --}}

                                            <!-- Customer Dropdown -->
                                            <div style="flex: 1;">
                                                <select id="customer_slt" class="form-select"
                                                    @if (!empty($currentCustomer)) disabled @endif>

                                                    @if ($currentCustomer)
                                                        <option value="{{ $currentCustomer['cus_id'] }}" selected>
                                                            {{ $currentCustomer['cus_name'] }}
                                                        </option>
                                                    @else
                                                        <option value="" selected disabled>Select Customer*</option>
                                                    @endif
                                                </select>
                                            </div>

                                            {{-- THESE are what validation reads --}}
                                            <input type="hidden" id="cus_id" name="cus_id"
                                                value="{{ $currentCustomer['cus_id'] ?? '' }}">
                                            <input type="hidden" id="cus_name" name="cus_name"
                                                value="{{ $currentCustomer['cus_name'] ?? '' }}">
                                        </div>
                                        <br>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">

                                            <!-- Item Dropdown -->

                                            <div style="flex: 1;">
                                                <select class="form-select" name="item_slt" id="item_slt" required>
                                                    <option value="" selected>Search Item*</option>
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div style="width: 300px;">
                                                <input type="number" class="form-control" name="itm_qty" id="itm_qty" step="any"
                                                    placeholder="Enter Item Qty" required>
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
                                        <input type="hidden" name="issue_typ" id="issue_typ" value="Running">
                                        {{-- <input type="hidden" name="issue_date" id="issue_date"
                                            value="{{ date('Y-m-d') }}"> --}}
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
                                                    <th class="border-0 py-2">Old Stock in Hand</th>
                                                    <th class="border-0 py-2">QTY</th>
                                                    <th class="border-0 py-2">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($issuings as $issuing)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $issuing->cus_name }}</td>
                                                        <td>{{ $issuing->itm_code }}</td>
                                                        <td>{{ $issuing->item->itm_name ?? '-' }}</td>
                                                        <td>{{ $issuing->item->itm_unit_of_measure ?? '-' }}</td>
                                                        <td>{{ $issuing->itm_stockinhand }}</td>
                                                        <td>{{ $issuing->itm_qty }}</td>
                                                        <td>
                                                            {{-- <button type="button"
                                                                class="btn btn-sm btn-soft-secondary me-1"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#UpdateModalCenter{{ $issuing->id }}">
                                                                <i class="bx bx-edit fs-16"></i>
                                                            </button> --}}
                                                            <button type="button" class="btn btn-sm btn-soft-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal{{ $issuing->id }}">
                                                                <i class="bx bx-trash fs-16"></i>
                                                            </button>
                                                            {{-- <!-- Modal Update -->
                                                            <div class="modal fade"
                                                                id="UpdateModalCenter{{ $orderm->id }}" tabindex="-1"
                                                                aria-labelledby="SaveModalCenterTitle" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <form method="POST"
                                                                            action="{{ route('orderm.updateData', $orderm->id) }}">
                                                                            @csrf <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="SaveModalCenterTitle">
                                                                                    Order Item Update</h5>
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
                                                                                        value="{{ $orderm->order_id }}">
                                                                                </div>

                                                                                <div class="mb-3">
                                                                                    <select class="form-select w-100"
                                                                                        name="cus_sltu"
                                                                                        id="cus_sltu_{{ $orderm->id }}"
                                                                                        required>
                                                                                        @if ($orderm->cus_id)
                                                                                            <option
                                                                                                value="{{ $orderm->cus_id ?? $orderm->cus_name }}"
                                                                                                selected="selected">
                                                                                                {{ $orderm->cus_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    </select>
                                                                                </div>

                                                                                <input type="hidden" name="cus_id"
                                                                                    id="cus_id_{{ $orderm->id }}"
                                                                                    value="{{ $orderm->cus_id ?? '' }}">
                                                                                <input type="hidden" name="cus_name"
                                                                                    id="cus_name_{{ $orderm->id }}"
                                                                                    value="{{ $orderm->cus_name ?? '' }}">



                                                                                <div class="mb-3">
                                                                                    <select class="form-select w-100"
                                                                                        name="itm_sltu"
                                                                                        id="itm_sltu_{{ $orderm->id }}"
                                                                                        required>
                                                                                        @if ($orderm->itm_code)
                                                                                            <option
                                                                                                value="{{ $orderm->itm_code ?? $orderm->itm_name }}"
                                                                                                selected="selected">
                                                                                                {{ $orderm->item->itm_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    </select>
                                                                                </div>

                                                                                <input type="hidden" name="itm_code"
                                                                                    id="itm_code_{{ $orderm->id }}"
                                                                                    value="{{ $orderm->itm_code ?? '' }}">
                                                                                <input type="hidden" name="itm_name"
                                                                                    id="itm_name_{{ $orderm->id }}"
                                                                                    value="{{ $orderm->item->itm_name ?? '' }}">

                                                                                <div class="mb-3">
                                                                                    <input type="number"
                                                                                        class="form-control w-100"
                                                                                        name="itm_qty" id="itm_qty"
                                                                                        placeholder="Enter Item Qty*"
                                                                                        value="{{ $orderm->itm_qty }}"
                                                                                        required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <input type="hidden"
                                                                                        class="form-control w-100"
                                                                                        name="order_typ" id="order_typ"
                                                                                        placeholder="Name"
                                                                                        value="{{ $orderm->order_typ }}">
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <input type="hidden"
                                                                                        class="form-control w-100"
                                                                                        name="order_date" id="order_date"
                                                                                        placeholder="Name"
                                                                                        value="{{ $orderm->order_date }}">
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
                                                            </div>--}}
                                                            <!-- Modal Delete-->
                                                            <div class="modal fade" id="deleteModal{{ $issuing->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="deleteModalLabel{{ $issuing->id }}"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <!-- Modal Header -->
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title fw-bold text-danger"
                                                                                id="deleteModalLabel{{ $issuing->id }}">
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
                                                                            <a href="{{ route('issuing.deleteData', $issuing->id) }}"
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

                        @if ($issuings->isNotEmpty())
                            <div class="col-12 text-end mb-3 d-flex justify-content-end gap-3">
                                <!-- Loan Order Button (Trigger Modal) -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#loanOrderModal{{ $issueId }}">
                                    Loan Order
                                </button>

                                <!-- Loan Order Confirmation Modal -->
                                <div class="modal fade" id="loanOrderModal{{ $issueId }}" tabindex="-1"
                                    aria-labelledby="loanOrderLabel{{ $issueId }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-warning"
                                                    id="loanOrderLabel{{ $issueId }}">
                                                    Confirm Loan Order
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="modal-body text-center">
                                                <p class="mb-2 fw-semibold fs-5">⚠️ Are you sure you want to
                                                    mark this order as LOAN?</p>
                                                <p class="mb-0 text-muted">
                                                    Once confirmed, this order will be marked as <strong>Loan</strong>.
                                                </p>
                                            </div>

                                            <!-- Modal Footer -->
                                            <div class="modal-footer d-flex justify-content-end">
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Cancel</button>

                                                <form action="{{ route('issuing.markLoan') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $issueId }}">
                                                    <button type="submit" class="btn btn-warning btn-sm ms-2">
                                                        <i class="bx bx-time-five fs-16 me-1"></i> Confirm
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#finishOrderModal{{ $issueId }}">
                                    Finish Order
                                </button>

                                <!-- Finish Order Confirmation Modal -->
                                <div class="modal fade" id="finishOrderModal{{ $issueId }}" tabindex="-1"
                                    aria-labelledby="finishOrderLabel{{ $issueId }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-success"
                                                    id="finishOrderLabel{{ $issueId }}">
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

                                                <form action="{{ route('issuing.finishOrder') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $issueId }}">
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
    @if (session('print_url'))
        <script>
            // Automatically open print view in new tab after order finishes
            window.open("{{ session('print_url') }}", "_blank");
        </script>
    @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInput = document.getElementById('itm_qty');
        const stockInput = document.getElementById('itm_stockinhand');
        const form = qtyInput.closest('form');

        qtyInput.addEventListener('input', function() {
            const qty = parseFloat(qtyInput.value) || 0;
            const stock = parseFloat(stockInput.value) || 0;

            if (qty <= 0) {
                qtyInput.setCustomValidity('Quantity must be greater than 0');
            } else if (qty > stock) {
                qtyInput.setCustomValidity('Quantity cannot be greater than stock in hand');
            } else {
                qtyInput.setCustomValidity('');
            }
        });

        form.addEventListener('submit', function(e) {
            const qty = parseFloat(qtyInput.value) || 0;
            const stock = parseFloat(stockInput.value) || 0;

            if (qty <= 0) {
                e.preventDefault();
                alert('❌ Quantity must be greater than 0');
                qtyInput.focus();
                return;
            }

            if (qty > stock) {
                e.preventDefault();
                alert('❌ Quantity cannot be greater than stock in hand');
                qtyInput.focus();
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('issue_typ_1');
        const hidden = document.getElementById('issue_typ_1_hidden');

        // Sync on change
        select.addEventListener('change', function() {
            hidden.value = this.value;
        });

        // Sync on load (important when editing / old data)
        hidden.value = select.value;
    });
</script>


<!-- ==================================================== -->
<!-- End Page Content -->
