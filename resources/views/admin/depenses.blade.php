@extends('admin.layout')

@section('page-title', 'Dépenses')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold">Gestion des Dépenses</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Button to Show Add Form -->
    <button id="showAddFormBtn" class="bg-green-600 text-white px-4 py-2 rounded mb-4">
        ➕ Ajouter une nouvelle dépense
    </button>

    <!-- Add Expense Form (Hidden by default) -->
    <div id="addFormContainer" class="bg-white p-6 rounded shadow w-full hidden">
        <h2 class="font-bold mb-4">Nouvelle dépense</h2>
        <form method="POST" action="{{ route('admin.depenses.store') }}" class="space-y-4 w-full">
            @csrf
            <div>
                <label class="block font-medium">Dépense</label>
                <input type="text" name="depense" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">Montant (KMF)</label>
                <input type="number" name="amount" min="0" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">Mois</label>
                <select name="month" required class="w-full border rounded px-3 py-2">
                    @php
                        // Last 12 months including current month
                        $monthsList = [];
                        for ($i = 0; $i < 12; $i++) {
                            $date = \Carbon\Carbon::now()->subMonths($i);
                            $monthsList[$date->format('Y-m')] = ucfirst($date->locale('fr')->translatedFormat('F Y'));
                        }
                    @endphp
                    @foreach($monthsList as $value => $label)
                        <option value="{{ $value }}" {{ $value == now()->format('Y-m') ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="hideAddForm()" class="border px-4 py-2 rounded">Annuler</button>
                <button class="bg-green-600 text-white px-4 py-2 rounded">Ajouter</button>
            </div>
        </form>
    </div>

    <!-- Month Filter -->
    <form method="GET" action="{{ route('admin.depenses') }}" class="flex items-center gap-4 bg-white p-4 rounded shadow w-fit">
        <label class="font-medium">Mois :</label>
        <select name="month" onchange="this.form.submit()" class="border rounded px-3 py-2">
            @foreach($months as $m)
                <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->locale('fr')->translatedFormat('F Y') }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Total -->
    <div class="bg-blue-50 border border-blue-200 rounded p-4 text-blue-900 font-semibold w-fit">
        Total du mois : <span class="text-lg">{{ number_format($totalAmount, 0, ',', ' ') }} KMF</span>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded shadow overflow-hidden w-full">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="p-3 border text-left">Dépense</th>
                    <th class="p-3 border text-right">Montant</th>
                    <th class="p-3 border text-center">Date</th>
                    <th class="p-3 border text-center">Mois</th>
                    <th class="p-3 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($depenses as $depense)
                    @php
                        $depenseMonth = \Carbon\Carbon::parse($depense->created_at)->format('Y-m');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $depense->depense }}</td>
                        <td class="p-3 text-right">{{ number_format($depense->amount, 0, ',', ' ') }}</td>
                        <td class="p-3 text-center">{{ $depense->created_at->format('d/m/Y') }}</td>
                        <td class="p-3 text-center">{{ \Carbon\Carbon::createFromFormat('Y-m', $depenseMonth)->locale('fr')->translatedFormat('F Y') }}</td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <!-- Edit Button -->
                            <button type="button"
                                    onclick="openEdit({{ $depense->id }}, '{{ $depense->depense }}', {{ $depense->amount }}, '{{ $depenseMonth }}')"
                                    class="bg-yellow-500 text-white px-2 py-1 rounded">
                                ✏️ Editer
                            </button>

                            <!-- Delete Form -->
                            <form method="POST"
                                  action="{{ route('admin.depenses.destroy', $depense) }}"
                                  onsubmit="return confirm('Supprimer cette dépense ?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-2 py-1 rounded">🗑 Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            Aucune dépense pour ce mois
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center">
    <div class="bg-white p-6 rounded w-full max-w-md">
        <h2 class="font-bold mb-4">Modifier dépense</h2>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="block font-medium">Dépense</label>
                <input type="text" name="depense" id="editDepense" class="w-full border px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-medium">Montant (KMF)</label>
                <input type="number" name="amount" id="editAmount" class="w-full border px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block font-medium">Mois</label>
                <select name="month" id="editMonth" class="w-full border px-3 py-2" required>
                    @php
                        $monthsList = [];
                        for ($i = 0; $i < 12; $i++) {
                            $date = \Carbon\Carbon::now()->subMonths($i);
                            $monthsList[$date->format('Y-m')] = ucfirst($date->locale('fr')->translatedFormat('F Y'));
                        }
                    @endphp
                    @foreach($monthsList as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEdit()" class="border px-4 py-2 rounded">Annuler</button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
const addFormContainer = document.getElementById('addFormContainer');
const showAddFormBtn = document.getElementById('showAddFormBtn');
const editModal = document.getElementById('editModal');
const editForm = document.getElementById('editForm');
const editDepense = document.getElementById('editDepense');
const editAmount = document.getElementById('editAmount');
const editMonth = document.getElementById('editMonth');

showAddFormBtn.addEventListener('click', () => {
    addFormContainer.classList.remove('hidden');
    showAddFormBtn.classList.add('hidden');
});

function hideAddForm() {
    addFormContainer.classList.add('hidden');
    showAddFormBtn.classList.remove('hidden');
}

function openEdit(id, depense, amount, month) {
    editForm.action = `/admin/depenses/${id}`;
    editDepense.value = depense;
    editAmount.value = amount;
    editMonth.value = month;
    editModal.classList.remove('hidden');
    editModal.classList.add('flex');
}

function closeEdit() {
    editModal.classList.add('hidden');
}
</script>
@endsection
