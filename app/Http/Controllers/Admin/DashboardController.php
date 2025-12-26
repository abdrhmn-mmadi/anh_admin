<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\User;
use App\Models\Employee;
use App\Models\ProductType;
use App\Models\ProductStock;
use App\Models\Sale;

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
        $totalSales     = Sale::count();

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

        $productsByTypeLabels = $productsByType->pluck('name');
        $productsByTypeData   = $productsByType->pluck('total');

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

        $productsByRegionLabels = $productsByRegion->pluck('name');
        $productsByRegionData   = $productsByRegion->pluck('total');

        /* =========================
           MONTHLY SALES & REVENUE
        ========================= */
        $monthlySales = Sale::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_amount')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // Prepare fixed 12 months
        $monthlySalesLabels = [];
        $monthlySalesData   = [];
        $monthlySalesAmount = [];

        for ($m = 1; $m <= 12; $m++) {
            $data = $monthlySales->firstWhere('month', $m);

            $monthlySalesLabels[] = now()->setMonth($m)->format('M');
            $monthlySalesData[]   = $data->total_quantity ?? 0;
            $monthlySalesAmount[] = $data->total_amount ?? 0;
        }

        return view('admin.welcome', compact(
            'totalUsers',
            'totalEmployees',
            'totalProducts',
            'totalSales',

            'productsByTypeLabels',
            'productsByTypeData',

            'productsByRegionLabels',
            'productsByRegionData',

            'monthlySalesLabels',
            'monthlySalesData',
            'monthlySalesAmount'
        ));
    }
}
