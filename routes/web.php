<?php

use App\Http\Controllers\KelasController;
use App\Http\Controllers\KeterlambatanController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\NaikKelasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Hitung tanggal untuk filter
    $today = \Carbon\Carbon::today();
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

    // Jika user adalah Walikelas, filter berdasarkan kelas yang dia pegang
    if ($user && $user->role === 'Walikelas') {
        // Ambil semua kelas yang dipegang oleh Walikelas
        $kelasIds = \App\Models\Kelas::where('username', $user->username)->pluck('kelas');

        // Query base untuk keterlambatan
        $keterlambatanQuery = \App\Models\Keterlambatan::whereIn('kelas', $kelasIds);

        // Statistik untuk Walikelas - gunakan snapshot kelas
        $stats = [
            'total_kelas' => $kelasIds->count(),
            'total_murid' => \App\Models\Murid::whereIn('kelas', $kelasIds)->count(),
            'keterlambatan_hari_ini' => (clone $keterlambatanQuery)->whereDate('tanggal', $today)->count(),
            'keterlambatan_bulan_ini' => (clone $keterlambatanQuery)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count(),
            'recent_keterlambatan' => \App\Models\Keterlambatan::whereIn('kelas', $kelasIds)
                ->latest()
                ->take(5)
                ->get(),
        ];
    } else {
        // Query base untuk keterlambatan
        $keterlambatanQuery = \App\Models\Keterlambatan::query();

        // Statistik untuk Admin dan TATIB (semua data)
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_kelas' => \App\Models\Kelas::count(),
            'total_murid' => \App\Models\Murid::count(),
            'keterlambatan_hari_ini' => (clone $keterlambatanQuery)->whereDate('tanggal', $today)->count(),
            'keterlambatan_bulan_ini' => (clone $keterlambatanQuery)->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->count(),
            'recent_keterlambatan' => \App\Models\Keterlambatan::latest()
                ->take(5)
                ->get(),
        ];
    }

    return view('dashboard', compact('stats'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Dinonaktifkan

    // User Management
    Route::resource('users', UserController::class);

    // Data Kelas
    Route::resource('kelas', KelasController::class);

    // Data Murid
    // Import routes harus diletakkan sebelum resource route agar tidak tertangkap sebagai parameter
    Route::get('/murid/import', [MuridController::class, 'showImport'])->name('murid.import');
    Route::post('/murid/import', [MuridController::class, 'import'])->name('murid.import.store');
    Route::get('/murid/import/template', [MuridController::class, 'downloadTemplate'])->name('murid.import.template');
    Route::resource('murid', MuridController::class);

    // Data Keterlambatan
    Route::resource('keterlambatan', KeterlambatanController::class);
    Route::get('/api/murid/{nis}', [KeterlambatanController::class, 'getMurid'])->name('api.murid');

    // Report
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/kelas', [ReportController::class, 'indexKelas'])->name('report.kelas');

    // Naik Kelas
    Route::get('/naik-kelas', [NaikKelasController::class, 'index'])->name('naik-kelas.index');
    Route::post('/naik-kelas/process', [NaikKelasController::class, 'processNaikKelas'])->name('naik-kelas.process');
});

require __DIR__.'/auth.php';
