@extends('layout')

@section('content')
<style>
    .reports-container {
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

    .filters-bar {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .filters-row {
        display: flex;
        gap: 15px;
        align-items: end;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .quick-filters {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .quick-filter-btn {
        padding: 10px 20px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }

    .quick-filter-btn:hover {
        background: #FFD700;
        border-color: #FFD700;
        color: #800000;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
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

    .stat-card.revenue {
        border-top-color: #4CAF50;
    }

    .stat-card.items {
        border-top-color: #2196F3;
    }

    .stat-card.transactions {
        border-top-color: #FF9800;
    }

    .stat-card.average {
        border-top-color: #9C27B0;
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
        color: #333;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        font-size: 18px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .top-products-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .product-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-rank {
        background: #FFD700;
        color: #800000;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .product-info {
        flex: 1;
        margin: 0 15px;
    }

    .product-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
    }

    .product-stats {
        font-size: 12px;
        color: #666;
    }

    .product-revenue {
        font-size: 16px;
        font-weight: bold;
        color: #4CAF50;
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
        .content-grid {
            grid-template-columns: 1fr;
        }

        .filters-row {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }
    }
</style>

<div class="reports-container">
    <div class="page-header">
        <h2>📊 Sales Report</h2>
        <p>Analyze your sales performance and trends</p>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <form action="{{ route('reports.daily-sales') }}" method="GET">
            <div class="filters-row">
                <div class="filter-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" required>
                </div>

                <div class="filter-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" required>
                </div>

                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="fas fa-search"></i> Apply Filter
                    </button>
                </div>

                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="{{ route('reports.download') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                        class="btn btn-success" style="display:block; text-align:center; text-decoration:none;">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div style="margin-top:15px;">
                <strong style="color:#666; font-size:13px; margin-right:10px;">Quick Filters:</strong>
                <div class="quick-filters">
                    <button type="submit" name="filter" value="today" class="quick-filter-btn">Today</button>
                    <button type="submit" name="filter" value="yesterday" class="quick-filter-btn">Yesterday</button>
                    <button type="submit" name="filter" value="this_week" class="quick-filter-btn">This Week</button>
                    <button type="submit" name="filter" value="this_month" class="quick-filter-btn">This Month</button>
                    <button type="submit" name="filter" value="last_month" class="quick-filter-btn">Last Month</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card revenue">
            <div class="stat-icon">💵</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="stat-card items">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Items Sold</div>
            <div class="stat-value">{{ number_format($totalItems) }}</div>
        </div>

        <div class="stat-card transactions">
            <div class="stat-icon">🧾</div>
            <div class="stat-label">Transactions</div>
            <div class="stat-value">{{ number_format($totalTransactions) }}</div>
        </div>

        <div class="stat-card average">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Average Sale</div>
            <div class="stat-value">₱{{ number_format($averageSale, 2) }}</div>
        </div>
    </div>

    <!-- Charts and Top Products -->
    <div class="content-grid">
        <!-- Sales Chart -->
        <div class="section-card">
            <div class="section-header">
                <h3>📈 Sales Trend</h3>
            </div>

            @if($salesByDate->count() > 0)
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
            @else
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <p>No sales data for selected period</p>
            </div>
            @endif
        </div>

        <!-- Top Products -->
        <div class="section-card">
            <div class="section-header">
                <h3>🏆 Top Products</h3>
            </div>

            @if($topProducts->count() > 0)
            <ul class="top-products-list">
                @foreach($topProducts as $index => $product)
                <li class="product-item">
                    <div class="product-rank">{{ $index + 1 }}</div>
                    <div class="product-info">
                        <div class="product-name">{{ $product['name'] }}</div>
                        <div class="product-stats">
                            {{ $product['quantity'] }} sold • {{ $product['transactions'] }} orders
                        </div>
                    </div>
                    <div class="product-revenue">
                        ₱{{ number_format($product['revenue'], 2) }}
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <p>No products sold</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Detailed Transactions -->
    <div class="section-card">
        <div class="section-header">
            <h3>📝 Detailed Transactions</h3>
            <span style="color:#666; font-size:14px;">{{ $sales->count() }} transactions</span>
        </div>

        @if($sales->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                        <th style="padding:12px; text-align:left; font-size:13px; color:#800000;">Date & Time</th>
                        <th style="padding:12px; text-align:left; font-size:13px; color:#800000;">Product</th>
                        <th style="padding:12px; text-align:center; font-size:13px; color:#800000;">Quantity</th>
                        <th style="padding:12px; text-align:right; font-size:13px; color:#800000;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:12px; font-size:13px;">
                            {{ $sale->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td style="padding:12px; font-weight:600; font-size:14px;">
                            {{ $sale->product->name }}
                        </td>
                        <td style="padding:12px; text-align:center; font-size:14px;">
                            {{ $sale->quantity }}
                        </td>
                        <td style="padding:12px; text-align:right; font-weight:600; color:#4CAF50; font-size:14px;">
                            ₱{{ number_format($sale->total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <p>No transactions found for selected period</p>
        </div>
        @endif
    </div>
</div>

@if($salesByDate->count() > 0)
<script>
    // Lazy load Chart.js only when needed
    (function() {
        const chartLabels = @json($salesByDate->keys());
        const chartData = @json($salesByDate->pluck('total')->values());

        function initChart() {
            const ctx = document.getElementById('salesChart');
            const chartConfig = {
                labels: chartLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: chartData,
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            };

            new Chart(ctx, {
                type: 'line',
                data: chartConfig,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₱' + context.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return '₱' + value.toLocaleString(); }
                            }
                        }
                    }
                }
            });
        }

        // Load Chart.js dynamically
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        script.onload = initChart;
        document.head.appendChild(script);
    })();
</script>
@endif

@endsection