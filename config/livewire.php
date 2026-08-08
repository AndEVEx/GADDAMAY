<?php

return [
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:12288'],
        'directory' => null,
        'middleware' => ['web'],
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],
];
