@extends('layouts.admin')

@section('title', 'Товары категории: ' . $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Товары категории «{{ $category->name }}»</h2>
    <a href="{{ route('categories.products.create', $category) }}" class="btn btn-success">
        + Добавить товар
    </a>
</div>



<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Изображение</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->description }}</td>
            <td>{{ $product->price }}</td>
             <td><img src="{{ asset('storage/' . $product->image_mini) }}" alt="photo"></td> 
            <td>
                <a href="{{ route('categories.products.edit', [$category, $product]) }}" class="btn btn-sm btn-primary">
                    ✎ Редактировать
                </a>
                <form action="{{ route('categories.products.destroy', [$category, $product]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">
                        🗑 Удалить
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">В этой категории пока нет товаров.</td>
        </tr>
        @endforelse
    </tbody>
</table>


<a href="{{ route('categories.index') }}" class="btn btn-secondary">← Назад к категориям</a>
@endsection