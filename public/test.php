<?php

echo 'PHP Version: '.phpversion()."\n";
echo 'APP_KEY: '.(env('APP_KEY') ?: 'NOT SET')."\n";
echo 'APP_DEBUG: '.(env('APP_DEBUG') ?: 'NOT SET')."\n";
echo 'Storage path: '.storage_path()."\n";
echo 'Cache dir exists: '.(is_dir(storage_path('framework/cache/data')) ? 'YES' : 'NO')."\n";
