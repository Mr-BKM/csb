<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2 class="center">{{ $title }}</h2>
    <p class="right">Date: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Stock Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->itm_name }}</td>
                    <td class="center">{{ $item->itm_stock }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
