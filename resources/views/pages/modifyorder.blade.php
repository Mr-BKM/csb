@extends('layouts.modifyorder')

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
                        <h5 class="card-title mb-3">Confirmed PO Modify</h5>

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
            // NOTE: addedItems array is kept for the 'Finish' button, but it won't be populated
            // by the new Update/Cancel buttons (which are for row actions, not bulk selection).
            let addedItems = [];

            const data = [
                @foreach ($orderms as $index => $orderm)
                    [
                        "{{ $loop->iteration }}",
                        "{{ $orderm->order_date }}",
                        "{{ $orderm->order_id }}",
                        "{{ $orderm->cus_name }}",
                        "{{ $orderm->itm_code }}",
                        "{{ $orderm->item->itm_name ?? '-' }}",
                        "{{ $orderm->itm_qty }}",
                        "{{ $orderm->po_date }}",
                        "{{ $orderm->po_number }}",
                        "{{ $orderm->sup_name }}",
                        gridjs.html(
                            `<div style="display: inline-flex; align-items: center; gap: 6px;">
                                <button class="btn btn-sm btn-info update-btn" data-id="{{ $orderm->id }}" title="Update Order">
                                    <i class="fas fa-pen-to-square"></i>
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
                    {
                        name: "Edit",
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
                        placeholder: '     Search Orders...'
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


            // Finish button logic (dependent on 'addedItems')
            document.getElementById("finishBtn")?.addEventListener("click", () => {
                const po_date = document.getElementById("date-picker").value;
                const po_number = document.getElementById("po_number").value;
                const sup_id = document.getElementById("sup_id").value;
                const sup_name = document.getElementById("sup_name").value;

                if (addedItems.length === 0) {
                    showToast("No items selected! Please select items for the PO.", 'danger');
                    return;
                }

                if (!po_date || !po_number || !sup_id || !sup_name) {
                    showToast("Please fill in all PO details!", 'warning');
                    return;
                }

                // Create a hidden form for submission
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('confirmorder.finish') }}";

                form.innerHTML = `
        @csrf
        <input type="hidden" name="po_date" value="${po_date}">
        <input type="hidden" name="po_number" value="${po_number}">
        <input type="hidden" name="sup_id" value="${sup_id}">
        <input type="hidden" name="sup_name" value="${sup_name}">
        ${addedItems.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
    `;

                document.body.appendChild(form);
                form.submit();
            });

        });
    </script>
    @foreach ($orderms as $orderm)
        <!-- ✅ Update Modal -->
        <div class="modal fade" id="UpdateModalCenter{{ $orderm->id }}" tabindex="-1"
            aria-labelledby="UpdateModalLabel{{ $orderm->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('modifyorder.updateData', $orderm->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Order PO</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">PO Date</label>
                                <input type="date" class="form-control" name="po_date" value="{{ $orderm->po_date }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">PO Number</label>
                                <input type="text" class="form-control"  name="po_number" value="{{ $orderm->po_number }}">
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Supplier</label>
                                <select class="form-select w-100" name="supsltu" id="sup_sltu_{{ $orderm->id }}"
                                    required>
                                    @if ($orderm->sup_id)
                                        <option value="{{ $orderm->sup_id ?? $orderm->sup_name }}" selected="selected">
                                            {{ $orderm->sup_name }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <input type="hidden" name="sup_id" id="sup_id_{{ $orderm->id }}"
                                value="{{ $orderm->sup_id ?? '' }}">
                            <input type="hidden" name="sup_name" id="sup_name_{{ $orderm->id }}"
                                value="{{ $orderm->sup_name ?? '' }}">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Flatpickr --}}
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
