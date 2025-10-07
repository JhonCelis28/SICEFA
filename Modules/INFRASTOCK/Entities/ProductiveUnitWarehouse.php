<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class ProductiveUnitWarehouse
 * @brief Modelo Eloquent para la tabla `productive_unit_warehouses` del módulo INFRASTOCK.
 *
 * Representa la relación entre una unidad productiva (`ProductiveUnit`)
 * y un almacén (`Warehouse`). Esta tabla sirve para especificar qué almacenes
 * están asociados a qué unidades productivas. Utiliza SoftDeletes para un borrado lógico.
 */
class ProductiveUnitWarehouse extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'productive_unit_warehouses';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'productive_unit_id',
        'warehouse_id',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    /**
     * Define la relación de pertenencia a una unidad productiva.
     * Una relación ProductiveUnitWarehouse pertenece a una `ProductiveUnit`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productiveUnit()
    {
        return $this->belongsTo(ProductiveUnit::class, 'productive_unit_id');
    }

    /**
     * Define la relación de pertenencia a un almacén.
     * Una relación ProductiveUnitWarehouse pertenece a un `Warehouse`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
