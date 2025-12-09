@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="text-center mb-4 fade-in-up">
                <div class="float-animation">
                    <h1 class="display-5 mb-3 gradient-text" style="font-weight: 900;">
                        <i class="bi bi-shield-lock"></i> Đặt Lại Mật Khẩu
                    </h1>
                    <p class="text-muted" style="font-size: 1.1rem;">Nhập mật khẩu mới của bạn</p>
                </div>
            </div>
            <div class="card shadow-lg fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body p-5">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control form-control-lg" 
                                   id="email" value="{{ $email }}" disabled>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="bi bi-lock me-2"></i>Mật khẩu mới
                            </label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="bi bi-lock-fill me-2"></i>Xác nhận mật khẩu
                            </label>
                            <input type="password" class="form-control form-control-lg" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Nhập lại mật khẩu mới" required>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 ripple" style="font-weight: 700;">
                                <i class="bi bi-check-circle me-2"></i>Đặt Lại Mật Khẩu
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

