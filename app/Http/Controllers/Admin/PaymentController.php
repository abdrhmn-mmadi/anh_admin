<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Bank;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display payments page
     */
    public function index()
    {
        $payments = Payment::with(['employee', 'bank'])
            ->latest()
            ->get();

        $banks = Bank::all();

        return view('admin.payments', compact('payments', 'banks'));
    }

    /**
     * Store a new payment (SINGLE EMPLOYEE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'bank_id'      => 'required|exists:banks,id',
            'bonus'        => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:Salary,Bonus',
            'month'        => 'required|date_format:Y-m',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $bonus = $request->bonus ?? 0;
        $month = $request->month;

        // Prevent duplicate salary for same employee & month
        if ($request->payment_type === 'Salary') {
            $exists = Payment::where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('payment_type', 'Salary')
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'payment_date' => 'Salaire déjà payé pour ce mois.'
                ])->withInput();
            }
        }

        Payment::create([
            'employee_id'  => $employee->id,
            'bank_id'      => $request->bank_id,
            'bonus'        => $bonus,
            'total_amount' => $employee->salary + $bonus,
            'payment_type' => $request->payment_type,
            'payment_date' => $request->payment_date,
            'month'        => $month,
            'reference'    => $request->reference ?? 'Salaire ' . $month,
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Paiement enregistré avec succès');
    }

    /**
     * Update payment
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'bank_id'      => 'required|exists:banks,id',
            'bonus'        => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:Salary,Bonus',
            'month'        => 'required|date_format:Y-m',
        ]);

        $bonus = $request->bonus ?? 0;
        $month = $request->month;

        // Prevent duplicate salary if changing month
        if ($request->payment_type === 'Salary') {
            $exists = Payment::where('employee_id', $payment->employee_id)
                ->where('month', $month)
                ->where('payment_type', 'Salary')
                ->where('id', '!=', $payment->id)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'payment_date' => 'Salaire déjà payé pour ce mois.'
                ])->withInput();
            }
        }

        $payment->update([
            'bank_id'      => $request->bank_id,
            'bonus'        => $bonus,
            'total_amount' => $payment->employee->salary + $bonus,
            'payment_type' => $request->payment_type,
            'payment_date' => $request->payment_date,
            'month'        => $month,
            'reference'    => $request->reference ?? 'Salaire ' . $month,
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Paiement mis à jour avec succès');
    }

    /**
     * Delete payment
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Paiement supprimé avec succès');
    }

    /**
     * 🔍 SEARCH EMPLOYEE BY NIN (AJAX)
     */
    public function searchEmployee(Request $request)
    {
        $request->validate([
            'nin' => 'required|string'
        ]);

        $employee = Employee::where('nin', $request->nin)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employé non trouvé'
            ]);
        }

        return response()->json([
            'status' => true,
            'employee' => [
                'id'           => $employee->id,
                'name'         => $employee->first_name . ' ' . $employee->last_name,
                'salary'       => $employee->salary,
                'nin'          => $employee->nin,
                'bank_id'      => $employee->bank_id ?? null,
                'month'        => date('Y-m'),
                'payment_date' => date('Y-m-d')
            ]
        ]);
    }

    /**
     * 💰 PAY ALL EMPLOYEES FOR A MONTH
     */
    public function payAll(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $bank = Bank::first();

        if (!$bank) {
            return back()->withErrors(['bank' => 'Aucune banque disponible pour le paiement.']);
        }

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $exists = Payment::where('employee_id', $employee->id)
                ->where('month', $request->month)
                ->where('payment_type', 'Salary')
                ->exists();

            if ($exists) {
                continue;
            }

            Payment::create([
                'employee_id'  => $employee->id,
                'bank_id'      => $bank->id,
                'bonus'        => 0,
                'total_amount' => $employee->salary,
                'payment_type' => 'Salary',
                'payment_date' => now(),
                'month'        => $request->month,
                'reference'    => 'Salaire ' . $request->month,
            ]);
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Paiement mensuel effectué pour tous les employés');
    }
}
