@extends('manager.layout')

@section('title', 'Manager Dashboard')
@section('page-title', 'Welcome, Manager!')

@section('content')

<!-- =======================
    KPI CARDS
======================= -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

    <!-- Employees -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg hover:scale-105 transition">
        <p class="text-sm opacity-80">Employees</p>
        <h2 class="text-3xl font-bold">{{ $totalEmployees ?? 0 }}</h2>
    </div>

    <!-- Products -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg hover:scale-105 transition">
        <p class="text-sm opacity-80">Products (Total Quantity)</p>
        <h2 class="text-3xl font-bold">{{ $totalProducts ?? 0 }}</h2>
    </div>

    <!-- Sales -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg hover:scale-105 transition">
        <p class="text-sm opacity-80">Total Sales (Amount)</p>
        <h2 class="text-3xl font-bold">{{ number_format($totalSales ?? 0, 2) }} KMF</h2>
    </div>

</div>

<!-- =======================
    CHARTS
======================= -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Products by Type -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Products by Type (Quantity)</h2>
        <div class="h-[300px]">
            <canvas id="productsChart"></canvas>
        </div>
    </div>

    <!-- Products by Region -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Products by Region (Quantity)</h2>
        <div class="h-[300px]">
            <canvas id="productsRegionChart"></canvas>
        </div>
    </div>

    <!-- Monthly Sales -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Monthly Sales (Invoices)</h2>
        <div class="h-[300px]">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-semibold mb-4">Monthly Revenue (KMF)</h2>
        <div class="h-[300px]">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

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
        scales: { y: { beginAtZero: true } }
    };

    /* ===== Products by Type ===== */
    if (productsByTypeLabels.length && productsByTypeData.length) {
        new Chart(document.getElementById('productsChart'), {
            type: 'bar',
            data: { 
                labels: productsByTypeLabels, 
                datasets: [{ 
                    label: 'Quantity',
                    data: productsByTypeData, 
                    backgroundColor: 'rgba(34,197,94,0.7)' 
                }] 
            },
            options: {
                ...baseOptions,
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#000',
                        font: { weight: 'bold' }
                    }
                }
            }
        });
    }

    /* ===== Products by Region ===== */
    if (productsByRegionLabels.length && productsByRegionData.length) {
        const regionColors = ['#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6'];
        new Chart(document.getElementById('productsRegionChart'), {
            type: 'pie',
            data: { 
                labels: productsByRegionLabels, 
                datasets: [{ 
                    data: productsByRegionData, 
                    backgroundColor: regionColors.slice(0, productsByRegionLabels.length) 
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#000',
                        font: { weight: 'bold', size: 16 }
                    }
                }
            }
        });
    }

    /* ===== Monthly Sales ===== */
    if (months.length && salesData.length) {
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: { 
                labels: months, 
                datasets: [{
                    label: 'Invoices',
                    data: salesData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                }] 
            },
            options: baseOptions
        });
    }

    /* ===== Monthly Revenue ===== */
    if (months.length && revenueData.length) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: { 
                labels: months, 
                datasets: [{
                    label: 'Revenue (KMF)',
                    data: revenueData,
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234,88,12,0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                }] 
            },
            options: {
                ...baseOptions,
                plugins: {
                    datalabels: {
                        color: '#16a34a',
                        anchor: 'end',
                        align: 'top',
                        font: { weight: 'bold' },
                        formatter: value => value.toLocaleString() + ' KMF'
                    }
                }
            }
        });
    }

});
</script>
@endsection
