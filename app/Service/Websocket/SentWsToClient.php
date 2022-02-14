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
        while (true) {
            $message = $redis->rPop($this->messageRedisKey);
            if (!$message) {
                continue;
            }

            $this->pushToWsClient($message);
        }
    }

    public function pushToWsClient($message) {
        $date = date('Y-m-d H:i:s');

        $fd = $this->getFd($message);
        if (!$fd) {
            return;
        }
        $result = $this->websocket->push($fd, $message);
        if ($result) {
            echo sprintf("[%s]sent message to fd: %d : %s\n", $date, $fd, $message);
        } else {
            echo sprintf("[%s]sent message to fd: %d : %s failed !\n", $date, $fd, $message);
        }
    }

    protected function getFd($message)
    {
        $message = json_decode($message, true);
        $channelName = $message['channel'];
        $clientSocket = new ClientSocket();
        return $clientSocket->getFd($channelName);
    }
}
