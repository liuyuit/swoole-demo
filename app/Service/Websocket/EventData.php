<?php
namespace App\Service\Websocket;

use App\Enum\EventName;

class EventData
{
    /**
     * @param $socketId
     * @param $fd int websocket 客户端唯一标识
     * @return string
     */
    public static function established($socketId, $fd)
    {
        $data = [
            'socket_id' => $socketId,
            'activity_timeout' => 30,
        ];

        $clientSocket = new ClientSocket();
        $clientSocket->putFd($socketId, $fd);

        return static::build(EventName::Established(), $data);
    }

    private static function build(string $event, array $data): string
    {
        $data = [
            'event' => $event,
            'data' => json_encode($data),
        ];

        return json_encode($data);
    }
}
