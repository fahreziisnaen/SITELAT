<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Keterlambatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('keterlambatan.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Murid (Searchable Dropdown) -->
                        <div class="mb-4">
                            <x-input-label for="NIS" :value="__('Nama Murid')" />
                            <select id="NIS" name="NIS" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm select2-murid" required>
                                <option value="">Pilih Murid</option>
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
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <!-- Waktu -->
                        <div class="mb-4">
                            <x-input-label for="waktu" :value="__('Waktu')" />
                            <x-text-input id="waktu" class="block mt-1 w-full" type="time" name="waktu" :value="old('waktu', date('H:i'))" required />
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
                            <input id="bukti" type="file" name="bukti" accept="image/*" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                            <x-input-error :messages="$errors->get('bukti')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
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
                placeholder: 'Ketik untuk mencari murid...',
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
        });
    </script>
</x-app-layout>