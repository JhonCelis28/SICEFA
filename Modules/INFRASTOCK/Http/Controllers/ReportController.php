<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\ProductiveUnit;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

/**
 * @class ReportController
 * @brief Controlador para la generación de reportes del módulo INFRASTOCK.
 */
class ReportController extends Controller
{
    /**
     * Muestra el reporte de Consumo de Insumos por Área
     */
    public function consumptionByArea(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        
        // Obtener años disponibles
        $availableYears = WarehouseMovement::where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }
        
        // Query base
        $query = WarehouseMovement::selectRaw('
                SUM(warehouse_movements.amount) as total_amount, 
                productive_units.name as area_name,
                productive_units.id as area_id
            ')
            ->join('productive_unit_warehouses', 'warehouse_movements.productive_unit_warehouse_id', '=', 'productive_unit_warehouses.id')
            ->join('productive_units', 'productive_unit_warehouses.productive_unit_id', '=', 'productive_units.id')
            ->where('warehouse_movements.item_type', 'equipment')
            ->where('warehouse_movements.role', 'Entrega')
            ->whereYear('warehouse_movements.created_at', $year);
        
        if ($month) {
            $query->whereMonth('warehouse_movements.created_at', $month);
        }
        
        $consumptionByArea = $query->groupBy('productive_units.id', 'productive_units.name')
            ->orderByDesc('total_amount')
            ->get();
        
        // Detalle de consumos
        $detailQuery = WarehouseMovement::with(['equipment.category', 'user.person', 'productiveUnitWarehouse.productiveUnit'])
            ->where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $detailQuery->whereMonth('created_at', $month);
        }
        
        $consumptionDetails = $detailQuery->orderBy('created_at', 'desc')->get();
        
