@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('user.list users') }}</h2>
    <div class="card-body">
        <a href="{{ route('users.export') }}" class="btn btn-success mb-3">Export</a>

        <table id="table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('user.name') }}</th>
                    <th>Email</th>
                    <th>{{ __('user.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(in_array('pengguna.lihat', Auth::user()->roles->permissions ?? []))
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">View</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
