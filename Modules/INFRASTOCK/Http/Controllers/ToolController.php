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
        $tools = Tool::with('category', 'labor', 'inventory')->get(); // Obtiene todas las herramientas con sus relaciones.
        $categories = InfrastockCategory::where('type', 'tool')->get(); // Obtiene categorías específicas para herramientas.
        $labors = Labor::all(); // Obtiene todas las labores.
        $inventories = Inventory::all(); // Obtiene todos los inventarios.
        // Retorna la vista index de herramientas con todos los datos necesarios.
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
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id', // ID de inventario es obligatorio y debe existir.
            'labor_id' => 'required|exists:labors,id', // ID de labor es obligatorio y debe existir.
            'amount' => 'required|integer|min:0', // Cantidad es obligatoria, entera y mínimo 0.
            'price' => 'required|numeric|min:0', // Precio es obligatorio, numérico y mínimo 0.
            'category_id' => 'required|exists:infrastock_categories,id', // ID de categoría es obligatorio y debe existir.
        ]);

        Tool::create($request->all()); // Crea una nueva herramienta con los datos validados.

        // Redirige a la vista index con un mensaje de éxito.
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
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'labor_id' => 'required|exists:labors,id',
            'amount' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:infrastock_categories,id',
        ]);

        $tool = Tool::findOrFail($id); // Encuentra la herramienta por su ID o lanza una excepción.
        $tool->update($request->all()); // Actualiza la herramienta con los nuevos datos.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta actualizada exitosamente.');
    }

    /**
     * Elimina una herramienta de la base de datos.
     * @param int $id El ID de la herramienta a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $tool = Tool::findOrFail($id); // Encuentra la herramienta por su ID o lanza una excepción.
        $tool->delete(); // Elimina la herramienta de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.tools.index')->with('success', 'Herramienta eliminada exitosamente.');
    }
}
