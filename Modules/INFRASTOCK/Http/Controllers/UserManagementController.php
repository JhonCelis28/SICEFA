<?php

namespace Modules\INFRASTOCK\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Modules\SICA\Entities\Person;
use Modules\SICA\Entities\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @class UserManagementController
 * @brief Controlador para la gestión de usuarios en el módulo INFRASTOCK.
 *
 * Este controlador maneja todas las operaciones relacionadas con la gestión de usuarios
 * desde el panel administrativo, incluyendo registro de nuevos usuarios, listado de usuarios
 * existentes y cambio de estado activo/inactivo.
 */
class UserManagementController extends Controller
{
    /**
     * Obtiene los nombres de los roles de INFRASTOCK
     * Solo incluye roles que realmente existen con app_id = 19
     * @return array
     */
    private function getInfrastockRoleNames()
    {
        return [
            'Operario',
            'Aseo',
            'Ganadería', // Con tilde, como está en la BD
            'Centro de Convivencia',
            'Vigilancia',
            'Agroindustria',
            'Administrador',
            'Ciencias Basicas',
            'Psicola',
            'Instructor'
        ];
    }

    /**
     * Obtiene todos los roles de INFRASTOCK (solo app_id = 19 y nombres específicos)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getInfrastockRoles()
    {
        $infrastockRoleNames = $this->getInfrastockRoleNames();
        
        // Obtener SOLO roles con app_id = 19 Y que estén en la lista de nombres
        $roles = Role::where('app_id', 19)
            ->whereIn('name', $infrastockRoleNames)
            ->orderBy('name')
            ->get();

        return $roles;
    }

    /**
     * Muestra el formulario para registrar un nuevo usuario.
     * @return Renderable
     */
    public function create()
    {
        // Obtener todos los roles de INFRASTOCK
        $roles = $this->getInfrastockRoles();

        // Si no se encuentran roles, mostrar mensaje informativo pero permitir continuar
        if ($roles->isEmpty()) {
            \Log::warning('No se encontraron roles de INFRASTOCK');
        } else {
            \Log::info('Roles de INFRASTOCK encontrados: ' . $roles->pluck('name')->implode(', '));
        }

        return view('infrastock::admin.users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Log de los datos recibidos
        \Log::info('Datos recibidos en store:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'first_last_name' => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:20|unique:people,document_number',
            'email' => 'required|email|max:100|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|in:0,1',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_last_name.required' => 'El primer apellido es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'role_id.required' => 'Debe seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'is_active.required' => 'Debe especificar si el usuario está activo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            \Log::error('Errores de validación:', $validator->errors()->toArray());
            \Log::error('Datos recibidos:', $request->all());
            
            // Construir mensaje de error más específico
            $errorMessages = [];
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
            $errorMessage = 'Por favor, corrige los siguientes errores: ' . implode(' ', $errorMessages);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', $errorMessage);
        }

        try {
            DB::beginTransaction();

            // Verificar que el rol existe
            $role = Role::find($request->role_id);
            if (!$role) {
                \Log::error('Rol no encontrado con ID: ' . $request->role_id);
                throw new \Exception('El rol seleccionado no existe. Por favor, selecciona un rol válido.');
            }
            
            // Verificar que el rol pertenece a INFRASTOCK (app_id = 19 o nombre específico)
            $infrastockRoleNames = $this->getInfrastockRoleNames();
            
            if ($role->app_id != 19 && !in_array($role->name, $infrastockRoleNames)) {
                \Log::warning('Rol no válido para INFRASTOCK. Rol ID: ' . $role->id . ', Nombre: ' . $role->name . ', app_id: ' . $role->app_id);
                throw new \Exception('El rol seleccionado no pertenece al módulo INFRASTOCK.');
            }
            
            // Log para debugging
            \Log::info('Registrando usuario con rol: ' . $role->name . ' (ID: ' . $role->id . ', app_id: ' . $role->app_id . ')');

            // Crear la persona
            $person = Person::create([
                'first_name' => $request->first_name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'telephone1' => $request->phone,
                'address' => $request->address,
                'eps_id' => 1, // Valor por defecto para EPS
                'population_group_id' => 1, // Valor por defecto para grupo poblacional
                'pension_entity_id' => 1, // Valor por defecto para entidad de pensión
                'date_of_issue' => now()->format('Y-m-d'), // Fecha de emisión por defecto
                'date_of_birth' => '1990-01-01', // Fecha de nacimiento por defecto
                'gender' => '1', // Género por defecto (1=Masculino, 2=Femenino)
                'marital_status' => '1', // Estado civil por defecto (1=Soltero)
                'blood_type' => '1', // Tipo de sangre por defecto (1=O+)
                'military_card' => '1', // Libreta militar por defecto (1=No aplica)
                'socioeconomical_status' => '1', // Estrato socioeconómico por defecto (1=Estrato 3)
                'sisben_level' => '1', // Nivel SISBEN por defecto (1=Nivel 1)
            ]);

            // Generar contraseña por defecto si no se proporciona
            $password = $request->password;
            if (empty($password)) {
                $first_name = \Illuminate\Support\Str::ascii($request->first_name);
                $first_last_name = \Illuminate\Support\Str::ascii($request->first_last_name);
                $password = ucfirst(strtolower(
                    substr($first_name, 0, 2) .
                    substr($first_last_name, 0, 2) .
                    substr($request->document_number, -4)
                ));
            }

            // Crear el usuario
            $user = User::create([
                'person_id' => $person->id,
                'email' => $request->email,
                'password' => Hash::make($password),
                'nickname' => $request->first_name . ' ' . $request->first_last_name,
                'email_verified_at' => now(), // Marcar email como verificado
            ]);

            // Asignar rol
            $user->roles()->attach($request->role_id);
            
            // Verificar que el rol se asignó correctamente
            $user->refresh();
            \Log::info('Usuario creado ID: ' . $user->id . ', Roles asignados: ' . $user->roles->pluck('name')->implode(', '));

            // Marcar como activo/inactivo usando soft deletes
            if ($request->is_active == '0') {
                $user->delete(); // Soft delete para marcar como inactivo
                \Log::info('Usuario marcado como inactivo (soft delete)');
            }

            DB::commit();

            return redirect()->route('infrastock.admin.users.index')
                ->with('success', 'Usuario registrado exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.users.index')
                    ->with('error', 'Ya existe un usuario con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log del error para debugging
            \Log::error('Error al registrar usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            $errorMessage = 'Error al registrar el usuario. ';
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $errorMessage .= 'Error de base de datos. Por favor, verifica los datos e intenta nuevamente.';
            } else {
                $errorMessage .= $e->getMessage();
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Muestra la lista de usuarios registrados.
     * @return Renderable
     */
    public function index()
    {
        try {
            // Obtener todos los roles de INFRASTOCK
            $roles = $this->getInfrastockRoles();
            $infrastockRoleNames = $this->getInfrastockRoleNames();
            
            // Obtener SOLO los IDs de roles que tienen app_id = 19 Y están en la lista de nombres
            $infrastockRoleIds = Role::where('app_id', 19)
                ->whereIn('name', $infrastockRoleNames)
                ->pluck('id')
                ->toArray();
            
            // Si no hay roles de INFRASTOCK, retornar lista vacía
            if (empty($infrastockRoleIds)) {
                $users = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect([]),
                    0,
                    15,
                    1,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
                return view('infrastock::admin.users.index', compact('users', 'roles'));
            }
            
            // Obtener usuarios que tienen al menos un rol de INFRASTOCK (app_id = 19)
            $usersWithInfrastockRoles = User::with(['person', 'roles'])
                ->withTrashed()
                ->whereHas('roles', function($query) use ($infrastockRoleIds) {
                    $query->whereIn('roles.id', $infrastockRoleIds);
                })
                ->get();
            
            // Filtrar usuarios que SOLO tienen roles de INFRASTOCK (app_id = 19 y nombre en la lista)
            $filteredUsers = $usersWithInfrastockRoles->filter(function($user) use ($infrastockRoleNames) {
                // Si el usuario no tiene roles, excluirlo
                if ($user->roles->isEmpty()) {
                    return false;
                }
                
                // Verificar que TODOS los roles del usuario tienen app_id = 19 Y están en la lista
                foreach ($user->roles as $role) {
                    // Verificar que el rol tiene app_id = 19
                    if ($role->app_id != 19) {
                        \Log::info('Usuario excluido: ' . $user->id . ' tiene rol: ' . $role->name . ' con app_id: ' . ($role->app_id ?? 'null') . ' (debe ser 19)');
                        return false;
                    }
                    
                    // Verificar que el nombre del rol está en la lista
                    if (!in_array($role->name, $infrastockRoleNames)) {
                        \Log::info('Usuario excluido: ' . $user->id . ' tiene rol: ' . $role->name . ' que NO está en la lista de INFRASTOCK');
                        return false;
                    }
                }
                
                // Si llegamos aquí, todos los roles tienen app_id = 19 y están en la lista
                return true;
            });
            
            // Convertir a paginación manual
            $currentPage = request()->get('page', 1);
            $perPage = 15;
            $items = $filteredUsers->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $total = $filteredUsers->count();
            
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('infrastock::admin.users.index', compact('users', 'roles'));
            
        } catch (\Exception $e) {
            \Log::error('Error en UserManagementController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Retornar lista vacía en caso de error
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $roles = collect([]);
            
            return view('infrastock::admin.users.index', compact('users', 'roles'))
                ->with('error', 'Error al cargar la lista de usuarios. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Cambia el estado activo/inactivo de un usuario.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            if ($user->trashed()) {
                // Usuario está inactivo, activarlo
                $user->restore();
                $status = 'activo';
            } else {
                // Usuario está activo, desactivarlo
                $user->delete();
                $status = 'inactivo';
            }

            return response()->json([
                'success' => true,
                'message' => "Usuario marcado como {$status} exitosamente.",
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del usuario.'
            ], 500);
        }
    }

    /**
     * Muestra los detalles de un usuario específico.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $user = User::with(['person', 'roles'])
            ->withTrashed()
            ->findOrFail($id);

        return view('infrastock::admin.users.show', compact('user'));
    }

    /**
     * Muestra el formulario para editar un usuario.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $user = User::with(['person', 'roles'])
            ->withTrashed()
            ->findOrFail($id);

        // Log para debugging
        \Log::info('Editando usuario ID: ' . $id);
        \Log::info('Usuario encontrado: ', $user->toArray());
        \Log::info('Persona asociada: ', $user->person ? $user->person->toArray() : 'No hay persona asociada');
        \Log::info('Document type: ' . ($user->person ? $user->person->document_type : 'N/A'));
        \Log::info('Phone: ' . ($user->person ? $user->person->phone : 'N/A'));

        // Obtener todos los roles de INFRASTOCK
        $roles = $this->getInfrastockRoles();

        return view('infrastock::admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza la información de un usuario.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'first_last_name' => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'document_type' => 'required|string|max:10',
            'document_number' => 'required|string|max:20|unique:people,document_number,' . $user->person_id,
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|in:0,1',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_last_name.required' => 'El primer apellido es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Este número de documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'role_id.required' => 'Debe seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'is_active.required' => 'Debe especificar si el usuario está activo.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            DB::beginTransaction();

            // Log para debugging
            \Log::info('Actualizando usuario ID: ' . $id);
            \Log::info('Datos recibidos: ', $request->all());

            // Actualizar datos de la persona
            $user->person->update([
                'first_name' => $request->first_name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'telephone1' => $request->phone,
                'address' => $request->address,
            ]);

            // Actualizar datos del usuario
            $userData = [
                'email' => $request->email,
                'nickname' => $request->first_name . ' ' . $request->first_last_name,
            ];

            // Actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Verificar que el rol existe y pertenece a INFRASTOCK
            $role = Role::find($request->role_id);
            if (!$role) {
                throw new \Exception('El rol seleccionado no existe.');
            }
            
            // Verificar que el rol pertenece a INFRASTOCK (app_id = 19 o nombre específico)
            $infrastockRoleNames = $this->getInfrastockRoleNames();
            
            if ($role->app_id != 19 && !in_array($role->name, $infrastockRoleNames)) {
                throw new \Exception('El rol seleccionado no pertenece al módulo INFRASTOCK.');
            }

            // Actualizar rol
            $user->roles()->sync([$request->role_id]);

            // Actualizar estado activo/inactivo
            if ($request->is_active == '1' && $user->trashed()) {
                $user->restore();
            } elseif ($request->is_active == '0' && !$user->trashed()) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('infrastock.admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->route('infrastock.admin.users.index')
                    ->with('error', 'Ya existe un usuario con estos datos. Por favor, verifica la información.')
                    ->withInput();
            }
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log del error para debugging
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Verifica si un usuario tiene solicitudes pendientes.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRequests($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            // Aquí necesitarías verificar en la tabla de solicitudes
            // Por ahora, vamos a simular que no hay solicitudes pendientes
            // En el futuro, esto debería consultar la tabla real de solicitudes
            
            $pendingCount = 0; // Placeholder - aquí iría la consulta real
            $hasPendingRequests = $pendingCount > 0;
            
            return response()->json([
                'has_pending_requests' => $hasPendingRequests,
                'pending_count' => $pendingCount,
                'user_id' => $id,
                'user_name' => $user->nickname
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'has_pending_requests' => false,
                'pending_count' => 0,
                'error' => 'Error al verificar solicitudes: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            \Log::info('Intentando eliminar usuario ID: ' . $id);
            
            $user = User::withTrashed()->findOrFail($id);
            
            \Log::info('Usuario encontrado para eliminar: ', $user->toArray());
            
            // Verificar si el usuario tiene solicitudes pendientes
            $hasRelatedRecords = false; // TODO: Implement validation
            
            if ($hasRelatedRecords) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar el usuario porque tiene registros relacionados.'
                    ], 422);
                }
                return redirect()->route('infrastock.admin.users.index')->with('error', 'No se puede eliminar el usuario porque tiene registros relacionados.');
            }
            
            // Eliminar relaciones primero
            $user->roles()->detach();
            
            // Eliminar usuario y persona permanentemente
            $user->forceDelete();
            $user->person->delete();

            \Log::info('Usuario eliminado exitosamente');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario eliminado permanentemente.'
                ]);
            }
            return redirect()->route('infrastock.admin.users.index')->with('success', 'deleted');

        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('infrastock.admin.users.index')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
