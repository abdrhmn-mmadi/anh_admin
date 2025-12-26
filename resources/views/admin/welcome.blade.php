@extends('agent.layout') {{-- Using same layout as agent dashboard --}}

@section('title', 'Accueil')
@section('page-title', 'Bienvenue, Admin !')

@section('content')

<!-- =======================
    KPI CARDS
======================= -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

    <!-- Users -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Utilisateurs</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalUsers ?? 0 }}</span>
        </p>
    </div>

    <!-- Employees -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Employés</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalEmployees ?? 0 }}</span>
        </p>
    </div>

    <!-- Products -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Produits</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalProducts ?? 0 }}</span>
        </p>
    </div>

    <!-- Sales -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Ventes</h2>
        <p class="text-gray-600 mt-2">
            Total : <span class="font-bold">{{ $totalSales ?? 0 }}</span>
        </p>
    </div>

</div>

<!-- =======================
    CHARTS
======================= -->
<div class="graph-container">

    <div class="graph-card">
        <h2>Produits par type</h2>
        <canvas id="productsChart"></canvas>
    </div>

    <div class="graph-card">
        <h2>Produits par région</h2>
        <canvas id="productsRegionChart"></canvas>
    </div>

    <div class="graph-card">
        <h2>Ventes mensuelles</h2>
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

    // Safely handle empty arrays
    const productsByTypeLabels = @json($productsByTypeLabels ?? []);
    const productsByTypeData   = @json($productsByTypeData ?? []);

    const productsByRegionLabels = @json($productsByRegionLabels ?? []);
    const productsByRegionData   = @json($productsByRegionData ?? []);

    const salesLabels   = @json($monthlySalesLabels ?? []);
    const salesData     = @json($monthlySalesData ?? []);
    const revenueData   = @json($monthlySalesAmount ?? []);

    // Products by type (Bar)
    new Chart(document.getElementById('productsChart'), {
        type: 'bar',
        data: {
            labels: productsByTypeLabels,
            datasets: [{
                data: productsByTypeData,
                backgroundColor: 'rgba(34,197,94,0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Products by region (Pie)
    new Chart(document.getElementById('productsRegionChart'), {
        type: 'pie',
        data: {
            labels: productsByRegionLabels,
            datasets: [{
                data: productsByRegionData,
                backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6']
            }]
        },
        options: { responsive: true }
    });

    // Monthly sales (Line)
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

    // Monthly revenue (Line)
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
