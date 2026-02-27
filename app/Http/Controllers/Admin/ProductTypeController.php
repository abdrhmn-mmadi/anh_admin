<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductBatchesExport;
use Maatwebsite\Excel\Facades\Excel;


class ProductTypeController extends Controller
{
    /**
     * Display a listing of product types.
     */
    public function index()
    {
        $productTypes = ProductType::all();
        return view('admin.product-types', compact('productTypes'));
    }

    /**
     * Store a newly created product type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
        ]);

        ProductType::create($request->only('name', 'price'));
        return back()->with('success', 'Product type created.');
    }

    /**
     * Update the specified product type.
     */
    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
        ]);

        $productType->update($request->only('name', 'price'));
        return back()->with('success', 'Product type updated.');
    }

    /**
     * Remove the specified product type.
     */
    public function destroy(ProductType $productType)
    {
        $productType->delete();
        return back()->with('success', 'Product type deleted.');
    }

    /**
     * Display products with their total quantity,
     * with optional filters for region and product type.
     */
    public function products(Request $request)
    {
        $regionId  = $request->region_id;
        $productId = $request->product_id;

        /** --------------------------------
         * TOTAL QUANTITY PER PRODUCT TYPE
         * -------------------------------- */
        $products = DB::table('product_types')
            ->leftJoin('product_batches', 'product_types.id', '=', 'product_batches.product_id')
            ->select(
                'product_types.id as product_id',
                'product_types.name as product_name',
                DB::raw('SUM(product_batches.quantity_available) as total_quantity')
            )
            ->groupBy('product_types.id', 'product_types.name')
            ->get();

        /** --------------------------------
         * PRODUCT BATCH RECORDS (FILTERABLE)
         * -------------------------------- */
        $batchesQuery = DB::table('product_batches')
            ->join('product_types', 'product_types.id', '=', 'product_batches.product_id')
            ->join('regions', 'regions.id', '=', 'product_batches.region_id')
            ->select(
                'product_batches.*',
                'product_types.name as product_name',
                'regions.name as region_name'
            );

        if ($regionId) {
            $batchesQuery->where('product_batches.region_id', $regionId);
        }

        if ($productId) {
            $batchesQuery->where('product_batches.product_id', $productId);
        }

        $batches = $batchesQuery
            ->orderBy('product_batches.created_at', 'desc')
            ->get();

        return view('admin.products', [
            'products'       => $products,
            'batches'        => $batches,
            'regions'        => DB::table('regions')->get(),
            'productTypes'   => DB::table('product_types')->get(),
            'selectedRegion' => $regionId,
            'selectedProduct'=> $productId,
        ]);
    }

    /**
     * Export filtered product batches to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new ProductBatchesExport(
                $request->region_id,
                $request->product_id
            ),
            'product_batches.xlsx'
        );
    }



}
