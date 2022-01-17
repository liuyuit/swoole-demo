<?php
require_once __DIR__ . '/../bootstrap.php';
use App\Service\Websocket\EventData;
use App\Service\Websocket\SentWsToClient;
use App\Service\Websocket\Store;
use Swoole\WebSocket\Server;

$ws = new Server('0.0.0.0', 9503);

$ws->on('Open', function ($ws, $request){
    $socketId = random_int(100000000, 999999999) . '.' . random_int(10000000, 99999999);
    $data = EventData::established($socketId, $request->fd);
    echo sprintf("connection established socket_id: %s  fd: %d\n", $socketId, $request->fd);
    $ws->push($request->fd, $data);
});

$ws->on('Message', function ($ws, $frame) {
    echo "fd {$frame->fd} received message: {$frame->data}\n";

    $message = json_decode($frame->data, true);
    switch ($message['event']) {
        case 'pusher:ping': // 心跳消息
            $ws->push($frame->fd, json_encode([
                'event' => 'pusher:pong',
            ]));
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
