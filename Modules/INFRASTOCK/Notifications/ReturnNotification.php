<?php

namespace Modules\INFRASTOCK\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\WarehouseMovement;

class ReturnNotification extends Notification
{
    use Queueable;

    protected $surplus;
    protected $returnMovement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Surplus $surplus, WarehouseMovement $returnMovement)
    {
        $this->surplus = $surplus;
        $this->returnMovement = $returnMovement;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        $equipmentName = $this->returnMovement->equipment ? $this->returnMovement->equipment->name : 'Insumo';
        $userName = $this->returnMovement->user ? ($this->returnMovement->user->nickname ?? $this->returnMovement->user->name) : 'Usuario';

        return [
            'type' => 'return_created',
            'title' => 'Nueva Devolución de Insumos',
            'message' => "El usuario {$userName} ha registrado una devolución de {$this->returnMovement->amount} {$this->returnMovement->equipment->unit ?? 'unidades'} de {$equipmentName}.",
            'return_id' => $this->returnMovement->id,
            'surplus_id' => $this->surplus->id,
            'equipment_name' => $equipmentName,
            'amount' => $this->returnMovement->amount,
            'user_name' => $userName,
        ];
    }
}

