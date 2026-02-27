<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Region;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ManagerSalesController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with([
                'items.product.stocks.region',
                'items.region'
            ])
            ->when($request->region, function ($query) use ($request) {
                $query->whereHas('items', function ($q) use ($request) {
                    $q->where('region_id', $request->region);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $regions = Region::all();

        // ✅ TOTAL OF DISPLAYED SALES
        $totalAmount = $sales->sum('grand_total');

        return view('manager.sales', compact(
            'sales',
            'regions',
            'totalAmount'
        ));
    }

    public function export(Request $request)
    {
        $sales = Sale::with([
                'items.product.stocks.region',
                'items.region'
            ])
            ->when($request->region, function ($query) use ($request) {
                $query->whereHas('items', function ($q) use ($request) {
                    $q->where('region_id', $request->region);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Excel::download(
            new SalesExport($sales),
            'ventes.xlsx'
        );
    }
}
