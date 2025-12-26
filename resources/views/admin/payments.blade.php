@extends('admin.layout')

@section('page-title', 'Paiements')

@section('content')
<div x-data="paymentsPage()" class="space-y-6">

    <!-- ACTION BUTTONS -->
    <div class="flex gap-3 flex-wrap justify-between items-center">
        <div class="flex gap-3 flex-wrap">
            <button @click="openForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                + Nouveau Paiement
            </button>

            <button @click="showPayAll = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                💰 Payer Tous les Employés
            </button>
        </div>

        <button @click="showFilters = !showFilters"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow">
            🔍 Filtres & Recherche
        </button>
    </div>

    <!-- FILTERS MODAL -->
    <div x-cloak x-show="showFilters" x-transition
         class="bg-white border rounded shadow p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-sm font-medium block mb-1">Région</label>
                <select x-model="filterRegion" @change="applyFilters()" class="w-full border rounded p-2">
                    <option value="">Toutes</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ request('region')==$region->id?'selected':'' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium block mb-1">Mois</label>
                <select x-model="filterMonth" @change="applyFilters()" class="w-full border rounded p-2">
                    <option value="">Tous</option>
                    @php $year = now()->year; @endphp
                    @foreach($months as $num => $label)
                        <option value="{{ $year }}-{{ $num }}" {{ request('month')==$year.'-'.$num?'selected':'' }}>
                            {{ $label }} {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium block mb-1">Rechercher</label>
                <input type="text" x-model="tableSearch" @keydown.enter="applyFilters()" placeholder="Nom ou NIN..." 
                       class="w-full border rounded p-2">
            </div>

            <div>
                <button @click="resetFilters()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded w-full">
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- SINGLE PAYMENT FORM MODAL -->
    <div x-cloak x-show="showForm" x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded shadow p-6 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold" x-text="editMode ? 'Modifier Paiement' : 'Nouveau Paiement'"></h2>
                <button @click="closeForm()" class="text-2xl font-bold">&times;</button>
            </div>

            <form :action="editMode ? editUrl : '{{ route('admin.payments.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- NIN / Name Search -->
                <div x-show="!editMode" class="space-y-2 relative">
                    <label class="text-sm font-medium">NIN ou Nom Employé</label>
                    <input type="text" x-model="search" @input.debounce.300ms="searchEmployee" placeholder="Ex: 0121758 ou Nom..." 
                           class="w-full border rounded p-2">

                    <div x-cloak x-show="suggestions.length > 0" class="absolute z-50 w-full bg-white border rounded shadow mt-1 max-h-40 overflow-y-auto">
                        <template x-for="item in suggestions" :key="item.id">
                            <div @click="selectEmployee(item)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                <span x-text="item.nin"></span> - <span x-text="item.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="employee.name" class="text-gray-700">
                    <strong>Nom :</strong> <span x-text="employee.name"></span>
                </div>

                <input type="hidden" name="employee_id" x-model="employee.id" required>
                <input type="hidden" name="bank_id" x-model="employee.bank_id" required>

                <div x-show="employee.bank_name" class="text-gray-700">
                    <label class="text-sm font-medium">Banque</label>
                    <div class="p-2 border rounded bg-gray-100" x-text="employee.bank_name"></div>
                </div>

                <div>
                    <label class="text-sm font-medium">Mois</label>
                    <select name="month" x-model="employee.month" class="w-full border rounded p-2" required>
                        @foreach($months as $num => $label)
                            <option value="{{ $year }}-{{ $num }}">{{ $label }} {{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium">Salaire</label>
                        <input type="number" class="w-full bg-gray-100 border rounded p-2" x-model="employee.salary" readonly>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Bonus</label>
                        <input type="number" name="bonus" x-model="bonus" @input="calculateTotal" min="0" step="0.01" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="text-sm font-medium">IGR</label>
                        <input type="number" name="igr" class="w-full bg-gray-100 border rounded p-2" x-model="igr" readonly>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium">Total à payer</label>
                    <input type="number" name="total_amount" class="w-full bg-gray-100 border rounded p-2" x-model="total" readonly>
                </div>

                <div>
                    <label class="text-sm font-medium">Date de paiement</label>
                    <input type="date" name="payment_date" x-model="employee.payment_date" class="w-full border rounded p-2" required>
                </div>

                <input type="hidden" name="payment_type" value="Salary">

                <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded shadow" x-text="editMode ? 'Mettre à jour' : 'Payer'"></button>
            </form>
        </div>
    </div>

    <!-- PAY ALL EMPLOYEES MODAL -->
    <div x-cloak x-show="showPayAll" x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Payer Tous les Employés</h2>
                <button @click="showPayAll = false" class="text-2xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.payments.payAll') }}">
                @csrf
                <label class="text-sm font-medium">Mois de paiement</label>
                <select name="month" class="w-full mb-4 border rounded p-2" required>
                    @foreach($months as $num => $label)
                        <option value="{{ $year }}-{{ $num }}">{{ $label }} {{ $year }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded shadow">Payer Tous</button>
            </form>
        </div>
    </div>

    <!-- PAYMENTS TABLE -->
    <div class="bg-white p-6 rounded shadow overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Employé</th>
                    <th class="p-2 text-left">Région</th>
                    <th class="p-2 text-left">Banque</th>
                    <th class="p-2 text-left">Bonus</th>
                    <th class="p-2 text-left">IGR</th>
                    <th class="p-2 text-left">Total</th>
                    <th class="p-2 text-left">Date</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="p in payments" :key="p.id">
                    <tr class="border-t">
                        <td class="p-2">
                            <span x-text="p.employee.first_name + ' ' + p.employee.last_name"></span>
                            <div class="text-xs text-gray-500" x-text="p.employee.nin"></div>
                        </td>
                        <td class="p-2" x-text="p.employee.region ? p.employee.region.name : '-'"></td>
                        <td class="p-2" x-text="p.employee.bank.name"></td>
                        <td class="p-2" x-text="parseFloat(p.bonus).toFixed(2)"></td>
                        <td class="p-2" x-text="calculateIGR(p.employee.salary).toFixed(2)"></td>
                        <td class="p-2 font-semibold" x-text="(parseFloat(p.employee.salary) + parseFloat(p.bonus) - calculateIGR(p.employee.salary)).toFixed(2)"></td>
                        <td class="p-2" x-text="p.payment_date"></td>
                        <td class="p-2 flex gap-2">
                            <button type="button"
                                    @click="editPayment(p)"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                                Edit
                            </button>
                            <form :action="`/admin/payments/${p.id}`" method="POST" @submit.prevent="if(confirm('Voulez-vous supprimer ce paiement ?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div class="flex justify-between items-center mt-4">
            <div>
                {{ $payments->links() }}
            </div>
            <div>
                <label class="text-sm font-medium mr-2">Afficher par :</label>
                <select x-model="perPage" @change="applyFilters()" class="border rounded p-1">
                    <option value="10" {{ request('per_page')==10?'selected':'' }}>10</option>
                    <option value="20" {{ request('per_page')==20?'selected':'' }}>20</option>
                    <option value="50" {{ request('per_page')==50?'selected':'' }}>50</option>
                    <option value="100" {{ request('per_page')==100?'selected':'' }}>100</option>
                    <option value="200" {{ request('per_page')==200?'selected':'' }}>200</option>
                </select>
            </div>
        </div>
    </div>

</div>

<script>
function paymentsPage() {
    return {
        showForm: false,
        showPayAll: false,
        showFilters: false,
        tableSearch: '{{ request("search") }}',
        filterRegion: '{{ request("region") }}',
        filterMonth: '{{ request("month") }}',
        perPage: {{ request('per_page', 10) }},

        editMode: false,
        editUrl: '',
        search: '',
        employee: { id: '', name: '', salary: 0, bank_name: '', bank_id: '', month: '', payment_date: '' },
        bonus: 0,
        igr: 0,
        total: 0,
        suggestions: [],

        // Use precomputed JSON from the controller
        payments: @json($paymentsJson),

        openForm() { 
            this.showForm = true; 
            this.editMode = false; 
            this.resetForm(); 
        },

        closeForm() { 
            this.showForm = false; 
            this.editMode = false; 
            this.resetForm(); 
        },

        resetForm() { 
            this.employee = { id: '', name: '', salary: 0, bank_name: '', bank_id: '', month: '', payment_date: '' }; 
            this.bonus = 0; 
            this.igr = 0; 
            this.total = 0; 
            this.search = ''; 
            this.suggestions = []; 
        },

        editPayment(payment){
            this.showForm = true;
            this.editMode = true;
            this.editUrl = `/admin/payments/${payment.id}`;
            this.employee = {
                id: payment.employee_id,
                name: payment.employee.first_name + ' ' + payment.employee.last_name,
                salary: payment.employee.salary,
                bank_name: payment.employee.bank_name,
                bank_id: payment.employee.bank_id,
                month: payment.month,
                payment_date: payment.payment_date
            };
            this.bonus = payment.bonus;
            this.calculateTotal();
        },

        searchEmployee(){
            if(this.search.length < 1){ 
                this.suggestions = []; 
                return; 
            }
            fetch("{{ route('admin.payments.searchEmployee') }}", {
                method:'POST',
                headers:{ 
                    'Content-Type':'application/json', 
                    'X-CSRF-TOKEN':'{{ csrf_token() }}' 
                },
                body: JSON.stringify({ query: this.search })
            })
            .then(res => res.json())
            .then(data => {
                this.suggestions = data.status ? data.employees : [];
            });
        },

        selectEmployee(emp){
            this.employee = {
                id: emp.id,
                name: emp.name,
                salary: emp.salary,
                bank_name: emp.bank_name,
                bank_id: emp.bank_id,
                month: emp.month,
                payment_date: emp.payment_date
            };
            this.suggestions = [];
            this.calculateTotal();
        },

        calculateIGR(salary){
            if(salary <= 70000) return 2000;
            if(salary <= 80000) return 3000;
            if(salary <= 100000) return 4000;
            if(salary <= 110000) return 5000;
            return 10000;
        },

        calculateTotal(){
            this.igr = this.calculateIGR(parseFloat(this.employee.salary || 0));
            this.total = (parseFloat(this.employee.salary || 0) + parseFloat(this.bonus || 0)) - this.igr;
        },

        applyFilters(){
            let params = new URLSearchParams();
            if(this.filterRegion) params.set('region', this.filterRegion);
            if(this.filterMonth) params.set('month', this.filterMonth);
            if(this.tableSearch) params.set('search', this.tableSearch);
            if(this.perPage) params.set('per_page', this.perPage);
            window.location.href = '?' + params.toString();
        },

        resetFilters(){
            this.filterRegion = '';
            this.filterMonth = '';
            this.tableSearch = '';
            this.perPage = 10;
            this.applyFilters();
        }
    }
}
</script>

@endsection
