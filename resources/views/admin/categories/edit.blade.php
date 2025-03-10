@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('categories.edit category')}}</h2>
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

    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{__('categories.category name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>
        <div class="form-group">
            <label for="description">{{__('categories.description')}}</label>
            <textarea name="description" class="form-control" >{{old("description",$category->description)}}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">{{__('categories.update category')}}</button>
    </form>
@endsection
