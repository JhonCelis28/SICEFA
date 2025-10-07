@extends('infrastock::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalles de la Categoría</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('cefa.infrastock.admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('infrastock.admin.categories.index') }}">Categorías</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información de la Categoría: {{ $category->name }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $category->id }}</dd>

                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9">{{ $category->name }}</dd>

                        <dt class="col-sm-3">Tipo:</dt>
                        <dd class="col-sm-9">{{ $category->type }}</dd>

                        <dt class="col-sm-3">Creado en:</dt>
                        <dd class="col-sm-9">{{ $category->created_at }}</dd>

                        <dt class="col-sm-3">Actualizado en:</dt>
                        <dd class="col-sm-9">{{ $category->updated_at }}</dd>
                    </dl>
                    <a href="{{ route('infrastock.admin.categories.edit', $category->id) }}" class="btn btn-warning">Editar</a>
                    <a href="{{ route('infrastock.admin.categories.index') }}" class="btn btn-secondary">Volver al Listado</a>
                </div>
            </div>
        </div>
    </div>
@endsection
