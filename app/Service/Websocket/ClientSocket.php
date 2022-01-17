<?php

namespace App\Service\Websocket;

class ClientSocket
{
    protected $fdRedisKeyPrefix = 'websocket:fd:socket_id:';

    protected $socketIdRedisPrefix = 'websocket:socket_id:channel_name:';

    /**
     * @param $socketId
     * @param $fd int websocket 客户端唯一标识
     * @return bool
     */
    public function putFd($socketId, $fd)
    {
        $redis = getRedisClient();
        return $redis->setex($this->fdRedisKeyPrefix . $socketId, 86400, $fd);
    }

    /**
     * @param $channelName string
     * @return int|null
     */
    public function getFd($channelName)
    {
        $redis = getRedisClient();
        $socketId = $redis->get($this->socketIdRedisPrefix . $channelName);
        if (!$socketId) {
            return null;
        }

        $fd = $redis->get($this->fdRedisKeyPrefix . $socketId);
        if (!$fd) {
            return null;
        }
        return (int)$fd;
    }
}
