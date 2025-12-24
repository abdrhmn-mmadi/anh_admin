<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;

class BankController extends Controller
{
    // Display all banks
    public function index()
    {
        $banks = Bank::all();
        return view('admin.banks', compact('banks'));
    }

    // Store a new bank
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:banks,code',
            'name' => 'required|string'
        ]);

        Bank::create($request->only('code', 'name'));

        return back()->with('success', 'Banque ajoutée.');
    }

    // Show edit form (optional if using modal inline)
    public function edit(Bank $bank)
    {
        return view('admin.banks_edit', compact('bank'));
    }

    // Update bank
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'code' => 'required|string|unique:banks,code,' . $bank->id,
            'name' => 'required|string'
        ]);

        $bank->update($request->only('code', 'name'));

        return back()->with('success', 'Banque mise à jour.');
    }

    // Delete bank
    public function destroy(Bank $bank)
    {
        $bank->delete();
        return back()->with('success', 'Banque supprimée.');
    }
}
