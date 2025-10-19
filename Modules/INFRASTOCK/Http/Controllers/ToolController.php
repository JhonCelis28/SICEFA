<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Tool;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;

/**
 * @class ToolController
 * @brief Controlador para la gestión de Herramientas en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para las herramientas.
 * Permite registrar, visualizar, actualizar y eliminar herramientas.
 * Utiliza modales para la creación y edición de herramientas para una experiencia de usuario optimizada.
 */
class ToolController extends Controller
{
    /**
     * Muestra una lista de todas las herramientas registradas.
     * Carga las relaciones de categoría, labor e inventario para cada herramienta.
     * También obtiene categorías de tipo 'tool', labores e inventarios para poblar los selectores en los modales.
     * @return Renderable
     */
    public function index()
    {
        $tools = Tool::with('category', 'labor', 'inventory')->paginate(15); // Obtiene las herramientas con paginación (15 por página).
        $categories = InfrastockCategory::where('type', 'tool')->get(); // Obtiene categorías específicas para herramientas.
        $labors = Labor::all(); // Obtiene todas las labores.
        $inventories = Inventory::all(); // Obtiene todos los inventarios.
        return view('infrastock::admin.tools.index', compact('tools', 'categories', 'labors', 'inventories'));
    }

    /**
     * Muestra el formulario para crear una nueva herramienta.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.tools.index');
    }

    /**
     * Almacena una nueva herramienta en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos de la herramienta.
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'labor_id' => 'required|exists:labors,id',
            'amount' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:infrastock_categories,id',
        ]);

        try {
            Tool::create($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', 'Ya existe una herramienta con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta registrada exitosamente.');
    }

    /**
     * Muestra los detalles de una herramienta específica.
     * @param int $id El ID de la herramienta.
     * @return Renderable
     */
    public function show($id)
    {
        $tool = Tool::with('category', 'labor', 'inventory')->findOrFail($id); // Encuentra la herramienta con sus relaciones.
        return view('infrastock::admin.tools.show', compact('tool')); // Retorna la vista show con los detalles de la herramienta.
    }

    /**
     * Muestra el formulario para editar una herramienta específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID de la herramienta.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.tools.index');
    }

    /**
     * Actualiza una herramienta existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados de la herramienta.
     * @param int $id El ID de la herramienta a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'labor_id' => 'required|exists:labors,id',
            'amount' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:infrastock_categories,id',
        ]);

        $tool = Tool::findOrFail($id);
        
        try {
            $tool->update($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.tools.index')
                    ->with('error', 'Ya existe una herramienta con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta actualizada exitosamente.');
    }

    /**
     * Elimina una herramienta de la base de datos.
     * @param int $id El ID de la herramienta a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $tool = Tool::findOrFail($id);
        $hasRelatedRecords = false; // TODO: Implement validation
        
        if ($hasRelatedRecords) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la herramienta porque tiene registros relacionados.'
                ], 422);
            }
            return redirect()->route('infrastock.admin.tools.index')->with('error', 'No se puede eliminar la herramienta porque tiene registros relacionados.');
        }
        
        $tool->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Herramienta eliminada exitosamente.'
            ]);
        }
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'deleted');
    }
}
