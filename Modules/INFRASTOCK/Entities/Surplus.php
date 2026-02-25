<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surplus extends Model
{
    protected $fillable = [
        'equipment_id',
        'user_id',
        'request_id',
        'request_item_id',
        'surplus_amount',
        'reason',
        'description',
        'surplus_date',
        'status',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'surplus_date' => 'date',
        'processed_at' => 'datetime',
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
     * Relación con la solicitud
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Relación con el item de la solicitud
     */
    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(RequestItem::class);
    }

    /**
     * Verificar si el sobrante está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si el sobrante está aprobado
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si el sobrante está rechazado
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
