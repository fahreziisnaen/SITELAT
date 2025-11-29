<x-app-layout>
    <x-slot name="header">
        <!-- Mobile View - Left Aligned and Uppercase -->
        <div class="md:hidden">
            <div class="flex items-center">
                <div class="bg-white/25 backdrop-blur-md rounded-xl p-2.5 sm:p-3 mr-3 sm:mr-4 shrink-0 shadow-lg ring-2 ring-white/20">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white drop-shadow-2xl uppercase tracking-wide">
                    Import Siswa
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight flex items-center">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Import Data Murid
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Import data murid dari file Excel (.xlsx) atau CSV</p>
            </div>
            <a href="{{ route('murid.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm sm:text-base font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
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

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 sm:px-6 py-3 sm:py-4">
                    <h3 class="text-base sm:text-lg font-semibold text-white flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload File Excel atau CSV
                    </h3>
                </div>
                <div class="p-4 sm:p-6">
                    <form method="POST" action="{{ route('murid.import.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih File Excel atau CSV <span class="text-red-500">*</span>
                            </label>
                            <div id="fileUploadArea" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors duration-200">
                                <div class="space-y-1 text-center w-full">
                                    <svg id="fileIcon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div id="fileUploadText" class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload file</span>
                                            <input id="file" name="file" type="file" accept=".xlsx,.csv,.txt" required class="sr-only" onchange="handleFileSelect(this)">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">Excel (.xlsx), CSV, TXT hingga 10MB</p>
                                    <!-- File Info Display -->
                                    <div id="fileInfo" class="hidden mt-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <svg class="h-5 w-5 text-indigo-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p id="fileName" class="text-sm font-medium text-indigo-900 truncate"></p>
                                                    <p id="fileSize" class="text-xs text-indigo-600"></p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="clearFile()" class="ml-2 text-indigo-600 hover:text-indigo-800 flex-shrink-0">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Format Info -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800 mb-2">Format File Excel atau CSV</h3>
                                    <div class="text-sm text-blue-700">
                                        <p class="mb-2">File Excel (.xlsx) atau CSV harus memiliki format berikut:</p>
                                        <div class="bg-white p-3 rounded border border-blue-200 font-mono text-xs overflow-x-auto">
                                            <table class="min-w-full">
                                                <thead>
                                                    <tr class="bg-gray-100">
                                                        <th class="px-3 py-2 text-left border">NIS</th>
                                                        <th class="px-3 py-2 text-left border">Nama Lengkap</th>
                                                        <th class="px-3 py-2 text-left border">Gender</th>
                                                        <th class="px-3 py-2 text-left border">Kelas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="px-3 py-2 border">1234567890</td>
                                                        <td class="px-3 py-2 border">Andi Prasetyo</td>
                                                        <td class="px-3 py-2 border">L</td>
                                                        <td class="px-3 py-2 border">X-1</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-3 py-2 border">1234567891</td>
                                                        <td class="px-3 py-2 border">Siti Nurhaliza</td>
                                                        <td class="px-3 py-2 border">P</td>
                                                        <td class="px-3 py-2 border">X-2</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <ul class="mt-3 list-disc list-inside space-y-1">
                                            <li>Baris pertama harus berisi header: <strong>NIS, Nama Lengkap, Gender, Kelas</strong></li>
                                            <li>Gender harus: <strong>L</strong> untuk Laki-laki atau <strong>P</strong> untuk Perempuan</li>
                                            <li>Kelas harus sudah ada di sistem (contoh: X-1, XI-2, XII-3)</li>
                                            <li>NIS harus unik (tidak boleh duplikat)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Download Template -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1">Download Template Excel</h4>
                                    <p class="text-xs text-gray-600">Gunakan template Excel (.xlsx) ini sebagai referensi format file</p>
                                </div>
                                <a href="{{ route('murid.import.template') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download Template
                                </a>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('murid.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                                Batal
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                const fileInfo = document.getElementById('fileInfo');
                const fileName = document.getElementById('fileName');
                const fileSize = document.getElementById('fileSize');
                const fileIcon = document.getElementById('fileIcon');
                const fileUploadText = document.getElementById('fileUploadText');
                const fileUploadArea = document.getElementById('fileUploadArea');

                // Show file info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.remove('hidden');
                
                // Update icon and text
                fileIcon.classList.add('text-indigo-600');
                fileUploadText.classList.add('hidden');
                fileUploadArea.classList.add('border-indigo-400', 'bg-indigo-50');
            }
        }

        function clearFile() {
            const fileInput = document.getElementById('file');
            const fileInfo = document.getElementById('fileInfo');
            const fileIcon = document.getElementById('fileIcon');
            const fileUploadText = document.getElementById('fileUploadText');
            const fileUploadArea = document.getElementById('fileUploadArea');

            fileInput.value = '';
            fileInfo.classList.add('hidden');
            fileIcon.classList.remove('text-indigo-600');
            fileUploadText.classList.remove('hidden');
            fileUploadArea.classList.remove('border-indigo-400', 'bg-indigo-50');
        }

        // Handle drag and drop
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('file');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, () => {
                fileUploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, () => {
                fileUploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
            }, false);
        });

        fileUploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        }, false);
    </script>
</x-app-layout>

