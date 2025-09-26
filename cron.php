<?php

$laravelBasePath = __DIR__ . '/../../'; // اضبط حسب مكان مجلد Laravel بالنسبة لمجلد stock

echo __DIR__ . '/../../';
echo 'Cron Job is runing';

// تنفيذ الأمر artisan المطلوب وحفظ المخرجات (اختياري)
passthru("/usr/bin/php $laravelBasePath/artisan attendance:calculate-daily-hours >> /dev/null 2>&1");

?>
