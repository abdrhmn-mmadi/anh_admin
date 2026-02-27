@extends('admin.layout')

@section('title', 'Ventes')
@section('page-title', 'Ventes - détails')

@section('content')

<div class="bg-white p-6 rounded-xl shadow">

    {{-- HEADER ACTIONS --}}
    <div class="flex flex-wrap justify-between items-end gap-4 mb-6">
        <h2 class="font-semibold">Liste des ventes</h2>

        <div class="flex gap-3">
            {{-- FILTER --}}
            <form method="GET" action="{{ route('admin.sales.index') }}" class="flex gap-2">
                <select name="region" class="border rounded px-3 py-2">
                    <option value="">Toutes les régions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                            {{ request('region') == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>

                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Filtrer
                </button>
            </form>

            {{-- EXPORT --}}
            <a href="{{ route('admin.sales.export', request()->query()) }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                📊 Export Excel
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Date</th>
                <th class="px-4 py-2 border">Produits</th>
                <th class="px-4 py-2 border">Quantité</th>
                <th class="px-4 py-2 border">Région</th>
                <th class="px-4 py-2 border">Total (KMF)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>

                    <td class="px-4 py-2 border">
                        {{ $sale->created_at->format('d M Y') }}
                    </td>

                    <td class="px-4 py-2 border">
                        {!! $sale->items
                            ->map(fn($item) => "<strong>- </strong>{$item->product->name}")
                            ->join('<br>') !!}
                    </td>

                    <td class="px-4 py-2 border">
                        {!! $sale->items
                            ->map(fn($item) => $item->quantity)
                            ->join('<br>') !!}
                    </td>

                    <td class="px-4 py-2 border">
                        {{
                            $sale->items
                                ->map(fn($item) =>
                                    $item->region->name
                                    ?? $item->product->stocks->first()?->region->name
                                )
                                ->unique()
                                ->join(', ')
                            ?: 'N/A'
                        }}
                    </td>

                    <td class="px-4 py-2 border font-semibold">
                        {{ number_format($sale->grand_total, 2) }} KMF
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">
                        Aucune vente trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
