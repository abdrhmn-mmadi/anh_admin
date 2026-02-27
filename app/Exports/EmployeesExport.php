<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Employee::with(['region','department','service','bank']);

        // SEARCH
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nin', 'like', "%{$search}%");
            });
        }

        // REGION
        if (!empty($this->filters['region_id'])) {
            $query->where('region_id', $this->filters['region_id']);
        }

        // DEPARTMENT
        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        // CONTRACT
        if (!empty($this->filters['contract_type'])) {
            $query->where('contract_type', $this->filters['contract_type']);
        }

        return $query->get()->map(function ($employee) {
            return [
                'ID' => $employee->id,
                'Nom' => $employee->first_name,
                'Prénom' => $employee->last_name,
                'Matricule' => $employee->nin,
                'Région' => $employee->region->name ?? '-',
                'Département' => $employee->department->name ?? '-',
                'Service' => $employee->service->name ?? '-',
                'Banque' => $employee->bank->name ?? '-',
                'Salaire (KMF)' => $employee->salary,
                'Contrat' => $employee->contract_type ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Matricule',
            'Région',
            'Département',
            'Service',
            'Banque',
            'Salaire (KMF)',
            'Contrat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Header row
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
        ];
    }
}