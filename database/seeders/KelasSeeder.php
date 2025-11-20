<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [];
        
        // Kelas X-1 sampai X-10
        for ($i = 1; $i <= 10; $i++) {
            $kelas[] = ['kelas' => 'X-' . $i, 'username' => null];
        }
        
        // Kelas XI-1 sampai XI-10
        for ($i = 1; $i <= 10; $i++) {
            $kelas[] = ['kelas' => 'XI-' . $i, 'username' => null];
        }
        
        // Kelas XII-1 sampai XII-10
        for ($i = 1; $i <= 10; $i++) {
            $kelas[] = ['kelas' => 'XII-' . $i, 'username' => null];
        }
        
        DB::table('kelas')->insert($kelas);
    }
}
