@extends('admin.layout')

@section('page-title', 'Paiements')

@section('content')
<div x-data="paymentsPage()" x-init="init()" class="space-y-6">

    <!-- FILTERS -->
    <div class="flex gap-3 flex-wrap justify-between items-center">

        <div class="flex gap-3 flex-wrap items-center">

            <!-- New Payment -->
            <button @click="openForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                + Nouveau Paiement
            </button>

            <!-- Pay All -->
            <button @click="showPayAll = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                💰 Payer Tous
            </button>

            <!-- 🔍 Live Search -->
            <input type="text"
                   x-model.debounce.500ms="tableSearch"
                   @input="applyFilters"
                   placeholder="Nom, NIN..."
                   class="border rounded p-2">

            <!-- 📍 Region -->
            <select x-model="region" @change="applyFilters" class="border rounded p-2">
                <option value="">Toutes Régions</option>
                @foreach($regions as $r)
                    <option value="{{ $r->id }}" {{ request('region') == $r->id ? 'selected' : '' }}>
                        {{ $r->name }}
                    </option>
                @endforeach
            </select>

            <!-- 📅 Month -->
            <select x-model="month" @change="applyFilters" class="border rounded p-2">
                @foreach($availableMonths as $m)
                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}
                    </option>
                @endforeach
            </select>

        </div>
    </div>

    <!-- SINGLE PAYMENT FORM MODAL -->
    <div x-cloak x-show="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded shadow p-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold" x-text="editMode ? 'Modifier Paiement' : 'Nouveau Paiement'"></h2>
                <button @click="closeForm()" class="text-2xl font-bold">&times;</button>
            </div>

            <form :action="editMode ? editUrl : '{{ route('admin.payments.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Employee search -->
                <input type="text" x-model="search" @input.debounce.300ms="searchEmployee"
                       placeholder="Chercher employé..."
                       class="w-full border rounded p-2 mb-2">

                <div x-show="suggestions.length" class="border rounded bg-white max-h-40 overflow-y-auto">
                    <template x-for="item in suggestions" :key="item.id">
                        <div @click="selectEmployee(item)"
                             class="p-2 hover:bg-gray-100 cursor-pointer">
                            <span x-text="item.name"></span>
                        </div>
                    </template>
                </div>

                <input type="hidden" name="employee_id" x-model="employee.id">

                <!-- Month -->
                <select name="month" x-model="employee.month" class="w-full border rounded p-2 mt-3" required>
                    <template x-for="month in monthsList">
                        <option :value="month.value" x-text="month.label"></option>
                    </template>
                </select>

                <!-- Bank -->
                <select name="bank_id" x-model="employee.bank_id" class="w-full border rounded p-2 mt-2" required>
                    <option value="" disabled>Choisir la banque</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" :selected="employee.bank_id == {{ $bank->id }}">
                            {{ $bank->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Salary -->
                <input type="number" x-model="employee.salary" readonly class="w-full mt-2 border p-2">

                <!-- Bonus -->
                <input type="number" name="bonus" x-model="bonus" @input="calculateTotal"
                       class="w-full mt-2 border p-2">

                <!-- Total -->
                <input type="number" name="total_amount" x-model="total" readonly
                       class="w-full mt-2 border p-2">

                <!-- Date -->
                <input type="date" name="payment_date" x-model="employee.payment_date"
                       class="w-full mt-2 border p-2">

                <input type="hidden" name="payment_type" value="Salary">

                <button class="w-full bg-green-600 text-white py-2 mt-3 rounded">
                    Enregistrer
                </button>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white p-6 rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Employé</th>
                    <th class="p-2">Région</th>
                    <th class="p-2">Banque</th>
                    <th class="p-2">Indemnité</th>
                    <th class="p-2">IGR</th>
                    <th class="p-2">Total</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="p in payments" :key="p.id">
                    <tr class="border-t">
                        <td class="p-2" x-text="p.employee.first_name + ' ' + p.employee.last_name"></td>
                        <td class="p-2" x-text="p.employee.region ? p.employee.region.name : '-'"></td>
                        <td class="p-2" x-text="p.employee.bank.name"></td>
                        <td class="p-2" x-text="p.bonus"></td>
                        <td class="p-2" x-text="calculateIGR(p.employee.salary)"></td>
                        <td class="p-2 font-bold"
                            x-text="(parseFloat(p.employee.salary) + parseFloat(p.bonus) - calculateIGR(p.employee.salary))">
                        </td>
                        <td class="p-2" x-text="p.payment_date"></td>
                        <td class="p-2 flex gap-2">
                            <button @click="editPayment(p)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">✏️</button>
                            <form :action="`/admin/payments/${p.id}`" method="POST" @submit.prevent="if(confirm('Voulez-vous supprimer ce paiement ?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">🗑️</button>
                            </form>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>

</div>

<script>
function paymentsPage() {
    return {
        showForm: false,
        showPayAll: false,
        editMode: false,
        editUrl: '',

        tableSearch: '{{ request("search") }}',
        perPage: {{ request('per_page', 10) }},
        region: '{{ request("region") }}',
        month: '{{ $selectedMonth }}',

        employee: { id: '', salary: 0, month: '', payment_date: '', bank_id: '' },
        bonus: 0,
        total: 0,
        suggestions: [],
        search: '',
        payments: @json($paymentsJson),

        monthsList: [],

        init() {
            this.generateMonths();
        },

        generateMonths() {
            let now = new Date();
            for (let i = 0; i < 12; i++) {
                let d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                this.monthsList.push({
                    value: d.toISOString().slice(0, 7),
                    label: d.toLocaleString('fr-FR', { month: 'long', year: 'numeric' })
                });
            }
        },

        applyFilters() {
            let params = new URLSearchParams();

            if (this.tableSearch) params.set('search', this.tableSearch);
            if (this.region) params.set('region', this.region);
            if (this.month) params.set('month', this.month);
            if (this.perPage) params.set('per_page', this.perPage);

            window.location.href = '?' + params.toString();
        },

        openForm() {
            this.showForm = true;
            this.editMode = false;
            this.employee = { id: '', salary: 0, month: '', payment_date: '', bank_id: '' };
            this.bonus = 0;
            this.total = 0;
        },

        closeForm() {
            this.showForm = false;
            this.editMode = false;
            this.employee = { id: '', salary: 0, month: '', payment_date: '', bank_id: '' };
            this.bonus = 0;
            this.total = 0;
        },

        searchEmployee() {
            fetch("{{ route('admin.payments.searchEmployee') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query: this.search })
            })
            .then(res => res.json())
            .then(data => this.suggestions = data.employees || []);
        },

        selectEmployee(emp) {
            this.employee = {
                id: emp.id,
                salary: emp.salary,
                bank_id: emp.bank ? emp.bank.id : '',
                month: '',
                payment_date: ''
            };
            this.suggestions = [];
            this.calculateTotal();
        },

        editPayment(payment) {
            this.showForm = true;
            this.editMode = true;
            this.editUrl = `/admin/payments/${payment.id}`;

            this.employee = {
                id: payment.employee_id,
                salary: payment.employee.salary,
                bank_id: payment.bank_id || (payment.employee.bank ? payment.employee.bank.id : ''),
                month: payment.month,
                payment_date: payment.payment_date
            };
            this.bonus = payment.bonus;
            this.calculateTotal();
        },

        calculateIGR(salary){
            salary = parseFloat(salary || 0);
            if (salary <= 70000) return (salary * 0.025) + 350;
            if (salary <= 85000) return (salary * 0.035) + 425;
            if (salary <= 110000) return (salary * 0.045) + 500;
            if (salary <= 250000) return (salary * 0.08);
            if (salary <= 300000) return (salary * 0.11) + 1510;
            return (salary * 0.15) + 2633;
        },

        calculateTotal(){
            let igr = this.calculateIGR(this.employee.salary);
            this.total = (parseFloat(this.employee.salary || 0) + parseFloat(this.bonus || 0)) - igr;
        }
    }
}
</script>

@endsection
