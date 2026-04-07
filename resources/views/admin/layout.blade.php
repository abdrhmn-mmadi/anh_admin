<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Optional: Apple touch icon for iOS -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon.png') }}">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- PAGE STYLES --}}
    @yield('styles')
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-600 text-white flex flex-col">

        <!-- Logo -->
        <div class="p-6 flex items-center gap-3 border-b border-green-500">
            <div class="w-11 h-11 rounded-full overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="w-full h-full object-cover">
            </div>
            <span class="text-xl font-bold tracking-wide">Amin Panel</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3a2 2 0 01-2-2z"/>
                </svg>
                Accueil
            </a>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m7-8a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Utilisateurs
            </a>

            <!-- Departments -->
            <a href="{{ route('admin.departments.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                Départements
            </a>

            <!-- Product Types -->
            <a href="{{ route('admin.product-types.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 7h10M7 12h10M7 17h10"/>
                </svg>
                Types de Produits
            </a>

            <!-- Products -->
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6M3 13h18M5 21h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/>
                </svg>
                Produits
            </a>

            <!-- Employees -->
            <a href="{{ route('admin.employees.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A9 9 0 1119 12a9 9 0 01-13.879 5.804z"/>
                </svg>
                Employés
            </a>

            <!-- Banks -->
            <a href="{{ route('admin.banks.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10l9-6 9 6M4 10h16v10H4z"/>
                </svg>
                Banques
            </a>

            <!-- Payments -->
            <a href="{{ route('admin.payments.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10h18M3 14h18M5 6h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                </svg>
                Paiements
            </a>

            <!-- Dépenses -->
            <a href="{{ route('admin.depenses') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-3 0-6 1.343-6 3s3 3 6 3 6-1.343 6-3-3-3-6-3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 11v2c0 1.657 2.686 3 6 3s6-1.343 6-3v-2"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16v4H4V6z"/>
                </svg>
                Dépenses
            </a>

            <!-- Sales / Ventes -->
            <a href="{{ route('admin.sales.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h18v18H3V3z"/>
                </svg>
                Ventes
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 17v-6h6v6M7 3h10v4H7z"/>
                </svg>
                Rapports
            </a>

        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Déconnexion
                </button>
            </form>
        </header>

        <!-- Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t text-center py-4 text-sm text-gray-600">
            © 2025 Admin Dashboard
        </footer>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- PAGE SCRIPTS --}}
    @yield('scripts')

</body>
</html>
