<?php

namespace App\Exports;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use App\Models\Klinik;

class RekamMedisWordExport
{
    protected $data;
    protected $klinik;

    public function __construct($data, $klinikId = null)
    {
        $this->data = $data;
        $this->klinik = Klinik::find($klinikId) ?? Klinik::first();
    }

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

        // logo kiri
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

        // teks kanan
        $textCell = $kopTable->addCell(8000, ['valign' => 'center']);
        $textCell->addText(
            'LAPORAN REKAM MEDIS',
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

        // garis pemisah
        $section->addTextBreak(1);
        $section->addLine(['weight' => 1.5, 'width' => 450, 'height' => 0, 'color' => '1e3a5f']);
        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize'  => 8,
            'borderColor' => '1e3a5f',
            'cellMargin'  => 100,
            'alignment'   => Jc::CENTER,
        ]);

        $cellStyle = ['valign' => 'center'];
        $widths = [700, 1600, 2200, 1800, 2200, 2400, 1800];

        // HEADER
        $table->addRow(400);
        $headers = ['No', 'Tanggal', 'Nama Pasien', 'No HP', 'Dokter', 'Diagnosa', 'Klinik'];

        foreach ($headers as $i => $header) {
            $cell = $table->addCell($widths[$i], $cellStyle);
            $cell->addText(
                $header,
                ['bold' => true, 'size' => 11, 'color' => 'FFFFFF'],
                ['alignment' => 'center']
            );
            $cell->getStyle()->setShading(['fill' => '0f766e']);
        }

        // data
        if (count($this->data) === 0) {
            $table->addRow(350);
            $cell = $table->addCell(array_sum($widths), ['gridSpan' => 7, 'valign' => 'center']);
            $cell->addText(
                'Tidak ada data rekam medis.',
                ['size' => 10, 'italic' => true, 'color' => '9ca3af'],
                ['alignment' => 'center']
            );
        } else {
            $no = 1;
            foreach ($this->data as $item) {
                $table->addRow(350);

                $cells = [
                    $no++,
                    $item->tgl_periksa ? \Carbon\Carbon::parse($item->tgl_periksa)->format('d-m-Y') : '-',
                    $item->reservasi->pasien->nama ?? '-',
                    $item->reservasi->pasien->no_hp ?? '-',
                    $item->dokter->nama ?? '-',
                    $item->diagnosa ?? '-',
                    $item->reservasi->jadwal->klinik->nama_klinik ?? '-',
                ];

                foreach ($cells as $i => $value) {
                    $cell = $table->addCell($widths[$i], $cellStyle);
                    $cell->addText(
                        (string) $value,
                        ['size' => 10, 'color' => '1c1917'],
                        ['alignment' => $i === 0 ? 'center' : 'left']
                    );
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
        $filename = $filename ?? 'laporan-rekam-medis-' . now()->format('Ymd-His') . '.docx';
        $path = storage_path('app/public/' . $filename);

        IOFactory::createWriter($this->generate(), 'Word2007')->save($path);

        return $path;
    }
    // download file dan hapus setelah dikirim
    public function download(string $filename = null)
    {
        $filename = $filename ?? 'laporan-rekam-medis.docx';
        $path = $this->save($filename);

        return response()->download($path)->deleteFileAfterSend();
    }
}