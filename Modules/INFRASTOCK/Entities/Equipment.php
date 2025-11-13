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
    protected $dates = ['deleted_at', 'expiration_date'];

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
     * Calcula la cantidad utilizada basada en los movimientos de almacén aprobados.
     * @return int
     */
    public function getUsedAmountAttribute()
    {
        // Calcular las solicitudes aprobadas y entregadas para este equipo
        // Excluir registros eliminados (soft deletes) y solo contar registros activos
        // 'Entrega' suma al usado, 'Recibe' resta del usado (porque es material que vuelve)
        $entregas = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('equipment_id', $this->id)
            ->where('item_type', 'equipment')
            ->where('role', 'Entrega')
            ->withoutTrashed() // Excluir registros eliminados
            ->sum('amount') ?: 0;
        
        $recibes = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('equipment_id', $this->id)
            ->where('item_type', 'equipment')
            ->where('role', 'Recibe')
            ->withoutTrashed() // Excluir registros eliminados
            ->sum('amount') ?: 0;
        
        // El usado es lo entregado menos lo recibido de vuelta
        return max(0, $entregas - $recibes);
    }

    /**
     * Obtiene la cantidad inicial del insumo.
     * Si no hay cantidad inicial definida, usa la cantidad actual.
     * @return int
     */
    public function getInitialAmountAttribute()
    {
        // Si hay cantidad inicial definida y es mayor a 0, usarla
        // Acceder directamente a los atributos sin usar el accessor para evitar recursión
        $initialAmount = isset($this->attributes['initial_amount']) ? $this->attributes['initial_amount'] : null;
        if ($initialAmount !== null && $initialAmount > 0) {
            return (int) $initialAmount;
        }
        // Si no, usar la cantidad actual como inicial
        $amount = isset($this->attributes['amount']) ? $this->attributes['amount'] : 0;
        return (int) $amount;
    }

    /**
     * Calcula el stock disponible basado en la cantidad inicial menos las solicitudes aprobadas.
     * @return int
     */
    public function getStockAttribute()
    {
        $initialAmount = $this->initial_amount;
        $consumedAmount = $this->used_amount;
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
