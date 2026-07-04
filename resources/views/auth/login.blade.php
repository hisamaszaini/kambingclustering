<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Monitoring Kambing</title>

    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-6 lg:p-12">
    <!-- Main Card Container -->
    <div class="w-full max-w-5xl min-h-[550px] bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col md:flex-row">

        <!-- Left Column: Branding (Gradient orange) -->
        <div class="hidden md:flex w-1/2 bg-gradient-to-br from-primary to-orange-700 p-12 flex-col justify-between text-white relative">
            <div class="absolute inset-0 bg-white/[0.01] backdrop-blur-xs pointer-events-none"></div>

            <div class="relative z-10 flex items-center space-x-2">
                <span class="text-xxs px-2.5 py-1 bg-white/20 rounded-full font-semibold tracking-wider uppercase flex items-center">
                    <svg class="w-3 h-3 mr-2 inline-block" viewBox="0 0 602 639" fill="currentColor">
                        <g transform="translate(-43.163654,-141.76256)">
                            <path d="m 451.42858,205.21932 c -114.21192,-135.725559 44.75328,-5.71146 52.85713,17.14285 48.24018,27.64804 56.67788,30.47844 85.71429,54.28572 60.79497,58.62489 74.08099,48.36125 10,75.71429 -65.72261,13.10086 -14.42291,62.81103 -64.28572,78.57142 -24.49513,-13.32228 -17.78518,-49.64882 -18.57142,-25.71428 -27.14286,60 -72.11605,107.28425 -68.57143,192.85714 l 15,163.57144 c -29.98227,7.86861 -39.5313,6.64807 -60.71428,-0.71429 L 370,608.07646 c -79.91074,31.53405 -88.8086,-8.57694 -130,-0.71428 -19.41097,5.84638 -42.759,-7.79273 -72.61204,16.6019 -5.23809,62.38095 -6.41692,97.47669 -15.61275,149.91079 -48.42615,7.39246 -29.65135,2.43622 -62.612044,0.17331 9.169912,-64.7619 15.361124,-92.19023 12.713564,-165.03336 L 57.142856,583.79075 c 0,0 -12.365097,-65.79156 -11.428567,-71.42857 6.01301,-36.19262 26.94349,-77.80602 67.142851,-90 l 245.71429,-17.14286 c 39.39763,-45.36428 8.65876,2.91264 65.71429,-97.14285 -1.54967,-18.57143 43.74097,-62.85715 27.14286,-102.85715 z" />
                        </g>
                    </svg> Sistem Monitoring Kambing
                </span>
            </div>

            <div class="relative z-10 my-auto space-y-4 max-w-md">
                <div class="text-2xl font-bold leading-snug tracking-tight">
                    Analisis Karakteristik & Produktivitas Kambing Secara Sistematis.
                </div>
                <p class="text-orange-100 text-xs font-medium leading-relaxed">
                    Sistem monitoring cerdas yang memanfaatkan algoritma K-Means Clustering untuk mengelompokkan produktivitas kambing (bobot badan, tingkat kelahiran, produksi susu) guna mendukung pengambilan keputusan peternakan.
                </p>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                <div>
                    <span class="block text-xxs text-orange-200 font-medium uppercase">Kriteria</span>
                    <span class="text-xs font-bold mt-0.5">3 Parameter</span>
                </div>
                <div>
                    <span class="block text-xxs text-orange-200 font-medium uppercase">Kluster</span>
                    <span class="text-xs font-bold mt-0.5">3 Kelas</span>
                </div>
                <div>
                    <span class="block text-xxs text-orange-200 font-medium uppercase">Metodologi</span>
                    <span class="text-xs font-bold mt-0.5">K-Means</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-between bg-white">

            <!-- Logo area -->
            <div class="flex items-center space-x-3 mb-6 md:mb-0">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-md shadow-primary/20 p-2">
                    <svg class="w-full h-full" viewBox="0 0 602 639" fill="currentColor">
                        <g transform="translate(-43.163654,-141.76256)">
                            <path d="m 451.42858,205.21932 c -114.21192,-135.725559 44.75328,-5.71146 52.85713,17.14285 48.24018,27.64804 56.67788,30.47844 85.71429,54.28572 60.79497,58.62489 74.08099,48.36125 10,75.71429 -65.72261,13.10086 -14.42291,62.81103 -64.28572,78.57142 -24.49513,-13.32228 -17.78518,-49.64882 -18.57142,-25.71428 -27.14286,60 -72.11605,107.28425 -68.57143,192.85714 l 15,163.57144 c -29.98227,7.86861 -39.5313,6.64807 -60.71428,-0.71429 L 370,608.07646 c -79.91074,31.53405 -88.8086,-8.57694 -130,-0.71428 -19.41097,5.84638 -42.759,-7.79273 -72.61204,16.6019 -5.23809,62.38095 -6.41692,97.47669 -15.61275,149.91079 -48.42615,7.39246 -29.65135,2.43622 -62.612044,0.17331 9.169912,-64.7619 15.361124,-92.19023 12.713564,-165.03336 L 57.142856,583.79075 c 0,0 -12.365097,-65.79156 -11.428567,-71.42857 6.01301,-36.19262 26.94349,-77.80602 67.142851,-90 l 245.71429,-17.14286 c 39.39763,-45.36428 8.65876,2.91264 65.71429,-97.14285 -1.54967,-18.57143 43.74097,-62.85715 27.14286,-102.85715 z" />
                        </g>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm text-slate-800 tracking-tight block leading-tight">Sistem Monitoring Kambing</span>
                    <span class="text-xxs font-medium text-slate-400 block mt-0.5">SPK K-Means Clustering</span>
                </div>
            </div>

            <!-- Login Form Area -->
            <div class="my-auto space-y-6 pt-6 md:pt-0">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Selamat Datang</h2>
                    <p class="text-slate-400 text-xs font-medium mt-1">Silakan masuk menggunakan username akun Anda untuk mengakses dashboard.</p>
                </div>

                @if($errors->any())
                <div class="p-3 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-800 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
                @endif

                @if(session('success'))
                <div class="p-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 text-xs font-medium">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" required placeholder="Masukkan kata sandi"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                        <label for="remember" class="ml-2 text-xs font-medium text-slate-500">Ingat perangkat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-md shadow-primary/20 transition duration-200">
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            <!-- Footer Text -->
            <div class="text-[10px] text-slate-400 font-medium mt-8 border-t border-slate-100 pt-4 flex justify-between">
                <span>Sistem Monitoring Kambing</span>
                <span>2026 &copy; All rights reserved</span>
            </div>
        </div>

    </div>
</body>

</html>