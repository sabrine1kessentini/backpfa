<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmploiUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $groupe;
    public $message;

    public function __construct($groupe)
    {
        $this->groupe = $groupe;
        $this->message = "Votre emploi du temps a été mis à jour";
    }

    public function broadcastOn()
    {
        return new Channel('emploi.'.$this->groupe);
    }

    public function broadcastAs()
    {
        return 'emploi.updated';
    }
}