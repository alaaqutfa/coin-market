<?php

$laravelBasePath = __DIR__;

echo $laravelBasePath . PHP_EOL;
echo 'Cron Job is running' . PHP_EOL;

passthru("/usr/bin/php $laravelBasePath/artisan attendance:calculate-daily-hours");

?>
