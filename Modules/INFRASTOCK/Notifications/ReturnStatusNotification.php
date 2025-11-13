<?php

namespace Modules\INFRASTOCK\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\INFRASTOCK\Entities\WarehouseMovement;

class ReturnStatusNotification extends Notification
{
    use Queueable;

    protected $returnMovement;
    protected $status;
    protected $title;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(WarehouseMovement $returnMovement, $status, $title, $message)
    {
        $this->returnMovement = $returnMovement;
        $this->status = $status;
        $this->title = $title;
        $this->message = $message;
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
        return [
            'type' => 'return_' . $this->status,
            'title' => $this->title,
            'message' => $this->message,
            'return_id' => $this->returnMovement->id,
            'status' => $this->status,
        ];
    }
}

