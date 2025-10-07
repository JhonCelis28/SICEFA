<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\InfrastockCategory;

/**
 * @class CategoryController
 * @brief Controlador para la gestión de Categorías de Insumos y Herramientas en el módulo INFRASTOCK.
 *
 * Este controlador maneja las operaciones CRUD (Crear, Leer, Actualizar, Eliminar) para las
 * categorías utilizadas para clasificar insumos y herramientas.
 * Utiliza modales para la creación y edición de categorías para una mejor experiencia de usuario.
 */
class CategoryController extends Controller
{
    /**
     * Muestra una lista de todas las categorías de insumos y herramientas.
     * @return Renderable
     */
    public function index()
    {
        $categories = InfrastockCategory::all(); // Obtiene todas las categorías.
        return view('infrastock::admin.categories.index', compact('categories')); // Retorna la vista index con las categorías.
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     * Redirige al index ya que la creación se realiza a través de un modal en la vista principal.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('infrastock.admin.categories.index');
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     * Realiza validación de los datos antes de la creación.
     * @param Request $request La solicitud HTTP que contiene los datos de la categoría.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|unique:infrastock_categories|max:255', // El nombre es obligatorio, único y máximo 255 caracteres.
            'type' => 'required|in:supply,tool', // El tipo es obligatorio y debe ser 'supply' o 'tool'.
        ]);

        InfrastockCategory::create($request->all()); // Crea una nueva categoría con los datos validados.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Muestra los detalles de una categoría específica.
     * Redirige al index ya que la visualización se realiza a través de un modal de edición en la vista principal.
     * @param int $id El ID de la categoría.
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('infrastock.admin.categories.index');
    }

    /**
     * Muestra el formulario para editar una categoría específica.
     * Redirige al index ya que la edición se realiza a través de un modal en la vista principal.
     * @param int $id El ID de la categoría.
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('infrastock.admin.categories.index');
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     * Realiza validación de los datos antes de la actualización.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados de la categoría.
     * @param int $id El ID de la categoría a actualizar.
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // Valida los datos de entrada de la solicitud, asegurando que el nombre sea único excluyendo la categoría actual.
        $request->validate([
            'name' => 'required|max:255|unique:infrastock_categories,name,' . $id,
            'type' => 'required|in:supply,tool',
        ]);

        $category = InfrastockCategory::findOrFail($id); // Encuentra la categoría por su ID o lanza una excepción.
        $category->update($request->all()); // Actualiza la categoría con los nuevos datos.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Elimina una categoría de la base de datos.
     * @param int $id El ID de la categoría a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $category = InfrastockCategory::findOrFail($id); // Encuentra la categoría por su ID o lanza una excepción.
        $category->delete(); // Elimina la categoría de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
