<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\ProductiveUnit;
use Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse;
use Modules\INFRASTOCK\Entities\WarehouseMovement;

/**
 * @class AreaController
 * @brief Controlador para la gestión de Áreas Productivas en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para las
 * unidades productivas, que representan las áreas dentro del centro de formación.
 * Utiliza modales para la creación y edición de áreas para una mejor experiencia de usuario.
 */
class AreaController extends Controller
{
    /**
     * Muestra una lista de todas las áreas productivas.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $query = ProductiveUnit::query();

        // Búsqueda server-side
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $areas = $query->paginate(15)->appends($request->query());
        return view('infrastock::admin.areas.index', compact('areas'));
    }

    /**
     * Muestra el formulario para crear una nueva área productiva.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.areas.index');
    }

    /**
     * Almacena una nueva área productiva en la base de datos.
     * Realiza validación de los datos antes de la creación.
     * @param Request $request La solicitud HTTP que contiene los datos del área.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Verificar si el nombre ya existe antes de validar
        $existingArea = ProductiveUnit::where('name', $request->name)->first();
        if ($existingArea) {
            return redirect()->route('infrastock.admin.areas.index')
                ->with('error', 'El nombre del área ya está en uso. Por favor, elige otro nombre.')
                ->withInput();
        }

        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|max:255', // El nombre es obligatorio y máximo 255 caracteres.
            'description' => 'nullable', // La descripción es opcional.
        ]);

        // Preparar los datos para crear el área, asegurando que description no sea null
        $data = $request->all();
        $data['description'] = $data['description'] ?? ''; // Si description es null, usar string vacío
        
        try {
            ProductiveUnit::create($data); // Crea una nueva unidad productiva con los datos validados.
        } catch (\Illuminate\Database\QueryException $e) {
            // Si hay error de duplicado en la base de datos
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.areas.index')
                    ->with('error', 'El nombre del área ya está en uso. Por favor, elige otro nombre.')
                    ->withInput();
            }
            throw $e; // Re-lanzar otros errores de base de datos
        }

        // Si es una petición AJAX, devolver respuesta JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Área creada exitosamente.'
            ]);
        }

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.areas.index')->with('success', 'Área creada exitosamente.');
    }

    /**
     * Muestra los detalles de un área productiva específica.
     * Redirige al index ya que la visualización se realiza a través de un modal de edición en la vista principal.
     * @param int $id El ID del área productiva.
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('infrastock.admin.areas.index');
    }

    /**
     * Muestra el formulario para editar un área productiva específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID del área productiva.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.areas.index');
    }

    /**
     * Actualiza un área productiva existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados del área.
     * @param int $id El ID del área productiva a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Verificar si el nombre ya existe en otra área antes de validar
        $existingArea = ProductiveUnit::where('name', $request->name)->where('id', '!=', $id)->first();
        if ($existingArea) {
            return redirect()->route('infrastock.admin.areas.index')
                ->with('error', 'El nombre del área ya está en uso. Por favor, elige otro nombre.')
                ->withInput();
        }

        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $area = ProductiveUnit::findOrFail($id); // Encuentra el área por su ID o lanza una excepción.
        
        // Preparar los datos para actualizar el área, asegurando que description no sea null
        $data = $request->all();
        $data['description'] = $data['description'] ?? ''; // Si description es null, usar string vacío
        
        try {
            $area->update($data); // Actualiza el área con los nuevos datos.
        } catch (\Illuminate\Database\QueryException $e) {
            // Si hay error de duplicado en la base de datos
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.areas.index')
                    ->with('error', 'El nombre del área ya está en uso. Por favor, elige otro nombre.')
                    ->withInput();
            }
            throw $e; // Re-lanzar otros errores de base de datos
        }

        // Si es una petición AJAX, devolver respuesta JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Área actualizada exitosamente.'
            ]);
        }

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.areas.index')->with('success', 'Área actualizada exitosamente.');
    }

    /**
     * Elimina un área productiva de la base de datos.
     * Valida que no tenga historial activo de insumos o herramientas antes de eliminar.
     * @param int $id El ID del área productiva a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $area = ProductiveUnit::findOrFail($id);

        // Obtener los IDs de los almacenes asociados a esta área
        $warehouseIds = ProductiveUnitWarehouse::where('productive_unit_id', $area->id)
            ->whereNull('deleted_at')
            ->pluck('id');

        // Verificar si tiene movimientos (préstamos, solicitudes, devoluciones) activos
        if ($warehouseIds->isNotEmpty()) {
            $activeMovements = WarehouseMovement::whereIn('productive_unit_warehouse_id', $warehouseIds)
                ->whereIn('status', ['pending', 'approved'])
                ->whereNull('deleted_at')
                ->count();

            if ($activeMovements > 0) {
                $message = "No se puede eliminar esta área porque tiene {$activeMovements} movimiento(s) activo(s) (préstamos, solicitudes o devoluciones).";
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->route('infrastock.admin.areas.index')->with('error', $message);
            }
        }

        $area->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Área eliminada exitosamente.']);
        }
        return redirect()->route('infrastock.admin.areas.index')->with('success', 'deleted');
    }
}
