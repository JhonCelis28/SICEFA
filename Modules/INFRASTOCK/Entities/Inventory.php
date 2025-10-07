<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Inventory
 * @brief Modelo Eloquent para la tabla `inventories` del módulo INFRASTOCK.
 *
 * Representa un registro de inventario para un elemento específico. Contiene detalles
 * sobre la persona asociada, la ubicación (unidad productiva/almacén), el elemento en sí,
 * descripción, cantidades, fechas de producción y vencimiento, estado, marca y código de inventario.
 * Utiliza SoftDeletes para un borrado lógico.
 */
class Inventory extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'inventories';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'person_id',
        'productive_unit_warehouse_id',
        'element_id',
        'destination',
        'description',
        'price',
        'amount',
        'stock',
        'production_date',
        'lot_number',
        'expiration_date',
        'state',
        'mark',
        'inventory_code',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    // Si existen otras relaciones, se pueden añadir aquí con sus respectivos docblocks.
}
