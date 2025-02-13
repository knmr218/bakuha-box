function sendMoveToServer(num) {
    // サーバーに送信
    fetch('/bakuha/game/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ "num": num }) // 箱番号をサーバーに送信
    })
    .then(response => response.json()) // サーバーからのレスポンスをJSONで受け取る
    .then(data => {
        if (data.Invalid) {
            alert('無効な操作');
            return;
        }
        
        // サーバーから返ってきたデータを使ってクライアント側のボードを更新
        clientBox = data.box;
        updateBox(clientBox);

        document.getElementById('point_text').textContent = data.point;
        document.getElementById('life_text').textContent = data.life;
        
        if (data.winner !== 0) {
            disableClick();
            return;
        }
    })
    .catch(error => {
        console.log('Error:', error);
        alert('エラーが発生しました');
        window.location.href = '/bakuha/game/end';
    });
}

function updateBox(box) {
    // サーバーから送られた状態をもとにクライアントを更新
    for (let i = 0; i < box.length; i++) {
        const boxElement = boxes[i]; // 対応する要素を取得
        if (box[i] === "2") {
            boxElement.style.color = "gray";
        }
    }
}

// マスをクリックしたときの処理
function handleClick(box, index) {
    if (clientBox[index] !== "2") {
        disableClick();
        clearTimeout(countdown);
        sendMoveToServer(index);
    } 
}

const boxes = document.querySelectorAll('.btn');
let clientBox = "00000000"; // 0:未開封　1:爆弾　2:開封済み
let phase = 0; // 0:爆弾セット　1:開封
let gameStatus = 0; // 0:ゲーム中 1:ゲーム終了

let countdown;
const remainingTime = 10 * 60 * 1000; // 10分（ミリ秒単位）


boxes.forEach((box,index) => {
    box.addEventListener('click', () => {
        handleClick(box, index);
    });
});

// 無効化する関数
function disableClick() {
    boxes.forEach(box => {
        box.style.pointerEvents = "none";
    });
}

// 有効化する関数
function enableClick() {
    boxes.forEach(box => {
        box.style.pointerEvents = "auto";
    });
}

function gameForceEnd() {
    fetch('/bakuha/game/force_end', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
    })
    .then(response => response.json()) // サーバーからのレスポンスをJSONで受け取る
    .catch(error => {
        console.log('Error:', error);
        alert('エラーが発生しました');
        window.location.href = '/bakuha/game/end';
    });
}

function gameEnd() {
    if (gameStatus === 0) {
        gameForceEnd();
    } else {
        location.href = "/bakuha/game/end";
    }
}

function startCountdown() {
    countdown = setTimeout(() => {
        gameEnd();
    }, remainingTime);
}
