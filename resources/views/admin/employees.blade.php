@extends('admin.layout')

@section('title', 'Employés')
@section('page-title', 'Employés')

@section('content')
<div x-data="employeeModal()" class="p-8">

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- FILTER CARD --}}
    <div class="mb-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 text-gray-700">Filtrer les Employés</h2>
        <form method="GET" action="{{ route('admin.employees.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Rechercher par NIN ou Nom"
                   class="border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">

            <select name="region_id" class="border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Toutes les Régions</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" @selected(request('region_id') == $region->id)>{{ $region->name }}</option>
                @endforeach
            </select>

            <select name="department_id" class="border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Tous les Départements</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>

            <select name="contract_type" class="border border-gray-300 rounded p-2 focus:ring-1 focus:ring-blue-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Tous les Contrats</option>
                <option value="CDD" @selected(request('contract_type') == 'CDD')>CDD</option>
                <option value="CDI" @selected(request('contract_type') == 'CDI')>CDI</option>
                <option value="Stagiaire" @selected(request('contract_type') == 'Stagiaire')>Stagiaire</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                Filtrer
            </button>
        </form>
    </div>

    {{-- ADD BUTTON --}}
    <button @click="openCreate()"
            class="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
        Ajouter un Employé
    </button>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3">#</th>
                <th class="px-6 py-3">Nom</th>
                <th class="px-6 py-3">Poste</th>
                <th class="px-6 py-3">Département</th>
                <th class="px-6 py-3">Service</th>
                <th class="px-6 py-3">Contrat</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
            </thead>

            <tbody class="divide-y bg-white">
            @foreach($employees as $employee)
                <tr>
                    <td class="px-6 py-4">{{ $employee->id }}</td>
                    <td class="px-6 py-4">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td class="px-6 py-4">{{ $employee->position }}</td>
                    <td class="px-6 py-4">{{ $employee->department->name }}</td>
                    <td class="px-6 py-4">{{ $employee->service->name }}</td>
                    <td class="px-6 py-4">{{ $employee->contract_type }}</td>
                    <td class="px-6 py-4 space-x-2">
                        <button
                            @click='openEdit(@json($employee))'
                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition">
                            Modifier
                        </button>

                        <form method="POST"
                              action="{{ route('admin.employees.destroy',$employee) }}"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Supprimer ?')"
                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= MODAL (CREATE + EDIT) ================= --}}
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-[900px] max-h-screen overflow-y-auto">
            <h2 class="text-xl font-bold mb-4"
                x-text="isEdit ? 'Modifier l\'Employé' : 'Ajouter un Employé'"></h2>

            <form :action="formAction" method="POST"
                  class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- IDENTITÉ --}}
                <input name="first_name" x-model="form.first_name"
                       placeholder="Nom" class="border rounded p-2" required>

                <input name="last_name" x-model="form.last_name"
                       placeholder="Prénom" class="border rounded p-2" required>

                <input name="nin" x-model="form.nin"
                       placeholder="NIN" class="border rounded p-2" required>

                {{-- DOB --}}
                <div class="flex flex-col">
                    <label class="text-xs text-gray-600 mb-1">Date de naissance</label>
                    <input type="date" name="dob"
                           x-model="form.dob"
                           class="border rounded p-2" required>
                </div>

                {{-- CONTACT --}}
                <input name="address" x-model="form.address"
                       placeholder="Adresse" class="border rounded p-2" required>

                <input type="email" name="email" x-model="form.email"
                       placeholder="Email" class="border rounded p-2" required>

                <input name="phone" x-model="form.phone"
                       placeholder="Téléphone" class="border rounded p-2">

                {{-- FINANCE --}}
                <input type="number" step="0.01" name="salary"
                       x-model="form.salary"
                       placeholder="Salaire"
                       class="border rounded p-2" required>

                <input name="account_number" x-model="form.account_number"
                       placeholder="N° Compte" class="border rounded p-2">

                <select name="bank_id" x-model="form.bank_id"
                        class="border rounded p-2" required>
                    <option value="">Banque</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>

                {{-- ORGANISATION --}}
                <select name="region_id" x-model="form.region_id"
                        class="border rounded p-2" required>
                    <option value="">Région</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>

                <select name="department_id"
                        x-model="form.department_id"
                        @change="fetchServices()"
                        class="border rounded p-2" required>
                    <option value="">Département</option>
                    @foreach($departments as $department)
                        <option :value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>

                <select name="service_id"
                        x-model="form.service_id"
                        class="border rounded p-2" required>
                    <option value="">Service</option>
                    <template x-for="service in services" :key="service.id">
                        <option :value="service.id" x-text="service.name"
                                :selected="service.id === form.service_id"></option>
                    </template>
                </select>

                <input name="position" x-model="form.position"
                       placeholder="Poste" class="border rounded p-2" required>

                <select name="contract_type" x-model="form.contract_type"
                        class="border rounded p-2" required>
                    <option value="">Contrat</option>
                    <option value="CDD">CDD</option>
                    <option value="CDI">CDI</option>
                    <option value="Stagiaire">Stagiaire</option>
                </select>

                {{-- RECRUITMENT DATE --}}
                <div class="flex flex-col">
                    <label class="text-xs text-gray-600 mb-1">Date de recrutement</label>
                    <input type="date" name="date_recruited"
                           x-model="form.date_recruited"
                           class="border rounded p-2" required>
                </div>

                {{-- ACTIONS --}}
                <div class="col-span-3 flex justify-end gap-2">
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function employeeModal() {
    return {
        showModal: false,
        isEdit: false,
        services: [],
        formAction: '',
        form: {
            first_name: '',
            last_name: '',
            nin: '',
            dob: '',
            address: '',
            email: '',
            phone: '',
            salary: '',
            account_number: '',
            bank_id: '',
            region_id: '',
            department_id: '',
            service_id: '',
            position: '',
            contract_type: '',
            date_recruited: ''
        },

        openCreate() {
            this.resetForm();
            this.formAction = "{{ route('admin.employees.store') }}";
            this.isEdit = false;
            this.showModal = true;
        },

        openEdit(employee) {
            this.resetForm();
            this.form = { ...employee };
            this.formAction = `/admin/employees/${employee.id}`;
            this.isEdit = true;
            this.showModal = true;

            if (employee.department_id) {
                fetch(`/admin/departments/${employee.department_id}/services`)
                    .then(res => res.json())
                    .then(data => {
                        this.services = data;
                        this.form.service_id = employee.service_id;
                    });
            }
        },

        fetchServices() {
            if (!this.form.department_id) {
                this.services = [];
                this.form.service_id = '';
                return;
            }

            fetch(`/admin/departments/${this.form.department_id}/services`)
                .then(res => res.json())
                .then(data => {
                    this.services = data;
                    if (!this.isEdit) this.form.service_id = '';
                });
        },

        closeModal() {
            this.showModal = false;
        },

        resetForm() {
            this.form = {
                first_name: '',
                last_name: '',
                nin: '',
                dob: '',
                address: '',
                email: '',
                phone: '',
                salary: '',
                account_number: '',
                bank_id: '',
                region_id: '',
                department_id: '',
                service_id: '',
                position: '',
                contract_type: '',
                date_recruited: ''
            };
            this.services = [];
        }
    }
}
</script>
@endsection
