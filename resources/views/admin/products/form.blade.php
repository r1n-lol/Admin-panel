@extends('layouts.admin')

@section('title', isset($product) ? 'Редактировать урок' : 'Новый товар')

@section('content')
<h2>{{ isset($product) ? 'Редактировать товар' : 'Добавить товар' }} для курса «{{ $category->name }}»</h2>

<form method="POST" action="{{ isset($product) ? route('categories.products.update', [$category, $product]) : route('categories.products.store', $category) }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($product))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Название * (макс. 20 симв.)</label>
        <input type="text" 
               name="name" 
               id="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $product->name ?? '') }}" 
               maxlength="50" 
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Описание *</label>
        <textarea name="description" 
                  id="description" 
                  rows="5" 
                  class="form-control @error('description') is-invalid @enderror" 
                  required>{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Цена * (>10, формат xx.xx)</label>
        <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price ?? '') }}" min="2" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

      <div class="mb-3">
        <label for="image" class="form-label">Обложка (JPG, ≤2 МБ) @if(!isset($product)) * @endif</label>
        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg" {{ isset($product) ? '' : 'required' }}>
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if(isset($product) && $product->cover)
            <img src="{{ asset('storage/' . $product->image_mini) }}" width="100" class="mt-2">
        @endif
    </div>


  

    <button type="submit" class="btn btn-primary">
        {{ isset($product) ? 'Сохранить изменения' : 'Создать товар' }}
    </button>
    <a href="{{ route('categories.products.index', $category) }}" class="btn btn-secondary">Отмена</a>
</form>
@endsection
