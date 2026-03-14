<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Product;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function buy(Request $request, $id)
    {
        //получаем пользователя
        $user = $request->user();
        //получаем товар
        $product = Product::findOrFail($id);

        // Генерация ссылки на оплату 
        $paymentLink = url("/payment/checkout?user_id={$user->id}&product_id={$product->id}");

        return response()->json([
            'payment_url' => $paymentLink
        ], 200);
    }


    public function index()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with('product')
            ->get();

        $data = $enrollments->map(function ($enrollment) {
            $statusMap = [
                'pending' => 'ожидает оплаты',
                'success' => 'оплачено',
                'failed' => 'ошибка оплаты',
                'cancelled' => 'отменено',
            ];
            $paymentStatus = $statusMap[$enrollment->payment_status] ?? $enrollment->payment_status;

            $product = $enrollment->product;
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => number_format($product->price, 2, '.', ''),
                'img' => $product->image ? asset('storage/' . $product->image) : null,
            ];

            return [
                'id' => $enrollment->id,
                'payment_status' => $paymentStatus,
                'product' => $productData,
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    
    public function cancel(Enrollment $order)
    {
        // Проверка принадлежности пользователю
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden for you'], 403);
        }

        if (in_array($order->payment_status, ['pending', 'failed'])) {
            $order->update(['payment_status' => 'cancelled']);
            return response()->json(['status' => 'success'], 200);
        }

        if ($order->payment_status === 'success') {
            return response()->json(['status' => 'was payed'], 418);
        }

        return response()->json(['message' => 'Cannot cancel this order'], 422);
    }

}
