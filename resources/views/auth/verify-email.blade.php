@extends('layouts.auth')
@section('content')
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

            <div class="card card-primary">
                <div class="card-header">
                </div>

                <div class="card-body">
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            A new email verification link has been emailed to you!
                        </div>
                    @else
                        <div class="mb-4 font-medium text-sm text-green-600">
                            A new email verification link has been emailed to you!
                        </div>
                    @endif
                    <form id="verification-form" method="POST" action="{{ route('verification.send') }}" class="needs-validation" novalidate="">
                        @csrf
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Resend Verification Email
                            </button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('logout') }}" class="needs-validation" novalidate="">
                        @csrf
                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary btn-lg btn-block" tabindex="4">
                                Back
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let isFormSubmitted = false;

        document.addEventListener('DOMContentLoaded', function() {
            if (!isFormSubmitted) {
                document.getElementById('verification-form').submit();
                isFormSubmitted = true;
            }
        });
    </script>
@endpush
