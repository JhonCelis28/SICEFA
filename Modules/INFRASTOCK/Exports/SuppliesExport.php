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
 * Clase para exportar los insumos a Excel
 * Genera un archivo Excel con formato profesional
 */
class SuppliesExport implements FromView, WithStyles, WithColumnWidths, WithTitle, WithDrawings, WithEvents
{
    protected $supplies;
    protected $adminName;

    public function __construct($supplies)
    {
        $this->supplies = $supplies;
        $this->adminName = auth()->user()->name ?? 'Administrador del Sistema';
    }

    /**
     * Retorna la vista que se usará para el Excel
     */
    public function view(): View
    {
        // Calcular estadísticas
        $totalInicial = $this->supplies->sum('initial_amount');
        $totalConsumos = $this->supplies->sum('used_amount');
        $totalStock = $this->supplies->sum('stock');
        $disponibles = $this->supplies->where('status', 'disponible')->count();
        $criticos = $this->supplies->whereIn('status', ['agotado', 'critico', 'vencido'])->count();
        $bajoStock = $this->supplies->where('status', 'bajo_stock')->count();

        return view('infrastock::admin.supplies.exports.excel', [
            'supplies' => $this->supplies,
            'adminName' => $this->adminName,
            'totalInicial' => $totalInicial,
            'totalConsumos' => $totalConsumos,
            'totalStock' => $totalStock,
            'disponibles' => $disponibles,
            'criticos' => $criticos,
            'bajoStock' => $bajoStock,
        ]);
    }

    /**
     * Título de la hoja de Excel
     */
    public function title(): string
    {
        return 'Inventario de Insumos';
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
            $drawing->setCoordinates('J1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWidth(120);
            $drawing->setHeight(100);
            
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
            'B' => 28,  // Nombre
            'C' => 18,  // Categoría
            'D' => 32,  // Características
            'E' => 12,  // Cantidad Inicial
            'F' => 12,  // Consumos
            'G' => 12,  // Stock
            'H' => 10,  // Mínimo
            'I' => 12,  // Estado
            'J' => 14,  // Unidad Medida
            'K' => 14,  // Vencimiento
            'L' => 32,  // Observaciones
        ];
    }

    /**
     * Eventos para manejar celdas combinadas después de crear la hoja
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Combinar celdas para el encabezado institucional
                $sheet->mergeCells('A2:I2');
                $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('A4:I4');
                $sheet->mergeCells('A5:I5');
                $sheet->mergeCells('A7:L7');
                $sheet->mergeCells('A8:L8');
                
                // Combinar celdas para estadísticas (títulos)
                $sheet->mergeCells('A10:B10');
                $sheet->mergeCells('C10:D10');
                $sheet->mergeCells('E10:F10');
                $sheet->mergeCells('G10:H10');
                $sheet->mergeCells('I10:J10');
                $sheet->mergeCells('K10:L10');
                
                // Combinar celdas para estadísticas (valores)
                $sheet->mergeCells('A11:B11');
                $sheet->mergeCells('C11:D11');
                $sheet->mergeCells('E11:F11');
                $sheet->mergeCells('G11:H11');
                $sheet->mergeCells('I11:J11');
                $sheet->mergeCells('K11:L11');
            },
        ];
    }

    /**
     * Estilos para la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        $dataStartRow = 14;
        $lastRow = $this->supplies->count() + $dataStartRow - 1;

        // Fila 1: Espacio para logo
        $sheet->getRowDimension(1)->setRowHeight(35);
        
        // Fila 2: Título institucional principal
        $sheet->getStyle('A2:L2')->applyFromArray([
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

        // Fila 3: Regional
        $sheet->getStyle('A3:L3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '333333'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Fila 4: Área
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '555555'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(16);

        // Fila 5: Responsable
        $sheet->getStyle('A5:L5')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '555555'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(16);

        // Fila 6: Vacía
        $sheet->getRowDimension(6)->setRowHeight(10);

        // Fila 7: Título del documento
        $sheet->getStyle('A7:L7')->applyFromArray([
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

        // Fila 8: Fecha y total
        $sheet->getStyle('A8:L8')->applyFromArray([
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

        // Fila 10: Títulos de estadísticas
        $sheet->getStyle('A10:L10')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 8,
                'color' => ['rgb' => '555555'],
            ],
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
        $sheet->getRowDimension(10)->setRowHeight(22);

        // Fila 11: Valores de estadísticas
        $sheet->getStyle('A11:L11')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '1565C0'],
            ],
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
        $sheet->getRowDimension(11)->setRowHeight(25);

        // Colores específicos para estadísticas
        $sheet->getStyle('G11:H11')->getFont()->getColor()->setRGB('2E7D32');
        $sheet->getStyle('I11:J11')->getFont()->getColor()->setRGB('F57F17');
        $sheet->getStyle('K11:L11')->getFont()->getColor()->setRGB('C62828');

        // Fila 12: Vacía
        $sheet->getRowDimension(12)->setRowHeight(8);

        // Fila 13: Encabezados de tabla
        $sheet->getStyle('A13:L13')->applyFromArray([
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
        $sheet->getRowDimension(13)->setRowHeight(28);

        // Estilos para los datos
        if ($lastRow >= $dataStartRow) {
            $sheet->getStyle('A' . $dataStartRow . ':L' . $lastRow)->applyFromArray([
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
            $sheet->getStyle('A' . $dataStartRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $dataStartRow . ':H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $dataStartRow . ':K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternar colores de filas
            for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                if (($i - $dataStartRow) % 2 == 1) {
                    $sheet->getStyle('A' . $i . ':L' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F5F5F5'],
                        ],
                    ]);
                }
            }

            // Resaltar estados
            for ($i = $dataStartRow; $i <= $lastRow; $i++) {
                $status = $sheet->getCell('I' . $i)->getValue();
                $color = match(strtolower($status ?? '')) {
                    'disponible' => 'C8E6C9',
                    'agotado' => 'FFCDD2',
                    'vencido' => 'FFE0B2',
                    'bajo stock' => 'FFF9C4',
                    'crítico', 'critico' => 'FFCDD2',
                    default => 'FFFFFF',
                };
                if ($color !== 'FFFFFF') {
                    $sheet->getStyle('I' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color],
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ]);
                }
            }
        }

        // Congelar filas de encabezado
        $sheet->freezePane('A14');

        return [];
    }
}
