<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $cacheTime = 60; // Cache for 1 minute

        // Batch all-time aggregate stats into a single cache entry
        $stats = Cache::remember("dashboard_stats_{$userId}", $cacheTime, function () use ($userId) {
            return [
                'totalSales' => Sale::where('user_id', $userId)->sum('total'),
                'totalProducts' => Product::where('user_id', $userId)->count(),
                'totalItemsSold' => Sale::where('user_id', $userId)->sum('quantity'),
            ];
        });

        $totalSales = $stats['totalSales'];
        $totalProducts = $stats['totalProducts'];
        $totalItemsSold = $stats['totalItemsSold'];

        $lowStock = Cache::remember("dashboard_low_stock_{$userId}", $cacheTime, function () use ($userId) {
            return Product::where('user_id', $userId)
                ->where('stock', '<=', 5)
                ->get();
        });

        $sales = Sale::with('product')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Top 5 Best Sellers - cached for 5 minutes (expensive query)
        $topProducts = Cache::remember("dashboard_top_products_{$userId}", 300, function () use ($userId) {
            return Product::where('user_id', $userId)
                ->withSum('sales', 'quantity')
                ->orderBy('sales_sum_quantity', 'desc')
                ->take(5)
                ->get()
                ->map(function ($p) {
                    $p->total_sold = $p->sales_sum_quantity ?? 0;
                    return $p;
                });
        });

        // Batch today's stats into a single cache entry
        $todayStats = Cache::remember("dashboard_today_{$userId}", $cacheTime, function () use ($userId) {
            return [
                'todaySales' => Sale::where('user_id', $userId)->whereDate('created_at', today())->count(),
                'todayRevenue' => Sale::where('user_id', $userId)->whereDate('created_at', today())->sum('total'),
            ];
        });

        $todaySales = $todayStats['todaySales'];
        $todayRevenue = $todayStats['todayRevenue'];

        return view('dashboard', compact(
            'totalSales',
            'totalProducts',
            'totalItemsSold',
            'lowStock',
            'sales',
            'topProducts',
            'todaySales',
            'todayRevenue'
        ));
    }
}
