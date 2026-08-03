<?php

namespace App\Exports;

use App\Models\Klinik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class HariLiburExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    protected $hariLibur;
    protected $klinik;

    public function __construct($hariLibur, $klinikId = null)
    {
        $this->hariLibur = $hariLibur;
        $this->klinik = Klinik::find($klinikId) ?? Klinik::first();
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        return $this->hariLibur;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Keterangan',
            'Hari',
        ];
    }

    public function map($hariLibur): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $hariLibur->tanggal,
            $hariLibur->keterangan,
            Carbon::parse($hariLibur->tanggal)->isoFormat('dddd'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $dataCount = $this->hariLibur->count();
                $lastRow = 5 + $dataCount;

                // logo
                $logoPath = public_path('images/logo-klinik.png');

                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                // header kop
                $sheet->mergeCells('B1:D1');
                $sheet->setCellValue('B1', 'LAPORAN HARI LIBUR');
                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->mergeCells('B2:D2');
                $sheet->setCellValue('B2', 'KLINIK GIGI ' . strtoupper($this->klinik->nama_klinik ?? '-'));
                $sheet->getStyle('B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->mergeCells('B3:D3');
                $sheet->setCellValue('B3', $this->klinik->alamat ?? '-');
                $sheet->getStyle('B3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // garis bawah biru tua
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1e3a5f'],
                        ],
                    ],
                ]);

                $headers = ['No', 'Tanggal', 'Keterangan', 'Hari'];
                $columns = ['A', 'B', 'C', 'D'];

                foreach ($headers as $i => $h) {
                    $sheet->setCellValue($columns[$i] . '5', $h);
                }

                $sheet->getStyle('A5:D5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0f766e'],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1e3a5f'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(25);

               
                $row = 6;
                $no = 1;

                if ($dataCount == 0) {
                    $sheet->mergeCells('A6:D6');
                    $sheet->setCellValue('A6', 'Tidak ada data hari libur.');
                    $sheet->getStyle('A6')->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '9ca3af']],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ]);
                    $lastRow = 6;
                } else {
                    for ($i = 0; $i < $dataCount; $i++) {
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'd1d5db'],
                                ],
                            ],
                            'alignment' => ['vertical' => 'center'],
                        ]);

                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');

                        $row++;
                    }
                    $lastRow = $row - 1;
                }

                // tanda tangan
                $ttdRow = $lastRow + 2;
                $sheet->mergeCells("C{$ttdRow}:D{$ttdRow}");
                $sheet->setCellValue("C{$ttdRow}", 'Purwokerto, ' . now()->format('d F Y'));
                $sheet->getStyle("C{$ttdRow}")->getAlignment()->setHorizontal('center');

                $ttdRow += 3;
                $sheet->mergeCells("C{$ttdRow}:D{$ttdRow}");
                $sheet->setCellValue("C{$ttdRow}", '(_________________________)');
                $sheet->getStyle("C{$ttdRow}")->getAlignment()->setHorizontal('center');

                $ttdRow += 1;
                $sheet->mergeCells("C{$ttdRow}:D{$ttdRow}");
                $sheet->setCellValue("C{$ttdRow}", 'Admin Klinik');
                $sheet->getStyle("C{$ttdRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(16);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(18);
            },
        ];
    }
}