@extends('layouts.admin')

@section('title', 'Категории')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Категории</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-success">+ Добавить категорию</a> 
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>{{ $category->description }}</td>
            <td>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">✎</a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить категорию?')">🗑</button>
                </form>
                <a href="{{ route('categories.products.create', $category) }}" class="btn btn-sm btn-secondary">+ Товар</a>
                <a href="{{ route('categories.products.index', $category) }}" class="btn btn-sm  btn-success">
                    <i class="bi bi-box-seam"></i> Смотреть товары
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $categories->withQueryString()->links('pagination::bootstrap-5') }}
@endsection