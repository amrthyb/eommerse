@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('product.edit')}}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="category_id">{{__('product.category')}}</label>
            <select name="category_id" class="form-control select2" required>
                <option value="">{{ __('product.select a category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="name">{{__('product.product name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label for="description">{{__('product.description')}}</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">{{__('product.price')}}</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
        </div>

        <div class="form-group">
            <label for="stock">{{__('product.stock')}}</label>
            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
        </div>

        <div class="form-group">
            <label for="images">{{__('product.product images')}}</label>
            <input type="file" name="images[]" class="form-control" multiple>
            <small class="form-text text-muted">{{__('product.nb')}}</small>

            @if($product->images->isNotEmpty())
                <div class="mt-2">
                    <label>Gambar Sebelumnya:</label>
                    <ul>
                        @foreach($product->images as $image)
                            <li><img src="{{ asset('storage/' . $image->image_url) }}" width="100" alt="Product Image"></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">{{__('product.update product')}}</button>
    </form>
@endsection
