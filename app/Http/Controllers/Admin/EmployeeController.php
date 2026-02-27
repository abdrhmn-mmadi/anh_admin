<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Bank;
use App\Models\Region;
use App\Models\Department;
use App\Models\Service;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['bank', 'region', 'department', 'service']);

        // Search by NIN, first name, or last name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nin', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }

        // Filter by region
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by contract type
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // ✅ PAGINATION (20 default)
        $perPage = $request->get('per_page', 20);
        $employees = $query->paginate($perPage)->withQueryString();

        $banks = Bank::all();
        $regions = Region::all();
        $departments = Department::all();
        $services = Service::all();

        return view('admin.employees', compact(
            'employees',
            'banks',
            'regions',
            'departments',
            'services'
        ));
    }

    // ✅ EXPORT EXCEL WITH FILTERS
    public function export(Request $request)
    {
        $filters = $request->only([
            'search',
            'region_id',
            'department_id',
            'contract_type'
        ]);

        return Excel::download(
            new EmployeesExport($filters),
            'employees.xlsx'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'sex'            => 'required|in:M,F',
            'dob'            => 'required|date',
            'address'        => 'required|string|max:255',
            'email'          => 'required|email|unique:employees,email',
            'phone'          => 'nullable|string|max:255',
            'salary'         => 'required|numeric',
            'account_number' => 'nullable|string|max:255',
            'nin'            => 'required|string|max:50|unique:employees,nin',
            'bank_id'        => 'required|exists:banks,id',
            'region_id'      => 'required|exists:regions,id',
            'department_id'  => 'required|exists:departments,id',
            'service_id'     => 'required|exists:services,id',
            'position'       => 'required|string|max:255',
            'contract_type'  => 'required|in:CDD,CDI,Stagiaire',
            'date_recruited' => 'required|date',
        ]);

        Employee::create($request->all());

        return back()->with('success', 'Employé ajouté avec succès.');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'sex'            => 'required|in:M,F',
            'position'       => 'required|string|max:255',
            'salary'         => 'required|numeric',
            'department_id'  => 'required|exists:departments,id',
            'service_id'     => 'required|exists:services,id',
            'nin'            => 'required|string|max:50|unique:employees,nin,' . $employee->id,
        ]);

        $employee->update($request->all());

        return back()->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return back()->with('success', 'Employé supprimé avec succès.');
    }
}