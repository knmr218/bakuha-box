<?php

namespace App\Http\Controllers;

use App\Events\GameStart;
use App\Events\PlayerMatched;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function searchRoom(Request $request) {
        $playerId = $request->session()->get('player_id');

        // 部屋の検索
        $room = Room::where('status',1)->first();
        if ($room) { // 待ち状態の部屋を優先して
            $room->update([
                'player_2' => $playerId,
                'status' => 2
            ]);

            event(new PlayerMatched($room));
            return response()->json(['roomId' => $room->id, 'matched' => true]);
        } else if (Room::where('status',0)->exists()) { // 部屋に空きがあれば割り当てて
            $room = Room::where('status',0)->first();
            $room->update([
                'player_1' => $playerId,
                'status' => 1
            ]);
            return response()->json(['roomId' => $room->id, 'matched' => false]);
        } else {
            // 空きがない場合は待機orエラーで返す
        }
    }
}
