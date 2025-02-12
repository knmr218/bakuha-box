<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>title</title>
    <style>
        body {
            margin: 0;
            background-image: url('{{ asset('images/panel_title_no_btn.jpg') }}');
            background-size: cover;
            padding-top: 65vh;
        }

        .btn_box {
            display: flex;
            justify-content: center;
        }

        #start_btn {
            width: 26vw;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #start_btn:hover {
            transform: translateY(-10px);
            opacity: 0.8;
        }

        .text_box {
            margin: 0 auto;
            justify-content: center;
            align-items: center;
            gap: 10px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        #match_text {
            margin: 0;
            font-size: 30px;
            color: white;
        }

        .loader {
            width: 25px;
            height: 25px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-top: -5px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }


    </style>
</head>
<body>
    <div class="btn_box">
        <!-- <button type="button" class="btn btn-primary" id="start_btn">開始</button> -->
        <img src="{{ asset('images/start_btn.png') }}" alt="" id="start_btn">
    </div>

    <div class="text_box">
        <h1 id="match_text">マッチング中</h1>
        <div class="loader"></div>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ config('const.pusher.app_key') }}", {
            cluster: "{{ config('const.pusher.cluster') }}"
        });


        const startBtn = document.getElementById("start_btn");

        startBtn.addEventListener("click", function() {
            fetch('/bakuha/room/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.matched) {
                    location.href = '/bakuha/game/init';
                } else {
                    if (data.roomId) {
                        startBtn.style.display = "none";
                        const matchText = document.getElementById("match_text");
                        document.querySelector(".text_box").style.display = "flex";
                        setTimeout(() => {
                            document.querySelector(".text_box").style.opacity = 1;
                        }, 50);
                        
                        var roomId = data.roomId;
                        var channel = pusher.subscribe('room.' + roomId);

                        channel.bind('PlayerMatched', function(data) {
                            matchText.textContent = "マッチング完了！";
                        });

                        channel.bind('GameStart', function (data) {
                            matchText.textContent = "ゲーム開始！";
                            window.location.href = '/bakuha/game/start';
                        });

                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました');
            });
        });

    </script>
</body>
</html>