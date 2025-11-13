<?php

namespace Modules\INFRASTOCK\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\INFRASTOCK\Entities\Surplus;
use Modules\INFRASTOCK\Entities\WarehouseMovement;
use App\Models\User;

class ReturnNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $surplus;
    public $returnMovement;
    public $admin;

    /**
     * Create a new message instance.
     */
    public function __construct(Surplus $surplus, WarehouseMovement $returnMovement, User $admin)
    {
        $this->surplus = $surplus;
        $this->returnMovement = $returnMovement;
        $this->admin = $admin;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $equipmentName = $this->returnMovement->equipment ? $this->returnMovement->equipment->name : 'Insumo';
        $userName = $this->returnMovement->user ? ($this->returnMovement->user->nickname ?? $this->returnMovement->user->name) : 'Usuario';

        return $this->subject('Nueva Devolución de Insumos - INFRASTOCK')
                    ->view('infrastock::emails.return-notification', [
                        'surplus' => $this->surplus,
                        'returnMovement' => $this->returnMovement,
                        'equipmentName' => $equipmentName,
                        'userName' => $userName,
                        'admin' => $this->admin,
                    ]);
    }
}

