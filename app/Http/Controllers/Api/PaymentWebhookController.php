<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;

class PaymentWebhookController extends Controller
{
        public function handle(Request $request)
    {
        // Валидация входящих данных 
        $data = $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|in:success,failed'
        ]);

        // Поиск заказа
        $enrollment = Enrollment::find($data['order_id']);

        if ($enrollment) {
            // Обновляем статус
            $enrollment->payment_status = $data['status'];
            $enrollment->save();
        }

        // В любом случае возвращаем 204 (даже если заказ не найден – чтобы не плодить повторные попытки)
        return response()->noContent();
    }
}

