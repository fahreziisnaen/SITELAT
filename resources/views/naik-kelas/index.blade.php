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
                        <div id="muridTetapInputs"></div>

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

            <!-- Tabel Murid -->
            <div class="flex-1 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Nama Lengkap</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis Kelamin</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Kelas</th>
                        </tr>
                    </thead>
                    <tbody id="muridTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Memuat data murid...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Total dipilih: <span id="modalSelectedCount" class="font-semibold text-indigo-600">0</span> murid
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
        let selectedMuridTetap = [];
        let allMurids = [];

        // Handle radio button selection
        document.getElementById('radioYa').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('muridTetapSection').classList.remove('hidden');
                document.getElementById('excludeInfo').classList.remove('hidden');
                document.getElementById('btnSubmit').disabled = true; // Disable sampai pilih murid
                // Jangan load data sekarang, tunggu sampai modal dibuka
                // loadAllMurids(); // Dihapus - akan dimuat saat modal dibuka
            }
        });

        document.getElementById('radioTidak').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('muridTetapSection').classList.add('hidden');
                document.getElementById('excludeInfo').classList.add('hidden');
                selectedMuridTetap = [];
                updateMuridTetapInputs();
                document.getElementById('btnSubmit').disabled = false; // Enable submit
            }
        });

        // Load semua murid aktif dari semua kelas
        function loadAllMurids() {
            // Show loading state - pastikan elemen ada
            const tbody = document.getElementById('muridTableBody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Memuat data murid...</td></tr>';
            }

            // Gunakan secure URL (HTTPS) untuk menghindari Mixed Content error
            // Jika halaman di-load via HTTPS, semua request harus HTTPS juga
            const url = '{{ secure_url(route('naik-kelas.get-murid-tetap')) }}';
            
            console.log('Fetching URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(response => {
                    // Check if response is ok
                    if (!response.ok) {
                        // Try to parse error response
                        return response.json().then(data => {
                            throw new Error(data.error || `HTTP error! status: ${response.status}`);
                        }).catch(() => {
                            throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        allMurids = data.murids;
                        // Pastikan modal sudah terbuka sebelum render
                        const modal = document.getElementById('modalMuridTetap');
                        if (modal && !modal.classList.contains('hidden')) {
                            renderMuridTable(data.murids);
                            updateModalSelectedCount();
                        }
                    } else {
                        // Handle backend error response
                        const errorMsg = data.error || 'Terjadi kesalahan saat memuat data murid.';
                        const tbody = document.getElementById('muridTableBody');
                        if (tbody) {
                            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">${errorMsg}</td></tr>`;
                        }
                        alert(errorMsg);
                    }
                })
                .catch(error => {
                    console.error('Error loading murid:', error);
                    console.error('Error name:', error.name);
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    console.error('URL yang dicoba:', url);
                    console.error('Current location:', window.location.href);
                    
                    let errorMsg = 'Terjadi kesalahan saat memuat data murid.';
                    
                    // Handle specific error types
                    if (error.name === 'TypeError' && (error.message.includes('Failed to fetch') || error.message.includes('NetworkError'))) {
                        errorMsg = 'Gagal terhubung ke server.\n\n' +
                                   'Kemungkinan penyebab:\n' +
                                   '1. Server tidak berjalan atau tidak merespons\n' +
                                   '2. Koneksi internet terputus\n' +
                                   '3. URL tidak valid atau tidak dapat diakses\n' +
                                   '4. Masalah dengan session/authentication\n\n' +
                                   'URL yang dicoba: ' + url + '\n\n' +
                                   'Silakan:\n' +
                                   '1. Refresh halaman (F5)\n' +
                                   '2. Cek koneksi internet\n' +
                                   '3. Cek console browser untuk detail error (F12)\n' +
                                   '4. Hubungi administrator jika masalah berlanjut';
                    } else if (error.message) {
                        errorMsg = error.message;
                    }
                    
                    const tbody = document.getElementById('muridTableBody');
                    if (tbody) {
                        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">
                            <div class="space-y-2">
                                <p class="font-semibold">${errorMsg.split('\n')[0]}</p>
                                <p class="text-xs text-gray-600">Cek console browser (F12) untuk detail error</p>
                            </div>
                        </td></tr>`;
                    }
                    alert(errorMsg);
                });
        }

        // Open modal
        document.getElementById('btnBukaModal').addEventListener('click', function() {
            // Buka modal dulu
            document.getElementById('modalMuridTetap').classList.remove('hidden');
            
            // Jika data sudah ada, render langsung
            if (allMurids.length > 0) {
                renderMuridTable(allMurids);
                updateModalSelectedCount();
            } else {
                // Jika belum ada, load data
                loadAllMurids();
                updateModalSelectedCount();
            }
        });

        // Render murid table
        function renderMuridTable(murids) {
            const tbody = document.getElementById('muridTableBody');
            tbody.innerHTML = '';

            if (murids.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada murid aktif</td></tr>';
                return;
            }

            murids.forEach(murid => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.dataset.nis = murid.NIS;
                
                const isSelected = selectedMuridTetap.includes(murid.NIS);
                
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <input type="checkbox" class="murid-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                            value="${murid.NIS}" ${isSelected ? 'checked' : ''}>
                    </td>
                    <td class="px-4 py-3 text-sm font-mono">${murid.NIS}</td>
                    <td class="px-4 py-3 text-sm font-medium">${murid.nama_lengkap}</td>
                    <td class="px-4 py-3 text-sm">${murid.gender}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-semibold">${murid.kelas}</span>
                    </td>
                `;
                
                tbody.appendChild(tr);
            });

            // Attach event listeners
            document.querySelectorAll('.murid-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        if (!selectedMuridTetap.includes(this.value)) {
                            selectedMuridTetap.push(this.value);
                        }
                    } else {
                        selectedMuridTetap = selectedMuridTetap.filter(nis => nis !== this.value);
                    }
                    updateSelectAll();
                    updateModalSelectedCount();
                });
            });

            updateSelectAll();
        }

        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.murid-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    if (!selectedMuridTetap.includes(checkbox.value)) {
                        selectedMuridTetap.push(checkbox.value);
                    }
                } else {
                    selectedMuridTetap = selectedMuridTetap.filter(nis => nis !== checkbox.value);
                }
            });
            updateModalSelectedCount();
        });

        function updateSelectAll() {
            const checkboxes = document.querySelectorAll('.murid-checkbox');
            const selectAll = document.getElementById('selectAll');
            if (checkboxes.length === 0) {
                selectAll.checked = false;
                return;
            }
            selectAll.checked = checkboxes.length === selectedMuridTetap.length && checkboxes.length > 0;
        }

        function updateModalSelectedCount() {
            document.getElementById('modalSelectedCount').textContent = selectedMuridTetap.length;
        }

        // Search functionality
        document.getElementById('searchMurid').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = allMurids.filter(murid => 
                murid.NIS.toLowerCase().includes(searchTerm) ||
                murid.nama_lengkap.toLowerCase().includes(searchTerm) ||
                (murid.kelas && murid.kelas.toLowerCase().includes(searchTerm))
            );
            renderMuridTable(filtered);
        });

        // Close modal
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelModal').addEventListener('click', closeModal);

        function closeModal() {
            document.getElementById('modalMuridTetap').classList.add('hidden');
        }

        // Save murid tetap
        document.getElementById('saveMuridTetap').addEventListener('click', function() {
            closeModal();
            updateMuridTetapInputs();
            updateSelectedMuridInfo();
            
            // Enable submit button setelah pilih murid
            document.getElementById('btnSubmit').disabled = false;
        });

        // Update hidden inputs untuk murid tetap
        function updateMuridTetapInputs() {
            const container = document.getElementById('muridTetapInputs');
            container.innerHTML = '';
            
            selectedMuridTetap.forEach(nis => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'murid_tetap[]';
                input.value = nis;
                container.appendChild(input);
            });
        }

        // Update info murid yang dipilih
        function updateSelectedMuridInfo() {
            const infoSection = document.getElementById('selectedMuridInfo');
            const countEl = document.getElementById('selectedCount');
            
            if (selectedMuridTetap.length > 0) {
                infoSection.classList.remove('hidden');
                countEl.textContent = selectedMuridTetap.length;
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
            
            if (adaMuridTetap.value === 'ya' && selectedMuridTetap.length > 0) {
                confirmMessage += `Murid yang akan naik: Semua murid kecuali ${selectedMuridTetap.length} murid tetap.\n\n`;
            } else if (adaMuridTetap.value === 'ya' && selectedMuridTetap.length === 0) {
                alert('Anda memilih "Iya" tetapi belum memilih murid tetap. Silakan pilih murid tetap terlebih dahulu atau pilih "Tidak".');
                return;
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
