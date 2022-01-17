<?php

namespace App\Service\Websocket;

use Redis;
use Swoole\WebSocket\Server;

class SentWsToClient
{
    protected $messageRedisKey = 'websocket:sent_to_client:message';

    /**
     * @var Server
     */
    protected $websocket;

    public function sent(Server $websocket)
    {
        $this->websocket = $websocket;
        $redis = getRedisClient();
        echo "prepare to sent ws message to client\n";
        $redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
        $redis->subscribe([$this->messageRedisKey], [$this, 'push']);
    }

    public function push($redisInstance, $channelName, $message) {
        $fd = $this->getFd($message);
        echo sprintf("sent message to fd: %d : %s\n", $fd, $message);
        $this->websocket->push($fd, $message);
    }

    protected function getFd($message)
    {
        $message = json_decode($message, true);
        $channelName = $message['channel'];
        $clientSocket = new ClientSocket();
        return $clientSocket->getFd($channelName);
    }
}
