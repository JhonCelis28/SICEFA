@extends('infrastock::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalles del Insumo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('cefa.infrastock.admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('infrastock.admin.supplies.index') }}">Insumos</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Insumo: {{ $supply->name }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $supply->id }}</dd>

                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9"><strong>{{ $supply->name }}</strong></dd>

                        <dt class="col-sm-3">Categoría:</dt>
                        <dd class="col-sm-9">
                            @php
                                $categoryName = strtolower($supply->category->name ?? '');
                                $badgeClass = 'px-2 py-1 text-xs font-semibold rounded-full ';
                                if (strpos($categoryName, 'ferretería') !== false || strpos($categoryName, 'ferreteria') !== false) {
                                    $badgeClass .= 'bg-orange-100 text-orange-600';
                                } elseif (strpos($categoryName, 'aseo') !== false) {
                                    $badgeClass .= 'bg-sky-100 text-sky-600';
                                } else {
                                    $badgeClass .= 'bg-purple-100 text-purple-800';
                                }
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $supply->category->name ?? 'N/A' }}</span>
                        </dd>

                        <dt class="col-sm-3">Características:</dt>
                        <dd class="col-sm-9">{{ $supply->characteristics ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Cantidad Inicial:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $supply->initial_amount }}</span>
                        </dd>

                        <dt class="col-sm-3">Consumos:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-warning">{{ $supply->used_amount }}</span>
                        </dd>

                        <dt class="col-sm-3">Cantidad Restante:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-success">{{ $supply->stock }}</span>
                        </dd>

                        <dt class="col-sm-3">Unidad de Medida:</dt>
                        <dd class="col-sm-9">{{ $supply->unit_measure ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Fecha de Vencimiento:</dt>
                        <dd class="col-sm-9">
                            @if($supply->expiration_date)
                                @php
                                    $daysUntilExpiration = now()->diffInDays($supply->expiration_date, false);
                                    $isExpired = $supply->expiration_date->isPast();
                                    $isExpiringSoon = !$isExpired && $daysUntilExpiration <= 30;
                                @endphp
                                <span class="badge {{ $isExpired ? 'badge-danger' : ($isExpiringSoon ? 'badge-warning' : 'badge-success') }}">
                                    {{ $supply->expiration_date->format('d/m/Y') }}
                                </span>
                                @if($isExpired)
                                    <span class="text-danger ml-2">(Vencido)</span>
                                @elseif($isExpiringSoon)
                                    <span class="text-warning ml-2">(Por vencer en {{ $daysUntilExpiration }} días)</span>
                                @endif
                            @else
                                <span class="badge badge-secondary">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Observaciones:</dt>
                        <dd class="col-sm-9">{{ $supply->observations ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Creado en:</dt>
                        <dd class="col-sm-9">{{ $supply->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-3">Actualizado en:</dt>
                        <dd class="col-sm-9">{{ $supply->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <a href="{{ route('infrastock.admin.supplies.edit', $supply->id) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('infrastock.admin.supplies.index') }}" class="btn btn-secondary">Volver al Listado</a>
                </div>
            </div>
        </div>
    </div>
@endsection
