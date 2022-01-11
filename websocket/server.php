<?php
require_once __DIR__ . '/../bootstrap.php';
use App\Service\Websocket\EventData;



$ws = new Swoole\WebSocket\Server('0.0.0.0', 9503);

$ws->on('Open', function ($ws, $request){
    $socketId = random_int(100000000, 999999999) . random_int(10000000, 99999999);
    $data = EventData::established($socketId);
    $ws->push($request->fd, $data);
});

$ws->on('Message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";

    Co\run(function() {
        go(function () {//创建100个协程
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            $redis->get('key');//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
        });
    });

    $ws->push($frame->fd, "server: {$frame->data}");
});

$ws->on('Close', function ($ws, $fd){
    echo "client-{$fd} is closed\n";
});

$ws->start();
