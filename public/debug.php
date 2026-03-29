<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle($request = Request::capture());
echo $response->getContent();
