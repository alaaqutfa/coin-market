<?php

$laravelBasePath = __DIR__ . '/../../'; // تأكد أن هذا هو المسار الصحيح لمجلد لارافيل (مجلد يحتوي على ملف artisan)

// طباعة المسار (فقط للتجربة، يمكنك إزالته لاحقاً)
echo $laravelBasePath . PHP_EOL;

// طباعة رسالة لتأكيد عمل الكرون (اختياري)
echo 'Cron Job is running' . PHP_EOL;

// تنفيذ أمر Artisan المطلوب لتنفيذ الحسابات المطلوبة
passthru("/usr/bin/php $laravelBasePath/artisan attendance:calculate-daily-hours >> /dev/null 2>&1");

?>
