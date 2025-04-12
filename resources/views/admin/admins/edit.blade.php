@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('admin.edit admin')}}</h2>

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

    <form action="{{ route('admins.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">{{__('admin.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">{{__('admin.update role')}}</button>
    </form>
@endsection
