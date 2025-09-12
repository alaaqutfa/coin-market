<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductBarcodeLog;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $notifications = ProductBarcodeLog::getTemporaryNotifications(20);
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    public function createNotification(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'note' => 'required|string|max:500',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $notification = ProductBarcodeLog::createTemporaryNotification(
            $request->product_id,
            $request->note,
            $request->user_id
        );

        return response()->json([
            'success' => $notification !== null,
            'notification' => $notification,
            'message' => $notification ? 'تم إنشاء الإشعار' : 'فشل في إنشاء الإشعار'
        ]);
    }
}