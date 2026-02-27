<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Employee;
use App\Models\ProductType;
use App\Models\ProductStock;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /* =========================
           TOTAL COUNTS
        ========================= */
        $totalUsers     = User::count();
        $totalEmployees = Employee::count();
        $totalProducts  = ProductType::count();
        $totalSales     = Sale::count(); // number of invoices

        /* =========================
           PRODUCTS BY TYPE
        ========================= */
        $productsByType = ProductType::leftJoin(
                'product_stocks',
                'product_types.id',
                '=',
                'product_stocks.product_id'
            )
            ->select(
                'product_types.name',
                DB::raw('SUM(product_stocks.total_quantity) as total')
            )
            ->groupBy('product_types.name')
            ->get();

        $productsByTypeLabels = $productsByType->pluck('name')->toArray();
        $productsByTypeData   = $productsByType->pluck('total')->map(fn ($v) => (int) $v)->toArray();

        if (empty($productsByTypeLabels)) {
            $productsByTypeLabels = ['No Data'];
            $productsByTypeData   = [0];
        }

        /* =========================
           PRODUCTS BY REGION
        ========================= */
        $productsByRegion = ProductStock::join(
                'regions',
                'product_stocks.region_id',
                '=',
                'regions.id'
            )
            ->select(
                'regions.name',
                DB::raw('SUM(product_stocks.total_quantity) as total')
            )
            ->groupBy('regions.name')
            ->get();

        $productsByRegionLabels = $productsByRegion->pluck('name')->toArray();
        $productsByRegionData   = $productsByRegion->pluck('total')->map(fn ($v) => (int) $v)->toArray();

        if (empty($productsByRegionLabels)) {
            $productsByRegionLabels = ['No Data'];
            $productsByRegionData   = [0];
        }

        /* =========================
           LAST 12 MONTHS (ROLLING)
           Example: Mar 2025 → Feb 2026
        ========================= */
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $months = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        /* =========================
           SALES + REVENUE AGGREGATION
        ========================= */
        $salesRaw = Sale::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(id) as total_sales,
                SUM(grand_total) as total_revenue
            ")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlySalesData   = [];
        $monthlyRevenueData = [];
        $monthLabels        = [];

        foreach ($months as $month) {
            $monthLabels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');

            $monthlySalesData[]   = isset($salesRaw[$month])
                ? (int) $salesRaw[$month]->total_sales
                : 0;

            $monthlyRevenueData[] = isset($salesRaw[$month])
                ? round((float) $salesRaw[$month]->total_revenue, 2)
                : 0;
        }

        /* =========================
           RETURN VIEW
        ========================= */
        return view('admin.welcome', [
            'totalUsers'               => $totalUsers,
            'totalEmployees'           => $totalEmployees,
            'totalProducts'            => $totalProducts,
            'totalSales'               => $totalSales,
            'productsByTypeLabels'     => $productsByTypeLabels,
            'productsByTypeData'       => $productsByTypeData,
            'productsByRegionLabels'   => $productsByRegionLabels,
            'productsByRegionData'     => $productsByRegionData,
            'months'                   => $monthLabels,
            'monthlySalesData'         => $monthlySalesData,
            'monthlyRevenueData'       => $monthlyRevenueData,
        ]);
    }
}
