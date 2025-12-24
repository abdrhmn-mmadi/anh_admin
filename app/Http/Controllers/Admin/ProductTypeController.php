<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductType;

class ProductTypeController extends Controller
{
    public function index()
    {
        $productTypes = ProductType::all();
        return view('admin.product-types', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        ProductType::create($request->only('name', 'price'));
        return back()->with('success', 'Product type created.');
    }

    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        $productType->update($request->only('name', 'price'));
        return back()->with('success', 'Product type updated.');
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();
        return back()->with('success', 'Product type deleted.');
    }

}
