<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\Depense;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompletePaymentsExport;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reports dashboard
     */
    public function index()
    {
        $employees = Employee::with('bank')->get();
        $banks = Bank::all();

        $availableMonths = Payment::select('month')
            ->whereNotNull('month')
            ->where('month', '!=', '')
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month')
            ->filter()
            ->values();

        $months = [];
        foreach ($availableMonths as $ym) {
            try {
                $months[$ym] = ucfirst(
                    Carbon::createFromFormat('Y-m', $ym)
                        ->locale('fr')
                        ->translatedFormat('F Y')
                );
            } catch (\Exception $e) {}
        }

        if (empty($months)) {
            $currentMonth = Carbon::now()->format('Y-m');
            $months[$currentMonth] = ucfirst(
                Carbon::now()->locale('fr')->translatedFormat('F Y')
            );
        }

        return view('admin.reports', compact('employees', 'banks', 'months'));
    }

    /**
     * Generate employee payslip
     */
    public function payslip(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month'       => 'required|date_format:Y-m',
        ]);

        $employee = Employee::with('bank', 'region')
            ->findOrFail($request->employee_id);

        $payment = Payment::where('employee_id', $employee->id)
            ->where('month', $request->month)
            ->first();

        if (!$payment) {
            return back()->withErrors([
                'employee_id' => 'Aucun paiement trouvé pour ce mois.'
            ]);
        }

        $salary = $employee->salary;

        // ✅ NEW IGR CALCULATION
        if ($salary <= 70000) {
            $igr = ($salary * 0.025) - 350;
        } elseif ($salary <= 85000) {
            $igr = ($salary * 0.035) - 425;
        } elseif ($salary <= 110000) {
            $igr = ($salary * 0.045) - 500;
        } elseif ($salary <= 250000) {
            $igr = ($salary * 0.08);
        } elseif ($salary <= 300000) {
            $igr = ($salary * 0.11) - 1510;
        } else {
            $igr = ($salary * 0.15) - 2633;
        }

        $igr = max(0, $igr); // safety

        $total = ($salary + $payment->bonus) - $igr;

        $pdf = PDF::loadView('admin.reports.payslip', [
            'employee' => $employee,
            'payment'  => $payment,
            'igr'      => $igr,
            'total'    => $total,
            'month'    => $request->month,
        ]);

        return $pdf->stream(
            "Payslip_{$employee->first_name}_{$request->month}.pdf"
        );
    }

    /**
     * Generate bank payment list
     */
    public function bank(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'month'   => 'required|date_format:Y-m',
        ]);

        $bank = Bank::findOrFail($request->bank_id);

        $payments = Payment::where('bank_id', $bank->id)
            ->where('month', $request->month)
            ->with('employee')
            ->get();

        $pdf = PDF::loadView('admin.reports.bank', [
            'bank'     => $bank,
            'payments' => $payments,
            'month'    => $request->month,
        ]);

        return $pdf->stream(
            "BankPayments_{$bank->name}_{$request->month}.pdf"
        );
    }

    /**
     * Generate complete payment list (PDF)
     */
    public function completeList(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $payments = Payment::with('employee.bank')
            ->where('month', $request->month)
            ->get();

        $pdf = PDF::loadView('admin.reports.complete_list', [
            'payments' => $payments,
            'month'    => $request->month,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream(
            "CompletePayments_{$request->month}.pdf"
        );
    }

    /**
     * Generate complete payment list (Excel)
     */
    public function completeExcel(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        return Excel::download(
            new CompletePaymentsExport($request->month),
            "CompletePayments_{$request->month}.xlsx"
        );
    }

    /**
     * Generate expenses PDF by month
     */
    public function expense(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $date = Carbon::createFromFormat('Y-m', $request->month);

        $depenses = Depense::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->orderBy('created_at')
            ->get();

        $totalAmount = $depenses->sum('amount');

        $monthLabel = ucfirst($date->translatedFormat('F Y'));

        $pdf = PDF::loadView('admin.reports.depenses-pdf', [
            'depenses'    => $depenses,
            'totalAmount' => $totalAmount,
            'monthLabel'  => $monthLabel,
        ]);

        return $pdf->stream(
            "Depenses_{$request->month}.pdf"
        );
    }
}
