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
                    <h3 class="card-title">Información del Insumo: {{ $supply->inventory_id }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $supply->id }}</dd>

                        <dt class="col-sm-3">Inventario:</dt>
                        <dd class="col-sm-9">ID: {{ $supply->inventory->id }} - {{ $supply->inventory->description ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9">{{ $supply->name }}</dd>

                        <dt class="col-sm-3">Labor:</dt>
                        <dd class="col-sm-9">{{ $supply->labor->description ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Cantidad:</dt>
                        <dd class="col-sm-9">{{ $supply->amount }}</dd>

                        <dt class="col-sm-3">Precio:</dt>
                        <dd class="col-sm-9">{{ $supply->price }}</dd>

                        <dt class="col-sm-3">Categoría:</dt>
                        <dd class="col-sm-9">{{ $supply->category->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Creado en:</dt>
                        <dd class="col-sm-9">{{ $supply->created_at }}</dd>

                        <dt class="col-sm-3">Actualizado en:</dt>
                        <dd class="col-sm-9">{{ $supply->updated_at }}</dd>
                    </dl>
                    <a href="{{ route('infrastock.admin.supplies.edit', $supply->id) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('infrastock.admin.supplies.index') }}" class="btn btn-secondary">Volver al Listado</a>
                </div>
            </div>
        </div>
    </div>
@endsection
