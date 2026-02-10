@extends('layout')

@section('content')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.sales::before {
        background: #4CAF50;
    }

    .stat-card.products::before {
        background: #2196F3;
    }

    .stat-card.items::before {
        background: #FF9800;
    }

    .stat-card.stock::before {
        background: #f44336;
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .stat-card.sales .stat-icon {
        color: #4CAF50;
    }

    .stat-card.products .stat-icon {
        color: #2196F3;
    }

    .stat-card.items .stat-icon {
        color: #FF9800;
    }

    .stat-card.stock .stat-icon {
        color: #f44336;
    }

    .stat-label {
        color: #666;
        font-size: 13px;
        text-transform: uppercase;
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
    }

    .stat-card.sales .stat-value {
        color: #4CAF50;
    }

    .stat-card.products .stat-value {
        color: #2196F3;
    }

    .stat-card.items .stat-value {
        color: #FF9800;
    }

    .stat-card.stock .stat-value {
        color: #f44336;
    }

    /* Section Cards */
    .section-card {
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .view-all-link {
        color: #800000;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: color 0.3s;
    }

    .view-all-link:hover {
        color: #a00000;
    }

    /* Modern Tables */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead tr {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .modern-table th {
        padding: 12px;
        text-align: left;
        color: #800000;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
    }

    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .amount {
        color: #4CAF50;
        font-weight: 600;
    }

    .date-time {
        color: #666;
        font-size: 13px;
    }

    .stock-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-low {
        background: #ffebee;
        color: #c62828;
    }

    .stock-medium {
        background: #fff3e0;
        color: #e65100;
    }

    .stock-good {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 25px;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h2>📊 Dashboard</h2>
        <p>Overview of your business performance</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card sales">
            <div class="stat-icon">💰</div>
            <p class="stat-label">Total Sales</p>
            <p class="stat-value">₱{{ number_format($totalSales, 2) }}</p>
        </div>

        <div class="stat-card products">
            <div class="stat-icon">📦</div>
            <p class="stat-label">Products</p>
            <p class="stat-value">{{ $totalProducts }}</p>
        </div>

        <div class="stat-card items">
            <div class="stat-icon">🛒</div>
            <p class="stat-label">Items Sold</p>
            <p class="stat-value">{{ $totalItemsSold }}</p>
        </div>

        <div class="stat-card stock">
            <div class="stat-icon">⚠️</div>
            <p class="stat-label">Low Stock</p>
            <p class="stat-value">{{ $lowStock->count() }}</p>
        </div>
    </div>

    <!-- Recent Sales Section -->
    <div class="section-card">
        <div class="section-header">
            <h3>
                <i class="fas fa-receipt"></i>
                Recent Sales
            </h3>
            <a href="{{ route('sales.index') }}" class="view-all-link">View All →</a>
        </div>

        @if($sales->count() > 0)
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales->take(5) as $sale)
                <tr>
                    <td class="product-name">{{ $sale->product->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="amount">₱{{ number_format($sale->total, 2) }}</td>
                    <td class="date-time">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <p>No sales recorded yet</p>
        </div>
        @endif
    </div>

    <!-- Grid Layout for Low Stock and Best Sellers -->
    <div class="grid-2">
        <!-- Low Stock Section -->
        <div class="section-card">
            <div class="section-header">
                <h3>
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Stock Items
                </h3>
                <a href="{{ route('products.index') }}" class="view-all-link">Manage →</a>
            </div>

            @if($lowStock->count() > 0)
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStock as $product)
                    <tr>
                        <td class="product-name">{{ $product->name }}</td>
                        <td>
                            <span class="stock-badge stock-low">
                                {{ $product->stock }} left
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>All products are well stocked! 🎉</p>
            </div>
            @endif
        </div>

        <!-- Best Sellers Section -->
        <div class="section-card">
            <div class="section-header">
                <h3>
                    <i class="fas fa-trophy"></i>
                    Top 5 Best Sellers
                </h3>
            </div>

            @if($topProducts->count() > 0)
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product</th>
                        <th>Total Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $index => $product)
                    <tr>
                        <td>
                            @if($index == 0)
                            🥇
                            @elseif($index == 1)
                            🥈
                            @elseif($index == 2)
                            🥉
                            @else
                            {{ $index + 1 }}
                            @endif
                        </td>
                        <td class="product-name">{{ $product->name }}</td>
                        <td><strong>{{ $product->total_sold }}</strong> items</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <p>No sales data available yet</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    // Show daily summary notification (once per day)
    window.addEventListener('load', () => {
        const lastNotification = localStorage.getItem('lastDailySummary');
        const today = new Date().toDateString();

        if (lastNotification !== today) {
            // Check if it's evening (after 6 PM) for end-of-day summary
            const hour = new Date().getHours();
            if (hour >= 18) {
                const todaySales = {{ $todaySales }};
                const todayRevenue = {{ $todayRevenue }};

                if (todaySales > 0 && typeof showNotification === 'function') {
                    setTimeout(() => {
                        showNotification('📊 Daily Sales Summary', {
                            body: `Today: ${todaySales} sales totaling ₱${todayRevenue.toFixed(2)}`,
                            tag: 'daily-summary',
                            requireInteraction: true
                        });
                        localStorage.setItem('lastDailySummary', today);
                    }, 2000);
                }
            }
        }
    });
</script>
@endpush
