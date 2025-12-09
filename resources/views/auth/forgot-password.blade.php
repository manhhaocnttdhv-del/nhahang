@extends('layouts.app')

@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="text-center mb-4 fade-in-up">
                <div class="float-animation">
                    <h1 class="display-5 mb-3 gradient-text" style="font-weight: 900;">
                        <i class="bi bi-key"></i> Quên Mật Khẩu
                    </h1>
                    <p class="text-muted" style="font-size: 1.1rem;">Nhập email để nhận link đặt lại mật khẩu</p>
                </div>
            </div>
            <div class="card shadow-lg fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body p-5">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Nhập email của bạn" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 ripple" style="font-weight: 700;">
                                <i class="bi bi-send me-2"></i>Gửi Link Đặt Lại Mật Khẩu
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none" style="color: var(--primary-color);">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

