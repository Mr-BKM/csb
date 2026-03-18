@extends('layouts.orderprint')

@section('content')
    <style>
    /* ============================================================
       Print Styles (A4 Portrait Layout)
       ============================================================ */
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm 5mm 5mm 5mm !important; /* Top, Right, Bottom, Left */
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .container-fluid,
        .row,
        .card,
        .card-body {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            padding-top: 0 !important;
            color: #000 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
        }

        th {
            background-color: #f2f2f2 !important;
            border: 1px solid black !important;
            color: #000 !important;
            font-size: 11px !important;
            font-weight: bold !important;
            text-align: center;
        }

        td {
            border: 1px solid black !important;
            padding: 5px !important;
            font-size: 11px !important;
            color: black !important;
            word-wrap: break-word;
        }

        .no-print,
        .btn-group,
        .btn,
        .btn-custom {
            display: none !important;
        }
    }

    /* ============================================================
       Screen View Styles
       ============================================================ */
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 1px; /* Overwritten from 20px to match your specific setting */
        text-align: left;
    }

    /* Table Styles */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .report-table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Button Customization */
    .btn-custom {
        display: inline-flex; /* Combined flex and inline-block logic */
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        padding: 8px 15px;
        border-radius: 5px;
        color: rgb(255, 255, 255);
        text-decoration: none;
    }

    .btn-custom iconify-icon {
        font-size: 20px;
        line-height: 1;
        display: block;
    }

    /* Button Colors */
    .btn-pdf,
    .btn-word,
    .btn-print {
        background-color: #5d7186;
    }

    /* Layout Adjustments */
    .container-fluid > .card.no-print {
        margin-bottom: 10px !important;
    }

    .container-fluid > .card:not(.no-print) {
        margin-top: 0 !important;
    }

    .card-body {
        padding: 1rem !important;
    }

    .card {
        width: 100% !important;
        border: none !important;
        border-radius: 15px !important;
        box-shadow: none !important;
    }
</style>

    <div class="container-fluid">
        <div class="card no-print">
            <div class="card-body p-2">
                <div class="row align-items-center">
                    <div class="col-12 text-end">
                        <div class="btn-group justify-content-end w-100">

                            <a href="{{ url('/items-export?type=pdf') }}"
                                class="btn-custom btn-pdf d-flex align-items-center">
                                <iconify-icon icon="fa6-solid:file-pdf"></iconify-icon>
                            </a>

                            <a href="{{ url('/items-export?type=word') }}"
                                class="btn-custom btn-word d-flex align-items-center">
                                <iconify-icon icon="fa6-solid:file-word"></iconify-icon>
                            </a>

                            <a href="javascript:window.print()" class="btn-custom btn-print d-flex align-items-center">
                                <iconify-icon icon="fa6-solid:print"></iconify-icon>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3 class="mb-0"><strong><u>Item Stock Report</u></strong></h3>
                        <div style="line-height: 1.2; margin-top: 5px;">
                            <p class="mb-0" style="font-size: 14px;">පාරිභෝජ්‍ය ගබඩාව - B | ශික්ෂණ රෝහල, අනුරාධපුර</p>
                            <small class="text-muted" style="font-size: 12px; font-weight: bold;">
                                Generated on: {{ date('Y-m-d H:i A') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="report-table table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Code</th>
                                        <th>Item Name</th>
                                        <th>Item Name Sinhala</th>
                                        <!-- <th>Group</th> -->
                                        <th style="text-align: center;">Unit</th>
                                        <th style="text-align: center;">Stock</th>
                                        <th style="text-align: center;">Reorder Level</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $serialNumber = 1; @endphp

                                    @foreach ($groupedItems as $groupName => $items)
                                        <tr style="background-color: #f1f3f5;">
                                            <td colspan="8" style="padding: 10px; border: 1px solid black;">
                                                <h6 class="mb-0" style="font-weight: bold; color: #495057;">
                                                    <iconify-icon icon="solar:folder-with-files-bold-duotone"
                                                        class="me-2"></iconify-icon>
                                                    Category: {{ $groupName ?: 'Other Items' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        @foreach ($items as $item)
                                            <tr>
                                                <td style="text-align: center; font-weight: bold;">{{ $serialNumber++ }}
                                                </td>

                                                <td style="text-align: center;">{{ $item->itm_code }}</td>
                                                <td>
                                                    <strong>{{ $item->itm_name }}</strong><br>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $item->itm_sinhalaname }}</small>
                                                </td>
                                                <!-- <td>{{ $item->itm_group }}</td> -->
                                                <td style="text-align: center;">{{ $item->itm_unit_of_measure }}</td>
                                                <td style="text-align: center;">
                                                    <span style="font-weight: bold; color: {{ $item->itm_reorder_flag == 'Yes' ? ($item->itm_stock <= $item->itm_reorder_level ? 'red' : 'green') : 'black' }};">
                                                        {{ $item->itm_stock }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">{{ $item->itm_reorder_level }}</td>
                                                <td style="text-align: center;"> <span class="badge"
          style="color: {{ $item->itm_status == 'ordered' ? '#2ecc71' : '#e74c3c' }}; font-weight: bold;">
        
        {{ $item->itm_status == 'ordered' ? 'ORDERED' : 'NOT ORDERED' }}
        
    </span>
</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-5 d-none d-print-block" style="margin-top: 50px !important;">
                    <div class="col-6">
                        <br>..............................................<br>
                        ගබඩා භාර කළමනාකරණ සහකාර,<br>
                        පාරිභෝජ්‍ය ගබඩාව - B
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $index + 1 }}</td>
                                            <td style="text-align: center;">{{ $item->itm_code }}</td>
                                            <td>
                                                <strong>{{ $item->itm_name }}</strong><br>
                                                <small class="text-muted">{{ $item->itm_sinhalaname }}</small>
                                            </td>
                                            <td>{{ $item->itm_group }}</td>
                                            <td style="text-align: center;">{{ $item->itm_unit_of_measure }}</td>
                                            <td style="text-align: center;">
                                                <span
                                                    style="{{ $item->itm_stock <= $item->itm_reorder_level ? 'color: red; font-weight: bold;' : '' }}">
                                                    {{ $item->itm_stock }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">{{ $item->itm_reorder_level }}</td>
                                            <td style="text-align: center;">
                                                <span class="badge"
                                                    style="background-color: {{ $item->itm_status == 'active' ? '#2ecc71' : '#e74c3c' }}; color: white; padding: 2px 5px; border-radius: 3px;">
                                                    {{ strtoupper($item->itm_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>  -->
