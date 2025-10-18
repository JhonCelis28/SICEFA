<!--
    * @file create-request.blade.php
    * @brief Vista para crear una nueva solicitud de insumo para el Personal de Aseo.
    *
    * Esta vista presenta un formulario completo para que el personal de aseo pueda
    * solicitar insumos necesarios para sus labores. Incluye validación del lado del
    * cliente y del servidor, así como una interfaz intuitiva para seleccionar insumos
    * y especificar cantidades y detalles.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $equipments Lista de insumos disponibles para solicitar.
    * @param Collection $productiveUnitWarehouses Lista de unidades productivas/almacenes disponibles.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Nueva Solicitud - Personal de Aseo INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="text-green-600 hover:text-green-800">Mis Solicitudes</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Nueva Solicitud</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Nueva Solicitud de Insumo</h2>
            <p class="text-gray-600 mt-2">Complete el formulario para solicitar los insumos necesarios para sus labores de aseo.</p>
        </div>

        <!-- Mensaje de Éxito -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensaje de Error -->
        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-md p-8">
            <form action="{{ route('infrastock.cleaning-staff.requests.store') }}" method="POST" id="requestForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Selección de Insumo -->
                    <div class="lg:col-span-2">
                        <label for="movement_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Insumo a Solicitar <span class="text-red-500">*</span>
                        </label>
                        <select name="movement_id" id="movement_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            <option value="">Seleccione un insumo</option>
                            @foreach($equipments as $equipment)
                                <option value="{{ $equipment->id }}" 
                                        data-stock="{{ $equipment->stock }}"
                                        data-category="{{ $equipment->category->name ?? 'Sin categoría' }}"
                                        data-description="{{ $equipment->description ?? '' }}">
                                    {{ $equipment->name }} - Stock: {{ $equipment->stock }} {{ $equipment->unit ?? 'unidades' }}
                                </option>
                            @endforeach
                        </select>
                        @error('movement_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información del Insumo Seleccionado -->
                    <div class="lg:col-span-2" id="equipment-info" style="display: none;">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-blue-900 mb-2">Información del Insumo</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm text-blue-700"><strong>Categoría:</strong> <span id="equipment-category"></span></p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700"><strong>Stock Disponible:</strong> <span id="equipment-stock"></span></p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700"><strong>Descripción:</strong> <span id="equipment-description"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               min="1" 
                               max="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                               placeholder="Ingrese la cantidad"
                               required>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1" id="amount-help">Seleccione primero un insumo</p>
                    </div>

                    <!-- Unidad Productiva/Almacén -->
                    <div>
                        <label for="productive_unit_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Unidad Productiva/Almacén <span class="text-red-500">*</span>
                        </label>
                        <select name="productive_unit_warehouse_id" id="productive_unit_warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            <option value="">Seleccione una unidad</option>
                            @foreach($productiveUnitWarehouses as $puw)
                                <option value="{{ $puw->id }}">
                                    {{ $puw->productiveUnit->name ?? 'Sin nombre' }} - {{ $puw->warehouse->name ?? 'Sin nombre' }}
                                </option>
                            @endforeach
                        </select>
                        @error('productive_unit_warehouse_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción/Justificación -->
                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción/Justificación de la Solicitud
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                  placeholder="Explique brevemente por qué necesita este insumo y para qué lo utilizará..."></textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Máximo 500 caracteres</p>
                    </div>

                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('infrastock.cleaning-staff.dashboard') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Enviar Solicitud
                    </button>
                </div>

            </form>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentSelect = document.getElementById('movement_id');
            const amountInput = document.getElementById('amount');
            const amountHelp = document.getElementById('amount-help');
            const equipmentInfo = document.getElementById('equipment-info');

            // Manejar cambio de insumo seleccionado
            equipmentSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    const stock = parseInt(selectedOption.dataset.stock);
                    const category = selectedOption.dataset.category;
                    const description = selectedOption.dataset.description;
                    
                    // Mostrar información del insumo
                    document.getElementById('equipment-category').textContent = category;
                    document.getElementById('equipment-stock').textContent = stock;
                    document.getElementById('equipment-description').textContent = description || 'Sin descripción';
                    equipmentInfo.style.display = 'block';
                    
                    // Configurar cantidad máxima
                    amountInput.max = stock;
                    amountInput.value = '';
                    amountHelp.textContent = `Máximo ${stock} unidades disponibles`;
                } else {
                    equipmentInfo.style.display = 'none';
                    amountInput.max = 1;
                    amountInput.value = '';
                    amountHelp.textContent = 'Seleccione primero un insumo';
                }
            });

            // Validar cantidad en tiempo real
            amountInput.addEventListener('input', function() {
                const maxAmount = parseInt(this.max);
                const currentAmount = parseInt(this.value);
                
                if (currentAmount > maxAmount) {
                    this.setCustomValidity(`La cantidad no puede ser mayor a ${maxAmount}`);
                } else if (currentAmount < 1) {
                    this.setCustomValidity('La cantidad debe ser al menos 1');
                } else {
                    this.setCustomValidity('');
                }
            });

            // Validar longitud de descripción
            const descriptionTextarea = document.getElementById('description');
            descriptionTextarea.addEventListener('input', function() {
                const maxLength = 500;
                const currentLength = this.value.length;
                
                if (currentLength > maxLength) {
                    this.setCustomValidity(`La descripción no puede tener más de ${maxLength} caracteres`);
                } else {
                    this.setCustomValidity('');
                }
            });

            // Limpiar formulario después de una solicitud exitosa
            @if(session('success'))
                // Limpiar todos los campos del formulario
                document.getElementById('movement_id').value = '';
                document.getElementById('amount').value = '';
                document.getElementById('productive_unit_warehouse_id').value = '';
                document.getElementById('description').value = '';
                
                // Ocultar información del equipo
                const equipmentInfo = document.getElementById('equipmentInfo');
                if (equipmentInfo) {
                    equipmentInfo.style.display = 'none';
                }
                
                // Restablecer ayuda de cantidad
                const amountHelp = document.getElementById('amountHelp');
                if (amountHelp) {
                    amountHelp.textContent = 'Seleccione primero un insumo';
                }
                
                // Scroll hacia arriba para mostrar el mensaje de éxito
                window.scrollTo({ top: 0, behavior: 'smooth' });
            @endif
        });
    </script>
@endsection

@section('script')
<script>
    // Script específico para el formulario de nueva solicitud
    console.log('Formulario de Nueva Solicitud cargado correctamente');
</script>
@endsection
