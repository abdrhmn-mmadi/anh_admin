@extends('agent.layout')

@section('title', 'Accueil')
@section('page-title', 'Bienvenue, Agent !')

@section('content')

<!-- Top Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

    <!-- Products -->
    <a href="{{ route('agent.products.index') }}"
       class="block bg-white p-6 rounded-lg shadow hover:shadow-lg transition transform hover:-translate-y-1">
        <h2 class="text-lg font-semibold">Produits</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalProducts ?? 0 }}</span>
        </p>
    </a>

    <!-- Sales -->
    <a href="{{ route('agent.sales.index') }}"
       class="block bg-white p-6 rounded-lg shadow hover:shadow-lg transition transform hover:-translate-y-1">
        <h2 class="text-lg font-semibold">Ventes</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalSales ?? 0 }}</span>
        </p>
    </a>

    <!-- Revenue -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Chiffre d'affaires</h2>
        <p class="text-2xl font-bold text-green-600 mt-2">
            {{ number_format(array_sum($monthlySalesAmount), 2) }} KMF
        </p>
    </div>

    <!-- Best Month -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Meilleur mois</h2>
        @php
            $maxAmount = max($monthlySalesAmount);
            $bestMonthIndex = array_search($maxAmount, $monthlySalesAmount);
        @endphp
        <p class="text-xl font-bold mt-2">
            {{ $monthlySalesLabels[$bestMonthIndex] ?? '-' }}
        </p>
        <p class="text-green-600 font-semibold">
            {{ number_format($maxAmount, 2) }} KMF
        </p>
    </div>

</div>

<!-- Charts -->
<div class="graph-container">

    <div class="graph-card">
        <h2>Produits par type</h2>
        <canvas id="productsChart"></canvas>
    </div>

    <div class="graph-card">
        <h2>Ventes mensuelles (Quantité)</h2>
        <canvas id="salesChart"></canvas>
    </div>

    <div class="graph-card">
        <h2>Chiffre d'affaires mensuel</h2>
        <canvas id="revenueChart"></canvas>
    </div>

</div>

@endsection

@section('styles')
<style>
.graph-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.graph-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    flex: 1 1 400px;
    min-width: 300px;
}
.graph-card h2 {
    margin-bottom: 10px;
    font-size: 1.2rem;
}
canvas {
    max-height: 300px;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const productLabels = @json($productsByTypeLabels);
    const productData   = @json($productsByTypeData);

    const salesLabels   = @json($monthlySalesLabels);
    const salesData     = @json($monthlySalesData);
    const revenueData   = @json($monthlySalesAmount);

    // Products by type
    new Chart(document.getElementById('productsChart'), {
        type: 'bar',
        data: {
            labels: productLabels,
            datasets: [{
                data: productData,
                backgroundColor: 'rgba(34,197,94,0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Monthly sales quantity
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Quantité vendue',
                data: salesData,
                borderColor: 'rgba(59,130,246,1)',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Monthly revenue
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Chiffre d\'affaires (KMF)',
                data: revenueData,
                borderColor: 'rgba(234,88,12,1)',
                backgroundColor: 'rgba(234,88,12,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

});
</script>
@endsection
