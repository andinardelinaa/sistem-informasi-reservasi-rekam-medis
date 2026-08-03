<?php

namespace App\Exports;

use App\Models\Reservasi;
use App\Models\Klinik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ReservasiExport implements FromCollection, WithEvents, WithCustomStartCell
{
    protected $klinik;
    protected $dataCount = 0;

    public function __construct($klinikId = null)
    {
        $this->klinik = Klinik::find($klinikId) ?? Klinik::first();
    }
    public function startCell(): string
    {
        return 'A6';
    }
    // mapping data untuk export
    public function collection()
    {
        $this->rows = Reservasi::with(['pasien', 'jadwal.dokter'])
            ->orderBy('tgl_reservasi', 'desc')
            ->orderBy('jam_reservasi', 'asc')
            ->get()
            ->map(function ($r) {
                return [
                    'nama_pasien' => $r->pasien->nama ?? '-',
                    'tanggal'     => $r->tgl_reservasi ? Carbon::parse($r->tgl_reservasi)->format('d-m-Y') : '-',
                    'jam'         => $r->jam_reservasi ?? '-',
                    'dokter'      => $r->jadwal->dokter->nama ?? '-',
                    'keluhan'     => $r->keluhan ?? '-',
                    'status'      => ucfirst($r->status ?? '-'),
                ];
            });

        $this->dataCount = $this->rows->count();

        return $this->rows;
    }
    // styling dan menambahkan logo
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = 5 + $this->dataCount; // 5 baris kop + data

                // logo
                $logoPath = public_path('images/logo-klinik.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo Klinik');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(60);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                }

                // kop
                $sheet->mergeCells('B1:F1');
                $sheet->setCellValue('B1', 'LAPORAN RESERVASI');
                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->mergeCells('B2:F2');
                $sheet->setCellValue('B2', 'KLINIK GIGI ' . strtoupper($this->klinik->nama_klinik ?? '-'));
                $sheet->getStyle('B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->mergeCells('B3:F3');
                $sheet->setCellValue('B3', $this->klinik->alamat ?? '-');
                $sheet->getStyle('B3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Row height kop
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // Garis pemisah (border bottom row 3 tebal)
                $sheet->getStyle('A3:F3')->applyFromArray([
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1e3a5f']],
                    ],
                ]);

                $headers = ['No', 'Nama Pasien', 'Tanggal', 'Jam', 'Dokter', 'Keluhan', 'Status'];
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

                foreach ($headers as $i => $header) {
                    $sheet->setCellValue($columns[$i] . '5', $header);
                }

                $sheet->getStyle('A5:G5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID,'startColor' => ['rgb' => '0f766e']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1e3a5f']],
                    ],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(25);
            
                $row = 6;
                $no = 1;
                foreach ($this->rows as $item) {
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $item['nama_pasien']);
                    $sheet->setCellValue('C' . $row, $item['tanggal']);
                    $sheet->setCellValue('D' . $row, $item['jam']);
                    $sheet->setCellValue('E' . $row, $item['dokter']);
                    $sheet->setCellValue('F' . $row, $item['keluhan']);
                    $sheet->setCellValue('G' . $row, $item['status']);

                    // Border & alignment default
                    $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0f766e']],
                        ],
                        'alignment' => ['vertical' => 'center'],
                    ]);

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');

                    // Warna status
                    $status = strtolower($item['status']);
                    $colors = [
                        'Menunggu'         => ['bg' => 'fef3c7', 'text' => '92400e'],
                        'Dibatalkan'       => ['bg' => 'fee2e2', 'text' => '991b1b'],
                        'Selesai'          => ['bg' => 'dcfce7', 'text' => '166534'],
                        'Sedang diperiksa' => ['bg' => 'dbeafe', 'text' => '1e40af'],
                    ];
                    $color = $colors[ucfirst($status)] ?? ['bg' => 'f3f4f6', 'text' => '374151'];

                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color['bg']]],
                        'font' => ['color' => ['rgb' => $color['text']], 'bold' => true],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);

                    $row++;
                }

                // Kalau data kosong
                if ($this->dataCount === 0) {
                    $sheet->mergeCells('A6:G6');
                    $sheet->setCellValue('A6', 'Tidak ada data reservasi.');
                    $sheet->getStyle('A6')->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '9ca3af']],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                    $lastRow = 6;
                }

                //tanda tangan
                $ttdRow = $lastRow + 2;
                $sheet->mergeCells("E{$ttdRow}:G{$ttdRow}");
                $sheet->setCellValue("E{$ttdRow}", 'Purwokerto, ' . now()->format('d F Y'));
                $sheet->getStyle("E{$ttdRow}")->getAlignment()->setHorizontal('center');

                $ttdRow += 3;
                $sheet->mergeCells("E{$ttdRow}:G{$ttdRow}");
                $sheet->setCellValue("E{$ttdRow}", '(_________________________)');
                $sheet->getStyle("E{$ttdRow}")->getAlignment()->setHorizontal('center');

                $ttdRow += 1;
                $sheet->mergeCells("E{$ttdRow}:G{$ttdRow}");
                $sheet->setCellValue("E{$ttdRow}", 'Admin Klinik');
                $sheet->getStyle("E{$ttdRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(26);
                $sheet->getColumnDimension('F')->setWidth(30);
                $sheet->getColumnDimension('G')->setWidth(14);
            },
        ];
    }
}