@extends('agent.layout')

@section('title', 'Gestion des Produits')
@section('page-title', 'Gestion des Stocks')

@section('content')

@if(session('success'))
<div class="bg-green-100 text-green-700 p-4 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="bg-red-100 text-red-700 p-4 rounded mb-4">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="mb-4">
    <button id="toggleFormBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        {{ $editStock ? 'Modifier le stock' : 'Ajouter un stock' }}
    </button>
</div>

<div id="stockForm" class="bg-white p-6 rounded-lg shadow mb-6 {{ $editStock ? '' : 'hidden' }}">
    <h2 class="text-xl font-semibold mb-4">{{ $editStock ? 'Modifier le stock' : 'Ajouter un stock' }}</h2>
    <form method="POST" action="{{ $editStock ? route('agent.products.update', $editStock->id) : route('agent.products.store') }}" class="space-y-4">
        @csrf
        @if($editStock)
            @method('PUT')
        @endif

        <!-- Product Selection -->
        <div>
            <label>Produit</label>
            <select name="product_id" class="border p-2 w-full" required>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ $editStock && $editStock->product_id == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Region -->
        <div>
            <label>Région</label>
            <input type="text" class="border p-2 w-full bg-gray-100" 
                   value="{{ Auth::user()->region->name ?? 'Non défini' }}" disabled>
            <input type="hidden" name="region_id" value="{{ Auth::user()->region_id }}">
        </div>

        <!-- Quantity -->
        <div>
            <label>Quantité</label>
            <input type="number" name="quantity" class="border p-2 w-full" min="1" 
                   value="{{ $editStock ? $editStock->quantity_available : '' }}" required>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
            {{ $editStock ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
    </form>
</div>

<!-- Stocks Table -->
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Batches actuels</h2>
    <table class="min-w-full bg-white border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border">Produit</th>
                <th class="py-2 px-4 border">Quantité disponible</th>
                <th class="py-2 px-4 border">Région</th>
                <th class="py-2 px-4 border">Créé le</th>
                <th class="py-2 px-4 border">Mis à jour le</th>
                <th class="py-2 px-4 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $batch)
            <tr class="hover:bg-gray-50">
                <td class="py-2 px-4 border">{{ $batch->product->name }}</td>
                <td class="py-2 px-4 border">{{ $batch->quantity_available }}</td>
                <td class="py-2 px-4 border">{{ $batch->region->name }}</td>
                <td class="py-2 px-4 border">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                <td class="py-2 px-4 border">{{ $batch->updated_at->format('d/m/Y H:i') }}</td>
                <td class="py-2 px-4 border flex gap-2">
                    <!-- Edit Button -->
                    <a href="{{ route('agent.products.index', ['edit' => $batch->id]) }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                        Éditer
                    </a>

                    <!-- Delete Button -->
                    <form method="POST" action="{{ route('agent.products.destroy', $batch->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce batch ?')">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-4 px-4 text-center text-gray-500">Aucun batch disponible.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        document.getElementById('stockForm').classList.toggle('hidden');
    });
});
</script>

@endsection
