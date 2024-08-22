<?php

namespace App\Events;

use App\Models\AgendaDocs;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotaAdministradorActualizada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $agenda;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AgendaDocs $agenda)
    {
        $this->agenda = $agenda;
    }
}
