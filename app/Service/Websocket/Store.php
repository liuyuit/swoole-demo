<?php

namespace App\Service\Websocket;

class Store
{
    protected $messageRedisKey = 'websocket:client:message';

    public function cache($messageData)
    {
        $redis = getRedisClient();
        $redis->lpush($this->messageRedisKey, $messageData);
    }
}
