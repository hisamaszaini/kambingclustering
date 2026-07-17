<?php

namespace App\Http\Controllers;

use App\Models\Kambing;
use App\Models\DataProduktivitas;
use App\Models\SesiClustering;
use App\Models\HasilClustering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan Halaman Dashboard Utama.
     */
    public function index()
    {
        // 1. Stat cards
        $totalKambing = Kambing::count();
        $totalJantan = Kambing::where('jenis_kelamin', 'Jantan')->count();
        $totalBetina = Kambing::where('jenis_kelamin', 'Betina')->count();
        $totalProduktivitas = DataProduktivitas::count();

        // 2. Sesi clustering terakhir
        $latestSesi = SesiClustering::with('user')->orderBy('id', 'desc')->first();
        
        $clusterCounts = [
            'Rendah' => 0,
            'Sedang' => 0,
            'Tinggi' => 0,
        ];

        if ($latestSesi) {
            $clusterCounts = [
                'Rendah' => HasilClustering::where('sesi_id', $latestSesi->id)->where('cluster', 'Rendah')->count(),
                'Sedang' => HasilClustering::where('sesi_id', $latestSesi->id)->where('cluster', 'Sedang')->count(),
                'Tinggi' => HasilClustering::where('sesi_id', $latestSesi->id)->where('cluster', 'Tinggi')->count(),
            ];
        }

        // 3. Tren Bobot & Susu Bulanan (SQLite vs MySQL format compatibility)
        $isSqlite = (config('database.default') === 'sqlite');
        $monthFormat = $isSqlite 
            ? "strftime('%m-%Y', tanggal_pencatatan)" 
            : "DATE_FORMAT(tanggal_pencatatan, '%m-%Y')";

        $monthlyData = DataProduktivitas::select(
            DB::raw("$monthFormat as label"),
            DB::raw("AVG(bobot_badan) as avg_bobot"),
            DB::raw("AVG(produksi_susu) as avg_susu")
        )
        ->groupBy('label')
        ->orderBy(DB::raw("MIN(tanggal_pencatatan)"), 'asc')
        ->get();

        $chartLabels = $monthlyData->pluck('label')->toArray();
        $chartBobot = $monthlyData->pluck('avg_bobot')->map(fn($val) => round($val, 2))->toArray();
        $chartSusu = $monthlyData->pluck('avg_susu')->map(fn($val) => round($val, 2))->toArray();

        // 4. Hitung notifikasi aktif untuk kambing (real-time peringatan)
        $kambings = Kambing::with(['produktivitas'])->get();
        $kambingsWithAlerts = [];
        
        foreach ($kambings as $kambing) {
            $alerts = $kambing->getNotifikasi();
            if (!empty($alerts)) {
                $kambingsWithAlerts[] = [
                    'kambing' => $kambing,
                    'alerts' => $alerts
                ];
            }
        }

        return view('dashboard', compact(
            'totalKambing',
            'totalJantan',
            'totalBetina',
            'totalProduktivitas',
            'latestSesi',
            'clusterCounts',
            'chartLabels',
            'chartBobot',
            'chartSusu',
            'kambingsWithAlerts'
        ));
    }
}
