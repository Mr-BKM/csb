@extends('layouts.itemreceived')

@section('content')
    <div class="container-fluid">

        {{-- Session Messages and Validation Errors --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" id="msg-alert">
                <strong>There were some problems with your input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <script>
            // Auto-close after 4 seconds
            setTimeout(() => {
                const closeAlert = (id) => {
                    const alertEl = document.getElementById(id);
                    if (alertEl && typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                        bootstrap.Alert.getOrCreateInstance(alertEl).close();
                    }
                };
                closeAlert('success-alert');
                closeAlert('error-alert');
                closeAlert('msg-alert');
            }, 4000); // 4 seconds
        </script>

        {{-- Order Confirmation Card --}}
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Order Received</h5>

                        {{-- Grid.js Table Container --}}
                        <div id="table-gridjs"></div>

                        {{-- Toast Notification --}}
                        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                            <div id="actionToast" class="toast align-items-center text-bg-primary border-0" role="alert"
                                aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body" id="toastMessage">Action message</div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Grid.js and Action Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            let addedItems = [];

            const data = [
                @foreach ($orderms as $index => $orderm)
                    [
                        "{{ $loop->iteration }}",
                        "{{ $orderm->order_date }}",
                        "{{ $orderm->order_id }}",
                        "{{ $orderm->cus_name }}",
                        "{{ $orderm->itm_code }}",
                        gridjs.html(`{!! $orderm->item->itm_name ?? '-' !!}`),
                        "{{ $orderm->itm_qty }}",
                        "{{ $orderm->po_date }}",
                        "{{ $orderm->po_number }}",
                        "{{ $orderm->sup_name }}",
                        "{{ $orderm->received->sum('itm_res_qty') ?: '-' }}",
                        gridjs.html(
                            `<div style="display: inline-flex; align-items: center; gap: 6px;">
                                <button class="btn btn-sm btn-info update-btn" data-id="{{ $orderm->id }}" title="Update Order">
                                    <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $orderm->id }}" title="Cancel Order">
                                    <i class="fas fa-times"></i>
                                    </button>
                                        </div>`
                        )
                    ]
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            ];

            const grid = new gridjs.Grid({
                columns: [{
                        name: "No",
                        width: "60px",
                        sort: false
                    },
                    "Order Date",
                    "Order ID",
                    "Customer Name",
                    "Item Code",
                    "Item Name",
                    "Item QTY",
                    "PO Date",
                    "PO Number",
                    "Supplier Name",
                    "Rec.QTY",
                    {
                        name: "Update",
                        sort: false,
                        width: "50px"
                    },
                ],
                data,
                search: true,
                sort: true,
                pagination: {
                    enabled: true,
                    limit: 10
                },
                className: {
                    table: 'table table-bordered table-hover mb-0',
                    th: 'bg-light text-center',
                    td: 'text-center align-middle'
                },
                language: {
                    search: {
                        placeholder: 'Search Orders...'
                    }
                },
                html: true
            });

            grid.render(document.getElementById("table-gridjs"));

            // Toast function
            function showToast(message, type = 'primary') {
                const toastEl = document.getElementById('actionToast');
                const toastMsg = document.getElementById('toastMessage');
                toastEl.className = `toast align-items-center text-bg-${type} border-0`;
                toastMsg.textContent = message;
                if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                    new bootstrap.Toast(toastEl).show();
                }
            }

            // Function to attach listeners to the new action icons
            function attachActionIconListeners() {
                // Attach listener for the Update Icon
                document.querySelectorAll(".update-btn").forEach(btn => {
                    btn.onclick = (e) => {
                        e.preventDefault();
                        const id = btn.dataset.id;

                        const modalEl = document.getElementById(`UpdateModalCenter${id}`);
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show(); // ✅ Open the modal

                        // showToast(`Opening Update Modal for Order ID: ${id}`, 'info');
                    };
                });

                // Attach listener for the Cancel Icon
                document.querySelectorAll(".cancel-btn").forEach(btn => {
                    btn.onclick = (e) => {
                        e.preventDefault();
                        const id = btn.dataset.id;
                        // Placeholder: Implement actual cancellation logic (e.g., AJAX POST) here
                        const modalEl = document.getElementById(`deleteModal${id}`);
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    };
                });
            }

            // Attach listeners after the grid is rendered or updated
            grid.on('ready', attachActionIconListeners);
            grid.on('updated', attachActionIconListeners);

        });
    </script>
    @foreach ($orderms as $orderm)
        <!-- ✅ Update Modal -->
        <div class="modal fade" id="UpdateModalCenter{{ $orderm->id }}" tabindex="-1"
            aria-labelledby="UpdateModalLabel{{ $orderm->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('itemreceived.update') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Enter Received Item Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Item Name :</label>
                                <input type="text" class="form-control" name="po_number"
                                    value="{{ $orderm->item->itm_name }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">QTY :</label>
                                <input type="text" class="form-control" name="po_number" value="{{ $orderm->itm_qty }}"
                                    disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Supplier :</label>
                                <input type="text" class="form-control" name="po_number" value="{{ $orderm->sup_name }}"
                                    disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Received Date :</label>
                                <input type="date" class="form-control" name="itm_rec_date"
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Received QTY :</label>
                                <input type="number" step="any" class="form-control" name="itm_res_qty"
                                    id="itm_res_qty_{{ $orderm->id }}"
                                    value = "{{ $orderm->received->sum('itm_res_qty') ?: '-' }}">
                                <input type="hidden" id="itm_prev_rec_qty_{{ $orderm->id }}"
                                    value="{{ $orderm->received->sum('itm_res_qty') }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Total Rec. QTY :</label>
                                <input type="number" step="any" class="form-control" name="itm_tot_res_qty"
                                    id="itm_tot_res_qty{{ $orderm->id }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Warranty :</label>
                                <input type="text" class="form-control" name="itm_warranty" value="-"
                                    id="itm_warranty_{{ $orderm->id }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Unit Price :</label>
                                <input type="number" class="form-control" name="itm_unit_price"
                                    id="itm_unit_price_{{ $orderm->id }}" value="{{ $orderm->itm_unit_price }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Total Price :</label>
                                <input type="text" class="form-control" name="itm_tot_price"
                                    id="itm_tot_price_{{ $orderm->id }}" readonly>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <input type="hidden" id="itm_qty_{{ $orderm->id }}" value="{{ $orderm->itm_qty }}">
                                <label class="me-3 mb-0" style="width: 150px;">All QTY Received :</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="inlineRadio1_{{ $orderm->id }}" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                        id="inlineRadio2_{{ $orderm->id }}" value="option2" checked>
                                    <label class="form-check-label" for="inlineRadio2">No</label>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{ $orderm->id }}">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ✅ Cancel Modal -->
        <div class="modal fade" id="deleteModal{{ $orderm->id }}" tabindex="-1"
            aria-labelledby="UpdateModalLabel{{ $orderm->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('itemreceived.cancelorder') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Enter</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Item Name :</label>
                                <input type="text" class="form-control" name="po_number"
                                    value="{{ $orderm->item->itm_name }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">QTY :</label>
                                <input type="text" class="form-control" name="po_number"
                                    value="{{ $orderm->itm_qty }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Supplier :</label>
                                <input type="text" class="form-control" name="po_number"
                                    value="{{ $orderm->sup_name }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Received Date :</label>
                                <input type="date" class="form-control" name="itm_rec_date"
                                    value="{{ $orderm->itm_rec_date}}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Received QTY :</label>
                                <input type="number" step="any" class="form-control" name="itm_res_qty"
                                    id="update_itm_res_qty_{{ $orderm->id }}"
                                    value = "{{ $orderm->received->sum('itm_res_qty') ?: '-' }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Total Rec. QTY :</label>
                                <input type="number" step="any" class="form-control" name="itm_tot_res_qty"
                                    id="itm_tot_res_qty{{ $orderm->id }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Warranty :</label>
                                <input type="text" class="form-control" name="itm_warranty" value="-"
                                    id="itm_warranty_{{ $orderm->id }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Unit Price :</label>
                                <input type="number" class="form-control" name="itm_unit_price"
                                    id="itm_unit_price_{{ $orderm->id }}" value="{{ $orderm->itm_unit_price }}"
                                    disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Total Price :</label>
                                <input type="text" class="form-control" name="itm_tot_price"
                                    id="itm_tot_price_{{ $orderm->id }}" value="{{ $orderm->itm_tot_price }}" disabled>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{ $orderm->id }}">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Canel Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach



    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {

                const qtyInputs = document.querySelectorAll('input[id^="itm_res_qty_"]');

                qtyInputs.forEach(qtyInput => {

                    const idSuffix = qtyInput.id.replace('itm_res_qty_', '');
                    const priceInput = document.getElementById(`itm_unit_price_${idSuffix}`);
                    const totalInput = document.getElementById(`itm_tot_price_${idSuffix}`);
                    const modalEl = document.getElementById(`UpdateModalCenter${idSuffix}`);
                    const totalOrderQtyInput = document.getElementById(`itm_qty_${idSuffix}`);
                    const totalOrderQty = parseFloat(totalOrderQtyInput.value) || 0;

                    // ⭐ ADDED: Get the Total Received QTY input field (where the sum will be displayed)
                    const totalRecQtyInput = document.getElementById(`itm_tot_res_qty${idSuffix}`);

                    // ⭐ ADDED: Get the hidden input containing the historical received total
                    const prevRecQtyInput = document.getElementById(`itm_prev_rec_qty_${idSuffix}`);
                    const initialTotalReceivedQty = parseFloat(prevRecQtyInput.value) ||
                        0; // The historical sum


                    if (!priceInput || !totalInput || !totalRecQtyInput)
                        return; // Added totalRecQtyInput check

                    function calculateTotalPrice() {
                        const qty = parseFloat(qtyInput.value) || 0;
                        const cleaned = priceInput.value.replace(',', '.').replace(/[^0-9.\-]/g, '');
                        const price = parseFloat(cleaned) || 0;
                        const total = qty * price;
                        totalInput.value = total.toFixed(2);
                    }

                    // ⭐ NEW FUNCTION: Calculates and updates the running total
                    function updateTotalReceivedQty() {
                        const newlyReceivedQty = parseFloat(qtyInput.value) || 0;

                        // Calculation: Historical Sum + Newly Entered QTY
                        const newTotal = initialTotalReceivedQty + newlyReceivedQty;

                        totalRecQtyInput.value = newTotal; // Update the Total Rec. QTY field
                    }

                    qtyInput.addEventListener('input', calculateTotalPrice);
                    priceInput.addEventListener('input', calculateTotalPrice);

                    // ⭐ ADDED: Listener for the Total Rec. QTY calculation
                    qtyInput.addEventListener('input', updateTotalReceivedQty);

                    // Auto select Yes/No based on qty
                    const radioYes = document.getElementById(`inlineRadio1_${idSuffix}`);
                    const radioNo = document.getElementById(`inlineRadio2_${idSuffix}`);

                    function checkQtyStatus() {
                        // We now check the totalRecQtyInput value against the total ordered QTY
                        const receivedQty = parseFloat(totalRecQtyInput.value) || 0;

                        if (receivedQty >= totalOrderQty && receivedQty > 0) {
                            radioYes.checked = true;
                            radioNo.checked = false;
                        } else {
                            radioNo.checked = true;
                            radioYes.checked = false;
                        }
                    }

                    // ⭐ MODIFIED: Check QTY status when either the received amount changes
                    qtyInput.addEventListener('input', checkQtyStatus);

                    priceInput.addEventListener('blur', function() {
                        let v = priceInput.value.replace(',', '.');
                        let n = parseFloat(v);

                        if (!isNaN(n)) {
                            priceInput.value = n.toFixed(2);
                        } else {
                            priceInput.value = "";
                        }
                    });
                    const warrantyInput = document.getElementById(`itm_warranty_${idSuffix}`);
                    warrantyInput.addEventListener("input", function() {
                        let v = warrantyInput.value.trim().toLowerCase();

                        if (v === "l") {
                            warrantyInput.value = "Lifetime";
                            return;
                        }

                        const match = v.match(/^(\d+)\s*(y|m|d)$/);

                        if (match) {
                            const num = parseInt(match[1]);
                            const type = match[2];

                            if (type === "y") {
                                warrantyInput.value = num + " " + (num > 1 ? "Years" : "Year");
                            } else if (type === "m") {
                                warrantyInput.value = num + " " + (num > 1 ? "Months" :
                                    "Month");
                            } else if (type === "d") {
                                warrantyInput.value = num + " " + (num > 1 ? "Days" : "Day");
                            }
                        }
                    });

                    // FIX: Run all functions when modal opens
                    if (modalEl) {
                        modalEl.addEventListener('shown.bs.modal', function() {
                            calculateTotalPrice();
                            checkQtyStatus();
                        });
                    }

                });
            });
        })();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#date-picker", {
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });
        });
    </script>

    {{-- Custom Styles for Medium Side-by-Side Icons --}}
    <style>
        .update-btn,
        .cancel-btn {
            padding: 4px 8px !important;
            font-size: 12px !important;
            border-radius: 6px !important;
        }

        .update-btn i,
        .cancel-btn i {
            font-size: 13px !important;
        }


        /* Prevent row from breaking due to word wrapping */
        #table-gridjs td.gridjs-td {
            white-space: nowrap !important;
        }


        /* Optional cleanup */
        .update-btn,
        .cancel-btn {
            margin: 0 !important;
        }



        /* Remove margin from the buttons in the row */
        .update-btn.me-1 {
            margin-right: 5px !important;
            /* Ensures a gap between the two buttons */
        }

        /* Default Grid.js/Bootstrap Styles (Retained) */
        .gridjs-search input {
            padding-left: 30px !important;
            background-position: 8px center !important;
        }

        .gridjs-pages {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
        }

        .gridjs-pages button,
        .gridjs-pages span {
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #333;
            padding: 5px 12px;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .gridjs-currentPage {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection {
            height: 58px !important;
            padding-top: 1.0rem !important;
            line-height: 1.25 !important;
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection__arrow {
            height: 100% !important;
            top: 1.625rem !important;
        }
    </style>

@endsection
