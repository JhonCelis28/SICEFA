@extends('infrastock::layouts.ciencias-basicas-master')

@section('title', 'Detalles de Solicitud - Ciencias Basicas INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="{{ route('infrastock.ciencias-basicas.requests.index') }}" class="text-green-600 hover:text-green-800">Mis Solicitudes</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Solicitud #{{ $request->id }}</li>
@endsection

<!-- Header -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Detalles de la Solicitud #{{ $request->id }}</h2>
            <p class="text-gray-600 mt-2">Información completa de la solicitud y sus insumos.</p>
        </div>
        <a href="{{ route('infrastock.ciencias-basicas.requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver
        </a>
    </div>
</div>

<!-- Información General -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Información General</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <div class="mt-1">
                @if($request->status == 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i> Pendiente
                    </span>
                @elseif($request->status == 'approved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i> Aprobada
                    </span>
                @elseif($request->status == 'delivered')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-truck mr-1"></i> Entregada
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1"></i> Rechazada
                    </span>
                @endif
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Fecha de Solicitud</label>
            <p class="mt-1 text-gray-900">{{ $request->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Unidad Productiva/Almacén</label>
            <p class="mt-1 text-gray-900">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} - {{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }}</p>
        </div>
    </div>
    
    @if($request->description)
    <div class="mt-4 pt-4 border-t border-gray-200">
        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción/Justificación</label>
        <p class="text-gray-900">{{ $request->description }}</p>
    </div>
    @endif
</div>

<!-- Lista de Insumos -->
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Insumos de la Solicitud</h3>
    
    @if($request->items->count() > 0)
        <div class="space-y-4">
            @foreach($request->items as $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $item->equipment->name ?? 'N/A' }}</h4>
                            <p class="text-sm text-gray-600">{{ $item->equipment->category->name ?? 'Sin categoría' }}</p>
                        </div>
                        <div>
                            @if($item->status == 'pending')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pendiente
                                </span>
                            @elseif($item->status == 'approved')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Aprobado
                                </span>
                            @elseif($item->status == 'delivered')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-truck mr-1"></i> Entregado
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Rechazado
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Cantidad Solicitada</label>
                            <p class="text-gray-900 font-medium">{{ $item->requested_amount }} {{ $item->equipment->unit ?? 'unidades' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Cantidad Aprobada</label>
                            <p class="text-gray-900 font-medium">{{ $item->approved_amount ?? 'N/A' }} {{ $item->equipment->unit ?? 'unidades' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Cantidad Entregada</label>
                            <p class="text-gray-900 font-medium">{{ $item->delivered_amount ?? 'N/A' }} {{ $item->equipment->unit ?? 'unidades' }}</p>
                        </div>
                    </div>
                    
                    @if($item->notes)
                    <div class="mt-3 p-2 bg-gray-50 rounded">
                        <p class="text-xs text-gray-600"><strong>Notas:</strong> {{ $item->notes }}</p>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
            <p class="text-gray-500">No hay insumos en esta solicitud.</p>
        </div>
    @endif
</div>
@endsection

