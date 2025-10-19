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
        $loans = WarehouseMovement::with('user', 'productiveUnitWarehouse', 'tool', 'equipment')
                                ->whereIn('role', ['Préstamo', 'Devolución'])
                                ->paginate(15);
        $tools = Tool::all();
        $equipments = Equipment::all();
        $users = User::all();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();
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
        $request->validate([
            'item_type' => 'required|in:equipment,tool',
            'movement_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Préstamo,Devolución',
        ]);

        $productiveUnitWarehouse = ProductiveUnitWarehouse::first();
        if (!$productiveUnitWarehouse) {
            return back()->withErrors(['error' => 'No se encontró una unidad productiva/almacén.']);
        }

        try {
            WarehouseMovement::create([
                'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                'movement_id' => $request->movement_id,
                'item_type' => $request->item_type,
                'user_id' => $request->user_id,
                'role' => $request->role,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', 'Ya existe un movimiento con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }
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
        $request->validate([
            'item_type' => 'required|in:equipment,tool',
            'movement_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:Préstamo,Devolución',
        ]);

        $loan = WarehouseMovement::findOrFail($id);
        
        try {
            $loan->update($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', 'Ya existe un movimiento con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }
        return redirect()->route('infrastock.admin.loans.index')->with('success', 'Movimiento actualizado exitosamente.');
    }

    /**
     * Elimina un movimiento de préstamo o devolución de la base de datos.
     * @param int $id El ID del movimiento de almacén a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $loan = WarehouseMovement::findOrFail($id);
        $hasRelatedRecords = false; // TODO: Implement validation
        
        if ($hasRelatedRecords) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el movimiento porque tiene registros relacionados.'
                ], 422);
            }
            return redirect()->route('infrastock.admin.loans.index')->with('error', 'No se puede eliminar el movimiento porque tiene registros relacionados.');
        }
        
        $loan->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Movimiento eliminado exitosamente.'
            ]);
        }
        return redirect()->route('infrastock.admin.loans.index')->with('success', 'deleted');
    }
}
