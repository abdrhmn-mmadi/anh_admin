<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Bank;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompletePaymentsExport;

class ReportController extends Controller
{
    /**
     * Reports dashboard
     */
    public function index()
    {
        $employees = Employee::with('bank')->get();
        $banks = Bank::all();

        $months = [
            '01' => 'Janvier','02' => 'Février','03' => 'Mars','04' => 'Avril',
            '05' => 'Mai','06' => 'Juin','07' => 'Juillet','08' => 'Août',
            '09' => 'Septembre','10' => 'Octobre','11' => 'Novembre','12' => 'Décembre',
        ];

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

        $igr = match (true) {
            $salary <= 70000  => 2000,
            $salary <= 80000  => 3000,
            $salary <= 100000 => 4000,
            $salary <= 110000 => 5000,
            default           => 10000,
        };

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

}
