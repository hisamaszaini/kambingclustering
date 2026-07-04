<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesiClustering extends Model
{
    use HasFactory;

    protected $table = 'tbl_sesi_clustering';

    protected $fillable = [
        'user_id',
        'jumlah_cluster',
        'total_iterasi',
        'centroid_awal',
        'centroid_akhir',
        'total_data',
    ];

    protected $casts = [
        'centroid_awal' => 'array',
        'centroid_akhir' => 'array',
    ];

    /**
     * Get the user/admin that executed this clustering session.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all clustering results for this session.
     */
    public function hasilClustering()
    {
        return $this->hasMany(HasilClustering::class, 'sesi_id');
    }
}
