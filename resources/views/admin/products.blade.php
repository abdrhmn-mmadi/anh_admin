@extends('admin.layout')

@section('title', 'Products')
@section('page-title', 'Produits')

@section('content')

<!-- ===================== -->
<!-- FILTERS -->
<!-- ===================== -->
<div class="bg-white p-6 rounded shadow mb-6 flex space-x-6">

    <!-- Region Filter -->
    <div>
        <label class="block font-semibold mb-1">Région</label>
        <select
            class="border border-gray-300 rounded px-3 py-2"
            onchange="location = `?region_id=${this.value}&product_id={{ $selectedProduct }}`">
            <option value="">Toutes les régions</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}" {{ $selectedRegion == $region->id ? 'selected' : '' }}>
                    {{ $region->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Product Filter -->
    <div>
        <label class="block font-semibold mb-1">Produit</label>
        <select
            class="border border-gray-300 rounded px-3 py-2"
            onchange="location = `?product_id=${this.value}&region_id={{ $selectedRegion }}`">
            <option value="">Tous les produits</option>
            @foreach($productTypes as $type)
                <option value="{{ $type->id }}" {{ $selectedProduct == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<!-- ===================== -->
<!-- TOTAL PRODUCTS CARDS -->
<!-- ===================== -->
<div class="mb-8">
    <h2 class="text-xl font-semibold mb-4">Produits (Quantité Totale)</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded shadow p-5 border-l-4 border-blue-500 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-700">
                    {{ $product->product_name }}
                </h3>
                <p class="text-3xl font-bold text-blue-600 mt-2">
                    {{ $product->total_quantity ?? 0 }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Quantité totale en stock
                </p>
            </div>
        @endforeach
    </div>
</div>

<!-- ===================== -->
<!-- PRODUCT BATCHES -->
<!-- ===================== -->
<div class="bg-white p-6 rounded shadow">

    <!-- HEADER + TOTAL + EXPORT -->
    <div class="flex justify-between items-center mb-4">

        <h2 class="text-xl font-semibold">
            Détails des lots
        </h2>

        <div class="flex items-center space-x-4">
            <!-- TOTAL LABEL -->
            <div class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full font-semibold">
                Total affiché :
                <span class="ml-1 text-lg">
                    {{ $batches->sum('quantity_available') }}
                </span>
            </div>

            <!-- EXPORT BUTTON -->
            <a
                href="{{ route('admin.products.export', [
                    'region_id' => $selectedRegion,
                    'product_id' => $selectedProduct
                ]) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold flex items-center"
            >
                📊 Export Excel
            </a>
        </div>

    </div>

    <!-- BATCH TABLE -->
    <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-3 px-4 border text-left">Produit</th>
                <th class="py-3 px-4 border text-left">Région</th>
                <th class="py-3 px-4 border text-center">Quantité</th>
                <th class="py-3 px-4 border text-left">Créé le</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 border font-medium">
                        {{ $batch->product_name }}
                    </td>
                    <td class="py-3 px-4 border">
                        {{ $batch->region_name }}
                    </td>
                    <td class="py-3 px-4 border text-center font-semibold text-blue-600">
                        {{ $batch->quantity_available }}
                    </td>
                    <td class="py-3 px-4 border text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-500">
                        Aucun lot trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
