<?php

namespace App\Exports;

use App\Models\HasilClustering;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HasilExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sesiId;

    public function __construct($sesiId)
    {
        $this->sesiId = $sesiId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return HasilClustering::with('kambing')
            ->where('sesi_id', $this->sesiId)
            ->orderByRaw("FIELD(cluster, 'Tinggi', 'Sedang', 'Rendah') ASC")
            ->orderBy('produksi_susu_val', 'desc')
            ->orderBy('bobot_badan_val', 'desc')
            ->orderBy('tingkat_kelahiran_val', 'desc')
            ->get();
    }

    /**
     * @var HasilClustering $row
     */
    public function map($row): array
    {
        return [
            $row->kambing->kode_kambing,
            $row->kambing->jenis_kelamin,
            $row->bobot_badan_val,
            $row->tingkat_kelahiran_val,
            $row->produksi_susu_val,
            $row->jarak_c1,
            $row->jarak_c2,
            $row->jarak_c3,
            $row->cluster,
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Kambing',
            'Jenis Kelamin',
            'Rata-rata Bobot Badan (kg)',
            'Maks Tingkat Kelahiran',
            'Rata-rata Produksi Susu (Liter)',
            'Jarak ke Centroid Rendah (C1)',
            'Jarak ke Centroid Sedang (C2)',
            'Jarak ke Centroid Tinggi (C3)',
            'Cluster',
        ];
    }
}
