<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Equipment
 * @brief Modelo Eloquent para la tabla `equipments` del módulo INFRASTOCK.
 *
 * Representa un insumo o equipo dentro del sistema de inventario. Incluye información
 * como la cantidad, precio, fecha de vencimiento y relaciones con su categoría,
 * la labor a la que está asociado y el inventario al que pertenece.
 * Utiliza SoftDeletes para un borrado lógico.
 */
class Equipment extends Model
{
    use SoftDeletes;

    /**
     * @property string $table Nombre de la tabla asociada con el modelo.
     */
    protected $table = 'equipments';

    /**
     * @property array $fillable Atributos que son asignables masivamente.
     */
    protected $fillable = [
        'labor_id',
        'inventory_id',
        'name',
        'amount',
        'initial_amount',
        'price',
        'category_id',
        'expiration_date',
    ];

    /**
     * @property array $dates Atributos que deben ser mutados a instancias de Carbon.
     */
    protected $dates = ['deleted_at'];

    /**
     * Define la relación de pertenencia a una categoría.
     * Un equipo pertenece a una `InfrastockCategory`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(InfrastockCategory::class, 'category_id');
    }

    /**
     * Define la relación de pertenencia a una labor.
     * Un equipo está asociado a una `Labor`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function labor()
    {
        return $this->belongsTo(Labor::class, 'labor_id');
    }

    /**
     * Define la relación de pertenencia a un inventario.
     * Un equipo pertenece a un `Inventory`.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Calcula el stock disponible basado en la cantidad inicial menos las solicitudes aprobadas.
     * @return int
     */
    public function getStockAttribute()
    {
        // Si no hay cantidad inicial definida o es 0, usar la cantidad actual
        $initialAmount = ($this->initial_amount && $this->initial_amount > 0) ? $this->initial_amount : $this->amount;
        
        // Calcular las solicitudes aprobadas y entregadas para este equipo
        $consumedAmount = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('equipment_id', $this->id)
            ->where('item_type', 'equipment')
            ->whereIn('role', ['Entrega', 'Recibe'])
            ->sum('amount');
            
        return max(0, $initialAmount - $consumedAmount);
    }

    /**
     * Verifica si hay stock suficiente para una cantidad específica.
     * @param int $requestedAmount
     * @return bool
     */
    public function hasStockFor($requestedAmount)
    {
        return $this->stock >= $requestedAmount;
    }
}
