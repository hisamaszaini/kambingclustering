@extends('layouts.app')

@section('content')
<div x-data="{ 
    openAddProduktivitasModal: false,
    openEditModal: false,
    openDeleteModal: false,
    editLog: {},
    deleteActionUrl: '',
    chartRange: 'all',
    updateRange(range) {
        this.chartRange = range;
        window.dispatchEvent(new CustomEvent('filter-charts', { detail: range }));
    }
}" class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                <i class="fa-solid fa-file-invoice text-xl"></i>
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-800">Detail & Perkembangan Kambing</h1>
                <p class="text-slate-400 text-xs font-medium mt-0.5">Pantau data historis produktivitas dan riwayat kluster kambing.</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Input Productivity Button -->
            <button @click="openAddProduktivitasModal = true" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-xs shadow-sm transition flex items-center space-x-2">
                <span><i class="fa-solid fa-plus"></i></span>
                <span>Input Produktivitas</span>
            </button>

            <!-- Back Button -->
            <a href="{{ route('kambing.index') }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition flex items-center space-x-2">
                <span><i class="fa-solid fa-arrow-left"></i></span>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    @if(count($notifikasis) > 0)
    <!-- Active Alerts Alert Box -->
    <div class="bg-rose-50 border border-rose-100 p-5 rounded-2xl space-y-3">
        <div class="flex items-center space-x-2 text-rose-800 font-bold text-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
            <span>Peringatan Jadwal Perekaman Aktif</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($notifikasis as $notif)
                <div class="flex items-start space-x-2.5 bg-white p-3 rounded-xl border border-rose-100/50 shadow-sm text-[10px] font-semibold">
                    @if($notif['status'] === 'critical')
                        <span class="text-rose-600 shrink-0 mt-0.5"><i class="fa-solid fa-triangle-exclamation text-sm"></i></span>
                    @elseif($notif['status'] === 'warning')
                        <span class="text-amber-500 shrink-0 mt-0.5"><i class="fa-solid fa-circle-exclamation text-sm"></i></span>
                    @else
                        <span class="text-sky-500 shrink-0 mt-0.5"><i class="fa-solid fa-circle-info text-sm"></i></span>
                    @endif
                    
                    <div class="flex flex-col space-y-1">
                        <span class="text-slate-650 leading-relaxed">{{ $notif['pesan'] }}</span>
                        <button type="button" @click="openAddProduktivitasModal = true" class="text-primary hover:text-primary-hover hover:underline text-[9px] font-bold text-left flex items-center gap-0.5 mt-0.5">
                            <span>Input Data Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-[7px] mt-0.5"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Stats Widgets Grid -->
    @if($kambing->jenis_kelamin == 'Betina')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Profile Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Identitas Kambing</span>
                    <span class="text-sm font-bold text-slate-800">{{ $kambing->kode_kambing }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-pink-50 text-pink-600">
                    <i class="fa-solid fa-venus mr-1"></i>{{ $kambing->jenis_kelamin }}
                </span>
            </div>
            <div class="pt-3 text-[11px] font-semibold text-slate-400">
                Terdaftar: <span class="text-slate-600">{{ $kambing->created_at->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <!-- Weight Stats Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bobot Badan (Terakhir)</span>
                <span class="text-2xl font-bold text-slate-800 leading-none">
                    {{ $latestWeightRecord ? $latestWeightRecord->bobot_badan : 0 }} <span class="text-xs font-semibold text-slate-400">kg</span>
                </span>
            </div>
            <div class="border-t border-slate-100/80 pt-3 mt-3 flex justify-between items-center text-[11px] text-slate-500 font-semibold">
                <span>Rata-rata:</span>
                <span class="text-slate-800">{{ round($avgBobot, 2) }} kg</span>
            </div>
        </div>

        <!-- Milk Production Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Produksi Susu (Terakhir)</span>
                <span class="text-2xl font-bold text-slate-800 leading-none">
                    {{ $latestMilkRecord ? $latestMilkRecord->produksi_susu : 0 }} <span class="text-xs font-semibold text-slate-400">Liter</span>
                </span>
            </div>
            <div class="border-t border-slate-100/80 pt-3 mt-3 flex justify-between items-center text-[11px] text-slate-500 font-semibold">
                <span>Rata-rata:</span>
                <span class="text-slate-800">{{ round($avgSusu, 2) }} L</span>
            </div>
        </div>

        <!-- Birth Rate Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tingkat Kelahiran (Total)</span>
                <span class="text-2xl font-bold text-slate-800 leading-none">
                    {{ $produktivitasHistory->sum('tingkat_kelahiran') }} <span class="text-xs font-semibold text-slate-400">ekor</span>
                </span>
            </div>
            <div class="border-t border-slate-100/80 pt-3 mt-3 flex justify-between items-center text-[11px] text-slate-500 font-semibold">
                <span>Rata-rata per Lahir:</span>
                <span class="text-slate-800">{{ round($avgLahir, 2) }} ekor</span>
            </div>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profile Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Identitas Kambing</span>
                    <span class="text-sm font-bold text-slate-800">{{ $kambing->kode_kambing }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-sky-50 text-sky-600">
                    <i class="fa-solid fa-mars mr-1"></i>{{ $kambing->jenis_kelamin }}
                </span>
            </div>
            <div class="pt-3 text-[11px] font-semibold text-slate-400">
                Terdaftar: <span class="text-slate-600">{{ $kambing->created_at->translatedFormat('d M Y') }}</span>
            </div>
        </div>

        <!-- Weight Stats Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bobot Badan (Terakhir)</span>
                <span class="text-2xl font-bold text-slate-800 leading-none">
                    {{ $latestWeightRecord ? $latestWeightRecord->bobot_badan : 0 }} <span class="text-xs font-semibold text-slate-400">kg</span>
                </span>
            </div>
            <div class="border-t border-slate-100/80 pt-3 mt-3 flex justify-between items-center text-[11px] text-slate-500 font-semibold">
                <span>Rata-rata:</span>
                <span class="text-slate-800">{{ round($avgBobot, 2) }} kg</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts Section Header with Filter -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-primary"></i> Grafik Tren Perkembangan
            </h3>
            <p class="text-slate-400 text-[10px] font-medium mt-0.5">Visualisasi data historis produktivitas kambing (hanya tanggal terisi).</p>
        </div>
        
        <!-- Filter Tabs (AlpineJS) -->
        <div class="flex bg-slate-100 p-0.5 rounded-xl border border-slate-200 text-[10px] font-bold">
            <button @click="updateRange('all')"
                :class="chartRange === 'all' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-3 py-1.5 rounded-lg transition-all">Semua</button>
            <button @click="updateRange('3m')"
                :class="chartRange === '3m' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-3 py-1.5 rounded-lg transition-all">3 Bulan</button>
            <button @click="updateRange('6m')"
                :class="chartRange === '6m' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-3 py-1.5 rounded-lg transition-all">6 Bulan</button>
            <button @click="updateRange('1y')"
                :class="chartRange === '1y' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-3 py-1.5 rounded-lg transition-all">1 Tahun</button>
        </div>
    </div>

    <!-- Progress Charts -->
    <div class="grid grid-cols-1 {{ $kambing->jenis_kelamin == 'Betina' ? 'lg:grid-cols-3' : '' }} gap-6">
        <!-- Bobot Badan Chart -->
        <div class="{{ $kambing->jenis_kelamin == 'Betina' ? '' : 'col-span-1' }} bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> Progres Bobot Badan (kg)
            </h3>
            <div class="h-64">
                <canvas id="weightChart"></canvas>
            </div>
        </div>

        @if($kambing->jenis_kelamin == 'Betina')
        <!-- Produksi Susu Chart -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Progres Produksi Susu (Liter)
            </h3>
            <div class="h-64">
                <canvas id="milkChart"></canvas>
            </div>
        </div>

        <!-- Tingkat Kelahiran Chart -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Progres Tingkat Kelahiran (ekor)
            </h3>
            <div class="h-64">
                <canvas id="birthChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    @if($kambing->jenis_kelamin == 'Jantan')
    <!-- Informative Notice for Jantan -->
    <div class="bg-slate-50 border border-slate-200 p-4.5 rounded-2xl flex items-center space-x-3.5">
        <div class="w-9 h-9 rounded-full bg-orange-100 flex items-center justify-center text-primary shrink-0">
            <i class="fa-solid fa-circle-info text-sm"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold text-slate-700">Informasi Parameter Produktivitas peJantan</h4>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5">Berdasarkan karakteristik biologis, kambing pejantan tidak memiliki data tingkat kelahiran dan produksi susu. Hanya parameter bobot badan yang dipantau serta dihitung dalam proses clustering.</p>
        </div>
    </div>
    @endif

    <!-- Data Logs & Clustering History Split/Stack Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <!-- History Productivity Logs Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Log Riwayat Produktivitas</h3>
                    <p class="text-slate-400 text-[10px] font-medium mt-0.5">Daftar rekaman fisik kambing berdasarkan tanggal pencatatan.</p>
                </div>

                <div class="overflow-x-auto max-h-[350px] overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider sticky top-0">
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3 text-center">Bobot</th>
                                @if($kambing->jenis_kelamin == 'Betina')
                                <th class="px-6 py-3 text-center">Kelahiran</th>
                                <th class="px-6 py-3 text-center">Susu</th>
                                @endif
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-650 font-medium">
                            @forelse($produktivitasHistory as $log)
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-3 font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($log->tanggal_pencatatan)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-3 text-center font-bold text-slate-700">
                                    @if($log->bobot_badan !== null)
                                    {{ $log->bobot_badan }} kg
                                    @else
                                    <span class="text-slate-300 italic text-[10px] font-normal">Belum diisi</span>
                                    @endif
                                </td>
                                @if($kambing->jenis_kelamin == 'Betina')
                                <td class="px-6 py-3 text-center text-slate-650">
                                    @if($log->tingkat_kelahiran !== null)
                                    {{ $log->tingkat_kelahiran }} ekor
                                    @else
                                    <span class="text-slate-300 italic text-[10px] font-normal">Belum diisi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-slate-650">
                                    @if($log->produksi_susu !== null)
                                    {{ $log->produksi_susu }} L
                                    @else
                                    <span class="text-slate-300 italic text-[10px] font-normal">Belum diisi</span>
                                    @endif
                                </td>
                                @endif
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button @click="
                                                editLog = {
                                                    id: '{{ $log->id }}',
                                                    kambing_id: '{{ $log->kambing_id }}',
                                                    tanggal_pencatatan: '{{ $log->tanggal_pencatatan }}',
                                                    bobot_badan: '{{ $log->bobot_badan }}',
                                                    tingkat_kelahiran: '{{ $log->tingkat_kelahiran }}',
                                                    produksi_susu: '{{ $log->produksi_susu }}'
                                                };
                                                editKambingId = '{{ $log->kambing_id }}';
                                                openEditModal = true;
                                            "
                                            class="w-6 h-6 flex items-center justify-center rounded-lg bg-orange-50 hover:bg-orange-100 text-primary hover:text-primary-hover transition"
                                            title="Edit Log">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        </button>

                                        <button type="button"
                                            @click="deleteActionUrl = '{{ route('produktivitas.destroy', $log->id) }}'; openDeleteModal = true;"
                                            class="w-6 h-6 flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                            title="Hapus Log">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $kambing->jenis_kelamin == 'Betina' ? 5 : 3 }}" class="px-6 py-12 text-center text-slate-400">
                                    <div class="text-[11px] font-semibold">Tidak ada riwayat pencatatan</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- K-Means Clustering History Table -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Jejak Kluster K-Means</h3>
                    <p class="text-slate-400 text-[10px] font-medium mt-0.5">Riwayat hasil kluster dan jarak centroid pada setiap sesi analisis.</p>
                </div>

                <div class="overflow-x-auto max-h-[350px] overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wider sticky top-0">
                                <th class="px-6 py-3">Sesi</th>
                                <th class="px-6 py-3 text-center">Kluster</th>
                                <th class="px-6 py-3 text-center">Data saat itu</th>
                                <th class="px-6 py-3 text-center">Jarak (C1 / C2 / C3)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-650 font-medium">
                            @forelse($clusteringHistory as $cLog)
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <td class="px-6 py-3">
                                    <span class="block font-bold text-slate-800 leading-tight">Sesi #{{ $cLog->sesi_id }}</span>
                                    <span class="block text-[9px] text-slate-400">{{ $cLog->sesi->created_at->translatedFormat('d/m/y H:i') }}</span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($cLog->cluster == 'Rendah')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-red-50 text-red-700 border border-red-100/50">
                                        Rendah
                                    </span>
                                    @elseif($cLog->cluster == 'Sedang')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100/50">
                                        Sedang
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
                                        Tinggi
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-[10px] text-center font-semibold text-slate-500">
                                    {{ $cLog->bobot_badan_val }}kg
                                    @if($kambing->jenis_kelamin == 'Betina')
                                    / {{ $cLog->tingkat_kelahiran_val }}ekor / {{ $cLog->produksi_susu_val }}L
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-[10px] text-slate-400 font-normal">
                                    {{ round($cLog->jarak_c1, 2) }} / {{ round($cLog->jarak_c2, 2) }} / {{ round($cLog->jarak_c3, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div class="text-[11px] font-semibold">Belum pernah di-cluster</div>
                                    <div class="text-[9px] text-slate-400">Silakan jalankan proses clustering K-Means.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- MODAL: ADD PRODUCTIVITAS -->
    <div x-show="openAddProduktivitasModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddProduktivitasModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Catat Produktivitas Baru</h3>
                    </div>
                    <button @click="openAddProduktivitasModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('produktivitas.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <!-- Hidden inputs -->
                    <input type="hidden" name="kambing_id" value="{{ $kambing->id }}">
                    <input type="hidden" name="redirect_to" value="{{ route('kambing.show', $kambing->id) }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kode Kambing</label>
                        <input type="text" readonly value="{{ $kambing->kode_kambing }} ({{ $kambing->jenis_kelamin }})" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold bg-slate-50 text-slate-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Pencatatan</label>
                        <input type="date" name="tanggal_pencatatan" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bobot Badan (kg)</label>
                        <input type="number" name="bobot_badan" step="0.01" min="0" placeholder="Kosongkan jika tidak ditimbang" value="" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    @if($kambing->jenis_kelamin === 'Betina')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tingkat Kelahiran (ekor)</label>
                        <input type="number" name="tingkat_kelahiran" min="0" placeholder="Kosongkan jika tidak melahirkan" value="" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Produksi Susu (Liter)</label>
                        <input type="number" name="produksi_susu" step="0.01" min="0" placeholder="Kosongkan jika tidak diperah" value="" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>
                    @else
                    <!-- Hidden fields for Jantan to satisfy backend validation rules -->
                    <input type="hidden" name="tingkat_kelahiran" value="0">
                    <input type="hidden" name="produksi_susu" value="0">
                    @endif

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openAddProduktivitasModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan Rekaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT PRODUCTIVITAS -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Ubah Pencatatan Produktivitas</h3>
                    </div>
                    <button @click="openEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form :action="`{{ url('produktivitas') }}/${editLog.id}`" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <!-- Hidden inputs -->
                    <input type="hidden" name="kambing_id" value="{{ $kambing->id }}">
                    <input type="hidden" name="redirect_to" value="{{ route('kambing.show', $kambing->id) }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kode Kambing</label>
                        <input type="text" readonly value="{{ $kambing->kode_kambing }} ({{ $kambing->jenis_kelamin }})" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold bg-slate-50 text-slate-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Pencatatan</label>
                        <input type="date" name="tanggal_pencatatan" x-model="editLog.tanggal_pencatatan" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bobot Badan (kg)</label>
                        <input type="number" name="bobot_badan" x-model="editLog.bobot_badan" step="0.01" min="0" placeholder="Kosongkan jika tidak ditimbang" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    @if($kambing->jenis_kelamin === 'Betina')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tingkat Kelahiran (ekor)</label>
                        <input type="number" name="tingkat_kelahiran" x-model="editLog.tingkat_kelahiran" min="0" placeholder="Kosongkan jika tidak melahirkan" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Produksi Susu (Liter)</label>
                        <input type="number" name="produksi_susu" x-model="editLog.produksi_susu" step="0.01" min="0" placeholder="Kosongkan jika tidak diperah" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>
                    @else
                    <!-- Hidden fields for Jantan -->
                    <input type="hidden" name="tingkat_kelahiran" value="">
                    <input type="hidden" name="produksi_susu" value="">
                    @endif

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="openDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openDeleteModal = false"></div>

            <div class="relative z-10 w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform p-6">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">Konfirmasi Hapus?</h3>
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin menghapus data pencatatan produktivitas ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <form :action="deleteActionUrl" method="POST" class="flex justify-center space-x-3 pt-6 mt-2 border-t border-slate-100">
                    @csrf
                    @method('DELETE')
                    <!-- Hidden input to redirect back to show page -->
                    <input type="hidden" name="redirect_to" value="{{ route('kambing.show', $kambing->id) }}">
                    
                    <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition">Hapus Data</button>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Interactive Chart Scripts (Chart.js) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Parse database values passed from controller
        const rawHistory = {!! json_encode($produktivitasHistory) !!};
        
        // Filter history for each metric to handle nulls safely (only keep records with actual values)
        const weightHistory = rawHistory.filter(item => item.bobot_badan !== null);
        const milkHistory = rawHistory.filter(item => item.produksi_susu !== null);
        const birthHistory = rawHistory.filter(item => item.tingkat_kelahiran !== null && item.tingkat_kelahiran > 0);

        // Shared chart styling parameters
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    titleFont: { size: 10, weight: 'bold', family: 'Poppins' },
                    bodyFont: { size: 11, family: 'Poppins' },
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: 'Poppins' }
                    }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 9, family: 'Poppins' }
                    }
                }
            }
        };

        // Helper to format date
        const formatDate = (dateStr) => {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        };

        // 1. Weight Chart Initialization
        const ctxWeight = document.getElementById('weightChart').getContext('2d');
        const weightGradient = ctxWeight.createLinearGradient(0, 0, 0, 240);
        weightGradient.addColorStop(0, 'rgba(255, 132, 0, 0.2)');
        weightGradient.addColorStop(1, 'rgba(255, 132, 0, 0.0)');

        const weightChart = new Chart(ctxWeight, {
            type: 'line',
            data: {
                labels: weightHistory.map(item => formatDate(item.tanggal_pencatatan)),
                datasets: [{
                    label: 'Bobot Badan',
                    data: weightHistory.map(item => parseFloat(item.bobot_badan)),
                    borderColor: '#FF8400',
                    borderWidth: 2,
                    backgroundColor: weightGradient,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#FF8400',
                    pointHoverRadius: 6,
                }]
            },
            options: chartOptions
        });

        // 2. Milk Chart Initialization (Betina only)
        let milkChart;
        const ctxMilkEl = document.getElementById('milkChart');
        if (ctxMilkEl) {
            const ctxMilk = ctxMilkEl.getContext('2d');
            const milkGradient = ctxMilk.createLinearGradient(0, 0, 0, 240);
            milkGradient.addColorStop(0, 'rgba(14, 165, 233, 0.2)');
            milkGradient.addColorStop(1, 'rgba(14, 165, 233, 0.0)');

            milkChart = new Chart(ctxMilk, {
                type: 'line',
                data: {
                    labels: milkHistory.map(item => formatDate(item.tanggal_pencatatan)),
                    datasets: [{
                        label: 'Produksi Susu',
                        data: milkHistory.map(item => parseFloat(item.produksi_susu)),
                        borderColor: '#0ea5e9',
                        borderWidth: 2,
                        backgroundColor: milkGradient,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0ea5e9',
                        pointHoverRadius: 6,
                    }]
                },
                options: chartOptions
            });
        }

        // 3. Birth Rate Chart Initialization (Betina only)
        let birthChart;
        const ctxBirthEl = document.getElementById('birthChart');
        if (ctxBirthEl) {
            const ctxBirth = ctxBirthEl.getContext('2d');
            const birthGradient = ctxBirth.createLinearGradient(0, 0, 0, 240);
            birthGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            birthGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            birthChart = new Chart(ctxBirth, {
                type: 'line',
                data: {
                    labels: birthHistory.map(item => formatDate(item.tanggal_pencatatan)),
                    datasets: [{
                        label: 'Tingkat Kelahiran',
                        data: birthHistory.map(item => parseInt(item.tingkat_kelahiran)),
                        borderColor: '#10b981',
                        borderWidth: 2,
                        backgroundColor: birthGradient,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6,
                    }]
                },
                options: chartOptions
            });
        }

        // Listener to filter charts dynamically by time range
        window.addEventListener('filter-charts', function (e) {
            const range = e.detail;
            
            // Calculate cutoff date
            let cutoffDate = null;
            if (range !== 'all') {
                cutoffDate = new Date();
                if (range === '3m') cutoffDate.setMonth(cutoffDate.getMonth() - 3);
                else if (range === '6m') cutoffDate.setMonth(cutoffDate.getMonth() - 6);
                else if (range === '1y') cutoffDate.setFullYear(cutoffDate.getFullYear() - 1);
            }

            // A. Update Weight Chart
            let filteredWeight = [...weightHistory];
            if (cutoffDate) {
                filteredWeight = weightHistory.filter(item => new Date(item.tanggal_pencatatan) >= cutoffDate);
            }
            weightChart.data.labels = filteredWeight.map(item => formatDate(item.tanggal_pencatatan));
            weightChart.data.datasets[0].data = filteredWeight.map(item => parseFloat(item.bobot_badan));
            weightChart.update();

            // B. Update Milk Chart
            if (milkChart) {
                let filteredMilk = [...milkHistory];
                if (cutoffDate) {
                    filteredMilk = milkHistory.filter(item => new Date(item.tanggal_pencatatan) >= cutoffDate);
                }
                milkChart.data.labels = filteredMilk.map(item => formatDate(item.tanggal_pencatatan));
                milkChart.data.datasets[0].data = filteredMilk.map(item => parseFloat(item.produksi_susu));
                milkChart.update();
            }

            // C. Update Birth Chart
            if (birthChart) {
                let filteredBirth = [...birthHistory];
                if (cutoffDate) {
                    filteredBirth = birthHistory.filter(item => new Date(item.tanggal_pencatatan) >= cutoffDate);
                }
                birthChart.data.labels = filteredBirth.map(item => formatDate(item.tanggal_pencatatan));
                birthChart.data.datasets[0].data = filteredBirth.map(item => parseInt(item.tingkat_kelahiran));
                birthChart.update();
            }
        });
    });
</script>
@endsection
