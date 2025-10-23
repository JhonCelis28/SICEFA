<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surplus extends Model
{
    protected $fillable = [
        'equipment_id',
        'user_id',
        'surplus_amount',
        'reason',
        'surplus_date',
    ];

    protected $casts = [
        'surplus_date' => 'date',
    ];

    /**
     * Relación con el equipo/insumo
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Relación con el usuario que registró el sobrante
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('surplus_date', [$startDate, $endDate]);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por equipo
     */
    public function scopeByEquipment($query, $equipmentId)
    {
        return $query->where('equipment_id', $equipmentId);
    }
}
