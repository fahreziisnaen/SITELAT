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
                    Edit Keterlambatan
                </h2>
            </div>
        </div>
        
        <!-- Desktop View - Original Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                {{ __('Edit Data Keterlambatan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('keterlambatan.update', $keterlambatan->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nama Murid (Searchable Dropdown) -->
                        <div class="mb-4">
                            <x-input-label for="NIS" :value="__('Nama Siswa')" />
                            <select id="NIS" name="NIS" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm select2-murid" required>
                                <option value="">Pilih Siswa</option>
                                @foreach ($murids as $murid)
                                    <option value="{{ $murid->NIS }}" data-gender="{{ $murid->gender }}" {{ old('NIS', $keterlambatan->NIS) == $murid->NIS ? 'selected' : '' }}>
                                        {{ $murid->nama_lengkap }} - {{ $murid->kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('NIS')" class="mt-2" />
                        </div>

                        <!-- Jenis Kelamin (Auto-filled) -->
                        <div class="mb-4">
                            <x-input-label for="gender_display" :value="__('Jenis Kelamin')" />
                            <x-text-input id="gender_display" class="block mt-1 w-full bg-gray-100" type="text" :value="$keterlambatan->murid->gender ?? ''" readonly />
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-4">
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', $keterlambatan->tanggal->format('Y-m-d'))" required />
                            <p class="mt-1 text-sm text-gray-500">Format: DD/MM/YYYY (Tampilan: {{ $keterlambatan->tanggal->format('d/m/Y') }})</p>
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <!-- Waktu -->
                        <div class="mb-4">
                            <x-input-label for="waktu" :value="__('Waktu')" />
                            <x-text-input id="waktu" class="block mt-1 w-full" type="time" name="waktu" :value="old('waktu', $keterlambatan->waktu->format('H:i'))" required />
                            <x-input-error :messages="$errors->get('waktu')" class="mt-2" />
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <x-input-label for="keterangan" :value="__('Keterangan')" />
                            <textarea id="keterangan" name="keterangan" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('keterangan', $keterlambatan->keterangan) }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <!-- Bukti (Upload Gambar) -->
                        <div class="mb-4">
                            <x-input-label for="bukti" :value="__('Bukti (Gambar/Foto)')" />
                            @if($keterlambatan->bukti)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $keterlambatan->bukti) }}" alt="Bukti" class="h-32 w-auto">
                                    <p class="text-sm text-gray-500 mt-1">Bukti saat ini</p>
                                </div>
                            @endif
                            <input id="bukti" type="file" name="bukti" accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('bukti')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('keterlambatan.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

            // Trigger on page load
            if ($('#NIS').val()) {
                $('#NIS').trigger('change');
            }
        });
    </script>
</x-app-layout>