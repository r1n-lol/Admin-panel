<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Http\Requests\PaymentWebhookRequest;

class PaymentWebhookController extends Controller
{
        public function handle(PaymentWebhookRequest $request)
    {
        // Валидация входящих данных 
        $data = $request->validated();
        // Поиск заказа
        $enrollment = Enrollment::where('order_id', $data['order_id'])->firstOrFail();

        if ($enrollment) {
            // Обновляем статус
            $enrollment->status = $data['status'];
            $enrollment->save();
        }

        // В любом случае возвращаем 204 (даже если заказ не найден – чтобы не плодить повторные попытки)
        return response()->noContent();
    }
}

