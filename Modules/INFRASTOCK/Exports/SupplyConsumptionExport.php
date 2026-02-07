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

/**
 * Clase para exportar los consumos de insumos a Excel
 * Genera un archivo Excel con formato profesional
 */
class SupplyConsumptionExport implements FromView, WithStyles, WithColumnWidths, WithTitle, WithDrawings, WithEvents
{
    protected $consumptions;
    protected $topConsumed;
    protected $period;
    protected $periodLabel;
    protected $adminName;
    protected $stats;

    public function __construct($consumptions, $topConsumed, $period, $periodLabel, $stats)
    {
        $this->consumptions = $consumptions;
        $this->topConsumed = $topConsumed;
        $this->period = $period;
        $this->periodLabel = $periodLabel;
        $this->adminName = auth()->user()->name ?? 'Administrador del Sistema';
        $this->stats = $stats;
    }

    /**
     * Retorna la vista que se usará para el Excel
     */
    public function view(): View
    {
        return view('infrastock::admin.supply-requests.exports.excel', [
            'consumptions' => $this->consumptions,
            'topConsumed' => $this->topConsumed,
            'period' => $this->period,
            'periodLabel' => $this->periodLabel,
            'adminName' => $this->adminName,
            'stats' => $this->stats,
        ]);
    }

    /**
     * Título de la hoja de Excel
     */
    public function title(): string
    {
        return 'Consumos de Insumos';
    }

    /**
     * Agrega el logo al documento
     */
    public function drawings()
    {
        $logoPath = public_path('assets/img/logo.png');
        
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo INFRASTOCK');
            $drawing->setDescription('Logo del Sistema INFRASTOCK');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('H1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWidth(100);
            $drawing->setHeight(85);
            
            return $drawing;
        }
        
        return [];
    }

    /**
     * Anchos de columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // #
            'B' => 28,  // Insumo
            'C' => 18,  // Categoría
            'D' => 12,  // Cantidad
            'E' => 14,  // Unidad
            'F' => 22,  // Solicitante
            'G' => 18,  // Rol
            'H' => 14,  // Fecha
            'I' => 25,  // Destino
        ];
    }

    /**
     * Eventos para manejar celdas combinadas después de crear la hoja
     */
    public function registerEvents(): array
    {
        $topCount = $this->topConsumed->count();
        
        return [
            AfterSheet::class => function(AfterSheet $event) use ($topCount) {
                $sheet = $event->sheet->getDelegate();
                
                // Combinar celdas para el encabezado institucional
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                $sheet->mergeCells('A4:G4');
                $sheet->mergeCells('A5:G5');
                $sheet->mergeCells('A7:I7');
                $sheet->mergeCells('A8:I8');
                
                // Combinar celdas para estadísticas
                $sheet->mergeCells('A10:C10');
                $sheet->mergeCells('D10:F10');
                $sheet->mergeCells('G10:I10');
                $sheet->mergeCells('A11:C11');
                $sheet->mergeCells('D11:F11');
                $sheet->mergeCells('G11:I11');
                
                // Combinar para sección de top consumidos
                $sheet->mergeCells('A13:I13');
                
                // Combinar para encabezados de top
                $sheet->mergeCells('A14:B14');
                $sheet->mergeCells('C14:D14');
                $sheet->mergeCells('E14:F14');
                $sheet->mergeCells('G14:I14');
                
                // Combinar filas de top consumidos
                for ($i = 0; $i < $topCount; $i++) {
                    $row = 15 + $i;
                    $sheet->mergeCells("A{$row}:B{$row}");
                    $sheet->mergeCells("C{$row}:D{$row}");
                    $sheet->mergeCells("E{$row}:F{$row}");
                    $sheet->mergeCells("G{$row}:I{$row}");
                }
            },
        ];
    }

    /**
     * Estilos para la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        $topCount = $this->topConsumed->count();
        $dataStartRow = 15 + $topCount + 2; // Después de top consumidos + espacio + encabezado
        $lastRow = $this->consumptions->count() + $dataStartRow - 1;

        // Fila 1: Espacio para logo
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Fila 2: Título institucional principal
        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1565C0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Filas 3-5: Info institucional
        foreach ([3, 4, 5] as $row) {
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => '555555'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
        }

        // Fila 6: Vacía
        $sheet->getRowDimension(6)->setRowHeight(8);

        // Fila 7: Título del reporte
        $sheet->getStyle('A7:I7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E88E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(28);

        // Fila 8: Período y fecha
        $sheet->getStyle('A8:I8')->applyFromArray([
            'font' => [
                'size' => 9,
                'color' => ['rgb' => '666666'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(18);

        // Fila 9: Vacía
        $sheet->getRowDimension(9)->setRowHeight(8);

        // Filas 10-11: Estadísticas
        $sheet->getStyle('A10:I11')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '90CAF9'],
                ],
            ],
        ]);
        $sheet->getStyle('A10:I10')->getFont()->setBold(true)->setSize(8)->getColor()->setRGB('555555');
        $sheet->getStyle('A11:I11')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('1565C0');
        $sheet->getRowDimension(10)->setRowHeight(20);
        $sheet->getRowDimension(11)->setRowHeight(25);

        // Fila 12: Vacía
        $sheet->getRowDimension(12)->setRowHeight(8);

        // Fila 13: Título Top Consumidos
        $sheet->getStyle('A13:I13')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF9800'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(13)->setRowHeight(24);

        // Fila 14: Encabezados de top consumidos
        $sheet->getStyle('A14:I14')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['rgb' => '333333'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3E0'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFB74D'],
                ],
            ],
        ]);
        $sheet->getRowDimension(14)->setRowHeight(22);

        // Filas de top consumidos
        for ($i = 0; $i < $topCount; $i++) {
            $row = 15 + $i;
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
            if ($i % 2 == 1) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF8E1'],
                    ],
                ]);
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // Fila vacía después de top
        $emptyRow = 15 + $topCount;
        $sheet->getRowDimension($emptyRow)->setRowHeight(8);

        // Encabezados de tabla de detalle
        $headerRow = $emptyRow + 1;
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '43A047'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '2E7D32'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        // Estilos para los datos
        $dataStartRow = $headerRow + 1;
        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle("A{$dataStartRow}:I{$lastRow}")->applyFromArray([
                'font' => [
                    'size' => 9,
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);

            // Centrar columnas específicas
            $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$dataStartRow}:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$dataStartRow}:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternar colores de filas
            for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                if (($i - $dataStartRow) % 2 == 1) {
                    $sheet->getStyle("A{$i}:I{$i}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F5F5F5'],
                        ],
                    ]);
                }
            }
        }

        // Congelar filas de encabezado
        $sheet->freezePane("A{$dataStartRow}");

        return [];
    }
}
