<?php

namespace Modules\INFRASTOCK\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\INFRASTOCK\Entities\Request;

class NewSupplyRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $userName;
    public $roleName;
    public $totalItems;
    public $equipmentList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request, $userName, $roleName, $totalItems, $equipmentList)
    {
        $this->request = $request;
        $this->userName = $userName;
        $this->roleName = $roleName;
        $this->totalItems = $totalItems;
        $this->equipmentList = $equipmentList;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nueva Solicitud de Insumos - INFRASTOCK')
                    ->view('infrastock::emails.new-supply-request')
                    ->with([
                        'request' => $this->request,
                        'userName' => $this->userName,
                        'roleName' => $this->roleName,
                        'totalItems' => $this->totalItems,
                        'equipmentList' => $this->equipmentList,
                    ]);
    }
}

