<!--
    * @file profile.blade.php
    * @brief Vista para la edición del perfil de usuario en el panel administrativo del módulo INFRASTOCK.
    *
    * Esta vista Blade proporciona un formulario simple para que el usuario autenticado pueda editar
    * su información de perfil, como nombre, nickname y correo electrónico. Extiende la plantilla
    * `master.blade.php` y utiliza Tailwind CSS para la estilización del formulario.
    *
    * @param Illuminate\Support\Facades\Auth::user() El usuario autenticado, para precargar los datos del formulario.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Editar Perfil')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Perfil de Usuario</h2>

        <!-- Formulario para la edición del perfil de usuario -->
        <form action="{{ route('cefa.infrastock.admin.profile.update') }}" method="POST">
            @csrf {{-- Token CSRF para protección contra ataques. --}}
            @method('PUT') {{-- Método HTTP PUT para la actualización de recursos. --}}

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <!-- Campo para el Nickname del usuario -->
            <div class="mb-4">
                <label for="nickname" class="block text-gray-700 text-sm font-bold mb-2">Nickname:</label>
                <input type="text" name="nickname" id="nickname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('nickname', Auth::user()->nickname ?? '') }}">
            </div>

            <!-- Campo para el Correo Electrónico del usuario -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('email', Auth::user()->email ?? '') }}" required>
            </div>

            <!-- Sección de Cambio de Contraseña (Opcional) -->
            <div class="mb-6 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Cambiar Contraseña (Opcional)</h3>
                <p class="text-sm text-gray-600 mb-4">Deja estos campos vacíos si no deseas cambiar tu contraseña.</p>
                
                <!-- Campo para la Nueva Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Nueva Contraseña:</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Mínimo 8 caracteres">
                    <p class="text-xs text-gray-500 mt-1">Deja vacío si no deseas cambiar la contraseña</p>
                </div>

                <!-- Campo para Confirmar Contraseña -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nueva Contraseña:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Repite la nueva contraseña">
                </div>
            </div>

            <!-- Botones de acción: Guardar Cambios y Cancelar -->
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors duration-200">
                    Guardar Cambios
                </button>
                <a href="{{ route('cefa.infrastock.admin.dashboard') }}" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
