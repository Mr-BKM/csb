@extends('layouts.orderprint')

@section('content')
    <style>
        /* Print Styles (Oya dapu parana layout ekata match wenna) */
        @media print {
            @page {
                size: A4 landscape;
                /* Items godak nisa landscape damme */
                margin: 7mm 5mm 5mm 15mm;
            }

            .container-fluid,
            .row,
            .card,
            .card-body {
                color: #000 !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: auto;
            }

            th {
                border: 1px solid black !important;
                background-color: #f2f2f2 !important;
                font-size: 12px !important;
                color: #000 !important;
                font-weight: bold !important;
                text-align: center;
            }

            td {
                border: 1px solid black !important;
                padding: 5px !important;
                font-size: 11px !important;
                color: black !important;
            }

            .no-print,
            .btn-group,
            .btn {
                display: none !important;
            }
        }

        /* Screen View Styles */

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            /* ඉඩ මදි නම් buttons යටට යන්න */
            gap: 10px;
            margin-bottom: 20px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 800px;
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

        .btn-group {
            margin-bottom: 1px;
            text-align: left;
        }

        .btn-custom iconify-icon {
            font-size: 20px;
            /* Icon එකේ size එක */
            line-height: 1;
            display: block;
        }

        .btn-custom {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            /* Icon එක වටේ සමාන ඉඩක් එන්න */
            height: 45px;
            padding: 0;
        }

        .btn-custom {
            padding: 8px 15px;
            border-radius: 5px;
            color: rgb(255, 255, 255);
            text-decoration: none;
            display: inline-block;
        }

        .btn-pdf {
            background-color: #5d7186;
            /* margin-right: 5px; */
        }

        .btn-word {
            background-color: #5d7186;
            /* margin-right: 5px; */
        }

        .btn-print {
            background-color: #5d7186;
        }

        .container-fluid>.card.no-print {
            margin-bottom: 10px !important;
        }

        .container-fluid>.card:not(.no-print) {
            margin-top: 0 !important;
        }

        /* Card body එකේ padding එකත් පොඩ්ඩක් අඩු කළොත් තවත් ලස්සන වෙයි */
        .card-body {
            padding: 1rem !important;
        }

        .card {
            border-radius: 15px !important;
            overflow: hidden;
            border: 1px solid #eee !important;
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
                                        <th>Item Name / විස්තරය</th>
                                        <th>Group</th>
                                        <th style="text-align: center;">Unit</th>
                                        <th style="text-align: center;">Stock</th>
                                        <th style="text-align: center;">Reorder Lvl</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                {{-- <tbody>
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
                                </tbody> --}}
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
                    <div class="col-6 text-end" style="text-align: right;">
                        <br>..............................................<br>
                        අධ්‍යක්ෂ,<br>
                        ශික්ෂණ රෝහල, අනුරාධපුර.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
