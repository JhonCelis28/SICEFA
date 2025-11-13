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
        $loans = WarehouseMovement::with(['user', 'productiveUnitWarehouse', 'equipment', 'surplus'])
                                ->whereIn('role', ['Préstamo', 'Devolución'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        // Cargar herramientas solo para los movimientos que son de tipo 'tool'
        $loans->getCollection()->each(function($loan) {
            if ($loan->item_type === 'tool') {
                $loan->load('tool');
            }
        });
        
        // Contar devoluciones pendientes
        $pendingReturns = WarehouseMovement::where('role', 'Devolución')
                                          ->where('status', 'pending')
                                          ->count();
        
        $tools = Tool::all();
        $equipments = Equipment::all();
        $users = User::all();
        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();
        return view('infrastock::admin.loans.index', compact('loans', 'tools', 'equipments', 'users', 'productiveUnitWarehouses', 'pendingReturns'));
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

    /**
     * Aprueba una devolución de insumos
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveReturn($id)
    {
        $returnMovement = WarehouseMovement::with('surplus', 'equipment')
            ->where('id', $id)
            ->where('role', 'Devolución')
            ->where('status', 'pending')
            ->first();

        if (!$returnMovement) {
            return redirect()->back()->with('error', 'Devolución no encontrada o ya procesada.');
        }

        try {
            $returnMovement->update([
                'status' => 'approved',
            ]);

            // Si hay un sobrante asociado, actualizar el stock del equipo
            if ($returnMovement->equipment && $returnMovement->amount > 0) {
                $equipment = $returnMovement->equipment;
                
                // Crear un movimiento de tipo 'Recibe' para registrar la devolución en el historial
                // Esto permite rastrear que se recibió material de vuelta
                // El cálculo de stock (initial_amount - used_amount) se ajustará automáticamente
                // porque 'Recibe' resta del used_amount, aumentando así el stock disponible
                WarehouseMovement::create([
                    'productive_unit_warehouse_id' => $returnMovement->productive_unit_warehouse_id,
                    'movement_id' => null,
                    'equipment_id' => $equipment->id,
                    'item_type' => 'equipment',
                    'user_id' => $returnMovement->user_id,
                    'role' => 'Recibe', // Indica que se está recibiendo material de vuelta
                    'amount' => $returnMovement->amount,
                ]);
            }

            // Enviar notificación al usuario
            $this->notifyUserReturnStatus($returnMovement, 'approved');

            return redirect()->back()->with('success', 'Devolución aprobada exitosamente. El stock del insumo ha sido actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza una devolución de insumos
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectReturn(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $returnMovement = WarehouseMovement::with('surplus', 'equipment')
            ->where('id', $id)
            ->where('role', 'Devolución')
            ->where('status', 'pending')
            ->first();

        if (!$returnMovement) {
            return redirect()->back()->with('error', 'Devolución no encontrada o ya procesada.');
        }

        try {
            $returnMovement->update([
                'status' => 'rejected',
                'description' => ($returnMovement->description ?? '') . ' | Motivo de rechazo: ' . $request->rejection_reason,
            ]);

            // Enviar notificación al usuario
            $this->notifyUserReturnStatus($returnMovement, 'rejected', $request->rejection_reason);

            return redirect()->back()->with('success', 'Devolución rechazada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al rechazar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Envía notificación al usuario sobre el estado de su devolución
     * @param WarehouseMovement $returnMovement
     * @param string $status
     * @param string|null $rejectionReason
     * @return void
     */
    private function notifyUserReturnStatus($returnMovement, $status, $rejectionReason = null)
    {
        try {
            $user = $returnMovement->user;
            if (!$user) {
                return;
            }

            $equipmentName = $returnMovement->equipment ? $returnMovement->equipment->name : 'Insumo';
            $unit = $returnMovement->equipment ? ($returnMovement->equipment->unit ?? 'unidades') : 'unidades';
            
            if ($status === 'approved') {
                $title = 'Devolución Aprobada';
                $message = "Tu devolución de {$returnMovement->amount} {$unit} de {$equipmentName} ha sido aprobada.";
            } else {
                $title = 'Devolución Rechazada';
                $message = "Tu devolución de {$returnMovement->amount} {$unit} de {$equipmentName} ha sido rechazada.";
                if ($rejectionReason) {
                    $message .= " Motivo: {$rejectionReason}";
                }
            }

            $user->notify(new \Modules\INFRASTOCK\Notifications\ReturnStatusNotification($returnMovement, $status, $title, $message));
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de estado de devolución: ' . $e->getMessage());
        }
    }
}
