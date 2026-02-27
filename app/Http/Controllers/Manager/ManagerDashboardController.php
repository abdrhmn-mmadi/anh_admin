<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\ProductType;
use App\Models\ProductStock;
use App\Models\Sale;
use Carbon\Carbon;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        /* =========================
           TOTAL COUNTS / SUMS
        ========================= */
        $totalEmployees = Employee::count();                  
        $totalProducts  = ProductStock::sum('total_quantity'); 
        $totalSales     = Sale::sum('grand_total');          

        /* =========================
           PRODUCTS BY TYPE (quantity)
        ========================= */
        $productsByType = ProductType::leftJoin('product_stocks', 'product_types.id', '=', 'product_stocks.product_id')
            ->select('product_types.name', DB::raw('SUM(product_stocks.total_quantity) as total'))
            ->groupBy('product_types.name')
            ->get();

        $productsByTypeLabels = $productsByType->pluck('name')->toArray();
        $productsByTypeData   = $productsByType->pluck('total')->map(fn($v) => (int)$v)->toArray();

        if (empty($productsByTypeLabels)) {
            $productsByTypeLabels = ['No Data'];
            $productsByTypeData   = [0];
        }

        /* =========================
           PRODUCTS BY REGION (quantity)
        ========================= */
        $productsByRegion = ProductStock::join('regions', 'product_stocks.region_id', '=', 'regions.id')
            ->select('regions.name', DB::raw('SUM(product_stocks.total_quantity) as total'))
            ->groupBy('regions.name')
            ->get();

        $productsByRegionLabels = $productsByRegion->pluck('name')->toArray();
        $productsByRegionData   = $productsByRegion->pluck('total')->map(fn($v) => (int)$v)->toArray();

        if (empty($productsByRegionLabels)) {
            $productsByRegionLabels = ['No Data'];
            $productsByRegionData   = [0];
        }

        /* =========================
           LAST 12 MONTHS INCLUDING CURRENT
        ========================= */
        $months = collect();
        $monthlySalesData = [];
        $monthlyRevenueData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M Y')); // e.g., Feb 2026

            // Sales count
            $monthlySalesData[] = Sale::whereYear('created_at', $date->year)
                                      ->whereMonth('created_at', $date->month)
                                      ->count();

            // Revenue
            $monthlyRevenueData[] = Sale::whereYear('created_at', $date->year)
                                        ->whereMonth('created_at', $date->month)
                                        ->sum('grand_total');
        }

        /* =========================
           RETURN VIEW
        ========================= */
        return view('manager.welcome', [
            'totalEmployees'       => $totalEmployees,
            'totalProducts'        => $totalProducts,
            'totalSales'           => $totalSales,
            'productsByTypeLabels' => $productsByTypeLabels,
            'productsByTypeData'   => $productsByTypeData,
            'productsByRegionLabels'=> $productsByRegionLabels,
            'productsByRegionData' => $productsByRegionData,
            'months'               => $months->toArray(),
            'monthlySalesData'     => $monthlySalesData,
            'monthlyRevenueData'   => $monthlyRevenueData,
        ]);
    }
}
