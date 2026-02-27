<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manager Dashboard')</title>

    <!-- Favicon (PNG) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon.png') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @yield('styles')
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-green-600 text-white flex flex-col">

        <!-- Logo -->
        <div class="p-6 flex items-center gap-3 border-b border-green-500">
            <div class="w-10 h-10 rounded-full overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="w-full h-full object-cover">
            </div>
            <span class="text-xl font-bold">Manager Panel</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">

            <!-- Accueil -->
            <a href="{{ route('manager.dashboard') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg 
                      {{ request()->routeIs('manager.dashboard') ? 'bg-green-500' : 'hover:bg-green-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3a2 2 0 01-2-2z"/>
                </svg>
                Accueil
            </a>

            <!-- Employés -->
            <a href="{{ route('manager.employees') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg 
                      {{ request()->routeIs('manager.employees') ? 'bg-green-500' : 'hover:bg-green-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m7-8a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Employés
            </a>

            <!-- Produits -->
            <a href="{{ route('manager.products') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg 
                      {{ request()->routeIs('manager.products') ? 'bg-green-500' : 'hover:bg-green-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8H19m-12-8v8m8-8v8m-5-8v8M5 5h16"/>
                </svg>
                Produits
            </a>

            <!-- Ventes -->
            <a href="{{ route('manager.sales.index') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg 
                      {{ request()->routeIs('manager.sales.*') ? 'bg-green-500' : 'hover:bg-green-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-3 0-6 1.343-6 3s3 3 6 3 6-1.343 6-3-3-3-6-3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 11v2c0 1.657 2.686 3 6 3s6-1.343 6-3v-2"/>
                </svg>
                Ventes
            </a>

            <!-- Profil -->
            <a href="{{ route('manager.profile') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-lg 
                      {{ request()->routeIs('manager.profile') ? 'bg-green-500' : 'hover:bg-green-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Profil
            </a>

        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Logout
                </button>
            </form>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t text-center py-4 text-sm text-gray-600">
            © 2025 Manager Dashboard
        </footer>
    </div>

    @yield('scripts')

</body>
</html>