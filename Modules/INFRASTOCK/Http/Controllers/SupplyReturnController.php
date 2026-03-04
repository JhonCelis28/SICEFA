<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\Notification;

class SupplyReturnController extends Controller
{
    /**
     * Muestra la lista de devoluciones de insumos (historial completo)
     * @param Request $request
     * @return Renderable
     */
    public function index(Request $request)
    {
        // Obtener todos los sobrantes de insumos (equipment) con historial completo
        $query = Surplus::with([
            'equipment.category',
            'user' => function ($query) {
            $query->with('roles');
        },
            'request'
        ]);

        // Filtrar por estado si se proporciona
        if ($request->has('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Ordenar por fecha de creación (más recientes primero)
        // Aumentar el número de registros por página para facilitar la visualización
        $surpluses = $query->orderBy('created_at', 'desc')->paginate(50);

        // Contadores para las tarjetas de resumen
        $pendingCount = Surplus::where('status', 'pending')->count();
        $approvedCount = Surplus::where('status', 'approved')->count();
        $rejectedCount = Surplus::where('status', 'rejected')->count();
        $totalCount = Surplus::count();

        // Log para depuración
        \Log::info('SupplyReturnController@index - Total surpluses: ' . $totalCount . ', Pendientes: ' . $pendingCount . ', Aprobadas: ' . $approvedCount . ', Rechazadas: ' . $rejectedCount);
        \Log::info('SupplyReturnController@index - Query result count: ' . $surpluses->total());

        // Las notificaciones ya están compartidas por el middleware ShareNotifications
        // No es necesario cargarlas aquí para evitar conflictos

        return view('infrastock::admin.supply-returns.index', compact('surpluses', 'pendingCount', 'approvedCount', 'rejectedCount', 'totalCount'));
    }

    /**
     * Aprueba una devolución de insumo, incrementando el stock
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        $surplus = Surplus::with(['equipment', 'user'])->findOrFail($id);

        try {
            // Obtener la primera unidad productiva/almacén disponible
            $productiveUnitWarehouse = ProductiveUnitWarehouse::first();

            if (!$productiveUnitWarehouse) {
                return redirect()->route('infrastock.admin.supply-returns.index')
                    ->with('error', 'No se encontró una unidad productiva/almacén configurada.');
            }

            // Crear movimiento de recepción (Recibe) para incrementar el stock
            WarehouseMovement::create([
                'productive_unit_warehouse_id' => $productiveUnitWarehouse->id,
                'equipment_id' => $surplus->equipment_id,
                'item_type' => 'equipment',
                'user_id' => auth()->id(), // Admin que aprueba
                'role' => 'Recibe',
                'amount' => $surplus->surplus_amount,
                'description' => 'Devolución aprobada - Sobrante #' . $surplus->id . ' | Usuario: ' . ($surplus->user->name ?? 'N/A') . ' | Motivo: ' . ($surplus->reason ?? 'N/A'),
            ]);

            // Marcar el sobrante como aprobado (mantener historial)
            $surplus->update([
                'status' => 'approved',
                'processed_at' => now(),
                'processed_by' => auth()->user()->name ?? 'Administrador',
            ]);

            // Notificar al usuario que realizó la devolución
            $this->notifyUserSurplusStatus($surplus, 'approved');

            return redirect()->route('infrastock.admin.supply-returns.index')
                ->with('success', 'Devolución aprobada exitosamente. El stock ha sido actualizado.');

        }
        catch (\Exception $e) {
            \Log::error('Error al aprobar devolución: ' . $e->getMessage());
            return redirect()->route('infrastock.admin.supply-returns.index')
                ->with('error', 'Error al aprobar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza una devolución de insumo
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $surplus = Surplus::findOrFail($id);

        try {
            // Marcar el sobrante como rechazado (mantener historial)
            $surplus->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->user()->name ?? 'Administrador',
                'description' => ($surplus->description ?? '') . ' | Rechazado: ' . $request->rejection_reason,
            ]);

            // Notificar al usuario que realizó la devolución
            $this->notifyUserSurplusStatus($surplus, 'rejected', $request->rejection_reason);

            return redirect()->route('infrastock.admin.supply-returns.index')
                ->with('success', 'Devolución rechazada exitosamente.');

        }
        catch (\Exception $e) {
            \Log::error('Error al rechazar devolución: ' . $e->getMessage());
            return redirect()->route('infrastock.admin.supply-returns.index')
                ->with('error', 'Error al rechazar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Notifica al usuario sobre el cambio de estado de su devolución
     * @param Surplus $surplus
     * @param string $status
     * @param string|null $reason
     * @return void
     */
    private function notifyUserSurplusStatus(Surplus $surplus, $status, $reason = null)
    {
        try {
            $isApproved = ($status === 'approved');
            $title = $isApproved ? 'Devolución Aprobada' : 'Devolución Rechazada';
            $message = $isApproved
                ? "Tu devolución de {$surplus->surplus_amount} unidades de {$surplus->equipment->name} ha sido aprobada."
                : "Tu devolución de {$surplus->surplus_amount} unidades de {$surplus->equipment->name} ha sido rechazada.";

            if (!$isApproved && $reason) {
                $message .= " Motivo: {$reason}";
            }

            Notification::create([
                'type' => $isApproved ? 'surplus_approved' : 'surplus_rejected',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $surplus->user_id,
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'surplus_id' => $surplus->id,
                    'equipment_name' => $surplus->equipment->name ?? 'Insumo',
                    'surplus_amount' => $surplus->surplus_amount,
                    'status' => $status,
                    'processed_at' => now()->format('d/m/Y H:i'),
                    'action_url' => route('infrastock.cleaning-staff.surplus.index'), // Ajustar según el rol si es necesario
                ],
            ]);
        }
        catch (\Exception $e) {
            \Log::error('Error enviando notificación de estado de devolución al usuario: ' . $e->getMessage());
        }
    }
}
