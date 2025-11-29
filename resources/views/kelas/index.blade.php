<x-app-layout>
    <x-slot name="header">
        <!-- Mobile View - Left Aligned and Uppercase -->
        <div class="md:hidden">
            <div class="flex items-center">
                <div class="bg-white/25 backdrop-blur-md rounded-xl p-2.5 sm:p-3 mr-3 sm:mr-4 shrink-0 shadow-lg ring-2 ring-white/20">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white drop-shadow-2xl uppercase tracking-wide">
                    Kelas
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:block">
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Data Kelas
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola Data Kelas</p>
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

            <!-- Action Buttons -->
            <div class="mb-4 sm:mb-6 flex justify-center sm:justify-end">
                <a href="{{ route('kelas.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kelas
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="w-full overflow-hidden">
                        <table class="w-full divide-y divide-gray-200" style="table-layout: fixed; width: 100%;">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <!-- Desktop View -->
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0 hidden md:table-cell" style="width: 15%;">Kelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 50%;">Nama Walikelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 20%;">Jumlah Siswa</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap hidden md:table-cell" style="width: 15%;">Aksi</th>
                                    <!-- Mobile View -->
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0 md:hidden" style="width: 45%;">Kelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider md:hidden" style="width: 30%;">Jumlah Siswa</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap md:hidden" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($kelas as $kls)
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                        <!-- Desktop View -->
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 hidden md:table-cell">
                                            <div class="bg-indigo-100 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 inline-block">
                                                <span class="font-bold text-xs sm:text-sm text-indigo-700">{{ $kls->kelas }}</span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 hidden md:table-cell">
                                            <div class="flex items-center">
                                                <div class="bg-purple-100 rounded-full p-1 sm:p-1.5 mr-1.5 sm:mr-2 shrink-0">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs sm:text-sm font-bold text-gray-700 break-words">{{ $kls->walikelas->nama_lengkap ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                            <div class="flex items-center">
                                                <div class="bg-cyan-100 rounded-full p-1 sm:p-1.5 mr-1.5 sm:mr-2">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs sm:text-sm font-semibold text-gray-700">{{ $kls->murids_count }}</span>
                                                <span class="text-xs text-gray-500 ml-1">Siswa</span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-left hidden md:table-cell">
                                            <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                                                <a href="{{ route('kelas.edit', $kls->kelas) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200" title="Edit">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('kelas.destroy', $kls->kelas) }}" method="POST" class="inline-flex m-0">
                                                @csrf
                                                @method('DELETE')
                                                    @php
                                                        $canDelete = $kls->murids_count == 0 && empty($kls->username);
                                                    @endphp
                                                    <button type="submit" 
                                                        class="inline-flex items-center justify-center p-1.5 sm:p-2 rounded-lg transition-colors duration-200 {{ $canDelete ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed opacity-50' }}" 
                                                        onclick="{{ $canDelete ? "return confirm('Yakin ingin menghapus kelas ini?');" : 'return false;' }}"
                                                        {{ $canDelete ? '' : 'disabled' }}
                                                        title="{{ $canDelete ? 'Hapus kelas' : ($kls->murids_count > 0 ? 'Kelas tidak dapat dihapus karena masih memiliki murid' : 'Kelas tidak dapat dihapus karena masih memiliki walikelas') }}">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            </div>
                                        </td>
                                        
                                        <!-- Mobile View -->
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 md:hidden">
                                            <div class="flex items-center min-w-0 gap-2">
                                                <div class="bg-indigo-100 rounded-lg px-1.5 sm:px-2 py-1 sm:py-1.5 shrink-0">
                                                    <span class="font-bold text-xs sm:text-sm text-indigo-700">{{ $kls->kelas }}</span>
                                                </div>
                                                <div class="flex flex-col min-w-0 flex-1 break-words gap-0.5">
                                                    <div class="flex items-center">
                                                        <svg class="w-3 h-3 text-purple-600 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <span class="text-xs font-bold text-gray-700 break-words leading-tight">{{ $kls->walikelas->nama_lengkap ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left md:hidden">
                                            <div class="flex items-center">
                                                <div class="bg-cyan-100 rounded-full p-1.5 sm:p-2 mr-2 shrink-0">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-sm sm:text-base font-bold text-gray-700">{{ $kls->murids_count }}</span>
                                                <span class="text-sm sm:text-base text-gray-700">&nbspSiswa</span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-left md:hidden">
                                            <div class="flex flex-col items-end gap-1.5 sm:gap-2">
                                                <a href="{{ route('kelas.edit', $kls->kelas) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200" title="Edit">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('kelas.destroy', $kls->kelas) }}" method="POST" class="inline-flex m-0">
                                                @csrf
                                                @method('DELETE')
                                                    @php
                                                        $canDelete = $kls->murids_count == 0 && empty($kls->username);
                                                    @endphp
                                                    <button type="submit" 
                                                        class="inline-flex items-center justify-center p-1.5 sm:p-2 rounded-lg transition-colors duration-200 {{ $canDelete ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed opacity-50' }}" 
                                                        onclick="{{ $canDelete ? "return confirm('Yakin ingin menghapus kelas ini?');" : 'return false;' }}"
                                                        {{ $canDelete ? '' : 'disabled' }}
                                                        title="{{ $canDelete ? 'Hapus kelas' : ($kls->murids_count > 0 ? 'Kelas tidak dapat dihapus karena masih memiliki murid' : 'Kelas tidak dapat dihapus karena masih memiliki walikelas') }}">
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
                                        <td colspan="4" class="px-6 py-12 text-center hidden md:table-cell">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada data kelas</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Kelas" untuk menambahkan data</p>
                                        </td>
                                        <td colspan="3" class="px-6 py-12 text-center md:hidden">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium">Tidak ada data kelas</p>
                                            <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Kelas" untuk menambahkan data</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $kelas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>