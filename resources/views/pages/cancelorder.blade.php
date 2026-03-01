@extends('layouts.cancelorder')

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
                        <h5 class="card-title mb-3">Cancel Order</h5>

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
    // Initialize the data array by looping through the PHP orderms collection
    const data = [
        @foreach ($orderms as $index => $orderm)
            [
                "{{ $loop->iteration }}",
                "{{ $orderm->order_date }}",
                "{{ $orderm->order_id }}",
                "{{ $orderm->cus_name }}",
                "{{ $orderm->itm_code }}",
                gridjs.html(`{!! $orderm->item->itm_name ?? '-' !!}`), // Render item name or dash if null
                "{{ $orderm->itm_qty }}",
                gridjs.html(
                    `<div style="display: inline-flex; align-items: center; gap: 6px;">
                        <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $orderm->id }}" title="Cancel">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>`
                )
            ]
            @if (!$loop->last) , @endif
        @endforeach
    ];

    // Configure the Grid.js instance
    const grid = new gridjs.Grid({
        columns: [
            { name: "No", width: "60px", sort: false },
            "Order Date", 
            "Order ID", 
            "Customer Name", 
            "Item Code", 
            "Item Name", 
            "Item QTY",
            { name: "Cancel", sort: false, width: "50px" },
        ],
        data,
        search: true,
        sort: true,
        pagination: { enabled: true, limit: 10 },
        className: {
            table: 'table table-bordered table-hover mb-0',
            th: 'bg-light text-center',
            td: 'text-center align-middle'
        },
        language: {
            search: { placeholder: '     Search Orders...' }
        },
        html: true
    });

    // Render the grid into the specific HTML container
    grid.render(document.getElementById("table-gridjs"));

    /**
     * FIX: EVENT DELEGATION
     * Instead of attaching listeners to individual buttons (which disappear when changing pages),
     * we attach one listener to the parent container. This ensures that buttons on Page 2, 3, etc.,
     * remain functional after the grid re-renders.
     */
    document.getElementById("table-gridjs").addEventListener("click", function(e) {
        // Check if the clicked element is the cancel button or inside it
        const btn = e.target.closest(".cancel-btn");
        
        if (btn) {
            e.preventDefault();
            // Extract the unique ID from the data-id attribute
            const id = btn.dataset.id;
            // Locate the corresponding Bootstrap modal by ID
            const modalEl = document.getElementById(`deleteModal${id}`);
            
            if (modalEl) {
                // Initialize and display the Bootstrap modal
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    });
});
    </script>
    @foreach ($orderms as $orderm)
        <!-- Modal Delete-->
        <div class="modal fade" id="deleteModal{{ $orderm->id }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $orderm->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $orderm->id }}">
                            Confirm Cancellation </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <p class="mb-2 fw-semibold fs-5 text-danger">
                            ⚠️ This action is irreversible! </p>
                        <p class="mb-0 text-muted"> This
                            order will be <strong>canceled
                                immediately</strong>. <br>You
                            can't undo this action. </p>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"> Cancel
                        </button>
                        <form action="{{ route('cancelorder.updateData', $orderm->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info  btn-sm ms-2">
                                <i class="bx bx-check-circle fs-16 me-1"></i>
                                Confirm
                            </button>
                        </form>
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
