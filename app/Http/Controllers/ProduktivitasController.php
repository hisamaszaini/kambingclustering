<?php

namespace App\Http\Controllers;

use App\Models\Kambing;
use App\Models\DataProduktivitas;
use Illuminate\Http\Request;

class ProduktivitasController extends Controller
{
    /**
     * Tampilkan data produktivitas dengan pencarian, sorting, filter, dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kambingId = $request->input('kambing_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sortBy = $request->input('sort_by', 'tanggal_pencatatan');
        $sortDir = $request->input('sort_dir', 'desc');
        $perPage = $request->input('per_page', 10);

        // Security check for sorting columns
        $allowedSortColumns = ['id', 'tanggal_pencatatan', 'bobot_badan', 'tingkat_kelahiran', 'produksi_susu', 'kode_kambing'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'tanggal_pencatatan';
        }

        $allowedSortDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedSortDirs)) {
            $sortDir = 'desc';
        }

        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Gunakan join jika sort berdasarkan kode_kambing
        $query = DataProduktivitas::with('kambing')
            ->select('tbl_data_produktivitas.*')
            ->join('tbl_kambing', 'tbl_data_produktivitas.kambing_id', '=', 'tbl_kambing.id');

        if ($search) {
            $query->where('tbl_kambing.kode_kambing', 'like', "%{$search}%");
        }

        if ($kambingId) {
            $query->where('tbl_data_produktivitas.kambing_id', $kambingId);
        }

        if ($startDate) {
            $query->whereDate('tbl_data_produktivitas.tanggal_pencatatan', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tbl_data_produktivitas.tanggal_pencatatan', '<=', $endDate);
        }

        // Apply sorting
        if ($sortBy === 'kode_kambing') {
            $query->orderBy('tbl_kambing.kode_kambing', $sortDir);
        } else {
            $query->orderBy('tbl_data_produktivitas.' . $sortBy, $sortDir);
        }

        $produktivitasList = $query->paginate($perPage)->withQueryString();
        $kambings = Kambing::orderBy('kode_kambing', 'asc')->get();

        return view('produktivitas.index', compact('produktivitasList', 'kambings', 'search', 'kambingId', 'startDate', 'endDate', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Simpan data produktivitas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kambing_id' => 'required|exists:tbl_kambing,id',
            'tanggal_pencatatan' => 'required|date',
            'bobot_badan' => 'required_without_all:tingkat_kelahiran,produksi_susu|nullable|numeric|min:0|max:999.99',
            'tingkat_kelahiran' => 'required_without_all:bobot_badan,produksi_susu|nullable|integer|min:0|max:100',
            'produksi_susu' => 'required_without_all:bobot_badan,tingkat_kelahiran|nullable|numeric|min:0|max:999.99',
        ]);

        // Validasi tambahan: kambing jantan tidak boleh punya produksi susu atau tingkat kelahiran
        $kambing = Kambing::findOrFail($request->kambing_id);
        $tingkatKelahiran = $request->tingkat_kelahiran;
        $produksiSusu = $request->produksi_susu;

        if ($kambing->jenis_kelamin === 'Jantan') {
            $tingkatKelahiran = null;
            $produksiSusu = null;
        }

        DataProduktivitas::create([
            'kambing_id' => $request->kambing_id,
            'tanggal_pencatatan' => $request->tanggal_pencatatan,
            'bobot_badan' => $request->bobot_badan,
            'tingkat_kelahiran' => $tingkatKelahiran,
            'produksi_susu' => $produksiSusu,
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Data produktivitas berhasil ditambahkan!');
        }

        return redirect()->route('produktivitas.index')->with('success', 'Data produktivitas berhasil ditambahkan!');
    }

    /**
     * Update data produktivitas.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kambing_id' => 'required|exists:tbl_kambing,id',
            'tanggal_pencatatan' => 'required|date',
            'bobot_badan' => 'required_without_all:tingkat_kelahiran,produksi_susu|nullable|numeric|min:0|max:999.99',
            'tingkat_kelahiran' => 'required_without_all:bobot_badan,produksi_susu|nullable|integer|min:0|max:100',
            'produksi_susu' => 'required_without_all:bobot_badan,tingkat_kelahiran|nullable|numeric|min:0|max:999.99',
        ]);

        $record = DataProduktivitas::findOrFail($id);
        $kambing = Kambing::findOrFail($request->kambing_id);

        $tingkatKelahiran = $request->tingkat_kelahiran;
        $produksiSusu = $request->produksi_susu;

        if ($kambing->jenis_kelamin === 'Jantan') {
            $tingkatKelahiran = null;
            $produksiSusu = null;
        }

        $record->update([
            'kambing_id' => $request->kambing_id,
            'tanggal_pencatatan' => $request->tanggal_pencatatan,
            'bobot_badan' => $request->bobot_badan,
            'tingkat_kelahiran' => $tingkatKelahiran,
            'produksi_susu' => $produksiSusu,
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Data produktivitas berhasil diupdate!');
        }

        return redirect()->route('produktivitas.index')->with('success', 'Data produktivitas berhasil diupdate!');
    }

    /**
     * Hapus data produktivitas.
     */
    public function destroy($id)
    {
        $record = DataProduktivitas::findOrFail($id);
        $record->delete();

        if (request()->filled('redirect_to')) {
            return redirect(request('redirect_to'))->with('success', 'Data produktivitas berhasil dihapus!');
        }

        return redirect()->route('produktivitas.index')->with('success', 'Data produktivitas berhasil dihapus!');
    }

    /**
     * Hapus massal (Bulk Delete).
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->route('produktivitas.index')->with('error', 'Tidak ada data produktivitas yang dipilih untuk dihapus.');
        }

        DataProduktivitas::whereIn('id', $ids)->delete();

        return redirect()->route('produktivitas.index')->with('success', count($ids) . ' data produktivitas berhasil dihapus secara massal!');
    }
}
