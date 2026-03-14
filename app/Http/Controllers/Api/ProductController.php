<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
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
            ]
        ],200);
    }
}
