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
    <style>
        /* .btn_box {
            display: none;
        } */

        .btn{
            position: relative;
            display: inline-block;
            /* 枠線を消す */
            border: none;
            /* クリックした際に枠線をnone消す */
            outline: none;
            /* 影を消す */
            box-shadow: none;
            padding: 0;
            margin: 5px;
            /* text-align: center; */
            width: 100px;
            height: 100px;
            overflow: hidden;
            cursor: pointer;
        }

        .close{
            background: url( '{{asset("images/close_takarabako.png")}}' )no-repeat center center / cover;
        }

        .open{
            background: url( '{{asset("images/open_takarabako.png")}}' )no-repeat center center / cover;
        }

        .close::before, .close::after{
            content: "";
            display: block;
            position: absolute;
            margin: auto;
            top: 0;
            left: 0;
            margin: auto;
            transition: .3s cubic-bezier(0.45, 0, 0.55, 1);
            opacity: 0;
        }

        .close::after{
            background: rgba(190,91,70,.6);
            width: 100%;
            height: 100%;
            color: #fff;
            content: "Interact";
            font-size: 22px;
            font-weight: bold;
            display: flex;
            text-align: center;
            justify-content: center;
            align-items: center;
            bottom: 0;
            right: 0;
        }

        .close::before{
            /* color: #fff;
            content: "開ける";
            font-size: 22px; */
            /* font-weight: bold;
            display: flex;
            text-align: center;
            justify-content: center;
            /* align-items: center; */
            bottom: 0;
            right: 0;
        }

        .close:hover::before, .close:hover::after{
            opacity: 1;
        }

        .btn p{
            position: absolute;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0 !important;
            padding: 0 !important;
            color: black;
            background-color: rgba(255, 145, 0, 0.3);
            backdrop-filter: blur(2px);
            width: 90%;
            font-weight: bold;
        }

        .label_container {
            display: none;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            position: fixed; /* コンテンツ内で絶対配置 */
            top: 0%; /* 親要素の中央に配置 */
            left: 0%;
            /* transform: translate(-40%, -50%); 中心に調整 */
            margin: 0;
            z-index: 100000; /* コンテンツより前面に表示 */
            transition: opacity 0.5s ease;
        }

        .label_image {
            width: 80vw;
        }

        .game_info {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
        }

        .player_info {
            text-align: center;
        }

        .player_info table td {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="game_info">
        <div id="your_info" class="player_info">
            <h3>YOU</h3>
            <table border="1">
                <tr>
                    <td>POINT</td>
                    <td id="your_point">0</td>
                    
                </tr>
                <tr>
                    <td>LIFE</td>
                    <td id="your_life">2</td>
                </tr>
            </table>
        </div>
        <h2 id="turn_info">1ターン　前半</h2>
        <div id="enemy_info" class="player_info">
            <h3>ENEMY</h3>
            <table border="1">
                <tr>
                    <td>POINT</td>
                    <td id="enemy_point">0</td>
                </tr>
                <tr>
                    <td>LIFE</td>
                    <td id="enemy_life">2</td>
                </tr>
            </table>
        </div>
    </div>

    <h2 id="game_msg"></h2>
    <!-- <button type="button" name="Box1" id="btn1" class="btn">Box1</button>
    <button type="button" name="Box2" id="btn2" class="btn">Box2</button>
    <button type="button" name="Box3" id="btn3" class="btn">Box3</button>
    <button type="button" name="Box4" id="btn4" class="btn">Box4</button>
    <button type="button" name="Box5" id="btn5" class="btn">Box5</button>
    <button type="button" name="Box6" id="btn6" class="btn">Box6</button>
    <button type="button" name="Box7" id="btn7" class="btn">Box7</button>
    <button type="button" name="Box8" id="btn8" class="btn">Box8</button> -->
    <button type="button" name="Box1" id="btn1" class="btn close">
        <p>1</p>
    </button>
    <button type="button" name="Box2" id="btn2" class="btn close">
        <p>2</p>
    </button>
    <button type="button" name="Box3" id="btn3" class="btn close">
        <p>3</p>
    </button>
    <button type="button" name="Box4" id="btn4" class="btn close">
        <p>4</p>
    </button>
    <button type="button" name="Box5" id="btn5" class="btn close">
        <p>5</p>
    </button>
    <button type="button" name="Box6" id="btn6" class="btn close">
        <p>6</p>
    </button>
    <button type="button" name="Box7" id="btn7" class="btn close">
        <p>7</p>
    </button>
    <button type="button" name="Box8" id="btn8" class="btn close">
        <p>8</p>
    </button>

    <!-- <div>
        <p>point:<span id="point_text">0</span></p>
        <p>life:<span id="life_text">2</span></p>
    </div> -->

    <h3 id="result_msg"></h3>

    <div class="btn_box">
        <button type="button" onclick="gameEnd()">やめる</button>
    </div>

    <div class="label_container">
        <img src="" alt="" class="label_image">
    </div>


    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/game.js')}}"></script>
    <script src="{{asset('js/anim.js')}}"></script>
    <script>
        let first = "{{$first}}";
        let playerId = "{{$playerId}}";

        if (first === "1") {
            document.getElementById("game_msg").textContent = "相手が爆弾を仕掛けています";
            disableClick();
            setTimeout(() => {
                startLabelAnimation("{{ asset('images/enemy_set_turn.png') }}");    
            }, 1000);
            
        } else {
            document.getElementById("game_msg").textContent = "爆弾を仕掛ける箱を選択してください";
            enableClick();
            setTimeout(() => {
                startLabelAnimation("{{ asset('images/your_set_turn.png') }}");  
            }, 1000);
            startCountdown();
        }

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ config('const.pusher.app_key') }}", {
            cluster: "{{ config('const.pusher.cluster') }}"
        });

        var channel = pusher.subscribe('room.{{$roomId}}');
        
        channel.bind('GameStateUpdate', function(data) {
            phase = data.phase;
            document.getElementById('game_msg').textContent = data.turn_msg[playerId];
            let labelPath;
            if (data.selectPlayer === playerId) {
                if (phase == 0) {
                    labelPath = "{{ asset('images/your_set_turn.png') }}";
                } else {
                    labelPath = "{{ asset('images/your_open_turn.png') }}";
                }
                enableClick();
                startCountdown();
            } else {
                if (phase == 0) {
                    labelPath = "{{ asset('images/enemy_set_turn.png') }}";
                } else {
                    labelPath = "{{ asset('images/enemy_open_turn.png') }}";
                }
                disableClick();
            }
            
            if (phase == 0) {
                clientBox = data.box;
                const btns = document.querySelectorAll(".btn");
                for(let i = 0; i < 8; i++){
                    if (clientBox[i] == "2"){
                        btns[i].classList.add("open");
                        btns[i].classList.remove("close");
                    }
                }
                updateBox(clientBox);
                document.getElementById("turn_info").textContent = data.turn_num;
                
                document.getElementById("your_point").textContent = data.player_info[playerId][0];
                document.getElementById("your_life").textContent = data.player_info[playerId][1];

                // すべてのキーを取得
                let keys = Object.keys(data.player_info);
                // 自分以外のキーを取得
                let enemyKey = keys.find(key => key !== playerId);
                // もう片方の情報を取得
                let enemyValue = data.player_info[enemyKey];

                document.getElementById("enemy_point").textContent = enemyValue[0];
                document.getElementById("enemy_life").textContent = enemyValue[1];
                
                if (data.msg[playerId]) {
                    startLabelAnimation("{{ asset('images/safe_point.png') }}");
                } else {
                    startLabelAnimation("{{ asset('images/bomb_0point_life-1.png') }}");
                }
                
            }

            startLabelAnimation(labelPath);
        });

        channel.bind('GameEnd', function(data) {
            clientBox = data.box;
            document.getElementById("turn_info").textContent = data.turn_num;
                
            document.getElementById("your_point").textContent = data.player_info[playerId][0];
            document.getElementById("your_life").textContent = data.player_info[playerId][1];

            // すべてのキーを取得
            let keys = Object.keys(data.player_info);
            // 自分以外のキーを取得
            let enemyKey = keys.find(key => key !== playerId);
            // もう片方の情報を取得
            let enemyValue = data.player_info[enemyKey];

            document.getElementById("enemy_point").textContent = enemyValue[0];
            document.getElementById("enemy_life").textContent = enemyValue[1];
            updateBox(clientBox);
            disableClick();
            document.getElementById("game_msg").textContent = data.res_msg[playerId];
            gameStatus = 1;
            if (data.safe) {
                startLabelAnimation("{{ asset('images/safe_point.png') }}");
            } else {
                startLabelAnimation("{{ asset('images/bomb_0point_life-1.png') }}");
            }
            if (data.res_msg[playerId] === "あなたの勝ちです") {
                startLabelAnimation("{{ asset('images/you_win.png') }}");
            } else {
                startLabelAnimation("{{ asset('images/you_lose.png') }}");
            }
        });

        channel.bind('GameForceEnd', function(data) {
            disableClick();
            document.getElementById("game_msg").textContent = data.res_msg[playerId];
            gameStatus = 1;
            if (data.res_msg[playerId] === "あなたの勝ちです") {
                startLabelAnimation("{{ asset('images/you_win.png') }}");
            } else {
                startLabelAnimation("{{ asset('images/you_lose.png') }}");
            }
        });


    </script>
</body>
</html>