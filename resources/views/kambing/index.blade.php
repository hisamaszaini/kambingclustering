@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openImportModal: false,
        openDeleteModal: false,
        deleteActionUrl: '',
        openBulkDeleteModal: false,
        editKambing: {},
        selectedIds: [],
        selectAll: false,
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedIds = [@foreach($kambings as $kambing)'{{ $kambing->id }}',@endforeach];
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
                if (this.selectedIds.length === {{ $kambings->count() }}) {
                    this.selectAll = true;
                }
            }
        }
    }"
    class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base font-bold text-slate-800">Data Master Kambing</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Kelola seluruh data profil kambing yang terdaftar di peternakan.</p>
        </div>

        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <!-- Import Button -->
            <button @click="openImportModal = true" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition flex items-center space-x-2">
                <span><i class="fa-solid fa-file-import"></i></span>
                <span>Impor Excel</span>
            </button>

            <!-- Create Button -->
            <button @click="openAddModal = true" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-xs shadow-sm transition flex items-center space-x-2">
                <span><i class="fa-solid fa-plus"></i></span>
                <span>Tambah Kambing</span>
            </button>
        </div>
    </div>

    <!-- Search, Sort & Bulk Action Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Form -->
        <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
            <form action="{{ route('kambing.index') }}" method="GET" class="w-full sm:w-80 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary">
                <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode kambing..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
                <input type="hidden" name="jenis_kelamin" value="{{ $jenisKelamin }}">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                @if($search)
                <a href="{{ route('kambing.index', ['jenis_kelamin' => $jenisKelamin, 'per_page' => $perPage]) }}" class="text-xxs text-slate-400 hover:text-slate-600 font-semibold px-2 py-0.5 bg-slate-200 rounded-md">Reset</a>
                @endif
            </form>

            <!-- Filter Gender -->
            <div class="flex items-center space-x-2">
                <div class="flex bg-slate-100 p-0.5 rounded-xl border border-slate-200 text-xs font-semibold">
                    <a href="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => '', 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => $perPage]) }}"
                        class="px-3 py-1.5 rounded-lg {{ $jenisKelamin == '' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Semua</a>
                    <a href="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => 'Jantan', 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => $perPage]) }}"
                        class="px-3 py-1.5 rounded-lg {{ $jenisKelamin == 'Jantan' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Jantan</a>
                    <a href="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => 'Betina', 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => $perPage]) }}"
                        class="px-3 py-1.5 rounded-lg {{ $jenisKelamin == 'Betina' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Betina</a>
                </div>
            </div>
        </div>

        <!-- Dynamic Bulk Action Bar (AlpineJS) -->
        <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center space-x-3 w-full md:w-auto bg-rose-50 border border-rose-100 px-4 py-2 rounded-xl" x-transition>
            <span class="text-xxs font-bold text-rose-700"><span x-text="selectedIds.length"></span> kambing dipilih</span>
            <form id="bulk-delete-form" action="{{ route('kambing.destroy-bulk') }}" method="POST" class="inline">
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
        <div x-show="selectedIds.length === 0" class="flex items-center space-x-3 text-xs text-slate-400 font-medium">
            <div>
                Menampilkan <span class="text-slate-700 font-semibold">{{ $kambings->firstItem() ?? 0 }}</span> - <span class="text-slate-700 font-semibold">{{ $kambings->lastItem() ?? 0 }}</span> dari <span class="text-slate-700 font-semibold">{{ $kambings->total() }}</span> kambing
            </div>
            <span>•</span>
            <div class="flex items-center space-x-1">
                <span>Baris:</span>
                <select onchange="window.location.href=this.value" class="bg-transparent border-none focus:ring-0 text-slate-700 font-semibold cursor-pointer">
                    <option value="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 10]) }}" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 25]) }}" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 50]) }}" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => $sortBy, 'sort_dir' => $sortDir, 'per_page' => 100]) }}" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
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
                            <a href="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => 'kode_kambing', 'sort_dir' => $sortBy == 'kode_kambing' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Kode Kambing</span>
                                <span>{!! $sortBy == 'kode_kambing' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('kambing.index', ['search' => $search, 'jenis_kelamin' => $jenisKelamin, 'sort_by' => 'jenis_kelamin', 'sort_dir' => $sortBy == 'jenis_kelamin' && $sortDir == 'asc' ? 'desc' : 'asc', 'per_page' => $perPage]) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Jenis Kelamin</span>
                                <span>{!! $sortBy == 'jenis_kelamin' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($kambings as $kambing)
                    <tr class="hover:bg-slate-50/40 transition-colors" :class="selectedIds.includes('{{ $kambing->id }}') ? 'bg-orange-50/10' : ''">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" :checked="selectedIds.includes('{{ $kambing->id }}')" @click="toggleSelect('{{ $kambing->id }}')" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary cursor-pointer">
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $kambing->kode_kambing }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xxs font-semibold {{ $kambing->jenis_kelamin == 'Jantan' ? 'bg-sky-50 text-sky-600' : 'bg-pink-50 text-pink-600' }}">
                                <i class="fa-solid {{ $kambing->jenis_kelamin == 'Jantan' ? 'fa-mars mr-1' : 'fa-venus mr-1' }}"></i>{{ $kambing->jenis_kelamin }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('kambing.show', $kambing->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 transition"
                                    title="Detail Perkembangan Kambing">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>

                                <button @click="
                                        editKambing = {
                                            id: '{{ $kambing->id }}',
                                            kode_kambing: '{{ $kambing->kode_kambing }}',
                                            jenis_kelamin: '{{ $kambing->jenis_kelamin }}'
                                        };
                                        openEditModal = true;
                                    "
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-orange-50 hover:bg-orange-100 text-primary hover:text-primary-hover transition"
                                    title="Edit Kambing">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                <button type="button"
                                    @click="deleteActionUrl = '{{ route('kambing.destroy', $kambing->id) }}'; openDeleteModal = true;"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                    title="Hapus Kambing">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <div class="text-xs font-semibold">Data kambing tidak ditemukan</div>
                            <div class="text-xxs font-medium text-slate-400">Silakan masukkan data atau impor dari file Excel.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination links -->
        @if($kambings->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $kambings->links('partials.pagination') }}
        </div>
        @endif
    </div>

    <!-- MODAL: ADD KAMBING -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Tambah Kambing Baru</h3>
                    </div>
                    <button @click="openAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('kambing.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kode Kambing</label>
                        <input type="text" name="kode_kambing" required placeholder="Contoh: K001" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <option value="Betina">Betina</option>
                            <option value="Jantan">Jantan</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT KAMBING -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Ubah Data Kambing</h3>
                    </div>
                    <button @click="openEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form :action="`{{ url('kambing') }}/${editKambing.id}`" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kode Kambing</label>
                        <input type="text" name="kode_kambing" x-model="editKambing.kode_kambing" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" x-model="editKambing.jenis_kelamin" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            <option value="Betina">Betina</option>
                            <option value="Jantan">Jantan</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: IMPORT EXCEL -->
    <div x-show="openImportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openImportModal = false"></div>

            <div class="relative z-10 w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-excel text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Impor Data Kambing Massal</h3>
                    </div>
                    <button @click="openImportModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('kambing.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="excel_file" required accept=".xlsx, .xls, .csv" class="w-full text-xs text-slate-600 border border-slate-200 rounded-xl p-2 bg-slate-50 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xxs file:font-semibold file:bg-orange-50 file:text-primary file:cursor-pointer">
                    </div>

                    <!-- Guidelines -->
                    <div class="bg-slate-50 p-3.5 rounded-xl space-y-2">
                        <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-primary"></i> Ketentuan Format Kolom Excel:</span>
                        <ul class="text-xxs text-slate-400 font-medium list-disc list-inside space-y-1">
                            <li>Format kolom harus urut: <code class="text-primary font-semibold bg-orange-50 px-1 rounded">No, Kode Kambing, Jenis Kelamin, Bobot Badan (kg), Tingkat Kelahiran, Produksi Susu (Liter)</code>.</li>
                            <li>Tingkat Kelahiran dan Produksi Susu peJantan akan diatur ke <code class="text-slate-700">0</code> secara otomatis.</li>
                            <li>Data numerik yang berkoma (misal: <code class="text-slate-700">45,2</code>) akan diparsing menjadi format desimal yang benar.</li>
                        </ul>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openImportModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center space-x-2">
                            <span><i class="fa-solid fa-cloud-arrow-up"></i></span>
                            <span>Impor Sekarang</span>
                        </button>
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
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin menghapus data kambing ini beserta seluruh data log produktivitasnya? Tindakan ini tidak dapat dibatalkan.</p>
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
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-700" x-text="selectedIds.length"></span> kambing terpilih beserta seluruh data produktivitas mereka? Tindakan ini tidak dapat dibatalkan.</p>
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