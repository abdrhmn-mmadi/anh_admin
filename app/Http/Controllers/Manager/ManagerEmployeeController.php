<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Region;
use Illuminate\Http\Request;

class ManagerEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::all();

        $query = Employee::with(['region', 'department', 'bank']);

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nin', 'like', "%{$search}%");
            });
        }

        // 🌍 Region filter
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Pagination size
        $perPage = $request->input('per_page', 10);

        // Clone query for salary calculation
        $salaryQuery = clone $query;

        $employees = $query
            ->paginate($perPage)
            ->withQueryString();

        $totalSalary = $salaryQuery->sum('salary');

        return view('manager.employees', compact(
            'employees',
            'regions',
            'totalSalary'
        ));
    }

    /**
     * 📤 Export employees to CSV (Excel compatible)
     */
    public function export(Request $request)
    {
        $query = Employee::with(['region', 'department', 'bank']);

        // Same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nin', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        $fileName = 'employees_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // CSV header row
            fputcsv($handle, [
                'ID',
                'Nom',
                'NIN',
                'Région',
                'Département',
                'Banque',
                'Type de contrat',
                'Salaire'
            ]);

            $query->chunk(500, function ($employees) use ($handle) {
                foreach ($employees as $employee) {
                    fputcsv($handle, [
                        $employee->id,
                        $employee->first_name . ' ' . $employee->last_name,
                        $employee->nin,
                        optional($employee->region)->name,
                        optional($employee->department)->name,
                        optional($employee->bank)->name,
                        $employee->contract_type,
                        $employee->salary,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
