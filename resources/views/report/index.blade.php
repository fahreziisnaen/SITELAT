<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Report Keterlambatan
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Rekap dan analisis data keterlambatan siswa</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-md mb-6 flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 sm:px-6 py-3 sm:py-4">
                    <h3 class="text-base sm:text-lg font-semibold text-white flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter Report
                    </h3>
                </div>
                <div class="p-4 sm:p-6 text-gray-900">
                    <form method="GET" action="{{ route('report.index') }}" id="reportForm">
                        <div class="space-y-6">
                            <!-- Kelas -->
                            <div>
                                @if(Auth::user()->role === 'Walikelas')
                                    <!-- Untuk Walikelas: Tampilkan kelas yang dipegang sebagai informasi (read-only) -->
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        @php
                                            $userKelas = \App\Models\Kelas::where('username', Auth::user()->username)
                                                ->orderBy('kelas')
                                                ->pluck('kelas');
                                        @endphp
                                        <input type="text" value="{{ $userKelas->join(', ') ?: 'Tidak ada kelas' }}" readonly
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Kelas yang Anda pegang</p>
                                @else
                                    <!-- Untuk Admin dan TATIB: Dropdown untuk memilih kelas -->
                                <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-2">Kelas</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                        <select id="kelas" name="kelas"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                            <option value="semua" {{ (!$selectedKelas || $selectedKelas == 'semua') ? 'selected' : '' }}>Semua Kelas</option>
                                        @foreach ($kelas as $kls)
                                            <option value="{{ $kls->kelas }}" {{ $selectedKelas == $kls->kelas ? 'selected' : '' }}>
                                                {{ $kls->kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>

                            <!-- Jenis Laporan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Laporan</label>
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="jenis_laporan" value="bulanan" id="radioBulanan" 
                                            class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                            {{ $jenisLaporan == 'bulanan' ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 font-medium">Laporan Bulanan</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="jenis_laporan" value="semester" id="radioSemester"
                                            class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                            {{ $jenisLaporan == 'semester' ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 font-medium">Laporan Semester</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter Bulanan (muncul jika pilih Laporan Bulanan) -->
                            <div id="filterBulanan" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tahun -->
                                <div>
                                    <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <select id="tahun" name="tahun"
                                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                            <option value="">Pilih Tahun Ajaran</option>
                                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                @php
                                                    $tahunRangeValue = $y . '-' . ($y + 1);
                                                    $tahunRangeLabel = $y . '-' . ($y + 1);
                                                @endphp
                                                <option value="{{ $tahunRangeValue }}" {{ $tahunRange == $tahunRangeValue ? 'selected' : '' }}>{{ $tahunRangeLabel }}</option>
                                            @endfor
                                    </select>
                                </div>
                            </div>

                                <!-- Bulan -->
                            <div>
                                    <label for="bulan" class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                        <select id="bulan" name="bulan"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                            <option value="">Pilih Bulan</option>
                                            @php
                                                $bulanNama = [
                                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                                ];
                                            @endphp
                                            @foreach($bulanNama as $num => $nama)
                                                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Semester (muncul jika pilih Laporan Semester) -->
                            <div id="filterSemester" class="hidden">
                                <!-- Tahun -->
                            <div>
                                    <label for="tahun_semester" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                        <select id="tahun_semester" name="tahun"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                            <option value="">Pilih Tahun Ajaran</option>
                                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                @php
                                                    $tahunRangeValue = $y . '-' . ($y + 1);
                                                    $tahunRangeLabel = $y . '-' . ($y + 1);
                                                @endphp
                                                <option value="{{ $tahunRangeValue }}" {{ $tahunRange == $tahunRangeValue ? 'selected' : '' }}>{{ $tahunRangeLabel }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Laporan akan menampilkan data Semester 1 dan Semester 2</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" id="submitButton" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span id="buttonText">Tampilkan Report</span>
                            </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($report)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 no-print">
                        <div class="text-white">
                            <h3 class="text-lg font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Summary Keterlambatan
                            </h3>
                            <p class="text-sm text-green-100 mt-1">
                                @if(Auth::user()->role === 'Walikelas')
                                    @php
                                        $userKelasSummary = \App\Models\Kelas::where('username', Auth::user()->username)
                                            ->orderBy('kelas')
                                            ->pluck('kelas');
                                    @endphp
                                    Kelas: <strong>{{ $userKelasSummary->join(', ') }}</strong> |
                                @else
                                    Kelas: <strong>{{ (!$selectedKelas || $selectedKelas == 'semua') ? 'Semua Kelas' : $selectedKelas }}</strong> |
                                @endif
                                Periode: <strong>{{ $periodeLabel ?? (\Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y')) }}</strong>
                            </p>
                        </div>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print Report
                        </button>
                    </div>
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Lengkap</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jenis Kelamin</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Keterlambatan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($report as $index => $item)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $item['NIS'] ?? '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $item['nama_lengkap'] ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm
                                                    {{ $item['gender'] === 'Laki-laki' ? 'bg-gradient-to-r from-blue-400 to-blue-500 text-white' : (($item['gender'] === 'Perempuan') ? 'bg-gradient-to-r from-pink-400 to-pink-500 text-white' : 'bg-gray-400 text-white') }}">
                                                    {{ $item['gender'] ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg font-semibold">{{ $item['kelas'] ?? '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-4 py-2 inline-flex text-sm font-bold rounded-lg shadow-md
                                                    {{ $item['total_keterlambatan'] > 5 ? 'bg-gradient-to-r from-red-500 to-red-600 text-white' : ($item['total_keterlambatan'] > 2 ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white' : 'bg-gradient-to-r from-green-400 to-green-500 text-white') }}">
                                                    {{ $item['total_keterlambatan'] }} kali
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-gray-500 font-medium">Tidak ada data keterlambatan untuk periode ini</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Handle jenis laporan radio buttons
        document.getElementById('radioBulanan').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('filterBulanan').classList.remove('hidden');
                document.getElementById('filterSemester').classList.add('hidden');
                // Clear semester fields
                if (document.getElementById('tahun_semester')) {
                    document.getElementById('tahun_semester').value = '';
                }
                // Update button text
                document.getElementById('buttonText').textContent = 'Tampilkan Report';
            }
        });

        document.getElementById('radioSemester').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('filterSemester').classList.remove('hidden');
                document.getElementById('filterBulanan').classList.add('hidden');
                // Clear bulanan fields
                document.getElementById('bulan').value = '';
                // Update button text
                document.getElementById('buttonText').textContent = 'Export Excel';
            }
        });

        // Initialize on page load
        @if($jenisLaporan == 'bulanan')
            document.getElementById('filterBulanan').classList.remove('hidden');
            document.getElementById('filterSemester').classList.add('hidden');
            document.getElementById('buttonText').textContent = 'Tampilkan Report';
        @elseif($jenisLaporan == 'semester')
            document.getElementById('filterSemester').classList.remove('hidden');
            document.getElementById('filterBulanan').classList.add('hidden');
            document.getElementById('buttonText').textContent = 'Export Excel';
        @endif

        // Sync tahun fields (tahun ajaran)
        document.getElementById('tahun')?.addEventListener('change', function() {
            if (document.getElementById('tahun_semester')) {
                document.getElementById('tahun_semester').value = this.value;
            }
        });

        document.getElementById('tahun_semester')?.addEventListener('change', function() {
            if (document.getElementById('tahun')) {
                document.getElementById('tahun').value = this.value;
            }
        });
    </script>

    <style>
        @media print {
            nav, header button, .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</x-app-layout>
