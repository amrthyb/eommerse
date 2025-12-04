@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('setting.Change Email') }}</h2>

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

    @if (auth()->check())
        <form action="{{ route('update-account') }}" method="POST">
            @csrf

            <!-- Input untuk Nama -->
            <div class="form-group">
                <label for="name">{{ __('auth.name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
            </div>

            <!-- Input untuk Email -->
            <div class="form-group">
                <label for="email">{{ __('auth.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
            </div>

            <!-- Input untuk Password Baru -->
            <div class="form-group">
                <label for="password">{{ __('auth.password') }}</label>
                <input type="password" name="password" class="form-control">
                <small class="form-text text-muted">{{ __('auth.leave blank if not change password') }}</small>
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group">
                <label for="password_confirmation">{{ __('auth.confirm password') }}</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <!-- Input untuk Alamat -->
            <div class="form-group">
                <label for="address">{{ __('auth.address') }}</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', auth()->user()->address) }}" required>
            </div>

            <!-- Input untuk Nomor Telepon -->
            <div class="form-group">
                <label for="phone_number">{{ __('auth.phone number') }}</label>
                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', auth()->user()->phone_number) }}" required>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    @else
        <p>{{ __('auth.please_login_to_update') }}</p> <!-- Pesan jika pengguna belum login -->
    @endif

@endsection
