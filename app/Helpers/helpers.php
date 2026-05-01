<?php

use App\Models\Customer;

if (! function_exists('formatLebanesePhoneNumber')) {
    function formatLebanesePhoneNumber($phone)
    {
        // إزالة كل ما ليس رقماً
        $phone = preg_replace('/\D/', '', $phone);
        if (empty($phone)) {
            return null;
        }

        // إزالة 00 بادئة إن وجدت (00961...)
        if (substr($phone, 0, 4) === '00961') {
            $phone = substr($phone, 2);
        }

        // إزالة 0 البادئ للرقم المحلي
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }

        // إذا كان الرقم بالفعل يبدأ بـ 961 نتركه
        if (substr($phone, 0, 3) === '961') {
            return $phone;
        }

        // أي رقم آخر نضيف له 961
        return '961' . $phone;
    }
}

if (! function_exists('getCurrentCustomer')) {
    function getCurrentCustomer()
    {
        if (session()->has('customer_id')) {
            return Customer::find(session('customer_id'));
        }
        return null;
    }
}
