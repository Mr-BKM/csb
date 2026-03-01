@extends('layouts.customer')

@section('content')
<div class="container-fluid">

    {{-- Alert Section: Displays Success, Error, or Validation Messages --}}
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

    {{-- Script to auto-hide alerts after 4 seconds (Optimized) --}}
    <script>
        setTimeout(() => {
            const alerts = ['success-alert', 'error-alert', 'msg-alert'];
            alerts.forEach(id => {
                const alertElement = document.getElementById(id);
                if (alertElement) {
                    bootstrap.Alert.getOrCreateInstance(alertElement).close();
                }
            });
        }, 4000);
    </script>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1 anchor" id="basic">Customer Registration</h5>
                    <br>

                    {{-- Search Section & Create Button Header --}}
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <form action="{{ URL('/customer') }}" method="GET" class="d-flex gap-2">
                            <div class="search-bar">
                                <span><i class="bx bx-search-alt"></i></span>
                                <input type="search" class="form-control" id="search" placeholder="Search Customer..." name="search">
                            </div>
                        </form>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#SaveModalCenter">
                                <i class="bx bx-plus me-1"></i>Create Customer
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    {{-- Customers Data Table --}}
                    <div class="table-responsive table-centered">
                        <table class="table text-nowrap mb-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="border-0 py-2">No</th>
                                    <th class="border-0 py-2">Id</th>
                                    <th class="border-0 py-2">Name</th>
                                    <th class="border-0 py-2">Address</th>
                                    <th class="border-0 py-2">Telephone Number</th>
                                    <th class="border-0 py-2">Description</th>
                                    <th class="border-0 py-2">Status</th>
                                    <th class="border-0 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                <tr>
                                    <td>{{ $customers->firstItem() + $loop->index }}</td>
                                    <td><a href="#" class="fw-medium">{{ $customer->cus_id }}</a></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle me-2">
                                            <div><h5 class="fs-14 m-0 fw-normal">{{ $customer->cus_name }}</h5></div>
                                        </div>
                                    </td>
                                    <td><small>{{ $customer->cus_address }}</small></td>
                                    <td>{{ $customer->cus_telephone }}</td>
                                    <td>{{ $customer->cus_description }}</td>
                                    <td><span class="badge badge-soft-success">{{ $customer->cus_status }}</span></td>
                                    <td>
                                        {{-- Edit Button --}}
                                        <button class="btn btn-sm btn-soft-secondary me-1" data-bs-toggle="modal" data-bs-target="#UpdateModalCenter{{ $customer->id }}">
                                            <i class="bx bx-edit fs-16"></i>
                                        </button>
                                        {{-- Delete Button --}}
                                        <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $customer->id }}">
                                            <i class="bx bx-trash fs-16"></i>
                                        </button>

                                        {{-- Modal: Update Customer --}}
                                        <div class="modal fade" id="UpdateModalCenter{{ $customer->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('customer.updateData', $customer->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Customer</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="cus_id" value="{{ $customer->cus_id }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="cus_name" value="{{ $customer->cus_name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="cus_address" value="{{ $customer->cus_address }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="cus_telephone" value="{{ $customer->cus_telephone }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="cus_description" value="{{ $customer->cus_description }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update Now</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal: Confirm Delete --}}
                                        <div class="modal fade" id="deleteModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">Confirm Deletion</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p class="text-danger fs-5">⚠️ This action is irreversible!</p>
                                                        <p>Customer <strong>{{ $customer->cus_name }}</strong> will be <strong>deleted immediately</strong>.<br>You can't undo this action.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <a href="{{ route('customer.deleteData', $customer->id) }}" class="btn btn-danger btn-sm">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No customers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Modal: Save New Customer (Registration) --}}
                    <div class="modal fade" id="SaveModalCenter" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('customer.saveData') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Customer Registration</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" value="{{ $newCustomerId }}" disabled>
                                            <input type="hidden" name="cus_id" value="{{ $newCustomerId }}">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="cus_name" id="cus_name1" placeholder="Customer Name*" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="cus_address" placeholder="Customer Address*" value="THA" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="cus_telephone" placeholder="Telephone Number*" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="cus_description" placeholder="Description">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Register Now</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Pagination Links with Search Queries --}}
                    <div class="container mt-3">
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script: Auto-focus first input field when Create Modal opens --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveModal = document.getElementById('SaveModalCenter');
        saveModal.addEventListener('shown.bs.modal', function() {
            document.getElementById('cus_name1').focus();
        });
    });
</script>
@endsection