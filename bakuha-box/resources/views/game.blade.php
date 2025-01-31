<?php
$first = $player->first;
$playerId = $player->id;
$roomId = $room->id;
?>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>爆破BOX</title>
    <link rel="stylesheet" href="{{asset('css/game.css')}}">
</head>
<body>
    <h1 id="game_title">爆破BOX</h1>

    <h2 id="game_msg"></h2>
    <button type="button" name="Box1" id="btn1" class="btn">Box1</button>
    <button type="button" name="Box2" id="btn2" class="btn">Box2</button>
    <button type="button" name="Box3" id="btn3" class="btn">Box3</button>
    <button type="button" name="Box4" id="btn4" class="btn">Box4</button>
    <button type="button" name="Box5" id="btn5" class="btn">Box5</button>
    <button type="button" name="Box6" id="btn6" class="btn">Box6</button>
    <button type="button" name="Box7" id="btn7" class="btn">Box7</button>
    <button type="button" name="Box8" id="btn8" class="btn">Box8</button>

    <div class="btn_box">
        <button type="button" onclick="window.location.href = '/game/onemore'">もう一度</button>
        <button type="button" onclick="window.location.href = '/game/end'">やめる</button>
    </div>


    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/game.js')}}"></script>
    <script>
        let first = "{{$first}}";
        let playerId = "{{$playerId}}";
        if (first === "1") {
            document.geteElementById("game_msg").textContent = "相手が爆弾を仕掛けています...";
            disableClick();
        } else {
            document.geteElementById("game_msg").textContent = "爆弾を仕掛ける箱を選択してください";
            enableClick();
        }

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ config('const.pusher.app_key') }}", {
            cluster: "{{ config('const.pusher.cluster') }}"
        });

        var channel = pusher.subscribe('room.{{$roomId}}');
        
        channel.bind('GameStateUpdate', function(data) {
            document.getElementById("game_text").textContent = "あなたのターン";
            clientBoard = data.board;
            updateBoard(clientBoard);
            enableClick();
        });

        channel.bind('GameEnd', function(data) {
            clientBoard = data.board;
            updateBoard(clientBoard);
            enableClick();
            document.querySelector('.btn_box').style.display = "block";
            if (data.status === 1) {
                if (data.winner != playerId) {
                    document.getElementById("game_text").textContent = "あなたの負けです";
                }
            } else if (data.status === 2) {
                document.getElementById("game_text").textContent = "引き分けです";
            }
        });

    </script>
</body>
</html>