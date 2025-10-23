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
     * Muestra la página principal de sobrantes con el formulario
     * @return Renderable
     */
    public function index()
    {
        // Obtener equipos disponibles para el formulario
        $equipments = Equipment::with('category')
            ->orderBy('name')
            ->get();

        // Obtener sobrantes del usuario actual
        $surpluses = Surplus::with('equipment.category')
            ->where('user_id', auth()->id())
            ->orderBy('surplus_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('infrastock::cleaning-staff.surplus.index', compact('equipments', 'surpluses'));
    }

    /**
     * Almacena un nuevo registro de sobrante
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
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
     * Muestra los detalles de un sobrante específico
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $surplus = Surplus::with('equipment.category')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$surplus) {
            return response()->json(['error' => 'Sobrante no encontrado'], 404);
        }

        return response()->json([
            'id' => $surplus->id,
            'equipment_name' => $surplus->equipment->name,
            'equipment_category' => $surplus->equipment->category->name ?? 'Sin categoría',
            'surplus_amount' => $surplus->surplus_amount,
            'unit' => $surplus->equipment->unit ?? 'unidades',
            'reason' => $surplus->reason,
            'surplus_date' => $surplus->surplus_date->format('d/m/Y'),
            'created_at' => $surplus->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Elimina un registro de sobrante
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
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
