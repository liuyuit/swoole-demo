<?php

$ws = new Swoole\WebSocket\Server('0.0.0.0', 9502);

$ws->on('Open', function ($ws, $request){
    $ws->push($request->fd, "hello, welcome!\n");
});

$ws->on('Message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";
    $ws->push($frame->fd, "server: {$frame->data}");
});

$ws->on('Close', function ($ws, $fd){
    echo "client-{$fd} is closed\n";
});

$ws->start();
