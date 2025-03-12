<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print All Products</title>
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

            .label {
                page-break-inside: avoid;
            }

            @page {
                margin: 1cm;
                size: portrait;
            }

            @page :first {
                margin-top: 2cm;
            }

            .label {
                border: 1px solid #000 !important;
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

        .label-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .label {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .product-name {
            /* font-weight: bold; */
            font-size: 14pt;
            text-align: center;
            /* margin-bottom: 9px; */
            color: #2c3e50;
            /* height: 30px; */
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
            hyphens: auto;
        }

        .barcode {
            /* margin: 5px 0; */
            /* padding: 5px; */
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            width: 100%;
            text-align: center;
        }

        .barcode img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .barcode-number {
            font-family: monospace;
            font-size: 12pt;
            letter-spacing: 2px;
            text-align: center;
            /* margin-top: 8px;
            padding: 5px; */
            /* background-color: #f8f9fa; */
            border-radius: 4px;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
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
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #2980b9;
        }

        .settings-panel {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .settings-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .settings-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .settings-option {
            margin-bottom: 5px;
        }

        label {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Product Barcode Labels</h1>
        <div class="print-date">Printed on: <span id="current-date"></span></div>
    </header>

    <div class="settings-panel no-print">
        <div class="settings-title">Label Settings:</div>
        <div class="settings-options">
            <div class="settings-option">
                <input type="checkbox" id="show-name" checked>
                <label for="show-name">Show Product Name</label>
            </div>
            <div class="settings-option">
                <input type="checkbox" id="show-price" checked>
                <label for="show-price">Show Product Price</label>
            </div>
            <div class="settings-option">
                <input type="checkbox" id="show-barcode-number" checked>
                <label for="show-barcode-number">Show Barcode Number</label>
            </div>
            <div class="settings-option">
                <label for="labels-per-row">Labels per row:</label>
                <select id="labels-per-row">
                    <option value="2">2</option>
                    <option value="3" selected>3</option>
                    <option value="4">4</option>
                </select>
            </div>
        </div>
    </div>

    <button class="print-button no-print" onclick="window.print()">Print Barcode Labels</button>

    <div class="label-grid">
        @foreach ($products as $product)
        <div class="label">
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">{{ $product->price }} </div>
            <div class="barcode">
                <img src="data:image/png;base64,{{ base64_encode((new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($product->barcode, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128)) }}" alt="Barcode">
            </div>
            <div class="barcode-number">{{ $product->barcode }}</div>
        </div>
        @endforeach
    </div>

    <footer>
        <div>Page <span class="pageNumber"></span> of <span class="pageCount"></span></div>
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

            const showNameCheckbox = document.getElementById('show-name');
            const showPriceCheckbox = document.getElementById('show-price');
            const showBarcodeNumberCheckbox = document.getElementById('show-barcode-number');
            const labelsPerRowSelect = document.getElementById('labels-per-row');

            showNameCheckbox.addEventListener('change', function() {
                const productNames = document.querySelectorAll('.product-name');
                productNames.forEach(name => {
                    name.style.display = this.checked ? 'flex' : 'none';
                });
            });

            showPriceCheckbox.addEventListener('change', function() {
                const productPrices = document.querySelectorAll('.product-price');
                productPrices.forEach(name => {
                    name.style.display = this.checked ? 'flex' : 'none';
                });
            });

            showBarcodeNumberCheckbox.addEventListener('change', function() {
                const barcodeNumbers = document.querySelectorAll('.barcode-number');
                barcodeNumbers.forEach(number => {
                    number.style.display = this.checked ? 'block' : 'none';
                });
            });

            labelsPerRowSelect.addEventListener('change', function() {
                const labelGrid = document.querySelector('.label-grid');
                const columnsCount = this.value;
                labelGrid.style.gridTemplateColumns = `repeat(${columnsCount}, 1fr)`;
            });

            const labelGrid = document.querySelector('.label-grid');
            labelGrid.style.gridTemplateColumns = `repeat(${labelsPerRowSelect.value}, 1fr)`;

            setTimeout(function() {
                window.print();
            }, 1000);
        });

        window.onbeforeprint = function() {
            let pageNumbers = document.querySelectorAll('.pageNumber');
            let pageCount = document.querySelectorAll('.pageCount');

            pageNumbers.forEach(element => {
                element.textContent = '1';
            });

            pageCount.forEach(element => {
                element.textContent = '1';
            });
        };
    </script>
</body>

</html>