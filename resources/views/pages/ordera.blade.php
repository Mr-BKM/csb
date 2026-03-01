@extends('layouts.ordera')
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
                        <form class="row g-3" method="POST" action="{{ route('ordera.processBookCode') }}">
                            @csrf
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1 anchor" id="basic"> Auto Anual Order Place </h5>
                                        <br>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div style="width: 180px;">
                                                <input type="text" class="form-control" id="order_id"
                                                    placeholder="Order Id" value="{{ $neworderId }}" disabled>
                                                <input type="hidden" name="order_id" value="{{ $neworderId }}">
                                            </div>
                                            <div class="flex: 1;">
                                                <select class="form-select w-100" name="itm_book_code" id="itm_book_code"
                                                    required>
                                                    <option value="" disabled selected>Select Book Code*</option>
                                                    <option value="E">E</option>
                                                    <option value="H">H</option>
                                                    <option value="M">M</option>
                                                    <option value="P">P</option>
                                                </select>
                                            </div>

                                            <div>
                                                <button class="btn btn-primary" type="submit"
                                                    {{ $disableSubmit == true ? 'disabled' : '' }}>
                                                    Submit form
                                                </button>
                                            </div>
                                            <div>
                                                <button class="btn btn-danger" type="button" data-bs-toggle="modal"
                                                    data-bs-target="#alldeleteModal"
                                                    {{ $disableSubmit == false ? 'disabled' : '' }}>
                                                    All Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                        <!-- End Order Form -->
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
                                            {{-- <tbody> --}}
                                            @forelse ($orderas->sortBy('itm_code') as $ordera)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $ordera->cus_name }}</td>
                                                    <td>{{ $ordera->itm_code }}</td>
                                                    <td>{{ $ordera->item->itm_name ?? '-' }}</td>
                                                    <td>{{ $ordera->item->itm_unit_of_measure ?? '-' }}</td>
                                                    <td>{{ $ordera->item->itm_stock ?? '-' }}</td>
                                                    <td>{{ $ordera->itm_qty }}</td>

                                                    <td>
                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn btn-sm btn-soft-secondary me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#UpdateModalCenter{{ $ordera->id }}">
                                                            <i class="bx bx-edit fs-16"></i>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <button type="button" class="btn btn-sm btn-soft-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{ $ordera->id }}">
                                                            <i class="bx bx-trash fs-16"></i>
                                                        </button>

                                                        <!-- ✅ Modal Update -->
                                                        <div class="modal fade" id="UpdateModalCenter{{ $ordera->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="UpdateModalLabel{{ $ordera->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <form method="POST"
                                                                        action="{{ route('ordera.updateData', $ordera->id) }}">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="UpdateModalLabel{{ $ordera->id }}">
                                                                                Update Order Item
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <div class="mb-3 d-flex align-items-center">
                                                                                <label class="me-3 mb-0"
                                                                                    style="width: 150px;">Item Name</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="itm_name"
                                                                                    value="{{ $ordera->item->itm_name ?? 'Unknown Item' }}"
                                                                                    disabled>
                                                                            </div>

                                                                            <div class="mb-3 d-flex align-items-center">
                                                                                <label class="me-3 mb-0"
                                                                                    style="width: 150px;">Stock in
                                                                                    Hand</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="itm_stock"
                                                                                    value="{{ $ordera->item->itm_stock ?? '0' }}"
                                                                                    disabled>
                                                                            </div>

                                                                            <div class="mb-3 d-flex align-items-center">
                                                                                <label class="me-3 mb-0"
                                                                                    style="width: 150px;">QTY</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="itm_qty"
                                                                                    value="{{ $ordera->itm_qty }}">
                                                                            </div>

                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Update Now</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- ✅ Modal Delete -->
                                                        <div class="modal fade" id="deleteModal{{ $ordera->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="deleteModalLabel{{ $ordera->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title fw-bold text-danger"
                                                                            id="deleteModalLabel{{ $ordera->id }}">
                                                                            Confirm Deletion
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>

                                                                    <div class="modal-body text-center">
                                                                        <p class="fw-semibold text-danger">⚠️ This action
                                                                            is irreversible!</p>
                                                                        <p class="text-muted">
                                                                            This item will be <strong>deleted
                                                                                immediately</strong>.<br>
                                                                            You can’t undo this action.
                                                                        </p>
                                                                    </div>

                                                                    <div class="modal-footer justify-content-end">
                                                                        <button type="button"
                                                                            class="btn btn-secondary btn-sm"
                                                                            data-bs-dismiss="modal">Cancel</button>
                                                                        <a href="{{ route('ordera.deleteData', $ordera->id) }}"
                                                                            class="btn btn-danger btn-sm ms-2">
                                                                            <i class="bx bx-trash fs-16 me-1"></i> Delete
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No Orders found.
                                                    </td>
                                                </tr>
                                            @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Modal Delete All -->
                                    <div class="modal fade" id="alldeleteModal" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">
                                                        Confirm Delete All Items
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p class="mb-2 fw-semibold fs-5 text-danger">⚠️ This action is
                                                        irreversible!</p>
                                                    <p class="mb-0 text-muted">
                                                        All items for order <strong>{{ $neworderId }}</strong> will be
                                                        <strong>deleted immediately</strong>.<br>
                                                        You can't undo this action.
                                                    </p>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-end">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        data-bs-dismiss="modal">Cancel</button>

                                                    <!-- Change from <a> tag to form for DELETE method -->
                                                    <form action="{{ route('ordera.alldeleteData', $neworderId) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm ms-2">
                                                            <i class="bx bx-trash fs-16 me-1"></i> Delete All
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- End Table View -->
                        @if ($orderas->isNotEmpty())
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

                                                <form action="{{ route('ordera.finishOrder') }}" method="POST"
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

<!-- ==================================================== -->
<!-- End Page Content -->
