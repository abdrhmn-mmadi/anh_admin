@extends('admin.layout')

@section('page-title', 'Rapports')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">Rapports de Paiements</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Payslip Card -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">📄 Générer Bulletin de paye</h2>
            <form id="payslipForm" method="GET" action="{{ route('admin.reports.payslip') }}" target="_blank">
                @csrf
                <input type="hidden" name="employee_id" id="employee_id">

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Rechercher Employé</label>
                    <input type="text" id="employee_search"
                           class="w-full border rounded p-2"
                           placeholder="Nom ou Matricule"
                           list="employee-list" required>
                    <datalist id="employee-list">
                        @foreach($employees as $employee)
                            <option data-id="{{ $employee->id }}"
                                value="{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->nin }})">
                        @endforeach
                    </datalist>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Mois</label>
                    <select name="month" class="w-full border rounded p-2" required>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">Générer Fiche de Paye</button>
            </form>
        </div>

        <!-- Bank Payments Card -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">💰 Liste Paiements Banques</h2>
            <form method="GET" action="{{ route('admin.reports.bank') }}" target="_blank">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Banque</label>
                    <select name="bank_id" class="w-full border rounded p-2" required>
                        <option value="">-- Choisir une banque --</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Mois</label>
                    <select name="month" class="w-full border rounded p-2" required>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="bg-green-600 text-white px-4 py-2 rounded">Générer Liste</button>
            </form>
        </div>

        <!-- Complete Payments Card -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">📝 Liste Complète des Paiements</h2>
            <form method="GET" action="{{ route('admin.reports.complete') }}" target="_blank">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-medium">Mois</label>
                    <select name="month" id="complete-month" class="w-full border rounded p-2" required>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <button class="bg-purple-600 text-white px-4 py-2 rounded">Générer PDF</button>
                    <a id="excelLink" class="bg-green-600 text-white px-4 py-2 rounded" target="_blank">
                        Générer Excel
                    </a>
                </div>
            </form>
        </div>

<!-- ✅ DÉPENSES CARD (Month dropdown only) -->
<div class="bg-white rounded shadow p-6 border border-gray-200">
    <h2 class="text-lg font-semibold mb-4">💸 Dépenses par Mois</h2>

    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\DB;

        // Get distinct months from depenses table (YYYY-MM)
        $depenseMonths = DB::table('depenses')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'))
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month');
    @endphp

    <form method="POST" action="{{ route('admin.depenses.pdf') }}" target="_blank">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-medium">Mois</label>
            <select name="month" class="w-full border rounded p-2" required>
                @foreach($depenseMonths as $m)
                    <option value="{{ $m }}">
                        {{ Carbon::createFromFormat('Y-m', $m)->locale('fr')->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="bg-yellow-600 text-white px-4 py-2 rounded">
            Générer PDF Dépenses
        </button>
    </form>
</div>


    </div>
</div>

<script>
/* Payslip validation */
document.getElementById('payslipForm').addEventListener('submit', function(e){
    const input = document.getElementById('employee_search');
    const hidden = document.getElementById('employee_id');
    const options = document.querySelectorAll('#employee-list option');

    const found = Array.from(options).find(o => o.value === input.value);
    if (!found) {
        e.preventDefault();
        alert('Veuillez sélectionner un employé valide.');
    } else {
        hidden.value = found.dataset.id;
    }
});

/* Excel dynamic link */
const monthSelect = document.getElementById('complete-month');
const excelLink = document.getElementById('excelLink');

function updateExcelLink() {
    excelLink.href = "{{ route('admin.reports.complete.excel') }}?month=" + monthSelect.value;
}

monthSelect.addEventListener('change', updateExcelLink);
updateExcelLink();
</script>
@endsection
