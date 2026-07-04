<?php

namespace App\Http\Controllers;

use App\Models\Kambing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KambingController extends Controller
{
    /**
     * Tampilkan data kambing dengan pencarian, sorting, filter, dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenisKelamin = $request->input('jenis_kelamin');
        
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        // Security check for sorting columns
        $allowedSortColumns = ['id', 'kode_kambing', 'jenis_kelamin'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }

        $allowedSortDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedSortDirs)) {
            $sortDir = 'desc';
        }

        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $query = Kambing::query();

        if ($search) {
            $query->where('kode_kambing', 'like', "%{$search}%");
        }

        if ($jenisKelamin && in_array($jenisKelamin, ['Jantan', 'Betina'])) {
            $query->where('jenis_kelamin', $jenisKelamin);
        }

        $kambings = $query->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('kambing.index', compact('kambings', 'search', 'jenisKelamin', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Simpan data kambing baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_kambing' => 'required|string|max:50|unique:tbl_kambing,kode_kambing',
            'jenis_kelamin' => 'required|in:Jantan,Betina',
        ]);

        Kambing::create([
            'kode_kambing' => trim($request->kode_kambing),
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->route('kambing.index')->with('success', 'Data kambing berhasil ditambahkan!');
    }

    /**
     * Update data kambing.
     */
    public function update(Request $request, Kambing $kambing)
    {
        $request->validate([
            'kode_kambing' => 'required|string|max:50|unique:tbl_kambing,kode_kambing,' . $kambing->id,
            'jenis_kelamin' => 'required|in:Jantan,Betina',
        ]);

        $kambing->update([
            'kode_kambing' => trim($request->kode_kambing),
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->route('kambing.index')->with('success', 'Data kambing berhasil diupdate!');
    }

    /**
     * Hapus satu data kambing.
     */
    public function destroy(Kambing $kambing)
    {
        $kambing->delete();
        return redirect()->route('kambing.index')->with('success', 'Data kambing berhasil dihapus!');
    }

    /**
     * Hapus massal (Bulk Delete).
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->route('kambing.index')->with('error', 'Tidak ada data kambing yang dipilih untuk dihapus.');
        }

        Kambing::whereIn('id', $ids)->delete();

        return redirect()->route('kambing.index')->with('success', count($ids) . ' data kambing berhasil dihapus secara massal!');
    }

    /**
     * Import data dari Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new \App\Imports\KambingImport();
            Excel::import($import, $request->file('excel_file'));

            if ($import->importedCount > 0) {
                return redirect()->route('kambing.index')->with('success', "Sukses mengimpor {$import->importedCount} data kambing beserta data produktivitas awal!");
            } else {
                return redirect()->route('kambing.index')->with('error', 'Tidak ada data valid yang diimpor. Pastikan format kolom Excel Anda sudah benar.');
            }
        } catch (\Exception $e) {
            return redirect()->route('kambing.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
