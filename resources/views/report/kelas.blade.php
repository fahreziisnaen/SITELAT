<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan Keterlambatan Siswa
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Export laporan keterlambatan untuk Walikelas </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                        Laporan Keterlambatan Siswa
                    </h3>
                </div>
                <div class="p-4 sm:p-6 text-gray-900">
                    <form method="GET" action="{{ route('report.kelas') }}" id="reportForm">
                        <!-- Tahun Ajaran -->
                        <div class="mb-6">
                            <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <select id="tahun" name="tahun" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                <option value="">Pilih Tahun Ajaran</option>
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    @php
                                        $tahunRangeValue = $y . '-' . ($y + 1);
                                        $tahunRangeLabel = $y . '-' . ($y + 1);
                                    @endphp
                                    <option value="{{ $tahunRangeValue }}" {{ old('tahun', $tahunRange) == $tahunRangeValue ? 'selected' : '' }}>{{ $tahunRangeLabel }}</option>
                                @endfor
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Laporan akan menampilkan data Semester 1 dan Semester 2</p>
                        </div>

                        <!-- Info Kelas yang Dipegang -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-800 mb-2">Anda adalah Walikelas:</h4>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $kelasWalikelas = \App\Models\Kelas::sortNatural(\App\Models\Kelas::where('username', auth()->user()->username)->get());
                                @endphp
                                @if($kelasWalikelas->isEmpty())
                                    <span class="text-sm text-gray-600">Tidak ada kelas yang dipegang</span>
                                @else
                                    @foreach($kelasWalikelas as $kelas)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                            {{ $kelas->kelas }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Generate Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

