@extends('admin.layout')

@section('title', 'Banques')
@section('page-title', 'Banques')

@section('content')

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Ajouter une Banque</h2>

    <!-- Add Bank Form -->
    <form method="POST" action="{{ route('admin.banks.store') }}" class="flex gap-2 mb-6">
        @csrf
        <input type="text" name="code" placeholder="Code" required
            class="px-4 py-2 border rounded-lg flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <input type="text" name="name" placeholder="Nom" required
            class="px-4 py-2 border rounded-lg flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ajouter</button>
    </form>

    <!-- Banks List -->
    <table class="w-full text-left border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Code</th>
                <th class="px-4 py-2 border">Nom</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banks as $bank)
            <tr>
                <td class="px-4 py-2 border">{{ $bank->id }}</td>

                <!-- Inline Edit Form -->
                <form method="POST" action="{{ route('admin.banks.update', $bank->id) }}" class="flex gap-2 w-full">
                    @csrf
                    @method('PUT')

                    <td class="px-2 py-2 border">
                        <input type="text" name="code" value="{{ $bank->code }}" class="w-full px-2 py-1 border rounded" required>
                    </td>
                    <td class="px-2 py-2 border">
                        <input type="text" name="name" value="{{ $bank->name }}" class="w-full px-2 py-1 border rounded" required>
                    </td>
                    <td class="px-2 py-2 border flex gap-2">
                        <button type="submit"
                            class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Modifier</button>
                </form>

                        <!-- Delete Form -->
                        <form action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                        </form>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
