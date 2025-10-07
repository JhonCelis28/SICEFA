<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use App\Models\User;

/**
 * @class LoanController
 * @brief Controlador para la gestión de Préstamos y Devoluciones en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para los movimientos de almacén de tipo 'Préstamo' y 'Devolución'.
 * Permite registrar, visualizar, actualizar y eliminar préstamos o devoluciones de herramientas e insumos.
 * Utiliza modales para la creación y edición de movimientos para una experiencia de usuario optimizada.
 */
class LoanController extends Controller
{
    /**
     * Muestra una lista de todos los préstamos y devoluciones registrados.
     * @return Renderable
     */
    public function index()
    {
        // Obtiene todos los movimientos de almacén que son de tipo 'Préstamo' o 'Devolución'.
        // Incluye las relaciones con usuario, unidad productiva/almacén, herramienta e insumo para mostrar detalles.
        $loans = WarehouseMovement::with('user', 'productiveUnitWarehouse', 'tool', 'equipment')
                                ->whereIn('role', ['Préstamo', 'Devolución'])
                                ->get();

        // Obtiene todas las herramientas, insumos, usuarios y unidades productivas/almacenes
        // para poblar los selectores en los modales de creación/edición.
        $tools = Tool::all();
        $equipments = Equipment::all();
        $users = User::all();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();

        // Retorna la vista index de préstamos con todos los datos necesarios.
        return view('infrastock::admin.loans.index', compact('loans', 'tools', 'equipments', 'users', 'productiveUnitWarehouses'));
    }

    /**
     * Muestra el formulario para crear un nuevo movimiento de préstamo o devolución.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.loans.index');
    }

    /**
     * Almacena un nuevo movimiento de préstamo o devolución en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos del movimiento.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'item_type' => 'required|in:equipment,tool', // Tipo de elemento: insumo o herramienta.
            'movement_id' => 'required|integer', // ID del elemento (insumo o herramienta).
            'user_id' => 'required|exists:users,id', // ID del usuario que realiza/recibe el movimiento.
            'role' => 'required|in:Préstamo,Devolución', // Tipo de movimiento: Préstamo o Devolución.
        ]);

        // Lógica placeholder para obtener la unidad productiva/almacén.
        // En un escenario real, esto debería ser determinado por el contexto del elemento o una selección del usuario.
        $productiveUnitWarehouse = ProductiveUnitWarehouse::first();

        // Maneja el caso en que no se encuentre una unidad productiva/almacén (debe ser provista en un escenario real).
        if (!$productiveUnitWarehouse) {
            return back()->withErrors(['error' => 'No se encontró una unidad productiva/almacén.']);
        }

        // Crea un nuevo registro de movimiento de almacén con los datos validados.
        WarehouseMovement::create([
            'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
            'movement_id' => $request->movement_id,
            'item_type' => $request->item_type,
            'user_id' => $request->user_id,
            'role' => $request->role,
        ]);

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.loans.index')->with('success', 'Movimiento registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un movimiento de préstamo o devolución específico.
     * Redirige al index ya que la visualización se realiza a través de un modal de edición en la vista principal.
     * @param int $id El ID del movimiento de almacén.
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('infrastock.admin.loans.index');
    }

    /**
     * Muestra el formulario para editar un movimiento de préstamo o devolución específico.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID del movimiento de almacén.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.loans.index');
    }

    /**
     * Actualiza un movimiento de préstamo o devolución existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados del movimiento.
     * @param int $id El ID del movimiento de almacén a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'item_type' => 'required|in:equipment,tool', // Tipo de elemento: insumo o herramienta.
            'movement_id' => 'required|integer', // ID del elemento (insumo o herramienta).
            'user_id' => 'required|exists:users,id', // ID del usuario que realiza/recibe el movimiento.
            'role' => 'required|in:Préstamo,Devolución', // Tipo de movimiento: Préstamo o Devolución.
        ]);

        $loan = WarehouseMovement::findOrFail($id); // Encuentra el movimiento de almacén por su ID o lanza una excepción.
        $loan->update($request->all()); // Actualiza el movimiento con los nuevos datos.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.loans.index')->with('success', 'Movimiento actualizado exitosamente.');
    }

    /**
     * Elimina un movimiento de préstamo o devolución de la base de datos.
     * @param int $id El ID del movimiento de almacén a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $loan = WarehouseMovement::findOrFail($id); // Encuentra el movimiento de almacén por su ID o lanza una excepción.
        $loan->delete(); // Elimina el movimiento de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.loans.index')->with('success', 'Movimiento eliminado exitosamente.');
    }
}
