<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Region;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminSalesController extends Controller
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

        return view('admin.sales', compact('sales', 'regions'));
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
