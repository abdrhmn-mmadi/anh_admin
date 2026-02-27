<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\ProductType;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class SaleController extends Controller
{
    // ======================
    // LIST SALES
    // ======================
    public function index(Request $request)
    {
        $agent = Auth::user();

        $products = ProductStock::with('product')
            ->where('total_quantity', '>', 0)
            ->where('region_id', $agent->region_id)
            ->get();

        $salesQuery = Sale::with(['items.product', 'items.region'])
            ->where('user_id', $agent->id)
            ->orderByDesc('created_at');

        // FILTER BY CLIENT NAME
        if ($request->filled('client_name')) {
            $salesQuery->where('customer_name', 'like', '%'.$request->client_name.'%');
        }

        // FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $salesQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $salesQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $salesQuery->get();

        return view('agent.sales', compact('products', 'sales'));
    }


    // ======================
    // CREATE SALE
    // ======================
    public function store(Request $request)
    {
        $agent = Auth::user();

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product_types,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'invoice_type' => 'required|in:proforma,facture',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'customer_address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $agent) {

            $grandTotal = 0;

            $sale = Sale::create([
                'user_id' => $agent->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'invoice_type' => $request->invoice_type,
                'grand_total' => 0,
            ]);

            foreach ($request->products as $p) {
                $product = ProductType::findOrFail($p['product_id']);
                $total = $p['quantity'] * $p['unit_price'];

                if ($request->invoice_type === 'facture') {
                    $stock = ProductStock::where('product_id', $p['product_id'])
                        ->where('region_id', $agent->region_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($stock->total_quantity < $p['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->name}");
                    }

                    $stock->decrement('total_quantity', $p['quantity']);
                }

                $sale->items()->create([
                    'product_id' => $p['product_id'],
                    'quantity' => $p['quantity'],
                    'unit_price' => $p['unit_price'],
                    'total_price' => $total,
                    'region_id' => $agent->region_id,
                ]);

                $grandTotal += $total;
            }

            $sale->update(['grand_total' => $grandTotal]);
        });

        return back()->with('success', 'Vente créée avec succès');
    }

    // ======================
    // EDIT SALE (AJAX)
    // ======================
    public function edit(Sale $sale)
    {
        $sale->load('items');
        return response()->json([
            'customer_name' => $sale->customer_name,
            'customer_email' => $sale->customer_email,
            'customer_phone' => $sale->customer_phone,
            'customer_address' => $sale->customer_address,
            'invoice_type' => $sale->invoice_type,
            'items' => $sale->items->map(fn($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ]),
        ]);
    }

    // ======================
    // UPDATE SALE
    // ======================
    public function update(Request $request, Sale $sale)
    {
        $agent = Auth::user();

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product_types,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'invoice_type' => 'required|in:proforma,facture',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'customer_address' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $sale, $agent) {

            // Restore stock for previous items if facture
            if ($sale->invoice_type === 'facture') {
                foreach ($sale->items as $item) {
                    ProductStock::where('product_id', $item->product_id)
                        ->where('region_id', $agent->region_id)
                        ->increment('total_quantity', $item->quantity);
                }
            }

            $sale->items()->delete();

            $grandTotal = 0;

            foreach ($request->products as $p) {
                $product = ProductType::findOrFail($p['product_id']);
                $total = $p['quantity'] * $p['unit_price'];

                if ($request->invoice_type === 'facture') {
                    $stock = ProductStock::where('product_id', $p['product_id'])
                        ->where('region_id', $agent->region_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($stock->total_quantity < $p['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->name}");
                    }

                    $stock->decrement('total_quantity', $p['quantity']);
                }

                $sale->items()->create([
                    'product_id' => $p['product_id'],
                    'quantity' => $p['quantity'],
                    'unit_price' => $p['unit_price'],
                    'total_price' => $total,
                    'region_id' => $agent->region_id,
                ]);

                $grandTotal += $total;
            }

            $sale->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'invoice_type' => $request->invoice_type,
                'grand_total' => $grandTotal,
            ]);
        });

        return back()->with('success', 'Vente modifiée avec succès');
    }

    // ======================
    // DELETE SALE
    // ======================
    public function destroy(Sale $sale)
    {
        $agent = Auth::user();

        if ($sale->invoice_type === 'facture') {
            foreach ($sale->items as $item) {
                ProductStock::where('product_id', $item->product_id)
                    ->where('region_id', $agent->region_id)
                    ->increment('total_quantity', $item->quantity);
            }
        }

        $sale->delete();

        return back()->with('success', 'Vente supprimée');
    }

    // ======================
    // SALE PDF
    // ======================
    public function pdf(Sale $sale)
    {
        $sale->load('items.product', 'items.region', 'agent');

        $lines = $sale->items->map(function($item) {
            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total_price,
                'region_name' => $item->region->name ?? 'N/A', // Add this
            ];
        })->toArray();

        $pdf = PDF::loadView('agent.sales-pdf', [
            'lines' => $lines,
            'grandTotal' => $sale->grand_total,
            'customer' => $sale,
            'type' => $sale->invoice_type,
            'agent' => $sale->agent,
        ]);

        return $pdf->stream('facture.pdf', ['Attachment' => false]);
    }

    public function search(Request $request)
    {
        $agent = Auth::user();
        $query = Sale::with(['items.product', 'items.region'])
            ->where('user_id', $agent->id)
            ->orderByDesc('created_at');

        if ($request->filled('client_name')) {
            $query->where('customer_name', 'like', '%'.$request->client_name.'%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->get();
        return response()->json($sales);
    }

}
