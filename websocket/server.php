<?php
require_once __DIR__ . '/../bootstrap.php';
use App\Service\Websocket\EventData;
use App\Service\Websocket\SentWsToClient;
use App\Service\Websocket\Store;
use Swoole\WebSocket\Server;

ini_set('memory_limit', '2048M');
$ws = new Server('0.0.0.0', 6001);

$ws->on('Open', function ($ws, $request){
    $socketId = random_int(100000000, 999999999) . '.' . random_int(10000000, 99999999);
    $data = EventData::established($socketId, $request->fd);
    echo sprintf("connection established socket_id: %s  fd: %d\n", $socketId, $request->fd);
    $ws->push($request->fd, $data);
});

$ws->on('Message', function ($ws, $frame) {
    $date = date('Y-m-d H:i:s');
    echo "[{$date}]fd {$frame->fd} received message: {$frame->data}\n";

    $message = json_decode($frame->data, true);
    switch ($message['event']) {
        case 'pusher:ping': // 心跳消息
            $result = $ws->push($frame->fd, json_encode([
                'event' => 'pusher:pong',
                'data' => [],
            ]));
            if ($result) {
                echo sprintf("[%s]sent message to fd: %d : %s\n", $date, $frame->fd, 'pusher:pong');
            } else {
                echo sprintf("[%s]sent message to fd: %d : %s failed \n", $date, $frame->fd, 'pusher:pong');
            }
            break;
        case 'pusher:subscribe': // 订阅消息，收到订阅消息后需要回传一个消息给安卓客户端，然后客户端才会修改状态为订阅成功
            $result = $ws->push($frame->fd, json_encode([
                'event' => 'pusher_internal:subscription_succeeded',
                'data' => [],
            ]));
            if ($result) {
                echo sprintf("[%s]sent message to fd: %d : %s\n", $date, $frame->fd, 'pusher_internal:subscription_succeeded');
            } else {
                echo sprintf("[%s]sent message to fd: %d : %s failed\n", $date, $frame->fd, 'pusher_internal:subscription_succeeded');
            }
            break;
        default:
            $store = new Store();
            $store->cache($frame->data);
    }
});

$ws->on('Close', function ($ws, $fd){
    echo "client-{$fd} is closed\n";
});

$ws->on("WorkerStart", function (Server $ws, int $workerId) {
    echo "on WorkerStart \n";
    $sentWs = new SentWsToClient();
    $sentWs->sent($ws);
});

echo "staring the websocket service \n";
$ws->start();
