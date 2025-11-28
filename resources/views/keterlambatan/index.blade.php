<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<x-app-layout>
    <x-slot name="header">
        <!-- Mobile View - Centered and Attractive -->
        <div class="md:hidden text-center">
            <div class="flex items-center justify-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-2.5 mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-white drop-shadow-lg">
                    Keterlambatan
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:block">
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Data Keterlambatan
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola data keterlambatan siswa</p>
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
            @if(Auth::user()->role !== 'Walikelas')
                <div class="mb-4 sm:mb-6 flex justify-center sm:justify-end">
                    <a href="{{ route('keterlambatan.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Data Keterlambatan
                    </a>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-lg overflow-hidden mb-4 sm:mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-3 sm:px-4 py-2 sm:py-2.5">
                    <h3 class="text-sm sm:text-base font-semibold text-white flex items-center">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter Data
                    </h3>
                </div>
                <div class="p-3 sm:p-4">
                    <form method="GET" action="{{ route('keterlambatan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                        <!-- Filter Tanggal -->
                        <div>
                            <label for="tanggal" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Tanggal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @php
                                    $tanggalValue = '';
                                    if (isset($tanggal) && $tanggal) {
                                        $tanggalValue = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal)->format('d/m/Y');
                                    }
                                @endphp
                                <input id="tanggal" type="text" name="tanggal_display" value="{{ $tanggalValue }}" placeholder="Pilih tanggal..."
                                    class="block w-full pl-8 sm:pl-10 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                                <input type="hidden" id="tanggal_hidden" name="tanggal" value="{{ $tanggal ?? '' }}">
                            </div>
                        </div>

                        <!-- Filter Nama Murid -->
                        <div>
                            <label for="nama_murid" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5 sm:mb-2">Nama Siswa</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input id="nama_murid" type="text" name="nama_murid" value="{{ $namaMurid ?? '' }}" placeholder="Cari nama siswa..."
                                    class="block w-full pl-8 sm:pl-10 pr-2.5 sm:pr-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200">
                            </div>
                        </div>

                        <!-- Tombol Filter -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs sm:text-sm font-semibold rounded-lg shadow hover:shadow-md transform hover:scale-105 transition-all duration-200">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filter
                            </button>
                            @if($tanggal || $namaMurid)
                                <a href="{{ route('keterlambatan.index') }}" class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 min-w-full" style="table-layout: fixed; width: 100%;">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="width: 20%; min-width: 80px;">Tanggal</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-0" style="width: 35%; min-width: 120px;">Nama Siswa</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell" style="width: 10%;">NIS</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell" style="width: 10%;">Jenis Kelamin</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider" style="width: 15%; min-width: 60px;">Kelas</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell min-w-0" style="width: 20%;">Keterangan</th>
                                    <th class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="width: 20%; min-width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($keterlambatan as $item)
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span class="font-medium text-xs sm:text-sm text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</span>
                                                </div>
                                                <div class="flex items-center mt-0.5">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="font-medium text-xs sm:text-sm text-red-600">{{ $item->waktu->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0">
                                            <div class="flex flex-col min-w-0 break-words">
                                                <span class="font-medium text-xs sm:text-sm text-gray-900 break-words" title="{{ $item->nama_murid ?? '-' }}">{{ $item->nama_murid ?? '-' }}</span>
                                                <span class="text-xs text-gray-500 md:hidden">{{ $item->NIS ?? '-' }}</span>
                                                <span class="text-xs text-gray-500 lg:hidden mt-0.5">
                                                    <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full
                                                        {{ $item->gender === 'Laki-laki' ? 'bg-blue-100 text-blue-700' : (($item->gender === 'Perempuan') ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-700') }}">
                                                        {{ $item->gender ?? '-' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden md:table-cell">
                                            <span class="font-mono text-xs sm:text-sm bg-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded">{{ $item->NIS ?? '-' }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap hidden lg:table-cell">
                                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 font-semibold rounded-full shadow-sm
                                                {{ $item->gender === 'Laki-laki' ? 'bg-gradient-to-r from-blue-400 to-blue-500 text-white' : (($item->gender === 'Perempuan') ? 'bg-gradient-to-r from-pink-400 to-pink-500 text-white' : 'bg-gray-400 text-white') }}">
                                                {{ $item->gender ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3">
                                            <span class="bg-purple-100 text-purple-700 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-lg text-xs sm:text-sm font-semibold">{{ $item->kelas ?? '-' }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 min-w-0 hidden lg:table-cell">
                                            <span class="text-xs sm:text-sm text-gray-700 break-words block">{{ $item->keterangan ?? '-' }}</span>
                                        </td>
                                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 whitespace-nowrap text-right">
                                            @if($item->bukti || Auth::user()->role !== 'Walikelas')
                                                <div class="flex items-center justify-end gap-1.5 sm:gap-2">
                                                    @if($item->bukti)
                                                        <button type="button" onclick="openBuktiModal('{{ asset('storage/' . $item->bukti) }}')" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors duration-200 shrink-0" title="Lihat Bukti">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if(Auth::user()->role !== 'Walikelas')
                                                        <a href="{{ route('keterlambatan.edit', $item->id) }}" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200 shrink-0" title="Edit">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('keterlambatan.destroy', $item->id) }}" method="POST" class="inline-flex m-0 shrink-0">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center justify-center p-1.5 sm:p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200" onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
                                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-2 sm:px-4 md:px-6 py-6 sm:py-8 md:py-12 text-center w-full">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mx-auto text-gray-300 mb-2 sm:mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-gray-500 font-medium text-xs sm:text-sm md:text-base px-2">Tidak ada data keterlambatan</p>
                                                <p class="text-gray-400 text-xs sm:text-sm mt-1 px-3 max-w-xs sm:max-w-none">Klik tombol "Tambah Data Keterlambatan" untuk menambahkan data</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    <div class="mt-6">
                        {{ $keterlambatan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Bukti -->
    <div id="buktiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeBuktiModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Preview Bukti Keterlambatan</h3>
                        <button type="button" onclick="closeBuktiModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-4">
                        <img id="buktiImage" src="" alt="Bukti Keterlambatan" class="max-w-full h-auto mx-auto rounded-lg shadow-lg">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeBuktiModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        function openBuktiModal(imageUrl) {
            document.getElementById('buktiImage').src = imageUrl;
            document.getElementById('buktiModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeBuktiModal() {
            document.getElementById('buktiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBuktiModal();
            }
        });

        // Initialize Flatpickr for date filter
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalInput = document.getElementById('tanggal');
            const tanggalHidden = document.getElementById('tanggal_hidden');
            
            if (tanggalInput && tanggalHidden) {
                // Get initial value
                let initialDateValue = '';
                if (tanggalHidden.value) {
                    try {
                        const date = new Date(tanggalHidden.value + 'T00:00:00');
                        if (!isNaN(date.getTime())) {
                            initialDateValue = tanggalHidden.value;
                        }
                    } catch (e) {
                        // Invalid date, use empty
                    }
                }
                
                const flatpickrInstance = flatpickr(tanggalInput, {
                    dateFormat: 'd/m/Y',
                    locale: 'id',
                    defaultDate: initialDateValue || null,
                    allowInput: false,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Convert DD/MM/YYYY to YYYY-MM-DD for hidden input
                        if (selectedDates.length > 0) {
                            const date = selectedDates[0];
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            tanggalHidden.value = year + '-' + month + '-' + day;
                        } else {
                            tanggalHidden.value = '';
                        }
                    }
                });
                
                // Update hidden input on form submit
                const form = document.querySelector('form[method="GET"]');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (flatpickrInstance.selectedDates.length > 0) {
                            const date = flatpickrInstance.selectedDates[0];
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            tanggalHidden.value = year + '-' + month + '-' + day;
                        } else {
                            tanggalHidden.value = '';
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>