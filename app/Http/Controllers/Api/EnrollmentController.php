<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EnrollmentResource;
use App\Models\Enrollment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnrollmentController extends Controller
{
    public function buy(Request $request, Product $product)
    {
        //получаем пользователя
        // $user = $request->user();
        // //получаем товар
        $product = Product::findOrFail($request->product_id);

        // // Генерация ссылки на оплату 
        // $paymentLink = url("/payment/checkout?user_id={$user->id}&product_id={$product->id}");

        // return response()->json([
        //     'payment_url' => $paymentLink
        // ], 200);

        $enrollment = Enrollment::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        $response = Http::post('http://localhost:8081/payments', [
            'price' => $product->price,
            'webhook_url' => route('payment-webhook'),
        ]);

        if ($response->failed()) {
            $enrollment->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment service unavailable'], 500);
        }

        $data = $response->json();
        // $paymentUrl = $data['payment_url'] ?? null;

        // if (!$paymentUrl) {
        //     return response()->json([
        //         'message' => 'Payment URL missing'
        //     ], 500);
        // }


        $enrollment->update([
            'order_id' => $data['order_id'],
        ]);

        return response()->json([
            'pay_url' => $data['pay_url'],
        ], 200);
    }


    public function index()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with('product')
            ->get();
        return EnrollmentResource::collection(($enrollments));

        // $data = $enrollments->map(function ($enrollment) {
        //     $statusMap = [
        //         'pending' => 'ожидает оплаты',
        //         'success' => 'оплачено',
        //         'failed' => 'ошибка оплаты',
        //         'cancelled' => 'отменено',
        //     ];
        //     $paymentStatus = $statusMap[$enrollment->status] ?? $enrollment->status;

        //     $product = $enrollment->product;
        //     $productData = [
        //         'id' => $product->id,
        //         'name' => $product->name,
        //         'description' => $product->description,
        //         'price' => number_format($product->price, 2, '.', ''),
        //         'img' => $product->image ? asset('storage/' . $product->image) : null,
        //     ];

        //     return [
        //         'id' => $enrollment->id,
        //         'payment_status' => $paymentStatus,
        //         'product' => $productData,
        //     ];
        // });


    }


    public function cancel(Enrollment $order)
    {
        // Проверка принадлежности пользователю
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden for you'], 403);
        }

        if (in_array($order->status, ['pending', 'failed'])) {
            $order->update(['status' => 'cancelled']);
            return response()->json(['status' => 'success'], 200);
        }

        if ($order->status === 'success') {
            return response()->json(['status' => 'was payed'], 418);
        }

        return response()->json(['message' => 'Cannot cancel this order'], 422);
    }
}
