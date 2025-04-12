@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('product.add') }}</h2>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="category_id">{{ __('product.category') }}</label>
            <select name="category_id" class="form-control select2" required>
                <option value="">{{ __('product.select a category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="name">{{ __('product.product name') }}</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">{{ __('product.description') }}</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="price">{{ __('product.price') }}</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="stock">{{ __('product.stock') }}</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="images">{{ __('product.product images') }}</label>
            <input type="file" name="images[]" class="form-control" multiple required>
            <small class="form-text text-muted">{{ __('product.nb') }}</small>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('product.create product') }}</button>
    </form>
@endsection
