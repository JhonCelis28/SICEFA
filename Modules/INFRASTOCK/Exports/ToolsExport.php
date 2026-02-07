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

class ToolsExport implements FromView, WithStyles, WithColumnWidths, WithTitle, WithDrawings, WithEvents
{
    protected $tools;
    protected $adminName;

    public function __construct($tools)
    {
        $this->tools = $tools;
        $this->adminName = auth()->user()->name ?? 'Administrador del Sistema';
    }

    public function view(): View
    {
        $totalTools = $this->tools->count();
        $totalStock = $this->tools->sum('cantidad_total');
        $totalDisponible = $this->tools->sum('cantidad_disponible');
        $disponibles = $this->tools->where('estado', 'disponible')->count();
        $enPrestamo = $this->tools->where('estado', 'en_prestamo')->count();
        $mantenimiento = $this->tools->where('estado', 'mantenimiento')->count();

        return view('infrastock::admin.tools.exports.excel', [
            'tools' => $this->tools,
            'adminName' => $this->adminName,
            'totalTools' => $totalTools,
            'totalStock' => $totalStock,
            'totalDisponible' => $totalDisponible,
            'disponibles' => $disponibles,
            'enPrestamo' => $enPrestamo,
            'mantenimiento' => $mantenimiento,
        ]);
    }

    public function title(): string
    {
        return 'Inventario de Herramientas';
    }

    public function drawings()
    {
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo INFRASTOCK');
            $drawing->setDescription('Logo del Sistema INFRASTOCK');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('I1');
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
            'B' => 28,
            'C' => 16,
            'D' => 30,
            'E' => 14,
            'F' => 14,
            'G' => 14,
            'H' => 14,
            'I' => 14,
            'J' => 16,
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
                $sheet->mergeCells('A7:J7');
                $sheet->mergeCells('A8:J8');
                // Stats
                $sheet->mergeCells('A10:B10');
                $sheet->mergeCells('C10:D10');
                $sheet->mergeCells('E10:F10');
                $sheet->mergeCells('G10:H10');
                $sheet->mergeCells('I10:J10');
                $sheet->mergeCells('A11:B11');
                $sheet->mergeCells('C11:D11');
                $sheet->mergeCells('E11:F11');
                $sheet->mergeCells('G11:H11');
                $sheet->mergeCells('I11:J11');
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataStartRow = 14;
        $lastRow = $this->tools->count() + $dataStartRow - 1;

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->getStyle('A3:J3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(16);

        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(16);

        $sheet->getRowDimension(6)->setRowHeight(10);

        $sheet->getStyle('A7:J7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E65100']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(28);

        $sheet->getStyle('A8:J8')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(18);

        $sheet->getRowDimension(9)->setRowHeight(8);

        $sheet->getStyle('A10:J10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '555555']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFE0B2']]],
        ]);
        $sheet->getRowDimension(10)->setRowHeight(22);

        $sheet->getStyle('A11:J11')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'E65100']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFE0B2']]],
        ]);
        $sheet->getRowDimension(11)->setRowHeight(25);

        $sheet->getStyle('E11:F11')->getFont()->getColor()->setRGB('2E7D32');
        $sheet->getStyle('G11:H11')->getFont()->getColor()->setRGB('F57F17');
        $sheet->getStyle('I11:J11')->getFont()->getColor()->setRGB('C62828');

        $sheet->getRowDimension(12)->setRowHeight(8);

        $sheet->getStyle('A13:J13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E65100']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BF360C']]],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(28);

        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':J' . $lastRow)->applyFromArray([
                'font' => ['size' => 9],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
            ]);

            $sheet->getStyle('A' . $dataStartRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $dataStartRow . ':I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                if (($i - $dataStartRow) % 2 == 1) {
                    $sheet->getStyle('A' . $i . ':J' . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                    ]);
                }
            }
        }

        $sheet->freezePane('A14');

        return [];
    }
}
