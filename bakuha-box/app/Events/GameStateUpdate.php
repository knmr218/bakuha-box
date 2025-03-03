<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStateUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room, $game, $selectPlayer, $msg, $turn_msg, $box, $player_info, $turn_num;

    /**
     * Create a new event instance.
     */
    public function __construct($room,$game,$selectPlayer,$msg,$turn_msg,$box,$player_info,$turn_num)
    {
        $this->room = $room;
        $this->game = $game;
        $this->selectPlayer = $selectPlayer;
        $this->msg = $msg;
        $this->turn_msg = $turn_msg;
        $this->box = $box;
        $this->player_info = $player_info;
        $this->turn_num = $turn_num;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['room.' . $this->room->id];
    }

    public function broadcastAs()
    {
        return 'GameStateUpdate';
    }

    public function broadcastWith()
    {
        return [
            'box' => $this->game->box,
            'phase' => $this->game->phase,
            'selectPlayer' => $this->selectPlayer,
            'msg' => $this->msg,
            'turn_msg' => $this->turn_msg,
            'box' => $this->box,
            'player_info' => $this->player_info,
            'turn_num' => $this->turn_num,
        ];
    }
}
