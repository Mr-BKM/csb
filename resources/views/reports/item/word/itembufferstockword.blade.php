<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Item Buffer Stock Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
        }

        .center {
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .red {
            color: red;
            font-weight: bold;
        }

        .green {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="center">
        <h2 style="margin-bottom: 0;"><strong><u>Item Buffer Stock Report</u></strong></h2>
        <p style="margin-top: 5px; font-size: 13px;">පාරිභෝජ්‍ය ගබඩාව - B | ශික්ෂණ රෝහල, අනුරාධපුර</p>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Item Name</th>
                <th>Unit</th>
                <th>Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->itm_code }}</td>
                    <td>
                        {{ $item->itm_name }}
                        @if ($item->itm_sinhalaname)
                            - {{ $item->itm_sinhalaname }}
                        @endif
                    </td>
                    <td class="text-center">{{ $item->itm_unit_of_measure }}</td>
                    <td class="text-center {{ $item->itm_stock <= $item->itm_reorder_level ? 'red' : 'green' }}">
                        {{ $item->itm_stock }}
                    </td>
                    <td class="text-center">{{ $item->itm_reorder_level }}</td>
                    <td class="text-center {{ $item->itm_status == 'ordered' ? 'green' : 'red' }}">
                        {{ $item->itm_status == 'ordered' ? 'Ordered' : 'Not ordered' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <p>..............................................</p>
        <p>ගබඩා භාර කළමනාකරණ සහකාර,<br>පාරිභෝජ්‍ය ගබඩාව - B</p>
    </div>
</body>

</html>
