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
}
