<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Laba & Saham - PT/CV')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Backdrop for Mobile -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden" 
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white flex flex-col z-50 transition-transform duration-300 md:translate-x-0 md:static md:flex h-full"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           style="display: flex;">
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800 shrink-0">
            <h1 class="text-2xl font-bold tracking-wider text-blue-500">Alas<span class="font-light text-white">kakii</span></h1>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="p-2 text-slate-400 hover:text-white rounded-xl md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>
            @if(auth()->user()->role === 'superadmin')
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('transactions.*') ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span class="font-medium">Transaksi</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('products.*') ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span class="font-medium">Produk / Layanan</span>
            </a>
            <a href="{{ route('investors.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('investors.*') ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-medium">Kelola Investor</span>
            </a>
            @endif
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('reports.*') ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="font-medium">Ekspor Laporan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-y-auto bg-[#F8FAFC]">
        <!-- Header -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-10 shrink-0">
            <div class="flex items-center gap-3">
                <!-- Mobile Hamburger Menu Button -->
                <button @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-600 hover:text-slate-900 rounded-xl md:hidden focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h2 class="text-lg md:text-xl font-semibold text-slate-800 leading-tight">@yield('header_title', 'Ringkasan Keuangan')</h2>
                    <p class="text-xs text-slate-500 hidden sm:block">@yield('header_subtitle')</p>
                </div>
            </div>
            <div class="flex items-center gap-3 md:gap-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs md:text-sm font-medium text-slate-500 hover:text-red-500 transition-colors mr-1">
                        Logout
                    </button>
                </form>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 text-white flex items-center justify-center font-bold shadow-md text-sm md:text-base">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="p-4 md:p-8 space-y-6 md:space-y-8 flex-1">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm transition-all duration-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm transition-all duration-300">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
