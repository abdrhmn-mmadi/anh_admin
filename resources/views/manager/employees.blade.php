@extends('manager.layout')

@section('title', 'Employees')
@section('page-title', 'Employees List')

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

    {{-- Filters --}}
    <div class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition">
        <form action="{{ route('manager.employees') }}"
              method="GET"
              class="flex flex-col md:flex-row gap-2 items-start md:items-center"
              x-data
              @change="$el.submit()"
              @keyup.debounce.500ms="$el.submit()">

            {{-- Search --}}
            <input type="text"
                   name="search"
                   placeholder="Search by name or NIN"
                   value="{{ request('search') }}"
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-green-400">

            {{-- Region --}}
            <select name="region_id" class="px-4 py-2 border rounded-lg">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}"
                        @selected(request('region_id') == $region->id)>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>

            {{-- Per page --}}
            <select name="per_page" class="px-4 py-2 border rounded-lg">
                @foreach([10, 20, 50, 100, 200] as $size)
                    <option value="{{ $size }}"
                        @selected(request('per_page', 10) == $size)>
                        {{ $size }} par page
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Export --}}
    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition flex items-center gap-3">
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4v16m8-8H4"/>
        </svg>

        <a href="{{ route('manager.employees.export', request()->all()) }}"
           class="text-green-700 font-semibold hover:underline">
            Exporter la liste en Excel
        </a>
    </div>
</div>

{{-- Totals --}}
<div class="mb-4 p-4 bg-white shadow rounded-lg flex flex-col md:flex-row justify-between items-center gap-4">

    <div class="flex items-center gap-2">
        <span class="text-gray-700 font-semibold">Total Employees:</span>
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold text-lg">
            {{ $employees->total() }}
        </span>
    </div>

    <div class="flex items-center gap-2">
        <span class="text-gray-700 font-semibold">Salaire Total:</span>
        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold text-lg">
            {{ number_format($totalSalary, 2) }} KMF
        </span>
    </div>
</div>

{{-- Table --}}
<div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIN</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Région</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Département</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Banque</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contrat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salaire (KMF)</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($employees as $employee)
                <tr>
                    <td class="px-6 py-4">{{ $employee->id }}</td>
                    <td class="px-6 py-4">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td class="px-6 py-4">{{ $employee->nin }}</td>
                    <td class="px-6 py-4">{{ optional($employee->region)->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ optional($employee->department)->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ optional($employee->bank)->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $employee->contract_type }}</td>
                    <td class="px-6 py-4">{{ number_format($employee->salary, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-6 text-center text-gray-500">
                        No employees found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="p-4">
        {{ $employees->withQueryString()->links() }}
    </div>
</div>

@endsection
