<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;

/**
 * @class SupplyRequestController
 * @brief Controlador para la gestión de Solicitudes de Insumos en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para las solicitudes de insumos realizadas
 * por los usuarios. Permite al administrador visualizar, gestionar (cambiar estado) y eliminar
 * estas solicitudes. La creación y edición se gestionan a través de modales para una
 * experiencia de usuario fluida.
 */
class SupplyRequestController extends Controller
{
    /**
     * Muestra una lista de todas las solicitudes de insumos pendientes.
     * Carga las relaciones con el usuario solicitante, el insumo solicitado y la unidad
     * productiva/almacén asociada para mostrar detalles completos en la tabla.
     * También obtiene listas de insumos, usuarios y unidades productivas/almacenes
     * para poblar los selectores en los modales de gestión.
     * @return Renderable
     */
    public function index()
    {
        // Obtiene las solicitudes de insumos con rol 'Solicitud' (pendientes) y sus relaciones.
        $supplyRequests = WarehouseMovement::with('user', 'equipment', 'productiveUnitWarehouse.productiveUnit', 'productiveUnitWarehouse.warehouse')
                                        ->where('item_type', 'equipment')
                                        ->where('role', 'Solicitud') // Asume 'Solicitud' como el rol para solicitudes pendientes.
                                        ->get();

        // Datos adicionales para los selectores en los modales (si se usaran para crear/editar en el mismo modal).
        $equipments = Equipment::all();
        $users = User::all();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();

        // Retorna la vista index de solicitudes con todos los datos necesarios.
        return view('infrastock::admin.supply-requests.index', compact('supplyRequests', 'equipments', 'users', 'productiveUnitWarehouses'));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud de insumo.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Almacena una nueva solicitud de insumo en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos de la solicitud.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'movement_id' => 'required|exists:equipments,id', // ID del insumo es obligatorio y debe existir.
            'user_id' => 'required|exists:users,id', // ID del usuario es obligatorio y debe existir.
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id', // ID de unidad/almacén es obligatorio y debe existir.
            'amount' => 'required|integer|min:1', // Cantidad solicitada es obligatoria, entera y mínimo 1.
            'description' => 'nullable|string', // Descripción/razón de la solicitud es opcional.
        ]);

        // Crea un nuevo registro de movimiento de almacén con el rol 'Solicitud'.
        WarehouseMovement::create([
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'movement_id' => $request->movement_id,
            'item_type' => 'equipment',
            'user_id' => $request->user_id,
            'role' => 'Solicitud', // Estado inicial de la solicitud.
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Solicitud de insumo creada exitosamente.');
    }

    /**
     * Muestra los detalles de una solicitud de insumo específica.
     * Redirige al index ya que la visualización se realiza a través del modal de edición en la vista principal.
     * @param int $id El ID de la solicitud de insumo.
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Muestra el formulario para editar una solicitud de insumo específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID de la solicitud de insumo.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.supply-requests.index');
    }

    /**
     * Actualiza el estado de una solicitud de insumo existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados de la solicitud.
     * @param int $id El ID de la solicitud de insumo a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'movement_id' => 'required|exists:equipments,id',
            'user_id' => 'required|exists:users,id',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'role' => 'required|in:Solicitud,approved,rejected,delivered', // El estado puede ser actualizado por el administrador.
        ]);

        $supplyRequest = WarehouseMovement::findOrFail($id); // Encuentra la solicitud por su ID o lanza una excepción.
        // Actualiza la solicitud con los nuevos datos, incluyendo el cambio de rol (estado).
        $supplyRequest->update([
            'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
            'movement_id' => $request->movement_id,
            'item_type' => 'equipment',
            'user_id' => $request->user_id,
            'role' => $request->role, // Actualización del estado de la solicitud.
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Estado de la solicitud actualizado exitosamente.');
    }

    /**
     * Elimina una solicitud de insumo de la base de datos.
     * @param int $id El ID de la solicitud de insumo a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $supplyRequest = WarehouseMovement::findOrFail($id); // Encuentra la solicitud por su ID o lanza una excepción.
        $supplyRequest->delete(); // Elimina la solicitud de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supply-requests.index')->with('success', 'Solicitud eliminada exitosamente.');
    }
}
