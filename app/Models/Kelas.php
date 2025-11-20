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
}