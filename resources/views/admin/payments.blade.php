@extends('admin.layout')

@section('page-title', 'Paiements')

@section('content')
<div x-data="paymentsPage()" class="space-y-6">

    <!-- ACTION BUTTONS -->
    <div class="flex gap-3">
        <button @click="openForm()"
                class="bg-blue-600 text-white px-4 py-2 rounded">
            + Nouveau Paiement
        </button>

        <button @click="showPayAll = true"
                class="bg-green-600 text-white px-4 py-2 rounded">
            💰 Payer Tous les Employés
        </button>
    </div>

    <!-- ===================== -->
    <!-- SINGLE PAYMENT FORM (Add/Edit) -->
    <!-- ===================== -->
    <div x-show="showForm" x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded shadow p-6" x-data="paymentForm()">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold" x-text="editMode ? 'Modifier Paiement' : 'Nouveau Paiement'"></h2>
                <button @click="closeForm()" class="text-xl">&times;</button>
            </div>

            <form :action="editMode ? editUrl : '{{ route('admin.payments.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- NIN SEARCH (only for adding new) -->
                <div x-show="!editMode">
                    <label class="text-sm">NIN Employé</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" x-model="search" placeholder="Ex: 0121758" class="flex-1 border rounded p-2">
                        <button type="button" @click="searchEmployee" class="bg-blue-600 text-white px-4 rounded">
                            Rechercher
                        </button>
                    </div>
                </div>

                <div x-show="employee.name" class="mb-2">
                    <strong>Nom :</strong> <span x-text="employee.name"></span>
                </div>

                <input type="hidden" name="employee_id" x-model="employee.id" required>

                <!-- BANK -->
                <label class="text-sm">Banque</label>
                <select name="bank_id" class="w-full mb-3 border rounded p-2" x-model="employee.bank_id" required>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>

                <!-- MONTH -->
                <label class="text-sm">Mois de paiement</label>
                <select name="month" class="w-full mb-3 border rounded p-2" x-model="employee.month" required>
                    @php
                        $year = now()->year;
                        $months = [
                            '01' => 'Janvier',
                            '02' => 'Février',
                            '03' => 'Mars',
                            '04' => 'Avril',
                            '05' => 'Mai',
                            '06' => 'Juin',
                            '07' => 'Juillet',
                            '08' => 'Août',
                            '09' => 'Septembre',
                            '10' => 'Octobre',
                            '11' => 'Novembre',
                            '12' => 'Décembre',
                        ];
                    @endphp
                    @foreach($months as $num => $label)
                        <option value="{{ $year }}-{{ $num }}">{{ $label }} {{ $year }}</option>
                    @endforeach
                </select>

                <!-- SALARY -->
                <label class="text-sm">Salaire</label>
                <input type="number" class="w-full mb-3 bg-gray-100 border rounded p-2" x-model="employee.salary" readonly>

                <!-- BONUS -->
                <label class="text-sm">Bonus</label>
                <input type="number" name="bonus" x-model="bonus" @input="calculateTotal" min="0" step="0.01" class="w-full mb-3 border rounded p-2">

                <!-- TOTAL -->
                <label class="text-sm">Total à payer</label>
                <input type="number" class="w-full mb-3 bg-gray-100 border rounded p-2" x-model="total" readonly>

                <!-- DATE -->
                <label class="text-sm">Date de paiement</label>
                <input type="date" name="payment_date" x-model="employee.payment_date" class="w-full mb-3 border rounded p-2" required>

                <input type="hidden" name="payment_type" value="Salary">

                <button class="w-full bg-green-600 text-white py-2 rounded" x-text="editMode ? 'Mettre à jour' : 'Payer'"></button>
            </form>
        </div>
    </div>

    <!-- ===================== -->
    <!-- PAY ALL EMPLOYEES -->
    <!-- ===================== -->
    <div x-show="showPayAll" x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Payer Tous les Employés</h2>
                <button @click="showPayAll = false" class="text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.payments.payAll') }}">
                @csrf
                <label class="text-sm">Mois de paiement</label>
                <select name="month" class="w-full mb-4 border rounded p-2" required>
                    @foreach($months as $num => $label)
                        <option value="{{ $year }}-{{ $num }}">{{ $label }} {{ $year }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-green-700 text-white py-2 rounded">Payer Tous</button>
            </form>
        </div>
    </div>

    <!-- ===================== -->
    <!-- TABLE SEARCH -->
    <!-- ===================== -->
    <input type="text"
           x-model="tableSearch"
           placeholder="🔍 Rechercher par nom ou NIN..."
           class="w-full border rounded p-2">

    <!-- ===================== -->
    <!-- PAYMENTS TABLE -->
    <!-- ===================== -->
    <div class="bg-white p-6 rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Employé</th>
                    <th class="p-2">Banque</th>
                    <th class="p-2">Bonus</th>
                    <th class="p-2">Total</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                <tr class="border-t"
                    x-show="matches('{{ strtolower($p->employee->first_name.' '.$p->employee->last_name.' '.$p->employee->nin) }}')">
                    <td class="p-2">
                        {{ $p->employee->first_name }} {{ $p->employee->last_name }}
                        <div class="text-xs text-gray-500">{{ $p->employee->nin }}</div>
                    </td>
                    <td class="p-2">{{ $p->bank->name }}</td>
                    <td class="p-2">{{ number_format($p->bonus, 2) }}</td>
                    <td class="p-2 font-semibold">{{ number_format($p->total_amount, 2) }}</td>
                    <td class="p-2">{{ $p->payment_date }}</td>
                    <td class="p-2 flex gap-2">
                        <button type="button"
                                @click="editPayment({{ json_encode($p) }})"
                                class="bg-yellow-500 text-white px-2 py-1 rounded">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('admin.payments.destroy', $p->id) }}" onsubmit="return confirm('Voulez-vous supprimer ce paiement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-2 py-1 rounded">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- ===================== -->
<!-- ALPINE JS -->
<!-- ===================== -->
<script>
function paymentsPage() {
    return {
        showForm: false,
        showPayAll: false,
        tableSearch: '',

        editMode: false,
        editUrl: '',
        employee: {},
        bonus: 0,
        total: 0,

        matches(text) {
            return text.includes(this.tableSearch.toLowerCase());
        },

        openForm() {
            this.showForm = true;
            this.editMode = false;
            this.employee = {};
            this.bonus = 0;
            this.total = 0;
        },

        editPayment(payment) {
            this.showForm = true;
            this.editMode = true;
            this.editUrl = `/admin/payments/${payment.id}`;
            this.employee = {
                id: payment.employee_id,
                name: payment.employee.first_name + ' ' + payment.employee.last_name,
                salary: payment.total_amount - payment.bonus,
                bank_id: payment.bank_id,
                month: payment.month,
                payment_date: payment.payment_date,
            };
            this.bonus = payment.bonus;
            this.total = payment.total_amount;
        },

        closeForm() {
            this.showForm = false;
            this.editMode = false;
            this.employee = {};
            this.bonus = 0;
            this.total = 0;
        }
    }
}

function paymentForm() {
    return {
        search: '',
        employee: { id: '', salary: 0, name: '', nin: '' },
        bonus: 0,
        total: 0,

        searchEmployee() {
            if (this.search.length < 2) return;

            fetch("{{ route('admin.payments.searchEmployee') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nin: this.search })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.employee = data.employee;
                    this.bonus = 0;
                    this.total = parseFloat(data.employee.salary);
                } else {
                    alert(data.message);
                }
            });
        },

        calculateTotal() {
            this.total = parseFloat(this.employee.salary || 0) + parseFloat(this.bonus || 0);
        }
    }
}
</script>
@endsection
