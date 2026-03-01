@extends('layouts.allmigration') {{-- change layout if needed --}}

@section('content')
    <div class="container-fluid">

        {{-- Alerts --}}
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
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card --}}
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="card-title mb-3">
                            📂 Item Group – Excel Import
                        </h5>

                        <p class="text-muted mb-3">
                            Upload the Excel file containing <strong>Item Group</strong> details.
                            Please ensure the Excel format matches the required structure.
                        </p>

                        {{-- Instructions --}}
                        <div class="alert alert-info">
                            <strong>Excel Format:</strong>
                            <ul class="mb-0">
                                <li>First row must contain column headers</li>
                                <li>No merged cells</li>
                                <li>Allowed formats: <code>.xlsx</code>, <code>.xls</code></li>
                            </ul>
                            <hr>
                            <strong>Required Columns:</strong>
                            <ul class="mb-0">
                                <li><code>grp_id</code></li>
                                <li><code>grp_name</code></li>
                            </ul>
                        </div>

                        {{-- Upload Form --}}
                        {{-- <form action="{{ route('group.excel.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Group Excel File</label>
                            <input type="file"
                                   name="file"
                                   class="form-control"
                                   accept=".xlsx,.xls"
                                   required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Import Group Excel
                            </button>
                        </div>
                    </form> --}}
                        <form action="{{ route('allmigration.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Import Type</label>
                                <select name="import_type" class="form-select" required>
                                    <option value="" selected disabled>-- Choose Type --</option>
                                    <option value="group">Item Group</option>
                                    <option value="subgroup">Sub Group</option>
                                    <option value="item">Items</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Excel File</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Import Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Auto close alerts --}}
    <script>
        setTimeout(() => {
            ['success-alert', 'error-alert', 'msg-alert'].forEach(id => {
                const el = document.getElementById(id);
                if (el) bootstrap.Alert.getOrCreateInstance(el).close();
            });
        }, 4000);
    </script>
@endsection
