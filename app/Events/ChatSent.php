<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $target_user_id;
    public $user_id;

    public function __construct($message, $user_id, $target_user_id)
    {
        $this->user_id = $user_id;
        $this->message = $message;
        $this->target_user_id = $target_user_id;
    }

    public function broadcastOn()
    {
        return [$this->user_id . '-' . $this->target_user_id];
    }

    public function broadcastAs()
    {
        return 'chat.sent';
    }
}
