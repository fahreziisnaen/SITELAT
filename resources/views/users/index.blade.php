<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    User Management
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola Data Pengguna</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg shadow-md mb-6 flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-md mb-6 flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 text-gray-900">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4 sm:mb-6 bg-gray-50 rounded-lg p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <div class="flex-1">
                                <label for="nama" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                    Cari Nama User
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 sm:pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="nama" id="nama" value="{{ request('nama') }}" 
                                        placeholder="Cari nama user..." 
                                        class="block w-full pl-8 sm:pl-10 pr-2 sm:pr-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span class="hidden sm:inline">Cari</span>
                                </button>
                                @if(request('nama'))
                                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="w-full overflow-hidden">
                        <table class="w-full divide-y divide-gray-200" style="table-layout: fixed; width: 100%;">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0 hidden md:table-cell" style="width: 12%;">Username</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0 hidden md:table-cell" style="width: 30%;">Nama Lengkap</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 10%;">Role</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 10%;">Contact</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 10%;">Kelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap hidden md:table-cell" style="width: 10%;">Aksi</th>
                                    <!-- Mobile View: Combined User Column -->
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0 md:hidden" style="width: 30%;">User</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider md:hidden" style="width: 15%;">Contact</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider md:hidden" style="width: 12%;">Kelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap md:hidden" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                        <!-- Desktop View: Separate Columns -->
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                            <span class="font-mono text-xs sm:text-sm bg-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded">{{ $user->username }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 hidden md:table-cell">
                                            <span class="text-xs sm:text-sm text-gray-900 break-words" title="{{ $user->nama_lengkap }}">{{ $user->nama_lengkap }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 font-semibold rounded-full shadow-sm
                                                {{ $user->role === 'Admin' ? 'bg-gradient-to-r from-green-400 to-green-500 text-white' : ($user->role === 'TATIB' ? 'bg-gradient-to-r from-purple-400 to-purple-500 text-white' : 'bg-gradient-to-r from-blue-400 to-blue-500 text-white') }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm text-gray-700 text-left hidden md:table-cell">{{ $user->nomor_telepon ?? '-' }}</td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-700 text-right hidden md:table-cell">
                                            @if($user->kelas->count() > 0)
                                                <div class="flex flex-wrap gap-1 justify-end">
                                                    @foreach($user->kelas as $kelas)
                                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-indigo-100 text-indigo-700 rounded-md font-medium text-xs">
                                                            {{ $kelas->kelas }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-right hidden md:table-cell">
                                            <div class="flex flex-col items-end gap-1.5 sm:gap-2">
                                                <a href="{{ route('users.edit', $user->username) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200" title="Edit">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->username) }}" method="POST" class="inline-flex m-0">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200" onclick="return confirm('Yakin ingin menghapus user ini?')" title="Hapus">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            </div>
                                        </td>
                                        
                                        <!-- Mobile View: Combined User Column -->
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 md:hidden">
                                            <div class="flex items-start min-w-0 gap-2">
                                                <div class="bg-indigo-100 rounded-full p-1 sm:p-1.5 shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col min-w-0 flex-1 break-words gap-0.5">
                                                    <span class="font-semibold text-xs sm:text-sm text-gray-900 break-words leading-tight" title="{{ $user->nama_lengkap }}">{{ $user->nama_lengkap }}</span>
                                                    <span class="text-xs text-gray-500 font-mono break-words">{{ $user->username }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full
                                                            {{ $user->role === 'Admin' ? 'bg-green-100 text-green-700' : ($user->role === 'TATIB' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                                            {{ $user->role }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm text-gray-700 text-left md:hidden">{{ $user->nomor_telepon ?? '-' }}</td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-700 text-right md:hidden">
                                            @if($user->kelas->count() > 0)
                                                <div class="flex flex-wrap gap-1 justify-end">
                                                    @foreach($user->kelas as $kelas)
                                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-indigo-100 text-indigo-700 rounded-md font-medium text-center">
                                                            {{ $kelas->kelas }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-right md:hidden">
                                            <div class="flex flex-col items-end gap-1.5 sm:gap-2">
                                                <a href="{{ route('users.edit', $user->username) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200" title="Edit">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->username) }}" method="POST" class="inline-flex m-0">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200" onclick="return confirm('Yakin ingin menghapus user ini?')" title="Hapus">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center hidden md:table-cell">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada data user</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah User" untuk menambahkan data</p>
                                        </td>
                                        <td colspan="4" class="px-6 py-12 text-center md:hidden">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada data user</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah User" untuk menambahkan data</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>