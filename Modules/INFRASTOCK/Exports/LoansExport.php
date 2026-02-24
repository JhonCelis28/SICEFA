<?php

namespace Modules\INFRASTOCK\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LoansExport implements FromView, WithStyles, WithColumnWidths, WithTitle, WithDrawings, WithEvents
{
    protected $loans;
    protected $stats;
    protected $periodLabel;
    protected $adminName;

    public function __construct($loans, $stats, $periodLabel)
    {
        $this->loans = $loans;
        $this->stats = $stats;
        $this->periodLabel = $periodLabel;
        $this->adminName = auth()->user()->name ?? 'Administrador del Sistema';
    }

    public function view(): View
    {
        return view('infrastock::admin.loans.exports.excel', [
            'loans' => $this->loans,
            'stats' => $this->stats,
            'periodLabel' => $this->periodLabel,
            'adminName' => $this->adminName,
        ]);
    }

    public function title(): string
    {
        return 'Préstamos de Herramientas';
    }

    public function drawings()
    {
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo INFRASTOCK');
            $drawing->setDescription('Logo del Sistema INFRASTOCK');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('G1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWidth(120);
            $drawing->setHeight(100);
            return $drawing;
        }
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 24,
            'C' => 22,
            'D' => 12,
            'E' => 22,
            'F' => 14,
            'G' => 14,
            'H' => 18,
            'I' => 40,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                $sheet->mergeCells('A4:H4');
                $sheet->mergeCells('A5:H5');
                $sheet->mergeCells('A7:I7');
                $sheet->mergeCells('A8:I8');
                // Stats
                $sheet->mergeCells('A10:B10');
                $sheet->mergeCells('C10:D10');
                $sheet->mergeCells('E10:F10');
                $sheet->mergeCells('G10:H10');
                $sheet->mergeCells('A11:B11');
                $sheet->mergeCells('C11:D11');
                $sheet->mergeCells('E11:F11');
                $sheet->mergeCells('G11:H11');
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataStartRow = 14;
        $lastRow = $this->loans->count() + $dataStartRow - 1;

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->getStyle('A3:I3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(16);

        $sheet->getStyle('A5:I5')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(16);

        $sheet->getRowDimension(6)->setRowHeight(10);

        $sheet->getStyle('A7:I7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(28);

        $sheet->getStyle('A8:I8')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(18);

        $sheet->getRowDimension(9)->setRowHeight(8);

        $sheet->getStyle('A10:H10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '555555']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '90CAF9']]],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(22);

        $sheet->getStyle('A11:H11')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1565C0']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '90CAF9']]],
        ]);
        $sheet->getRowDimension(11)->setRowHeight(25);

        $sheet->getStyle('E11:F11')->getFont()->getColor()->setRGB('2E7D32');
        $sheet->getStyle('G11:H11')->getFont()->getColor()->setRGB('C62828');

        $sheet->getRowDimension(12)->setRowHeight(8);

        $sheet->getStyle('A13:I13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0D47A1']]],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(28);

        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':I' . $lastRow)->applyFromArray([
                'font' => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
            ]);

            $sheet->getStyle('A' . $dataStartRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $dataStartRow . ':G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                if (($i - $dataStartRow) % 2 == 1) {
                    $sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                    ]);
                }
            }
        }

        $sheet->freezePane('A14');

        return [];
    }
}
