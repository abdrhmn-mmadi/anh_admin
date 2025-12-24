<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\ProductBatch;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    /* ==========================
       STOCK MANAGEMENT
    ========================== */
    
    public function products(Request $request)
    {
        $agent = Auth::user();

        $stocks = ProductBatch::with(['product', 'region'])
            ->where('agent_id', $agent->id)
            ->where('region_id', $agent->region_id)
            ->get();

        $products = ProductType::all();

        $editStock = null;
        if ($request->filled('edit')) {
            $editStock = ProductBatch::where('id', $request->edit)
                ->where('agent_id', $agent->id)
                ->where('region_id', $agent->region_id)
                ->first();
        }

        return view('agent.products', compact('stocks', 'products', 'editStock'));
    }

    public function store(Request $request)
    {
        $agent = Auth::user();

        $request->validate([
            'product_id' => 'required|exists:product_types,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $agent) {
            ProductBatch::create([
                'product_id'         => $request->product_id,
                'agent_id'           => $agent->id,
                'region_id'          => $agent->region_id,
                'quantity_available' => $request->quantity,
            ]);

            ProductStock::updateOrCreate(
                [
                    'product_id' => $request->product_id,
                    'region_id'  => $agent->region_id,
                ],
                [
                    'total_quantity' => DB::raw('COALESCE(total_quantity,0) + ' . (int) $request->quantity),
                ]
            );
        });

        return redirect()->route('agent.products.index')
            ->with('success', 'Stock ajouté avec succès.');
    }

    public function update(Request $request, $id)
    {
        $agent = Auth::user();

        $request->validate([
            'product_id' => 'required|exists:product_types,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id, $agent) {
            $batch = ProductBatch::where('id', $id)
                ->where('agent_id', $agent->id)
                ->where('region_id', $agent->region_id)
                ->firstOrFail();

            $difference = $request->quantity - $batch->quantity_available;

            $batch->update([
                'product_id'         => $request->product_id,
                'quantity_available' => $request->quantity,
            ]);

            ProductStock::updateOrCreate(
                [
                    'product_id' => $request->product_id,
                    'region_id'  => $agent->region_id,
                ],
                [
                    'total_quantity' => DB::raw('COALESCE(total_quantity,0) + ' . (int) $difference),
                ]
            );
        });

        return redirect()->route('agent.products.index')
            ->with('success', 'Stock mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $agent = Auth::user();

        $batch = ProductBatch::where('id', $id)
            ->where('agent_id', $agent->id)
            ->where('region_id', $agent->region_id)
            ->firstOrFail();

        DB::transaction(function () use ($batch, $agent) {
            ProductStock::where('product_id', $batch->product_id)
                ->where('region_id', $agent->region_id)
                ->decrement('total_quantity', $batch->quantity_available);

            $batch->delete();
        });

        return redirect()->route('agent.products.index')
            ->with('success', 'Stock supprimé avec succès.');
    }

    /* ==========================
       PROFILE MANAGEMENT
    ========================== */

    public function profile()
    {
        return view('agent.profile', [
            'user' => Auth::user()
        ]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update($validated);

        return back()->with('success_info', 'Informations mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mot de passe actuel incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Mot de passe mis à jour avec succès.');
    }

    /* ==========================
       DASHBOARD
    ========================== */

public function dashboard()
{
    $agent = Auth::user();

    /* ==========================
       PRODUCTS BY TYPE (STOCK)
    ========================== */
    $productsByType = DB::table('product_stocks')
        ->join('product_types', 'product_stocks.product_id', '=', 'product_types.id')
        ->select('product_types.name', DB::raw('SUM(product_stocks.total_quantity) as total'))
        ->where('product_stocks.region_id', $agent->region_id)
        ->groupBy('product_types.name')
        ->get();

    /* ==========================
       MONTHLY SALES (QTY + AMOUNT)
    ========================== */
    $monthlySalesQuery = DB::table('sales')
        ->selectRaw('
            MONTH(created_at) as month,
            SUM(quantity) as total_quantity,
            SUM(total_price) as total_amount
        ')
        ->where('user_id', $agent->id)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    $monthlySalesQty = array_fill(0, 12, 0);
    $monthlySalesAmount = array_fill(0, 12, 0);

    foreach ($monthlySalesQuery as $sale) {
        $monthlySalesQty[$sale->month - 1] = $sale->total_quantity;
        $monthlySalesAmount[$sale->month - 1] = $sale->total_amount;
    }

    return view('agent.welcome', [
        /* Cards */
        'totalProducts' => DB::table('product_stocks')
            ->where('region_id', $agent->region_id)
            ->sum('total_quantity'),

        'totalSales' => DB::table('sales')
            ->where('user_id', $agent->id)
            ->sum('quantity'),

        'totalRevenue' => DB::table('sales')
            ->where('user_id', $agent->id)
            ->sum('total_price'),

        /* Charts */
        'productsByTypeLabels' => $productsByType->pluck('name'),
        'productsByTypeData'   => $productsByType->pluck('total'),

        'monthlySalesLabels'   => $months,
        'monthlySalesData'     => $monthlySalesQty,
        'monthlySalesAmount'   => $monthlySalesAmount,
    ]);
}


}
