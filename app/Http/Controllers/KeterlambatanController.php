<?php

namespace App\Http\Controllers;

use App\Models\Keterlambatan;
use App\Models\Murid;
use App\Services\KeterlambatanNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeterlambatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil filter dari request
        $tanggal = $request->input('tanggal');
        $namaMurid = $request->input('nama_murid');

        // Query dasar - gunakan snapshot, tidak perlu relasi murid
        $query = Keterlambatan::with('walikelas');

        // Jika user adalah Walikelas, filter berdasarkan snapshot kelas yang dia pegang
        if ($user && $user->role === 'Walikelas') {
            // Ambil semua kelas yang dipegang oleh Walikelas
            $kelasIds = \App\Models\Kelas::where('username', $user->username)->pluck('kelas');

            // Filter berdasarkan snapshot kelas saja (data historis)
            $query->whereIn('kelas', $kelasIds);
        }

        // Filter berdasarkan tanggal
        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        // Filter berdasarkan nama murid - gunakan snapshot nama_murid
        if ($namaMurid) {
            $query->where('nama_murid', 'like', '%'.$namaMurid.'%');
        }

        // Order dan paginate
        $keterlambatan = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->paginate(10)
            ->withQueryString(); // Preserve query string untuk pagination

        return view('keterlambatan.index', compact('keterlambatan', 'tanggal', 'namaMurid'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Walikelas tidak boleh create
        if (auth()->user()->role === 'Walikelas') {
            return redirect()->route('keterlambatan.index')->with('error', 'Anda tidak memiliki izin untuk menambahkan data keterlambatan.');
        }

        $murids = Murid::orderBy('nama_lengkap')->get();

        return view('keterlambatan.create', compact('murids'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Walikelas tidak boleh store
        if (auth()->user()->role === 'Walikelas') {
            return redirect()->route('keterlambatan.index')->with('error', 'Anda tidak memiliki izin untuk menambahkan data keterlambatan.');
        }

        $validated = $request->validate([
            'NIS' => ['required', 'exists:murid,NIS'],
            'tanggal' => ['required', 'date'],
            'waktu' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string'],
            'bukti' => ['nullable', 'image', 'max:2048'],
        ]);

        // Validasi: 1 murid hanya bisa 1 keterlambatan per hari
        $existingKeterlambatan = Keterlambatan::where('NIS', $validated['NIS'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($existingKeterlambatan) {
            return redirect()->back()
                ->withErrors(['NIS' => 'Murid ini sudah memiliki data keterlambatan pada tanggal yang dipilih.'])
                ->withInput();
        }

        // Ambil data murid untuk snapshot NIS, nama_murid, gender, kelas, dan walikelas
        $murid = Murid::where('NIS', $validated['NIS'])->first();
        if ($murid) {
            // Simpan snapshot: NIS, nama_murid, gender, kelas, dan walikelas
            $validated['nama_murid'] = $murid->nama_lengkap; // Snapshot nama murid
            $validated['gender'] = $murid->gender; // Snapshot gender
            $validated['kelas'] = $murid->kelas; // Snapshot kelas

            // Ambil walikelas dari kelas murid
            if ($murid->kelas) {
                $kelasData = \App\Models\Kelas::where('kelas', $murid->kelas)->first();
                if ($kelasData && $kelasData->username) {
                    $validated['username'] = $kelasData->username; // Snapshot walikelas
                }
            }
        }

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti-keterlambatan', 'public');
        }

        $keterlambatan = Keterlambatan::create($validated);

        // Send notification to internal system
        $notificationService = new KeterlambatanNotificationService;
        $notificationService->sendNotification($keterlambatan);

        return redirect()->route('keterlambatan.index')->with('success', 'Data keterlambatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Keterlambatan $keterlambatan)
    {
        // Tidak perlu load murid, gunakan snapshot saja
        return view('keterlambatan.show', compact('keterlambatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Keterlambatan $keterlambatan)
    {
        // Walikelas tidak boleh edit
        if (auth()->user()->role === 'Walikelas') {
            return redirect()->route('keterlambatan.index')->with('error', 'Anda tidak memiliki izin untuk mengubah data keterlambatan.');
        }

        $murids = Murid::orderBy('nama_lengkap')->get();

        return view('keterlambatan.edit', compact('keterlambatan', 'murids'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Keterlambatan $keterlambatan)
    {
        // Walikelas tidak boleh update
        if (auth()->user()->role === 'Walikelas') {
            return redirect()->route('keterlambatan.index')->with('error', 'Anda tidak memiliki izin untuk mengubah data keterlambatan.');
        }

        $validated = $request->validate([
            'NIS' => ['required', 'exists:murid,NIS'],
            'tanggal' => ['required', 'date'],
            'waktu' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string'],
            'bukti' => ['nullable', 'image', 'max:2048'],
        ]);

        // Validasi: 1 murid hanya bisa 1 keterlambatan per hari (exclude record yang sedang diupdate)
        $existingKeterlambatan = Keterlambatan::where('NIS', $validated['NIS'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->where('id', '!=', $keterlambatan->id)
            ->first();

        if ($existingKeterlambatan) {
            return redirect()->back()
                ->withErrors(['NIS' => 'Murid ini sudah memiliki data keterlambatan pada tanggal yang dipilih.'])
                ->withInput();
        }

        // Snapshot tidak boleh diupdate jika NIS tidak berubah
        // Hanya update snapshot jika:
        // 1. NIS berubah (karena ini keterlambatan untuk murid yang berbeda, perlu snapshot baru)
        // 2. Belum ada snapshot sama sekali (untuk data lama yang mungkin belum punya snapshot)
        if ($keterlambatan->NIS != $validated['NIS']) {
            // NIS berubah, ambil snapshot baru dari murid yang baru
            $murid = Murid::where('NIS', $validated['NIS'])->first();
            if ($murid) {
                // Simpan snapshot baru: NIS, nama_murid, gender, kelas, dan walikelas
                $validated['nama_murid'] = $murid->nama_lengkap; // Snapshot nama murid
                $validated['gender'] = $murid->gender; // Snapshot gender
                $validated['kelas'] = $murid->kelas; // Snapshot kelas

                // Ambil walikelas dari kelas murid
                if ($murid->kelas) {
                    $kelasData = \App\Models\Kelas::where('kelas', $murid->kelas)->first();
                    if ($kelasData && $kelasData->username) {
                        $validated['username'] = $kelasData->username; // Snapshot walikelas
                    } else {
                        $validated['username'] = null;
                    }
                } else {
                    $validated['username'] = null;
                }
            }
        } elseif (! $keterlambatan->kelas || ! $keterlambatan->nama_murid || ! $keterlambatan->gender) {
            // NIS tidak berubah tapi belum ada snapshot lengkap, ambil snapshot dari data saat ini
            // (untuk data lama yang mungkin belum punya snapshot)
            $murid = Murid::where('NIS', $validated['NIS'])->first();
            if ($murid) {
                if (! $keterlambatan->nama_murid) {
                    $validated['nama_murid'] = $murid->nama_lengkap; // Snapshot nama murid
                }
                if (! $keterlambatan->gender) {
                    $validated['gender'] = $murid->gender; // Snapshot gender
                }
                if (! $keterlambatan->kelas) {
                    $validated['kelas'] = $murid->kelas; // Snapshot kelas
                }

                // Ambil walikelas dari kelas murid
                if ($murid->kelas) {
                    $kelasData = \App\Models\Kelas::where('kelas', $murid->kelas)->first();
                    if ($kelasData && $kelasData->username) {
                        $validated['username'] = $kelasData->username; // Snapshot walikelas
                    } else {
                        $validated['username'] = null;
                    }
                } else {
                    $validated['username'] = null;
                }
            }
        } else {
            // NIS tidak berubah dan sudah ada snapshot lengkap, JANGAN update snapshot
            // Tetap gunakan snapshot yang ada (tidak masukkan snapshot ke validated)
            unset($validated['nama_murid']);
            unset($validated['gender']);
            unset($validated['kelas']);
            unset($validated['username']);
        }

        if ($request->hasFile('bukti')) {
            // Delete old file if exists
            if ($keterlambatan->bukti) {
                Storage::disk('public')->delete($keterlambatan->bukti);
            }
            $validated['bukti'] = $request->file('bukti')->store('bukti-keterlambatan', 'public');
        }

        $keterlambatan->update($validated);

        return redirect()->route('keterlambatan.index')->with('success', 'Data keterlambatan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Keterlambatan $keterlambatan)
    {
        // Walikelas tidak boleh delete
        if (auth()->user()->role === 'Walikelas') {
            return redirect()->route('keterlambatan.index')->with('error', 'Anda tidak memiliki izin untuk menghapus data keterlambatan.');
        }

        // Delete file if exists
        if ($keterlambatan->bukti) {
            Storage::disk('public')->delete($keterlambatan->bukti);
        }

        $keterlambatan->delete();

        return redirect()->route('keterlambatan.index')->with('success', 'Data keterlambatan berhasil dihapus.');
    }

    /**
     * Get murid data for AJAX request
     */
    public function getMurid($nis)
    {
        $murid = Murid::where('NIS', $nis)->first();
        if ($murid) {
            return response()->json([
                'success' => true,
                'gender' => $murid->gender,
            ]);
        }

        return response()->json(['success' => false]);
    }
}
