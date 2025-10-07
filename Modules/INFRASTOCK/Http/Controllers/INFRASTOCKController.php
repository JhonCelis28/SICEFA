<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\ProductiveUnit;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;
use Carbon\Carbon;

/**
 * @class INFRASTOCKController
 * @brief Controlador principal para el módulo INFRASTOCK.
 *
 * Este controlador maneja las vistas principales del módulo, como la página de inicio
 * y el panel de administración (dashboard). También contiene métodos placeholder
 * para operaciones CRUD genéricas a nivel de módulo que no están siendo utilizadas
 * activamente o han sido delegadas a controladores específicos (ej. AreaController).
 */
class INFRASTOCKController extends Controller
{
    /**
     * Muestra la página de inicio del módulo INFRASTOCK.
     * Esta vista suele ser el punto de entrada público o de bienvenida del módulo.
     * @return Renderable
     */
    public function index()
    {
        return view('infrastock::index'); // Retorna la vista 'index.blade.php' del módulo INFRASTOCK.
    }

    /**
     * Muestra el formulario para crear un nuevo recurso a nivel de módulo.
     * Actualmente, esta función actúa como un placeholder y redirige a la vista principal
     * ya que las operaciones de creación se manejan a través de modales en vistas específicas (ej. áreas, insumos).
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('cefa.infrastock.index'); // Redirige a la página principal del módulo.
    }

    /**
     * Almacena un recurso recién creado a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de almacenamiento.
     * Las operaciones de almacenamiento se delegan a controladores específicos.
     * @param Request $request La solicitud HTTP.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Este método es un placeholder. La lógica de almacenamiento se maneja en controladores específicos.
    }

    /**
     * Muestra el panel de administración (dashboard) del módulo INFRASTOCK.
     * Recopila y presenta datos estadísticos y en tiempo real sobre inventarios,
     * movimientos, solicitudes y herramientas prestadas.
     * @return Renderable
     */
    public function dashboard()
    {
        // Datos para las tarjetas de información en el dashboard:

        // Conteo de nuevas solicitudes de insumos en los últimos 7 días.
        $newSupplyRequestsCount = WarehouseMovement::where('item_type', 'equipment')
                                                ->where('role', 'Solicitud')
                                                ->where('created_at', '>=', Carbon::now()->subDays(7))
                                                ->count();

        // Conteo total de equipos (insumos) registrados.
        $totalEquipments = Equipment::count();
        // Suma total de la cantidad de todos los insumos en stock.
        $suppliesInStock = Equipment::sum('amount');
        // Calcula el porcentaje de insumos en stock respecto al total de equipos.
        $suppliesPercentage = ($totalEquipments > 0) ? round(($suppliesInStock / $totalEquipments) * 100, 2) : 0;

        // Conteo de herramientas que actualmente están en préstamo.
        $toolsOnLoanCount = WarehouseMovement::where('item_type', 'tool')
                                            ->where('role', 'Préstamo')
                                            ->count();

        // Conteo de insumos próximos a vencer (con fecha de vencimiento dentro de los próximos 30 días).
        $expiringSuppliesCount = Equipment::whereNotNull('expiration_date')
                                        ->where('expiration_date', '<=', Carbon::now()->addDays(30))
                                        ->count();

        // Datos para el gráfico de Consumo de Insumos por Área:
        // Realiza un join con las tablas 'productive_unit_warehouses' y 'productive_units' para agrupar
        // el consumo por el nombre del área productiva.
        $consumptionByArea = WarehouseMovement::selectRaw('SUM(amount) as total_amount, productive_units.name as area_name')
            ->join('productive_unit_warehouses', 'warehouse_movements.productive_unit_warehouse_id', '=', 'productive_unit_warehouses.id')
            ->join('productive_units', 'productive_unit_warehouses.productive_unit_id', '=', 'productive_units.id')
            ->where('warehouse_movements.item_type', 'equipment')
            ->groupBy('productive_units.name')
            ->get();

        // Extrae los nombres de las áreas y las cantidades de consumo para el gráfico.
        $areaNames = $consumptionByArea->pluck('area_name')->toArray();
        $consumptionAmounts = $consumptionByArea->pluck('total_amount')->toArray();

        // Datos para el gráfico de uso de Herramientas por Instructor:
        // Realiza un join con la tabla 'users' para agrupar los préstamos por el apodo (nickname) del usuario (instructor).
        $toolsByInstructor = WarehouseMovement::selectRaw('COUNT(warehouse_movements.id) as total_loans, users.nickname as user_name')
            ->join('users', 'warehouse_movements.user_id', '=', 'users.id')
            ->where('warehouse_movements.item_type', 'tool')
            ->where('warehouse_movements.role', 'Préstamo')
            ->groupBy('users.nickname')
            ->get();
        
        // Extrae los nombres de los instructores y el conteo de préstamos para el gráfico.
        $instructorNames = $toolsByInstructor->pluck('user_name')->toArray();
        $loanCounts = $toolsByInstructor->pluck('total_loans')->toArray();


        // Retorna la vista del dashboard con todos los datos recopilados.
        return view('infrastock::admin.dashboard', compact(
            'newSupplyRequestsCount',
            'suppliesPercentage',
            'toolsOnLoanCount',
            'expiringSuppliesCount',
            'areaNames',
            'consumptionAmounts',
            'instructorNames',
            'loanCounts'
        ));
    }

    /**
     * Muestra el formulario para editar un recurso a nivel de módulo.
     * Actualmente, esta función actúa como un placeholder y redirige a la vista principal
     * ya que las operaciones de edición se manejan a través de modales en vistas específicas.
     * @param int $id El ID del recurso a editar (no utilizado activamente).
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('cefa.infrastock.index'); // Redirige a la página principal del módulo.
    }

    /**
     * Actualiza un recurso existente a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de actualización.
     * Las operaciones de actualización se delegan a controladores específicos.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados.
     * @param int $id El ID del recurso a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Este método es un placeholder. La lógica de actualización se maneja en controladores específicos.
    }

    /**
     * Elimina un recurso existente a nivel de módulo.
     * Actualmente, este método es un placeholder y no implementa ninguna lógica de eliminación.
     * Las operaciones de eliminación se delegan a controladores específicos.
     * @param int $id El ID del recurso a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        // Este método es un placeholder. La lógica de eliminación se maneja en controladores específicos.
    }

    /**
     * Maneja la lógica posterior al inicio de sesión para el módulo INFRASTOCK.
     * Redirige a una vista específica o realiza acciones después de que un usuario ha iniciado sesión.
     * @return Renderable
     */
    public function postlogin(){
        return view('infrastock::postlogin'); // Retorna la vista de post-login del módulo.
    }
}
