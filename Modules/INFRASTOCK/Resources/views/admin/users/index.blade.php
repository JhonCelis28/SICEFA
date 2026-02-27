<!--
    * @file index.blade.php
    * @brief Vista para la gestión de Usuarios en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, registrar, editar y eliminar
    * usuarios del sistema. Utiliza Tailwind CSS para un diseño moderno y responsive, y Alpine.js para
    * la interactividad de los modales de creación y edición. Estos modales manejan las
    * operaciones de forma asíncrona (AJAX) y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\User[] $users Colección de usuarios existentes.
    * @param Modules\INFRASTOCK\Entities\Role[] $roles Colección de roles disponibles.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Usuarios')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Usuarios" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.users.index') }}" class="text-green-600 hover:text-green-800">Usuarios</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de usuarios -->
    <div x-data="userManagement()">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-start mb-6">
                <div></div>
                <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600  transition-colors duration-200 flex items-center">
                    <i class="fa-solid fa-user mr-2"></i> Registrar Usuario
                </button>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Buscar por nombre, email, documento o rol..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} de {{ $users->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipo de Documento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Documento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rol</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nickname ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @switch($user->person->document_type ?? '')
                                                @case('1')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Cédula de Ciudadanía</span>
                                                    @break
                                                @case('2')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Tarjeta de Identidad</span>
                                                    @break
                                                @case('3')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Cédula de Extranjería</span>
                                                    @break
                                                @case('4')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Pasaporte</span>
                                                    @break
                                                @default
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $user->person->document_type ?? 'N/A' }}</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="font-medium">{{ $user->person->document_number ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($user->roles->count() > 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $user->roles->first()->name }}</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Sin rol</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($user->trashed())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end items-center space-x-2">
                                                <button @click="openEditModal({{ $user->id }}, '{{ addslashes($user->person->first_name ?? '') }}', '{{ addslashes($user->person->first_last_name ?? '') }}', '{{ addslashes($user->person->second_last_name ?? '') }}', '{{ $user->person->document_type ?? '' }}', '{{ $user->person->document_number ?? '' }}', '{{ $user->person->telephone1 ?? '' }}', '{{ $user->email }}', '{{ addslashes($user->person->address ?? '') }}', {{ $user->roles->first()->id ?? 0 }}, '{{ $user->trashed() ? '0' : '1' }}')" 
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors" 
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        onclick="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->nickname ?? $user->name ?? 'Usuario') }}', {{ $user->trashed() ? 'true' : 'false' }})" 
                                                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white {{ $user->trashed() ? 'bg-red-500 hover:bg-red-600 focus:ring-red-500' : 'bg-green-500 hover:bg-green-600 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors" 
                                                        title="{{ $user->trashed() ? 'Activar' : 'Inactivar' }}">
                                                    <i class="fas {{ $user->trashed() ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                </button>
                                                <form method="POST" action="{{ route('infrastock.admin.users.destroy', $user->id) }}" style="display: inline;" id="delete-form-{{ $user->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->nickname ?? $user->name ?? 'Usuario') }}')" 
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mensaje cuando no hay resultados -->
                    <div id="noResultsMessage" class="text-center py-8" style="display: none;">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron resultados</h3>
                        <p class="text-gray-500">No hay usuarios que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación de Usuario -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="handleOutsideClick($event, 'create')" class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto p-6 max-h-screen overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Usuario</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.admin.users.store') }}">
                        @csrf
                        
                        <!-- Información Personal -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-user mr-2 text-blue-600"></i>Información Personal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="create_first_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre *</label>
                                    <input type="text" name="first_name" id="create_first_name" value="{{ old('first_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('first_name', 'create') border-red-500 @enderror" required>
                                    @error('first_name', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_first_last_name" class="block text-gray-700 text-sm font-bold mb-2">Primer Apellido *</label>
                                    <input type="text" name="first_last_name" id="create_first_last_name" value="{{ old('first_last_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('first_last_name', 'create') border-red-500 @enderror" required>
                                    @error('first_last_name', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_second_last_name" class="block text-gray-700 text-sm font-bold mb-2">Segundo Apellido</label>
                                    <input type="text" name="second_last_name" id="create_second_last_name" value="{{ old('second_last_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('second_last_name', 'create') border-red-500 @enderror">
                                    @error('second_last_name', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_document_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Documento *</label>
                                    <select name="document_type" id="create_document_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('document_type', 'create') border-red-500 @enderror" required>
                                        <option value="">Seleccione el tipo</option>
                                        <option value="1" {{ old('document_type') == '1' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                        <option value="2" {{ old('document_type') == '2' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                        <option value="3" {{ old('document_type') == '3' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                        <option value="4" {{ old('document_type') == '4' ? 'selected' : '' }}>Pasaporte</option>
                                    </select>
                                    @error('document_type', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_document_number" class="block text-gray-700 text-sm font-bold mb-2">Número de Documento *</label>
                                    <input type="text" name="document_number" id="create_document_number" value="{{ old('document_number') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('document_number', 'create') border-red-500 @enderror" required>
                                    @error('document_number', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_phone" class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                                    <input type="text" name="phone" id="create_phone" value="{{ old('phone') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('phone', 'create') border-red-500 @enderror">
                                    @error('phone', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico *</label>
                                    <input type="email" name="email" id="create_email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email', 'create') border-red-500 @enderror" required>
                                    @error('email', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="create_address" class="block text-gray-700 text-sm font-bold mb-2">Dirección</label>
                                    <textarea name="address" id="create_address" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('address', 'create') border-red-500 @enderror">{{ old('address') }}</textarea>
                                    @error('address', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración del Usuario -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-cog mr-2 text-green-600"></i>Configuración del Usuario
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="create_role_id" class="block text-gray-700 text-sm font-bold mb-2">Rol *</label>
                                    <select name="role_id" id="create_role_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role_id', 'create') border-red-500 @enderror" required>
                                        <option value="">Seleccione un rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_is_active" class="block text-gray-700 text-sm font-bold mb-2">Estado *</label>
                                    <select name="is_active" id="create_is_active" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('is_active', 'create') border-red-500 @enderror" required>
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('is_active', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-lock mr-2 text-red-600"></i>Contraseña
                            </h4>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong> Si no especifica una contraseña, se generará automáticamente usando las primeras letras del nombre, apellido y los últimos 4 dígitos del documento.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="create_password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                                    <input type="password" name="password" autocomplete="new-password" id="create_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password', 'create') border-red-500 @enderror" placeholder="••••••••">
                                    @error('password', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="create_password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password" id="create_password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password_confirmation', 'create') border-red-500 @enderror" placeholder="••••••••">
                                    @error('password_confirmation', 'create')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Usuario -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="handleOutsideClick($event, 'edit')" class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto p-6 max-h-screen overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Usuario</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.admin.users.update', '') }}/${currentUser.id}`">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" x-model="currentUser.id">
                        
                        <!-- Información Personal -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-user mr-2 text-blue-600"></i>Información Personal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_first_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre *</label>
                                    <input type="text" name="first_name" id="edit_first_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('first_name', 'edit') border-red-500 @enderror" x-model="currentUser.first_name" required>
                                    @error('first_name', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_first_last_name" class="block text-gray-700 text-sm font-bold mb-2">Primer Apellido *</label>
                                    <input type="text" name="first_last_name" id="edit_first_last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('first_last_name', 'edit') border-red-500 @enderror" x-model="currentUser.first_last_name" required>
                                    @error('first_last_name', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_second_last_name" class="block text-gray-700 text-sm font-bold mb-2">Segundo Apellido</label>
                                    <input type="text" name="second_last_name" id="edit_second_last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('second_last_name', 'edit') border-red-500 @enderror" x-model="currentUser.second_last_name">
                                    @error('second_last_name', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_document_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Documento *</label>
                                    <select name="document_type" id="edit_document_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('document_type', 'edit') border-red-500 @enderror" x-model="currentUser.document_type" required>
                                        <option value="">Seleccione el tipo</option>
                                        <option value="1">Cédula de Ciudadanía</option>
                                        <option value="2">Tarjeta de Identidad</option>
                                        <option value="3">Cédula de Extranjería</option>
                                        <option value="4">Pasaporte</option>
                                    </select>
                                    @error('document_type', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_document_number" class="block text-gray-700 text-sm font-bold mb-2">Número de Documento *</label>
                                    <input type="text" name="document_number" id="edit_document_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('document_number', 'edit') border-red-500 @enderror" x-model="currentUser.document_number" required>
                                    @error('document_number', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_phone" class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                                    <input type="text" name="phone" id="edit_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('phone', 'edit') border-red-500 @enderror" x-model="currentUser.phone">
                                    @error('phone', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico *</label>
                                    <input type="email" name="email" id="edit_email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email', 'edit') border-red-500 @enderror" x-model="currentUser.email" required>
                                    @error('email', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="edit_address" class="block text-gray-700 text-sm font-bold mb-2">Dirección</label>
                                    <textarea name="address" id="edit_address" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('address', 'edit') border-red-500 @enderror" x-model="currentUser.address"></textarea>
                                    @error('address', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración del Usuario -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-cog mr-2 text-green-600"></i>Configuración del Usuario
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_role_id" class="block text-gray-700 text-sm font-bold mb-2">Rol *</label>
                                    <select name="role_id" id="edit_role_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role_id', 'edit') border-red-500 @enderror" x-model="currentUser.role_id" required>
                                        <option value="">Seleccione un rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_is_active" class="block text-gray-700 text-sm font-bold mb-2">Estado *</label>
                                    <select name="is_active" id="edit_is_active" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('is_active', 'edit') border-red-500 @enderror" x-model="currentUser.is_active" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                    @error('is_active', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-lock mr-2 text-red-600"></i>Contraseña
                            </h4>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong> Si no especifica una contraseña, se generará automáticamente usando las primeras letras del nombre, apellido y los últimos 4 dígitos del documento.
                                </p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                                    <input type="password" name="password" autocomplete="new-password" id="edit_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password', 'edit') border-red-500 @enderror" placeholder="••••••••">
                                    @error('password', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password" id="edit_password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password_confirmation', 'edit') border-red-500 @enderror" placeholder="••••••••">
                                    @error('password_confirmation', 'edit')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('alpine:init', () => {
    const oldData = @json(old());
    const hasCreateErrors = {{ $errors->hasBag('create') ? 'true' : 'false' }};
    const hasEditErrors = {{ $errors->hasBag('edit') ? 'true' : 'false' }};
    const hasSessionError = {{ session('error') ? 'true' : 'false' }};
    
    // Si falló por base de datos (Ej: duplicado) no hay validacion de form, pero viejo input existe
    const isEditFail = hasEditErrors || (hasSessionError && oldData && oldData._method === 'PUT');
    const isCreateFail = hasCreateErrors || (hasSessionError && (!oldData || oldData._method !== 'PUT'));

    Alpine.data('userManagement', () => ({
        isCreateModalOpen: isCreateFail,
        isEditModalOpen: isEditFail,
        currentUser: { 
            id: isEditFail ? (oldData.user_id || null) : null, 
            first_name: isEditFail ? (oldData.first_name || '') : '', 
            first_last_name: isEditFail ? (oldData.first_last_name || '') : '', 
            second_last_name: isEditFail ? (oldData.second_last_name || '') : '', 
            document_type: isEditFail ? (oldData.document_type || '') : '', 
            document_number: isEditFail ? (oldData.document_number || '') : '', 
            phone: isEditFail ? (oldData.phone || '') : '', 
            email: isEditFail ? (oldData.email || '') : '', 
            address: isEditFail ? (oldData.address || '') : '', 
            role_id: isEditFail ? (oldData.role_id || '') : '', 
            is_active: isEditFail ? (oldData.is_active || '1') : '1', 
            password: '', 
            password_confirmation: '' 
        },
        validationErrors: {},
        createForm: { 
            first_name: '', 
            first_last_name: '', 
            second_last_name: '', 
            document_type: '', 
            document_number: '', 
            phone: '', 
            email: '', 
            address: '', 
            role_id: '', 
            is_active: '1', 
            password: '', 
            password_confirmation: '' 
        },

        init() {
            @if(session('error'))
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Aviso',
                        text: '{!! addslashes(session('error')) !!}',
                        confirmButtonText: 'Entendido'
                    });
                }, 100);
            @endif
        },

        openCreateModal() {
            this.isCreateModalOpen = true;
            this.resetCreateForm();
        },

        openEditModal(id, first_name, first_last_name, second_last_name, document_type, document_number, phone, email, address, role_id, is_active) {
            this.isEditModalOpen = true;
            this.currentUser = { id: id, first_name: first_name, first_last_name: first_last_name, second_last_name: second_last_name, document_type: document_type, document_number: document_number, phone: phone, email: email, address: address, role_id: role_id, is_active: is_active, password: '', password_confirmation: '' };
        },

        handleOutsideClick(event, type) {
            // No cerrar si el clic fue en un elemento de SweetAlert2 o si un SweetAlert2 está abierto
            if (event.target.closest('.swal2-container') || document.body.classList.contains('swal2-shown')) {
                return;
            }
            if (type === 'create') {
                this.isCreateModalOpen = false;
                this.resetCreateForm();
            } else if (type === 'edit') {
                this.isEditModalOpen = false;
            }
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
        },

        resetCreateForm() {
            this.createForm = { first_name: '', first_last_name: '', second_last_name: '', document_type: '', document_number: '', phone: '', email: '', address: '', role_id: '', is_active: '1', password: '', password_confirmation: '' };
        }
    }));
});

// Función para activar/inactivar usuario
function toggleUserStatus(userId, userName, isCurrentlyInactive) {
    const action = isCurrentlyInactive ? 'activar' : 'inactivar';
    const icon = isCurrentlyInactive ? 'question' : 'warning';
    const confirmColor = isCurrentlyInactive ? '#10B981' : '#F97316';
    const confirmText = isCurrentlyInactive ? 'Sí, activar' : 'Sí, inactivar';

    Swal.fire({
        title: `¿${isCurrentlyInactive ? 'Activar' : 'Inactivar'} usuario?`,
        text: `¿Estás seguro de que deseas ${action} al usuario "${userName}"?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            fetch(`/infrastock/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo cambiar el estado del usuario.',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Por favor intenta nuevamente.',
                    confirmButtonText: 'Entendido'
                });
            });
        }
    });
}

// Función para confirmar eliminación con SweetAlert2
function confirmDeleteUser(userId, userName) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            document.getElementById('delete-form-' + userId).submit();
        }
    });
}

// Verificar si hay mensajes de sesión
document.addEventListener('DOMContentLoaded', function() {
    @if(session('user_deleted'))
        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El usuario ha sido eliminado correctamente.',
            showConfirmButton: false,
            timer: 1500
        });
    @endif
    
    @if(session('error') && session('error') !== 'Ya existe un usuario con estos datos. Por favor, verifica la información.')
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonText: 'Entendido'
        });
    @endif
    
    // Configurar filtro automático
    setupAutoFilter();
});

// Búsqueda server-side con debounce
function setupAutoFilter() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            serverSearch(this.value);
        }, 500);
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer);
            serverSearch(this.value);
        }
    });
}

function serverSearch(term) {
    const url = new URL(window.location.href);
    if (term && term.trim() !== '') {
        url.searchParams.set('search', term.trim());
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function clearSearch() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endsection