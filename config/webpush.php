<?php

return [
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@smkn2indramayu.sch.id'),
        'public_key' => env('VAPID_PUBLIC_KEY', 'BBJsQONJI51xiul3vybZ2noD1FMZ0IrcmeIRRwvBXZRuyhmK78YU2unLZ-UE97XLkyzc6lrJEIUNX3nGzn1NMz4'),
        'private_key' => env('VAPID_PRIVATE_KEY', 'TpKHhOlsKwJhQzh-VhstGxMIfBcrROnB3PyG5PDMlxA'),
    ],
    'options' => [
        'TTL' => 86400, // 24 hours
        'urgency' => 'high',
    ],
];
