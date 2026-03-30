<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'APP_URL from config: '.config('app.url')."\n";
echo 'Request is secure: '.(request()->secure() ? 'yes' : 'no')."\n";
echo 'Request scheme: '.request()->getScheme()."\n";
