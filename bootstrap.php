<?php

require __DIR__ . '/vendor/autoload.php';
Co::set(['hook_flags'=> SWOOLE_HOOK_ALL]);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$appService = new \App\Provider\AppServiceProvider();
$appService->boot(__DIR__);
