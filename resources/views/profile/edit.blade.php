@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-base font-bold text-slate-800">Pengaturan Akun</h1>
        <p class="text-slate-400 text-xs font-medium mt-0.5">Perbarui informasi profil pribadi dan ubah kata sandi akun Anda secara berkala.</p>
    </div>

    <!-- Error/Validation Banners -->
    @if($errors->any())
    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl text-rose-800 text-xs font-semibold shadow-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card 1: Basic Information -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Informasi Profil Mandiri</h3>
                <p class="text-xxs text-slate-400 mt-0.5">Ubah nama lengkap, username login, dan email terdaftar.</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username Login</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email (Opsional)</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Belum ditautkan" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="px-4 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-xl font-semibold text-xs shadow-sm transition">
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Card 2: Update Password -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Ubah Kata Sandi</h3>
                <p class="text-xxs text-slate-400 mt-0.5">Jamin keamanan akun Anda dengan memperbarui kata sandi secara berkala.</p>
            </div>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                </div>

                <!-- New Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Sandi Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi sandi baru" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection