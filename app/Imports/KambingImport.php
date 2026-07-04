<?php

namespace App\Imports;

use App\Models\Kambing;
use App\Models\DataProduktivitas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class KambingImport implements ToCollection, WithHeadingRow
{
    public $importedCount = 0;

    public function collection(Collection $rows)
    {
        $today = Carbon::today()->format('Y-m-d');

        foreach ($rows as $row) {
            // Kita support mapping heading key dari Excel
            // Excel heading row converts:
            // "Kode Kambing" -> "kode_kambing"
            // "Jenis Kelamin" -> "jenis_kelamin"
            // "Bobot Badan (kg)" -> "bobot_badan_kg"
            // "Tingkat Kelahiran" -> "tingkat_kelahiran"
            // "Produksi Susu (Liter)" -> "produksi_susu_liter"

            $kodeKambing = isset($row['kode_kambing']) ? trim($row['kode_kambing']) : null;
            if (empty($kodeKambing)) {
                continue;
            }

            $jenisKelamin = isset($row['jenis_kelamin']) ? trim($row['jenis_kelamin']) : 'Betina';
            // Normalize jenis kelamin to match enum ['Jantan', 'Betina']
            if (stripos($jenisKelamin, 'jantan') !== false || stripos($jenisKelamin, 'L') === 0) {
                $jenisKelamin = 'Jantan';
            } else {
                $jenisKelamin = 'Betina';
            }

            // Parse values and clean commas
            $bobotRaw = $row['bobot_badan_kg'] ?? $row['bobot_badan'] ?? 0;
            $bobotBadan = $this->parseFloat($bobotRaw);

            $tingkatKelahiran = (int)($row['tingkat_kelahiran'] ?? 0);

            $susuRaw = $row['produksi_susu_liter'] ?? $row['produksi_susu'] ?? 0;
            $produksiSusu = $this->parseFloat($susuRaw);

            // Cek apakah kambing sudah ada
            $kambing = Kambing::updateOrCreate(
                ['kode_kambing' => $kodeKambing],
                ['jenis_kelamin' => $jenisKelamin]
            );

            // Buat record data produktivitas
            DataProduktivitas::create([
                'kambing_id' => $kambing->id,
                'tanggal_pencatatan' => $today,
                'bobot_badan' => $bobotBadan,
                'tingkat_kelahiran' => $tingkatKelahiran,
                'produksi_susu' => $produksiSusu,
            ]);

            $this->importedCount++;
        }
    }

    /**
     * Parse raw string/number representation of float.
     * Handles comma decimal separators (e.g., 45,2 -> 45.2).
     */
    private function parseFloat($val): float
    {
        if (is_numeric($val)) {
            return (double)$val;
        }

        $str = (string)$val;
        $str = str_replace(',', '.', $str);
        $str = preg_replace('/[^0-9.]/', '', $str);

        return is_numeric($str) ? (double)$str : 0.00;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
