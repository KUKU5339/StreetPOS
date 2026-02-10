@extends('layout')

@section('content')
<style>
    .alerts-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #800000;
        margin: 0 0 5px 0;
        font-size: 28px;
    }

    .page-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    .settings-bar {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .threshold-setting {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .threshold-setting label {
        font-weight: 600;
        color: #333;
    }

    .threshold-setting input {
        width: 80px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #800000;
        color: #fff;
    }

    .btn-primary:hover {
        background: #a00000;
    }

    .btn-success {
        background: #28a745;
        color: #fff;
    }

    .btn-success:hover {
        background: #218838;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-top: 4px solid;
    }

    .stat-card.critical {
        border-top-color: #dc3545;
    }

    .stat-card.warning {
        border-top-color: #ffc107;
    }

    .stat-card.info {
        border-top-color: #17a2b8;
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .stat-label {
        color: #666;
        font-size: 13px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
    }

    .stat-card.critical .stat-value {
        color: #dc3545;
    }

    .stat-card.warning .stat-value {
        color: #ffc107;
    }

    .stat-card.info .stat-value {
        color: #17a2b8;
    }

    .products-section {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #FFD700;
    }

    .section-header h3 {
        margin: 0;
        color: #800000;
        font-size: 20px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .product-alert-card {
        border: 2px solid;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.3s;
    }

    .product-alert-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .product-alert-card.critical {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .product-alert-card.warning {
        border-color: #ffc107;
        background: #fffbf0;
    }

    .product-alert-card.info {
        border-color: #17a2b8;
        background: #f0f9fa;
    }

    .product-alert-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }

    .product-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    .stock-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-badge.critical {
        background: #dc3545;
        color: #fff;
    }

    .stock-badge.warning {
        background: #ffc107;
        color: #000;
    }

    .product-info {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .settings-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="alerts-container">
    <div class="page-header">
        <h2>⚠️ Low Stock Alerts</h2>
        <p>Monitor inventory levels and get notified before running out</p>
    </div>

    @if(session('success'))
    <div class="alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    @endif

    <!-- Settings Bar -->
    <div class="settings-bar">
        <form action="{{ route('stock-alerts.threshold') }}" method="POST" class="threshold-setting">
            @csrf
            <label>Alert Threshold:</label>
            <input type="number" name="threshold" value="{{ $threshold }}" min="1" max="100">
            <button type="submit" class="btn btn-primary">Update</button>
        </form>

        <div style="display:flex; gap:10px;">
            <a href="{{ route('stock-alerts.shopping-list') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download Shopping List
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card critical">
            <div class="stat-icon">🚨</div>
            <div class="stat-label">Out of Stock</div>
            <div class="stat-value">{{ $outOfStock->count() }}</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">⚠️</div>
            <div class="stat-label">Low Stock (≤{{ $threshold }})</div>
            <div class="stat-value">{{ $lowStockProducts->count() }}</div>
        </div>

        <div class="stat-card info">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Approaching Low Stock</div>
            <div class="stat-value">{{ $approachingLowStock->count() }}</div>
        </div>
    </div>

    <!-- Out of Stock -->
    @if($outOfStock->count() > 0)
    <div class="products-section">
        <div class="section-header">
            <h3>🚨 Out of Stock - Urgent!</h3>
        </div>

        <div class="products-grid">
            @foreach($outOfStock as $product)
            <div class="product-alert-card critical">
                <div class="product-alert-header">
                    <div class="product-name">{{ $product->name }}</div>
                    <span class="stock-badge critical">0 left</span>
                </div>
                <div class="product-info">
                    <div>Price: ₱{{ number_format($product->price, 2) }}</div>
                    <div style="color:#dc3545; font-weight:600; margin-top:5px;">
                        ⚠️ Restock immediately!
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Low Stock -->
    @if($lowStockProducts->count() > 0)
    <div class="products-section">
        <div class="section-header">
            <h3>⚠️ Low Stock Products</h3>
        </div>

        <div class="products-grid">
            @foreach($lowStockProducts as $product)
            <div class="product-alert-card warning">
                <div class="product-alert-header">
                    <div class="product-name">{{ $product->name }}</div>
                    <span class="stock-badge warning">{{ $product->stock }} left</span>
                </div>
                <div class="product-info">
                    <div>Price: ₱{{ number_format($product->price, 2) }}</div>
                    <div style="color:#856404; font-weight:600; margin-top:5px;">
                        ✓ in the shopping list
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Approaching Low Stock -->
    @if($approachingLowStock->count() > 0)
    <div class="products-section">
        <div class="section-header">
            <h3>📊 Approaching Low Stock</h3>
        </div>

        <div class="products-grid">
            @foreach($approachingLowStock as $product)
            <div class="product-alert-card info">
                <div class="product-alert-header">
                    <div class="product-name">{{ $product->name }}</div>
                    <span class="stock-badge" style="background:#17a2b8; color:#fff;">{{ $product->stock }} left</span>
                </div>
                <div class="product-info">
                    <div>Price: ₱{{ number_format($product->price, 2) }}</div>
                    <div style="color:#0c5460; font-weight:600; margin-top:5px;">
                        ℹ️ Monitor closely
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- All Good State -->
    @if($outOfStock->count() == 0 && $lowStockProducts->count() == 0 && $approachingLowStock->count() == 0)
    <div class="products-section">
        <div class="empty-state">
            <i class="fas fa-check-circle" style="color:#28a745;"></i>
            <h3 style="color:#28a745;">All Stocked Up! 🎉</h3>
            <p>All your products have sufficient stock levels.</p>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // Send notification if there are low stock items
    window.addEventListener('load', () => {
        const outOfStockCount = {{ $outOfStock->count() }};
        const lowStockCount = {{ $lowStockProducts->count() }};

        if (outOfStockCount > 0 || lowStockCount > 0) {
            // Check if we can send notifications
            if ('showNotification' in window.parent) {
                let message = '';
                if (outOfStockCount > 0) {
                    message = `⚠️ ${outOfStockCount} product(s) out of stock!`;
                } else if (lowStockCount > 0) {
                    message = `📉 ${lowStockCount} product(s) running low on stock`;
                }

                // Send notification via parent window function
                setTimeout(() => {
                    if (typeof showNotification === 'function') {
                        showNotification('Stock Alert - StreetPOS', {
                            body: message,
                            tag: 'stock-alert',
                            requireInteraction: true
                        });
                    }
                }, 1000);
            }
        }
    });
</script>
@endpush