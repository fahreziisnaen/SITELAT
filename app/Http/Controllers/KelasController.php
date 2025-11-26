<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Check if user is Walikelas and block access
     */
    private function checkWalikelasAccess()
    {
        $user = auth()->user();
        if ($user && $user->role === 'Walikelas') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        // Sort kelas dengan natural sort untuk pagination
        $kelas = Kelas::with('walikelas')
            ->withCount('murids')
            ->orderByRaw("
                CASE 
                    WHEN kelas LIKE 'X-%' THEN 1
                    WHEN kelas LIKE 'XI-%' THEN 2
                    WHEN kelas LIKE 'XII-%' THEN 3
                    ELSE 4
                END,
                CAST(SUBSTRING_INDEX(kelas, '-', -1) AS UNSIGNED)
            ")
            ->paginate(10);

        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        // Tampilkan user dengan role Walikelas dan TATIB
        $walikelas = User::whereIn('role', ['Walikelas', 'TATIB'])->orderBy('nama_lengkap')->get();

        return view('kelas.create', compact('walikelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'kelas' => ['required', 'string', 'max:255', 'unique:kelas'],
            'username' => ['nullable', Rule::exists('users', 'username')],
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $kela->load('walikelas', 'murids');

        return view('kelas.show', compact('kela'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        // Tampilkan user dengan role Walikelas dan TATIB
        $walikelas = User::whereIn('role', ['Walikelas', 'TATIB'])->orderBy('nama_lengkap')->get();

        return view('kelas.edit', compact('kela', 'walikelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'kelas' => ['required', 'string', 'max:255', Rule::unique('kelas')->ignore($kela->kelas, 'kelas')],
            'username' => ['nullable', Rule::exists('users', 'username')],
        ]);

        $kela->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        // Cek apakah ada murid di kelas ini
        $muridCount = $kela->murids()->count();
        if ($muridCount > 0) {
            return redirect()->route('kelas.index')
                ->with('error', "Kelas {$kela->kelas} tidak dapat dihapus karena masih memiliki {$muridCount} murid. Pindahkan atau hapus murid terlebih dahulu.");
        }

        $kela->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
