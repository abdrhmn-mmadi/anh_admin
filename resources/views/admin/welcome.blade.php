@extends('admin.layout')

@section('title', 'Accueil')
@section('page-title', 'Bienvenue, Admin !')

@section('content')
    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2">Utilisateurs</h2>
            <p class="text-gray-600">Gérez vos utilisateurs</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2">Départements</h2>
            <p class="text-gray-600">Gérez les départements et services</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2">Types de Produits</h2>
            <p class="text-gray-600">Gérez les types de produits</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2">Employés</h2>
            <p class="text-gray-600">Gérez vos employés</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-xl font-semibold mb-2">Banques</h2>
            <p class="text-gray-600">Gérez les informations bancaires</p>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Aperçu</h2>
        <div class="h-48 bg-gray-200 flex items-center justify-center text-gray-500">
            Chart Placeholder
        </div>
    </div>
@endsection
