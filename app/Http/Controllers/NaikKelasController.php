<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NaikKelasController extends Controller
{
    /**
     * Check if user is Admin, block access for other roles
     */
    private function checkAdminAccess()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini. Hanya Admin yang dapat mengakses.');
        }
        return null;
    }

    /**
     * Display the naik kelas page.
     */
    public function index()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        return view('naik-kelas.index');
    }

    /**
     * Get semua murid aktif dari semua kelas untuk dipilih sebagai murid tetap.
     */
    public function getMuridTetap(Request $request)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Ambil semua murid aktif dari semua kelas
        $murids = Murid::where('status', 'Aktif')
            ->whereNotNull('kelas')
            ->orderBy('kelas')
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json([
            'success' => true,
            'murids' => $murids->map(function ($murid) {
                return [
                    'NIS' => $murid->NIS,
                    'nama_lengkap' => $murid->nama_lengkap,
                    'gender' => $murid->gender,
                    'kelas' => $murid->kelas,
                ];
            }),
        ]);
    }

    /**
     * Process naik kelas untuk semua murid di semua kelas sekaligus.
     */
    public function processNaikKelas(Request $request)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $request->validate([
            'ada_murid_tetap' => ['required', 'in:ya,tidak'],
            'murid_tetap' => ['nullable', 'array'],
            'murid_tetap.*' => ['exists:murid,NIS'],
        ]);

        $adaMuridTetap = $request->ada_murid_tetap;
        $muridTetap = $request->murid_tetap ?? [];

        // Validasi: jika pilih "ya" tapi tidak ada murid yang dipilih
        if ($adaMuridTetap === 'ya' && empty($muridTetap)) {
            return redirect()->back()
                ->with('error', 'Anda memilih "Iya" tetapi belum memilih murid tetap. Silakan pilih murid tetap terlebih dahulu.')
                ->withInput();
        }

        // Ambil semua murid aktif dari semua kelas
        $allMurids = Murid::where('status', 'Aktif')
            ->whereNotNull('kelas')
            ->get();

        $currentYear = date('Y');
        $processedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            // Group murid by kelas untuk efisiensi
            $muridsByKelas = $allMurids->groupBy('kelas');

            foreach ($muridsByKelas as $currentKelas => $murids) {
                // Tentukan kelas baru berdasarkan kelas saat ini
                $newKelas = $this->getNextKelas($currentKelas);
                
                if (!$newKelas) {
                    // Skip jika format kelas tidak valid
                    continue;
                }

                // Jika kelas baru adalah "Lulus"
                if ($newKelas === 'Lulus') {
                    foreach ($murids as $murid) {
                        // Jika murid termasuk dalam murid tetap, skip
                        if (in_array($murid->NIS, $muridTetap)) {
                            $skippedCount++;
                            continue;
                        }

                        $murid->update([
                            'status' => 'Lulus',
                            'tahun_lulus' => $currentYear,
                            'kelas' => null,
                        ]);
                        $processedCount++;
                    }
                } else {
                    // Cek apakah kelas baru sudah ada di database
                    $kelasExists = Kelas::where('kelas', $newKelas)->exists();
                    
                    if (!$kelasExists) {
                        // Jika kelas baru belum ada, buat kelas baru dengan username yang sama
                        $oldKelas = Kelas::where('kelas', $currentKelas)->first();
                        if ($oldKelas) {
                            Kelas::create([
                                'kelas' => $newKelas,
                                'username' => $oldKelas->username,
                            ]);
                        } else {
                            // Jika kelas lama tidak ada, buat kelas baru tanpa wali
                            Kelas::create([
                                'kelas' => $newKelas,
                                'username' => null,
                            ]);
                        }
                    }

                    // Update kelas murid
                    foreach ($murids as $murid) {
                        // Jika murid termasuk dalam murid tetap, skip
                        if (in_array($murid->NIS, $muridTetap)) {
                            $skippedCount++;
                            continue;
                        }

                        $murid->update([
                            'kelas' => $newKelas,
                        ]);
                        $processedCount++;
                    }
                }
            }

            DB::commit();

            $message = 'Proses naik kelas berhasil dilakukan. ';
            $message .= $processedCount . ' murid dinaikkan/luluskan.';
            if ($skippedCount > 0) {
                $message .= ' ' . $skippedCount . ' murid tetap di kelas yang sama.';
            }

            return redirect()->route('naik-kelas.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses naik kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get next kelas based on current kelas.
     * X-* → XI-*
     * XI-* → XII-*
     * XII-* → Lulus
     */
    private function getNextKelas($currentKelas)
    {
        // Cek apakah kelas adalah XII
        if (preg_match('/^XII-(\d+)$/', $currentKelas, $matches)) {
            return 'Lulus';
        }

        // Cek apakah kelas adalah XI
        if (preg_match('/^XI-(\d+)$/', $currentKelas, $matches)) {
            return 'XII-' . $matches[1];
        }

        // Cek apakah kelas adalah X
        if (preg_match('/^X-(\d+)$/', $currentKelas, $matches)) {
            return 'XI-' . $matches[1];
        }

        // Jika format tidak sesuai, return null
        return null;
    }
}

