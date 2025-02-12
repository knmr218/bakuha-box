<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>standby</title>
    <style>
        body {
            margin: 0;
            background-image: url('{{ asset('images/panel_title_no_btn.jpg') }}');
            background-size: cover;
        }

        #match_text {
            padding-top: 73vh;
            margin: 0 auto;
            text-align: center;
            width: 26vw;
            height: 13vh;
            color: white;
        }
    </style>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ config('const.pusher.app_key') }}", {
            cluster: "{{ config('const.pusher.cluster') }}"
        });

        var channel = pusher.subscribe('room.{{$roomId}}');
        channel.bind('PlayerMatched', function(data) {
            document.getElementById("match_text").textContent = "PlayerMatched!";
        });

        channel.bind('GameStart', function (data) {
            document.getElementById("match_text").textContent = "Game start!";
            window.location.href = '/bakuha/game/start';
        });
    </script>

</head>
<body>
    <h1 id="match_text">マッチング中</h1>
</body>
</html>