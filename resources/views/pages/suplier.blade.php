@extends('layouts.suplier')

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

    {{-- Script to auto-hide alerts after 4 seconds --}}
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
                    <h5 class="card-title mb-1 anchor" id="basic">Supplier Registration</h5>
                    <br>

                    {{-- Search and Create Button Header --}}
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <form action="{{ URL('/suplier') }}" method="GET" class="d-flex gap-2">
                            <div class="search-bar">
                                <span><i class="bx bx-search-alt"></i></span>
                                <input type="search" class="form-control" id="search" placeholder="Search Supplier..." name="search">
                            </div>
                        </form>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#SaveModalCenter">
                                <i class="bx bx-plus me-1"></i>Create Supplier
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    {{-- Suppliers Data Table --}}
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
                                @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                                    <td><a href="#" class="fw-medium">{{ $supplier->sup_id }}</a></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle me-2">
                                            <div><h5 class="fs-14 m-0 fw-normal">{{ $supplier->sup_name }}</h5></div>
                                        </div>
                                    </td>
                                    <td><small>{{ $supplier->sup_address }}</small></td>
                                    <td>{{ $supplier->sup_telephone }}</td>
                                    <td>{{ $supplier->sup_description }}</td>
                                    <td><span class="badge badge-soft-success">{{ $supplier->sup_status }}</span></td>
                                    <td>
                                        {{-- Edit Button --}}
                                        <button class="btn btn-sm btn-soft-secondary me-1" data-bs-toggle="modal" data-bs-target="#UpdateModalCenter{{ $supplier->id }}">
                                            <i class="bx bx-edit fs-16"></i>
                                        </button>
                                        {{-- Delete Button --}}
                                        <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $supplier->id }}">
                                            <i class="bx bx-trash fs-16"></i>
                                        </button>

                                        {{-- Modal: Update Supplier (Static backdrop prevents closing on outside click) --}}
                                        <div class="modal fade" id="UpdateModalCenter{{ $supplier->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('supplier.updateData', $supplier->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Supplier</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" value="{{ $supplier->sup_id }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="sup_name" value="{{ $supplier->sup_name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="sup_address" value="{{ $supplier->sup_address }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="sup_telephone" value="{{ $supplier->sup_telephone }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control" name="sup_description" value="{{ $supplier->sup_description }}">
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
                                        <div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">Confirm Deletion</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <p class="text-danger fs-5">⚠️ This action is irreversible!</p>
                                                        <p>Supplier <strong>{{ $supplier->sup_name }}</strong> will be <strong>deleted immediately</strong>.<br>You can't undo this action.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <a href="{{ route('supplier.deleteData', $supplier->id) }}" class="btn btn-danger btn-sm">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No suppliers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Modal: Save New Supplier (Static backdrop prevents closing on outside click) --}}
                    <div class="modal fade" id="SaveModalCenter" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('supplier.saveData') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Supplier Registration</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" value="{{ $newSupplierId }}" disabled>
                                            <input type="hidden" name="sup_id" value="{{ $newSupplierId }}">
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="sup_name" id="sup_name1" placeholder="Supplier Name*" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="sup_address" placeholder="Address*" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="sup_telephone" placeholder="Telephone*" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="sup_description" placeholder="Description">
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

                    {{-- Pagination Links --}}
                    <div class="container mt-3">
                        {{ $suppliers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Autofocus the name field when the registration modal opens --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveModal = document.getElementById('SaveModalCenter');
        saveModal.addEventListener('shown.bs.modal', function() {
            document.getElementById('sup_name1').focus();
        });
    });
</script>
@endsection