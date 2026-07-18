@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openDeleteModal: false,
        deleteActionUrl: '',
        openBulkDeleteModal: false,
        editLog: {},
        selectedIds: [],
        selectAll: false,
        kambingGenders: {
            @foreach($kambings as $k)
                '{{ $k->id }}': '{{ $k->jenis_kelamin }}',
            @endforeach
        },
        selectedKambingId: '',
        editKambingId: '',
        isJantan(id) {
            return this.kambingGenders[id] === 'Jantan';
        },
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedIds = [@foreach($produktivitasList as $log)'{{ $log->id }}',@endforeach];
            } else {
                this.selectedIds = [];
            }
        },
        toggleSelect(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(item => item !== id);
                this.selectAll = false;
            } else {
                this.selectedIds.push(id);
                if (this.selectedIds.length === {{ $produktivitasList->count() }}) {
                    this.selectAll = true;
                }
            }
        },
        searchAddKambing: '',
        searchEditKambing: '',
        openAddKambingDropdown: false,
        openEditKambingDropdown: false,
        kambingList: [
            @foreach($kambings as $k)
                { id: '{{ $k->id }}', kode: '{{ $k->kode_kambing }}', jenis: '{{ $k->jenis_kelamin }}' },
            @endforeach
        ],
        get filteredAddKambings() {
            if (!this.searchAddKambing) return this.kambingList.slice(0, 20);
            const query = this.searchAddKambing.toLowerCase();
            return this.kambingList.filter(k => 
                k.kode.toLowerCase().includes(query) || 
                k.jenis.toLowerCase().includes(query)
            ).slice(0, 20);
        },
        get filteredEditKambings() {
            if (!this.searchEditKambing) return this.kambingList.slice(0, 20);
            const query = this.searchEditKambing.toLowerCase();
            return this.kambingList.filter(k => 
                k.kode.toLowerCase().includes(query) || 
                k.jenis.toLowerCase().includes(query)
            ).slice(0, 20);
        },
        getSelectedKambingText(id) {
            const k = this.kambingList.find(item => item.id == id);
            return k ? `${k.kode} (${k.jenis})` : 'Pilih Kambing';
        }
    }"
    class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base font-bold text-slate-800">Data Log Produktivitas Kambing</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Catat bobot badan (bulanan), tingkat kelahiran, dan produksi susu (harian) kambing secara periodik.</p>
        </div>

        <button @click="openAddModal = true; selectedKambingId = '{{ $kambings->first()->id ?? '' }}'; searchAddKambing = ''; openAddKambingDropdown = false;" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-xs shadow-sm transition flex items-center space-x-2 w-full sm:w-auto justify-center">
            <span><i class="fa-solid fa-plus"></i></span>
            <span>Tambah Log Baru</span>
        </button>
    </div>

    <!-- Search, Sort & Bulk Action Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <!-- Search and Date Range Form -->
        <form action="{{ route('produktivitas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <!-- Search -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Cari Kambing</label>
                <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary">
                    <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Kode kambing..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
                </div>
            </div>

            <!-- Start Date -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
            </div>

            <!-- End Date -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-grow py-2 px-4 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Filter Data
                </button>
                @if($search || $kambingId || $startDate || $endDate)
                <a href="{{ route('produktivitas.index', ['per_page' => $perPage]) }}" class="py-2 px-4 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold transition flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>

            <!-- Hidden Inputs for Sorting & Pagination -->
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
        </form>

        <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Dynamic Bulk Action Bar (AlpineJS) -->
            <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center space-x-3 w-full sm:w-auto bg-rose-50 border border-rose-100 px-4 py-2 rounded-xl" x-transition>
                <span class="text-xs font-bold text-rose-700"><span x-text="selectedIds.length"></span> log dipilih</span>
                <form id="bulk-delete-form" action="{{ route('produktivitas.destroy-bulk') }}" method="POST" class="inline">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="button" @click="openBulkDeleteModal = true" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                        Hapus Terpilih
                    </button>
                </form>
            </div>

            <!-- Pagination size selector -->
            <div x-show="selectedIds.length === 0" class="flex items-center justify-between w-full text-xs text-slate-400 font-medium">
                <div>
                    Menampilkan <span class="text-slate-700 font-semibold">{{ $produktivitasList->firstItem() ?? 0 }}</span> - <span class="text-slate-700 font-semibold">{{ $produktivitasList->lastItem() ?? 0 }}</span> dari <span class="text-slate-700 font-semibold">{{ $produktivitasList->total() }}</span> record data
                </div>

                <div class="flex items-center space-x-1">
                    <span>Baris:</span>
                    <select onchange="window.location.href=this.value" class="bg-transparent border-none focus:ring-0 text-slate-700 font-semibold cursor-pointer">
                        <option value="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 10]) }}" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 25]) }}" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 50]) }}" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 100]) }}" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4 text-center w-12">
                            <input type="checkbox" :checked="selectAll" @click="toggleSelectAll" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary cursor-pointer">
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => 'kode_kambing', 'sort_dir' => $sortBy == 'kode_kambing' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Kode Kambing</span>
                                <span>{!! $sortBy == 'kode_kambing' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => 'tanggal_pencatatan', 'sort_dir' => $sortBy == 'tanggal_pencatatan' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Tanggal Pencatatan</span>
                                <span>{!! $sortBy == 'tanggal_pencatatan' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => 'bobot_badan', 'sort_dir' => $sortBy == 'bobot_badan' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Bobot Badan</span>
                                <span>{!! $sortBy == 'bobot_badan' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => 'tingkat_kelahiran', 'sort_dir' => $sortBy == 'tingkat_kelahiran' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Tingkat Kelahiran</span>
                                <span>{!! $sortBy == 'tingkat_kelahiran' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('produktivitas.index', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'sort_by' => 'produksi_susu', 'sort_dir' => $sortBy == 'produksi_susu' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Produksi Susu</span>
                                <span>{!! $sortBy == 'produksi_susu' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($produktivitasList as $log)
                    <tr class="hover:bg-slate-50/40 transition-colors" :class="selectedIds.includes('{{ $log->id }}') ? 'bg-orange-50/10' : ''">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" :checked="selectedIds.includes('{{ $log->id }}')" @click="toggleSelect('{{ $log->id }}')" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary cursor-pointer">
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $log->kambing->kode_kambing }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($log->tanggal_pencatatan)->translatedFormat('d M Y') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-700">{{ $log->bobot_badan }} kg</td>
                        <td class="px-6 py-4">
                            @if($log->kambing->jenis_kelamin === 'Jantan')
                            <span class="text-slate-300 text-xxs font-normal italic">N/A (Jantan)</span>
                            @else
                            <span class="font-bold">{{ $log->tingkat_kelahiran }} ekor</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($log->kambing->jenis_kelamin === 'Jantan')
                            <span class="text-slate-300 text-xxs font-normal italic">N/A (Jantan)</span>
                            @else
                            <span class="font-bold text-primary">{{ $log->produksi_susu }} L</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
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
                                        searchEditKambing = '';
                                        openEditKambingDropdown = false;
                                        openEditModal = true;
                                    "
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-orange-50 hover:bg-orange-100 text-primary hover:text-primary-hover transition"
                                    title="Edit Log">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                <button type="button"
                                    @click="deleteActionUrl = '{{ route('produktivitas.destroy', $log->id) }}'; openDeleteModal = true;"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                    title="Hapus Log">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-scale-balanced"></i></div>
                            <div class="text-xs font-semibold">Data log produktivitas tidak ditemukan</div>
                            <div class="text-xxs font-medium text-slate-400">Silakan pilih filter lain atau masukkan pencatatan baru.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination links -->
        @if($produktivitasList->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $produktivitasList->links('partials.pagination') }}
        </div>
        @endif
    </div>

    <!-- MODAL: ADD PRODUCTIVITAS -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-3xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Tambah Pencatatan Produktivitas</h3>
                    </div>
                    <button @click="openAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('produktivitas.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pilih Kambing</label>
                        <div class="relative">
                            <!-- Hidden input for standard form submission -->
                            <input type="hidden" name="kambing_id" :value="selectedKambingId" required>
                            
                            <!-- Dropdown Button Trigger -->
                            <button type="button" @click="openAddKambingDropdown = !openAddKambingDropdown" 
                                class="w-full flex items-center justify-between px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                                <span x-text="getSelectedKambingText(selectedKambingId)" class="text-slate-700"></span>
                                <span class="text-slate-400"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="openAddKambingDropdown" @click.outside="openAddKambingDropdown = false" 
                                class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden" 
                                x-transition style="display: none;">
                                <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                                    <div class="flex items-center bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary">
                                        <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <input type="text" x-model="searchAddKambing" placeholder="Cari kode kambing..." 
                                            class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0 p-0">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <template x-for="k in filteredAddKambings" :key="k.id">
                                        <li>
                                            <button type="button" @click="selectedKambingId = k.id; openAddKambingDropdown = false; searchAddKambing = ''" 
                                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between text-xs text-slate-700 transition-colors"
                                                :class="selectedKambingId == k.id ? 'bg-orange-50/50 text-primary font-semibold' : ''">
                                                <span x-text="`${k.kode} (${k.jenis})`"></span>
                                                <span x-show="selectedKambingId == k.id" class="text-primary text-[10px]"><i class="fa-solid fa-check"></i></span>
                                            </button>
                                        </li>
                                    </template>
                                    <div x-show="filteredAddKambings.length === 0" class="px-4 py-3 text-center text-slate-400 text-xs">
                                        Kambing tidak ditemukan
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Catat</label>
                            <input type="date" name="tanggal_pencatatan" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bobot Badan (kg)</label>
                            <input type="number" step="0.01" name="bobot_badan" placeholder="Kosongkan jika tidak ditimbang" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                    </div>

                    <!-- Gender specific inputs (AlpineJS interactive disables) -->
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tingkat Kelahiran</label>
                            <input type="number" name="tingkat_kelahiran" placeholder="Kosongkan jika tidak melahirkan" :disabled="isJantan(selectedKambingId)" :class="isJantan(selectedKambingId) ? 'bg-slate-150 text-slate-400 border-slate-200 cursor-not-allowed' : ''" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <span x-show="isJantan(selectedKambingId)" class="text-[9px] text-slate-400 mt-1 block italic">Tidak berlaku untuk Jantan</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Produksi Susu (Liter)</label>
                            <input type="number" step="0.01" name="produksi_susu" placeholder="Kosongkan jika tidak diperah" :disabled="isJantan(selectedKambingId)" :class="isJantan(selectedKambingId) ? 'bg-slate-150 text-slate-400 border-slate-200 cursor-not-allowed' : ''" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <span x-show="isJantan(selectedKambingId)" class="text-[9px] text-slate-400 mt-1 block italic">Tidak berlaku untuk Jantan</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT PRODUCTIVITAS -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-3xl">
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
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pilih Kambing</label>
                        <div class="relative">
                            <!-- Hidden input for standard form submission -->
                            <input type="hidden" name="kambing_id" :value="editKambingId" required>
                            
                            <!-- Dropdown Button Trigger -->
                            <button type="button" @click="openEditKambingDropdown = !openEditKambingDropdown" 
                                class="w-full flex items-center justify-between px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                                <span x-text="getSelectedKambingText(editKambingId)" class="text-slate-700"></span>
                                <span class="text-slate-400"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="openEditKambingDropdown" @click.outside="openEditKambingDropdown = false" 
                                class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden" 
                                x-transition style="display: none;">
                                <div class="p-2 border-b border-slate-100 bg-slate-50/50">
                                    <div class="flex items-center bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary">
                                        <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <input type="text" x-model="searchEditKambing" placeholder="Cari kode kambing..." 
                                            class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0 p-0">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <template x-for="k in filteredEditKambings" :key="k.id">
                                        <li>
                                            <button type="button" @click="editKambingId = k.id; openEditKambingDropdown = false; searchEditKambing = ''" 
                                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between text-xs text-slate-700 transition-colors"
                                                :class="editKambingId == k.id ? 'bg-orange-50/50 text-primary font-semibold' : ''">
                                                <span x-text="`${k.kode} (${k.jenis})`"></span>
                                                <span x-show="editKambingId == k.id" class="text-primary text-[10px]"><i class="fa-solid fa-check"></i></span>
                                            </button>
                                        </li>
                                    </template>
                                    <div x-show="filteredEditKambings.length === 0" class="px-4 py-3 text-center text-slate-400 text-xs">
                                        Kambing tidak ditemukan
                                    </div>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Catat</label>
                            <input type="date" name="tanggal_pencatatan" x-model="editLog.tanggal_pencatatan" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Bobot Badan (kg)</label>
                            <input type="number" step="0.01" name="bobot_badan" x-model="editLog.bobot_badan" placeholder="Kosongkan jika tidak ditimbang" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                    </div>

                    <!-- Gender specific inputs (AlpineJS interactive disables) -->
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tingkat Kelahiran</label>
                            <input type="number" name="tingkat_kelahiran" x-model="editLog.tingkat_kelahiran" placeholder="Kosongkan jika tidak melahirkan" :disabled="isJantan(editKambingId)" :class="isJantan(editKambingId) ? 'bg-slate-150 text-slate-400 border-slate-200 cursor-not-allowed' : ''" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <span x-show="isJantan(editKambingId)" class="text-[9px] text-slate-400 mt-1 block italic">Tidak berlaku untuk Jantan</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Produksi Susu (Liter)</label>
                            <input type="number" step="0.01" name="produksi_susu" x-model="editLog.produksi_susu" placeholder="Kosongkan jika tidak diperah" :disabled="isJantan(editKambingId)" :class="isJantan(editKambingId) ? 'bg-slate-150 text-slate-400 border-slate-200 cursor-not-allowed' : ''" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <span x-show="isJantan(editKambingId)" class="text-[9px] text-slate-400 mt-1 block italic">Tidak berlaku untuk Jantan</span>
                        </div>
                    </div>

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
                    <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition">Hapus Data</button>
                </form>
            </div>
        </div>
    </div>

    <!-- BULK DELETE CONFIRMATION MODAL -->
    <div x-show="openBulkDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openBulkDeleteModal = false"></div>

            <div class="relative z-10 w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform p-6">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">Hapus Terpilih?</h3>
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-700" x-text="selectedIds.length"></span> record data produktivitas terpilih? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <div class="flex justify-center space-x-3 pt-6 mt-2 border-t border-slate-100">
                    <button type="button" @click="openBulkDeleteModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">Batal</button>
                    <button type="button" @click="document.getElementById('bulk-delete-form').submit()" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition">Hapus Semua</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection