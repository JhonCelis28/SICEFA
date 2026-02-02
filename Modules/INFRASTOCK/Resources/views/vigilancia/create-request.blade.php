<!--
    * @file create-request.blade.php
    * @brief Vista para crear una nueva solicitud de insumo para el Vigilancia.
    *
    * Esta vista presenta un formulario completo para que el Vigilancia pueda
    * solicitar múltiples insumos necesarios para sus labores. Incluye validación del lado del
    * cliente y del servidor, así como una interfaz intuitiva para seleccionar insumos
    * y especificar cantidades y detalles.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $equipments Lista de insumos disponibles para solicitar.
    * @param Collection $productiveUnitWarehouses Lista de unidades productivas/almacenes disponibles.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.usuarios-master')

@section('title', 'Nueva Solicitud - Vigilancia INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="{{ route('infrastock.vigilancia.requests.index') }}" class="text-green-600 hover:text-green-800">Mis Solicitudes</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Nueva Solicitud</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Nueva Solicitud de Insumos</h2>
            <p class="text-gray-600 mt-2">Seleccione los insumos necesarios, especifique la unidad productiva y agregue una descripción de la solicitud.</p>
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
            <form action="{{ route('infrastock.vigilancia.requests.store') }}" method="POST" id="requestForm">
                @csrf
                
                <!-- Sección de Insumos -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Insumos a Solicitar</h3>
                        <button type="button" id="add-equipment-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Agregar Insumo
                        </button>
                    </div>

                    <!-- Lista de Insumos Seleccionados -->
                    <div id="equipment-list" class="space-y-4">
                        <!-- Los insumos se agregarán dinámicamente aquí -->
                    </div>

                    <!-- Mensaje cuando no hay insumos -->
                    <div id="no-equipment-message" class="text-center py-8 bg-gray-50 rounded-lg">
                        <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No hay insumos seleccionados</h4>
                        <p class="text-gray-500 mb-4">Haga clic en "Agregar Insumo" para comenzar a seleccionar los insumos necesarios.</p>
                    </div>
                    </div>

                    <!-- Unidad Productiva/Almacén -->
                <div class="mb-8">
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
                <div class="mb-8">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción/Justificación de la Solicitud
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                              placeholder="Explique brevemente por qué necesita estos insumos y para qué los utilizará..."></textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('infrastock.vigilancia.requests.index') }}" 
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

        <!-- Modal para seleccionar insumo -->
        <div id="equipment-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Seleccionar Insumo</h3>
                    <button type="button" id="close-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($equipments as $equipment)
                            <div class="equipment-card border border-gray-200 rounded-lg p-4 hover:border-green-500 hover:shadow-md transition-all duration-200 cursor-pointer" 
                                 data-equipment-id="{{ $equipment->id }}"
                                 data-equipment-name="{{ $equipment->name }}"
                                 data-equipment-stock="{{ $equipment->stock }}"
                                 data-equipment-category="{{ $equipment->category->name ?? 'Sin categoría' }}"
                                 data-equipment-description="{{ $equipment->description ?? '' }}"
                                 data-equipment-unit="{{ $equipment->unit ?? 'unidades' }}"
                                 data-equipment-price="{{ $equipment->price ?? 0 }}">
                                
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $equipment->name }}</h4>
                                    <div class="flex items-center space-x-2">
                                        @if($equipment->stock <= 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Agotado
                                            </span>
                                        @elseif($equipment->stock <= 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Poco Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Disponible
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p><strong>Categoría:</strong> {{ $equipment->category->name ?? 'Sin categoría' }}</p>
                                    <p><strong>Stock:</strong> {{ $equipment->stock }} {{ $equipment->unit ?? 'unidades' }}</p>
                                    @if($equipment->description)
                                        <p><strong>Descripción:</strong> {{ Str::limit($equipment->description, 50) }}</p>
                                    @endif
                                    @if($equipment->price)
                                        <p><strong>Precio:</strong> ${{ number_format($equipment->price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addEquipmentBtn = document.getElementById('add-equipment-btn');
            const equipmentModal = document.getElementById('equipment-modal');
            const closeModalBtn = document.getElementById('close-modal');
            const equipmentList = document.getElementById('equipment-list');
            const noEquipmentMessage = document.getElementById('no-equipment-message');
            const equipmentCards = document.querySelectorAll('.equipment-card');
            
            let equipmentCounter = 0;
            let selectedEquipments = new Set();

            // Mostrar modal
            addEquipmentBtn.addEventListener('click', function() {
                equipmentModal.classList.remove('hidden');
                updateEquipmentCards();
            });

            // Cerrar modal
            closeModalBtn.addEventListener('click', function() {
                equipmentModal.classList.add('hidden');
            });

            // Cerrar modal al hacer clic fuera
            equipmentModal.addEventListener('click', function(e) {
                if (e.target === equipmentModal) {
                    equipmentModal.classList.add('hidden');
                }
            });

            // Seleccionar insumo
            equipmentCards.forEach(card => {
                card.addEventListener('click', function() {
                    const equipmentId = this.dataset.equipmentId;
                    const equipmentStock = parseInt(this.dataset.equipmentStock);
                    
                    // Verificar si el insumo está agotado
                    if (equipmentStock <= 0) {
                        alert('Este insumo está agotado y no se puede seleccionar.');
                        return;
                    }
                    
                    if (selectedEquipments.has(equipmentId)) {
                        alert('Este insumo ya ha sido seleccionado.');
                        return;
                    }

                    addEquipmentToList(this);
                    equipmentModal.classList.add('hidden');
                });
            });

            function addEquipmentToList(card) {
                const equipmentId = card.dataset.equipmentId;
                const equipmentName = card.dataset.equipmentName;
                const equipmentStock = parseInt(card.dataset.equipmentStock);
                const equipmentCategory = card.dataset.equipmentCategory;
                const equipmentDescription = card.dataset.equipmentDescription;
                const equipmentUnit = card.dataset.equipmentUnit;
                const equipmentPrice = parseFloat(card.dataset.equipmentPrice);

                selectedEquipments.add(equipmentId);
                equipmentCounter++;

                const equipmentItem = document.createElement('div');
                equipmentItem.className = 'equipment-item bg-gray-50 border border-gray-200 rounded-lg p-4';
                equipmentItem.innerHTML = `
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">${equipmentName}</h4>
                            <p class="text-sm text-gray-600">${equipmentCategory}</p>
                        </div>
                        <button type="button" class="remove-equipment text-red-500 hover:text-red-700" data-equipment-id="${equipmentId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" 
                                   name="equipments[${equipmentId}][amount]" 
                                   min="1" 
                                   max="${equipmentStock}"
                                   value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Máximo: ${equipmentStock} ${equipmentUnit}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Información</label>
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Stock:</strong> ${equipmentStock} ${equipmentUnit}</p>
                                ${equipmentDescription ? `<p><strong>Descripción:</strong> ${equipmentDescription}</p>` : ''}
                                ${equipmentPrice > 0 ? `<p><strong>Precio:</strong> $${equipmentPrice.toLocaleString()}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;

                equipmentList.appendChild(equipmentItem);
                noEquipmentMessage.style.display = 'none';

                // Agregar evento para remover insumo
                const removeBtn = equipmentItem.querySelector('.remove-equipment');
                removeBtn.addEventListener('click', function() {
                    const id = this.dataset.equipmentId;
                    selectedEquipments.delete(id);
                    equipmentItem.remove();
                    
                    if (selectedEquipments.size === 0) {
                        noEquipmentMessage.style.display = 'block';
                    }
                    
                    updateEquipmentCards();
                });

                updateEquipmentCards();
            }

            function updateEquipmentCards() {
                equipmentCards.forEach(card => {
                    const equipmentId = card.dataset.equipmentId;
                    const equipmentStock = parseInt(card.dataset.equipmentStock);
                    
                    if (selectedEquipments.has(equipmentId)) {
                        card.classList.add('opacity-50', 'cursor-not-allowed');
                        card.style.pointerEvents = 'none';
                    } else if (equipmentStock <= 0) {
                        card.classList.add('opacity-60', 'cursor-not-allowed');
                        card.style.pointerEvents = 'none';
                        card.style.backgroundColor = '#fef2f2';
                    } else {
                        card.classList.remove('opacity-50', 'opacity-60', 'cursor-not-allowed');
                        card.style.pointerEvents = 'auto';
                        card.style.backgroundColor = '';
                    }
                });
            }

            // Validar formulario antes de enviar
            document.getElementById('requestForm').addEventListener('submit', function(e) {
                if (selectedEquipments.size === 0) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos un insumo para la solicitud.');
                    return false;
                }

                // Validar cantidades
                const amountInputs = document.querySelectorAll('input[name*="[amount]"]');
                let hasErrors = false;

                amountInputs.forEach(input => {
                    const maxAmount = parseInt(input.max);
                    const currentAmount = parseInt(input.value);
                
                    if (currentAmount > maxAmount) {
                        input.setCustomValidity(`La cantidad no puede ser mayor a ${maxAmount}`);
                        hasErrors = true;
                    } else if (currentAmount < 1) {
                        input.setCustomValidity('La cantidad debe ser al menos 1');
                        hasErrors = true;
                    } else {
                        input.setCustomValidity('');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Por favor, corrija los errores en las cantidades antes de enviar.');
                    return false;
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
                document.getElementById('productive_unit_warehouse_id').value = '';
                document.getElementById('description').value = '';
                equipmentList.innerHTML = '';
                selectedEquipments.clear();
                equipmentCounter = 0;
                noEquipmentMessage.style.display = 'block';
                updateEquipmentCards();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            @endif
        });
    </script>
@endsection





