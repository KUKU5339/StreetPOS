<!DOCTYPE html>
<html>

<head>
    <title>Shopping List</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            padding: 20px;
        }

        h1 {
            color: #800000;
            border-bottom: 3px solid #FFD700;
            padding-bottom: 10px;
        }

        .header-info {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #800000;
            color: #fff;
            padding: 12px;
            text-align: left;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        .urgent {
            color: #dc3545;
            font-weight: bold;
        }

        .warning {
            color: #856404;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #800000;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="logo">StreetPOS Shopping List</div>
    <h1>Products to Restock - {{ $user->name }}</h1>

    <div class="header-info">
        <strong>Generated:</strong> {{ date('F d, Y h:i A') }}<br>
        <strong>Alert Threshold:</strong> {{ $user->default_stock_threshold }} items or less<br>
        <strong>Total Items:</strong> {{ $lowStockProducts->count() }}
    </div>

    @if($lowStockProducts->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:30%;">Product Name</th>
                <th style="width:15%;">Current Stock</th>
                <th style="width:15%;">Price</th>
                <th style="width:15%;">Status</th>
                <th style="width:20%;">Qty to Buy</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStockProducts as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $product->name }}</strong></td>
                <td style="text-align:center;">
                    <strong>{{ $product->stock }}</strong>
                </td>
                <td>P{{ number_format($product->price, 2) }}</td>
                <td class="{{ $product->stock == 0 ? 'urgent' : 'warning' }}">
                    {{ $product->stock == 0 ? 'OUT OF STOCK' : 'Low Stock' }}
                </td>
                <td style="text-align:center;">_____________</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:30px; padding:15px; background:#f8f9fa; border-left:4px solid #ffc107;">
        <strong>Notes:</strong>
        <ul style="margin:10px 0;">
            <li>Check quality before purchasing</li>
            <li>Compare prices from different suppliers</li>
            <li>Buy in bulk for better deals when possible</li>
        </ul>
    </div>
    @else
    <div style="text-align:center; color:#28a745; padding:40px; background:#e8f5e9; border-radius:8px; margin-top:20px;">
        <strong style="font-size:18px;">All products are well stocked!</strong>
        <p style="margin-top:10px; color:#666;">No items need restocking at this time.</p>
    </div>
    @endif

    <div class="footer">
        <strong>StreetPOS</strong> - Your Filipino Street Food Business Companion<br>
        Keep this list handy when shopping at the market!
    </div>
</body>

</html>