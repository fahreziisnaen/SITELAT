<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('murid')->insert([
            [
                'NIS' => '1234567890',
                'nama_lengkap' => 'Andi Prasetyo',
                'gender' => 'Laki-laki',
                'kelas' => 'X-2',
                'status' => 'Aktif',
                'tahun_lulus' => null,
            ],
            [
                'NIS' => '1234567891',
                'nama_lengkap' => 'Siti Aisyah',
                'gender' => 'Perempuan',
                'kelas' => 'X-2',
                'status' => 'Aktif',
                'tahun_lulus' => null,
            ],
        ]);
    }
}
