<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Current Item Stock Report</title>
    <style>
        /* Word වලට ගැලපෙන Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #000;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            text-align: center;
        }

        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
            text-transform: uppercase;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="text-center">
        <h2 class="mb-0"><strong><u>Current Item Stock Report</u></strong></h2>
        <p style="font-size: 14px; margin-top: 5px;">පාරිභෝජ්‍ය ගබඩාව - B | ශික්ෂණ රෝහල, අනුරාධපුර</p>
        <p style="font-size: 11px; color: #666;">
            Generated on: {{ date('Y-m-d H:i A') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Item Name</th>
                <th>Unit</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->itm_code }}</td>
                    <td>
                        <strong>{{ $item->itm_name }}</strong><br>
                        <span style="font-size: 10px; color: #555;">{{ $item->itm_sinhalaname }}</span>
                    </td>
                    <td class="text-center">{{ $item->itm_unit_of_measure }}</td>
                    <td class="text-center"
                        style="{{ $item->itm_stock <= $item->itm_reorder_level ? 'color: red; font-weight: bold;' : '' }}">
                        {{ $item->itm_stock }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <p>..............................................</p>
        <p>ගබඩා භාර කළමනාකරණ සහකාර,<br>
            පාරිභෝජ්‍ය ගබඩාව - B</p>
    </div>

</body>

</html>
