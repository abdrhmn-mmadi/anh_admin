<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompletePaymentsExport implements FromCollection, WithHeadings, WithStyles
{
    protected string $month;

    public function __construct(string $month)
    {
        $this->month = $month;
    }

    public function collection(): Collection
    {
        $payments = Payment::with('employee.bank')
            ->where('month', $this->month)
            ->get();

        $rows = [];
        $i = 1;
        $total = 0;

        foreach ($payments as $payment) {
            $salary = $payment->employee->salary;
            $bonus  = $payment->bonus ?? 0;

            $igr = match (true) {
                $salary <= 70000  => 2000,
                $salary <= 80000  => 3000,
                $salary <= 100000 => 4000,
                $salary <= 110000 => 5000,
                default           => 10000,
            };

            $indice = match (true) {
                $salary <= 70000  => 100,
                $salary <= 80000  => 200,
                $salary <= 100000 => 300,
                $salary <= 110000 => 400,
                default           => 500,
            };

            $net = ($salary + $bonus) - $igr;
            $total += $net;

            $rows[] = [
                $i++,
                $payment->employee->first_name . ' ' . $payment->employee->last_name,
                $payment->employee->nin,
                $payment->employee->bank->name ?? '-',
                $salary,
                $bonus,
                $indice,
                $net,
            ];
        }

        // Total row
        $rows[] = ['', '', '', '', '', '', 'TOTAL', $total];

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            '#',
            'Nom',
            'NIN',
            'Banque / IBG',
            'Salaire',
            'Bonus',
            'Indice',
            'Net à payer',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFont()->setBold(true);

        return [];
    }
}
