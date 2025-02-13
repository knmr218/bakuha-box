<?php

namespace App\Http\Controllers;

use App\Events\GameStart;
use App\Events\PlayerMatched;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function searchRoom(Request $request) {
        $playerId = $request->session()->get('player_id');

        // 部屋の空きがない場合
        if (!Room::where('status',0)->exists() and !Room::where('status',1)->exists()) {
            // 最も最終更新日時が古い部屋を取得
            $oldest_room = Room::orderBy('updated_at', 'asc')->first();
            if ($oldest_room->updated_at < Carbon::now()->subHour()) { // 最終更新日時が1時間以上前かどうか
                $oldest_room->update([
                    'status' => 0,
                    'player_1' => null,
                    'player_2' => null
                ]);
            } else {
                return response()->json(['roomId' => null, 'matched' => false]);
            }
        }

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
        }
    }
}
