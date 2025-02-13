<?php

namespace App\Http\Controllers;

use App\Events\GameEnd;
use App\Events\GameStart;
use App\Events\GameStateUpdate;
use App\Events\GameForceEnd;
use App\Models\Game;
use App\Models\History;
use App\Models\Player;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GameController extends Controller
{
    public function initGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player1 = Player::find($room->player_1);
        $player2 = Player::find($room->player_2);

        // 先攻後攻決め
        $random = rand(0,1);
        if ($random === 1) {
            $player1->update([
                'cur_turn' => 1,
                'first' => 1
            ]);

            $player2->update([
                'cur_turn' => 0,
                'first' => 0
            ]);
        } else {
            $player1->update([
                'cur_turn' => 0,
                'first' => 0
            ]);

            $player2->update([
                'cur_turn' => 1,
                'first' => 1
            ]);
        }
        

        // マスを初期化
        $game = Game::find($room->game_id);
        $game->update([
            'box' => '00000000'
        ]);

        $player = Player::find($playerId);
        event(new GameStart($room));
        return view('game', ['player' => $player, 'room' => $room, 'game' => $game]);
    }

    public function startGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player = Player::find($playerId);
        $game = Game::find($room->game_id);
        return view('game', ['player' => $player, 'room' => $room, 'game' => $game]);
    }

    public function game(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player1 = Player::find($room->player_1);
        $player2 = Player::find($room->player_2);
        $player = Player::find($playerId);
        $another_player = Player::find($room->player_1);
        if ($another_player->id == $playerId) {
            $another_player = Player::find($room->player_2);
        }
        $game = Game::find($room->game_id);
        $box = $game->box;
        $cur_point = $player->point;
        $cur_life = $player->life;
        $turn_num = $game->turn;
        $next_phase = 0;


        // リクエストで送信された箱index
        $select_num = $request->input('num');

        // 入力値の検証
        if ($box[$select_num] == "2") {
            return response()->json(['box' => $box, 'winner' => 0, 'Invalid' => true, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $game->phase]);
        }

        $my_msg = ""; // 相手プレイヤーに選択状況を知らせるメッセージ
        $another_msg = "";
        if ($game->phase == 0) {
            // 爆弾仕掛けフェーズ
            $box[$select_num] = "1";
            $next_phase = 1;
        } else {
            // 箱開けフェーズ
            if ($box[$select_num] == "0") {
                // 爆弾箱じゃない場合
                $cur_point += ($select_num + 1);
                $box[$select_num] = "2";

                $box = str_replace('1', '0', $box);

                $my_msg = "相手のプレイヤーは" . $select_num + 1 . "番の箱を開けて、" . $select_num + 1 . "ポイント取得した";
                $another_msg = "あなたは" . $select_num + 1 . "番の箱を開けて、" . $select_num + 1 . "ポイント取得した";
            } else {
                // 爆弾箱の場合
                $cur_point = 0;
                $cur_life--;

                $box[$select_num] = "0";

                $my_msg = "相手のプレイヤーは" . $select_num + 1 . "番の箱を開けて、爆弾を引いてしまった";
                $another_msg = "あなたは" . $select_num + 1 . "番の箱を開けて、爆弾を引いてしまった";
            }
        }

        // 勝敗判定
        if ($cur_point >= 18) {
            event(new GameEnd($room,$game,$player->id,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "あなたの勝ちです", $another_player->id => "あなたの負けです"], $player->id));
            return response()->json(['box' => $box, 'winner' => 1, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
        }

        if ($cur_life <= 0) {
            event(new GameEnd($room,$game,$another_player->id,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "あなたの負けです", $another_player->id => "あなたの勝ちです"], $player->id));
            return response()->json(['box' => $box, 'winner' => 2, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
        }

        // 10ターン or 空き箱が一つ
        if ($turn_num >= 10 || (substr_count($box, "0") <= 1 && substr_count($box, "1") <= 0)) {
            if ($cur_point > $another_player->point) {
                event(new GameEnd($room,$game,$player->id,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "あなたの勝ちです", $another_player->id => "あなたの負けです"], $player->id));
                return response()->json(['box' => $box, 'winner' => 1, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
            } else if ($cur_point < $another_player->point) {
                event(new GameEnd($room,$game,$another_player->id,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "あなたの負けです", $another_player->id => "あなたの勝ちです"], $player->id));
                return response()->json(['box' => $box, 'winner' => 2, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
            } else {
                // 引き分け
                event(new GameEnd($room,$game,null,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "引き分けです", $another_player->id => "引き分けです"], $player->id));
                return response()->json(['box' => $box, 'winner' => 3, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
            }
        }

        $turn_num++;
        if ($game->phase == 0) {
            $game->update([
                'box' => $box,
                'phase' => 1
            ]);
        } else {
            $game->update([
                'box' => $box,
                'turn' => $turn_num,
                'phase' => 0
            ]);

            // ターンを変更
            $player->update([
                'cur_turn' => 0
            ]);
            $another_player->update([
                'cur_turn' => 1
            ]);

            
        }

        // ポイントとライフをDBに保存
        $player->update([
            'point' => $cur_point,
            'life' => $cur_life
        ]);
        
        if ($game->phase == 0) {
            event(new GameStateUpdate($room, $game, $player->id,[$player->id => $another_msg, $another_player->id => $my_msg],[$player->id => "爆弾を仕掛ける箱を選択してください", $another_player->id => "相手が爆弾を仕掛けています"], $game->box));
        } else {
            event(new GameStateUpdate($room, $game, $another_player->id,[$player->id => $my_msg, $another_player->id => $another_msg],[$player->id => "相手が箱を開けています", $another_player->id => "開く箱を選択してください"],null));
        }
        return response()->json(['box' => $box, 'winner' => 0, 'Invalid' => false, 'point' => $cur_point, 'life' => $cur_life, 'phase' => $next_phase]);
    }


    public function reset($playerId) {
        $player = Player::find($playerId);
        $player->update([
            'cur_turn' => null,
            'first' => null,
            'point' => 0,
            'life' => 2,
        ]);
        try {
            $room = Room::where('player_1', '=', $playerId)
                        ->orWhere('player_2', '=', $playerId)
                        ->first();
            $game = Game::find($room->game_id);
            $game->update([
                'box' => "00000000",
                'status' => 0,
                'turn' => 1,
                'phase' => 0
            ]);
            $room->update([
                'status' => 0,
                'player_1' => null,
                'player_2' => null
            ]);
        } catch (\Exception $e){
            // 
        }
        
    }
    
    public function endGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $this->reset($playerId);
        return redirect('/bakuha');
    }

    public function onemoreGame(Request $request) {
        $playerId = $request->session()->get('player_id');
        $this->reset($playerId);
        return redirect('/bakuha/room/search');
    }

    public function forceEnd(Request $request) {
        $playerId = $request->session()->get('player_id');
        $room = Room::where('player_1', '=', $playerId)
                    ->orWhere('player_2', '=', $playerId)
                    ->first();
        $player1 = Player::find($room->player_1);
        $player2 = Player::find($room->player_2);
        $player = Player::find($playerId);
        $another_player = Player::find($room->player_1);
        if ($another_player->id == $playerId) {
            $another_player = Player::find($room->player_2);
        }
        event(new GameForceEnd($room, [$player->id => "あなたの負けです", $another_player->id => "あなたの勝ちです"]));
        return response()->json();
    }
}
