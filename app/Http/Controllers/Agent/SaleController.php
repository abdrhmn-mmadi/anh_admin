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
    public function index()
    {
        $agent = Auth::user();

        $products = ProductStock::with('product')
            ->where('total_quantity', '>', 0)
            ->where('region_id', $agent->region_id)
            ->get();

        $sales = Sale::with('product')
            ->where('user_id', $agent->id)
            ->orderByDesc('created_at')
            ->get();

        return view('agent.sales', compact('products', 'sales'));
    }

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

                Sale::create([
                    'user_id' => $agent->id,
                    'product_id' => $p['product_id'],
                    'quantity' => $p['quantity'],
                    'unit_price' => $p['unit_price'],
                    'total_price' => $total,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'customer_address' => $request->customer_address,
                    'invoice_type' => $request->invoice_type,
                ]);
            }
        });

        return back()->with('success', 'Vente créée avec succès');
    }

    // ======================
    // EDIT SALE (AJAX)
    // ======================
    public function edit(Sale $sale)
    {
        return response()->json($sale);
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
            // Restore old stock
            ProductStock::where('product_id', $sale->product_id)
                ->where('region_id', $agent->region_id)
                ->increment('total_quantity', $sale->quantity);

            $p = $request->products[0];

            // Check new stock
            $stock = ProductStock::where('product_id', $p['product_id'])
                ->where('region_id', $agent->region_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stock->total_quantity < $p['quantity']) {
                throw new \Exception('Stock insuffisant');
            }

            $stock->decrement('total_quantity', $p['quantity']);

            $sale->update([
                'product_id' => $p['product_id'],
                'quantity' => $p['quantity'],
                'unit_price' => $p['unit_price'],
                'total_price' => $p['quantity'] * $p['unit_price'],
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'invoice_type' => $request->invoice_type,
            ]);
        });

        return redirect()->back()->with('success', 'Vente modifiée avec succès');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Vente supprimée');
    }

    public function pdf(Sale $sale)
    {
        $pdf = PDF::loadView('agent.sales-pdf', [
            'lines' => [[
                'name' => $sale->product->name,
                'quantity' => $sale->quantity,
                'unit_price' => $sale->unit_price,
                'total' => $sale->total_price,
            ]],
            'grandTotal' => $sale->total_price,
            'customer' => $sale,
            'type' => 'facture',
            'agent' => $sale->agent,
        ]);

        return $pdf->stream('facture.pdf', ['Attachment' => false]);
    }
}
