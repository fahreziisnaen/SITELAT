<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $primaryKey = 'kelas';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kelas',
        'username',
    ];

    /**
     * Get the walikelas (user) that owns the kelas.
     */
    public function walikelas()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    /**
     * Get the murids for the kelas.
     */
    public function murids()
    {
        return $this->hasMany(Murid::class, 'kelas', 'kelas');
    }

    /**
     * Scope untuk mengurutkan kelas dengan natural sort (X-1, X-2, ..., X-10, XI-1, ...)
     */
    public function scopeOrderByNatural($query)
    {
        return $query->get()->sort(function ($a, $b) {
            // Extract level (X, XI, XII) dan nomor
            preg_match('/^(XII|XI|X)-(\d+)$/', $a->kelas, $matchesA);
            preg_match('/^(XII|XI|X)-(\d+)$/', $b->kelas, $matchesB);

            if (empty($matchesA) || empty($matchesB)) {
                return strcmp($a->kelas, $b->kelas);
            }

            $levelA = $matchesA[1];
            $levelB = $matchesB[1];
            $numA = (int) $matchesA[2];
            $numB = (int) $matchesB[2];

            // Urutkan berdasarkan level dulu (X < XI < XII)
            $levelOrder = ['X' => 1, 'XI' => 2, 'XII' => 3];
            $levelCompare = ($levelOrder[$levelA] ?? 999) <=> ($levelOrder[$levelB] ?? 999);

            if ($levelCompare !== 0) {
                return $levelCompare;
            }

            // Jika level sama, urutkan berdasarkan nomor
            return $numA <=> $numB;
        })->values();
    }

    /**
     * Static method untuk sort collection kelas dengan natural sort
     */
    public static function sortNatural($kelasCollection)
    {
        return $kelasCollection->sort(function ($a, $b) {
            // Extract level (X, XI, XII) dan nomor
            preg_match('/^(XII|XI|X)-(\d+)$/', $a->kelas, $matchesA);
            preg_match('/^(XII|XI|X)-(\d+)$/', $b->kelas, $matchesB);

            if (empty($matchesA) || empty($matchesB)) {
                return strcmp($a->kelas, $b->kelas);
            }

            $levelA = $matchesA[1];
            $levelB = $matchesB[1];
            $numA = (int) $matchesA[2];
            $numB = (int) $matchesB[2];

            // Urutkan berdasarkan level dulu (X < XI < XII)
            $levelOrder = ['X' => 1, 'XI' => 2, 'XII' => 3];
            $levelCompare = ($levelOrder[$levelA] ?? 999) <=> ($levelOrder[$levelB] ?? 999);

            if ($levelCompare !== 0) {
                return $levelCompare;
            }

            // Jika level sama, urutkan berdasarkan nomor
            return $numA <=> $numB;
        })->values();
    }
}
