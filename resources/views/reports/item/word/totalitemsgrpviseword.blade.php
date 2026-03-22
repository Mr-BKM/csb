<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Item Stock Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
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
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .category-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .footer-section {
            margin-top: 50px;
        }
    </style>
</head>

<body>

    <div class="header-section text-center">
        <h2 style="margin-bottom: 5px;"><u>Group Vise Item Stock Report</u></h2>
        <p style="margin: 0; font-size: 13px;">පාරිභෝජ්‍ය ගබඩාව - B | ශික්ෂණ රෝහල, අනුරාධපුර</p>
        <p style="margin: 5px 0; font-size: 10px;">Generated on: {{ date('Y-m-d H:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th class="text-center" style="width: 80px;">Code</th>
                <th>Item Name</th>
                <th class="text-center" style="width: 60px;">Unit</th>
                <th class="text-center" style="width: 60px;">Stock</th>
            </tr>
        </thead>
        <tbody>
            @php $serialNumber = 1; @endphp
            @foreach ($groupedItems as $groupName => $items)
                <tr class="category-row">
                    <td colspan="5">Category: {{ $groupName ?: 'Other Items' }}</td>
                </tr>
                @foreach ($items as $item)
                    <tr>
                        <td class="text-center">{{ $serialNumber++ }}</td>
                        <td class="text-center">{{ $item->itm_code }}</td>
                        <td><strong>{{ $item->itm_name }}</strong></td>
                        <td class="text-center">{{ $item->itm_unit_of_measure }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $item->itm_stock }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer-section">
        <p>..............................................</p>
        <p>ගබඩා භාර කළමනාකරණ සහකාර,<br>පාරිභෝජ්‍ය ගබඩාව - B</p>
    </div>

</body>

</html>
