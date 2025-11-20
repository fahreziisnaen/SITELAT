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
        $user = auth()->user();

        // Jika user adalah Walikelas, hanya bisa melihat kelas yang dia pegang
        if ($user && $user->role === 'Walikelas') {
            // Pastikan hanya mengambil kelas yang dipegang oleh user ini
            $kelas = Kelas::where('username', $user->username)
                ->orderBy('kelas')
                ->get();
            // Walikelas tidak perlu memilih kelas, akan otomatis menampilkan semua kelas yang dia pegang
            $selectedKelas = null;
        } else {
            // Admin dan TATIB bisa melihat semua kelas
            $kelas = Kelas::orderBy('kelas')->get();
            $selectedKelas = $request->input('kelas');
        }

        $jenisLaporan = $request->input('jenis_laporan');
        $tahunRange = $request->input('tahun'); // Format: "2025-2026"
        $bulan = $request->input('bulan');
        $semester = $request->input('semester');

        // Parse tahun range (format: "2025-2026")
        $tahun = null;
        $tahunBerikutnya = null;
        if ($tahunRange && strpos($tahunRange, '-') !== false) {
            $tahunParts = explode('-', $tahunRange);
            $tahun = (int) $tahunParts[0];
            $tahunBerikutnya = (int) $tahunParts[1];
        }

        $startDate = null;
        $endDate = null;
        $periodeLabel = null;

        // Hitung start_date dan end_date berdasarkan jenis laporan
        if ($jenisLaporan && $tahun) {
            if ($jenisLaporan === 'bulanan' && $bulan) {
                // Laporan Bulanan: tahun dan bulan (gunakan tahun pertama dari range)
                $startDate = \Carbon\Carbon::create($tahun, $bulan, 1)->format('Y-m-d');
                $endDate = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

                // Format label bulan
                $bulanNama = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];
                $periodeLabel = $bulanNama[$bulan].' '.$tahunRange;
            } elseif ($jenisLaporan === 'semester' && $tahunRange) {
                // Laporan Semester: langsung export Excel untuk semua semester (1 dan 2)
                return $this->exportSemester($request);
            }
        }

        $report = null;

        if ($startDate && $endDate) {
            // Query keterlambatan berdasarkan filter
            $query = Keterlambatan::whereBetween('tanggal', [$startDate, $endDate]);

            // Filter berdasarkan kelas jika dipilih
            if ($selectedKelas) {
                $query->where('kelas', $selectedKelas);
            }

            // Jika user adalah Walikelas, filter berdasarkan kelas yang dia pegang
            if ($user && $user->role === 'Walikelas') {
                $kelasIds = Kelas::where('username', $user->username)->pluck('kelas')->toArray();
                $query->whereIn('kelas', $kelasIds);
            }

            $report = $query->orderBy('tanggal', 'desc')
                ->orderBy('kelas')
                ->orderBy('nama_murid')
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->tanggal,
                        'NIS' => $item->NIS ?? '-',
                        'nama_murid' => $item->nama_murid ?? '-',
                        'gender' => $item->gender ?? '-',
                        'kelas' => $item->kelas ?? '-',
                        'username' => $item->username ?? '-',
                        'keterangan' => $item->keterangan ?? '-',
                    ];
                });
        }

        return view('report.index', compact('kelas', 'selectedKelas', 'jenisLaporan', 'tahunRange', 'bulan', 'semester', 'startDate', 'endDate', 'periodeLabel', 'report'));
    }

    /**
     * Export laporan semester ke Excel menggunakan OpenSpout dan ZipArchive
     * Lebih cepat karena tidak perlu memuat semua data dari template
     */
    public function exportSemester(Request $request)
    {
        $user = auth()->user();
        $tahunRange = $request->input('tahun'); // Format: "2025-2026"
        $selectedKelas = $request->input('kelas');

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
            // Query data
            $allKelas = $this->sortKelasNatural(Kelas::all());

            $muridAktif = Murid::where('status', 'Aktif')
                ->whereNotNull('kelas')
                ->select('NIS', 'nama_lengkap', 'gender', 'kelas')
                ->orderBy('kelas')
                ->orderBy('nama_lengkap')
                ->get()
                ->groupBy('kelas');

            $keterlambatanData = Keterlambatan::whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('NIS')
                ->select('NIS', 'tanggal')
                ->orderBy('NIS')
                ->orderBy('tanggal')
                ->get()
                ->groupBy('NIS')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return \Carbon\Carbon::parse($item->tanggal)->format('n/j/Y');
                    })->implode(',');
                });

            // Buat sheet baru menggunakan OpenSpout ke file sementara
            $newSheetPath = storage_path('app/temp/new_sheet_'.uniqid().'.xlsx');
            $this->createDataMuridSheet($newSheetPath, $tahunRange, $allKelas, $muridAktif, $keterlambatanData);

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
     */
    private function createDataMuridSheet($filePath, $tahunRange, $allKelas, $muridAktif, $keterlambatanData)
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

        // Loop untuk setiap kelas
        foreach ($allKelas as $kelas) {
            // Ambil walikelas
            $walikelasNama = '-';
            if ($kelas->username) {
                $walikelas = User::where('username', $kelas->username)->first();
                if ($walikelas) {
                    $walikelasNama = $walikelas->nama_lengkap;
                }
            }

            // Kelas dan Walikelas
            $writer->addRow(Row::fromValues(['Kelas: '.$kelas->kelas], $kelasHeaderStyle));
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
            $muridKelas = $muridAktif->get($kelas->kelas, collect());
            $noMurid = 1;

            // Siapkan 40 baris untuk setiap kelas
            for ($i = 0; $i < 40; $i++) {
                if ($i < $muridKelas->count()) {
                    // Ada data murid
                    $murid = $muridKelas->values()[$i];
                    $tanggalKeterlambatan = $keterlambatanData->get($murid->NIS, '');

                    $writer->addRow(Row::fromValues([
                        (string) $noMurid++,
                        $murid->NIS ?? '-',
                        $murid->nama_lengkap ?? '-',
                        $murid->gender ?? '-',
                        $tanggalKeterlambatan,
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
}
