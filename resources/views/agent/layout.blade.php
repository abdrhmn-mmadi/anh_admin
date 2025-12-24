<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agent Dashboard')</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Page specific styles -->
    @yield('styles')
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-green-600 to-green-700 text-white flex flex-col shadow-lg">

        <!-- Logo -->
        <div class="p-6 flex items-center gap-3 border-b border-green-500">
            <div class="w-11 h-11 bg-white text-green-600 rounded-full flex items-center justify-center font-bold text-xl">
                A
            </div>
            <span class="text-xl font-bold tracking-wide">Agent Panel</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">

            <a href="{{ route('agent.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('agent.dashboard') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10"/>
                </svg>
                Accueil
            </a>

            <a href="{{ route('agent.products.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('agent.products.*') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14l-8-4m8 4v14"/>
                </svg>
                Produits
            </a>

            <a href="{{ route('agent.sales.index') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('agent.sales.*') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 3v18m4-10v10m4-6v6M5 7v14"/>
                </svg>
                Ventes
            </a>

            <a href="{{ route('agent.profile') }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ request()->routeIs('agent.profile*') ? 'bg-white/20 font-semibold' : 'hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Profile
            </a>
        </nav>

        <!-- Logged User -->
        <div class="p-4 border-t border-green-500 flex items-center gap-3">
            <div class="w-10 h-10 bg-white text-green-600 rounded-full flex items-center justify-center font-bold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="text-sm">
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-green-200">{{ Auth::user()->role->name ?? 'Agent' }}</p>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                    </svg>
                    Déconnexion
                </button>
            </form>
        </header>

        <!-- Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t text-center py-4 text-sm text-gray-500">
            © 2025 Agent Dashboard — Tous droits réservés
        </footer>
    </div>

    <!-- Page specific scripts (VERY IMPORTANT) -->
    @yield('scripts')

</body>
</html>
