<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';
    protected $primaryKey = 'NIS';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'NIS',
        'nama_lengkap',
        'gender',
        'kelas',
        'status',
        'tahun_lulus',
    ];

    /**
     * Get the kelas that owns the murid.
     */
    public function kelasRelation()
    {
        return $this->belongsTo(Kelas::class, 'kelas', 'kelas');
    }

    /**
     * Get the keterlambatan records for the murid.
     */
    public function keterlambatan()
    {
        return $this->hasMany(Keterlambatan::class, 'NIS', 'NIS');
    }
}