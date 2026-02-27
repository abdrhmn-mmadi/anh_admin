@extends('admin.layout')

@section('title', 'Gestion des Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div x-data="userModal()" class="p-8">

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <button @click="openCreate()" class="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Ajouter un utilisateur
    </button>

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">#</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Nom</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Rôle</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Région</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Téléphone</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $user->id }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->role?->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->region?->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $user->phone }}</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <button @click="openEdit({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role_id }}', '{{ $user->region_id }}', '{{ $user->phone }}')" 
                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                            Modifier
                        </button>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600"
                                    onclick="return confirm('Voulez-vous supprimer cet utilisateur ?');">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Create Modal --}}
    <div x-show="createModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Ajouter un Utilisateur</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-2">
                    <input type="text" name="name" placeholder="Nom" class="w-full border rounded px-2 py-1" required>
                    <input type="email" name="email" placeholder="Email" class="w-full border rounded px-2 py-1" required>
                    <input type="password" name="password" placeholder="Mot de passe" class="w-full border rounded px-2 py-1" required>
                    <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" class="w-full border rounded px-2 py-1" required>
                    <select name="role_id" class="w-full border rounded px-2 py-1" required>
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="region_id" class="w-full border rounded px-2 py-1" required>
                        <option value="">Sélectionner une région</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="phone" placeholder="Téléphone" class="w-full border rounded px-2 py-1">
                    <input type="file" name="photo" class="w-full border rounded px-2 py-1">
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="closeModals()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Annuler</button>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Créer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-xl font-bold mb-4">Modifier l'Utilisateur</h2>
            <form :action="`/admin/users/${selectedUser}`" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <input type="text" name="name" placeholder="Nom" class="w-full border rounded px-2 py-1" x-model="selectedUserName">
                    <input type="email" name="email" placeholder="Email" class="w-full border rounded px-2 py-1" x-model="selectedUserEmail">
                    <input type="password" name="password" placeholder="Mot de passe (laisser vide si inchangé)" class="w-full border rounded px-2 py-1">
                    <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" class="w-full border rounded px-2 py-1">
                    <select name="role_id" class="w-full border rounded px-2 py-1" x-model="selectedUserRole">
                        @foreach($roles as $role)
                            <option :value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="region_id" class="w-full border rounded px-2 py-1" x-model="selectedUserRegion">
                        @foreach($regions as $region)
                            <option :value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="phone" placeholder="Téléphone" class="w-full border rounded px-2 py-1" x-model="selectedUserPhone">
                    <input type="file" name="photo" class="w-full border rounded px-2 py-1">
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" @click="closeModals()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Annuler</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function userModal() {
    return {
        createModal: false,
        editModal: false,
        selectedUser: null,
        selectedUserName: '',
        selectedUserEmail: '',
        selectedUserRole: '',
        selectedUserRegion: '',
        selectedUserPhone: '',
        openCreate() {
            this.createModal = true;
        },
        openEdit(id, name, email, role, region, phone) {
            this.selectedUser = id;
            this.selectedUserName = name;
            this.selectedUserEmail = email;
            this.selectedUserRole = role;
            this.selectedUserRegion = region;
            this.selectedUserPhone = phone;
            this.editModal = true;
        },
        closeModals() {
            this.createModal = false;
            this.editModal = false;
        }
    }
}
</script>
@endsection
