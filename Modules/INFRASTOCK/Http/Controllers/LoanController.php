<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Exports\LoansExport;
use Modules\SICA\Entities\Role;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        // Solo mostrar préstamos y devoluciones de HERRAMIENTAS (tools), no de insumos
        $query = WarehouseMovement::with(['user', 'user.person', 'productiveUnitWarehouse', 'tool', 'surplus'])
                                ->whereIn('role', ['Préstamo', 'Devolución'])
                                ->where('item_type', 'tool'); // Solo herramientas

        // Búsqueda server-side
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('tool', function ($tq) use ($search) {
                      $tq->where('nombre', 'like', "%{$search}%")
                         ->orWhere('placa', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nickname', 'like', "%{$search}%")
                         ->orWhereHas('person', function ($pq) use ($search) {
                             $pq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('first_last_name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        $loans = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->appends($request->query());
        
        // Contar devoluciones pendientes solo de herramientas
        $pendingReturns = WarehouseMovement::where('role', 'Devolución')
                                          ->where('item_type', 'tool') // Solo herramientas
                                          ->where('status', 'pending')
                                          ->count();
        
        // Todas las herramientas con info de disponibilidad para el modal
        $tools = Tool::select('id', 'nombre', 'estado', 'cantidad_disponible', 'cantidad_total', 'placa')
                     ->whereNotNull('nombre')
                     ->orderBy('nombre')
                     ->get();

        // Solo usuarios con rol Instructor para INFRASTOCK
        $instructors = collect();

        // Intentar localizar el rol principal de Instructor para INFRASTOCK
        $instructorRole = Role::where(function ($q) {
                $q->where('app_id', 19)->where('name', 'Instructor');
            })
            ->orWhere(function ($q) {
                $q->where('slug', 'infrastock.instructor');
            })
            ->orWhere(function ($q) {
                $q->where('slug', 'instructor');
            })
            ->first();

        if ($instructorRole) {
            // Caso ideal: tenemos un rol bien identificado, filtramos por su ID
            $instructors = User::with('person')
                ->whereHas('roles', function ($q) use ($instructorRole) {
                    $q->where('roles.id', $instructorRole->id);
                })
                ->get();
        } else {
            // Fallback: buscar cualquier usuario que tenga un rol tipo "Instructor"
            $instructors = User::with('person')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'Instructor')
                      ->orWhere('slug', 'infrastock.instructor')
                      ->orWhere('slug', 'instructor');
                })
                ->get();
        }

        $productiveUnitWarehouses = ProductiveUnitWarehouse::with('productiveUnit', 'warehouse')->get();

        $allToolLoans = WarehouseMovement::where('item_type', 'tool')
                                         ->whereIn('role', ['Préstamo', 'Devolución'])
                                         ->get();
        $totalPrestamos = $allToolLoans->where('role', 'Préstamo')->count();
        $totalDevoluciones = $allToolLoans->where('role', 'Devolución')->whereIn('status', ['approved', 'pending'])->count();
        $prestamosActivos = $allToolLoans->where('role', 'Préstamo')->where('status', 'approved')->count()
                          - $allToolLoans->where('role', 'Devolución')->where('status', 'approved')->count();
        if ($prestamosActivos < 0) $prestamosActivos = 0;

        // Top herramientas más prestadas
        $topTools = WarehouseMovement::with('tool')
            ->select('movement_id')
            ->selectRaw('COUNT(*) as total_loans')
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->groupBy('movement_id')
            ->orderByDesc('total_loans')
            ->limit(5)
            ->get();

        // Herramientas en mantenimiento
        $toolsEnMantenimiento = Tool::where('estado', 'mantenimiento')->count();

        $stats = [
            'totalPrestamos' => $totalPrestamos,
            'totalDevoluciones' => $totalDevoluciones,
            'prestamosActivos' => $prestamosActivos,
            'pendingReturns' => $pendingReturns,
            'topTools' => $topTools,
            'toolsEnMantenimiento' => $toolsEnMantenimiento,
        ];

        // Períodos disponibles para exportación
        $availablePeriods = $this->getAvailablePeriods();

        return view('infrastock::admin.loans.index', compact('loans', 'tools', 'instructors', 'productiveUnitWarehouses', 'pendingReturns', 'stats', 'availablePeriods'));
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
     * Si es un Préstamo de herramienta, descuenta automáticamente el stock disponible.
     * @param Request $request La solicitud HTTP que contiene los datos del movimiento.
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'tools' => 'required|array|min:1',
            'tools.*.movement_id' => 'required|integer|exists:tools,id',
            'tools.*.amount' => 'required|integer|min:1',
            'user_id' => 'nullable|exists:users,id',
            'borrower_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
        ]);

        if (!$request->user_id && !$request->borrower_name) {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'Debe seleccionar un usuario del sistema o ingresar el nombre del prestatario.')
                ->withInput();
        }

        $productiveUnitWarehouse = ProductiveUnitWarehouse::find($request->productive_unit_warehouse_id);
        if (!$productiveUnitWarehouse) {
            return back()->withErrors(['error' => 'La unidad productiva/almacén seleccionada no existe.']);
        }

        $userId = $request->user_id ?? auth()->id();
        $description = $request->description ?? '';

        if ($request->borrower_name && !$request->user_id) {
            $description = 'Prestatario: ' . $request->borrower_name . ($description ? ' | ' . $description : '');
        }

        $toolsToProcess = [];
        foreach ($request->tools as $toolData) {
            $tool = Tool::find($toolData['movement_id']);
            if (!$tool) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', 'Una de las herramientas seleccionadas no existe.')
                    ->withInput();
            }
            if ($tool->estado === 'mantenimiento') {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', "La herramienta \"{$tool->nombre}\" se encuentra en mantenimiento y no puede ser prestada.")
                    ->withInput();
            }
            $amount = (int) $toolData['amount'];
            $available = $tool->cantidad_disponible ?? 0;
            if ($available < $amount) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', "No hay stock suficiente de \"{$tool->nombre}\". Disponible: {$available}, Solicitado: {$amount}")
                    ->withInput();
            }
            $toolsToProcess[] = ['tool' => $tool, 'amount' => $amount, 'available' => $available];
        }

        try {
            foreach ($toolsToProcess as $tp) {
                WarehouseMovement::create([
                    'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                    'movement_id' => $tp['tool']->id,
                    'item_type' => 'tool',
                    'user_id' => $userId,
                    'role' => 'Préstamo',
                    'amount' => $tp['amount'],
                    'description' => $description,
                    'status' => 'approved',
                ]);

                $newAvailable = max(0, $tp['available'] - $tp['amount']);
                $tp['tool']->update([
                    'cantidad_disponible' => $newAvailable,
                    'estado' => $newAvailable <= 0 ? 'en_prestamo' : 'disponible',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', 'Ya existe un movimiento con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }

        $count = count($toolsToProcess);
        $msg = $count === 1
            ? 'Préstamo registrado exitosamente. Stock actualizado.'
            : "{$count} préstamos registrados exitosamente. Stock actualizado.";
        return redirect()->route('infrastock.admin.loans.index')->with('success', $msg);
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
     * Si el movimiento ya es una Devolución, se bloquea la edición.
     * Si se cambia de Préstamo a Devolución, se revierte el stock de la herramienta automáticamente.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados del movimiento.
     * @param int $id El ID del movimiento de almacén a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $loan = WarehouseMovement::findOrFail($id);

        if ($loan->role === 'Devolución') {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'Este registro de devolución está bloqueado y no puede ser editado.');
        }

        $request->validate([
            'movement_id' => 'required|integer|exists:tools,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'return_date' => 'nullable|date',
            'productive_unit_warehouse_id' => 'required|exists:productive_unit_warehouses,id',
        ]);

        $newAmount = (int) $request->amount;
        $tool = Tool::find($request->movement_id);

        if (!$tool) {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'Herramienta no encontrada.')->withInput();
        }

        if ($tool->estado === 'mantenimiento') {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', "La herramienta \"{$tool->nombre}\" se encuentra en mantenimiento y no puede ser prestada.")
                ->withInput();
        }

        if ($loan->status === 'approved') {
            $oldTool = Tool::find($loan->movement_id);
            $oldAmount = $loan->amount ?? 1;

            if ($oldTool && $oldTool->id !== $tool->id) {
                $oldNewAvailable = min(($oldTool->cantidad_disponible ?? 0) + $oldAmount, $oldTool->cantidad_total ?? $oldAmount);
                $oldTool->update(['cantidad_disponible' => $oldNewAvailable, 'estado' => 'disponible']);

                $available = $tool->cantidad_disponible ?? 0;
                if ($available < $newAmount) {
                    return redirect()->route('infrastock.admin.loans.index')
                        ->with('error', "Stock insuficiente de \"{$tool->nombre}\". Disponible: {$available}, Solicitado: {$newAmount}")
                        ->withInput();
                }
                $tool->update([
                    'cantidad_disponible' => max(0, $available - $newAmount),
                    'estado' => ($available - $newAmount) <= 0 ? 'en_prestamo' : 'disponible',
                ]);
            } elseif ($newAmount !== $oldAmount) {
                $available = $tool->cantidad_disponible ?? 0;
                $diff = $newAmount - $oldAmount;
                if ($diff > 0 && $diff > $available) {
                    return redirect()->route('infrastock.admin.loans.index')
                        ->with('error', "Stock insuficiente de \"{$tool->nombre}\". Disponible: {$available}, Se necesitan {$diff} adicional(es)")
                        ->withInput();
                }
                $newAvailable = max(0, $available - $diff);
                $tool->update([
                    'cantidad_disponible' => $newAvailable,
                    'estado' => $newAvailable <= 0 ? 'en_prestamo' : 'disponible',
                ]);
            }
        } else {
            $available = $tool->cantidad_disponible ?? 0;
            if ($newAmount > $available) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', "Stock insuficiente de \"{$tool->nombre}\". Disponible: {$available}, Solicitado: {$newAmount}")
                    ->withInput();
            }
        }

        try {
            $loan->update([
                'item_type' => 'tool',
                'movement_id' => $request->movement_id,
                'user_id' => $request->user_id,
                'productive_unit_warehouse_id' => $request->productive_unit_warehouse_id,
                'amount' => $newAmount,
                'description' => $request->description ?? $loan->description,
                'return_date' => $request->return_date ?? $loan->return_date,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.loans.index')
                    ->with('error', 'Ya existe un movimiento con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }

        return redirect()->route('infrastock.admin.loans.index')->with('success', 'Préstamo actualizado exitosamente.');
    }

    /**
     * Registra la devolución de un préstamo de herramienta.
     * Cambia el role a 'Devolución', restaura el stock de la herramienta y bloquea el registro.
     * @param int $id El ID del movimiento de almacén (Préstamo).
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnLoan($id)
    {
        $loan = WarehouseMovement::findOrFail($id);

        if ($loan->role !== 'Préstamo') {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'Solo se pueden devolver registros de tipo Préstamo.');
        }

        try {
            $returnDateTime = now()->format('d/m/Y H:i:s');

            // Cambiar a Devolución y agregar fecha
            $loan->update([
                'role' => 'Devolución',
                'description' => ($loan->description ?? '') . ' | DEVUELTO: ' . $returnDateTime,
            ]);

            // Restaurar stock de la herramienta
            if ($loan->item_type === 'tool') {
                $tool = Tool::find($loan->movement_id);
                if ($tool) {
                    $qty = $loan->amount ?? 1;
                    $newAvailable = ($tool->cantidad_disponible ?? 0) + $qty;
                    $maxStock = $tool->cantidad_total ?? $newAvailable;
                    $tool->update([
                        'cantidad_disponible' => min($newAvailable, $maxStock),
                        'estado' => 'disponible',
                    ]);
                }
            }

            return redirect()->route('infrastock.admin.loans.index')
                ->with('success', "Devolución registrada exitosamente ({$returnDateTime}). El stock ha sido restaurado.");
        } catch (\Exception $e) {
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un movimiento de préstamo o devolución de la base de datos.
     * Si se elimina un Préstamo activo (no devuelto), restaura el stock de la herramienta.
     * Los registros de Devolución están bloqueados y no se pueden eliminar.
     * @param int $id El ID del movimiento de almacén a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $loan = WarehouseMovement::findOrFail($id);

        // Bloquear eliminación de registros de Devolución
        if ($loan->role === 'Devolución') {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un registro de devolución. El movimiento está bloqueado.'
                ], 422);
            }
            return redirect()->route('infrastock.admin.loans.index')
                ->with('error', 'No se puede eliminar un registro de devolución. El movimiento está bloqueado.');
        }

        // Si se elimina un Préstamo APROBADO de herramienta, restaurar el stock
        // (Los préstamos pendientes o rechazados nunca descontaron stock)
        if ($loan->role === 'Préstamo' && $loan->item_type === 'tool' && $loan->status === 'approved') {
            $tool = Tool::find($loan->movement_id);
            if ($tool) {
                $qty = $loan->amount ?? 1;
                $newAvailable = ($tool->cantidad_disponible ?? 0) + $qty;
                $maxStock = $tool->cantidad_total ?? $newAvailable;
                $tool->update([
                    'cantidad_disponible' => min($newAvailable, $maxStock),
                    'estado' => 'disponible',
                ]);
            }
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
        $returnMovement = WarehouseMovement::with('surplus', 'equipment', 'tool')
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

            // Verificar si es devolución de herramienta o de insumo
            if ($returnMovement->item_type === 'tool') {
                // Devolución de herramienta: restaurar stock de la herramienta
                $tool = $returnMovement->tool;
                if ($tool) {
                    $amountReturned = $returnMovement->amount ?? 1;
                    $tool->cantidad_disponible = min(
                        ($tool->cantidad_disponible ?? 0) + $amountReturned,
                        $tool->cantidad_total ?? $amountReturned
                    );
                    // Si todas las unidades están disponibles, cambiar estado a disponible
                    if ($tool->cantidad_disponible >= ($tool->cantidad_total ?? 1)) {
                        $tool->estado = 'disponible';
                    }
                    $tool->save();
                }
            } elseif ($returnMovement->equipment && $returnMovement->amount > 0) {
                // Devolución de insumo: crear movimiento de recepción
                $equipment = $returnMovement->equipment;
                WarehouseMovement::create([
                    'productive_unit_warehouse_id' => $returnMovement->productive_unit_warehouse_id,
                    'movement_id' => null,
                    'equipment_id' => $equipment->id,
                    'item_type' => 'equipment',
                    'user_id' => $returnMovement->user_id,
                    'role' => 'Recibe',
                    'amount' => $returnMovement->amount,
                ]);
            }

            // Enviar notificación al usuario
            $this->notifyUserReturnStatus($returnMovement, 'approved');

            $itemLabel = $returnMovement->item_type === 'tool' ? 'herramienta' : 'insumo';
            return redirect()->back()->with('success', "Devolución aprobada exitosamente. El stock de la {$itemLabel} ha sido actualizado.");
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

        $returnMovement = WarehouseMovement::with('surplus', 'equipment', 'tool')
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
     * Aprueba un préstamo de herramienta
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveLoan($id)
    {
        $loanMovement = WarehouseMovement::with('tool')
            ->where('id', $id)
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->where('status', 'pending')
            ->first();

        if (!$loanMovement) {
            return redirect()->back()->with('error', 'Préstamo no encontrado o ya procesado.');
        }

        // Verificar que la herramienta tiene stock suficiente antes de aprobar
        $tool = $loanMovement->tool;
        // Refrescar herramienta desde la base de datos para obtener el stock más reciente
        if ($tool) {
            $tool->refresh();
            $amount = $loanMovement->amount ?? 1;
            $available = $tool->cantidad_disponible ?? 0;

            if ($available < $amount) {
                return redirect()->back()->with('error', "No se puede aprobar: stock insuficiente de \"{$tool->nombre}\". Disponible: {$available}, Solicitado: {$amount}.");
            }
        }

        try {
            $loanMovement->update([
                'status' => 'approved',
            ]);

            // Descontar stock de la herramienta al aprobar
            if ($tool) {
                $amount = $loanMovement->amount ?? 1;
                $newAvailable = max(0, ($tool->cantidad_disponible ?? 0) - $amount);
                $tool->update([
                    'cantidad_disponible' => $newAvailable,
                    'estado' => $newAvailable <= 0 ? 'en_prestamo' : 'disponible',
                ]);
            }

            // Enviar notificación al instructor
            $this->notifyUserLoanStatus($loanMovement, 'approved');

            return redirect()->back()->with('success', 'Préstamo aprobado exitosamente. Stock actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al aprobar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza un préstamo de herramienta
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectLoan(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $loanMovement = WarehouseMovement::with('tool')
            ->where('id', $id)
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->where('status', 'pending')
            ->first();

        if (!$loanMovement) {
            return redirect()->back()->with('error', 'Préstamo no encontrado o ya procesado.');
        }

        try {
            $loanMovement->update([
                'status' => 'rejected',
                'description' => ($loanMovement->description ?? '') . ' | Motivo de rechazo: ' . $request->rejection_reason,
            ]);

            // Enviar notificación al instructor
            $this->notifyUserLoanStatus($loanMovement, 'rejected', $request->rejection_reason);

            return redirect()->back()->with('success', 'Préstamo rechazado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al rechazar el préstamo: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los períodos disponibles que tienen registros de préstamos
     */
    private function getAvailablePeriods()
    {
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $records = WarehouseMovement::where('item_type', 'tool')
            ->whereIn('role', ['Préstamo', 'Devolución'])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        if ($records->isEmpty()) {
            return ['hasRecords' => false, 'years' => []];
        }

        $years = [];
        foreach ($records as $record) {
            $year = $record->year;
            $month = $record->month;

            if (!isset($years[$year])) {
                $years[$year] = ['year' => $year, 'months' => [], 'quarters' => [], 'totalRecords' => 0];
            }

            $years[$year]['months'][$month] = ['month' => $month, 'name' => $monthNames[$month], 'count' => $record->count];
            $years[$year]['totalRecords'] += $record->count;

            $quarter = ceil($month / 3);
            if (!isset($years[$year]['quarters'][$quarter])) {
                $years[$year]['quarters'][$quarter] = ['quarter' => $quarter, 'name' => 'Q' . $quarter, 'months' => [], 'count' => 0];
            }
            $years[$year]['quarters'][$quarter]['months'][] = $month;
            $years[$year]['quarters'][$quarter]['count'] += $record->count;
        }

        foreach ($years as &$yearData) {
            ksort($yearData['months']);
            ksort($yearData['quarters']);
        }

        return ['hasRecords' => true, 'years' => $years];
    }

    /**
     * Obtiene las fechas de inicio y fin según los parámetros
     */
    private function getPeriodDates($type, $year = null, $month = null, $quarter = null)
    {
        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $year = $year ?? Carbon::now()->year;

        switch ($type) {
            case 'monthly':
                $month = $month ?? Carbon::now()->month;
                $date = Carbon::create($year, $month, 1);
                return ['start' => $date->copy()->startOfMonth(), 'end' => $date->copy()->endOfMonth(), 'label' => $monthNames[$month] . ' ' . $year];
            case 'quarterly':
                $quarter = $quarter ?? Carbon::now()->quarter;
                $startMonth = (($quarter - 1) * 3) + 1;
                $date = Carbon::create($year, $startMonth, 1);
                return ['start' => $date->copy()->startOfQuarter(), 'end' => $date->copy()->endOfQuarter(), 'label' => 'Q' . $quarter . ' ' . $year . ' (Trimestre ' . $quarter . ')'];
            case 'yearly':
                $date = Carbon::create($year, 1, 1);
                return ['start' => $date->copy()->startOfYear(), 'end' => $date->copy()->endOfYear(), 'label' => 'Año ' . $year];
            default:
                $month = $month ?? Carbon::now()->month;
                $date = Carbon::create($year, $month, 1);
                return ['start' => $date->copy()->startOfMonth(), 'end' => $date->copy()->endOfMonth(), 'label' => $monthNames[$month] . ' ' . $year];
        }
    }

    /**
     * Obtiene los datos de préstamos para exportación
     */
    private function getLoanData($type, $year = null, $month = null, $quarter = null)
    {
        $periodDates = $this->getPeriodDates($type, $year, $month, $quarter);

        $loans = WarehouseMovement::with(['user.person', 'tool', 'productiveUnitWarehouse.productiveUnit', 'productiveUnitWarehouse.warehouse'])
            ->where('item_type', 'tool')
            ->whereIn('role', ['Préstamo', 'Devolución'])
            ->whereBetween('created_at', [$periodDates['start'], $periodDates['end']])
            ->orderBy('created_at', 'desc')
            ->get();

        $topTools = WarehouseMovement::with('tool')
            ->select('movement_id')
            ->selectRaw('COUNT(*) as total_loans')
            ->where('role', 'Préstamo')
            ->where('item_type', 'tool')
            ->whereBetween('created_at', [$periodDates['start'], $periodDates['end']])
            ->groupBy('movement_id')
            ->orderByDesc('total_loans')
            ->limit(5)
            ->get();

        $stats = [
            'total' => $loans->count(),
            'prestamos' => $loans->where('role', 'Préstamo')->count(),
            'devoluciones' => $loans->where('role', 'Devolución')->count(),
            'pendientes' => $loans->where('role', 'Préstamo')->where('status', 'pending')->count(),
            'topTools' => $topTools,
        ];

        return ['loans' => $loans, 'stats' => $stats, 'periodLabel' => $periodDates['label']];
    }

    /**
     * Exporta los préstamos de herramientas a PDF
     */
    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month');
        $quarter = $request->get('quarter');

        $data = $this->getLoanData($type, $year, $month, $quarter);

        $pdf = Pdf::loadView('infrastock::admin.loans.exports.pdf', [
            'loans' => $data['loans'],
            'stats' => $data['stats'],
            'periodLabel' => $data['periodLabel'],
        ]);

        $pdf->setPaper('letter', 'landscape');

        $filename = 'Prestamos_Herramientas_INFRASTOCK_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Exporta los préstamos de herramientas a Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $year = $request->get('year');
        $month = $request->get('month');
        $quarter = $request->get('quarter');

        $data = $this->getLoanData($type, $year, $month, $quarter);

        $filename = 'Prestamos_Herramientas_INFRASTOCK_';
        if ($type === 'monthly' && $month && $year) {
            $filename .= $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT);
        } elseif ($type === 'quarterly' && $quarter && $year) {
            $filename .= $year . '_Q' . $quarter;
        } elseif ($type === 'yearly' && $year) {
            $filename .= $year;
        } else {
            $filename .= now()->format('Y-m-d');
        }

        return Excel::download(new LoansExport($data['loans'], $data['stats'], $data['periodLabel']), $filename . '.xlsx');
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

            // Determinar nombre y URL según el tipo de ítem
            $isTool = $returnMovement->item_type === 'tool';
            if ($isTool) {
                $itemName = $returnMovement->tool ? ($returnMovement->tool->nombre ?? 'Herramienta') : 'Herramienta';
                $actionUrl = route('infrastock.instructor.my-loans');
            } else {
                $itemName = $returnMovement->equipment ? $returnMovement->equipment->name : 'Insumo';
                $actionUrl = route('infrastock.admin.loans.index');
            }
            
            $itemLabel = $isTool ? 'herramienta' : 'insumo';
            
            if ($status === 'approved') {
                $title = 'Devolución Aprobada';
                $message = "Tu devolución de la {$itemLabel}: {$itemName} ha sido aprobada.";
                $type = 'return_approved';
            } else {
                $title = 'Devolución Rechazada';
                $message = "Tu devolución de la {$itemLabel}: {$itemName} ha sido rechazada.";
                if ($rejectionReason) {
                    $message .= " Motivo: {$rejectionReason}";
                }
                $type = 'return_rejected';
            }

            // Usar el modelo Notification de INFRASTOCK (compatible con la tabla notifications)
            \Modules\INFRASTOCK\Entities\Notification::create([
                'type' => $type,
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'return_id' => $returnMovement->id,
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                    'action_url' => $actionUrl,
                    'created_at' => now()->format('d/m/Y H:i'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de estado de devolución: ' . $e->getMessage());
        }
    }

    /**
     * Envía notificación al instructor sobre el estado de su préstamo
     * @param WarehouseMovement $loanMovement
     * @param string $status
     * @param string|null $rejectionReason
     * @return void
     */
    private function notifyUserLoanStatus($loanMovement, $status, $rejectionReason = null)
    {
        try {
            $user = $loanMovement->user;
            if (!$user) {
                return;
            }

            $toolName = $loanMovement->tool ? ($loanMovement->tool->nombre ?? $loanMovement->tool->name ?? 'Herramienta') : 'Herramienta';
            
            if ($status === 'approved') {
                $title = 'Préstamo Aprobado';
                $message = "Tu préstamo de la herramienta: {$toolName} ha sido aprobado.";
            } else {
                $title = 'Préstamo Rechazado';
                $message = "Tu préstamo de la herramienta: {$toolName} ha sido rechazado.";
                if ($rejectionReason) {
                    $message .= " Motivo: {$rejectionReason}";
                }
            }

            // Crear notificación usando el modelo Notification de INFRASTOCK
            \Modules\INFRASTOCK\Entities\Notification::create([
                'type' => $status === 'approved' ? 'loan_approved' : 'loan_rejected',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'tool_id' => $loanMovement->tool ? $loanMovement->tool->id : null,
                    'tool_name' => $loanMovement->tool ? ($loanMovement->tool->nombre ?? $loanMovement->tool->name ?? 'Herramienta') : 'Herramienta',
                    'loan_id' => $loanMovement->id,
                    'status' => $status,
                    'rejection_reason' => $rejectionReason,
                    'created_at' => now()->format('d/m/Y H:i'),
                    'action_url' => route('infrastock.instructor.my-loans'),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de estado de préstamo: ' . $e->getMessage());
        }
    }
}