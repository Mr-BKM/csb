@extends('layouts.confirmorder')

@section('content')
    <div class="container-fluid">

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

                {{-- Customer Section --}}
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Order PO Confirm</h5>

                                {{-- Grid.js Table --}}
                                <div id="table-gridjs"></div>

                                {{-- Toast --}}
                                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                                    <div id="actionToast" class="toast align-items-center text-bg-primary border-0"
                                        role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="d-flex">
                                            <div class="toast-body" id="toastMessage">Action message</div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Input Section --}}
                                <div class="text-left mt-3 mb-3 me-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <!-- PO Date -->
                                        <div class="input-group" style="width: 250px;">
                                            <div class="form-floating flex-grow-1">
                                                <input type="date" id="date-picker" name="po_date" class="form-control"
                                                    placeholder="Select Date" required>
                                                <label for="date-picker">Date</label>
                                            </div>
                                        </div>

                                        <!-- PO Number -->
                                        <div class="input-group" style="width: 400px;">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" name="po_number" id="po_number"
                                                    placeholder="Enter PO Number" required>
                                                <label for="po_number">PO Number</label>
                                            </div>
                                        </div>

                                        <!-- Supplier Dropdown -->
                                        <div class="input-group flex-grow-1 supplier-select2-container" style="flex: 1;">
                                            <div class="form-floating flex-grow-1">
                                                <select id="supplier_slt" class="form-select" name="supplier" required
                                                    aria-label="Supplier Name">
                                                    @if (!empty($currentSupplier))
                                                        <option value="{{ $currentSupplier->sup_id }}" selected>
                                                            {{ $currentSupplier->sup_name }}
                                                        </option>
                                                    @endif
                                                    {{-- Add other options here --}}
                                                </select>
                                                {{-- <label for="supplier_slt">Supplier Name</label> --}}
                                            </div>
                                        </div>

                                        <input type="hidden" id="sup_id" name="sup_id"
                                            value="{{ $currentSupplier->sup_id ?? '' }}">
                                        <input type="hidden" id="sup_name" name="sup_name"
                                            value="{{ $currentSupplier->sup_name ?? '' }}">

                                        <button id="finishBtn" class="btn btn-primary">Finish</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
    </div>

    {{-- JS --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let addedItems = [];

            document.addEventListener("click", function (e) {
        if (e.target.classList.contains("add-btn")) {

            const btn = e.target;
            const id = btn.dataset.id;
            const itemName = btn.dataset.itemName;

            if (!addedItems.includes(id)) {
                addedItems.push(id);
                btn.classList.replace("btn-success", "btn-secondary");
                btn.textContent = "Added";
                showToast(`"${itemName}" added successfully!`, 'success');
            } else {
                addedItems = addedItems.filter(item => item !== id);
                btn.classList.replace("btn-secondary", "btn-success");
                btn.textContent = "Add";
                showToast(`"${itemName}" removed!`, 'warning');
            }
        }
    });

    function refreshAddedButtons() {
        document.querySelectorAll(".add-btn").forEach(btn => {
            const id = btn.dataset.id;

            if (addedItems.includes(id)) {
                btn.classList.remove("btn-success");
                btn.classList.add("btn-secondary");
                btn.textContent = "Added";
            } else {
                btn.classList.remove("btn-secondary");
                btn.classList.add("btn-success");
                btn.textContent = "Add";
            }
        });
    }

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
                        gridjs.html(
                            `<button class="btn btn-sm btn-success add-btn" data-id="{{ $orderm->id }}" data-item-name="{{ $orderm->item->itm_name ?? 'Item' }}">Add</button>`
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
                    {
                        name: "Add to PO",
                        sort: false,
                        width: "10px"
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

            const tableContainer = document.getElementById("table-gridjs");

let isUpdating = false;

const observer = new MutationObserver(() => {
    if (isUpdating) return;

    isUpdating = true;             // stop infinite loop
    refreshAddedButtons();         // update buttons safely
    setTimeout(() => { isUpdating = false; }, 30);
});

observer.observe(tableContainer, {
    childList: true,
    subtree: true
});

            // Toast function
            function showToast(message, type = 'primary') {
                const toastEl = document.getElementById('actionToast');
                const toastMsg = document.getElementById('toastMessage');
                toastEl.className = `toast align-items-center text-bg-${type} border-0`;
                toastMsg.textContent = message;
                new bootstrap.Toast(toastEl).show();
            }

            // Add/Remove item logic
            // function attachAddButtonListeners() {
            //     document.querySelectorAll(".add-btn").forEach(btn => {
            //         const id = btn.dataset.id;
            //         const itemName = btn.dataset.itemName;

            //         if (addedItems.includes(id)) {
            //             btn.classList.replace("btn-success", "btn-secondary");
            //             btn.textContent = "Added";
            //         } else {
            //             btn.classList.replace("btn-secondary", "btn-success");
            //             btn.textContent = "Add";
            //         }

            //         btn.onclick = () => {
            //             if (!addedItems.includes(id)) {
            //                 addedItems.push(id);
            //                 btn.classList.replace("btn-success", "btn-secondary");
            //                 btn.textContent = "Added";
            //                 showToast(`"${itemName}" added successfully!`, 'success');
            //             } else {
            //                 addedItems = addedItems.filter(item => item !== id);
            //                 btn.classList.replace("btn-secondary", "btn-success");
            //                 btn.textContent = "Add";
            //                 showToast(`"${itemName}" removed!`, 'warning');
            //             }
            //         };
            //     });
            // }

            //grid.on('ready', () => setTimeout(refreshAddedButtons, 10));
//grid.on('updated', () => setTimeout(refreshAddedButtons, 10));
//grid.on('pageChanged', () => setTimeout(refreshAddedButtons, 10));

            // Finish button
            document.getElementById("finishBtn")?.addEventListener("click", () => {
                const po_date = document.getElementById("date-picker").value;
                const po_number = document.getElementById("po_number").value;
                const sup_id = document.getElementById("sup_id").value;
                const sup_name = document.getElementById("sup_name").value;

                if (addedItems.length === 0) {
                    showToast("No items selected!", 'danger');
                    return;
                }

                if (!po_date || !po_number || !sup_id || !sup_name) {
                    showToast("Please fill in all PO details!", 'warning');
                    return;
                }

                // Create a hidden form for redirect-based submission
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

    {{-- Flatpickr --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#date-picker", {
                dateFormat: "Y-m-d",
                defaultDate: new Date()
            });
        });
    </script>

    <style>
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

        .gridjs-pages button:hover:not([disabled]) {
            background-color: #f1f1f1;
            border-color: #bdbdbd;
        }

        .gridjs-pages button[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .gridjs-currentPage {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }

        .gridjs-pages button,
        .gridjs-pages span {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection {
            height: 58px !important;
            padding-top: 1.0rem !important;
            line-height: 1.25 !important;
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection__rendered {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection__arrow {
            height: 100% !important;
            top: 1.625rem !important;
        }

        .supplier-select2-container .select2-container--bootstrap-5 .select2-selection__placeholder {
            line-height: 1.25 !important;
            top: 1.625rem !important;
        }
    </style>
@endsection
