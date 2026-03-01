@extends('layouts.issuingloan')

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
                                <h5 class="card-title mb-3">Order Confirm</h5>

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
                                                <input type="date" id="date-picker" name="issue_date"
                                                    class="form-control" placeholder="Select Date" required>
                                                <label for="date-picker">Date</label>
                                            </div>
                                        </div>

                                        <!-- PO Number -->
                                        <div class="input-group" style="width: 400px;">
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" name="issue_id" id="issue_id"
                                                    placeholder="Enter Issuing Number" required>
                                                <label for="issue_id">Issuing Number</label>
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

            document.addEventListener("click", function(e) {
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
                @foreach ($issuings as $index => $issuing)
                    [
                        "{{ $loop->iteration }}",
                        "{{ $issuing->issue_date }}",
                        "{{ $issuing->cus_name }}",
                        "{{ $issuing->itm_code }}",
                        gridjs.html(`{!! $issuing->item->itm_name ?? '-' !!}`),
                        "{{ $issuing->itm_qty }}",
                        gridjs.html(
                            `<button class="btn btn-sm btn-success add-btn" data-id="{{ $issuing->id }}" data-item-name="{{ $issuing->item->itm_name ?? 'Item' }}">Add</button>`
                        ),
                        gridjs.html(
                            `<div style="display: inline-flex; align-items: center; gap: 6px;">
                                <button class="btn btn-sm btn-info update-btn" data-id="{{ $issuing->id }}" title="Update Order">
                                    <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $issuing->id }}" title="Delete Order">
                                    <i class="fas fa-trash"></i>
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
                    "Issuing Date",
                    "Customer Name",
                    "Item Code",
                    "Item Name",
                    "Item QTY",
                    {
                        name: "Add to PO",
                        sort: false,
                        width: "10px"
                    },
                    {
                        name: "Edit QTY",
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

            const tableContainer = document.getElementById("table-gridjs");

            let isUpdating = false;

            const observer = new MutationObserver(() => {
                if (isUpdating) return;

                isUpdating = true; // stop infinite loop
                refreshAddedButtons(); // update buttons safely
                setTimeout(() => {
                    isUpdating = false;
                }, 30);
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

            document.addEventListener("click", function(e) {
                const updateBtn = e.target.closest(".update-btn");
                if (!updateBtn) return;

                e.preventDefault();

                const id = updateBtn.dataset.id;
                const modalEl = document.getElementById(`UpdateModalCenter${id}`);

                if (!modalEl) {
                    console.error("Modal not found for ID:", id);
                    return;
                }

                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });

            document.addEventListener("click", function(e) {
                const deleteBtn = e.target.closest(".delete-btn");
                if (!deleteBtn) return;

                e.preventDefault();

                const id = deleteBtn.dataset.id;
                const modalEl = document.getElementById(`deleteModal${id}`);

                if (!modalEl) {
                    console.error("Modal not found for ID:", id);
                    return;
                }

                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });

            // Finish button
            document.getElementById("finishBtn")?.addEventListener("click", () => {
                const issue_date = document.getElementById("date-picker").value;
                const issue_id = document.getElementById("issue_id").value;

                if (addedItems.length === 0) {
                    showToast("No items selected!", 'danger');
                    return;
                }

                if (!issue_date || !issue_id) {
                    showToast("Please fill in all Issue details!", 'warning');
                    return;
                }

                // Create a hidden form for redirect-based submission
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('issuingLoan.finish') }}";

                form.innerHTML = `
        @csrf
        <input type="hidden" name="issue_date" value="${issue_date}">
        <input type="hidden" name="issue_id" value="${issue_id}">
        ${addedItems.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
    `;

                document.body.appendChild(form);
                form.submit();
            });

        });
    </script>

    @foreach ($issuings as $issuing)
        <!-- ✅ Update Modal -->
        <div class="modal fade" id="UpdateModalCenter{{ $issuing->id }}" tabindex="-1"
            aria-labelledby="UpdateModalLabel{{ $issuing->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('issuingLoan.update') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Enter Received Item Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Item Name :</label>
                                <input type="text" class="form-control" name="issue_id"
                                    value="{{ $issuing->item->itm_name }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Item QTY :</label>
                                <input type="text" class="form-control" name="issue_id" value="{{ $issuing->itm_qty }}"
                                    disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <label class="me-3 mb-0" style="width: 150px;">Settled Item QTY :</label>
                                <input type="number" class="form-control settled-qty" name="settled_qty" min="1"
                                    data-max="{{ $issuing->itm_qty }}" required>
                            </div>

                            <div class="mb-2">
                                <div class="alert alert-danger d-none settled-error" role="alert"></div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{ $issuing->id }}">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Delete-->
        <div class="modal fade" id="deleteModal{{ $issuing->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $issuing->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $issuing->id }}">
                            Confirm Deletion </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"> Cancel
                        </button>
                        <a href="{{ route('issuingLoan.delete', $issuing->id) }}" class="btn btn-danger btn-sm ms-2">
                            <i class="bx bx-trash fs-16 me-1"></i>
                            Delete
                        </a>
                    </div>
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

    <script>
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('settled-qty')) return;

            const input = e.target;
            const modalBody = input.closest('.modal-body');
            const errorBox = modalBody.querySelector('.settled-error');
            const submitBtn = input.closest('form').querySelector('.update-btn');
            const submitBtn = input.closest('form').querySelector('.delete-btn');

            const maxQty = parseInt(input.dataset.max);
            const value = parseInt(input.value);

            // Reset UI
            errorBox.classList.add('d-none');
            errorBox.innerText = '';

            if (!value || value === 0) {
                errorBox.innerText = '⚠️ Settled Item Quantity cannot be zero.';
                errorBox.classList.remove('d-none');
                input.value = '';
                return;
            }

            if (value >= maxQty) {
                errorBox.innerText =
                    `⚠️ Settled Item Quantity must be LESS THAN the Item Quantity (${maxQty}).`;
                errorBox.classList.remove('d-none');
                input.value = '';
                return;
            }
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
