<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    /**
     *  для отображения страницы категорий

     */
    public function index()
    {
        $categories = Category::paginate(5);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     *  для отображения формы создания категорий
     */
    public function create()
    {
        return view('admin.categories.form');
    }

    /**
     * тут описана проверка введённых данных
     * и вызов модели Category::created для добавления новой категории в базу данных
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|max:15',
            'description' => 'nullable|max:50',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Категория успешно сощдана');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * принимает объект категории и возвращает представление формы редактирования, 
     * передавая данные о категории для автозаполнения полей.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    /**
     * выполняет обновление данных категории, принимает объект запроса и категории, 
     * проверяет его данные на корректность,
     * обновляет запись в базе данных и перенаправляет пользователя обратно
     * на страницу со списком категорий с сообщением об успешном обновлении.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|max:15',
            'description' => 'nullable|max:50',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Категория успешно перезаписана');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Категория успешно удалена');
    }
}
