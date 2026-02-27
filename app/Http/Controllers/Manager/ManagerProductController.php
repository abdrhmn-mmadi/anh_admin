<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\ProductType;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;

class ManagerProductController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::all();
        $productTypes = ProductType::all();

        $batches = DB::table('product_batches')
            ->join('product_types', 'product_types.id', '=', 'product_batches.product_id')
            ->join('regions', 'regions.id', '=', 'product_batches.region_id')
            ->select(
                'product_batches.*',
                'product_types.name as product_name',
                'regions.name as region_name'
            )
            ->when($request->region_id, function ($q) use ($request) {
                $q->where('product_batches.region_id', $request->region_id);
            })
            ->when($request->product_id, function ($q) use ($request) {
                $q->where('product_batches.product_id', $request->product_id);
            })
            ->orderBy('product_batches.created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return view('manager.products', compact(
            'regions',
            'productTypes',
            'batches'
        ));
    }
}





