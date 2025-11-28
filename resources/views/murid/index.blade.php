<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Data Siswa
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola Data Siswa</p>
            </div>
            <div class="flex flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('murid.import') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span class="hidden sm:inline">Import</span>
                    <span class="sm:hidden">Import</span>
                </a>
                <a href="{{ route('murid.create') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                    <span class="hidden sm:inline">Tambah Siswa</span>
                    <span class="sm:hidden">Tambah</span>
            </a>
            </div>
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

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 text-gray-900">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('murid.index') }}" class="mb-4 sm:mb-6 bg-gray-50 rounded-lg p-3 sm:p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Filter Kelas -->
                            <div>
                                <label for="kelas" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                                    Filter Kelas
                                </label>
                                <select name="kelas" id="kelas" class="block w-full px-2.5 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="">Semua Kelas</option>
                                    @foreach($allKelas as $kelas)
                                        <option value="{{ $kelas->kelas }}" {{ request('kelas') == $kelas->kelas ? 'selected' : '' }}>
                                            {{ $kelas->kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Nama Murid -->
                            <div>
                                <label for="nama" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">
                                    Cari Nama Siswa
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="nama" id="nama" value="{{ request('nama') }}" 
                                        placeholder="Cari nama siswa..." 
                                        class="block w-full pl-8 sm:pl-10 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row gap-2 sm:justify-end">
                            <a href="{{ route('murid.index') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                                Reset
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cari
                            </button>
                        </div>
                    </form>

                    <div class="w-full overflow-hidden">
                        <table class="w-full divide-y divide-gray-200" style="table-layout: fixed; width: 100%;">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0" style="width: 45%;">Nama Siswa</th>
                                        <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 15%;">NIS</th>
                                        <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell" style="width: 15%;">Jenis Kelamin</th>
                                        <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="width: 10%;">Kelas</th>
                                        <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($murids as $murid)
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0">
                                            <div class="flex items-start min-w-0">
                                                <div class="bg-indigo-100 rounded-full p-1 sm:p-1.5 mr-1.5 sm:mr-2 shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col min-w-0 flex-1 break-words">
                                                    <span class="font-medium text-xs sm:text-sm text-gray-900 break-words" title="{{ $murid->nama_lengkap }}">{{ $murid->nama_lengkap }}</span>
                                                    <span class="text-xs text-gray-500 md:hidden font-mono break-words mt-0.5">{{ $murid->NIS }}</span>
                                                    <span class="text-xs text-gray-500 lg:hidden mt-0.5">
                                                        <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full
                                                            {{ $murid->gender === 'Laki-laki' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                                            {{ $murid->gender }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                            <span class="font-mono text-xs sm:text-sm bg-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded">{{ $murid->NIS }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden lg:table-cell">
                                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 font-semibold rounded-full shadow-sm
                                                {{ $murid->gender === 'Laki-laki' ? 'bg-gradient-to-r from-blue-400 to-blue-500 text-white' : 'bg-gradient-to-r from-pink-400 to-pink-500 text-white' }}">
                                                {{ $murid->gender }}
                                            </span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3">
                                            <span class="bg-purple-100 text-purple-700 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg text-xs sm:text-sm font-semibold">{{ $murid->kelas }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                                                <a href="{{ route('murid.edit', $murid->NIS) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200" title="Edit">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('murid.destroy', $murid->NIS) }}" method="POST" class="inline-flex m-0">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200" onclick="return confirm('Yakin ingin menghapus murid ini?')" title="Hapus">
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
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada data siswa</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Siswa" untuk menambahkan data</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $murids->links() }}
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>