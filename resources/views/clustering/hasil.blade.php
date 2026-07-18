@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-base font-bold text-slate-800">Hasil Analisis Kluster K-Means</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Pemetaan kinerja produktivitas kambing berdasarkan kluster Rendah, Sedang, dan Tinggi.</p>
        </div>

        @if($selectedSesi)
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <!-- Export Excel -->
            <a href="{{ route('clustering.export-excel', ['sesi_id' => $selectedSesi->id]) }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition flex items-center space-x-2 w-full sm:w-auto justify-center">
                <span><i class="fa-solid fa-file-excel text-emerald-600"></i></span>
                <span>Ekspor Excel</span>
            </a>

            <!-- Cetak PDF -->
            <a href="{{ route('clustering.export-pdf', ['sesi_id' => $selectedSesi->id]) }}" target="_blank" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-xs shadow-sm transition flex items-center space-x-2 w-full sm:w-auto justify-center">
                <span><i class="fa-solid fa-print"></i></span>
                <span>Cetak Laporan</span>
            </a>
        </div>
        @endif
    </div>

    @if($selectedSesi)
    <!-- Top Section: Info & Profiles Grid (Horizontal Row) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Sesi Selector & Stats Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Sesi Terpilih</span>
                @if($sesiList->isNotEmpty())
                <form action="{{ route('clustering.hasil') }}" method="GET" class="w-full">
                    <select name="sesi_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary cursor-pointer text-slate-700">
                        @foreach($sesiList as $s)
                        <option value="{{ $s->id }}" {{ $selectedSesi->id == $s->id ? 'selected' : '' }}>
                            Sesi {{ $s->id }} - {{ $s->created_at->translatedFormat('d M Y, H:i') }}
                        </option>
                        @endforeach
                    </select>
                    @if($filterCluster)
                    <input type="hidden" name="cluster" value="{{ $filterCluster }}">
                    @endif
                </form>
                @else
                <span class="text-slate-400 text-xs font-medium italic">Tidak ada sesi clustering aktif</span>
                @endif
            </div>

            <!-- Selected Sesi Stats List -->
            <div class="space-y-2 pt-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-405 font-medium">Proses</span>
                    <span class="font-bold text-slate-700">{{ $selectedSesi->created_at->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-405 font-medium">Oleh</span>
                    <span class="font-bold text-slate-700 truncate max-w-[120px]">{{ $selectedSesi->user->name }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-405 font-medium">Data</span>
                    <span class="font-bold text-slate-700">{{ $selectedSesi->total_data }} ekor</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-405 font-medium">Iterasi</span>
                    <span class="font-bold text-slate-700">{{ $selectedSesi->total_iterasi }} kali</span>
                </div>
            </div>
        </div>

        <!-- Tinggi Profile -->
        <div class="bg-emerald-50/30 p-5 rounded-2xl border border-emerald-100/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center border-b border-emerald-100/50 pb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                    <i class="fa-solid fa-arrow-trend-up text-emerald-600"></i> Tinggi
                </span>
            </div>
            <div class="py-4 text-center">
                <span class="block text-4xl font-black text-slate-800 leading-none">{{ $clusterCounts['Tinggi'] }}</span>
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">Ekor Kambing</span>
            </div>
            <div class="space-y-1.5 border-t border-emerald-100/50 pt-3 text-[11px] font-medium text-slate-655">
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Bobot Terakhir</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Tinggi']['C1'] }} kg</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Lahir 6 Bln</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Tinggi']['C2'] }} ekor</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Avg Susu</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Tinggi']['C3'] }} L</span>
                </div>
            </div>
        </div>

        <!-- Sedang Profile -->
        <div class="bg-amber-50/30 p-5 rounded-2xl border border-amber-100/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center border-b border-amber-100/50 pb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800">
                    <i class="fa-solid fa-chart-simple text-amber-600"></i> Sedang
                </span>
            </div>
            <div class="py-4 text-center">
                <span class="block text-4xl font-black text-slate-800 leading-none">{{ $clusterCounts['Sedang'] }}</span>
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">Ekor Kambing</span>
            </div>
            <div class="space-y-1.5 border-t border-amber-100/50 pt-3 text-[11px] font-medium text-slate-655">
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Bobot Terakhir</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Sedang']['C1'] }} kg</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Lahir 6 Bln</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Sedang']['C2'] }} ekor</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Avg Susu</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Sedang']['C3'] }} L</span>
                </div>
            </div>
        </div>

        <!-- Rendah Profile -->
        <div class="bg-red-50/30 p-5 rounded-2xl border border-red-100/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center border-b border-red-100/50 pb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-800">
                    <i class="fa-solid fa-arrow-trend-down text-red-600"></i> Rendah
                </span>
            </div>
            <div class="py-4 text-center">
                <span class="block text-4xl font-black text-slate-800 leading-none">{{ $clusterCounts['Rendah'] }}</span>
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">Ekor Kambing</span>
            </div>
            <div class="space-y-1.5 border-t border-red-100/50 pt-3 text-[11px] font-medium text-slate-655">
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Bobot Terakhir</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Rendah']['C1'] }} kg</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Lahir 6 Bln</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Rendah']['C2'] }} ekor</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-450">Avg Susu</span>
                    <span class="font-bold text-slate-700">{{ $clusterAverages['Rendah']['C3'] }} L</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Full Width Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tabel Pembagian Kluster</h3>

            <!-- Filter Cluster Tabs -->
            <div class="flex bg-slate-100 p-0.5 rounded-xl border border-slate-200 text-xs font-semibold">
                <a href="{{ route('clustering.hasil', ['sesi_id' => $selectedSesi->id, 'cluster' => '']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filterCluster == '' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Semua</a>
                <a href="{{ route('clustering.hasil', ['sesi_id' => $selectedSesi->id, 'cluster' => 'Tinggi']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filterCluster == 'Tinggi' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Tinggi</a>
                <a href="{{ route('clustering.hasil', ['sesi_id' => $selectedSesi->id, 'cluster' => 'Sedang']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filterCluster == 'Sedang' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Sedang</a>
                <a href="{{ route('clustering.hasil', ['sesi_id' => $selectedSesi->id, 'cluster' => 'Rendah']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filterCluster == 'Rendah' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Rendah</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Kode Kambing</th>
                        <th class="px-6 py-4">Gender</th>
                        <th class="px-6 py-4 text-center">Bobot Terakhir</th>
                        <th class="px-6 py-4 text-center">Lahir 6 Bln</th>
                        <th class="px-6 py-4 text-center">Avg Susu</th>
                        <th class="px-6 py-4 text-center">Jarak Rendah</th>
                        <th class="px-6 py-4 text-center">Jarak Sedang</th>
                        <th class="px-6 py-4 text-center">Jarak Tinggi</th>
                        <th class="px-6 py-4 text-center">Kluster</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($hasils as $h)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-3.5 font-bold text-slate-800">{{ $h->kambing->kode_kambing }}</td>
                        <td class="px-6 py-3.5">
                            <span class="px-2 py-0.5 rounded-lg text-xxs font-semibold {{ $h->kambing->jenis_kelamin == 'Jantan' ? 'bg-sky-50 text-sky-600' : 'bg-pink-50 text-pink-600' }}">
                                {{ $h->kambing->jenis_kelamin }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center font-semibold text-slate-750">{{ $h->bobot_badan_val }} kg</td>
                        <td class="px-6 py-3.5 text-center font-semibold text-slate-750">
                            @if($h->kambing->jenis_kelamin == 'Jantan')
                            <span class="text-slate-300 italic text-[10px] font-normal">N/A</span>
                            @else
                            {{ $h->tingkat_kelahiran_val }} ekor
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center font-semibold text-slate-750">
                            @if($h->kambing->jenis_kelamin == 'Jantan')
                            <span class="text-slate-300 italic text-[10px] font-normal">N/A</span>
                            @else
                            {{ number_format($h->produksi_susu_val, 2) }} L
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center font-normal text-slate-400">{{ round($h->jarak_c1, 3) }}</td>
                        <td class="px-6 py-3.5 text-center font-normal text-slate-400">{{ round($h->jarak_c2, 3) }}</td>
                        <td class="px-6 py-3.5 text-center font-normal text-slate-400">{{ round($h->jarak_c3, 3) }}</td>
                        <td class="px-6 py-3.5 text-center">
                            @if($h->cluster == 'Rendah')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-red-50 text-red-700 border border-red-100/50">
                                <i class="fa-solid fa-arrow-trend-down mr-1"></i> Rendah
                            </span>
                            @elseif($h->cluster == 'Sedang')
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
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                            <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-diagram-project"></i></div>
                            <div class="text-xs font-semibold">Data hasil cluster tidak ditemukan</div>
                            <div class="text-xxs font-medium text-slate-400">Silakan pilih filter lain atau jalankan K-Means kembali.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($hasils->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $hasils->links('partials.pagination') }}
        </div>
        @endif
    </div>
    @else
    <!-- Empty Sesi List View -->
    <div class="bg-white p-12 text-center space-y-3 rounded-2xl border border-slate-200 shadow-sm max-w-2xl mx-auto">
        <div class="text-4xl text-slate-300"><i class="fa-solid fa-diagram-project"></i></div>
        <h3 class="text-sm font-bold text-slate-700">Analisis Kluster Belum Tersedia</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto">Sistem mendeteksi belum ada proses clustering K-Means yang tersimpan di database. Silakan masuk ke halaman Proses K-Means terlebih dahulu.</p>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('clustering.proses-form') }}" class="inline-block px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-sm transition">
            Jalankan Perhitungan Sekarang
        </a>
        @endif
    </div>
    @endif
</div>
@endsection