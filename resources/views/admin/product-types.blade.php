@extends('admin.layout')

@section('title', 'Types de Produits')
@section('page-title', 'Types de Produits')

@section('content')

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Ajouter un Type de Produit</h2>

    <!-- Add Product Type Form -->
    <form method="POST" action="{{ route('admin.product-types.store') }}" class="flex gap-2 mb-6">
        @csrf
        <input type="text" name="name" placeholder="Nom" required
            class="px-4 py-2 border rounded-lg flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <input type="number" step="0.01" name="price" placeholder="Prix" required
            class="px-4 py-2 border rounded-lg flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ajouter</button>
    </form>

    <!-- List of Product Types -->
    <table class="w-full text-left border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Nom</th>
                <th class="px-4 py-2 border">Prix (KMF)</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productTypes as $type)
            <tr x-data="{ editing: false, name: '{{ $type->name }}', price: '{{ $type->price }}' }">
                <td class="px-4 py-2 border">{{ $type->id }}</td>

                <!-- Name -->
                <td class="px-4 py-2 border">
                    <span x-show="!editing">{{ $type->name }}</span>
                    <input x-show="editing" type="text" x-model="name"
                        class="px-2 py-1 border rounded w-full" />
                </td>

                <!-- Price -->
                <td class="px-4 py-2 border">
                    <span x-show="!editing">{{ number_format($type->price, 2) }}</span>
                    <input x-show="editing" type="number" step="0.01" x-model="price"
                        class="px-2 py-1 border rounded w-full" />
                </td>

                <!-- Actions -->
                <td class="px-4 py-2 border flex gap-2">
                    <!-- Edit / Save -->
                    <button x-show="!editing" @click.prevent="editing = true"
                        class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Modifier</button>

                    <form x-show="editing" :action="'/admin/product-types/' + {{ $type->id }}" method="POST" class="flex gap-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" :value="name" />
                        <input type="hidden" name="price" :value="price" />
                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">Enregistrer</button>
                        <button type="button" @click="editing=false"
                            class="px-2 py-1 bg-gray-400 text-white rounded hover:bg-gray-500">Annuler</button>
                    </form>

                    <!-- Delete -->
                    <form action="{{ route('admin.product-types.destroy', $type->id) }}" method="POST"
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
