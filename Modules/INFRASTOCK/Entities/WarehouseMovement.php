<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class WarehouseMovement
 * @brief Modelo Eloquent para la tabla `warehouse_movements` del módulo INFRASTOCK.
 *
 * Representa un movimiento de inventario, que puede ser una solicitud de insumo,
 * un préstamo de herramienta o una devolución. Registra el tipo de elemento,
 * la cantidad, la descripción y las relaciones con el usuario, la unidad
 * productiva/almacén y el elemento específico (insumo o herramienta).
 * Utiliza SoftDeletes para un borrado lógico.
 */
class WarehouseMovement extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'warehouse_movements';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'productive_unit_warehouse_id',
        'movement_id',
        'equipment_id',
        'role',
        'user_id',
        'item_type',
        'amount',
        'status',
        'surplus_id',
        'description',
        'imagen',
        'purpose',
        'required_date',
        'return_date',
        'delivery_image',
        'return_image',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at', 'required_date', 'return_date'];

    /**
     * Define la relación de pertenencia a una unidad productiva/almacén.
     * Un movimiento de almacén pertenece a una `ProductiveUnitWarehouse`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productiveUnitWarehouse()
    {
        return $this->belongsTo(ProductiveUnitWarehouse::class, 'productive_unit_warehouse_id');
    }

    /**
     * Define la relación de pertenencia a un usuario.
     * Un movimiento de almacén está asociado a un `User` del sistema principal.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Define la relación de pertenencia a un equipo/insumo.
     * Este método solo se aplica si `item_type` es 'equipment'.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /**
     * Define la relación de pertenencia a una herramienta.
     * Este método solo se aplica si `item_type` es 'tool'.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tool()
    {
        return $this->belongsTo(Tool::class, 'movement_id');
    }

    /**
     * Define la relación de pertenencia a un sobrante.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function surplus()
    {
        return $this->belongsTo(Surplus::class, 'surplus_id');
    }
}
