@extends('layouts.admin')

@section('content')
    <h2 class="mt-3">Notifikasi</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($notifications->count())
    <ul class="list-group">
        @foreach ($notifications as $notification)
            <li class="list-group-item d-flex justify-content-between align-items-center notification-item
                {{ is_null($notification->read_at) ? 'bg-light border-left-primary' : '' }}"
                data-id="{{ $notification->id }}"
                style="cursor: pointer;">

                <div class="flex-grow-1">
                    @if ($notification->type === 'App\Notifications\NewUserRegistered')
                        <strong>Pengguna Baru Didaftarkan:</strong>
                        {{ $notification->data['name'] }} baru saja mendaftar.

                    @elseif ($notification->type === 'App\Notifications\NewOrder')
                        <strong>Pesanan Baru:</strong>
                        Pesanan #{{ $notification->data['order_id'] }} telah dibuat.

                    @elseif ($notification->type === 'App\Notifications\OrderStatusChanged')
                        <strong>Status Pesanan Diubah:</strong>
                        Pesanan #{{ $notification->data['order_id'] }} sekarang berstatus
                        <strong>{{ $notification->data['status'] }}</strong>.

                    @elseif ($notification->type === 'App\Notifications\NewProduct')
                        <strong>Produk Baru:</strong>
                        {{ $notification->data['product_name'] ?? '[Nama Produk Tidak Tersedia]' }}

                    @else
                        <strong>Notifikasi Tidak Dikenali</strong>
                    @endif

                    <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('notifications.show', $notification->id) }}"
                       class="btn btn-sm btn-primary">
                        View
                    </a>

                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="margin-left: 8px;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>


        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <p class="text-muted">Tidak ada notifikasi.</p>
    @endif
@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".notification-item").forEach(item => {
            item.addEventListener("click", function (e) {
                // Jika yang diklik tombol View/Delete, jangan tandai read
                if (e.target.closest('a') || e.target.closest('button')) return;

                const id = this.dataset.id;

                fetch(`/notifications/mark-as-read/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(res => {
                    if (res.ok) {
                        this.classList.remove('bg-light', 'border-left-primary');
                    }
                });
            });
        });
    });
</script>
@endpush
