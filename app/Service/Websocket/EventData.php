<?php
namespace App\Service\Websocket;

use App\Enum\EventName;

class EventData
{
    public static function established($socketId)
    {
        $data = [
            'socket_id' => $socketId,
            'activity_timeout' => 30,
        ];

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
