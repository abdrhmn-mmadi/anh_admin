<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-600 text-white flex flex-col">

        <!-- Logo / Header -->
        <div class="p-6 flex items-center gap-3 border-b border-green-500">
            <div class="w-10 h-10 bg-white text-green-600 rounded-full flex items-center justify-center font-bold text-lg">A</div>
            <span class="text-xl font-bold">Admin Panel</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2">
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3a2 2 0 01-2-2V9z"/>
                </svg>
                Accueil
            </a>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m7-8a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Utilisateurs
            </a>

            <!-- Departments / Services -->
            <a href="{{ route('admin.departments.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-6 5h6"/>
                </svg>
                Départements / Services
            </a>

            <!-- Product Types -->
            <a href="{{ route('admin.product-types.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l10 10M17 7l-10 10"/>
                </svg>
                Types de Produits
            </a>

            <!-- Employees -->
            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Employés
            </a>

            <!-- Banks -->
            <a href="{{ route('admin.banks.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18m-6 5h6"/>
                </svg>
                Banques
            </a>

            <!-- Payments -->
            <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v2m0 4h.01"/>
                </svg>
                Paiements
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6M12 3v4m0 4v4m0 0v4"/>
                </svg>
                Rapports
            </a>

        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Logout
                </button>
            </form>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 text-center py-4 text-sm text-gray-600">
            © 2025 Admin Dashboard. Tous droits réservés.
        </footer>

    </div>

</body>
</html>
