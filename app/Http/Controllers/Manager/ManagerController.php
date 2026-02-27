<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Models\ProductBatch;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ManagerController extends Controller
{
    /* ==========================
       DASHBOARD
    ========================== */
    public function dashboard()
    {
        $totalStock = ProductStock::sum('total_quantity');
        $totalSales = DB::table('sales')->count();
        $totalRevenue = DB::table('sales')->sum('grand_total');

        return view('manager.welcome', compact('totalStock', 'totalSales', 'totalRevenue'));
    }

    /* ==========================
       STOCK MANAGEMENT
    ========================== */
    public function products(Request $request)
    {
        $stocks = ProductBatch::with(['product', 'region', 'agent'])->get();
        $products = ProductType::all();

        $editStock = null;
        if ($request->filled('edit')) {
            $editStock = ProductBatch::findOrFail($request->edit);
        }

        return view('manager.products', compact('stocks', 'products', 'editStock'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_types,id',
            'agent_id'   => 'required|exists:users,id',
            'region_id'  => 'required|exists:regions,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            ProductBatch::create([
                'product_id'         => $request->product_id,
                'agent_id'           => $request->agent_id,
                'region_id'          => $request->region_id,
                'quantity_available' => $request->quantity,
            ]);

            ProductStock::updateOrCreate(
                [
                    'product_id' => $request->product_id,
                    'region_id'  => $request->region_id,
                ],
                [
                    'total_quantity' => DB::raw('COALESCE(total_quantity,0) + ' . (int) $request->quantity),
                ]
            );
        });

        return back()->with('success', 'Stock ajouté avec succès.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {
            $batch = ProductBatch::findOrFail($id);
            $difference = $request->quantity - $batch->quantity_available;

            $batch->update([
                'quantity_available' => $request->quantity,
            ]);

            ProductStock::where('product_id', $batch->product_id)
                ->where('region_id', $batch->region_id)
                ->update([
                    'total_quantity' => DB::raw('total_quantity + ' . (int) $difference),
                ]);
        });

        return back()->with('success', 'Stock mis à jour.');
    }

    public function destroy($id)
    {
        $batch = ProductBatch::findOrFail($id);

        DB::transaction(function () use ($batch) {
            ProductStock::where('product_id', $batch->product_id)
                ->where('region_id', $batch->region_id)
                ->decrement('total_quantity', $batch->quantity_available);

            $batch->delete();
        });

        return back()->with('success', 'Stock supprimé.');
    }

    /* ==========================
       PROFILE MANAGEMENT
    ========================== */
    public function profile()
    {
        return view('manager.profile', [
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

        return back()->with('success_info', 'Profil mis à jour.');
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
                'current_password' => 'Mot de passe incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Mot de passe modifié.');
    }
}
