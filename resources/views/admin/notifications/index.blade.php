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
            @php
                $type = class_basename($notification->type);
                $isUnread = is_null($notification->read_at);
            @endphp

            <li class="list-group-item d-flex justify-content-between align-items-center {{ $isUnread ? 'bg-info text-dark fw-bold' : 'bg-light text-muted' }}">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div>
                        @if ($type === 'NewUserRegistered')
                            New user registered: {{ $notification->data['name'] ?? 'Unknown User' }}
                            <button
                               class="btn btn-sm mark-as-read {{ $isUnread ? 'btn-secondary' : 'btn-outline-secondary' }}"
                               data-id="{{ $notification->id }}"
                               data-url="{{ route('users.show', $notification->data['user_id']) }}">
                               View
                    </button>

                        @elseif ($type === 'OrderStatusChanged')
                            Order #{{ $notification->data['order_id'] ?? 'Unknown Order' }} status changed to {{ $notification->data['status'] ?? 'Unknown Status' }}
                            <button
                               class="btn btn-sm mark-as-read {{ $isUnread ? 'btn-secondary' : 'btn-outline-secondary' }}"
                               data-id="{{ $notification->id }}"
                               data-url="{{ route('orders.show', $notification->data['order_id']) }}">
                               View
                </button>

                        @elseif ($type === 'NewOrder')
                            Order baru: Order #{{ $notification->data['order_id'] ?? 'Unknown Order' }}
                            <button
                               class="btn btn-sm mark-as-read {{ $isUnread ? 'btn-secondary' : 'btn-outline-secondary' }}"
                               data-id="{{ $notification->id }}"
                               data-url="{{ route('orders.show', $notification->data['order_id']) }}">
                               View
            </button>

                        @else
                            <strong>Notifikasi tidak dikenal</strong>
                        @endif

                        <br>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </li>
        @endforeach


        </ul>
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <p class="text-muted">Tidak ada notifikasi.</p>
    @endif
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.mark-as-read', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const url = $(this).data('url');
        $.ajax({
            url: `{{url('/notifications/mark-as-read/${id}')}}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("Response:", response);
                window.location.href = url;
            },
            error: function(xhr) {
                console.error("Error Response:", xhr.responseText);
                // window.location.href = url;
            }
        });
    });
</script>
@endpush

