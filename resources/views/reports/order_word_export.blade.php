<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Report - {{ $order_id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .text-center {
            text-align: center;
        }

        .header-section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer-section {
            margin-top: 40px;
        }
    </style>
</head>

<body>

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
                                පහත සඳහන් ඒකක සඳහා නිකුත් කිරීමට අවශ්‍ය භාණ්ඩ වලින් ප‍්‍රමාණවත් තොග පාරිභෝජ්‍ය ගබඩාව - B
                                සතුව නොමැති බැවින් එම භාණ්ඩ ගබඩාව වෙත සපයා දීම සඳහා අවශ්‍ය කටයුතු සලසා දෙන ලෙස කාරුණිකව
                                ඉල්ලා සිටිමි. </h5>
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
                                                    <td>{{ $orderm->item->itm_name ?? ($orderm->itm_name ?? 'N/A') }}
                                                        <br>
                                                        ({{ $orderm->item->itm_sinhalaname ?? ($orderm->itm_sinhalaname ?? 'N/A') }})
                                                        - <strong>({{ $orderm->cus_name }})</strong>
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
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>

</body>

</html>
