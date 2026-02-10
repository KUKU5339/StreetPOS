<!DOCTYPE html>
<html>

<head>
    <title>Receipt #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .receipt-container {
            width: 100%;
            max-width: 320px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: 2px dashed #ddd;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .store-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .store-tagline {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }

        .receipt-info {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        .receipt-body {
            margin: 15px 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .item-row {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 11px;
        }

        .receipt-totals {
            border-top: 2px dashed #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 8px;
            margin-top: 8px;
        }

        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 11px;
        }

        .thank-you {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .footer-message {
            color: #666;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s;
        }

        .btn-print {
            background: #4CAF50;
            color: white;
        }

        .btn-print:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-pdf {
            background: #2196F3;
            color: white;
        }

        .btn-pdf:hover {
            background: #0b7dda;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-back {
            background: #9E9E9E;
            color: white;
        }

        .btn-back:hover {
            background: #757575;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }

            .action-buttons {
                display: none;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 10px;
            }

            .receipt-container {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="store-name">🍢 StreetPOS</div>
            <div class="store-tagline">Your Neighborhood Street Food</div>
            <div class="receipt-info">
                <div>Receipt #: {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div>Date: {{ $sale->created_at->format('M d, Y') }}</div>
                <div>Time: {{ $sale->created_at->format('h:i A') }}</div>
                <div>Cashier: {{ $sale->user->name ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <div class="section-title">Order Details</div>

            <div class="item-row">
                <div class="item-name">{{ $sale->product->name }}</div>
                <div class="item-details">
                    <span>{{ $sale->quantity }} × ₱{{ number_format($sale->product->price, 2) }}</span>
                    <span>₱{{ number_format($sale->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="receipt-totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₱{{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>₱{{ number_format($sale->total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="thank-you">SALAMAT PO! 🙏</div>
            <div class="footer-message">
                Thank you for your purchase!<br>
                Come again soon! 😊
            </div>
        </div>

        <!-- Action Buttons (hidden when printing) -->
        <div class="action-buttons">
            <button class="btn btn-print" onclick="window.print();">
                🖨️ Print
            </button>
            <a href="{{ route('sales.receipt.pdf', $sale->id) }}" class="btn btn-pdf">
                📄 PDF
            </a>
            <a href="{{ route('sales.quick') }}" class="btn btn-back">
                ← Back
            </a>
        </div>
    </div>

    <!-- Sale Notification -->
    @if(session('sale_notification'))
    <script>
        window.addEventListener('load', () => {
            const saleData = @json(session('sale_notification'));
            if (typeof showNotification === 'function') {
                showNotification('🎉 Sale Completed!', {
                    body: `₱${saleData.total.toFixed(2)} - ${saleData.items} item(s) sold`,
                    tag: 'sale-complete',
                    vibrate: [200, 100, 200, 100, 200]
                });
            } else if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('🎉 Sale Completed!', {
                    body: `₱${saleData.total.toFixed(2)} - ${saleData.items} item(s) sold`,
                    icon: '/icon-192.png'
                });
            }
        });
    </script>
    @endif

</body>

</html>
