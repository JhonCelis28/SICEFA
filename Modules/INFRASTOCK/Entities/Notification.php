<?php

namespace Modules\INFRASTOCK\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $table = 'notifications';

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // =============================================
    // SISTEMA DE PRIORIDADES
    // =============================================

    /** 🔴 CRÍTICA: Vencimiento de insumos, mantenimiento de herramientas */
    const PRIORITY_CRITICAL = 'critical';
    /** 🟠 ALTA: Nuevas solicitudes, sobrantes, préstamos (requieren acción admin) */
    const PRIORITY_HIGH = 'high';
    /** 🟡 MEDIA: Rechazos (el usuario debe saber) */
    const PRIORITY_MEDIUM = 'medium';
    /** 🟢 BAJA: Aprobaciones, confirmaciones (informativas) */
    const PRIORITY_LOW = 'low';

    /**
     * Clasificación de tipos de notificación por prioridad.
     */
    const PRIORITY_MAP = [
        // 🔴 CRÍTICA — Escalamiento progresivo: 30d→15d→7d→diario
        'supply_expiring'    => self::PRIORITY_CRITICAL,
        'tool_maintenance'   => self::PRIORITY_CRITICAL,

        // 🟠 ALTA — Requieren acción del admin
        'request_created'    => self::PRIORITY_HIGH,
        'surplus_reported'   => self::PRIORITY_HIGH,
        'loan_created'       => self::PRIORITY_HIGH,
        'return_created'     => self::PRIORITY_HIGH,

        // 🟡 MEDIA — El usuario necesita saberlo
        'request_rejected'   => self::PRIORITY_MEDIUM,
        'loan_rejected'      => self::PRIORITY_MEDIUM,
        'return_rejected'    => self::PRIORITY_MEDIUM,

        // 🟢 BAJA — Informativas/confirmaciones
        'request_approved'   => self::PRIORITY_LOW,
        'loan_approved'      => self::PRIORITY_LOW,
        'return_approved'    => self::PRIORITY_LOW,
    ];

    /**
     * Retención máxima (días) para notificaciones NO LEÍDAS según prioridad.
     */
    const RETENTION_UNREAD = [
        self::PRIORITY_CRITICAL => 60,   // 2 meses (hasta que se resuelva idealmente)
        self::PRIORITY_HIGH     => 30,   // 1 mes
        self::PRIORITY_MEDIUM   => 15,   // 15 días
        self::PRIORITY_LOW      => 7,    // 1 semana
    ];

    /**
     * Retención (días) para notificaciones YA LEÍDAS según prioridad.
     */
    const RETENTION_READ = [
        self::PRIORITY_CRITICAL => 30,   // 1 mes después de leída
        self::PRIORITY_HIGH     => 15,   // 15 días después de leída
        self::PRIORITY_MEDIUM   => 7,    // 1 semana después de leída
        self::PRIORITY_LOW      => 3,    // 3 días después de leída
    ];

    /**
     * Frecuencia de re-notificación para tipo CRÍTICO (supply_expiring, tool_maintenance).
     * Escalamiento progresivo según días restantes al evento.
     * [días_restantes_max => intervalo_renotificación_días]
     */
    const ESCALATION_INTERVALS = [
        30 => 15,   // De 30 a 16 días: cada 15 días (una vez)
        15 => 7,    // De 15 a 8 días: cada 7 días (una vez)
        7  => 3,    // De 7 a 4 días: cada 3 días
        3  => 1,    // De 3 a 0 días: ¡diario!
    ];

    // =============================================
    // MÉTODOS DE PRIORIDAD
    // =============================================

    /**
     * Obtener la prioridad de esta notificación.
     */
    public function getPriority(): string
    {
        return self::PRIORITY_MAP[$this->type] ?? self::PRIORITY_LOW;
    }

    /**
     * Obtener la prioridad de un tipo de notificación.
     */
    public static function getPriorityForType(string $type): string
    {
        return self::PRIORITY_MAP[$type] ?? self::PRIORITY_LOW;
    }

    /**
     * Obtener la etiqueta de prioridad para mostrar en la UI.
     */
    public function getPriorityLabel(): string
    {
        return match ($this->getPriority()) {
            self::PRIORITY_CRITICAL => '🔴 Crítica',
            self::PRIORITY_HIGH     => '🟠 Alta',
            self::PRIORITY_MEDIUM   => '🟡 Media',
            self::PRIORITY_LOW      => '🟢 Baja',
            default                 => '⚪ Sin clasificar',
        };
    }

    /**
     * Obtener el color CSS de la prioridad.
     */
    public function getPriorityColor(): string
    {
        return match ($this->getPriority()) {
            self::PRIORITY_CRITICAL => 'red',
            self::PRIORITY_HIGH     => 'orange',
            self::PRIORITY_MEDIUM   => 'yellow',
            self::PRIORITY_LOW      => 'green',
            default                 => 'gray',
        };
    }

    /**
     * Determinar el intervalo de re-notificación según los días restantes al evento.
     * Se usa para notificaciones críticas con escalamiento progresivo.
     *
     * @param int $daysRemaining Días restantes hasta el evento (vencimiento/mantenimiento)
     * @return int Días de intervalo entre notificaciones
     */
    public static function getEscalationInterval(int $daysRemaining): int
    {
        foreach (self::ESCALATION_INTERVALS as $threshold => $interval) {
            if ($daysRemaining <= $threshold) {
                // Continuar buscando umbrales más bajos
            }
        }

        // Recorrer de menor a mayor umbral para obtener el más restrictivo
        $selectedInterval = 30; // default: mensual si está muy lejos
        foreach (self::ESCALATION_INTERVALS as $threshold => $interval) {
            if ($daysRemaining <= $threshold) {
                $selectedInterval = $interval;
            }
        }

        return $selectedInterval;
    }

    /**
     * Verificar si se debe crear una nueva notificación crítica según el escalamiento.
     *
     * @param int $daysRemaining Días restantes hasta el evento
     * @param \Illuminate\Support\Carbon|null $lastReadAt Fecha en que se leyó la última notificación
     * @return bool
     */
    public static function shouldRenotify(int $daysRemaining, $lastReadAt = null): bool
    {
        if (!$lastReadAt) {
            return true; // No hay notificación previa, crear una
        }

        $interval = self::getEscalationInterval($daysRemaining);
        $daysSinceRead = $lastReadAt->diffInDays(now());

        return $daysSinceRead >= $interval;
    }

    // =============================================
    // LIMPIEZA AUTOMÁTICA
    // =============================================

    /**
     * Eliminar notificaciones expiradas según su prioridad y estado de lectura.
     *
     * @return array Estadísticas de limpieza ['deleted' => N, 'by_priority' => [...]]
     */
    public static function cleanupExpired(): array
    {
        $stats = ['deleted' => 0, 'by_priority' => []];

        foreach (self::PRIORITY_MAP as $type => $priority) {
            $retentionRead = self::RETENTION_READ[$priority];
            $retentionUnread = self::RETENTION_UNREAD[$priority];

            // Eliminar notificaciones LEÍDAS que superaron su retención
            $deletedRead = self::where('type', $type)
                ->whereNotNull('read_at')
                ->where('read_at', '<', now()->subDays($retentionRead))
                ->delete();

            // Eliminar notificaciones NO LEÍDAS que superaron su retención máxima
            $deletedUnread = self::where('type', $type)
                ->whereNull('read_at')
                ->where('created_at', '<', now()->subDays($retentionUnread))
                ->delete();

            $total = $deletedRead + $deletedUnread;
            if ($total > 0) {
                $stats['by_priority'][$type] = [
                    'priority' => $priority,
                    'read_deleted' => $deletedRead,
                    'unread_deleted' => $deletedUnread,
                ];
            }
            $stats['deleted'] += $total;
        }

        return $stats;
    }

    /**
     * Obtener las notificaciones vigentes para un usuario, respetando prioridades.
     * Las de mayor prioridad se muestran primero, luego por fecha.
     *
     * @param int $userId
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveForUser(int $userId, ?int $limit = null)
    {
        $query = self::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->where(function ($q) {
                // Filtrar por retención según tipo y estado de lectura
                foreach (self::PRIORITY_MAP as $type => $priority) {
                    $q->orWhere(function ($subQ) use ($type, $priority) {
                        $subQ->where('type', $type)
                            ->where(function ($innerQ) use ($priority) {
                                // No leídas: dentro de retención de no leídas
                                $innerQ->where(function ($readQ) use ($priority) {
                                    $readQ->whereNull('read_at')
                                        ->where('created_at', '>=', now()->subDays(self::RETENTION_UNREAD[$priority]));
                                })
                                // Leídas: dentro de retención de leídas
                                ->orWhere(function ($unreadQ) use ($priority) {
                                    $unreadQ->whereNotNull('read_at')
                                        ->where('read_at', '>=', now()->subDays(self::RETENTION_READ[$priority]));
                                });
                            });
                    });
                }
            })
            ->orderByRaw("
                CASE 
                    WHEN read_at IS NULL THEN 0 
                    ELSE 1 
                END ASC
            ")
            ->orderByRaw("
                CASE type 
                    WHEN 'supply_expiring' THEN 1
                    WHEN 'tool_maintenance' THEN 1
                    WHEN 'request_created' THEN 2
                    WHEN 'surplus_reported' THEN 2
                    WHEN 'loan_created' THEN 2
                    WHEN 'return_created' THEN 2
                    WHEN 'request_rejected' THEN 3
                    WHEN 'loan_rejected' THEN 3
                    WHEN 'return_rejected' THEN 3
                    WHEN 'request_approved' THEN 4
                    WHEN 'loan_approved' THEN 4
                    WHEN 'return_approved' THEN 4
                    ELSE 5
                END ASC
            ")
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    // =============================================
    // RELACIONES Y MÉTODOS BASE
    // =============================================

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

    // =============================================
    // MÉTODOS ESTÁTICOS DE CREACIÓN
    // =============================================

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
