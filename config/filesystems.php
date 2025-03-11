<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'bukti-activity' => [
            'driver' => 'local',
            'root' => public_path(). '/uploads/customer-activity',
            'url' => env('APP_URL').'/public/uploads/customer-activity',
            'visibility' => 'public',
            'throw' => false,
        ],
        'spk' => [
            'driver' => 'local',
            'root' => public_path(). '/spk',
            'url' => env('APP_URL').'/public/spk',
            'visibility' => 'public',
            'throw' => false,
        ],
        'pks' => [
            'driver' => 'local',
            'root' => public_path(). '/pks',
            'url' => env('APP_URL').'/public/pks',
            'visibility' => 'public',
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
        'sdt-training-image' => [
            'driver' => 'local',
            'root' => public_path(). '/uploads/sdt-training/image',
            'url' => env('APP_URL').'/public/uploads/sdt-training/image',
            'visibility' => 'public',
            'throw' => false,
        ],
        'sdt-training-file' => [
            'driver' => 'local',
            'root' => public_path(). '/uploads/sdt-training/file',
            'url' => env('APP_URL').'/public/uploads/sdt-training/file',
            'visibility' => 'public',
            'throw' => false,
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
