<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\Equipment;
use Carbon\Carbon;

class SurplusController extends Controller
{
    /**
     * Verifica que el usuario tenga el rol de Personal de Aseo.
     */
    private function verifyRole()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Aseo', $userRoles) && !in_array('Personal de Aseo', $userRoles)) {
            abort(403, 'No tienes permiso para acceder a esta sección. Solo usuarios con rol de Personal de Aseo pueden acceder.');
        }
    }

    /**
     * Muestra la página principal de sobrantes con las solicitudes aprobadas
     * @return Renderable
     */
    public function index()
    {
        $this->verifyRole();
        
        // Obtener sobrantes relacionados con solicitudes aprobadas del usuario actual
        $surpluses = Surplus::with([
            'equipment.category',
            'request',
            'requestItem'
        ])
            ->where('user_id', auth()->id())
            ->whereNotNull('request_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('infrastock::cleaning-staff.surplus.index', compact('surpluses'));
    }

    /**
     * Almacena un nuevo registro de sobrante
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->verifyRole();
        $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'surplus_amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'surplus_date' => 'required|date|before_or_equal:today',
        ], [
            'equipment_id.required' => 'Debe seleccionar un insumo.',
            'equipment_id.exists' => 'El insumo seleccionado no es válido.',
            'surplus_amount.required' => 'Debe especificar la cantidad que sobró.',
            'surplus_amount.integer' => 'La cantidad debe ser un número entero.',
            'surplus_amount.min' => 'La cantidad debe ser mayor a 0.',
            'reason.required' => 'Debe especificar la causa del sobrante.',
            'reason.max' => 'La causa no puede exceder los 500 caracteres.',
            'surplus_date.required' => 'Debe especificar la fecha del sobrante.',
            'surplus_date.date' => 'La fecha debe ser válida.',
            'surplus_date.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        try {
            Surplus::create([
                'equipment_id' => $request->equipment_id,
                'user_id' => auth()->id(),
                'surplus_amount' => $request->surplus_amount,
                'reason' => $request->reason,
                'surplus_date' => $request->surplus_date,
            ]);

            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('success', 'Sobrante registrado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('error', 'Error al registrar el sobrante: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un sobrante específico con información de la solicitud
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->verifyRole();
        $surplus = Surplus::with([
            'equipment.category',
            'request',
            'requestItem'
        ])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return response()->json(['error' => 'Sobrante no encontrado'], 404);
        }

        $requestedAmount = $surplus->requestItem ? $surplus->requestItem->requested_amount : 0;

        return response()->json([
            'id' => $surplus->id,
            'equipment_name' => $surplus->equipment->name,
            'equipment_category' => $surplus->equipment->category->name ?? 'Sin categoría',
            'surplus_amount' => $surplus->surplus_amount,
            'requested_amount' => $requestedAmount,
            'unit' => $surplus->equipment->unit ?? 'unidades',
            'reason' => $surplus->reason,
            'description' => $surplus->description,
            'surplus_date' => $surplus->surplus_date->format('d/m/Y'),
            'request_id' => $surplus->request_id,
            'created_at' => $surplus->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Actualiza la cantidad y descripción de un sobrante
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->verifyRole();
        
        $surplus = Surplus::with('requestItem')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('error', 'Sobrante no encontrado.');
        }

        $requestedAmount = $surplus->requestItem ? $surplus->requestItem->requested_amount : 0;

        $request->validate([
            'surplus_amount' => [
                'required',
                'integer',
                'min:1',
                'max:' . $requestedAmount,
            ],
            'description' => 'required|string|max:1000',
        ], [
            'surplus_amount.required' => 'Debe especificar la cantidad que sobró.',
            'surplus_amount.integer' => 'La cantidad debe ser un número entero.',
            'surplus_amount.min' => 'La cantidad debe ser mayor a 0 y menor a la cantidad solicitada.',
            'surplus_amount.max' => "La cantidad debe ser mayor a 0 y menor a la cantidad solicitada ({$requestedAmount}).",
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
        ]);

        try {
            $surplus->update([
                'surplus_amount' => $request->surplus_amount,
                'description' => $request->description,
            ]);

            // Si la cantidad es mayor a 0, crear una devolución pendiente
            if ($request->surplus_amount > 0) {
                // Verificar si ya existe una devolución para este sobrante
                $existingReturn = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('surplus_id', $surplus->id)
                    ->where('role', 'Devolución')
                    ->first();

                if (!$existingReturn) {
                    // Obtener la unidad productiva/almacén de la solicitud
                    $requestData = $surplus->request;
                    $productiveUnitWarehouseId = $requestData ? $requestData->productive_unit_warehouse_id : null;

                    if (!$productiveUnitWarehouseId) {
                        $productiveUnitWarehouse = \Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse::first();
                        $productiveUnitWarehouseId = $productiveUnitWarehouse ? $productiveUnitWarehouse->id : null;
                    }

                    if ($productiveUnitWarehouseId) {
                        // Crear devolución pendiente
                        $returnMovement = \Modules\INFRASTOCK\Entities\WarehouseMovement::create([
                            'productive_unit_warehouse_id' => $productiveUnitWarehouseId,
                            'movement_id' => null,
                            'equipment_id' => $surplus->equipment_id,
                            'item_type' => 'equipment',
                            'user_id' => $surplus->user_id,
                            'role' => 'Devolución',
                            'amount' => $request->surplus_amount,
                            'status' => 'pending',
                            'surplus_id' => $surplus->id,
                            'description' => $request->description,
                        ]);

                        // Enviar notificación al administrador
                        $this->notifyAdminReturn($surplus, $returnMovement);
                    }
                } else {
                    // Actualizar la devolución existente
                    $existingReturn->update([
                        'amount' => $request->surplus_amount,
                        'description' => $request->description,
                        'status' => 'pending', // Reiniciar estado a pendiente si fue rechazada
                    ]);
                }
            }

            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('success', 'Sobrante actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('error', 'Error al actualizar el sobrante: ' . $e->getMessage());
        }
    }

    /**
     * Envía notificación al administrador sobre una nueva devolución
     * @param Surplus $surplus
     * @param WarehouseMovement $returnMovement
     * @return void
     */
    private function notifyAdminReturn($surplus, $returnMovement)
    {
        try {
            // Obtener todos los administradores
            $admins = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'Administrador')
                      ->where('app_id', 19); // ID de la app INFRASTOCK
            })->get();

            foreach ($admins as $admin) {
                // Crear notificación en la base de datos
                $admin->notify(new \Modules\INFRASTOCK\Notifications\ReturnNotification($surplus, $returnMovement));

                // Enviar email al administrador
                try {
                    \Mail::to($admin->email)->send(
                        new \Modules\INFRASTOCK\Mail\ReturnNotificationMail($surplus, $returnMovement, $admin)
                    );
                } catch (\Exception $e) {
                    \Log::error('Error enviando email de devolución al administrador: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación de devolución al administrador: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un registro de sobrante
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->verifyRole();
        $surplus = Surplus::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('error', 'Sobrante no encontrado.');
        }

        try {
            $surplus->delete();
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('success', 'Sobrante eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('infrastock.cleaning-staff.surplus.index')
                ->with('error', 'Error al eliminar el sobrante: ' . $e->getMessage());
        }
    }
}
