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
        'characteristics',
        'amount',
        'initial_amount',
        'minimum_stock',
        'unit_measure',
        'price',
        'category_id',
        'expiration_date',
        'observations',
        'status',
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

    /**
     * Calcula automáticamente el estado del insumo basado en:
     * - Vencido: si expiration_date < hoy (prioridad máxima)
     * - Agotado: si stock = 0
     * - Crítico: si stock = mínimo o máximo 1 unidad por encima (stock <= mínimo + 1)
     * - Bajo Stock: si stock > mínimo pero diferencia de 1 a 10 unidades (mínimo + 1 < stock <= mínimo + 10)
     * - Disponible: si stock > mínimo + 10 unidades
     * 
     * @return string
     */
    public function calculateStatus()
    {
        // Verificar si está vencido - usar atributos directamente para evitar recursión
        $expirationDate = isset($this->attributes['expiration_date']) ? $this->attributes['expiration_date'] : null;
        if ($expirationDate) {
            try {
                $expDate = \Carbon\Carbon::parse($expirationDate);
                if ($expDate->isPast()) {
                    return 'vencido';
                }
            } catch (\Exception $e) {
                // Si hay error al parsear la fecha, continuar con otras validaciones
            }
        }

        // Obtener el stock usando el accessor, pero accediendo directamente a los atributos
        // para evitar recursión infinita
        $initialAmount = isset($this->attributes['initial_amount']) ? (int)$this->attributes['initial_amount'] : (isset($this->attributes['amount']) ? (int)$this->attributes['amount'] : 0);
        
        // Obtener el valor mínimo permitido
        $minimumStock = isset($this->attributes['minimum_stock']) ? (int)$this->attributes['minimum_stock'] : 0;
        
        // Calcular stock usado directamente desde la BD (igual que en getUsedAmountAttribute)
        // Usar $this->id si está disponible, sino usar $this->attributes['id']
        $equipmentId = $this->id ?? (isset($this->attributes['id']) ? $this->attributes['id'] : null);
        $usedAmount = 0;
        if ($equipmentId) {
            $entregas = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('equipment_id', $equipmentId)
                ->where('item_type', 'equipment')
                ->where('role', 'Entrega')
                ->withoutTrashed()
                ->sum('amount') ?: 0;
            
            $recibes = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('equipment_id', $equipmentId)
                ->where('item_type', 'equipment')
                ->where('role', 'Recibe')
                ->withoutTrashed()
                ->sum('amount') ?: 0;
            
            $usedAmount = max(0, $entregas - $recibes);
        }
        
        // Calcular stock actual (cantidad restante)
        $stock = max(0, $initialAmount - $usedAmount);

        // Verificar estados en orden de prioridad (de más crítico a menos crítico)
        
        // 1. Agotado: si stock = 0
        if ($stock <= 0) {
            return 'agotado';
        }

        // 2. Crítico: si stock = mínimo o máximo 1 unidad por encima (stock <= mínimo + 1)
        if ($stock <= ($minimumStock + 1)) {
            return 'critico';
        }

        // 3. Bajo Stock: si stock > mínimo pero diferencia de 1 a 10 unidades
        // (mínimo + 1 < stock <= mínimo + 10)
        if ($stock <= ($minimumStock + 10)) {
            return 'bajo_stock';
        }

        // 4. Disponible: si stock > mínimo + 10 unidades
        return 'disponible';
    }

    /**
     * Accessor para obtener el estado calculado automáticamente.
     * Siempre calcula el estado basándose en los datos actuales para asegurar precisión.
     * 
     * @return string
     */
    public function getStatusAttribute($value)
    {
        // Siempre recalcular el estado para asegurar que esté actualizado
        // basándose en el stock actual, fecha de vencimiento, etc.
        return $this->calculateStatus();
    }

    /**
     * Boot method para actualizar automáticamente el status antes de guardar.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($equipment) {
            // Calcular y actualizar el status antes de guardar
            $equipment->status = $equipment->calculateStatus();
        });
    }

    /**
     * Obtiene el color del badge según el estado.
     * 
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'disponible' => 'bg-green-100 text-green-800',
            'agotado' => 'bg-red-100 text-red-800',
            'vencido' => 'bg-orange-100 text-orange-800',
            'bajo_stock' => 'bg-yellow-100 text-yellow-800',
            'critico' => 'bg-red-200 text-red-900',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Obtiene el texto legible del estado.
     * 
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'disponible' => 'Disponible',
            'agotado' => 'Agotado',
            'vencido' => 'Vencido',
            'bajo_stock' => 'Bajo Stock',
            'critico' => 'Crítico',
            default => 'Desconocido',
        };
    }
    /**
     * Define la relación con los movimientos de almacén.
     */
    public function warehouseMovements()
    {
        return $this->hasMany(WarehouseMovement::class, 'equipment_id');
    }
}
