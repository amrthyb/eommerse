@extends('layouts.admin')

@section('content')
<h2 class="mt-3">{{ __('admin.list admins') }}</h2>

@if(in_array('admin.buat', Auth::user()->roles->permissions ?? []))
<a href="{{ route('admins.create') }}" class="btn btn-primary mb-3">{{ __('admin.add') }}</a>
@endif

<table id="adminTable" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>{{ __('user.name') }}</th>
            <th>Email</th>
            <th>{{ __('admin.role name')}}</th>
            <th>{{ __('user.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admins as $index => $admin)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->role_name }}</td>
                <td>

                    @if(in_array('admin.edit', Auth::user()->roles->permissions ?? []))
                    <a href="{{ route('admins.edit', $admin->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    @endif

                    @if(in_array('admin.hapus', Auth::user()->roles->permissions ?? []))
                    <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?')">Delete</button>
                    </form>
                    @endif

                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
