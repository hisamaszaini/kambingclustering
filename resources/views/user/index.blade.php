@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openDeleteModal: false,
        deleteActionUrl: '',
        editUser: {}
    }"
    class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-base font-bold text-slate-800">Kelola Pengguna</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Kelola hak akses sistem, tambah administrator, dan akun petugas pencatat.</p>
        </div>

        <button @click="openAddModal = true" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-xs shadow-sm transition flex items-center space-x-2 w-full sm:w-auto justify-center">
            <span><i class="fa-solid fa-plus"></i></span>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Search / Filter Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Form -->
        <form action="{{ route('user.index') }}" method="GET" class="w-full md:w-96 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-primary/10 focus-within:border-primary">
            <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau username..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            @if($search)
            <a href="{{ route('user.index') }}" class="text-xxs text-slate-400 hover:text-slate-600 font-semibold px-2 py-0.5 bg-slate-200 rounded-md">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-400 font-medium">
            Menampilkan <span class="text-slate-700 font-semibold">{{ $users->firstItem() ?? 0 }}</span> - <span class="text-slate-700 font-semibold">{{ $users->lastItem() ?? 0 }}</span> dari <span class="text-slate-700 font-semibold">{{ $users->total() }}</span> pengguna
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Nama Lengkap</span>
                                <span>{!! $sortBy == 'name' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'username', 'sort_dir' => $sortBy == 'username' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Username</span>
                                <span>{!! $sortBy == 'username' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $u->name }}</td>
                        <td class="px-6 py-4 font-mono text-slate-600">{{ $u->username }}</td>
                        <td class="px-6 py-4 text-slate-400 font-normal">{{ $u->email ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xxs font-semibold {{ $u->role == 'admin' ? 'bg-primary-light text-primary' : 'bg-slate-100 text-slate-600' }}">
                                {{ $u->role == 'admin' ? 'Admin' : 'Petugas' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <button @click="
                                        editUser = {
                                            id: '{{ $u->id }}',
                                            name: '{{ addslashes($u->name) }}',
                                            username: '{{ $u->username }}',
                                            email: '{{ $u->email }}',
                                            role: '{{ $u->role }}'
                                        };
                                        openEditModal = true;
                                    "
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-orange-50 hover:bg-orange-100 text-primary hover:text-primary-hover transition"
                                    title="Edit User">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                @if(Auth::id() !== $u->id)
                                <button type="button"
                                    @click="deleteActionUrl = '{{ route('user.destroy', $u->id) }}'; openDeleteModal = true;"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                    title="Hapus User">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                                @else
                                <div class="w-8 h-8"></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-users-gear"></i></div>
                            <div class="text-xs font-semibold">Data pengguna tidak ditemukan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links('partials.pagination') }}
        </div>
        @endif
    </div>

    <!-- MODAL: ADD USER -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Tambah Pengguna Baru</h3>
                    </div>
                    <button @click="openAddModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('user.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Username</label>
                            <input type="text" name="username" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Role</label>
                            <select name="role" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                                <option value="user">Petugas (User)</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Email (Opsional)</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kata Sandi</label>
                            <input type="password" name="password" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Konfirmasi Sandi</label>
                            <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-500 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>

            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-left transition-all transform">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Ubah Data Pengguna</h3>
                    </div>
                    <button @click="openEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form :action="`{{ url('user') }}/${editUser.id}`" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Username</label>
                            <input type="text" name="username" x-model="editUser.username" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Role</label>
                            <select name="role" x-model="editUser.role" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-primary/10 focus:border-primary">
                                <option value="user">Petugas (User)</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Email (Opsional)</label>
                        <input type="email" name="email" x-model="editUser.email" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2">
                        <span class="block text-[10px] font-bold text-slate-500 uppercase">Ubah Kata Sandi (Opsional)</span>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase mb-0.5">Sandi Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase mb-0.5">Konfirmasi</label>
                                <input type="password" name="password_confirmation" placeholder="Kosongkan jika tetap" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-medium bg-white outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                            </div>
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
                        <h3 class="text-sm font-bold text-slate-800 mb-2">Hapus Akun Pengguna?</h3>
                        <p class="text-xs text-slate-500 font-medium px-2">Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen dari sistem? Hak akses login pengguna akan dicabut seketika.</p>
                    </div>
                </div>

                <form :action="deleteActionUrl" method="POST" class="flex justify-center space-x-3 pt-6 mt-2 border-t border-slate-100">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-sm transition">Hapus Pengguna</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection