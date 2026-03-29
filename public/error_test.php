<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');
echo 'Error log: '.ini_get('error_log')."\n";
echo 'Display errors: '.ini_get('display_errors')."\n";
echo 'Error reporting: '.error_reporting()."\n";
