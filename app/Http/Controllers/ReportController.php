<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Keterlambatan;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class ReportController extends Controller
{
    /**
     * Convert memory limit string to bytes
     */
    private function convertToBytes($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $value = (int) $memoryLimit;

        switch ($last) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Sort kelas dengan natural sort (X-1, X-2, ..., X-10, XI-1, ...)
     */
    private function sortKelasNatural($kelasCollection)
    {
        return $kelasCollection->sort(function ($a, $b) {
            // Extract level (X, XI, XII) dan nomor
            preg_match('/^(XII|XI|X)-(\d+)$/', $a->kelas, $matchesA);
            preg_match('/^(XII|XI|X)-(\d+)$/', $b->kelas, $matchesB);

            if (empty($matchesA) || empty($matchesB)) {
                return strcmp($a->kelas, $b->kelas);
            }

            $levelA = $matchesA[1];
            $levelB = $matchesB[1];
            $numA = (int) $matchesA[2];
            $numB = (int) $matchesB[2];

            // Urutkan berdasarkan level dulu (X < XI < XII)
            $levelOrder = ['X' => 1, 'XI' => 2, 'XII' => 3];
            $levelCompare = ($levelOrder[$levelA] ?? 999) <=> ($levelOrder[$levelB] ?? 999);

            if ($levelCompare !== 0) {
                return $levelCompare;
            }

            // Jika level sama, urutkan berdasarkan nomor
            return $numA <=> $numB;
        });
    }

    public function index(Request $request)
    {
        // Hanya Admin dan TATIB yang bisa mengakses report
        if (auth()->user()->role === 'Walikelas') {
            abort(403, 'Unauthorized action.');
        }

        $tahunRange = $request->input('tahun'); // Format: "2025-2026"

        // Jika ada tahun ajaran yang dipilih, langsung export Excel
        if ($tahunRange) {
            return $this->exportSemester($request);
        }

        // Jika belum ada tahun ajaran, tampilkan form
        return view('report.index', compact('tahunRange'));
    }

    /**
     * Index untuk Walikelas - form export report kelas
     */
    public function indexKelas(Request $request)
    {
        // Hanya Walikelas yang bisa mengakses
        if (auth()->user()->role !== 'Walikelas') {
            abort(403, 'Unauthorized action.');
        }

        $tahunRange = $request->input('tahun'); // Format: "2025-2026"

        // Jika ada tahun ajaran yang dipilih, langsung export Excel
        if ($tahunRange) {
            return $this->exportKelas($request);
        }

        // Jika belum ada tahun ajaran, tampilkan form
        return view('report.kelas', compact('tahunRange'));
    }

    /**
     * Export laporan semester ke Excel menggunakan OpenSpout dan ZipArchive
     * Lebih cepat karena tidak perlu memuat semua data dari template
     */
    public function exportSemester(Request $request)
    {
        // Hanya Admin dan TATIB yang bisa mengakses report
        if (auth()->user()->role === 'Walikelas') {
            abort(403, 'Unauthorized action.');
        }

        $tahunRange = $request->input('tahun'); // Format: "2025-2026"

        if (! $tahunRange) {
            return redirect()->route('report.index')
                ->with('error', 'Tahun Ajaran harus dipilih.');
        }

        // Parse tahun range untuk menghitung periode semester
        $tahunParts = explode('-', $tahunRange);
        $tahunAwal = (int) $tahunParts[0];
        $tahunAkhir = (int) $tahunParts[1];

        // Periode semester: Semester 1 (Juli-Desember tahun awal) dan Semester 2 (Januari-Juni tahun akhir)
        $startDate = \Carbon\Carbon::create($tahunAwal, 7, 1)->format('Y-m-d'); // 1 Juli tahun awal
        $endDate = \Carbon\Carbon::create($tahunAkhir, 6, 30)->format('Y-m-d'); // 30 Juni tahun akhir

        // Template path
        $templatePath = storage_path('template/template-rekap.xlsx');

        if (! file_exists($templatePath)) {
            return redirect()->route('report.index')
                ->with('error', 'Template Excel tidak ditemukan. Pastikan file template ada di storage/template/template-rekap.xlsx');
        }

        // Set filename dan temp path
        $filename = 'Laporan_Keterlambatan_Semester_'.$tahunRange.'_'.date('YmdHis').'.xlsx';
        $tempPath = storage_path('app/temp/'.$filename);
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Copy template tanpa memuat isinya (sangat cepat)
        copy($templatePath, $tempPath);

        try {
            // Query keterlambatan dengan snapshot kelas untuk pengelompokan yang benar
            $keterlambatanQuery = Keterlambatan::whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('NIS')
                ->whereNotNull('kelas') // Hanya ambil yang punya snapshot kelas
                ->select('NIS', 'nama_murid', 'gender', 'kelas', 'tanggal')
                ->orderBy('kelas')
                ->orderBy('nama_murid')
                ->orderBy('tanggal')
                ->get();

            // Kelompokkan keterlambatan berdasarkan snapshot kelas
            $keterlambatanByKelas = $keterlambatanQuery->groupBy('kelas');

            // Ambil semua kelas dari tabel kelas (untuk menampilkan semua murid aktif)
            $allKelas = $this->sortKelasNatural(Kelas::all());

            // Ambil semua NIS yang punya keterlambatan (untuk menghindari duplikasi)
            $nisWithKeterlambatan = $keterlambatanQuery->pluck('NIS')->unique();

            // Buat struktur data untuk setiap kelas berdasarkan snapshot keterlambatan
            $dataByKelas = [];
            foreach ($keterlambatanByKelas as $snapshotKelas => $keterlambatanItems) {
                // Kelompokkan keterlambatan per murid (NIS) dalam kelas ini
                $keterlambatanPerMurid = $keterlambatanItems->groupBy('NIS')->map(function ($items) {
                    $firstItem = $items->first();
                    $tanggalKeterlambatan = $items->map(function ($item) {
                        return \Carbon\Carbon::parse($item->tanggal)->format('n/j/Y');
                    })->implode(',');

                    return [
                        'NIS' => $firstItem->NIS,
                        'nama_lengkap' => $firstItem->nama_murid, // Dari snapshot
                        'gender' => $firstItem->gender, // Dari snapshot
                        'kelas' => $firstItem->kelas, // Dari snapshot
                        'tanggal_keterlambatan' => $tanggalKeterlambatan,
                    ];
                })->values();

                $dataByKelas[$snapshotKelas] = $keterlambatanPerMurid;
            }

            // Pastikan semua kelas dari tabel Kelas ditampilkan
            // Tambahkan murid aktif yang tidak punya keterlambatan di kelas mereka saat ini
            foreach ($allKelas as $kelas) {
                $kelasName = $kelas->kelas;

                // Jika kelas ini belum ada di dataByKelas (tidak ada keterlambatan dengan snapshot kelas ini)
                if (! isset($dataByKelas[$kelasName])) {
                    // Ambil murid aktif di kelas ini yang tidak punya keterlambatan
                    $muridAktifKelas = Murid::where('status', 'Aktif')
                        ->where('kelas', $kelasName)
                        ->whereNotIn('NIS', $nisWithKeterlambatan) // Exclude yang sudah punya keterlambatan
                        ->select('NIS', 'nama_lengkap', 'gender', 'kelas')
                        ->orderBy('nama_lengkap')
                        ->get()
                        ->map(function ($murid) {
                            return [
                                'NIS' => $murid->NIS,
                                'nama_lengkap' => $murid->nama_lengkap,
                                'gender' => $murid->gender,
                                'kelas' => $murid->kelas,
                                'tanggal_keterlambatan' => '', // Tidak ada keterlambatan
                            ];
                        });

                    // Selalu tambahkan kelas, meskipun tidak ada murid (untuk menampilkan semua kelas)
                    $dataByKelas[$kelasName] = $muridAktifKelas;
                }
            }

            // Pastikan semua kelas dari tabel Kelas ada di dataByKelas (termasuk yang tidak ada muridnya)
            foreach ($allKelas as $kelas) {
                $kelasName = $kelas->kelas;
                if (! isset($dataByKelas[$kelasName])) {
                    // Kelas tidak ada murid sama sekali, tambahkan dengan collection kosong
                    $dataByKelas[$kelasName] = collect();
                }
            }

            // Pastikan semua kelas ditampilkan (termasuk yang tidak ada di dataByKelas)
            // Re-sort kelas menggunakan semua kelas dari tabel Kelas
            $allKelas = $this->sortKelasNatural(Kelas::all());

            // Buat sheet baru menggunakan OpenSpout ke file sementara
            $newSheetPath = storage_path('app/temp/new_sheet_'.uniqid().'.xlsx');
            $this->createDataMuridSheet($newSheetPath, $tahunRange, $allKelas, $dataByKelas);

            // Tambahkan sheet baru ke template menggunakan ZipArchive
            $this->addSheetToTemplate($tempPath, $newSheetPath, 'Data Murid');

            // Hapus file sheet sementara
            if (file_exists($newSheetPath)) {
                unlink($newSheetPath);
            }

            // Return download response
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Cleanup on error
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return redirect()->route('report.index')
                ->with('error', 'Gagal export Excel: '.$e->getMessage());
        }
    }

    /**
     * Buat sheet Data Murid menggunakan OpenSpout
     * $dataByKelas: array dengan key = snapshot kelas, value = collection murid dengan keterlambatan
     */
    private function createDataMuridSheet($filePath, $tahunRange, $allKelas, $dataByKelas)
    {
        $options = new Options;
        $options->setTempFolder(storage_path('app/temp'));
        $writer = new Writer($options);
        $writer->openToFile($filePath);

        // Header style
        $headerStyle = (new Style)
            ->setFontBold()
            ->setFontSize(14);

        // Table header style
        $tableHeaderStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setBackgroundColor(Color::rgb(68, 114, 196))
            ->setFontColor(Color::rgb(255, 255, 255));

        // Kelas header style
        $kelasHeaderStyle = (new Style)
            ->setFontBold()
            ->setFontSize(12);

        // Data style
        $dataStyle = new Style;

        // Center style untuk kolom No
        $centerStyle = (new Style)
            ->setCellAlignment(\OpenSpout\Common\Entity\Style\CellAlignment::CENTER);

        // Judul
        $writer->addRow(Row::fromValues(['DATA MURID AKTIF'], $headerStyle));
        $writer->addRow(Row::fromValues(['TAHUN PELAJARAN'], $headerStyle));
        $writer->addRow(Row::fromValues([$tahunRange], $headerStyle));
        $writer->addRow(Row::fromValues([])); // Baris kosong

        // Loop untuk setiap kelas berdasarkan snapshot keterlambatan
        foreach ($allKelas as $kelas) {
            $snapshotKelas = $kelas->kelas;

            // Ambil walikelas dari snapshot (cari kelas berdasarkan snapshot kelas)
            $walikelasNama = '-';
            $kelasData = Kelas::where('kelas', $snapshotKelas)->first();
            if ($kelasData && $kelasData->username) {
                $walikelas = User::where('username', $kelasData->username)->first();
                if ($walikelas) {
                    $walikelasNama = $walikelas->nama_lengkap;
                }
            }

            // Kelas dan Walikelas (menggunakan snapshot kelas)
            $writer->addRow(Row::fromValues(['Kelas: '.$snapshotKelas], $kelasHeaderStyle));
            $writer->addRow(Row::fromValues(['WALIKELAS: '.$walikelasNama], $kelasHeaderStyle));

            // Header tabel
            $writer->addRow(Row::fromValues([
                'No',
                'NIS',
                'Nama Lengkap',
                'Gender',
                'Tanggal Keterlambatan',
            ], $tableHeaderStyle));

            // Ambil data murid untuk kelas ini berdasarkan snapshot (dari keterlambatan)
            $muridKelas = $dataByKelas[$snapshotKelas] ?? collect();
            $noMurid = 1;

            // Siapkan 40 baris untuk setiap kelas
            for ($i = 0; $i < 40; $i++) {
                if ($i < $muridKelas->count()) {
                    // Ada data murid dari snapshot keterlambatan
                    $murid = $muridKelas[$i];

                    $writer->addRow(Row::fromValues([
                        (string) $noMurid++,
                        $murid['NIS'] ?? '-',
                        $murid['nama_lengkap'] ?? '-', // Dari snapshot
                        $murid['gender'] ?? '-', // Dari snapshot
                        $murid['tanggal_keterlambatan'] ?? '',
                    ], $dataStyle));
                } else {
                    // Baris kosong
                    $writer->addRow(Row::fromValues([
                        (string) $noMurid++,
                        '',
                        '',
                        '',
                        '',
                    ], $dataStyle));
                }
            }

            // Baris kosong antara kelas
            $writer->addRow(Row::fromValues([]));
        }

        $writer->close();
    }

    /**
     * Tambahkan sheet baru ke template Excel menggunakan ZipArchive
     */
    private function addSheetToTemplate(string $templatePath, string $newSheetPath, string $sheetName)
    {
        // Buka template sebagai ZIP
        $zipTemplate = new \ZipArchive;
        if ($zipTemplate->open($templatePath, \ZipArchive::CREATE) !== true) {
            throw new \Exception('Gagal membuka template sebagai ZIP');
        }

        // Buka sheet baru sebagai ZIP untuk extract XML
        $zipNewSheet = new \ZipArchive;
        if ($zipNewSheet->open($newSheetPath) !== true) {
            $zipTemplate->close();
            throw new \Exception('Gagal membuka sheet baru');
        }

        // Extract XML sheet baru
        $newSheetXml = $zipNewSheet->getFromName('xl/worksheets/sheet1.xml');
        if ($newSheetXml === false) {
            $zipNewSheet->close();
            $zipTemplate->close();
            throw new \Exception('Gagal membaca XML sheet baru');
        }

        // Extract shared strings jika ada
        $newSharedStringsXml = $zipNewSheet->getFromName('xl/sharedStrings.xml');
        $zipNewSheet->close();

        // Bersihkan worksheet XML dari elemen drawing yang tidak diperlukan
        // Ini mencegah error "Drawing shape" saat Excel membuka file
        $sheetDom = new \DOMDocument;
        $sheetDom->preserveWhiteSpace = true;
        $sheetDom->formatOutput = false;

        if ($sheetDom->loadXML($newSheetXml)) {
            $sheetXpath = new \DOMXPath($sheetDom);
            $sheetXpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $sheetXpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            // Hapus elemen drawing jika ada (tanpa namespace karena bisa di berbagai tempat)
            $drawingParts = $sheetXpath->query('//*[local-name()="drawing"]');
            foreach ($drawingParts as $drawingPart) {
                if ($drawingPart->parentNode) {
                    $drawingPart->parentNode->removeChild($drawingPart);
                }
            }

            // Hapus elemen legacyDrawing jika ada
            $legacyDrawings = $sheetXpath->query('//*[local-name()="legacyDrawing"]');
            foreach ($legacyDrawings as $legacyDrawing) {
                if ($legacyDrawing->parentNode) {
                    $legacyDrawing->parentNode->removeChild($legacyDrawing);
                }
            }

            // Hapus elemen drawing dengan namespace main
            $mainDrawings = $sheetXpath->query('//main:drawing');
            foreach ($mainDrawings as $mainDrawing) {
                if ($mainDrawing->parentNode) {
                    $mainDrawing->parentNode->removeChild($mainDrawing);
                }
            }

            $newSheetXml = $sheetDom->saveXML();
        }

        // Baca workbook.xml untuk mendapatkan sheetId terbesar
        $workbookXml = $zipTemplate->getFromName('xl/workbook.xml');
        if ($workbookXml === false) {
            $zipTemplate->close();
            throw new \Exception('Gagal membaca workbook.xml');
        }

        $workbookDom = new \DOMDocument;
        $workbookDom->loadXML($workbookXml);
        $workbookXpath = new \DOMXPath($workbookDom);
        $workbookXpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $workbookXpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        // Cari sheetId terbesar
        $sheets = $workbookXpath->query('//main:sheets/main:sheet');
        $maxSheetId = 0;
        foreach ($sheets as $sheet) {
            $sheetId = (int) $sheet->getAttribute('sheetId');
            if ($sheetId > $maxSheetId) {
                $maxSheetId = $sheetId;
            }
        }
        $newSheetId = $maxSheetId + 1;

        // Hitung rId dari workbook.xml.rels
        $relsXml = $zipTemplate->getFromName('xl/_rels/workbook.xml.rels');
        $maxRId = 0;
        if ($relsXml !== false) {
            $relsDom = new \DOMDocument;
            $relsDom->loadXML($relsXml);
            $relsXpath = new \DOMXPath($relsDom);
            $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');
            $relationships = $relsXpath->query('//rel:Relationship');
            foreach ($relationships as $rel) {
                $rId = (int) str_replace('rId', '', $rel->getAttribute('Id'));
                if ($rId > $maxRId) {
                    $maxRId = $rId;
                }
            }
        }
        $newRId = 'rId'.($maxRId + 1);

        // Update workbook.xml - tambahkan referensi sheet baru
        $sheetsNodes = $workbookXpath->query('//main:sheets');
        if ($sheetsNodes->length === 0) {
            $zipTemplate->close();
            throw new \Exception('Tidak ditemukan node sheets di workbook.xml');
        }
        $sheetsNode = $sheetsNodes->item(0);

        $newSheetNode = $workbookDom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'sheet');
        $newSheetNode->setAttribute('name', $sheetName);
        $newSheetNode->setAttribute('sheetId', (string) $newSheetId);
        $newSheetNode->setAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'r:id', $newRId);
        $newSheetNode->setAttribute('state', 'hidden'); // Set sheet sebagai hidden
        $sheetsNode->appendChild($newSheetNode);
        $zipTemplate->addFromString('xl/workbook.xml', $workbookDom->saveXML());

        // Update workbook.xml.rels
        if ($relsXml !== false) {
            $relsDom = new \DOMDocument;
            $relsDom->loadXML($relsXml);
            $relsXpath = new \DOMXPath($relsDom);
            $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

            $relationshipsNodes = $relsXpath->query('//rel:Relationships');
            if ($relationshipsNodes->length > 0) {
                $relationshipsNode = $relationshipsNodes->item(0);
                $newRelationship = $relsDom->createElementNS('http://schemas.openxmlformats.org/package/2006/relationships', 'Relationship');
                $newRelationship->setAttribute('Id', $newRId);
                $newRelationship->setAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet');
                $newRelationship->setAttribute('Target', "worksheets/sheet{$newSheetId}.xml");
                $relationshipsNode->appendChild($newRelationship);
                $zipTemplate->addFromString('xl/_rels/workbook.xml.rels', $relsDom->saveXML());
            }
        }

        // Update [Content_Types].xml
        $contentTypesXml = $zipTemplate->getFromName('[Content_Types].xml');
        if ($contentTypesXml !== false) {
            $contentTypesDom = new \DOMDocument;
            $contentTypesDom->loadXML($contentTypesXml);
            $contentTypesXpath = new \DOMXPath($contentTypesDom);
            $contentTypesXpath->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');

            $typesNodes = $contentTypesXpath->query('//ct:Types');
            if ($typesNodes->length > 0) {
                $typesNode = $typesNodes->item(0);
                $newOverride = $contentTypesDom->createElementNS('http://schemas.openxmlformats.org/package/2006/content-types', 'Override');
                $newOverride->setAttribute('PartName', "/xl/worksheets/sheet{$newSheetId}.xml");
                $newOverride->setAttribute('ContentType', 'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml');
                $typesNode->appendChild($newOverride);
                $zipTemplate->addFromString('[Content_Types].xml', $contentTypesDom->saveXML());
            }
        }

        // Handle shared strings - merge jika perlu
        if ($newSharedStringsXml !== false) {
            $existingSharedStrings = $zipTemplate->getFromName('xl/sharedStrings.xml');
            if ($existingSharedStrings === false) {
                // Jika template tidak punya shared strings, tambahkan
                $zipTemplate->addFromString('xl/sharedStrings.xml', $newSharedStringsXml);
            } else {
                // Merge shared strings
                $existingDom = new \DOMDocument;
                $existingDom->loadXML($existingSharedStrings);
                $newDom = new \DOMDocument;
                $newDom->loadXML($newSharedStringsXml);

                $existingXpath = new \DOMXPath($existingDom);
                $existingXpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $newXpath = new \DOMXPath($newDom);
                $newXpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                $existingSst = $existingXpath->query('//main:sst')->item(0);
                $newSis = $newXpath->query('//main:sst/main:si');

                if ($existingSst && $newSis->length > 0) {
                    $existingCount = (int) $existingSst->getAttribute('count');
                    $existingUniqueCount = (int) $existingSst->getAttribute('uniqueCount');
                    $sharedStringOffset = $existingCount;

                    foreach ($newSis as $si) {
                        $imported = $existingDom->importNode($si, true);
                        $existingSst->appendChild($imported);
                        $existingCount++;
                        $existingUniqueCount++;
                    }

                    $existingSst->setAttribute('count', (string) $existingCount);
                    $existingSst->setAttribute('uniqueCount', (string) $existingUniqueCount);

                    $zipTemplate->addFromString('xl/sharedStrings.xml', $existingDom->saveXML());

                    // Update referensi shared strings di worksheet XML
                    $sheetDom = new \DOMDocument;
                    $sheetDom->loadXML($newSheetXml);
                    $sheetXpath = new \DOMXPath($sheetDom);
                    $sheetXpath->registerNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

                    $cells = $sheetXpath->query('//main:row/main:c[@t="s"]');
                    foreach ($cells as $cell) {
                        $vNodes = $sheetXpath->query('./main:v', $cell);
                        if ($vNodes->length > 0) {
                            $vNode = $vNodes->item(0);
                            $oldIndex = (int) $vNode->nodeValue;
                            $newIndex = $oldIndex + $sharedStringOffset;
                            $vNode->nodeValue = (string) $newIndex;
                        }
                    }

                    $newSheetXml = $sheetDom->saveXML();
                }
            }
        }

        // Tambahkan worksheet XML baru
        $zipTemplate->addFromString("xl/worksheets/sheet{$newSheetId}.xml", $newSheetXml);

        $zipTemplate->close();
    }

    /**
     * Export laporan kelas untuk Walikelas
     * Hanya menampilkan kelas yang dipegang oleh Walikelas
     */
    public function exportKelas(Request $request)
    {
        // Hanya Walikelas yang bisa mengakses
        if (auth()->user()->role !== 'Walikelas') {
            abort(403, 'Unauthorized action.');
        }

        $user = auth()->user();
        $tahunRange = $request->input('tahun'); // Format: "2025-2026"

        if (! $tahunRange) {
            return redirect()->route('report.kelas')
                ->with('error', 'Tahun Ajaran harus dipilih.');
        }

        // Ambil kelas yang dipegang oleh Walikelas
        $kelasWalikelas = Kelas::where('username', $user->username)->get();

        if ($kelasWalikelas->isEmpty()) {
            return redirect()->route('report.kelas')
                ->with('error', 'Anda tidak memiliki kelas yang dipegang.');
        }

        // Parse tahun range untuk menghitung periode semester
        $tahunParts = explode('-', $tahunRange);
        $tahunAwal = (int) $tahunParts[0];
        $tahunAkhir = (int) $tahunParts[1];

        // Periode semester: Semester 1 (Juli-Desember tahun awal) dan Semester 2 (Januari-Juni tahun akhir)
        $startDate = \Carbon\Carbon::create($tahunAwal, 7, 1)->format('Y-m-d'); // 1 Juli tahun awal
        $endDate = \Carbon\Carbon::create($tahunAkhir, 6, 30)->format('Y-m-d'); // 30 Juni tahun akhir

        // Template path untuk kelas
        $templatePath = storage_path('template/template-kelas.xlsx');

        if (! file_exists($templatePath)) {
            return redirect()->route('report.kelas')
                ->with('error', 'Template Excel tidak ditemukan. Pastikan file template ada di storage/template/template-kelas.xlsx');
        }

        // Set filename dan temp path
        $kelasNames = $kelasWalikelas->pluck('kelas')->implode('_');
        $filename = 'Laporan_Keterlambatan_Kelas_'.$kelasNames.'_'.$tahunRange.'_'.date('YmdHis').'.xlsx';
        $tempPath = storage_path('app/temp/'.$filename);
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Copy template tanpa memuat isinya (sangat cepat)
        copy($templatePath, $tempPath);

        try {
            // Ambil nama kelas yang dipegang
            $kelasNamesArray = $kelasWalikelas->pluck('kelas')->toArray();

            // Query keterlambatan dengan snapshot kelas yang dipegang oleh Walikelas
            $keterlambatanQuery = Keterlambatan::whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('NIS')
                ->whereNotNull('kelas')
                ->whereIn('kelas', $kelasNamesArray) // Filter hanya kelas yang dipegang
                ->select('NIS', 'nama_murid', 'gender', 'kelas', 'tanggal')
                ->orderBy('kelas')
                ->orderBy('nama_murid')
                ->orderBy('tanggal')
                ->get();

            // Kelompokkan keterlambatan berdasarkan snapshot kelas
            $keterlambatanByKelas = $keterlambatanQuery->groupBy('kelas');

            // Ambil semua NIS yang punya keterlambatan (untuk menghindari duplikasi)
            $nisWithKeterlambatan = $keterlambatanQuery->pluck('NIS')->unique();

            // Buat struktur data untuk setiap kelas berdasarkan snapshot keterlambatan
            $dataByKelas = [];
            foreach ($keterlambatanByKelas as $snapshotKelas => $keterlambatanItems) {
                // Kelompokkan keterlambatan per murid (NIS) dalam kelas ini
                $keterlambatanPerMurid = $keterlambatanItems->groupBy('NIS')->map(function ($items) {
                    $firstItem = $items->first();
                    $tanggalKeterlambatan = $items->map(function ($item) {
                        return \Carbon\Carbon::parse($item->tanggal)->format('n/j/Y');
                    })->implode(',');

                    return [
                        'NIS' => $firstItem->NIS,
                        'nama_lengkap' => $firstItem->nama_murid, // Dari snapshot
                        'gender' => $firstItem->gender, // Dari snapshot
                        'kelas' => $firstItem->kelas, // Dari snapshot
                        'tanggal_keterlambatan' => $tanggalKeterlambatan,
                    ];
                })->values();

                $dataByKelas[$snapshotKelas] = $keterlambatanPerMurid;
            }

            // Tambahkan murid aktif yang tidak punya keterlambatan untuk setiap kelas yang dipegang
            foreach ($kelasWalikelas as $kelas) {
                $kelasName = $kelas->kelas;

                // Jika kelas ini belum ada di dataByKelas (tidak ada keterlambatan dengan snapshot kelas ini)
                if (! isset($dataByKelas[$kelasName])) {
                    // Ambil murid aktif di kelas ini yang tidak punya keterlambatan
                    $muridAktifKelas = Murid::where('status', 'Aktif')
                        ->where('kelas', $kelasName)
                        ->whereNotIn('NIS', $nisWithKeterlambatan) // Exclude yang sudah punya keterlambatan
                        ->select('NIS', 'nama_lengkap', 'gender', 'kelas')
                        ->orderBy('nama_lengkap')
                        ->get()
                        ->map(function ($murid) {
                            return [
                                'NIS' => $murid->NIS,
                                'nama_lengkap' => $murid->nama_lengkap,
                                'gender' => $murid->gender,
                                'kelas' => $murid->kelas,
                                'tanggal_keterlambatan' => '', // Tidak ada keterlambatan
                            ];
                        });

                    $dataByKelas[$kelasName] = $muridAktifKelas;
                }
            }

            // Sort kelas yang dipegang
            $sortedKelas = $this->sortKelasNatural($kelasWalikelas);

            // Buat sheet baru menggunakan OpenSpout ke file sementara
            $newSheetPath = storage_path('app/temp/new_sheet_'.uniqid().'.xlsx');
            $this->createDataMuridSheetKelas($newSheetPath, $tahunRange, $sortedKelas, $dataByKelas);

            // Tambahkan sheet baru ke template menggunakan ZipArchive
            $this->addSheetToTemplate($tempPath, $newSheetPath, 'Data Murid');

            // Hapus file sheet sementara
            if (file_exists($newSheetPath)) {
                unlink($newSheetPath);
            }

            // Return download response
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Cleanup on error
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return redirect()->route('report.kelas')
                ->with('error', 'Gagal export Excel: '.$e->getMessage());
        }
    }

    /**
     * Buat sheet Data Murid untuk Walikelas (hanya kelas yang dipegang)
     * Hanya generate 40 kolom untuk setiap kelas
     */
    private function createDataMuridSheetKelas($filePath, $tahunRange, $allKelas, $dataByKelas)
    {
        $options = new Options;
        $options->setTempFolder(storage_path('app/temp'));
        $writer = new Writer($options);
        $writer->openToFile($filePath);

        // Header style
        $headerStyle = (new Style)
            ->setFontBold()
            ->setFontSize(14);

        // Table header style
        $tableHeaderStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setBackgroundColor(Color::rgb(68, 114, 196))
            ->setFontColor(Color::rgb(255, 255, 255));

        // Kelas header style
        $kelasHeaderStyle = (new Style)
            ->setFontBold()
            ->setFontSize(12);

        // Data style
        $dataStyle = new Style;

        // Center style untuk kolom No
        $centerStyle = (new Style)
            ->setCellAlignment(\OpenSpout\Common\Entity\Style\CellAlignment::CENTER);

        // Judul
        $writer->addRow(Row::fromValues(['DATA MURID AKTIF'], $headerStyle));
        $writer->addRow(Row::fromValues(['TAHUN PELAJARAN'], $headerStyle));
        $writer->addRow(Row::fromValues([$tahunRange], $headerStyle));
        $writer->addRow(Row::fromValues([])); // Baris kosong

        // Loop untuk setiap kelas yang dipegang
        foreach ($allKelas as $kelas) {
            $snapshotKelas = $kelas->kelas;

            // Ambil walikelas
            $walikelasNama = '-';
            if ($kelas->username) {
                $walikelas = User::where('username', $kelas->username)->first();
                if ($walikelas) {
                    $walikelasNama = $walikelas->nama_lengkap;
                }
            }

            // Kelas dan Walikelas
            $writer->addRow(Row::fromValues(['Kelas: '.$snapshotKelas], $kelasHeaderStyle));
            $writer->addRow(Row::fromValues(['WALIKELAS: '.$walikelasNama], $kelasHeaderStyle));

            // Header tabel
            $writer->addRow(Row::fromValues([
                'No',
                'NIS',
                'Nama Lengkap',
                'Gender',
                'Tanggal Keterlambatan',
            ], $tableHeaderStyle));

            // Ambil data murid untuk kelas ini
            $muridKelas = $dataByKelas[$snapshotKelas] ?? collect();
            $noMurid = 1;

            // Generate hanya 40 baris untuk setiap kelas
            for ($i = 0; $i < 40; $i++) {
                if ($i < $muridKelas->count()) {
                    // Ada data murid
                    $murid = $muridKelas[$i];

                    $writer->addRow(Row::fromValues([
                        (string) $noMurid++,
                        $murid['NIS'] ?? '-',
                        $murid['nama_lengkap'] ?? '-',
                        $murid['gender'] ?? '-',
                        $murid['tanggal_keterlambatan'] ?? '',
                    ], $dataStyle));
                } else {
                    // Baris kosong
                    $writer->addRow(Row::fromValues([
                        (string) $noMurid++,
                        '',
                        '',
                        '',
                        '',
                    ], $dataStyle));
                }
            }

            // Baris kosong antara kelas
            $writer->addRow(Row::fromValues([]));
        }

        $writer->close();
    }
}
