<?php

namespace App\Exports;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use App\Models\Klinik;

class ReservasiWordExport
{
    protected $data;
    protected $klinik;

    public function __construct($data, $klinikId = null)
    {
        $this->data = $data;
        $this->klinik = Klinik::find($klinikId) ?? Klinik::first();
    }
    // generate dokumen word
    public function generate()
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop'    => 1200,
            'marginBottom' => 1200,
            'marginLeft'   => 1200,
            'marginRight'  => 1200,
        ]);

        // kop
        $kopTable = $section->addTable([
            'width'       => 100 * 50,
            'cellMargin'  => 0,
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
        ]);

        $kopTable->addRow();

        // logo
        $logoCell = $kopTable->addCell(2000, ['valign' => 'center']);
        $logoPath = public_path('images/logo-klinik.png');

        if (file_exists($logoPath)) {
            $logoCell->addImage($logoPath, [
                'width'     => 65,
                'height'    => 65,
                'alignment' => Jc::CENTER,
            ]);
        } else {
            $logoCell->addText('LOGO', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        }

        // teks
        $textCell = $kopTable->addCell(8000, ['valign' => 'center']);
        $textCell->addText(
            'LAPORAN RESERVASI',
            ['bold' => true, 'size' => 16, 'color' => '1e3a5f'],
            ['alignment' => 'center', 'spaceAfter' => 60]
        );
        $textCell->addText(
            'KLINIK GIGI ' . strtoupper($this->klinik->nama_klinik ?? '-'),
            ['bold' => true, 'size' => 13, 'color' => '1e3a5f'],
            ['alignment' => 'center', 'spaceAfter' => 40]
        );
        $textCell->addText(
            $this->klinik->alamat ?? '-',
            ['size' => 10, 'color' => '555555'],
            ['alignment' => 'center']
        );

        $section->addTextBreak(1);
        $section->addLine(['weight' => 2.0, 'width' => 450, 'height' => 0, 'color' => '1e3a5f']);
        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize'  => 8,
            'borderColor' => '1e3a5f',
            'cellMargin'  => 100,
            'alignment'   => Jc::CENTER,
        ]);

        $cellStyle = ['valign' => 'center'];
        $widths = [700, 2800, 1800, 1600, 2600, 2500, 1900];
        // HEADER
        $table->addRow(400);
        $headers = ['No', 'Nama Pasien', 'Tanggal', 'Jam', 'Dokter','Keluhan', 'Status'];

        foreach ($headers as $i => $header) {
            $cell = $table->addCell($widths[$i], $cellStyle);
            $cell->addText(
                $header,
                ['bold' => true, 'size' => 11, 'color' => 'FFFFFF'],
                ['alignment' => 'center']
            );
            $cell->getStyle()->setShading(['fill' => '0f766e']);
        }

        if (count($this->data) === 0) {
            $table->addRow(350);
            $cell = $table->addCell(array_sum($widths), ['gridSpan' => 6, 'valign' => 'center']);
            $cell->addText(
                'Tidak ada data reservasi.',
                ['size' => 10, 'italic' => true, 'color' => '9ca3af'],
                ['alignment' => 'center']
            );
        } else {
            $no = 1;
            foreach ($this->data as $item) {
                $table->addRow(350);

                $status = strtolower($item->status ?? '-');
                $statusColors = [
                    'menunggu'         => ['bg' => 'fef3c7', 'text' => '92400e'],
                    'dibatalkan'       => ['bg' => 'fee2e2', 'text' => '991b1b'],
                    'selesai'          => ['bg' => 'dcfce7', 'text' => '166534'],
                    'sedang diperiksa' => ['bg' => 'dbeafe', 'text' => '1e40af'],
                ];
                $statusColor = $statusColors[$status] ?? ['bg' => 'f3f4f6', 'text' => '374151'];

                $cells = [
                    $no++,
                    $item->pasien->nama ?? '-',
                    $item->tgl_reservasi ? \Carbon\Carbon::parse($item->tgl_reservasi)->format('d-m-Y') : '-',
                    $item->jam_reservasi ?? '-',
                    $item->jadwal->dokter->nama ?? '-',
                    $item->keluhan ?? '-',
                    ucfirst($status),
                ];

                foreach ($cells as $i => $value) {
                    $isStatus = $i === 6;
                    $cell = $table->addCell($widths[$i], $cellStyle);
                    $cell->addText(
                        (string) $value,
                        [
                            'size'  => 10,
                            'color' => $isStatus ? $statusColor['text'] : '1c1917',
                            'bold'  => $isStatus,
                        ],
                        ['alignment' => $i === 0 ? 'center' : 'left']
                    );

                    if ($isStatus) {
                        $cell->getStyle()->setShading(['fill' => $statusColor['bg']]);
                    }
                }
            }
        }
        $section->addTextBreak(2);
        // tanda tangan
        $ttdTable = $section->addTable([
            'width'      => 100 * 50,
            'borderSize' => 0,
        ]);
        $ttdTable->addRow();
        $ttdTable->addCell(5000);
        $ttdCell = $ttdTable->addCell(5000);
        $ttdCell->addText(
            'Purwokerto, ' . now()->format('d F Y'),
            ['size' => 11],
            ['alignment' => 'center']
        );
        $ttdCell->addTextBreak(2);
        $ttdCell->addText(
            '(_________________________)',
            ['size' => 11],
            ['alignment' => 'center']
        );
        $ttdCell->addText(
            'Admin Klinik',
            ['size' => 11, 'bold' => true],
            ['alignment' => 'center']
        );

        return $phpWord;
    }
    // simpan file ke storage dan return path
    public function save(string $filename = null): string
    {
        $filename = $filename ?? 'laporan-reservasi-' . now()->format('Ymd-His') . '.docx';
        $path = storage_path('app/public/' . $filename);

        IOFactory::createWriter($this->generate(), 'Word2007')->save($path);

        return $path;
    }
    // download file dan hapus setelah dikirim
    public function download(string $filename = null)
    {
        $filename = $filename ?? 'laporan-reservasi.docx';
        $path = $this->save($filename);

        return response()->download($path)->deleteFileAfterSend();
    }
}