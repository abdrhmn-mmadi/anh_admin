@extends('admin.layout')

@section('title', 'Accueil')
@section('page-title', 'Bienvenue, Admin !')

@section('content')

<!-- =======================
    KPI CARDS
======================= -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
        <p class="text-sm opacity-80">Utilisateurs</p>
        <h2 class="text-3xl font-bold">{{ $totalUsers ?? 0 }}</h2>
    </div>

    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
        <p class="text-sm opacity-80">Employés</p>
        <h2 class="text-3xl font-bold">{{ $totalEmployees ?? 0 }}</h2>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
        <p class="text-sm opacity-80">Produits</p>
        <h2 class="text-3xl font-bold">{{ $totalProducts ?? 0 }}</h2>
    </div>

    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
        <p class="text-sm opacity-80">Ventes</p>
        <h2 class="text-3xl font-bold">{{ $totalSales ?? 0 }}</h2>
    </div>

</div>

<!-- =======================
    CHARTS
======================= -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Produits par type</h2>
        <div class="h-[300px]">
            <canvas id="productsChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Produits par région</h2>
        <div class="h-[300px]">
            <canvas id="productsRegionChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Ventes mensuelles (12 derniers mois)</h2>
        <div class="h-[300px]">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Chiffre d'affaires mensuel (12 derniers mois)</h2>
        <div class="h-[300px]">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

</div>

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    Chart.register(ChartDataLabels);

    const productsByTypeLabels   = @json($productsByTypeLabels ?? []);
    const productsByTypeData     = @json($productsByTypeData ?? []);

    const productsByRegionLabels = @json($productsByRegionLabels ?? []);
    const productsByRegionData   = @json($productsByRegionData ?? []);

    const months      = @json($months ?? []);
    const salesData   = @json($monthlySalesData ?? []);
    const revenueData = @json($monthlyRevenueData ?? []);

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    };

    if (productsByTypeLabels.length) {
        new Chart(document.getElementById('productsChart'), {
            type: 'bar',
            data: {
                labels: productsByTypeLabels,
                datasets: [{
                    data: productsByTypeData,
                    backgroundColor: 'rgba(34,197,94,0.7)',
                }]
            },
            options: baseOptions
        });
    }

    if (productsByRegionLabels.length) {
        new Chart(document.getElementById('productsRegionChart'), {
            type: 'pie',
            data: {
                labels: productsByRegionLabels,
                datasets: [{ data: productsByRegionData }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (months.length) {
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Quantité vendue',
                    data: salesData,
                    borderColor: '#3b82f6',
                    tension: 0.4
                }]
            },
            options: baseOptions
        });

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Chiffre d\'affaires (KMF)',
                    data: revenueData,
                    borderColor: '#ea580c',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: baseOptions
        });
    }
});
</script>
@endsection
