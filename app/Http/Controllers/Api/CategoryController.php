<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\CategoryCollection;

class CategoryController extends Controller
{
    /**
     * Список категорий с пагинацией
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return new CategoryCollection($categories);

        //Формируем данные для каждого курса.
        //Перебираем с помощью map()

        // $data = $categories->map(function ($category) {
        //     return [
        //         'id' => $category->id,
        //         'name' => $category->name,
        //         'description' => $category->description,
        //     ];
        // });

        //делаем пагинацию

        // $pagination = [
        //     'total' => $categories->total(),
        //     'current' => $categories->currentPage(),
        //     'per_page' => $categories->perPage(),
        // ];

        // return response()->json([
        //     'data' =>CategoryResource::collection($categories),
        //     'pagination' => $pagination,
        // ], 200);
    }


    /**
     * Товары конкретной категории
     */
    public function show($id)
    {
        //Подгружаем все свзяанные с конкретной категорией товары и ищем курс по ключу  
        $category = Category::with('products')->findOrFail($id);
        return response()->json([
            'data' => ProductResource::collection($category->products),
        ], 200);

        // $products = $category->products->map(function ($product) {
        //     return [
        //         'id' => $product->id,
        //         'name' => $product->name,
        //         'description' => $product->description,
        //         'price' => number_format($product->price, 2, '.', ''),
        //         'image_url' => $product->image ? asset('storage/' . $product->image) : null,
        //     ];
        // });
    }

    public function store(Request $request){
        $validate = $request-> validate([
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        Category::create($validate);

        return  response()->json([
            'data' => 'Товар успешно добавлен',
        ], 201);
    }
}
