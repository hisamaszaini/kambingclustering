@extends('layouts.app')

@section('content')
<!-- MathJax Configuration and Script for Beautiful Mathematical Equations -->
<script>
    window.MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ],
            displayMath: [
                ['$$', '$$'],
                ['\\[', '\\]']
            ],
            processEscapes: true
        }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>

<div class="space-y-8">
    <!-- Greeting Banner -->
    <div class="bg-gradient-to-r from-primary to-orange-600 p-6 sm:p-8 rounded-3xl shadow-sm text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold mt-2">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-orange-100 text-xs font-medium max-w-xl">Panel monitoring produktivitas dan clustering K-Means Kambing. Gunakan navigasi untuk mengelola data kambing, mencatat produktivitas secara periodik, dan memproses kelompok kinerja produktivitas.</p>
        </div>
        <div class="flex-shrink-0 flex gap-2">
            <a href="{{ route('kambing.index') }}" class="px-4 py-2.5 bg-white text-primary hover:bg-orange-50 rounded-xl font-bold text-xs transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Kelola Kambing</span>
            </a>
            <a href="{{ route('clustering.proses-form') }}" class="px-4 py-2.5 bg-orange-500 text-white hover:bg-orange-400 rounded-xl font-bold text-xs border border-orange-400/20 transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-bolt"></i>
                <span>Jalankan K-Means</span>
            </a>
        </div>
    </div>

    <!-- Stats Widget Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Kambing -->
        <div class="bg-gradient-to-br from-primary to-orange-600 p-5 rounded-2xl flex items-center justify-between shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-1 transition duration-300 text-white">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-orange-100 uppercase tracking-wider block">Total Kambing</span>
                <span class="block text-3xl font-black leading-none">{{ $totalKambing }}</span>
                <span class="block text-[11px] text-orange-100 font-medium pt-1">Terdaftar di sistem</span>
            </div>
            <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center shadow-sm p-2.5">
                <svg class="w-full h-full" viewBox="0 0 602 639" fill="currentColor">
                    <g transform="translate(-43.163654,-141.76256)">
                        <path d="m 451.42858,205.21932 c -114.21192,-135.725559 44.75328,-5.71146 52.85713,17.14285 48.24018,27.64804 56.67788,30.47844 85.71429,54.28572 60.79497,58.62489 74.08099,48.36125 10,75.71429 -65.72261,13.10086 -14.42291,62.81103 -64.28572,78.57142 -24.49513,-13.32228 -17.78518,-49.64882 -18.57142,-25.71428 -27.14286,60 -72.11605,107.28425 -68.57143,192.85714 l 15,163.57144 c -29.98227,7.86861 -39.5313,6.64807 -60.71428,-0.71429 L 370,608.07646 c -79.91074,31.53405 -88.8086,-8.57694 -130,-0.71428 -19.41097,5.84638 -42.759,-7.79273 -72.61204,16.6019 -5.23809,62.38095 -6.41692,97.47669 -15.61275,149.91079 -48.42615,7.39246 -29.65135,2.43622 -62.612044,0.17331 9.169912,-64.7619 15.361124,-92.19023 12.713564,-165.03336 L 57.142856,583.79075 c 0,0 -12.365097,-65.79156 -11.428567,-71.42857 6.01301,-36.19262 26.94349,-77.80602 67.142851,-90 l 245.71429,-17.14286 c 39.39763,-45.36428 8.65876,2.91264 65.71429,-97.14285 -1.54967,-18.57143 43.74097,-62.85715 27.14286,-102.85715 z" />
                    </g>
                </svg>
            </div>
        </div>

        <!-- Card 2: Jantan -->
        <div class="bg-gradient-to-br from-sky-500 to-blue-600 p-5 rounded-2xl flex items-center justify-between shadow-md shadow-sky-500/20 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-1 transition duration-300 text-white">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-sky-100 uppercase tracking-wider block">Kambing Jantan</span>
                <span class="block text-3xl font-black leading-none">{{ $totalJantan }}</span>
                <span class="block text-[11px] text-sky-100 font-medium pt-1">Pejantan</span>
            </div>
            <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center text-base shadow-sm">
                <i class="fa-solid fa-mars"></i>
            </div>
        </div>

        <!-- Card 3: Betina -->
        <div class="bg-gradient-to-br from-pink-500 to-rose-600 p-5 rounded-2xl flex items-center justify-between shadow-md shadow-pink-500/20 hover:shadow-lg hover:shadow-pink-500/30 hover:-translate-y-1 transition duration-300 text-white">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-pink-100 uppercase tracking-wider block">Kambing Betina</span>
                <span class="block text-3xl font-black leading-none">{{ $totalBetina }}</span>
                <span class="block text-[11px] text-pink-100 font-medium pt-1">Indukan</span>
            </div>
            <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center text-base shadow-sm">
                <i class="fa-solid fa-venus"></i>
            </div>
        </div>

        <!-- Card 4: Data Produktivitas -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-5 rounded-2xl flex items-center justify-between shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-1 transition duration-300 text-white">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-emerald-100 uppercase tracking-wider block">Data Produktivitas</span>
                <span class="block text-3xl font-black leading-none">{{ $totalProduktivitas }}</span>
                <span class="block text-[11px] text-emerald-100 font-medium pt-1">Log historis periodik</span>
            </div>
            <div class="w-12 h-12 bg-white/20 text-white rounded-2xl flex items-center justify-center text-base shadow-sm">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- Charts & Cluster Info Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Chart 1: Cluster Share (Doughnut) & Recent Sesi -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Distribusi Hasil Kluster</h3>
                    <p class="text-xxs text-slate-400 mt-0.5">Proporsi sebaran produktivitas kambing pada sesi analisis terakhir.</p>
                </div>

                @if(!$latestSesi)
                <div class="py-12 text-center space-y-3">
                    <div class="text-3xl text-slate-300"><i class="fa-solid fa-diagram-project"></i></div>
                    <h4 class="text-xs font-bold text-slate-700">Belum Ada Hasil Clustering</h4>
                    <p class="text-xxs text-slate-400 max-w-xs mx-auto">Sistem belum mendeteksi riwayat sesi clustering. Admin perlu menjalankan modul K-Means terlebih dahulu.</p>
                </div>
                @else
                <div class="relative w-full max-w-[200px] mx-auto py-2">
                    <canvas id="clusterChart"
                        data-rendah="{{ $clusterCounts['Rendah'] }}"
                        data-sedang="{{ $clusterCounts['Sedang'] }}"
                        data-tinggi="{{ $clusterCounts['Tinggi'] }}"></canvas>
                </div>

                <div class="mt-6 space-y-2">
                    <div class="flex items-center justify-between text-xxs font-semibold">
                        <span class="flex items-center gap-1.5 text-red-500"><i class="fa-solid fa-circle text-[8px]"></i> Rendah</span>
                        <span class="text-slate-800">{{ $clusterCounts['Rendah'] }} Kambing</span>
                    </div>
                    <div class="flex items-center justify-between text-xxs font-semibold">
                        <span class="flex items-center gap-1.5 text-amber-500"><i class="fa-solid fa-circle text-[8px]"></i> Sedang</span>
                        <span class="text-slate-800">{{ $clusterCounts['Sedang'] }} Kambing</span>
                    </div>
                    <div class="flex items-center justify-between text-xxs font-semibold">
                        <span class="flex items-center gap-1.5 text-emerald-500"><i class="fa-solid fa-circle text-[8px]"></i> Tinggi</span>
                        <span class="text-slate-800">{{ $clusterCounts['Tinggi'] }} Kambing</span>
                    </div>
                </div>
                @endif
            </div>

            @if($latestSesi)
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center flex-col text-xxs text-slate-700">
                <span>Diproses oleh: <strong>{{ $latestSesi->user->name }}</strong></span>
                <span>Iterasi: <strong>{{ $latestSesi->total_iterasi }} kali</strong></span>
            </div>
            @endif
        </div>

        <!-- Chart 2: Monthly Avg Productivity Trend (Line Chart) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Tren Produktivitas Bulanan</h3>
                    <p class="text-xxs text-slate-400 mt-0.5">Perkembangan rata-rata bobot badan (kg) dan produksi susu (Liter) kambing dari pencatatan periodik.</p>
                </div>

                @if(empty($chartLabels))
                <div class="py-16 text-center space-y-3">
                    <div class="text-3xl text-slate-300"><i class="fa-solid fa-chart-line"></i></div>
                    <h4 class="text-xs font-bold text-slate-700">Belum Ada Data Tren</h4>
                    <p class="text-xxs text-slate-400 max-w-xs mx-auto">Pencatatan data produktivitas kambing masih kosong. Silakan tambahkan data produktivitas terlebih dahulu.</p>
                </div>
                @else
                <div class="w-full relative h-[250px]">
                    <canvas id="trendChart"
                        data-labels="{{ json_encode($chartLabels) }}"
                        data-bobot="{{ json_encode($chartBobot) }}"
                        data-susu="{{ json_encode($chartSusu) }}"></canvas>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- K-Means Explanation Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Konsep Pemetaan Kluster K-Means</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-slate-600 text-xxs font-medium leading-relaxed">
            <div class="space-y-2">
                <span class="block font-bold text-primary">1. Parameter Pengelompokan (Kriteria)</span>
                <p>Sistem ini mengagregasikan data log produktivitas periodik setiap kambing ke dalam 3 parameter koordinat geometri:</p>
                <ul class="space-y-1.5 pl-2 mt-1 list-disc list-inside">
                    <li><strong class="font-bold text-slate-700">C1 - Bobot Badan (kg):</strong> Nilai rata-rata dari semua pencatatan bobot badan kambing.</li>
                    <li><strong class="font-bold text-slate-700">C2 - Tingkat Kelahiran (ekor):</strong> Nilai maksimum kelahiran yang dicapai kambing.</li>
                    <li><strong class="font-bold text-slate-700">C3 - Produksi Susu (Liter):</strong> Nilai rata-rata produksi susu harian kambing.</li>
                </ul>
                <p class="mt-2">Jarak Euclidean dihitung secara geometris tanpa bobot kriteria ($w_j = 1$):</p>
                <div class="py-2 text-center font-bold text-slate-700 bg-slate-50 rounded-xl border border-slate-100 mt-1">
                    $$d(p, c) = \sqrt{(C1_p - C1_c)^2 + (C2_p - C2_c)^2 + (C3_p - C3_c)^2}$$
                </div>
            </div>
            <div class="space-y-2">
                <span class="block font-bold text-primary">2. Penentuan Centroid Awal Objektif</span>
                <p>Guna menghindari kerancuan acak (random initialization), sistem memposisikan centroid awal secara objektif dengan teknik sebaran persentil populasi dari rata-rata parameter:</p>
                <ul class="space-y-2 pl-2 mt-2">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500 font-bold">•</span>
                        <span><strong class="font-bold text-slate-700">Kluster Rendah:</strong> Mengambil koordinat kambing pada letak persentil 15% ($P_{15}$) kelompok bawah.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">•</span>
                        <span><strong class="font-bold text-slate-700">Kluster Sedang:</strong> Mengambil koordinat kambing pada letak persentil 50% ($P_{50}$ / Median).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-500 font-bold">•</span>
                        <span><strong class="font-bold text-slate-700">Kluster Tinggi:</strong> Mengambil koordinat kambing pada letak persentil 85% ($P_{85}$) kelompok atas.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if($latestSesi)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Doughnut Chart for Cluster Share
        const ctxCluster = document.getElementById('clusterChart');
        if (ctxCluster) {
            const rendah = parseFloat(ctxCluster.dataset.rendah || 0);
            const sedang = parseFloat(ctxCluster.dataset.sedang || 0);
            const tinggi = parseFloat(ctxCluster.dataset.tinggi || 0);

            new Chart(ctxCluster.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Rendah', 'Sedang', 'Tinggi'],
                    datasets: [{
                        data: [rendah, sedang, tinggi],
                        backgroundColor: ['#EF4444', '#F59E0B', '#10B981'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endif

@if(!empty($chartLabels))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Line/Bar Chart for Avg Weight and Avg Milk Trend
        const ctxTrend = document.getElementById('trendChart');
        if (ctxTrend) {
            const labels = JSON.parse(ctxTrend.dataset.labels || '[]');
            const bobot = JSON.parse(ctxTrend.dataset.bobot || '[]');
            const susu = JSON.parse(ctxTrend.dataset.susu || '[]');

            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Rata-rata Bobot Badan (kg)',
                            data: bobot,
                            borderColor: '#FF8400',
                            backgroundColor: 'rgba(255, 132, 0, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Rata-rata Produksi Susu (L)',
                            data: susu,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Bobot Badan (kg)',
                                font: {
                                    size: 10,
                                    weight: 'bold',
                                    family: 'Poppins'
                                }
                            },
                            ticks: {
                                font: {
                                    size: 9,
                                    family: 'Poppins'
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'Produksi Susu (Liter)',
                                font: {
                                    size: 10,
                                    weight: 'bold',
                                    family: 'Poppins'
                                }
                            },
                            ticks: {
                                font: {
                                    size: 9,
                                    family: 'Poppins'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 9,
                                    family: 'Poppins'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10,
                                    family: 'Poppins'
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endif
@endsection