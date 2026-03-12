@extends('layouts.admin')

@section('title', isset($category) ? 'Редактировать курс' : 'Новая категория')

@section('content')
<h2>{{ isset($category) ? 'Редактировать категорию' : 'Создать категорию' }}</h2>

<form method="POST" action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($category)) @method('PUT') @endif

    <div class="mb-3">
        <label for="title" class="form-label">Название *</label>
        <input type="text" name="name" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $category->name ?? '') }}" maxlength="15" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Описание (макс. 50)</label>
        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" maxlength="50">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>




    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Сохранить' : 'Создать' }}</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Отмена</a>
</form>
@endsection