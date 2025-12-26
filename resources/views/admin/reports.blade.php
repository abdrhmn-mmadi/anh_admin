@extends('admin.layout')

@section('page-title', 'Rapports')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">Rapports de Paiements</h1>

    <!-- Cards Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Payslip Card -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">📄 Générer Bulletin de paye</h2>

            <form id="payslipForm" method="GET" action="{{ route('admin.reports.payslip') }}" target="_blank">
                @csrf
                <input type="hidden" name="employee_id" id="employee_id">

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Rechercher Employé</label>
                    <input type="text" id="employee_search" placeholder="Nom ou NIN"
                           class="w-full border rounded p-2" list="employee-list" required>
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
                        @foreach($months as $num => $label)
                            <option value="{{ now()->year }}-{{ $num }}">
                                {{ $label }} {{ now()->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Générer Payslip
                </button>
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
                        @foreach($months as $num => $label)
                            <option value="{{ now()->year }}-{{ $num }}">
                                {{ $label }} {{ now()->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Générer Liste
                </button>
            </form>
        </div>

        <!-- Whole Payment List Card (PDF + Excel) -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">📝 Liste Complète des Paiements</h2>

            <form method="GET" action="{{ route('admin.reports.complete') }}" target="_blank">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-medium">Mois</label>
                    <select name="month" id="complete-month"
                            class="w-full border rounded p-2" required>
                        @foreach($months as $num => $label)
                            <option value="{{ now()->year }}-{{ $num }}">
                                {{ $label }} {{ now()->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <!-- PDF -->
                    <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                        Générer PDF
                    </button>

                    <!-- Excel -->
                    <a id="excelLink"
                       href="#"
                       target="_blank"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Générer Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Autre Dépense Card -->
        <div class="bg-white rounded shadow p-6 border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">💸 Autres Dépenses</h2>

            <form method="GET" action="{{ route('admin.reports.expense') }}" target="_blank">
                @csrf
                <div class="mb-4">
                    <label class="block mb-1 font-medium">Type Dépense</label>
                    <input type="text" name="expense_type"
                           placeholder="Ex: Fournitures, Transport..."
                           class="w-full border rounded p-2" required>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Mois</label>
                    <select name="month" class="w-full border rounded p-2" required>
                        @foreach($months as $num => $label)
                            <option value="{{ now()->year }}-{{ $num }}">
                                {{ $label }} {{ now()->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
                    Générer Dépense
                </button>
            </form>
        </div>

    </div>
</div>

<script>
/* Payslip employee validation */
document.getElementById('payslipForm').addEventListener('submit', function(e){
    const input = document.getElementById('employee_search');
    const datalist = document.getElementById('employee-list');
    const hidden = document.getElementById('employee_id');

    let selectedOption = Array.from(datalist.options)
        .find(option => option.value === input.value);

    if(!selectedOption){
        e.preventDefault();
        alert('Veuillez sélectionner un employé valide depuis la liste.');
    } else {
        hidden.value = selectedOption.dataset.id;
    }
});

/* Dynamic Excel link */
const monthSelect = document.getElementById('complete-month');
const excelLink = document.getElementById('excelLink');

function updateExcelLink() {
    excelLink.href =
        "{{ route('admin.reports.complete.excel') }}" + "?month=" + monthSelect.value;
}

monthSelect.addEventListener('change', updateExcelLink);
updateExcelLink();
</script>
@endsection
