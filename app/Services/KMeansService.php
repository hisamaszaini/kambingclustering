<?php

namespace App\Services;

use App\Models\Kambing;
use App\Models\SesiClustering;
use App\Models\HasilClustering;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class KMeansService
{
    /**
     * Jalankan proses clustering K-Means.
     *
     * @param int $maxIterations Batas maksimal iterasi.
     * @return array Log lengkap iterasi untuk kebutuhan visualisasi.
     */
    public function runClustering(int $maxIterations = 100): array
    {
        // 1. Ambil data semua kambing dengan data produktivitasnya
        $kambings = Kambing::with('produktivitas')->get();

        if ($kambings->isEmpty()) {
            throw new Exception("Data kambing masih kosong. Silakan isi atau import data terlebih dahulu.");
        }

        // Siapkan dataset agregat dalam bentuk koordinat: [kambing_id => [C1, C2, C3]]
        $dataset = [];
        $kambingInfo = []; // Menyimpan detail untuk log

        foreach ($kambings as $kambing) {
            $avgBobot = $kambing->produktivitas->avg('bobot_badan') ?? 0;
            $maxKelahiran = $kambing->produktivitas->max('tingkat_kelahiran') ?? 0;
            $avgSusu = $kambing->produktivitas->avg('produksi_susu') ?? 0;

            $dataset[$kambing->id] = [
                'C1' => (float)$avgBobot,
                'C2' => (float)$maxKelahiran,
                'C3' => (float)$avgSusu
            ];

            $kambingInfo[$kambing->id] = [
                'kode_kambing' => $kambing->kode_kambing,
                'jenis_kelamin' => $kambing->jenis_kelamin,
            ];
        }

        // 2. Inisialisasi Centroid Awal secara Objektif & Dinamis (Metode Persentil)
        // Urutkan dataset berdasarkan skor rata-rata kriteria (C1 + C2 + C3) / 3
        $sortedDataset = collect($dataset)->map(function ($scores, $id) use ($kambingInfo) {
            $average = ($scores['C1'] + $scores['C2'] + $scores['C3']) / 3;
            return [
                'id' => $id,
                'scores' => $scores,
                'average' => $average,
                'kode_kambing' => $kambingInfo[$id]['kode_kambing']
            ];
        })->sortBy('average')->values()->all();

        $countDataset = count($sortedDataset);

        // Tentukan indeks persentil 15% (Rendah), 50% (Sedang), dan 85% (Tinggi)
        $idxRendah = max(0, min($countDataset - 1, (int)floor($countDataset * 0.15)));
        $idxSedang = max(0, min($countDataset - 1, (int)floor($countDataset * 0.50)));
        $idxTinggi = max(0, min($countDataset - 1, (int)floor($countDataset * 0.85)));

        $kambingRendah = $sortedDataset[$idxRendah];
        $kambingSedang = $sortedDataset[$idxSedang];
        $kambingTinggi = $sortedDataset[$idxTinggi];

        $centroids = [
            'Rendah' => $kambingRendah['scores'],
            'Sedang' => $kambingSedang['scores'],
            'Tinggi' => $kambingTinggi['scores']
        ];

        $centroidAwalInfo = [
            'Rendah' => [
                'kode_kambing' => $kambingRendah['kode_kambing'],
                'scores' => $kambingRendah['scores']
            ],
            'Sedang' => [
                'kode_kambing' => $kambingSedang['kode_kambing'],
                'scores' => $kambingSedang['scores']
            ],
            'Tinggi' => [
                'kode_kambing' => $kambingTinggi['kode_kambing'],
                'scores' => $kambingTinggi['scores']
            ]
        ];

        $history = []; // Untuk mencatat pergeseran centroid dan jarak per iterasi
        $converged = false;
        $iteration = 0;

        while (!$converged && $iteration < $maxIterations) {
            $iteration++;
            $assignments = [
                'Rendah' => [],
                'Sedang' => [],
                'Tinggi' => []
            ];

            $iterationLog = [
                'centroids' => $centroids,
                'details' => []
            ];

            // A. Hitung jarak Euclidean untuk tiap kambing ke masing-masing centroid
            foreach ($dataset as $kambingId => $scores) {
                $distC1 = $this->calculateEuclideanDistance($scores, $centroids['Rendah']);
                $distC2 = $this->calculateEuclideanDistance($scores, $centroids['Sedang']);
                $distC3 = $this->calculateEuclideanDistance($scores, $centroids['Tinggi']);

                // Tentukan cluster terdekat (jarak terkecil)
                $minDist = min($distC1, $distC2, $distC3);
                $cluster = '';

                if ($minDist === $distC1) {
                    $cluster = 'Rendah';
                } elseif ($minDist === $distC2) {
                    $cluster = 'Sedang';
                } else {
                    $cluster = 'Tinggi';
                }

                $assignments[$cluster][] = $kambingId;

                // Log rincian untuk iterasi saat ini
                $iterationLog['details'][$kambingId] = [
                    'kode_kambing' => $kambingInfo[$kambingId]['kode_kambing'],
                    'scores' => $scores,
                    'jarak_c1' => round($distC1, 4),
                    'jarak_c2' => round($distC2, 4),
                    'jarak_c3' => round($distC3, 4),
                    'kluster_sementara' => $cluster
                ];
            }

            // B. Hitung ulang koordinat centroid baru (rata-rata nilai anggota cluster)
            $newCentroids = [];
            foreach ($assignments as $clusterName => $members) {
                if (count($members) > 0) {
                    $sumC1 = 0;
                    $sumC2 = 0;
                    $sumC3 = 0;

                    foreach ($members as $memberId) {
                        $sumC1 += $dataset[$memberId]['C1'];
                        $sumC2 += $dataset[$memberId]['C2'];
                        $sumC3 += $dataset[$memberId]['C3'];
                    }

                    $count = count($members);
                    $newCentroids[$clusterName] = [
                        'C1' => round($sumC1 / $count, 4),
                        'C2' => round($sumC2 / $count, 4),
                        'C3' => round($sumC3 / $count, 4)
                    ];
                } else {
                    // Jika cluster kosong, centroid tidak bergeser
                    $newCentroids[$clusterName] = $centroids[$clusterName];
                }
            }

            // C. Cek Konvergensi (apakah centroid bergeser?)
            $converged = $this->checkCentroidsConvergence($centroids, $newCentroids);

            // Catat log iterasi ini ke history
            $iterationLog['assignments_count'] = [
                'Rendah' => count($assignments['Rendah']),
                'Sedang' => count($assignments['Sedang']),
                'Tinggi' => count($assignments['Tinggi'])
            ];
            $history[] = $iterationLog;

            // Update centroid untuk iterasi selanjutnya
            $centroids = $newCentroids;
        }

        // 3. Simpan Hasil Akhir ke Database (tabel_sesi_clustering & tabel_hasil_clustering)
        $sesi = null;
        DB::transaction(function () use ($dataset, $history, $centroids, $centroidAwalInfo, $countDataset, &$sesi) {
            // Buat record sesi baru
            $sesi = SesiClustering::create([
                'user_id' => Auth::id() ?? 1, // Default ke ID 1 jika tidak ada auth session (seeder / console)
                'jumlah_cluster' => 3,
                'total_iterasi' => count($history),
                'centroid_awal' => $centroidAwalInfo,
                'centroid_akhir' => $centroids,
                'total_data' => $countDataset,
            ]);

            // Ambil rincian iterasi terakhir
            $finalIteration = end($history);

            foreach ($dataset as $kambingId => $scores) {
                $detail = $finalIteration['details'][$kambingId];

                HasilClustering::create([
                    'sesi_id' => $sesi->id,
                    'kambing_id' => $kambingId,
                    'cluster' => $detail['kluster_sementara'],
                    'bobot_badan_val' => $scores['C1'],
                    'tingkat_kelahiran_val' => (int)$scores['C2'],
                    'produksi_susu_val' => $scores['C3'],
                    'jarak_c1' => $detail['jarak_c1'],
                    'jarak_c2' => $detail['jarak_c2'],
                    'jarak_c3' => $detail['jarak_c3'],
                ]);
            }
        });

        return [
            'sesi_id' => $sesi ? $sesi->id : null,
            'total_iterasi' => $iteration,
            'log_iterasi' => $history,
            'centroid_akhir' => $centroids,
            'centroid_awal' => $centroidAwalInfo,
        ];
    }

    /**
     * Dapatkan data centroid awal secara dinamis.
     */
    public function getInitialCentroids(): array
    {
        $kambings = Kambing::with('produktivitas')->get();
        if ($kambings->isEmpty()) {
            return [];
        }

        $dataset = [];
        $kambingInfo = [];

        foreach ($kambings as $kambing) {
            $avgBobot = $kambing->produktivitas->avg('bobot_badan') ?? 0;
            $maxKelahiran = $kambing->produktivitas->max('tingkat_kelahiran') ?? 0;
            $avgSusu = $kambing->produktivitas->avg('produksi_susu') ?? 0;

            $dataset[$kambing->id] = [
                'C1' => (float)$avgBobot,
                'C2' => (float)$maxKelahiran,
                'C3' => (float)$avgSusu
            ];

            $kambingInfo[$kambing->id] = [
                'kode_kambing' => $kambing->kode_kambing,
            ];
        }

        $sortedDataset = collect($dataset)->map(function ($scores, $id) use ($kambingInfo) {
            $average = ($scores['C1'] + $scores['C2'] + $scores['C3']) / 3;
            return [
                'id' => $id,
                'scores' => $scores,
                'average' => $average,
                'kode_kambing' => $kambingInfo[$id]['kode_kambing']
            ];
        })->sortBy('average')->values()->all();

        $countDataset = count($sortedDataset);
        if ($countDataset === 0) {
            return [];
        }

        $idxRendah = max(0, min($countDataset - 1, (int)floor($countDataset * 0.15)));
        $idxSedang = max(0, min($countDataset - 1, (int)floor($countDataset * 0.50)));
        $idxTinggi = max(0, min($countDataset - 1, (int)floor($countDataset * 0.85)));

        $kambingRendah = $sortedDataset[$idxRendah];
        $kambingSedang = $sortedDataset[$idxSedang];
        $kambingTinggi = $sortedDataset[$idxTinggi];

        return [
            'Rendah' => [
                'kode_kambing' => $kambingRendah['kode_kambing'],
                'scores' => $kambingRendah['scores']
            ],
            'Sedang' => [
                'kode_kambing' => $kambingSedang['kode_kambing'],
                'scores' => $kambingSedang['scores']
            ],
            'Tinggi' => [
                'kode_kambing' => $kambingTinggi['kode_kambing'],
                'scores' => $kambingTinggi['scores']
            ]
        ];
    }

    /**
     * Hitung Jarak Euclidean 3-Dimensi.
     */
    private function calculateEuclideanDistance(array $point1, array $point2): float
    {
        $sum = pow($point1['C1'] - $point2['C1'], 2) +
            pow($point1['C2'] - $point2['C2'], 2) +
            pow($point1['C3'] - $point2['C3'], 2);

        return sqrt($sum);
    }

    /**
     * Cek apakah koordinat centroid sama persis antara sebelum dan sesudah update.
     */
    private function checkCentroidsConvergence(array $oldCentroids, array $newCentroids): bool
    {
        foreach ($oldCentroids as $cluster => $coords) {
            if (
                $coords['C1'] !== $newCentroids[$cluster]['C1'] ||
                $coords['C2'] !== $newCentroids[$cluster]['C2'] ||
                $coords['C3'] !== $newCentroids[$cluster]['C3']
            ) {
                return false; // Ada pergeseran
            }
        }
        return true; // Konvergen
    }
}
