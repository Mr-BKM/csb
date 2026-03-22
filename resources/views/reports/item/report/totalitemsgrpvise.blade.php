@extends('layouts.orderprint')

@section('content')
    <style>
        /* ============================================================
                            Print Styles (A4 Portrait Layout)
                        ============================================================ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 5mm 5mm 5mm !important;
                /* Top, Right, Bottom, Left */
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

        /* Sticky Container Fix */
        @media screen {
            .sticky-header-container {
                position: -webkit-sticky;
                position: sticky;
                /* Dashboard eke top bar ekata wada yata thiyenna 70px wage danna */
                top: 70px;
                z-index: 1020;
                background-color: #f4f7fa;
                /* padding-top: 10px; */
                /* padding-bottom: 10px; */
            }

            .sticky-header-container .card {
                border: 1px solid #5d7186 !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
                background: #fff !important;
            }
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 1px;
            /* Overwritten from 20px to match your specific setting */
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
            display: inline-flex;
            /* Combined flex and inline-block logic */
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

        .report-main-card {
            position: relative;
            z-index: 1;
            margin-top: 5px;
        }

        /* Layout Adjustments */
        .container-fluid>.card.no-print {
            margin-bottom: 10px !important;
        }

        .container-fluid>.card:not(.no-print) {
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

        /* Hover effects */
        .btn-custom:hover {
            transform: translateY(-2px);
            /* Click karanna kalin poddak uda yanawa */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            filter: brightness(1.1);
        }

        /* Specific Colors */
        .btn-word {
            background: linear-gradient(135deg, #1b64d1 0%, #39a9dc 100%);
            /* Word Blue */
        }

        .btn-excel {
            background: linear-gradient(135deg, #23a15c 0%, #0de982 100%);
            /* Excel Green */
        }

        .btn-print {
            background: linear-gradient(135deg, #4b4b4b 0%, #7d7d7d 100%);
            /* Professional Gray */
        }
    </style>

    <div class="container-fluid">
        <div class="sticky-header-container no-print">
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center justify-content-between">

                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                            <form action="{{ route('itemsreport.showData') }}" method="GET" id="reportForm"
                                class="d-flex align-items-center gap-2 w-100">

                                <div style="flex: 1;">
                                    <select class="form-control form-select-sm" data-choices name="report_type"
                                        id="choices-report-type">
                                        <option value="C_I_S_Report"
                                            {{ request('report_type') == 'C_I_S_Report' ? 'selected' : '' }}>
                                            1. Current Item Stock Report
                                        </option>
                                        <option value="G_V_I_S_Report"
                                            {{ request('report_type') == 'G_V_I_S_Report' || !request('report_type') ? 'selected' : '' }}>
                                            2. Group Vise Item Stock Report
                                        </option>
                                        <option value="I_B_S_Report"
                                            {{ request('report_type') == 'I_B_S_Report' ? 'selected' : '' }}>
                                            3. Item Buffer Stock Report
                                        </option>
                                    </select>
                                </div>

                                <div style="flex: 1;">
                                    <select class="form-control form-select-sm" data-choices name="group_id"
                                        id="choices-group">
                                        <option value="">All Groups</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->grp_name }}"
                                                {{ request('group_id') == $group->grp_name ? 'selected' : '' }}>
                                                {{ $group->grp_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm px-4 d-flex align-items-center"
                                    style="height: 38px; white-space: nowrap;">
                                    <iconify-icon icon="fa6-solid:eye" class="me-1"></iconify-icon> View
                                </button>
                            </form>
                        </div>

                        <div class="ms-3 d-flex gap-1">
                            <a href="{{ url('/items-export?type=word&report_type=' . request('report_type') . '&group_id=' . request('group_id')) }}"
                                class="btn-custom btn-word" title="Word">
                                <iconify-icon icon="fa6-solid:file-word"></iconify-icon>
                            </a>

                            <a href="{{ url('/items-export?type=excel&report_type=' . request('report_type') . '&group_id=' . request('group_id')) }}"
                                class="btn-custom btn-excel" title="Excel">
                                <iconify-icon icon="fa6-solid:file-excel"></iconify-icon>
                            </a>

                            <a href="javascript:window.print()" class="btn-custom btn-print" title="Print">
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
                        <h3 class="mb-0"><strong><u>Group Vise Item Stock Report</u></strong></h3>
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
                                        <th style="text-align: center;">Unit</th>
                                        <th style="text-align: center;">Stock</th>
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
                                                    Group Name: {{ $groupName ?: 'Other Items' }}
                                                </h6>
                                            </td>
                                        </tr>

                                        @foreach ($items as $item)
                                            <tr>
                                                <td style="text-align: center; font-weight: bold;">{{ $serialNumber++ }}
                                                </td>

                                                <td style="text-align: center;">{{ $item->itm_code }}</td>
                                                <td>
                                                    <strong>{{ $item->itm_name }}</strong>
                                                    @if ($item->itm_sinhalaname)
                                                        - <strong>{{ $item->itm_sinhalaname }}</strong>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">{{ $item->itm_unit_of_measure }}</td>
                                                <td style="text-align: center;">
                                                    <span
                                                        style="font-weight: bold; color: {{ $item->itm_reorder_flag == 'Yes' ? ($item->itm_stock <= $item->itm_reorder_level ? 'red' : 'green') : 'black' }};">
                                                        {{ $item->itm_stock }}
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
