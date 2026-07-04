<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasilClustering extends Model
{
    use HasFactory;

    protected $table = 'tbl_hasil_clustering';

    protected $fillable = [
        'sesi_id',
        'kambing_id',
        'cluster',
        'bobot_badan_val',
        'tingkat_kelahiran_val',
        'produksi_susu_val',
        'jarak_c1',
        'jarak_c2',
        'jarak_c3',
    ];

    /**
     * Get the session of this result.
     */
    public function sesi()
    {
        return $this->belongsTo(SesiClustering::class, 'sesi_id');
    }

    /**
     * Get the goat of this result.
     */
    public function kambing()
    {
        return $this->belongsTo(Kambing::class, 'kambing_id');
    }
}
