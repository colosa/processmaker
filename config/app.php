<?php

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

return [
    'name' => env('APP_NAME', 'ProcessMaker'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'log' => env('APP_LOG', 'single'),
    'log_level' => env('APP_LOG_LEVEL', 'debug'),
    'cache_lifetime' => env('APP_CACHE_LIFETIME', 60),

    'providers' => [
        FilesystemServiceProvider::class,
        CacheServiceProvider::class,
        ViewServiceProvider::class,
    ],

    'aliases' => [
    ],

];
