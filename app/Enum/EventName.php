<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static EventName Established()
 */
final class EventName Extends Enum
{
    private const Established = 'pusher:connection_established';
}
