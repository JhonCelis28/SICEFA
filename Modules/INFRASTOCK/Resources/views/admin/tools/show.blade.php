@extends('infrastock::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalles de la Herramienta</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('cefa.infrastock.admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('infrastock.admin.tools.index') }}">Herramientas</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información de la Herramienta: {{ $tool->inventory_id }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $tool->id }}</dd>

                        @if($tool->imagen)
                        <dt class="col-sm-3">Imagen:</dt>
                        <dd class="col-sm-9">
                            <img src="{{ asset('storage/' . $tool->imagen) }}" alt="{{ $tool->nombre }}" class="img-thumbnail" style="max-width: 200px;">
                        </dd>
                        @endif

                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9">{{ $tool->nombre ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Placa:</dt>
                        <dd class="col-sm-9">{{ $tool->placa ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">{{ $tool->descripcion ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Marca:</dt>
                        <dd class="col-sm-9">{{ $tool->marca ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Modelo:</dt>
                        <dd class="col-sm-9">{{ $tool->modelo ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Estado:</dt>
                        <dd class="col-sm-9">
                            @php
                                $estadoLabels = [
                                    'disponible' => 'Disponible',
                                    'en_prestamo' => 'En Préstamo',
                                    'mantenimiento' => 'Mantenimiento',
                                    'no_disponible' => 'No Disponible'
                                ];
                                $estado = $tool->estado ?? 'disponible';
                            @endphp
                            <span class="badge badge-{{ $estado == 'disponible' ? 'success' : ($estado == 'en_prestamo' ? 'warning' : ($estado == 'mantenimiento' ? 'info' : 'danger')) }}">
                                {{ $estadoLabels[$estado] ?? 'N/A' }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Cantidad Total:</dt>
                        <dd class="col-sm-9">{{ $tool->cantidad_total ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Cantidad Disponible:</dt>
                        <dd class="col-sm-9">{{ $tool->cantidad_disponible ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Fecha de Adquisición:</dt>
                        <dd class="col-sm-9">{{ $tool->fecha_adquisicion ? \Carbon\Carbon::parse($tool->fecha_adquisicion)->format('d/m/Y') : 'N/A' }}</dd>

                        <dt class="col-sm-3">Fecha Mantenimiento:</dt>
                        <dd class="col-sm-9">{{ $tool->fecha_mantenimiento ? \Carbon\Carbon::parse($tool->fecha_mantenimiento)->format('d/m/Y') : 'N/A' }}</dd>

                        <dt class="col-sm-3">Próximo Mantenimiento:</dt>
                        <dd class="col-sm-9">{{ $tool->proximo_mantenimiento ? \Carbon\Carbon::parse($tool->proximo_mantenimiento)->format('d/m/Y') : 'N/A' }}</dd>

                        <dt class="col-sm-3">Descripción Actual:</dt>
                        <dd class="col-sm-9">{{ $tool->descripcion_actual ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Atributos:</dt>
                        <dd class="col-sm-9">{{ $tool->atributos ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Descripción de Mantenimiento:</dt>
                        <dd class="col-sm-9">{{ $tool->descripcion_mantenimiento ?? 'N/A' }}</dd>

                        @if($tool->inventory_id)
                        <dt class="col-sm-3">Inventario:</dt>
                        <dd class="col-sm-9">ID: {{ $tool->inventory->id ?? 'N/A' }} - {{ $tool->inventory->description ?? 'N/A' }}</dd>
                        @endif

                        @if($tool->labor_id)
                        <dt class="col-sm-3">Labor:</dt>
                        <dd class="col-sm-9">{{ $tool->labor->description ?? 'N/A' }}</dd>
                        @endif

                        @if($tool->amount)
                        <dt class="col-sm-3">Cantidad:</dt>
                        <dd class="col-sm-9">{{ $tool->amount }}</dd>
                        @endif

                        @if($tool->price)
                        <dt class="col-sm-3">Precio:</dt>
                        <dd class="col-sm-9">${{ number_format($tool->price, 2) }}</dd>
                        @endif

                        @if($tool->category_id)
                        <dt class="col-sm-3">Categoría:</dt>
                        <dd class="col-sm-9">{{ $tool->category->name ?? 'N/A' }}</dd>
                        @endif

                        <dt class="col-sm-3">Creado en:</dt>
                        <dd class="col-sm-9">{{ $tool->created_at }}</dd>

                        <dt class="col-sm-3">Actualizado en:</dt>
                        <dd class="col-sm-9">{{ $tool->updated_at }}</dd>
                    </dl>
                    <a href="{{ route('infrastock.admin.tools.edit', $tool->id) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('infrastock.admin.tools.index') }}" class="btn btn-secondary">Volver al Listado</a>
                </div>
            </div>
        </div>
    </div>
@endsection