        // Top 10 insumos más consumidos
        $topSuppliesQuery = WarehouseMovement::with('equipment')
            ->select('equipment_id')
            ->selectRaw('SUM(amount) as total_consumed')
            ->where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $topSuppliesQuery->whereMonth('created_at', $month);
        }
        
        $topSupplies = $topSuppliesQuery->groupBy('equipment_id')
            ->orderByDesc('total_consumed')
            ->limit(10)
            ->get();
        
        // Estadísticas
        $totalConsumed = $consumptionByArea->sum('total_amount');
        $totalAreas = $consumptionByArea->count();
        $totalTransactions = $consumptionDetails->count();
        
        // Meses disponibles para el año seleccionado
        $availableMonths = WarehouseMovement::where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();
        
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return view('infrastock::admin.reports.consumption-by-area', compact(
            'consumptionByArea',
            'consumptionDetails',
            'topSupplies',
            'totalConsumed',
            'totalAreas',
            'totalTransactions',
            'year',
            'month',
            'availableYears',
            'availableMonths',
            'monthNames'
        ));
    }
    
    /**
     * Exporta el reporte de Consumo por Área a PDF
     */
    public function consumptionByAreaPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        
        $query = WarehouseMovement::selectRaw('
                SUM(warehouse_movements.amount) as total_amount, 
                productive_units.name as area_name
            ')
            ->join('productive_unit_warehouses', 'warehouse_movements.productive_unit_warehouse_id', '=', 'productive_unit_warehouses.id')
            ->join('productive_units', 'productive_unit_warehouses.productive_unit_id', '=', 'productive_units.id')
            ->where('warehouse_movements.item_type', 'equipment')
            ->where('warehouse_movements.role', 'Entrega')
            ->whereYear('warehouse_movements.created_at', $year);
        
        if ($month) {
            $query->whereMonth('warehouse_movements.created_at', $month);
        }
        
        $consumptionByArea = $query->groupBy('productive_units.name')
            ->orderByDesc('total_amount')
            ->get();
        
        $detailQuery = WarehouseMovement::with(['equipment.category', 'user.person', 'productiveUnitWarehouse.productiveUnit'])
            ->where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $detailQuery->whereMonth('created_at', $month);
        }
        
        $consumptionDetails = $detailQuery->orderBy('created_at', 'desc')->get();
        
        $topSuppliesQuery = WarehouseMovement::with('equipment')
            ->select('equipment_id')
            ->selectRaw('SUM(amount) as total_consumed')
            ->where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $topSuppliesQuery->whereMonth('created_at', $month);
        }
        
        $topSupplies = $topSuppliesQuery->groupBy('equipment_id')
            ->orderByDesc('total_consumed')
            ->limit(10)
            ->get();
        
        $totalConsumed = $consumptionByArea->sum('total_amount');
        $adminName = auth()->user()->name ?? 'Administrador';
        
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $periodLabel = $month ? $monthNames[$month] . ' ' . $year : 'Año ' . $year;
        
        // Logo en base64
        $logoPath = public_path('assets/img/logo.png');
        $base64Logo = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
        }
        
        $pdf = PDF::loadView('infrastock::admin.reports.exports.consumption-pdf', compact(
            'consumptionByArea',
            'consumptionDetails',
            'topSupplies',
            'totalConsumed',
            'periodLabel',
            'adminName',
            'base64Logo'
        ));
        
        $pdf->setPaper('letter', 'landscape');
        
        return $pdf->stream('consumo_por_area_' . $year . ($month ? '_' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.pdf');
    }
    
    /**
     * Muestra el reporte de Uso de Herramientas por Instructor
     */
    public function toolsByInstructor(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        
        // Obtener años disponibles
        $availableYears = WarehouseMovement::where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        if (empty($availableYears)) {
            $availableYears = [Carbon::now()->year];
        }
        
        // Query para préstamos por instructor
        $query = WarehouseMovement::selectRaw('
                COUNT(warehouse_movements.id) as total_loans, 
                users.id as user_id,
                users.nickname as user_nickname,
                CONCAT(people.first_name, " ", people.first_last_name) as user_name
            ')
            ->join('users', 'warehouse_movements.user_id', '=', 'users.id')
            ->join('people', 'users.person_id', '=', 'people.id')
            ->where('warehouse_movements.item_type', 'tool')
            ->where('warehouse_movements.role', 'Préstamo')
            ->whereYear('warehouse_movements.created_at', $year);
        
        if ($month) {
            $query->whereMonth('warehouse_movements.created_at', $month);
        }
        
        $loansByInstructor = $query->groupBy('users.id', 'users.nickname', 'people.first_name', 'people.first_last_name')
            ->orderByDesc('total_loans')
            ->get();
        
        // Detalle de préstamos
        $detailQuery = WarehouseMovement::with(['tool', 'user.person'])
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $detailQuery->whereMonth('created_at', $month);
        }
        
        $loanDetails = $detailQuery->orderBy('created_at', 'desc')->get();
        
        // Top 10 herramientas más prestadas
        $topToolsQuery = WarehouseMovement::with('tool')
            ->select('movement_id')
            ->selectRaw('COUNT(*) as total_loans')
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $topToolsQuery->whereMonth('created_at', $month);
        }
        
        $topTools = $topToolsQuery->groupBy('movement_id')
            ->orderByDesc('total_loans')
            ->limit(10)
            ->get();
        
        // Estadísticas
        $totalLoans = $loansByInstructor->sum('total_loans');
        $totalInstructors = $loansByInstructor->count();
        
        // Meses disponibles
        $availableMonths = WarehouseMovement::where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();
        
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return view('infrastock::admin.reports.tools-by-instructor', compact(
            'loansByInstructor',
            'loanDetails',
            'topTools',
            'totalLoans',
            'totalInstructors',
            'year',
            'month',
            'availableYears',
            'availableMonths',
            'monthNames'
        ));
    }
    
    /**
     * Exporta el reporte de Herramientas por Instructor a PDF
     */
    public function toolsByInstructorPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        
        $query = WarehouseMovement::selectRaw('
                COUNT(warehouse_movements.id) as total_loans, 
                users.nickname as user_nickname,
                CONCAT(people.first_name, " ", people.first_last_name) as user_name
            ')
            ->join('users', 'warehouse_movements.user_id', '=', 'users.id')
            ->join('people', 'users.person_id', '=', 'people.id')
            ->where('warehouse_movements.item_type', 'tool')
            ->where('warehouse_movements.role', 'Préstamo')
            ->whereYear('warehouse_movements.created_at', $year);
        
        if ($month) {
            $query->whereMonth('warehouse_movements.created_at', $month);
        }
        
        $loansByInstructor = $query->groupBy('users.nickname', 'people.first_name', 'people.first_last_name')
            ->orderByDesc('total_loans')
            ->get();
        
        $detailQuery = WarehouseMovement::with(['tool', 'user.person'])
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $detailQuery->whereMonth('created_at', $month);
        }
        
        $loanDetails = $detailQuery->orderBy('created_at', 'desc')->get();
        
        $topToolsQuery = WarehouseMovement::with('tool')
            ->select('movement_id')
            ->selectRaw('COUNT(*) as total_loans')
            ->where('item_type', 'tool')
            ->where('role', 'Préstamo')
            ->whereYear('created_at', $year);
        
        if ($month) {
            $topToolsQuery->whereMonth('created_at', $month);
        }
        
        $topTools = $topToolsQuery->groupBy('movement_id')
            ->orderByDesc('total_loans')
            ->limit(10)
            ->get();
        
        $totalLoans = $loansByInstructor->sum('total_loans');
        $adminName = auth()->user()->name ?? 'Administrador';
        
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        $periodLabel = $month ? $monthNames[$month] . ' ' . $year : 'Año ' . $year;
        
        // Logo en base64
        $logoPath = public_path('assets/img/logo.png');
        $base64Logo = '';
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoData = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
        }
        
        $pdf = PDF::loadView('infrastock::admin.reports.exports.tools-pdf', compact(
            'loansByInstructor',
            'loanDetails',
            'topTools',
            'totalLoans',
            'periodLabel',
            'adminName',
            'base64Logo'
        ));
        
        $pdf->setPaper('letter', 'landscape');
        
        return $pdf->stream('herramientas_por_instructor_' . $year . ($month ? '_' . str_pad($month, 2, '0', STR_PAD_LEFT) : '') . '.pdf');
    }
}
