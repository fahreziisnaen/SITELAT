<x-app-layout>
    <x-slot name="header">
        <!-- Mobile View - Left Aligned and Uppercase -->
        <div class="md:hidden">
            <div class="flex items-center">
                <div class="bg-white/25 backdrop-blur-md rounded-xl p-2.5 sm:p-3 mr-3 sm:mr-4 shrink-0 shadow-lg ring-2 ring-white/20">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white drop-shadow-2xl uppercase tracking-wide">
                    Dashboard
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div class="text-xs sm:text-sm text-gray-600">
                <span class="font-medium">{{ Auth::user()->nama_lengkap }}</span> •
                <span class="text-indigo-600">{{ Auth::user()->role }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-3 sm:px-6 py-3 sm:py-4 rounded-lg shadow-md mb-4 sm:mb-6 flex items-center text-sm" role="alert">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl sm:rounded-2xl shadow-xl p-3 sm:p-6 md:p-8 mb-4 sm:mb-6 md:mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold mb-1 sm:mb-2">Selamat Datang, {{ Auth::user()->nama_lengkap }}! 👋</h3>
                        <p class="text-indigo-100 text-xs sm:text-sm md:text-base lg:text-lg">di Sistem Keterlambatan Siswa</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-20 h-20 lg:w-28 lg:h-28 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-2 sm:gap-3 md:gap-4 lg:gap-6 mb-4 sm:mb-6 md:mb-8">
                <!-- Total Keterlambatan (Nama Bulan) -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-lg p-3 sm:p-4 md:p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-0.5 sm:mb-1">Total Keterlambatan {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['keterlambatan_bulan_ini'] ?? 0 }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Keterlambatan Hari Ini (Nama Hari) -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-lg p-3 sm:p-4 md:p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-0.5 sm:mb-1">Total Keterlambatan Hari Ini - {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l') }}</p>
                            <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['keterlambatan_hari_ini'] ?? 0 }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-2 sm:p-3">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-6 mb-4 sm:mb-6 md:mb-8">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-3 gap-2 sm:gap-3 md:gap-4">
                    <a href="{{ route('keterlambatan.create') }}" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-300 group">
                        <div class="bg-blue-500 rounded-lg p-2 sm:p-3 mb-2 sm:mb-0 sm:mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="font-semibold text-xs sm:text-sm md:text-base text-gray-800">Buat Keterlambatan</p>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Membuat Keterlambatan Siswa</p>
                        </div>
                    </a>

                    <a href="{{ route('report.index') }}" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start p-3 sm:p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-300 group">
                        <div class="bg-green-500 rounded-lg p-2 sm:p-3 mb-2 sm:mb-0 sm:mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="font-semibold text-xs sm:text-sm md:text-base text-gray-800">Laporan Semester</p>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Rekap Keterlambatan Siswa</p>
                        </div>
                    </a>

                    <a href="{{ route('murid.index') }}" class="flex flex-col sm:flex-row items-center justify-center sm:justify-start p-3 sm:p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-300 group">
                        <div class="bg-purple-500 rounded-lg p-2 sm:p-3 mb-2 sm:mb-0 sm:mr-3 sm:mr-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="font-semibold text-xs sm:text-sm md:text-base text-gray-800">Data Siswa<br>Aktif</p>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Kelola Data Siswa</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Top 5 Murid dengan Keterlambatan Terbanyak -->
            <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-6 mb-4 sm:mb-6 md:mb-8">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Siswa dengan Keterlambatan Terbanyak ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }})
                </h3>
                <div class="w-full overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 min-w-full" style="table-layout: fixed; width: 100%;">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0" style="width: 50%; min-width: 120px;">Nama Siswa</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 15%;">NIS</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="width: 20%; min-width: 60px;">Kelas</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="width: 30%; min-width: 70px;">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stats['top_keterlambatan'] ?? [] as $item)
                                <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                    <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0">
                                        <div class="flex flex-col min-w-0 break-words">
                                            <span class="font-medium text-xs sm:text-sm text-gray-900 break-words" title="{{ $item->nama_murid ?? '-' }}">{{ $item->nama_murid ?? '-' }}</span>
                                            <span class="text-xs text-gray-500 md:hidden mt-0.5">{{ $item->NIS ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                        <span class="font-mono text-xs sm:text-sm bg-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded">{{ $item->NIS ?? '-' }}</span>
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3">
                                        <span class="bg-purple-100 text-purple-700 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg text-xs sm:text-sm font-semibold">{{ $item->kelas ?? '-' }}</span>
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-semibold bg-red-100 text-red-700 ml-auto">
                                            {{ $item->total_keterlambatan ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 sm:px-3 md:px-6 py-6 sm:py-8 md:py-12 text-center w-full">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mx-auto text-gray-300 mb-2 sm:mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium text-xs sm:text-sm md:text-base px-2">Belum ada data keterlambatan bulan ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 md:p-6">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Keterlambatan Terbaru
                </h3>
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-full" style="table-layout: fixed; width: 100%;">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 25%; min-width: 80px;">Tanggal</th>
                                <th class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-0" style="width: 40%; min-width: 120px;">Nama Siswa</th>
                                <th class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 18%; min-width: 60px;">Kelas</th>
                                <th class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 17%; min-width: 60px;">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stats['recent_keterlambatan'] ?? [] as $item)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 min-w-0">
                                        <span class="text-xs sm:text-sm font-medium text-gray-900 break-words block" title="{{ $item->nama_murid ?? '-' }}">{{ $item->nama_murid ?? '-' }}</span>
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">{{ $item->kelas ?? '-' }}</td>
                                    <td class="px-2 sm:px-3 md:px-4 lg:px-6 py-2 sm:py-3 md:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">{{ $item->waktu->format('H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-2 sm:px-3 md:px-6 py-6 sm:py-8 md:py-12 text-center w-full">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mx-auto text-gray-300 mb-2 sm:mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="text-gray-500 font-medium text-xs sm:text-sm md:text-base px-2">Belum ada data keterlambatan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 sm:mt-4 text-center">
                    <a href="{{ route('keterlambatan.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs sm:text-sm">
                        Lihat Semua Keterlambatan →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
