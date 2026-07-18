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

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-base font-bold text-slate-800">Proses Perhitungan K-Means</h1>
        <p class="text-slate-400 text-xs font-medium mt-0.5">Kalkulasi algoritma pengelompokan secara transparan, akurat, dan langkah demi langkah (step-by-step).</p>
    </div>

    @if(!$run)
    <div class="mx-auto bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
        <div class="bg-gradient-to-br from-primary to-orange-600 p-8 text-white text-center space-y-3 relative">
            <span class="px-2.5 py-1 bg-white/20 rounded-full text-xxs font-semibold tracking-wider uppercase">Fase Perhitungan</span>
            <h2 class="text-xl font-bold">Mulai Pengelompokan Kinerja Produktivitas Kambing</h2>
            <p class="text-orange-100 text-xs font-medium max-w-lg mx-auto">Sistem akan mengagregasikan log data produktivitas ke dalam parameter 3-dimensi (C1 Bobot Terakhir, C2 Kelahiran 6 Bulan Terakhir, C3 Rata-rata Susu 30 Hari Terakhir) dari seluruh kambing, lalu mengklasifikasikannya secara objektif.</p>

            @if($totalKambing > 0)
            <form action="{{ route('clustering.proses') }}" method="POST" class="pt-4">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white hover:bg-orange-50 text-primary rounded-xl font-bold text-xs transition shadow-md duration-200">
                    <i class="fa-solid fa-bolt mr-1.5"></i> Jalankan Perhitungan K-Means
                </button>
            </form>
            @else
            <div class="pt-4 text-xs font-semibold text-orange-200">
                Tidak ada data kambing untuk diproses. Silakan tambahkan atau import data terlebih dahulu.
            </div>
            @endif
        </div>

        <!-- Parameters Overview -->
        <div class="p-6 sm:p-8 space-y-6 text-slate-600 text-xxs font-medium leading-relaxed">
            <h3 class="text-xs font-bold text-slate-800 border-b border-slate-100 pb-3">Konfigurasi Pengelompokan (Fixed Parameters)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Param 1 -->
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1">
                    <span class="block font-bold text-slate-700">Jumlah Kluster ($K = 3$)</span>
                    <p class="text-slate-400 text-[10px]">Ditetapkan sebanyak <strong>$K = 3$</strong> kelompok (Rendah, Sedang, dan Tinggi) berdasarkan target pembagian produktivitas kambing di peternakan.</p>
                </div>

                <!-- Param 2 -->
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5">
                    <span class="block font-bold text-slate-700">Inisialisasi Centroid</span>
                    <p class="text-slate-400 text-[10px] leading-relaxed">
                        Ditentukan secara <strong>Dinamis & Objektif</strong> berbasis letak persentil ($P$) dari skor rata-rata agregat kriteria:
                    </p>
                    <div class="text-[10px] text-slate-500 font-medium space-y-1.5 bg-white p-3 rounded-lg border border-slate-100">
                        @if(!empty($centroidAwal))
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-red-500">C1 (Rendah - $P_{15}$):</span>
                            <span class="text-slate-600 truncate font-semibold">{{ $centroidAwal['Rendah']['kode_kambing'] }}</span>
                        </div>
                        <div class="text-[9px] text-slate-400 pl-4 border-l border-red-200">
                            Bobot: {{ $centroidAwal['Rendah']['scores']['C1'] }} kg | Lahir: {{ $centroidAwal['Rendah']['scores']['C2'] }} ekor | Susu: {{ number_format($centroidAwal['Rendah']['scores']['C3'], 2) }} L
                        </div>

                        <div class="flex justify-between items-center mt-1 pt-1 border-t border-slate-50">
                            <span class="font-bold text-amber-500">C2 (Sedang - $P_{50}$):</span>
                            <span class="text-slate-600 truncate font-semibold">{{ $centroidAwal['Sedang']['kode_kambing'] }}</span>
                        </div>
                        <div class="text-[9px] text-slate-400 pl-4 border-l border-amber-200">
                            Bobot: {{ $centroidAwal['Sedang']['scores']['C1'] }} kg | Lahir: {{ $centroidAwal['Sedang']['scores']['C2'] }} ekor | Susu: {{ number_format($centroidAwal['Sedang']['scores']['C3'], 2) }} L
                        </div>

                        <div class="flex justify-between items-center mt-1 pt-1 border-t border-slate-50">
                            <span class="font-bold text-emerald-500">C3 (Tinggi - $P_{85}$):</span>
                            <span class="text-slate-600 truncate font-semibold">{{ $centroidAwal['Tinggi']['kode_kambing'] }}</span>
                        </div>
                        <div class="text-[9px] text-slate-400 pl-4 border-l border-emerald-200">
                            Bobot: {{ $centroidAwal['Tinggi']['scores']['C1'] }} kg | Lahir: {{ $centroidAwal['Tinggi']['scores']['C2'] }} ekor | Susu: {{ number_format($centroidAwal['Tinggi']['scores']['C3'], 2) }} L
                        </div>
                        @else
                        <span class="text-slate-400 italic">Data kambing dan log produktivitas belum tersedia.</span>
                        @endif
                    </div>
                </div>

                <!-- Param 3 -->
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1 md:col-span-2">
                    <span class="block font-bold text-slate-700">Kriteria Pembobot & Rumus Jarak</span>
                    <p class="text-slate-400 text-[10px] leading-relaxed">
                        Menggunakan <strong>jarak Euclidean 3D murni</strong> tanpa pembobotan kriteria ($w_j = 1$ untuk semua kriteria). Jarak geometris dihitung dengan formula:
                        <span class="block text-center font-mono font-semibold text-slate-700 bg-white py-2 px-2 rounded-lg border border-slate-100 mt-1">
                            $$d(p, c) = \sqrt{(p_{C1} - c_{C1})^2 + (p_{C2} - c_{C2})^2 + (p_{C3} - c_{C3})^2}$$
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- DISPLAY ITERATIVE STEP-BY-STEP CALCULATION -->
    <div x-data="{ activeTab: 1 }" class="space-y-6">

        <!-- Success Convergence Info Banner -->
        <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-xs font-medium border border-emerald-100/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-sm">
            <div class="flex items-start space-x-3">
                <span class="text-emerald-600 text-base"><i class="fa-solid fa-circle-check"></i></span>
                <div>
                    <span class="block font-bold">Algoritma K-Means Konvergen Sempurna!</span>
                    <span class="block text-slate-500 font-normal mt-0.5">Sistem berhasil mencapai kestabilan (centroid tidak lagi bergeser) pada **Iterasi ke-{{ $totalIterasi }}**. Pengelompokan kambing dihentikan dan disimpan secara permanen.</span>
                </div>
            </div>
            <a href="{{ route('clustering.hasil', ['sesi_id' => $sesiId]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-sm flex-shrink-0 transition text-center w-full sm:w-auto">
                Lihat Hasil Akhir <i class="fa-solid fa-chart-line ml-1"></i>
            </a>
        </div>

        <!-- Initial Centroid Iteration 0 Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 space-y-4 shadow-sm">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
                <span class="text-primary text-sm"><i class="fa-solid fa-flag-checkered"></i></span>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Hasil Inisialisasi Centroid Awal (Iterasi 0)</h3>
            </div>
            <p class="text-xxs text-slate-400">Berikut adalah data kambing riil yang terpilih secara dinamis berdasarkan letak persentil sebaran rata-rata parameter awal sebagai centroid awal.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Rendah Initial -->
                <div class="p-4 bg-red-50/50 border border-red-100/50 rounded-2xl space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xxs text-red-800 font-bold uppercase">C1: Rendah ($P_{15}$)</span>
                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[9px] font-semibold">Bawah</span>
                    </div>
                    <span class="block text-xs font-bold text-slate-800">Kambing: {{ $centroidAwal['Rendah']['kode_kambing'] }}</span>
                    <div class="pt-2 border-t border-red-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                        <div>
                            <span class="block text-slate-400 text-[9px]">Bobot (kg)</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Rendah']['scores']['C1'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Lahir</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Rendah']['scores']['C2'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Susu (L)</span>
                            <span class="font-bold text-slate-700">{{ number_format($centroidAwal['Rendah']['scores']['C3'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sedang Initial -->
                <div class="p-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xxs text-amber-800 font-bold uppercase">C2: Sedang ($P_{50}$)</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[9px] font-semibold">Tengah</span>
                    </div>
                    <span class="block text-xs font-bold text-slate-800">Kambing: {{ $centroidAwal['Sedang']['kode_kambing'] }}</span>
                    <div class="pt-2 border-t border-amber-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                        <div>
                            <span class="block text-slate-400 text-[9px]">Bobot (kg)</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Sedang']['scores']['C1'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Lahir</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Sedang']['scores']['C2'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Susu (L)</span>
                            <span class="font-bold text-slate-700">{{ number_format($centroidAwal['Sedang']['scores']['C3'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tinggi Initial -->
                <div class="p-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xxs text-emerald-800 font-bold uppercase">C3: Tinggi ($P_{85}$)</span>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[9px] font-semibold">Atas</span>
                    </div>
                    <span class="block text-xs font-bold text-slate-800">Kambing: {{ $centroidAwal['Tinggi']['kode_kambing'] }}</span>
                    <div class="pt-2 border-t border-emerald-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                        <div>
                            <span class="block text-slate-400 text-[9px]">Bobot (kg)</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Tinggi']['scores']['C1'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Lahir</span>
                            <span class="font-bold text-slate-700">{{ $centroidAwal['Tinggi']['scores']['C2'] }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px]">Susu (L)</span>
                            <span class="font-bold text-slate-700">{{ number_format($centroidAwal['Tinggi']['scores']['C3'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Iterations Tab Buttons -->
        <div class="bg-white p-3 rounded-2xl border border-slate-200 flex flex-wrap gap-1.5 shadow-sm">
            @for($i = 1; $i <= $totalIterasi; $i++)
                <button @click="activeTab = {{ $i }}"
                :class="activeTab === {{ $i }} ? 'bg-primary text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800'"
                class="px-4 py-2 rounded-xl text-xxs font-semibold transition duration-150">
                Iterasi {{ $i }} {{ $i == $totalIterasi ? '(Stabil)' : '' }}
                </button>
                @endfor
        </div>

        <!-- Iterations Container (AlpineJS Switch) -->
        @foreach($logIterasi as $index => $log)
        @php
        $iterationNum = $index + 1;
        @endphp
        <div x-show="activeTab === {{ $iterationNum }}" style="display: none;" class="space-y-6" x-transition>

            <!-- Grid: Centroids & Counts in this iteration -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Centroids in this iteration -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 md:col-span-2 space-y-4 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-800 border-b border-slate-100 pb-3 uppercase tracking-wider">Koordinat Centroid Iterasi {{ $iterationNum }}</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <!-- Rendah Centroid -->
                        <div class="p-3 bg-red-50/50 border border-red-100/50 rounded-xl space-y-1">
                            <span class="block text-xxs text-red-800 font-semibold">C1 (Rendah)</span>
                            <span class="block text-xs font-bold text-slate-700">Bobot: {{ $log['centroids']['Rendah']['C1'] }} kg</span>
                            <span class="block text-[9px] text-slate-400 font-medium">Lahir: {{ $log['centroids']['Rendah']['C2'] }} | Susu: {{ number_format($log['centroids']['Rendah']['C3'], 2) }} L</span>
                        </div>
                        <!-- Sedang Centroid -->
                        <div class="p-3 bg-amber-50/50 border border-amber-100/50 rounded-xl space-y-1">
                            <span class="block text-xxs text-amber-800 font-semibold">C2 (Sedang)</span>
                            <span class="block text-xs font-bold text-slate-700">Bobot: {{ $log['centroids']['Sedang']['C1'] }} kg</span>
                            <span class="block text-[9px] text-slate-400 font-medium">Lahir: {{ $log['centroids']['Sedang']['C2'] }} | Susu: {{ number_format($log['centroids']['Sedang']['C3'], 2) }} L</span>
                        </div>
                        <!-- Tinggi Centroid -->
                        <div class="p-3 bg-emerald-50/50 border border-emerald-100/50 rounded-xl space-y-1">
                            <span class="block text-xxs text-emerald-800 font-semibold">C3 (Tinggi)</span>
                            <span class="block text-xs font-bold text-slate-700">Bobot: {{ $log['centroids']['Tinggi']['C1'] }} kg</span>
                            <span class="block text-[9px] text-slate-400 font-medium">Lahir: {{ $log['centroids']['Tinggi']['C2'] }} | Susu: {{ number_format($log['centroids']['Tinggi']['C3'], 2) }} L</span>
                        </div>
                    </div>
                </div>

                <!-- Assigned Count in this iteration -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col justify-between shadow-sm">
                    <h3 class="text-xs font-bold text-slate-800 border-b border-slate-100 pb-3 uppercase tracking-wider">Sebaran Anggota</h3>
                    <div class="space-y-2 pt-2 text-xs text-slate-500 font-semibold">
                        <div class="flex justify-between items-center py-1 border-b border-slate-50">
                            <span>Kluster Rendah (C1):</span>
                            <span class="font-bold text-red-700 px-2 py-0.5 bg-red-50 rounded-lg">{{ $log['assignments_count']['Rendah'] }} Kambing</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-50">
                            <span>Kluster Sedang (C2):</span>
                            <span class="font-bold text-amber-700 px-2 py-0.5 bg-amber-50 rounded-lg">{{ $log['assignments_count']['Sedang'] }} Kambing</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span>Kluster Tinggi (C3):</span>
                            <span class="font-bold text-emerald-700 px-2 py-0.5 bg-emerald-50 rounded-lg">{{ $log['assignments_count']['Tinggi'] }} Kambing</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distance Calculation Table Card in this iteration -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-slate-100">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Lembar Hitung Jarak Euclidean Iterasi {{ $iterationNum }}</h4>
                    <p class="text-xxs text-slate-400 mt-0.5">Tabel visualisasi perhitungan jarak geometri (Euclidean) setiap koordinat agregat kambing ke ketiga centroid.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Kode Kambing</th>
                                <th class="px-6 py-4 text-center">Bobot Terakhir</th>
                                <th class="px-6 py-4 text-center">Lahir 6 Bln</th>
                                <th class="px-6 py-4 text-center">Avg Susu</th>
                                <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak ke Rendah (C1)</th>
                                <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak ke Sedang (C2)</th>
                                <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak ke Tinggi (C3)</th>
                                <th class="px-6 py-4 text-center">Kluster Sementara</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                            @foreach($log['details'] as $kambingId => $detail)
                            @php
                            $c1 = $detail['jarak_c1'];
                            $c2 = $detail['jarak_c2'];
                            $c3 = $detail['jarak_c3'];
                            $minDist = min($c1, $c2, $c3);
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <!-- Kode -->
                                <td class="px-6 py-3.5 font-bold text-slate-800">
                                    {{ $detail['kode_kambing'] }}
                                </td>

                                <!-- Agregasi Parameter -->
                                <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ $detail['scores']['C1'] }} kg</td>
                                <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ $detail['scores']['C2'] }} ekor</td>
                                <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ number_format($detail['scores']['C3'], 2) }} L</td>

                                <!-- Euclidean distances with smallest highlighted -->
                                <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c1 == $minDist ? 'bg-red-50 text-red-700 border-x border-red-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c1, 4) }}
                                </td>
                                <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c2 == $minDist ? 'bg-amber-50 text-amber-700 border-x border-amber-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c2, 4) }}
                                </td>
                                <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c3 == $minDist ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c3, 4) }}
                                </td>

                                <!-- Assigned Kluster -->
                                <td class="px-6 py-3.5 text-center">
                                    @if($detail['kluster_sementara'] == 'Rendah')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-red-50 text-red-700 border border-red-100/50">
                                        <i class="fa-solid fa-arrow-trend-down mr-1"></i> Rendah
                                    </span>
                                    @elseif($detail['kluster_sementara'] == 'Sedang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-amber-50 text-amber-700 border border-amber-100/50">
                                        <i class="fa-solid fa-chart-simple mr-1"></i> Sedang
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
                                        <i class="fa-solid fa-arrow-trend-up mr-1"></i> Tinggi
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        @endforeach

    </div>
    @endif
</div>
@endsection