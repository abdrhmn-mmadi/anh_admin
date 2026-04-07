<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Bank;
use App\Models\Region;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
  public function index(Request $request)
  {
      $query = Payment::with(['employee.region', 'employee.bank']);

      // ✅ Default: latest month
      $latestMonth = Payment::max('month');

      $selectedMonth = $request->month ?? $latestMonth;

      if ($selectedMonth) {
          $query->where('month', $selectedMonth);
      }

      // ✅ Filter by region
      if ($request->region) {
          $query->whereHas('employee', function ($q) use ($request) {
              $q->where('region_id', $request->region);
          });
      }

      // ✅ Live search
      if ($request->search) {
          $query->whereHas('employee', function ($q) use ($request) {
              $q->where('nin', 'like', "%{$request->search}%")
                ->orWhere('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%");
          });
      }

      $query->latest();

      $perPage = $request->per_page ?? 10;

      $payments = $query->paginate($perPage)->appends($request->all());

      $paymentsJson = $payments->map(function ($p) {
          return [
              'id' => $p->id,
              'bonus' => $p->bonus,
              'month' => $p->month,
              'payment_date' => \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d'),
              'employee_id' => $p->employee_id,
              'employee' => [
                  'id' => $p->employee->id,
                  'first_name' => $p->employee->first_name,
                  'last_name' => $p->employee->last_name,
                  'nin' => $p->employee->nin,
                  'salary' => $p->employee->salary,
                  'bank' => $p->employee->bank ? [
                      'id' => $p->employee->bank->id,
                      'name' => $p->employee->bank->name,
                  ] : null,
                  'region' => $p->employee->region ? [
                      'id' => $p->employee->region->id,
                      'name' => $p->employee->region->name,
                  ] : null,
              ],
          ];
      });

      $banks = Bank::all();
      $regions = Region::all();

      // ✅ Month list
      $availableMonths = Payment::select('month')
          ->distinct()
          ->orderByDesc('month')
          ->pluck('month');

      return view('admin.payments', compact(
          'payments',
          'paymentsJson',
          'banks',
          'regions',
          'availableMonths',
          'selectedMonth'
      ));
  }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'bank_id'      => 'nullable|exists:banks,id',
            'bonus'        => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:Salary,Bonus',
            'month'        => 'required|date_format:Y-m',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $bonus = $request->bonus ?? 0;
        $salary = (float) $employee->salary;

        $igr = $this->calculateIGR($salary);
        $total = ($salary + $bonus) - $igr;

        if ($request->payment_type === 'Salary') {
            $exists = Payment::where('employee_id', $employee->id)
                ->where('month', $request->month)
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
            'total_amount' => $total,
            'payment_type' => $request->payment_type,
            'payment_date' => $request->payment_date,
            'month'        => $request->month,
            'reference'    => 'Salaire ' . $request->month,
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Paiement enregistré avec succès');
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'bank_id'      => 'nullable|exists:banks,id',
            'bonus'        => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:Salary,Bonus',
            'month'        => 'required|date_format:Y-m',
        ]);

        $bonus = $request->bonus ?? 0;
        $salary = (float) $payment->employee->salary;

        $igr = $this->calculateIGR($salary);
        $total = ($salary + $bonus) - $igr;

        if ($request->payment_type === 'Salary') {
            $exists = Payment::where('employee_id', $payment->employee_id)
                ->where('month', $request->month)
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
            'total_amount' => $total,
            'payment_type' => $request->payment_type,
            'payment_date' => $request->payment_date,
            'month'        => $request->month,
            'reference'    => 'Salaire ' . $request->month,
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Paiement mis à jour avec succès');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Paiement supprimé avec succès');
    }

    public function searchEmployee(Request $request)
    {
        $query = $request->input('query');

        $employees = Employee::with('bank')
            ->where('nin', 'like', "%$query%")
            ->orWhere('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'nin' => $emp->nin,
                    'salary' => $emp->salary,
                    'bank_name' => $emp->bank->name ?? '-',
                    'bank_id' => $emp->bank_id,
                    'month' => now()->format('Y-m'),
                    'payment_date' => now()->format('Y-m-d'),
                ];
            });

        return response()->json([
            'status' => true,
            'employees' => $employees
        ]);
    }

    public function payAll(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $bank = Bank::first();
        if (!$bank) {
            return back()->withErrors(['bank' => 'Aucune banque disponible.']);
        }

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $exists = Payment::where('employee_id', $employee->id)
                ->where('month', $request->month)
                ->where('payment_type', 'Salary')
                ->exists();

            if ($exists) continue;

            $salary = (float) $employee->salary;
            $igr = $this->calculateIGR($salary);

            Payment::create([
                'employee_id' => $employee->id,
                'bank_id' => $bank->id,
                'bonus' => 0,
                'total_amount' => $salary - $igr,
                'payment_type' => 'Salary',
                'payment_date' => now(),
                'month' => $request->month,
                'reference' => 'Salaire ' . $request->month,
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Paiement mensuel effectué pour tous les employés');
    }

    private function calculateIGR($salary)
    {
        if ($salary <= 70000) return round(($salary * 0.025) + 350);
        if ($salary <= 85000) return round(($salary * 0.035) + 425);
        if ($salary <= 110000) return round(($salary * 0.045) + 500);
        if ($salary <= 250000) return round($salary * 0.08);
        if ($salary <= 300000) return round(($salary * 0.11) + 1510);
        return round(($salary * 0.15) + 2633);
    }
}
