@extends('layouts.orderprint')
<!-- Start right Content here -->
<!-- ==================================================== -->
@section('content')
    <style>
        /* ✅ Fix blank second page and clean print */
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            html,
            body {
                width: 210mm;
                height: auto !important;
                margin: 0;
                padding: 0;
                overflow: visible !important;
                background: none !important;
            }

            /* Remove Bootstrap card shadow/margin that causes extra space */
            .card,
            .card-body,
            .container-fluid,
            .row,
            .col-12 {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-inside: avoid !important;
            }

            /* Table fix to prevent overflow and extra height */
            table {
                page-break-inside: auto !important;
                border-collapse: collapse !important;
            }

            tr,
            td,
            th {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            /* Hide all non-print buttons or page UI */
            .btn,
            .d-print-none {
                display: none !important;
            }

            /* Prevent forced min-heights from Bootstrap */
            * {
                max-height: 100% !important;
            }
        }

        /* ✅ Optional fine-tuning for screen view */
        .card-title {
            margin-bottom: 0.3rem !important;
        }
    </style>
    <!-- Start Container Fluid -->
    <title>Order - {{ $order_id }} - {{ $orderDate }}</title>

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-1">
                            <div class="col-md-6">
                                <h4 class="card-title mb-1">ඇණවුම් අංකය: {{ $order_id }}</h4>
                                <div class="mb-2"></div>
                                <address>
                                    ඒ. එල්. එම්. සිෆාන්,<br>
                                    ගබඩා භාර කළමනාකරණ සහකාර,<br>
                                    පාරිභෝජ්‍ය ගබඩාව - B,<br>
                                    FP විෂය,<br>
                                    ශික්ෂණ රෝහල,<br>
                                    අනුරාධපුර.<br>
                                    <abbr title="Date"></abbr> {{ $orderDate }}
                                </address>

                                <address>
                                    ගණකාධිකාරි මගින්,<br>
                                    අධ්‍යක්ෂ,<br>
                                    ශික්ෂණ රෝහල,<br>
                                    අනුරාධපුර.<br>
                                </address>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <h3><b><u>ගබඩා ද්‍රව්‍ය ඇණවුම් කිරීම</u></b></h3>
                            <h4 class="text-justify" style="text-align: justify;">
                                පහත සඳහන් ඒකක සඳහා නිකුත් කිරීමට අවශ්‍ය භාණ්ඩ වලින් ප‍්‍රමාණවත් තොග පාරිභෝජ්‍ය ගබඩාව - B
                                සතුව
                                නොමැති බැවින්
                                එම භාණ්ඩ හා ඉදිරි වසරක කාලයක් සඳහා පාරිභෝජ්‍ය ගබඩාව - B හට අවශ්‍ය නඩත්තු ගබඩා ද්‍රව්‍ය,
                                ගබඩාව
                                වෙත සපයා දීම
                                සඳහා අවශ්‍ය කටයුතු සලසා දෙන ලෙස කාරුණිකව ඉල්ලා සිටිමි. </h4>
                        </div>


                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive table-borderless text-nowrap mt-3 table-centered">
                                    <table class="table mb-0">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr class="border-bottom">
                                                <th class="border-0 py-2">අනු අංක</th>
                                                <th class="border-0 py-2">ද්‍රව්‍ය</th>
                                                <th class="border-0 py-2">විස්තරය</th>
                                                <th class="border-0 py-2" style="text-align: center;">ගබඩා ශේෂය</th>
                                                <th class="border-0 py-2" style="text-align: center;">මිනුම් ඒකකය</th>
                                                <th class="border-0 py-2" style="text-align: center;">ඉල්ලා සිටින ප‍්‍රමාණය
                                                </th>
                                            </tr>
                                        </thead> <!-- end thead -->

                                        <tbody>
                                            @foreach ($orderDetails as $index => $orderm)
                                                <tr class="border-bottom">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $orderm->item->itm_name ?? ($orderm->itm_name ?? 'N/A') }}</td>
                                                    <td>{{ $orderm->item->itm_barcode ?? ($orderm->itm_barcode ?? 'N/A') }}
                                                    </td>
                                                    <td style="text-align: center;">
                                                        {{ $orderm->item->itm_stock ?? ($orderm->itm_stockinhand ?? 0) }}
                                                    </td>
                                                    <td style="text-align: center;">
                                                        {{ $orderm->item->itm_unit_of_measure ?? ($orderm->itm_unit_of_measure ?? '-') }}
                                                    </td>
                                                    <td style="text-align: center;">{{ $orderm->itm_qty }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table> <!-- end table -->
                                </div> <!-- end table responsive -->
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="row mt-3">
                            <address>
                                <br><br><br>.....................................<br>
                                ගබඩා භාර කළමනාකරණ සහකාර,<br>
                                පාරිභෝජ්‍ය ගබඩාව - B,<br>
                                ශික්ෂණ රෝහල,<br>
                                අනුරාධපුර.<br>
                                <abbr title="Date"></abbr> {{ $orderDate }}
                            </address>

                            <h5><br>ලියාපදිංචි සැපයුම්කරුවන් / ජාතික තරගකාරි ලංසු කැඳවීමේ ක‍්‍රමය මගින් මිල ගණන් කැඳවීමට
                                අනුමත කරමි.</h5><br><br><br><br><br>

                            <address>
                                .....................................<br>
                                අධ්‍යක්ෂ,<br>
                                ශික්ෂණ රෝහල,<br>
                                අනුරාධපුර.<br>
                            </address>
                        </div> <!-- end row -->

                        <div class="mt-5 mb-1">
                            <div class="text-end d-print-none">
                                <a href="javascript:window.print()" class="btn btn-primary">Print</a>
                            </div>
                        </div>

                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>
@endsection
