<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\Equipment;
use Modules\INFRASTOCK\Entities\InfrastockCategory;
use Modules\INFRASTOCK\Entities\Labor;
use Modules\INFRASTOCK\Entities\Inventory;

/**
 * @class SupplyController
 * @brief Controlador para la gestión de Insumos en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD para los equipos/insumos.
 * Permite registrar, visualizar, actualizar y eliminar insumos.
 * Utiliza modales para la creación y edición de insumos para una experiencia de usuario optimizada.
 */
class SupplyController extends Controller
{
    /**
     * Muestra una lista de todos los insumos registrados.
     * Carga las relaciones de categoría, labor e inventario para cada insumo.
     * También obtiene categorías de tipo 'supply', labores e inventarios para poblar los selectores en los modales.
     * @return Renderable
     */
    public function index()
    {
        $supplies = Equipment::with('category', 'labor', 'inventory')->get(); // Obtiene todos los insumos con sus relaciones.
        $categories = InfrastockCategory::where('type', 'supply')->get(); // Obtiene categorías específicas para insumos.
        $labors = Labor::all(); // Obtiene todas las labores.
        $inventories = Inventory::all(); // Obtiene todos los inventarios.
        // Retorna la vista index de insumos con todos los datos necesarios.
        return view('infrastock::admin.supplies.index', compact('supplies', 'categories', 'labors', 'inventories'));
    }

    /**
     * Muestra el formulario para crear un nuevo insumo.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.supplies.index');
    }

    /**
     * Almacena un nuevo insumo en la base de datos.
     * Realiza validación de los datos antes del almacenamiento.
     * @param Request $request La solicitud HTTP que contiene los datos del insumo.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id', // ID de inventario es obligatorio y debe existir.
            'name' => 'required|string|max:255', // Nombre del insumo es obligatorio y máximo 255 caracteres.
            'amount' => 'required|integer|min:0', // Cantidad es obligatoria, entera y mínimo 0.
            'price' => 'required|numeric|min:0', // Precio es obligatorio, numérico y mínimo 0.
            'category_id' => 'required|exists:infrastock_categories,id', // ID de categoría es obligatorio y debe existir.
            'labor_id' => 'required|exists:labors,id', // ID de labor es obligatorio y debe existir.
        ]);

        Equipment::create($request->all()); // Crea un nuevo insumo con los datos validados.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'Insumo registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un insumo específico.
     * @param int $id El ID del insumo.
     * @return Renderable
     */
    public function show($id)
    {
        $supply = Equipment::with('category', 'labor', 'inventory')->findOrFail($id); // Encuentra el insumo con sus relaciones.
        return view('infrastock::admin.supplies.show', compact('supply')); // Retorna la vista show con los detalles del insumo.
    }

    /**
     * Muestra el formulario para editar un insumo específico.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID del insumo.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.supplies.index');
    }

    /**
     * Actualiza un insumo existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados del insumo.
     * @param int $id El ID del insumo a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:infrastock_categories,id',
            'labor_id' => 'required|exists:labors,id',
        ]);

        $supply = Equipment::findOrFail($id); // Encuentra el insumo por su ID o lanza una excepción.
        $supply->update($request->all()); // Actualiza el insumo con los nuevos datos.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'Insumo actualizado exitosamente.');
    }

    /**
     * Elimina un insumo de la base de datos.
     * @param int $id El ID del insumo a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $supply = Equipment::findOrFail($id); // Encuentra el insumo por su ID o lanza una excepción.
        $supply->delete(); // Elimina el insumo de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.supplies.index')->with('success', 'Insumo eliminado exitosamente.');
    }
}
