<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depense;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DepenseController extends Controller
{
    /**
     * Display a listing of the depenses.
     *
     * Shows all expenses filtered by month.
     */
    public function index(Request $request)
    {
        // Get distinct months from depenses table as strings (Y-m)
        $months = \DB::table('depenses')
                     ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
                     ->distinct()
                     ->orderBy('month', 'desc')
                     ->get()
                     ->pluck('month'); // returns collection of strings

        // Get the selected month from request, default to current month
        $month = $request->get('month', now()->format('Y-m'));
        $parsedMonth = Carbon::parse($month);

        // Filter depenses by the selected month and year
        $depenses = Depense::whereMonth('created_at', $parsedMonth->month)
                            ->whereYear('created_at', $parsedMonth->year)
                            ->get();

        // Calculate the total amount for the month
        $totalAmount = $depenses->sum('amount');

        // Return the Blade view with all data
        return view('admin.depenses', compact('depenses', 'totalAmount', 'months', 'month'));
    }

    /**
     * Store a new depense.
     */
    public function store(Request $request)
    {
        // Validate inputs
        $request->validate([
            'depense' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        // Create new depense
        Depense::create($request->only('depense', 'amount'));

        // Redirect back with success message
        return redirect()->route('admin.depenses')->with('success', 'Dépense ajoutée avec succès !');
    }

    /**
     * Update an existing depense.
     */
    public function update(Request $request, Depense $depense)
    {
        // Validate inputs
        $request->validate([
            'depense' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        // Update the depense
        $depense->update($request->only('depense', 'amount'));

        // Redirect back with success message
        return redirect()->route('admin.depenses')->with('success', 'Dépense modifiée avec succès !');
    }

    /**
     * Delete a depense.
     */
    public function destroy(Depense $depense)
    {
        // Delete the depense
        $depense->delete();

        // Redirect back with success message
        return redirect()->route('admin.depenses')->with('success', 'Dépense supprimée avec succès !');
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;
        $parsedMonth = Carbon::parse($month);

        $depenses = Depense::whereMonth('created_at', $parsedMonth->month)
                            ->whereYear('created_at', $parsedMonth->year)
                            ->get();

        // IMPORTANT: Blade path matches your folder
        $pdf = Pdf::loadView(
            'admin.reports.depenses-pdf',
            compact('depenses', 'month')
        );

        return $pdf->stream('depenses-' . $month . '.pdf');
    }

}
