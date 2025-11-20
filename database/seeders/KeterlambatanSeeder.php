<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KeterlambatanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data murid untuk mendapatkan snapshot: NIS, nama_murid, kelas, dan walikelas
        $murid1 = DB::table('murid')->where('NIS', '1234567890')->first();
        $murid2 = DB::table('murid')->where('NIS', '1234567891')->first();
        
        // Snapshot untuk murid 1
        $nis1 = $murid1 ? $murid1->NIS : null;
        $namaMurid1 = $murid1 ? $murid1->nama_lengkap : null;
        $gender1 = $murid1 ? $murid1->gender : null;
        $kelas1 = $murid1 ? $murid1->kelas : null;
        
        // Snapshot untuk murid 2
        $nis2 = $murid2 ? $murid2->NIS : null;
        $namaMurid2 = $murid2 ? $murid2->nama_lengkap : null;
        $gender2 = $murid2 ? $murid2->gender : null;
        $kelas2 = $murid2 ? $murid2->kelas : null;
        
        // Ambil walikelas dari kelas (snapshot)
        $walikelas1 = null;
        $walikelas2 = null;
        if ($kelas1) {
            $kelasData1 = DB::table('kelas')->where('kelas', $kelas1)->first();
            $walikelas1 = $kelasData1 ? $kelasData1->username : null;
        }
        if ($kelas2) {
            $kelasData2 = DB::table('kelas')->where('kelas', $kelas2)->first();
            $walikelas2 = $kelasData2 ? $kelasData2->username : null;
        }
        
        DB::table('keterlambatan')->insert([
            [
                'NIS' => $nis1,
                'nama_murid' => $namaMurid1, // Snapshot nama murid
                'gender' => $gender1, // Snapshot gender
                'kelas' => $kelas1, // Snapshot kelas
                'username' => $walikelas1, // Snapshot walikelas
                'tanggal' => Carbon::now()->toDateString(),
                'waktu' => '07:45:00',
                'keterangan' => 'Terlambat karena macet',
                'bukti' => 'foto1.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'NIS' => $nis2,
                'nama_murid' => $namaMurid2, // Snapshot nama murid
                'gender' => $gender2, // Snapshot gender
                'kelas' => $kelas2, // Snapshot kelas
                'username' => $walikelas2, // Snapshot walikelas
                'tanggal' => Carbon::now()->toDateString(),
                'waktu' => '08:10:00',
                'keterangan' => 'Bangun kesiangan',
                'bukti' => 'foto2.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
