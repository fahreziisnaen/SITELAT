<x-app-layout>
    <x-slot name="header">
        <!-- Mobile View - Left Aligned and Uppercase -->
        <div class="md:hidden">
            <div class="flex items-center">
                <div class="bg-white/25 backdrop-blur-md rounded-xl p-2.5 sm:p-3 mr-3 sm:mr-4 shrink-0 shadow-lg ring-2 ring-white/20">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white drop-shadow-2xl uppercase tracking-wide">
                    Tambah Keterlambatan
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                {{ __('Tambah Data Keterlambatan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('keterlambatan.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Murid (Searchable Dropdown) -->
                        <div class="mb-4">
                            <x-input-label for="NIS" :value="__('Nama Siswa')" />
                            <select id="NIS" name="NIS" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm select2-murid" required>
                                <option value="">Pilih Siswa</option>
                                @foreach ($murids as $murid)
                                    <option value="{{ $murid->NIS }}" data-gender="{{ $murid->gender }}" {{ old('NIS') == $murid->NIS ? 'selected' : '' }}>
                                        {{ $murid->nama_lengkap }} - {{ $murid->kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('NIS')" class="mt-2" />
                        </div>

                        <!-- Jenis Kelamin (Auto-filled) -->
                        <div class="mb-4">
                            <x-input-label for="gender_display" :value="__('Jenis Kelamin')" />
                            <x-text-input id="gender_display" class="block mt-1 w-full bg-gray-100" type="text" readonly />
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-4">
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            @php
                                // Hanya gunakan nilai lama jika ada (dari validation error)
                                $tanggalHiddenValue = old('tanggal');
                                $hasOldValue = old('tanggal') !== null;
                            @endphp
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="text" name="tanggal_display" placeholder="DD/MM/YYYY" required />
                            <input type="hidden" id="tanggal_hidden" name="tanggal" value="{{ $tanggalHiddenValue }}" required />
                            <input type="hidden" id="has_old_value" value="{{ $hasOldValue ? '1' : '0' }}" />
                            <p class="mt-1 text-sm text-gray-500">Format: DD/MM/YYYY</p>
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <!-- Waktu -->
                        <div class="mb-4">
                            <x-input-label for="waktu" :value="__('Waktu')" />
                            <x-text-input id="waktu" class="block mt-1 w-full" type="time" name="waktu" :value="old('waktu')" required />
                            <x-input-error :messages="$errors->get('waktu')" class="mt-2" />
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <x-input-label for="keterangan" :value="__('Keterangan')" />
                            <textarea id="keterangan" name="keterangan" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <!-- Bukti (Upload Gambar) -->
                        <div class="mb-4">
                            <x-input-label for="bukti" :value="__('Bukti (Gambar/Foto)')" />
                            
                            <!-- Camera Modal -->
                            <div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                                <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800">Ambil Foto</h3>
                                        <button type="button" id="closeCamera" class="text-gray-500 hover:text-gray-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="relative bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio: 4/3;">
                                        <video id="video" autoplay playsinline class="w-full h-full object-cover"></video>
                                        <img id="capturedPreview" src="" alt="Captured Photo" class="hidden w-full h-full object-cover">
                                        <canvas id="canvas" class="hidden"></canvas>
                                    </div>
                                    <div class="flex gap-3 justify-center">
                                        <button type="button" id="captureBtn" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Ambil Foto
                                        </button>
                                        <button type="button" id="retakeBtn" class="hidden px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                            Ambil Ulang
                                        </button>
                                        <button type="button" id="usePhotoBtn" class="hidden px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                            Gunakan Foto Ini
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Image -->
                            <div id="imagePreview" class="hidden mb-3">
                                <img id="previewImg" src="" alt="Preview" class="max-w-full h-auto max-h-64 rounded-lg border border-gray-300 shadow-sm">
                                <button type="button" id="removeImage" class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                                    Hapus Foto
                                </button>
                            </div>

                            <!-- Camera/File Input -->
                            <div id="cameraButtons" class="flex gap-2 mb-2">
                                <button type="button" id="openCameraBtn" class="flex-1 flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Ambil Foto dari Kamera
                                </button>
                                <label for="bukti-gallery" class="flex-1 cursor-pointer">
                                    <input id="bukti-gallery" type="file" accept="image/*" class="hidden" />
                                    <div class="flex items-center justify-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Pilih dari Galeri
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Hidden file input for form submission -->
                            <input id="bukti" type="file" name="bukti" accept="image/*" class="hidden" />
                            
                            <x-input-error :messages="$errors->get('bukti')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB. Klik "Ambil Foto dari Kamera" untuk langsung memfoto siswa.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('keterlambatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with search
            $('.select2-murid').select2({
                placeholder: 'Ketik untuk mencari siswa...',
                allowClear: true,
                width: '100%'
            });

            // Update gender when murid is selected
            $('#NIS').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const gender = selectedOption.data('gender');
                $('#gender_display').val(gender || '');
            });

            // Trigger on page load if there's an old value
            if ($('#NIS').val()) {
                $('#NIS').trigger('change');
            }

            // Initialize Flatpickr for date picker with Indonesian format (gunakan waktu device)
            const tanggalInput = document.getElementById('tanggal');
            const tanggalHidden = document.getElementById('tanggal_hidden');
            const hasOldValueInput = document.getElementById('has_old_value');
            const hasOldValue = hasOldValueInput && hasOldValueInput.value === '1';
            
            // Always use today's date as default
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Set to start of day to avoid timezone issues
            
            // Get today's date string
            const todayYear = today.getFullYear();
            const todayMonth = String(today.getMonth() + 1).padStart(2, '0');
            const todayDay = String(today.getDate()).padStart(2, '0');
            const todayString = todayYear + '-' + todayMonth + '-' + todayDay;
            
            // Only use old value if it exists from validation error, otherwise always use today
            let initialDateValue;
            if (hasOldValue) {
                // Use old value from validation error
                initialDateValue = tanggalHidden.value;
            } else {
                // Always use today - force update hidden input
                initialDateValue = todayString;
                tanggalHidden.value = todayString;
            }
            
            // Initialize Flatpickr with today's date (or old value if exists)
            const flatpickrInstance = flatpickr(tanggalInput, {
                dateFormat: 'd/m/Y',
                locale: 'id',
                defaultDate: initialDateValue,
                allowInput: false, // Prevent manual input to avoid format issues
                onChange: function(selectedDates, dateStr, instance) {
                    // Convert DD/MM/YYYY to YYYY-MM-DD for hidden input
                    if (selectedDates.length > 0) {
                        const date = selectedDates[0];
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        tanggalHidden.value = year + '-' + month + '-' + day;
                    }
                }
            });
            
            // Force set today (device) after initialization jika tidak ada old value
            if (!hasOldValue) {
                // Immediately set to today
                flatpickrInstance.setDate(today, false);
                tanggalHidden.value = todayString;
                
                // Also set after a short delay to ensure it's applied
                setTimeout(function() {
                    flatpickrInstance.setDate(today, false);
                    tanggalHidden.value = todayString;
                }, 50);
            }

            // Set default waktu berdasarkan waktu device jika tidak ada old value
            const waktuInput = document.getElementById('waktu');
            if (waktuInput && !waktuInput.value) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                waktuInput.value = hours + ':' + minutes;
            }

            // Handle image preview and camera/gallery selection
            const buktiInput = document.getElementById('bukti');
            const buktiGalleryInput = document.getElementById('bukti-gallery');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');
            const openCameraBtn = document.getElementById('openCameraBtn');
            const cameraModal = document.getElementById('cameraModal');
            const closeCameraBtn = document.getElementById('closeCamera');
            const video = document.getElementById('video');
            const capturedPreview = document.getElementById('capturedPreview');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');
            const retakeBtn = document.getElementById('retakeBtn');
            const usePhotoBtn = document.getElementById('usePhotoBtn');
            const cameraButtons = document.getElementById('cameraButtons');
            
            let stream = null;
            let capturedImage = null;
            let capturedImageUrl = null;

            function handleFileSelect(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        // Hide camera buttons after file is selected
                        cameraButtons.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                    
                    // Set file to main input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    buktiInput.files = dataTransfer.files;
                }
            }

            // Open camera
            openCameraBtn.addEventListener('click', async function() {
                try {
                    // Request camera access
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'environment' // Prefer back camera on mobile
                        } 
                    });
                    
                    video.srcObject = stream;
                    cameraModal.classList.remove('hidden');
                    captureBtn.classList.remove('hidden');
                    retakeBtn.classList.add('hidden');
                    usePhotoBtn.classList.add('hidden');
                } catch (error) {
                    console.error('Error accessing camera:', error);
                    alert('Tidak dapat mengakses kamera. Pastikan kamera tersedia dan izin telah diberikan. Silakan gunakan "Pilih dari Galeri" sebagai alternatif.');
                }
            });

            // Close camera
            closeCameraBtn.addEventListener('click', function() {
                stopCamera();
                cameraModal.classList.add('hidden');
            });

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                video.srcObject = null;
            }

            // Capture photo
            captureBtn.addEventListener('click', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                
                // Stop video stream
                stopCamera();
                
                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        console.error('Failed to create blob from canvas');
                        return;
                    }
                    
                    capturedImage = blob;
                    
                    // Create object URL for preview
                    if (capturedImageUrl) {
                        URL.revokeObjectURL(capturedImageUrl);
                    }
                    capturedImageUrl = URL.createObjectURL(blob);
                    
                    // Show preview in modal (hide video, show captured image)
                    video.classList.add('hidden');
                    capturedPreview.src = capturedImageUrl;
                    capturedPreview.classList.remove('hidden');
                    
                    // Show buttons
                    captureBtn.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');
                    usePhotoBtn.classList.remove('hidden');
                }, 'image/jpeg', 0.9);
            });

            // Retake photo
            retakeBtn.addEventListener('click', async function() {
                // Hide captured preview
                capturedPreview.classList.add('hidden');
                capturedPreview.src = '';
                video.classList.remove('hidden');
                
                // Revoke old URL
                if (capturedImageUrl) {
                    URL.revokeObjectURL(capturedImageUrl);
                    capturedImageUrl = null;
                }
                capturedImage = null;
                
                try {
                    // Restart camera
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'environment'
                        } 
                    });
                    
                    video.srcObject = stream;
                    captureBtn.classList.remove('hidden');
                    retakeBtn.classList.add('hidden');
                    usePhotoBtn.classList.add('hidden');
                } catch (error) {
                    console.error('Error accessing camera:', error);
                    alert('Tidak dapat mengakses kamera. Silakan tutup modal dan coba lagi.');
                }
            });

            // Use photo
            usePhotoBtn.addEventListener('click', function() {
                if (capturedImage) {
                    // Create file from blob
                    const file = new File([capturedImage], 'camera-photo.jpg', { type: 'image/jpeg' });
                    
                    // Set to input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    buktiInput.files = dataTransfer.files;
                    
                    // Show preview outside modal (use the same URL)
                    previewImg.src = capturedImageUrl;
                    imagePreview.classList.remove('hidden');
                    
                    // Hide camera buttons after photo is used
                    cameraButtons.classList.add('hidden');
                    
                    // Cleanup and close modal
                    stopCamera();
                    cameraModal.classList.add('hidden');
                    
                    // Reset modal state
                    video.classList.remove('hidden');
                    capturedPreview.classList.add('hidden');
                    capturedPreview.src = '';
                    captureBtn.classList.remove('hidden');
                    retakeBtn.classList.add('hidden');
                    usePhotoBtn.classList.add('hidden');
                    
                    // Note: Don't revoke URL yet, we need it for preview
                    // It will be revoked when user removes image or submits form
                }
            });

            // Gallery input
            buktiGalleryInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imagePreview.classList.add('hidden');
                if (previewImg.src) {
                    URL.revokeObjectURL(previewImg.src);
                }
                previewImg.src = '';
                buktiInput.value = '';
                buktiGalleryInput.value = '';
                if (capturedImageUrl) {
                    URL.revokeObjectURL(capturedImageUrl);
                    capturedImageUrl = null;
                }
                capturedImage = null;
                
                // Show camera buttons again after image is removed
                cameraButtons.classList.remove('hidden');
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                stopCamera();
                if (capturedImageUrl) {
                    URL.revokeObjectURL(capturedImageUrl);
                }
            });
            
            // Cleanup when closing camera modal
            closeCameraBtn.addEventListener('click', function() {
                if (capturedImageUrl) {
                    URL.revokeObjectURL(capturedImageUrl);
                    capturedImageUrl = null;
                }
                capturedImage = null;
                video.classList.remove('hidden');
                capturedPreview.classList.add('hidden');
                capturedPreview.src = '';
            });

            // Ensure hidden input is set before form submit
            document.querySelector('form').addEventListener('submit', function(e) {
                // If flatpickr has a selected date but hidden input is empty, set it
                if (!tanggalHidden.value && flatpickrInstance.selectedDates.length > 0) {
                    const date = flatpickrInstance.selectedDates[0];
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    tanggalHidden.value = year + '-' + month + '-' + day;
                }
            });
        });
    </script>
</x-app-layout>