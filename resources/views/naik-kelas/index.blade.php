<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Naik Kelas
            </h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Proses kenaikan kelas dan kelulusan siswa untuk semua kelas</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Form Naik Kelas
                    </h3>
                </div>
                <div class="p-6 text-gray-900">
                    <form id="naikKelasForm" method="POST" action="{{ route('naik-kelas.process') }}">
                        @csrf
                        
                        <!-- Pertanyaan: Apakah ada murid yang tinggal kelas? -->
                        <div class="mb-6">
                            <label class="block text-base font-semibold text-gray-800 mb-4">
                                Apakah ada murid yang tinggal kelas?
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="ada_murid_tetap" value="ya" id="radioYa" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-3 text-gray-700 font-medium text-lg group-hover:text-indigo-600">Iya</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="ada_murid_tetap" value="tidak" id="radioTidak" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-3 text-gray-700 font-medium text-lg group-hover:text-indigo-600">Tidak</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('ada_murid_tetap')" class="mt-2" />
                        </div>

                        <!-- Section Pilih Murid Tetap (muncul jika pilih IYA) -->
                        <div id="muridTetapSection" class="hidden mb-6">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-yellow-800 mb-1">Pilih Murid Tetap (Tinggal Kelas)</h4>
                                        <p class="text-xs text-yellow-700">Centang murid dari semua kelas yang tidak naik kelas</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Daftar Murid dengan Checkbox -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm font-semibold text-gray-700">Pilih Semua</span>
                                    </label>
                                    <span class="text-sm text-gray-600">
                                        Total dipilih: <span id="selectedCount" class="font-semibold text-indigo-600">0</span> murid
                                    </span>
                                </div>
                                
                                <div class="space-y-2">
                                    @forelse($murids as $murid)
                                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                            <input type="checkbox" name="murid_tetap[]" value="{{ $murid->NIS }}" 
                                                class="murid-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-mono text-gray-600">{{ $murid->NIS }}</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $murid->nama_lengkap }}</span>
                                                    <span class="text-xs text-gray-500">{{ $murid->gender }}</span>
                                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-semibold">{{ $murid->kelas }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada murid aktif</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Info Proses -->
                        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-6 py-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="font-semibold text-blue-900 mb-1">Informasi Proses:</p>
                                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                                        <li>Semua murid di semua kelas akan dinaikkan/luluskan</li>
                                        <li>Kelas X-* akan naik ke XI-*</li>
                                        <li>Kelas XI-* akan naik ke XII-*</li>
                                        <li>Kelas XII-* akan lulus</li>
                                        <li id="excludeInfo" class="hidden">Murid yang dipilih sebagai murid tetap akan tetap di kelas yang sama</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="mt-6 pt-6 border-t">
                            <button type="submit" id="btnSubmit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Proses Naik Kelas / Lulus untuk Semua Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle radio button selection
        document.getElementById('radioYa').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('muridTetapSection').classList.remove('hidden');
                document.getElementById('excludeInfo').classList.remove('hidden');
                updateSelectedCount();
            }
        });

        document.getElementById('radioTidak').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('muridTetapSection').classList.add('hidden');
                document.getElementById('excludeInfo').classList.add('hidden');
                // Uncheck all checkboxes
                document.querySelectorAll('.murid-checkbox').forEach(cb => cb.checked = false);
                document.getElementById('selectAll').checked = false;
                updateSelectedCount();
            }
        });

        // Select All functionality
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.murid-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        // Update selected count
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.murid-checkbox:checked');
            const count = checkboxes.length;
            const countEl = document.getElementById('selectedCount');
            if (countEl) {
                countEl.textContent = count;
            }
            
            // Update select all checkbox
            const allCheckboxes = document.querySelectorAll('.murid-checkbox');
            if (selectAll && allCheckboxes.length > 0) {
                selectAll.checked = count === allCheckboxes.length;
            }
        }

        // Update count when checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('murid-checkbox')) {
                updateSelectedCount();
            }
        });

        // Form submit dengan konfirmasi
        document.getElementById('naikKelasForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const adaMuridTetap = document.querySelector('input[name="ada_murid_tetap"]:checked');
            if (!adaMuridTetap) {
                alert('Pilih terlebih dahulu apakah ada murid yang tinggal kelas');
                return;
            }

            let confirmMessage = `⚠️ PERINGATAN PENTING ⚠️\n\n`;
            confirmMessage += `Anda akan memproses naik kelas untuk SEMUA KELAS sekaligus.\n\n`;
            confirmMessage += `Proses yang akan dilakukan:\n`;
            confirmMessage += `• Kelas X-* akan naik ke XI-*\n`;
            confirmMessage += `• Kelas XI-* akan naik ke XII-*\n`;
            confirmMessage += `• Kelas XII-* akan lulus\n\n`;
            
            if (adaMuridTetap.value === 'ya') {
                const selectedCount = document.querySelectorAll('.murid-checkbox:checked').length;
                if (selectedCount === 0) {
                    alert('Anda memilih "Iya" tetapi belum memilih murid tetap. Silakan pilih murid tetap terlebih dahulu atau pilih "Tidak".');
                    return;
                }
                confirmMessage += `Murid yang akan naik: Semua murid kecuali ${selectedCount} murid tetap.\n\n`;
            } else {
                confirmMessage += `Semua murid di semua kelas akan naik/lulus.\n\n`;
            }
            
            confirmMessage += `⚠️ PERHATIAN: Setelah proses ini dilakukan, data TIDAK DAPAT dikembalikan!\n\n`;
            confirmMessage += `Apakah Anda yakin ingin melanjutkan?`;

            if (confirm(confirmMessage)) {
                // Double confirmation
                const doubleConfirm = confirm('Konfirmasi akhir: Apakah Anda benar-benar yakin? Proses ini akan mempengaruhi SEMUA KELAS dan TIDAK DAPAT DIBATALKAN!');
                if (doubleConfirm) {
                    this.submit();
                }
            }
        });
    </script>
</x-app-layout>
