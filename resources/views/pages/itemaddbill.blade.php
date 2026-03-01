@extends('layouts.itemaddbill')

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
                                <h5 class="card-title mb-3">Add Bill Submit Details</h5>

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
                                        <!-- Bill Date -->
                                        <div class="input-group" style="width: 250px;">
                                            <div class="form-floating flex-grow-1">
                                                <input type="date" id="date-picker" name="bill_submit_date" class="form-control"
                                                    placeholder="Select Date" required>
                                                <label for="date-picker">Date</label>
                                            </div>
                                        </div>

                                        <!-- Bill Number -->
                                        <div class="input-group" style="width: 400px;">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" name="bill_number" id="bill_number"
                                                    placeholder="Enter Bill Number" required>
                                                <label for="bill_number">Bill Number</label>
                                            </div>
                                        </div>
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
                @foreach ($orderreceiveds as $index => $orderreceived)
                    [
                        "{{ $loop->iteration }}",
                        "{{ $orderreceived->order_id }}",
                        "{{ $orderreceived->itm_code }}",
                        gridjs.html(`{!! $orderreceived->item->itm_name ?? '-' !!}`),
                        "{{ $orderreceived->itm_qty }}",
                        "{{ $orderreceived->itm_inv_numer }}",
                        gridjs.html(
                            `<button class="btn btn-sm btn-success add-btn" data-id="{{ $orderreceived->id }}" data-item-name="{{ $orderreceived->item->itm_name ?? 'Item' }}">Add</button>`
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
                    "Order ID",
                    "Item Code",
                    "Item Name",
                    "Item QTY",
                    "Invoice Number",
                    {
                        name: "Invoice",
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
            // Finish button
            document.getElementById("finishBtn")?.addEventListener("click", () => {
                const bill_submit_date = document.getElementById("date-picker").value;
                const bill_number = document.getElementById("bill_number").value;

                if (addedItems.length === 0) {
                    showToast("No items selected!", 'danger');
                    return;
                }

                if (!bill_submit_date || !bill_number) {
                    showToast("Please fill in all Bill details!", 'warning');
                    return;
                }

                // Create a hidden form for redirect-based submission
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('itemaddbill.finish') }}";

                form.innerHTML = `
        @csrf
        <input type="hidden" name="bill_submit_date" value="${bill_submit_date}">
        <input type="hidden" name="bill_number" value="${bill_number}">
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
