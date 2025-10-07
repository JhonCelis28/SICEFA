<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Warehouse
 * @brief Modelo Eloquent para la tabla `warehouses` del módulo INFRASTOCK.
 *
 * Representa un almacén o bodega dentro del sistema. Contiene información
 * como el nombre, descripción y la aplicación a la que está asociado.
 * Utiliza SoftDeletes para un borrado lógico.
 */
class Warehouse extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'warehouses';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'name',
        'description',
        'app_id',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    // Si existen otras relaciones (ej. con App), se pueden añadir aquí con sus respectivos docblocks.
}
