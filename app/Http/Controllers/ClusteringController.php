<?php

namespace App\Http\Controllers;

use App\Models\Kambing;
use App\Models\SesiClustering;
use App\Models\HasilClustering;
use App\Services\KMeansService;
use App\Exports\HasilExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClusteringController extends Controller
{
    protected $kMeansService;

    public function __construct(KMeansService $kMeansService)
    {
        $this->kMeansService = $kMeansService;
    }

    /**
     * Tampilkan form untuk memulai proses K-Means.
     */
    public function prosesForm()
    {
        $run = false;
        $totalKambing = Kambing::count();
        $isClustered = SesiClustering::exists();
        
        $centroidAwal = [];
        if ($totalKambing > 0) {
            try {
                $centroidAwal = $this->kMeansService->getInitialCentroids();
            } catch (\Exception $e) {
                // handle gracefully
            }
        }

        return view('clustering.proses', compact('run', 'totalKambing', 'isClustered', 'centroidAwal'));
    }

    /**
     * Jalankan proses K-Means (POST) dan tampilkan log iterasi matematis lengkap.
     */
    public function proses(Request $request)
    {
        try {
            // Jalankan core service K-Means
            $output = $this->kMeansService->runClustering();
            
            $run = true;
            $sesiId = $output['sesi_id'];
            $totalIterasi = $output['total_iterasi'];
            $logIterasi = $output['log_iterasi'];
            $centroidAkhir = $output['centroid_akhir'];
            $centroidAwal = $output['centroid_awal'];
            $totalKambing = Kambing::count();

            return view('clustering.proses', compact('run', 'sesiId', 'totalIterasi', 'logIterasi', 'centroidAkhir', 'centroidAwal', 'totalKambing'))
                ->with('success', 'Algoritma K-Means berhasil dijalankan dan dikalkulasikan!');
        } catch (\Exception $e) {
            return redirect()->route('clustering.proses-form')->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan Dashboard Hasil Clustering Akhir.
     */
    public function hasil(Request $request)
    {
        $sesiList = SesiClustering::orderBy('id', 'desc')->get();
        
        // Pilih sesi (default: sesi terakhir)
        $sesiId = $request->input('sesi_id');
        if (empty($sesiId) && $sesiList->isNotEmpty()) {
            $sesiId = $sesiList->first()->id;
        }

        $selectedSesi = $sesiId ? SesiClustering::find($sesiId) : null;

        if (!$selectedSesi) {
            return view('clustering.hasil', [
                'sesiList' => $sesiList,
                'selectedSesi' => null,
                'hasils' => collect(),
                'clusterCounts' => ['Rendah' => 0, 'Sedang' => 0, 'Tinggi' => 0],
                'clusterAverages' => []
            ]);
        }

        // Ambil hasil untuk sesi terpilih beserta data kambingnya
        $hasilsQuery = HasilClustering::with('kambing')
            ->where('sesi_id', $selectedSesi->id)
            ->orderByRaw("FIELD(cluster, 'Tinggi', 'Sedang', 'Rendah') ASC")
            ->orderBy('produksi_susu_val', 'desc')
            ->orderBy('bobot_badan_val', 'desc')
            ->orderBy('tingkat_kelahiran_val', 'desc');
        
        // Filter by cluster
        $filterCluster = $request->input('cluster');
        if ($filterCluster && in_array($filterCluster, ['Rendah', 'Sedang', 'Tinggi'])) {
            $hasilsQuery->where('cluster', $filterCluster);
        }

        $hasils = $hasilsQuery->paginate(15)->withQueryString();

        // Hitung statistik ringkasan dari DB (untuk akurasi data)
        $allHasils = HasilClustering::where('sesi_id', $selectedSesi->id)->get();
        $clusterCounts = [
            'Rendah' => $allHasils->where('cluster', 'Rendah')->count(),
            'Sedang' => $allHasils->where('cluster', 'Sedang')->count(),
            'Tinggi' => $allHasils->where('cluster', 'Tinggi')->count(),
        ];

        // Hitung rata-rata kriteria per cluster untuk centroid akhir yang sesungguhnya dari data ter-cluster
        $clusterAverages = [
            'Rendah' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
            'Sedang' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
            'Tinggi' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
        ];

        foreach ($allHasils as $hasil) {
            $c = $hasil->cluster;
            $clusterAverages[$c]['count']++;
            $clusterAverages[$c]['C1'] += $hasil->bobot_badan_val;
            $clusterAverages[$c]['C2'] += $hasil->tingkat_kelahiran_val;
            $clusterAverages[$c]['C3'] += $hasil->produksi_susu_val;
        }

        foreach ($clusterAverages as $c => $data) {
            if ($data['count'] > 0) {
                $clusterAverages[$c]['C1'] = round($data['C1'] / $data['count'], 2);
                $clusterAverages[$c]['C2'] = round($data['C2'] / $data['count'], 2);
                $clusterAverages[$c]['C3'] = round($data['C3'] / $data['count'], 2);
            }
        }

        return view('clustering.hasil', compact('sesiList', 'selectedSesi', 'hasils', 'clusterCounts', 'clusterAverages', 'filterCluster'));
    }

    /**
     * Export hasil clustering ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $sesiId = $request->input('sesi_id');
        $sesi = SesiClustering::findOrFail($sesiId);

        return Excel::download(new HasilExport($sesi->id), 'Hasil_Clustering_Sesi_' . $sesi->id . '.xlsx');
    }

    /**
     * Tampilkan halaman cetak PDF hasil clustering.
     */
    public function exportPdf(Request $request)
    {
        $sesiId = $request->input('sesi_id');
        $sesi = SesiClustering::with('user')->findOrFail($sesiId);
        $hasils = HasilClustering::with('kambing')
            ->where('sesi_id', $sesi->id)
            ->orderByRaw("FIELD(cluster, 'Tinggi', 'Sedang', 'Rendah') ASC")
            ->orderBy('produksi_susu_val', 'desc')
            ->orderBy('bobot_badan_val', 'desc')
            ->orderBy('tingkat_kelahiran_val', 'desc')
            ->get();

        return view('clustering.pdf', compact('sesi', 'hasils'));
    }
}
