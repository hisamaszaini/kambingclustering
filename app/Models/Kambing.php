<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kambing extends Model
{
    use HasFactory;

    protected $table = 'tbl_kambing';

    protected $fillable = [
        'kode_kambing',
        'jenis_kelamin',
    ];

    /**
     * Get all productivity data for the goat.
     */
    public function produktivitas()
    {
        return $this->hasMany(DataProduktivitas::class, 'kambing_id');
    }

    /**
     * Get all clustering results for the goat.
     */
    public function hasilClustering()
    {
        return $this->hasMany(HasilClustering::class, 'kambing_id');
    }

    /**
     * Dapatkan daftar notifikasi/peringatan produktivitas untuk kambing ini.
     */
    public function getNotifikasi()
    {
        $alerts = [];
        $today = now()->toDateString();
        $thirtyDaysAgo = now()->subDays(30)->toDateString();
        $fiveMonthsAgo = now()->subMonths(5)->toDateString();

        // Ambil riwayat produktivitas diurutkan tanggal terbaru ke terlama (untuk efisiensi pencarian di memory)
        // Jika relasi sudah diload (eager load), gunakan collection. Jika belum, jalankan query.
        $history = $this->relationLoaded('produktivitas') 
            ? $this->produktivitas->sortByDesc('tanggal_pencatatan') 
            : $this->produktivitas()->orderBy('tanggal_pencatatan', 'desc')->get();
        
        $latestRecord = $history->first();

        // 1. Cek Bobot per bulan (Semua Kambing)
        if (!$latestRecord) {
            $alerts[] = [
                'tipe' => 'bobot',
                'pesan' => 'Belum pernah mencatat data bobot badan.',
                'status' => 'critical',
            ];
        } else {
            // Cari pencatatan bobot badan terakhir (jika yang terbaru kosong/null, cari sebelumnya)
            $latestWeightRecord = $history->first(fn($p) => !is_null($p->bobot_badan));

            if (!$latestWeightRecord) {
                $alerts[] = [
                    'tipe' => 'bobot',
                    'pesan' => 'Belum pernah mencatat data bobot badan.',
                    'status' => 'critical',
                ];
            } elseif ($latestWeightRecord->tanggal_pencatatan < $thirtyDaysAgo) {
                $diff = now()->diffInDays(\Carbon\Carbon::parse($latestWeightRecord->tanggal_pencatatan));
                $alerts[] = [
                    'tipe' => 'bobot',
                    'pesan' => "Bobot badan belum diupdate selama {$diff} hari (terakhir dicatat " . \Carbon\Carbon::parse($latestWeightRecord->tanggal_pencatatan)->translatedFormat('d M Y') . ").",
                    'status' => 'warning',
                ];
            }
        }

        // Khusus Betina
        if ($this->jenis_kelamin === 'Betina') {
            // 2. Cek Susu perhari
            $latestMilkRecord = $history->first(fn($p) => $p->tanggal_pencatatan === $today);

            if (!$latestMilkRecord || is_null($latestMilkRecord->produksi_susu)) {
                // Cari pencatatan susu terakhir untuk informasi tambahan
                $lastRecordedMilk = $history->first(fn($p) => !is_null($p->produksi_susu));

                if (!$lastRecordedMilk) {
                    $alerts[] = [
                        'tipe' => 'susu',
                        'pesan' => 'Belum pernah mencatat produksi susu harian.',
                        'status' => 'critical',
                    ];
                } else {
                    $alerts[] = [
                        'tipe' => 'susu',
                        'pesan' => 'Produksi susu harian belum dicatat hari ini.',
                        'status' => 'warning',
                    ];
                }
            }

            // 3. Cek Beranak 5 bulan (beranak 5 bulan (betina only))
            $latestBirthRecord = $history->first(fn($p) => $p->tingkat_kelahiran > 0);

            if (!$latestBirthRecord) {
                $alerts[] = [
                    'tipe' => 'kelahiran',
                    'pesan' => 'Belum pernah mencatat riwayat kelahiran (beranak).',
                    'status' => 'info',
                ];
            } elseif ($latestBirthRecord->tanggal_pencatatan < $fiveMonthsAgo) {
                $diffMonths = now()->diffInMonths(\Carbon\Carbon::parse($latestBirthRecord->tanggal_pencatatan));
                $alerts[] = [
                    'tipe' => 'kelahiran',
                    'pesan' => "Sudah {$diffMonths} bulan sejak kelahiran terakhir (" . \Carbon\Carbon::parse($latestBirthRecord->tanggal_pencatatan)->translatedFormat('d M Y') . "). Perlu evaluasi siklus beranak.",
                    'status' => 'warning',
                ];
            }
        }

        return $alerts;
    }
}
