<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keterlambatan extends Model
{
    use HasFactory;

    protected $table = 'keterlambatan';

    protected $fillable = [
        'NIS',
        'nama_murid',
        'gender',
        'kelas',
        'username',
        'tanggal',
        'waktu',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i',
    ];

    /**
     * Get the murid that owns the keterlambatan.
     */
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'NIS', 'NIS');
    }

    /**
     * Get the walikelas (user) at the time of keterlambatan.
     */
    public function walikelas()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}