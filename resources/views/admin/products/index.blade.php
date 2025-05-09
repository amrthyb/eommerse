@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">{{ __('product.products') }}</h2>

    <div class="button-action" style="margin-bottom: 20px">
        @if(in_array('produk.buat', Auth::user()->roles->permissions ?? []))
        <a href="{{ route('products.create') }}" class="btn btn-primary mb-md">{{ __('product.add') }}</a>
        @endif

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#import">
            IMPORT
        </button>

        <form action="{{ route('products.export') }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="btn btn-success">EXPORT</button>
        </form>
    </div>


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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table id="productTable" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('product.name') }}</th>
                <th>{{ __('product.description') }}</th>
                <th>{{ __('product.price') }}</th>
                <th>{{ __('product.stock') }}</th>
                <th>{{ __('product.images') }}</th>
                <th>{{ __('product.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        @if ($product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                alt="{{ $product->name }}" width="50">
                        @else
                            <p>No Image</p>
                        @endif
                    </td>
                    <td>
                        @if(in_array('produk.edit', Auth::user()->roles->permissions ?? []))
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endif

                        @if(in_array('produk.hapus', Auth::user()->roles->permissions ?? []))
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal untuk Import Produk -->
    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">IMPORT DATA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>PILIH FILE (CSV, XLS, XLSX)</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">TUTUP</button>
                        <button type="submit" class="btn btn-success">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include jQuery, Bootstrap, and DataTables JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>


@endsection
