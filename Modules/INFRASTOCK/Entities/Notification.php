<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $table = 'notifications';
    
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($notification) {
            if (empty($notification->id)) {
                $notification->id = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    /**
     * Relación polimórfica con el modelo notificable (User)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Marcar la notificación como leída
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Verificar si la notificación está leída
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Crear notificación para solicitud creada
     */
    public static function createRequestCreatedNotification($userId, $equipmentName, $amount, $requestId)
    {
        return self::create([
            'type' => 'request_created',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => [
                'title' => 'Nueva Solicitud de Insumo',
                'message' => "Se ha creado una nueva solicitud de {$equipmentName} ({$amount} unidades)",
                'equipment_name' => $equipmentName,
                'amount' => $amount,
                'request_id' => $requestId,
                'action_url' => route('infrastock.admin.supply-requests.index'),
            ],
        ]);
    }

    /**
     * Crear notificación para solicitud aprobada
     */
    public static function createRequestApprovedNotification($userId, $equipmentName, $amount, $requestId)
    {
        return self::create([
            'type' => 'request_approved',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => [
                'title' => 'Solicitud Aprobada',
                'message' => "Tu solicitud de {$equipmentName} ({$amount} unidades) ha sido aprobada",
                'equipment_name' => $equipmentName,
                'amount' => $amount,
                'request_id' => $requestId,
                'action_url' => route('infrastock.cleaning-staff.requests.index'),
            ],
        ]);
    }

    /**
     * Crear notificación para solicitud rechazada
     */
    public static function createRequestRejectedNotification($userId, $equipmentName, $amount, $requestId)
    {
        return self::create([
            'type' => 'request_rejected',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $userId,
            'data' => [
                'title' => 'Solicitud Rechazada',
                'message' => "Tu solicitud de {$equipmentName} ({$amount} unidades) ha sido rechazada",
                'equipment_name' => $equipmentName,
                'amount' => $amount,
                'request_id' => $requestId,
                'action_url' => route('infrastock.cleaning-staff.requests.index'),
            ],
        ]);
    }
}
