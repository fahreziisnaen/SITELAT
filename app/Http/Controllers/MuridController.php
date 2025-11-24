<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class MuridController extends Controller
{
    /**
     * Check if user is Walikelas and block access
     */
    private function checkWalikelasAccess()
    {
        $user = auth()->user();
        if ($user && $user->role === 'Walikelas') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $murids = Murid::with('kelasRelation')->orderBy('nama_lengkap')->paginate(10);

        return view('murid.index', compact('murids'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $kelas = Kelas::orderBy('kelas')->get();

        return view('murid.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'NIS' => ['required', 'string', 'max:255', 'unique:murid'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'kelas' => ['required', 'exists:kelas,kelas'],
        ]);

        // Validasi: maksimal 40 murid per kelas
        $muridCount = Murid::where('kelas', $validated['kelas'])
            ->where('status', 'Aktif')
            ->count();

        if ($muridCount >= 40) {
            return redirect()->back()
                ->withErrors(['kelas' => 'Kelas ini sudah mencapai batas maksimal 40 murid.'])
                ->withInput();
        }

        Murid::create($validated);

        return redirect()->route('murid.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Murid $murid)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $murid->load('kelasRelation', 'keterlambatan');

        return view('murid.show', compact('murid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Murid $murid)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $kelas = Kelas::orderBy('kelas')->get();

        return view('murid.edit', compact('murid', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Murid $murid)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'NIS' => ['required', 'string', 'max:255', Rule::unique('murid')->ignore($murid->NIS, 'NIS')],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'kelas' => ['required', 'exists:kelas,kelas'],
        ]);

        // Validasi: maksimal 40 murid per kelas (hanya jika kelas berubah atau murid dipindahkan)
        if ($murid->kelas !== $validated['kelas']) {
            $muridCount = Murid::where('kelas', $validated['kelas'])
                ->where('status', 'Aktif')
                ->where('NIS', '!=', $murid->NIS) // Exclude murid yang sedang diupdate
                ->count();

            if ($muridCount >= 40) {
                return redirect()->back()
                    ->withErrors(['kelas' => 'Kelas tujuan sudah mencapai batas maksimal 40 murid.'])
                    ->withInput();
            }
        }

        $murid->update($validated);

        return redirect()->route('murid.index')->with('success', 'Murid berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Murid $murid)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        // Cek apakah murid masih memiliki data keterlambatan
        $keterlambatanCount = $murid->keterlambatan()->count();

        $murid->delete();

        // Jika ada keterlambatan, NIS akan menjadi null (set null) tapi data tetap ada sebagai snapshot
        if ($keterlambatanCount > 0) {
            return redirect()->route('murid.index')
                ->with('success', "Murid berhasil dihapus. {$keterlambatanCount} data keterlambatan tetap tersimpan sebagai snapshot historis.");
        }

        return redirect()->route('murid.index')->with('success', 'Murid berhasil dihapus.');
    }

    /**
     * Show the import form.
     */
    public function showImport()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        return view('murid.import');
    }

    /**
     * Download template Excel file.
     */
    public function downloadTemplate()
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $filename = 'template_import_murid.xlsx';
        $tempPath = storage_path('app/temp/'.$filename);

        // Pastikan direktori temp ada
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            // Setup OpenSpout writer
            $options = new Options;
            $options->setTempFolder(storage_path('app/temp'));
            $writer = new Writer($options);
            $writer->openToFile($tempPath);

            // Header style (bold)
            $headerStyle = (new Style)
                ->setFontBold()
                ->setFontSize(11);

            // Write header
            $writer->addRow(Row::fromValues(['NIS', 'Nama Lengkap', 'Gender', 'Kelas'], $headerStyle));

            // Sample data (Gender: L untuk Laki-laki, P untuk Perempuan)
            $writer->addRow(Row::fromValues(['1234567890', 'Andi Prasetyo', 'L', 'X-1']));
            $writer->addRow(Row::fromValues(['1234567891', 'Siti Nurhaliza', 'P', 'X-2']));
            $writer->addRow(Row::fromValues(['1234567892', 'Budi Santoso', 'L', 'XI-1']));
            $writer->addRow(Row::fromValues(['1234567893', 'Dewi Lestari', 'P', 'XI-2']));

            $writer->close();

            // Return download response
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Cleanup on error
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return redirect()->route('murid.import')
                ->with('error', 'Gagal download template: '.$e->getMessage());
        }
    }

    /**
     * Process the import file.
     */
    public function import(Request $request)
    {
        $redirect = $this->checkWalikelasAccess();
        if ($redirect) {
            return $redirect;
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'], // Max 10MB
        ], [
            'file.required' => 'File harus diupload.',
            'file.file' => 'File tidak valid.',
            'file.mimes' => 'File harus berformat CSV, TXT, atau Excel (.xlsx).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        // Validasi: Import hanya bisa dilakukan jika kelas X kosong
        $kelasX = Kelas::where('kelas', 'like', 'X-%')->pluck('kelas');
        $muridKelasX = Murid::whereIn('kelas', $kelasX)
            ->where('status', 'Aktif')
            ->count();

        if ($muridKelasX > 0) {
            return redirect()->route('murid.import')
                ->with('error', 'Import tidak dapat dilakukan karena masih ada murid di kelas X. Silakan naikkan kelas terlebih dahulu sebelum melakukan import murid baru.');
        }

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $errors = [];
        $successCount = 0;
        $skipCount = 0;
        $rowNumber = 0;

        try {
            // Expected header format: NIS, Nama Lengkap, Gender, Kelas
            $expectedHeaders = ['NIS', 'Nama Lengkap', 'Gender', 'Kelas'];
            $header = null;
            $rows = [];

            // Read file based on extension
            if ($extension === 'xlsx') {
                // Read Excel file using OpenSpout
                $reader = Reader::createFromFile($path);
                $reader->open($path);

                $rowIndex = 0;
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $rowIndex++;
                        $rowData = [];
                        foreach ($row->getCells() as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        if ($rowIndex === 1) {
                            // First row is header
                            $header = $rowData;
                        } else {
                            // Data rows
                            $rows[] = $rowData;
                        }
                    }
                    // Only read first sheet
                    break;
                }

                $reader->close();
            } else {
                // Read CSV file
                // Read file content and remove BOM if present
                $content = file_get_contents($path);
                if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                    $content = substr($content, 3);
                    file_put_contents($path, $content);
                }

                // Open file with UTF-8 encoding support
                $handle = fopen($path, 'r');
                if ($handle === false) {
                    return redirect()->route('murid.import')->with('error', 'Gagal membuka file.');
                }

                // Read header row
                $header = fgetcsv($handle, 1000, ',');

                // Read data rows
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    $rows[] = $row;
                }

                fclose($handle);
            }

            // Validate header
            if (! $header || count($header) < 4) {
                return redirect()->route('murid.import')->with('error', 'Format file tidak valid. Pastikan file memiliki header: NIS, Nama Lengkap, Gender, Kelas');
            }

            DB::beginTransaction();

            // Process rows
            foreach ($rows as $row) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has enough columns
                if (count($row) < 4) {
                    $errors[] = "Baris $rowNumber: Data tidak lengkap";
                    $skipCount++;

                    continue;
                }

                $nis = trim($row[0] ?? '');
                $namaLengkap = trim($row[1] ?? '');
                $gender = trim($row[2] ?? '');
                $kelas = trim($row[3] ?? '');

                // Validate required fields
                if (empty($nis) || empty($namaLengkap) || empty($gender) || empty($kelas)) {
                    $errors[] = "Baris $rowNumber: Data tidak lengkap (NIS: $nis, Nama: $namaLengkap)";
                    $skipCount++;

                    continue;
                }

                // Validate dan konversi gender (L -> Laki-laki, P -> Perempuan)
                $genderNormalized = strtoupper(trim($gender));
                if ($genderNormalized === 'L') {
                    $gender = 'Laki-laki';
                } elseif ($genderNormalized === 'P') {
                    $gender = 'Perempuan';
                } elseif (! in_array($gender, ['Laki-laki', 'Perempuan'])) {
                    $errors[] = "Baris $rowNumber: Gender tidak valid (harus 'L' untuk Laki-laki atau 'P' untuk Perempuan)";
                    $skipCount++;

                    continue;
                }

                // Validate kelas exists
                if (! Kelas::where('kelas', $kelas)->exists()) {
                    $errors[] = "Baris $rowNumber: Kelas '$kelas' tidak ditemukan";
                    $skipCount++;

                    continue;
                }

                // Check if NIS already exists
                if (Murid::where('NIS', $nis)->exists()) {
                    $errors[] = "Baris $rowNumber: NIS '$nis' sudah ada";
                    $skipCount++;

                    continue;
                }

                // Validasi: maksimal 40 murid per kelas
                $muridCount = Murid::where('kelas', $kelas)
                    ->where('status', 'Aktif')
                    ->count();

                if ($muridCount >= 40) {
                    $errors[] = "Baris $rowNumber: Kelas '$kelas' sudah mencapai batas maksimal 40 murid";
                    $skipCount++;

                    continue;
                }

                // Create murid
                try {
                    Murid::create([
                        'NIS' => $nis,
                        'nama_lengkap' => $namaLengkap,
                        'gender' => $gender,
                        'kelas' => $kelas,
                        'status' => 'Aktif',
                        'tahun_lulus' => null,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: Gagal menyimpan data - ".$e->getMessage();
                    $skipCount++;
                }
            }

            DB::commit();

            $message = "Import selesai! Berhasil: $successCount, Gagal/Dilewati: $skipCount";
            if (! empty($errors)) {
                $message .= '. Detail error: '.implode('; ', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= ' dan '.(count($errors) - 10).' error lainnya.';
                }
            }

            if ($successCount > 0) {
                return redirect()->route('murid.index')->with('success', $message);
            } else {
                return redirect()->route('murid.import')->with('error', $message);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('murid.import')->with('error', 'Terjadi kesalahan saat membaca file: '.$e->getMessage());
        }
    }
}
