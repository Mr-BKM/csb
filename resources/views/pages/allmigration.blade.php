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
                            📂 Data Migration – Excel Import
                        </h5>

                        <p class="text-muted mb-3">
                            Select the data category and upload the corresponding Excel file. 
                            Please ensure the Excel format matches the required structure for each type.
                        </p>

                        {{-- Instructions --}}
                        <div class="alert alert-info">
                            <strong>General Excel Format:</strong>
                            <ul class="mb-2">
                                <li>First row must contain column headers</li>
                                <li>No merged cells</li>
                                <li>Allowed formats: <code>.xlsx</code>, <code>.xls</code></li>
                            </ul>
                            
                            <hr>

                            <strong>Required Columns (Based on Selection):</strong>
                            <div id="instruction-details" class="mt-2">
                                <p class="text-secondary small">Please select an <strong>Import Type</strong> to see the required column names.</p>
                            </div>
                        </div>

                        {{-- Upload Form --}}
                        <form action="{{ route('allmigration.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Select Import Type</label>
                                <select name="import_type" id="import_type" class="form-select" required onchange="updateInstructions()">
                                    <option value="" selected disabled>-- Choose Type --</option>
                                    <option value="group">Item Group</option>
                                    <option value="subgroup">Sub Group</option>
                                    <option value="item">Items</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" id="file-label">Select Excel File</label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Import Data Now</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Column mapping based on drop-down selection
        const columnMap = {
            'group': ['grp_id', 'grp_name'],
            'subgroup': ['sub_grp_id', 'sub_grp_name', 'grp_id'],
            'item': ['itm_code', 'itm_name', 'itm_unit', 'itm_stock', 'grp_id'],
            'customer': ['cus_id', 'cus_name', 'cus_address', 'cus_phone']
        };

        function updateInstructions() {
            const select = document.getElementById('import_type');
            const container = document.getElementById('instruction-details');
            const type = select.value;
            const columns = columnMap[type];

            if (columns) {
                let html = '<ul class="mb-0">';
                columns.forEach(col => {
                    html += `<li><code>${col}</code></li>`;
                });
                html += '</ul>';
                container.innerHTML = html;
            }
        }

        // Auto close alerts
        setTimeout(() => {
            ['success-alert', 'error-alert', 'msg-alert'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const alert = bootstrap.Alert.getOrCreateInstance(el);
                    alert.close();
                }
            });
        }, 4000);
    </script>
@endsection