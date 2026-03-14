@extends('layouts.issuingbookupdate')

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
                                <div class="mt-3 mb-3 me-3 text-end">
                                    <button id="finishBtn" class="btn btn-primary">Book Update</button>
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
                        showToast(`Page NO "${itemName}" added successfully!`, 'success');
                    } else {
                        addedItems = addedItems.filter(item => item !== id);
                        btn.classList.replace("btn-secondary", "btn-success");
                        btn.textContent = "Add";
                        showToast(`Page NO "${itemName}" removed!`, 'warning');
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
                        "{{ $issuing->issue_id }}",
                        "{{ $issuing->cus_name }}",
                        "{{ $issuing->item->itm_page_num }}",
                        "{{ $issuing->itm_code }}",
                        gridjs.html(`{!! $issuing->item->itm_name ?? '-' !!}`),
                        "{{ $issuing->itm_stockinhand }}",
                        "{{ $issuing->itm_qty }}",
                        "{{ number_format($issuing->itm_stockinhand - $issuing->itm_qty, 2) }}",
                        gridjs.html(
                            `<button class="btn btn-sm btn-success add-btn" data-id="{{ $issuing->id }}" data-item-name="{{ $issuing->item->itm_page_num ?? 'Item' }}">Add</button>`
                        ),
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
                    "Issuing H500 No",
                    "Customer Name",
                    "Page No",
                    "Item Code",
                    "Item Name",
                    "Last QTY",
                    "Item QTY",
                    "New QTY",
                    {
                        name: "Add to Update List",
                        sort: false,
                        width: "10px"
                    }
                ],
                data,
                search: true,
                sort: true,
                pagination: {
                    enabled: true,
                    limit: 16
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
            // Finish button
            document.getElementById("finishBtn")?.addEventListener("click", () => {

                if (addedItems.length === 0) {
                    showToast("No items selected!", 'danger');
                    return;
                }


                // Create a hidden form for redirect-based submission
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('issuingbookupdate.finish') }}";

                form.innerHTML = `
        @csrf
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
