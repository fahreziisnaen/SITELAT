<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Administrator',
                'role' => 'Admin',
            ],
            [
                'username' => 'tatib',
                'password' => Hash::make('tatib123'),
                'nama_lengkap' => 'Andi Prasetyo',
                'role' => 'TATIB',
            ],
            [
                'username' => 'wali_x2',
                'password' => Hash::make('password'),
                'nama_lengkap' => 'Budi Santoso',
                'role' => 'Walikelas',
            ],
        ]);
    }
}
