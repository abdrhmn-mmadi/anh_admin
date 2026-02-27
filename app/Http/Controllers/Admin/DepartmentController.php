<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Service;

class DepartmentController extends Controller
{
    // =============================
    // Show departments & services
    // =============================
    public function index()
    {
        $departments = Department::with('services')->get();
        $services = Service::with('department')->get();

        return view('admin.departments', compact('departments', 'services'));
    }

    // =============================
    // Create or Update Department
    // =============================
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->filled('id')) {
            $department = Department::findOrFail($request->id);
            $department->update(['name' => $request->name]);

            return back()->with('success', 'Département mis à jour.');
        }

        Department::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Département ajouté.');
    }

    // =============================
    // Create or Update Service
    // =============================
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($request->filled('id')) {
            $service = Service::findOrFail($request->id);
            $service->update([
                'name' => $request->name,
                'department_id' => $request->department_id
            ]);

            return back()->with('success', 'Service mis à jour.');
        }

        Service::create([
            'name' => $request->name,
            'department_id' => $request->department_id
        ]);

        return back()->with('success', 'Service ajouté.');
    }

    // =============================
    // Delete Department
    // =============================
    public function destroyDepartment($id)
    {
        $department = Department::findOrFail($id);

        // Optional safety check
        if ($department->services()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un département contenant des services.');
        }

        $department->delete();

        return back()->with('success', 'Département supprimé.');
    }

    // =============================
    // Delete Service
    // =============================
    public function destroyService($id)
    {
        Service::findOrFail($id)->delete();

        return back()->with('success', 'Service supprimé.');
    }

    // =============================
    // AJAX: Get services by department
    // =============================
    public function services(Department $department)
    {
        return response()->json(
            $department->services()->select('id', 'name')->get()
        );
    }
}
