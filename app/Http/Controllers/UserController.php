<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $user = auth()->user();
        $query = User::query();

        // Jika user adalah TATIB, sembunyikan user dengan role Admin
        if ($user->role === 'TATIB') {
            $query->where('role', '!=', 'Admin');
        }

        $users = $query->orderBy('username')->paginate(10);

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

        return view('users.create', compact('allowedRoles'));
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
        ]);

        // Double check: TATIB tidak boleh membuat Admin
        if ($user->role === 'TATIB' && $validated['role'] === 'Admin') {
            return redirect()->back()->withErrors(['role' => 'Anda tidak memiliki izin untuk membuat user dengan role Admin.'])->withInput();
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

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

        return view('users.edit', compact('user', 'allowedRoles'));
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

        $user->update($validated);

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

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
