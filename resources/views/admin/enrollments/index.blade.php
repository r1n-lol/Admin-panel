@extends('layouts.admin')

@section('title', 'Просмотор заказов')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .badge { padding: 0.5em 0.75em; }
</style>

<div class="container mt-4">
    <h2>Заказы пользователей</h2>

    <!-- Форма фильтрации по курсу -->
    <form action="{{ route('enrollments.index') }}" method="GET" class="row g-3 mb-3">
        <div class="col-auto">
            <select name="course_id" class="form-select">
                <option value="">Все товары</option>
                @foreach ($products as $product)
                    <option value="{{ $product>id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Фильтр</button>
        </div>
    </form>

    <!-- Таблица записей -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Название товара</th>
                <th>Email</th>
                <th>Цена</th>
                <th>Статус оплаты</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enrollment)
            <tr>
                <td>{{ $enrollment->user->email }}</td>
                <td>{{ $enrollment->user->name }}</td>
                <td>{{ $enrollment->product->name }}</td>
                <td>
                    @switch($enrollment->status)
                        @case('pending' )
                            <span class="badge bg-warning text-dark">Ожидает оплаты</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success">Оплачено</span>
                            @break
                        @case('failed' )
                            <span class="badge bg-danger">Ошибка</span>
                            @break
                        @case( 'refunded' )
                            <span class="badge bg-info"> Возврат <span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ $enrollment->status }}</span>
                    @endswitch
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Нет записей</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Пагинация -->
    {{ $enrollments->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection