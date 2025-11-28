<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
    public function index(Request $request)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $user = auth()->user();
        $query = User::with('kelas');

        // Jika user adalah TATIB, sembunyikan user dengan role Admin
        if ($user->role === 'TATIB') {
            $query->where('role', '!=', 'Admin');
        }

        // Filter berdasarkan nama lengkap
        if ($request->filled('nama')) {
            $query->where('nama_lengkap', 'like', '%'.$request->nama.'%');
        }

        $users = $query->orderBy('username')->paginate(5);

        return view('users.index', compact('users'));
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

        $user = auth()->user();
        $allowedRoles = ['Admin', 'TATIB', 'Walikelas'];

        // Jika user adalah TATIB, hanya bisa membuat TATIB dan Walikelas
        if ($user->role === 'TATIB') {
            $allowedRoles = ['TATIB', 'Walikelas'];
        }

        // Load kelas yang tersedia (hanya kelas yang belum punya walikelas)
        $kelasList = Kelas::whereNull('username')
            ->orderByRaw("
                CASE 
                    WHEN kelas LIKE 'X-%' THEN 1
                    WHEN kelas LIKE 'XI-%' THEN 2
                    WHEN kelas LIKE 'XII-%' THEN 3
                    ELSE 4
                END,
                CAST(SUBSTRING_INDEX(kelas, '-', -1) AS UNSIGNED)
            ")
            ->get();

        return view('users.create', compact('allowedRoles', 'kelasList'));
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

        $user = auth()->user();
        $allowedRoles = ['Admin', 'TATIB', 'Walikelas'];

        // Jika user adalah TATIB, tidak boleh membuat user dengan role Admin
        if ($user->role === 'TATIB') {
            $allowedRoles = ['TATIB', 'Walikelas'];
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in($allowedRoles)],
            'kelas' => ['nullable', 'string', Rule::exists('kelas', 'kelas')],
        ]);

        // Double check: TATIB tidak boleh membuat Admin
        if ($user->role === 'TATIB' && $validated['role'] === 'Admin') {
            return redirect()->back()->withErrors(['role' => 'Anda tidak memiliki izin untuk membuat user dengan role Admin.'])->withInput();
        }

        // Validasi kelas sebelum create user
        $kelasToAssign = $validated['kelas'] ?? null;
        unset($validated['kelas']);

        if (in_array($validated['role'], ['Walikelas', 'TATIB']) && ! empty($kelasToAssign)) {
            // Validasi: Pastikan kelas yang dipilih belum punya walikelas
            $kelas = Kelas::where('kelas', $kelasToAssign)->first();
            if ($kelas && $kelas->username) {
                return redirect()->back()
                    ->withErrors(['kelas' => "Kelas {$kelasToAssign} sudah memiliki walikelas."])
                    ->withInput();
            }
        }

        $validated['password'] = Hash::make($validated['password']);

        $newUser = User::create($validated);

        // Assign kelas jika role adalah Walikelas atau TATIB
        if (in_array($validated['role'], ['Walikelas', 'TATIB']) && ! empty($kelasToAssign)) {
            Kelas::where('kelas', $kelasToAssign)->update(['username' => $newUser->username]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $currentUser = auth()->user();

        // Jika user adalah TATIB, tidak boleh melihat user dengan role Admin
        if ($currentUser->role === 'TATIB' && $user->role === 'Admin') {
            return redirect()->route('users.index')->with('error', 'Anda tidak memiliki izin untuk melihat user dengan role Admin.');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $currentUser = auth()->user();
        $allowedRoles = ['Admin', 'TATIB', 'Walikelas'];

        // Jika user adalah TATIB, hanya bisa mengubah ke TATIB dan Walikelas
        // Dan tidak boleh mengubah user yang sudah Admin atau TATIB lain
        if ($currentUser->role === 'TATIB') {
            $allowedRoles = ['TATIB', 'Walikelas'];

            // Jika user yang akan diupdate adalah Admin, tidak boleh diubah
            if ($user->role === 'Admin') {
                return redirect()->route('users.index')->with('error', 'Anda tidak memiliki izin untuk mengubah user dengan role Admin.');
            }

            // TATIB tidak boleh mengedit sesama TATIB
            if ($user->role === 'TATIB' && $user->username !== $currentUser->username) {
                return redirect()->route('users.index')->with('error', 'Anda tidak memiliki izin untuk mengubah user dengan role TATIB.');
            }
        }

        // Load kelas yang tersedia untuk user dengan role Walikelas atau TATIB
        $kelasList = collect();
        if (in_array($user->role, ['Walikelas', 'TATIB']) || in_array('Walikelas', $allowedRoles) || in_array('TATIB', $allowedRoles)) {
            // Hanya tampilkan kelas yang belum punya walikelas (username = null)
            // atau kelas yang sedang dimiliki user ini (untuk bisa tetap dipilih saat edit)
            $kelasList = Kelas::where(function ($query) use ($user) {
                $query->whereNull('username')
                    ->orWhere('username', $user->username);
            })
                ->orderByRaw("
                    CASE 
                        WHEN kelas LIKE 'X-%' THEN 1
                        WHEN kelas LIKE 'XI-%' THEN 2
                        WHEN kelas LIKE 'XII-%' THEN 3
                        ELSE 4
                    END,
                    CAST(SUBSTRING_INDEX(kelas, '-', -1) AS UNSIGNED)
                ")
                ->get();
        }

        // Load kelas yang sedang dimiliki user untuk pre-select
        $user->load('kelas');

        return view('users.edit', compact('user', 'allowedRoles', 'kelasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $currentUser = auth()->user();
        $allowedRoles = ['Admin', 'TATIB', 'Walikelas'];

        // Jika user adalah TATIB, tidak boleh mengubah role menjadi Admin
        if ($currentUser->role === 'TATIB') {
            $allowedRoles = ['TATIB', 'Walikelas'];

            // Jika user yang akan diupdate adalah Admin, tidak boleh diubah
            if ($user->role === 'Admin') {
                return redirect()->back()->withErrors(['role' => 'Anda tidak memiliki izin untuk mengubah user dengan role Admin.'])->withInput();
            }

            // TATIB tidak boleh mengedit sesama TATIB
            if ($user->role === 'TATIB' && $user->username !== $currentUser->username) {
                return redirect()->back()->withErrors(['role' => 'Anda tidak memiliki izin untuk mengubah user dengan role TATIB.'])->withInput();
            }
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->username, 'username')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in($allowedRoles)],
            'kelas' => ['nullable', 'string', Rule::exists('kelas', 'kelas')],
        ]);

        // Double check: TATIB tidak boleh mengubah menjadi Admin
        if ($currentUser->role === 'TATIB' && $validated['role'] === 'Admin') {
            return redirect()->back()->withErrors(['role' => 'Anda tidak memiliki izin untuk mengubah role menjadi Admin.'])->withInput();
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle kelas assignment untuk Walikelas dan TATIB
        $kelasToAssign = $validated['kelas'] ?? null;
        unset($validated['kelas']);

        // Validasi kelas sebelum update user
        if (in_array($validated['role'], ['Walikelas', 'TATIB']) && ! empty($kelasToAssign)) {
            // Validasi: Pastikan kelas yang dipilih belum punya walikelas (kecuali kelas yang sedang dimiliki user)
            $kelas = Kelas::where('kelas', $kelasToAssign)->first();
            if ($kelas && $kelas->username && $kelas->username !== $user->username) {
                return redirect()->back()
                    ->withErrors(['kelas' => "Kelas {$kelasToAssign} sudah memiliki walikelas."])
                    ->withInput();
            }
        }

        $user->update($validated);

        // Update kelas assignment
        if (in_array($validated['role'], ['Walikelas', 'TATIB'])) {
            // Hapus semua kelas yang sebelumnya dimiliki user ini
            Kelas::where('username', $user->username)->update(['username' => null]);

            // Assign kelas baru yang dipilih (jika ada)
            if (! empty($kelasToAssign)) {
                Kelas::where('kelas', $kelasToAssign)->update(['username' => $user->username]);
            }
        } else {
            // Jika role bukan Walikelas/TATIB, hapus semua kelas assignment
            Kelas::where('username', $user->username)->update(['username' => null]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $currentUser = auth()->user();

        // Jika user adalah TATIB, tidak boleh menghapus user dengan role Admin
        if ($currentUser->role === 'TATIB' && $user->role === 'Admin') {
            return redirect()->route('users.index')->with('error', 'Anda tidak memiliki izin untuk menghapus user dengan role Admin.');
        }

        // Cek apakah user memiliki kelas (hanya untuk Walikelas dan TATIB)
        if (in_array($user->role, ['Walikelas', 'TATIB'])) {
            $kelasList = Kelas::where('username', $user->username)->get();
            if ($kelasList->count() > 0) {
                $kelasNames = $kelasList->pluck('kelas')->implode(', ');
                $errorMessage = 'User masih menjadi walikelas dari kelas '.$kelasNames.'. Ubah walikelas di kelas tersebut sebelum menghapus user ini.';

                return redirect()->route('users.index')->with('error', $errorMessage);
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
