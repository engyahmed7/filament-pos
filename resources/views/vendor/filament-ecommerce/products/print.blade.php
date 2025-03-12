<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Product without vendor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: white;
            padding: 20px;
        }

        @media print {
            body {
                padding: 0;
                font-size: 11pt;
            }

            .no-print {
                display: none !important;
            }

            tr {
                page-break-inside: avoid;
            }

            @page {
                margin: 1cm;
                size: portrait;
            }

            @page :first {
                margin-top: 2cm;
            }

            th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        header {
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 15px;
        }

        h1 {
            font-size: 24pt;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .print-date {
            font-style: italic;
            color: #666;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 2px solid #ddd;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
            font-size: 12pt;
        }

        .barcode-col {
            font-family: monospace;
            letter-spacing: 1px;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .print-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            display: block;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <header>
        <h1>Product Details</h1>
        <div class="print-date">Printed on: <span id="current-date"></span></div>
    </header>

    <button class="print-button no-print" onclick="window.print()">Print Product</button>

    <table>
        <thead>
            <tr>
                <th style="width: 70%">Product Name</th>
                <th style="width: 30%">Barcode</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $model->name }}</td>
                <td class="barcode-col">{{ $model->barcode }}</td>
            </tr>
        </tbody>
    </table>

    <footer>
        <div>© Your Company Name - Product Specification</div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);

            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>

</html>