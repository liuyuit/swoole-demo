<?php

$server = new Swoole\Server('127.0.0.1', 9501);

$server->on('Connect', function ($server, $fd){
    echo "Client: Connect.\n";
});

$server->on('Receive', function ($server, $fd, $reactor_id, $data) {
    $server->send($fd, "Message from server: {$data}");
});

$server->on('Close', function ($server, $fd){
    echo "Client: Closed: \n";
});

$server->start();
