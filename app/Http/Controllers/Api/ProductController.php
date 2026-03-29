<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();

        return response()->json([
            'data' => $products
        ], 200);
    }

    /**
     * Просмотор одного товара по id
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => number_format($product->price, 2, '.', ''),
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                'user_id' => $product->user_id,
                'user_email' => $product->user->email, // сюда вернем email
            ]
        ], 200);
    }

    public function myProducts(Request $request)
    {
        // Возвращаем только продукты, где user_id совпадает с авторизованным пользователем
        $products = Product::where('user_id', $request->user()->id)->get();

        return response()->json([
            'data' => $products
        ]);
    }

    // Метод удаления
    public function destroy($id, Request $request)
    {
        $product = Product::findOrFail($id);

        // Проверяем, что это продукт текущего пользователя
        if ($product->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Нет доступа'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Продукт удалён']);
    }

    public function store(Request $request)
    {
        // Валидация
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Сохраняем основное изображение
            $path = $image->storeAs('public', $imageName); // сохраняется в storage/app/public/

            // Миниатюру просто указываем как имя файла с префиксом
            $imageMiniName = 'mini_' . $imageName;

            // Можно просто не создавать физически пока миниатюру, а оставить имя
            // Если нужно физически копировать, убедись, что путь существует:
            $fullPath = storage_path('app/public/' . $imageMiniName);
            copy(storage_path('app/public/' . $imageName), $fullPath);
        } else {
            return response()->json(['error' => 'Изображение не загружено'], 400);
        }
        // Создаём продукт
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'],
            'image' => $imageName,
            'image_mini' => $imageMiniName,
            'user_id' => $request->user()->id,
            'category_id' => 1, // можно подставить "стандартную" категорию
        ]);

        return response()->json([
            'message' => 'Продукт успешно создан',
            'data' => $product,
        ], 201);
    }
}
