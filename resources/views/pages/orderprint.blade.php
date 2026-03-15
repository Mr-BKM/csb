@extends('layouts.orderprint')
<!-- Start right Content here -->
<!-- ==================================================== -->
@section('content')
    <style>
        @media print {
            @page {
                size: A4;
                /*(Top, Right, Bottom, Left)*/
                margin: 7mm 5mm 5mm 15mm;
            }

            /* Content eka madi hariyen kedenna dena eka nawaththanna */
            .container-fluid,
            .row,
            .card,
            .card-body {
                color: #000000 !important;
                page-break-inside: avoid !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Table eka lassanata fit karanna */
            table {
                color: #000000 !important;
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: auto !important;
                page-break-inside: auto;
                margin-bottom: 15px !important;
            }

            th {
        
        border: 1px solid black !important;
        font-size: 15px !important;
        color: #000000 !important;
        font-weight: 800 !important;
        text-align: center;
        -webkit-print-color-adjust: exact;
    }
            td {
                border: 1px solid black !important;
                padding: 8px !important;
                font-size: 13px !important;
                color: black !important;
                white-space: normal !important;
                word-wrap: break-word !important;
            }

            tr {
                color: #000000 !important;
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Space control */
            .mt-3,
            .mt-5 {
                margin-top: 10px !important;
            }

            .mb-1,
            .mb-2 {
                margin-bottom: 5px !important;
            }

            /* Blank spaces control */
            br {
                content: "";
                display: block;
                margin: 5px 0;
            }

            .d-print-none,
            .btn {
                display: none !important;
            }
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
                                <h5 class="card-title mb-1">ඇණවුම් අංකය: {{ $order_id }}</h5>
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
                            <h4><strong><u>ගබඩා ද්‍රව්‍ය ඇණවුම් කිරීම</u></strong></h4>
                            <h5 class="text-justify" style="text-align: justify; color: black;">
                                පහත සඳහන් ඒකක සඳහා නිකුත් කිරීමට අවශ්‍ය භාණ්ඩ වලින් ප‍්‍රමාණවත් තොග පාරිභෝජ්‍ය ගබඩාව - B සතුව නොමැති බැවින් එම භාණ්ඩ ගබඩාව වෙත සපයා දීම සඳහා අවශ්‍ය කටයුතු සලසා දෙන ලෙස කාරුණිකව ඉල්ලා සිටිමි. </h5>
                        </div>


                        <div class="row">
                            <div class="col-12">
                                <div class="mt-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">අනු අංක</th>
                                                <th style="text-align: center;">ද්‍රව්‍ය හා ඒකකය</th>
                                                <th style="text-align: center;">විස්තරය</th>
                                                <th style="text-align: center;">ගබඩා ශේෂය</th>
                                                <th style="text-align: center;">මිනුම් ඒකකය</th>
                                                <th style="text-align: center;">ඉල්ලා සිටින ප්‍රමාණය</th>
                                            </tr>
                                        </thead>	
                                        <tbody>
                                            @foreach ($orderDetails as $index => $orderm)
                                                <tr>
                                                    <td style="text-align: center;">
                                                        {{ $index + 1 }}</td>
                                                    <td>{{ $orderm->item->itm_name ?? ($orderm->itm_name ?? 'N/A') }} <br> ({{ $orderm->item->itm_sinhalaname ?? ($orderm->itm_sinhalaname ?? 'N/A') }}) - <strong>({{ $orderm->cus_name }})</strong>
                                                    </td>
                                                    <td>{{ $orderm->item->itm_description ?? ($orderm->itm_description ?? 'N/A') }}
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
                                    </table>
                                </div><!-- end table -->
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                        <div class="row mt-2">
                            <div class="col-12">
                                <div style="margin-top: 40px;">
                                    <br>..............................................<br>
                                    ගබඩා භාර කළමනාකරණ සහකාර,<br>
                                    පාරිභෝජ්‍ය ගබඩාව - B,<br>
                                    ශික්ෂණ රෝහල,<br>
                                    අනුරාධපුර.<br>
                                </div>

                                <h5 style="margin-top: 20px; font-weight: bold;">
                                    ලියාපදිංචි සැපයුම්කරුවන් / ජාතික තරගකාරි ලංසු කැඳවීමේ ක‍්‍රමය මගින් මිල ගණන් කැඳවීමට
                                    අනුමත කරමි.
                                </h5>

                                <div style="margin-top: 40px;">
                                    <br>..............................................<br>
                                    අධ්‍යක්ෂ,<br>
                                    ශික්ෂණ රෝහල,<br>
                                    අනුරාධපුර.
                                </div>
                            </div>
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
