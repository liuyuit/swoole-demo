<?php

namespace App\Service\Websocket;

class Store
{
    public function cache($messageData)
    {
        Co\run(function() use($messageData) {
            go(function ()  use($messageData) {//创建100个协程
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程 // todo 用 env + config 的方式获取配置信息
                $redis->set('key', $messageData);//此处产生协程调度，cpu切到下一个协程，不会阻塞进程
            });
        });
    }
}
