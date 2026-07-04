<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Monitoring Kambing - K-Means Clustering</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #fcfcfc;
            font-family: 'Poppins', 'Instrument Sans', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.01);
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col text-slate-700 font-poppins" x-data="{ mobileSidebarOpen: false, openLogoutModal: false }">

    <!-- TOP NAVBAR (FULL WIDTH) -->
    <header class="bg-white border-b border-slate-200 px-6 h-16 flex items-center justify-between shadow-xs sticky top-0 z-50">
        <!-- Left: Logo & Toggle -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Sidebar Toggle -->
            <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="md:hidden text-slate-500 hover:text-slate-800 focus:outline-none">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>

            <!-- Logo & Title -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-md shadow-primary/20 p-2">
                    <!-- Custom SVG Goat Icon -->
                    <svg class="w-full h-full" viewBox="0 0 602 639" fill="currentColor">
                        <g transform="translate(-43.163654,-141.76256)">
                            <path d="m 451.42858,205.21932 c -114.21192,-135.725559 44.75328,-5.71146 52.85713,17.14285 48.24018,27.64804 56.67788,30.47844 85.71429,54.28572 60.79497,58.62489 74.08099,48.36125 10,75.71429 -65.72261,13.10086 -14.42291,62.81103 -64.28572,78.57142 -24.49513,-13.32228 -17.78518,-49.64882 -18.57142,-25.71428 -27.14286,60 -72.11605,107.28425 -68.57143,192.85714 l 15,163.57144 c -29.98227,7.86861 -39.5313,6.64807 -60.71428,-0.71429 L 370,608.07646 c -79.91074,31.53405 -88.8086,-8.57694 -130,-0.71428 -19.41097,5.84638 -42.759,-7.79273 -72.61204,16.6019 -5.23809,62.38095 -6.41692,97.47669 -15.61275,149.91079 -48.42615,7.39246 -29.65135,2.43622 -62.612044,0.17331 9.169912,-64.7619 15.361124,-92.19023 12.713564,-165.03336 L 57.142856,583.79075 c 0,0 -12.365097,-65.79156 -11.428567,-71.42857 6.01301,-36.19262 26.94349,-77.80602 67.142851,-90 l 245.71429,-17.14286 c 39.39763,-45.36428 8.65876,2.91264 65.71429,-97.14285 -1.54967,-18.57143 43.74097,-62.85715 27.14286,-102.85715 z" />
                        </g>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm text-slate-855 tracking-tight block leading-tight mb-1">Sistem Monitoring Kambing</span>
                </div>
            </div>
        </div>

        <!-- Right: User Profile Dropdown -->
        <div class="relative" x-data="{ openDropdown: false }" @click.away="openDropdown = false">
            <button @click="openDropdown = !openDropdown" class="flex items-center space-x-3 px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-2xl hover:bg-slate-100 hover:border-slate-350 transition focus:outline-none group">
                <div class="w-7 h-7 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center font-bold text-xs text-primary uppercase transition group-hover:bg-primary group-hover:text-white">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="text-left">
                    <span class="block text-xs font-bold text-slate-800 group-hover:text-slate-900 transition leading-none">{{ Auth::user()->name }}</span>
                    <span class="block text-[9px] font-semibold text-slate-400 capitalize mt-0.5">{{ Auth::user()->role }}</span>
                </div>
                <span class="text-xxs text-slate-400 group-hover:text-slate-650 transition"><i class="fa-solid fa-chevron-down text-[9px]"></i></span>
            </button>

            <!-- Profile Dropdown Card -->
            <div x-show="openDropdown"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                style="display: none;"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl border border-slate-200 shadow-lg py-1.5 z-50">
                <div class="px-4 py-2 border-b border-slate-100">
                    <span class="block text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</span>
                    <span class="block text-[10px] text-slate-400 font-medium truncate capitalize">Role: {{ Auth::user()->role }}</span>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2.5 px-4 py-2.5 text-xs text-slate-650 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-user-gear text-slate-400 w-4 text-center"></i>
                    <span>Pengaturan Akun</span>
                </a>

                <button type="button" @click="openLogoutModal = true" class="w-full flex items-center space-x-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50/50 transition text-left">
                    <i class="fa-solid fa-right-from-bracket text-rose-400 w-4 text-center"></i>
                    <span>Keluar Sistem</span>
                </button>
            </div>
        </div>
    </header>

    <!-- WRAPPER (BELOW HEADER) -->
    <div class="flex-grow flex flex-row relative min-h-[calc(100vh-4rem)]">

        <!-- SIDEBAR -->
        <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-[4rem] left-0 z-40 w-64 bg-white text-slate-650 flex flex-col justify-between transition-transform duration-300 ease-in-out md:sticky md:top-16 md:translate-x-0 h-[calc(100vh-4rem)] shrink-0 border-r border-slate-200/80">

            <!-- Sidebar Navigation Menu Items -->
            <nav class="flex-grow py-6 px-4 space-y-1.5 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('dashboard') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-chart-pie"></i></span>
                    <span>Dashboard</span>
                </a>

                <!-- Data Kambing -->
                <a href="{{ route('kambing.index') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('kambing*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-file-invoice"></i></span>
                    <span>Data Kambing</span>
                </a>

                <!-- Data Produktivitas -->
                <a href="{{ route('produktivitas.index') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('produktivitas*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-scale-balanced"></i></span>
                    <span>Data Produktivitas</span>
                </a>

                <!-- Admin-only Clustering & User Management -->
                @if(Auth::user()->role === 'admin')
                <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">K-Means Analisis</div>

                <!-- Proses K-Means -->
                <a href="{{ route('clustering.proses-form') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('clustering/proses*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-bolt"></i></span>
                    <span>Proses K-Means</span>
                </a>

                <!-- Hasil Clustering -->
                <a href="{{ route('clustering.hasil') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('clustering/hasil*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-diagram-project"></i></span>
                    <span>Hasil Clustering</span>
                </a>

                <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengaturan Sistem</div>

                <!-- Kelola Pengguna -->
                <a href="{{ route('user.index') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('user*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-users-gear"></i></span>
                    <span>Kelola Pengguna</span>
                </a>
                @else
                <!-- Read-only clustering for User role -->
                <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">K-Means Analisis</div>

                <a href="{{ route('clustering.hasil') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('clustering/hasil*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-diagram-project"></i></span>
                    <span>Hasil Clustering</span>
                </a>
                @endif

                <!-- Pengaturan Akun -->
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('profile*') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-user-gear"></i></span>
                    <span>Pengaturan Akun</span>
                </a>
            </nav>

            <!-- Bottom Sidebar Logout -->
            <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
                <button type="button" @click="openLogoutModal = true" class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span>Keluar Sistem</span>
                </button>
            </div>

            <!-- Bottom logout trigger placeholder (hidden POST form) -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </aside>

        <!-- CONTENT WRAPPER -->
        <div class="flex-grow flex flex-col min-h-[calc(100vh-4rem)] overflow-x-hidden bg-slate-50/50">

            <!-- Toast Notifications -->
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                @if(session('success'))
                <div class="p-3.5 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-sm flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center space-x-2.5">
                        <span class="text-xs text-emerald-600"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="text-xs font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 text-sm font-bold">&times;</button>
                </div>
                @endif

                @if(session('error'))
                <div class="p-3.5 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-sm flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex items-center space-x-2.5">
                        <span class="text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation"></i></span>
                        <span class="text-xs font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-400 hover:text-rose-600 text-sm font-bold">&times;</button>
                </div>
                @endif
            </div>

            <!-- Main Content View Area -->
            <main class="flex-grow py-6 px-4 sm:px-6 lg:px-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-5 mt-auto">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 text-slate-400 text-xxs font-medium text-center">
                    <p>&copy; 2026 Sistem Monitoring Kambing.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div x-show="openLogoutModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openLogoutModal = false"></div>

            <div class="relative z-10 w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform p-6">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">Konfirmasi Keluar?</h3>
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin keluar dari sistem?</p>
                    </div>
                </div>

                <div class="flex justify-center space-x-3 pt-6 mt-2 border-t border-slate-100">
                    <button type="button" @click="openLogoutModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">Batal</button>
                    <button type="button" @click="document.getElementById('logout-form').submit()" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition">Ya, Keluar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>