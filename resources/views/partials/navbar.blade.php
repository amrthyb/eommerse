    <nav class="navbar navbar-expand-lg navbar-light px-3 bg-light">
        <div class="container-fluid">
            <a href="/dashboard" class="d-flex align-items-center me-md-auto text-black text-decoration-none">
                <img src="{{ asset('assets/logo.svg') }}" alt="Dashboard"
                    style="width: 25px; height: 25px; margin-right: 10px;">
                <span class="fs-4">Admin Panel</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href=""></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=""></a>
                    </li>
                </ul>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ app()->getLocale() === 'en' ? 'Language' : 'Bahasa' }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="{{ route('set.language', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('set.language', 'id') }}">Indonesia</a></li>
                    </ul>
                </div>

                <!-- resources/views/layouts/admin.blade.php -->
                <div id="notificationBell" class="ms-3 position-relative">
                    <a href="{{ route('notifications.index') }}">
                        <img src="{{ asset('assets/bell.svg') }}" alt="Dashboard"
                            style="width: 20px; height: 20px; margin-right: 5px;">
                        <!-- Badge Notifikasi -->
                        <span id="unreadNotificationBadge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display: none;">
                            0
                        </span>
                    </a>
                </div>


            </div>
        </div>
    </nav>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk mengambil jumlah notifikasi yang belum dibaca
            function updateUnreadNotificationCount() {
                fetch('{{ route('notifications.unreadCount') }}')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('unreadNotificationBadge');
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.style.display = 'inline'; // Tampilkan badge jika ada notifikasi
                        } else {
                            badge.style.display = 'none'; // Sembunyikan badge jika tidak ada notifikasi
                        }
                    })
                    .catch(error => console.error('Error fetching unread notifications:', error));
            }

            // Panggil fungsi untuk memperbarui badge saat halaman dimuat
            updateUnreadNotificationCount();

            // Set interval untuk memperbarui setiap 5 detik
            setInterval(updateUnreadNotificationCount, 5000);
        });
    </script>
    @endpush
