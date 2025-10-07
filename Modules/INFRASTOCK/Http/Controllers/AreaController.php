<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\INFRASTOCK\Entities\ProductiveUnit;

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
    public function index()
    {
        $areas = ProductiveUnit::all(); // Obtiene todas las unidades productivas.
        return view('infrastock::admin.areas.index', compact('areas')); // Retorna la vista index con las áreas.
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
        // Valida los datos de entrada de la solicitud.
        $request->validate([
            'name' => 'required|unique:productive_units|max:255', // El nombre es obligatorio, único y máximo 255 caracteres.
            'description' => 'nullable', // La descripción es opcional.
        ]);

        ProductiveUnit::create($request->all()); // Crea una nueva unidad productiva con los datos validados.

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
        // Valida los datos de entrada de la solicitud, asegurando que el nombre sea único excluyendo el área actual.
        $request->validate([
            'name' => 'required|max:255|unique:productive_units,name,' . $id,
            'description' => 'nullable',
        ]);

        $area = ProductiveUnit::findOrFail($id); // Encuentra el área por su ID o lanza una excepción.
        $area->update($request->all()); // Actualiza el área con los nuevos datos.

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.areas.index')->with('success', 'Área actualizada exitosamente.');
    }

    /**
     * Elimina un área productiva de la base de datos.
     * @param int $id El ID del área productiva a eliminar.
     * @return Renderable
     */
    public function destroy($id)
    {
        $area = ProductiveUnit::findOrFail($id); // Encuentra el área por su ID o lanza una excepción.
        $area->delete(); // Elimina el área de la base de datos (soft delete si está configurado).

        // Redirige a la vista index con un mensaje de éxito.
        return redirect()->route('infrastock.admin.areas.index')->with('success', 'Área eliminada exitosamente.');
    }
}
