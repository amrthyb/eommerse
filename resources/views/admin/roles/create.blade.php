@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{__('role.add')}}</h2>

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

    <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">{{__('admin.name')}}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group mt-3">
            <label>Izin Akses</label>
            <div class="row">
                @php
                    $modules = [
                        'kategori' => ['buat', 'edit', 'hapus'],
                        'produk' => ['buat', 'edit', 'hapus'],
                        'admin' => ['buat', 'edit'],
                        'peran' => ['buat', 'edit', 'hapus'],
                        'pengguna' => ['lihat'],
                        'pesanan' => ['lihat'],
                    ];

                    $selectedPermissions = old('permissions', $roles->permissions ?? []);
                @endphp

                @foreach ($modules as $module => $actions)
                    <div class="col-md-4 mb-3 border rounded p-2">
                        @php
                            $moduleKey = strtolower($module);
                            $hasAnyPermission = collect($actions)->some(function ($action) use ($moduleKey, $selectedPermissions) {
                                return in_array($moduleKey . '.' . $action, $selectedPermissions);
                            });
                        @endphp

                        <div class="form-check mb-1">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="module_{{ $moduleKey }}"
                                   name="permissions[]"
                                   value="{{ $moduleKey }}"
                                   onclick="toggleModulePermissions('{{ $moduleKey }}')"
                                   {{ $hasAnyPermission ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-bold" for="module_{{ $moduleKey }}">
                                {{ $module }}
                            </label>
                        </div>

                        @foreach ($actions as $action)
                            @php
                                $perm = $moduleKey . '.' . $action;
                            @endphp
                            <div class="form-check ml-3">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $perm }}"
                                       id="{{ $perm }}"
                                       class="form-check-input perm-{{ $moduleKey }}"
                                       {{ in_array($perm, $selectedPermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $perm }}">
                                    {{ ucfirst($action) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">{{__('role.add')}}</button>
    </form>
@endsection
    <script>
    function toggleModulePermissions(module) {
        const moduleCheckbox = document.getElementById('module_' + module);
        const subPermissions = document.querySelectorAll('.perm-' + module);

        subPermissions.forEach(cb => {
            cb.checked = moduleCheckbox.checked;
            cb.disabled = !moduleCheckbox.checked;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const moduleCheckboxes = document.querySelectorAll('[id^="module_"]');

        moduleCheckboxes.forEach(checkbox => {
            const moduleKey = checkbox.id.replace('module_', '');
            const subPermissions = document.querySelectorAll('.perm-' + moduleKey);

            const allChecked = Array.from(subPermissions).every(cb => cb.checked);

            // jika semua sub-permission dicentang, maka modul utama juga dicentang
            checkbox.checked = allChecked;

            subPermissions.forEach(cb => {
                cb.disabled = !checkbox.checked;
            });

            checkbox.addEventListener('change', function () {
                subPermissions.forEach(cb => {
                    cb.checked = checkbox.checked;
                    cb.disabled = !checkbox.checked;
                });
            });
        });
    });
</script>
