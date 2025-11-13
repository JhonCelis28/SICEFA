<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @class UserProfileController
 * @brief Controlador para la gestión del perfil de usuario en el módulo INFRASTOCK.
 *
 * Este controlador maneja la visualización y edición del perfil del usuario autenticado.
 * Métodos CRUD adicionales son placeholders y no se utilizan activamente, ya que la gestión
 * principal del perfil se enfoca en la vista de edición.
 */
class UserProfileController extends Controller
{
    /**
     * Muestra una lista de recursos (placeholder).
     * Redirige a la vista de edición de perfil, ya que no hay una lista de perfiles para mostrar.
     * @return Renderable
     */
    public function index()
    {
        return redirect()->route('cefa.infrastock.admin.profile.edit');
    }

    /**
     * Muestra el formulario para crear un nuevo recurso (placeholder).
     * Redirige a la vista de edición de perfil, ya que no se crean perfiles de usuario desde aquí.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->route('cefa.infrastock.admin.profile.edit');
    }

    /**
     * Almacena un recurso recién creado (placeholder).
     * Este método es un placeholder y no implementa ninguna lógica de almacenamiento de perfil.
     * @param Request $request La solicitud HTTP.
     * @return Renderable
     */
    public function store(Request $request)
    {
        // Este método es un placeholder. La lógica de almacenamiento de perfil se manejaría en un método update.
    }

    /**
     * Muestra los detalles de un recurso específico (placeholder).
     * Redirige a la vista de edición de perfil, ya que la visualización detallada se realiza allí.
     * @param int $id El ID del recurso (no utilizado activamente).
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->route('cefa.infrastock.admin.profile.edit');
    }

    /**
     * Muestra el formulario para editar el perfil del usuario autenticado.
     * @return Renderable
     */
    public function edit()
    {
        // Aquí se puede cargar la información del usuario autenticado si es necesario
        // $user = auth()->user();
        return view('infrastock::admin.profile'); // Retorna la vista de edición de perfil.
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     * @param Request $request La solicitud HTTP que contiene los datos actualizados.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nickname' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'nickname' => $request->nickname,
            ];

            // Solo actualizar la contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = \Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('cefa.infrastock.admin.profile.edit')
                ->with('success', 'Perfil actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->route('cefa.infrastock.admin.profile.edit')
                ->with('error', 'Hubo un error al actualizar el perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina el recurso especificado del almacenamiento (placeholder).
     * Este método es un placeholder y no implementa ninguna lógica de eliminación de perfil.
     * @param int $id El ID del recurso a eliminar (no utilizado activamente).
     * @return Renderable
     */
    public function destroy($id)
    {
        // Este método es un placeholder. La lógica de eliminación de perfil se implementaría aquí.
    }
}
