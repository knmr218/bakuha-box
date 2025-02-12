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
    </style>
</head>
<body>
    <h1 id="game_title">爆破BOX</h1>

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

    <div>
        <p>point:<span id="point_text">0</span></p>
        <p>life:<span id="life_text">2</span></p>
    </div>

    <h3 id="result_msg"></h3>

    <div class="btn_box">
        <!-- <button type="button" onclick="window.location.href = '/bakuha/game/onemore'">もう一度</button> -->
        <button type="button" onclick="window.location.href = '/bakuha/game/end'">やめる</button>
    </div>


    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{asset('js/game.js')}}"></script>
    <script>
        let first = "{{$first}}";
        let playerId = "{{$playerId}}";
        if (first === "1") {
            document.getElementById("game_msg").textContent = "相手が爆弾を仕掛けています";
            disableClick();
        } else {
            document.getElementById("game_msg").textContent = "爆弾を仕掛ける箱を選択してください";
            enableClick();
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
            if (data.selectPlayer === playerId) {
                enableClick();
            } else {
                disableClick();
            }
            if (phase == 0) {
                document.getElementById('result_msg').textContent = data.msg[playerId];
                clientBox = data.box;
                const btns = document.querySelectorAll(".btn");
                for(let i = 0; i < 8; i++){
                    if (clientBox[i] == "2"){
                        btns[i].classList.add("open");
                        btns[i].classList.remove("close");
                    }
                }
                updateBox(clientBox);
            }
        });

        channel.bind('GameEnd', function(data) {
            clientBox = data.box;
            updateBox(clientBox);
            disableClick();
            document.getElementById("game_msg").textContent = data.res_msg[playerId];
            document.querySelector(".btn_box").style.display = "block";
            document.getElementById('result_msg').textContent = data.msg[playerId];
        });


    </script>
</body>
</html>