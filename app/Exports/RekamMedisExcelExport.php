<?php

namespace App\Exports;

use App\Models\Klinik;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;

class RekamMedisExcelExport implements FromCollection, WithEvents, WithCustomStartCell
{
    protected $klinik;
    protected $data;
    protected $rows;
    protected $dataCount = 0;

    public function __construct($data = null, $klinikId = null)
    {
        $this->data = $data;
        $this->klinik = Klinik::find($klinikId) ?? Klinik::first();
    }

    public function startCell(): string
    {
        return 'A5';
    }

    // mapping data untuk export
    public function collection()
    {
        $this->rows = collect($this->data ?? []);
        $this->dataCount = $this->rows->count();

        return $this->rows->map(function ($item) {
            return [
                'no'          => '',
                'tanggal'     => $item->tgl_periksa
                    ? Carbon::parse($item->tgl_periksa)->format('d-m-Y')
                    : '-',
                'nama_pasien' => $item->reservasi->pasien->nama ?? '-',
                'no_hp'       => $item->reservasi->pasien->no_hp ?? '-',
                'dokter'      => $item->dokter->nama ?? '-',
                'diagnosa'    => $item->diagnosa ?? '-',
                'klinik'      => $item->reservasi->jadwal->klinik->nama_klinik ?? '-',
            ];
        });
    }

    //styling dan menambahkan logo
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $lastRow = 5 + $this->dataCount;

                //logo
                $logoPath = public_path('images/logo-klinik.png');

                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setPath($logoPath);
                    $drawing->setCoordinates('A1');
                    $drawing->setHeight(60);
                    $drawing->setWorksheet($sheet);
                }

                //kop
                $sheet->mergeCells('B1:G1');
                $sheet->setCellValue('B1', 'LAPORAN REKAM MEDIS');

                $sheet->mergeCells('B2:G2');
                $sheet->setCellValue('B2', 'KLINIK GIGI ' . strtoupper($this->klinik->nama_klinik ?? '-'));

                $sheet->mergeCells('B3:G3');
                $sheet->setCellValue('B3', $this->klinik->alamat ?? '-');

                $sheet->getStyle('B1:B3')->getAlignment()->setHorizontal('center');

                // garis bawah
                $sheet->getStyle('A3:G3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '0f766e'],
                        ],
                    ],
                ]);
                $headers = ['No', 'Tanggal', 'Nama Pasien', 'No HP', 'Dokter', 'Diagnosa', 'Klinik'];
                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

                foreach ($headers as $i => $h) {
                    $sheet->setCellValue($columns[$i] . '5', $h);
                }

                $sheet->getStyle('A5:G5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0f766e'],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                //data
                if ($this->dataCount == 0) {
                    $sheet->mergeCells('A6:G6');
                    $sheet->setCellValue('A6', 'Tidak ada data rekam medis');
                }

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
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}