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
                                        <p class="text-xs text-yellow-700">Pilih murid dari semua kelas yang tidak naik kelas</p>
                                    </div>
                                    <button type="button" id="btnBukaModal" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        Buka Daftar
                                    </button>
                                </div>
                            </div>
                            
                            <div id="selectedMuridInfo" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold text-indigo-600" id="selectedCount">0</span> murid dipilih sebagai murid tetap (tinggal kelas)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Input untuk Murid Tetap -->
                        <div id="muridTetapInputs">
                            @if(old('murid_tetap'))
                                @foreach(old('murid_tetap') as $nis)
                                    <input type="hidden" name="murid_tetap[]" value="{{ $nis }}">
                                @endforeach
                            @endif
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

    <!-- Modal Pilih Murid Tetap -->
    <div id="modalMuridTetap" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-2xl bg-white max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Pilih Murid Tetap (Tinggal Kelas) - Semua Kelas
                </h3>
                <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="searchMurid" placeholder="Cari murid berdasarkan NIS, nama, atau kelas..." 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <!-- Daftar Murid -->
            <div class="flex-1 overflow-y-auto border border-gray-200 rounded-lg p-4">
                <div id="emptyState" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Ketik nama, NIS, atau kelas untuk mencari murid</p>
                </div>
                
                <div id="muridList" class="space-y-2 hidden">
                    @forelse($murids as $murid)
                        <label class="murid-item flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer" 
                            data-nis="{{ $murid->NIS }}" 
                            data-nama="{{ strtolower($murid->nama_lengkap) }}" 
                            data-kelas="{{ strtolower($murid->kelas) }}">
                            <input type="checkbox" name="murid_tetap[]" value="{{ $murid->NIS }}" 
                                class="murid-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
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
                
                <div id="noResults" class="hidden text-center py-8">
                    <p class="text-sm text-gray-500">Tidak ada murid yang ditemukan</p>
                </div>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Total dipilih: <span id="modalSelectedCountBottom" class="font-semibold text-indigo-600">0</span> murid
                </div>
                <div class="flex gap-3">
                    <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="button" id="saveMuridTetap" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                        Simpan Pilihan
                    </button>
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
            }
        });

        document.getElementById('radioTidak').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('muridTetapSection').classList.add('hidden');
                document.getElementById('excludeInfo').classList.add('hidden');
                document.getElementById('selectedMuridInfo').classList.add('hidden');
                // Clear all selections
                document.querySelectorAll('.murid-checkbox').forEach(cb => cb.checked = false);
                updateMuridTetapInputs();
                updateSelectedCount();
            }
        });

        // Open modal
        document.getElementById('btnBukaModal').addEventListener('click', function() {
            // Pastikan semua checkbox tidak tercentang dulu
            document.querySelectorAll('.murid-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Sync checkbox di modal dengan hidden inputs yang sudah ada (jika ada)
            const existingInputs = document.querySelectorAll('#muridTetapInputs input[name="murid_tetap[]"]');
            const selectedNIS = Array.from(existingInputs).map(input => input.value);
            
            // Update checkbox di modal - hanya centang yang sudah dipilih sebelumnya
            if (selectedNIS.length > 0) {
                document.querySelectorAll('.murid-checkbox').forEach(checkbox => {
                    if (selectedNIS.includes(checkbox.value)) {
                        checkbox.checked = true;
                    }
                });
            }
            
            // Reset search dan tampilkan empty state
            document.getElementById('searchMurid').value = '';
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('muridList').classList.add('hidden');
            document.getElementById('noResults').classList.add('hidden');
            
            document.getElementById('modalMuridTetap').classList.remove('hidden');
            updateModalSelectedCount();
            
            // Focus ke search box
            setTimeout(() => {
                document.getElementById('searchMurid').focus();
            }, 100);
        });

        // Close modal
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelModal').addEventListener('click', closeModal);

        function closeModal() {
            document.getElementById('modalMuridTetap').classList.add('hidden');
        }

        // Search functionality
        document.getElementById('searchMurid').addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            const items = document.querySelectorAll('.murid-item');
            const emptyState = document.getElementById('emptyState');
            const muridList = document.getElementById('muridList');
            const noResults = document.getElementById('noResults');
            
            // Jika search kosong, sembunyikan semua
            if (searchTerm === '') {
                emptyState.classList.remove('hidden');
                muridList.classList.add('hidden');
                noResults.classList.add('hidden');
                return;
            }
            
            // Tampilkan list, sembunyikan empty state
            emptyState.classList.add('hidden');
            muridList.classList.remove('hidden');
            
            let visibleCount = 0;
            
            items.forEach(item => {
                const nis = item.dataset.nis.toLowerCase();
                const nama = item.dataset.nama;
                const kelas = item.dataset.kelas;
                
                if (nis.includes(searchTerm) || nama.includes(searchTerm) || kelas.includes(searchTerm)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Tampilkan "tidak ada hasil" jika tidak ada yang match
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        });

        // Update count when checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('murid-checkbox')) {
                updateModalSelectedCount();
            }
        });

        // Update modal selected count
        function updateModalSelectedCount() {
            const checkboxes = document.querySelectorAll('.murid-checkbox:checked');
            const count = checkboxes.length;
            
            const countEl = document.getElementById('modalSelectedCount');
            const countElBottom = document.getElementById('modalSelectedCountBottom');
            if (countEl) countEl.textContent = count;
            if (countElBottom) countElBottom.textContent = count;
        }

        // Save murid tetap
        document.getElementById('saveMuridTetap').addEventListener('click', function() {
            closeModal();
            updateMuridTetapInputs();
            updateSelectedCount();
        });

        // Update hidden inputs untuk murid tetap
        function updateMuridTetapInputs() {
            const container = document.getElementById('muridTetapInputs');
            container.innerHTML = '';
            
            // Hanya ambil checkbox yang tercentang dan terlihat (tidak hidden)
            const checkedBoxes = Array.from(document.querySelectorAll('.murid-checkbox:checked')).filter(checkbox => {
                const item = checkbox.closest('.murid-item');
                return item && item.style.display !== 'none';
            });
            
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'murid_tetap[]';
                input.value = checkbox.value;
                container.appendChild(input);
            });
        }

        // Update info murid yang dipilih di form utama
        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.murid-checkbox:checked');
            const count = checkedBoxes.length;
            const infoSection = document.getElementById('selectedMuridInfo');
            const countEl = document.getElementById('selectedCount');
            
            if (count > 0) {
                infoSection.classList.remove('hidden');
                if (countEl) countEl.textContent = count;
            } else {
                infoSection.classList.add('hidden');
            }
        }

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
                const selectedInputs = document.querySelectorAll('input[name="murid_tetap[]"]');
                const selectedCount = selectedInputs.length;
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
