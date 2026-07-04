<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Pengecekan otorisasi admin untuk menjamin keamanan rute.
     */
    protected function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak. Halaman ini hanya dapat diakses oleh Administrator.');
        }
    }

    /**
     * Tampilkan daftar pengguna dengan pencarian, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        
        $allowedSortColumns = ['id', 'name', 'username', 'role', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }
        
        $allowedSortDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedSortDirs)) {
            $sortDir = 'desc';
        }

        $query = User::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();
        
        return view('user.index', compact('users', 'search', 'sortBy', 'sortDir'));
    }

    /**
     * Simpan data pengguna baru.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:tbl_users,username',
            'email' => 'nullable|string|email|max:255|unique:tbl_users,email',
            'role' => 'required|in:admin,user',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar di sistem.',
            'role.required' => 'Peran akun wajib dipilih.',
            'role.in' => 'Peran akun tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => trim($request->username),
            'email' => $request->email ? trim($request->email) : null,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.index')->with('success', 'Akun pengguna baru berhasil ditambahkan!');
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:tbl_users,username,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:tbl_users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
            'role.required' => 'Peran akun wajib dipilih.',
            'role.in' => 'Peran akun tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal harus 6 karakter.',
        ]);

        $updateData = [
            'name' => $request->name,
            'username' => trim($request->username),
            'email' => $request->email ? trim($request->email) : null,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('user.index')->with('success', 'Data akun pengguna berhasil diperbarui!');
    }

    /**
     * Hapus data pengguna.
     */
    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        // Mencegah admin menghapus dirinya sendiri yang sedang login
        if ($user->id === Auth::user()->id) {
            return redirect()->route('user.index')->with('error', 'Aksi ditolak! Anda tidak diperkenankan menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil dihapus!');
    }
}
