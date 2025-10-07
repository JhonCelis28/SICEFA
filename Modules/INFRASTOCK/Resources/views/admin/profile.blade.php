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
        <form action="#" method="POST">
            @csrf {{-- Token CSRF para protección contra ataques. --}}
            @method('PUT') {{-- Método HTTP PUT para la actualización de recursos. --}}

            <!-- Campo para el Nombre del usuario -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ Auth::user()->name ?? '' }}">
            </div>

            <!-- Campo para el Nickname del usuario -->
            <div class="mb-4">
                <label for="nickname" class="block text-gray-700 text-sm font-bold mb-2">Nickname:</label>
                <input type="text" name="nickname" id="nickname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ Auth::user()->nickname ?? '' }}">
            </div>

            <!-- Campo para el Correo Electrónico del usuario -->
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ Auth::user()->email ?? '' }}">
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
