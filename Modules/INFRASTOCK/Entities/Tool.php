<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Tool
 * @brief Modelo Eloquent para la tabla `tools` del módulo INFRASTOCK.
 *
 * Representa una herramienta dentro del sistema de inventario. Incluye información
 * como la cantidad, precio y relaciones con su categoría, la labor a la que está
 * asociada y el inventario al que pertenece. Utiliza SoftDeletes para un borrado lógico.
 */
class Tool extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'tools';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'inventory_id',
        'labor_id',
        'amount',
        'price',
        'category_id',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    /**
     * Define la relación de pertenencia a una categoría.
     * Una herramienta pertenece a una `InfrastockCategory`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(InfrastockCategory::class, 'category_id');
    }

    /**
     * Define la relación de pertenencia a una labor.
     * Una herramienta está asociada a una `Labor`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }

    /**
     * Define la relación de pertenencia a un inventario.
     * Una herramienta pertenece a un `Inventory`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
