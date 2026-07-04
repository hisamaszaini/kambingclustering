<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataProduktivitas extends Model
{
    use HasFactory;

    protected $table = 'tbl_data_produktivitas';

    protected $fillable = [
        'kambing_id',
        'tanggal_pencatatan',
        'bobot_badan',
        'tingkat_kelahiran',
        'produksi_susu',
    ];

    /**
     * Get the goat that owns this productivity record.
     */
    public function kambing()
    {
        return $this->belongsTo(Kambing::class, 'kambing_id');
    }
}
