<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function dailySales(Request $request)
    {
        $user = auth()->user();

        // Get date range from request or default to today
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Quick filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'today':
                    $startDate = Carbon::today()->toDateString();
                    $endDate = Carbon::today()->toDateString();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday()->toDateString();
                    $endDate = Carbon::yesterday()->toDateString();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek()->toDateString();
                    $endDate = Carbon::now()->endOfWeek()->toDateString();
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth()->toDateString();
                    $endDate = Carbon::now()->endOfMonth()->toDateString();
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                    $endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                    break;
            }
        }

        // Get sales within date range (limit to 500 for performance)
        $sales = Sale::with('product')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest()
            ->take(500)
            ->get();

        // Calculate summary statistics
        $totalRevenue = $sales->sum('total');
        $totalItems = $sales->sum('quantity');
        $totalTransactions = $sales->count();
        $averageSale = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Group sales by date for chart
        $salesByDate = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('Y-m-d');
        })->map(function ($daySales) {
            return [
                'total' => $daySales->sum('total'),
                'count' => $daySales->count(),
                'items' => $daySales->sum('quantity')
            ];
        });

        // Top selling products
        $topProducts = $sales->groupBy('product_id')->map(function ($productSales) {
            $product = $productSales->first()->product;

            // Skip if product was deleted
            if (!$product) {
                return null;
            }

            return [
                'name' => $product->name,
                'quantity' => $productSales->sum('quantity'),
                'revenue' => $productSales->sum('total'),
                'transactions' => $productSales->count()
            ];
        })->filter()->sortByDesc('revenue')->take(10);

        // Sales by hour (for today only)
        $salesByHour = [];
        if ($startDate === $endDate && $startDate === Carbon::today()->toDateString()) {
            $salesByHour = $sales->groupBy(function ($sale) {
                return Carbon::parse($sale->created_at)->format('H:00');
            })->map(function ($hourSales) {
                return $hourSales->sum('total');
            });
        }

        return view('reports.daily-sales', compact(
            'sales',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalItems',
            'totalTransactions',
            'averageSale',
            'salesByDate',
            'topProducts',
            'salesByHour'
        ));
    }

    public function downloadReport(Request $request)
    {
        $user = auth()->user();

        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $sales = Sale::with('product')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->latest()
            ->take(2000)
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalItems = $sales->sum('quantity');
        $totalTransactions = $sales->count();

        $topProducts = $sales->groupBy('product_id')->map(function ($productSales) {
            $product = $productSales->first()->product;

            // Skip if product was deleted
            if (!$product) {
                return null;
            }

            return [
                'name' => $product->name,
                'quantity' => $productSales->sum('quantity'),
                'revenue' => $productSales->sum('total')
            ];
        })->filter()->sortByDesc('revenue')->take(10);

        $pdf = Pdf::loadView('reports.sales-pdf', compact(
            'sales',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalItems',
            'totalTransactions',
            'topProducts',
            'user'
        ));

        return $pdf->download('sales-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }
}
