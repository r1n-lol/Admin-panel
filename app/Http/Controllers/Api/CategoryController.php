<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Список категорий с пагинацией
     */
    public function index()
    {
        $categories = Category::paginate(5);

        //Формируем данные для каждого курса.
        //Перебираем с помощью map()

        $data = $categories->map(function ($category) {
            return [
                'name' => $category->name,
                'description' => $category->description,
            ];
        });

        //делаем пагинацию

        $pagination = [
            'total' => $categories->total(),
            'current' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
        ];

        return response()->json([
            'data' => $data,
            'pagination' => $pagination,
        ], 200);
    }


    /**
     * Товары конкретной категории
     */
    public function show($id)
    {
        //Подгружаем все свзяанные с конкретной категорией товары и ищем курс по ключу  
        $category = Category::with('products')->findOrFail($id);

        $products = $category->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => number_format($product->price, 2, '.', ''),
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
            ];
        });

        return response()->json([
            'data' => $products
        ],200);
    }
}
