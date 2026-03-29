<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Список товаров для указанной категории
     */
    public function index(Category $category)
    {
        $products = $category->products;
        return view('admin.products.index', compact('category', 'products'));
    }

    /**
     * для отображения формы создания нового товара для категории
     */
    public function create(Category $category)
    {
        return view('admin.products.form', compact('category'));
    }

    /**
     * проверка введённых данных, обработка изображения,cохранение нового товара в базу
     */
    public function store(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|max:20',
            'description' => 'nullable|max:50',
            'price'       => 'required|min:4',
            'image'       => 'required|image|mimes:jpeg,jpg|max:2000'
        ]);

        //Обработка изображения и миниатюр
        $imagePaths = $this->uploadImage($request->file('image'));
        $validated['image'] = $imagePaths['original'];
        $validated['image_mini'] = $imagePaths['mini'];

        $validated['category_id'] = $category->id;

        Product::create($validated);

        return redirect()->route('categories.products.index', $category)
            ->with('success', 'Товар усешно создан');
    }


    /**
     * Форма редактирования товара
     */
    public function edit(Category $category, Product $product)
    {
        if ($product->category_id !== $category->id) {
            abort(404);
        }

        return view('admin.products.form', compact('category', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category, Product $product)
    {
        if ($product->category_id !== $category->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name'        => 'required|max:20',
            'description' => 'nullable|max:50',
            'price'       => 'required|min:4',
            'image'       => 'required|image|mimes:jpeg,jpg|max:2000'
        ]);

        // Если загруженно новое изображение

        if ($request->hasFile('image')) {
            //Удаляем старые файлы 
            $this->deleteImageFiles($product->image, $product->image_mini);

            //Загружаем новые
            $imagePaths =  $this->uploadImage($request->file('image'));
            $validated['image'] = $imagePaths['original'];
            $validated['image_mini'] = $imagePaths['mini'];
        } else {
            //Если  изображение не меняли, убираем из данных (чтобы не перезаписывать пустотой)
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect()->route('categories.products.index',$category)
            ->with('success', 'Товар успешно обнавлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, Product $product)
    {
        if ($product->category_id !== $category->id) {
            abort(404);
        }

        $product->delete();
        return redirect()->route('categories.products.index',$category)
            ->with('success', 'Товар удален');
    }


    public function uploadImage($file)
    {
        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $originalPath = $file->storeAs('products/original', $filename, 'public');

        $image = Image::read($file);

        //Изменяем размер с сохранением пропорций, максимум 300px
        $image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $miniFilename = 'mpic_' . $filename;
        $miniPath = 'courses/thumbnails/' . $miniFilename;

        //Сохраняем миниатюру в public disk
        Storage::disk('public')->put($miniPath, (string) $image->encodeByExtension('jpg', 90));
        return [
            'original'  => $originalPath,
            'mini' => $miniPath,
        ];
    }

    public function deleteImageFiles($originalPath, $miniPath)
    {
        if ($originalPath && Storage::disk('public')->exists($originalPath)) {
            Storage::disk('public')->delete($originalPath);
        }
        if ($miniPath && Storage::disk('public')->exists($miniPath)) {
            Storage::disk('public')->delete($miniPath);
        }
    }
}
