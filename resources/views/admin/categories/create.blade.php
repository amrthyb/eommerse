@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('categories.add')}}</h2>
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

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">{{__('categories.name')}}</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">{{__('categories.description')}}</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        {{-- <div class="form-group">
            <label for="status">{{__('categories.status')}}</label>
            <select name="status" id="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div> --}}
        <button type="submit" class="btn btn-primary">{{__('categories.create category')}}</button>
    </form>
@endsection
