@extends('manager.layout')

@section('title', 'Produits')
@section('page-title', 'Produits')

@section('content')

{{-- ================= FILTERS ================= --}}
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form
        method="GET"
        action="{{ route('manager.products') }}"
        class="flex flex-col md:flex-row gap-4 items-end"
        x-data
        @change="$el.submit()"
    >

        {{-- Région --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Région
            </label>
            <select name="region_id" class="px-4 py-2 border rounded-lg w-48">
                <option value="">Toutes les régions</option>
                @foreach($regions as $region)
                    <option
                        value="{{ $region->id }}"
                        @selected(request('region_id') == $region->id)
                    >
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Type de produit --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Type de produit
            </label>
            <select name="product_id" class="px-4 py-2 border rounded-lg w-48">
                <option value="">Tous les produits</option>
                @foreach($productTypes as $type)
                    <option
                        value="{{ $type->id }}"
                        @selected(request('product_id') == $type->id)
                    >
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Par page --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Par page
            </label>
            <select name="per_page" class="px-4 py-2 border rounded-lg">
                @foreach([10, 20, 50, 100] as $size)
                    <option
                        value="{{ $size }}"
                        @selected(request('per_page', 10) == $size)
                    >
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </div>

    </form>
</div>

{{-- ================= TOTAL ================= --}}
<div class="mb-4 bg-white p-4 rounded-lg shadow flex justify-between items-center">
    <span class="text-gray-700 font-semibold">
        Total affiché :
        <span class="ml-2 bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold">
            {{ $batches->sum('quantity_available') }}
        </span>
    </span>
</div>

{{-- ================= TABLE ================= --}}
<div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Produit
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Région
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                    Quantité
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Créé le
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($batches as $batch)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        {{ $batch->product_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $batch->region_name }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-green-600">
                        {{ $batch->quantity_available }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                        Aucun produit trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{-- Pagination --}}
    <div class="p-4">
        {{ $batches->withQueryString()->links() }}
    </div>
</div>

@endsection
